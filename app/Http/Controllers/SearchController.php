<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Release;
use App\Models\Track;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $genre = $request->input('genre');
        $type = $request->input('type');
        $date = $request->input('date');
        $artistSearch = $request->input('artist');

        // Search Artists
        $artistsQuery = Artist::query();
        if ($query) {
            $artistsQuery->where('name', 'like', '%' . $query . '%');
        }
        if ($genre) {
            $artistsQuery->whereIn('id', function($subQuery) use ($genre) {
                $subQuery->select('artist_id')->from('releases')->where('genre', $genre);
            });
        }
        $artists = $artistsQuery->take(15)->get();

        // Search Releases
        $releasesQuery = Release::with('artist', 'tracks');
        if ($query) {
            $releasesQuery->where('title', 'like', '%' . $query . '%')
                ->orWhereHas('artist', function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                });
        }
        if ($genre) {
            $releasesQuery->where('genre', $genre);
        }
        if ($type) {
            $releasesQuery->where('type', $type);
        }
        if ($date) {
            $releasesQuery->whereDate('release_date', '>=', $date);
        }
        if ($artistSearch) {
            $releasesQuery->whereHas('artist', function($q) use ($artistSearch) {
                $q->where('name', 'like', '%' . $artistSearch . '%');
            });
        }
        $releases = $releasesQuery->orderBy('created_at', 'desc')->take(20)->get();

        // Search Tracks
        $tracksQuery = Track::with('release.artist');
        if ($query) {
            $tracksQuery->where('title', 'like', '%' . $query . '%')
                ->orWhere('artist_name', 'like', '%' . $query . '%');
        }
        if ($genre) {
            $tracksQuery->whereHas('release', function($q) use ($genre) {
                $q->where('genre', $genre);
            });
        }
        $tracks = $tracksQuery->orderBy('created_at', 'desc')->take(25)->get();

        // Collect all distinct genres and artists for filters
        $allGenres = Release::select('genre')->distinct()->pluck('genre')->toArray();
        if (empty($allGenres)) {
            $allGenres = ['Pop', 'Hip-Hop/Rap', 'Rock', 'Electronic/Dance', 'R&B/Soul', 'Afrobeats', 'Reggae', 'Jazz', 'Classical'];
        }

        return view('search.index', compact('artists', 'releases', 'tracks', 'allGenres'));
    }

    public function explore(Request $request)
    {
        $query = $request->input('q');
        $genre = $request->input('genre');
        $type = $request->input('type');
        $date = $request->input('date');
        $artistSearch = $request->input('artist');

        // Search Artists (verified or having approved/distributed releases)
        $artistsQuery = Artist::query();
        if ($query) {
            $artistsQuery->where('name', 'like', '%' . $query . '%');
        }
        if ($genre) {
            $artistsQuery->whereIn('id', function($subQuery) use ($genre) {
                $subQuery->select('artist_id')->from('releases')
                    ->where('genre', $genre)
                    ->whereIn('distribution_status', ['approved', 'distributed']);
            });
        }
        // Only show verified artists or those who have at least one approved/distributed release
        $artistsQuery->where(function($q) {
            $q->where('verification_status', 'verified')
              ->orWhereHas('releases', function($sub) {
                  $sub->whereIn('distribution_status', ['approved', 'distributed']);
              });
        });
        $artists = $artistsQuery->take(15)->get();

        // Search Releases (must be approved or distributed)
        $releasesQuery = Release::with('artist', 'tracks', 'stores')
            ->whereIn('distribution_status', ['approved', 'distributed']);
        if ($query) {
            $releasesQuery->where(function($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhereHas('artist', function($sub) use ($query) {
                      $sub->where('name', 'like', '%' . $query . '%');
                  });
            });
        }
        if ($genre) {
            $releasesQuery->where('genre', $genre);
        }
        if ($type) {
            $releasesQuery->where('type', $type);
        }
        if ($date) {
            $releasesQuery->whereDate('release_date', '>=', $date);
        }
        if ($artistSearch) {
            $releasesQuery->whereHas('artist', function($q) use ($artistSearch) {
                $q->where('name', 'like', '%' . $artistSearch . '%');
            });
        }
        $releases = $releasesQuery->orderBy('created_at', 'desc')->take(20)->get();

        // Search Tracks (must belong to approved or distributed releases)
        $tracksQuery = Track::with('release.artist')
            ->whereHas('release', function($q) {
                $q->whereIn('distribution_status', ['approved', 'distributed']);
            });
        if ($query) {
            $tracksQuery->where(function($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhere('artist_name', 'like', '%' . $query . '%');
            });
        }
        if ($genre) {
            $tracksQuery->whereHas('release', function($q) use ($genre) {
                $q->where('genre', $genre);
            });
        }
        $tracks = $tracksQuery->orderBy('created_at', 'desc')->take(25)->get();

        // Collect all distinct genres and artists for filters
        $allGenres = Release::whereIn('distribution_status', ['approved', 'distributed'])
            ->select('genre')
            ->distinct()
            ->pluck('genre')
            ->toArray();
        if (empty($allGenres)) {
            $allGenres = ['Pop', 'Hip-Hop/Rap', 'Rock', 'Electronic/Dance', 'R&B/Soul', 'Afrobeats', 'Reggae', 'Jazz', 'Classical'];
        }

        return view('search.explore', compact('artists', 'releases', 'tracks', 'allGenres'));
    }
}
