<x-layout>
    <x-slot name="title">Halaman Stock Opname</x-slot>
    <x-slot name="page_heading">Stock Opname</x-slot>

    <div class="card">
        <div class="card-body">
            <x-filter>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kondisi Barang</label>
                            <select name="condition" class="form-control">
                                <option value="">Semua Kondisi</option>
                                <option value="1" {{ request('condition') == 1 ? 'selected' : '' }}>Baik</option>
                                <option value="2" {{ request('condition') == 2 ? 'selected' : '' }}>Rusak</option>
                                <option value="3" {{ request('condition') == 3 ? 'selected' : '' }}>Belum di SO
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tahun Pembelian</label>
                            <select name="date_of_purchase" class="form-control">
                                <option value="">Semua Tahun</option>
                                @foreach ($date_of_purchases as $date)
                                    <option value="{{ $date }}"
                                        {{ request('date_of_purchase') == $date ? 'selected' : '' }}>
                                        {{ $date }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Lokasi Barang</label>
                            <select name="commodity_location_id" class="form-control">
                                <option value="">Semua Lokasi</option>
                                @foreach ($commodity_locations as $location)
                                    <option value="{{ $location->id }}"
                                        {{ request('commodity_location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-2">
                            <label>Cari Nama Barang / Kode / Merk:</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Ketik nama atau kode barang..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <x-slot name="resetFilterURL">{{ route('stock-opname.index') }}</x-slot>
            </x-filter>

            <form action="{{ route('stock-opname.store') }}" method="POST">
                @csrf
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <label class="mr-2 mb-0 small text-muted font-weight-bold">Tampilkan:</label>
                        <select class="form-control form-control-sm w-auto d-inline-block shadow-sm" onchange="window.location.href = updateQueryParam('per_page', this.value)">
                            <option value="10" @selected(request('per_page') == 10)>10</option>
                            <option value="25" @selected(!request('per_page') || request('per_page') == 25)>25</option>
                            <option value="50" @selected(request('per_page') == 50)>50</option>
                            <option value="100" @selected(request('per_page') == 100)>100</option>
                            <option value="250" @selected(request('per_page') == 250)>250</option>
                        </select>
                        <span class="ml-2 small text-muted">data per halaman</span>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-success mr-2" id="startScanButton">
                            <i class="fas fa-barcode"></i> Scan Barcode
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Stock Opname
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover my-2" style="width: 100%">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th style="width: 140px;">Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Lokasi</th>
                                <th style="width: 90px;" class="text-center">Stok Sistem</th>
                                <th style="width: 120px;" class="text-center">Stok Fisik</th>
                                <th style="width: 140px;">Kondisi</th>
                                <th style="width: 170px;">Terakhir SO</th>
                                <th style="width: 70px;" class="text-center">Histori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($commodities as $commodity)
                                <tr>
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="row-selector"
                                            name="commodities[{{ $commodity->id }}][selected]" value="1">
                                    </td>
                                    <td class="align-middle font-weight-bold">
                                        <span class="badge badge-primary">{{ $commodity->item_code }}</span>
                                    </td>
                                    <td class="align-middle">{{ $commodity->name }}</td>
                                    <td class="align-middle">{{ $commodity->commodity_location?->name ?? '-' }}</td>
                                    <td class="align-middle text-center font-weight-bold">{{ $commodity->quantity }}</td>
                                    <td class="align-middle">
                                        <input type="number" name="commodities[{{ $commodity->id }}][physical_quantity]"
                                            value="{{ $commodity->quantity }}" class="form-control form-control-sm physical-quantity text-center font-weight-bold"
                                            min="0" required>
                                    </td>
                                    <td class="align-middle">
                                        <select name="commodities[{{ $commodity->id }}][condition]"
                                            class="form-control form-control-sm condition-select" required>
                                            <option value="1" {{ $commodity->condition == 1 ? 'selected' : '' }}>Baik</option>
                                            <option value="2" {{ $commodity->condition == 2 ? 'selected' : '' }}>Rusak</option>
                                            <option value="3" {{ $commodity->condition == 3 ? 'selected' : '' }}>Belum di SO</option>
                                        </select>
                                    </td>
                                    <td class="align-middle small">
                                        @if ($commodity->latestStockOpname)
                                            <span class="text-dark"><i class="far fa-calendar-check text-success mr-1"></i> {{ $commodity->latestStockOpname->stock_opname_at->translatedFormat('d M Y H:i') }}</span>
                                        @else
                                            <span class="text-muted"><i class="fas fa-clock mr-1"></i> Belum Pernah</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <input type="hidden" name="commodities[{{ $commodity->id }}][id]"
                                            value="{{ $commodity->id }}">
                                        <button type="button" class="btn btn-sm btn-info show-history"
                                            data-commodity-id="{{ $commodity->id }}"
                                            data-commodity-name="{{ $commodity->name }}"
                                            title="Lihat Histori">
                                            <i class="fas fa-history"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-clipboard-check fa-3x mb-2 d-block"></i>
                                        Tidak ada data barang yang sesuai dengan filter / pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 pt-2 border-top">
                    <div class="text-muted small mb-2 mb-md-0">
                        Menampilkan <strong>{{ $commodities->firstItem() ?? 0 }}</strong> sampai <strong>{{ $commodities->lastItem() ?? 0 }}</strong> dari <strong>{{ number_format($commodities->total(), 0, ',', '.') }}</strong> data barang
                    </div>
                    <div>
                        {{ $commodities->withQueryString()->links() }}
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('modal')
        @include('stock-opname.modal.scan')
        @include('stock-opname.modal.input-stock')
        @include('stock-opname.modal.history')
    @endpush

    @push('js')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            function updateQueryParam(key, value) {
                let url = new URL(window.location.href);
                url.searchParams.set(key, value);
                url.searchParams.set('page', '1');
                return url.toString();
            }

            $(document).ready(function() {
                let html5QrCode = null;
                let currentCommodityId = null;


                // Handle select all checkbox
                $('#selectAll').click(function() {
                    $('.row-selector').prop('checked', this.checked);
                });

                // Handle individual checkbox
                $('.row-selector').click(function() {
                    if (!this.checked) {
                        $('#selectAll').prop('checked', false);
                    } else {
                        const allChecked = $('.row-selector:not(:checked)').length === 0;
                        $('#selectAll').prop('checked', allChecked);
                    }
                });

                $('#startScanButton').click(function() {
                    $('#scanModal').modal('show');
                });

                $('#scanModal').on('shown.bs.modal', function() {
                    html5QrCode = new Html5Qrcode("reader");
                    const config = {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    };
                    html5QrCode.start({
                            facingMode: "environment"
                        }, config, onScanSuccess, onScanError)
                        .catch(err => {
                            $('#scanResult').html(`<div class="alert alert-danger">${err}</div>`);
                        });
                });

                $('#scanModal').on('hidden.bs.modal', function() {
                    if (html5QrCode) {
                        html5QrCode.stop().then(() => {
                            html5QrCode = null;
                        }).catch(console.error);
                    }
                });

                function onScanSuccess(decodedText) {
                    if (html5QrCode) {
                        html5QrCode.stop().then(() => {
                            html5QrCode = null;
                        }).catch(console.error);
                    }

                    $('#scanResultInput').val(decodedText); // tampilkan hasil scan di input
                    $('#scanMessage').html('<div class="alert alert-info">Mencari barang...</div>');

                    $.get("{{ route('stock-opname.find-by-barcode') }}", {
                        code: decodedText
                    }, function(response) {
                        if (response.exists) {
                            // Barang ditemukan
                            currentCommodityId = response.commodity.id;
                            $('#modalItemCode').text(response.commodity.item_code);
                            $('#modalItemName').text(response.commodity.name);
                            $('#modalSystemStock').text(response.commodity.quantity);
                            $('#modalPhysicalQuantity').val(response.commodity.quantity);
                            $('#modalCondition').val(response.commodity.condition);
                            $('#scanModal').modal('hide');
                            $('#inputStockModal').modal('show');
                        } else {
                            // Barang tidak ditemukan
                            $('#scanMessage').html(`
                <div class="alert alert-warning">
                    <strong>Barang tidak ditemukan!</strong><br>
                    Barcode: ${decodedText}
                </div>
            `);
                        }
                    }).fail(function(xhr) {
                        // Error saat mencari
                        $('#scanMessage').html(`
            <div class="alert alert-danger">
                <strong>Gagal mencari barang!</strong><br>
                ${xhr.statusText}
            </div>
        `);
                    });
                }


                function onScanError(errorMessage) {
                    // do nothing for now
                }

                $('#saveStockButton').click(function() {
                    const physicalQty = $('#modalPhysicalQuantity').val();
                    const condition = $('#modalCondition').val();

                    $.ajax({
                        url: "{{ route('stock-opname.store-single') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            commodity_id: currentCommodityId,
                            physical_quantity: physicalQty,
                            condition: condition
                        },
                        success: function(response) {
                            const $row = $(`input[name*='[id]'][value='${currentCommodityId}']`)
                                .closest('tr');

                            // Centang checkbox setelah simpan via scan
                            $row.find('.row-selector').prop('checked', true);

                            // Update nilai di tabel
                            $row.find('.physical-quantity').val(physicalQty);
                            $row.find('.condition-select').val(condition);

                            // Update kolom "Terakhir Stock Opname"
                            const now = new Date();
                            const formattedDate = now.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            $row.find('td:eq(7)').html(
                                formattedDate); // Pastikan index kolom sesuai

                            $('#inputStockModal').modal('hide');
                            showToast('success', 'Data berhasil disimpan!');
                        },
                        error: function(xhr) {
                            alert('Gagal menyimpan: ' + xhr.responseJSON.message);
                        }
                    });
                });

                // Fungsi toast notifikasi
                function showToast(type, message) {
                    const toast = `<div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3">
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>`;

                    $(toast).toast({
                        delay: 3000
                    }).toast('show');
                }

                function getConditionBadge(condition) {
                    switch (condition) {
                        case 1:
                            return 'badge-success';
                        case 2:
                            return 'badge-warning';
                        case 3:
                            return 'badge-danger';
                        default:
                            return 'badge-secondary';
                    }
                }

                // Handle tampilkan riwayat stock opname
                $(document).on('click', '.show-history', function() {
                    const commodityId = $(this).data('commodity-id');
                    const commodityName = $(this).data('commodity-name');

                    $('#commodityName').text(commodityName);
                    $('#loading').show();
                    $('#historyContent').html('');

                    $.ajax({
                        url: "{{ route('stock-opname.history') }}",
                        method: "GET",
                        data: {
                            commodity_id: commodityId
                        },
                        success: function(response) {
                            let html = '';
                            if (response.data.length > 0) {
                                response.data.forEach((item, index) => {
                                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.stock_opname_at}</td>
                            <td>${item.system_stock}</td>
                            <td>${item.physical_stock}</td>
                            <td>${item.difference}</td>
                            <td><span class="badge ${getConditionBadge(item.condition)}">${item.condition_text}</span></td>
                            <td>${item.updated_by_name}</td>
                        </tr>
                    `;
                                });
                            } else {
                                html =
                                    `<tr><td colspan="6" class="text-center">Tidak ada riwayat</td></tr>`;
                            }
                            $('#historyContent').html(html);
                        },
                        error: function(xhr) {
                            $('#historyContent').html(
                                `<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>`
                            );
                        },
                        complete: function() {
                            $('#loading').hide();
                            $('#historyModal').modal('show');
                        }
                    });
                });

                // Fungsi tambahan untuk mendapatkan kelas badge kondisi
                function getConditionBadge(condition) {
                    switch (condition) {
                        case 1:
                            return 'badge-success';
                        case 2:
                            return 'badge-warning';
                        case 3:
                            return 'badge-danger';
                        default:
                            return 'badge-secondary';
                    }
                }
            });
        </script>
    @endpush
</x-layout>
