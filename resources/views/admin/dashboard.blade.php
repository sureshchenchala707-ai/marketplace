
@extends('layouts.app')

@section('title', 'Admin Dashboard | ESS MARKETPLACE')

@section('content')

<!-- @include('layouts.admin-sidebar') -->

<div id="adminMain" class="admin-main">

    <div class="admin-topbar d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <button id="sidebarToggle" class="btn btn-outline-secondary">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div>
                <h5 class="fw-bold mb-0">Dashboard Overview</h5>
                <small class="text-muted">Welcome back, Administrator</small>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button id="themeToggle" class="btn btn-outline-secondary">
                <i class="fa-solid fa-moon me-1"></i>
                <span id="themeLabel">Dark Mode</span>
            </button>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="btn btn-dark">
                    <i class="fa-solid fa-right-from-bracket me-1"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <div class="mb-4">
        <span class="gradient-text fw-bold">ADMIN CONTROL CENTER</span>
        <h2 class="fw-bold mt-2">Marketplace Statistics</h2>
    </div>

    <div class="row g-4">

        <div class="col-sm-6 col-xl-3">
            <div class="auth-card h-100">
                <i class="fa-solid fa-users gradient-text fs-2"></i>
                <p class="text-muted mt-3 mb-1">Total Users</p>
                <h2 class="fw-bold">{{ number_format($totalUsers) }}</h2>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="auth-card h-100">
                <i class="fa-solid fa-user gradient-text fs-2"></i>
                <p class="text-muted mt-3 mb-1">Customers</p>
                <h2 class="fw-bold">{{ number_format($totalCustomers) }}</h2>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="auth-card h-100">
                <i class="fa-solid fa-store gradient-text fs-2"></i>
                <p class="text-muted mt-3 mb-1">Total Vendors</p>
                <h2 class="fw-bold">{{ number_format($totalVendors) }}</h2>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="auth-card h-100">
                <i class="fa-solid fa-clock gradient-text fs-2"></i>
                <p class="text-muted mt-3 mb-1">Pending Vendors</p>
                <h2 class="fw-bold">{{ number_format($pendingVendors) }}</h2>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="auth-card h-100">
                <h5 class="fw-bold mb-4">Vendor Application Overview</h5>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light">
                            <span class="text-muted small">Pending</span>
                            <h3 class="fw-bold text-warning mb-0">
                                {{ number_format($pendingVendors) }}
                            </h3>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light">
                            <span class="text-muted small">Approved</span>
                            <h3 class="fw-bold text-success mb-0">
                                {{ number_format($approvedVendors) }}
                            </h3>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light">
                            <span class="text-muted small">Rejected</span>
                            <h3 class="fw-bold text-danger mb-0">
                                {{ number_format($rejectedVendors) }}
                            </h3>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.vendors.index') }}"
                   class="btn btn-primary mt-4">
                    Manage Vendor Applications
                    <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="auth-card h-100">
                <h5 class="fw-bold mb-3">Quick Actions</h5>

                <a href="{{ route('admin.vendors.index') }}"
                   class="btn btn-outline-primary w-100 mb-3">
                    <i class="fa-solid fa-store me-2"></i>
                    Manage Vendors
                </a>

                <div class="alert alert-info mb-0">
                    More marketplace modules will be added here.
                </div>
            </div>
        </div>

    </div>

    <!-- Monthly Registration Analytics -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="auth-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Marketplace Growth Analytics</h5>
                        <p class="text-muted mb-0">Customer and vendor registrations – last 12 months</p>
                    </div>
                    <span class="badge bg-primary">12 Months</span>
                </div>

                <div style="position: relative; height: 350px;">
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Recent Vendor Activity -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="auth-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Recent Vendor Activity</h5>
                    <p class="text-muted mb-0">Latest vendor applications</p>
                </div>

                <a href="{{ route('admin.vendors.index') }}"
                   class="btn btn-outline-primary btn-sm">
                    View All Vendors
                </a>
            </div>

            @forelse($recentVendors as $vendor)
                <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom py-3 gap-3">

                    <div>
                        <h6 class="fw-bold mb-1">
                            {{ $vendor->shop_name }}
                        </h6>

                        <small class="text-muted">
                            Owner: {{ $vendor->user->name }}
                        </small>

                        <br>

                        <small class="text-muted">
                            {{ $vendor->created_at->format('d M Y, h:i A') }}
                        </small>
                    </div>

                    <div>
                        @if($vendor->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($vendor->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($vendor->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-secondary">
                                {{ ucfirst($vendor->status) }}
                            </span>
                        @endif
                    </div>

                </div>
            @empty
                <p class="text-muted mb-0">No vendor activity available.</p>
            @endforelse

        </div>
    </div>
</div>

</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const canvas = document.getElementById('registrationChart');

    if (!canvas || typeof Chart === 'undefined') {
        return;
    }

    new Chart(canvas, {
        type: 'line',

        data: {
            labels: @json($labels),

            datasets: [
                {
                    label: 'Customers',
                    data: @json($customerChartData),
                    borderColor: '#7657ff',
                    backgroundColor: 'rgba(118, 87, 255, 0.12)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Vendors',
                    data: @json($vendorChartData),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.10)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
                intersect: false,
                mode: 'index'
            },

            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

});
</script>
<style>
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

.sidebar-overlay {
    display: none;
}

body.dark-admin {
    background: #111827 !important;
    color: #f9fafb !important;
}

body.dark-admin .auth-card {
    background: #1f2937 !important;
    border-color: #374151 !important;
    color: #f9fafb !important;
}

body.dark-admin .text-muted {
    color: #9ca3af !important;
}

body.dark-admin .bg-light {
    background: #374151 !important;
}

body.dark-admin .alert-info {
    background: #1e3a5f;
    color: #dbeafe;
    border-color: #2563eb;
}

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

    const themeToggle = document.getElementById('themeToggle');
    const themeLabel = document.getElementById('themeLabel');

    function isMobile() {
        return window.innerWidth < 768;
    }

    // Desktop starts expanded; mobile starts closed.
    function initializeSidebar() {
        if (isMobile()) {
            sidebar.classList.remove('mobile-open', 'collapsed');
            overlay.classList.remove('show');
            main.classList.remove('expanded');
        } else {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');

            const collapsed = localStorage.getItem('adminSidebarCollapsed') === 'true';

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

    // Persistent Dark / Light theme.
    function applyTheme(theme) {
        const dark = theme === 'dark';

        document.body.classList.toggle('dark-admin', dark);

        themeToggle.querySelector('i').className = dark
            ? 'fa-solid fa-sun me-1'
            : 'fa-solid fa-moon me-1';

        themeLabel.textContent = dark ? 'Light Mode' : 'Dark Mode';
    }

    const savedTheme = localStorage.getItem('adminTheme') || 'light';
    applyTheme(savedTheme);

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
