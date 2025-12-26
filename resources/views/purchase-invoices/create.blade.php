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
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearDraftBtn" title="مسح المسودة">
                            <i class="ti ti-trash me-1"></i>
                            مسح المسودة
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="addProductBtn">
                            <i class="ti ti-plus me-1"></i>
                            {{ __('messages.add_product') }}
                        </button>
                    </div>
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

{{-- Print Barcode Modal --}}
<div class="modal fade" id="printBarcodeModal" tabindex="-1" aria-labelledby="printBarcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="printBarcodeModalLabel">
                    <i class="ti ti-printer me-2"></i>
                    {{ __('messages.print_barcode') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        {{ __('messages.product_code') }}
                    </label>
                    <input type="text" id="barcode_code_display" class="form-control" dir="ltr" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        {{ __('messages.product_name') }}
                    </label>
                    <input type="text" id="barcode_name_display" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        {{ __('messages.number_of_copies') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number" id="barcode_copies" class="form-control" min="1" value="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="confirmPrintBarcodeBtn">
                    <i class="ti ti-printer me-1"></i>
                    {{ __('messages.print') }}
                </button>
            </div>
        </div>
    </div>
</div>

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
    const CACHE_KEY = 'purchase_invoice_draft';

    console.log('Add Product Button:', addProductBtn);

    if (!addProductBtn) {
        console.error('addProductBtn not found!');
        return;
    }

    // ========== Auto-save functionality ==========
    function saveToCache() {
        const formData = {
            invoice_date: document.querySelector('input[name="invoice_date"]').value,
            supplier_id: document.querySelector('#supplier_select').value,
            payment_method: document.querySelector('input[name="payment_method"]:checked')?.value,
            cashbox_id: document.querySelector('#cashbox_select').value,
            notes: document.querySelector('textarea[name="notes"]').value,
            products: []
        };

        // Save all products
        document.querySelectorAll('.product-item').forEach((productEl) => {
            const productIndex = productEl.getAttribute('data-product-index');
            const productType = productEl.querySelector('.product-type-radio:checked').value;

            const product = {
                index: productIndex,
                type: productType,
                code: productEl.querySelector('.product-code').value,
                name: productEl.querySelector(`input[name="products[${productIndex}][name]"]`).value,
                sku: productEl.querySelector(`input[name="products[${productIndex}][sku]"]`).value,
            };

            if (productType === 'simple') {
                product.quantity = productEl.querySelector('.simple-quantity').value;
                product.purchase_price = productEl.querySelector('.simple-purchase-price').value;
                product.selling_price = productEl.querySelector('.simple-selling-price').value;
            } else {
                product.variants = [];
                productEl.querySelectorAll('.variant-item').forEach((variantEl) => {
                    const variantIndex = variantEl.getAttribute('data-variant-index');
                    product.variants.push({
                        variant_name: variantEl.querySelector(`input[name="products[${productIndex}][variants][${variantIndex}][variant_name]"]`).value,
                        code: variantEl.querySelector('.variant-code').value,
                        quantity: variantEl.querySelector('.variant-quantity').value,
                        purchase_price: variantEl.querySelector('.variant-purchase-price').value,
                        selling_price: variantEl.querySelector('.variant-selling-price').value,
                    });
                });
            }

            formData.products.push(product);
        });

        localStorage.setItem(CACHE_KEY, JSON.stringify(formData));
        console.log('Data saved to cache');
    }

    function loadFromCache() {
        const cached = localStorage.getItem(CACHE_KEY);
        if (!cached) return false;

        try {
            const data = JSON.parse(cached);

            // Restore basic fields
            if (data.invoice_date) {
                document.querySelector('input[name="invoice_date"]').value = data.invoice_date;
            }
            if (data.supplier_id) {
                document.querySelector('#supplier_select').value = data.supplier_id;
                $('#supplier_select').trigger('change');
            }
            if (data.payment_method) {
                const paymentRadio = document.querySelector(`input[name="payment_method"][value="${data.payment_method}"]`);
                if (paymentRadio) {
                    paymentRadio.checked = true;
                    paymentRadio.dispatchEvent(new Event('change'));
                }
            }
            if (data.cashbox_id) {
                document.querySelector('#cashbox_select').value = data.cashbox_id;
            }
            if (data.notes) {
                document.querySelector('textarea[name="notes"]').value = data.notes;
            }

            // Restore products
            if (data.products && data.products.length > 0) {
                data.products.forEach(product => {
                    addProductFromCache(product);
                });
                return true;
            }
        } catch (error) {
            console.error('Error loading from cache:', error);
            localStorage.removeItem(CACHE_KEY);
        }
        return false;
    }

    function addProductFromCache(productData) {
        const productHTML = getProductTemplate(productData.index);
        productsContainer.insertAdjacentHTML('beforeend', productHTML);
        emptyState.style.display = 'none';

        const newProduct = productsContainer.lastElementChild;

        // Fill product data
        newProduct.querySelector('.product-code').value = productData.code || '';
        newProduct.querySelector(`input[name="products[${productData.index}][name]"]`).value = productData.name || '';
        newProduct.querySelector(`input[name="products[${productData.index}][sku]"]`).value = productData.sku || '';

        // Set product type
        const typeRadio = newProduct.querySelector(`input[name="products[${productData.index}][type]"][value="${productData.type}"]`);
        if (typeRadio) {
            typeRadio.checked = true;
            typeRadio.dispatchEvent(new Event('change'));
        }

        if (productData.type === 'simple') {
            newProduct.querySelector('.simple-quantity').value = productData.quantity || '';
            newProduct.querySelector('.simple-purchase-price').value = productData.purchase_price || '';
            newProduct.querySelector('.simple-selling-price').value = productData.selling_price || '';
            calculateSimpleProfit(newProduct);
        } else if (productData.variants && productData.variants.length > 0) {
            productData.variants.forEach((variantData, variantIndex) => {
                const variantHTML = getVariantTemplate(productData.index, variantIndex);
                const variantsContainer = newProduct.querySelector('.variants-container');
                const emptyVariantsState = newProduct.querySelector('.empty-variants-state');

                variantsContainer.insertAdjacentHTML('beforeend', variantHTML);
                emptyVariantsState.style.display = 'none';

                const newVariant = variantsContainer.lastElementChild;

                // Fill variant data
                newVariant.querySelector(`input[name="products[${productData.index}][variants][${variantIndex}][variant_name]"]`).value = variantData.variant_name || '';
                newVariant.querySelector('.variant-code').value = variantData.code || '';
                newVariant.querySelector('.variant-quantity').value = variantData.quantity || '';
                newVariant.querySelector('.variant-purchase-price').value = variantData.purchase_price || '';
                newVariant.querySelector('.variant-selling-price').value = variantData.selling_price || '';

                calculateVariantProfit(newVariant);
                attachVariantEvents(newVariant, newProduct);
            });
        }

        attachProductEvents(newProduct, productData.index);

        if (productData.index >= productCounter) {
            productCounter = parseInt(productData.index) + 1;
        }
    }

    function clearCache() {
        localStorage.removeItem(CACHE_KEY);
        console.log('Cache cleared');
    }

    function setupAutoSave() {
        const form = document.getElementById('purchaseInvoiceForm');

        // Debounce function to avoid too frequent saves
        let saveTimeout;
        const debouncedSave = () => {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(saveToCache, 500);
        };

        // Listen to form changes
        form.addEventListener('input', debouncedSave);
        form.addEventListener('change', debouncedSave);
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
                            <button type="button" class="btn btn-outline-primary print-barcode-btn" title="${window.printBarcodeLabel || 'Print Barcode'}">
                                <i class="ti ti-printer"></i>
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
                            <button type="button" class="btn btn-outline-primary print-variant-barcode-btn" title="${window.printBarcodeLabel || 'Print Barcode'}">
                                <i class="ti ti-printer"></i>
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
            saveToCache(); // Save after removing product
        });

        // Generate code
        productElement.querySelector('.generate-code-btn').addEventListener('click', function() {
            productElement.querySelector('.product-code').value = generateCode();
        });

        // Print barcode
        productElement.querySelector('.print-barcode-btn').addEventListener('click', function() {
            const code = productElement.querySelector('.product-code').value;
            const name = productElement.querySelector(`input[name="products[${index}][name]"]`).value;
            const price = productElement.querySelector('.simple-selling-price').value || '0';

            if (!code) {
                alert(window.pleaseEnterCodeLabel || 'Please enter product code first');
                return;
            }
            if (!name) {
                alert(window.pleaseEnterNameLabel || 'Please enter product name first');
                return;
            }

            openPrintBarcodeModal(code, name, price);
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
            saveToCache(); // Save after removing variant
        });

        // Generate variant code
        variantElement.querySelector('.generate-variant-code-btn').addEventListener('click', function() {
            variantElement.querySelector('.variant-code').value = generateCode();
        });

        // Print variant barcode
        variantElement.querySelector('.print-variant-barcode-btn').addEventListener('click', function() {
            const variantIndex = variantElement.getAttribute('data-variant-index');
            const productIndex = productElement.getAttribute('data-product-index');

            const code = variantElement.querySelector('.variant-code').value;
            const productName = productElement.querySelector(`input[name="products[${productIndex}][name]"]`).value;
            const variantName = variantElement.querySelector(`input[name="products[${productIndex}][variants][${variantIndex}][variant_name]"]`).value;
            const price = variantElement.querySelector('.variant-selling-price').value || '0';

            const fullName = variantName ? `${productName} - ${variantName}` : productName;

            if (!code) {
                alert(window.pleaseEnterCodeLabel || 'Please enter variant code first');
                return;
            }
            if (!productName) {
                alert(window.pleaseEnterNameLabel || 'Please enter product name first');
                return;
            }

            openPrintBarcodeModal(code, fullName, price);
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

        // Don't clear cache here - it will be cleared after successful save
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

    // ========== Print Barcode Functionality ==========
    let currentBarcodeData = null;
    const printBarcodeModal = new bootstrap.Modal(document.getElementById('printBarcodeModal'));

    function openPrintBarcodeModal(code, name, price) {
        currentBarcodeData = { code, name, price };
        document.getElementById('barcode_code_display').value = code;
        document.getElementById('barcode_name_display').value = name;
        document.getElementById('barcode_copies').value = 1;
        printBarcodeModal.show();
    }

    document.getElementById('confirmPrintBarcodeBtn').addEventListener('click', function() {
        if (!currentBarcodeData) return;

        const copies = parseInt(document.getElementById('barcode_copies').value) || 1;

        // Build barcode data array
        const barcodes = [];
        for (let i = 0; i < copies; i++) {
            barcodes.push({
                code: currentBarcodeData.code,
                name: currentBarcodeData.name,
                price: parseFloat(currentBarcodeData.price) || 0
            });
        }

        // Send to server to generate barcode print page
        fetch('{{ route('inventory.bulk-barcode') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'text/html'
            },
            body: JSON.stringify({ barcodes: barcodes })
        })
        .then(response => response.text())
        .then(html => {
            // Open new window with the barcode print page
            const printWindow = window.open('', '_blank');
            printWindow.document.write(html);
            printWindow.document.close();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إعداد صفحة الطباعة');
        });

        printBarcodeModal.hide();
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
    window.printBarcodeLabel = '{{ __('messages.print_barcode') }}';
    window.pleaseEnterCodeLabel = '{{ __('messages.please_enter_code') }}';
    window.pleaseEnterNameLabel = '{{ __('messages.please_enter_name') }}';

    // ========== Initialize ==========
    // Clear draft button
    document.getElementById('clearDraftBtn').addEventListener('click', function() {
        if (confirm('هل أنت متأكد من مسح المسودة المحفوظة؟ لا يمكن التراجع عن هذا الإجراء.')) {
            clearCache();
            location.reload();
        }
    });

    // Load cached data on page load
    const hasCachedData = loadFromCache();
    if (hasCachedData) {
        // Show notification that data was restored
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 80px; left: 50%; transform: translateX(-50%); z-index: 9999; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="ti ti-info-circle me-2" style="font-size: 1.5rem;"></i>
                <div>
                    <strong>تم استرجاع البيانات المحفوظة</strong>
                    <p class="mb-0 small">تم تحميل المسودة السابقة تلقائياً</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 150);
        }, 5000);
    }

    // Setup auto-save
    setupAutoSave();
});
</script>
@endpush
@endsection
