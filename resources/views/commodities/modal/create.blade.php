<div class="modal fade" id="commodity_create_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Tambah Data Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <i class="text-warning fa-solid fa-circle-info mr-2"></i>
                    <p class="font-italic mb-0">
                        Kolom yang memiliki tanda merah <span class="font-weight-bold">wajib diisi.</span>
                    </p>
                </div>
                <hr>
                <form action="{{ route('barang.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="item_code">Kode Barang<span
                                        class="font-weight-bold text-danger">*</span></label>
                                <input type="text" class="form-control" value="Akan digenerate otomatis" readonly>
                                <small class="form-text text-muted">
                                    Kode barang akan digenerate otomatis
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="item_number">Nomor Barang<span
                                        class="font-weight-bold text-danger">*</span></label>
                                <input type="text" class="form-control" value="Akan digenerate otomatis" readonly>
                                <small class="form-text text-muted">
                                    Nomor barang akan digenerate otomatis berdasarkan lokasi
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="name">Nama Barang<span
                                        class="font-weight-bold text-danger">*</span></label>
                                <input type="text" class="form-control @error('name', 'store') is-invalid @enderror"
                                    name="name" id="name" value="{{ old('name') }}"
                                    placeholder="Masukan nama barang..">
                                @error('name', 'store')
                                    <div class="d-block invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="gedung_id">Peruntukan Barang<span class="font-weight-bold text-danger">*</span></label>
                                <select class="form-control @error('gedung_id', 'store') is-invalid @enderror"
                                    id="gedung_id" name="gedung_id" required>
                                    <option value="">Pilih..</option>
                                    @foreach ($gedung as $g)
                                        <option value="{{ $g->id }}" @selected(old('gedung_id') == $g->id)>
                                            {{ $g->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gedung_id', 'store')
                                    <div class="d-block invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="commodity_location_id">Lokasi Barang<span
                                        class="font-weight-bold text-danger">*</span></label>
                                <select
                                    class="form-control @error('commodity_location_id', 'store') is-invalid @enderror"
                                    name="commodity_location_id" id="commodity_location_id" style="width: 100%;">
                                    <option value="" selected>Pilih..</option>
                                    @foreach ($commodity_locations as $commodity_location)
                                        <option value="{{ $commodity_location->id }}" @selected(old('commodity_location_id') == $commodity_location->id)>
                                            {{ $commodity_location->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('commodity_location_id', 'store')
                                    <div class="d-block invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="jenis_barang_id">Jenis Aset Tetap<span
                                        class="font-weight-bold text-danger">*</span></label>
                                <select class="form-control @error('jenis_barang_id', 'store') is-invalid @enderror"
                                    name="jenis_barang_id" id="jenis_barang_id" style="width: 100%;">
                                    <option value="" selected>Pilih..</option>
                                    @foreach ($jenisBarang as $jenis)
                                        <option value="{{ $jenis->id }}" @selected(old('jenis_barang_id') == $jenis->id)>
                                            {{ $jenis->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_barang_id', 'store')
                                    <div class="d-block invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="material">Bahan</label>
                                <input type="text"
                                    class="form-control @error('material', 'store') is-invalid @enderror"
                                    name="material" id="material" value="{{ old('material') }}"
                                    placeholder="Masukan bahan barang..">
                                @error('material', 'store')
                                    <div class="d-block invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="brand">Merek</label>
                                <input type="text"
                                    class="form-control @error('brand', 'store') is-invalid @enderror" name="brand"
                                    id="brand" value="{{ old('brand') }}" placeholder="Masukan merek barang..">
                                @error('brand', 'store')
                                    <div class="d-block invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-12">
    <div class="form-group">
        <label>Tanggal Pembelian<span class="font-weight-bold text-danger">*</span></label>

        {{-- Toggle Tombol --}}
        <div class="btn-group btn-group-sm d-flex mb-2" role="group">
            <button type="button" class="btn btn-outline-primary active" id="btn_mode_full" onclick="setDateMode('full')">
                <i class="fas fa-calendar-alt mr-1"></i> Tanggal Lengkap
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btn_mode_year" onclick="setDateMode('year')">
                <i class="fas fa-calendar mr-1"></i> Tahun Saja
            </button>
        </div>

        {{-- Input Tanggal Lengkap --}}
        <div id="input_date_full">
            <input type="date" 
                class="form-control @error('date_of_purchase', 'store') is-invalid @enderror"
                id="date_of_purchase_full"
                name="date_of_purchase" 
                value="{{ old('date_of_purchase') && strlen(old('date_of_purchase')) > 4 ? old('date_of_purchase') : '' }}">
            <small class="form-text text-muted">Format: BB/HH/TTTT</small>
        </div>

        {{-- Input Tahun Saja --}}
        <div id="input_date_year" style="display:none;">
            <input type="number" 
                class="form-control @error('date_of_purchase', 'store') is-invalid @enderror"
                id="date_of_purchase_year"
                name="date_of_purchase" 
                value="{{ old('date_of_purchase') && strlen(old('date_of_purchase')) == 4 ? old('date_of_purchase') : '' }}"
                placeholder="Contoh: 2024" min="1900" max="{{ date('Y') }}" disabled> {{-- Default disabled --}}
            <small class="form-text text-muted">Masukkan tahun pembelian (4 digit)</small>
        </div>

        {{-- ERROR FEEDBACK (Cukup satu saja karena name-nya sama) --}}
        @error('date_of_purchase', 'store')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
        @enderror
        
        {{-- HAPUS INPUT HIDDEN / KOTAK KUNING ANDA DISINI --}}
    </div>
</div>
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for="commodity_acquisition_id">Asal Perolehan<span
                                        class="font-weight-bold text-danger">*</span></label>
                                <select
                                    class="form-control @error('commodity_acquisition_id', 'store') is-invalid @enderror"
                                    name="commodity_acquisition_id" id="commodity_acquisition_id"
                                    style="width: 100%;">
                                    <option value="" selected">Pilih..</option>
                                    @foreach ($commodity_acquisitions as $commodity_acquisition)
                                        <option value="{{ $commodity_acquisition->id }}" @selected(old('commodity_acquisition_id') == $commodity_acquisition->id)>
                                            {{ $commodity_acquisition->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('commodity_acquisition_id', 'store')
                                    <div class="d-block invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for="condition">Kondisi<span
                                        class="font-weight-bold text-danger">*</span></label>
                                <select class="form-control @error('condition', 'store') is-invalid @enderror"
                                    name="condition" id="condition" style="width: 100%;">
                                    <option value="" selected>Pilih..</option>
                                    <option value="1" @selected(old('condition') == 1)>Baik</option>
                                    <option value="2" @selected(old('condition') == 2)>Rusak</option>
                                </select>
                                @error('condition', 'store')
                                    <div class="d-block invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="quantity">Kuantitas<span
                                        class="font-weight-bold text-danger">*</span></label>
                                <input type="number"
                                    class="form-control @error('quantity', 'store') is-invalid @enderror"
                                    name="quantity" id="quantity" value="{{ old('quantity') }}"
                                    placeholder="Masukan kuantitas barang..">
                                @error('quantity', 'store')
                                    <div class="d-block invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="form-group">
                                <label for="price">Harga</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">Rp.</span>
                                    </div>
                                    <input type="number"
                                        class="form-control @error('price', 'store') is-invalid @enderror"
                                        name="price" id="price" value="{{ old('price') }}"
                                        placeholder="Masukan harga barang..">
                                    @error('price', 'store')
                                        <div class="d-block invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="form-group">
                                <label for="price_per_item">Harga Satuan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">Rp.</span>
                                    </div>
                                    <input type="number"
                                        class="form-control @error('price_per_item', 'store') is-invalid @enderror"
                                        name="price_per_item" id="price_per_item"
                                        value="{{ old('price_per_item') }}"
                                        placeholder="Masukan harga satuan barang..">
                                    @error('price_per_item', 'store')
                                        <div class="d-block invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="note">Keterangan <span class="font-italic">(opsional)</span></label>
                                <textarea class="form-control @error('note', 'store') is-invalid @enderror" name="note" id="note"
                                    style="height: 100px;" placeholder="Masukan keterangan (opsional)..">{{ old('note') }}</textarea>
                                @error('note', 'store')
                                    <div class="d-block invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Logika Ganti Mode
        window.setDateMode = function(mode) {
            var fullDiv = $('#input_date_full');
            var yearDiv = $('#input_date_year');
            
            var inputFull = $('#date_of_purchase_full');
            var inputYear = $('#date_of_purchase_year');

            var btnFull = $('#btn_mode_full');
            var btnYear = $('#btn_mode_year');

            if (mode === 'full') {
                // Tampilan
                fullDiv.show();
                yearDiv.hide();
                
                // Button Style
                btnFull.addClass('active btn-outline-primary').removeClass('btn-outline-secondary');
                btnYear.removeClass('active btn-outline-primary').addClass('btn-outline-secondary');
                
                // LOGIKA PENTING: Enable tanggal, Disable tahun
                inputFull.prop('disabled', false); // Aktifkan input tanggal
                inputYear.prop('disabled', true);  // Matikan input tahun (tidak akan dikirim ke server)
                
            } else {
                // Tampilan
                fullDiv.hide();
                yearDiv.show();

                // Button Style
                btnYear.addClass('active btn-outline-primary').removeClass('btn-outline-secondary');
                btnFull.removeClass('active btn-outline-primary').addClass('btn-outline-secondary');

                // LOGIKA PENTING: Enable tahun, Disable tanggal
                inputYear.prop('disabled', false); // Aktifkan input tahun
                inputFull.prop('disabled', true);  // Matikan input tanggal
            }
        }

        // Cek Error Laravel saat halaman reload
        @if ($errors->store->any())
            $('#commodity_create_modal').modal('show');
        @endif

        // Inisialisasi Awal berdasarkan Old Data
        @if(old('date_of_purchase'))
            var oldVal = "{{ old('date_of_purchase') }}";
            if (/^\d{4}$/.test(oldVal)) {
                setDateMode('year');
            } else {
                setDateMode('full');
            }
        @else
            // Default Start
            setDateMode('full');
        @endif
        
        // Validasi Client-Side agar modal tidak tutup jika kosong
        $('#commodity_create_modal form').on('submit', function(e) {
            var mode = $('#input_date_full').is(':visible') ? 'full' : 'year';
            var val = mode === 'full' ? $('#date_of_purchase_full').val() : $('#date_of_purchase_year').val();
            
            if(!val) {
                e.preventDefault(); // Jangan submit
                alert('Silakan isi tanggal atau tahun pembelian!');
                return false;
            }
        });
    });
</script>