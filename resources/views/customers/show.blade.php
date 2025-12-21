@extends('layouts.app')

@section('title', __('messages.customer_details'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.sales') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('customers.index') }}">{{ __('messages.customers') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $customer->name }}</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Customer Info Card --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-light-primary rounded-circle mx-auto mb-4"
                     style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-user text-primary" style="font-size: 3rem;"></i>
                </div>
                <h3 class="mb-2">{{ $customer->name }}</h3>
                @if($customer->is_default)
                    <span class="badge bg-info mb-2">{{ __('messages.default_customer') }}</span>
                @endif
                <p class="text-muted mb-4" dir="ltr">
                    <i class="ti ti-phone me-1"></i>
                    {{ $customer->phone ?? __('messages.not_available') }}
                </p>

                {{-- Current Balance --}}
                <div class="bg-light rounded-3 p-4 mb-4">
                    <small class="text-muted d-block mb-2">{{ __('messages.current_balance') }}</small>
                    <h2 class="mb-0 {{ $customer->balance > 0 ? 'text-danger' : ($customer->balance < 0 ? 'text-success' : 'text-secondary') }}" dir="ltr">
                        {{ number_format($customer->balance, 2) }}
                        <small class="fs-5">{{ __('messages.currency') }}</small>
                    </h2>
                    @if($customer->balance > 0)
                        <small class="text-danger">{{ __('messages.customer_owes_us') }}</small>
                    @elseif($customer->balance < 0)
                        <small class="text-success">{{ __('messages.we_owe_customer') }}</small>
                    @endif
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    @if($customer->is_active)
                        <span class="badge bg-success-subtle text-success fs-6">
                            <i class="ti ti-check me-1"></i>
                            {{ __('messages.active') }}
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger fs-6">
                            <i class="ti ti-x me-1"></i>
                            {{ __('messages.inactive') }}
                        </span>
                    @endif
                </div>

                {{-- Action Buttons --}}
                @if(!$customer->is_default)
                <div class="d-grid gap-2">
                    <a href="{{ route('customers.transactions.create', $customer) }}" class="btn btn-success">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('messages.add_transaction') }}
                    </a>
                    <a href="{{ route('customers.transactions', $customer) }}" class="btn btn-outline-primary">
                        <i class="ti ti-list me-1"></i>
                        {{ __('messages.customer_transactions') }}
                    </a>
                    <a href="{{ route('customers.statement', $customer) }}" class="btn btn-outline-info">
                        <i class="ti ti-file-text me-1"></i>
                        {{ __('messages.account_statement') }}
                    </a>
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-secondary">
                        <i class="ti ti-edit me-1"></i>
                        {{ __('messages.edit') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats & Transactions --}}
    <div class="col-lg-8">
        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-success text-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-white bg-opacity-25 rounded">
                                    <i class="ti ti-arrow-down-left text-white fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="opacity-75">{{ __('messages.total_payments') }}</small>
                                <h4 class="mb-0 text-white" dir="ltr">{{ number_format($customer->total_deposits, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-danger text-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-white bg-opacity-25 rounded">
                                    <i class="ti ti-arrow-up-right text-white fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="opacity-75">{{ __('messages.total_credits') }}</small>
                                <h4 class="mb-0 text-white" dir="ltr">{{ number_format($customer->total_withdrawals, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-primary text-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-white bg-opacity-25 rounded">
                                    <i class="ti ti-receipt text-white fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="opacity-75">{{ __('messages.transactions_count') }}</small>
                                <h4 class="mb-0 text-white">{{ $customer->transactions->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-history me-2"></i>
                    {{ __('messages.recent_transactions') }}
                </h5>
                @if(!$customer->is_default)
                <a href="{{ route('customers.transactions', $customer) }}" class="btn btn-sm btn-outline-primary">
                    {{ __('messages.view_all') }}
                    <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} ms-1"></i>
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                @php
                    $recentTransactions = $customer->transactions()->with(['cashbox'])->latest()->take(5)->get();
                @endphp

                @if($recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.type') }}</th>
                                    <th>{{ __('messages.amount') }}</th>
                                    <th>{{ __('messages.cashbox') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            @if($transaction->type == 'deposit')
                                                <span class="badge bg-success-subtle text-success">
                                                    <i class="ti ti-arrow-down-left me-1"></i>
                                                    {{ __('messages.payment') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">
                                                    <i class="ti ti-arrow-up-right me-1"></i>
                                                    {{ __('messages.credit') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td dir="ltr" class="fw-bold {{ $transaction->type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td>{{ $transaction->cashbox->name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-receipt-off text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">{{ __('messages.no_transactions') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Sales --}}
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-shopping-cart me-2"></i>
                    {{ __('messages.recent_sales') }}
                </h5>
            </div>
            <div class="card-body p-0">
                @php
                    $recentSales = $customer->sales()->with('user')->latest()->take(5)->get();
                @endphp

                @if($recentSales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.invoice_number') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.total') }}</th>
                                    <th>{{ __('messages.payment_method') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSales as $sale)
                                    <tr>
                                        <td>
                                            <a href="{{ route('pos.show', $sale) }}" class="fw-semibold text-primary">
                                                {{ $sale->invoice_number }}
                                            </a>
                                        </td>
                                        <td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                                        <td dir="ltr" class="fw-bold">{{ number_format($sale->total_amount, 2) }}</td>
                                        <td>
                                            @if($sale->payment_method == 'cash')
                                                <span class="badge bg-success-subtle text-success">{{ __('messages.cash') }}</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">{{ __('messages.credit') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $sale->status == 'completed' ? 'success' : 'secondary' }}-subtle text-{{ $sale->status == 'completed' ? 'success' : 'secondary' }}">
                                                {{ __('messages.' . $sale->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-receipt-off text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">{{ __('messages.no_sales_yet') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- System Information --}}
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 text-primary">
                    <i class="ti ti-info-circle me-2"></i>
                    {{ __('messages.system_info') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('messages.created_at') }}</small>
                        <span class="fw-semibold">{{ $customer->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('messages.updated_at') }}</small>
                        <span class="fw-semibold">{{ $customer->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('messages.customer_type') }}</small>
                        <span class="fw-semibold">
                            @if($customer->is_default)
                                {{ __('messages.default_customer') }}
                            @else
                                {{ __('messages.regular_customer') }}
                            @endif
                        </span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('messages.status') }}</small>
                        <span class="fw-semibold">
                            @if($customer->is_active)
                                <span class="text-success">{{ __('messages.active') }}</span>
                            @else
                                <span class="text-danger">{{ __('messages.inactive') }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="col-12">
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
            {{ __('messages.back') }}
        </a>
    </div>
</div>

<style>
.bg-light-primary {
    background-color: rgba(182, 95, 122, 0.1) !important;
}
.avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}
.avatar-lg {
    width: 80px;
    height: 80px;
}
</style>
@endsection
