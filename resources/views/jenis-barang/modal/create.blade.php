{{-- resources/views/jenis_barang/modal/create.blade.php --}}
<div class="modal fade" id="jenis_barang_create_modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Tambah Jenis Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <i class="text-warning fa-solid fa-circle-info mr-2"></i>
                    <p class="font-italic mb-0">
                        Kolom dengan tanda <span class="font-weight-bold">*</span> wajib diisi.
                    </p>
                </div>
                <hr>
                <form action="{{ route('jenis-barang.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Kode<span class="text-danger">*</span></label>
                                <input type="text" name="kode" class="form-control @error('kode', 'store') is-invalid @enderror"
                                       maxlength="5" value="{{ old('kode') }}" required>
                                @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Nama<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name', 'store') is-invalid @enderror"
                                       maxlength="50" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>