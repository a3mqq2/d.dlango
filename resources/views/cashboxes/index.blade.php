@extends('layouts.app')

@section('title', __('messages.cashboxes'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.finance') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.cashboxes') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-cash me-2"></i>
                    {{ __('messages.cashboxes') }}
                </h5>
                <a href="{{ route('cashboxes.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    {{ __('messages.add_cashbox') }}
                </a>
            </div>
            <div class="card-body">
                {{-- Search Filter --}}
                <form method="GET" action="{{ route('cashboxes.index') }}" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                   placeholder="{{ __('messages.search_cashboxes') }}"
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary me-2">
                            <i class="ti ti-filter me-1"></i>
                            {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('cashboxes.index') }}" class="btn btn-outline-secondary">
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
                                <th width="35%">{{ __('messages.cashbox_name') }}</th>
                                <th width="20%">{{ __('messages.current_balance') }}</th>
                                <th width="20%">{{ __('messages.opening_balance') }}</th>
                                <th width="20%" class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cashboxes as $cashbox)
                                <tr>
                                    <td class="fw-semibold">{{ $loop->iteration + ($cashboxes->currentPage() - 1) * $cashboxes->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-light-primary rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="ti ti-cash text-primary fs-5"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">{{ $cashbox->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $cashbox->current_balance > 0 ? 'success' : ($cashbox->current_balance < 0 ? 'danger' : 'secondary') }} fs-6 px-3 py-2">
                                            <span dir="ltr">{{ number_format($cashbox->current_balance, 2) }}</span>
                                            <small>{{ __('messages.currency') }}</small>
                                        </span>
                                    </td>
                                    <td>
                                        <span dir="ltr" class="fw-semibold">{{ number_format($cashbox->opening_balance, 2) }}</span>
                                        <small class="text-muted">{{ __('messages.currency') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('cashboxes.show', $cashbox) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="{{ __('messages.view') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('cashboxes.edit', $cashbox) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="{{ __('messages.edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('cashboxes.destroy', $cashbox) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('{{ __('messages.confirm_delete_cashbox') }}')">
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
                                            <i class="ti ti-cash-off" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h5 class="mt-3">{{ __('messages.no_cashboxes') }}</h5>
                                            <p>{{ __('messages.no_cashboxes_desc') }}</p>
                                            <a href="{{ route('cashboxes.create') }}" class="btn btn-primary mt-2">
                                                <i class="ti ti-plus me-1"></i>
                                                {{ __('messages.add_first_cashbox') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($cashboxes->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $cashboxes->withQueryString()->links() }}
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
