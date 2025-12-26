@extends('layouts.app')

@section('title', __('messages.purchase_invoices'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.purchases') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.purchase_invoices') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-file-invoice me-2"></i>
                    {{ __('messages.purchase_invoices') }}
                </h5>
                <a href="{{ route('purchase-invoices.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    {{ __('messages.add_purchase_invoice') }}
                </a>
            </div>
            <div class="card-body">
                {{-- Search Filter --}}
                <form method="GET" action="{{ route('purchase-invoices.index') }}" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                   placeholder="{{ __('messages.search_invoice_or_supplier') }}"
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            <option value="pending_shipment" {{ request('status') == 'pending_shipment' ? 'selected' : '' }}>
                                {{ __('messages.pending_shipment') }}
                            </option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>
                                {{ __('messages.received') }}
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                {{ __('messages.cancelled') }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="supplier_id" class="form-select">
                            <option value="">{{ __('messages.all_suppliers') }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary me-2">
                            <i class="ti ti-filter me-1"></i>
                            {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('purchase-invoices.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-refresh me-1"></i>
                            {{ __('messages.reset') }}
                        </a>
                    </div>
                </form>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">{{ __('messages.invoice_number') }}</th>
                                <th width="15%">{{ __('messages.invoice_date') }}</th>
                                <th width="20%">{{ __('messages.supplier') }}</th>
                                <th width="15%">{{ __('messages.total_amount') }}</th>
                                <th width="15%">{{ __('messages.payment_method') }}</th>
                                <th width="10%">{{ __('messages.status') }}</th>
                                <th width="10%" class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-primary" dir="ltr">{{ $invoice->invoice_number }}</span>
                                    </td>
                                    <td>
                                        <i class="ti ti-calendar text-muted me-1"></i>
                                        {{ $invoice->invoice_date }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-light-primary rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 35px; height: 35px;">
                                                    <i class="ti ti-truck-delivery text-primary"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h6 class="mb-0 small">{{ $invoice->supplier->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold" dir="ltr">
                                            {{ number_format($invoice->total_amount, 2) }} {{ __('messages.currency') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($invoice->payment_method == 'cash')
                                            <span class="badge bg-success">
                                                <i class="ti ti-cash me-1"></i>
                                                {{ __('messages.cash') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="ti ti-calendar-due me-1"></i>
                                                {{ __('messages.credit') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->status == 'pending_shipment')
                                            <span class="badge bg-warning">
                                                {{ __('messages.pending_shipment') }}
                                            </span>
                                        @elseif($invoice->status == 'received')
                                            <span class="badge bg-success">
                                                {{ __('messages.received') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                {{ __('messages.cancelled') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('purchase-invoices.show', $invoice) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="{{ __('messages.view') }}">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ti ti-file-invoice" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h5 class="mt-3">{{ __('messages.no_purchase_invoices') }}</h5>
                                            <p>{{ __('messages.no_purchase_invoices_desc') }}</p>
                                            <a href="{{ route('purchase-invoices.create') }}" class="btn btn-primary mt-2">
                                                <i class="ti ti-plus me-1"></i>
                                                {{ __('messages.add_first_purchase_invoice') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($invoices->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $invoices->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.bg-light-primary {
    background-color: rgba(182, 95, 122, 0.1) !important;
}
.avatar-sm {
    width: 35px;
    height: 35px;
}
</style>

@push('scripts')
<script>
// Clear purchase invoice draft cache when viewing the list
// This ensures the cache is cleared after successfully creating an invoice
document.addEventListener('DOMContentLoaded', function() {
    const CACHE_KEY = 'purchase_invoice_draft';
    localStorage.removeItem(CACHE_KEY);
});
</script>
@endpush
@endsection
