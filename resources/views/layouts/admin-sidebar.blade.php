<style>


.admin-layout {
    display: flex;
    min-height: calc(100vh - 75px);
    background: #f5f6ff;
}

.admin-main-content {
    flex: 1;
    min-width: 0;
    padding: 28px;
    overflow-x: hidden;
}

@media (max-width: 768px) {
    .admin-main-content {
        padding: 15px;
    }
}

</style>
@php
    $showAdminSidebar = request()->routeIs('admin.*')
        && auth()->check()
        && auth()->user()->hasRole('admin');

    $showCustomerSidebar = request()->routeIs(
        'account.dashboard',
        'account.orders.*',
        'account.addresses.*',
        'account.profile.*'
    );

    $showVendorSidebar = request()->routeIs('vendor.*')
        && auth()->check()
        && auth()->user()->hasRole('vendor');
@endphp

@if($showAdminSidebar)
    <div class="admin-layout">
        @include('layouts.partials.admin-sidebar')

        <main class="admin-main-content">
            @yield('content')
        </main>
    </div>

@elseif($showCustomerSidebar && auth()->check() && auth()->user()->hasRole('customer'))
    <div class="container-fluid px-lg-4 py-4">
        <div class="row g-4">
            <div class="col-lg-3">
                @include('layouts.partials.customer-sidebar')
            </div>
            <main class="col-lg-9">
                @yield('content')
            </main>
        </div>
    </div>

@elseif($showVendorSidebar)
    <div class="container-fluid px-lg-4 py-4">
        <div class="row g-4">
            <div class="col-lg-3 col-xl-2">
                @include('layouts.partials.vendor-sidebar')
            </div>
            <main class="col-lg-9 col-xl-10">
                @yield('content')
            </main>
        </div>
    </div>

@else
    @yield('content')
@endif
