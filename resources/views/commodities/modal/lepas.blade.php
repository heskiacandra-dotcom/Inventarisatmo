{{-- Modal: Lepas Barang --}}
<div class="modal fade" id="lepas_commodity" tabindex="-1" role="dialog" aria-labelledby="lepasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="lepasModalLabel">
                    <i class="fas fa-fw fa-sign-out-alt mr-2"></i> Lepas Barang
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="lepas_form" method="POST" action="">
                @csrf

                <div class="modal-body">
                    {{-- Info Barang --}}
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Barang yang dilepas <strong>tidak akan dihapus dari database</strong>, namun tidak akan muncul lagi di daftar barang aktif.
                    </div>

                    <div class="form-group">
                        <label><strong>Nama Barang</strong></label>
                        <p id="lepas_nama_barang" class="form-control-plaintext font-weight-bold text-dark border-bottom pb-1">—</p>
                    </div>

                    <div class="form-group">
                        <label for="lepas_tanggal_pelepasan">Tanggal Pelepasan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pelepasan" id="lepas_tanggal_pelepasan"
                            class="form-control" required
                            value="{{ now()->format('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label for="lepas_metode_pelepasan">Metode Pelepasan <span class="text-danger">*</span></label>
                        <select name="metode_pelepasan" id="lepas_metode_pelepasan" class="form-control" required>
                            <option value="" disabled selected>— Pilih metode pelepasan —</option>
                            <option value="Dijual">Dijual</option>
                            <option value="Dihapusbukukan (Rusak Total)">Dihapusbukukan (Rusak Total)</option>
                            <option value="Hilang">Hilang</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lepas_catatan_pelepasan">Catatan <span class="text-muted">(Opsional)</span></label>
                        <textarea name="catatan_pelepasan" id="lepas_catatan_pelepasan"
                            class="form-control" rows="3"
                            placeholder="Catatan tambahan mengenai pelepasan barang ini..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="fas fa-sign-out-alt mr-1"></i> Lepas Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
