<x-layout>
	<x-slot name="title">Halaman Daftar Barang</x-slot>
	<x-slot name="page_heading">Daftar Barang</x-slot>

	<div class="row mb-4">
		<div class="col-12">
			<h4 class="mb-0">
				@if(request()->filled('commodity_location_id'))
					Ruang: {{ $commodity_locations->firstWhere('id', request('commodity_location_id'))->name }}
				@else
					Semua Ruang
				@endif
			</h4>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-3 col-md-6 col-sm-6 col-12">
			<div class="card card-statistic-1">
				<div class="card-icon bg-primary shadow-primary">
					<i class="fas fa-boxes-stacked"></i>
				</div>
				<div class="card-wrap">
					<div class="card-header">
						<h4>Total Barang</h4>
					</div>
					<div class="card-body">
						{{ $commodity_counts['commodity_in_total'] }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-12">
			<div class="card card-statistic-1">
				<div class="card-icon bg-success shadow-success">
					<i class="fas fa-fw fa-check-circle"></i>
				</div>
				<div class="card-wrap">
					<div class="card-header">
						<h4>Kondisi Baik</h4>
					</div>
					<div class="card-body">
						{{ $commodity_counts['commodity_in_good_condition'] }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-12">
			<div class="card card-statistic-1">
				<div class="card-icon bg-warning shadow-warning">
					<i class="fas fa-fw fa-exclamation-circle"></i>
				</div>
				<div class="card-wrap">
					<div class="card-header">
						<h4>Kondisi Rusak</h4>
					</div>
					<div class="card-body">
						{{ $commodity_counts['commodity_in_not_good_condition'] }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-12">
			<div class="card card-statistic-1">
				<div class="card-icon bg-danger shadow-danger">
					<i class="fas fa-fw fa-times-circle"></i>
				</div>
				<div class="card-wrap">
					<div class="card-header">
						<h4>Belum di SO</h4>
					</div>
					<div class="card-body">
						{{ $commodity_counts['commodity_in_heavily_damage_condition'] }}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			@include('utilities.alert')
			<div class="d-flex justify-content-end mb-3">
				<div class="btn-group">
					@can('import barang')
					<button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#excel_menu">
						<i class="fas fa-fw fa-upload"></i>
						Import Excel
					</button>
					@endcan

					@can('export barang')
					<button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#export_menu">
						<i class="fas fa-fw fa-download"></i>
						Export
					</button>
					@endcan

					@can('tambah barang')
					<button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#commodity_create_modal">
						<i class="fas fa-fw fa-plus"></i>
						Tambah Data
					</button>
					@endcan

					@can('print barang')
					@php
					    $query = request()->query();
					@endphp
					<form action="{{ route('barang.print', $query) }}" method="POST">
						@csrf
						<button type="submit" class="btn btn-success">
							<i class="fas fa-fw fa-print"></i>
							Print
						</button>
					</form>
					@endcan
				</div>
			</div>

			<x-filter>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="commodity_location_id">Lokasi Barang:</label>
							<select name="commodity_location_id" id="commodity_location_id" @class(['form-control','is-valid'=> request()->filled('commodity_location_id')])>
								<option value="">Semua Lokasi</option>
								@foreach ($commodity_locations as $commodity_location)
								<option value="{{ $commodity_location->id }}" @selected(request('commodity_location_id')==$commodity_location->id)>
									{{ $commodity_location->name }}
								</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="commodity_acquisition_id">Asal Perolehan:</label>
							<select name="commodity_acquisition_id" id="commodity_acquisition_id" @class(['form-control','is-valid'=> request()->filled('commodity_acquisition_id')])>
								<option value="">Semua Perolehan</option>
								@foreach ($commodity_acquisitions as $commodity_acquisition)
								<option value="{{ $commodity_acquisition->id }}" @selected(request('commodity_acquisition_id')==$commodity_acquisition->id)>
									{{ $commodity_acquisition->name }}
								</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="condition">Kondisi Barang:</label>
							<select name="condition" id="condition" @class(['form-control','is-valid'=> request()->filled('condition')])>
								<option value="">Semua Kondisi</option>
								<option value="1" @selected(request('condition') == '1')>Baik</option>
								<option value="2" @selected(request('condition') == '2')>Rusak</option>
								<option value="3" @selected(request('condition') == '3')>Belum di SO</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="date_of_purchase">Tahun Pembelian:</label>
							<select name="date_of_purchase" id="date_of_purchase" @class(['form-control','is-valid'=> request()->filled('date_of_purchase')])>
								<option value="">Semua Tahun</option>
								@foreach ($date_of_purchases as $dop)
								<option value="{{ $dop }}" @selected(request('date_of_purchase') == $dop)>{{ $dop }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="brand">Merk:</label>
							<select name="brand" id="brand" @class(['form-control','is-valid'=> request()->filled('brand')])>
								<option value="">Semua Merk</option>
								@foreach ($commodity_brands as $b)
								<option value="{{ $b }}" @selected(request('brand') == $b)>{{ $b }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="material">Bahan:</label>
							<select name="material" id="material" @class(['form-control','is-valid'=> request()->filled('material')])>
								<option value="">Semua Bahan</option>
								@foreach ($commodity_materials as $mat)
								<option value="{{ $mat }}" @selected(request('material') == $mat)>{{ $mat }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group mb-2">
							<label for="filter_search">Pencarian Kata Kunci (Nama, Kode, Nomor, Merk, Bahan):</label>
							<div class="input-group">
								<input type="text" name="search" id="filter_search" class="form-control" placeholder="Ketik kata kunci pencarian..." value="{{ request('search') }}">
							</div>
						</div>
					</div>
				</div>

				<x-slot name="resetFilterURL">{{ route('barang.index') }}</x-slot>
			</x-filter>

			<div class="row">
				<div class="col-lg-12">
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
						<div>
							<form action="" method="GET" class="form-inline">
								@foreach(request()->except(['search', 'page']) as $k => $v)
									@if(is_array($v))
										@foreach($v as $subK => $subV)
											<input type="hidden" name="{{ $k }}[{{ $subK }}]" value="{{ $subV }}">
										@endforeach
									@else
										<input type="hidden" name="{{ $k }}" value="{{ $v }}">
									@endif
								@endforeach
								<div class="input-group input-group-sm shadow-sm" style="min-width: 260px;">
									<input type="text" name="search" class="form-control" placeholder="Cari nama, kode, merk..." value="{{ request('search') }}">
									<div class="input-group-append">
										<button class="btn btn-primary" type="submit" title="Cari"><i class="fas fa-search"></i></button>
										@if(request()->filled('search'))
											<a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}" class="btn btn-outline-secondary" title="Reset cari"><i class="fas fa-times"></i></a>
										@endif
									</div>
								</div>
							</form>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-bordered table-hover my-2" style="width: 100%">
							<thead class="thead-light">
								<tr>
									<th scope="col" style="width: 50px;">#</th>
									<th scope="col">Kode Barang</th>
									<th scope="col">Nomor Barang</th>
									<th scope="col">Nama Barang</th>
									<th scope="col">Bahan</th>
									<th scope="col">Merk</th>
									<th scope="col">Tanggal Pembelian</th>
									<th scope="col">Kondisi</th>
									<th scope="col" class="text-center" style="width: 160px;">Aksi</th>
								</tr>
							</thead>
							<tbody>
								@forelse($commodities as $commodity)
								<tr>
									<th scope="row" class="text-center align-middle">{{ $commodities->firstItem() + $loop->index }}</th>
									<td class="text-center align-middle">
										<div class="d-flex flex-column align-items-center">
											<span class="badge badge-primary mb-1">
												{{ $commodity->item_code }}
											</span>
											<span class="d-flex align-items-center">
												<span class="badge badge-pill badge-info px-2" title="{{ $commodity->commodity_acquisition?->name }}">
													<i class="fa fa-fw far fa-face-laugh mr-1"></i>
													{{ $commodity->commodity_acquisition?->name }}
												</span>
											</span>
										</div>
									</td>
									<td class="align-middle font-weight-bold">{{ $commodity->formatted_item_number }}</td>
									<td class="align-middle">{{ Str::limit($commodity->name, 55, '...') }}</td>
									<td class="align-middle">{{ $commodity->material }}</td>
									<td class="align-middle">{{ $commodity->brand }}</td>
									<td class="align-middle">{{ $commodity->date_of_purchase }}</td>
									<td class="align-middle">
										@if($commodity->condition === 1)
										<span class="badge badge-pill badge-success" title="Baik">
											<i class="fas fa-fw fa-check-circle"></i> Baik
										</span>
										@elseif($commodity->condition === 2)
										<span class="badge badge-pill badge-warning" title="Rusak">
											<i class="fa fa-fw fa-exclamation-circle"></i> Rusak
										</span>
										@else
										<span class="badge badge-pill badge-danger" title="Belum di SO">
											<i class="fa fa-fw fa-times-circle"></i> Belum di SO
										</span>
										@endif
									</td>
									<td class="text-center align-middle">
										<div class="btn-group" role="group" aria-label="Aksi Barang">
											@can('detail barang')
											<a data-id="{{ $commodity->id }}" class="btn btn-sm btn-info text-white show-modal mr-1"
												data-toggle="modal" data-target="#show_commodity" title="Lihat Detail">
												<i class="fas fa-fw fa-search"></i>
											</a>
											@endcan

											@can('ubah barang')
											<a data-id="{{ $commodity->id }}" class="btn btn-sm btn-success text-white edit-modal mr-1"
												data-toggle="modal" data-target="#edit_commodity" title="Ubah data">
												<i class="fas fa-fw fa-edit"></i>
											</a>
											@endcan

											@can('print individual barang')
											<a href="{{ route('barang.print-qrcode', $commodity->id) }}" 
												class="btn btn-sm btn-primary mr-1" 
												title="Generate QR Code">
												<i class="fas fa-fw fa-qrcode"></i>
											</a>
											@endcan

											@can('hapus barang')
											<button type="button"
												class="btn btn-sm btn-warning text-white lepas-modal"
												data-id="{{ $commodity->id }}"
												data-nama="{{ $commodity->name }}"
												data-toggle="modal"
												data-target="#lepas_commodity"
												title="Lepas Barang">
												<i class="fas fa-fw fa-sign-out-alt"></i>
											</button>
											@endcan
										</div>
									</td>
								</tr>
								@empty
								<tr>
									<td colspan="9" class="text-center py-4 text-muted">
										<i class="fas fa-box-open fa-3x mb-2 d-block"></i>
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
				</div>
			</div>

		</div>
	</div>

	@push('modal')
	@include('commodities.modal.show')
	@include('commodities.modal.create')
	@include('commodities.modal.edit')
	@include('commodities.modal.import')
	@include('commodities.modal.export')
	@include('commodities.modal.lepas')
	@endpush

	@push('js')
	@include('commodities._script')

	<script>
function updateQueryParam(key, value) {
    let url = new URL(window.location.href);
    url.searchParams.set(key, value);
    url.searchParams.set('page', '1');
    return url.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    const printBtn = document.getElementById('print-all-btn');
    if (!printBtn) return;

    printBtn.addEventListener('click', async () => {
        printBtn.disabled = true;
        printBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Membuat PDF...';

        try {
            const response = await fetch("{{ route('barang.print', request()->query()) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            });

            if (!response.ok) throw new Error("Gagal membuat PDF");

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = "Stiker-Barang.pdf";
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        } catch (e) {
            alert("Terjadi kesalahan saat membuat PDF!");
            console.error(e);
        } finally {
            printBtn.disabled = false;
            printBtn.innerHTML = '<i class="fas fa-fw fa-print"></i> Print';
        }
    });
});
</script>
	@endpush

</x-layout>
