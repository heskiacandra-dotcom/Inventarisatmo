<?php

namespace App\Http\Controllers;

use App\Commodity;
use Illuminate\Http\Request;

class LaporanPelepasanController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('hapus barang');

        $query = Commodity::with('commodity_location', 'commodity_acquisition', 'jenisBarang', 'gedung')
            ->where('aktif', 0);

        $query->when($request->filled('metode_pelepasan'), function ($q) use ($request) {
            return $q->where('metode_pelepasan', $request->metode_pelepasan);
        });

        $query->when($request->filled('dari_tanggal'), function ($q) use ($request) {
            return $q->whereDate('tanggal_pelepasan', '>=', $request->dari_tanggal);
        });

        $query->when($request->filled('sampai_tanggal'), function ($q) use ($request) {
            return $q->whereDate('tanggal_pelepasan', '<=', $request->sampai_tanggal);
        });

        $barangDilepas = $query->orderBy('tanggal_pelepasan', 'desc')->get();

        $metodeOptions = ['Dijual', 'Dihapusbukukan (Rusak Total)', 'Hilang'];

        return view('laporan.pelepasan.index', compact('barangDilepas', 'metodeOptions'));
    }
}
