
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h3>Edit Offer</h3>
        <p class="text-muted">Update {{ $offer->name }}.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.offers.update', $offer) }}" method="POST">
                @method('PUT')
                @include('admin.offers._form')
            </form>
        </div>
    </div>
</div>
@endsection
