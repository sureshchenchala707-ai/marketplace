
@extends('layouts.app')

@section('title', 'My Profile - ESS MARKETPLACE')

@section('content')

<div class="container py-5">

    <div class="mb-4">
        <h2 class="fw-bold">My Profile</h2>
        <p class="text-muted">Manage your personal information and password.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">

            <form action="{{ route('account.profile.update') }}"
                  method="POST">

                @csrf
                @method('PUT')

                <h5 class="fw-bold mb-4">Personal Information</h5>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', auth()->user()->name) }}"
                           required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email', auth()->user()->email) }}"
                           required>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-2">Change Password</h5>
                <p class="text-muted small">
                    Leave these fields empty if you don't want to change your password.
                </p>

                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password"
                           name="current_password"
                           class="form-control"
                           autocomplete="current-password">
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password"
                           name="new_password"
                           class="form-control"
                           autocomplete="new-password">
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password"
                           name="new_password_confirmation"
                           class="form-control"
                           autocomplete="new-password">
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>
                        Save Changes
                    </button>

                    <a href="{{ route('account.dashboard') }}"
                       class="btn btn-light px-4">
                        Back to Dashboard
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
