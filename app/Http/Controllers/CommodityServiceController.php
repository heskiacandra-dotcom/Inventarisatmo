<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Commodity;
use App\CommodityService;

class CommodityServiceController extends Controller
{
    // Halaman awal
    public function index()
    {
        $barangServis = Commodity::where('aktif', 1)->with(['commodity_location.gedung', 'services' => function($query) {
            $query->orderBy('tanggal_diservis', 'desc');
        }])
            ->whereHas('services', function ($query) {
                $query->where('status', '!=', 'Selesai');
            })
            ->get();

        $barang = null;

        return view('servis.index', compact('barang', 'barangServis'));
    }

    public function search(Request $request)
    {
        $barang = Commodity::where('aktif', 1)->with(['commodity_location.gedung', 'services' => function($query) {
            $query->orderBy('tanggal_diservis', 'desc');
        }])
            ->where('item_code', $request->item_code)
            ->first();

        $barangServis = collect();

        return view('servis.index', compact('barang', 'barangServis'));
    }

    // Simpan data servis
    public function store(Request $request)
    {
        $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'tanggal_diservis' => 'required|date',
            'kerusakan' => 'required|max:300',
            'perbaikan' => 'required|max:500',
            'tanggal_selesai' => 'nullable|date'
        ]);

        CommodityService::create([
            'commodity_id' => $request->commodity_id,
            'tanggal_diservis' => $request->tanggal_diservis,
            'kerusakan' => $request->kerusakan,
            'perbaikan' => $request->perbaikan,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->tanggal_selesai ? 'Selesai' : 'Sedang Diservis',
        ]);

        return redirect()->route('servis.index')->with('success', 'Data servis berhasil disimpan.');
    }

    // Tandai servis selesai
    public function markAsFinished($id)
    {
        $servis = CommodityService::findOrFail($id);
        $servis->update([
            'status' => 'Selesai',
            'tanggal_selesai' => now()
        ]);

        return back()->with('success', 'Barang berhasil ditandai selesai.');
    }

    // Riwayat servis untuk modal
    public function historyAjax($id)
    {
        $barang = Commodity::with([
            'commodity_location.gedung',
            'commodity_acquisition',
            'services' => function($query) {
                $query->orderBy('tanggal_diservis', 'desc');
            }
        ])->findOrFail($id);

        $servisList = $barang->services;

        return view('servis._history_modal_content', compact('barang', 'servisList'));
    }

    // Daftar barang yang masih diservis
    public function ongoing()
    {
        $barangServis = CommodityService::with('commodity')
            ->whereNull('tanggal_selesai')
            ->orderBy('tanggal_diservis', 'desc')
            ->get();

        return view('servis.ongoing', compact('barangServis'));
    }
}