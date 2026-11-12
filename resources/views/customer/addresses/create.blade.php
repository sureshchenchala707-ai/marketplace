@extends('layouts.app')

@section('title', 'Add Address - ESS MARKETPLACE')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width:850px">
        <div class="card-body p-4 p-md-5">

            <h3 class="fw-bold mb-2">Add Delivery Address</h3>
            <p class="text-muted mb-4">Enter your complete delivery details.</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('account.addresses.store') }}">
                @include('customer.addresses._form')
            </form>

        </div>
    </div>
</div>
@endsection