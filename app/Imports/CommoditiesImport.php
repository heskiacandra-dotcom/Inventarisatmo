<?php

namespace App\Imports;

use App\Commodity;
use App\CommodityAcquisition;
use App\CommodityLocation;
use App\Gedung;
use App\JenisBarang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Throwable;
use Illuminate\Support\Facades\Log;

class CommoditiesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            Log::info('Mulai import barang...');

            $gedungName = trim($row['gedung'] ?? '');
            $lokasiName = trim($row['lokasi'] ?? '');
            $asalName   = trim($row['asal_perolehan'] ?? '');
            $jenisName  = trim($row['jenis_barang'] ?? '');
            $kondisiStr = trim(strtolower($row['kondisi'] ?? ''));

            // Mapping kondisi teks → angka
            $conditionMap = [
                'baik' => 1,
                'rusak' => 2,
                'belum di so' => 3,
                'belum diso' => 3,
                'belum di stock opname' => 3,
            ];
            $conditionValue = $conditionMap[$kondisiStr] ?? 3;

            // Auto-create relasi jika belum ada
            $gedung = $gedungName ? Gedung::firstOrCreate(['name' => $gedungName]) : null;
            $lokasi = $lokasiName ? CommodityLocation::firstOrCreate(['name' => $lokasiName]) : null;
            $asal   = $asalName ? CommodityAcquisition::firstOrCreate(['name' => $asalName]) : null;
            $jenis  = $jenisName ? JenisBarang::firstOrCreate(['name' => $jenisName]) : null;

            if (!$lokasi || !$gedung || !$asal) {
                Log::warning('Relasi tidak valid pada baris import', [
                    'lokasi' => $lokasiName,
                    'asal_perolehan' => $asalName,
                    'gedung' => $gedungName,
                    'jenis_barang' => $jenisName,
                ]);
                return null;
            }

            // Format tanggal fleksibel
            $tanggal = $this->parseFlexibleDate($row['tanggal_pembelian'] ?? null);

            return new Commodity([
                'name'                    => $row['nama_barang'] ?? null,
                'brand'                   => $row['merek'] ?? null,
                'material'                => $row['bahan'] ?? null,
                'commodity_acquisition_id'=> $asal?->id,
                'commodity_location_id'   => $lokasi?->id,
                'gedung_id'               => $gedung?->id,
                'jenis_barang_id'         => $jenis?->id,
                'date_of_purchase'        => $tanggal,
                'condition'               => $conditionValue,
                'quantity'                => $row['kuantitas'] ?? 1,
                'price'                   => $row['harga'] ?? 0,
                'price_per_item'          => $row['harga_satuan'] ?? 0,
                'note'                    => $row['keterangan'] ?? null,
            ]);
        } catch (Throwable $e) {
            Log::error('Import gagal: '.$e->getMessage());
            return null;
        }
    }

    private function parseFlexibleDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Hapus formula seperti =CLEAN(2025)
        if (is_string($value)) {
            $value = preg_replace('/^=CLEAN\((\d+)\)$/', '$1', $value);
        }

        // Jika hanya tahun (2025)
        if (is_numeric($value) && strlen((string) $value) == 4) {
            return (string) $value;
        }

        // Jika Excel date serial number
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            } catch (\Throwable $e) {
                return (string) $value;
            }
        }

        // Format dd/mm/yyyy
        if (preg_match('/^(\d{1,2})[\/](\d{1,2})[\/](\d{4})$/', $value, $m)) {
            return Carbon::createFromDate($m[3], $m[2], $m[1])->format('Y-m-d');
        }

        // Format mm/yyyy
        if (preg_match('/^(\d{1,2})[\/](\d{4})$/', $value, $m)) {
            return $m[2] . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT);
        }

        // Default: simpan teks apa adanya
        return trim($value);
    }
}
