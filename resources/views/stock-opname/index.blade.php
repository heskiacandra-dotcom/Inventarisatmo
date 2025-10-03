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

                <div class="col-md-12 mt-2">
                    <button type="button" class="btn btn-success float-right" id="startScanButton">
                        <i class="fas fa-barcode"></i> Scan Barcode
                    </button>
                </div>

                <x-slot name="resetFilterURL">{{ route('stock-opname.index') }}</x-slot>
            </x-filter>

            <form action="{{ route('stock-opname.store') }}" method="POST">
                @csrf
                <div class="d-flex justify-content-end mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Stock Opname
                    </button>
                </div>

                <x-datatable>
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Lokasi</th>
                            <th>Stok Sistem</th>
                            <th>Stok Fisik</th>
                            <th>Kondisi</th>
                            <th>Terakhir Stock Opname</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commodities as $commodity)
                            <tr>
                                <td>
                                    <input type="checkbox" class="row-selector"
                                        name="commodities[{{ $loop->index }}][selected]" value="1">
                                </td>
                                <td>{{ $commodity->item_code }}</td>
                                <td>{{ $commodity->name }}</td>
                                <td>{{ $commodity->commodity_location->name }}</td>
                                <td>{{ $commodity->quantity }}</td>
                                <td>
                                    <input type="number" name="commodities[{{ $loop->index }}][physical_quantity]"
                                        value="{{ $commodity->quantity }}" class="form-control physical-quantity"
                                        min="0" required>
                                </td>
                                <td>
                                    <select name="commodities[{{ $loop->index }}][condition]"
                                        class="form-control condition-select" required>
                                        <option value="1" {{ $commodity->condition == 1 ? 'selected' : '' }}>Baik
                                        </option>
                                        <option value="2" {{ $commodity->condition == 2 ? 'selected' : '' }}>Rusak
                                        </option>
                                        <option value="3" {{ $commodity->condition == 3 ? 'selected' : '' }}>Belum
                                            di SO</option>
                                    </select>
                                </td>
                                <td>
                                    @if ($commodity->stockOpnames->isNotEmpty())
                                        {{ $commodity->stockOpnames->last()->stock_opname_at->translatedFormat('d F Y H:i') }}
                                    @else
                                        <span class="text-muted">Belum Pernah</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="hidden" name="commodities[{{ $loop->index }}][id]"
                                        value="{{ $commodity->id }}">
                                    <button type="button" class="btn btn-sm btn-info show-history"
                                        data-commodity-id="{{ $commodity->id }}"
                                        data-commodity-name="{{ $commodity->name }}">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-datatable>
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
