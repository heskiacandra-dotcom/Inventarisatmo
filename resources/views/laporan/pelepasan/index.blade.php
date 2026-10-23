<x-layout>
    <x-slot name="title">Laporan Barang Dilepas</x-slot>
    <x-slot name="page_heading">Laporan Barang Dilepas</x-slot>

    <div class="card">
        <div class="card-body">
            @include('utilities.alert')

            {{-- Filter --}}
            <x-filter>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="metode_pelepasan">Metode Pelepasan:</label>
                            <select name="metode_pelepasan" id="metode_pelepasan"
                                @class(['form-control','is-valid' => request()->filled('metode_pelepasan')])>
                                <option value="">— Semua Metode —</option>
                                @foreach ($metodeOptions as $metode)
                                    <option value="{{ $metode }}" @selected(request('metode_pelepasan') == $metode)>
                                        {{ $metode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dari_tanggal">Dari Tanggal:</label>
                            <input type="date" name="dari_tanggal" id="dari_tanggal"
                                @class(['form-control','is-valid' => request()->filled('dari_tanggal')])
                                value="{{ request('dari_tanggal') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sampai_tanggal">Sampai Tanggal:</label>
                            <input type="date" name="sampai_tanggal" id="sampai_tanggal"
                                @class(['form-control','is-valid' => request()->filled('sampai_tanggal')])
                                value="{{ request('sampai_tanggal') }}">
                        </div>
                    </div>
                </div>
                <x-slot name="resetFilterURL">{{ route('laporan.pelepasan.index') }}</x-slot>
            </x-filter>

            {{-- Summary Card --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning shadow-warning">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>Total Barang Dilepas</h4></div>
                            <div class="card-body">{{ $barangDilepas->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success shadow-success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>Dijual</h4></div>
                            <div class="card-body">{{ $barangDilepas->where('metode_pelepasan', 'Dijual')->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger shadow-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>Rusak / Hilang</h4></div>
                            <div class="card-body">{{ $barangDilepas->whereIn('metode_pelepasan', ['Dihapusbukukan (Rusak Total)', 'Hilang'])->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="row">
                <div class="col-lg-12">
                    <x-datatable>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Kode Barang</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Lokasi Terakhir</th>
                                <th scope="col">Asal Perolehan</th>
                                <th scope="col">Tanggal Pelepasan</th>
                                <th scope="col">Metode Pelepasan</th>
                                <th scope="col">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($barangDilepas as $barang)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>
                                        <span class="badge badge-secondary">{{ $barang->item_code }}</span>
                                    </td>
                                    <td>{{ $barang->name }}</td>
                                    <td>{{ $barang->commodity_location->name ?? '—' }}</td>
                                    <td>{{ $barang->commodity_acquisition->name ?? '—' }}</td>
                                    <td>
                                        {{ $barang->tanggal_pelepasan
                                            ? $barang->tanggal_pelepasan->format('d-m-Y')
                                            : '—' }}
                                    </td>
                                    <td>
                                        @php
                                            $metode = $barang->metode_pelepasan;
                                            $badge = match($metode) {
                                                'Dijual'                           => 'badge-success',
                                                'Dihapusbukukan (Rusak Total)'     => 'badge-danger',
                                                'Hilang'                           => 'badge-warning',
                                                default                            => 'badge-secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-pill {{ $badge }}">{{ $metode ?? '—' }}</span>
                                    </td>
                                    <td class="text-muted" style="max-width:200px;">
                                        <small>{{ $barang->catatan_pelepasan ?? '—' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                        Belum ada barang yang dilepas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </x-datatable>
                </div>
            </div>

        </div>
    </div>
</x-layout>
