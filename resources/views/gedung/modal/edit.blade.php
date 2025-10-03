<div class="modal fade" id="gedung_edit_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Ubah Data Gedung</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="gedung_id"> <!-- Tambahkan ini -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name">Nama Gedung</label>
                                <input type="text" class="form-control" name="name" id="name">
                            </div>
                            <div class="form-group">
                                <label for="kode">Kode</label>
                                <input type="text" class="form-control" name="kode" id="kode">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success">Ubah</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
