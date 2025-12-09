{{-- Dashboard - Always visible to authenticated users --}}
<li class="pc-item {{ request()->routeIs('home') ? 'active' : '' }}">
    <a href="{{ route('home') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-dashboard"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.dashboard') }}</span>
    </a>
</li>

{{-- Sales Management Section (Most Used) --}}
@if(auth()->user()->hasAnyPermission(['sales.pos', 'sales.view', 'returns.view']))
<li class="pc-item pc-caption">
    <label>{{ __('messages.sales') }}</label>
</li>

@if(auth()->user()->hasPermission('sales.pos'))
<li class="pc-item {{ request()->routeIs('pos.index') ? 'active' : '' }}">
    <a href="{{ route('pos.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-device-desktop"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.pos') }}</span>
    </a>
</li>
@endif

@if(auth()->user()->hasPermission('sales.view'))
<li class="pc-item {{ request()->routeIs('pos.history') || request()->routeIs('pos.show') ? 'active' : '' }}">
    <a href="{{ route('pos.history') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-receipt"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.sales_history') }}</span>
    </a>
</li>
@endif

@if(auth()->user()->hasPermission('returns.view'))
<li class="pc-item {{ request()->routeIs('returns.*') ? 'active' : '' }}">
    <a href="{{ route('returns.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-receipt-refund"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.sales_returns') }}</span>
    </a>
</li>
@endif
@endif

{{-- Inventory Management Section --}}
@if(auth()->user()->hasPermission('inventory.view'))
<li class="pc-item pc-caption">
    <label>{{ __('messages.inventory') }}</label>
</li>

<li class="pc-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
    <a href="{{ route('inventory.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-box"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.products') }}</span>
    </a>
</li>
@endif

{{-- Purchases Management Section --}}
@if(auth()->user()->hasAnyPermission(['purchases.view', 'suppliers.view']))
<li class="pc-item pc-caption">
    <label>{{ __('messages.purchase_management') }}</label>
</li>

@if(auth()->user()->hasPermission('purchases.view'))
<li class="pc-item {{ request()->routeIs('purchase-invoices.*') ? 'active' : '' }}">
    <a href="{{ route('purchase-invoices.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-file-invoice"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.purchase_invoices') }}</span>
    </a>
</li>
@endif

@if(auth()->user()->hasPermission('suppliers.view'))
<li class="pc-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
    <a href="{{ route('suppliers.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-truck-delivery"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.suppliers') }}</span>
    </a>
</li>
@endif
@endif

{{-- Customers Section --}}
@if(auth()->user()->hasPermission('customers.view'))
<li class="pc-item pc-caption">
    <label>{{ __('messages.customers') }}</label>
</li>

<li class="pc-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
    <a href="{{ route('customers.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-user"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.customers') }}</span>
    </a>
</li>
@endif

{{-- Financial Management Section --}}
@if(auth()->user()->hasAnyPermission(['finance.cashboxes', 'finance.transactions', 'finance.categories', 'finance.statement']))
<li class="pc-item pc-caption">
    <label>{{ __('messages.financial_management') }}</label>
</li>

@if(auth()->user()->hasPermission('finance.cashboxes'))
<li class="pc-item {{ request()->routeIs('cashboxes.*') ? 'active' : '' }}">
    <a href="{{ route('cashboxes.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-box"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.cashboxes') }}</span>
    </a>
</li>
@endif

@if(auth()->user()->hasPermission('finance.transactions'))
<li class="pc-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
    <a href="{{ route('transactions.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-arrows-right-left"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.transactions') }}</span>
    </a>
</li>
@endif

@if(auth()->user()->hasPermission('finance.categories'))
<li class="pc-item {{ request()->routeIs('transaction-categories.*') ? 'active' : '' }}">
    <a href="{{ route('transaction-categories.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-tag"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.transaction_categories') }}</span>
    </a>
</li>
@endif

@if(auth()->user()->hasPermission('finance.statement'))
<li class="pc-item {{ request()->routeIs('transactions.statement') ? 'active' : '' }}">
    <a href="{{ route('transactions.statement') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-file-text"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.account_statement') }}</span>
    </a>
</li>
@endif
@endif

{{-- Reports Section --}}
@if(auth()->user()->hasPermission('reports.view'))
<li class="pc-item pc-caption">
    <label>{{ __('messages.reports') }}</label>
</li>

<li class="pc-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
    <a href="{{ route('reports.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-chart-bar"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.statistics') }}</span>
    </a>
</li>
@endif

{{-- Coupons Section --}}
@if(auth()->user()->hasPermission('coupons.view'))
<li class="pc-item pc-caption">
    <label>{{ __('messages.marketing') }}</label>
</li>

<li class="pc-item {{ request()->routeIs('coupons.*') ? 'active' : '' }}">
    <a href="{{ route('coupons.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-discount-2"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.coupons') }}</span>
    </a>
</li>
@endif

{{-- User Management Section (Admin Only) --}}
@if(auth()->user()->isAdmin())
<li class="pc-item pc-caption">
    <label>{{ __('messages.user_management') }}</label>
</li>

<li class="pc-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
    <a href="{{ route('users.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-users"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.users') }}</span>
    </a>
</li>
@endif

{{-- Settings Section - Always visible --}}
<li class="pc-item pc-caption">
    <label>{{ __('messages.settings') }}</label>
</li>

<li class="pc-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
    <a href="{{ route('profile.edit') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-user"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.my_profile') }}</span>
    </a>
</li>
