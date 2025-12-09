@extends('layouts.app')

@section('title', __('messages.edit_cashbox'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.finance') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('cashboxes.index') }}">{{ __('messages.cashboxes') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.edit_cashbox') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-edit me-2"></i>
                    {{ __('messages.edit_cashbox') }}: {{ $cashbox->name }}
                </h5>
                <a href="{{ route('cashboxes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} me-1"></i>
                    {{ __('messages.back') }}
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('cashboxes.update', $cashbox) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                {{ __('messages.cashbox_name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-cash"></i>
                                </span>
                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $cashbox->name) }}"
                                       placeholder="{{ __('messages.enter_cashbox_name') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('messages.current_balance') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-currency-dollar"></i>
                                </span>
                                <input type="number"
                                       class="form-control"
                                       value="{{ $cashbox->current_balance }}"
                                       step="0.01"
                                       disabled
                                       dir="ltr">
                                <span class="input-group-text bg-light">{{ __('messages.currency') }}</span>
                            </div>
                            <small class="form-text text-muted">
                                <i class="ti ti-info-circle me-1"></i>
                                {{ __('messages.balance_readonly_help') }}
                            </small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('messages.opening_balance') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-currency-dollar"></i>
                                </span>
                                <input type="number"
                                       class="form-control"
                                       value="{{ $cashbox->opening_balance }}"
                                       step="0.01"
                                       disabled
                                       dir="ltr">
                                <span class="input-group-text bg-light">{{ __('messages.currency') }}</span>
                            </div>
                            <small class="form-text text-muted">
                                <i class="ti ti-info-circle me-1"></i>
                                {{ __('messages.balance_readonly_help') }}
                            </small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('cashboxes.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x me-1"></i>
                            {{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>
                            {{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
