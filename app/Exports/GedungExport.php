<?php

namespace App\Exports;

use App\Gedung;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GedungExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Gedung::all();
    }

    /**
     * Specify the headings for the exported file.
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama',
        ];
    }

    /**
     * Map each row of the collection to an array for export.
     */
    public function map($row): array
    {
        static $i = 0;

        $i++;

        return [
            $i,
            $row->name,
        ];
    }
}
