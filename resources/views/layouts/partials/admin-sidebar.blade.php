
<aside id="adminSidebar" class="admin-sidebar">

    {{-- BRAND --}}
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fa-solid fa-store"></i>
        </div>
        <span class="sidebar-label fw-bold">ESS MARKETPLACE</span>
    </div>

    {{-- MAIN MENU --}}
    <div class="sidebar-section">MAIN MENU</div>

    <a href="{{ route('admin.dashboard') }}"
       class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i>
        <span class="sidebar-label">Dashboard</span>
    </a>

    <a href="{{ route('admin.users.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users"></i>
        <span class="sidebar-label">Users</span>
    </a>

    <a href="{{ route('admin.vendors.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i>
        <span class="sidebar-label">Vendors</span>
    </a>

    <a href="{{ route('admin.products.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i class="fa-solid fa-box-open"></i>
        <span class="sidebar-label">Products</span>
    </a>

    <a href="{{ route('admin.orders.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <i class="fa-solid fa-shopping-bag"></i>
        <span class="sidebar-label">Orders</span>
    </a>

    <a href="{{ route('admin.payments.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
        <i class="fa-solid fa-credit-card"></i>
        <span class="sidebar-label">Payments</span>
    </a>

    {{-- PROMOTIONS --}}
    <div class="sidebar-section mt-4">PROMOTIONS</div>

    <a href="{{ route('admin.coupons.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
        <i class="fa-solid fa-ticket"></i>
        <span class="sidebar-label">Coupons</span>
    </a>

    <a href="{{ route('admin.offers.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}">
        <i class="fa-solid fa-tags"></i>
        <span class="sidebar-label">Offers</span>
    </a>

    {{-- MANAGEMENT --}}
    <div class="sidebar-section mt-4">MANAGEMENT</div>

    <a href="{{ route('admin.dashboard') }}"
       class="sidebar-link">
        <i class="fa-solid fa-chart-line"></i>
        <span class="sidebar-label">Reports</span>
    </a>

    <a href="{{ route('admin.dashboard') }}"
       class="sidebar-link">
        <i class="fa-solid fa-gear"></i>
        <span class="sidebar-label">Settings</span>
    </a>

    {{-- FOOTER --}}
    <div class="sidebar-footer">
        <div class="sidebar-label fw-semibold">ESS MARKETPLACE</div>
        <small class="sidebar-label">Admin Panel v1.0</small>

        <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="sidebar-link sidebar-logout w-100 border-0">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="sidebar-label">Logout</span>
            </button>
        </form>
    </div>

</aside>

<div id="sidebarOverlay" class="sidebar-overlay"></div>
