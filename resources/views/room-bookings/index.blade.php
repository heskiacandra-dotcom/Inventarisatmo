<x-layout>
    <x-slot name="title">Jadwal Ruangan</x-slot>

    <div class="section-header">
        <h1>Jadwal Peminjaman Ruangan</h1>
        <div class="section-header-breadcrumb">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalBooking">
                <i class="fas fa-plus"></i> Tambah Peminjaman
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Form Filter --}}
            <form method="GET" class="row mb-4">
                <div class="col-md-4">
                    <label>Pilih Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-4">
                    <label>Filter Ruangan (Opsional)</label>
                    <select name="room_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Tampilkan Semua Ruangan --</option>
                        @foreach($allRooms as $r)
                            <option value="{{ $r->id }}" {{ $selectedRoomId == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('room-bookings.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            {{-- Tabel Jadwal --}}
            @if($rooms->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered text-center table-sm">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th style="width: 80px; position: sticky; left: 0; z-index: 10;" class="bg-primary">Jam</th>
                            @foreach($rooms as $room)
                                <th style="min-width: 150px;">{{ $room->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hours as $time)
                        <tr>
                            {{-- Kolom Jam --}}
                            <td class="font-weight-bold bg-light" style="position: sticky; left: 0;">{{ $time }}</td>
                            
                            {{-- Kolom Ruangan --}}
                            @foreach($rooms as $room)
                                @php
                                    // Cek apakah ada booking di ruangan ini pada jam ini
                                    $booking = $bookings->where('commodity_location_id', $room->id)->filter(function($b) use ($time) {
                                        $curr = \Carbon\Carbon::parse($time);
                                        $start = \Carbon\Carbon::parse($b->start_time);
                                        $end = \Carbon\Carbon::parse($b->end_time);
                                        
                                        // Tampilkan jika jam saat ini >= start DAN jam saat ini < end
                                        // (contoh: booking 07:00-09:00, maka jam 07:00 dan 08:00 akan berwarna)
                                        return $curr->gte($start) && $curr->lt($end);
                                    })->first();
                                @endphp

                                <td class="{{ $booking ? 'bg-danger text-white' : '' }} p-1 align-middle">
                                    @if($booking)
                                        <div style="font-size:11px; line-height:1.2;">
                                            <strong>{{ $booking->user_name }}</strong>
                                            <hr class="m-1 border-white" style="opacity: 0.3">
                                            <span style="opacity: 0.9">{{ $booking->purpose }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size:10px;">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="alert alert-info">
                    Tidak ada data ruangan yang ditemukan. Pastikan data master ruangan sudah diinput.
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Booking --}}
    @push('modal')
    <div class="modal fade" id="modalBooking" tabindex="-1" role="dialog" aria-labelledby="modalBookingLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('room-bookings.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalBookingLabel">Booking Ruangan Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih Ruangan</label>
                            <select name="commodity_location_id" class="form-control" required>
                                @foreach($allRooms as $r)
                                    <option value="{{ $r->id }}" {{ $selectedRoomId == $r->id ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama Peminjam / Penanggung Jawab</label>
                            <input type="text" name="user_name" class="form-control" required placeholder="Contoh: Panitia Paskah">
                        </div>
                        <div class="form-group">
                            <label>Kegunaan / Acara</label>
                            <input type="text" name="purpose" class="form-control" required placeholder="Contoh: Rapat Pleno">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-6">
                                <label>Tanggal Selesai</label>
                                <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <label>Jam Mulai</label>
                                <input type="time" name="start_time" class="form-control" value="08:00" required>
                            </div>
                            <div class="col-6">
                                <label>Jam Selesai</label>
                                <input type="time" name="end_time" class="form-control" value="10:00" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Booking</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endpush
</x-layout>