
@extends('layouts.app')

@section('title', 'Vendor Dashboard | ESS MARKETPLACE')

@section('content')

<style>
    .vendor-welcome {
        background: linear-gradient(115deg, #302078, #7657ff, #a855f7);
        border-radius: 20px;
        padding: 30px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .vendor-welcome::after {
        content: '';
        position: absolute;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: rgba(255,255,255,.10);
        right: -55px;
        top: -95px;
    }

    .vendor-welcome h2 {
        font-weight: 850;
        position: relative;
        z-index: 1;
    }

    .vendor-welcome p {
        color: rgba(255,255,255,.82);
        position: relative;
        z-index: 1;
    }

    .vendor-welcome a {
        position: relative;
        z-index: 1;
        display: inline-block;
        background: white;
        color: #6045d8;
        padding: 10px 17px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
    }

    .vendor-section-title {
        font-size: 19px;
        font-weight: 800;
        color: #171832;
    }

    .vendor-action-card {
        display: block;
        height: 100%;
        padding: 22px;
        background: white;
        border: 1px solid #e9eaff;
        border-radius: 17px;
        text-decoration: none;
        color: #171832;
        transition: .25s ease;
        box-shadow: 0 8px 25px rgba(35,30,90,.04);
    }

    .vendor-action-card:hover {
        transform: translateY(-4px);
        border-color: #bcb0ff;
        box-shadow: 0 14px 30px rgba(80,60,160,.10);
        color: #7657ff;
    }

    .vendor-action-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #f1edff;
        color: #7657ff;
        font-size: 20px;
        margin-bottom: 17px;
    }

    .vendor-action-card h6 {
        font-weight: 800;
        margin-bottom: 8px;
    }

    .vendor-action-card p {
        color: #8589a8;
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .vendor-action-link {
        font-size: 12px;
        font-weight: 800;
        color: #7657ff;
    }

    .vendor-info-card {
        background: white;
        border: 1px solid #e9eaff;
        border-radius: 17px;
        padding: 20px;
    }
</style>

<div class="animate-up">

    {{-- PAGE HEADING --}}
    <div class="mb-4">
        <span class="gradient-text fw-bold small">
            SELLER WORKSPACE
        </span>

        <h3 class="fw-bold mt-2 mb-1">
            Vendor Dashboard
        </h3>

        <p class="text-muted mb-0">
            Manage your shop, products and customer orders.
        </p>
    </div>

    {{-- WELCOME BANNER --}}
    <div class="vendor-welcome mb-4">
        <h2>
            Welcome back, {{ auth()->user()->name }}!
        </h2>

        <p class="mb-3">
            Your seller workspace is ready. Manage your products
            and keep your customers updated.
        </p>

        <a href="{{ route('vendor.products.create') }}">
            <i class="fas fa-plus me-2"></i>
            Add New Product
        </a>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- QUICK MANAGEMENT --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="vendor-section-title mb-0">
            Manage Your Business
        </h5>
    </div>

    <div class="row g-3 mb-4">

        {{-- PRODUCTS --}}
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('vendor.products.index') }}"
               class="vendor-action-card">

                <div class="vendor-action-icon">
                    <i class="fas fa-box-open"></i>
                </div>

                <h6>Manage Products</h6>

                <p>
                    View, edit and manage all your shop products,
                    prices and product information.
                </p>

                <span class="vendor-action-link">
                    View Products
                    <i class="fas fa-arrow-right ms-1"></i>
                </span>

            </a>
        </div>

        {{-- ADD PRODUCT --}}
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('vendor.products.create') }}"
               class="vendor-action-card">

                <div class="vendor-action-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>

                <h6>Add New Product</h6>

                <p>
                    Add new products to your store with images,
                    descriptions, variants and stock details.
                </p>

                <span class="vendor-action-link">
                    Create Product
                    <i class="fas fa-arrow-right ms-1"></i>
                </span>

            </a>
        </div>

        {{-- ORDERS --}}
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('vendor.orders.index') }}"
               class="vendor-action-card">

                <div class="vendor-action-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>

                <h6>Manage Orders</h6>

                <p>
                    Review customer orders and manage order
                    processing, shipments and delivery status.
                </p>

                <span class="vendor-action-link">
                    View Orders
                    <i class="fas fa-arrow-right ms-1"></i>
                </span>

            </a>
        </div>

    </div>

    {{-- ACCOUNT INFORMATION --}}
    <div class="vendor-info-card">
        <div class="d-flex align-items-center gap-3">
            <div class="vendor-action-icon mb-0">
                <i class="fas fa-user-circle"></i>
            </div>

            <div>
                <h6 class="fw-bold mb-1">
                    Your Vendor Account
                </h6>

                <p class="text-muted small mb-0">
                    Logged in as {{ auth()->user()->email }}
                </p>
            </div>
        </div>
    </div>

</div>

@endsection
