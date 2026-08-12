<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Withdrawal;
use App\Models\Subscription;
use App\Models\AuditLog;
use App\Models\Royalty;
use App\Models\Track;
use App\Models\Artist;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function financeIndex()
    {
        $user = Auth::user();
        
        // Calculate earnings and balance
        // Load user's artists
        if ($user->isRecordLabel() || $user->isDistributor()) {
            $artistIds = Artist::where('user_id', $user->id)->pluck('id')->toArray();
        } else {
            $artistIds = $user->artist ? [$user->artist->id] : [];
        }

        // Get all track IDs for these artists
        $trackIds = Track::whereIn('release_id', function ($query) use ($artistIds) {
            $query->select('id')->from('releases')->whereIn('artist_id', $artistIds);
        })->pluck('id')->toArray();

        // Total Royalties earned
        $totalEarned = Royalty::whereIn('track_id', $trackIds)->sum('amount');

        // Total Withdrawals (both completed and pending)
        $totalWithdrawn = Withdrawal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'completed'])
            ->sum('amount');

        $availableBalance = max(0.00, $totalEarned - $totalWithdrawn);

        // Load payout history
        $withdrawals = Withdrawal::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        // Load transaction history
        $payments = Payment::where('user_id', $user->id)->with('release', 'subscription')->orderBy('created_at', 'desc')->get();

        // Active plan details
        $subscription = $user->subscription;

        // Artist/Label configured payout receiving account
        $payoutAccount = $user->payout_account;

        // Platform settlement account set by administrator
        $platformAccount = SystemSetting::get('platform_payout_account', [
            'payout_method' => 'bank_transfer',
            'bank_name' => 'JPMorgan Chase Bank, N.A.',
            'account_number' => '987654321098',
            'account_name' => 'CollegeMusic Global Distribution LLC',
            'routing_swift' => 'CHASUS33XXX',
            'mobile_network' => 'Safaricom M-Pesa Buy Goods / Paybill (Till #876543)',
            'paypal_email' => 'finance@collegemusic.io',
            'currency' => 'USD',
            'notes' => 'Official platform treasury settlement account for receiving catalog revenues, subscription fees, and account upgrades.',
        ]);

        return view('finance.index', compact('totalEarned', 'availableBalance', 'withdrawals', 'payments', 'subscription', 'payoutAccount', 'platformAccount'));
    }

    public function updatePayoutAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'payout_method' => 'required|string|in:bank_transfer,mobile_money,paypal,bank_card',
            'account_number' => 'required|string|max:100',
            'account_name' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:100',
            'mobile_network' => 'nullable|string|max:100',
            'paypal_email' => 'nullable|email|max:255',
            'currency' => 'nullable|string|max:10',
        ]);

        $accountData = [
            'payout_method' => $request->payout_method,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'routing_number' => $request->routing_number,
            'mobile_network' => $request->mobile_network,
            'paypal_email' => $request->paypal_email,
            'currency' => $request->currency ?? 'USD',
            'updated_at' => now()->toDateTimeString(),
        ];

        $user->payout_account = $accountData;
        $user->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'update_payout_account',
            'description' => "Updated default payout receiving account ({$request->payout_method} - {$request->account_number} - {$request->account_name}).",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', 'Your payout receiving account details have been saved and secured successfully.');
    }

    public function requestWithdrawal(Request $request)
    {
        $user = Auth::user();

        // Calculate available balance
        if ($user->isRecordLabel() || $user->isDistributor()) {
            $artistIds = Artist::where('user_id', $user->id)->pluck('id')->toArray();
        } else {
            $artistIds = $user->artist ? [$user->artist->id] : [];
        }

        $trackIds = Track::whereIn('release_id', function ($query) use ($artistIds) {
            $query->select('id')->from('releases')->whereIn('artist_id', $artistIds);
        })->pluck('id')->toArray();

        $totalEarned = Royalty::whereIn('track_id', $trackIds)->sum('amount');
        $totalWithdrawn = Withdrawal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'completed'])
            ->sum('amount');

        $availableBalance = max(0.00, $totalEarned - $totalWithdrawn);

        $request->validate([
            'amount' => 'required|numeric|min:10|max:' . $availableBalance,
            'payment_method' => 'required|string|in:bank_card,mobile_money,paypal,bank_transfer',
            'payment_details' => 'required|string',
        ]);

        $inv = 'WD-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_details' => $request->payment_details,
            'status' => 'pending',
            'invoice_number' => $inv,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'request_withdrawal',
            'description' => "Requested a royalty withdrawal of $" . $request->amount . " via {$request->payment_method}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', 'Withdrawal request submitted successfully! Funds will be transferred once approved by administrators.');
    }

    public function processUpgrade(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'plan_name' => 'required|string|in:Artist Premium,Record Label Pro,VIP Lifetime,Premium',
            'payment_method' => 'required|string|in:card,mobile_money,bank_transfer,paypal',
            'card_name' => 'required_if:payment_method,card|nullable|string|max:255',
            'card_number' => 'required_if:payment_method,card|nullable|string|min:15|max:19',
            'card_expiry' => 'required_if:payment_method,card|nullable|string|max:7',
            'card_cvc' => 'required_if:payment_method,card|nullable|string|min:3|max:4',
            'transaction_reference' => 'required_unless:payment_method,card|nullable|string|max:100',
            'payer_phone' => 'nullable|string|max:50',
            'proof_notes' => 'nullable|string|max:500',
        ]);

        $plans = [
            'Premium' => ['price' => 49.99, 'duration' => 365, 'role' => 'artist', 'label' => 'Artist Premium Distribution Plan'],
            'Artist Premium' => ['price' => 49.99, 'duration' => 365, 'role' => 'artist', 'label' => 'Artist Premium Distribution Plan'],
            'Record Label Pro' => ['price' => 149.99, 'duration' => 365, 'role' => 'record_label', 'label' => 'Record Label Pro Distribution Plan'],
            'VIP Lifetime' => ['price' => 299.99, 'duration' => 3650, 'role' => $user->role, 'label' => 'VIP Lifetime Distribution Plan'],
        ];

        $planKey = $request->plan_name === 'Premium' ? 'Artist Premium' : $request->plan_name;
        $selectedPlan = $plans[$request->plan_name] ?? $plans['Artist Premium'];
        $price = $selectedPlan['price'];

        // Retrieve active platform settlement account configured by admin
        $platformAccount = SystemSetting::get('platform_payout_account', [
            'payout_method' => 'bank_transfer',
            'bank_name' => 'JPMorgan Chase Bank, N.A.',
            'account_number' => '987654321098',
            'account_name' => 'CollegeMusic Global Distribution LLC',
            'currency' => 'USD',
        ]);

        DB::beginTransaction();
        try {
            $startsAt = now();
            $endsAt = now()->addDays($selectedPlan['duration']);

            $sub = Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_name' => $planKey,
                    'price' => $price,
                    'status' => 'active',
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                ]
            );

            // If user upgraded to Record Label Pro and current role is artist, promote role
            if ($planKey === 'Record Label Pro' && $user->role === 'artist') {
                $user->role = 'record_label';
                $user->save();
            }

            // Generate transaction reference and invoice
            $ref = $request->transaction_reference ?: ('TX-' . Str::upper(Str::random(12)));
            $inv = 'INV-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));

            Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $sub->id,
                'amount' => $price,
                'status' => 'completed',
                'payment_method' => $request->payment_method,
                'transaction_reference' => $ref,
                'invoice_number' => $inv,
            ]);

            $platformAccStr = ($platformAccount['bank_name'] ?? 'Platform Treasury') . ' Acc #' . ($platformAccount['account_number'] ?? 'N/A') . ' (' . ($platformAccount['account_name'] ?? 'CollegeMusic') . ')';

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'account_upgrade',
                'description' => "Upgraded account to '{$planKey}' ($" . number_format($price, 2) . ") via {$request->payment_method}. Received into platform receiving account: {$platformAccStr}. Ref: {$ref}.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Broadcast revenue notification to all administrators
            $allAdmins = User::where('role', 'admin')->get();
            $adminAlert = "REVENUE RECEIVED: User {$user->name} ({$user->email}, Role: {$user->role}) paid $" . number_format($price, 2) . " for '{$planKey}' via {$request->payment_method}. Payment deposited into platform account: {$platformAccStr}. Ref: {$ref}.";

            foreach ($allAdmins as $admin) {
                AuditLog::create([
                    'user_id' => $admin->id,
                    'action' => 'admin_revenue_received',
                    'description' => $adminAlert,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }

            DB::commit();

            return back()->with('success', "Congratulations! Your account has been upgraded to {$planKey} successfully. Payment settled into the platform distribution system (Invoice #{$inv}).");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Upgrade processing failed: ' . $e->getMessage());
        }
    }

    public function subscribePremium(Request $request)
    {
        return $this->processUpgrade($request);
    }

    public function viewInvoice(Payment $payment)
    {
        // Security check
        if (!Auth::user()->isAdmin() && $payment->user_id !== Auth::id()) {
            abort(403);
        }
        return view('finance.invoice', compact('payment'));
    }

    public function viewWithdrawalInvoice(Withdrawal $withdrawal)
    {
        if (!Auth::user()->isAdmin() && $withdrawal->user_id !== Auth::id()) {
            abort(403);
        }
        return view('finance.withdrawal_invoice', compact('withdrawal'));
    }
}
