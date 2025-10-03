<?php

namespace App\Exports;

use App\Commodity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class PenyusutanExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Commodity::all();
    }

    public function headings(): array
    {
        return [
            'Nama Aset',
            'Harga Perolehan',
            'Tanggal Perolehan',
            'Umur Manfaat (Bulan)',
            'Nilai Residu',
            'Penyusutan per Bulan',
            'Akumulasi Penyusutan',
            'Nilai Buku'
        ];
    }

    public function map($commodity): array
    {
        $harga = $commodity->price_per_item;
        $umur = $commodity->umur_manfaat;
        $residu = $commodity->nilai_residu;
        $perolehan = Carbon::parse($commodity->tanggal_perolehan);
        $penyusutan_bulanan = ($harga - $residu) / $umur;

        $bulan_berjalan = $perolehan->diffInMonths(now());
        if ($bulan_berjalan > $umur) $bulan_berjalan = $umur;

        $akumulasi = $penyusutan_bulanan * $bulan_berjalan;
        $nilai_buku = $harga - $akumulasi;

        return [
            $commodity->name,
            number_format($harga, 2),
            $commodity->tanggal_perolehan,
            $umur,
            number_format($residu, 2),
            number_format($penyusutan_bulanan, 2),
            number_format($akumulasi, 2),
            number_format($nilai_buku, 2),
        ];
    }
}
