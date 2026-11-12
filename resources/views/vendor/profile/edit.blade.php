
@extends('layouts.app')

@section('title', 'Shop Profile | ESS MARKETPLACE')

@section('content')
<div class="container-fluid py-3">

    <div class="mb-4">
        <h2 class="fw-bold">Shop Profile</h2>
        <p class="text-muted">Manage your store information and branding.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct these errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('vendor.profile.update') }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Shop Branding</h5>

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Shop Logo</label>

                        @if($vendor->logo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/'.$vendor->logo) }}"
                                     alt="Shop logo"
                                     style="width:100px;height:100px;object-fit:cover;border-radius:12px;">
                            </div>
                        @endif

                        <input type="file" name="logo"
                               class="form-control" accept="image/*">
                        <small class="text-muted">JPG, PNG or WebP. Maximum 2 MB.</small>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Shop Banner</label>

                        @if($vendor->banner)
                            <div class="mb-2">
                                <img src="{{ asset('storage/'.$vendor->banner) }}"
                                     alt="Shop banner"
                                     style="width:100%;max-height:130px;object-fit:cover;border-radius:12px;">
                            </div>
                        @endif

                        <input type="file" name="banner"
                               class="form-control" accept="image/*">
                        <small class="text-muted">JPG, PNG or WebP. Maximum 4 MB.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Business Information</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Shop Name *</label>
                        <input type="text" name="shop_name"
                               class="form-control"
                               value="{{ old('shop_name', $vendor->shop_name) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Business Email</label>
                        <input type="email" name="business_email"
                               class="form-control"
                               value="{{ old('business_email', $vendor->business_email) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Business Phone *</label>
                        <input type="text" name="business_phone"
                               class="form-control"
                               value="{{ old('business_phone', $vendor->business_phone) }}"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">GST Number</label>
                        <input type="text" name="gst_number"
                               class="form-control text-uppercase"
                               value="{{ old('gst_number', $vendor->gst_number) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">PAN Number</label>
                        <input type="text" name="pan_number"
                               class="form-control text-uppercase"
                               maxlength="10"
                               value="{{ old('pan_number', $vendor->pan_number) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Business Address *</label>
                        <textarea name="business_address"
                                  class="form-control" rows="3"
                                  required>{{ old('business_address', $vendor->business_address) }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">City *</label>
                        <input type="text" name="city"
                               class="form-control"
                               value="{{ old('city', $vendor->city) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">State *</label>
                        <input type="text" name="state"
                               class="form-control"
                               value="{{ old('state', $vendor->state) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pincode *</label>
                        <input type="text" name="pincode"
                               class="form-control"
                               value="{{ old('pincode', $vendor->pincode) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button type="submit" class="btn btn-primary px-4 py-2">
                <i class="fa-solid fa-floppy-disk me-2"></i>
                Save Shop Profile
            </button>
        </div>

    </form>
</div>
@endsection
