@extends('layouts.app')

@section('title', 'Payment & Payout Control')
@section('header_title', 'Payment Processing')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Payment & Withdrawal Management</h1>
        <p class="page-subtitle">Process outgoing artist royalty withdrawals and monitor incoming subscription fees</p>
    </div>
    <div>
        <a href="{{ route('admin') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Console
        </a>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Payout Requests -->
    <div style="grid-column: span 2;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-hand-holding-dollar"></i> Artist Royalty Withdrawal Requests</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($withdrawals->isEmpty())
                    <p style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No withdrawal requests found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Artist User</th>
                                    <th>Requested Amount</th>
                                    <th>Method</th>
                                    <th>Payout Details</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Review Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $withdrawal)
                                    <tr>
                                        <td>
                                            <div style="font-weight: bold;">{{ $withdrawal->user->name }}</div>
                                            <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ $withdrawal->user->email }}</span>
                                        </td>
                                        <td style="font-weight: 700; color: var(--primary);">${{ number_format($withdrawal->amount, 2) }}</td>
                                        <td><span style="text-transform: capitalize;">{{ str_replace('_', ' ', $withdrawal->payment_method) }}</span></td>
                                        <td style="font-family: monospace; font-size: 0.8rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $withdrawal->payment_details }}">
                                            {{ $withdrawal->payment_details }}
                                        </td>
                                        <td>{{ $withdrawal->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($withdrawal->status === 'pending')
                                                <span class="badge badge-pending">Pending Approval</span>
                                            @elseif($withdrawal->status === 'completed')
                                                <span class="badge badge-approved">Paid</span>
                                            @else
                                                <span class="badge badge-rejected" title="Reason: {{ $withdrawal->rejection_reason }}">Rejected</span>
                                            @endif
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            @if($withdrawal->status === 'pending')
                                                <div style="display: inline-flex; gap: 0.4rem; justify-content: flex-end;">
                                                    <form action="{{ route('admin.withdrawals.status', $withdrawal->id) }}" method="POST" style="margin: 0;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fa-solid fa-circle-check"></i> Process Payout
                                                        </button>
                                                    </form>
                                                    
                                                    <button type="button" class="btn btn-danger btn-sm" style="background-color: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.2); color: #fca5a5;" onclick="showWithdrawalRejection('{{ $withdrawal->id }}')">
                                                        <i class="fa-solid fa-circle-xmark"></i> Reject
                                                    </button>
                                                </div>

                                                <!-- Rejection Form Input -->
                                                <div id="withdrawalRejection_{{ $withdrawal->id }}" style="display: none; margin-top: 0.5rem; text-align: left; background-color: var(--bg-input); padding: 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); animation: fadeIn 0.2s ease;">
                                                    <form action="{{ route('admin.withdrawals.status', $withdrawal->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="rejected">
                                                        <label class="form-label" style="font-size: 0.75rem;">Rejection Reason</label>
                                                        <input type="text" name="rejection_reason" class="form-input" style="font-size: 0.8rem; padding: 0.4rem;" placeholder="e.g. Account number incorrect or verification failed." required>
                                                        <div style="display: flex; gap: 0.25rem; justify-content: flex-end; margin-top: 0.5rem;">
                                                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="hideWithdrawalRejection('{{ $withdrawal->id }}')">Cancel</button>
                                                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Reject Request</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @elseif($withdrawal->status === 'completed')
                                                <a href="{{ route('finance.withdrawal.invoice', $withdrawal->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-file-invoice-dollar"></i> View Receipt
                                                </a>
                                            @else
                                                <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Platform Income History -->
    <div style="grid-column: span 2;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-money-bill-wave"></i> Inbound Income Logs (Sales & Subscriptions)</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($payments->isEmpty())
                    <p style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No incoming transactions recorded.</p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Billing User</th>
                                    <th>Item Paid For</th>
                                    <th>Amount Paid</th>
                                    <th>Ref ID</th>
                                    <th>Date</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td style="font-weight: 600; font-family: monospace;">{{ $payment->invoice_number }}</td>
                                        <td>
                                            <div style="font-weight: 500;">{{ $payment->user->name }}</div>
                                            <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ $payment->user->email }}</span>
                                        </td>
                                        <td>
                                            @if($payment->release)
                                                Release distribution: <strong>{{ $payment->release->title }}</strong>
                                            @elseif($payment->subscription)
                                                Premium Subscription plan: <strong>{{ $payment->subscription->plan_name }}</strong>
                                            @else
                                                Platform Fees
                                            @endif
                                        </td>
                                        <td style="font-weight: bold; color: var(--success);">${{ number_format($payment->amount, 2) }}</td>
                                        <td style="font-family: monospace; font-size: 0.85rem;">{{ $payment->transaction_reference }}</td>
                                        <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('finance.invoice', $payment->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                                <i class="fa-solid fa-file-pdf"></i> Receipt Invoice
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showWithdrawalRejection(id) {
        document.getElementById('withdrawalRejection_' + id).style.display = 'block';
    }
    function hideWithdrawalRejection(id) {
        document.getElementById('withdrawalRejection_' + id).style.display = 'none';
    }
</script>
@endsection
