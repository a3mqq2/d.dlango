@extends('layouts.app')

@section('title', __('messages.return_details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('returns.index') }}">{{ __('messages.sales_returns') }}</a></li>
    <li class="breadcrumb-item active">{{ $return->return_number }}</li>
@endsection

@section('content')
<div class="row">
    {{-- Return Info --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('messages.return_details') }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('returns.receipt', $return) }}" class="btn btn-primary btn-sm" target="_blank">
                        <i class="ti ti-printer me-1"></i>
                        {{ __('messages.print_receipt') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Return Summary --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th class="ps-0" style="width: 150px;">{{ __('messages.return_number') }}:</th>
                                <td><strong>{{ $return->return_number }}</strong></td>
                            </tr>
                            <tr>
                                <th class="ps-0">{{ __('messages.invoice_number') }}:</th>
                                <td>
                                    <a href="{{ route('pos.show', $return->sale_id) }}">
                                        {{ $return->sale->invoice_number }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">{{ __('messages.return_date') }}:</th>
                                <td>{{ $return->return_date->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th class="ps-0">{{ __('messages.processed_by') }}:</th>
                                <td>{{ $return->user->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th class="ps-0" style="width: 150px;">{{ __('messages.customer') }}:</th>
                                <td>{{ $return->customer->name }}</td>
                            </tr>
                            <tr>
                                <th class="ps-0">{{ __('messages.refund_method') }}:</th>
                                <td>
                                    @if($return->refund_method === 'cash')
                                        <span class="badge bg-success">{{ __('messages.cash') }}</span>
                                        @if($return->cashbox)
                                            - {{ $return->cashbox->name }}
                                        @endif
                                    @else
                                        <span class="badge bg-warning">{{ __('messages.credit') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">{{ __('messages.status') }}:</th>
                                <td>
                                    @if($return->status === 'completed')
                                        <span class="badge bg-success">{{ __('messages.completed') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('messages.cancelled') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Reason & Notes --}}
                @if($return->reason || $return->notes)
                <div class="row mb-4">
                    @if($return->reason)
                    <div class="col-md-6">
                        <div class="alert alert-warning mb-0">
                            <strong>{{ __('messages.reason') }}:</strong> {{ $return->reason }}
                        </div>
                    </div>
                    @endif
                    @if($return->notes)
                    <div class="col-md-6">
                        <div class="alert alert-info mb-0">
                            <strong>{{ __('messages.notes') }}:</strong> {{ $return->notes }}
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Items Table --}}
                <h6 class="mb-3">{{ __('messages.returned_items') }}</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.product') }}</th>
                                <th class="text-center">{{ __('messages.quantity') }}</th>
                                <th class="text-end">{{ __('messages.unit_price') }}</th>
                                <th class="text-end">{{ __('messages.subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($return->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                    @if($item->variant)
                                        <br><small class="text-muted">{{ $item->variant->variant_name }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2) }} {{ __('messages.currency') }}</td>
                                <td class="text-end">{{ number_format($item->subtotal, 2) }} {{ __('messages.currency') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">{{ __('messages.total') }}:</td>
                                <td class="text-end fw-bold">{{ number_format($return->total_amount, 2) }} {{ __('messages.currency') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Side Info --}}
    <div class="col-lg-4">
        {{-- Total Card --}}
        <div class="card bg-primary text-white mb-3">
            <div class="card-body text-center">
                <h6 class="text-white mb-2">{{ __('messages.return_total') }}</h6>
                <h2 class="mb-0">{{ number_format($return->total_amount, 2) }} {{ __('messages.currency') }}</h2>
            </div>
        </div>

        {{-- Original Sale Card --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">{{ __('messages.original_sale') }}</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">{{ __('messages.invoice_number') }}:</td>
                        <td class="text-end">
                            <a href="{{ route('pos.show', $return->sale_id) }}">
                                {{ $return->sale->invoice_number }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('messages.sale_date') }}:</td>
                        <td class="text-end">{{ $return->sale->sale_date->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('messages.total_amount') }}:</td>
                        <td class="text-end">{{ number_format($return->sale->total_amount, 2) }} {{ __('messages.currency') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('messages.payment_method') }}:</td>
                        <td class="text-end">
                            @if($return->sale->payment_method === 'cash')
                                {{ __('messages.cash') }}
                            @else
                                {{ __('messages.credit') }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('returns.receipt', $return) }}" class="btn btn-outline-primary" target="_blank">
                        <i class="ti ti-printer me-1"></i>
                        {{ __('messages.print_receipt') }}
                    </a>
                    <a href="{{ route('returns.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('messages.new_return') }}
                    </a>
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-1"></i>
                        {{ __('messages.back_to_returns') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
