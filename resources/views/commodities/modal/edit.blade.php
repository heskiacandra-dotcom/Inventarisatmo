<div class="modal fade" id="edit_commodity" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Ubah Data Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                {{-- form action dikosongkan, akan diisi via jQuery --}}
                <form method="POST" action="">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="item_code">Kode Barang</label>
                                <input type="text" class="form-control" name="item_code" id="item_code" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="item_number">Nomor Barang</label>
                                <input type="text" class="form-control" name="item_number" id="item_number" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="name">Nama Barang<span class="font-weight-bold text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Masukan nama barang..">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="gedung_id">Peruntukan Barang<span class="font-weight-bold text-danger">*</span></label>
                                <select class="form-control" name="gedung_id" id="gedung_id" required>
                                    <option value="">Pilih Gedung</option>
                                    @foreach ($gedung as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="commodity_location_id">Lokasi Barang<span class="font-weight-bold text-danger">*</span></label>
                                <select class="form-control" name="commodity_location_id" id="commodity_location_id" style="width: 100%;">
                                    <option value="">Pilih..</option>
                                    @foreach ($commodity_locations as $commodity_location)
                                        <option value="{{ $commodity_location->id }}">{{ $commodity_location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="jenis_barang_id">Jenis Aset Tetap<span class="font-weight-bold text-danger">*</span></label>
                                <select class="form-control" name="jenis_barang_id" id="jenis_barang_id" required>
                                    <option value="">Pilih Jenis Barang</option>
                                    @foreach ($jenisBarang as $jb)
                                        <option value="{{ $jb->id }}">{{ $jb->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="material">Bahan</label>
                                <input type="text" class="form-control" name="material" id="material"
                                    placeholder="Masukan bahan barang..">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="brand">Merek</label>
                                <input type="text" class="form-control" name="brand" id="brand"
                                    placeholder="Masukan merek barang..">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for="date_of_purchase">Tanggal Pembelian<span class="font-weight-bold text-danger">*</span></label>
                                <input type="text" class="form-control" name="date_of_purchase" id="date_of_purchase">
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for="commodity_acquisition_id">Asal Perolehan<span class="font-weight-bold text-danger">*</span></label>
                                <select class="form-control" name="commodity_acquisition_id" id="commodity_acquisition_id" style="width: 100%;">
                                    <option value="">Pilih..</option>
                                    @foreach ($commodity_acquisitions as $commodity_acquisition)
                                        <option value="{{ $commodity_acquisition->id }}">{{ $commodity_acquisition->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for="condition">Kondisi<span class="font-weight-bold text-danger">*</span></label>
                                <select class="form-control" name="condition" id="condition" style="width: 100%;">
                                    <option value="">Pilih..</option>
                                    <option value="1">Baik</option>
                                    <option value="2">Rusak</option>
                                    <option value="3">Belum di SO</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="quantity">Kuantitas<span class="font-weight-bold text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="quantity"
                                    placeholder="Masukan kuantitas barang..">
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="form-group">
                                <label for="price">Harga<span class="font-weight-bold text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="number" class="form-control" name="price" id="price"
                                        placeholder="Masukan harga barang..">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="form-group">
                                <label for="price_per_item">Harga Satuan<span class="font-weight-bold text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="number" class="form-control" name="price_per_item" id="price_per_item"
                                        placeholder="Masukan harga satuan barang..">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="note">Keterangan <span class="font-italic">(opsional)</span></label>
                                <textarea class="form-control" name="note" id="note" style="height: 100px;"
                                    placeholder="Masukan keterangan (opsional).."></textarea>
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
