<x-layout>
    <x-slot name="title">Scan Barang</x-slot>
    <x-slot name="page_heading">Scan QR Code Barang</x-slot>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <button id="start-camera" class="btn btn-primary">Mulai Scan</button>
                    <div id="reader" style="width: 100%; margin-top: 20px;"></div>
                    <div id="scan-result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    @push('modal')
        @include('commodities.modal.show')
    @endpush

    @push('js')
        <script src="https://unpkg.com/html5-qrcode@latest/html5-qrcode.min.js"></script>
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        
        <script>
            @can('detail barang')
                const canDetailBarang = true;
            @else
                const canDetailBarang = false;
            @endcan

            document.addEventListener('DOMContentLoaded', function() {
                let html5Qrcode;
                const startButton = document.getElementById('start-camera');
                const resultDiv = document.getElementById('scan-result');

                // Event listener untuk tombol scan
                startButton.addEventListener('click', () => {
                    if (!html5Qrcode) {
                        html5Qrcode = new Html5Qrcode("reader");
                        
                        html5Qrcode.start(
                            { facingMode: "environment" },
                            { fps: 10, qrbox: { width: 250, height: 250 } },
                            (decodedText) => {
                                handleScan(decodedText);
                            }
                        ).catch(err => {
                            console.error("Gagal membuka kamera:", err);
                            html5Qrcode = null; // Reset jika error
                        });
                    }
                });

                // Fungsi utama penanganan scan
                function handleScan(content) {
                    if(isNaN(content)) {
                        resultDiv.innerHTML = `
                            <div class="alert alert-danger">
                                Kode QR tidak valid! Pastikan memindai kode resmi barang.
                            </div>
                        `;
                        return;
                    }

                    html5Qrcode.stop().then(() => {
                        if(canDetailBarang) {
                            const url = "{{ route('api.barang.show', ':id') }}".replace(':id', content);
                            
                            $.ajax({
                                url: url,
                                method: "GET",
                                headers: {
                                    "Content-Type": "application/json",
                                },
                                success: (res) => {
                                    // Isi data ke modal
                                    $("#show_commodity #item_code").val(res.data.item_code);
                                    $("#show_commodity #name").val(res.data.name);
                                    $("#show_commodity #commodity_location_id").val(res.data.commodity_location.name);
                                    $("#show_commodity #material").val(res.data.material);
                                    $("#show_commodity #brand").val(res.data.brand);
                                    $("#show_commodity #date_of_purchase").val(res.data.date_of_purchase);
                                    $("#show_commodity #condition").val(res.data.condition_name);
                                    $("#show_commodity #commodity_acquisition_id").val(res.data.commodity_acquisition.name);
                                    $("#show_commodity #note").val(res.data.note);
                                    $("#show_commodity #quantity").val(res.data.quantity);
                                    $("#show_commodity #price").val(res.data.price_formatted);
                                    $("#show_commodity #price_per_item").val(res.data.price_per_item_formatted);
                                    
                                    // Tampilkan modal
                                    $('#show_commodity').modal('show');
                                },
                                error: (err) => {
                                    resultDiv.innerHTML = `
                                        <div class="alert alert-warning">
                                            Barang dengan ID ${content} tidak ditemukan!
                                        </div>
                                    `;
                                    console.error(err);
                                }
                            });
                        } else {
                            resultDiv.innerHTML = `
                                <div class="alert alert-warning">
                                    Anda tidak memiliki akses untuk melihat detail barang
                                </div>
                            `;
                        }

                        // Reset instance kamera
                        html5Qrcode = null;
                    });
                }

                // Fungsi restart scan
                window.restartScan = function() {
                    if(html5Qrcode) {
                        html5Qrcode.stop().then(() => {
                            html5Qrcode = null;
                        });
                    }
                    resultDiv.innerHTML = '';
                    $('#show_commodity').modal('hide');
                }

                // Event listener saat modal ditutup
                $('#show_commodity').on('hidden.bs.modal', function () {
                    html5Qrcode = null; // Pastikan instance direset
                    startButton.focus(); // Kembalikan fokus ke tombol
                });
            });
        </script>
    @endpush
</x-layout>