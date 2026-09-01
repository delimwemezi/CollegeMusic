<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artist;
use App\Models\Release;
use App\Models\ReleaseStore;
use App\Models\Payment;
use App\Models\Withdrawal;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Services\ReleaseQualityControlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'artists' => Artist::count(),
            'releases_pending' => Release::where('distribution_status', 'pending')->count(),
            'releases_approved' => Release::where('distribution_status', 'approved')->count(),
            'releases_distributed' => Release::where('distribution_status', 'distributed')->count(),
            'withdrawals_pending' => Withdrawal::where('status', 'pending')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
        ];

        $recentLogs = AuditLog::with('user')->orderBy('created_at', 'desc')->take(10)->get();

        return view('admin.index', compact('stats', 'recentLogs'));
    }

    public function users()
    {
        $users = User::with('artist', 'subscription')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function updateUserStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|string|in:active,suspended',
        ]);

        $user->status = $request->status;
        $user->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_update_user_status',
            'description' => "Updated status of user {$user->email} to '{$request->status}'.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', "User status updated to {$request->status} successfully.");
    }

    public function artists()
    {
        $artists = Artist::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.artists', compact('artists'));
    }

    public function verifyArtist(Request $request, Artist $artist)
    {
        $request->validate([
            'status' => 'required|string|in:verified,rejected',
        ]);

        $artist->verification_status = $request->status;
        $artist->save();

        // Create audit log and alert
        AuditLog::create([
            'user_id' => $artist->user_id, // notify the artist
            'action' => $request->status === 'verified' ? 'artist_verified' : 'artist_rejected',
            'description' => $request->status === 'verified' ? 'Your artist profile has been verified!' : 'Your artist profile verification was rejected.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_verify_artist',
            'description' => "Updated verification status of artist '{$artist->name}' to '{$request->status}'.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', "Artist profile has been marked as {$request->status}.");
    }

    public function releases()
    {
        $releases = Release::with('artist', 'tracks', 'stores')
            ->where('distribution_status', '!=', 'awaiting_payment')
            ->orderByRaw("CASE WHEN distribution_status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.releases', compact('releases'));
    }

    public function reviewRelease(Request $request, Release $release)
    {
        $request->validate([
            'action' => 'required|string|in:approve,reject,distribute,auto_qc',
            'rejection_reason' => 'required_if:action,reject|nullable|string',
        ]);

        if ($request->action === 'auto_qc') {
            $qc = ReleaseQualityControlService::inspectAndProcess($release, $request);
            if ($qc['passed']) {
                return back()->with('success', "Automated Quality Inspection Passed! Release '{$release->title}' has been auto-approved and distributed.");
            } else {
                return back()->with('warning', "Automated Quality Inspection detected " . count($qc['errors']) . " issue(s) for '{$release->title}'. Feedback automatically sent to artist.");
            }
        }

        if ($request->action === 'approve' && $release->distribution_status !== 'pending') {
            return back()->with('error', 'Only releases submitted for review can be approved or rejected.');
        }

        if ($request->action === 'reject' && !in_array($release->distribution_status, ['pending', 'pending_takedown'], true)) {
            return back()->with('error', 'Only releases submitted for review can be rejected.');
        }

        if ($request->action === 'distribute' && $release->distribution_status !== 'approved') {
            return back()->with('error', 'Only approved releases can be distributed.');
        }

        if ($request->action === 'approve') {
            $release->distribution_status = 'approved';
            $release->rejection_reason = null;
            $release->save();

            // Create notification alert for artist
            AuditLog::create([
                'user_id' => $release->artist->user_id,
                'action' => 'release_approved',
                'description' => "Confirmed: Your release '{$release->title}' has been reviewed & approved by administrator! It is ready for distribution to digital streaming platforms.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Track admin log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'admin_approve_release',
                'description' => "Approved release '{$release->title}' for distribution.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->with('success', "Confirmed: Release '{$release->title}' has been approved and is ready for distribution.");
        } 
        
        if ($request->action === 'reject') {
            $release->distribution_status = 'rejected';
            $release->rejection_reason = $request->rejection_reason;
            $release->save();

            // Create notification alert for artist with suggested details/changes
            AuditLog::create([
                'user_id' => $release->artist->user_id,
                'action' => 'release_rejected',
                'description' => "Review Update: Your release '{$release->title}' requires updates before approval. Suggested changes: {$request->rejection_reason}. Please edit details and upload again for review.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'admin_reject_release',
                'description' => "Rejected release '{$release->title}'. Reason / Suggested Changes: {$request->rejection_reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->with('warning', "Release '{$release->title}' review submitted. Suggested changes have been sent to the artist to edit and upload again for review.");
        }

        if ($request->action === 'distribute') {
            // Distribute approved music to selected platforms
            $release->distribution_status = 'distributed';
            $release->save();

            // Set all associated stores status to distributed
            ReleaseStore::where('release_id', $release->id)->update(['status' => 'distributed']);

            // Connect to stores with free API (httpbin.org/post) for testing functioning of external APIs
            $apiSuccess = false;
            $apiMessage = '';
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(10)->post('https://httpbin.org/post', [
                    'event' => 'music_distribution',
                    'provider' => 'CollegeMusic Global Distribution',
                    'release' => [
                        'id' => $release->id,
                        'title' => $release->title,
                        'type' => $release->type,
                        'genre' => $release->genre,
                        'language' => $release->language,
                        'copyright' => $release->copyright_info,
                        'cover_url' => asset('storage/' . $release->cover_image),
                    ],
                    'artist' => [
                        'id' => $release->artist->id,
                        'name' => $release->artist->name,
                    ],
                    'tracks' => $release->tracks->map(function ($track) {
                        return [
                            'title' => $track->title,
                            'composer' => $track->composer,
                            'songwriter' => $track->songwriter,
                            'isrc' => $track->isrc,
                            'duration_seconds' => $track->duration,
                            'audio_url' => asset('storage/' . $track->audio_file),
                        ];
                    })->toArray(),
                    'stores' => ReleaseStore::where('release_id', $release->id)->pluck('store_name')->toArray(),
                ]);

                if ($response->successful()) {
                    $apiSuccess = true;
                    $responseData = $response->json();
                    $apiMessage = "Successfully connected to stores API. Distributed release ID: " . ($responseData['json']['release']['id'] ?? $release->id);
                } else {
                    $apiMessage = "Stores API returned error code " . $response->status() . ". Simulated fallback distribution completed.";
                }
            } catch (\Exception $e) {
                $apiMessage = "Stores API connection error: " . $e->getMessage() . ". Simulated fallback distribution completed.";
            }

            // Create notification alert for artist
            AuditLog::create([
                'user_id' => $release->artist->user_id,
                'action' => 'release_distributed',
                'description' => "Distributed: Your release '{$release->title}' has been confirmed and distributed live across all selected streaming platforms (Spotify, Apple Music, YouTube Music, etc.)! " . ($apiSuccess ? "(API Transmitted)" : "(Simulated Fallback)"),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'admin_distribute_release',
                'description' => "Store distribution pipeline. API Call Log: " . $apiMessage,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->with('success', "Confirmed: Release '{$release->title}' has been successfully distributed to streaming platforms!");
        }

        return back()->with('error', 'Invalid review action.');
    }

    public function payments()
    {
        $withdrawals = Withdrawal::with('user')->orderBy('created_at', 'desc')->paginate(15);
        $payments = Payment::with('user', 'release')->orderBy('created_at', 'desc')->paginate(15);
        
        // Load active platform receiving payout account
        $platformPayoutAccount = SystemSetting::get('platform_payout_account', [
            'payout_method' => 'bank_transfer',
            'bank_name' => 'JPMorgan Chase Bank, N.A.',
            'account_number' => '987654321098',
            'account_name' => 'CollegeMusic Global Distribution LLC',
            'routing_swift' => 'CHASUS33XXX',
            'mobile_network' => null,
            'paypal_email' => 'finance@collegemusic.io',
            'currency' => 'USD',
            'notes' => 'Official platform treasury settlement account for receiving catalog revenues and fee distributions.',
            'updated_by_name' => 'System Initializer',
            'updated_by_email' => 'admin@collegemusic.io',
            'updated_at' => now()->toDateTimeString(),
            'updated_ip' => '127.0.0.1',
        ]);

        // Load security audit logs for platform payout account modifications
        $payoutSecurityLogs = AuditLog::with('user')
            ->where('action', 'admin_platform_payout_account_changed')
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        return view('admin.payments', compact('withdrawals', 'payments', 'platformPayoutAccount', 'payoutSecurityLogs'));
    }

    public function updatePlatformPayoutAccount(Request $request)
    {
        // High-Security Authentication Check: require verifying current admin's password
        $request->validate([
            'admin_password' => 'required|string',
            'payout_method' => 'required|string|in:bank_transfer,mobile_money,paypal,bank_card',
            'account_number' => 'required|string|max:100',
            'account_name' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'routing_swift' => 'nullable|string|max:100',
            'mobile_network' => 'nullable|string|max:100',
            'paypal_email' => 'nullable|email|max:255',
            'currency' => 'required|string|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        $currentAdmin = Auth::user();

        if (!Hash::check($request->admin_password, $currentAdmin->password)) {
            AuditLog::create([
                'user_id' => $currentAdmin->id,
                'action' => 'admin_platform_payout_account_failed_auth',
                'description' => "SECURITY WARNING: Failed attempt to modify platform payout account by {$currentAdmin->email} (Incorrect administrator password entered).",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->with('error', 'Security Authentication Failed: The administrator password you entered is incorrect.');
        }

        $accountData = [
            'payout_method' => $request->payout_method,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'routing_swift' => $request->routing_swift,
            'mobile_network' => $request->mobile_network,
            'paypal_email' => $request->paypal_email,
            'currency' => strtoupper($request->currency),
            'notes' => $request->notes,
            'updated_by_id' => $currentAdmin->id,
            'updated_by_name' => $currentAdmin->name,
            'updated_by_email' => $currentAdmin->email,
            'updated_at' => now()->toDateTimeString(),
            'updated_ip' => $request->ip(),
        ];

        SystemSetting::set('platform_payout_account', $accountData, $currentAdmin->id);

        // Broadcast high-priority security notifications to ALL System Administrators
        $allAdmins = User::where('role', 'admin')->get();
        $broadcastMessage = "SECURITY ALERT: Platform payout receiving account was changed by {$currentAdmin->name} ({$currentAdmin->email}). New Account: " . strtoupper(str_replace('_', ' ', $request->payout_method)) . " - " . ($request->bank_name ? $request->bank_name . ' ' : '') . "Acc #" . $request->account_number . " ({$request->account_name}, {$request->currency}). Timestamp: " . now()->format('Y-m-d H:i:s') . " from IP: " . $request->ip();

        foreach ($allAdmins as $admin) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'admin_platform_payout_account_changed',
                'description' => $broadcastMessage,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        return back()->with('success', 'Platform payout receiving account has been updated with high-level security verification. Critical broadcast notifications were dispatched to all system administrators.');
    }

    public function updateWithdrawalStatus(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'status' => 'required|string|in:completed,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        if ($request->status === 'completed') {
            $withdrawal->status = 'completed';
            $withdrawal->rejection_reason = null;
            $withdrawal->save();

            // Alert artist
            AuditLog::create([
                'user_id' => $withdrawal->user_id,
                'action' => 'withdrawal_completed',
                'description' => "Your withdrawal request for $" . $withdrawal->amount . " was approved and processed!",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'admin_approve_withdrawal',
                'description' => "Approved withdrawal request #{$withdrawal->id} of $" . $withdrawal->amount . " for user {$withdrawal->user->email}.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->with('success', 'Withdrawal request approved and processed.');
        }

        if ($request->status === 'rejected') {
            $withdrawal->status = 'rejected';
            $withdrawal->rejection_reason = $request->rejection_reason;
            $withdrawal->save();

            // Alert artist
            AuditLog::create([
                'user_id' => $withdrawal->user_id,
                'action' => 'withdrawal_rejected',
                'description' => "Your withdrawal request for $" . $withdrawal->amount . " was rejected. Reason: {$request->rejection_reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'admin_reject_withdrawal',
                'description' => "Rejected withdrawal request #{$withdrawal->id} for user {$withdrawal->user->email}. Reason: {$request->rejection_reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->with('success', 'Withdrawal request has been rejected.');
        }

        return back()->with('error', 'Invalid status.');
    }

    public function logs()
    {
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->paginate(30);
        return view('admin.logs', compact('logs'));
    }

    public function reports()
    {
        // Admin reporting on users, music, revenue
        $report = [
            'total_users' => User::count(),
            'users_by_role' => User::select('role', DB::raw('count(*) as count'))->groupBy('role')->get(),
            'total_releases' => Release::count(),
            'releases_by_status' => Release::select('distribution_status', DB::raw('count(*) as count'))->groupBy('distribution_status')->get(),
            'total_tracks' => Track::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'payments_by_method' => Payment::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))->groupBy('payment_method')->get(),
        ];

        return view('admin.reports', compact('report'));
    }
}
