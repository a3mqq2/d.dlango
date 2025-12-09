@extends('layouts.app')

@section('title', __('messages.coupon_details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('coupons.index') }}">{{ __('messages.coupons') }}</a></li>
    <li class="breadcrumb-item active">{{ $coupon->code }}</li>
@endsection

@section('content')
<div class="row">
    {{-- Coupon Details --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('messages.coupon_details') }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-edit me-1"></i>
                        {{ __('messages.edit') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th class="ps-0" style="width: 150px;">{{ __('messages.code') }}:</th>
                                <td><code class="fs-5">{{ $coupon->code }}</code></td>
                            </tr>
                            <tr>
                                <th class="ps-0">{{ __('messages.name') }}:</th>
                                <td>{{ $coupon->name }}</td>
                            </tr>
                            <tr>
                                <th class="ps-0">{{ __('messages.discount') }}:</th>
                                <td><span class="badge bg-primary fs-6">{{ $coupon->discount_text }}</span></td>
                            </tr>
                            @if($coupon->min_order_amount)
                            <tr>
                                <th class="ps-0">{{ __('messages.min_order') }}:</th>
                                <td>{{ number_format($coupon->min_order_amount, 2) }} {{ __('messages.currency') }}</td>
                            </tr>
                            @endif
                            @if($coupon->type == 'percentage' && $coupon->max_discount)
                            <tr>
                                <th class="ps-0">{{ __('messages.max_discount') }}:</th>
                                <td>{{ number_format($coupon->max_discount, 2) }} {{ __('messages.currency') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th class="ps-0" style="width: 150px;">{{ __('messages.usage') }}:</th>
                                <td>
                                    {{ $coupon->used_count }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @else
                                        / {{ __('messages.unlimited') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">{{ __('messages.per_customer') }}:</th>
                                <td>{{ $coupon->usage_limit_per_customer ?? __('messages.unlimited') }}</td>
                            </tr>
                            <tr>
                                <th class="ps-0">{{ __('messages.validity') }}:</th>
                                <td>
                                    @if($coupon->start_date || $coupon->end_date)
                                        {{ $coupon->start_date?->format('Y-m-d') ?? __('messages.any') }}
                                        -
                                        {{ $coupon->end_date?->format('Y-m-d') ?? __('messages.any') }}
                                    @else
                                        {{ __('messages.no_limit') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">{{ __('messages.status') }}:</th>
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
                            </tr>
                        </table>
                    </div>
                </div>

                @if($coupon->description)
                <div class="mt-3">
                    <h6>{{ __('messages.description') }}</h6>
                    <p class="text-muted">{{ $coupon->description }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Usage History --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.usage_history') }}</h5>
            </div>
            <div class="card-body">
                @if($coupon->usages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.invoice_number') }}</th>
                                <th class="text-end">{{ __('messages.discount_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coupon->usages as $usage)
                            <tr>
                                <td>{{ $usage->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $usage->customer->name }}</td>
                                <td>
                                    <a href="{{ route('pos.show', $usage->sale_id) }}">
                                        {{ $usage->sale->invoice_number }}
                                    </a>
                                </td>
                                <td class="text-end">{{ number_format($usage->discount_amount, 2) }} {{ __('messages.currency') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="ti ti-receipt-off fs-1 d-block mb-2"></i>
                    {{ __('messages.no_usage_yet') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Side Stats --}}
    <div class="col-lg-4">
        {{-- Stats Card --}}
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="coupon-preview p-4 border border-2 border-dashed rounded mb-3"
                     style="background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);">
                    <h3 class="text-primary mb-1">{{ $coupon->code }}</h3>
                    <p class="text-muted mb-3">{{ $coupon->name }}</p>
                    <h2 class="mb-0">{{ $coupon->discount_text }}</h2>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">{{ __('messages.quick_stats') }}</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">{{ __('messages.total_discount_given') }}</span>
                    <strong>{{ number_format($coupon->usages->sum('discount_amount'), 2) }} {{ __('messages.currency') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">{{ __('messages.unique_customers') }}</span>
                    <strong>{{ $coupon->usages->unique('customer_id')->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">{{ __('messages.avg_discount') }}</span>
                    <strong>
                        @if($coupon->usages->count() > 0)
                            {{ number_format($coupon->usages->avg('discount_amount'), 2) }} {{ __('messages.currency') }}
                        @else
                            -
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-primary">
                        <i class="ti ti-edit me-1"></i>
                        {{ __('messages.edit_coupon') }}
                    </a>
                    <form action="{{ route('coupons.toggle-status', $coupon) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $coupon->is_active ? 'warning' : 'success' }} w-100">
                            <i class="ti ti-{{ $coupon->is_active ? 'player-pause' : 'player-play' }} me-1"></i>
                            {{ $coupon->is_active ? __('messages.deactivate') : __('messages.activate') }}
                        </button>
                    </form>
                    <a href="{{ route('coupons.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-1"></i>
                        {{ __('messages.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
