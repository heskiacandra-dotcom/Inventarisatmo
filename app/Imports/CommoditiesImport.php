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
use Illuminate\Support\Facades\Log; // <-- 1. TAMBAHKAN INI

class CommoditiesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            // Mapping header Excel → field standar sistem
            $mapping = [
                'peruntukan_barang' => 'gedung',
                'ruangan'           => 'lokasi',
                'asal_perolehan'    => 'asal_perolehan',
                'jenis_aset_tetap'  => 'jenis_barang',
                'kondisi'           => 'kondisi',
                'tanggal_pembelian' => 'tanggal_pembelian',
                'kuantitas'         => 'kuantitas',
                'harga'             => 'harga',
                'harga_satuan'      => 'harga_satuan',
                'keterangan'        => 'keterangan',
                'nama_barang'       => 'nama_barang',
                'merek'             => 'merek',
                'bahan'             => 'bahan',
            ];

            // Normalisasi row berdasarkan mapping
            $normalizedRow = [];
            foreach ($row as $key => $value) {
                $cleanKey = strtolower(trim(preg_replace('/\s+/', ' ', $key)));
                if (isset($mapping[$cleanKey])) {
                    $normalizedRow[$mapping[$cleanKey]] = $value;
                } else {
                    $normalizedRow[$cleanKey] = $value;
                }
            }

            // Ambil data sesuai nama standar
            $gedungName = trim($normalizedRow['gedung'] ?? '');
            $lokasiName = trim($normalizedRow['lokasi'] ?? '');
            $asalName   = trim($normalizedRow['asal_perolehan'] ?? '');
            $jenisName  = trim($normalizedRow['jenis_barang'] ?? '');
            $kondisiStr = trim(strtolower($normalizedRow['kondisi'] ?? ''));

            // Mapping kondisi
            $conditionMap = [
                'baik'         => 1,
                'rusak'        => 2,
                'belum di so'  => 3,
                'belum diso'   => 3,
                'belum di stock opname' => 3,
            ];
            $conditionValue = $conditionMap[$kondisiStr] ?? 3;

            // Relasi ke tabel lain
            $gedung = $gedungName ? Gedung::firstOrCreate(['name' => $gedungName]) : null;
            $lokasi = $lokasiName ? CommodityLocation::firstOrCreate(['name' => $lokasiName]) : null;
            $asal   = $asalName ? CommodityAcquisition::firstOrCreate(['name' => $asalName]) : null;
            $jenis  = $jenisName ? JenisBarang::firstOrCreate(['name' => $jenisName]) : null;

            
            // --- 2. MODIFIKASI BLOK VALIDASI ---
            // Cek satu per satu agar log-nya jelas
            
            // Dari data Anda, kolom 'No.' dan 'Jenis Aset Tetap' banyak yang kosong.
            // Mari kita cek data yang *kritis* untuk sistem Anda
            
            if (empty($normalizedRow['nama_barang'])) {
                Log::warning('Baris dilewati: Kolom "Nama Barang" kosong.', ['data_baris' => $row]);
                return null;
            }
            if (!$lokasi) {
                Log::warning('Baris dilewati: Kolom "Ruangan" kosong atau tidak valid.', ['nama_lokasi' => $lokasiName, 'data_baris' => $row]);
                return null;
            }
            if (!$gedung) {
                Log::warning('Baris dilewati: Kolom "Peruntukan Barang" kosong atau tidak valid.', ['nama_gedung' => $gedungName, 'data_baris' => $row]);
                return null;
            }
            if (!$asal) {
                Log::warning('Baris dilewati: Kolom "Asal Perolehan" kosong atau tidak valid.', ['nama_asal' => $asalName, 'data_baris' => $row]);
                return null;
            }

            // Parse tanggal
            $tanggal = $this->parseFlexibleDate($normalizedRow['tanggal_pembelian'] ?? null);

            // Jumlah barang (kuantitas)
            $quantity = (int) ($normalizedRow['kuantitas'] ?? 1);
            if ($quantity <= 0) {
                 $quantity = 1; // Pastikan minimal 1
            }
            $originalQuantity = $quantity; // simpan untuk isi ke setiap record

            // Hapus field kuantitas dari data per barang
            unset($normalizedRow['kuantitas']);

            // Generate data per barang (1 record = 1 barang unik)
            for ($i = 0; $i < $quantity; $i++) {
                Commodity::create([
                    'name'                     => $normalizedRow['nama_barang'] ?? null,
                    'brand'                    => $normalizedRow['merek'] ?? null,
                    'material'                 => $normalizedRow['bahan'] ?? null,
                    'commodity_acquisition_id' => $asal?->id,
                    'commodity_location_id'    => $lokasi?->id,
                    'gedung_id'                => $gedung?->id,
                    'jenis_barang_id'          => $jenis?->id, // Boleh null jika jenis tidak wajib
                    'date_of_purchase'         => $tanggal,
                    'condition'                => $conditionValue,
                    'quantity'                 => $originalQuantity, 
                    'price'                    => $normalizedRow['harga'] ?? 0,
                    'price_per_item'           => $normalizedRow['harga_satuan'] ?? 0,
                    'note'                     => $normalizedRow['keterangan'] ?? null,
                ]);
            }

            // return null supaya ToModel tidak menyimpan duplikat default
            return null;

        } catch (Throwable $e) {
            
            // --- 3. INI BAGIAN TERPENTING ---
            // Catat error apapun yang terjadi ke log
            $errorMessage = 'Gagal import baris. Error: ' . $e->getMessage();
            Log::error($errorMessage, ['data_baris' => $row, 'trace' => $e->getTraceAsString()]);
            
            return null; // tetap lewati baris, tapi sekarang ada log
        }
    }

    private function parseFlexibleDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = preg_replace('/^=CLEAN\((\d+)\)$/', '$1', $value);
        }

        if (is_numeric($value) && strlen((string) $value) == 4) {
            return (string) $value; // hanya tahun
        }

        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            } catch (\Throwable $e) {
                return (string) $value;
            }
        }

        // Format: 1/1/2024 atau 01/01/2024
        if (preg_match('/^(\d{1,2})[\/](\d{1,2})[\/](\d{4})$/', $value, $m)) {
            return Carbon::createFromDate($m[3], $m[2], $m[1])->format('Y-m-d');
        }
        
        // Format: 1/2024 atau 01/2024 (Bulan/Tahun)
        if (preg_match('/^(\d{1,2})[\/](\d{4})$/', $value, $m)) {
            return $m[2] . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT) . '-01'; // Default tgl 1
        }

        return trim($value); // fallback
    }
}