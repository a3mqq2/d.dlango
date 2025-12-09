@extends('layouts.app')

@section('title', __('messages.add_supplier'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.purchases') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">{{ __('messages.suppliers') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.add_supplier') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-truck-delivery me-2"></i>
                    {{ __('messages.add_supplier') }}
                </h5>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} me-1"></i>
                    {{ __('messages.back') }}
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                {{ __('messages.supplier_name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-building-store"></i>
                                </span>
                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       placeholder="{{ __('messages.enter_supplier_name') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('messages.phone') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-phone"></i>
                                </span>
                                <input type="text"
                                       name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}"
                                       placeholder="{{ __('messages.enter_phone') }}"
                                       required
                                       dir="ltr">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('messages.balance') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-currency-dollar"></i>
                                </span>
                                <input type="number"
                                       name="balance"
                                       class="form-control @error('balance') is-invalid @enderror"
                                       value="{{ old('balance', 0) }}"
                                       step="0.01"
                                       placeholder="0.00"
                                       dir="ltr">
                                <span class="input-group-text bg-light">{{ __('messages.currency') }}</span>
                                @error('balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                {{ __('messages.initial_balance_help') }}
                            </small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x me-1"></i>
                            {{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
