<?php

// app/Http/Controllers/LaporanPenyusutanController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Commodity;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenyusutanExport;

class LaporanPenyusutanController extends Controller
{
    public function index()
    {
        $commodities = Commodity::all();
        return view('laporan.penyusutan.index', compact('commodities'));
    }

    public function export()
    {
        return Excel::download(new PenyusutanExport, 'laporan_penyusutan.xlsx');
    }
}
