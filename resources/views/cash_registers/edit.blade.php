@extends('layouts.app')

@section('title', __('messages.edit_cash_register'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-safe me-2"></i>
                    {{ __('messages.edit_cash_register') }}
                </h5>
                <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-right me-1"></i>
                    {{ __('messages.back') }}
                </a>
            </div>
            <div class="card-body">
                @include('layouts.messages')

                <form action="{{ route('cash-registers.update', $cashRegister) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.cash_register_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $cashRegister->name) }}" placeholder="{{ __('messages.enter_cash_register_name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.currency') }} <span class="text-danger">*</span></label>
                            <select name="currency_id" class="form-select @error('currency_id') is-invalid @enderror" required>
                                <option value="">{{ __('messages.select_currency') }}</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" {{ old('currency_id', $cashRegister->currency_id) == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->name }}
                                        @if($currency->symbol) ({{ $currency->symbol }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('currency_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.opening_balance') }}</label>
                            <input type="text" class="form-control" value="{{ number_format($cashRegister->opening_balance, 2) }}" disabled>
                            <small class="text-muted">{{ __('messages.opening_balance_readonly') }}</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.current_balance') }}</label>
                            <input type="text" class="form-control fw-bold {{ $cashRegister->current_balance >= 0 ? 'text-success' : 'text-danger' }}" value="{{ number_format($cashRegister->current_balance, 2) }}" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.status') }}</label>
                            <div class="form-check mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $cashRegister->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('messages.active') }}</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('messages.description') }}</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="{{ __('messages.enter_description') }}">{{ old('description', $cashRegister->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x me-1"></i>
                            {{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary" style="background-color: #b65f7a; border-color: #b65f7a;">
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
