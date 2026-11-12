
@extends('layouts.app')

@section('title', 'Vendor Applications | ESS MARKETPLACE')

@section('content')
<div class="container py-5 animate-up">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="gradient-text fw-bold">ADMIN CONTROL CENTER</span>
            <h1 class="fw-bold mt-2">Vendor Management</h1>
            <p class="text-muted mb-0">Search, review and manage marketplace sellers.</p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark">
            <i class="fa-solid fa-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Statistics --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['Pending', $pendingCount, 'fa-clock', '#f59e0b'],
            ['Approved', $approvedCount, 'fa-circle-check', '#16a36a'],
            ['Rejected', $rejectedCount, 'fa-circle-xmark', '#e05260'],
            ['Suspended', $suspendedCount, 'fa-ban', '#64748b'],
        ] as $stat)
            <div class="col-md-3 col-sm-6">
                <div class="auth-card h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">{{ $stat[0] }} Applications</p>
                            <h2 class="fw-bold mb-0">{{ $stat[1] }}</h2>
                        </div>
                        <i class="fa-solid {{ $stat[2] }} fs-2"
                           style="color:{{ $stat[3] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="auth-card">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <h4 class="fw-bold mb-0">Seller Applications</h4>

            <span class="badge rounded-pill"
                  style="background:#eeeaff;color:#7657ff">
                {{ $vendors->total() }} Results
            </span>
        </div>

        {{-- Search and Filters --}}
        <form method="GET"
              action="{{ route('admin.vendors.index') }}"
              class="row g-3 mb-4">

            <div class="col-lg-6">
                <label class="form-label fw-semibold">Search Vendors</label>

                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>

                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Shop, owner, email or phone..."
                           value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-3">
                <label class="form-label fw-semibold">Application Status</label>

                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" @selected(request('status') === 'pending')>
                        Pending
                    </option>
                    <option value="approved" @selected(request('status') === 'approved')>
                        Approved
                    </option>
                    <option value="rejected" @selected(request('status') === 'rejected')>
                        Rejected
                    </option>
                    <option value="suspended" @selected(request('status') === 'suspended')>
                        Suspended
                    </option>
                </select>
            </div>

            <div class="col-lg-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fa-solid fa-filter me-1"></i> Apply
                </button>

                <a href="{{ route('admin.vendors.index') }}"
                   class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

        {{-- Vendor Table --}}
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Shop / Owner</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($vendors as $vendor)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $vendor->shop_name }}</div>
                                <small class="text-muted">
                                    {{ $vendor->user?->name ?? 'User unavailable' }}
                                </small>
                            </td>

                            <td>
                                <div>{{ $vendor->business_phone }}</div>
                                <small class="text-muted">
                                    {{ $vendor->business_email ?? $vendor->user?->email }}
                                </small>
                            </td>

                            <td>
                                {{ $vendor->city }}, {{ $vendor->state }}
                                <br>
                                <small class="text-muted">{{ $vendor->pincode }}</small>
                            </td>

                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'suspended' => 'secondary',
                                    ];
                                @endphp

                                <span class="badge text-bg-{{ $statusColors[$vendor->status] ?? 'secondary' }}">
                                    {{ ucfirst($vendor->status) }}
                                </span>
                            </td>


<td>
    <div class="d-flex flex-wrap gap-2">

        {{-- View --}}
        <a href="{{ route('admin.vendors.show', $vendor) }}"
           class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-eye me-1"></i> View
        </a>

        {{-- Pending --}}
        @if($vendor->status === 'pending')

            <form method="POST"
                  action="{{ route('admin.vendors.approve', $vendor) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    Approve
                </button>
            </form>

            <button type="button"
                    class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectModal{{ $vendor->id }}">
                Reject
            </button>

        {{-- Approved --}}
        @elseif($vendor->status === 'approved')

            <button type="button"
                    class="btn btn-sm btn-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#suspendModal{{ $vendor->id }}">
                <i class="fa-solid fa-ban me-1"></i> Suspend
            </button>

        {{-- Suspended --}}
        @elseif($vendor->status === 'suspended')

            <form method="POST"
                  action="{{ route('admin.vendors.reactivate', $vendor) }}">
                @csrf
                <button type="submit"
                        class="btn btn-sm btn-success"
                        onclick="return confirm('Reactivate this vendor?')">
                    Reactivate
                </button>
            </form>

        @endif
    </div>
</td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fa-solid fa-store fs-1 text-muted mb-3"></i>

                                <h5>No vendors found</h5>

                                <p class="text-muted mb-0">
                                    Try changing your search or status filter.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $vendors->links() }}
        </div>




    </div>

</div>

{{-- Vendor Modals - Outside the responsive table --}}

@foreach($vendors as $vendor)

    {{-- Reject Modal --}}
    @if($vendor->status === 'pending')
        <div class="modal fade"
             id="rejectModal{{ $vendor->id }}"
             tabindex="-1"
             aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form method="POST"
                          action="{{ route('admin.vendors.reject', $vendor) }}">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">Reject Application</h5>
                            <button type="button" class="btn-close"
                                    data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <p>Rejection reason for
                                <strong>{{ $vendor->shop_name }}</strong>
                            </p>

                            <textarea name="admin_remarks"
                                      class="form-control"
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="Enter rejection reason..."
                                      required></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                    data-bs-dismiss="modal">Cancel</button>

                            <button type="submit" class="btn btn-danger">
                                Confirm Rejection
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

    {{-- Suspend Modal --}}
    @if($vendor->status === 'approved')
        <div class="modal fade"
             id="suspendModal{{ $vendor->id }}"
             tabindex="-1"
             aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form method="POST"
                          action="{{ route('admin.vendors.suspend', $vendor) }}">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">Suspend Vendor</h5>
                            <button type="button" class="btn-close"
                                    data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <p>
                                Suspend <strong>{{ $vendor->shop_name }}</strong>?
                            </p>

                            <label class="form-label">
                                Suspension Reason
                            </label>

                            <textarea name="admin_remarks"
                                      class="form-control"
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="Enter suspension reason..."
                                      required></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                    data-bs-dismiss="modal">Cancel</button>

                            <button type="submit" class="btn btn-warning">
                                Confirm Suspension
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

@endforeach




@endsection
