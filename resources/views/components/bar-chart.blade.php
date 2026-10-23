@props([
    'chartTitle',
    'chartID',
    'series' => [],
    'categories' => [],
    'colors' => null,
    'horizontal' => false,
    'height' => 340,
    'icon' => 'fa-chart-bar',
    'badge' => null
])

@php
    $hasData = count($series) > 0 && collect($series)->sum() > 0;
    $isHorizontal = $horizontal ? 'true' : 'false';
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
    <div class="card-body p-4">
        @if(!$hasData)
            <div class="text-center py-5">
                <div class="mb-3 text-muted" style="opacity: 0.35;">
                    <i class="fas fa-chart-column fa-3x"></i>
                </div>
                <h6 class="text-muted font-weight-bold mb-1">Belum ada data untuk ditampilkan</h6>
                <small class="text-muted">Data akan otomatis terisi saat item tercatat dalam sistem.</small>
            </div>
        @else
            <div id="{{ $chartID }}" style="min-height: {{ $height }}px;"></div>
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
        const seriesData = @json($series);
        const isHorizontal = {{ $isHorizontal }};
        const customColors = @json($colors);

        let defaultPalette = [
            '#4f46e5', '#10b981', '#f59e0b', '#06b6d4', 
            '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', 
            '#f97316', '#6366f1', '#84cc16', '#a855f7'
        ];

        let options = {
            chart: {
                height: {{ $height }},
                type: "bar",
                fontFamily: "'Plus Jakarta Sans', sans-serif",
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 500,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: isHorizontal,
                    borderRadius: 6,
                    borderRadiusApplication: 'end',
                    distributed: isHorizontal ? false : (customColors ? true : false),
                    columnWidth: isHorizontal ? '65%' : '42%',
                    barHeight: isHorizontal ? '65%' : '42%',
                    dataLabels: {
                        position: 'top',
                    }
                }
            },
            dataLabels: {
                enabled: true,
                offsetX: isHorizontal ? 28 : 0,
                offsetY: isHorizontal ? 0 : -20,
                style: {
                    fontSize: '11px',
                    fontWeight: 700,
                    colors: isHorizontal ? ['#334155'] : ['#475569']
                },
                formatter: function(val) {
                    return val ? val.toLocaleString('id-ID') : '';
                }
            },
            series: [{
                name: 'Jumlah',
                data: seriesData
            }],
            colors: customColors || (isHorizontal ? ['#4f46e5'] : defaultPalette),
            xaxis: {
                categories: categories,
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '11px',
                        fontWeight: 600,
                        fontFamily: "'Plus Jakarta Sans', sans-serif"
                    },
                    formatter: function(val) {
                        if (typeof val === 'number') {
                            return val.toLocaleString('id-ID');
                        }
                        if (typeof val === 'string' && val.length > 20 && !isHorizontal) {
                            return val.substring(0, 18) + '...';
                        }
                        return val;
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '11px',
                        fontWeight: 600,
                        fontFamily: "'Plus Jakarta Sans', sans-serif"
                    },
                    formatter: function(val) {
                        if (typeof val === 'number') {
                            return val.toLocaleString('id-ID');
                        }
                        return val;
                    }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                padding: {
                    top: 10,
                    right: isHorizontal ? 40 : 15,
                    bottom: 0,
                    left: 10
                }
            },
            legend: {
                show: false
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
            }
        };

        new ApexCharts(chartElement, options).render();
    });
</script>
@endpush
@endif
