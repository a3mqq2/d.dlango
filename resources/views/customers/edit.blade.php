@extends('layouts.app')

@section('title', __('messages.edit_customer'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.sales') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('customers.index') }}">{{ __('messages.customers') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.edit_customer') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-user-edit me-2"></i>
                    {{ __('messages.edit_customer') }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">
                            {{ __('messages.customer_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control form-control-lg @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $customer->name) }}"
                               placeholder="{{ __('messages.enter_customer_name') }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label fw-semibold">
                            {{ __('messages.phone') }}
                        </label>
                        <input type="text"
                               class="form-control form-control-lg @error('phone') is-invalid @enderror"
                               id="phone"
                               name="phone"
                               value="{{ old('phone', $customer->phone) }}"
                               placeholder="{{ __('messages.enter_phone') }}"
                               dir="ltr">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('messages.current_balance') }}</label>
                        <div class="form-control-lg bg-light rounded p-3 text-center">
                            <h4 class="mb-0 {{ $customer->balance >= 0 ? 'text-success' : 'text-danger' }}" dir="ltr">
                                {{ number_format($customer->balance, 2) }}
                                <small class="fs-6">{{ __('messages.currency') }}</small>
                            </h4>
                        </div>
                        <small class="text-muted">{{ __('messages.balance_changed_through_sales') }}</small>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
                            {{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ti ti-check me-1"></i>
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
