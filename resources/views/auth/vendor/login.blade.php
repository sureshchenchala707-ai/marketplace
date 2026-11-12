
@extends('layouts.app')

@section('title', 'Vendor Login | ESS MARKETPLACE')

@section('content')
<div class="container py-5">
    <div class="row align-items-center justify-content-center g-5">

        <div class="col-lg-6 animate-up">
            <span class="badge rounded-pill px-3 py-2 mb-4"
                  style="background:#e8f7f0;color:#159b68;">
                <i class="fa-solid fa-store me-2"></i>
                SELL SMARTER. GROW FASTER.
            </span>

            <h1 class="display-4 fw-bold mb-3">
                Grow your business with
                <span class="gradient-text">ESS MARKETPLACE.</span>
            </h1>

            <p class="text-muted fs-5">
                Manage your shop, products, inventory, orders and
                earnings from one powerful vendor dashboard.
            </p>

            <div class="mt-4 d-flex flex-column gap-3">
                <div><i class="fa-solid fa-circle-check gradient-text me-2"></i> Manage your products and inventory</div>
                <div><i class="fa-solid fa-circle-check gradient-text me-2"></i> Track orders and sales</div>
                <div><i class="fa-solid fa-circle-check gradient-text me-2"></i> Monitor earnings and payouts</div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="auth-card">

                <div class="text-center mb-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:65px;height:65px;border-radius:20px;
                         background:linear-gradient(135deg,#119c78,#38c995);
                         color:white;font-size:26px;">
                        <i class="fa-solid fa-store"></i>
                    </div>

                    <h2 class="fw-bold">Vendor Login</h2>
                    <p class="text-muted">Access your seller account</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-warning">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('vendor.login.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email"
                               class="form-control"
                               value="{{ old('email') }}"
                               placeholder="business@example.com"
                               autocomplete="email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password"
                                   id="vendorPassword"
                                   class="form-control"
                                   placeholder="Enter your password"
                                   autocomplete="current-password" required>

                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="toggleVendorPassword()"
                                    aria-label="Show or hide password">
                                <i class="fa-regular fa-eye" id="vendorPasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small">
                            <input type="checkbox" name="remember" value="1">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Access Vendor Panel
                        <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span class="small text-muted">Not a seller yet?</span>
                    <a href="{{ route('vendor.register') }}"
                       class="fw-bold text-decoration-none gradient-text">
                        Apply Now
                    </a>
                </div>

                <hr class="my-4">

                <div class="text-center">
                    <a href="{{ route('login') }}"
                       class="small text-decoration-none text-muted">
                        Customer Login
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleVendorPassword() {
    const password = document.getElementById('vendorPassword');
    const icon = document.getElementById('vendorPasswordIcon');

    const visible = password.type === 'password';
    password.type = visible ? 'text' : 'password';

    icon.classList.toggle('fa-eye', !visible);
    icon.classList.toggle('fa-eye-slash', visible);
}
</script>
@endpush
