@extends('layouts.app')

@section('title', __('messages.supplier_details'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.purchases') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">{{ __('messages.suppliers') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $supplier->name }}</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Supplier Info Card --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-light-primary rounded-circle mx-auto mb-4"
                     style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-building-store text-primary" style="font-size: 3rem;"></i>
                </div>
                <h3 class="mb-2">{{ $supplier->name }}</h3>
                <p class="text-muted mb-4" dir="ltr">
                    <i class="ti ti-phone me-1"></i>
                    {{ $supplier->phone }}
                </p>

                {{-- Current Balance --}}
                <div class="bg-light rounded-3 p-4 mb-4">
                    <small class="text-muted d-block mb-2">{{ __('messages.current_balance') }}</small>
                    <h2 class="mb-0 {{ $supplier->balance > 0 ? 'text-success' : ($supplier->balance < 0 ? 'text-danger' : 'text-secondary') }}" dir="ltr">
                        {{ number_format($supplier->balance, 2) }}
                        <small class="fs-5">{{ __('messages.currency') }}</small>
                    </h2>
                    @if($supplier->balance > 0)
                        <small class="text-success">{{ __('messages.credit_balance') }}</small>
                    @elseif($supplier->balance < 0)
                        <small class="text-danger">{{ __('messages.debit_balance') }}</small>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="d-grid gap-2">
                    <a href="{{ route('suppliers.transactions.create', $supplier) }}" class="btn btn-success">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('messages.add_transaction') }}
                    </a>
                    <a href="{{ route('suppliers.transactions', $supplier) }}" class="btn btn-outline-primary">
                        <i class="ti ti-list me-1"></i>
                        {{ __('messages.supplier_transactions') }}
                    </a>
                    <a href="{{ route('suppliers.statement', $supplier) }}" class="btn btn-outline-info">
                        <i class="ti ti-file-text me-1"></i>
                        {{ __('messages.account_statement') }}
                    </a>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-secondary">
                        <i class="ti ti-edit me-1"></i>
                        {{ __('messages.edit') }}
                    </a>
                </div>
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
                                <small class="opacity-75">{{ __('messages.total_deposits') }}</small>
                                <h4 class="mb-0 text-white" dir="ltr">{{ number_format($supplier->total_deposits, 2) }}</h4>
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
                                <small class="opacity-75">{{ __('messages.total_withdrawals') }}</small>
                                <h4 class="mb-0 text-white" dir="ltr">{{ number_format($supplier->total_withdrawals, 2) }}</h4>
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
                                <h4 class="mb-0 text-white">{{ $supplier->transactions->count() }}</h4>
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
                <a href="{{ route('suppliers.transactions', $supplier) }}" class="btn btn-sm btn-outline-primary">
                    {{ __('messages.view_all') }}
                    <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @php
                    $recentTransactions = $supplier->transactions()->with(['cashbox'])->latest()->take(5)->get();
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
                                                    {{ __('messages.deposit') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">
                                                    <i class="ti ti-arrow-up-right me-1"></i>
                                                    {{ __('messages.withdrawal') }}
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
                        <span class="fw-semibold">{{ $supplier->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('messages.updated_at') }}</small>
                        <span class="fw-semibold">{{ $supplier->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="col-12">
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
            {{ __('messages.back') }}
        </a>
    </div>
</div>

<style>
.bg-light-primary {
    background-color: rgba(41, 26, 107, 0.1) !important;
}
.avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}
.avatar-sm {
    width: 35px;
    height: 35px;
}
.avatar-lg {
    width: 80px;
    height: 80px;
}
</style>
@endsection
