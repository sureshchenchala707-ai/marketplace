

@extends('layouts.app')

@section('title', 'Create Account | ESS MARKETPLACE')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-9">

            <div class="auth-card">
                <div class="text-center mb-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:65px;height:65px;border-radius:20px;
                         background:linear-gradient(135deg,#7657ff,#a855f7);
                         color:white;font-size:26px;">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>

                    <h2 class="fw-bold">Create Your Account</h2>
                    <p class="text-muted">Join ESS MARKETPLACE today</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name"
                               class="form-control"
                               value="{{ old('name') }}"
                               placeholder="Enter your full name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email"
                               class="form-control"
                               value="{{ old('email') }}"
                               placeholder="you@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password"
                               class="form-control"
                               placeholder="Minimum 8 characters" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Re-enter password" required>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" name="terms" value="1"
                               class="form-check-input" id="terms" required>
                        <label class="form-check-label small" for="terms">
                            I agree to the Terms and Conditions.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Create Account
                        <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span class="text-muted small">Already registered?</span>
                    <a href="{{ route('login') }}"
                       class="fw-bold text-decoration-none gradient-text">
                        Login
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
