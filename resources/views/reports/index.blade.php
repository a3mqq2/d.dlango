@extends('layouts.app')

@section('title', __('messages.reports'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
    <li class="breadcrumb-item active">{{ __('messages.reports') }}</li>
@endsection

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .profit-positive {
        color: #28a745;
    }
    .profit-negative {
        color: #dc3545;
    }
    .summary-table td {
        padding: 8px 12px;
    }
    .summary-table .label {
        color: #6c757d;
    }
    .summary-table .value {
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="row">
    {{-- Date Filter --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.from_date') }}</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.to_date') }}</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.quick_filter') }}</label>
                        <select class="form-select" id="quickFilter">
                            <option value="">{{ __('messages.custom') }}</option>
                            <option value="today">{{ __('messages.today') }}</option>
                            <option value="yesterday">{{ __('messages.yesterday') }}</option>
                            <option value="this_week">{{ __('messages.this_week') }}</option>
                            <option value="last_week">{{ __('messages.last_week') }}</option>
                            <option value="this_month">{{ __('messages.this_month') }}</option>
                            <option value="last_month">{{ __('messages.last_month') }}</option>
                            <option value="this_year">{{ __('messages.this_year') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ti ti-filter me-1"></i>
                            {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-secondary" target="_blank">
                            <i class="ti ti-printer me-1"></i>
                            {{ __('messages.print') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Main Stats Cards --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-light-primary text-primary">
                        <i class="ti ti-shopping-cart"></i>
                    </div>
                    <div class="ms-3">
                        <p class="mb-1 text-muted">{{ __('messages.total_sales') }}</p>
                        <h4 class="mb-0">{{ number_format($salesStats['total_revenue'], 2) }}</h4>
                        <small class="text-muted">{{ $salesStats['total_sales'] }} {{ __('messages.invoice') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-light-warning text-warning">
                        <i class="ti ti-truck"></i>
                    </div>
                    <div class="ms-3">
                        <p class="mb-1 text-muted">{{ __('messages.total_purchases') }}</p>
                        <h4 class="mb-0">{{ number_format($purchasesStats['total_cost'], 2) }}</h4>
                        <small class="text-muted">{{ $purchasesStats['total_purchases'] }} {{ __('messages.invoice') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-light-success text-success">
                        <i class="ti ti-trending-up"></i>
                    </div>
                    <div class="ms-3">
                        <p class="mb-1 text-muted">{{ __('messages.gross_profit') }}</p>
                        <h4 class="mb-0 {{ $profitStats['gross_profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            {{ number_format($profitStats['gross_profit'], 2) }}
                        </h4>
                        <small class="text-muted">{{ $profitStats['gross_profit_margin'] }}% {{ __('messages.margin') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon {{ $profitStats['net_profit'] >= 0 ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">
                        <i class="ti ti-wallet"></i>
                    </div>
                    <div class="ms-3">
                        <p class="mb-1 text-muted">{{ __('messages.net_profit') }}</p>
                        <h4 class="mb-0 {{ $profitStats['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            {{ number_format($profitStats['net_profit'], 2) }}
                        </h4>
                        <small class="text-muted">{{ $profitStats['net_profit_margin'] }}% {{ __('messages.margin') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Statistics --}}
    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-report-money me-2"></i>
                    {{ __('messages.sales_details') }}
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless summary-table mb-0">
                    <tr>
                        <td class="label">{{ __('messages.total_revenue') }}</td>
                        <td class="value text-end">{{ number_format($salesStats['total_revenue'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.total_discounts') }}</td>
                        <td class="value text-end text-danger">-{{ number_format($salesStats['total_discount'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.returns') }}</td>
                        <td class="value text-end text-danger">-{{ number_format($returnsStats['total_refunded'], 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="label fw-bold">{{ __('messages.net_sales') }}</td>
                        <td class="value text-end fw-bold">{{ number_format($profitStats['net_revenue'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.items_sold') }}</td>
                        <td class="value text-end">{{ number_format($salesStats['items_sold']) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.average_sale') }}</td>
                        <td class="value text-end">{{ number_format($salesStats['average_sale'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.paid_amount') }}</td>
                        <td class="value text-end text-success">{{ number_format($salesStats['total_paid'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.remaining_amount') }}</td>
                        <td class="value text-end text-warning">{{ number_format($salesStats['total_remaining'], 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-calculator me-2"></i>
                    {{ __('messages.profit_details') }}
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless summary-table mb-0">
                    <tr>
                        <td class="label">{{ __('messages.net_sales') }}</td>
                        <td class="value text-end">{{ number_format($profitStats['net_revenue'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.cost_of_goods_sold') }}</td>
                        <td class="value text-end text-danger">-{{ number_format($profitStats['cost_of_goods_sold'], 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="label fw-bold">{{ __('messages.gross_profit') }}</td>
                        <td class="value text-end fw-bold {{ $profitStats['gross_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($profitStats['gross_profit'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.expenses') }}</td>
                        <td class="value text-end text-danger">-{{ number_format($expensesStats['total_expenses'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.other_income') }}</td>
                        <td class="value text-end text-success">+{{ number_format($expensesStats['total_other_income'], 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="label fw-bold">{{ __('messages.net_profit') }}</td>
                        <td class="value text-end fw-bold {{ $profitStats['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($profitStats['net_profit'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.gross_profit_margin') }}</td>
                        <td class="value text-end">{{ $profitStats['gross_profit_margin'] }}%</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.net_profit_margin') }}</td>
                        <td class="value text-end">{{ $profitStats['net_profit_margin'] }}%</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-credit-card me-2"></i>
                    {{ __('messages.payment_methods') }}
                </h5>
            </div>
            <div class="card-body">
                @if($salesByPayment->count() > 0)
                    <table class="table table-borderless summary-table mb-0">
                        @foreach($salesByPayment as $payment)
                            <tr>
                                <td class="label">
                                    @if($payment->payment_method == 'cash')
                                        <i class="ti ti-cash me-1"></i>
                                    @elseif($payment->payment_method == 'card')
                                        <i class="ti ti-credit-card me-1"></i>
                                    @else
                                        <i class="ti ti-building-bank me-1"></i>
                                    @endif
                                    {{ __('messages.' . $payment->payment_method) }}
                                </td>
                                <td class="value text-end">
                                    {{ number_format($payment->total, 2) }}
                                    <small class="text-muted d-block">{{ $payment->count }} {{ __('messages.sale') }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <p class="text-muted text-center mb-0">{{ __('messages.no_data') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Sales Chart --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-chart-line me-2"></i>
                    {{ __('messages.sales_chart') }}
                </h5>
            </div>
            <div class="card-body">
                <div id="salesChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="col-xl-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-star me-2"></i>
                    {{ __('messages.top_selling_products') }}
                </h5>
            </div>
            <div class="card-body">
                @if($topProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.product') }}</th>
                                    <th class="text-center">{{ __('messages.quantity') }}</th>
                                    <th class="text-end">{{ __('messages.revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            <small class="text-muted d-block">{{ $product->code }}</small>
                                        </td>
                                        <td class="text-center">{{ number_format($product->total_quantity) }}</td>
                                        <td class="text-end">{{ number_format($product->total_revenue, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">{{ __('messages.no_data') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Customers --}}
    <div class="col-xl-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>
                    {{ __('messages.top_customers') }}
                </h5>
            </div>
            <div class="card-body">
                @if($topCustomers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.customer') }}</th>
                                    <th class="text-center">{{ __('messages.orders') }}</th>
                                    <th class="text-end">{{ __('messages.total_spent') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCustomers as $index => $customer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $customer->name }}</strong>
                                            <small class="text-muted d-block">{{ $customer->phone }}</small>
                                        </td>
                                        <td class="text-center">{{ $customer->total_orders }}</td>
                                        <td class="text-end">{{ number_format($customer->total_spent, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">{{ __('messages.no_data') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Sales by User --}}
    <div class="col-xl-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-user-check me-2"></i>
                    {{ __('messages.sales_by_user') }}
                </h5>
            </div>
            <div class="card-body">
                @if($salesByUser->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.user') }}</th>
                                    <th class="text-center">{{ __('messages.sales_count') }}</th>
                                    <th class="text-end">{{ __('messages.total_revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesByUser as $user)
                                    <tr>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td class="text-center">{{ $user->total_sales }}</td>
                                        <td class="text-end">{{ number_format($user->total_revenue, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">{{ __('messages.no_data') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Expenses by Category --}}
    <div class="col-xl-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-receipt-2 me-2"></i>
                    {{ __('messages.expenses_by_category') }}
                </h5>
            </div>
            <div class="card-body">
                @if($expensesStats['expenses_by_category']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.category') }}</th>
                                    <th class="text-end">{{ __('messages.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expensesStats['expenses_by_category'] as $expense)
                                    <tr>
                                        <td>{{ $expense->category?->name ?? __('messages.uncategorized') }}</td>
                                        <td class="text-end text-danger">{{ number_format($expense->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td class="fw-bold">{{ __('messages.total') }}</td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($expensesStats['total_expenses'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">{{ __('messages.no_expenses') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick Filter
    const quickFilter = document.getElementById('quickFilter');
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');

    quickFilter.addEventListener('change', function() {
        const today = new Date();
        let startDate, endDate;

        switch(this.value) {
            case 'today':
                startDate = endDate = formatDate(today);
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                startDate = endDate = formatDate(yesterday);
                break;
            case 'this_week':
                const weekStart = new Date(today);
                weekStart.setDate(today.getDate() - today.getDay());
                startDate = formatDate(weekStart);
                endDate = formatDate(today);
                break;
            case 'last_week':
                const lastWeekEnd = new Date(today);
                lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                const lastWeekStart = new Date(lastWeekEnd);
                lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                startDate = formatDate(lastWeekStart);
                endDate = formatDate(lastWeekEnd);
                break;
            case 'this_month':
                startDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                endDate = formatDate(today);
                break;
            case 'last_month':
                startDate = formatDate(new Date(today.getFullYear(), today.getMonth() - 1, 1));
                endDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 0));
                break;
            case 'this_year':
                startDate = formatDate(new Date(today.getFullYear(), 0, 1));
                endDate = formatDate(today);
                break;
            default:
                return;
        }

        startDateInput.value = startDate;
        endDateInput.value = endDate;
    });

    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    // Sales Chart
    const salesData = @json($dailySales);

    if (typeof ApexCharts !== 'undefined') {
        const chartOptions = {
            series: [{
                name: '{{ __("messages.sales") }}',
                data: salesData.totals
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: true
                },
                fontFamily: 'Changa, sans-serif'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                }
            },
            xaxis: {
                categories: salesData.labels,
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

        const chart = new ApexCharts(document.querySelector("#salesChart"), chartOptions);
        chart.render();
    }
});
</script>
@endpush
