@extends('layouts.app')

@section('title', __('messages.suppliers'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.purchases') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.suppliers') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-truck-delivery me-2"></i>
                    {{ __('messages.suppliers') }}
                </h5>
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    {{ __('messages.add_supplier') }}
                </a>
            </div>
            <div class="card-body">
                {{-- Search Filter --}}
                <form method="GET" action="{{ route('suppliers.index') }}" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                   placeholder="{{ __('messages.search_suppliers') }}"
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary me-2">
                            <i class="ti ti-filter me-1"></i>
                            {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
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
                                <th width="5%">#</th>
                                <th width="40%">{{ __('messages.supplier_name') }}</th>
                                <th width="20%">{{ __('messages.phone') }}</th>
                                <th width="20%">{{ __('messages.balance') }}</th>
                                <th width="15%" class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr>
                                    <td class="fw-semibold">{{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-light-primary rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="ti ti-building-store text-primary fs-5"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">{{ $supplier->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="ti ti-phone text-muted me-1"></i>
                                        <span dir="ltr">{{ $supplier->phone }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $supplier->balance > 0 ? 'success' : ($supplier->balance < 0 ? 'danger' : 'secondary') }} fs-6 px-3 py-2">
                                            {{ number_format($supplier->balance, 2) }}
                                            <small>{{ __('messages.currency') }}</small>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('suppliers.show', $supplier) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="{{ __('messages.view') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="{{ __('messages.edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('suppliers.destroy', $supplier) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('{{ __('messages.confirm_delete_supplier') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="{{ __('messages.delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ti ti-building-store-off" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h5 class="mt-3">{{ __('messages.no_suppliers') }}</h5>
                                            <p>{{ __('messages.no_suppliers_desc') }}</p>
                                            <a href="{{ route('suppliers.create') }}" class="btn btn-primary mt-2">
                                                <i class="ti ti-plus me-1"></i>
                                                {{ __('messages.add_first_supplier') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($suppliers->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $suppliers->withQueryString()->links() }}
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
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
@endsection
