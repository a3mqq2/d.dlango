@extends('layouts.app')

@section('title', __('messages.account_statement'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.finance') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">{{ __('messages.transactions') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.account_statement') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-file-text me-2"></i>
                    {{ __('messages.account_statement') }}
                </h5>
            </div>
            <div class="card-body">
                {{-- Filter Form --}}
                <form method="GET" action="{{ route('transactions.statement') }}" id="statementForm">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                {{ __('messages.cashbox') }}
                                <span class="text-danger">*</span>
                            </label>
                            <select name="cashbox_id" class="form-select" required>
                                <option value="">{{ __('messages.select_cashbox') }}</option>
                                @foreach($cashboxes as $cashbox)
                                    <option value="{{ $cashbox->id }}" {{ request('cashbox_id') == $cashbox->id ? 'selected' : '' }}>
                                        {{ $cashbox->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                {{ __('messages.transaction_category') }}
                            </label>
                            <select name="category_id" class="form-select">
                                <option value="">{{ __('messages.all_categories') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                {{ __('messages.from_date') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}" required dir="ltr">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                {{ __('messages.to_date') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date', now()->format('Y-m-d')) }}" required dir="ltr">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search me-1"></i>
                                {{ __('messages.show') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedCashbox)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 text-primary">
                            {{ $selectedCashbox->name }}
                        </h5>
                        <small class="text-muted" dir="ltr">
                            {{ __('messages.from') }}: {{ request('from_date') }}
                            {{ __('messages.to') }}: {{ request('to_date') }}
                        </small>
                    </div>
                    <a href="{{ route('transactions.print-statement', request()->all()) }}"
                       class="btn btn-outline-primary"
                       target="_blank">
                        <i class="ti ti-printer me-1"></i>
                        {{ __('messages.print') }}
                    </a>
                </div>
                <div class="card-body">
                    {{-- Summary Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">{{ __('messages.opening_balance') }}</small>
                                    <h4 class="mb-0 text-primary" dir="ltr">
                                        {{ number_format($openingBalance, 2) }}
                                        <small class="text-muted fs-6">{{ __('messages.currency') }}</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success bg-opacity-10 border-0">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">{{ __('messages.total_deposits') }}</small>
                                    <h4 class="mb-0 text-success" dir="ltr">
                                        +{{ number_format($totalDeposits, 2) }}
                                        <small class="text-muted fs-6">{{ __('messages.currency') }}</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger bg-opacity-10 border-0">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">{{ __('messages.total_withdrawals') }}</small>
                                    <h4 class="mb-0 text-danger" dir="ltr">
                                        -{{ number_format($totalWithdrawals, 2) }}
                                        <small class="text-muted fs-6">{{ __('messages.currency') }}</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary bg-opacity-10 border-0">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">{{ __('messages.closing_balance') }}</small>
                                    <h4 class="mb-0 text-primary" dir="ltr">
                                        {{ number_format($closingBalance, 2) }}
                                        <small class="text-muted fs-6">{{ __('messages.currency') }}</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Transactions Table --}}
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">{{ __('messages.date') }}</th>
                                        <th width="25%">{{ __('messages.description') }}</th>
                                        <th width="15%">{{ __('messages.category') }}</th>
                                        <th width="15%" class="text-end">{{ __('messages.deposits') }}</th>
                                        <th width="15%" class="text-end">{{ __('messages.withdrawals') }}</th>
                                        <th width="20%" class="text-end">{{ __('messages.balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $runningBalance = $openingBalance; @endphp
                                    @foreach($transactions as $transaction)
                                        @php
                                            if ($transaction->type == 'deposit') {
                                                $runningBalance += $transaction->amount;
                                            } else {
                                                $runningBalance -= $transaction->amount;
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                <small dir="ltr">{{ $transaction->created_at->format('Y-m-d') }}</small>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $transaction->recipient_name }}</strong>
                                                    @if($transaction->description)
                                                        <br><small class="text-muted">{{ $transaction->description }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ $transaction->category->name }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if($transaction->type == 'deposit')
                                                    <span class="text-success fw-bold" dir="ltr">
                                                        {{ number_format($transaction->amount, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($transaction->type == 'withdrawal')
                                                    <span class="text-danger fw-bold" dir="ltr">
                                                        {{ number_format($transaction->amount, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-{{ $runningBalance > 0 ? 'success' : ($runningBalance < 0 ? 'danger' : 'secondary') }}" dir="ltr">
                                                    {{ number_format($runningBalance, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">{{ __('messages.totals') }}</td>
                                        <td class="text-end text-success" dir="ltr">
                                            {{ number_format($totalDeposits, 2) }}
                                        </td>
                                        <td class="text-end text-danger" dir="ltr">
                                            {{ number_format($totalWithdrawals, 2) }}
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $closingBalance > 0 ? 'success' : ($closingBalance < 0 ? 'danger' : 'secondary') }} fs-6 px-3 py-2" dir="ltr">
                                                {{ number_format($closingBalance, 2) }}
                                                <small>{{ __('messages.currency') }}</small>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">{{ __('messages.net_change') }}</td>
                                        <td colspan="3" class="text-end">
                                            <span class="text-{{ ($closingBalance - $openingBalance) > 0 ? 'success' : (($closingBalance - $openingBalance) < 0 ? 'danger' : 'secondary') }}" dir="ltr">
                                                {{ ($closingBalance - $openingBalance) > 0 ? '+' : '' }}
                                                {{ number_format($closingBalance - $openingBalance, 2) }}
                                                <small>{{ __('messages.currency') }}</small>
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="ti ti-file-off" style="font-size: 3rem; opacity: 0.3;"></i>
                            <h5 class="mt-3">{{ __('messages.no_transactions_in_period') }}</h5>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
