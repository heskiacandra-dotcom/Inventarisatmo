<?php

namespace App\Imports;

use App\Commodity;
use App\CommodityAcquisition;
use App\CommodityLocation;
use App\JenisBarang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CommoditiesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari relasi berdasarkan nama
        $commodity_location = CommodityLocation::where('name', $row['lokasi'])->first();
        $commodity_acquisition = CommodityAcquisition::where('name', $row['asal_perolehan'])->first();
        $jenis_barang = JenisBarang::where('name', $row['jenis_barang'])->first();

        // Validasi relasi wajib
        if (!$commodity_location || !$commodity_acquisition || !$jenis_barang) {
            throw new \Exception("Lokasi, Asal Perolehan, atau Jenis Barang tidak valid pada baris: " . json_encode($row));
        }

        // Konversi tanggal Excel ke format Y-m-d
        $tanggalPembelian = $this->transformDate($row['tanggal_pembelian']);

        // Pastikan quantity minimal 1
        $quantity = max((int) $row['kuantitas'], 1);

        // Buat beberapa instance sesuai kuantitas
        for ($i = 0; $i < $quantity; $i++) {
            Commodity::create([
                // 'item_number' dan 'item_code' akan digenerate otomatis
                'commodity_acquisition_id' => $commodity_acquisition->id,
                'commodity_location_id'    => $commodity_location->id,
                'jenis_barang_id'          => $jenis_barang->id,
                'name'                     => $row['nama_barang'],
                'brand'                    => $row['merek'],
                'material'                 => $row['bahan'],
                'date_of_purchase'         => $tanggalPembelian,
                'condition'                => $this->translateConditionNameToNumber($row['kondisi']),
                'quantity'                 => 1, // selalu 1 per record
                'price'                    => $row['harga'],
                'price_per_item'           => $row['harga_satuan'],
                'note'                     => $row['keterangan'],
            ]);
        }

        // Karena ToModel butuh return model, kembalikan instance terakhir
        return new Commodity();
    }

    // Tangani konversi tanggal
    private function transformDate($value)
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        return $value;
    }

    private function translateConditionNameToNumber($conditionName)
    {
        return match (strtolower(trim($conditionName))) {
            'baik'        => 1,
            'rusak'       => 2,
            'belum di so' => 3,
            default       => throw new \Exception("Kondisi tidak valid: " . $conditionName),
        };
    }
}
