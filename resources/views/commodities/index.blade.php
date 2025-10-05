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
				<div class="card-icon bg-primary">
					<i class="fas fa-columns"></i>
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
				<div class="card-icon bg-success">
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
				<div class="card-icon bg-warning">
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
				<div class="card-icon bg-danger">
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
					<a href="{{ route('barang.download-pdf') }}" class="btn btn-success">
					📄 Download PDF (Backend)
					</a>
					@endcan
				</div>
			</div>

			<x-filter>
				{{-- filter tetap sama --}}
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="commodity_location_id">Lokasi Barang:</label>
							<select name="commodity_location_id" id="commodity_location_id" @class(['form-control','is-valid'=> request()->filled('commodity_location_id')])>
								<option value="">Pilih lokasi barang..</option>
								@foreach ($commodity_locations as $commodity_location)
								<option value="{{ $commodity_location->id }}" @selected(request('commodity_location_id')==$commodity_location->id)>
									{{ $commodity_location->name }}
								</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="commodity_acquisition_id">Asal Perolehan:</label>
							<select name="commodity_acquisition_id" id="commodity_acquisition_id" @class(['form-control','is-valid'=> request()->filled('commodity_acquisition_id')])>
								<option value="">Pilih asal perolehan..</option>
								@foreach ($commodity_acquisitions as $commodity_acquisition)
								<option value="{{ $commodity_acquisition->id }}" @selected(request('commodity_acquisition_id')==$commodity_acquisition->id)>
									{{ $commodity_acquisition->name }}
								</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				{{-- filter lainnya tetap sama --}}

				<x-slot name="resetFilterURL">{{ route('barang.index') }}</x-slot>
			</x-filter>

			<div class="row">
				<div class="col-lg-12">
					<x-datatable>
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">Kode Barang</th>
								<th scope="col">Nomor Barang</th>
								<th scope="col">Nama Barang</th>
								<th scope="col">Bahan</th>
								<th scope="col">Merk</th>
								<th scope="col">Tanggal Pembelian</th>
								<th scope="col">Kondisi</th>
								<th scope="col">Aksi</th>
							</tr>
						</thead>
						<tbody>
							@foreach($commodities as $commodity)
							<tr>
								<th scope="row">{{ $loop->iteration }}</th>
								<td class="text-center align-middle">
									<div class="d-flex flex-column align-items-center">
										<span class="badge badge-primary mb-2">
											{{ $commodity->item_code }}
										</span>
										<span class="d-flex align-items-center">
											<span class="badge badge-pill badge-info px-3" title="{{ $commodity->commodity_acquisition->name }}">
												<i class="fa fa-fw far fa-face-laugh mr-1"></i>
												{{ $commodity->commodity_acquisition->name }}
											</span>
										</span>
									</div>
								</td>
								<td>{{ $commodity->formatted_item_number }}</td>
								<td>{{ Str::limit($commodity->name, 55, '...') }}</td>
								<td>{{ $commodity->material }}</td>
								<td>{{ $commodity->brand }}</td>
								<td>{{ $commodity->date_of_purchase }}</td>
								@if($commodity->condition === 1)
								<td>
									<span class="badge badge-pill badge-success" title="Baik">
										<i class="fas fa-fw fa-check-circle"></i>
										Baik
									</span>
								</td>
								@elseif($commodity->condition === 2)
								<td>
									<span class="badge badge-pill badge-warning" title="Rusak">
										<i class="fa fa-fw fa-exclamation-circle"></i>
										Rusak
									</span>
								</td>
								@else
								<td>
									<span class="badge badge-pill badge-danger" title="Belum di SO">
										<i class="fa fa-fw fa-times-circle"></i>
										Belum di SO
									</span>
								</td>
								@endif
								<td class="text-center">
									<div class="btn-group" role="group" aria-label="Basic example">
										@can('detail barang')
										<a data-id="{{ $commodity->id }}" class="btn btn-sm btn-info text-white show-modal mr-2"
											data-toggle="modal" data-target="#show_commodity" title="Lihat Detail">
											<i class="fas fa-fw fa-search"></i>
										</a>
										@endcan

										@can('ubah barang')
										<a data-id="{{ $commodity->id }}" class="btn btn-sm btn-success text-white edit-modal mr-2"
											data-toggle="modal" data-target="#edit_commodity" title="Ubah data">
											<i class="fas fa-fw fa-edit"></i>
										</a>
										@endcan

										@can('print individual barang')
										<a href="{{ route('barang.print-qrcode', $commodity->id) }}" 
											class="btn btn-sm btn-primary mr-2" 
											title="Generate QR Code">
											<i class="fas fa-fw fa-qrcode"></i>
										</a>
										@endcan

										@can('hapus barang')
										<form action="{{ route('barang.destroy', $commodity) }}" method="POST">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-sm btn-danger delete-button">
												<i class="fas fa-fw fa-trash-alt"></i>
											</button>
										</form>
										@endcan
									</div>
								</td>
							</tr>
							@endforeach
						</tbody>
					</x-datatable>
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
	@endpush

	@push('js')
	@include('commodities._script')

	<script>
document.addEventListener('DOMContentLoaded', function() {
    const printBtn = document.getElementById('print-all-btn');
    if (!printBtn) return;

    printBtn.addEventListener('click', async () => {
        printBtn.disabled = true;
        printBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Membuat PDF...';

        try {
            const response = await fetch("{{ route('barang.print', $query) }}", {
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
