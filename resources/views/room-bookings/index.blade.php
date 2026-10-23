<x-layout>
    <x-slot name="title">Jadwal & Peminjaman Ruangan</x-slot>
    <x-slot name="page_heading">Jadwal & Peminjaman Ruangan</x-slot>

    {{-- Alert Notifikasi --}}
    @include('utilities.alert')

    @php
        $carbonDate = \Carbon\Carbon::parse($selectedDate);
        $prevDate = $carbonDate->copy()->subDay()->format('Y-m-d');
        $nextDate = $carbonDate->copy()->addDay()->format('Y-m-d');
        $isToday = $carbonDate->isToday();
    @endphp

    {{-- Top Action & Date Navigator --}}
    <div class="row align-items-center mb-4">
        <div class="col-lg-7 col-md-12 mb-3 mb-lg-0">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="btn-group shadow-sm mr-2" role="group">
                    <a href="{{ route('room-bookings.index', array_merge(request()->all(), ['date' => $prevDate])) }}" class="btn btn-outline-primary" title="Hari Sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="{{ route('room-bookings.index', array_merge(request()->all(), ['date' => date('Y-m-d')])) }}" class="btn {{ $isToday ? 'btn-primary' : 'btn-outline-primary' }}">
                        Hari Ini
                    </a>
                    <a href="{{ route('room-bookings.index', array_merge(request()->all(), ['date' => $nextDate])) }}" class="btn btn-outline-primary" title="Hari Berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>

                <div class="d-inline-flex align-items-center bg-white px-3 py-2 rounded shadow-sm border">
                    <i class="fas fa-calendar-day text-primary mr-2"></i>
                    <span class="font-weight-bold text-dark">
                        {{ $carbonDate->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </span>
                    @if($isToday)
                        <span class="badge badge-success ml-2 font-weight-normal">Hari Ini</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-md-12 text-lg-right">
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalBooking">
                <i class="fas fa-plus-circle mr-1"></i> Booking Ruangan Baru
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card card-statistic-1 modern-stat-card">
                <div class="card-icon bg-primary shadow-primary">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Ruangan</h4>
                    </div>
                    <div class="card-body">
                        {{ $allRooms->count() }}
                        <small class="text-muted d-block font-weight-normal" style="font-size: 0.72rem;">Ruangan Terdaftar</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12">
            <div class="card card-statistic-1 modern-stat-card">
                <div class="card-icon bg-danger shadow-danger">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Ruangan Terpakai</h4>
                    </div>
                    <div class="card-body text-danger">
                        {{ $totalRoomsUsed }}
                        <small class="text-muted d-block font-weight-normal" style="font-size: 0.72rem;">Memiliki Jadwal Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12">
            <div class="card card-statistic-1 modern-stat-card">
                <div class="card-icon bg-success shadow-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Ruangan Kosong</h4>
                    </div>
                    <div class="card-body text-success">
                        {{ $totalRoomsAvailable }}
                        <small class="text-muted d-block font-weight-normal" style="font-size: 0.72rem;">Tersedia untuk Digunakan</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12">
            <div class="card card-statistic-1 modern-stat-card">
                <div class="card-icon bg-info shadow-info">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Agenda</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalBookingsToday }}
                        <small class="text-muted d-block font-weight-normal" style="font-size: 0.72rem;">Kegiatan Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card modern-card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('room-bookings.index') }}" class="row align-items-end">
                <div class="col-md-5 mb-2 mb-md-0">
                    <label class="font-weight-bold text-muted small text-uppercase mb-1">Ganti Tanggal Jadwal</label>
                    <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-5 mb-2 mb-md-0">
                    <label class="font-weight-bold text-muted small text-uppercase mb-1">Filter Ruangan Spesifik</label>
                    <select name="room_id" class="form-control" onchange="this.form.submit()">
                        <option value="">— Tampilkan Semua Ruangan ({{ $allRooms->count() }}) —</option>
                        @foreach($allRooms as $r)
                            <option value="{{ $r->id }}" {{ $selectedRoomId == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-right">
                    <a href="{{ route('room-bookings.index') }}" class="btn btn-light btn-block" title="Reset Filter">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabs View: Timeline Matrix & List View --}}
    <div class="card modern-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 border-bottom">
            <ul class="nav nav-pills" id="bookingTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="grid-tab" data-toggle="tab" href="#tabGrid" role="tab" aria-selected="true">
                        <i class="fas fa-th mr-1"></i> Matriks Jadwal (Jam 07:00 - 21:00)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="list-tab" data-toggle="tab" href="#tabList" role="tab" aria-selected="false">
                        <i class="fas fa-list mr-1"></i> Daftar Kegiatan Hari Ini ({{ $bookings->count() }})
                    </a>
                </li>
            </ul>

            <div class="d-none d-md-flex align-items-center small">
                <span class="badge badge-success px-2 py-1 mr-2"><i class="fas fa-circle mr-1" style="font-size: 8px;"></i> Kosong</span>
                <span class="badge badge-danger px-2 py-1"><i class="fas fa-circle mr-1" style="font-size: 8px;"></i> Terpakai / Booking</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="bookingTabContent">
                {{-- TAB 1: GRID MATRIX --}}
                <div class="tab-pane fade show active" id="tabGrid" role="tabpanel">
                    @if($rooms->count() > 0)
                        <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                            <table class="table table-bordered text-center table-sm mb-0">
                                <thead>
                                    <tr class="bg-primary text-white" style="position: sticky; top: 0; z-index: 20;">
                                        <th style="width: 85px; position: sticky; left: 0; z-index: 30;" class="bg-primary text-white font-weight-bold">
                                            Waktu
                                        </th>
                                        @foreach($rooms as $room)
                                            <th style="min-width: 170px; max-width: 220px;" class="py-2">
                                                <div class="font-weight-bold" style="font-size: 0.9rem;">{{ $room->name }}</div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hours as $time)
                                        @php
                                            $curr = \Carbon\Carbon::parse($time);
                                            $nextHour = $curr->copy()->addHour()->format('H:i');
                                        @endphp
                                        <tr>
                                            {{-- Kolom Jam Sticky --}}
                                            <td class="font-weight-bold bg-light align-middle border-right" style="position: sticky; left: 0; z-index: 10; font-size: 0.85rem;">
                                                <span class="text-primary">{{ $time }}</span>
                                                <div class="text-muted small font-weight-normal" style="font-size: 0.68rem;">s/d {{ $nextHour }}</div>
                                            </td>

                                            {{-- Kolom Setiap Ruangan --}}
                                            @foreach($rooms as $room)
                                                @php
                                                    $booking = $bookings->where('commodity_location_id', $room->id)->filter(function($b) use ($time) {
                                                        $currTime = \Carbon\Carbon::parse($time);
                                                        $start = \Carbon\Carbon::parse($b->start_time);
                                                        $end = \Carbon\Carbon::parse($b->end_time);
                                                        return $currTime->gte($start) && $currTime->lt($end);
                                                    })->first();
                                                @endphp

                                                @if($booking)
                                                    <td class="bg-danger text-white p-2 align-middle border" style="background-color: #fee2e2 !important; color: #991b1b !important;">
                                                        <div class="p-1 rounded bg-white shadow-xs border text-left" style="font-size: 0.78rem; line-height: 1.3; border-left: 3px solid #ef4444 !important;">
                                                            <div class="font-weight-bold text-dark text-truncate" title="{{ $booking->user_name }}">
                                                                <i class="fas fa-user mr-1 text-danger"></i> {{ $booking->user_name }}
                                                            </div>
                                                            <div class="text-muted text-truncate" title="{{ $booking->purpose }}">
                                                                <i class="fas fa-tag mr-1 text-muted"></i> {{ $booking->purpose }}
                                                            </div>
                                                            <div class="small font-weight-bold text-danger mt-1">
                                                                <i class="far fa-clock mr-1"></i> {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="p-2 align-middle text-muted" style="background-color: #ffffff; min-height: 48px;">
                                                        <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.7rem; opacity: 0.6;">
                                                            Tersedia
                                                        </span>
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info m-4">
                            <i class="fas fa-info-circle mr-1"></i> Tidak ada data ruangan yang ditemukan. Silakan periksa master data ruangan.
                        </div>
                    @endif
                </div>

                {{-- TAB 2: LIST VIEW --}}
                <div class="tab-pane fade" id="tabList" role="tabpanel">
                    <div class="p-3">
                        @if($bookings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="bg-light">
                                        <tr class="text-uppercase small">
                                            <th>#</th>
                                            <th>Ruangan</th>
                                            <th>Peminjam / Penanggung Jawab</th>
                                            <th>Kegunaan / Acara</th>
                                            <th>Jam Peminjaman</th>
                                            <th>Tanggal</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookings as $b)
                                            <tr>
                                                <td class="align-middle font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                                <td class="align-middle font-weight-bold text-primary">
                                                    <i class="fas fa-door-open mr-1"></i>
                                                    {{ optional($b->room)->name ?? 'Ruangan #' . $b->commodity_location_id }}
                                                </td>
                                                <td class="align-middle font-weight-bold text-dark">
                                                    {{ $b->user_name }}
                                                </td>
                                                <td class="align-middle text-muted">
                                                    {{ $b->purpose ?? '—' }}
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-primary px-2 py-1 font-family-monospace">
                                                        {{ substr($b->start_time, 0, 5) }} &ndash; {{ substr($b->end_time, 0, 5) }} WIB
                                                    </span>
                                                </td>
                                                <td class="align-middle small text-muted">
                                                    {{ \Carbon\Carbon::parse($b->start_date)->format('d/m/Y') }}
                                                    @if($b->end_date && $b->end_date != $b->start_date)
                                                        s/d {{ \Carbon\Carbon::parse($b->end_date)->format('d/m/Y') }}
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">
                                                    <form action="{{ route('room-bookings.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan/menghapus jadwal booking ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus / Batalkan Booking">
                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-check fa-3x mb-3 text-muted" style="opacity: 0.4;"></i>
                                <h6 class="font-weight-bold">Tidak ada agenda ruangan pada tanggal ini</h6>
                                <p class="small text-muted mb-3">Semua ruangan kosong dan dapat dibooking.</p>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalBooking">
                                    <i class="fas fa-plus mr-1"></i> Booking Sekarang
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Booking Ruangan --}}
    @push('modal')
    <div class="modal fade" id="modalBooking" tabindex="-1" role="dialog" aria-labelledby="modalBookingLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('room-bookings.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold" id="modalBookingLabel">
                            <i class="fas fa-calendar-plus mr-2"></i> Form Booking Ruangan
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small text-uppercase">Pilih Ruangan <span class="text-danger">*</span></label>
                            <select name="commodity_location_id" class="form-control" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($allRooms as $r)
                                    <option value="{{ $r->id }}" {{ $selectedRoomId == $r->id ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small text-uppercase">Nama Peminjam / Penanggung Jawab <span class="text-danger">*</span></label>
                            <input type="text" name="user_name" class="form-control" required placeholder="Contoh: Panitia Paskah / Seksi Koor" value="{{ old('user_name') }}">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small text-uppercase">Kegunaan / Nama Acara <span class="text-danger">*</span></label>
                            <input type="text" name="purpose" class="form-control" required placeholder="Contoh: Rapat Pleno / Latihan Paduan Suara" value="{{ old('purpose') }}">
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark small text-uppercase">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $selectedDate }}" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark small text-uppercase">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $selectedDate }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark small text-uppercase">Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time" name="start_time" class="form-control font-weight-bold" value="08:00" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark small text-uppercase">Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time" name="end_time" class="form-control font-weight-bold" value="10:00" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark small text-uppercase">Catatan / Keterangan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan fasilitas tambahan, proyektor, sound, dll.">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary font-weight-bold">
                            <i class="fas fa-check-circle mr-1"></i> Simpan Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endpush
</x-layout>