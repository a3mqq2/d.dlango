@extends('layouts.app')

@section('title', __('messages.invoice_details'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.purchase_management') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('purchase-invoices.index') }}">{{ __('messages.purchase_invoices') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $invoice->invoice_number }}</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Card 1: Invoice Header & Information --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white border-bottom-0">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0 text-white">
                            <i class="ti ti-file-invoice me-2"></i>
                            {{ __('messages.invoice_details') }}
                        </h5>
                        <h3 class="mb-0 mt-2" dir="ltr">#{{ $invoice->invoice_number }}</h3>
                    </div>
                    <div class="col-md-6 text-md-end">
                        @if($invoice->status == 'pending_shipment')
                            <span class="badge bg-warning fs-5 px-4 py-2">
                                <i class="ti ti-truck-delivery me-1"></i>
                                {{ __('messages.pending_shipment') }}
                            </span>
                        @elseif($invoice->status == 'received')
                            <span class="badge bg-success fs-5 px-4 py-2">
                                <i class="ti ti-check me-1"></i>
                                {{ __('messages.received') }}
                            </span>
                        @else
                            <span class="badge bg-danger fs-5 px-4 py-2">
                                <i class="ti ti-x me-1"></i>
                                {{ __('messages.cancelled') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="border-start border-primary border-3 ps-3">
                            <label class="text-muted small mb-1">{{ __('messages.invoice_date') }}</label>
                            <p class="mb-0 fw-bold h6">
                                <i class="ti ti-calendar text-primary me-2"></i>
                                {{ $invoice->invoice_date->format('Y-m-d') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-start border-success border-3 ps-3">
                            <label class="text-muted small mb-1">{{ __('messages.supplier') }}</label>
                            <p class="mb-0 fw-bold h6">
                                <i class="ti ti-truck-delivery text-success me-2"></i>
                                {{ $invoice->supplier->name }}
                            </p>
                            <small class="text-muted">{{ $invoice->supplier->phone }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-start border-info border-3 ps-3">
                            <label class="text-muted small mb-1">{{ __('messages.payment_method') }}</label>
                            <p class="mb-0">
                                @if($invoice->payment_method == 'cash')
                                    <span class="badge bg-success fs-6 px-3 py-2">
                                        <i class="ti ti-cash me-1"></i>
                                        {{ __('messages.cash') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning fs-6 px-3 py-2">
                                        <i class="ti ti-calendar-due me-1"></i>
                                        {{ __('messages.credit') }}
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($invoice->payment_method == 'cash' && $invoice->cashbox)
                        <div class="col-md-3">
                            <div class="border-start border-warning border-3 ps-3">
                                <label class="text-muted small mb-1">{{ __('messages.cashbox') }}</label>
                                <p class="mb-0 fw-bold h6">
                                    <i class="ti ti-cash text-warning me-2"></i>
                                    {{ $invoice->cashbox->name }}
                                </p>
                            </div>
                        </div>
                    @endif
                    @if($invoice->notes)
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>{{ __('messages.notes') }}:</strong> {{ $invoice->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Invoice Items Grouped by Product --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-package me-2"></i>
                    {{ __('messages.invoice_items') }}
                    <span class="badge bg-primary ms-2">{{ $invoice->items->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                @php
                    $groupedItems = $invoice->items->groupBy('product_id');
                @endphp

                @foreach($groupedItems as $productId => $items)
                    @php
                        $firstItem = $items->first();
                        $product = $firstItem->product;
                    @endphp

                    <div class="border-bottom">
                        {{-- Product Header --}}
                        <div class="bg-light p-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar bg-primary text-white rounded d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px;">
                                                <i class="ti ti-package fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 fw-bold">{{ $product->name }}</h6>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-light text-dark" dir="ltr">
                                                    <i class="ti ti-barcode me-1"></i>
                                                    {{ $product->code }}
                                                </span>
                                                @if($product->sku)
                                                    <span class="badge bg-light text-dark">
                                                        SKU: {{ $product->sku }}
                                                    </span>
                                                @endif
                                                <span class="badge {{ $product->type == 'simple' ? 'bg-info' : 'bg-warning' }}">
                                                    {{ $product->type == 'simple' ? __('messages.simple_product') : __('messages.variable_product') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                    <div class="row g-2">
                                        <div class="col-md-12">
                                            <small class="text-muted d-block">{{ __('messages.total_items') }}</small>
                                            <strong class="text-primary">{{ $items->sum('quantity') }} {{ __('messages.pieces') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Items Table --}}
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        @if($product->type == 'variable')
                                            <th width="5%">#</th>
                                            <th width="20%">{{ __('messages.variant') }}</th>
                                            <th width="10%">{{ __('messages.code') }}</th>
                                        @else
                                            <th width="5%">#</th>
                                            <th width="30%" colspan="2">{{ __('messages.item') }}</th>
                                        @endif
                                        <th width="10%">{{ __('messages.quantity') }}</th>
                                        <th width="12%">{{ __('messages.purchase_price') }}</th>
                                        <th width="12%">{{ __('messages.selling_price') }}</th>
                                        <th width="12%">{{ __('messages.profit_per_unit') }}</th>
                                        <th width="12%">{{ __('messages.total_profit') }}</th>
                                        <th width="12%">{{ __('messages.subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="fw-semibold">{{ $loop->iteration }}</td>
                                            @if($product->type == 'variable')
                                                <td>
                                                    <span class="badge bg-info-subtle text-info fs-6 px-3 py-2">
                                                        <i class="ti ti-tag me-1"></i>
                                                        {{ $item->variant->variant_name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-dark" dir="ltr">{{ $item->variant->code }}</span>
                                                </td>
                                            @else
                                                <td colspan="2">
                                                    <span class="text-muted">{{ __('messages.single_item') }}</span>
                                                </td>
                                            @endif
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2" dir="ltr">
                                                    {{ $item->quantity }}
                                                </span>
                                            </td>
                                            <td dir="ltr" class="fw-semibold text-danger">
                                                {{ number_format($item->purchase_price, 2) }}
                                                <small class="text-muted">{{ __('messages.lyd') }}</small>
                                            </td>
                                            <td dir="ltr" class="fw-semibold text-success">
                                                {{ number_format($item->selling_price, 2) }}
                                                <small class="text-muted">{{ __('messages.lyd') }}</small>
                                            </td>
                                            <td dir="ltr" class="fw-bold text-info">
                                                {{ number_format($item->profit_per_unit, 2) }}
                                                <small class="text-muted">{{ __('messages.lyd') }}</small>
                                            </td>
                                            <td dir="ltr" class="fw-bold text-info">
                                                {{ number_format($item->total_profit, 2) }}
                                                <small class="text-muted">{{ __('messages.lyd') }}</small>
                                            </td>
                                            <td dir="ltr" class="fw-bold">
                                                {{ number_format($item->subtotal, 2) }}
                                                <small class="text-muted">{{ __('messages.lyd') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @if($items->count() > 1)
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="{{ $product->type == 'variable' ? 3 : 2 }}" class="text-end fw-bold">
                                                {{ __('messages.product_total') }}:
                                            </td>
                                            <td class="fw-bold" dir="ltr">
                                                {{ $items->sum('quantity') }}
                                            </td>
                                            <td colspan="3"></td>
                                            <td class="fw-bold text-info" dir="ltr">
                                                {{ number_format($items->sum('total_profit'), 2) }} {{ __('messages.lyd') }}
                                            </td>
                                            <td class="fw-bold text-success" dir="ltr">
                                                {{ number_format($items->sum('subtotal'), 2) }} {{ __('messages.lyd') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Card 3: Invoice Summary --}}
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <h5 class="text-white mb-4">
                    <i class="ti ti-file-text me-2"></i>
                    {{ __('messages.invoice_summary') }}
                </h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-white bg-opacity-25 border-0">
                            <div class="card-body text-center text-white">
                                <i class="ti ti-package fs-1 mb-2"></i>
                                <h6 class="mb-1">{{ __('messages.total_items') }}</h6>
                                <h2 class="mb-0">{{ $invoice->items->sum('quantity') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-white bg-opacity-25 border-0">
                            <div class="card-body text-center text-white">
                                <i class="ti ti-file-invoice fs-1 mb-2"></i>
                                <h6 class="mb-1">{{ __('messages.products_count') }}</h6>
                                <h2 class="mb-0">{{ $groupedItems->count() }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success bg-opacity-75 border-0">
                            <div class="card-body text-center text-white">
                                <i class="ti ti-currency-dollar fs-1 mb-2"></i>
                                <h6 class="mb-1">{{ __('messages.total_amount') }}</h6>
                                <h2 class="mb-0" dir="ltr">{{ number_format($invoice->total_amount, 2) }}</h2>
                                <small>{{ __('messages.lyd') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info bg-opacity-75 border-0">
                            <div class="card-body text-center text-white">
                                <i class="ti ti-trending-up fs-1 mb-2"></i>
                                <h6 class="mb-1">{{ __('messages.total_profit') }}</h6>
                                <h2 class="mb-0" dir="ltr">{{ number_format($invoice->total_profit, 2) }}</h2>
                                <small>{{ __('messages.lyd') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="{{ route('purchase-invoices.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
                        {{ __('messages.back') }}
                    </a>

                    <div class="d-flex gap-2 flex-wrap">
                        @if($invoice->status == 'pending_shipment')
                            <form action="{{ route('purchase-invoices.cancel', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_cancel_invoice') }}');">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="ti ti-x me-1"></i>
                                    {{ __('messages.cancel_invoice') }}
                                </button>
                            </form>
                            <form action="{{ route('purchase-invoices.receive', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_receive_invoice') }}');">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="ti ti-check me-1"></i>
                                    {{ __('messages.receive_invoice') }}
                                </button>
                            </form>
                        @elseif($invoice->status == 'received')
                            <div class="alert alert-success mb-0 py-3">
                                <i class="ti ti-circle-check me-2 fs-4"></i>
                                <strong>{{ __('messages.invoice_received_successfully') }}</strong>
                            </div>
                        @else
                            <div class="alert alert-danger mb-0 py-3">
                                <i class="ti ti-alert-circle me-2 fs-4"></i>
                                <strong>{{ __('messages.invoice_cancelled') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
