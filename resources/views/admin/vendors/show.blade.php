
@extends('layouts.app')

@section('title', 'Vendor Details | ESS MARKETPLACE')

@section('content')
<div class="container py-5 animate-up">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="gradient-text fw-bold">ADMIN CONTROL CENTER</span>
            <h1 class="fw-bold mt-2">Vendor Details</h1>
            <p class="text-muted mb-0">Complete seller application and business information.</p>
        </div>

        <a href="{{ route('admin.vendors.index') }}" class="btn btn-dark">
            <i class="fa-solid fa-arrow-left me-2"></i> Back to Vendors
        </a>
    </div>

    {{-- Status --}}
    @php
        $statusColors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'suspended' => 'secondary',
        ];
    @endphp

    <div class="auth-card mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h3 class="fw-bold mb-1">{{ $vendor->shop_name }}</h3>
                <p class="text-muted mb-0">
                    Vendor ID: #{{ $vendor->id }}
                </p>
            </div>

            <span class="badge fs-6 text-bg-{{ $statusColors[$vendor->status] ?? 'secondary' }}">
                {{ ucfirst($vendor->status) }}
            </span>
        </div>
    </div>

    <div class="row g-4">

        {{-- Shop Information --}}
        <div class="col-lg-6">
            <div class="auth-card h-100">
                <h5 class="fw-bold mb-4">
                    <i class="fa-solid fa-store me-2 text-primary"></i>
                    Shop Information
                </h5>

                <div class="mb-3">
                    <small class="text-muted">Shop Name</small>
                    <div class="fw-semibold">{{ $vendor->shop_name }}</div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Shop Slug</small>
                    <div class="fw-semibold">{{ $vendor->slug }}</div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Business Email</small>
                    <div class="fw-semibold">{{ $vendor->business_email ?? 'Not provided' }}</div>
                </div>

                <div>
                    <small class="text-muted">Business Phone</small>
                    <div class="fw-semibold">{{ $vendor->business_phone }}</div>
                </div>
            </div>
        </div>

        {{-- Owner Information --}}
        <div class="col-lg-6">
            <div class="auth-card h-100">
                <h5 class="fw-bold mb-4">
                    <i class="fa-solid fa-user me-2 text-primary"></i>
                    Owner Information
                </h5>

                <div class="mb-3">
                    <small class="text-muted">Owner Name</small>
                    <div class="fw-semibold">
                        {{ $vendor->user?->name ?? 'User unavailable' }}
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Account Email</small>
                    <div class="fw-semibold">
                        {{ $vendor->user?->email ?? 'Not available' }}
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Account Status</small>
                    <div class="fw-semibold">
                        {{ ucfirst($vendor->user?->status ?? 'Unknown') }}
                    </div>
                </div>

                <div>
                    <small class="text-muted">Registered On</small>
                    <div class="fw-semibold">
                        {{ $vendor->created_at?->format('d M Y, h:i A') ?? 'Not available' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Business Documents --}}
        <div class="col-lg-6">
            <div class="auth-card h-100">
                <h5 class="fw-bold mb-4">
                    <i class="fa-solid fa-file-shield me-2 text-primary"></i>
                    Business & Tax Details
                </h5>

                <div class="mb-3">
                    <small class="text-muted">GST Number</small>
                    <div class="fw-semibold">
                        {{ $vendor->gst_number ?? 'Not provided' }}
                    </div>
                </div>

                <div>
                    <small class="text-muted">PAN Number</small>
                    <div class="fw-semibold">
                        {{ $vendor->pan_number ?? 'Not provided' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Business Address --}}
        <div class="col-lg-6">
            <div class="auth-card h-100">
                <h5 class="fw-bold mb-4">
                    <i class="fa-solid fa-location-dot me-2 text-primary"></i>
                    Business Address
                </h5>

                <div class="mb-3">
                    <small class="text-muted">Address</small>
                    <div class="fw-semibold">
                        {{ $vendor->business_address }}
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">City</small>
                    <div class="fw-semibold">{{ $vendor->city }}</div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">State</small>
                    <div class="fw-semibold">{{ $vendor->state }}</div>
                </div>

                <div>
                    <small class="text-muted">Pincode</small>
                    <div class="fw-semibold">{{ $vendor->pincode }}</div>
                </div>
            </div>
        </div>

        {{-- Approval History --}}
        <div class="col-12">
            <div class="auth-card">
                <h5 class="fw-bold mb-4">
                    <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>
                    Application History
                </h5>

                <div class="row g-3">
                    <div class="col-md-4">
                        <small class="text-muted">Current Status</small>
                        <div class="fw-semibold">{{ ucfirst($vendor->status) }}</div>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Approved By</small>
                        <div class="fw-semibold">
                            {{ $vendor->approver?->name ?? 'Not applicable' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Approval Date</small>
                        <div class="fw-semibold">
                            {{ $vendor->approved_at?->format('d M Y, h:i A') ?? 'Not applicable' }}
                        </div>
                    </div>
                </div>

                @if($vendor->admin_remarks)
                    <hr>

                    <div>
                        <small class="text-muted">Admin Remarks / Rejection Reason</small>
                        <div class="alert alert-warning mt-2 mb-0">
                            {{ $vendor->admin_remarks }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
