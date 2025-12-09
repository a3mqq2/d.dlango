@extends('layouts.app')

@section('title', __('messages.sales_returns'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
    <li class="breadcrumb-item active">{{ __('messages.sales_returns') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('messages.sales_returns') }}</h5>
                <a href="{{ route('returns.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    {{ __('messages.new_return') }}
                </a>
            </div>
            <div class="card-body">
                {{-- Filters --}}
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control"
                               placeholder="{{ __('messages.search_returns') }}"
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_from" class="form-control datepicker"
                               placeholder="{{ __('messages.from_date') }}"
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_to" class="form-control datepicker"
                               placeholder="{{ __('messages.to_date') }}"
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="ti ti-search me-1"></i>
                            {{ __('messages.search') }}
                        </button>
                    </div>
                </form>

                {{-- Returns Table --}}
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.return_number') }}</th>
                                <th>{{ __('messages.invoice_number') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.return_date') }}</th>
                                <th>{{ __('messages.total_amount') }}</th>
                                <th>{{ __('messages.refund_method') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $return)
                            <tr>
                                <td>
                                    <a href="{{ route('returns.show', $return) }}" class="fw-bold">
                                        {{ $return->return_number }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('pos.show', $return->sale_id) }}">
                                        {{ $return->sale->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $return->customer->name }}</td>
                                <td>{{ $return->return_date->format('Y-m-d H:i') }}</td>
                                <td>{{ number_format($return->total_amount, 2) }} {{ __('messages.currency') }}</td>
                                <td>
                                    @if($return->refund_method === 'cash')
                                        <span class="badge bg-success">{{ __('messages.cash') }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ __('messages.credit') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($return->status === 'completed')
                                        <span class="badge bg-success">{{ __('messages.completed') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('messages.cancelled') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('returns.show', $return) }}" class="btn btn-sm btn-light-primary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('returns.receipt', $return) }}" class="btn btn-sm btn-light-info" target="_blank">
                                            <i class="ti ti-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-receipt-refund fs-1 d-block mb-2"></i>
                                        {{ __('messages.no_returns') }}
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $returns->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
