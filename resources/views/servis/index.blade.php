{{-- resources/views/servis/index.blade.php --}}
<x-layout>
    <x-slot name="title">Servis Barang</x-slot>
    <x-slot name="page_heading">Halaman Servis Barang</x-slot>

    <div class="card">
        <div class="card-body">
            @include('utilities.alert')

            {{-- Form Pencarian --}}
            <form action="{{ route('servis.search') }}" method="POST" class="mb-4">
                @csrf
                <div class="form-group">
                    <label for="item_code">Kode Barang</label>
                    <input type="text" name="item_code" class="form-control" id="item_code"
                        placeholder="Masukkan kode barang..." required>
                </div>
                <button type="submit" class="btn btn-primary mt-2">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>

            {{-- Barang yang sedang diservis --}}
            @if (empty($barang) && $barangServis->count())
                <h5 class="mt-4">Barang Sedang Diservis</h5>
                <x-datatable>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Lokasi (Gedung)</th>
                            <th>Letak Barang (Ruang)</th>
                            <th>Tanggal Diservis</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barangServis as $index => $b)
                            @php
                                $latestService = $b->services->first();
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $b->item_code }}</td>
                                <td>{{ $b->name }}</td>
                                <td>{{ optional(optional($b->commodity_location)->gedung)->name ?? '-' }}</td>
                                <td>{{ optional($b->commodity_location)->name ?? '-' }}</td>
                                <td>{{ $latestService->tanggal_diservis->format('d-m-Y') }}</td>
                                <td>{{ $latestService->tanggal_selesai ? $latestService->tanggal_selesai->format('d-m-Y') : '-' }}</td>
                                <td>{{ $latestService->status }}</td>
                                <td>
                                    @if ($latestService->status != 'Selesai')
                                        <form action="{{ route('servis.finish', $latestService->id) }}" method="POST" class="finish-service-form">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Selesai
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-datatable>
            @endif

            {{-- Hasil pencarian barang --}}
            @if (!empty($barang))
                <x-datatable>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Lokasi (Gedung)</th>
                            <th>Letak Barang (Ruang)</th>
                            <th>Tanggal Diservis</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $latestService = $barang->services->first() ?? null;
                        @endphp
                        <tr>
                            <td>1</td>
                            <td>{{ $barang->item_code }}</td>
                            <td>{{ $barang->name }}</td>
                            <td>{{ optional(optional($barang->commodity_location)->gedung)->name ?? '-' }}</td>
                            <td>{{ optional($barang->commodity_location)->name ?? '-' }}</td>
                            <td>{{ $latestService ? $latestService->tanggal_diservis->format('d-m-Y') : '-' }}</td>
                            <td>{{ $latestService && $latestService->tanggal_selesai ? $latestService->tanggal_selesai->format('d-m-Y') : '-' }}</td>
                            <td>{{ $latestService ? $latestService->status : 'Baik' }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#history_modal">
                                        <i class="fas fa-history"></i> History
                                    </button>
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#servis_barang_modal">
                                        <i class="fas fa-tools"></i> Servis
                                    </button>
                                    @if ($latestService && $latestService->status != 'Selesai')
                                        <form action="{{ route('servis.finish', $latestService->id) }}" method="POST" class="finish-service-form">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm ml-1">
                                                <i class="fas fa-check"></i> Selesai
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </x-datatable>
            @endif
        </div>
    </div>

    {{-- Modal Servis --}}
    @if (!empty($barang))
        @push('modal')
            <div class="modal fade" id="servis_barang_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form method="POST" action="{{ route('servis.store') }}" class="modal-content">
                        @csrf
                        <input type="hidden" name="commodity_id" value="{{ $barang->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title">Form Servis Barang</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Kode:</strong> {{ $barang->item_code }}</p>
                            <p><strong>Nama:</strong> {{ $barang->name }}</p>
                            <p><strong>Lokasi:</strong> {{ optional(optional($barang->commodity_location)->gedung)->name ?? '-' }}</p>
                            <p><strong>Letak:</strong> {{ optional($barang->commodity_location)->name ?? '-' }}</p>

                            <div class="form-group">
                                <label>Tanggal Diservis</label>
                                <input type="date" name="tanggal_diservis" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Kerusakan</label>
                                <textarea name="kerusakan" class="form-control" maxlength="300" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Perbaikan</label>
                                <textarea name="perbaikan" class="form-control" maxlength="500" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Selesai Servis (Opsional)</label>
                                <input type="date" name="tanggal_selesai" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                </div>
            </div>
        @endpush

        {{-- Modal History --}}
        @push('modal')
            <div class="modal fade" id="history_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Riwayat Servis</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if ($barang->services->count())
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal Servis</th>
                                            <th>Kerusakan</th>
                                            <th>Perbaikan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($barang->services as $servis)
                                            <tr>
                                                <td>{{ $servis->tanggal_diservis->format('d-m-Y') }}</td>
                                                <td>{{ $servis->kerusakan }}</td>
                                                <td>{{ $servis->perbaikan }}</td>
                                                <td>{{ $servis->status }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>Tidak ada riwayat servis.</p>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endpush
    @endif

    @push('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Konfirmasi sebelum menandai selesai
            const finishForms = document.querySelectorAll('.finish-service-form');
            finishForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Penyelesaian',
                        text: "Apakah barang sudah selesai diservis?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Selesai!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
    @endpush
</x-layout>