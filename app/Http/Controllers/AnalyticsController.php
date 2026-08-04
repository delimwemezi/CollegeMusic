<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Release;
use App\Models\Track;
use App\Models\Royalty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Load artist IDs that belong to the user
        if ($user->isRecordLabel() || $user->isDistributor()) {
            $artistIds = Artist::where('user_id', $user->id)->pluck('id')->toArray();
        } else {
            $artistIds = $user->artist ? [$user->artist->id] : [];
        }

        // Aggregate stats
        $releasesCount = Release::whereIn('artist_id', $artistIds)->count();
        $tracks = Track::whereIn('release_id', function ($query) use ($artistIds) {
            $query->select('id')->from('releases')->whereIn('artist_id', $artistIds);
        })->get();

        $totalStreams = $tracks->sum('streams_count');
        $totalDownloads = $tracks->sum('downloads_count');

        // Top Performing Songs
        $topSongs = Track::whereIn('release_id', function ($query) use ($artistIds) {
            $query->select('id')->from('releases')->whereIn('artist_id', $artistIds);
        })->orderBy('streams_count', 'desc')->take(5)->get();

        // Group royalties by platform (Platform breakdown)
        $trackIds = $tracks->pluck('id')->toArray();
        $platformStats = Royalty::select('platform', DB::raw('sum(streams_count) as total_streams'), DB::raw('sum(amount) as total_revenue'))
            ->whereIn('track_id', $trackIds)
            ->groupBy('platform')
            ->get();

        // Group royalties by country (Geographic breakdown)
        $countryStats = Royalty::select('country', DB::raw('sum(streams_count) as total_streams'), DB::raw('sum(amount) as total_revenue'))
            ->whereIn('track_id', $trackIds)
            ->groupBy('country')
            ->orderBy('total_streams', 'desc')
            ->get();

        // Check subscription tier for demographic locks
        $isPremium = $user->subscription && $user->subscription->plan_name === 'Premium' && $user->subscription->status === 'active' && $user->subscription->ends_at->isAfter(now());
        
        // If label or distributor, automatically unlock demographics
        if ($user->isRecordLabel() || $user->isDistributor()) {
            $isPremium = true;
        }

        return view('analytics.index', compact('releasesCount', 'totalStreams', 'totalDownloads', 'topSongs', 'platformStats', 'countryStats', 'isPremium'));
    }

    public function generateReport(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period', 'monthly'); // monthly or yearly
        
        if ($user->isRecordLabel() || $user->isDistributor()) {
            $artistIds = Artist::where('user_id', $user->id)->pluck('id')->toArray();
        } else {
            $artistIds = $user->artist ? [$user->artist->id] : [];
        }

        $tracks = Track::whereIn('release_id', function ($query) use ($artistIds) {
            $query->select('id')->from('releases')->whereIn('artist_id', $artistIds);
        })->get();

        $trackIds = $tracks->pluck('id')->toArray();

        $totalStreams = $tracks->sum('streams_count');
        $totalDownloads = $tracks->sum('downloads_count');
        $totalRevenue = Royalty::whereIn('track_id', $trackIds)->sum('amount');

        // Detailed breakdowns
        $topSongs = Track::whereIn('release_id', function ($query) use ($artistIds) {
            $query->select('id')->from('releases')->whereIn('artist_id', $artistIds);
        })->orderBy('streams_count', 'desc')->take(10)->get();

        $platformBreakdown = Royalty::select('platform', DB::raw('sum(streams_count) as total_streams'), DB::raw('sum(amount) as total_revenue'))
            ->whereIn('track_id', $trackIds)
            ->groupBy('platform')
            ->get();

        $countryBreakdown = Royalty::select('country', DB::raw('sum(streams_count) as total_streams'), DB::raw('sum(amount) as total_revenue'))
            ->whereIn('track_id', $trackIds)
            ->groupBy('country')
            ->orderBy('total_streams', 'desc')
            ->get();

        return view('analytics.report', compact('period', 'totalStreams', 'totalDownloads', 'totalRevenue', 'topSongs', 'platformBreakdown', 'countryBreakdown'));
    }
}
