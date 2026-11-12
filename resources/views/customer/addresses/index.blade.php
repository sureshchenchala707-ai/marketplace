
@extends('layouts.app')

@section('title', 'My Addresses - ESS MARKETPLACE')

@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">My Addresses</h2>
            <p class="text-muted mb-0">Manage your delivery addresses.</p>
        </div>

        <a href="{{ route('account.addresses.create') }}"
           class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Address
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        @forelse($addresses as $address)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-primary-subtle text-primary">
                                {{ ucfirst($address->address_type) }}
                            </span>

                            @if($address->is_default)
                                <span class="badge bg-success">Default</span>
                            @endif
                        </div>

                        <h5 class="fw-bold">{{ $address->name }}</h5>

                        <p class="text-muted mb-2">
                            {{ $address->phone }}
                        </p>

                        <p class="mb-3">
                            {{ $address->address_line1 }}<br>

                            @if($address->address_line2)
                                {{ $address->address_line2 }}<br>
                            @endif

                            {{ $address->city }}, {{ $address->state }}<br>
                            {{ $address->pincode }}, {{ $address->country }}
                        </p>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('account.addresses.edit', $address) }}"
                               class="btn btn-sm btn-outline-primary">
                                Edit
                            </a>

                            @unless($address->is_default)
                                <form method="POST"
                                      action="{{ route('account.addresses.default', $address) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success">
                                        Set Default
                                    </button>
                                </form>
                            @endunless

                            <form method="POST"
                                  action="{{ route('account.addresses.destroy', $address) }}"
                                  onsubmit="return confirm('Delete this address?')">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-outline-danger">
                                    Delete
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                    <h5>No saved addresses</h5>
                    <p class="text-muted">Add your first delivery address.</p>

                    <a href="{{ route('account.addresses.create') }}"
                       class="btn btn-primary mx-auto">
                        Add Address
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $addresses->links() }}
    </div>

</div>
@endsection
