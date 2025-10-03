{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Paroki Keluarga Kudus Atmodirono</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .title {
            font-weight: bold;
            text-align: center;
            flex-grow: 1;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>
    <div class="container py-4">

        <!-- Header -->
        <div class="header">
            <h1 class="title">Sistem Inventaris Paroki Keluarga Kudus Atmodirono</h1>
            <a href="{{ route('login') }}" class="btn btn-outline-primary">Login</a>
        </div>

        <!-- Form Pencarian -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8">
                <div class="card p-4 shadow-sm">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Cari commodities (ID / kode barang)...">
                        <button class="btn btn-primary" id="searchBtn">Cari</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Scan -->
        <div class="d-flex justify-content-center gap-3">
            <button class="btn btn-success shadow-sm" id="scanBtn">📷 Mulai Scan</button>
        </div>

    </div>

    <!-- Modal Detail Commodity -->
    <div class="modal fade" id="commodityModal" tabindex="-1" aria-labelledby="commodityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="commodityDetail">
                    <!-- detail commodity akan dimuat dengan AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Scanner -->
    <div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Scan QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <video id="preview" style="width: 100%;"></video>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script>
        // Format angka ke Rupiah
        function formatRupiah(angka) {
            if (!angka) return '-';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(angka);
        }

        // Ambil detail commodity dari API lalu tampilkan di modal
        function loadCommodityDetail(codeOrId) {
            fetch(`/barang/code/${codeOrId}`)
                .then(res => res.json())
                .then(response => {
                    if (!response.success) {
                        alert(response.message || "Data tidak ditemukan");
                        return;
                    }

                    let data = response.data;
                    document.getElementById('commodityDetail').innerHTML = `
    <table class="table table-bordered">
        <tr><th>Kode</th><td>${data.item_code}</td></tr>
        <tr><th>Nama</th><td>${data.name}</td></tr>
        <tr><th>Lokasi</th><td>${data.commodity_location?.name ?? '-'}</td></tr>
        <tr><th>Bahan</th><td>${data.material ?? '-'}</td></tr>
        <tr><th>Merk</th><td>${data.brand ?? '-'}</td></tr>
        <tr><th>Tanggal Perolehan</th><td>${data.date_of_purchase ?? '-'}</td></tr>
        <tr><th>Kondisi</th><td>${data.condition_name ?? '-'}</td></tr>
        <tr><th>Cara Perolehan</th><td>${data.commodity_acquisition?.name ?? '-'}</td></tr>
        <tr><th>Catatan</th><td>${data.note ?? '-'}</td></tr>
        <tr><th>Jumlah</th><td>${data.quantity ?? 0}</td></tr>
    </table>
`;

                    new bootstrap.Modal(document.getElementById('commodityModal')).show();
                })
                .catch(err => {
                    console.error(err);
                    alert("Terjadi kesalahan saat mengambil data.");
                });
        }

        // Pencarian manual (pakai ID atau kode barang)
        document.getElementById('searchBtn').addEventListener('click', function() {
            let keyword = document.getElementById('searchInput').value.trim();
            if (!keyword) return;
            loadCommodityDetail(keyword);
        });

        // Scanner setup
        let scanner = null;

        document.getElementById('scanBtn').addEventListener('click', function() {
            let scanModal = new bootstrap.Modal(document.getElementById('scanModal'));
            scanModal.show();

            if (!scanner) {
                scanner = new Instascan.Scanner({
                    video: document.getElementById('preview')
                });
                scanner.addListener('scan', function(content) {
                    scanModal.hide();
                    loadCommodityDetail(content);
                });
            }

            Instascan.Camera.getCameras().then(function(cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    alert('Tidak ada kamera terdeteksi.');
                }
            });

            // Stop kamera saat modal ditutup
            document.getElementById('scanModal').addEventListener('hidden.bs.modal', function() {
                if (scanner) {
                    scanner.stop();
                }
            }, {
                once: true
            });
        });
    </script>
</body>

</html>
