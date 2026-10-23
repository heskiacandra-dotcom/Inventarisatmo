<x-layout>
    <x-slot name="title">Laporan Penyusutan Aset Tetap</x-slot>
    <x-slot name="page_heading">Laporan Penyusutan Aset Tetap</x-slot>

    {{-- Alert Notifikasi --}}
    @include('utilities.alert')

    {{-- Action Bar --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <p class="text-muted mb-0">
                Laporan perhitungan depresiasi aset tetap menggunakan <strong>Metode Garis Lurus (Straight-Line Depreciation)</strong> secara otomatis berdasarkan waktu perolehan.
            </p>
        </div>
        <div class="col-md-5 text-md-right mt-2 mt-md-0">
            <button type="button" class="btn btn-outline-info mr-2 shadow-sm" data-toggle="modal" data-target="#modalRumus">
                <i class="fas fa-info-circle mr-1"></i> Rumus Perhitungan
            </button>
            <a href="{{ route('laporan.penyusutan.export') }}" class="btn btn-success shadow-sm">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
        </div>
    </div>

    {{-- KPI Cards / Summary Stats --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card card-statistic-1 modern-stat-card">
                <div class="card-icon bg-primary shadow-primary">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Aset Aktif</h4>
                    </div>
                    <div class="card-body">
                        {{ number_format($totalAset, 0, ',', '.') }}
                        <small class="text-muted d-block font-weight-normal" style="font-size: 0.72rem;">Unit Barang Terdaftar</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12">
            <div class="card card-statistic-1 modern-stat-card">
                <div class="card-icon bg-info shadow-info">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Nilai Perolehan</h4>
                    </div>
                    <div class="card-body" style="font-size: 1.25rem;">
                        Rp {{ number_format($totalHargaPerolehan, 0, ',', '.') }}
                        <small class="text-muted d-block font-weight-normal" style="font-size: 0.72rem;">Harga Beli Awal Aset</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12">
            <div class="card card-statistic-1 modern-stat-card">
                <div class="card-icon bg-warning shadow-warning">
                    <i class="fas fa-chart-line-down"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Akumulasi Penyusutan</h4>
                    </div>
                    <div class="card-body" style="font-size: 1.25rem;">
                        Rp {{ number_format($totalAkumulasiPenyusutan, 0, ',', '.') }}
                        <small class="text-muted d-block font-weight-normal" style="font-size: 0.72rem;">Total Penurunan Nilai</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12">
            <div class="card card-statistic-1 modern-stat-card">
                <div class="card-icon bg-success shadow-success">
                    <i class="fas fa-vault"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Nilai Buku</h4>
                    </div>
                    <div class="card-body text-success" style="font-size: 1.25rem;">
                        Rp {{ number_format($totalNilaiBuku, 0, ',', '.') }}
                        <small class="text-muted d-block font-weight-normal" style="font-size: 0.72rem;">Sisa Nilai Aset Saat Ini</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card modern-card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('laporan.penyusutan.index') }}" class="row align-items-end">
                <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                    <label class="font-weight-bold text-muted small text-uppercase mb-1">Cari Aset</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Kode / Nama / Merk..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                    <label class="font-weight-bold text-muted small text-uppercase mb-1">Lokasi / Ruangan</label>
                    <select name="location_id" class="form-control" onchange="this.form.submit()">
                        <option value="">— Semua Lokasi / Ruangan —</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                    <label class="font-weight-bold text-muted small text-uppercase mb-1">Jenis Aset</label>
                    <select name="jenis_barang_id" class="form-control" onchange="this.form.submit()">
                        <option value="">— Semua Jenis Aset —</option>
                        @foreach($jenisBarangList as $jb)
                            <option value="{{ $jb->id }}" {{ request('jenis_barang_id') == $jb->id ? 'selected' : '' }}>
                                {{ $jb->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 mb-2 mb-lg-0">
                    <label class="font-weight-bold text-muted small text-uppercase mb-1">Kondisi</label>
                    <select name="condition" class="form-control" onchange="this.form.submit()">
                        <option value="">— Semua Kondisi —</option>
                        <option value="1" {{ request('condition') == '1' ? 'selected' : '' }}>Baik</option>
                        <option value="2" {{ request('condition') == '2' ? 'selected' : '' }}>Rusak</option>
                        <option value="3" {{ request('condition') == '3' ? 'selected' : '' }}>Belum di SO</option>
                    </select>
                </div>

                <div class="col-lg-1 col-md-2 mb-2 mb-lg-0 text-right">
                    <a href="{{ route('laporan.penyusutan.index') }}" class="btn btn-light btn-block" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Laporan Penyusutan --}}
    <div class="card modern-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
            <h5 class="mb-0 text-dark font-weight-bold">
                <i class="fas fa-table mr-2 text-primary"></i> Rincian Penyusutan Barang
            </h5>
            <span class="badge badge-light border text-muted px-3 py-2">
                Menampilkan <strong>{{ $commodities->count() }}</strong> Aset
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablePenyusutan">
                    <thead class="bg-light">
                        <tr class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.04em;">
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Kode & Nama Barang</th>
                            <th>Lokasi & Jenis</th>
                            <th class="text-right">Harga Perolehan</th>
                            <th class="text-center">Umur & Residu</th>
                            <th class="text-right">Penyusutan / Bln</th>
                            <th>Akumulasi Penyusutan</th>
                            <th class="text-right">Nilai Buku</th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($commodities as $c)
                            @php
                                $persen = $c->persentase_penyusutan;
                                $progressColor = $persen >= 100 ? 'bg-danger' : ($persen > 70 ? 'bg-warning' : 'bg-primary');
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold text-muted align-middle">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-primary-light mb-1 font-family-monospace">
                                        {{ $c->item_code ?? 'BELUM ADA KODE' }}
                                    </span>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                        {{ $c->name }}
                                    </div>
                                    <small class="text-muted">
                                        Merk: {{ $c->brand ?? '—' }} | Tgl Beli: {{ $c->tanggal_perolehan_parsed->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td class="align-middle">
                                    <div class="text-dark">
                                        <i class="fas fa-map-marker-alt text-danger mr-1 small"></i>
                                        {{ optional($c->commodity_location)->name ?? '—' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ optional($c->jenisBarang)->name ?? 'Aset Tetap' }}
                                    </small>
                                </td>
                                <td class="text-right align-middle font-weight-bold text-dark font-family-monospace">
                                    Rp {{ number_format($c->harga_perolehan, 0, ',', '.') }}
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-light border text-dark font-weight-bold">
                                        {{ $c->umur_manfaat_bulan }} Bln
                                    </span>
                                    <small class="d-block text-muted mt-1 font-family-monospace">
                                        Residu: Rp {{ number_format($c->nilai_residu_value, 0, ',', '.') }}
                                    </small>
                                </td>
                                <td class="text-right align-middle font-family-monospace text-muted">
                                    Rp {{ number_format($c->penyusutan_per_bulan, 0, ',', '.') }}
                                </td>
                                <td class="align-middle" style="min-width: 170px;">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="font-weight-bold text-dark">
                                            Rp {{ number_format($c->akumulasi_penyusutan, 0, ',', '.') }}
                                        </span>
                                        <span class="text-muted font-weight-bold">{{ $persen }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; border-radius: 4px;">
                                        <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $persen }}%" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        Berjalan: {{ $c->bulan_berjalan }} dari {{ $c->umur_manfaat_bulan }} bln
                                    </small>
                                </td>
                                <td class="text-right align-middle font-weight-bold font-family-monospace text-success" style="font-size: 0.95rem;">
                                    Rp {{ number_format($c->nilai_buku, 0, ',', '.') }}
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-parameter"
                                        title="Ubah Parameter Penyusutan"
                                        data-id="{{ $c->id }}"
                                        data-name="{{ $c->name }}"
                                        data-code="{{ $c->item_code }}"
                                        data-price="{{ $c->harga_perolehan }}"
                                        data-umur="{{ $c->umur_manfaat_bulan }}"
                                        data-residu="{{ $c->nilai_residu_value }}"
                                        data-date="{{ $c->tanggal_perolehan_parsed->format('Y-m-d') }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 text-muted" style="opacity: 0.4;"></i>
                                    <h6 class="font-weight-bold">Tidak ada data aset ditemukan</h6>
                                    <p class="small text-muted mb-0">Coba ubah filter pencarian Anda atau periksa data master barang.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Rumus & Informasi --}}
    @push('modal')
    <div class="modal fade" id="modalRumus" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-calculator mr-2"></i> Metode Penyusutan Garis Lurus
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="font-weight-bold text-dark">Formula Perhitungan:</h6>
                    <div class="p-3 bg-light rounded text-center my-3 border font-family-monospace font-weight-bold text-primary">
                        Penyusutan / Bulan = (Harga Perolehan - Nilai Residu) / Umur Manfaat (Bulan)
                    </div>
                    <ul class="text-muted small pl-3 mb-3">
                        <li class="mb-1"><strong>Harga Perolehan:</strong> Harga awal beli aset / barang.</li>
                        <li class="mb-1"><strong>Nilai Residu:</strong> Estimasi nilai sisa aset setelah masa pakai berakhir (default Rp 0).</li>
                        <li class="mb-1"><strong>Umur Manfaat:</strong> Masa estimasi pakai aset dalam satuan bulan (default 60 bulan = 5 tahun).</li>
                        <li class="mb-1"><strong>Akumulasi Penyusutan:</strong> Total depresiasi yang telah berjalan sejak perolehan (maksimal sebesar Harga Perolehan - Nilai Residu).</li>
                        <li class="mb-1"><strong>Nilai Buku:</strong> Nilai aset saat ini = Harga Perolehan - Akumulasi Penyusutan.</li>
                    </ul>
                    <div class="alert alert-info py-2 px-3 mb-0 small">
                        <i class="fas fa-info-circle mr-1"></i> Perhitungan berjalan otomatis setiap bulan tanpa perlu intervensi manual.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Parameter Penyusutan --}}
    <div class="modal fade" id="modalEditParameter" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <form id="formEditParameter" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-sliders-h mr-2"></i> Pengaturan Parameter Aset
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-light border mb-3 py-2">
                            <div class="small text-muted">Kode & Nama Aset:</div>
                            <div class="font-weight-bold text-dark" id="modalCommodityName">-</div>
                            <div class="small font-family-monospace text-primary" id="modalCommodityCode">-</div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small text-uppercase">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" id="modalTanggalPerolehan" class="form-control" required>
                            <small class="text-muted">Tanggal kapan aset mulai digunakan / dibeli.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small text-uppercase">Umur Manfaat (Bulan)</label>
                            <div class="input-group">
                                <input type="number" name="umur_manfaat" id="modalUmurManfaat" min="1" max="600" class="form-control font-weight-bold" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">Bulan</span>
                                </div>
                            </div>
                            <small class="text-muted">Contoh: 60 bulan (5 tahun), 48 bulan (4 tahun), dsb.</small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark small text-uppercase">Nilai Residu (Sisa) Rp</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="nilai_residu" id="modalNilaiResidu" min="0" step="1000" class="form-control font-weight-bold" value="0">
                            </div>
                            <small class="text-muted">Perkiraan nilai sisa aset setelah habis umur manfaat (isi 0 jika tidak ada).</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endpush

    @push('js')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable jika lebih dari 10 data
            if ($('#tablePenyusutan tbody tr').length > 5) {
                $('#tablePenyusutan').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                    },
                    "pageLength": 25,
                    "order": []
                });
            }

            // Handler tombol edit parameter
            $('.btn-edit-parameter').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var code = $(this).data('code');
                var umur = $(this).data('umur');
                var residu = $(this).data('residu');
                var date = $(this).data('date');

                $('#modalCommodityName').text(name);
                $('#modalCommodityCode').text(code || '—');
                $('#modalTanggalPerolehan').val(date);
                $('#modalUmurManfaat').val(umur);
                $('#modalNilaiResidu').val(residu);

                var actionUrl = "{{ url('/laporan/penyusutan') }}/" + id + "/parameter";
                $('#formEditParameter').attr('action', actionUrl);

                $('#modalEditParameter').modal('show');
            });
        });
    </script>
    @endpush
</x-layout>
