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
        return Commodity::with(['commodity_location', 'jenisBarang'])
            ->where('aktif', 1)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Aset',
            'Ruangan / Lokasi',
            'Jenis Aset Tetap',
            'Harga Perolehan (Rp)',
            'Tanggal Perolehan',
            'Umur Manfaat (Bulan)',
            'Nilai Residu (Rp)',
            'Penyusutan / Bulan (Rp)',
            'Akumulasi Penyusutan (Rp)',
            'Nilai Buku Saat Ini (Rp)',
        ];
    }

    public function map($commodity): array
    {
        return [
            $commodity->item_code ?? '-',
            $commodity->name ?? '-',
            optional($commodity->commodity_location)->name ?? '-',
            optional($commodity->jenisBarang)->name ?? '-',
            number_format($commodity->harga_perolehan, 2, ',', '.'),
            $commodity->tanggal_perolehan_parsed->format('d-m-Y'),
            $commodity->umur_manfaat_bulan,
            number_format($commodity->nilai_residu_value, 2, ',', '.'),
            number_format($commodity->penyusutan_per_bulan, 2, ',', '.'),
            number_format($commodity->akumulasi_penyusutan, 2, ',', '.'),
            number_format($commodity->nilai_buku, 2, ',', '.'),
        ];
    }
}
