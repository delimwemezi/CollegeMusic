@extends('layouts.app')

@section('title', 'Finance & Royalties')
@section('header_title', 'Finance Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Finance & Earnings</h1>
        <p class="page-subtitle">Track your accumulated streaming royalties, request payouts, and manage billing</p>
    </div>
</div>

<!-- Balance Cards -->
<div class="grid-cols-3">
    <!-- Total Royalties Earned -->
    <div class="stat-card" style="grid-column: span 1;">
        <div>
            <div class="stat-title">Total Royalty Earnings</div>
            <div class="stat-value" style="color: var(--primary);">${{ number_format($totalEarned, 4) }}</div>
            <div class="stat-change" style="color: var(--text-muted);">Accumulated from streaming platforms</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
    </div>

    <!-- Available Payout Balance -->
    <div class="stat-card" style="grid-column: span 1; border-color: rgba(16, 185, 129, 0.2);">
        <div>
            <div class="stat-title">Available Payout Balance</div>
            <div class="stat-value" style="color: var(--success);">${{ number_format($availableBalance, 2) }}</div>
            <div class="stat-change" style="color: var(--text-muted);">Eligible for immediate withdrawal</div>
        </div>
        <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fa-solid fa-wallet"></i></div>
    </div>

    <!-- Active Subscription Card -->
    <div class="stat-card" style="grid-column: span 1;">
        <div>
            <div class="stat-title">Active Billing Plan</div>
            @if($subscription && $subscription->plan_name === 'Premium' && $subscription->status === 'active' && $subscription->ends_at->isAfter(now()))
                <div class="stat-value" style="color: var(--warning);"><i class="fa-solid fa-crown"></i> Premium Plan</div>
                <div class="stat-change" style="color: var(--text-secondary);">Renews: {{ $subscription->ends_at->format('Y-m-d') }}</div>
            @else
                <div class="stat-value" style="color: var(--text-secondary);">Free Tier</div>
                <div class="stat-change" style="color: var(--text-muted);">Pay-per-release distribution</div>
            @endif
        </div>
        <div class="stat-icon" style="background-color: rgba(245, 158, 11, 0.1); color: var(--warning);"><i class="fa-solid fa-gem"></i></div>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Payout Request Form -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-circle-dollar-to-slot"></i> Submit Royalty Payout Request</h3>
            </div>
            <div class="card-body">
                @if($availableBalance < 10.00)
                    <div style="text-align: center; padding: 2rem 1rem; color: var(--text-secondary);">
                        <i class="fa-solid fa-circle-exclamation" style="font-size: 2.5rem; color: var(--warning); margin-bottom: 0.75rem;"></i>
                        <h4>Threshold Not Met</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                            Minimum payout threshold is <strong>$10.00</strong>. You currently have ${{ number_format($availableBalance, 2) }} available.
                        </p>
                    </div>
                @else
                    <form action="{{ route('finance.withdraw') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="amount">Withdrawal Amount ($)</label>
                            <input type="number" id="amount" name="amount" class="form-input" min="10" max="{{ $availableBalance }}" step="0.01" value="{{ old('amount', $availableBalance) }}" required>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Max available: ${{ number_format($availableBalance, 2) }}</small>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="payment_method">Select Payout Method</label>
                            <select id="payment_method" name="payment_method" class="form-select" onchange="toggleDetailsPlaceholder()" required>
                                <option value="bank_transfer">Direct Bank Transfer</option>
                                <option value="paypal">PayPal Email</option>
                                <option value="mobile_money">Mobile Money Account</option>
                                <option value="bank_card">Visa/Mastercard Direct Payout</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="payment_details" id="detailsLabel">Bank Transfer Details</label>
                            <textarea id="payment_details" name="payment_details" class="form-textarea" rows="3" placeholder="Provide bank name, swift code, routing number, account number, and account holder name." required>{{ old('payment_details') }}</textarea>
                            @error('payment_details')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-paper-plane"></i> Submit Payout Request
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Subscription Management Form -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-circle-check"></i> Subscription Management</h3>
            </div>
            <div class="card-body">
                @if($subscription && $subscription->plan_name === 'Premium' && $subscription->status === 'active' && $subscription->ends_at->isAfter(now()))
                    <div style="text-align: center; color: var(--success); padding: 1.5rem 0;">
                        <i class="fa-solid fa-circle-check" style="font-size: 3.5rem; margin-bottom: 0.75rem;"></i>
                        <h4>Active Premium Subscription</h4>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.25rem;">
                            You have unlimited free music distributions to Spotify, Apple, and all DSPs.
                        </p>
                        <div style="margin-top: 1rem; font-size: 0.85rem; color: var(--text-muted);">
                            Expires: <strong>{{ $subscription->ends_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                @else
                    <div style="margin-bottom: 1.5rem; padding: 1rem; border-radius: var(--radius-md); background-color: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.15);">
                        <h4 style="color: var(--accent); margin-bottom: 0.25rem;"><i class="fa-solid fa-crown"></i> Upgrade to Premium Plan</h4>
                        <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.4; margin-top: 0.25rem;">
                            For just <strong>$49.99/year</strong>, unlock unlimited catalog uploads, bypass release distribution fees, and get detailed location stats.
                        </p>
                    </div>

                    <form action="{{ route('finance.subscribe') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Plan Selection</label>
                            <input type="text" class="form-input" value="Premium Plan - $49.99/year" disabled style="background-color: var(--bg-card);">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="card_name">Name on Card</label>
                            <input type="text" id="card_name" name="card_name" class="form-input" placeholder="e.g. John Doe" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="card_number">Card Number</label>
                            <input type="text" id="card_number" name="card_number" class="form-input" placeholder="xxxx xxxx xxxx xxxx" maxlength="16" required>
                        </div>

                        <div class="grid-cols-2" style="margin-bottom: 1.5rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" for="card_expiry">Expiry Date</label>
                                <input type="text" id="card_expiry" name="card_expiry" class="form-input" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" for="card_cvc">CVC</label>
                                <input type="text" id="card_cvc" name="card_cvc" class="form-input" placeholder="123" maxlength="3" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-credit-card"></i> Pay $49.99 & Subscribe
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Payout Logs -->
    <div style="grid-column: span 2;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Withdrawal Requests & Payout Logs</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($withdrawals->isEmpty())
                    <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No previous withdrawal requests found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Receipt Number</th>
                                    <th>Method</th>
                                    <th>Amount Requested</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $withdrawal)
                                    <tr>
                                        <td style="font-family: monospace; font-weight: 600;">{{ $withdrawal->invoice_number }}</td>
                                        <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $withdrawal->payment_method) }}</td>
                                        <td style="font-weight: bold; color: var(--primary);">${{ number_format($withdrawal->amount, 2) }}</td>
                                        <td>{{ $withdrawal->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($withdrawal->status === 'pending')
                                                <span class="badge badge-pending">Review Pending</span>
                                            @elseif($withdrawal->status === 'completed')
                                                <span class="badge badge-approved">Completed</span>
                                            @else
                                                <span class="badge badge-rejected" title="Reason: {{ $withdrawal->rejection_reason }}">Rejected</span>
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            @if($withdrawal->status === 'completed')
                                                <a href="{{ route('finance.withdrawal.invoice', $withdrawal->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-file-pdf"></i> Receipt
                                                </a>
                                            @else
                                                <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">Unavailable</span>
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

    <!-- Payments Logs -->
    <div style="grid-column: span 2;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-receipt"></i> Payment & Invoice Logs</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($payments->isEmpty())
                    <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No inbound payments found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Transaction Ref</th>
                                    <th>Amount Paid</th>
                                    <th>Type</th>
                                    <th>Billing Date</th>
                                    <th style="text-align: right;">Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td style="font-family: monospace; font-weight: 600;">{{ $payment->invoice_number }}</td>
                                        <td style="font-family: monospace; font-size: 0.85rem;">{{ $payment->transaction_reference }}</td>
                                        <td style="font-weight: bold; color: var(--success);">${{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            @if($payment->release)
                                                Distribution: {{ $payment->release->title }}
                                            @elseif($payment->subscription)
                                                Premium Subscription
                                            @else
                                                System Fees
                                            @endif
                                        </td>
                                        <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('finance.invoice', $payment->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                                <i class="fa-solid fa-file-pdf"></i> View Invoice
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
    function toggleDetailsPlaceholder() {
        var method = document.getElementById('payment_method').value;
        var label = document.getElementById('detailsLabel');
        var textarea = document.getElementById('payment_details');

        if (method === 'bank_transfer') {
            label.textContent = 'Bank Transfer Details';
            textarea.placeholder = 'Provide bank name, swift code, routing number, account number, and account holder name.';
        } else if (method === 'paypal') {
            label.textContent = 'PayPal Email Address';
            textarea.placeholder = 'Provide your primary registered PayPal email address.';
        } else if (method === 'mobile_money') {
            label.textContent = 'Mobile Money Details';
            textarea.placeholder = 'Provide mobile carrier network, subscriber phone number, and account name.';
        } else if (method === 'bank_card') {
            label.textContent = 'Visa / Mastercard Payout Details';
            textarea.placeholder = 'Provide full cardholder name, card number, and expiration date.';
        }
    }
</script>
@endsection
