{{-- resources/views/stock-opname/modal/scan.blade.php --}}
<div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-labelledby="scanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanModalLabel">Scan QR Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="reader" style="width:100%;"></div>
            
                <div class="mt-3">
                    <!-- Hasil teks scan (kalau mau lihat QR hasil scan dalam teks) -->
                    <input type="text" id="scanResultInput" class="form-control" placeholder="Hasil Scan" readonly>
            
                    <!-- Tempat untuk alert success/error -->
                    <div id="scanMessage" class="mt-3"></div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" id="stopScan" class="btn btn-danger">Stop Scan</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
