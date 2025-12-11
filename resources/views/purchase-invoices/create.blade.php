@extends('layouts.app')

@section('title', __('messages.add_purchase_invoice'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.purchases') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('purchase-invoices.index') }}">{{ __('messages.purchase_invoices') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.add_purchase_invoice') }}</li>
@endsection

@section('content')
<form id="purchaseInvoiceForm" method="POST" action="{{ route('purchase-invoices.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        {{-- Card 1: Invoice Details --}}
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-white">
                        <i class="ti ti-file-invoice me-2"></i>
                        {{ __('messages.invoice_details') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        {{-- Invoice Number --}}
                        <div class="col-md-4">
                            <label class="form-label text-white fw-semibold">
                                {{ __('messages.invoice_number') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-hash"></i>
                                </span>
                                <input type="text" class="form-control" value="{{ $nextInvoiceNumber }}" readonly dir="ltr">
                            </div>
                        </div>

                        {{-- Invoice Date --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                {{ __('messages.invoice_date') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-calendar"></i>
                                </span>
                                <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror"
                                       value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Supplier Selection --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                {{ __('messages.supplier') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-truck-delivery"></i>
                                </span>
                                <select name="supplier_id" id="supplier_select" class="form-select select2 @error('supplier_id') is-invalid @enderror" required>
                                    <option value="">{{ __('messages.select_supplier') }}</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                    <i class="ti ti-plus"></i>
                                </button>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Products --}}
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-package me-2"></i>
                        {{ __('messages.products') }}
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" id="addProductBtn">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('messages.add_product') }}
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="productsContainer">
                        {{-- Products will be added here dynamically --}}
                    </div>
                    <div id="emptyProductsState" class="text-center py-5 text-muted">
                        <i class="ti ti-package-off" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h5 class="mt-3">{{ __('messages.no_products_added') }}</h5>
                        <p>{{ __('messages.click_add_product_to_start') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Invoice Summary --}}
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-file-text me-2"></i>
                        {{ __('messages.invoice_summary') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        {{-- Summary Stats --}}
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card bg-light border-0">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2">{{ __('messages.items_count') }}</h6>
                                            <h3 class="mb-0 text-primary" id="totalItems">0</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light border-0">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2">{{ __('messages.total_amount') }}</h6>
                                            <h3 class="mb-0 text-success" dir="ltr">
                                                <span id="totalAmount">0.00</span> {{ __('messages.currency') }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light border-0">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2">{{ __('messages.total_profit') }}</h6>
                                            <h3 class="mb-0 text-info" dir="ltr">
                                                <span id="totalProfit">0.00</span> {{ __('messages.currency') }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                {{ __('messages.payment_method') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-4">
                                <div class="form-check form-check-lg">
                                    <input class="form-check-input @error('payment_method') is-invalid @enderror"
                                           type="radio"
                                           name="payment_method"
                                           id="payment_cash"
                                           value="cash"
                                           {{ old('payment_method', 'cash') == 'cash' ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label" for="payment_cash">
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="ti ti-cash me-1"></i>
                                            {{ __('messages.cash') }}
                                        </span>
                                    </label>
                                </div>
                                <div class="form-check form-check-lg">
                                    <input class="form-check-input @error('payment_method') is-invalid @enderror"
                                           type="radio"
                                           name="payment_method"
                                           id="payment_credit"
                                           value="credit"
                                           {{ old('payment_method') == 'credit' ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label" for="payment_credit">
                                        <span class="badge bg-warning px-3 py-2">
                                            <i class="ti ti-calendar-due me-1"></i>
                                            {{ __('messages.credit') }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                            @error('payment_method')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Cashbox (only for cash payment) --}}
                        <div class="col-12" id="cashboxContainer" style="{{ old('payment_method', 'cash') == 'cash' ? '' : 'display: none;' }}">
                            <label class="form-label fw-semibold">
                                {{ __('messages.cashbox') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-cash"></i>
                                </span>
                                <select name="cashbox_id" id="cashbox_select" class="form-select @error('cashbox_id') is-invalid @enderror">
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
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                {{ __('messages.notes') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-file-text"></i>
                                </span>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                          placeholder="{{ __('messages.enter_notes') }}">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('purchase-invoices.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x me-1"></i>
                                    {{ __('messages.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="ti ti-device-floppy me-1"></i>
                                    {{ __('messages.save_invoice') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Add Supplier Modal --}}
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addSupplierModalLabel">
                    <i class="ti ti-truck-delivery me-2"></i>
                    {{ __('messages.add_supplier') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ __('messages.supplier_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="new_supplier_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ __('messages.phone') }}
                        </label>
                        <input type="text" id="new_supplier_phone" class="form-control" dir="ltr">
                    </div>
                    <div id="supplier_error" class="alert alert-danger d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="saveSupplierBtn">
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
    console.log('Purchase invoice script loaded');
    let productCounter = 0;
    const productsContainer = document.getElementById('productsContainer');
    const emptyState = document.getElementById('emptyProductsState');
    const addProductBtn = document.getElementById('addProductBtn');

    console.log('Add Product Button:', addProductBtn);

    if (!addProductBtn) {
        console.error('addProductBtn not found!');
        return;
    }

    // Product Template
    function getProductTemplate(index) {
        return `
            <div class="product-item border rounded p-4 mb-3" data-product-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-primary">
                        <i class="ti ti-package me-1"></i>
                        ${window.productLabel || 'Product'} #${index + 1}
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-product-btn">
                        <i class="ti ti-trash"></i>
                        ${window.deleteLabel || 'Delete'}
                    </button>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">${window.productTypeLabel || 'Product Type'}</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input product-type-radio" type="radio"
                                       name="products[${index}][type]" id="type_simple_${index}"
                                       value="simple" checked required>
                                <label class="form-check-label" for="type_simple_${index}">
                                    <i class="ti ti-circle-dot me-1"></i>
                                    ${window.simpleProductLabel || 'Simple Product'}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input product-type-radio" type="radio"
                                       name="products[${index}][type]" id="type_variable_${index}"
                                       value="variable" required>
                                <label class="form-check-label" for="type_variable_${index}">
                                    <i class="ti ti-circle-dot me-1"></i>
                                    ${window.variableProductLabel || 'Variable Product'}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">${window.codeLabel || 'Code'} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="products[${index}][code]" class="form-control product-code"
                                   dir="ltr" required>
                            <button type="button" class="btn btn-outline-secondary generate-code-btn">
                                ${window.generateLabel || 'Generate'}
                            </button>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">${window.nameLabel || 'Name'} <span class="text-danger">*</span></label>
                        <input type="text" name="products[${index}][name]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">${window.skuLabel || 'SKU'}</label>
                        <input type="text" name="products[${index}][sku]" class="form-control" dir="ltr">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">${window.productImageLabel || 'Image'}</label>
                        <div class="product-image-upload">
                            <input type="file" name="products[${index}][image]" class="form-control form-control-sm product-image-input"
                                   accept="image/*" style="display: none;" id="product_image_${index}">
                            <div class="image-preview-container" style="position: relative;">
                                <img src="" class="image-preview img-thumbnail d-none" style="max-width: 60px; max-height: 60px; cursor: pointer;">
                                <button type="button" class="btn btn-sm btn-outline-secondary select-image-btn w-100">
                                    <i class="fa fa-picture-o"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-image-btn d-none" style="position: absolute; top: -5px; right: -5px; padding: 0 4px;">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="simple-product-fields">
                    <div class="border rounded p-3 bg-light">
                        <h6 class="mb-3">${window.simpleProductLabel || 'Simple Product'}</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">${window.quantityLabel || 'Quantity'} <span class="text-danger">*</span></label>
                                <input type="number" name="products[${index}][quantity]" class="form-control simple-quantity"
                                       min="1" step="1" dir="ltr">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">${window.purchasePriceLabel || 'Purchase Price'} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="products[${index}][purchase_price]"
                                           class="form-control simple-purchase-price" min="0" step="0.01" dir="ltr">
                                    <span class="input-group-text">${window.currencyLabel || 'LD'}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">${window.sellingPriceLabel || 'Selling Price'} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="products[${index}][selling_price]"
                                           class="form-control simple-selling-price" min="0" step="0.01" dir="ltr">
                                    <span class="input-group-text">${window.currencyLabel || 'LD'}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">${window.profitPerUnitLabel || 'Profit/Unit'}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control simple-profit-unit" readonly dir="ltr">
                                    <span class="input-group-text">${window.currencyLabel || 'LD'}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">${window.totalProfitLabel || 'Total Profit'}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control simple-profit-total" readonly dir="ltr">
                                    <span class="input-group-text">${window.currencyLabel || 'LD'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="variable-product-fields" style="display: none;">
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">${window.variantsLabel || 'Variants'}</h6>
                            <button type="button" class="btn btn-sm btn-primary add-variant-btn">
                                <i class="ti ti-plus me-1"></i>
                                ${window.addVariantLabel || 'Add Variant'}
                            </button>
                        </div>
                        <div class="variants-container">
                        </div>
                        <div class="empty-variants-state text-center py-3 text-muted">
                            <small>${window.noVariantsLabel || 'No variants added. Click "Add Variant" to start.'}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Variant Template
    function getVariantTemplate(productIndex, variantIndex) {
        return `
            <div class="variant-item border rounded p-3 mb-2 bg-white" data-variant-index="${variantIndex}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 small">
                        <i class="ti ti-tag me-1"></i>
                        ${window.variantLabel || 'Variant'} #${variantIndex + 1}
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small">${window.variantNameLabel || 'Variant Name'} <span class="text-danger">*</span></label>
                        <input type="text" name="products[${productIndex}][variants][${variantIndex}][variant_name]"
                               class="form-control form-control-sm" placeholder="${window.variantNamePlaceholder || 'e.g., Large, Red'}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">${window.codeLabel || 'Code'} <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="products[${productIndex}][variants][${variantIndex}][code]"
                                   class="form-control variant-code" dir="ltr">
                            <button type="button" class="btn btn-outline-secondary generate-variant-code-btn">
                                ${window.generateLabel || 'Generate'}
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">${window.quantityLabel || 'Quantity'} <span class="text-danger">*</span></label>
                        <input type="number" name="products[${productIndex}][variants][${variantIndex}][quantity]"
                               class="form-control form-control-sm variant-quantity" min="1" step="1" dir="ltr">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">${window.purchasePriceLabel || 'Purchase Price'} <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="products[${productIndex}][variants][${variantIndex}][purchase_price]"
                                   class="form-control variant-purchase-price" min="0" step="0.01" dir="ltr">
                            <span class="input-group-text">${window.currencyLabel || 'LD'}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">${window.sellingPriceLabel || 'Selling Price'} <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="products[${productIndex}][variants][${variantIndex}][selling_price]"
                                   class="form-control variant-selling-price" min="0" step="0.01" dir="ltr">
                            <span class="input-group-text">${window.currencyLabel || 'LD'}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">${window.profitPerUnitLabel || 'Profit/Unit'}</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control variant-profit-unit" readonly dir="ltr">
                            <span class="input-group-text">${window.currencyLabel || 'LD'}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">${window.totalProfitLabel || 'Total Profit'}</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control variant-profit-total" readonly dir="ltr">
                            <span class="input-group-text">${window.currencyLabel || 'LD'}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Generate random 4-digit code
    function generateCode() {
        return Math.floor(1000 + Math.random() * 9000).toString();
    }

    // Calculate profit for simple products
    function calculateSimpleProfit(productElement) {
        const purchasePrice = parseFloat(productElement.querySelector('.simple-purchase-price').value) || 0;
        const sellingPrice = parseFloat(productElement.querySelector('.simple-selling-price').value) || 0;
        const quantity = parseFloat(productElement.querySelector('.simple-quantity').value) || 0;

        const profitPerUnit = sellingPrice - purchasePrice;
        const totalProfit = profitPerUnit * quantity;

        productElement.querySelector('.simple-profit-unit').value = profitPerUnit.toFixed(2);
        productElement.querySelector('.simple-profit-total').value = totalProfit.toFixed(2);

        updateInvoiceTotals();
    }

    // Calculate profit for variants
    function calculateVariantProfit(variantElement) {
        const purchasePrice = parseFloat(variantElement.querySelector('.variant-purchase-price').value) || 0;
        const sellingPrice = parseFloat(variantElement.querySelector('.variant-selling-price').value) || 0;
        const quantity = parseFloat(variantElement.querySelector('.variant-quantity').value) || 0;

        const profitPerUnit = sellingPrice - purchasePrice;
        const totalProfit = profitPerUnit * quantity;

        variantElement.querySelector('.variant-profit-unit').value = profitPerUnit.toFixed(2);
        variantElement.querySelector('.variant-profit-total').value = totalProfit.toFixed(2);

        updateInvoiceTotals();
    }

    // Update invoice totals
    function updateInvoiceTotals() {
        let totalItems = 0;
        let totalAmount = 0;
        let totalProfit = 0;

        document.querySelectorAll('.product-item').forEach(product => {
            const type = product.querySelector('.product-type-radio:checked').value;

            if (type === 'simple') {
                const quantity = parseFloat(product.querySelector('.simple-quantity').value) || 0;
                const purchasePrice = parseFloat(product.querySelector('.simple-purchase-price').value) || 0;
                const profit = parseFloat(product.querySelector('.simple-profit-total').value) || 0;

                if (quantity > 0) {
                    totalItems++;
                    totalAmount += quantity * purchasePrice;
                    totalProfit += profit;
                }
            } else {
                product.querySelectorAll('.variant-item').forEach(variant => {
                    const quantity = parseFloat(variant.querySelector('.variant-quantity').value) || 0;
                    const purchasePrice = parseFloat(variant.querySelector('.variant-purchase-price').value) || 0;
                    const profit = parseFloat(variant.querySelector('.variant-profit-total').value) || 0;

                    if (quantity > 0) {
                        totalItems++;
                        totalAmount += quantity * purchasePrice;
                        totalProfit += profit;
                    }
                });
            }
        });

        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);
        document.getElementById('totalProfit').textContent = totalProfit.toFixed(2);
    }

    // Add product
    addProductBtn.addEventListener('click', function() {
        const productHTML = getProductTemplate(productCounter);
        productsContainer.insertAdjacentHTML('beforeend', productHTML);
        emptyState.style.display = 'none';

        const newProduct = productsContainer.lastElementChild;
        attachProductEvents(newProduct, productCounter);
        productCounter++;
    });

    // Attach events to product
    function attachProductEvents(productElement, index) {
        // Remove product
        productElement.querySelector('.remove-product-btn').addEventListener('click', function() {
            productElement.remove();
            if (productsContainer.children.length === 0) {
                emptyState.style.display = 'block';
            }
            updateInvoiceTotals();
        });

        // Generate code
        productElement.querySelector('.generate-code-btn').addEventListener('click', function() {
            productElement.querySelector('.product-code').value = generateCode();
        });

        // Product type change
        productElement.querySelectorAll('.product-type-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const simpleFields = productElement.querySelector('.simple-product-fields');
                const variableFields = productElement.querySelector('.variable-product-fields');

                if (this.value === 'simple') {
                    simpleFields.style.display = 'block';
                    variableFields.style.display = 'none';
                    // Make simple fields required
                    simpleFields.querySelectorAll('input[type="number"]').forEach(input => {
                        input.required = true;
                    });
                    // Make variant fields not required
                    variableFields.querySelectorAll('input').forEach(input => {
                        input.required = false;
                    });
                } else {
                    simpleFields.style.display = 'none';
                    variableFields.style.display = 'block';
                    // Make simple fields not required
                    simpleFields.querySelectorAll('input[type="number"]').forEach(input => {
                        input.required = false;
                    });
                }
                updateInvoiceTotals();
            });
        });

        // Calculate simple product profit
        productElement.querySelectorAll('.simple-purchase-price, .simple-selling-price, .simple-quantity').forEach(input => {
            input.addEventListener('input', function() {
                calculateSimpleProfit(productElement);
            });
        });

        // Add variant button
        let variantCounter = 0;
        productElement.querySelector('.add-variant-btn').addEventListener('click', function() {
            const variantsContainer = productElement.querySelector('.variants-container');
            const emptyVariantsState = productElement.querySelector('.empty-variants-state');

            const variantHTML = getVariantTemplate(index, variantCounter);
            variantsContainer.insertAdjacentHTML('beforeend', variantHTML);
            emptyVariantsState.style.display = 'none';

            const newVariant = variantsContainer.lastElementChild;
            attachVariantEvents(newVariant, productElement);
            variantCounter++;
        });

        // Image upload handling
        const imageInput = productElement.querySelector('.product-image-input');
        const selectImageBtn = productElement.querySelector('.select-image-btn');
        const removeImageBtn = productElement.querySelector('.remove-image-btn');
        const imagePreview = productElement.querySelector('.image-preview');

        selectImageBtn.addEventListener('click', function() {
            imageInput.click();
        });

        imagePreview.addEventListener('click', function() {
            imageInput.click();
        });

        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    selectImageBtn.classList.add('d-none');
                    removeImageBtn.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreview.src = '';
            imagePreview.classList.add('d-none');
            selectImageBtn.classList.remove('d-none');
            removeImageBtn.classList.add('d-none');
        });
    }

    // Attach events to variant
    function attachVariantEvents(variantElement, productElement) {
        // Remove variant
        variantElement.querySelector('.remove-variant-btn').addEventListener('click', function() {
            variantElement.remove();
            const variantsContainer = productElement.querySelector('.variants-container');
            const emptyVariantsState = productElement.querySelector('.empty-variants-state');
            if (variantsContainer.children.length === 0) {
                emptyVariantsState.style.display = 'block';
            }
            updateInvoiceTotals();
        });

        // Generate variant code
        variantElement.querySelector('.generate-variant-code-btn').addEventListener('click', function() {
            variantElement.querySelector('.variant-code').value = generateCode();
        });

        // Calculate variant profit
        variantElement.querySelectorAll('.variant-purchase-price, .variant-selling-price, .variant-quantity').forEach(input => {
            input.addEventListener('input', function() {
                calculateVariantProfit(variantElement);
            });
        });
    }

    // Payment method toggle
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const cashboxContainer = document.getElementById('cashboxContainer');
            const cashboxSelect = document.getElementById('cashbox_select');
            if (this.value === 'cash') {
                cashboxContainer.style.display = 'block';
                cashboxSelect.required = true;
            } else {
                cashboxContainer.style.display = 'none';
                cashboxSelect.required = false;
            }
        });
    });

    // Form validation before submit
    document.getElementById('purchaseInvoiceForm').addEventListener('submit', function(e) {
        const products = document.querySelectorAll('.product-item');
        if (products.length === 0) {
            e.preventDefault();
            alert(window.noProductsErrorLabel || 'Please add at least one product.');
            return false;
        }

        // Check if variable products have variants
        let hasError = false;
        products.forEach(product => {
            const type = product.querySelector('.product-type-radio:checked').value;
            if (type === 'variable') {
                const variants = product.querySelectorAll('.variant-item');
                if (variants.length === 0) {
                    hasError = true;
                }
            }
        });

        if (hasError) {
            e.preventDefault();
            alert(window.variableProductNoVariantsLabel || 'Variable products must have at least one variant.');
            return false;
        }
    });

    // Add Supplier Modal
    const saveSupplierBtn = document.getElementById('saveSupplierBtn');
    const supplierSelect = document.getElementById('supplier_select');
    const supplierError = document.getElementById('supplier_error');
    const modal = new bootstrap.Modal(document.getElementById('addSupplierModal'));

    saveSupplierBtn.addEventListener('click', async function() {
        const supplierName = document.getElementById('new_supplier_name').value.trim();
        const supplierPhone = document.getElementById('new_supplier_phone').value.trim();

        if (!supplierName) {
            supplierError.textContent = window.supplierNameRequiredLabel || 'Supplier name is required.';
            supplierError.classList.remove('d-none');
            return;
        }

        saveSupplierBtn.disabled = true;
        supplierError.classList.add('d-none');

        try {
            const response = await fetch('{{ route('api.suppliers.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: supplierName,
                    phone: supplierPhone
                })
            });

            const data = await response.json();

            if (response.ok) {
                // Add new option to select
                const option = new Option(data.supplier.name, data.supplier.id, true, true);
                supplierSelect.add(option);

                // Trigger change event for select2
                $(supplierSelect).trigger('change');

                // Close modal and reset form
                modal.hide();
                document.getElementById('new_supplier_name').value = '';
                document.getElementById('new_supplier_phone').value = '';
            } else {
                supplierError.textContent = data.message || (window.errorOccurredLabel || 'An error occurred.');
                supplierError.classList.remove('d-none');
            }
        } catch (error) {
            supplierError.textContent = window.errorOccurredLabel || 'An error occurred.';
            supplierError.classList.remove('d-none');
        } finally {
            saveSupplierBtn.disabled = false;
        }
    });

    // Reset error when modal is hidden
    document.getElementById('addSupplierModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('new_supplier_name').value = '';
        document.getElementById('new_supplier_phone').value = '';
        supplierError.classList.add('d-none');
    });

    // Set translation labels
    window.productLabel = '{{ __('messages.product') }}';
    window.deleteLabel = '{{ __('messages.delete') }}';
    window.productTypeLabel = '{{ __('messages.product_type') }}';
    window.simpleProductLabel = '{{ __('messages.simple_product') }}';
    window.variableProductLabel = '{{ __('messages.variable_product') }}';
    window.codeLabel = '{{ __('messages.code') }}';
    window.generateLabel = '{{ __('messages.generate') }}';
    window.nameLabel = '{{ __('messages.name') }}';
    window.skuLabel = '{{ __('messages.sku') }}';
    window.quantityLabel = '{{ __('messages.quantity') }}';
    window.purchasePriceLabel = '{{ __('messages.purchase_price') }}';
    window.sellingPriceLabel = '{{ __('messages.selling_price') }}';
    window.profitPerUnitLabel = '{{ __('messages.profit_per_unit') }}';
    window.totalProfitLabel = '{{ __('messages.total_profit') }}';
    window.currencyLabel = '{{ __('messages.currency') }}';
    window.variantsLabel = '{{ __('messages.variants') }}';
    window.addVariantLabel = '{{ __('messages.add_variant') }}';
    window.variantLabel = '{{ __('messages.variant') }}';
    window.variantNameLabel = '{{ __('messages.variant_name') }}';
    window.variantNamePlaceholder = '{{ __('messages.variant_name_placeholder') }}';
    window.noVariantsLabel = '{{ __('messages.no_variants_added') }}';
    window.noProductsErrorLabel = '{{ __('messages.no_products_error') }}';
    window.variableProductNoVariantsLabel = '{{ __('messages.variable_product_no_variants') }}';
    window.supplierNameRequiredLabel = '{{ __('messages.supplier_name_required') }}';
    window.errorOccurredLabel = '{{ __('messages.error_occurred') }}';
    window.productImageLabel = '{{ __('messages.product_image') }}';
});
</script>
@endpush
@endsection
