@extends('layouts.app')

@section('title', __('messages.customers'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.sales') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.customers') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-users me-2"></i>
                    {{ __('messages.customers') }}
                </h5>
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    {{ __('messages.add_customer') }}
                </a>
            </div>
            <div class="card-body">
                {{-- Search Filter --}}
                <form method="GET" action="{{ route('customers.index') }}" class="row g-3 mb-4">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                   placeholder="{{ __('messages.search_customers') }}"
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-secondary me-2">
                            <i class="ti ti-filter me-1"></i>
                            {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
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
                                <th width="30%">{{ __('messages.customer_name') }}</th>
                                <th width="20%">{{ __('messages.phone') }}</th>
                                <th width="15%">{{ __('messages.balance') }}</th>
                                <th width="10%">{{ __('messages.status') }}</th>
                                <th width="20%" class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td class="fw-semibold">{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-light-primary rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    @if($customer->is_default)
                                                        <i class="ti ti-walk text-primary fs-5"></i>
                                                    @else
                                                        <i class="ti ti-user text-primary fs-5"></i>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">
                                                    {{ $customer->name }}
                                                    @if($customer->is_default)
                                                        <span class="badge bg-info-subtle text-info ms-1">{{ __('messages.default') }}</span>
                                                    @endif
                                                </h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($customer->phone)
                                            <i class="ti ti-phone text-muted me-1"></i>
                                            <span dir="ltr">{{ $customer->phone }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $customer->balance > 0 ? 'success' : ($customer->balance < 0 ? 'danger' : 'secondary') }} fs-6 px-3 py-2">
                                            {{ number_format($customer->balance, 2) }}
                                            <small>{{ __('messages.currency') }}</small>
                                        </span>
                                    </td>
                                    <td>
                                        @if($customer->is_active)
                                            <span class="badge bg-success-subtle text-success">{{ __('messages.active') }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">{{ __('messages.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('customers.show', $customer) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="{{ __('messages.view') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if(!$customer->is_default)
                                                <a href="{{ route('customers.edit', $customer) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="{{ __('messages.edit') }}">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <form action="{{ route('customers.toggle-status', $customer) }}"
                                                      method="POST"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-{{ $customer->is_active ? 'warning' : 'success' }}"
                                                            title="{{ $customer->is_active ? __('messages.deactivate') : __('messages.activate') }}">
                                                        <i class="ti ti-{{ $customer->is_active ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('customers.destroy', $customer) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('{{ __('messages.confirm_delete_customer') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="{{ __('messages.delete') }}">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ti ti-users-off" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h5 class="mt-3">{{ __('messages.no_customers') }}</h5>
                                            <p>{{ __('messages.no_customers_desc') }}</p>
                                            <a href="{{ route('customers.create') }}" class="btn btn-primary mt-2">
                                                <i class="ti ti-plus me-1"></i>
                                                {{ __('messages.add_first_customer') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($customers->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $customers->withQueryString()->links() }}
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
