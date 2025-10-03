{{-- resources/views/stock-opname/modal/input-stock.blade.php --}}
<div class="modal fade" id="inputStockModal" tabindex="-1" role="dialog" aria-labelledby="inputStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="inputStockModalLabel">Input Stock Barang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table class="table table-borderless">
            <tr>
              <th>Kode Barang</th>
              <td id="modalItemCode"></td>
            </tr>
            <tr>
              <th>Nama Barang</th>
              <td id="modalItemName"></td>
            </tr>
            <tr>
              <th>Stok Sistem</th>
              <td id="modalSystemStock"></td>
            </tr>
            <tr>
              <th>Stok Fisik</th>
              <td>
                <input type="number" id="modalPhysicalQuantity" class="form-control" min="0">
              </td>
            </tr>
            <tr>
              <th>Kondisi</th>
              <td>
                <select id="modalCondition" class="form-control">
                  <option value="1">Baik</option>
                  <option value="2">Rusak</option>
                  <option value="3">Belum di SO</option>
                </select>
              </td>
            </tr>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="saveStockButton">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
  