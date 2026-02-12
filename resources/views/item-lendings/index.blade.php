<x-layout>
    <x-slot name="title">Peminjaman Barang</x-slot>

    <div class="section-header">
        <h1>Peminjaman Barang</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Filter & Cari</h4>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Cari Barang..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="location_id" class="form-control">
                            <option value="">Semua Lokasi</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Kode</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Peminjam (Aktif)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commodities as $item)
                        @php
                            $activeLoan = $item->itemLendings->where('status', 'borrowed')->first();
                        @endphp
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->commodity_location->name ?? '-' }}</td>
                            <td>
                                @if($activeLoan)
                                    <span class="badge badge-warning">Dipinjam</span>
                                @else
                                    <span class="badge badge-success">Tersedia</span>
                                @endif
                            </td>
                            <td>{{ $activeLoan ? $activeLoan->user_name : '-' }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-history" data-id="{{ $item->id }}">History</button>
                                
                                @if($activeLoan)
                                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalReturn-{{ $activeLoan->id }}">
                                        Kembalikan
                                    </button>
                                    
                                    {{-- Modal Return Inline --}}
                                    @push('modal')
                                    <div class="modal fade" id="modalReturn-{{ $activeLoan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form action="{{ route('item-lendings.return', $activeLoan->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Kembalikan: {{ $item->name }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Lokasi Pengembalian</label>
                                                            <select name="return_location_id" class="form-control" required>
                                                                @foreach($locations as $loc)
                                                                    <option value="{{ $loc->id }}" {{ $activeLoan->origin_location_id == $loc->id ? 'selected' : '' }}>
                                                                        {{ $loc->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <label>Tgl Kembali</label>
                                                                <input type="date" name="actual_return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                            </div>
                                                            <div class="col-6">
                                                                <label>Jam</label>
                                                                <input type="time" name="actual_return_time" class="form-control" value="{{ date('H:i') }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success">Proses</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endpush

                                @else
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalBorrow-{{ $item->id }}">
                                        Pinjam
                                    </button>

                                    {{-- Modal Borrow Inline --}}
                                    @push('modal')
                                    <div class="modal fade" id="modalBorrow-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form action="{{ route('item-lendings.store') }}" method="POST">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Pinjam: {{ $item->name }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="commodity_id" value="{{ $item->id }}">
                                                        <div class="form-group">
                                                            <label>Lokasi Asal</label>
                                                            <select name="origin_location_id" class="form-control" readonly style="pointer-events:none; background:#eee;">
                                                                <option value="{{ $item->commodity_location_id }}">
                                                                    {{ $item->commodity_location->name ?? '-' }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Peminjam</label>
                                                            <input type="text" name="user_name" class="form-control" required placeholder="Contoh: Panitia Paskah">
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6"><label>Tgl Mulai</label><input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                                                            <div class="col-6"><label>Tgl Selesai</label><input type="date" name="end_date" class="form-control"></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-6"><label>Jam Mulai</label><input type="time" name="start_time" class="form-control" value="{{ date('H:i') }}" required></div>
                                                            <div class="col-6"><label>Jam Selesai</label><input type="time" name="end_time" class="form-control"></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endpush
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-2">
                {{ $commodities->withQueryString()->links() }}
            </div>
        </div>
    </div>

    {{-- Modal History Placeholder --}}
    @push('modal')
    <div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Riwayat Peminjaman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="historyContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('js')
    <script>
        $(document).on('click', '.btn-history', function() {
            var id = $(this).data('id');
            var btn = $(this);
            btn.prop('disabled', true).text('Loading...');
            
            $.get('/peminjaman-barang/'+id+'/history', function(data) {
                let html = '<table class="table table-bordered table-sm text-center"><thead class="bg-light"><tr><th>Peminjam</th><th>Waktu Pinjam</th><th>Waktu Kembali</th><th>Lokasi</th><th>Status</th></tr></thead><tbody>';
                
                if(data.length === 0) {
                    html += '<tr><td colspan="5">Belum ada riwayat.</td></tr>';
                } else {
                    data.forEach(function(row) {
                        html += `<tr>
                            <td class="font-weight-bold">${row.user_name}</td>
                            <td>${row.formatted_start_date}<br><small>${row.start_time}</small></td>
                            <td>${row.formatted_return_date}<br><small>${row.actual_return_time}</small></td>
                            <td><small>${row.location_type}</small><br><strong>${row.location_label}</strong></td>
                            <td><span class="badge ${row.status_class}">${row.status_label}</span></td>
                        </tr>`;
                    });
                }
                html += '</tbody></table>';
                $('#historyContent').html(html);
                $('#historyModal').modal('show');
            }).always(function() {
                btn.prop('disabled', false).text('History');
            });
        });
    </script>
    @endpush
</x-layout>