@extends('layouts.app')

@section('title', __('messages.customer_statement') . ' - ' . $customer->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.sales') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('customers.index') }}">{{ __('messages.customers') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.account_statement') }}</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Customer Info Card --}}
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-primary text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h4 class="mb-1 text-white">
                            <i class="ti ti-user me-2"></i>
                            {{ $customer->name }}
                        </h4>
                        <p class="mb-0 opacity-75" dir="ltr">{{ $customer->phone ?? __('messages.not_available') }}</p>
                    </div>
                    <div class="col-md-8">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="bg-white bg-opacity-25 rounded-3 p-3">
                                    <small class="d-block opacity-75">{{ __('messages.opening_balance') }}</small>
                                    <h4 class="mb-0 text-white" dir="ltr">{{ number_format($openingBalance, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-success bg-opacity-50 rounded-3 p-3">
                                    <small class="d-block opacity-75">{{ __('messages.total_payments') }}</small>
                                    <h4 class="mb-0 text-white" dir="ltr">+{{ number_format($totalDeposits, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-danger bg-opacity-50 rounded-3 p-3">
                                    <small class="d-block opacity-75">{{ __('messages.total_credits') }}</small>
                                    <h4 class="mb-0 text-white" dir="ltr">-{{ number_format($totalWithdrawals, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <form action="{{ route('customers.statement', $customer) }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">{{ __('messages.from_date') }}</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">{{ __('messages.to_date') }}</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">{{ __('messages.type') }}</label>
                        <select name="type" class="form-select">
                            <option value="">{{ __('messages.all_types') }}</option>
                            <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>{{ __('messages.payment') }}</option>
                            <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>{{ __('messages.credit') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-filter me-1"></i>
                            {{ __('messages.filter') }}
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('customers.statement.print', $customer) }}?from_date={{ $fromDate }}&to_date={{ $toDate }}&type={{ request('type') }}"
                           class="btn btn-outline-secondary w-100" target="_blank">
                            <i class="ti ti-printer me-1"></i>
                            {{ __('messages.print') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Statement Table --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-file-text me-2"></i>
                        {{ __('messages.account_statement') }}
                    </h5>
                    <span class="text-muted">
                        {{ __('messages.from') }} {{ $fromDate }} {{ __('messages.to') }} {{ $toDate }}
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.description') }}</th>
                                    <th width="12%">{{ __('messages.payment') }}</th>
                                    <th width="12%">{{ __('messages.credit') }}</th>
                                    <th width="15%">{{ __('messages.balance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Opening Balance Row --}}
                                <tr class="table-secondary">
                                    <td colspan="5" class="fw-bold">{{ __('messages.opening_balance') }}</td>
                                    <td class="fw-bold {{ $openingBalance <= 0 ? 'text-success' : 'text-danger' }}" dir="ltr">
                                        {{ number_format($openingBalance, 2) }}
                                    </td>
                                </tr>

                                @php $runningBalance = $openingBalance; @endphp
                                @foreach($transactions as $transaction)
                                    @php
                                        // For customers: deposits reduce balance (they pay), withdrawals increase balance (they owe)
                                        if ($transaction->type == 'deposit') {
                                            $runningBalance -= $transaction->amount;
                                        } else {
                                            $runningBalance += $transaction->amount;
                                        }
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">{{ $loop->iteration }}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            {{ $transaction->description ?? '-' }}
                                            @if($transaction->category)
                                                <br><small class="text-muted">{{ $transaction->category->name }}</small>
                                            @endif
                                        </td>
                                        <td dir="ltr" class="text-success fw-semibold">
                                            {{ $transaction->type == 'deposit' ? number_format($transaction->amount, 2) : '-' }}
                                        </td>
                                        <td dir="ltr" class="text-danger fw-semibold">
                                            {{ $transaction->type == 'withdrawal' ? number_format($transaction->amount, 2) : '-' }}
                                        </td>
                                        <td dir="ltr" class="fw-bold {{ $runningBalance <= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($runningBalance, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-primary">
                                <tr>
                                    <td colspan="3" class="fw-bold text-end">{{ __('messages.totals') }}</td>
                                    <td class="fw-bold text-success" dir="ltr">{{ number_format($totalDeposits, 2) }}</td>
                                    <td class="fw-bold text-danger" dir="ltr">{{ number_format($totalWithdrawals, 2) }}</td>
                                    <td class="fw-bold {{ $runningBalance <= 0 ? 'text-success' : 'text-danger' }}" dir="ltr">
                                        {{ number_format($runningBalance, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-receipt-off text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">{{ __('messages.no_transactions_in_period') }}</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border-end">
                            <h6 class="text-muted mb-2">{{ __('messages.opening_balance') }}</h6>
                            <h3 class="{{ $openingBalance <= 0 ? 'text-success' : 'text-danger' }}" dir="ltr">
                                {{ number_format($openingBalance, 2) }}
                                <small class="fs-6 text-muted">{{ __('messages.currency') }}</small>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h6 class="text-muted mb-2">{{ __('messages.total_payments') }}</h6>
                            <h3 class="text-success" dir="ltr">
                                +{{ number_format($totalDeposits, 2) }}
                                <small class="fs-6 text-muted">{{ __('messages.currency') }}</small>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h6 class="text-muted mb-2">{{ __('messages.total_credits') }}</h6>
                            <h3 class="text-danger" dir="ltr">
                                -{{ number_format($totalWithdrawals, 2) }}
                                <small class="fs-6 text-muted">{{ __('messages.currency') }}</small>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">{{ __('messages.closing_balance') }}</h6>
                        @php $closingBalance = $openingBalance + $totalWithdrawals - $totalDeposits; @endphp
                        <h3 class="{{ $closingBalance <= 0 ? 'text-success' : 'text-danger' }}" dir="ltr">
                            {{ number_format($closingBalance, 2) }}
                            <small class="fs-6 text-muted">{{ __('messages.currency') }}</small>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="col-12">
        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
            {{ __('messages.back') }}
        </a>
    </div>
</div>
@endsection
