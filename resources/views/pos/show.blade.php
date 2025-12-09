@extends('layouts.app')

@section('title', __('messages.sale_details') . ' - ' . $sale->invoice_number)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.sales') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('pos.history') }}">{{ __('messages.sales_history') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $sale->invoice_number }}</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Sale Info --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-receipt me-2"></i>
                    {{ __('messages.sale_details') }}
                </h5>
                <div>
                    @switch($sale->status)
                        @case('completed')
                            <span class="badge bg-success fs-6">{{ __('messages.completed') }}</span>
                            @break
                        @case('pending')
                            <span class="badge bg-warning fs-6">{{ __('messages.pending') }}</span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-danger fs-6">{{ __('messages.cancelled') }}</span>
                            @break
                    @endswitch
                </div>
            </div>
            <div class="card-body">
                {{-- Invoice Info --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="small text-muted">{{ __('messages.invoice_number') }}</label>
                        <p class="fw-bold mb-0">{{ $sale->invoice_number }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted">{{ __('messages.date') }}</label>
                        <p class="fw-bold mb-0">{{ $sale->sale_date->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted">{{ __('messages.cashier') }}</label>
                        <p class="fw-bold mb-0">{{ $sale->user->name }}</p>
                    </div>
                </div>

                <hr>

                {{-- Items Table --}}
                <h6 class="mb-3">
                    <i class="ti ti-package me-1"></i>
                    {{ __('messages.items') }} ({{ $sale->items->count() }})
                </h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.product') }}</th>
                                <th class="text-center">{{ __('messages.quantity') }}</th>
                                <th class="text-end">{{ __('messages.unit_price') }}</th>
                                <th class="text-end">{{ __('messages.discount') }}</th>
                                <th class="text-end">{{ __('messages.subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                    @if($item->variant)
                                        <span class="text-muted">- {{ $item->variant->variant_name }}</span>
                                    @endif
                                    <small class="d-block text-muted">{{ $item->variant ? $item->variant->code : $item->product->code }}</small>
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end" dir="ltr">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end" dir="ltr">{{ number_format($item->discount, 2) }}</td>
                                <td class="text-end fw-bold" dir="ltr">{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <div class="bg-light rounded p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('messages.subtotal') }}</span>
                                <span dir="ltr">{{ number_format($sale->subtotal, 2) }} {{ __('messages.currency') }}</span>
                            </div>
                            @if($sale->discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>{{ __('messages.discount') }}</span>
                                <span dir="ltr">-{{ number_format($sale->discount, 2) }} {{ __('messages.currency') }}</span>
                            </div>
                            @endif
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ __('messages.total') }}</strong>
                                <strong class="text-primary fs-5" dir="ltr">{{ number_format($sale->total_amount, 2) }} {{ __('messages.currency') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                @if($sale->notes)
                <div class="mt-4">
                    <h6><i class="ti ti-notes me-1"></i> {{ __('messages.notes') }}</h6>
                    <p class="text-muted mb-0">{{ $sale->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Side Info --}}
    <div class="col-lg-4">
        {{-- Customer Info --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="ti ti-user me-1"></i>
                    {{ __('messages.customer') }}
                </h6>
            </div>
            <div class="card-body">
                <h5 class="mb-2">{{ $sale->customer->name }}</h5>
                @if($sale->customer->phone)
                    <p class="text-muted mb-0" dir="ltr">
                        <i class="ti ti-phone me-1"></i>
                        {{ $sale->customer->phone }}
                    </p>
                @endif
                @if($sale->customer->balance != 0)
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">{{ __('messages.current_balance') }}</small>
                        <h5 class="mb-0 {{ $sale->customer->balance > 0 ? 'text-success' : 'text-danger' }}" dir="ltr">
                            {{ number_format($sale->customer->balance, 2) }} {{ __('messages.currency') }}
                        </h5>
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="ti ti-cash me-1"></i>
                    {{ __('messages.payment_info') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">{{ __('messages.payment_method') }}</span>
                    @if($sale->payment_method === 'cash')
                        <span class="badge bg-success">{{ __('messages.cash') }}</span>
                    @else
                        <span class="badge bg-warning">{{ __('messages.credit') }}</span>
                    @endif
                </div>
                @if($sale->cashbox)
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">{{ __('messages.cashbox') }}</span>
                    <span>{{ $sale->cashbox->name }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">{{ __('messages.paid') }}</span>
                    <span class="text-success fw-bold" dir="ltr">{{ number_format($sale->paid_amount, 2) }} {{ __('messages.currency') }}</span>
                </div>
                @if($sale->remaining_amount > 0)
                <div class="d-flex justify-content-between">
                    <span class="text-muted">{{ __('messages.remaining') }}</span>
                    <span class="text-danger fw-bold" dir="ltr">{{ number_format($sale->remaining_amount, 2) }} {{ __('messages.currency') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-grid gap-2">
            <a href="{{ route('pos.receipt', $sale) }}" class="btn btn-primary" target="_blank">
                <i class="ti ti-printer me-1"></i>
                {{ __('messages.print_receipt') }}
            </a>
            <a href="{{ route('pos.history') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
                {{ __('messages.back') }}
            </a>
        </div>
    </div>
</div>
@endsection
