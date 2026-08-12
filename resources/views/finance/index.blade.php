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
    <!-- Artist Payout Receiving Account Settings -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-building-columns"></i> Receiving Payout Account</h3>
                @if($payoutAccount)
                    <span class="badge badge-approved"><i class="fa-solid fa-circle-check"></i> Account Configured</span>
                @else
                    <span class="badge badge-pending"><i class="fa-solid fa-triangle-exclamation"></i> Not Configured</span>
                @endif
            </div>
            <div class="card-body">
                <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1.25rem; line-height: 1.5;">
                    Set your permanent banking or digital account details to receive your accumulated royalties after payout calculations and platform processing.
                </p>

                @if($payoutAccount)
                    <div style="background-color: var(--bg-input); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <div>
                                <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--primary); font-weight: 700; letter-spacing: 0.05em;">
                                    {{ strtoupper(str_replace('_', ' ', $payoutAccount['payout_method'] ?? 'bank_transfer')) }}
                                </span>
                                <h4 style="margin: 0.25rem 0 0; color: var(--text-primary); font-size: 1.05rem;">
                                    {{ $payoutAccount['account_name'] ?? 'Account Holder' }}
                                </h4>
                            </div>
                            <span class="badge badge-approved" style="font-size: 0.7rem;">Active Target</span>
                        </div>

                        <div class="grid-cols-2" style="gap: 0.75rem; font-size: 0.85rem; margin-bottom: 0.5rem;">
                            <div>
                                <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Account / Phone / IBAN:</span>
                                <strong style="font-family: monospace; color: var(--text-primary);">
                                    {{ $payoutAccount['account_number'] ?? 'N/A' }}
                                </strong>
                            </div>
                            @if(!empty($payoutAccount['bank_name']))
                                <div>
                                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Bank / Institution:</span>
                                    <span style="color: var(--text-primary);">{{ $payoutAccount['bank_name'] }}</span>
                                </div>
                            @endif
                            @if(!empty($payoutAccount['routing_number']))
                                <div>
                                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Routing / SWIFT:</span>
                                    <span style="font-family: monospace; color: var(--text-primary);">{{ $payoutAccount['routing_number'] }}</span>
                                </div>
                            @endif
                            @if(!empty($payoutAccount['mobile_network']))
                                <div>
                                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Mobile Network:</span>
                                    <span style="color: var(--text-primary);">{{ $payoutAccount['mobile_network'] }}</span>
                                </div>
                            @endif
                            @if(!empty($payoutAccount['paypal_email']))
                                <div>
                                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">PayPal Email:</span>
                                    <span style="color: var(--text-primary);">{{ $payoutAccount['paypal_email'] }}</span>
                                </div>
                            @endif
                        </div>

                        <div style="font-size: 0.75rem; color: var(--text-muted); border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.5rem; margin-top: 0.5rem;">
                            Last Updated: {{ $payoutAccount['updated_at'] ?? 'Recently' }}
                        </div>
                    </div>
                @endif

                <!-- Form to update payout receiving account -->
                <details {{ empty($payoutAccount) ? 'open' : '' }} style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <summary style="cursor: pointer; font-weight: 600; font-size: 0.9rem; color: var(--primary); margin-bottom: 1rem;">
                        <i class="fa-solid fa-pen-to-square"></i> {{ $payoutAccount ? 'Update Payout Receiving Account' : 'Set Up Payout Receiving Account' }}
                    </summary>

                    <form action="{{ route('finance.payout_account') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="setting_payout_method">Payout Receiving Method</label>
                            <select id="setting_payout_method" name="payout_method" class="form-select" onchange="adjustPayoutMethodFields(this.value)" required>
                                <option value="bank_transfer" {{ ($payoutAccount['payout_method'] ?? '') === 'bank_transfer' ? 'selected' : '' }}>Direct Bank Transfer (Wire / ACH / SEPA)</option>
                                <option value="mobile_money" {{ ($payoutAccount['payout_method'] ?? '') === 'mobile_money' ? 'selected' : '' }}>Mobile Money (M-Pesa, Airtel, MTN)</option>
                                <option value="paypal" {{ ($payoutAccount['payout_method'] ?? '') === 'paypal' ? 'selected' : '' }}>PayPal Account</option>
                                <option value="bank_card" {{ ($payoutAccount['payout_method'] ?? '') === 'bank_card' ? 'selected' : '' }}>Debit / Credit Card Payout</option>
                            </select>
                        </div>

                        <div class="grid-cols-2">
                            <div class="form-group">
                                <label class="form-label" for="setting_account_name">Account Holder Full Name</label>
                                <input type="text" id="setting_account_name" name="account_name" class="form-input" placeholder="e.g. John Doe / Label Records LLC" value="{{ old('account_name', $payoutAccount['account_name'] ?? Auth::user()->name) }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="setting_account_number" id="settingAccountNumLabel">Account Number / IBAN / Phone</label>
                                <input type="text" id="setting_account_number" name="account_number" class="form-input" placeholder="e.g. 123456789012" value="{{ old('account_number', $payoutAccount['account_number'] ?? '') }}" required>
                            </div>
                        </div>

                        <div id="bankFieldsGroup" style="{{ ($payoutAccount['payout_method'] ?? 'bank_transfer') === 'bank_transfer' ? 'display: grid;' : 'display: none;' }}" class="grid-cols-2">
                            <div class="form-group">
                                <label class="form-label" for="setting_bank_name">Bank Name</label>
                                <input type="text" id="setting_bank_name" name="bank_name" class="form-input" placeholder="e.g. Barclays, Citibank, Chase" value="{{ old('bank_name', $payoutAccount['bank_name'] ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="setting_routing_number">SWIFT / BIC / Routing Code</label>
                                <input type="text" id="setting_routing_number" name="routing_number" class="form-input" placeholder="e.g. CHASUS33XXX" value="{{ old('routing_number', $payoutAccount['routing_number'] ?? '') }}">
                            </div>
                        </div>

                        <div id="mobileFieldsGroup" style="{{ ($payoutAccount['payout_method'] ?? '') === 'mobile_money' ? 'display: block;' : 'display: none;' }}" class="form-group">
                            <label class="form-label" for="setting_mobile_network">Mobile Money Operator / Network</label>
                            <input type="text" id="setting_mobile_network" name="mobile_network" class="form-input" placeholder="e.g. M-Pesa, MTN Mobile Money, Airtel Money" value="{{ old('mobile_network', $payoutAccount['mobile_network'] ?? '') }}">
                        </div>

                        <div id="paypalFieldsGroup" style="{{ ($payoutAccount['payout_method'] ?? '') === 'paypal' ? 'display: block;' : 'display: none;' }}" class="form-group">
                            <label class="form-label" for="setting_paypal_email">Registered PayPal Email</label>
                            <input type="email" id="setting_paypal_email" name="paypal_email" class="form-input" placeholder="e.g. payment@artistname.com" value="{{ old('paypal_email', $payoutAccount['paypal_email'] ?? Auth::user()->email) }}">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-floppy-disk"></i> Save Payout Receiving Account
                        </button>
                    </form>
                </details>
            </div>
        </div>
    </div>

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
                    @if($payoutAccount)
                        <div style="margin-bottom: 1.25rem; padding: 0.85rem 1rem; border-radius: var(--radius-md); background-color: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; justify-content: space-between;">
                            <div style="font-size: 0.85rem;">
                                <i class="fa-solid fa-check-circle" style="color: var(--success); margin-right: 0.35rem;"></i>
                                <span style="color: var(--text-primary); font-weight: 600;">Saved Target:</span> 
                                <span style="color: var(--text-secondary);">{{ $payoutAccount['account_name'] }} ({{ $payoutAccount['account_number'] }})</span>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="populateSavedPayoutAccount()" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                                <i class="fa-solid fa-bolt"></i> Auto-Fill
                            </button>
                        </div>
                    @endif

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
                                <option value="bank_transfer" {{ ($payoutAccount['payout_method'] ?? '') === 'bank_transfer' ? 'selected' : '' }}>Direct Bank Transfer</option>
                                <option value="mobile_money" {{ ($payoutAccount['payout_method'] ?? '') === 'mobile_money' ? 'selected' : '' }}>Mobile Money Account</option>
                                <option value="paypal" {{ ($payoutAccount['payout_method'] ?? '') === 'paypal' ? 'selected' : '' }}>PayPal Email</option>
                                <option value="bank_card" {{ ($payoutAccount['payout_method'] ?? '') === 'bank_card' ? 'selected' : '' }}>Visa/Mastercard Direct Payout</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="payment_details" id="detailsLabel">Payment Destination Details</label>
                            @php
                                $defaultDetails = '';
                                if ($payoutAccount) {
                                    $defaultDetails = "Holder: " . ($payoutAccount['account_name'] ?? '') . "\nAccount/Phone: " . ($payoutAccount['account_number'] ?? '');
                                    if (!empty($payoutAccount['bank_name'])) $defaultDetails .= "\nBank: " . $payoutAccount['bank_name'];
                                    if (!empty($payoutAccount['routing_number'])) $defaultDetails .= "\nSWIFT/Routing: " . $payoutAccount['routing_number'];
                                    if (!empty($payoutAccount['mobile_network'])) $defaultDetails .= "\nNetwork: " . $payoutAccount['mobile_network'];
                                    if (!empty($payoutAccount['paypal_email'])) $defaultDetails .= "\nPayPal: " . $payoutAccount['paypal_email'];
                                }
                            @endphp
                            <textarea id="payment_details" name="payment_details" class="form-textarea" rows="3" placeholder="Provide bank name, swift code, routing number, account number, and account holder name." required>{{ old('payment_details', $defaultDetails) }}</textarea>
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
</div>

<div class="grid-cols-1" style="margin-top: 1.5rem;">
    <!-- Subscription & Upgrade Management Form -->
    <div>
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <h3 class="card-title"><i class="fa-solid fa-crown"></i> Subscription & Distribution Account Upgrades</h3>
                @if($subscription && $subscription->status === 'active' && $subscription->ends_at->isAfter(now()))
                    <span class="badge badge-approved"><i class="fa-solid fa-circle-check"></i> {{ $subscription->plan_name }} (Expires: {{ $subscription->ends_at->format('M d, Y') }})</span>
                @else
                    <span class="badge badge-pending"><i class="fa-solid fa-tag"></i> Standard Free Plan</span>
                @endif
            </div>
            <div class="card-body">
                @if($subscription && $subscription->status === 'active' && $subscription->ends_at->isAfter(now()))
                    <div style="text-align: center; color: var(--success); padding: 1.5rem 0;">
                        <i class="fa-solid fa-circle-check" style="font-size: 3.5rem; margin-bottom: 0.75rem;"></i>
                        <h4>Active {{ $subscription->plan_name }} Subscription</h4>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.25rem;">
                            You have unlimited music distributions to Spotify, Apple Music, TikTok, and all DSPs.
                        </p>
                        <div style="margin-top: 1rem; font-size: 0.85rem; color: var(--text-muted);">
                            Active Period Ends: <strong>{{ $subscription->ends_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                @else
                    <form action="{{ route('finance.subscribe') }}" method="POST">
                        @csrf
                        <div class="grid-cols-2">
                            <div class="form-group">
                                <label class="form-label" for="finance_plan_name">Select Upgrade Plan</label>
                                <select id="finance_plan_name" name="plan_name" class="form-select" onchange="adjustFinancePlan(this.value)" required>
                                    <option value="Artist Premium">Artist Premium Plan - $49.99/year</option>
                                    <option value="Record Label Pro">Record Label Pro Plan - $149.99/year</option>
                                    <option value="VIP Lifetime">VIP Lifetime Unlimited - $299.99 one-time</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="finance_payment_method">Payment Method to Distribution System</label>
                                <select id="finance_payment_method" name="payment_method" class="form-select" onchange="adjustFinancePaymentMethod(this.value)" required>
                                    <option value="card">Credit / Debit Card (Instant Processing)</option>
                                    <option value="bank_transfer">Direct Bank Transfer (Platform Settlement Account)</option>
                                    <option value="mobile_money">Mobile Money (M-Pesa / Airtel / MTN)</option>
                                    <option value="paypal">PayPal Treasury Email</option>
                                </select>
                            </div>
                        </div>

                        <!-- Platform Settlement Info Box -->
                        <div id="financeSettlementBox" style="display: none; background: rgba(15, 23, 42, 0.7); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.5rem;">
                            <div style="color: var(--primary); font-weight: 700; font-size: 0.9rem; margin-bottom: 0.5rem;">
                                <i class="fa-solid fa-vault"></i> Platform Treasury Receiving Account:
                            </div>
                            <div class="grid-cols-2" style="gap: 0.75rem; font-size: 0.85rem; background: var(--bg-card); padding: 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                <div id="finSettlementBank">
                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Bank:</span>
                                    <strong>{{ $platformAccount['bank_name'] ?? 'JPMorgan Chase Bank, N.A.' }}</strong>
                                </div>
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Account / Till:</span>
                                    <strong style="color: var(--success); font-family: monospace;">{{ $platformAccount['account_number'] ?? '987654321098' }}</strong>
                                </div>
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Beneficiary:</span>
                                    <span>{{ $platformAccount['account_name'] ?? 'CollegeMusic Global Distribution LLC' }}</span>
                                </div>
                                <div id="finSettlementRouting">
                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">SWIFT / Routing:</span>
                                    <span style="font-family: monospace;">{{ $platformAccount['routing_swift'] ?? 'CHASUS33XXX' }}</span>
                                </div>
                                <div id="finSettlementMobile" style="display: none; grid-column: span 2;">
                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Operator / Network:</span>
                                    <span>{{ $platformAccount['mobile_network'] ?? 'Safaricom M-Pesa' }}</span>
                                </div>
                                <div id="finSettlementPaypal" style="display: none; grid-column: span 2;">
                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">PayPal Email:</span>
                                    <span>{{ $platformAccount['paypal_email'] ?? 'finance@collegemusic.io' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Inputs -->
                        <div id="financeCardGroup">
                            <div class="grid-cols-2">
                                <div class="form-group">
                                    <label class="form-label" for="fin_card_name">Name on Card</label>
                                    <input type="text" id="fin_card_name" name="card_name" class="form-input" placeholder="e.g. John Doe" value="{{ Auth::user()->name }}">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="fin_card_number">Card Number</label>
                                    <input type="text" id="fin_card_number" name="card_number" class="form-input" placeholder="xxxx xxxx xxxx xxxx" maxlength="19">
                                </div>
                            </div>

                            <div class="grid-cols-2" style="margin-bottom: 1.5rem;">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" for="fin_card_expiry">Expiry Date</label>
                                    <input type="text" id="fin_card_expiry" name="card_expiry" class="form-input" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" for="fin_card_cvc">CVC</label>
                                    <input type="text" id="fin_card_cvc" name="card_cvc" class="form-input" placeholder="123" maxlength="4">
                                </div>
                            </div>
                        </div>

                        <!-- Manual Reference Inputs -->
                        <div id="financeManualGroup" style="display: none; margin-bottom: 1.5rem;">
                            <div class="grid-cols-2">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" for="fin_trans_ref" id="finTransRefLabel">Transaction Reference / Slip Code</label>
                                    <input type="text" id="fin_trans_ref" name="transaction_reference" class="form-input" placeholder="e.g. Wire Reference / M-Pesa Code">
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" for="fin_payer_phone">Sender Phone / Email</label>
                                    <input type="text" id="fin_payer_phone" name="payer_phone" class="form-input" placeholder="e.g. +254712345678" value="{{ Auth::user()->phone ?? Auth::user()->email }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" id="financeSubmitBtn">
                            <i class="fa-solid fa-credit-card"></i> Pay $49.99 & Upgrade
                        </button>
                    </form>
                @endif
            </div>
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
    function adjustPayoutMethodFields(method) {
        var bankGroup = document.getElementById('bankFieldsGroup');
        var mobileGroup = document.getElementById('mobileFieldsGroup');
        var paypalGroup = document.getElementById('paypalFieldsGroup');
        var numLabel = document.getElementById('settingAccountNumLabel');

        if (bankGroup) bankGroup.style.display = (method === 'bank_transfer') ? 'grid' : 'none';
        if (mobileGroup) mobileGroup.style.display = (method === 'mobile_money') ? 'block' : 'none';
        if (paypalGroup) paypalGroup.style.display = (method === 'paypal') ? 'block' : 'none';

        if (numLabel) {
            if (method === 'bank_transfer') numLabel.textContent = 'Bank Account Number / IBAN';
            else if (method === 'mobile_money') numLabel.textContent = 'Subscriber Mobile Phone Number';
            else if (method === 'paypal') numLabel.textContent = 'Account ID / Reference';
            else if (method === 'bank_card') numLabel.textContent = '16-Digit Card Number';
        }
    }

    function populateSavedPayoutAccount() {
        @if($payoutAccount)
            var savedMethod = "{{ $payoutAccount['payout_method'] ?? 'bank_transfer' }}";
            var methodSelect = document.getElementById('payment_method');
            if (methodSelect) {
                methodSelect.value = savedMethod;
                toggleDetailsPlaceholder();
            }

            var textarea = document.getElementById('payment_details');
            if (textarea) {
                var details = "Holder: {{ $payoutAccount['account_name'] ?? '' }}\nAccount/Phone: {{ $payoutAccount['account_number'] ?? '' }}";
                @if(!empty($payoutAccount['bank_name']))
                    details += "\nBank: {{ $payoutAccount['bank_name'] }}";
                @endif
                @if(!empty($payoutAccount['routing_number']))
                    details += "\nSWIFT/Routing: {{ $payoutAccount['routing_number'] }}";
                @endif
                @if(!empty($payoutAccount['mobile_network']))
                    details += "\nNetwork: {{ $payoutAccount['mobile_network'] }}";
                @endif
                @if(!empty($payoutAccount['paypal_email']))
                    details += "\nPayPal: {{ $payoutAccount['paypal_email'] }}";
                @endif
                textarea.value = details;
                textarea.focus();
            }
        @endif
    }

    function toggleDetailsPlaceholder() {
        var method = document.getElementById('payment_method');
        if (!method) return;
        var methodVal = method.value;
        var label = document.getElementById('detailsLabel');
        var textarea = document.getElementById('payment_details');

        if (methodVal === 'bank_transfer') {
            label.textContent = 'Bank Transfer Details';
            textarea.placeholder = 'Provide bank name, swift code, routing number, account number, and account holder name.';
        } else if (methodVal === 'paypal') {
            label.textContent = 'PayPal Email Address';
            textarea.placeholder = 'Provide your primary registered PayPal email address.';
        } else if (methodVal === 'mobile_money') {
            label.textContent = 'Mobile Money Details';
            textarea.placeholder = 'Provide mobile carrier network, subscriber phone number, and account name.';
        } else if (methodVal === 'bank_card') {
            label.textContent = 'Visa / Mastercard Payout Details';
            textarea.placeholder = 'Provide full cardholder name, card number, and expiration date.';
        }
    }

    var financeSelectedPrice = 49.99;
    function adjustFinancePlan(plan) {
        if (plan === 'Artist Premium') financeSelectedPrice = 49.99;
        else if (plan === 'Record Label Pro') financeSelectedPrice = 149.99;
        else if (plan === 'VIP Lifetime') financeSelectedPrice = 299.99;
        updateFinanceSubmitBtn();
    }

    function adjustFinancePaymentMethod(method) {
        var cardGroup = document.getElementById('financeCardGroup');
        var manualGroup = document.getElementById('financeManualGroup');
        var settlementBox = document.getElementById('financeSettlementBox');

        var bankDiv = document.getElementById('finSettlementBank');
        var routingDiv = document.getElementById('finSettlementRouting');
        var mobileDiv = document.getElementById('finSettlementMobile');
        var paypalDiv = document.getElementById('finSettlementPaypal');
        var transLabel = document.getElementById('finTransRefLabel');

        var cardName = document.getElementById('fin_card_name');
        var cardNum = document.getElementById('fin_card_number');
        var cardExp = document.getElementById('fin_card_expiry');
        var cardCvc = document.getElementById('fin_card_cvc');
        var transRef = document.getElementById('fin_trans_ref');

        if (method === 'card') {
            if (cardGroup) cardGroup.style.display = 'block';
            if (manualGroup) manualGroup.style.display = 'none';
            if (settlementBox) settlementBox.style.display = 'none';

            if (cardName) cardName.required = true;
            if (cardNum) cardNum.required = true;
            if (cardExp) cardExp.required = true;
            if (cardCvc) cardCvc.required = true;
            if (transRef) transRef.required = false;
        } else {
            if (cardGroup) cardGroup.style.display = 'none';
            if (manualGroup) manualGroup.style.display = 'block';
            if (settlementBox) settlementBox.style.display = 'block';

            if (cardName) cardName.required = false;
            if (cardNum) cardNum.required = false;
            if (cardExp) cardExp.required = false;
            if (cardCvc) cardCvc.required = false;
            if (transRef) transRef.required = true;

            if (method === 'bank_transfer') {
                if (bankDiv) bankDiv.style.display = 'block';
                if (routingDiv) routingDiv.style.display = 'block';
                if (mobileDiv) mobileDiv.style.display = 'none';
                if (paypalDiv) paypalDiv.style.display = 'none';
                if (transLabel) transLabel.textContent = 'Bank Wire Reference / Deposit Code';
            } else if (method === 'mobile_money') {
                if (bankDiv) bankDiv.style.display = 'none';
                if (routingDiv) routingDiv.style.display = 'none';
                if (mobileDiv) mobileDiv.style.display = 'block';
                if (paypalDiv) paypalDiv.style.display = 'none';
                if (transLabel) transLabel.textContent = 'M-Pesa / Mobile Money Confirmation Reference';
            } else if (method === 'paypal') {
                if (bankDiv) bankDiv.style.display = 'none';
                if (routingDiv) routingDiv.style.display = 'none';
                if (mobileDiv) mobileDiv.style.display = 'none';
                if (paypalDiv) paypalDiv.style.display = 'block';
                if (transLabel) transLabel.textContent = 'PayPal Transaction ID / Payer Email';
            }
        }
        updateFinanceSubmitBtn();
    }

    function updateFinanceSubmitBtn() {
        var btn = document.getElementById('financeSubmitBtn');
        var methodSelect = document.getElementById('finance_payment_method');
        var method = methodSelect ? methodSelect.value : 'card';
        var actionText = (method === 'card') ? 'Pay' : 'Confirm & Submit';
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-credit-card"></i> ' + actionText + ' $' + financeSelectedPrice.toFixed(2) + ' & Upgrade';
        }
    }
</script>
@endsection
