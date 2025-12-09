@extends('layouts.app')

@section('title', __('messages.print_barcode') . ' - ' . $product->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('messages.inventory') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('inventory.show', $product) }}">{{ $product->name }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.print_barcode') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        {{-- Product Info --}}
        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-1 text-white">
                            @if($product->type === 'simple')
                                <i class="ti ti-box me-2"></i>
                            @else
                                <i class="ti ti-layers-subtract me-2"></i>
                            @endif
                            {{ $product->name }}
                        </h5>
                        <p class="mb-0 opacity-75">
                            {{ __('messages.code') }}: {{ $product->code }}
                            @if($product->sku)
                                | SKU: {{ $product->sku }}
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        @if($product->type === 'simple')
                            <span class="badge bg-white text-primary">{{ __('messages.simple_product') }}</span>
                        @else
                            <span class="badge bg-white text-info">{{ __('messages.variable_product') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Barcode Options --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-barcode me-2"></i>
                    {{ __('messages.select_items_to_print') }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('inventory.bulk-barcode') }}" method="POST" id="barcodeForm">
                    @csrf

                    @if($product->type === 'simple')
                        {{-- Simple Product --}}
                        <div class="card border mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-1">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input item-checkbox"
                                                   id="product_{{ $product->id }}"
                                                   data-type="product"
                                                   data-id="{{ $product->id }}" checked>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <small class="text-muted">
                                            <span class="badge bg-secondary font-monospace">{{ $product->code }}</span>
                                            <span class="ms-2">{{ number_format($product->selling_price, 2) }} {{ __('messages.currency') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ __('messages.stock') }}: {{ $product->quantity }}
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">{{ __('messages.quantity') }}</span>
                                            <input type="number" class="form-control quantity-input"
                                                   id="qty_product_{{ $product->id }}"
                                                   min="1" max="100" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Variable Product Variants --}}
                        @foreach($product->variants as $variant)
                            <div class="card border mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-1">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input item-checkbox"
                                                       id="variant_{{ $variant->id }}"
                                                       data-type="variant"
                                                       data-id="{{ $variant->id }}">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <h6 class="mb-1">{{ $product->name }} - {{ $variant->variant_name }}</h6>
                                            <small class="text-muted">
                                                <span class="badge bg-secondary font-monospace">{{ $variant->code }}</span>
                                                <span class="ms-2">{{ number_format($variant->selling_price, 2) }} {{ __('messages.currency') }}</span>
                                            </small>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="badge {{ $variant->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ __('messages.stock') }}: {{ $variant->quantity }}
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ __('messages.quantity') }}</span>
                                                <input type="number" class="form-control quantity-input"
                                                       id="qty_variant_{{ $variant->id }}"
                                                       min="1" max="100" value="1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    {{-- Hidden inputs for form submission --}}
                    <div id="hiddenInputs"></div>

                    {{-- Actions --}}
                    <div class="d-flex justify-content-between pt-3 border-top">
                        <div>
                            @if($product->type === 'variable')
                                <button type="button" class="btn btn-outline-primary btn-sm" id="selectAll">
                                    <i class="ti ti-checks me-1"></i>
                                    {{ __('messages.select_all') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAll">
                                    <i class="ti ti-x me-1"></i>
                                    {{ __('messages.deselect_all') }}
                                </button>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('inventory.show', $product) }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
                                {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-printer me-1"></i>
                                {{ __('messages.print_barcode') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('barcodeForm');
    const hiddenInputs = document.getElementById('hiddenInputs');

    // Select/Deselect all buttons
    document.getElementById('selectAll')?.addEventListener('click', function() {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = true);
    });

    document.getElementById('deselectAll')?.addEventListener('click', function() {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        hiddenInputs.innerHTML = '';

        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('{{ __("messages.select_at_least_one_item") }}');
            return;
        }

        let index = 0;
        checkboxes.forEach(function(cb) {
            const type = cb.dataset.type;
            const id = cb.dataset.id;
            const qtyInput = document.getElementById('qty_' + type + '_' + id);
            const qty = qtyInput ? qtyInput.value : 1;

            hiddenInputs.innerHTML += `
                <input type="hidden" name="items[${index}][type]" value="${type}">
                <input type="hidden" name="items[${index}][id]" value="${id}">
                <input type="hidden" name="items[${index}][quantity]" value="${qty}">
            `;
            index++;
        });
    });
});
</script>
@endpush
@endsection
