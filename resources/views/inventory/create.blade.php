@extends('layouts.app')

@section('title', __('messages.add_product_to_inventory'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('messages.inventory') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.add_product_to_inventory') }}</li>
@endsection

@section('content')
<form id="productForm" method="POST" action="{{ route('inventory.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        {{-- Card 1: Product Information --}}
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-package me-2"></i>
                        {{ __('messages.product_info') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        {{-- Product Type --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('messages.product_type') }}</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_simple"
                                           value="simple" {{ old('type', 'simple') == 'simple' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_simple">
                                        <span class="badge bg-primary px-3 py-2">
                                            <i class="ti ti-box me-1"></i>
                                            {{ __('messages.simple_product') }}
                                        </span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_variable"
                                           value="variable" {{ old('type') == 'variable' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_variable">
                                        <span class="badge bg-info px-3 py-2">
                                            <i class="ti ti-layers-subtract me-1"></i>
                                            {{ __('messages.variable_product') }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Code --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                {{ __('messages.product_code') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                       value="{{ old('code', $productCode) }}" dir="ltr" required maxlength="4">
                                <button type="button" class="btn btn-outline-secondary" id="generateCodeBtn">
                                    {{ __('messages.generate') }}
                                </button>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">
                                {{ __('messages.product_name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SKU --}}
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">{{ __('messages.sku') }}</label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                   value="{{ old('sku') }}" dir="ltr">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Image --}}
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">{{ __('messages.product_image') }}</label>
                            <input type="file" name="image" class="form-control form-control-sm @error('image') is-invalid @enderror"
                                   accept="image/*" id="productImage">
                            <img src="" class="image-preview img-thumbnail d-none mt-2" style="max-width: 80px; max-height: 80px;">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Simple Product Fields --}}
        <div class="col-12" id="simpleProductCard">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-cash me-2"></i>
                        {{ __('messages.pricing_info') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                {{ __('messages.initial_quantity') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', 0) }}" min="0" step="1" dir="ltr">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                {{ __('messages.purchase_price') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror"
                                       value="{{ old('purchase_price') }}" min="0" step="0.01" dir="ltr" id="simplePurchasePrice">
                                <span class="input-group-text">{{ __('messages.currency') }}</span>
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                {{ __('messages.selling_price') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror"
                                       value="{{ old('selling_price') }}" min="0" step="0.01" dir="ltr" id="simpleSellingPrice">
                                <span class="input-group-text">{{ __('messages.currency') }}</span>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ __('messages.profit_per_unit') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="simpleProfitUnit" readonly dir="ltr">
                                <span class="input-group-text">{{ __('messages.currency') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Variable Product Fields --}}
        <div class="col-12" id="variableProductCard" style="display: none;">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-layers-subtract me-2"></i>
                        {{ __('messages.variants') }}
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" id="addVariantBtn">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('messages.add_variant') }}
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="variantsContainer"></div>
                    <div id="emptyVariantsState" class="text-center py-5 text-muted">
                        <i class="ti ti-tags" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h5 class="mt-3">{{ __('messages.no_variants_added') }}</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-x me-1"></i>
                    {{ __('messages.cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>
                    {{ __('messages.save') }}
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let variantCounter = 0;
    const variantsContainer = document.getElementById('variantsContainer');
    const emptyVariantsState = document.getElementById('emptyVariantsState');
    const simpleCard = document.getElementById('simpleProductCard');
    const variableCard = document.getElementById('variableProductCard');

    // Product type toggle
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'simple') {
                simpleCard.style.display = 'block';
                variableCard.style.display = 'none';
            } else {
                simpleCard.style.display = 'none';
                variableCard.style.display = 'block';
            }
        });
    });

    // Initialize based on old value
    const checkedType = document.querySelector('input[name="type"]:checked');
    if (checkedType && checkedType.value === 'variable') {
        simpleCard.style.display = 'none';
        variableCard.style.display = 'block';
    }

    // Generate code
    document.getElementById('generateCodeBtn').addEventListener('click', function() {
        document.querySelector('input[name="code"]').value = Math.floor(1000 + Math.random() * 9000).toString();
    });

    // Image preview
    document.getElementById('productImage').addEventListener('change', function() {
        const preview = document.querySelector('.image-preview');
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Calculate profit
    function calculateSimpleProfit() {
        const purchase = parseFloat(document.getElementById('simplePurchasePrice').value) || 0;
        const selling = parseFloat(document.getElementById('simpleSellingPrice').value) || 0;
        document.getElementById('simpleProfitUnit').value = (selling - purchase).toFixed(2);
    }

    document.getElementById('simplePurchasePrice').addEventListener('input', calculateSimpleProfit);
    document.getElementById('simpleSellingPrice').addEventListener('input', calculateSimpleProfit);

    // Variant template
    function getVariantTemplate(index) {
        return `
            <div class="variant-item border rounded p-3 mb-3 bg-light" data-variant-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        <i class="ti ti-tag me-1"></i>
                        ${window.variantLabel} #${index + 1}
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small">${window.variantNameLabel} <span class="text-danger">*</span></label>
                        <input type="text" name="variants[${index}][variant_name]" class="form-control"
                               placeholder="${window.variantNamePlaceholder}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">${window.codeLabel} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="variants[${index}][code]" class="form-control variant-code" dir="ltr" maxlength="4">
                            <button type="button" class="btn btn-outline-secondary generate-variant-code-btn">${window.generateLabel}</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">${window.quantityLabel} <span class="text-danger">*</span></label>
                        <input type="number" name="variants[${index}][quantity]" class="form-control" min="0" step="1" dir="ltr" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">${window.purchasePriceLabel} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="variants[${index}][purchase_price]" class="form-control variant-purchase-price" min="0" step="0.01" dir="ltr">
                            <span class="input-group-text">${window.currencyLabel}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">${window.sellingPriceLabel} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="variants[${index}][selling_price]" class="form-control variant-selling-price" min="0" step="0.01" dir="ltr">
                            <span class="input-group-text">${window.currencyLabel}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">${window.profitPerUnitLabel}</label>
                        <div class="input-group">
                            <input type="text" class="form-control variant-profit-unit" readonly dir="ltr">
                            <span class="input-group-text">${window.currencyLabel}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Add variant
    document.getElementById('addVariantBtn').addEventListener('click', function() {
        variantsContainer.insertAdjacentHTML('beforeend', getVariantTemplate(variantCounter));
        emptyVariantsState.style.display = 'none';

        const newVariant = variantsContainer.lastElementChild;
        attachVariantEvents(newVariant);
        variantCounter++;
    });

    function attachVariantEvents(variantElement) {
        variantElement.querySelector('.remove-variant-btn').addEventListener('click', function() {
            variantElement.remove();
            if (variantsContainer.children.length === 0) {
                emptyVariantsState.style.display = 'block';
            }
        });

        variantElement.querySelector('.generate-variant-code-btn').addEventListener('click', function() {
            variantElement.querySelector('.variant-code').value = Math.floor(1000 + Math.random() * 9000).toString();
        });

        variantElement.querySelectorAll('.variant-purchase-price, .variant-selling-price').forEach(input => {
            input.addEventListener('input', function() {
                const purchase = parseFloat(variantElement.querySelector('.variant-purchase-price').value) || 0;
                const selling = parseFloat(variantElement.querySelector('.variant-selling-price').value) || 0;
                variantElement.querySelector('.variant-profit-unit').value = (selling - purchase).toFixed(2);
            });
        });
    }

    // Form validation
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const type = document.querySelector('input[name="type"]:checked').value;
        if (type === 'variable' && variantsContainer.children.length === 0) {
            e.preventDefault();
            alert(window.variableProductNoVariantsLabel);
            return false;
        }
    });

    // Translation labels
    window.variantLabel = '{{ __('messages.variant') }}';
    window.variantNameLabel = '{{ __('messages.variant_name') }}';
    window.variantNamePlaceholder = '{{ __('messages.variant_name_placeholder') }}';
    window.codeLabel = '{{ __('messages.code') }}';
    window.generateLabel = '{{ __('messages.generate') }}';
    window.quantityLabel = '{{ __('messages.quantity') }}';
    window.purchasePriceLabel = '{{ __('messages.purchase_price') }}';
    window.sellingPriceLabel = '{{ __('messages.selling_price') }}';
    window.profitPerUnitLabel = '{{ __('messages.profit_per_unit') }}';
    window.currencyLabel = '{{ __('messages.currency') }}';
    window.variableProductNoVariantsLabel = '{{ __('messages.variable_product_no_variants') }}';
});
</script>
@endpush
@endsection
