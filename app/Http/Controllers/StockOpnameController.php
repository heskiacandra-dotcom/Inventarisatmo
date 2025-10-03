<?php

namespace App\Http\Controllers;

use App\Commodity;
use App\CommodityAcquisition;
use App\CommodityLocation;
use App\CommodityStockOpname;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{

    public function index()
    {
        $query = Commodity::query()->with('commodity_location', 'commodity_acquisition', 'kategoriBarang');

        // Filter sama seperti commodity index
        $query->when(request()->filled('condition'), function ($q) {
            return $q->where('condition', request('condition'));
        });

        $query->when(request()->filled('commodity_location_id'), function ($q) {
            return $q->where('commodity_location_id', request('commodity_location_id'));
        });

        $query->when(request()->filled('commodity_acquisition_id'), function ($q) {
            return $q->where('commodity_acquisition_id', request('commodity_acquisition_id'));
        });

        $query->when(request()->filled('date_of_purchase'), function ($q) {
            return $q->where('date_of_purchase', request('date_of_purchase'));
        });

        $query->when(request()->filled('material'), function ($q) {
            return $q->where('material', request('material'));
        });

        $query->when(request()->filled('brand'), function ($q) {
            return $q->where('brand', request('brand'));
        });

        $commodities = $query->latest()->get();
        $date_of_purchases = Commodity::pluck('date_of_purchase')->unique()->sort();
        $commodity_brands = Commodity::pluck('brand')->unique()->sort();
        $commodity_materials = Commodity::pluck('material')->unique()->sort();
        $commodity_acquisitions = CommodityAcquisition::orderBy('name', 'ASC')->get();
        $commodity_locations = CommodityLocation::orderBy('name', 'ASC')->get();

        return view('stock-opname.index', compact(
            'commodities',
            'commodity_acquisitions',
            'commodity_locations',
            'date_of_purchases',
            'commodity_brands',
            'commodity_materials'
        ));
    }

    private function getConditionText($condition)
    {
        return match ($condition) {
            1 => 'Baik',
            2 => 'Rusak',
            3 => 'Belum di SO',
            default => 'Tidak Diketahui'
        };
    }

    // app/Http/Controllers/StockOpnameController.php
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'commodities' => 'required|array',
            'commodities.*.id' => 'required|exists:commodities,id',
            'commodities.*.physical_quantity' => 'required|integer|min:0',
            'commodities.*.condition' => 'required|in:1,2,3',
            'commodities.*.selected' => 'sometimes|accepted'
        ]);

        foreach ($request->commodities as $data) {
            // Hanya proses jika checkbox dicentang
            if (!isset($data['selected']) || !$data['selected']) {
                continue;
            }

            $commodity = Commodity::find($data['id']);
            $systemQuantity = $commodity->quantity;

            // Update data commodity
            $commodity->update([
                'quantity' => $data['physical_quantity'],
                'condition' => $data['condition']
            ]);

            // Simpan history
            CommodityStockOpname::create([
                'commodity_id' => $data['id'],
                'system_quantity' => $systemQuantity,
                'physical_quantity' => $data['physical_quantity'],
                'condition' => $data['condition'],
                'stock_opname_at' => now(),
                'updated_by' => auth()->id() // Ditambahkan
            ]);
        }

        return redirect()->route('stock-opname.index')
            ->with('success', 'Stock opname berhasil disimpan!');
    }
    public function storeSingle(Request $request)
    {
        $validated = $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'physical_quantity' => 'required|integer|min:0',
            'condition' => 'required|in:1,2,3',
        ]);

        // Ambil data commodity SEBELUM diupdate
        $commodity = Commodity::find($validated['commodity_id']);
        $systemQuantity = $commodity->quantity; // Ambil nilai sistem sebelum update

        // Update data commodity
        $commodity->update([
            'quantity' => $validated['physical_quantity'],
            'condition' => $validated['condition']
        ]);

        // Simpan data stock opname dengan system_quantity
        CommodityStockOpname::create([
            'commodity_id' => $validated['commodity_id'],
            'system_quantity' => $systemQuantity, // Gunakan nilai sebelum diupdate
            'physical_quantity' => $validated['physical_quantity'],
            'condition' => $validated['condition'],
            'stock_opname_at' => now(),
            'updated_by' => auth()->id() // Ditambahkan
        ]);

        return response()->json([
            'success' => true,
            'commodity' => $commodity
        ]);
    }

    public function findByBarcode(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        // Ubah dari 'item_code' menjadi 'id'
        $commodity = Commodity::where('id', $request->code)
            ->with('commodity_location', 'stockOpnames')
            ->first();

        if (!$commodity) {
            return response()->json([
                'exists' => false,
                'message' => 'Barang tidak ditemukan'
            ]);
        }

        return response()->json([
            'exists' => true,
            'commodity' => [
                'id' => $commodity->id,
                'item_code' => $commodity->item_code,
                'name' => $commodity->name,
                'quantity' => $commodity->quantity,
                'condition' => $commodity->condition,
            ]
        ]);
    }
    public function history(Request $request)
{
    $commodityId = $request->input('commodity_id');

    $history = CommodityStockOpname::where('commodity_id', $commodityId)
        ->orderBy('stock_opname_at', 'asc')
        ->get()
        ->map(function ($item) {
            return [
                'stock_opname_at' => $item->stock_opname_at->translatedFormat('d F Y H:i'),
                'system_stock' => $item->system_quantity,
                'physical_stock' => $item->physical_quantity,
                'difference' => $item->system_quantity - $item->physical_quantity,
                'condition' => $item->condition,
                'condition_text' => match ($item->condition) {
                    1 => 'Baik',
                    2 => 'Rusak',
                    3 => 'Belum di SO',
                    default => 'Tidak Diketahui'
                },
                // Tambahkan data user
                'updated_by_name' => $item->updatedBy ? $item->updatedBy->name : 'System',
            ];
        });

    return response()->json([
        'data' => $history
    ]);
}
}