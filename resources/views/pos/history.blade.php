@extends('layouts.app')

@section('title', __('messages.sales_history'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.sales') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.sales_history') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-receipt me-2"></i>
                    {{ __('messages.sales_history') }}
                </h5>
                <a href="{{ route('pos.index') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    {{ __('messages.new_sale') }}
                </a>
            </div>
            <div class="card-body">
                {{-- Filters --}}
                <form action="{{ route('pos.history') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control"
                                       placeholder="{{ __('messages.search') }}..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control"
                                   value="{{ request('date_from') }}"
                                   placeholder="{{ __('messages.from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control"
                                   value="{{ request('date_to') }}"
                                   placeholder="{{ __('messages.to_date') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">{{ __('messages.all_statuses') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-filter me-1"></i>
                                {{ __('messages.filter') }}
                            </button>
                            <a href="{{ route('pos.history') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Sales Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.invoice_number') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.items') }}</th>
                                <th>{{ __('messages.total') }}</th>
                                <th>{{ __('messages.payment_method') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('pos.show', $sale) }}" class="fw-bold text-primary">
                                        {{ $sale->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                                <td>{{ $sale->customer->name }}</td>
                                <td>{{ $sale->items_count }}</td>
                                <td class="fw-bold" dir="ltr">{{ number_format($sale->total_amount, 2) }} {{ __('messages.currency') }}</td>
                                <td>
                                    @if($sale->payment_method === 'cash')
                                        <span class="badge bg-success">{{ __('messages.cash') }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ __('messages.credit') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($sale->status)
                                        @case('completed')
                                            <span class="badge bg-success-subtle text-success">{{ __('messages.completed') }}</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning-subtle text-warning">{{ __('messages.pending') }}</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger-subtle text-danger">{{ __('messages.cancelled') }}</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('pos.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('pos.receipt', $sale) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                            <i class="ti ti-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="ti ti-receipt-off text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">{{ __('messages.no_sales_yet') }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $sales->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
