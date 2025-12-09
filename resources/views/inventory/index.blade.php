@extends('layouts.app')

@section('title', __('messages.inventory'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.inventory') }}</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Statistics Cards --}}
    <div class="col-12">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-primary text-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-white bg-opacity-25 rounded">
                                    <i class="ti ti-packages text-white fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="opacity-75">{{ __('messages.total_products') }}</small>
                                <h4 class="mb-0 text-white">{{ $totalProducts }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-success text-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-white bg-opacity-25 rounded">
                                    <i class="ti ti-check text-white fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="opacity-75">{{ __('messages.in_stock') }}</small>
                                <h4 class="mb-0 text-white">{{ $inStockCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-warning text-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-white bg-opacity-25 rounded">
                                    <i class="ti ti-alert-triangle text-white fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="opacity-75">{{ __('messages.low_stock') }}</small>
                                <h4 class="mb-0 text-white">{{ $lowStockCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-danger text-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-white bg-opacity-25 rounded">
                                    <i class="ti ti-x text-white fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="opacity-75">{{ __('messages.out_of_stock') }}</small>
                                <h4 class="mb-0 text-white">{{ $outOfStockCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inventory Value Card --}}
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">{{ __('messages.total_inventory_value') }}</h6>
                        <h3 class="text-primary mb-0" dir="ltr">
                            {{ number_format($totalInventoryValue, 2) }}
                            <small class="fs-5">{{ __('messages.currency') }}</small>
                        </h3>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge bg-primary me-2">
                            <i class="ti ti-box me-1"></i>
                            {{ __('messages.simple_products') }}: {{ $simpleProducts }}
                        </span>
                        <span class="badge bg-info">
                            <i class="ti ti-layers-subtract me-1"></i>
                            {{ __('messages.variable_products') }}: {{ $variableProducts }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <form action="{{ route('inventory.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">{{ __('messages.search') }}</label>
                        <input type="text" name="search" class="form-control"
                               placeholder="{{ __('messages.search_by_name_code_sku') }}"
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">{{ __('messages.product_type') }}</label>
                        <select name="type" class="form-select">
                            <option value="">{{ __('messages.all_types') }}</option>
                            <option value="simple" {{ request('type') == 'simple' ? 'selected' : '' }}>{{ __('messages.simple') }}</option>
                            <option value="variable" {{ request('type') == 'variable' ? 'selected' : '' }}>{{ __('messages.variable') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">{{ __('messages.stock_status') }}</label>
                        <select name="stock_status" class="form-select">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>{{ __('messages.in_stock') }}</option>
                            <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>{{ __('messages.low_stock') }}</option>
                            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>{{ __('messages.out_of_stock') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-filter me-1"></i>
                            {{ __('messages.filter') }}
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="ti ti-refresh me-1"></i>
                            {{ __('messages.reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-packages me-2"></i>
                    {{ __('messages.products_list') }}
                </h5>
                <span class="badge bg-secondary">{{ $products->total() }} {{ __('messages.product') }}</span>
            </div>
            <div class="card-body p-0">
                @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">{{ __('messages.code') }}</th>
                                    <th>{{ __('messages.product_name') }}</th>
                                    <th width="10%">{{ __('messages.type') }}</th>
                                    <th width="10%">{{ __('messages.quantity') }}</th>
                                    <th width="12%">{{ __('messages.purchase_price') }}</th>
                                    <th width="12%">{{ __('messages.selling_price') }}</th>
                                    <th width="10%">{{ __('messages.status') }}</th>
                                    <th width="12%">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary font-monospace">{{ $product->code }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $product->name }}</div>
                                            @if($product->sku)
                                                <small class="text-muted">SKU: {{ $product->sku }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->type === 'simple')
                                                <span class="badge bg-primary-subtle text-primary">
                                                    <i class="ti ti-box me-1"></i>
                                                    {{ __('messages.simple') }}
                                                </span>
                                            @else
                                                <span class="badge bg-info-subtle text-info">
                                                    <i class="ti ti-layers-subtract me-1"></i>
                                                    {{ __('messages.variable') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->type === 'simple')
                                                <span class="fw-bold {{ $product->quantity > 5 ? 'text-success' : ($product->quantity > 0 ? 'text-warning' : 'text-danger') }}">
                                                    {{ $product->quantity }}
                                                </span>
                                            @else
                                                <span class="fw-bold text-info">
                                                    {{ $product->total_quantity }}
                                                </span>
                                                <small class="text-muted d-block">
                                                    ({{ $product->variants->count() }} {{ __('messages.variants') }})
                                                </small>
                                            @endif
                                        </td>
                                        <td dir="ltr">
                                            @if($product->type === 'simple')
                                                {{ number_format($product->purchase_price, 2) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td dir="ltr">
                                            @if($product->type === 'simple')
                                                {{ number_format($product->selling_price, 2) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $qty = $product->type === 'simple' ? $product->quantity : $product->total_quantity;
                                            @endphp
                                            @if($qty > 5)
                                                <span class="badge bg-success-subtle text-success">
                                                    <i class="ti ti-check me-1"></i>
                                                    {{ __('messages.in_stock') }}
                                                </span>
                                            @elseif($qty > 0)
                                                <span class="badge bg-warning-subtle text-warning">
                                                    <i class="ti ti-alert-triangle me-1"></i>
                                                    {{ __('messages.low_stock') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">
                                                    <i class="ti ti-x me-1"></i>
                                                    {{ __('messages.out_of_stock') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('inventory.show', $product) }}"
                                                   class="btn btn-outline-primary" title="{{ __('messages.details') }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('inventory.barcode-form', $product) }}"
                                                   class="btn btn-outline-secondary" title="{{ __('messages.print_barcode') }}">
                                                    <i class="ti ti-barcode"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="card-footer bg-white border-top">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-packages text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">{{ __('messages.no_products_found') }}</h5>
                        <p class="text-muted">{{ __('messages.no_products_match_criteria') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}
</style>
@endsection
