<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Inventaris Paroki Atmodirono</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode@latest/html5-qrcode.min.js"></script>

    <style>
        body { background: #f8f9fa; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: system-ui, -apple-system, sans-serif; padding: 1rem; }
        .main-container { background: #fff; padding: 1.5rem; border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15); width: 100%; max-width: 600px; }
        .header { display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid #dee2e6; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .logo { height: 48px; flex-shrink: 0; }
        .title { font-size: 1.25rem; font-weight: 700; margin: 0; flex: 1; line-height: 1.2; }
        #reader { width: 100%; border-radius: 8px; overflow: hidden; }
        #reportSection { display: none; background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px solid #ffeeba; }
        @media (max-width: 576px) {
            .header { flex-direction: column; text-align: center; }
            .login-btn, .scan-btn { width: 100%; }
        }
    </style>
</head>

<body>

<div class="main-container">
    <div class="header">
        <img src="{{ asset('assets/img/logo.png') }}" class="logo" alt="Logo">
        <h1 class="title">Cek & Lapor Aset<br><small class="text-muted fw-normal" style="font-size: 0.8em">Paroki Atmodirono</small></h1>
        <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm login-btn">Login Petugas</a>
    </div>

    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body">
            <label class="form-label fw-bold small text-uppercase text-muted">Pencarian Manual</label>
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Kode Barang (mis: A001)">
                <button class="btn btn-primary" id="searchBtn"><i class="fas fa-search"></i> Cari</button>
            </div>
        </div>
    </div>

    <div class="d-grid">
        <button id="scanBtn" class="btn btn-success btn-lg scan-btn py-3">
            <i class="fas fa-qrcode fa-lg me-2"></i> Scan QR Code
        </button>
    </div>
</div>

<div class="modal fade" id="commodityModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-box-open me-2"></i>Detail Aset</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="loadingDetail" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>

                <div id="detailContent"></div>

                <div id="laporToggleBtn" class="d-grid gap-2 mt-3 d-none">
                    <button class="btn btn-warning fw-bold" onclick="toggleReportForm()">
                        <i class="fas fa-map-marker-alt me-1"></i> Lokasi Tidak Sesuai? Laporkan
                    </button>
                </div>

                <div id="reportSection">
                    <h6 class="fw-bold mb-3 text-warning-emphasis border-bottom border-warning pb-2">
                        <i class="fas fa-bullhorn me-1"></i> Lapor Pindah Lokasi
                    </h6>
                    <form id="reportForm">
                        <input type="hidden" id="report_commodity_id">
                        
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Lokasi Sebenarnya (Saat ini):</label>
                            <select id="report_new_location" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Pelapor:</label>
                            <input type="text" id="report_name" class="form-control form-control-sm" placeholder="Nama Anda (Umat/Petugas)" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-sm btn-light" onclick="toggleReportForm()">Batal</button>
                            <button type="submit" class="btn btn-sm btn-dark">Kirim Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="scanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Arahkan Kamera</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-dark">
                <div id="reader"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let html5Qrcode = null;
    const detailModal = new bootstrap.Modal(document.getElementById('commodityModal'));
    const scanModalEl = document.getElementById('scanModal');
    const scanModal = new bootstrap.Modal(scanModalEl);

    // --- 1. Load Detail ---
    function loadCommodityDetail(code) {
        // UI Reset
        document.getElementById('loadingDetail').classList.remove('d-none');
        document.getElementById('detailContent').innerHTML = '';
        document.getElementById('laporToggleBtn').classList.add('d-none');
        document.getElementById('reportSection').style.display = 'none';
        document.getElementById('reportForm').reset();
        
        detailModal.show();

        fetch(`/api/public/barang/${code}`)
            .then(res => res.json())
            .then(res => {
                document.getElementById('loadingDetail').classList.add('d-none');
                
                if (!res.success) {
                    document.getElementById('detailContent').innerHTML = 
                        `<div class="alert alert-danger text-center mb-0"><i class="fas fa-exclamation-triangle me-2"></i>${res.message}</div>`;
                    return;
                }

                const d = res.data;
                document.getElementById('report_commodity_id').value = d.id;

                // Render Table
                document.getElementById('detailContent').innerHTML = `
                    <table class="table table-bordered table-sm mb-0">
                        <tr><th class="bg-light w-25">Kode</th><td class="font-monospace fw-bold text-primary">${d.item_code}</td></tr>
                        <tr><th class="bg-light">Barang</th><td class="fw-bold">${d.name}</td></tr>
                        <tr><th class="bg-light">Lokasi</th><td><span class="badge bg-info text-dark"><i class="fas fa-map-pin me-1"></i>${d.location}</span></td></tr>
                        <tr><th class="bg-light">Merk</th><td>${d.brand || '-'}</td></tr>
                        <tr><th class="bg-light">Kondisi</th><td>${d.condition_name}</td></tr>
                    </table>
                `;
                
                // Show Report Button
                document.getElementById('laporToggleBtn').classList.remove('d-none');
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loadingDetail').classList.add('d-none');
                document.getElementById('detailContent').innerHTML = '<div class="alert alert-danger">Gagal menghubungi server.</div>';
            });
    }

    // --- 2. Report Logic ---
    function toggleReportForm() {
        const section = document.getElementById("reportSection");
        if (section.style.display === "none" || section.style.display === "") {
            section.style.display = "block";
            section.scrollIntoView({ behavior: 'smooth' });
        } else {
            section.style.display = "none";
        }
    }

    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        const payload = {
            commodity_id: document.getElementById('report_commodity_id').value,
            new_location_id: document.getElementById('report_new_location').value,
            reporter_name: document.getElementById('report_name').value,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        fetch('/api/public/lapor-lokasi', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': payload._token },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                alert("✅ " + res.message);
                // Reload data to reflect change
                // Kita ambil kode barang dari tabel yang sedang tampil untuk reload
                const currentCode = document.querySelector('#detailContent .font-monospace').innerText;
                loadCommodityDetail(currentCode);
            } else {
                alert("⚠️ " + res.message);
            }
        })
        .catch(err => alert("❌ Terjadi kesalahan jaringan."))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // --- 3. Event Listeners ---
    document.getElementById('searchBtn').addEventListener('click', () => {
        const val = document.getElementById('searchInput').value.trim();
        if(val) loadCommodityDetail(val);
    });

    document.getElementById('scanBtn').addEventListener('click', () => {
        scanModal.show();
        
        // Start scanner when modal opens
        scanModalEl.addEventListener('shown.bs.modal', () => {
            if(!html5Qrcode) {
                html5Qrcode = new Html5Qrcode("reader");
                html5Qrcode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        // Success
                        html5Qrcode.stop().then(() => {
                            html5Qrcode = null;
                            scanModal.hide();
                            loadCommodityDetail(decodedText);
                        });
                    },
                    (errorMessage) => { /* ignore per-frame errors */ }
                ).catch(err => {
                    alert("Kamera tidak dapat diakses.");
                    scanModal.hide();
                });
            }
        }, { once: true });
    });

    // Stop camera if modal closed manually
    scanModalEl.addEventListener('hidden.bs.modal', () => {
        if(html5Qrcode) {
            html5Qrcode.stop().then(() => html5Qrcode = null);
        }
    });
</script>

</body>
</html>