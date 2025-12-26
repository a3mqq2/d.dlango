@extends('layouts.app')

@section('title', __('messages.add_supplier_transaction') . ' - ' . $supplier->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.purchases') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">{{ __('messages.suppliers') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('suppliers.show', $supplier) }}">{{ $supplier->name }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.add_transaction') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        {{-- Supplier Info --}}
        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-1 text-white">
                            <i class="ti ti-building-store me-2"></i>
                            {{ $supplier->name }}
                        </h5>
                        <p class="mb-0 opacity-75" dir="ltr">{{ $supplier->phone }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small class="d-block opacity-75">{{ __('messages.current_balance') }}</small>
                        <h4 class="mb-0 text-white" dir="ltr">
                            {{ number_format($supplier->balance, 2) }}
                            <small class="fs-6">{{ __('messages.currency') }}</small>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction Form --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-plus me-2"></i>
                    {{ __('messages.add_supplier_transaction') }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('suppliers.transactions.store', $supplier) }}" method="POST">
                    @csrf

                    {{-- Transaction Type --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('messages.transaction_type') }} <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="type" id="type_deposit" value="deposit" {{ old('type', 'deposit') == 'deposit' ? 'checked' : '' }}>
                                <label class="btn btn-outline-success w-100 py-3" for="type_deposit">
                                    <i class="ti ti-cash fs-3 d-block mb-2"></i>
                                    <strong>دفع للمورد</strong>
                                    <small class="d-block text-muted mt-1">سداد دين / تسديد فاتورة</small>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="type" id="type_withdrawal" value="withdrawal" {{ old('type') == 'withdrawal' ? 'checked' : '' }}>
                                <label class="btn btn-outline-danger w-100 py-3" for="type_withdrawal">
                                    <i class="ti ti-shopping-cart fs-3 d-block mb-2"></i>
                                    <strong>شراء بالآجل</strong>
                                    <small class="d-block text-muted mt-1">زيادة الدين على المورد</small>
                                </label>
                            </div>
                        </div>
                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="mb-4">
                        <label for="amount" class="form-label fw-semibold">{{ __('messages.amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <input type="number" step="0.01" min="0.01"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   id="amount" name="amount" value="{{ old('amount') }}"
                                   placeholder="{{ __('messages.enter_amount') }}" required>
                            <span class="input-group-text">{{ __('messages.currency') }}</span>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Cashbox --}}
                    <div class="mb-4">
                        <label for="cashbox_id" class="form-label fw-semibold">{{ __('messages.cashbox') }} <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg @error('cashbox_id') is-invalid @enderror"
                                id="cashbox_id" name="cashbox_id" required>
                            <option value="">{{ __('messages.select_cashbox') }}</option>
                            @foreach($cashboxes as $cashbox)
                                <option value="{{ $cashbox->id }}" {{ old('cashbox_id') == $cashbox->id ? 'selected' : '' }}>
                                    {{ $cashbox->name }} ({{ number_format($cashbox->current_balance, 2) }} {{ __('messages.currency') }})
                                </option>
                            @endforeach
                        </select>
                        @error('cashbox_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">{{ __('messages.description') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="{{ __('messages.enter_description') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('suppliers.transactions', $supplier) }}" class="btn btn-outline-secondary btn-lg">
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
