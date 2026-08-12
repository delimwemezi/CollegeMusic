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

        return view('finance.index', compact('totalEarned', 'availableBalance', 'withdrawals', 'payments', 'subscription', 'payoutAccount'));
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

    public function subscribePremium(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'card_name' => 'required|string',
            'card_number' => 'required|string|min:16',
            'card_expiry' => 'required|string',
            'card_cvc' => 'required|string|min:3',
        ]);

        DB::beginTransaction();
        try {
            // Subscribe for 1 year
            $startsAt = now();
            $endsAt = now()->addYear();

            $sub = Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_name' => 'Premium',
                    'price' => 49.99,
                    'status' => 'active',
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                ]
            );

            // Record Payment
            $ref = 'TX-' . Str::upper(Str::random(12));
            $inv = 'INV-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));

            Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $sub->id,
                'amount' => 49.99,
                'status' => 'completed',
                'payment_method' => 'card',
                'transaction_reference' => $ref,
                'invoice_number' => $inv,
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'subscribe_premium',
                'description' => 'Subscribed to Premium plan ($49.99/year). Ref: ' . $ref,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            return back()->with('success', 'Congratulations! You are now subscribed to the Premium Distribution Plan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Subscription failed: ' . $e->getMessage());
        }
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
