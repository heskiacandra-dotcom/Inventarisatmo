<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Commodity;
use App\CommodityAcquisition;
use App\CommodityLocation;
use App\Exports\CommoditiesExport;
use App\Http\Requests\CommodityExportRequest;
use App\Http\Requests\CommodityImportRequest;
use App\Http\Requests\StoreCommodityRequest;
use App\Http\Requests\UpdateCommodityRequest;
use App\Imports\CommoditiesImport;
use App\Repositories\CommodityRepository;
use App\Gedung; // <-- Tambahkan import model Gedung
use App\JenisBarang; // tambahkan di atas
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CommodityController extends Controller
{
    public function __construct(private
        CommodityRepository $commodityRepository,
        )
    {
        $this->authorizeResource(Commodity::class , 'commodity');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Commodity::query()->with('commodity_location', 'commodity_acquisition')->where('aktif', 1);

        // Search filter (nama barang, kode, nomor, merk, bahan)
        $query->when(request()->filled('search'), function ($q) {
            $search = request('search');
            return $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%")
                    ->orWhere('item_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('material', 'like', "%{$search}%");
            });
        });

        // Apply filters
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

        // Ambil data gedung (untuk dropdown di modal create/edit)
        $gedung = Gedung::orderBy('name', 'ASC')->get();
        $jenisBarang = JenisBarang::orderBy('name', 'ASC')->get();

        $perPage = request()->integer('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100, 250])) {
            $perPage = 25;
        }

        // Paginasi server-side
        $commodities = $query->latest()->paginate($perPage)->withQueryString();

        // Optimized distinct dropdown queries
        $date_of_purchases = Commodity::where('aktif', 1)
            ->whereNotNull('date_of_purchase')
            ->where('date_of_purchase', '!=', '')
            ->distinct()
            ->orderBy('date_of_purchase', 'ASC')
            ->pluck('date_of_purchase');

        $commodity_brands = Commodity::where('aktif', 1)
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand', 'ASC')
            ->pluck('brand');

        $commodity_materials = Commodity::where('aktif', 1)
            ->whereNotNull('material')
            ->where('material', '!=', '')
            ->distinct()
            ->orderBy('material', 'ASC')
            ->pluck('material');

        $commodity_acquisitions = CommodityAcquisition::orderBy('name', 'ASC')->get();
        $commodity_locations = CommodityLocation::orderBy('name', 'ASC')->get();


        // Clone query dan reset order by
        $filteredQuery = clone $query;
        $filteredQuery->reorder();

        // Clone query untuk TOTAL tanpa group by
        $totalQuery = clone $query;
        $totalQuery->reorder();

        // Clone query untuk KONDISI
        $conditionQuery = clone $query;
        $conditionQuery->reorder();

        // Hitung total barang (TANPA group by)
        $totalCount = $totalQuery->count();

        // Hitung statistik kondisi
        $commodity_condition_count = $conditionQuery->selectRaw('`condition`, count(*) as count')
            ->groupBy('condition')
            ->get()
            ->map(function ($commodity) {
            return collect([
            'condition_name' => $this->commodityRepository->getConditionName($commodity->condition),
            'count' => $commodity->count,
            ]);
        });

        $commodity_counts = [
            'commodity_in_total' => $totalCount,
            'commodity_in_good_condition' => data_get($commodity_condition_count->firstWhere('condition_name', 'Baik'), 'count', 0),
            'commodity_in_not_good_condition' => data_get($commodity_condition_count->firstWhere('condition_name', 'Rusak'), 'count', 0),
            'commodity_in_heavily_damage_condition' => data_get($commodity_condition_count->firstWhere('condition_name', 'Belum di SO'), 'count', 0),
        ];

        return view(
            'commodities.index',
            compact(
            'commodities',
            'gedung', // <<< pastikan ini dikirim ke view
            'jenisBarang',
            'commodity_acquisitions',
            'commodity_locations',
            'date_of_purchases',
            'commodity_brands',
            'commodity_materials',
            'commodity_counts'
        )
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommodityRequest $request)
    {
        $validated = $request->validated();
        $quantity = $validated['quantity'] ?? 1;

        // Simpan kuantitas asli
        $originalQuantity = $quantity;

        for ($i = 0; $i < $quantity; $i++) {
            Commodity::create(array_merge($validated, [
                'quantity' => $originalQuantity // setiap record tetap pakai kuantitas asli
            ]));
        }

        return to_route('barang.index')->with('success', 'Data berhasil ditambahkan!');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommodityRequest $request, Commodity $commodity)
    {
        $commodity->update($request->all());

        return to_route('barang.index')->with('success', 'Data berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     * Disabled — gunakan lepas() untuk pelepasan barang.
     */
    public function destroy(Commodity $commodity)
    {
        // Hard delete dinonaktifkan; pelepasan dilakukan via lepas()
        abort(403, 'Hapus permanen tidak diizinkan. Gunakan fitur Lepas Barang.');
    }

    /**
     * Lepas (disposal) barang — soft delete dengan data pelepasan.
     */
    public function lepas(Request $request, Commodity $commodity)
    {
        $this->authorize('hapus barang');

        $validated = $request->validate([
            'tanggal_pelepasan' => 'required|date',
            'metode_pelepasan'  => 'required|in:Dijual,Dihapusbukukan (Rusak Total),Hilang',
            'catatan_pelepasan' => 'nullable|string|max:1000',
        ]);

        $commodity->lepas(
            $validated['tanggal_pelepasan'],
            $validated['metode_pelepasan'],
            $validated['catatan_pelepasan'] ?? null
        );

        return to_route('barang.index')->with('success', 'Barang berhasil dilepas!');
    }

    /**
     * Generate PDF for all commodities.
     */


    public function generatePDF()
    {
        $this->authorize('print barang');

        $query = Commodity::query()->with('commodity_location', 'commodity_acquisition')->where('aktif', 1);

        // Terapkan filter yang sama seperti di index()
        $query->when(request()->filled('search'), function ($q) {
            $search = request('search');
            return $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%")
                    ->orWhere('item_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('material', 'like', "%{$search}%");
            });
        });

        $query->when(
            request()->filled('condition'),
        fn($q) =>
        $q->where('condition', request('condition'))
        );


        $query->when(
            request()->filled('commodity_location_id'),
        fn($q) =>
        $q->where('commodity_location_id', request('commodity_location_id'))
        );

        $query->when(
            request()->filled('commodity_acquisition_id'),
        fn($q) =>
        $q->where('commodity_acquisition_id', request('commodity_acquisition_id'))
        );

        $query->when(
            request()->filled('date_of_purchase'),
        fn($q) =>
        $q->where('date_of_purchase', request('date_of_purchase'))
        );

        $query->when(
            request()->filled('material'),
        fn($q) =>
        $q->where('material', request('material'))
        );

        $query->when(
            request()->filled('brand'),
        fn($q) =>
        $q->where('brand', request('brand'))
        );

        $commodities = $query->get();

        return view('commodities.pdf', compact('commodities'));
    }

    public function generateQRCode($id)
    {
        $this->authorize('print individual barang');

        $commodity = Commodity::findOrFail($id);
        $qrCode = QrCode::size(300)->generate($commodity->id);

        return view('commodities.qrcode', compact('qrCode', 'commodity'));
    }

    /**
     * Export commodities data to Excel.
     */
    public function export(CommodityExportRequest $request)
    {
        $this->authorize('export barang');

        $filename = 'daftar-barang-' . date('d-m-Y');

        return match ($request->extension) {
                'xlsx' => Excel::download(new CommoditiesExport, $filename . '.xlsx', \Maatwebsite\Excel\Excel::XLSX),
                'xls' => Excel::download(new CommoditiesExport, $filename . '.xls', \Maatwebsite\Excel\Excel::XLS),
                'csv' => Excel::download(new CommoditiesExport, $filename . '.csv', \Maatwebsite\Excel\Excel::CSV),
                'html' => Excel::download(new CommoditiesExport, $filename . '.html', \Maatwebsite\Excel\Excel::HTML),
            };
    }

    /**
     * Import commodities data from Excel.
     */
    public function import(CommodityImportRequest $request)
    {
        $this->authorize('import barang');

        Excel::import(new CommoditiesImport, $request->file('file'));

        return to_route('barang.index')->with('success', 'Data barang berhasil diimpor!');
    }

    public function showScanPage()
    {
        $this->authorize('scan barang');
        return view('commodities.scan');
    }

    public function apiShowByCode($kode)
    {
        $commodity = Commodity::where('aktif', 1)
            ->with(['commodity_location', 'commodity_acquisition'])
            ->where(function ($q) use ($kode) {
                $q->where('item_code', $kode)->orWhere('id', $kode);
            })
            ->first();

        if (!$commodity) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        // === Mapping kondisi ===
        $conditionMap = [
            1 => 'Baik',
            2 => 'Kurang Baik',
            3 => 'Rusak Berat',
        ];
        $commodity_condition_text = $conditionMap[$commodity->condition] ?? 'Belum di SO';

        // === Hitung kondisi barang (hanya aktif) ===
        $totalCount = Commodity::where('aktif', 1)->count();

        $commodity_condition_count = Commodity::where('aktif', 1)->selectRaw('`condition`, count(*) as count')
            ->groupBy('condition')
            ->get()
            ->map(function ($row) use ($conditionMap) {
            return collect([
            'condition_name' => $conditionMap[$row->condition] ?? 'Belum di SO',
            'count' => $row->count,
            ]);
        });

        $commodity_counts = [
            'commodity_in_total' => $totalCount,
            'commodity_in_good_condition' => data_get($commodity_condition_count->firstWhere('condition_name', 'Baik'), 'count', 0),
            'commodity_in_not_good_condition' => data_get($commodity_condition_count->firstWhere('condition_name', 'Rusak'), 'count', 0),
            'commodity_in_heavily_damage_condition' => data_get($commodity_condition_count->firstWhere('condition_name', 'Belum di SO'), 'count', 0),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'item_code' => $commodity->item_code,
                'name' => $commodity->name,
                'commodity_location' => [
                    'name' => $commodity->commodity_location->name
                ],
                'material' => $commodity->material,
                'brand' => $commodity->brand,
                'date_of_purchase' => $commodity->date_of_purchase,
                'condition_name' => $commodity_condition_text, // pakai mapping
                'commodity_acquisition' => [
                    'name' => $commodity->commodity_acquisition->name
                ],
                'note' => $commodity->note,
                'quantity' => $commodity->quantity,
            ],
            'commodity_counts' => $commodity_counts
        ]);
    }

    public function apiShow($id)
    {
        $commodity = Commodity::where('aktif', 1)
            ->with(['commodity_location', 'commodity_acquisition', 'jenisBarang', 'gedung'])
            ->findOrFail($id);

        // Mapping kondisi
        $conditionMap = [
            1 => 'Baik',
            2 => 'Kurang Baik',
            3 => 'Rusak Berat',
        ];
        $conditionText = $conditionMap[$commodity->condition] ?? 'Belum di SO';

        return response()->json([
            'data' => [
                'item_code' => $commodity->item_code,
                'item_number' => $commodity->item_number, // nomor barang
                'name' => $commodity->name,

                // Lokasi barang
                'commodity_location' => [
                    'id' => $commodity->commodity_location->id,
                    'name' => $commodity->commodity_location->name
                ],

                // Asal perolehan
                'commodity_acquisition' => [
                    'id' => $commodity->commodity_acquisition->id,
                    'name' => $commodity->commodity_acquisition->name
                ],

                // Jenis barang
                'jenis_barang' => [
                    'id' => $commodity->jenisBarang->id,
                    'name' => $commodity->jenisBarang->name
                ],

                // Gedung
                'gedung' => [
                    'id' => $commodity->gedung->id,
                    'name' => $commodity->gedung->name
                ],

                'material' => $commodity->material,
                'brand' => $commodity->brand,
                'date_of_purchase' => $commodity->date_of_purchase,

                'condition' => $commodity->condition,
                'condition_name' => $conditionText, // pakai mapping

                'note' => $commodity->note,
                'quantity' => $commodity->quantity,

                // Harga asli
                'price' => $commodity->price,
                'price_per_item' => $commodity->price_per_item,

                // Harga format Rp
                'price_formatted' => number_format($commodity->price, 0, ',', '.'),
                'price_per_item_formatted' => number_format($commodity->price_per_item, 0, ',', '.'),
            ]
        ]);
    }


    public function publicScanPage()
    {
        $locations = CommodityLocation::orderBy('name')->get();
        return view('commodities.public-scan', compact('locations'));
    }

    public function reportLocation(Request $request)
    {
        $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'new_location_id' => 'required|exists:commodity_locations,id',
            'reporter_name' => 'required|string|max:255',
        ]);

        $commodity = Commodity::findOrFail($request->commodity_id);
        $oldLocationId = $commodity->commodity_location_id;

        // 1. Buat catatan mutasi di tabel ItemLending (sebagai histori)
        \App\ItemLending::create([
            'commodity_id' => $commodity->id,
            'user_name' => $request->reporter_name . ' (Lapor Scan)',
            'origin_location_id' => $oldLocationId,
            'return_location_id' => $request->new_location_id,
            'start_date' => now(),
            'start_time' => now()->format('H:i'),
            'end_date' => now(), // langsung selesai
            'end_time' => now()->format('H:i'), // langsung selesai
            'actual_return_date' => now(),
            'actual_return_time' => now()->format('H:i'),
            'status' => 'returned', // Anggap sudah kembali/pindah
            'notes' => 'Pemindahan lokasi otomatis via fitur Lapor Scan QR Code.'
        ]);

        // 2. Update lokasi barang sekarang
        $commodity->update([
            'commodity_location_id' => $request->new_location_id
        ]);

        return response()->json(['success' => true, 'message' => 'Lokasi barang berhasil diperbarui otomatis!']);
    }
}
