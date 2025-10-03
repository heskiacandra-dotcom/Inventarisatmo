<?php

namespace App\Http\Controllers;

use App\Gedung;
use App\Exports\GedungExport;
use App\Http\Requests\GedungExportRequest;
use App\Http\Requests\GedungImportRequest;
use App\Http\Requests\StoreGedungRequest;
use App\Http\Requests\UpdateGedungRequest;
use App\Imports\GedungImport;
use Maatwebsite\Excel\Facades\Excel;

class GedungController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Gedung::class, 'gedung');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Ubah dari $Gedung ke $gedung (lowercase)
    $gedung = Gedung::orderBy('kode', 'ASC')->get();
    return view('gedung.index', compact('gedung'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGedungRequest $request)
    {
        Gedung::create($request->validated());

        return to_route('gedung.index')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGedungRequest $request, Gedung $Gedung)
    {
        $Gedung->update($request->all());

        return to_route('gedung.index')->with('success', 'Data berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gedung $gedung) // Perbaiki nama variabel ke lowercase
    {
        // Periksa apakah ada commodity_location terkait
        if ($gedung->commodityLocations()->exists()) {
            return to_route('gedung.index')
                ->with('error', 'Gedung tidak dapat dihapus karena masih terkait dengan data ruangan!');
        }

        $gedung->delete();

        return to_route('gedung.index')->with('success', 'Data berhasil dihapus!');
    }

    /**
     * Export commodities data to Excel.
     */
    public function export(GedungExportRequest $request)
    {
        $this->authorize('export gedung');

        $filename = 'daftar-gedung-'.date('d-m-Y');

        return match ($request->extension) {
            'xlsx' => Excel::download(new GedungExport, $filename.'.xlsx', \Maatwebsite\Excel\Excel::XLSX),
            'xls' => Excel::download(new GedungExport, $filename.'.xls', \Maatwebsite\Excel\Excel::XLS),
            'csv' => Excel::download(new GedungExport, $filename.'.csv', \Maatwebsite\Excel\Excel::CSV),
            'html' => Excel::download(new GedungExport, $filename.'.html', \Maatwebsite\Excel\Excel::HTML),
        };
    }

    /**
     * Import commodity locations data from Excel.
     */
    public function import(GedungImportRequest $request)
    {
        Excel::import(new GedungImport, $request->file('file'));

        return to_route('gedung.index')->with('success', 'Data gedung berhasil diimpor!');
    }
}
