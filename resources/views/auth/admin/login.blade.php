
@extends('layouts.app')

@section('title', 'Admin Login | ESS MARKETPLACE')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">

            <div class="auth-card">
                <div class="text-center mb-4">

                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:70px;height:70px;border-radius:22px;
                         background:linear-gradient(135deg,#17172e,#7657ff);
                         color:white;font-size:27px;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>

                    <span class="badge rounded-pill mb-3"
                          style="background:#eeeaff;color:#7657ff;">
                        SECURE ADMINISTRATION
                    </span>

                    <h2 class="fw-bold">Admin Portal</h2>
                    <p class="text-muted">Sign in to manage ESS MARKETPLACE</p>
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

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Administrator Email</label>
                        <input type="email" name="email"
                               class="form-control"
                               value="{{ old('email') }}"
                               placeholder="admin@example.com"
                               autocomplete="username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password"
                                   id="adminPassword"
                                   class="form-control"
                                   placeholder="Enter administrator password"
                                   autocomplete="current-password" required>

                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="toggleAdminPassword()"
                                    aria-label="Show or hide password">
                                <i class="fa-regular fa-eye" id="adminPasswordIcon"></i>
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
                        Secure Login
                        <i class="fa-solid fa-lock ms-2"></i>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fa-solid fa-shield-halved me-1"></i>
                        Authorized administrators only
                    </small>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleAdminPassword() {
    const password = document.getElementById('adminPassword');
    const icon = document.getElementById('adminPasswordIcon');

    const visible = password.type === 'password';
    password.type = visible ? 'text' : 'password';

    icon.classList.toggle('fa-eye', !visible);
    icon.classList.toggle('fa-eye-slash', visible);
}
</script>
@endpush
