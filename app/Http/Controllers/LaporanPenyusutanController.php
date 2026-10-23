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
    /**
     * Ensure database columns exist (self-healing for unmigrated environments)
     */
    private function ensureSchemaExists(): void
    {
        try {
            if (Schema::hasTable('commodities')) {
                Schema::table('commodities', function ($table) {
                    if (!Schema::hasColumn('commodities', 'tanggal_perolehan')) {
                        $table->date('tanggal_perolehan')->nullable();
                    }
                    if (!Schema::hasColumn('commodities', 'umur_manfaat')) {
                        $table->integer('umur_manfaat')->default(60)->nullable();
                    }
                    if (!Schema::hasColumn('commodities', 'nilai_residu')) {
                        $table->decimal('nilai_residu', 15, 2)->default(0)->nullable();
                    }
                    if (!Schema::hasColumn('commodities', 'aktif')) {
                        $table->tinyInteger('aktif')->default(1)->nullable();
                    }
                });
            }
        } catch (\Throwable $e) {
            // Silently ignore if table altering is restricted
        }
    }

    public function index(Request $request)
    {
        $this->ensureSchemaExists();

        $query = Commodity::query()
            ->with(['commodity_location', 'jenisBarang', 'gedung', 'commodity_acquisition'])
            ->where(function ($q) {
                if (Schema::hasColumn('commodities', 'aktif')) {
                    $q->where('aktif', 1)->orWhereNull('aktif');
                }
            });

        // Filter Lokasi / Ruangan
        if ($request->filled('location_id')) {
            $query->where('commodity_location_id', $request->location_id);
        }

        // Filter Jenis Barang
        if ($request->filled('jenis_barang_id')) {
            $query->where('jenis_barang_id', $request->jenis_barang_id);
        }

        // Filter Kondisi
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Filter Pencarian Teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $commodities = $query->latest()->get();

        // Hitung akumulasi statistik
        $totalAset = $commodities->count();
        $totalHargaPerolehan = $commodities->sum(fn($c) => $c->harga_perolehan);
        $totalPenyusutanBulanan = $commodities->sum(fn($c) => $c->penyusutan_per_bulan);
        $totalAkumulasiPenyusutan = $commodities->sum(fn($c) => $c->akumulasi_penyusutan);
        $totalNilaiBuku = $commodities->sum(fn($c) => $c->nilai_buku);

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
        $this->ensureSchemaExists();
        return Excel::download(new PenyusutanExport, 'laporan_penyusutan_' . date('Ymd_His') . '.xlsx');
    }

    /**
     * Update parameter penyusutan aset (umur manfaat & nilai residu)
     */
    public function updateParameter(Request $request, $id)
    {
        $this->ensureSchemaExists();

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
