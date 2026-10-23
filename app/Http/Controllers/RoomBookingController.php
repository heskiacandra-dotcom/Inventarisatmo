<?php

namespace App\Http\Controllers;

use App\CommodityLocation;
use App\RoomBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class RoomBookingController extends Controller
{
    /**
     * Self-healing: pastikan tabel room_bookings otomatis ada jika belum termigrasi
     */
    private function ensureSchemaExists(): void
    {
        try {
            if (!Schema::hasTable('room_bookings')) {
                Schema::create('room_bookings', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('commodity_location_id')->index();
                    $table->string('user_name');
                    $table->string('purpose')->nullable();
                    $table->date('start_date');
                    $table->date('end_date')->nullable();
                    $table->time('start_time');
                    $table->time('end_time');
                    $table->string('status')->default('booked');
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            // Silently ignore if creation is restricted
        }
    }

    public function index(Request $request)
    {
        $this->ensureSchemaExists();

        // 1. Ambil input tanggal (Default hari ini)
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));

        // 2. Ambil input filter ruangan (Opsional)
        $selectedRoomId = $request->get('room_id');

        // 3. Query Ruangan
        $rooms = CommodityLocation::query()
            ->when($selectedRoomId, function ($q) use ($selectedRoomId) {
                $q->where('id', $selectedRoomId);
            })
            ->orderBy('name', 'ASC')
            ->get();

        // 4. Ambil data booking untuk tanggal tersebut secara aman
        $bookings = collect();
        if (Schema::hasTable('room_bookings') && $rooms->isNotEmpty()) {
            $bookings = RoomBooking::where(function ($q) use ($selectedDate) {
                $q->where(function ($sub) use ($selectedDate) {
                    $sub->whereDate('start_date', '<=', $selectedDate)
                        ->where(function ($endSub) use ($selectedDate) {
                            $endSub->whereDate('end_date', '>=', $selectedDate)
                                   ->orWhereNull('end_date');
                        });
                })->orWhereDate('start_date', $selectedDate);
            })
            ->whereIn('commodity_location_id', $rooms->pluck('id'))
            ->orderBy('start_time', 'ASC')
            ->get();
        }

        // 5. Generate Jam (07:00 - 21:00)
        $hours = [];
        for ($i = 7; $i <= 21; $i++) {
            $hours[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
        }

        // Dropdown semua ruangan untuk filter
        $allRooms = CommodityLocation::orderBy('name', 'ASC')->get();

        // Statistik ringkas hari ini
        $totalBookingsToday = $bookings->count();
        $bookedRoomIds = $bookings->pluck('commodity_location_id')->unique();
        $totalRoomsUsed = $bookedRoomIds->count();
        $totalRoomsAvailable = max(0, $allRooms->count() - $totalRoomsUsed);

        return view('room-bookings.index', compact(
            'rooms',
            'allRooms',
            'hours',
            'selectedDate',
            'bookings',
            'selectedRoomId',
            'totalBookingsToday',
            'totalRoomsUsed',
            'totalRoomsAvailable'
        ));
    }

    public function store(Request $request)
    {
        $this->ensureSchemaExists();

        $request->validate([
            'commodity_location_id' => 'required|exists:commodity_locations,id',
            'user_name' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date ?: $startDate;
        $startTime = Carbon::parse($request->start_time)->format('H:i:s');
        $endTime = Carbon::parse($request->end_time)->format('H:i:s');

        if ($endTime <= $startTime) {
            return back()->with('error', 'Jam selesai harus lebih akhir dari jam mulai!')->withInput();
        }

        // Cek Bentrok Jadwal
        $conflict = RoomBooking::where('commodity_location_id', $request->commodity_location_id)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                  });
            })
            ->where(function ($q) use ($startTime, $endTime) {
                // (StartA < EndB) and (EndA > StartB)
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Gagal: Ruangan sudah terpakai di rentang tanggal dan jam tersebut. Silakan pilih waktu lain.')->withInput();
        }

        RoomBooking::create([
            'commodity_location_id' => $request->commodity_location_id,
            'user_name' => $request->user_name,
            'purpose' => $request->purpose,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'booked',
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Jadwal peminjaman ruangan berhasil disimpan!');
    }

    public function destroy($id)
    {
        $this->ensureSchemaExists();

        $booking = RoomBooking::findOrFail($id);
        $booking->delete();

        return back()->with('success', 'Jadwal peminjaman berhasil dibatalkan/dihapus.');
    }

    public function checkAvailability()
    {
        return response()->json(['status' => 'ok']);
    }
}