<?php

namespace App\Exports;

use App\CommodityLocation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CommodityLocationsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function collection()
    {
        // Ambil data dengan relasi gedung untuk optimasi
        return CommodityLocation::with('gedung')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',          // Tambahkan kolom kode
            'Nama',
            'Deskripsi',
            'Gedung',        // Tambahkan kolom gedung
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row->kode,      // Tampilkan kode
            $row->name,
            $row->description,
            $row->gedung?->name ?? 'N/A', // Gunakan optional chaining
        ];
    }
}