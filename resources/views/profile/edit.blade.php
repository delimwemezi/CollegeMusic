@extends('layouts.app')

@section('title', 'Account Settings')
@section('header_title', 'Account Settings')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Settings & Profile</h1>
        <p class="page-subtitle">Manage your personal information, notification preferences, and security</p>
    </div>
</div>

<div class="grid-cols-1" style="margin-bottom: 1.5rem;">
    <!-- Account Upgrade & Distribution Tier Plan Card -->
    <div class="card" style="border-color: rgba(99, 102, 241, 0.35); box-shadow: 0 8px 30px rgba(99, 102, 241, 0.1);">
        <div class="card-header" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.05)); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.25rem;">
                    <i class="fa-solid fa-crown"></i>
                </div>
                <div>
                    <h3 class="card-title" style="margin: 0;">Distribution Account Upgrade & Plan Tier</h3>
                    <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted);">
                        Upgrade your artist or record label account to unlock unlimited DSP distribution, priority store deliveries, and split royalty tools.
                    </p>
                </div>
            </div>
            <div>
                @if($subscription && $subscription->status === 'active' && $subscription->ends_at->isAfter(now()))
                    <span class="badge badge-approved" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;">
                        <i class="fa-solid fa-circle-check"></i> Active Plan: {{ $subscription->plan_name }} (Expires: {{ $subscription->ends_at->format('M d, Y') }})
                    </span>
                @else
                    <span class="badge badge-pending" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;">
                        <i class="fa-solid fa-tag"></i> Standard Free Plan (Pay Per Release)
                    </span>
                @endif
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('profile.upgrade') }}" method="POST" id="upgradeAccountForm">
                @csrf

                <!-- Plan Selection Cards -->
                <label class="form-label" style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.75rem;">
                    1. Choose Your Target Distribution Plan:
                </label>

                <div class="grid-cols-3" style="gap: 1rem; margin-bottom: 1.5rem;">
                    <!-- Artist Premium -->
                    <label style="cursor: pointer; display: block; height: 100%;">
                        <input type="radio" name="plan_name" value="Artist Premium" class="plan-radio" checked onchange="updateSelectedPlan('Artist Premium', 49.99, 'Artist Premium Distribution Plan ($49.99/year)')" style="display: none;">
                        <div class="plan-card active-plan-card" id="plan_card_artist" style="border: 2px solid var(--primary); border-radius: var(--radius-md); padding: 1.25rem; background: rgba(99, 102, 241, 0.05); height: 100%; transition: all 0.2s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <span class="badge badge-approved" style="font-size: 0.7rem;">Solo Artists</span>
                                <span style="font-size: 1.35rem; font-weight: 800; color: var(--primary);">$49.99<small style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;">/yr</small></span>
                            </div>
                            <h4 style="margin: 0 0 0.5rem 0; font-size: 1.05rem; color: var(--text-primary);">Artist Premium Plan</h4>
                            <ul style="font-size: 0.8rem; color: var(--text-secondary); line-height: 1.6; padding-left: 1.2rem; margin: 0;">
                                <li>Unlimited song & album uploads</li>
                                <li>Keep 100% of all streaming royalties</li>
                                <li>Delivery to Spotify, Apple, TikTok, 150+ DSPs</li>
                                <li>Comprehensive analytics & reporting</li>
                            </ul>
                        </div>
                    </label>

                    <!-- Record Label Pro -->
                    <label style="cursor: pointer; display: block; height: 100%;">
                        <input type="radio" name="plan_name" value="Record Label Pro" class="plan-radio" onchange="updateSelectedPlan('Record Label Pro', 149.99, 'Record Label Pro Distribution Plan ($149.99/year)')" style="display: none;">
                        <div class="plan-card" id="plan_card_label" style="border: 2px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; background: var(--bg-input); height: 100%; transition: all 0.2s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <span class="badge" style="background: rgba(168, 85, 247, 0.15); color: #c084fc; font-size: 0.7rem;">Labels & Managers</span>
                                <span style="font-size: 1.35rem; font-weight: 800; color: #a855f7;">$149.99<small style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;">/yr</small></span>
                            </div>
                            <h4 style="margin: 0 0 0.5rem 0; font-size: 1.05rem; color: var(--text-primary);">Record Label Pro Plan</h4>
                            <ul style="font-size: 0.8rem; color: var(--text-secondary); line-height: 1.6; padding-left: 1.2rem; margin: 0;">
                                <li>Unlimited artists under your record label roster</li>
                                <li>Automatic revenue splitting for collaborators</li>
                                <li>Priority 24-hour DSP ingestion & review</li>
                                <li>Dedicated label account manager</li>
                            </ul>
                        </div>
                    </label>

                    <!-- VIP Lifetime -->
                    <label style="cursor: pointer; display: block; height: 100%;">
                        <input type="radio" name="plan_name" value="VIP Lifetime" class="plan-radio" onchange="updateSelectedPlan('VIP Lifetime', 299.99, 'VIP Lifetime Distribution Plan ($299.99 one-time)')" style="display: none;">
                        <div class="plan-card" id="plan_card_vip" style="border: 2px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; background: var(--bg-input); height: 100%; transition: all 0.2s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <span class="badge" style="background: rgba(234, 179, 8, 0.15); color: #facc15; font-size: 0.7rem;">Permanent Lifetime</span>
                                <span style="font-size: 1.35rem; font-weight: 800; color: #eab308;">$299.99<small style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;"> one-time</small></span>
                            </div>
                            <h4 style="margin: 0 0 0.5rem 0; font-size: 1.05rem; color: var(--text-primary);">VIP Lifetime Unlimited</h4>
                            <ul style="font-size: 0.8rem; color: var(--text-secondary); line-height: 1.6; padding-left: 1.2rem; margin: 0;">
                                <li>Never pay distribution fees again</li>
                                <li>Permanent lifetime catalog uploads</li>
                                <li>Instant automated releases approval</li>
                                <li>Zero annual renewal fees forever</li>
                            </ul>
                        </div>
                    </label>
                </div>

                <!-- Payment Methods Section -->
                <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <label class="form-label" style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0;">
                            2. Select Payment Method to the Distribution System:
                        </label>
                        <span id="selectedPlanBadge" style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">
                            Selected: Artist Premium Distribution Plan ($49.99/year)
                        </span>
                    </div>

                    <div class="form-group">
                        <select id="upgrade_payment_method" name="payment_method" class="form-select" onchange="switchUpgradePaymentMethod(this.value)" required>
                            <option value="card">Credit / Debit Card (Visa, Mastercard, Amex - Instant Activation)</option>
                            <option value="bank_transfer">Direct Bank Transfer / Wire (To Platform Settlement Account)</option>
                            <option value="mobile_money">Mobile Money (M-Pesa, Airtel, MTN Paybill/Till)</option>
                            <option value="paypal">PayPal Treasury Account</option>
                        </select>
                    </div>

                    <!-- Platform Settlement Target Info Box (Set by Admin) -->
                    <div id="platformSettlementInfoBox" style="display: none; background: rgba(15, 23, 42, 0.7); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; color: var(--primary); font-weight: 700; font-size: 0.9rem;">
                            <i class="fa-solid fa-vault"></i> Official Platform Settlement & Receiving Account:
                        </div>
                        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.4;">
                            Please send your payment to the official distribution platform treasury account below, then input your transaction confirmation code or reference ID to complete activation.
                        </p>

                        <div class="grid-cols-2" style="gap: 0.75rem; font-size: 0.85rem; background: var(--bg-card); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                            <div id="settlementBankDiv">
                                <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Bank / Clearing House:</span>
                                <strong style="color: var(--text-primary);">{{ $platformAccount['bank_name'] ?? 'JPMorgan Chase Bank, N.A.' }}</strong>
                            </div>

                            <div id="settlementAccountDiv">
                                <span style="font-size: 0.75rem; color: var(--text-muted); display: block;" id="settlementAccLabel">Account / IBAN / Till:</span>
                                <strong style="font-family: monospace; color: var(--success); font-size: 0.95rem;">{{ $platformAccount['account_number'] ?? '987654321098' }}</strong>
                            </div>

                            <div id="settlementNameDiv">
                                <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Beneficiary Account Name:</span>
                                <span style="color: var(--text-primary); font-weight: 600;">{{ $platformAccount['account_name'] ?? 'CollegeMusic Global Distribution LLC' }}</span>
                            </div>

                            <div id="settlementRoutingDiv">
                                <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">SWIFT / BIC / Routing:</span>
                                <span style="font-family: monospace; color: var(--text-primary);">{{ $platformAccount['routing_swift'] ?? 'CHASUS33XXX' }}</span>
                            </div>

                            <div id="settlementMobileDiv" style="display: none; grid-column: span 2;">
                                <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Mobile Money Operator:</span>
                                <span style="color: var(--text-primary); font-weight: 600;">{{ $platformAccount['mobile_network'] ?? 'Safaricom M-Pesa' }}</span>
                            </div>

                            <div id="settlementPaypalDiv" style="display: none; grid-column: span 2;">
                                <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">PayPal Treasury Email:</span>
                                <span style="color: var(--text-primary); font-weight: 600;">{{ $platformAccount['paypal_email'] ?? 'finance@collegemusic.io' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Form Inputs -->
                    <div id="cardInputsGroup">
                        <div class="grid-cols-2">
                            <div class="form-group">
                                <label class="form-label" for="upgrade_card_name">Name on Card</label>
                                <input type="text" id="upgrade_card_name" name="card_name" class="form-input" placeholder="e.g. John Doe" value="{{ Auth::user()->name }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="upgrade_card_number">Card Number</label>
                                <input type="text" id="upgrade_card_number" name="card_number" class="form-input" placeholder="4532 •••• •••• ••••" maxlength="19">
                            </div>
                        </div>

                        <div class="grid-cols-2">
                            <div class="form-group">
                                <label class="form-label" for="upgrade_card_expiry">Expiration (MM/YY)</label>
                                <input type="text" id="upgrade_card_expiry" name="card_expiry" class="form-input" placeholder="MM/YY" maxlength="5">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="upgrade_card_cvc">Security Code (CVC)</label>
                                <input type="text" id="upgrade_card_cvc" name="card_cvc" class="form-input" placeholder="123" maxlength="4">
                            </div>
                        </div>
                    </div>

                    <!-- Manual Reference Input (Bank Wire / Mobile Money / PayPal) -->
                    <div id="manualRefGroup" style="display: none;">
                        <div class="grid-cols-2">
                            <div class="form-group">
                                <label class="form-label" for="upgrade_trans_ref" id="transRefLabel">Transaction Reference / Slip Number</label>
                                <input type="text" id="upgrade_trans_ref" name="transaction_reference" class="form-input" placeholder="e.g. WT987654321 or M-Pesa Ref QJH123456">
                                <small style="color: var(--text-muted); font-size: 0.75rem;">Enter the receipt or transaction code received upon sending payment to the platform account.</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="upgrade_payer_phone">Sender Phone / Email Reference</label>
                                <input type="text" id="upgrade_payer_phone" name="payer_phone" class="form-input" placeholder="e.g. +254712345678 or payer@email.com" value="{{ Auth::user()->phone ?? Auth::user()->email }}">
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary" id="upgradeSubmitBtn" style="padding: 0.75rem 2rem; font-size: 1rem; font-weight: 700;">
                            <i class="fa-solid fa-circle-check"></i> Pay $49.99 & Upgrade Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Profile & Notifications Card -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-user-pen"></i> Personal Profile</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem;">
                        @if($user->artist && $user->artist->profile_picture)
                            <img src="{{ asset('storage/' . $user->artist->profile_picture) }}" alt="Avatar" style="width: 80px; height: 80px; border-radius: var(--radius-full); object-fit: cover; border: 2px solid var(--primary);">
                        @else
                            <div style="width: 80px; height: 80px; border-radius: var(--radius-full); background-color: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 1.5rem;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                        
                        <div>
                            <label class="form-label" for="profile_picture">Change Profile Picture</label>
                            <input type="file" id="profile_picture" name="profile_picture" class="form-input">
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Supported formats: JPEG, PNG, JPG (Max 2MB)</small>
                            @error('profile_picture')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($user->isArtist())
                        <div class="form-group">
                            <label class="form-label" for="bio">Artist Biography</label>
                            <textarea id="bio" name="bio" class="form-textarea" rows="4" placeholder="Tell the world about yourself...">{{ old('bio', $user->artist ? $user->artist->bio : '') }}</textarea>
                            @error('bio')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Save Profile
                    </button>
                </form>
            </div>
        </div>

        <!-- Notification Settings Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-bell"></i> Notification Preferences</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.settings') }}" method="POST">
                    @csrf
                    
                    @php
                        $prefs = $user->notification_preferences ?? ['email' => true, 'sms' => false, 'approvals' => true, 'royalties' => true];
                    @endphp

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <input type="checkbox" id="pref_email" name="pref_email" class="form-checkbox" {{ ($prefs['email'] ?? false) ? 'checked' : '' }}>
                        <label for="pref_email" class="form-checkbox-label">Receive email digests and alerts</label>
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <input type="checkbox" id="pref_sms" name="pref_sms" class="form-checkbox" {{ ($prefs['sms'] ?? false) ? 'checked' : '' }}>
                        <label for="pref_sms" class="form-checkbox-label">Receive SMS notifications on mobile</label>
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <input type="checkbox" id="pref_approvals" name="pref_approvals" class="form-checkbox" {{ ($prefs['approvals'] ?? false) ? 'checked' : '' }}>
                        <label for="pref_approvals" class="form-checkbox-label">Notify when music is reviewed (Approved/Rejected)</label>
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <input type="checkbox" id="pref_royalties" name="pref_royalties" class="form-checkbox" {{ ($prefs['royalties'] ?? false) ? 'checked' : '' }}>
                        <label for="pref_royalties" class="form-checkbox-label">Notify when royalty payments are processed</label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-sliders"></i> Update Preferences
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Security & Account Operations -->
    <div>
        <!-- Password Reset Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-lock"></i> Change Password</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-input" placeholder="••••••••" required>
                        @error('current_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">New Password</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-shield"></i> Change Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Deactivation Card -->
        <div class="card" style="border-color: rgba(239, 68, 68, 0.2);">
            <div class="card-header" style="background-color: rgba(239, 68, 68, 0.05); border-bottom: 1px solid rgba(239, 68, 68, 0.15);">
                <h3 class="card-title" style="color: var(--danger);"><i class="fa-solid fa-user-slash"></i> Deactivate Account</h3>
            </div>
            <div class="card-body">
                <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.6;">
                    Deactivating your account will suspend all active distributions and catalog streaming listings. You can reactivate your account at any time by signing back in with your credentials.
                </p>
                <form action="{{ route('profile.deactivate') }}" method="POST" onsubmit="return confirm('Are you absolutely sure you want to deactivate your account?');">
                    @csrf
                    
                    <div class="form-group" style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <input type="checkbox" id="confirm_deactivate" name="confirm_deactivate" class="form-checkbox" required>
                        <label for="confirm_deactivate" class="form-checkbox-label" style="font-size: 0.85rem; line-height: 1.4;">
                            I confirm that I want to deactivate my music distribution profile and take down all catalogs.
                        </label>
                        @error('confirm_deactivate')
                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-power-off"></i> Deactivate My Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var currentSelectedPrice = 49.99;
    var currentSelectedPlanName = 'Artist Premium';

    function updateSelectedPlan(planName, price, labelText) {
        currentSelectedPrice = price;
        currentSelectedPlanName = planName;

        var cardArtist = document.getElementById('plan_card_artist');
        var cardLabel = document.getElementById('plan_card_label');
        var cardVip = document.getElementById('plan_card_vip');

        if (cardArtist) {
            cardArtist.style.border = (planName === 'Artist Premium') ? '2px solid var(--primary)' : '2px solid var(--border-color)';
            cardArtist.style.background = (planName === 'Artist Premium') ? 'rgba(99, 102, 241, 0.05)' : 'var(--bg-input)';
        }

        if (cardLabel) {
            cardLabel.style.border = (planName === 'Record Label Pro') ? '2px solid #a855f7' : '2px solid var(--border-color)';
            cardLabel.style.background = (planName === 'Record Label Pro') ? 'rgba(168, 85, 247, 0.05)' : 'var(--bg-input)';
        }

        if (cardVip) {
            cardVip.style.border = (planName === 'VIP Lifetime') ? '2px solid #eab308' : '2px solid var(--border-color)';
            cardVip.style.background = (planName === 'VIP Lifetime') ? 'rgba(234, 179, 8, 0.05)' : 'var(--bg-input)';
        }

        var badge = document.getElementById('selectedPlanBadge');
        if (badge) badge.textContent = 'Selected: ' + labelText;

        updateSubmitBtnText();
    }

    function switchUpgradePaymentMethod(method) {
        var cardGroup = document.getElementById('cardInputsGroup');
        var manualGroup = document.getElementById('manualRefGroup');
        var settlementInfo = document.getElementById('platformSettlementInfoBox');
        
        var bankDiv = document.getElementById('settlementBankDiv');
        var accDiv = document.getElementById('settlementAccountDiv');
        var nameDiv = document.getElementById('settlementNameDiv');
        var routingDiv = document.getElementById('settlementRoutingDiv');
        var mobileDiv = document.getElementById('settlementMobileDiv');
        var paypalDiv = document.getElementById('settlementPaypalDiv');
        var transRefLabel = document.getElementById('transRefLabel');

        var cardName = document.getElementById('upgrade_card_name');
        var cardNum = document.getElementById('upgrade_card_number');
        var cardExp = document.getElementById('upgrade_card_expiry');
        var cardCvc = document.getElementById('upgrade_card_cvc');
        var transRef = document.getElementById('upgrade_trans_ref');

        if (method === 'card') {
            if (cardGroup) cardGroup.style.display = 'block';
            if (manualGroup) manualGroup.style.display = 'none';
            if (settlementInfo) settlementInfo.style.display = 'none';

            if (cardName) cardName.required = true;
            if (cardNum) cardNum.required = true;
            if (cardExp) cardExp.required = true;
            if (cardCvc) cardCvc.required = true;
            if (transRef) transRef.required = false;
        } else {
            if (cardGroup) cardGroup.style.display = 'none';
            if (manualGroup) manualGroup.style.display = 'block';
            if (settlementInfo) settlementInfo.style.display = 'block';

            if (cardName) cardName.required = false;
            if (cardNum) cardNum.required = false;
            if (cardExp) cardExp.required = false;
            if (cardCvc) cardCvc.required = false;
            if (transRef) transRef.required = true;

            if (method === 'bank_transfer') {
                if (bankDiv) bankDiv.style.display = 'block';
                if (accDiv) accDiv.style.display = 'block';
                if (nameDiv) nameDiv.style.display = 'block';
                if (routingDiv) routingDiv.style.display = 'block';
                if (mobileDiv) mobileDiv.style.display = 'none';
                if (paypalDiv) paypalDiv.style.display = 'none';
                if (transRefLabel) transRefLabel.textContent = 'Bank Transfer Reference / Deposit Slip Number';
            } else if (method === 'mobile_money') {
                if (bankDiv) bankDiv.style.display = 'none';
                if (accDiv) accDiv.style.display = 'block';
                if (nameDiv) nameDiv.style.display = 'block';
                if (routingDiv) routingDiv.style.display = 'none';
                if (mobileDiv) mobileDiv.style.display = 'block';
                if (paypalDiv) paypalDiv.style.display = 'none';
                if (transRefLabel) transRefLabel.textContent = 'M-Pesa / Mobile Money Confirmation Code (e.g. QJH987654)';
            } else if (method === 'paypal') {
                if (bankDiv) bankDiv.style.display = 'none';
                if (accDiv) accDiv.style.display = 'none';
                if (nameDiv) nameDiv.style.display = 'none';
                if (routingDiv) routingDiv.style.display = 'none';
                if (mobileDiv) mobileDiv.style.display = 'none';
                if (paypalDiv) paypalDiv.style.display = 'block';
                if (transRefLabel) transRefLabel.textContent = 'PayPal Transaction ID / Payer Email';
            }
        }
        updateSubmitBtnText();
    }

    function updateSubmitBtnText() {
        var btn = document.getElementById('upgradeSubmitBtn');
        var methodSelect = document.getElementById('upgrade_payment_method');
        var method = methodSelect ? methodSelect.value : 'card';
        var methodText = (method === 'card') ? 'Pay' : 'Confirm & Submit';
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + methodText + ' $' + currentSelectedPrice.toFixed(2) + ' & Upgrade Account';
        }
    }
</script>
@endsection
