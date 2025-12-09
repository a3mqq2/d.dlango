@extends('layouts.app')

@section('title', __('messages.dashboard'))
@section('skip-dashboard-analytics', true)

@section('content')
<div class="row">
    {{-- Welcome Section --}}
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white" style="background: linear-gradient(135deg, #b65f7a 0%, #a93257 100%) !important;">
            <div class="card-body py-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 text-white">{{ __('messages.welcome_back') }}، {{ auth()->user()->name }}!</h4>
                        <p class="mb-0 opacity-75">{{ now()->translatedFormat('l، j F Y') }}</p>
                    </div>
                    <div class="d-none d-md-block">
                        <i class="ti ti-chart-line" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap gap-2">
                    @if(auth()->user()->hasPermission('sales.pos'))
                    <a href="{{ route('pos.index') }}" class="btn btn-primary">
                        <i class="ti ti-device-desktop me-1"></i>
                        {{ __('messages.pos') }}
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('purchases.create'))
                    <a href="{{ route('purchase-invoices.create') }}" class="btn btn-outline-primary">
                        <i class="ti ti-file-plus me-1"></i>
                        {{ __('messages.add_purchase_invoice') }}
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('customers.create'))
                    <a href="{{ route('customers.create') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-plus me-1"></i>
                        {{ __('messages.add_customer') }}
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('returns.create'))
                    <a href="{{ route('returns.create') }}" class="btn btn-outline-primary">
                        <i class="ti ti-receipt-refund me-1"></i>
                        {{ __('messages.new_return') }}
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('reports.view'))
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-chart-bar me-1"></i>
                        {{ __('messages.reports') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Statistics --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-light-primary">
                        <i class="ti ti-shopping-cart text-primary"></i>
                    </div>
                    <div class="ms-3">
                        <p class="mb-1 text-muted">{{ __('messages.today_sales') }}</p>
                        <h4 class="mb-0">{{ number_format($todaySales ?? 0, 2) }}</h4>
                        <small class="text-muted">{{ $todaySalesCount ?? 0 }} {{ __('messages.invoice') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-light-success">
                        <i class="ti ti-trending-up text-success"></i>
                    </div>
                    <div class="ms-3">
                        <p class="mb-1 text-muted">{{ __('messages.monthly_sales') }}</p>
                        <h4 class="mb-0">{{ number_format($monthlySales ?? 0, 2) }}</h4>
                        <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon {{ ($monthlyProfit ?? 0) >= 0 ? 'bg-light-success' : 'bg-light-danger' }}">
                        <i class="ti ti-wallet {{ ($monthlyProfit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}"></i>
                    </div>
                    <div class="ms-3">
                        <p class="mb-1 text-muted">{{ __('messages.monthly_profit') }}</p>
                        <h4 class="mb-0 {{ ($monthlyProfit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($monthlyProfit ?? 0, 2) }}
                        </h4>
                        <small class="text-muted">{{ __('messages.estimated') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-light-info">
                        <i class="ti ti-cash text-info"></i>
                    </div>
                    <div class="ms-3">
                        <p class="mb-1 text-muted">{{ __('messages.cashbox_balance') }}</p>
                        <h4 class="mb-0">{{ number_format($totalCashboxBalance ?? 0, 2) }}</h4>
                        <small class="text-muted">{{ __('messages.all_cashboxes') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Info Cards --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ auth()->user()->hasPermission('inventory.view') ? route('inventory.index') : '#' }}" class="text-decoration-none">
            <div class="card hover-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">{{ __('messages.total_products') }}</p>
                            <h4 class="mb-0 text-dark">{{ $totalProducts ?? 0 }}</h4>
                        </div>
                        <div class="avtar bg-light-primary">
                            <i class="ti ti-box text-primary fs-4"></i>
                        </div>
                    </div>
                    @if(($lowStockProducts ?? 0) > 0 || ($outOfStockProducts ?? 0) > 0)
                    <div class="mt-2">
                        @if(($lowStockProducts ?? 0) > 0)
                        <span class="badge bg-warning me-1">{{ $lowStockProducts }} {{ __('messages.low_stock') }}</span>
                        @endif
                        @if(($outOfStockProducts ?? 0) > 0)
                        <span class="badge bg-danger">{{ $outOfStockProducts }} {{ __('messages.out_of_stock') }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ auth()->user()->hasPermission('customers.view') ? route('customers.index') : '#' }}" class="text-decoration-none">
            <div class="card hover-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">{{ __('messages.customers') }}</p>
                            <h4 class="mb-0 text-dark">{{ $totalCustomers ?? 0 }}</h4>
                        </div>
                        <div class="avtar bg-light-success">
                            <i class="ti ti-users text-success fs-4"></i>
                        </div>
                    </div>
                    @if(($pendingFromCustomers ?? 0) > 0)
                    <div class="mt-2">
                        <small class="text-warning">
                            <i class="ti ti-alert-circle me-1"></i>
                            {{ __('messages.pending_receivables') }}: {{ number_format($pendingFromCustomers, 2) }}
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ auth()->user()->hasPermission('suppliers.view') ? route('suppliers.index') : '#' }}" class="text-decoration-none">
            <div class="card hover-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">{{ __('messages.suppliers') }}</p>
                            <h4 class="mb-0 text-dark">{{ $totalSuppliers ?? 0 }}</h4>
                        </div>
                        <div class="avtar bg-light-warning">
                            <i class="ti ti-truck-delivery text-warning fs-4"></i>
                        </div>
                    </div>
                    @if(($pendingToSuppliers ?? 0) > 0)
                    <div class="mt-2">
                        <small class="text-danger">
                            <i class="ti ti-alert-circle me-1"></i>
                            {{ __('messages.pending_payables') }}: {{ number_format($pendingToSuppliers, 2) }}
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </a>
    </div>

    @if(auth()->user()->isAdmin())
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('users.index') }}" class="text-decoration-none">
            <div class="card hover-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-muted">{{ __('messages.users') }}</p>
                            <h4 class="mb-0 text-dark">{{ $totalUsers ?? 0 }}</h4>
                        </div>
                        <div class="avtar bg-light-info">
                            <i class="ti ti-user-check text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="ti ti-circle-check me-1"></i>
                            {{ $activeUsers ?? 0 }} {{ __('messages.active') }}
                        </small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @else
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-muted">{{ __('messages.today_returns') }}</p>
                        <h4 class="mb-0 text-danger">{{ number_format($todayReturns ?? 0, 2) }}</h4>
                    </div>
                    <div class="avtar bg-light-danger">
                        <i class="ti ti-receipt-refund text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Weekly Sales Chart --}}
    <div class="col-xl-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-chart-line me-2"></i>
                    {{ __('messages.weekly_sales') }}
                </h5>
                <span class="badge bg-light-primary text-primary">{{ __('messages.last_7_days') }}</span>
            </div>
            <div class="card-body">
                <div id="weeklySalesChart" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    {{-- Top Products Today --}}
    <div class="col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-star me-2"></i>
                    {{ __('messages.top_products_today') }}
                </h5>
            </div>
            <div class="card-body p-0">
                @if(count($topProductsToday ?? []) > 0)
                <ul class="list-group list-group-flush">
                    @foreach($topProductsToday as $index => $product)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-{{ $index == 0 ? 'primary' : 'light text-dark' }} rounded-pill me-2">{{ $index + 1 }}</span>
                            <div>
                                <span class="fw-medium">{{ $product->name }}</span>
                                <small class="d-block text-muted">{{ $product->code }}</small>
                            </div>
                        </div>
                        <span class="badge bg-success">{{ $product->total_qty }} {{ __('messages.unit') }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="ti ti-package-off fs-1 d-block mb-2"></i>
                    {{ __('messages.no_sales_today') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Sales --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-receipt me-2"></i>
                    {{ __('messages.recent_sales') }}
                </h5>
                @if(auth()->user()->hasPermission('sales.view'))
                <a href="{{ route('pos.history') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('messages.view_all') }}
                    <i class="ti ti-arrow-left ms-1"></i>
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                @if(count($recentSales ?? []) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('messages.invoice_number') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.cashier') }}</th>
                                <th class="text-end">{{ __('messages.total_amount') }}</th>
                                <th>{{ __('messages.payment_method') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSales as $sale)
                            <tr>
                                <td>
                                    <span class="fw-medium">{{ $sale->invoice_number }}</span>
                                </td>
                                <td>{{ $sale->customer?->name ?? __('messages.walk_in_customer') }}</td>
                                <td>{{ $sale->user?->name }}</td>
                                <td class="text-end fw-bold">{{ number_format($sale->total_amount, 2) }}</td>
                                <td>
                                    @if($sale->payment_method == 'cash')
                                    <span class="badge bg-light-success text-success">{{ __('messages.cash') }}</span>
                                    @elseif($sale->payment_method == 'credit')
                                    <span class="badge bg-light-warning text-warning">{{ __('messages.credit') }}</span>
                                    @else
                                    <span class="badge bg-light-info text-info">{{ __('messages.' . $sale->payment_method) }}</span>
                                    @endif
                                </td>
                                <td>{{ $sale->sale_date->format('Y/m/d H:i') }}</td>
                                <td>
                                    @if(auth()->user()->hasPermission('sales.view'))
                                    <a href="{{ route('pos.show', $sale) }}" class="btn btn-sm btn-light">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="ti ti-receipt-off fs-1 d-block mb-2"></i>
                    {{ __('messages.no_sales_yet') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-3px);
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}
.avtar {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
}
.bg-light-primary { background-color: rgba(182, 95, 122, 0.1) !important; }
.bg-light-success { background-color: rgba(40, 167, 69, 0.1) !important; }
.bg-light-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
.bg-light-danger { background-color: rgba(220, 53, 69, 0.1) !important; }
.bg-light-info { background-color: rgba(23, 162, 184, 0.1) !important; }
.text-primary { color: #b65f7a !important; }
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #17a2b8 !important; }
.hover-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const weeklySalesData = @json($weeklySales ?? ['labels' => [], 'totals' => []]);

    if (typeof ApexCharts !== 'undefined' && weeklySalesData.labels.length > 0) {
        const chartOptions = {
            series: [{
                name: '{{ __("messages.sales") }}',
                data: weeklySalesData.totals
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: {
                    show: false
                },
                fontFamily: 'Changa, sans-serif'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.5,
                    opacityTo: 0.1,
                }
            },
            xaxis: {
                categories: weeklySalesData.labels,
                labels: {
                    style: {
                        fontFamily: 'Changa, sans-serif'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return val.toFixed(0);
                    },
                    style: {
                        fontFamily: 'Changa, sans-serif'
                    }
                }
            },
            colors: ['#b65f7a'],
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val.toFixed(2);
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };

        const chart = new ApexCharts(document.querySelector("#weeklySalesChart"), chartOptions);
        chart.render();
    }
});
</script>
@endpush
