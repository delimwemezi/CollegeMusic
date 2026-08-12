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
    <!-- Platform Payout Settlement Account -->
    <div style="grid-column: span 2;">
        <div class="card" style="border-color: rgba(99, 102, 241, 0.3); box-shadow: 0 4px 20px rgba(99, 102, 241, 0.08);">
            <div class="card-header" style="background: rgba(99, 102, 241, 0.04); display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fa-solid fa-vault" style="font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <h3 class="card-title" style="margin: 0;">Platform Receiving & Settlement Account</h3>
                        <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted);">
                            Designated platform treasury account to receive company shares and fees after artist/label payout calculations
                        </p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="badge badge-approved" style="background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3);">
                        <i class="fa-solid fa-shield-halved"></i> High-Security Protected
                    </span>
                    <button type="button" class="btn btn-primary btn-sm" onclick="togglePlatformAccountForm()">
                        <i class="fa-solid fa-pen-to-square"></i> Update Account
                    </button>
                </div>
            </div>
            
            <div class="card-body">
                <div class="grid-cols-3" style="gap: 1.25rem; margin-bottom: 1.5rem; background: var(--bg-input); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; font-weight: 600;">Settlement Method</span>
                        <div style="font-weight: 700; color: var(--primary); font-size: 1rem; margin-top: 0.25rem;">
                            {{ strtoupper(str_replace('_', ' ', $platformPayoutAccount['payout_method'] ?? 'bank_transfer')) }}
                        </div>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; font-weight: 600;">Account Holder Name</span>
                        <div style="font-weight: 600; color: var(--text-primary); font-size: 1rem; margin-top: 0.25rem;">
                            {{ $platformPayoutAccount['account_name'] ?? 'Not Configured' }}
                        </div>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; font-weight: 600;">Account / IBAN / Phone</span>
                        <div style="font-family: monospace; font-weight: 700; color: var(--success); font-size: 1.05rem; margin-top: 0.25rem;">
                            {{ $platformPayoutAccount['account_number'] ?? 'N/A' }}
                        </div>
                    </div>

                    @if(!empty($platformPayoutAccount['bank_name']))
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; font-weight: 600;">Banking Institution</span>
                            <div style="color: var(--text-primary); font-size: 0.9rem; margin-top: 0.25rem;">
                                {{ $platformPayoutAccount['bank_name'] }}
                            </div>
                        </div>
                    @endif

                    @if(!empty($platformPayoutAccount['routing_swift']))
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; font-weight: 600;">SWIFT / BIC / Routing Code</span>
                            <div style="font-family: monospace; color: var(--text-primary); font-size: 0.9rem; margin-top: 0.25rem;">
                                {{ $platformPayoutAccount['routing_swift'] }}
                            </div>
                        </div>
                    @endif

                    <div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; font-weight: 600;">Currency</span>
                        <div style="font-weight: 600; color: var(--accent); font-size: 0.9rem; margin-top: 0.25rem;">
                            {{ $platformPayoutAccount['currency'] ?? 'USD' }}
                        </div>
                    </div>

                    @if(!empty($platformPayoutAccount['paypal_email']))
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; font-weight: 600;">PayPal Treasury Email</span>
                            <div style="color: var(--text-primary); font-size: 0.9rem; margin-top: 0.25rem;">
                                {{ $platformPayoutAccount['paypal_email'] }}
                            </div>
                        </div>
                    @endif

                    @if(!empty($platformPayoutAccount['notes']))
                        <div style="grid-column: span 3;">
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; font-weight: 600;">Operational Notes</span>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                {{ $platformPayoutAccount['notes'] }}
                            </div>
                        </div>
                    @endif
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: var(--text-muted); border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.75rem;">
                    <div>
                        <i class="fa-solid fa-user-shield" style="color: var(--primary); margin-right: 0.25rem;"></i>
                        Last Verified & Updated by: <strong>{{ $platformPayoutAccount['updated_by_name'] ?? 'Administrator' }}</strong> ({{ $platformPayoutAccount['updated_by_email'] ?? 'admin@collegemusic.io' }})
                    </div>
                    <div>
                        <i class="fa-solid fa-clock" style="margin-right: 0.25rem;"></i>
                        Timestamp: <strong>{{ $platformPayoutAccount['updated_at'] ?? 'N/A' }}</strong> &bull; IP: <strong>{{ $platformPayoutAccount['updated_ip'] ?? '127.0.0.1' }}</strong>
                    </div>
                </div>

                <!-- Update Platform Account Modal / Collapsible Form -->
                <div id="platformAccountUpdateBox" style="display: none; margin-top: 1.5rem; background: rgba(15, 23, 42, 0.7); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; animation: fadeIn 0.3s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <h4 style="margin: 0; color: var(--primary); font-size: 1.1rem;">
                            <i class="fa-solid fa-key"></i> High-Security Platform Account Configuration
                        </h4>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="togglePlatformAccountForm()" style="padding: 0.25rem 0.5rem;">
                            <i class="fa-solid fa-xmark"></i> Close
                        </button>
                    </div>

                    <div class="alert alert-warning" style="margin-bottom: 1.25rem; font-size: 0.85rem;">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem;"></i>
                        <div>
                            <strong>Security Protocol Active:</strong> Any update to the platform receiving account will trigger an immediate security broadcast notification to <strong>ALL system administrators</strong> with your identity, timestamp, IP address, and complete account details.
                        </div>
                    </div>

                    <form action="{{ route('admin.platform_payout_account') }}" method="POST">
                        @csrf
                        <div class="grid-cols-2">
                            <div class="form-group">
                                <label class="form-label" for="admin_payout_method">Receiving Method</label>
                                <select id="admin_payout_method" name="payout_method" class="form-select" onchange="adjustAdminPayoutMethod(this.value)" required>
                                    <option value="bank_transfer" {{ ($platformPayoutAccount['payout_method'] ?? '') === 'bank_transfer' ? 'selected' : '' }}>Direct Bank Wire / ACH Transfer</option>
                                    <option value="mobile_money" {{ ($platformPayoutAccount['payout_method'] ?? '') === 'mobile_money' ? 'selected' : '' }}>Mobile Money Merchant / Paybill / Till</option>
                                    <option value="paypal" {{ ($platformPayoutAccount['payout_method'] ?? '') === 'paypal' ? 'selected' : '' }}>PayPal Treasury Account</option>
                                    <option value="bank_card" {{ ($platformPayoutAccount['payout_method'] ?? '') === 'bank_card' ? 'selected' : '' }}>Direct Merchant Card Settlement</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="admin_currency">Settlement Currency</label>
                                <select id="admin_currency" name="currency" class="form-select" required>
                                    <option value="USD" {{ ($platformPayoutAccount['currency'] ?? 'USD') === 'USD' ? 'selected' : '' }}>USD - United States Dollar ($)</option>
                                    <option value="EUR" {{ ($platformPayoutAccount['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR - Euro (€)</option>
                                    <option value="GBP" {{ ($platformPayoutAccount['currency'] ?? '') === 'GBP' ? 'selected' : '' }}>GBP - British Pound (£)</option>
                                    <option value="KES" {{ ($platformPayoutAccount['currency'] ?? '') === 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                                    <option value="NGN" {{ ($platformPayoutAccount['currency'] ?? '') === 'NGN' ? 'selected' : '' }}>NGN - Nigerian Naira (₦)</option>
                                    <option value="ZAR" {{ ($platformPayoutAccount['currency'] ?? '') === 'ZAR' ? 'selected' : '' }}>ZAR - South African Rand</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid-cols-2">
                            <div class="form-group">
                                <label class="form-label" for="admin_account_name">Official Account Name / Beneficiary</label>
                                <input type="text" id="admin_account_name" name="account_name" class="form-input" placeholder="e.g. CollegeMusic Global Distribution LLC" value="{{ old('account_name', $platformPayoutAccount['account_name'] ?? '') }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="admin_account_number" id="adminAccountNumLabel">Account Number / IBAN / Merchant Till</label>
                                <input type="text" id="admin_account_number" name="account_number" class="form-input" placeholder="e.g. 987654321098" value="{{ old('account_number', $platformPayoutAccount['account_number'] ?? '') }}" required>
                            </div>
                        </div>

                        <div id="adminBankGroup" style="{{ ($platformPayoutAccount['payout_method'] ?? 'bank_transfer') === 'bank_transfer' ? 'display: grid;' : 'display: none;' }}" class="grid-cols-2">
                            <div class="form-group">
                                <label class="form-label" for="admin_bank_name">Bank / Clearing House</label>
                                <input type="text" id="admin_bank_name" name="bank_name" class="form-input" placeholder="e.g. JPMorgan Chase Bank, N.A." value="{{ old('bank_name', $platformPayoutAccount['bank_name'] ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="admin_routing_swift">SWIFT / BIC / Routing Code</label>
                                <input type="text" id="admin_routing_swift" name="routing_swift" class="form-input" placeholder="e.g. CHASUS33XXX" value="{{ old('routing_swift', $platformPayoutAccount['routing_swift'] ?? '') }}">
                            </div>
                        </div>

                        <div id="adminMobileGroup" style="{{ ($platformPayoutAccount['payout_method'] ?? '') === 'mobile_money' ? 'display: block;' : 'display: none;' }}" class="form-group">
                            <label class="form-label" for="admin_mobile_network">Carrier / Merchant Network</label>
                            <input type="text" id="admin_mobile_network" name="mobile_network" class="form-input" placeholder="e.g. Safaricom M-Pesa Buy Goods / Paybill" value="{{ old('mobile_network', $platformPayoutAccount['mobile_network'] ?? '') }}">
                        </div>

                        <div id="adminPaypalGroup" style="{{ ($platformPayoutAccount['payout_method'] ?? '') === 'paypal' ? 'display: block;' : 'display: none;' }}" class="form-group">
                            <label class="form-label" for="admin_paypal_email">PayPal Treasury Email</label>
                            <input type="email" id="admin_paypal_email" name="paypal_email" class="form-input" placeholder="e.g. payments@collegemusic.io" value="{{ old('paypal_email', $platformPayoutAccount['paypal_email'] ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="admin_notes">Administrative Notes & Payout Routing Instructions</label>
                            <textarea id="admin_notes" name="notes" class="form-textarea" rows="2" placeholder="Internal settlement guidelines...">{{ old('notes', $platformPayoutAccount['notes'] ?? '') }}</textarea>
                        </div>

                        <div class="form-group" style="margin-top: 1.5rem; padding: 1.25rem; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-md);">
                            <label class="form-label" for="admin_password" style="color: var(--danger); font-weight: 700;">
                                <i class="fa-solid fa-lock"></i> Verify Administrator Password (Required)
                            </label>
                            <input type="password" id="admin_password" name="admin_password" class="form-input" placeholder="Enter your current admin login password to confirm" required>
                            <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.35rem;">
                                This security challenge ensures only authorized administrators can alter platform receiving accounts.
                            </small>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                            <button type="button" class="btn btn-secondary" onclick="togglePlatformAccountForm()">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-shield-check"></i> Authorize & Update Platform Account
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Admin Security Notifications & Audit History for Platform Payout Account -->
                @if(isset($payoutSecurityLogs) && $payoutSecurityLogs->isNotEmpty())
                    <details style="margin-top: 1.25rem; border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
                        <summary style="cursor: pointer; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary);">
                            <i class="fa-solid fa-bullhorn" style="color: var(--accent); margin-right: 0.25rem;"></i> View Broadcasted Admin Security Alerts ({{ $payoutSecurityLogs->count() }} notifications)
                        </summary>
                        <div class="table-responsive" style="margin-top: 0.75rem;">
                            <table class="table" style="font-size: 0.8rem;">
                                <thead>
                                    <tr>
                                        <th>Admin Recipient</th>
                                        <th>Security Alert Broadcast</th>
                                        <th>IP Address</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payoutSecurityLogs as $secLog)
                                        <tr>
                                            <td>
                                                <strong>{{ $secLog->user ? $secLog->user->name : 'Administrator' }}</strong>
                                                <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $secLog->user ? $secLog->user->email : '' }}</div>
                                            </td>
                                            <td style="color: var(--text-primary); font-size: 0.8rem;">
                                                <i class="fa-solid fa-bell" style="color: var(--warning); margin-right: 0.25rem;"></i>
                                                {{ $secLog->description }}
                                            </td>
                                            <td style="font-family: monospace;">{{ $secLog->ip_address ?? '127.0.0.1' }}</td>
                                            <td style="white-space: nowrap;">{{ $secLog->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endif
            </div>
        </div>
    </div>

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

    function togglePlatformAccountForm() {
        var box = document.getElementById('platformAccountUpdateBox');
        if (box) {
            if (box.style.display === 'none' || box.style.display === '') {
                box.style.display = 'block';
                box.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                box.style.display = 'none';
            }
        }
    }

    function adjustAdminPayoutMethod(method) {
        var bankGroup = document.getElementById('adminBankGroup');
        var mobileGroup = document.getElementById('adminMobileGroup');
        var paypalGroup = document.getElementById('adminPaypalGroup');
        var numLabel = document.getElementById('adminAccountNumLabel');

        if (bankGroup) bankGroup.style.display = (method === 'bank_transfer') ? 'grid' : 'none';
        if (mobileGroup) mobileGroup.style.display = (method === 'mobile_money') ? 'block' : 'none';
        if (paypalGroup) paypalGroup.style.display = (method === 'paypal') ? 'block' : 'none';

        if (numLabel) {
            if (method === 'bank_transfer') numLabel.textContent = 'Account Number / IBAN';
            else if (method === 'mobile_money') numLabel.textContent = 'Merchant Till / Paybill / Account';
            else if (method === 'paypal') numLabel.textContent = 'PayPal Account ID / Reference';
            else if (method === 'bank_card') numLabel.textContent = 'Merchant Settlement Card Number';
        }
    }
</script>
@endsection
