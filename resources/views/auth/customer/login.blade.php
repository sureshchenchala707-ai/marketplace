
@extends('layouts.app')

@section('title', 'Customer Login | ESS MARKETPLACE')

@section('content')
<div class="container py-5">
    <div class="row align-items-center justify-content-center g-5">

        <div class="col-lg-6 animate-up">
            <span class="badge rounded-pill px-3 py-2 mb-4"
                  style="background:#eeeaff;color:#7657ff;">
                <i class="fa-solid fa-bag-shopping me-2"></i>
                YOUR SHOPPING JOURNEY STARTS HERE
            </span>

            <h1 class="display-4 fw-bold mb-3">
                Welcome back to
                <span class="gradient-text">ESS MARKETPLACE.</span>
            </h1>

            <p class="text-muted fs-5 mb-4">
                Discover amazing products, exclusive deals and a
                shopping experience made just for you.
            </p>

            <div class="d-flex flex-wrap gap-4">
                <div>
                    <h3 class="fw-bold mb-0">10K+</h3>
                    <small class="text-muted">Products</small>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">500+</h3>
                    <small class="text-muted">Sellers</small>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">24/7</h3>
                    <small class="text-muted">Support</small>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="auth-card">

                <div class="text-center mb-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:65px;height:65px;border-radius:20px;
                         background:linear-gradient(135deg,#7657ff,#a855f7);
                         color:white;font-size:26px;">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <h2 class="fw-bold">Customer Login</h2>
                    <p class="text-muted">Sign in to continue shopping</p>
                </div>

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

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fa-regular fa-envelope text-muted"></i>
                            </span>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   placeholder="you@example.com"
                                   value="{{ old('email') }}"
                                   autocomplete="email"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password"
                                   name="password"
                                   id="customerPassword"
                                   class="form-control"
                                   placeholder="Enter your password"
                                   autocomplete="current-password"
                                   required>

                            <button class="btn btn-outline-secondary"
                                    type="button"
                                    onclick="togglePassword()"
                                    aria-label="Show or hide password">
                                <i class="fa-regular fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <label class="small">
                            <input type="checkbox" name="remember" value="1">
                            Remember me
                        </label>

                        <a href="#"
                           class="small text-decoration-none gradient-text">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Sign In <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span class="text-muted small">New to ESS MARKETPLACE?</span>
                    <a href="{{ route('register') }}"
                       class="fw-bold text-decoration-none gradient-text">
                        Create Account
                    </a>
                </div>

                <hr class="my-4">

                <div class="text-center">
                    <a href="{{ route('vendor.login') }}"
                       class="small text-decoration-none text-muted">
                        Are you a seller? Vendor Login
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const password = document.getElementById('customerPassword');
    const icon = document.getElementById('passwordIcon');

    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush
