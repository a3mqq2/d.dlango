@extends('layouts.app')

@section('title', __('messages.edit_product') . ' - ' . $product->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('messages.inventory') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('inventory.show', $product) }}">{{ $product->name }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.edit_product') }}</li>
@endsection

@section('content')
<form id="productForm" method="POST" action="{{ route('inventory.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
                        {{-- Product Type (readonly) --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('messages.product_type') }}</label>
                            <div>
                                @if($product->type === 'simple')
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="ti ti-box me-1"></i>
                                        {{ __('messages.simple_product') }}
                                    </span>
                                @else
                                    <span class="badge bg-info px-3 py-2">
                                        <i class="ti ti-layers-subtract me-1"></i>
                                        {{ __('messages.variable_product') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Code (readonly) --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">{{ __('messages.product_code') }}</label>
                            <input type="text" class="form-control" value="{{ $product->code }}" readonly dir="ltr">
                        </div>

                        {{-- Name --}}
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">
                                {{ __('messages.product_name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SKU --}}
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">{{ __('messages.sku') }}</label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                   value="{{ old('sku', $product->sku) }}" dir="ltr">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Image --}}
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">{{ __('messages.product_image') }}</label>
                            @if($product->image)
                                <div class="mb-2">
                                    <img src="{{ $product->image_url }}" class="img-thumbnail" style="max-width: 80px; max-height: 80px;">
                                </div>
                            @endif
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

        @if($product->type === 'simple')
        {{-- Card 2: Simple Product Fields --}}
        <div class="col-12">
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
                                {{ __('messages.quantity') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', $product->quantity) }}" min="0" step="1" dir="ltr">
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
                                       value="{{ old('purchase_price', $product->purchase_price) }}" min="0" step="0.01" dir="ltr" id="simplePurchasePrice">
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
                                       value="{{ old('selling_price', $product->selling_price) }}" min="0" step="0.01" dir="ltr" id="simpleSellingPrice">
                                <span class="input-group-text">{{ __('messages.currency') }}</span>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ __('messages.profit_per_unit') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="simpleProfitUnit"
                                       value="{{ number_format($product->profit_per_unit, 2) }}" readonly dir="ltr">
                                <span class="input-group-text">{{ __('messages.currency') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Card 3: Variable Product Fields --}}
        <div class="col-12">
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
                    <div id="variantsContainer">
                        @foreach($product->variants as $vIndex => $variant)
                        <div class="variant-item border rounded p-3 mb-3 bg-light" data-variant-index="{{ $vIndex }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="ti ti-tag me-1"></i>
                                    {{ __('messages.variant') }} #{{ $vIndex + 1 }}
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                            <input type="hidden" name="variants[{{ $vIndex }}][id]" value="{{ $variant->id }}">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label small">{{ __('messages.variant_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="variants[{{ $vIndex }}][variant_name]" class="form-control"
                                           value="{{ $variant->variant_name }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">{{ __('messages.code') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="variants[{{ $vIndex }}][code]" class="form-control variant-code"
                                               value="{{ $variant->code }}" dir="ltr" maxlength="4">
                                        <button type="button" class="btn btn-outline-secondary generate-variant-code-btn">{{ __('messages.generate') }}</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">{{ __('messages.quantity') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="variants[{{ $vIndex }}][quantity]" class="form-control"
                                           value="{{ $variant->quantity }}" min="0" step="1" dir="ltr">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">{{ __('messages.purchase_price') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="variants[{{ $vIndex }}][purchase_price]" class="form-control variant-purchase-price"
                                               value="{{ $variant->purchase_price }}" min="0" step="0.01" dir="ltr">
                                        <span class="input-group-text">{{ __('messages.currency') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">{{ __('messages.selling_price') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="variants[{{ $vIndex }}][selling_price]" class="form-control variant-selling-price"
                                               value="{{ $variant->selling_price }}" min="0" step="0.01" dir="ltr">
                                        <span class="input-group-text">{{ __('messages.currency') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">{{ __('messages.profit_per_unit') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control variant-profit-unit"
                                               value="{{ number_format($variant->profit_per_unit, 2) }}" readonly dir="ltr">
                                        <span class="input-group-text">{{ __('messages.currency') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($product->variants->isEmpty())
                    <div id="emptyVariantsState" class="text-center py-5 text-muted">
                        <i class="ti ti-tags" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h5 class="mt-3">{{ __('messages.no_variants_added') }}</h5>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Action Buttons --}}
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('inventory.show', $product) }}" class="btn btn-outline-secondary">
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
    // Image preview
    const imageInput = document.getElementById('productImage');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
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
    }

    @if($product->type === 'simple')
    // Calculate profit for simple product
    function calculateSimpleProfit() {
        const purchase = parseFloat(document.getElementById('simplePurchasePrice').value) || 0;
        const selling = parseFloat(document.getElementById('simpleSellingPrice').value) || 0;
        document.getElementById('simpleProfitUnit').value = (selling - purchase).toFixed(2);
    }

    document.getElementById('simplePurchasePrice').addEventListener('input', calculateSimpleProfit);
    document.getElementById('simpleSellingPrice').addEventListener('input', calculateSimpleProfit);
    @else
    // Variable product variant management
    let variantCounter = {{ $product->variants->count() }};
    const variantsContainer = document.getElementById('variantsContainer');
    const emptyVariantsState = document.getElementById('emptyVariantsState');

    // Attach events to existing variants
    document.querySelectorAll('.variant-item').forEach(function(variantElement) {
        attachVariantEvents(variantElement);
    });

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

    document.getElementById('addVariantBtn').addEventListener('click', function() {
        variantsContainer.insertAdjacentHTML('beforeend', getVariantTemplate(variantCounter));
        if (emptyVariantsState) emptyVariantsState.style.display = 'none';

        const newVariant = variantsContainer.lastElementChild;
        attachVariantEvents(newVariant);
        variantCounter++;
    });

    function attachVariantEvents(variantElement) {
        variantElement.querySelector('.remove-variant-btn').addEventListener('click', function() {
            variantElement.remove();
            if (variantsContainer.children.length === 0 && emptyVariantsState) {
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
    @endif
});
</script>
@endpush
@endsection
