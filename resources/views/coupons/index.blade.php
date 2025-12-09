@extends('layouts.app')

@section('title', __('messages.coupons'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
    <li class="breadcrumb-item active">{{ __('messages.coupons') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('messages.coupons') }}</h5>
                <a href="{{ route('coupons.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    {{ __('messages.add_coupon') }}
                </a>
            </div>
            <div class="card-body">
                {{-- Filters --}}
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control"
                               placeholder="{{ __('messages.search_coupons') }}"
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>{{ __('messages.expired') }}</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>{{ __('messages.scheduled') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="ti ti-search me-1"></i>
                            {{ __('messages.search') }}
                        </button>
                    </div>
                </form>

                {{-- Coupons Table --}}
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.code') }}</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.discount') }}</th>
                                <th>{{ __('messages.usage') }}</th>
                                <th>{{ __('messages.validity') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                            <tr>
                                <td>
                                    <code class="fs-6">{{ $coupon->code }}</code>
                                </td>
                                <td>{{ $coupon->name }}</td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $coupon->discount_text }}
                                    </span>
                                    @if($coupon->min_order_amount)
                                        <br><small class="text-muted">{{ __('messages.min') }}: {{ number_format($coupon->min_order_amount, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $coupon->used_count }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @else
                                        <span class="text-muted">/ {{ __('messages.unlimited') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->start_date || $coupon->end_date)
                                        @if($coupon->start_date)
                                            <small>{{ __('messages.from') }}: {{ $coupon->start_date->format('Y-m-d') }}</small><br>
                                        @endif
                                        @if($coupon->end_date)
                                            <small>{{ __('messages.to') }}: {{ $coupon->end_date->format('Y-m-d') }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">{{ __('messages.no_limit') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php $status = $coupon->status; @endphp
                                    @switch($status)
                                        @case('active')
                                            <span class="badge bg-success">{{ __('messages.active') }}</span>
                                            @break
                                        @case('inactive')
                                            <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
                                            @break
                                        @case('expired')
                                            <span class="badge bg-danger">{{ __('messages.expired') }}</span>
                                            @break
                                        @case('scheduled')
                                            <span class="badge bg-info">{{ __('messages.scheduled') }}</span>
                                            @break
                                        @case('exhausted')
                                            <span class="badge bg-warning">{{ __('messages.exhausted') }}</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('coupons.show', $coupon) }}" class="btn btn-sm btn-light-primary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-sm btn-light-info">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('coupons.toggle-status', $coupon) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-light-{{ $coupon->is_active ? 'warning' : 'success' }}"
                                                    title="{{ $coupon->is_active ? __('messages.deactivate') : __('messages.activate') }}">
                                                <i class="ti ti-{{ $coupon->is_active ? 'player-pause' : 'player-play' }}"></i>
                                            </button>
                                        </form>
                                        @if($coupon->used_count == 0)
                                        <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ __('messages.confirm_delete_coupon') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-danger">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-discount-2 fs-1 d-block mb-2"></i>
                                        {{ __('messages.no_coupons') }}
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $coupons->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
