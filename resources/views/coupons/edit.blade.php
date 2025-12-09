@extends('layouts.app')

@section('title', __('messages.edit_coupon'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('coupons.index') }}">{{ __('messages.coupons') }}</a></li>
    <li class="breadcrumb-item active">{{ __('messages.edit_coupon') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('coupons.update', $coupon) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('messages.edit_coupon') }}: {{ $coupon->code }}</h5>
                </div>
                <div class="card-body">
                    {{-- Code & Name --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.coupon_code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="couponCode"
                                   class="form-control text-uppercase @error('code') is-invalid @enderror"
                                   value="{{ old('code', $coupon->code) }}"
                                   required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.coupon_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $coupon->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $coupon->description) }}</textarea>
                    </div>

                    {{-- Discount Type & Value --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.discount_type') }} <span class="text-danger">*</span></label>
                            <select name="type" id="discountType" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>{{ __('messages.fixed_amount') }}</option>
                                <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>{{ __('messages.percentage') }}</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.discount_value') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="value" step="0.01" min="0.01"
                                       class="form-control @error('value') is-invalid @enderror"
                                       value="{{ old('value', $coupon->value) }}"
                                       required>
                                <span class="input-group-text" id="valueUnit">{{ $coupon->type == 'percentage' ? '%' : __('messages.currency') }}</span>
                            </div>
                            @error('value')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Min Order & Max Discount --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.min_order_amount') }}</label>
                            <div class="input-group">
                                <input type="number" name="min_order_amount" step="0.01" min="0"
                                       class="form-control"
                                       value="{{ old('min_order_amount', $coupon->min_order_amount) }}">
                                <span class="input-group-text">{{ __('messages.currency') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 {{ $coupon->type == 'fixed' ? 'd-none' : '' }}" id="maxDiscountSection">
                            <label class="form-label">{{ __('messages.max_discount') }}</label>
                            <div class="input-group">
                                <input type="number" name="max_discount" step="0.01" min="0"
                                       class="form-control"
                                       value="{{ old('max_discount', $coupon->max_discount) }}">
                                <span class="input-group-text">{{ __('messages.currency') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Usage Limits --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.usage_limit') }}</label>
                            <input type="number" name="usage_limit" min="1"
                                   class="form-control"
                                   value="{{ old('usage_limit', $coupon->usage_limit) }}">
                            <small class="text-muted">{{ __('messages.current_usage') }}: {{ $coupon->used_count }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.usage_limit_per_customer') }}</label>
                            <input type="number" name="usage_limit_per_customer" min="1"
                                   class="form-control"
                                   value="{{ old('usage_limit_per_customer', $coupon->usage_limit_per_customer) }}">
                        </div>
                    </div>

                    {{-- Validity Dates --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.start_date') }}</label>
                            <input type="date" name="start_date"
                                   class="form-control datepicker"
                                   value="{{ old('start_date', $coupon->start_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.end_date') }}</label>
                            <input type="date" name="end_date"
                                   class="form-control datepicker"
                                   value="{{ old('end_date', $coupon->end_date?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    {{-- Active Status --}}
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                   {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">{{ __('messages.active_coupon') }}</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('coupons.index') }}" class="btn btn-secondary">
                            {{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i>
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Stats Card --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.coupon_stats') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">{{ __('messages.times_used') }}</span>
                    <strong>{{ $coupon->used_count }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">{{ __('messages.status') }}</span>
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
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">{{ __('messages.created_at') }}</span>
                    <span>{{ $coupon->created_at->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountType = document.getElementById('discountType');
    const valueUnit = document.getElementById('valueUnit');
    const maxDiscountSection = document.getElementById('maxDiscountSection');

    function updateDiscountType() {
        if (discountType.value === 'percentage') {
            valueUnit.textContent = '%';
            maxDiscountSection.classList.remove('d-none');
        } else {
            valueUnit.textContent = '{{ __("messages.currency") }}';
            maxDiscountSection.classList.add('d-none');
        }
    }

    discountType.addEventListener('change', updateDiscountType);
});
</script>
@endpush
@endsection
