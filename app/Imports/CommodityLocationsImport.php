<?php

namespace App\Imports;

use App\CommodityLocation;
use App\Gedung;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable; // <-- Tambahkan ini
use Throwable;

class CommodityLocationsImport implements 
    ToModel, 
    WithHeadingRow,
    SkipsOnError,
    SkipsOnFailure,
    WithValidation
{
    use Importable;

    public function model(array $row)
    {
        // Sesuaikan dengan header Excel (case-sensitive)
        $gedung = Gedung::where('name', $row['Gedung'])->first();

        return new CommodityLocation([
            'kode'        => $row['Kode'],
            'gedung_id'   => $gedung?->id,
            'name'        => $row['Nama'],
            'description' => $row['Deskripsi'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'Kode'      => 'required|string|max:5', // Sesuaikan header Excel
            'Gedung'    => 'required|string|exists:gedung,name',
            'Nama'      => 'required|string|max:255',
            'Deskripsi' => 'nullable|string',
        ];
    }

    public function onFailure(Failure ...$failures)
    {

    }

    public function onError(Throwable $e)
    {

    }
}