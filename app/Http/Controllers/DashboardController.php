<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Release;
use App\Models\Track;
use App\Models\Royalty;
use App\Models\Withdrawal;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirect admin to admin overview
        if ($user->isAdmin()) {
            return redirect()->route('admin');
        }

        // Get or initialize artist profile for artists
        $artist = $user->artist;
        if (!$artist && $user->role === 'artist') {
            $artist = Artist::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'verification_status' => 'pending',
            ]);
        }

        // Aggregate statistics based on user type
        if ($user->isRecordLabel() || $user->isDistributor()) {
            $artistIds = Artist::where('user_id', $user->id)->pluck('id')->toArray();
            $releases = Release::whereIn('artist_id', $artistIds)->with('artist', 'tracks', 'stores')->orderBy('created_at', 'desc')->take(5)->get();
            $releasesCount = Release::whereIn('artist_id', $artistIds)->count();
        } else {
            $artistIds = $artist ? [$artist->id] : [];
            $releases = Release::where('artist_id', $artist->id)->with('tracks', 'stores')->orderBy('created_at', 'desc')->take(5)->get();
            $releasesCount = Release::where('artist_id', $artist->id)->count();
        }

        $tracks = Track::whereIn('release_id', function ($query) use ($artistIds) {
            $query->select('id')->from('releases')->whereIn('artist_id', $artistIds);
        })->get();

        $totalStreams = $tracks->sum('streams_count');
        
        $trackIds = $tracks->pluck('id')->toArray();
        $totalEarned = Royalty::whereIn('track_id', $trackIds)->sum('amount');
        $totalWithdrawn = Withdrawal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'completed'])
            ->sum('amount');
        $availableBalance = max(0.00, $totalEarned - $totalWithdrawn);

        // Verification Warning
        $showVerificationWarning = false;
        if ($user->isArtist()) {
            $showVerificationWarning = ($artist->verification_status !== 'verified');
        } else {
            // Label/Distributor checks if they have submitted identity verification
            $showVerificationWarning = ($user->status === 'active' && !Artist::where('user_id', $user->id)->exists());
        }

        // Notifications (alerts generated for user, e.g. release reviews, payout reviews, upload status)
        $notifications = AuditLog::where('user_id', $user->id)
            ->whereIn('action', ['release_approved', 'release_rejected', 'release_distributed', 'withdrawal_completed', 'withdrawal_rejected', 'payment_completed', 'upload_success', 'upload_failed'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('releasesCount', 'totalStreams', 'availableBalance', 'releases', 'showVerificationWarning', 'notifications', 'artist'));
    }
}
