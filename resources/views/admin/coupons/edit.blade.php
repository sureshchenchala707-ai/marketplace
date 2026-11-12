
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h3>Edit Coupon</h3>
        <p class="text-muted">Update {{ $coupon->code }} coupon details.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            Please correct the errors below.
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                @method('PUT')
                @include('admin.coupons._form')
            </form>
        </div>
    </div>
</div>
@endsection
