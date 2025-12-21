@extends('layouts.app')

@section('title', __('messages.edit_transaction'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.finance') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">{{ __('messages.transactions') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.edit_transaction') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-edit me-2"></i>
                    {{ __('messages.edit_transaction') }}
                </h5>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} me-1"></i>
                    {{ __('messages.back') }}
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('transactions.update', $transaction) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        {{-- Cashbox Selection --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('messages.cashbox') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-cash"></i>
                                </span>
                                <select name="cashbox_id" class="form-select @error('cashbox_id') is-invalid @enderror" required>
                                    <option value="">{{ __('messages.select_cashbox') }}</option>
                                    @foreach($cashboxes as $cashbox)
                                        <option value="{{ $cashbox->id }}" {{ old('cashbox_id', $transaction->cashbox_id) == $cashbox->id ? 'selected' : '' }}>
                                            {{ $cashbox->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cashbox_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Transaction Type --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                {{ __('messages.transaction_type') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-4">
                                <div class="form-check form-check-lg">
                                    <input class="form-check-input @error('type') is-invalid @enderror"
                                           type="radio"
                                           name="type"
                                           id="type_deposit"
                                           value="deposit"
                                           {{ old('type', $transaction->type) == 'deposit' ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label" for="type_deposit">
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="ti ti-arrow-down me-1"></i>
                                            {{ __('messages.deposit') }}
                                        </span>
                                    </label>
                                </div>
                                <div class="form-check form-check-lg">
                                    <input class="form-check-input @error('type') is-invalid @enderror"
                                           type="radio"
                                           name="type"
                                           id="type_withdrawal"
                                           value="withdrawal"
                                           {{ old('type', $transaction->type) == 'withdrawal' ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label" for="type_withdrawal">
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="ti ti-arrow-up me-1"></i>
                                            {{ __('messages.withdrawal') }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Recipient/Payer Name --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('messages.recipient_payer_name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-user"></i>
                                </span>
                                <input type="text"
                                       name="recipient_name"
                                       class="form-control @error('recipient_name') is-invalid @enderror"
                                       value="{{ old('recipient_name', $transaction->recipient_name) }}"
                                       placeholder="{{ __('messages.enter_recipient_name') }}"
                                       required>
                                @error('recipient_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Recipient/Payer Number --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('messages.recipient_number') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-phone"></i>
                                </span>
                                <input type="text"
                                       name="recipient_number"
                                       class="form-control @error('recipient_number') is-invalid @enderror"
                                       value="{{ old('recipient_number', $transaction->recipient_number) }}"
                                       placeholder="{{ __('messages.enter_recipient_number') }}"
                                       dir="ltr">
                                @error('recipient_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                {{ __('messages.amount') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-currency-dollar"></i>
                                </span>
                                <input type="number"
                                       name="amount"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount', $transaction->amount) }}"
                                       step="0.01"
                                       placeholder="0.00"
                                       required
                                       dir="ltr">
                                <span class="input-group-text bg-light">{{ __('messages.currency') }}</span>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                {{ __('messages.description') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-file-text"></i>
                                </span>
                                <textarea name="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="3"
                                          placeholder="{{ __('messages.enter_description') }}">{{ old('description', $transaction->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
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
