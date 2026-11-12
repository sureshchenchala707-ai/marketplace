
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ESS MARKETPLACE')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(request()->routeIs('home', 'products.show', 'account.*', 'login', 'register'))
        @vite('resources/css/customer-tailwind.css')
    @endif

    <style>
        :root {
            --primary: #7657ff;
            --secondary: #a855f7;
            --dark: #111329;
            --muted: #8589a8;
            --surface: #ffffff;
            --background: #f5f6ff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--dark);
            overflow-x: hidden;
        }

        .navbar {
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(118,87,255,.10);
            padding: 17px 0;
            position: relative;
            z-index: 1999 !important;
        }

        .brand {
            text-decoration: none;
            font-size: 23px;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--dark);
        }

        .brand span {
            color: var(--primary);
        }

        /* ADMIN SIDEBAR */

        .admin-layout {
            display: flex;
            align-items: stretch;
            min-height: calc(100vh - 70px);
            width: 100%;
        }

        .admin-sidebar {
            width: 260px;
            min-width: 260px;
            min-height: calc(100vh - 70px);
            background: #111329;
            color: #fff;
            padding: 22px 15px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 1000;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px 25px;
            font-size: 16px;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: #7657ff;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-section {
            color: #8589a8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            padding: 15px 12px 9px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 14px;
            margin: 3px 0;
            color: #b7bad0;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            transition: .2s ease;
        }

        .sidebar-link i {
            width: 19px;
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
            padding: 25px 10px 5px;
            border-top: 1px solid #292b45;
            color: #b7bad0;
            font-size: 12px;
        }

        .sidebar-logout {
            background: transparent;
            text-align: left;
            cursor: pointer;
        }

        .admin-sidebar .sidebar-label {
            display: inline;
        }

        .admin-main-content {
            flex: 1;
            min-width: 0;
            padding: 25px;
            overflow-x: auto;
        }

        /* PAGE */

        .page-wrapper {
            min-height: calc(100vh - 70px);
            position: relative;
            overflow: hidden;
        }

        .glow {
            position: fixed;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            filter: blur(100px);
            opacity: .22;
            pointer-events: none;
            z-index: -1;
            animation: floatGlow 9s ease-in-out infinite alternate;
        }

        .glow-one {
            background: #7657ff;
            top: 10%;
            left: -130px;
        }

        .glow-two {
            background: #ec4899;
            bottom: 0;
            right: -130px;
            animation-delay: -4s;
        }

        .auth-card {
            background: rgba(255,255,255,.90);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.8);
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(35,30,90,.10);
            padding: 38px;
            animation: cardEnter .7s ease both;
        }

        .form-control,
        .form-select {
            min-height: 50px;
            border: 1px solid #e5e5f0;
            border-radius: 12px;
            padding: 12px 15px;
            transition: .25s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(118,87,255,.12);
        }

        .btn-primary {
            min-height: 50px;
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            background: linear-gradient(110deg, #7657ff, #a855f7);
            box-shadow: 0 8px 20px rgba(118,87,255,.25);
            transition: transform .25s, box-shadow .25s;
        }

        .btn-primary:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(118,87,255,.35);
        }

        .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #42445b;
            margin-bottom: 8px;
        }

        .text-muted {
            color: var(--muted) !important;
        }

        .gradient-text {
            background: linear-gradient(100deg, #7657ff, #c04cf5);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .animate-up {
            animation: cardEnter .7s ease both;
        }

        .alert {
            border: 0;
            border-radius: 12px;
        }

        @keyframes cardEnter {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatGlow {
            from {
                transform: translate(0,0) scale(1);
            }
            to {
                transform: translate(35px,-25px) scale(1.15);
            }
        }

        @media(max-width: 768px) {
            .admin-layout {
                display: block;
            }

            .admin-sidebar {
                width: 100%;
                min-width: 0;
                min-height: auto;
            }

            .admin-main-content {
                padding: 15px;
            }
        }

        @media(max-width: 576px) {
            .auth-card {
                padding: 25px 20px;
                border-radius: 18px;
            }
        }

        @media(prefers-reduced-motion:reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="{{ request()->routeIs('home', 'products.show', 'account.*', 'login', 'register') ? 'ess-customer' : '' }}">

    @php
        $isStandaloneLogin = request()->routeIs(
            'admin.login',
            'vendor.login'
        );

        $showAdminSidebar = !$isStandaloneLogin
            && auth()->check()
            && auth()->user()->hasRole('admin')
            && request()->routeIs('admin.*');

        $showVendorSidebar = !$isStandaloneLogin
            && auth()->check()
            && auth()->user()->hasRole('vendor')
            && request()->routeIs('vendor.*');

        $showCustomerSidebar = auth()->check()
            && auth()->user()->hasRole('customer')
            && request()->routeIs(
                'account.dashboard',
                'account.orders.*',
                'account.addresses.*',
                'account.profile.*'
            );
    @endphp

    {{-- HEADER --}}
    @if(!$isStandaloneLogin)

        @if(request()->routeIs(
            'home',
            'products.show',
            'account.*',
            'login',
            'register'
        ))

            {{-- STOREFRONT / CUSTOMER HEADER --}}
            @include('layouts.partials.storefront-header')

        @else

            {{-- ADMIN / VENDOR / OTHER HEADER --}}
            <nav class="navbar">
                <div class="container-fluid px-4">

                    <a href="/" class="brand">
                        ESS <span>MARKETPLACE.</span>
                    </a>

                    @auth
                        <div class="d-flex align-items-center gap-3">
                            @include('notifications.partials.bell')

                            @if(auth()->user()->hasRole('customer'))
                                <a href="{{ route('account.wishlist.index') }}"
                                   class="ess-icon-link position-relative"
                                   title="My Wishlist">
                                    <i class="fas fa-heart"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ auth()->user()->wishlistItems()->count() }}
                                    </span>
                                </a>

                                <a href="{{ route('account.cart.index') }}"
                                   class="ess-icon-link position-relative"
                                   title="Shopping Cart">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        {{ auth()->user()->cartItems()->count() }}
                                    </span>
                                </a>
                            @endif
                        </div>
                    @endauth

                </div>
            </nav>

        @endif

    @endif

    {{-- PAGE CONTENT --}}
    <div class="page-wrapper">

        <div class="glow glow-one"></div>
        <div class="glow glow-two"></div>

        {{-- ADMIN LAYOUT --}}
        @if($showAdminSidebar)

            <div class="admin-layout">

                @include('layouts.partials.admin-sidebar')

                <main class="admin-main-content">
                    @yield('content')
                </main>

            </div>

        {{-- CUSTOMER LAYOUT --}}
        @elseif($showCustomerSidebar)

            <div class="container-fluid px-lg-5 py-4">

                <div class="row g-4">

                    <div class="col-lg-3">
                        @include('layouts.partials.customer-sidebar')
                    </div>

                    <main class="col-lg-9" style="min-width:0">
                        @yield('content')
                    </main>

                </div>

            </div>

        {{-- VENDOR LAYOUT --}}
        @elseif($showVendorSidebar)

            <div class="container-fluid px-lg-4 py-4">

                <div class="row g-4">

                    <div class="col-lg-3 col-xl-2">
                        @include('layouts.partials.vendor-sidebar')
                    </div>

                    <main class="col-lg-9 col-xl-10" style="min-width:0">
                        @yield('content')
                    </main>

                </div>

            </div>

        {{-- NORMAL / LOGIN PAGE --}}
        @else

            <main>
                @yield('content')
            </main>

        @endif

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>
</html>
