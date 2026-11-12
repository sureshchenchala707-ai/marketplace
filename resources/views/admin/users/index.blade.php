@extends('layouts.app')

@section('title', 'User Management | ESS MARKETPLACE')

@section('content')

<!-- @include('layouts.admin-sidebar') -->

<div id="adminMain" class="admin-main">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <button id="sidebarToggle" class="btn btn-outline-secondary">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div>
                <h4 class="fw-bold mb-1">User Management</h4>
                <p class="text-muted mb-0">Manage marketplace accounts</p>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button id="themeToggle" class="btn btn-outline-secondary">
                <i class="fa-solid fa-moon me-1"></i>
                <span id="themeLabel">Dark Mode</span>
            </button>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Statistics -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="auth-card">
                <i class="fa-solid fa-users gradient-text fs-2"></i>
                <p class="text-muted mt-3 mb-1">Total Users</p>
                <h3 class="fw-bold">{{ number_format($totalUsers) }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="auth-card">
                <i class="fa-solid fa-user-check text-success fs-2"></i>
                <p class="text-muted mt-3 mb-1">Active Users</p>
                <h3 class="fw-bold">{{ number_format($activeUsers) }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="auth-card">
                <i class="fa-solid fa-user-slash text-danger fs-2"></i>
                <p class="text-muted mt-3 mb-1">Inactive Users</p>
                <h3 class="fw-bold">{{ number_format($inactiveUsers) }}</h3>
            </div>
        </div>

    </div>

    <!-- Search and Filters -->
    <div class="auth-card mb-4">

        <form method="GET" action="{{ route('admin.users.index') }}">

            <div class="row g-3 align-items-end">

                <div class="col-lg-5">
                    <label class="form-label fw-semibold">Search Users</label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search name or email..."
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-lg-3">
                    <label class="form-label fw-semibold">Role</label>

                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="vendor" @selected(request('role') === 'vendor')>Vendor</option>
                        <option value="customer" @selected(request('role') === 'customer')>Customer</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label fw-semibold">Status</label>

                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button class="btn btn-primary w-100">
                        <i class="fa-solid fa-search"></i>
                        Search
                    </button>

                    <a href="{{ route('admin.users.index') }}"
                       class="btn btn-outline-secondary">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>

            </div>
        </form>

    </div>

    <!-- User Table -->
    <div class="auth-card">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Marketplace Users</h5>
            <span class="badge bg-primary">
                {{ $users->total() }} Records
            </span>
        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($users as $user)

                        <tr>

                            <td>#{{ $user->id }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $user->name }}
                                </div>
                            </td>

                            <td>{{ $user->email }}</td>

                            <td>
                                @forelse($user->roles as $role)
                                    <span class="badge bg-info text-dark mb-1">
                                        {{ ucfirst($role->slug) }}
                                    </span>
                                @empty
                                    <span class="text-muted">No role</span>
                                @endforelse
                            </td>

                            <td>
                                @if($user->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            <td>
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            <td>

                                @if(!$user->hasRole('admin'))

                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.status', $user) }}"
                                        onsubmit="return confirm('Are you sure you want to change this account status?')"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        @if($user->status === 'active')
                                            <input type="hidden" name="status" value="inactive">

                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fa-solid fa-ban me-1"></i>
                                                Deactivate
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="active">

                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-check me-1"></i>
                                                Activate
                                            </button>
                                        @endif

                                    </form>

                                @else

                                    <span class="text-muted small">
                                        Protected
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fa-solid fa-users fs-1 text-muted"></i>
                                <p class="text-muted mt-3 mb-0">
                                    No users found.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>

    </div>

</div>

<style>

/* Admin Sidebar */
.admin-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 260px;
    height: 100vh;
    background: #171b2e;
    color: #fff;
    z-index: 1050;
    padding: 20px 14px;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    transition: width .25s ease, transform .25s ease;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    margin-bottom: 25px;
    white-space: nowrap;
}

.brand-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #7657ff;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}

.sidebar-section {
    color: #8d95ad;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    padding: 12px 10px;
}

.sidebar-link {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px;
    margin: 3px 0;
    color: #c5c9d8;
    text-decoration: none;
    border-radius: 9px;
    white-space: nowrap;
    transition: .2s;
}

.sidebar-link i {
    width: 20px;
    text-align: center;
    flex-shrink: 0;
}

.sidebar-link:hover,
.sidebar-link.active {
    background: #7657ff;
    color: #fff;
}

.sidebar-footer {
    margin-top: auto;
    padding: 18px 10px 5px;
    border-top: 1px solid #34394e;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sidebar-footer small {
    color: #8d95ad;
}

/* Main Content */
.admin-main {
    margin-left: 260px;
    padding: 24px;
    min-height: 100vh;
    transition: margin-left .25s ease;
}

.admin-sidebar.collapsed {
    width: 82px;
}

.admin-sidebar.collapsed .sidebar-label,
.admin-sidebar.collapsed .sidebar-section {
    display: none;
}

.admin-sidebar.collapsed .sidebar-link {
    justify-content: center;
}

.admin-main.expanded {
    margin-left: 82px;
}

/* Mobile Overlay */
.sidebar-overlay {
    display: none;
}

/* Dark Theme */
body.dark-admin {
    background: #111827 !important;
    color: #f9fafb !important;
}

body.dark-admin .auth-card {
    background: #1f2937 !important;
    color: #f9fafb !important;
    border-color: #374151 !important;
}

body.dark-admin .text-muted {
    color: #9ca3af !important;
}

body.dark-admin .table {
    --bs-table-bg: #1f2937;
    --bs-table-color: #f9fafb;
    --bs-table-border-color: #374151;
}

/* Mobile Responsive */
@media(max-width: 767.98px) {
    .admin-sidebar {
        transform: translateX(-100%);
        width: 260px !important;
    }

    .admin-sidebar.mobile-open {
        transform: translateX(0);
    }

    .admin-main,
    .admin-main.expanded {
        margin-left: 0;
        padding: 16px;
    }

    .sidebar-overlay.show {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.55);
        z-index: 1040;
    }
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('adminSidebar');
    const main = document.getElementById('adminMain');
    const toggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');

    function isMobile() {
        return window.innerWidth < 768;
    }

    function initializeSidebar() {
        if (isMobile()) {
            sidebar.classList.remove('mobile-open', 'collapsed');
            overlay.classList.remove('show');
            main.classList.remove('expanded');
        } else {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');

            const collapsed =
                localStorage.getItem('adminSidebarCollapsed') === 'true';

            sidebar.classList.toggle('collapsed', collapsed);
            main.classList.toggle('expanded', collapsed);
        }
    }

    toggle.addEventListener('click', function () {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');

            localStorage.setItem(
                'adminSidebarCollapsed',
                sidebar.classList.contains('collapsed')
            );
        }
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
    });

    window.addEventListener('resize', initializeSidebar);
    initializeSidebar();

    // Persistent theme
    const themeToggle = document.getElementById('themeToggle');
    const themeLabel = document.getElementById('themeLabel');

    function applyTheme(theme) {
        const dark = theme === 'dark';

        document.body.classList.toggle('dark-admin', dark);

        themeToggle.querySelector('i').className = dark
            ? 'fa-solid fa-sun me-1'
            : 'fa-solid fa-moon me-1';

        themeLabel.textContent = dark ? 'Light Mode' : 'Dark Mode';
    }

    applyTheme(localStorage.getItem('adminTheme') || 'light');

    themeToggle.addEventListener('click', function () {
        const nextTheme = document.body.classList.contains('dark-admin')
            ? 'light'
            : 'dark';

        localStorage.setItem('adminTheme', nextTheme);
        applyTheme(nextTheme);
    });

});
</script>

@endsection
