
<style>
    .vendor-sidebar {
        background: #fff;
        border: 1px solid #e8eaff;
        border-radius: 20px;
        padding: 20px 14px;
        box-shadow: 0 12px 35px rgba(35, 30, 90, .06);
        position: sticky;
        top: 20px;
    }

    .vendor-sidebar-brand {
        padding: 8px 12px 20px;
        border-bottom: 1px solid #edf0fa;
        margin-bottom: 16px;
    }

    .vendor-sidebar-brand h5 {
        font-weight: 850;
        margin: 0;
        color: #111329;
    }

    .vendor-sidebar-brand small {
        color: #8589a8;
        font-size: 11px;
        letter-spacing: 1px;
        font-weight: 700;
    }

    .vendor-sidebar-user {
        background: #f4f2ff;
        border-radius: 13px;
        padding: 12px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .vendor-avatar {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #7657ff, #a855f7);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 17px;
    }

    .vendor-user-info {
        min-width: 0;
    }

    .vendor-user-info strong,
    .vendor-user-info small {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .vendor-user-info strong {
        font-size: 13px;
    }

    .vendor-user-info small {
        font-size: 11px;
        color: #8589a8;
    }

    .vendor-nav-label {
        color: #9a9db2;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        padding: 12px 12px 7px;
        text-transform: uppercase;
    }

    .vendor-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 12px;
        margin: 3px 0;
        border-radius: 11px;
        text-decoration: none;
        color: #626680;
        font-size: 13px;
        font-weight: 650;
        transition: .2s ease;
    }

    .vendor-nav-link i {
        width: 19px;
        text-align: center;
        font-size: 15px;
    }

    .vendor-nav-link:hover {
        background: #f3f1ff;
        color: #7657ff;
        transform: translateX(3px);
    }

    .vendor-nav-link.active {
        background: linear-gradient(110deg, #7657ff, #a855f7);
        color: white;
        box-shadow: 0 7px 17px rgba(118, 87, 255, .20);
    }

    .vendor-sidebar-logout {
        border-top: 1px solid #edf0fa;
        margin-top: 16px;
        padding-top: 12px;
    }

    .vendor-sidebar-logout button {
        width: 100%;
        border: 0;
        background: #fff1f1;
        color: #d63939;
        border-radius: 11px;
        padding: 11px 12px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
    }

    .vendor-sidebar-logout button:hover {
        background: #ffe1e1;
    }

    @media(max-width: 991px) {
        .vendor-sidebar {
            position: static;
        }
    }
</style>

<aside class="vendor-sidebar">

    <div class="vendor-sidebar-brand">
        <h5>ESS <span class="gradient-text">SELLER</span></h5>
        <small>VENDOR WORKSPACE</small>
    </div>

    <div class="vendor-sidebar-user">
        <div class="vendor-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>

        <div class="vendor-user-info">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->email }}</small>
        </div>
    </div>

    <div class="vendor-nav-label">Workspace</div>

    <a href="{{ route('vendor.dashboard') }}"
       class="vendor-nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
        <i class="fas fa-chart-pie"></i>
        Dashboard
    </a>

    <a href="{{ route('vendor.profile.edit') }}"
   class="vendor-nav-link {{ request()->routeIs('vendor.profile.*') ? 'active' : '' }}">
    <i class="fa-solid fa-store"></i>
    <span>Profile</span>
</a>

    <div class="vendor-nav-label">Product Management</div>

    <a href="{{ route('vendor.products.index') }}"
       class="vendor-nav-link {{ request()->routeIs('vendor.products.*') ? 'active' : '' }}">
        <i class="fas fa-box-open"></i>
        Manage Products
    </a>

    <a href="{{ route('vendor.products.create') }}"
       class="vendor-nav-link {{ request()->routeIs('vendor.products.create') ? 'active' : '' }}">
        <i class="fas fa-plus-circle"></i>
        Add New Product
    </a>

    <div class="vendor-nav-label">Order Management</div>

    <a href="{{ route('vendor.orders.index') }}"
       class="vendor-nav-link {{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}">
        <i class="fas fa-shopping-bag"></i>
        Manage Orders
    </a>

    <div class="vendor-sidebar-logout">
        <form method="POST" action="{{ route('vendor.logout') }}">
            @csrf
            <button type="submit">
                <i class="fas fa-right-from-bracket me-2"></i>
                Logout
            </button>
        </form>
    </div>

</aside>
