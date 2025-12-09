@extends('layouts.app')

@section('title', __('messages.add_coupon'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('coupons.index') }}">{{ __('messages.coupons') }}</a></li>
    <li class="breadcrumb-item active">{{ __('messages.add_coupon') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('coupons.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('messages.coupon_details') }}</h5>
                </div>
                <div class="card-body">
                    {{-- Code & Name --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.coupon_code') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="code" id="couponCode"
                                       class="form-control text-uppercase @error('code') is-invalid @enderror"
                                       value="{{ old('code') }}"
                                       placeholder="{{ __('messages.enter_coupon_code') }}"
                                       required>
                                <button type="button" class="btn btn-outline-primary" id="generateCodeBtn">
                                    <i class="ti ti-refresh me-1"></i>
                                    {{ __('messages.generate') }}
                                </button>
                            </div>
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.coupon_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="{{ __('messages.enter_coupon_name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="{{ __('messages.enter_description') }}">{{ old('description') }}</textarea>
                    </div>

                    {{-- Discount Type & Value --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.discount_type') }} <span class="text-danger">*</span></label>
                            <select name="type" id="discountType" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>{{ __('messages.fixed_amount') }}</option>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>{{ __('messages.percentage') }}</option>
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
                                       value="{{ old('value') }}"
                                       placeholder="0.00"
                                       required>
                                <span class="input-group-text" id="valueUnit">{{ __('messages.currency') }}</span>
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
                                       value="{{ old('min_order_amount') }}"
                                       placeholder="{{ __('messages.no_minimum') }}">
                                <span class="input-group-text">{{ __('messages.currency') }}</span>
                            </div>
                            <small class="text-muted">{{ __('messages.min_order_help') }}</small>
                        </div>
                        <div class="col-md-6" id="maxDiscountSection">
                            <label class="form-label">{{ __('messages.max_discount') }}</label>
                            <div class="input-group">
                                <input type="number" name="max_discount" step="0.01" min="0"
                                       class="form-control"
                                       value="{{ old('max_discount') }}"
                                       placeholder="{{ __('messages.no_limit') }}">
                                <span class="input-group-text">{{ __('messages.currency') }}</span>
                            </div>
                            <small class="text-muted">{{ __('messages.max_discount_help') }}</small>
                        </div>
                    </div>

                    {{-- Usage Limits --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.usage_limit') }}</label>
                            <input type="number" name="usage_limit" min="1"
                                   class="form-control"
                                   value="{{ old('usage_limit') }}"
                                   placeholder="{{ __('messages.unlimited') }}">
                            <small class="text-muted">{{ __('messages.usage_limit_help') }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.usage_limit_per_customer') }}</label>
                            <input type="number" name="usage_limit_per_customer" min="1"
                                   class="form-control"
                                   value="{{ old('usage_limit_per_customer') }}"
                                   placeholder="{{ __('messages.unlimited') }}">
                            <small class="text-muted">{{ __('messages.usage_limit_per_customer_help') }}</small>
                        </div>
                    </div>

                    {{-- Validity Dates --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.start_date') }}</label>
                            <input type="date" name="start_date"
                                   class="form-control datepicker"
                                   value="{{ old('start_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.end_date') }}</label>
                            <input type="date" name="end_date"
                                   class="form-control datepicker"
                                   value="{{ old('end_date') }}">
                        </div>
                    </div>

                    {{-- Active Status --}}
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
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

    {{-- Preview Card --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.preview') }}</h5>
            </div>
            <div class="card-body text-center">
                <div class="coupon-preview p-4 border border-2 border-dashed rounded mb-3"
                     style="background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);">
                    <h3 class="text-primary mb-1" id="previewCode">COUPON</h3>
                    <p class="text-muted mb-3" id="previewName">{{ __('messages.coupon_name') }}</p>
                    <h2 class="mb-0" id="previewDiscount">
                        0 {{ __('messages.currency') }}
                    </h2>
                    <small class="text-muted" id="previewType">{{ __('messages.discount') }}</small>
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
    const generateCodeBtn = document.getElementById('generateCodeBtn');
    const couponCode = document.getElementById('couponCode');

    // Update unit based on discount type
    function updateDiscountType() {
        if (discountType.value === 'percentage') {
            valueUnit.textContent = '%';
            maxDiscountSection.classList.remove('d-none');
        } else {
            valueUnit.textContent = '{{ __("messages.currency") }}';
            maxDiscountSection.classList.add('d-none');
        }
        updatePreview();
    }

    discountType.addEventListener('change', updateDiscountType);
    updateDiscountType();

    // Generate code
    generateCodeBtn.addEventListener('click', function() {
        this.disabled = true;
        fetch('{{ route("coupons.generate-code") }}')
            .then(response => response.json())
            .then(data => {
                couponCode.value = data.code;
                updatePreview();
            })
            .finally(() => {
                this.disabled = false;
            });
    });

    // Preview updates
    function updatePreview() {
        const code = couponCode.value || 'COUPON';
        const name = document.querySelector('input[name="name"]').value || '{{ __("messages.coupon_name") }}';
        const value = document.querySelector('input[name="value"]').value || '0';
        const type = discountType.value;

        document.getElementById('previewCode').textContent = code.toUpperCase();
        document.getElementById('previewName').textContent = name;

        if (type === 'percentage') {
            document.getElementById('previewDiscount').textContent = value + '%';
        } else {
            document.getElementById('previewDiscount').textContent = parseFloat(value).toFixed(2) + ' {{ __("messages.currency") }}';
        }
    }

    couponCode.addEventListener('input', updatePreview);
    document.querySelector('input[name="name"]').addEventListener('input', updatePreview);
    document.querySelector('input[name="value"]').addEventListener('input', updatePreview);
});
</script>
@endpush
@endsection
