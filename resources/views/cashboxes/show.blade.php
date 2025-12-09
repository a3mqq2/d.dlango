@extends('layouts.app')

@section('title', __('messages.cashbox_details'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.finance') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('cashboxes.index') }}">{{ __('messages.cashboxes') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $cashbox->name }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        {{-- Cashbox Info Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body text-center p-5">
                <div class="avatar avatar-lg bg-light-primary rounded-circle mx-auto mb-4"
                     style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-cash text-primary" style="font-size: 3rem;"></i>
                </div>
                <h3 class="mb-4">{{ $cashbox->name }}</h3>

                <div class="row text-center mb-4">
                    <div class="col-md-6 mb-4">
                        <div class="card bg-light border-0">
                            <div class="card-body p-4">
                                <small class="text-muted d-block mb-2">{{ __('messages.current_balance') }}</small>
                                <span class="badge bg-{{ $cashbox->current_balance > 0 ? 'success' : ($cashbox->current_balance < 0 ? 'danger' : 'secondary') }} fs-3 px-4 py-3">
                                    <span dir="ltr">{{ number_format($cashbox->current_balance, 2) }}</span>
                                    <small class="fs-5">{{ __('messages.currency') }}</small>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card bg-light border-0">
                            <div class="card-body p-4">
                                <small class="text-muted d-block mb-2">{{ __('messages.opening_balance') }}</small>
                                <h3 class="mb-0" dir="ltr">
                                    {{ number_format($cashbox->opening_balance, 2) }}
                                    <small class="fs-5 text-muted">{{ __('messages.currency') }}</small>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('cashboxes.edit', $cashbox) }}" class="btn btn-primary">
                        <i class="ti ti-edit me-1"></i>
                        {{ __('messages.edit') }}
                    </a>
                    <a href="{{ route('cashboxes.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} me-1"></i>
                        {{ __('messages.back') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 text-primary">
                    <i class="ti ti-history me-2"></i>
                    {{ __('messages.recent_transactions') }}
                </h6>
            </div>
            <div class="card-body">
                @if($recentTransactions && $recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.description') }}</th>
                                    <th>{{ __('messages.type') }}</th>
                                    <th>{{ __('messages.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td>
                                            <small dir="ltr">{{ $transaction->created_at->format('Y-m-d H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;">
                                                {{ $transaction->description ?? __('messages.no_description') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->type == 'deposit' ? 'success' : 'danger' }}">
                                                {{ __('messages.' . $transaction->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-{{ $transaction->type == 'deposit' ? 'success' : 'danger' }}" dir="ltr">
                                                {{ $transaction->type == 'deposit' ? '+' : '-' }}
                                                {{ number_format($transaction->amount, 2) }}
                                                <small class="text-muted">{{ __('messages.currency') }}</small>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('transactions.index', ['cashbox' => $cashbox->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-list me-1"></i>
                            {{ __('messages.view_all_transactions') }}
                        </a>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-list-off" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2 mb-0">{{ __('messages.no_transactions') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- System Information --}}
        <div class="card shadow-sm border-0">
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
                        <span class="fw-semibold" dir="ltr">{{ $cashbox->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">{{ __('messages.updated_at') }}</small>
                        <span class="fw-semibold" dir="ltr">{{ $cashbox->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
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
