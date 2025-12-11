@extends('layouts.app')

@section('title', __('messages.add_customer_transaction') . ' - ' . $customer->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.sales') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('customers.index') }}">{{ __('messages.customers') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.add_transaction') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        {{-- Customer Info --}}
        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-1 text-white">
                            <i class="ti ti-user me-2"></i>
                            {{ $customer->name }}
                        </h5>
                        <p class="mb-0 opacity-75" dir="ltr">{{ $customer->phone ?? __('messages.not_available') }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small class="d-block opacity-75">{{ __('messages.current_balance') }}</small>
                        <h4 class="mb-0 text-white" dir="ltr">
                            {{ number_format($customer->balance, 2) }}
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
                    {{ __('messages.add_customer_transaction') }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('customers.transactions.store', $customer) }}" method="POST">
                    @csrf

                    {{-- Transaction Type --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('messages.transaction_type') }} <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="type" id="type_deposit" value="deposit" {{ old('type', 'deposit') == 'deposit' ? 'checked' : '' }}>
                                <label class="btn btn-outline-success w-100 py-3" for="type_deposit">
                                    <i class="ti ti-arrow-down-left fs-3 d-block mb-2"></i>
                                    <strong>{{ __('messages.payment') }}</strong>
                                    <small class="d-block text-muted mt-1">{{ __('messages.payment_from_customer') }}</small>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="type" id="type_withdrawal" value="withdrawal" {{ old('type') == 'withdrawal' ? 'checked' : '' }}>
                                <label class="btn btn-outline-danger w-100 py-3" for="type_withdrawal">
                                    <i class="ti ti-arrow-up-right fs-3 d-block mb-2"></i>
                                    <strong>{{ __('messages.credit') }}</strong>
                                    <small class="d-block text-muted mt-1">{{ __('messages.credit_to_customer') }}</small>
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

                    {{-- Category --}}
                    <div class="mb-4">
                        <label for="transaction_category_id" class="form-label fw-semibold">{{ __('messages.transaction_category') }}</label>
                        <div class="input-group">
                            <select class="form-select @error('transaction_category_id') is-invalid @enderror"
                                    id="transaction_category_id" name="transaction_category_id">
                                <option value="">{{ __('messages.select_transaction_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('transaction_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" id="addCategoryBtn" title="{{ __('messages.add_transaction_category') }}">
                                <i class="ti ti-plus"></i>
                            </button>
                        </div>
                        @error('transaction_category_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
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
                        <a href="{{ route('customers.transactions', $customer) }}" class="btn btn-outline-secondary btn-lg">
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

{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-tag me-2"></i>
                    {{ __('messages.add_transaction_category') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.category_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="newCategoryName" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    <i class="ti ti-check me-1"></i>
                    {{ __('messages.save') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addCategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    const saveCategoryBtn = document.getElementById('saveCategoryBtn');
    const newCategoryName = document.getElementById('newCategoryName');
    const categorySelect = document.getElementById('transaction_category_id');

    // Open modal
    addCategoryBtn.addEventListener('click', function() {
        newCategoryName.value = '';
        addCategoryModal.show();
    });

    // Save category
    saveCategoryBtn.addEventListener('click', async function() {
        const name = newCategoryName.value.trim();

        if (!name) {
            alert('{{ __("messages.category_name_required") }}');
            return;
        }

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("messages.saving") }}...';

        try {
            const response = await fetch('{{ route("transaction-categories.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name })
            });

            const data = await response.json();

            if (data.success) {
                // Add new category to select
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                option.selected = true;
                categorySelect.appendChild(option);

                addCategoryModal.hide();
            } else {
                alert(data.message || '{{ __("messages.error_occurred") }}');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('{{ __("messages.error_occurred") }}');
        } finally {
            this.disabled = false;
            this.innerHTML = '<i class="ti ti-check me-1"></i>{{ __("messages.save") }}';
        }
    });

    // Enter key to save
    newCategoryName.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveCategoryBtn.click();
        }
    });
});
</script>
@endpush
@endsection
