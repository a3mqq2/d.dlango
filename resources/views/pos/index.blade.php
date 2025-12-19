@extends('layouts.pos')

@section('title', __('messages.pos'))

@push('styles')
<style>
    .pos-container {
        height: 100%;
        overflow: hidden;
    }
    .products-section {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .products-grid {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    .product-card {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        height: 100%;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(182, 95, 122, 0.25);
        border-color: #b65f7a;
    }
    .product-card .product-image {
        height: 100px;
        object-fit: cover;
        background: #f8f9fa;
    }
    .product-card .no-image {
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #b65f7a 0%, #8b4558 100%);
        color: white;
        font-size: 2.5rem;
    }
    .cart-section {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0,0,0,0.08);
        max-height: calc(100vh - 2rem);
        overflow: hidden;
    }
    .cart-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e9ecef;
        background: linear-gradient(135deg, #b65f7a 0%, #8b4558 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        flex-shrink: 0;
    }
    .cart-items {
        flex: 1 1 0;
        overflow-y: auto;
        min-height: 80px;
    }
    .cart-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f3f4;
        transition: background 0.2s;
    }
    .cart-item:hover {
        background: #f8f9fa;
    }
    .cart-item .quantity-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .cart-item .quantity-controls button {
        width: 28px;
        height: 28px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .cart-item .quantity-controls input {
        width: 50px;
        text-align: center;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 0.25rem;
    }
    .cart-summary {
        padding: 0.75rem 1.25rem;
        background: #f8f9fa;
        border-top: 2px solid #e9ecef;
        flex-shrink: 0;
        flex-grow: 0;
    }
    .cart-total {
        font-size: 1.25rem;
        font-weight: 700;
        color: #b65f7a;
    }
    .cart-actions {
        padding: 0.75rem 1.25rem;
        background: white;
        border-radius: 0 0 12px 12px;
        flex-shrink: 0;
        flex-grow: 0;
        border-top: 1px solid #e9ecef;
    }
    .btn-pay {
        background: linear-gradient(135deg, #b65f7a 0%, #8b4558 100%);
        border: none;
        font-size: 1rem;
        padding: 0.65rem 1rem;
        font-weight: 600;
    }
    .btn-pay:hover {
        background: linear-gradient(135deg, #a3506a 0%, #7a3c4c 100%);
        transform: translateY(-1px);
    }
    .btn-pay:disabled {
        background: #ccc;
        transform: none;
    }
    .search-box {
        position: relative;
    }
    .search-box input {
        padding-right: 2.5rem;
        border-radius: 25px;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
    }
    .search-box input:focus {
        border-color: #b65f7a;
        box-shadow: 0 0 0 3px rgba(182, 95, 122, 0.15);
    }
    .search-box .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .empty-cart {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }
    .empty-cart i {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }
    .out-of-stock {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        border-radius: 8px;
    }
    .stock-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 0.7rem;
    }
    .customer-select-wrapper {
        background: white;
        border-radius: 8px;
        padding: 0.5rem;
    }
    .text-primary-custom {
        color: #b65f7a !important;
    }
    .bg-primary-custom {
        background-color: #b65f7a !important;
    }
    .discount-wrapper {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        max-width: 150px;
    }
    .discount-wrapper input {
        flex: 1;
        min-width: 0;
    }
    .discount-wrapper select {
        width: 60px;
        flex-shrink: 0;
        font-size: 0.875rem;
    }
    .variable-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        font-size: 0.65rem;
        z-index: 2;
    }
    .variant-item {
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    .variant-item:hover {
        background-color: #f8f9fa;
        border-color: #b65f7a;
    }
    .variant-item.out-of-stock-variant {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .price-range {
        font-size: 0.75rem;
    }

    /* Large screens - ensure cart actions always visible */
    @media (min-width: 992px) {
        .cart-items {
            max-height: calc(100vh - 600px);
            min-height: 80px;
        }
    }

    /* Small screen responsive fixes */
    @media (max-width: 991.98px) {
        .pos-container {
            height: auto;
            overflow: visible;
        }
        .products-section {
            height: auto;
            min-height: 300px;
        }
        .products-grid {
            max-height: 400px;
            overflow-y: auto;
        }
        .cart-section {
            height: auto;
            margin-top: 1rem;
            max-height: none;
        }
        .cart-items {
            max-height: 200px;
            min-height: 100px;
        }
        .cart-summary {
            padding: 0.5rem 1rem;
        }
        .cart-actions {
            padding: 0.75rem 1rem;
        }
        .btn-pay {
            padding: 0.75rem;
            font-size: 1rem;
        }
        .customer-select-wrapper {
            padding: 0.25rem;
        }
        .cart-total {
            font-size: 1.15rem;
        }
    }

    @media (max-width: 575.98px) {
        .product-card .product-image,
        .product-card .no-image {
            height: 80px;
        }
        .product-card .no-image {
            font-size: 2rem;
        }
        .cart-header {
            padding: 0.5rem 0.75rem;
        }
        .cart-summary {
            padding: 0.5rem 0.75rem;
        }
        .cart-actions {
            padding: 0.5rem 0.75rem;
        }
        .cart-total {
            font-size: 1rem;
        }
        .cart-items {
            max-height: 150px;
            min-height: 80px;
        }
        .empty-cart {
            padding: 1.5rem 1rem;
        }
        .empty-cart i {
            font-size: 2.5rem;
        }
        .payment-methods-grid {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }
        .payment-methods-grid .btn-check + label {
            width: 100%;
            font-size: 0.75rem;
            padding: 0.4rem;
        }
        .discount-wrapper {
            max-width: 130px;
        }
        .discount-wrapper input {
            font-size: 0.85rem;
        }
        .discount-wrapper select {
            width: 55px;
            font-size: 0.85rem;
        }
        .customer-select-wrapper {
            padding: 0.15rem;
        }
        .btn-pay {
            padding: 0.6rem;
            font-size: 0.95rem;
        }
    }

    /* Payment method buttons styling */
    .payment-methods-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .payment-methods-grid .btn-check + label {
        flex: 1;
        min-width: 80px;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="pos-container">
    <div class="row g-3 h-100">
        {{-- Products Section --}}
        <div class="col-lg-8">
            <div class="products-section">
                {{-- Search & Filters --}}
                <div class="bg-white rounded-3 shadow-sm p-3 mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <div class="search-box">
                                <i class="ti ti-search search-icon"></i>
                                <input type="text"
                                       id="productSearch"
                                       class="form-control form-control-lg ps-5"
                                       placeholder="{{ __('messages.search_by_name_code_barcode') }}"
                                       autofocus>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-primary w-100" id="refreshProducts">
                                <i class="ti ti-refresh me-1"></i>
                                {{ __('messages.refresh') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Products Grid --}}
                <div class="products-grid bg-white rounded-3 shadow-sm" id="productsGrid">
                    <div class="row g-3" id="productsContainer">
                        {{-- Products loaded via JavaScript --}}
                    </div>
                    <div id="loadingProducts" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">{{ __('messages.loading_products') }}...</p>
                    </div>
                    <div id="noProducts" class="text-center py-5 d-none">
                        <i class="ti ti-package-off text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p class="mt-2 text-muted">{{ __('messages.no_products_found') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cart Section --}}
        <div class="col-lg-4">
            <div class="cart-section">
             
                {{-- Customer Selection --}}
                <div class="p-2 border-bottom flex-shrink-0">
                    <div class="customer-select-wrapper">
                        <label class="form-label small text-muted mb-1">{{ __('messages.customer') }}</label>
                        <div class="input-group input-group-sm">
                            <select id="customerSelect" class="form-select">
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                            data-is-default="{{ $customer->is_default ? '1' : '0' }}"
                                            {{ $customer->is_default ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                        @if($customer->balance != 0)
                                            ({{ number_format($customer->balance, 2) }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" id="addCustomerBtn" title="{{ __('messages.add_customer') }}">
                                <i class="ti ti-user-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Cart Items --}}
                <div class="cart-items" id="cartItems">
                    <div class="empty-cart" id="emptyCart">
                        <i class="ti ti-shopping-cart-off"></i>
                        <p>{{ __('messages.cart_empty') }}</p>
                        <small class="text-muted">{{ __('messages.click_product_to_add') }}</small>
                    </div>
                </div>

                {{-- Cart Summary --}}
                <div class="cart-summary">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('messages.subtotal') }}</span>
                        <span id="cartSubtotal">0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">{{ __('messages.discount') }}</span>
                        <div class="discount-wrapper">
                            <input type="number" id="discountInput" class="form-control form-control-sm text-end" value="0" min="0" step="0.01">
                            <select id="discountType" class="form-select form-select-sm">
                                <option value="fixed">{{ __('messages.currency') }}</option>
                                <option value="percentage">%</option>
                            </select>
                        </div>
                    </div>
                    {{-- Coupon Section --}}
                    <div class="mb-2">
                        <div class="input-group input-group-sm">
                            <input type="text" id="couponCode" class="form-control text-uppercase"
                                   placeholder="{{ __('messages.enter_coupon') }}">
                            <button type="button" class="btn btn-outline-primary" id="applyCouponBtn">
                                <i class="ti ti-discount-2"></i>
                            </button>
                        </div>
                        <div id="couponMessage" class="small mt-1 d-none"></div>
                        <div id="appliedCoupon" class="d-none mt-2 p-2 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">
                                    <i class="ti ti-discount-2 text-success me-1"></i>
                                    <span id="appliedCouponCode"></span>
                                    (<span id="appliedCouponDiscount"></span>)
                                </span>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0" id="removeCouponBtn">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="couponDiscountRow" class="d-flex justify-content-between mb-2 d-none">
                        <span class="text-success">{{ __('messages.coupon_discount') }}</span>
                        <span class="text-success" id="couponDiscountAmount">-0.00</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">{{ __('messages.total') }}</span>
                        <span class="cart-total" id="cartTotal">0.00 <small>{{ __('messages.currency') }}</small></span>
                    </div>
                </div>

                {{-- Cart Actions --}}
                <div class="cart-actions">
                    {{-- Payment Method --}}
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">{{ __('messages.payment_method') }}</label>
                        <div class="payment-methods-grid" role="group">
                            <input type="radio" class="btn-check" name="paymentMethod" id="payCash" value="cash" checked>
                            <label class="btn btn-sm btn-outline-success text-white" for="payCash">
                                <i class="ti ti-cash me-1"></i>
                                {{ __('messages.cash') }}
                            </label>
                            <input type="radio" class="btn-check" name="paymentMethod" id="payCredit" value="credit">
                            <label class="btn btn-sm btn-outline-warning" for="payCredit">
                                <i class="ti ti-calendar-due me-1"></i>
                                {{ __('messages.credit') }}
                            </label>
                        </div>
                    </div>

                    {{-- Payment Type (for cash only: cash or bank_transfer) --}}
                    <div id="paymentTypeSection" class="mb-2">
                        <label class="form-label small text-muted mb-1">{{ __('messages.payment_type') }}</label>
                        <div class="payment-methods-grid" role="group">
                            <input type="radio" class="btn-check" name="paymentType" id="typeCash" value="cash" checked>
                            <label class="btn btn-sm btn-outline-primary text-white" for="typeCash">
                                <i class="ti ti-cash me-1"></i>
                                {{ __('messages.cash') }}
                            </label>
                            <input type="radio" class="btn-check" name="paymentType" id="typeBankTransfer" value="bank_transfer">
                            <label class="btn btn-sm btn-outline-info" for="typeBankTransfer">
                                <i class="ti ti-building-bank me-1"></i>
                                {{ __('messages.bank_transfer') }}
                            </label>
                        </div>
                    </div>

                    {{-- Bank Account (for bank_transfer only) --}}
                    <div id="bankAccountSection" class="mb-2" style="display: none;">
                        <label class="form-label small text-muted mb-1">{{ __('messages.bank_account_number') }}</label>
                        <input type="text" id="bankAccount" class="form-control form-control-sm" placeholder="{{ __('messages.enter_bank_account') }}">
                    </div>

                    {{-- Cashbox Selection (for cash payment only) --}}
                    <div id="cashboxSection" class="mb-2">
                        <select id="cashboxSelect" class="form-select form-select-sm">
                            @foreach($cashboxes as $cashbox)
                                <option value="{{ $cashbox->id }}">{{ $cashbox->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Paid Amount (optional) --}}
                    <div class="mb-2" id="paidAmountSection">
                        <label class="form-label small text-muted mb-1">{{ __('messages.paid_amount') }}</label>
                        <div class="input-group input-group-sm">
                            <input type="number" id="paidAmount" class="form-control" min="0" step="0.01">
                            <span class="input-group-text">{{ __('messages.currency') }}</span>
                        </div>
                    </div>

                    {{-- Pay Button --}}
                    <button type="button" class="btn btn-pay w-100 text-white" id="payBtn" disabled>
                        <i class="ti ti-check me-2"></i>
                        {{ __('messages.complete_sale') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Success Modal --}}
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="ti ti-check text-white" style="font-size: 3rem;"></i>
                    </div>
                </div>
                <h4 class="mb-2">{{ __('messages.sale_completed') }}</h4>
                <p class="text-muted mb-4">{{ __('messages.invoice_number') }}: <strong id="invoiceNumber"></strong></p>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-primary" id="printReceiptBtn">
                            <i class="ti ti-receipt me-1"></i>
                            {{ __('messages.print_receipt') }}
                        </button>
                        <button type="button" class="btn btn-primary" id="printInvoiceA4Btn">
                            <i class="ti ti-file-text me-1"></i>
                            {{ __('messages.print_invoice_a4') }}
                        </button>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="newSaleBtn">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('messages.new_sale') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Variants Modal --}}
<div class="modal fade" id="variantsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div id="variantProductImage" class="rounded" style="width: 60px; height: 60px; background: linear-gradient(135deg, #b65f7a 0%, #8b4558 100%); display: flex; align-items: center; justify-content: center;">
                        <i class="ti ti-package text-white" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-1" id="variantProductName"></h5>
                        <small class="text-muted" id="variantProductCode"></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="text-muted small mb-3">{{ __('messages.select_variant') }}</p>
                <div id="variantsList" class="d-flex flex-column gap-2">
                    {{-- Variants loaded via JavaScript --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Customer Modal --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-user-plus me-2"></i>
                    {{ __('messages.add_customer') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="newCustomerName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.phone') }}</label>
                        <input type="text" class="form-control" id="newCustomerPhone" dir="ltr">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="saveCustomerBtn">
                    <i class="ti ti-check me-1"></i>
                    {{ __('messages.save') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    let products = [];
    let currentSaleId = null;
    let currentVariantProduct = null;
    let appliedCoupon = null;
    let couponDiscount = 0;

    const productSearch = document.getElementById('productSearch');
    const productsContainer = document.getElementById('productsContainer');
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const loadingProducts = document.getElementById('loadingProducts');
    const noProducts = document.getElementById('noProducts');
    const payBtn = document.getElementById('payBtn');
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    const variantsModal = new bootstrap.Modal(document.getElementById('variantsModal'));

    // Keep focus on search input
    function focusSearch() {
        productSearch.focus();
        productSearch.select();
    }

    // Focus on page load
    focusSearch();

    // Refocus when clicking anywhere except inputs
    document.addEventListener('click', function(e) {
        if (!e.target.matches('input, select, button, textarea')) {
            setTimeout(focusSearch, 10);
        }
    });

    // Load products
    async function loadProducts() {
        loadingProducts.classList.remove('d-none');
        noProducts.classList.add('d-none');
        productsContainer.innerHTML = '';

        try {
            const response = await fetch('{{ route('pos.products') }}');
            products = await response.json();
            renderProducts(products);
        } catch (error) {
            console.error('Error loading products:', error);
        } finally {
            loadingProducts.classList.add('d-none');
            focusSearch();
        }
    }

    // Render products grid
    function renderProducts(productsList) {
        if (productsList.length === 0) {
            noProducts.classList.remove('d-none');
            return;
        }

        noProducts.classList.add('d-none');

        productsContainer.innerHTML = productsList.map(product => {
            const isVariable = product.type === 'variable' && product.variants && product.variants.length > 0;
            const priceDisplay = product.price_range
                ? `${parseFloat(product.price_range[0]).toFixed(2)} - ${parseFloat(product.price_range[1]).toFixed(2)}`
                : parseFloat(product.price).toFixed(2);

            return `
            <div class="col-6 col-md-4 col-xl-3">
                <div class="product-card card h-100 ${product.quantity <= 0 ? 'opacity-50' : ''}"
                     data-product='${JSON.stringify(product)}'
                     data-code="${product.code}"
                     ${product.quantity <= 0 ? '' : (isVariable ? 'onclick="showVariants(this)"' : 'onclick="addToCart(this)"')}>
                    ${product.image
                        ? `<img src="${product.image}" class="card-img-top product-image" alt="${product.name}">`
                        : `<div class="no-image"><i class="ti ti-package"></i></div>`
                    }
                    ${isVariable ? `<span class="badge bg-info variable-badge"><i class="ti ti-list me-1"></i>${product.variants.length}</span>` : ''}
                    <span class="badge ${product.quantity > 5 ? 'bg-success' : (product.quantity > 0 ? 'bg-warning' : 'bg-danger')} stock-badge">
                        ${product.quantity}
                    </span>
                    ${product.quantity <= 0 ? '<div class="out-of-stock">' + '{{ __("messages.out_of_stock") }}' + '</div>' : ''}
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1 small text-truncate" title="${product.name}">${product.name}</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">${product.code}</small>
                            <strong class="text-primary ${product.price_range ? 'price-range' : ''}">${priceDisplay}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `}).join('');
    }

    // Show variants modal for variable products
    window.showVariants = function(element) {
        const product = JSON.parse(element.dataset.product);
        currentVariantProduct = product;

        // Update modal header
        document.getElementById('variantProductName').textContent = product.name;
        document.getElementById('variantProductCode').textContent = product.code;

        // Update product image
        const imageContainer = document.getElementById('variantProductImage');
        if (product.image) {
            imageContainer.innerHTML = `<img src="${product.image}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">`;
        } else {
            imageContainer.innerHTML = `<i class="ti ti-package text-white" style="font-size: 1.5rem;"></i>`;
        }

        // Render variants list
        const variantsList = document.getElementById('variantsList');
        variantsList.innerHTML = product.variants.map(variant => `
            <div class="variant-item p-3 rounded ${variant.quantity <= 0 ? 'out-of-stock-variant' : ''}"
                 ${variant.quantity > 0 ? `onclick="selectVariant(${JSON.stringify(variant).replace(/"/g, '&quot;')})"` : ''}>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">${variant.name}</h6>
                        <small class="text-muted">${variant.code}</small>
                    </div>
                    <div class="text-end">
                        <strong class="text-primary d-block">${parseFloat(variant.price).toFixed(2)} {{ __('messages.currency') }}</strong>
                        <span class="badge ${variant.quantity > 5 ? 'bg-success' : (variant.quantity > 0 ? 'bg-warning' : 'bg-danger')}">
                            ${variant.quantity} {{ __('messages.in_stock') }}
                        </span>
                    </div>
                </div>
            </div>
        `).join('');

        variantsModal.show();
    };

    // Select variant and add to cart
    window.selectVariant = function(variant) {
        const productToAdd = {
            id: variant.id,
            variant_id: variant.variant_id,
            code: variant.code,
            name: variant.full_name,
            price: variant.price,
            quantity: variant.quantity,
            type: 'variable'
        };

        if (addToCartByProduct(productToAdd)) {
            variantsModal.hide();
        }
    };

    // Add to cart by product data
    function addToCartByProduct(product) {
        if (product.quantity <= 0) {
            return false;
        }

        const existingItem = cart.find(item =>
            item.product_id === product.id && item.variant_id === product.variant_id
        );

        if (existingItem) {
            if (existingItem.quantity < product.quantity) {
                existingItem.quantity++;
            } else {
                alert('{{ __("messages.max_quantity_reached") }}');
                return false;
            }
        } else {
            cart.push({
                product_id: product.id,
                variant_id: product.variant_id,
                code: product.code,
                name: product.name,
                price: parseFloat(product.price),
                quantity: 1,
                max_quantity: product.quantity,
                discount: 0
            });
        }

        renderCart();
        return true;
    }

    // Add to cart function (global) - for click
    window.addToCart = function(element) {
        const product = JSON.parse(element.dataset.product);
        addToCartByProduct(product);
    };

    // Search with Enter key for barcode scanning
    productSearch.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const searchValue = this.value.trim();

            if (searchValue) {
                // First, search for exact match in variant codes
                let variantMatch = null;
                let parentProduct = null;
                for (const product of products) {
                    if (product.type === 'variable' && product.variants) {
                        const foundVariant = product.variants.find(v => v.code === searchValue);
                        if (foundVariant) {
                            variantMatch = foundVariant;
                            parentProduct = product;
                            break;
                        }
                    }
                }

                if (variantMatch) {
                    // Found exact variant match by barcode
                    const productToAdd = {
                        id: variantMatch.id,
                        variant_id: variantMatch.variant_id,
                        code: variantMatch.code,
                        name: variantMatch.full_name,
                        price: variantMatch.price,
                        quantity: variantMatch.quantity,
                        type: 'variable'
                    };
                    if (addToCartByProduct(productToAdd)) {
                        this.value = '';
                        renderProducts(products);
                    }
                } else {
                    // Find exact match by product code
                    const exactMatch = products.find(p => p.code === searchValue);

                    if (exactMatch) {
                        if (exactMatch.type === 'variable' && exactMatch.variants && exactMatch.variants.length > 0) {
                            // Variable product - show variants modal
                            showVariantsForProduct(exactMatch);
                            this.value = '';
                        } else if (addToCartByProduct(exactMatch)) {
                            this.value = '';
                            renderProducts(products);
                        }
                    } else {
                        // Check filtered results (including variants)
                        const filtered = products.filter(p => {
                            if (p.name.toLowerCase().includes(searchValue.toLowerCase()) ||
                                p.code.toLowerCase().includes(searchValue.toLowerCase())) {
                                return true;
                            }
                            // Also search in variants
                            if (p.variants && p.variants.length > 0) {
                                return p.variants.some(v =>
                                    v.name.toLowerCase().includes(searchValue.toLowerCase()) ||
                                    v.code.toLowerCase().includes(searchValue.toLowerCase())
                                );
                            }
                            return false;
                        });

                        if (filtered.length === 1) {
                            const product = filtered[0];
                            if (product.type === 'variable' && product.variants && product.variants.length > 0) {
                                showVariantsForProduct(product);
                                this.value = '';
                            } else if (addToCartByProduct(product)) {
                                this.value = '';
                                renderProducts(products);
                            }
                        } else if (filtered.length > 1) {
                            renderProducts(filtered);
                        } else {
                            noProducts.classList.remove('d-none');
                        }
                    }
                }
            }

            focusSearch();
        }
    });

    // Helper function to show variants modal
    function showVariantsForProduct(product) {
        currentVariantProduct = product;

        document.getElementById('variantProductName').textContent = product.name;
        document.getElementById('variantProductCode').textContent = product.code;

        const imageContainer = document.getElementById('variantProductImage');
        if (product.image) {
            imageContainer.innerHTML = `<img src="${product.image}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">`;
        } else {
            imageContainer.innerHTML = `<i class="ti ti-package text-white" style="font-size: 1.5rem;"></i>`;
        }

        const variantsList = document.getElementById('variantsList');
        variantsList.innerHTML = product.variants.map(variant => `
            <div class="variant-item p-3 rounded ${variant.quantity <= 0 ? 'out-of-stock-variant' : ''}"
                 ${variant.quantity > 0 ? `onclick="selectVariant(${JSON.stringify(variant).replace(/"/g, '&quot;')})"` : ''}>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">${variant.name}</h6>
                        <small class="text-muted">${variant.code}</small>
                    </div>
                    <div class="text-end">
                        <strong class="text-primary d-block">${parseFloat(variant.price).toFixed(2)} {{ __('messages.currency') }}</strong>
                        <span class="badge ${variant.quantity > 5 ? 'bg-success' : (variant.quantity > 0 ? 'bg-warning' : 'bg-danger')}">
                            ${variant.quantity} {{ __('messages.in_stock') }}
                        </span>
                    </div>
                </div>
            </div>
        `).join('');

        variantsModal.show();
    }

    // Live search filter
    productSearch.addEventListener('input', function() {
        const search = this.value.toLowerCase().trim();

        if (search === '') {
            renderProducts(products);
            noProducts.classList.add('d-none');
        } else {
            const filtered = products.filter(p => {
                // Search in product name and code
                if (p.name.toLowerCase().includes(search) ||
                    p.code.toLowerCase().includes(search)) {
                    return true;
                }
                // Also search in variants
                if (p.variants && p.variants.length > 0) {
                    return p.variants.some(v =>
                        v.name.toLowerCase().includes(search) ||
                        v.code.toLowerCase().includes(search)
                    );
                }
                return false;
            });
            renderProducts(filtered);
        }
    });

    // Render cart
    function renderCart() {
        if (cart.length === 0) {
            emptyCart.style.display = 'block';
            cartItems.innerHTML = '';
            cartItems.appendChild(emptyCart);
            payBtn.disabled = true;
        } else {
            emptyCart.style.display = 'none';
            cartItems.innerHTML = cart.map((item, index) => `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 small">${item.name}</h6>
                            <small class="text-muted">${item.code}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="quantity-controls">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, -1)">
                                <i class="ti ti-minus"></i>
                            </button>
                            <input type="number" value="${item.quantity}" min="1" max="${item.max_quantity}"
                                   onchange="setQuantity(${index}, this.value)" class="form-control form-control-sm">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, 1)">
                                <i class="ti ti-plus"></i>
                            </button>
                        </div>
                        <div class="text-end">
                            <strong>${(item.price * item.quantity).toFixed(2)}</strong>
                            <small class="text-muted d-block">${item.price.toFixed(2)} x ${item.quantity}</small>
                        </div>
                    </div>
                </div>
            `).join('');
            payBtn.disabled = false;
        }

        updateTotals();
    }

    // Update quantity functions
    window.updateQuantity = function(index, delta) {
        const newQty = cart[index].quantity + delta;
        if (newQty >= 1 && newQty <= cart[index].max_quantity) {
            cart[index].quantity = newQty;
            renderCart();
        }
        focusSearch();
    };

    window.setQuantity = function(index, value) {
        const qty = parseInt(value);
        if (qty >= 1 && qty <= cart[index].max_quantity) {
            cart[index].quantity = qty;
            renderCart();
        }
        focusSearch();
    };

    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        renderCart();
        focusSearch();
    };

    // Update totals
    function updateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discountInput = parseFloat(document.getElementById('discountInput').value) || 0;
        const discountType = document.getElementById('discountType').value;

        let discount = 0;
        if (discountType === 'percentage') {
            discount = (subtotal * discountInput) / 100;
        } else {
            discount = discountInput;
        }

        // Recalculate coupon discount if applied
        if (appliedCoupon) {
            const afterManualDiscount = subtotal - discount;
            validateAndUpdateCoupon(afterManualDiscount);
        }

        const total = Math.max(0, subtotal - discount - couponDiscount);

        document.getElementById('cartSubtotal').textContent = subtotal.toFixed(2);
        document.getElementById('cartTotal').innerHTML = total.toFixed(2) + ' <small>{{ __("messages.currency") }}</small>';
        document.getElementById('paidAmount').value = total.toFixed(2);

        // Update coupon discount display
        if (couponDiscount > 0) {
            document.getElementById('couponDiscountRow').classList.remove('d-none');
            document.getElementById('couponDiscountAmount').textContent = '-' + couponDiscount.toFixed(2);
        } else {
            document.getElementById('couponDiscountRow').classList.add('d-none');
        }
    }

    // Validate and update coupon discount
    async function validateAndUpdateCoupon(orderTotal) {
        if (!appliedCoupon) return;

        const customerId = document.getElementById('customerSelect').value;

        try {
            const response = await fetch('{{ route("coupons.validate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: appliedCoupon.code,
                    customer_id: customerId,
                    order_total: orderTotal
                })
            });

            const data = await response.json();

            if (data.success) {
                couponDiscount = data.discount;
            } else {
                // Coupon no longer valid
                removeCoupon();
                showCouponMessage(data.message, 'danger');
            }
        } catch (error) {
            console.error('Error validating coupon:', error);
        }
    }

    // Coupon functions
    function showCouponMessage(message, type) {
        const msgEl = document.getElementById('couponMessage');
        msgEl.textContent = message;
        msgEl.className = `small mt-1 text-${type}`;
        msgEl.classList.remove('d-none');
        setTimeout(() => msgEl.classList.add('d-none'), 5000);
    }

    function removeCoupon() {
        appliedCoupon = null;
        couponDiscount = 0;
        document.getElementById('couponCode').value = '';
        document.getElementById('appliedCoupon').classList.add('d-none');
        document.getElementById('couponDiscountRow').classList.add('d-none');
        updateTotals();
    }

    async function applyCoupon() {
        const code = document.getElementById('couponCode').value.trim().toUpperCase();
        if (!code) return;

        if (cart.length === 0) {
            showCouponMessage('{{ __("messages.cart_empty") }}', 'warning');
            return;
        }

        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discountInput = parseFloat(document.getElementById('discountInput').value) || 0;
        const discountType = document.getElementById('discountType').value;
        let manualDiscount = discountType === 'percentage' ? (subtotal * discountInput) / 100 : discountInput;
        const orderTotal = subtotal - manualDiscount;

        const customerId = document.getElementById('customerSelect').value;
        const applyBtn = document.getElementById('applyCouponBtn');

        applyBtn.disabled = true;
        applyBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const response = await fetch('{{ route("coupons.validate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    customer_id: customerId,
                    order_total: orderTotal
                })
            });

            const data = await response.json();

            if (data.success) {
                appliedCoupon = data.coupon;
                couponDiscount = data.discount;

                document.getElementById('appliedCouponCode').textContent = data.coupon.code;
                document.getElementById('appliedCouponDiscount').textContent = data.coupon.discount_text;
                document.getElementById('appliedCoupon').classList.remove('d-none');
                document.getElementById('couponCode').value = '';

                showCouponMessage(data.message, 'success');
                updateTotals();
            } else {
                showCouponMessage(data.message, 'danger');
            }
        } catch (error) {
            console.error('Error applying coupon:', error);
            showCouponMessage('{{ __("messages.error_occurred") }}', 'danger');
        } finally {
            applyBtn.disabled = false;
            applyBtn.innerHTML = '<i class="ti ti-discount-2"></i>';
        }
    }

    // Coupon event listeners
    document.getElementById('applyCouponBtn').addEventListener('click', applyCoupon);
    document.getElementById('couponCode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyCoupon();
        }
    });
    document.getElementById('removeCouponBtn').addEventListener('click', removeCoupon);

    // Event listeners
    document.getElementById('discountInput').addEventListener('input', updateTotals);
    document.getElementById('discountType').addEventListener('change', updateTotals);

    document.getElementById('refreshProducts').addEventListener('click', function() {
        loadProducts();
    });

    // Clear cart button (optional)
    const clearCartBtn = document.getElementById('clearCart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function() {
            if (cart.length > 0 && confirm('{{ __("messages.confirm_clear_cart") }}')) {
                cart = [];
                removeCoupon();
                renderCart();
            }
            focusSearch();
        });
    }

    // Check if customer is default (walk-in) and handle credit restriction
    function updatePaymentMethodVisibility() {
        const customerSelect = document.getElementById('customerSelect');
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        const isDefault = selectedOption.dataset.isDefault === '1';
        const creditRadio = document.getElementById('payCredit');
        const creditLabel = document.querySelector('label[for="payCredit"]');
        const cashRadio = document.getElementById('payCash');

        if (isDefault) {
            // Disable credit for default customer
            creditRadio.disabled = true;
            creditLabel.classList.add('disabled');
            creditLabel.style.opacity = '0.5';
            creditLabel.style.cursor = 'not-allowed';
            creditLabel.title = '{{ __("messages.credit_not_allowed_for_default_customer") }}';
            // Force cash selection
            cashRadio.checked = true;
            document.getElementById('cashboxSection').style.display = 'block';
        } else {
            // Enable credit for regular customers
            creditRadio.disabled = false;
            creditLabel.classList.remove('disabled');
            creditLabel.style.opacity = '1';
            creditLabel.style.cursor = 'pointer';
            creditLabel.title = '';
        }
    }

    // Customer selection change
    document.getElementById('customerSelect').addEventListener('change', updatePaymentMethodVisibility);

    // Initial check
    updatePaymentMethodVisibility();

    // Payment method toggle (cash or credit)
    document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const paymentTypeSection = document.getElementById('paymentTypeSection');
            const cashboxSection = document.getElementById('cashboxSection');
            const bankAccountSection = document.getElementById('bankAccountSection');

            if (this.value === 'cash') {
                // Show payment type selection (cash or bank transfer)
                paymentTypeSection.style.display = 'block';
                cashboxSection.style.display = 'block';
                // Reset bank account visibility based on payment type
                const paymentType = document.querySelector('input[name="paymentType"]:checked').value;
                bankAccountSection.style.display = paymentType === 'bank_transfer' ? 'block' : 'none';
            } else {
                // Credit - hide payment type and cashbox
                paymentTypeSection.style.display = 'none';
                cashboxSection.style.display = 'none';
                bankAccountSection.style.display = 'none';
            }
        });
    });

    // Payment type toggle (cash or bank_transfer) - only shown when payment method is cash
    document.querySelectorAll('input[name="paymentType"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const bankAccountSection = document.getElementById('bankAccountSection');
            bankAccountSection.style.display = this.value === 'bank_transfer' ? 'block' : 'none';
        });
    });

    // Pay button
    payBtn.addEventListener('click', async function() {
        if (cart.length === 0) return;

        const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
        const paymentType = paymentMethod === 'cash' ? document.querySelector('input[name="paymentType"]:checked').value : null;
        const bankAccount = paymentType === 'bank_transfer' ? document.getElementById('bankAccount').value : null;
        const customerId = document.getElementById('customerSelect').value;
        const cashboxId = document.getElementById('cashboxSelect').value;
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const discountType = document.getElementById('discountType').value;
        const paidAmount = parseFloat(document.getElementById('paidAmount').value) || 0;

        // Validate bank account if payment type is bank_transfer
        if (paymentType === 'bank_transfer' && !bankAccount) {
            alert('{{ __("messages.please_enter_bank_account") }}');
            return;
        }

        payBtn.disabled = true;
        payBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("messages.processing") }}...';

        try {
            const response = await fetch('{{ route('pos.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    customer_id: customerId,
                    cashbox_id: paymentMethod === 'cash' ? cashboxId : null,
                    payment_method: paymentMethod,
                    payment_type: paymentType,
                    bank_account: bankAccount,
                    discount: discount,
                    discount_type: discountType,
                    paid_amount: paidAmount,
                    coupon_id: appliedCoupon ? appliedCoupon.id : null,
                    coupon_discount: couponDiscount,
                    items: cart.map(item => ({
                        product_id: item.product_id,
                        variant_id: item.variant_id,
                        quantity: item.quantity,
                        price: item.price,
                        discount: item.discount
                    }))
                })
            });

            const data = await response.json();

            if (data.success) {
                currentSaleId = data.sale.id;
                document.getElementById('invoiceNumber').textContent = data.invoice_number;
                successModal.show();
                cart = [];
                removeCoupon();
                renderCart();
                loadProducts(); // Refresh stock
            } else {
                alert(data.message || '{{ __("messages.error_occurred") }}');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('{{ __("messages.error_occurred") }}');
        } finally {
            payBtn.disabled = false;
            payBtn.innerHTML = '<i class="ti ti-check me-2"></i>{{ __("messages.complete_sale") }}';
        }
    });

    // Print receipt (80mm)
    document.getElementById('printReceiptBtn').addEventListener('click', function() {
        window.location.href = '/dlango/public/pos/receipt';
    });

    // Print A4 invoice
    document.getElementById('printInvoiceA4Btn').addEventListener('click', function() {
        if (currentSaleId) {
            window.open(`/dlango/public/pos/${currentSaleId}/invoice`, '_blank');
        }
    });

    // New sale button - refocus search
    document.getElementById('newSaleBtn').addEventListener('click', function() {
        document.getElementById('discountInput').value = 0;
        document.getElementById('paidAmount').value = 0;
        setTimeout(focusSearch, 100);
    });

    // Refocus after modal closes
    document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
        focusSearch();
    });

    // Add Customer Modal
    const addCustomerModal = new bootstrap.Modal(document.getElementById('addCustomerModal'));

    document.getElementById('addCustomerBtn').addEventListener('click', function() {
        document.getElementById('newCustomerName').value = '';
        document.getElementById('newCustomerPhone').value = '';
        addCustomerModal.show();
    });

    document.getElementById('saveCustomerBtn').addEventListener('click', async function() {
        const name = document.getElementById('newCustomerName').value.trim();
        const phone = document.getElementById('newCustomerPhone').value.trim();

        if (!name) {
            alert('{{ __("messages.name_required") }}');
            return;
        }

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("messages.saving") }}...';

        try {
            const response = await fetch('{{ route("pos.customer.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name, phone })
            });

            const data = await response.json();

            if (data.success) {
                // Add new customer to select
                const customerSelect = document.getElementById('customerSelect');
                const option = document.createElement('option');
                option.value = data.customer.id;
                option.dataset.isDefault = '0';
                option.textContent = data.customer.name;
                option.selected = true;
                customerSelect.appendChild(option);

                // Update payment method visibility
                updatePaymentMethodVisibility();

                addCustomerModal.hide();
                focusSearch();
            } else {
                alert(data.message || '{{ __("messages.error_occurred") }}');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('{{ __("messages.error_occurred") }}');
        } finally {
            this.disabled = false;
            this.innerHTML = '<i class="ti ti-check me-1"></i>{{ __("messages.save") }}';
        }
    });

    // Refocus after add customer modal closes
    document.getElementById('addCustomerModal').addEventListener('hidden.bs.modal', function() {
        focusSearch();
    });

    // Initial load
    loadProducts();
});
</script>
@endpush
@endsection
