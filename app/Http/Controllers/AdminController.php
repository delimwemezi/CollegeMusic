<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artist;
use App\Models\Release;
use App\Models\ReleaseStore;
use App\Models\Payment;
use App\Models\Withdrawal;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->orderByRaw("CASE WHEN distribution_status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.releases', compact('releases'));
    }

    public function reviewRelease(Request $request, Release $release)
    {
        $request->validate([
            'action' => 'required|string|in:approve,reject,distribute',
            'rejection_reason' => 'required_if:action,reject|nullable|string',
        ]);

        if ($request->action === 'approve') {
            $release->distribution_status = 'approved';
            $release->rejection_reason = null;
            $release->save();

            // Create notification alert for artist
            AuditLog::create([
                'user_id' => $release->artist->user_id,
                'action' => 'release_approved',
                'description' => "Your release '{$release->title}' has been approved for distribution!",
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

            return back()->with('success', "Release '{$release->title}' has been approved.");
        } 
        
        if ($request->action === 'reject') {
            $release->distribution_status = 'rejected';
            $release->rejection_reason = $request->rejection_reason;
            $release->save();

            // Create notification alert for artist
            AuditLog::create([
                'user_id' => $release->artist->user_id,
                'action' => 'release_rejected',
                'description' => "Your release '{$release->title}' was rejected. Reason: {$request->rejection_reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'admin_reject_release',
                'description' => "Rejected release '{$release->title}'. Reason: {$request->rejection_reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->with('success', "Release '{$release->title}' has been rejected.");
        }

        if ($request->action === 'distribute') {
            // Distribute approved music to selected platforms
            $release->distribution_status = 'distributed';
            $release->save();

            // Set all associated stores status to distributed
            ReleaseStore::where('release_id', $release->id)->update(['status' => 'distributed']);

            // Create notification alert for artist
            AuditLog::create([
                'user_id' => $release->artist->user_id,
                'action' => 'release_distributed',
                'description' => "Distribution completed! Your release '{$release->title}' is now live on digital platforms.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'admin_distribute_release',
                'description' => "Simulated store distribution pipeline for '{$release->title}'.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->with('success', "Release '{$release->title}' has been successfully distributed to streaming platforms!");
        }

        return back()->with('error', 'Invalid review action.');
    }

    public function payments()
    {
        $withdrawals = Withdrawal::with('user')->orderBy('created_at', 'desc')->paginate(15);
        $payments = Payment::with('user', 'release')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.payments', compact('withdrawals', 'payments'));
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
                'description' => "Your withdrawal request for ${$withdrawal->amount} was approved and processed!",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'admin_approve_withdrawal',
                'description' => "Approved withdrawal request #{$withdrawal->id} of ${$withdrawal->amount} for user {$withdrawal->user->email}.",
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
                'description' => "Your withdrawal request for ${$withdrawal->amount} was rejected. Reason: {$request->rejection_reason}",
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
