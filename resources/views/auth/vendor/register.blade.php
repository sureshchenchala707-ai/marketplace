
@extends('layouts.app')

@section('title', 'Become a Vendor | ESS MARKETPLACE')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="auth-card">
                <div class="text-center mb-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:65px;height:65px;border-radius:20px;
                         background:linear-gradient(135deg,#119c78,#38c995);
                         color:white;font-size:26px;">
                        <i class="fa-solid fa-shop"></i>
                    </div>

                    <h2 class="fw-bold">Become a Seller</h2>
                    <p class="text-muted">Create your shop on ESS MARKETPLACE</p>
                    <span class="badge rounded-pill text-bg-warning">Admin approval required</span>
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

                <form method="POST" action="{{ route('vendor.register.submit') }}">
                    @csrf

                    <h5 class="fw-bold mb-3">01. Account Details</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Your Full Name *</label>
                            <input name="name" class="form-control"
                                   value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Login Email *</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password"
                                   class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" required>
                        </div>
                    </div>

                    <hr>

                    <h5 class="fw-bold mb-3">02. Shop Information</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Shop Name *</label>
                            <input name="shop_name" class="form-control"
                                   value="{{ old('shop_name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Business Email</label>
                            <input type="email" name="business_email"
                                   class="form-control"
                                   value="{{ old('business_email') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Business Phone *</label>
                            <input name="business_phone" class="form-control"
                                   value="{{ old('business_phone') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">GST Number</label>
                            <input name="gst_number" class="form-control"
                                   value="{{ old('gst_number') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">PAN Number</label>
                            <input name="pan_number" class="form-control"
                                   value="{{ old('pan_number') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Business Address *</label>
                            <textarea name="business_address"
                                      class="form-control" rows="3"
                                      required>{{ old('business_address') }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">City *</label>
                            <input name="city" class="form-control"
                                   value="{{ old('city') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">State *</label>
                            <input name="state" class="form-control"
                                   value="{{ old('state') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">PIN Code *</label>
                            <input name="pincode" class="form-control"
                                   value="{{ old('pincode') }}" required>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" name="terms" value="1"
                               class="form-check-input" id="vendorTerms" required>

                        <label class="form-check-label small" for="vendorTerms">
                            I confirm that the information provided is accurate
                            and agree to the marketplace terms.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Submit Vendor Application
                        <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span class="small text-muted">Already registered?</span>
                    <a href="{{ route('vendor.login') }}"
                       class="fw-bold text-decoration-none gradient-text">
                        Vendor Login
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
