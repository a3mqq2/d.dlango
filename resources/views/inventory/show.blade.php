@extends('layouts.app')

@section('title', __('messages.product_details') . ' - ' . $product->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('messages.inventory') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Product Info Card --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-light-primary rounded-circle mx-auto mb-4"
                     style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    @if($product->type === 'simple')
                        <i class="ti ti-box text-primary" style="font-size: 3rem;"></i>
                    @else
                        <i class="ti ti-layers-subtract text-info" style="font-size: 3rem;"></i>
                    @endif
                </div>

                <h3 class="mb-2">{{ $product->name }}</h3>

                <div class="mb-3">
                    <span class="badge bg-secondary font-monospace fs-6">{{ $product->code }}</span>
                    @if($product->sku)
                        <span class="badge bg-light text-dark ms-1">SKU: {{ $product->sku }}</span>
                    @endif
                </div>

                <div class="mb-3">
                    @if($product->type === 'simple')
                        <span class="badge bg-primary-subtle text-primary">
                            <i class="ti ti-box me-1"></i>
                            {{ __('messages.simple_product') }}
                        </span>
                    @else
                        <span class="badge bg-info-subtle text-info">
                            <i class="ti ti-layers-subtract me-1"></i>
                            {{ __('messages.variable_product') }}
                        </span>
                    @endif
                </div>

                {{-- Stock Status --}}
                @php
                    $qty = $product->type === 'simple' ? $product->quantity : $product->total_quantity;
                @endphp
                <div class="bg-light rounded-3 p-4 mb-4">
                    <small class="text-muted d-block mb-2">{{ __('messages.current_stock') }}</small>
                    <h2 class="mb-1 {{ $qty > 5 ? 'text-success' : ($qty > 0 ? 'text-warning' : 'text-danger') }}">
                        {{ $qty }}
                        <small class="fs-5">{{ __('messages.unit') }}</small>
                    </h2>
                    @if($qty > 5)
                        <span class="badge bg-success">{{ __('messages.in_stock') }}</span>
                    @elseif($qty > 0)
                        <span class="badge bg-warning">{{ __('messages.low_stock') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('messages.out_of_stock') }}</span>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="d-grid gap-2">
                    <a href="{{ route('inventory.barcode-form', $product) }}" class="btn btn-primary">
                        <i class="ti ti-barcode me-1"></i>
                        {{ __('messages.print_barcode') }}
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
                        {{ __('messages.back_to_inventory') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Product Details --}}
    <div class="col-lg-8">
        @if($product->type === 'simple')
            {{-- Simple Product Details --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-info-circle me-2"></i>
                        {{ __('messages.pricing_info') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <small class="text-muted d-block mb-1">{{ __('messages.purchase_price') }}</small>
                                <h4 class="mb-0 text-danger" dir="ltr">
                                    {{ number_format($product->purchase_price, 2) }}
                                    <small class="fs-6">{{ __('messages.currency') }}</small>
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <small class="text-muted d-block mb-1">{{ __('messages.selling_price') }}</small>
                                <h4 class="mb-0 text-success" dir="ltr">
                                    {{ number_format($product->selling_price, 2) }}
                                    <small class="fs-6">{{ __('messages.currency') }}</small>
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <small class="text-muted d-block mb-1">{{ __('messages.profit_per_unit') }}</small>
                                <h4 class="mb-0 text-primary" dir="ltr">
                                    {{ number_format($product->profit_per_unit, 2) }}
                                    <small class="fs-6">{{ __('messages.currency') }}</small>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <small class="text-muted d-block mb-1">{{ __('messages.total_stock_value') }}</small>
                                <h5 class="mb-0 text-success" dir="ltr">
                                    {{ number_format($product->quantity * $product->purchase_price, 2) }}
                                    <small class="fs-6">{{ __('messages.currency') }}</small>
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <small class="text-white d-block mb-1">{{ __('messages.expected_profit') }}</small>
                                <h5 class="mb-0 text-white" dir="ltr">
                                    {{ number_format($product->quantity * $product->profit_per_unit, 2) }}
                                    <small class="fs-6">{{ __('messages.currency') }}</small>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Variable Product Variants --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-layers-subtract me-2"></i>
                        {{ __('messages.product_variants') }}
                    </h5>
                    <span class="badge bg-info">{{ $product->variants->count() }} {{ __('messages.variants') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">{{ __('messages.code') }}</th>
                                    <th>{{ __('messages.variant_name') }}</th>
                                    <th width="10%">{{ __('messages.quantity') }}</th>
                                    <th width="15%">{{ __('messages.purchase_price') }}</th>
                                    <th width="15%">{{ __('messages.selling_price') }}</th>
                                    <th width="12%">{{ __('messages.profit') }}</th>
                                    <th width="10%">{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary font-monospace">{{ $variant->code }}</span>
                                        </td>
                                        <td class="fw-semibold">{{ $variant->variant_name }}</td>
                                        <td>
                                            <span class="fw-bold {{ $variant->quantity > 5 ? 'text-success' : ($variant->quantity > 0 ? 'text-warning' : 'text-danger') }}">
                                                {{ $variant->quantity }}
                                            </span>
                                        </td>
                                        <td dir="ltr">{{ number_format($variant->purchase_price, 2) }}</td>
                                        <td dir="ltr">{{ number_format($variant->selling_price, 2) }}</td>
                                        <td dir="ltr" class="text-success">{{ number_format($variant->profit_per_unit, 2) }}</td>
                                        <td>
                                            @if($variant->quantity > 5)
                                                <span class="badge bg-success-subtle text-success">
                                                    {{ __('messages.in_stock') }}
                                                </span>
                                            @elseif($variant->quantity > 0)
                                                <span class="badge bg-warning-subtle text-warning">
                                                    {{ __('messages.low_stock') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">
                                                    {{ __('messages.out_of_stock') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Variants Summary --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-report-analytics me-2"></i>
                        {{ __('messages.variants_summary') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $totalVariantValue = $product->variants->sum(function($v) {
                                return $v->quantity * $v->purchase_price;
                            });
                            $totalVariantProfit = $product->variants->sum(function($v) {
                                return $v->quantity * $v->profit_per_unit;
                            });
                        @endphp
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <small class="text-muted d-block mb-1">{{ __('messages.total_variants_quantity') }}</small>
                                <h4 class="mb-0 text-primary">{{ $product->total_quantity }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3 text-center">
                                <small class="text-muted d-block mb-1">{{ __('messages.total_stock_value') }}</small>
                                <h4 class="mb-0 text-success" dir="ltr">
                                    {{ number_format($totalVariantValue, 2) }}
                                    <small class="fs-6">{{ __('messages.currency') }}</small>
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 text-center">
                                <small class="text-white d-block mb-1">{{ __('messages.expected_profit') }}</small>
                                <h4 class="mb-0 text-white" dir="ltr">
                                    {{ number_format($totalVariantProfit, 2) }}
                                    <small class="fs-6">{{ __('messages.currency') }}</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Purchase History --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-history me-2"></i>
                    {{ __('messages.purchase_history') }}
                </h5>
            </div>
            <div class="card-body p-0">
                @if($product->purchaseInvoiceItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.invoice_number') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.purchase_price') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->purchaseInvoiceItems->take(10) as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('purchase-invoices.show', $item->purchaseInvoice) }}" class="text-primary">
                                                #{{ $item->purchaseInvoice->invoice_number }}
                                            </a>
                                        </td>
                                        <td>{{ $item->purchaseInvoice->invoice_date }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td dir="ltr">{{ number_format($item->purchase_price, 2) }}</td>
                                        <td>
                                            @if($item->purchaseInvoice->status === 'received')
                                                <span class="badge bg-success-subtle text-success">{{ __('messages.received') }}</span>
                                            @elseif($item->purchaseInvoice->status === 'pending_shipment')
                                                <span class="badge bg-warning-subtle text-warning">{{ __('messages.pending_shipment') }}</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">{{ __('messages.cancelled') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-receipt-off text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0">{{ __('messages.no_purchase_history') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.bg-light-primary {
    background-color: rgba(41, 26, 107, 0.1) !important;
}
</style>
@endsection
