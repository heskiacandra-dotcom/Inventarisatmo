<x-layout>
	<x-slot name="title">Dashboard</x-slot>
	<x-slot name="page_heading">Dashboard</x-slot>

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
	<div class="row">
		<div class="col-md-6 col-lg-8">
			<x-bar-chart chartTitle="Grafik Kondisi Fisik Barang" chartID="chartCommodityCondition"
				icon="fa-heartbeat"
				badge="Status Aset"
				:series="$charts['commodity_condition_count']['series']"
				:categories="$charts['commodity_condition_count']['categories']" 
				:colors="['#10b981', '#f59e0b', '#ef4444']">
			</x-bar-chart>
		</div>

		<div class="col-md-6 col-lg-4">
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
					<h4 class="mb-0 d-flex align-items-center font-weight-bold" style="font-size: 1rem; color: #1e293b;">
						<span class="mr-2 d-inline-flex align-items-center justify-content-center rounded" style="width: 28px; height: 28px; background: #fef3c7; color: #d97706; font-size: 0.85rem;">
							<i class="fas fa-crown"></i>
						</span>
						5 Barang Nilai Tertinggi
					</h4>
					<span class="badge badge-warning text-dark font-weight-bold" style="font-size: 0.72rem;">Top Nilai</span>
				</div>
				<div class="card-body p-3">
					<ul class="list-unstyled mb-0">
						@foreach($commodity_order_by_price as $key => $order_by_price)
						<li class="media align-items-center py-2 px-3 rounded mb-2 border" style="background-color: #fafbfc; transition: all 0.2s ease;">
							<div class="mr-3 text-center rounded-circle font-weight-bold text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; background: linear-gradient(135deg, #4f46e5, #6366f1); font-size: 0.85rem; flex-shrink: 0;">
								{{ $loop->iteration }}
							</div>
							<div class="media-body text-truncate">
								<div class="font-weight-bold text-dark text-truncate" title="{{ $order_by_price->name }}" style="font-size: 0.9rem;">{{ $order_by_price->name }}</div>
								<span class="small font-weight-bold font-family-monospace text-primary">Rp {{ $order_by_price->indonesian_currency($order_by_price->price) }}</span>
							</div>
							@can('detail barang')
							<button data-id="{{ $order_by_price->id }}" class="btn btn-outline-primary btn-sm show-modal ml-2 rounded-circle" style="width: 32px; height: 32px; padding: 0;"
								data-toggle="modal" data-target="#show_commodity" title="Lihat Detail">
								<i class="fas fa-eye"></i>
							</button>
							@endcan
						</li>
						@endforeach
					</ul>
					@can('lihat barang')
					<div class="text-center pt-3">
						<a href="{{ route('barang.index') }}" class="btn btn-primary btn-block shadow-sm font-weight-semibold">
							Lihat Semua Barang <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
					@endcan
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-7">
			<x-bar-chart chartTitle="Pengadaan Barang Berdasarkan Tahun" chartID="chartCommodityCountEachDate"
				icon="fa-calendar-alt"
				badge="Tahun Perolehan"
				:colors="['#4f46e5']"
				:series="$charts['commodity_each_date_of_purchase_count']['series']"
				:categories="$charts['commodity_each_date_of_purchase_count']['categories']">
			</x-bar-chart>
		</div>
		<div class="col-lg-5">
			<x-pie-chart chartTitle="Distribusi Sumber Perolehan"
				chartID="chartCommodityByCommodityAcquisitionCount"
				icon="fa-hand-holding-usd"
				badge="Sumber Dana"
				:series="$charts['commodity_by_commodity_acquisition_count']['series']"
				:categories="$charts['commodity_by_commodity_acquisition_count']['categories']"></x-pie-chart>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-6">
			<x-pie-chart chartTitle="Distribusi Material Barang" chartID="chartCommodityByMaterial"
				icon="fa-layer-group"
				badge="Bahan Baku"
				:series="$charts['commodity_by_material_count']['series']"
				:categories="$charts['commodity_by_material_count']['categories']"></x-pie-chart>
		</div>
		<div class="col-lg-6">
			<x-pie-chart chartTitle="Distribusi Merk Terbanyak" chartID="chartCommodityByBrand"
				icon="fa-tags"
				badge="Top Merk"
				:series="$charts['commodity_by_brand_count']['series']"
				:categories="$charts['commodity_by_brand_count']['categories']"></x-pie-chart>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<x-bar-chart chartTitle="Distribusi Barang Berdasarkan Ruangan (Top 12)" chartID="chartCommodityCountEachLocation"
				icon="fa-door-open"
				badge="Lokasi Terbanyak"
				:horizontal="true"
				:height="460"
				:colors="['#4f46e5']"
				:series="$charts['commodity_each_location_count']['series']"
				:categories="$charts['commodity_each_location_count']['categories']">
			</x-bar-chart>
		</div>
	</div>

	@push('modal')
	@include('commodities.modal.show')
	@endpush

	@push('js')
	@include('_script');
	@endpush
</x-layout>
