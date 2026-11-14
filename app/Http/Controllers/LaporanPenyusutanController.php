<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Commodity;
use App\CommodityLocation;
use App\JenisBarang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenyusutanExport;

class LaporanPenyusutanController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Commodity::query()->where('aktif', 1);

        // Filter Lokasi / Ruangan
        if ($request->filled('location_id')) {
            $baseQuery->where('commodity_location_id', $request->location_id);
        }

        // Filter Jenis Barang
        if ($request->filled('jenis_barang_id')) {
            $baseQuery->where('jenis_barang_id', $request->jenis_barang_id);
        }

        // Filter Kondisi
        if ($request->filled('condition')) {
            $baseQuery->where('condition', $request->condition);
        }

        // Filter Pencarian Teks
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Hitung akumulasi statistik secara presisi dan efisien dalam satu pass
        $statsItems = (clone $baseQuery)->select([
            'id', 'price', 'price_per_item', 'tanggal_perolehan', 'date_of_purchase', 'umur_manfaat', 'nilai_residu'
        ])->get();

        $totalAset = $statsItems->count();
        $totalHargaPerolehan = 0;
        $totalPenyusutanBulanan = 0;
        $totalAkumulasiPenyusutan = 0;
        $totalNilaiBuku = 0;

        $nowYear = (int)date('Y');
        $nowMonth = (int)date('n');

        foreach ($statsItems as $c) {
            $harga = $c->price_per_item > 0 ? (float)$c->price_per_item : ($c->price > 0 ? (float)$c->price : 0);
            $residu = $c->nilai_residu ? (float)$c->nilai_residu : 0;
            $umur = ($c->umur_manfaat && (int)$c->umur_manfaat > 0) ? (int)$c->umur_manfaat : 60;

            $pYear = $nowYear;
            $pMonth = 1;
            if (!empty($c->tanggal_perolehan)) {
                $pYear = (int)substr($c->tanggal_perolehan, 0, 4);
                $pMonth = (int)substr($c->tanggal_perolehan, 5, 2);
            } elseif (!empty($c->date_of_purchase)) {
                if (preg_match('/\b(19|20)\d{2}\b/', (string)$c->date_of_purchase, $m)) {
                    $pYear = (int)$m[0];
                    $pMonth = 1;
                }
            }

            $diffMonths = max(0, ($nowYear - $pYear) * 12 + ($nowMonth - $pMonth));
            $bulanBerjalan = min($diffMonths, $umur);

            $penyusutanPerBulan = $umur > 0 ? max(0, ($harga - $residu) / $umur) : 0;
            $maxPenyusutan = max(0, $harga - $residu);
            $akumulasi = min($penyusutanPerBulan * $bulanBerjalan, $maxPenyusutan);
            $nilaiBuku = max($residu, $harga - $akumulasi);

            $totalHargaPerolehan += $harga;
            $totalPenyusutanBulanan += $penyusutanPerBulan;
            $totalAkumulasiPenyusutan += $akumulasi;
            $totalNilaiBuku += $nilaiBuku;
        }

        // Paginasi server-side
        $perPage = $request->integer('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100, 250])) {
            $perPage = 25;
        }

        $commodities = (clone $baseQuery)
            ->with(['commodity_location', 'jenisBarang', 'gedung', 'commodity_acquisition'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $locations = CommodityLocation::orderBy('name', 'ASC')->get();
        $jenisBarangList = JenisBarang::orderBy('name', 'ASC')->get();

        return view('laporan.penyusutan.index', compact(
            'commodities',
            'locations',
            'jenisBarangList',
            'totalAset',
            'totalHargaPerolehan',
            'totalPenyusutanBulanan',
            'totalAkumulasiPenyusutan',
            'totalNilaiBuku'
        ));
    }

    public function export()
    {
        return Excel::download(new PenyusutanExport, 'laporan_penyusutan_' . date('Ymd_His') . '.xlsx');
    }

    /**
     * Update parameter penyusutan aset (umur manfaat & nilai residu)
     */
    public function updateParameter(Request $request, $id)
    {
        $request->validate([
            'umur_manfaat' => 'required|numeric|min:1',
            'nilai_residu' => 'nullable|numeric|min:0',
            'tanggal_perolehan' => 'nullable|date',
        ]);

        $commodity = Commodity::findOrFail($id);
        $commodity->update([
            'umur_manfaat' => $request->umur_manfaat,
            'nilai_residu' => $request->nilai_residu ?? 0,
            'tanggal_perolehan' => $request->tanggal_perolehan ?? $commodity->tanggal_perolehan,
        ]);

        return back()->with('success', 'Parameter penyusutan aset ' . $commodity->name . ' berhasil diperbarui.');
    }
}
