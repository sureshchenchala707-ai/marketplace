
@extends('layouts.app')

@section('title', 'Payment Management | ESS MARKETPLACE')

@section('content')
<div class="container-fluid py-3">

    <div class="mb-4">
        <h2 class="fw-bold">Payment Management</h2>
        <p class="text-muted">Monitor customer payments and transaction records.</p>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Total Payments', $totalPayments, 'fa-credit-card', 'primary'],
            ['Successful', $successfulPayments, 'fa-circle-check', 'success'],
            ['Pending', $pendingPayments, 'fa-clock', 'warning'],
            ['Failed', $failedPayments, 'fa-circle-xmark', 'danger']
        ] as $stat)
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted mb-2">{{ $stat[0] }}</div>
                                <h3 class="fw-bold mb-0">{{ number_format($stat[1]) }}</h3>
                            </div>
                            <i class="fa-solid {{ $stat[2] }} fs-2 text-{{ $stat[3] }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <form method="GET" action="{{ route('admin.payments.index') }}"
                  class="row g-3 mb-4">

                <div class="col-lg-5">
                    <input type="text" name="search"
                           class="form-control"
                           placeholder="Search payment ID or order number..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['pending', 'paid', 'failed', 'refunded'] as $status)
                            <option value="{{ $status }}"
                                @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="gateway" class="form-select">
                        <option value="">All Gateways</option>
                        <option value="razorpay" @selected(request('gateway') === 'razorpay')>Razorpay</option>
                        <option value="cod" @selected(request('gateway') === 'cod')>Cash on Delivery</option>
                    </select>
                </div>

                <div class="col-lg-3 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.payments.index') }}"
                       class="btn btn-outline-secondary">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Payment ID</th>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Gateway</th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $payment->payment_number ?? '—' }}
                                </td>

                                <td>
                                    {{ $payment->order?->order_number ?? '—' }}
                                </td>

                                <td>
                                    {{ $payment->order?->user?->name ?? '—' }}
                                </td>

                                <td>
                                    {{ strtoupper($payment->gateway ?? '—') }}
                                </td>

                                <td>
                                    <small>{{ $payment->gateway_payment_id ?? '—' }}</small>
                                </td>

                                <td class="fw-bold">
                                    ₹{{ number_format((float) $payment->amount, 2) }}
                                </td>

                                <td>
                                    @php
                                        $colors = [
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'failed' => 'danger',
                                            'refunded' => 'info',
                                        ];
                                    @endphp

                                    <span class="badge text-bg-{{ $colors[$payment->status] ?? 'secondary' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $payment->created_at?->format('d M Y, h:i A') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No payment records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $payments->links() }}

        </div>
    </div>
</div>
@endsection
