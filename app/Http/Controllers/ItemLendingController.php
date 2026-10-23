<?php

namespace App\Http\Controllers;

use App\Commodity;
use App\CommodityLocation;
use App\ItemLending;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ItemLendingController extends Controller
{
    private function ensureSchemaExists(): void
    {
        try {
            if (!Schema::hasTable('item_lendings')) {
                Schema::create('item_lendings', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('commodity_id')->index();
                    $table->string('user_name');
                    $table->unsignedBigInteger('origin_location_id')->nullable()->index();
                    $table->unsignedBigInteger('return_location_id')->nullable()->index();
                    $table->date('start_date');
                    $table->date('end_date')->nullable();
                    $table->time('start_time');
                    $table->time('end_time')->nullable();
                    $table->date('actual_return_date')->nullable();
                    $table->time('actual_return_time')->nullable();
                    $table->string('status')->default('borrowed');
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {}
    }

    public function index(Request $request)
    {
        $this->ensureSchemaExists();

        $locations = CommodityLocation::orderBy('name')->get();

        // Relasi disesuaikan dengan Commodity.php (commodity_location)
        $query = Commodity::query()->with(['commodity_location', 'itemLendings' => function ($q) {
            $q->where('status', 'borrowed')->latest();
        }]);

        // Filter Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('item_code', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Lokasi
        if ($request->filled('location_id')) {
            $query->where('commodity_location_id', $request->location_id);
        }

        $commodities = $query->paginate(10);

        return view('item-lendings.index', compact('commodities', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'commodity_id' => 'required',
            'user_name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
        ]);

        $commodity = Commodity::findOrFail($request->commodity_id);
        // Fallback: jika lokasi input kosong, ambil dari data barang
        $origin_location_id = $request->origin_location_id ?? $commodity->commodity_location_id;

        // Cek konflik
        $isBorrowed = ItemLending::where('commodity_id', $commodity->id)
            ->where('status', 'borrowed')
            ->exists();

        if ($isBorrowed) {
            return back()->with('error', 'Barang ini sedang dipinjam.');
        }

        ItemLending::create([
            'commodity_id' => $commodity->id,
            'user_name' => $request->user_name,
            'origin_location_id' => $origin_location_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'borrowed',
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Peminjaman berhasil.');
    }

    public function returnItem(Request $request, $id)
    {
        $request->validate([
            'return_location_id' => 'required',
            'actual_return_date' => 'required|date',
            'actual_return_time' => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {
            $lending = ItemLending::findOrFail($id);

            // 1. Update status peminjaman
            $lending->update([
                'status' => 'returned',
                'return_location_id' => $request->return_location_id,
                'actual_return_date' => $request->actual_return_date,
                'actual_return_time' => $request->actual_return_time,
            ]);

            // 2. Update lokasi fisik barang di tabel commodities (Source of Truth)
            $lending->commodity->update([
                'commodity_location_id' => $request->return_location_id
            ]);
        });

        return back()->with('success', 'Barang dikembalikan & lokasi diperbarui.');
    }

    public function history($commodity_id)
    {
        $histories = ItemLending::with(['originLocation', 'returnLocation'])
            ->where('commodity_id', $commodity_id)
            ->latest()
            ->get()
            ->map(function ($item) {
            // Formatting Tanggal & Jam
            $item->formatted_start_date = Carbon::parse($item->start_date)->format('d-m-Y');
            $item->formatted_return_date = $item->actual_return_date ?Carbon::parse($item->actual_return_date)->format('d-m-Y') : '-';
            $item->start_time = Carbon::parse($item->start_time)->format('H:i');
            $item->actual_return_time = $item->actual_return_time ?Carbon::parse($item->actual_return_time)->format('H:i') : '';

            // Logika Status & Label
            if ($item->status == 'borrowed') {
                $item->status_label = 'Sedang Dipinjam';
                $item->status_class = 'badge-warning';
                $item->location_label = optional($item->originLocation)->name ?? '-';
                $item->location_type = 'Asal';
            }
            else {
                $item->status_label = 'Dikembalikan';
                $item->status_class = 'badge-success';
                $item->location_label = optional($item->returnLocation)->name ?? '-';
                $item->location_type = 'Kembali ke';
            }
            return $item;
        });

        return response()->json($histories);
    }

    // API Helper untuk cek ketersediaan (opsional)
    public function checkAvailability()
    {
        return response()->json(['status' => 'ok']);
    }
}