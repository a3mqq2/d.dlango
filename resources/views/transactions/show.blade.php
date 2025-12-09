@extends('layouts.app')

@section('title', __('messages.transaction_details'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.finance') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">{{ __('messages.transactions') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.transaction_details') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        {{-- Transaction Info Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body text-center p-5">
                <div class="avatar avatar-lg bg-light-{{ $transaction->type == 'deposit' ? 'success' : 'danger' }} rounded-circle mx-auto mb-4"
                     style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-arrow-{{ $transaction->type == 'deposit' ? 'down' : 'up' }} text-{{ $transaction->type == 'deposit' ? 'success' : 'danger' }}" style="font-size: 3rem;"></i>
                </div>

                <span class="badge bg-{{ $transaction->type == 'deposit' ? 'success' : 'danger' }} fs-5 px-4 py-2 mb-3">
                    {{ __('messages.' . $transaction->type) }}
                </span>

                <h2 class="mb-4 text-{{ $transaction->type == 'deposit' ? 'success' : 'danger' }}" dir="ltr">
                    {{ $transaction->type == 'deposit' ? '+' : '-' }}
                    {{ number_format($transaction->amount, 2) }}
                    <small class="fs-4 text-muted">{{ __('messages.currency') }}</small>
                </h2>

                <div class="row g-3 mb-4">
                    {{-- Cashbox --}}
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-light-primary rounded">
                                            <i class="ti ti-cash text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3 text-start">
                                        <small class="text-muted d-block">{{ __('messages.cashbox') }}</small>
                                        <h6 class="mb-0">{{ $transaction->cashbox->name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-light-primary rounded">
                                            <i class="ti ti-tag text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3 text-start">
                                        <small class="text-muted d-block">{{ __('messages.category') }}</small>
                                        <h6 class="mb-0">{{ $transaction->category->name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recipient Name --}}
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-light-primary rounded">
                                            <i class="ti ti-user text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3 text-start">
                                        <small class="text-muted d-block">{{ __('messages.recipient_payer_name') }}</small>
                                        <h6 class="mb-0">{{ $transaction->recipient_name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recipient Number --}}
                    @if($transaction->recipient_number)
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar avatar-sm bg-light-primary rounded">
                                                <i class="ti ti-phone text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3 text-start">
                                            <small class="text-muted d-block">{{ __('messages.recipient_number') }}</small>
                                            <h6 class="mb-0" dir="ltr">{{ $transaction->recipient_number }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Date --}}
                    <div class="col-md-{{ $transaction->recipient_number ? '12' : '6' }}">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-light-primary rounded">
                                            <i class="ti ti-calendar text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3 text-start">
                                        <small class="text-muted d-block">{{ __('messages.date') }}</small>
                                        <h6 class="mb-0" dir="ltr">{{ $transaction->created_at->format('Y-m-d H:i') }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($transaction->description)
                        <div class="col-md-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <small class="text-muted d-block mb-2">{{ __('messages.description') }}</small>
                                    <p class="mb-0">{{ $transaction->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('transactions.receipt', $transaction) }}" class="btn btn-success" target="_blank">
                        <i class="ti ti-printer me-1"></i>
                        {{ __('messages.print_receipt') }}
                    </a>
                    <div class="btn-group" role="group">
                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>
                            {{ __('messages.edit') }}
                        </a>
                        <form action="{{ route('transactions.destroy', $transaction) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('{{ __('messages.confirm_delete_transaction') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-trash me-1"></i>
                                {{ __('messages.delete') }}
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} me-1"></i>
                        {{ __('messages.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-light-primary {
    background-color: rgba(41, 26, 107, 0.1) !important;
}
.bg-light-success {
    background-color: rgba(40, 167, 69, 0.1) !important;
}
.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
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
