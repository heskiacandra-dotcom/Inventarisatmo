<?php

namespace App\Http\Controllers;

use App\CommodityLocation;
use App\RoomBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomBookingController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input tanggal (Default hari ini)
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        // 2. Ambil input filter ruangan (Opsional)
        $selectedRoomId = $request->get('room_id');

        // 3. Query Ruangan
        // Jika ada filter ruangan, ambil satu saja. Jika tidak, ambil semua.
        $rooms = CommodityLocation::query()
            ->when($selectedRoomId, function($q) use ($selectedRoomId) {
                $q->where('id', $selectedRoomId);
            })
            ->orderBy('name', 'ASC')
            ->get();

        // 4. Ambil data booking untuk tanggal tersebut
        // Kita filter booking yang 'room_id'-nya ada di dalam list $rooms di atas
        $bookings = RoomBooking::whereDate('start_date', '<=', $selectedDate)
                    ->whereDate('end_date', '>=', $selectedDate)
                    ->whereIn('commodity_location_id', $rooms->pluck('id'))
                    ->get();

        // 5. Generate Jam (07:00 - 21:00)
        $hours = [];
        for ($i = 7; $i <= 21; $i++) {
            $hours[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
        }

        // Kirim data tambahan $allRooms untuk isi dropdown filter
        $allRooms = CommodityLocation::orderBy('name', 'ASC')->get();

        return view('room-bookings.index', compact(
            'rooms', 
            'allRooms', 
            'hours', 
            'selectedDate', 
            'bookings', 
            'selectedRoomId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'commodity_location_id' => 'required',
            'user_name' => 'required',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        // Cek Bentrok Jadwal
        // Logika: (StartA < EndB) and (EndA > StartB)
        $conflict = RoomBooking::where('commodity_location_id', $request->commodity_location_id)
            ->whereDate('start_date', $request->start_date)
            ->where(function($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function($sub) use ($request){
                      $sub->where('start_time', '<', $request->start_time)
                          ->where('end_time', '>', $request->end_time);
                  });
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Gagal: Ruangan sudah terpakai di jam tersebut.');
        }

        RoomBooking::create($request->all());

        return back()->with('success', 'Booking berhasil disimpan.');
    }

    public function checkAvailability() 
    { 
        return response()->json(['status' => 'ok']); 
    }
}