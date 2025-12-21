{{-- Dashboard - Always visible to authenticated users --}}
<li class="pc-item {{ request()->routeIs('home') ? 'active' : '' }}">
    <a href="{{ route('home') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-dashboard"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.dashboard') }}</span>
    </a>
</li>

{{-- 1. Sales Section --}}
@if(auth()->user()->hasAnyPermission(['sales.pos', 'sales.view', 'returns.view', 'customers.view']))
<li class="pc-item pc-hasmenu {{ request()->routeIs('pos.*') || request()->routeIs('returns.*') || request()->routeIs('customers.*') ? 'pc-trigger active' : '' }}">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-shopping-cart"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.sales') }}</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        @if(auth()->user()->hasPermission('sales.pos'))
        <li class="pc-item {{ request()->routeIs('pos.index') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('pos.index') }}">{{ __('messages.pos') }}</a>
        </li>
        @endif
        @if(auth()->user()->hasPermission('sales.view'))
        <li class="pc-item {{ request()->routeIs('pos.history') || request()->routeIs('pos.show') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('pos.history') }}">{{ __('messages.sales_history') }}</a>
        </li>
        @endif
        @if(auth()->user()->hasPermission('returns.view'))
        <li class="pc-item {{ request()->routeIs('returns.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('returns.index') }}">{{ __('messages.sales_returns') }}</a>
        </li>
        @endif
        @if(auth()->user()->hasPermission('customers.view'))
        <li class="pc-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('customers.index') }}">{{ __('messages.customers') }}</a>
        </li>
        @endif
    </ul>
</li>
@endif

{{-- 2. Purchases Section --}}
@if(auth()->user()->hasAnyPermission(['purchases.view', 'suppliers.view']))
<li class="pc-item pc-hasmenu {{ request()->routeIs('purchase-invoices.*') || request()->routeIs('suppliers.*') ? 'pc-trigger active' : '' }}">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-truck-delivery"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.purchases') }}</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        @if(auth()->user()->hasPermission('purchases.view'))
        <li class="pc-item {{ request()->routeIs('purchase-invoices.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('purchase-invoices.index') }}">{{ __('messages.purchase_invoices') }}</a>
        </li>
        @endif
        @if(auth()->user()->hasPermission('suppliers.view'))
        <li class="pc-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('suppliers.index') }}">{{ __('messages.suppliers') }}</a>
        </li>
        @endif
    </ul>
</li>
@endif

{{-- 3. Inventory Section --}}
@if(auth()->user()->hasPermission('inventory.view'))
<li class="pc-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
    <a href="{{ route('inventory.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-box"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.inventory') }}</span>
    </a>
</li>
@endif

{{-- 4. Financial Management Section --}}
@if(auth()->user()->hasAnyPermission(['finance.cashboxes', 'finance.transactions', 'finance.statement', 'reports.view', 'coupons.view']))
<li class="pc-item pc-hasmenu {{ request()->routeIs('cashboxes.*') || request()->routeIs('transactions.*') || request()->routeIs('reports.*') || request()->routeIs('coupons.*') ? 'pc-trigger active' : '' }}">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-wallet"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.financial_management') }}</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        @if(auth()->user()->hasPermission('finance.cashboxes'))
        <li class="pc-item {{ request()->routeIs('cashboxes.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('cashboxes.index') }}">{{ __('messages.cashboxes') }}</a>
        </li>
        @endif
        @if(auth()->user()->hasPermission('finance.transactions'))
        <li class="pc-item {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.create') || request()->routeIs('transactions.edit') || request()->routeIs('transactions.show') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('transactions.index') }}">{{ __('messages.transactions') }}</a>
        </li>
        @endif
        @if(auth()->user()->hasPermission('finance.statement'))
        <li class="pc-item {{ request()->routeIs('transactions.statement') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('transactions.statement') }}">{{ __('messages.account_statement') }}</a>
        </li>
        @endif
        @if(auth()->user()->hasPermission('reports.view'))
        <li class="pc-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('reports.index') }}">{{ __('messages.statistics') }}</a>
        </li>
        @endif
        @if(auth()->user()->hasPermission('coupons.view'))
        <li class="pc-item {{ request()->routeIs('coupons.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('coupons.index') }}">{{ __('messages.coupons') }}</a>
        </li>
        @endif
    </ul>
</li>
@endif

{{-- 5. Users Section (Admin Only) --}}
@if(auth()->user()->isAdmin())
<li class="pc-item pc-hasmenu {{ request()->routeIs('users.*') || request()->routeIs('profile.*') ? 'pc-trigger active' : '' }}">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-users"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.users') }}</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('users.index') }}">{{ __('messages.user_management') }}</a>
        </li>
        <li class="pc-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('profile.edit') }}">{{ __('messages.my_profile') }}</a>
        </li>
    </ul>
</li>
@else
{{-- Non-admin users only see their profile --}}
<li class="pc-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
    <a href="{{ route('profile.edit') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-user"></i>
        </span>
        <span class="pc-mtext">{{ __('messages.my_profile') }}</span>
    </a>
</li>
@endif