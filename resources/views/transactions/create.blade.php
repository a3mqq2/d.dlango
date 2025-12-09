@extends('layouts.app')

@section('title', __('messages.add_transaction'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.finance') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">{{ __('messages.transactions') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.add_transaction') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-arrows-exchange me-2"></i>
                    {{ __('messages.add_transaction') }}
                </h5>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} me-1"></i>
                    {{ __('messages.back') }}
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf

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
                                        <option value="{{ $cashbox->id }}" {{ old('cashbox_id') == $cashbox->id ? 'selected' : '' }}>
                                            {{ $cashbox->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cashbox_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Category Selection --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('messages.category') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-tag"></i>
                                </span>
                                <select name="transaction_category_id" id="category_select" class="form-select @error('transaction_category_id') is-invalid @enderror" required>
                                    <option value="">{{ __('messages.select_category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('transaction_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                    <i class="ti ti-plus"></i>
                                </button>
                                @error('transaction_category_id')
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
                                           {{ old('type') == 'deposit' ? 'checked' : '' }}
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
                                           {{ old('type') == 'withdrawal' ? 'checked' : '' }}
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
                                       value="{{ old('recipient_name') }}"
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
                                       value="{{ old('recipient_number') }}"
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
                                       value="{{ old('amount') }}"
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
                                          placeholder="{{ __('messages.enter_description') }}">{{ old('description') }}</textarea>
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
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addCategoryModalLabel">
                    <i class="ti ti-tag me-2"></i>
                    {{ __('messages.add_category') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ __('messages.category_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="new_category_name" class="form-control" required>
                    </div>
                    <div id="category_error" class="alert alert-danger d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    <i class="ti ti-device-floppy me-1"></i>
                    {{ __('messages.save') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const saveCategoryBtn = document.getElementById('saveCategoryBtn');
    const categorySelect = document.getElementById('category_select');
    const categoryError = document.getElementById('category_error');
    const modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));

    saveCategoryBtn.addEventListener('click', async function() {
        const categoryName = document.getElementById('new_category_name').value.trim();

        if (!categoryName) {
            categoryError.textContent = '{{ __('messages.category_name_required') }}';
            categoryError.classList.remove('d-none');
            return;
        }

        saveCategoryBtn.disabled = true;
        categoryError.classList.add('d-none');

        try {
            const response = await fetch('{{ route('transaction-categories.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name: categoryName })
            });

            const data = await response.json();

            if (response.ok) {
                // Add new option to select
                const option = new Option(data.category.name, data.category.id, true, true);
                categorySelect.add(option);

                // Close modal and reset form
                modal.hide();
                document.getElementById('new_category_name').value = '';

                // Show success message (optional)
                // You can add a toast notification here
            } else {
                categoryError.textContent = data.message || '{{ __('messages.error_occurred') }}';
                categoryError.classList.remove('d-none');
            }
        } catch (error) {
            categoryError.textContent = '{{ __('messages.error_occurred') }}';
            categoryError.classList.remove('d-none');
        } finally {
            saveCategoryBtn.disabled = false;
        }
    });

    // Reset error when modal is hidden
    document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('new_category_name').value = '';
        categoryError.classList.add('d-none');
    });
});
</script>
@endpush
@endsection
