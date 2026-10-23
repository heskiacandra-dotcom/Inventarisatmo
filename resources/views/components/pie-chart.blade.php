@props([
    'chartTitle',
    'chartID',
    'series' => [],
    'categories' => [],
    'colors' => null,
    'height' => 340,
    'icon' => 'fa-chart-pie',
    'badge' => null
])

@php
    $hasData = count($series) > 0 && collect($series)->sum() > 0;
@endphp

<div class="card chart-card mb-4 shadow-sm border-0">
    <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
        <h4 class="mb-0 d-flex align-items-center font-weight-bold" style="font-size: 1rem; color: #1e293b;">
            @if($icon)
                <span class="chart-header-icon mr-2 d-inline-flex align-items-center justify-content-center rounded" style="width: 28px; height: 28px; background: #eef2ff; color: #4f46e5; font-size: 0.85rem;">
                    <i class="fas {{ $icon }}"></i>
                </span>
            @endif
            {{ $chartTitle }}
        </h4>
        @if($badge)
            <span class="badge badge-light text-muted font-weight-semibold px-2 py-1" style="font-size: 0.75rem; border: 1px solid #e2e8f0;">{{ $badge }}</span>
        @endif
    </div>
    <div class="card-body p-4 d-flex align-items-center justify-content-center">
        @if(!$hasData)
            <div class="text-center py-5">
                <div class="mb-3 text-muted" style="opacity: 0.35;">
                    <i class="fas fa-chart-pie fa-3x"></i>
                </div>
                <h6 class="text-muted font-weight-bold mb-1">Belum ada data untuk ditampilkan</h6>
                <small class="text-muted">Data akan otomatis terisi saat item tercatat dalam sistem.</small>
            </div>
        @else
            <div id="{{ $chartID }}" class="w-100" style="min-height: {{ $height }}px;"></div>
        @endif
    </div>
</div>

@if($hasData)
@push('js')
<script>
    $(function() {
        const chartElement = document.querySelector("#{{ $chartID }}");
        if (!chartElement) return;

        const categories = @json($categories);
        const seriesData = @json($series).map(function(val) { return Number(val); });
        const customColors = @json($colors);

        let modernPalette = [
            '#4f46e5', '#10b981', '#f59e0b', '#06b6d4', 
            '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', 
            '#f97316', '#6366f1', '#84cc16', '#a855f7'
        ];

        let options = {
            series: seriesData,
            labels: categories,
            chart: {
                height: {{ $height }},
                type: "donut",
                fontFamily: "'Plus Jakarta Sans', sans-serif",
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 500,
                }
            },
            colors: customColors || modernPalette,
            stroke: {
                width: 2,
                colors: ['#ffffff']
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '13px',
                                fontFamily: "'Plus Jakarta Sans', sans-serif",
                                fontWeight: 600,
                                color: '#64748b',
                                offsetY: -4
                            },
                            value: {
                                show: true,
                                fontSize: '22px',
                                fontFamily: "'Plus Jakarta Sans', sans-serif",
                                fontWeight: 800,
                                color: '#0f172a',
                                offsetY: 4,
                                formatter: function(val) {
                                    return Number(val).toLocaleString('id-ID');
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '12px',
                                fontWeight: 600,
                                color: '#64748b',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: "bottom",
                fontFamily: "'Plus Jakarta Sans', sans-serif",
                fontSize: '12px',
                fontWeight: 500,
                labels: {
                    colors: '#475569'
                },
                markers: {
                    width: 10,
                    height: 10,
                    radius: 12
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 4
                }
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontSize: '12px',
                    fontFamily: "'Plus Jakarta Sans', sans-serif"
                },
                y: {
                    formatter: function(val) {
                        return val.toLocaleString('id-ID') + ' Barang';
                    }
                }
            },
            responsive: [
                {
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 280
                        },
                        legend: {
                            position: "bottom"
                        }
                    }
                }
            ]
        };

        new ApexCharts(chartElement, options).render();
    });
</script>
@endpush
@endif
