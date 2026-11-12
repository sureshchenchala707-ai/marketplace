
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h3>Create Coupon</h3>
        <p class="text-muted">Add a new promotional coupon.</p>
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
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @include('admin.coupons._form')
            </form>
        </div>
    </div>
</div>
@endsection
