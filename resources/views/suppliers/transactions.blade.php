@extends('layouts.app')

@section('title', __('messages.supplier_transactions') . ' - ' . $supplier->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.purchases') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">{{ __('messages.suppliers') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('suppliers.show', $supplier) }}">{{ $supplier->name }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.transactions') }}</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Supplier Info Card --}}
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-primary text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-1 text-white">
                            <i class="ti ti-building-store me-2"></i>
                            {{ $supplier->name }}
                        </h4>
                        <p class="mb-0 opacity-75" dir="ltr">{{ $supplier->phone }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-inline-block bg-white bg-opacity-25 rounded-3 px-4 py-3">
                            <small class="d-block opacity-75">{{ __('messages.current_balance') }}</small>
                            <h3 class="mb-0 text-white {{ $supplier->balance < 0 ? 'text-warning' : '' }}" dir="ltr">
                                {{ number_format($supplier->balance, 2) }}
                                <small class="fs-6">{{ __('messages.currency') }}</small>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions & Filters --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <form action="{{ route('suppliers.transactions', $supplier) }}" method="GET" class="row g-2">
                            <div class="col-md-3">
                                <select name="type" class="form-select">
                                    <option value="">{{ __('messages.all_types') }}</option>
                                    <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>{{ __('messages.deposit') }}</option>
                                    <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>{{ __('messages.withdrawal') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="{{ __('messages.from_date') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="{{ __('messages.to_date') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-filter me-1"></i>
                                    {{ __('messages.filter') }}
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('suppliers.statement', $supplier) }}" class="btn btn-outline-info me-2">
                            <i class="ti ti-file-text me-1"></i>
                            {{ __('messages.account_statement') }}
                        </a>
                        <a href="{{ route('suppliers.transactions.create', $supplier) }}" class="btn btn-success">
                            <i class="ti ti-plus me-1"></i>
                            {{ __('messages.add_transaction') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-list me-2"></i>
                    {{ __('messages.transactions') }}
                    <span class="badge bg-primary ms-2">{{ $transactions->total() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">{{ __('messages.date') }}</th>
                                    <th width="15%">{{ __('messages.type') }}</th>
                                    <th width="15%">{{ __('messages.amount') }}</th>
                                    <th width="15%">{{ __('messages.cashbox') }}</th>
                                    <th>{{ __('messages.description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td class="fw-semibold">{{ $loop->iteration }}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($transaction->type == 'deposit')
                                                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2">
                                                    <i class="ti ti-arrow-down-left me-1"></i>
                                                    {{ __('messages.deposit') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2">
                                                    <i class="ti ti-arrow-up-right me-1"></i>
                                                    {{ __('messages.withdrawal') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td dir="ltr" class="fw-bold {{ $transaction->type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                            <small class="text-muted">{{ __('messages.currency') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <i class="ti ti-cash me-1"></i>
                                                {{ $transaction->cashbox->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ $transaction->description ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($transactions->hasPages())
                        <div class="card-footer bg-white border-top">
                            {{ $transactions->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-receipt-off text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">{{ __('messages.no_transactions') }}</h5>
                        <p class="text-muted">{{ __('messages.no_transactions_desc') }}</p>
                        <a href="{{ route('suppliers.transactions.create', $supplier) }}" class="btn btn-primary mt-2">
                            <i class="ti ti-plus me-1"></i>
                            {{ __('messages.add_first_transaction') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="col-12">
        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
            {{ __('messages.back') }}
        </a>
    </div>
</div>
@endsection
