
<style>
    .ess-account-sidebar {
        background: #fff;
        border: 1px solid #e6ecf7;
        border-radius: 18px;
        padding: 20px 14px;
        box-shadow: 0 5px 20px rgba(20, 50, 100, .04);
        position: sticky;
        top: 100px;
    }

    .ess-sidebar-user {
        padding: 12px;
        margin-bottom: 16px;
        border-radius: 13px;
        background: linear-gradient(135deg, #eaf1ff, #f5f0ff);
    }

    .ess-sidebar-avatar {
        width: 43px;
        height: 43px;
        border-radius: 50%;
        background: #0645e8;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .ess-sidebar-heading {
        color: #8a94a8;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 12px 12px 7px;
    }

    .ess-sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 12px;
        border-radius: 10px;
        color: #53617a;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 4px;
        transition: .2s ease;
    }

    .ess-sidebar-link i {
        width: 19px;
        text-align: center;
        font-size: 15px;
    }

    .ess-sidebar-link:hover {
        background: #edf3ff;
        color: #0645e8;
    }

    .ess-sidebar-link.active {
        background: #0645e8;
        color: #fff;
        box-shadow: 0 5px 12px rgba(6,69,232,.18);
    }

    .ess-sidebar-logout {
        border: 0;
        width: 100%;
        background: transparent;
        text-align: left;
        cursor: pointer;
    }

    @media(max-width: 991px) {
        .ess-account-sidebar {
            position: static;
            padding: 13px;
        }

        .ess-sidebar-links {
            display: flex;
            overflow-x: auto;
            gap: 6px;
            padding-bottom: 5px;
        }

        .ess-sidebar-link {
            flex: 0 0 auto;
            margin: 0;
            white-space: nowrap;
        }

        .ess-sidebar-heading {
            display: none;
        }

        .ess-sidebar-user {
            margin-bottom: 10px;
        }
    }
</style>

<aside class="ess-account-sidebar">

    <div class="ess-sidebar-user d-flex align-items-center gap-3">
        <div class="ess-sidebar-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>

        <div class="overflow-hidden">
            <div class="fw-bold text-dark text-truncate">
                {{ auth()->user()->name }}
            </div>
            <small class="text-muted text-truncate d-block">
                {{ auth()->user()->email }}
            </small>
        </div>
    </div>

    <div class="ess-sidebar-heading">My Account</div>

    <div class="ess-sidebar-links">

        <a href="{{ route('account.dashboard') }}"
           class="ess-sidebar-link {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            Dashboard
        </a>

        <a href="{{ route('account.orders.index') }}"
           class="ess-sidebar-link {{ request()->routeIs('account.orders.*') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            My Orders
        </a>

        <a href="{{ route('account.addresses.index') }}"
           class="ess-sidebar-link {{ request()->routeIs('account.addresses.*') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i>
            My Addresses
        </a>

        <a href="{{ route('account.profile.edit') }}"
           class="ess-sidebar-link {{ request()->routeIs('account.profile.*') ? 'active' : '' }}">
            <i class="fas fa-user-edit"></i>
            My Profile
        </a>

    </div>

    <div class="ess-sidebar-heading mt-3">Shopping</div>

    <div class="ess-sidebar-links">

        <a href="{{ route('account.cart.index') }}"
           class="ess-sidebar-link {{ request()->routeIs('account.cart.*') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            My Cart
        </a>

        <a href="{{ route('account.wishlist.index') }}"
           class="ess-sidebar-link {{ request()->routeIs('account.wishlist.*') ? 'active' : '' }}">
            <i class="fas fa-heart"></i>
            My Wishlist
        </a>

    </div>

    <div class="ess-sidebar-heading mt-3">Account Security</div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf

        <button type="submit" class="ess-sidebar-link ess-sidebar-logout text-danger">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </button>
    </form>

</aside>
