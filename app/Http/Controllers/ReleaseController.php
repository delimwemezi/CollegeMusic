<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Release;
use App\Models\Track;
use App\Models\ReleaseStore;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReleaseController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        
        // Load artists that this user can publish for
        if ($user->isRecordLabel() || $user->isDistributor()) {
            $artists = Artist::where('user_id', $user->id)->get();
        } else {
            $artist = $user->artist;
            if (!$artist) {
                $artist = Artist::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'verification_status' => 'pending',
                ]);
            }
            $artists = collect([$artist]);
        }

        if ($artists->isEmpty()) {
            return redirect()->route('catalogue')->with('warning', 'Please register at least one artist profile before uploading music.');
        }

        return view('releases.create', compact('artists'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate basic release info
        $rules = [
            'artist_id' => 'required|exists:artists,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:single,album,ep',
            'genre' => 'required|string|max:100',
            'language' => 'required|string|max:50',
            'release_date' => 'nullable|date|after_or_equal:today',
            'scheduling_type' => 'required|string|in:immediate,scheduled',
            'copyright_info' => 'required|string|max:255',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg|max:3072', // 3MB cover art
            'stores' => 'required|array|min:1',
            
            // Tracks arrays
            'track_title' => 'required|array|min:1',
            'track_title.*' => 'required|string|max:255',
            'track_composer' => 'required|array',
            'track_composer.*' => 'required|string|max:255',
            'track_songwriter' => 'required|array',
            'track_songwriter.*' => 'required|string|max:255',
            'track_isrc' => 'nullable|array',
            'track_isrc.*' => 'nullable|string|max:50',
        ];

        if ($request->boolean('use_mock_audio')) {
            $rules['track_file'] = 'nullable|array';
            $rules['track_file.*'] = 'nullable';
        } else {
            $rules['track_file'] = 'required|array|min:1';
            $rules['track_file.*'] = 'required|file|mimes:mp3,wav,flac|max:20480'; // 20MB audio tracks
        }

        $request->validate($rules);

        // Security check: ensure user owns the selected artist
        $artist = Artist::findOrFail($request->artist_id);
        if ($artist->user_id !== $user->id) {
            abort(403, 'Unauthorized artist selection.');
        }

        // Calculate distribution fee
        $fee = 9.99; // default for single
        if ($request->type === 'ep') $fee = 19.99;
        if ($request->type === 'album') $fee = 29.99;

        // Check if user has an active premium subscription
        $hasPremium = $user->subscription && $user->subscription->plan_name === 'Premium' && $user->subscription->status === 'active' && $user->subscription->ends_at->isAfter(now());
        if ($hasPremium) {
            $fee = 0.00;
        }

        DB::beginTransaction();
        try {
            // Upload cover image
            $coverPath = $request->file('cover_image')->store('covers', 'public');

            // Create Release
            $release = Release::create([
                'artist_id' => $artist->id,
                'title' => $request->title,
                'type' => $request->type,
                'cover_image' => $coverPath,
                'genre' => $request->genre,
                'language' => $request->language,
                'release_date' => $request->scheduling_type === 'scheduled' ? $request->release_date : null,
                'copyright_info' => $request->copyright_info,
                'scheduling_type' => $request->scheduling_type,
                'distribution_status' => 'pending',
                'billing_status' => $fee == 0.00 ? 'paid' : 'unpaid',
                'price_paid' => 0.00, // will be set once checkout completes
            ]);

            // Save Selected Stores
            foreach ($request->stores as $storeName) {
                ReleaseStore::create([
                    'release_id' => $release->id,
                    'store_name' => $storeName,
                    'status' => 'pending',
                ]);
            }

            // Save Tracks
            foreach ($request->track_title as $index => $trackTitle) {
                if ($request->hasFile('track_file') && isset($request->file('track_file')[$index])) {
                    $audioPath = $request->file('track_file')[$index]->store('tracks', 'public');
                } else {
                    $audioPath = 'tracks/mock_audio.mp3';
                    if (!Storage::disk('public')->exists($audioPath)) {
                        Storage::disk('public')->put($audioPath, 'MOCK AUDIO CONTENT FOR SYSTEM TESTING');
                    }
                }
                
                // Simulate duration (e.g. random 180 to 240 seconds)
                $duration = rand(180, 240);

                // Auto-generate ISRC if empty
                $isrc = $request->track_isrc[$index] ?? 'US-CM1-' . date('y') . '-' . sprintf('%05d', rand(1, 99999));

                Track::create([
                    'release_id' => $release->id,
                    'title' => $trackTitle,
                    'artist_name' => $artist->name,
                    'composer' => $request->track_composer[$index],
                    'songwriter' => $request->track_songwriter[$index],
                    'isrc' => $isrc,
                    'audio_file' => $audioPath,
                    'duration' => $duration,
                ]);
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'upload_release',
                'description' => "Uploaded release '{$release->title}' for review. Fee calculated: ${$fee}.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            if ($fee > 0.00) {
                return redirect()->route('releases.show', $release->id)->with('info', 'Release uploaded! Please process the distribution fee payment to submit for review.');
            } else {
                return redirect()->route('catalogue')->with('success', 'Release submitted for administrator review (Free with Premium subscription)!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while uploading your release: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Release $release)
    {
        $user = Auth::user();
        
        // Security check
        if (!$user->isAdmin() && $release->artist->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Calculate distribution fee
        $fee = 9.99;
        if ($release->type === 'ep') $fee = 19.99;
        if ($release->type === 'album') $fee = 29.99;

        $hasPremium = $user->subscription && $user->subscription->plan_name === 'Premium' && $user->subscription->status === 'active' && $user->subscription->ends_at->isAfter(now());
        if ($hasPremium) {
            $fee = 0.00;
        }

        return view('releases.show', compact('release', 'fee'));
    }

    public function edit(Release $release)
    {
        $user = Auth::user();

        // Security check: only creator can edit, and only if not approved yet
        if ($release->artist->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        if ($release->distribution_status === 'approved' || $release->distribution_status === 'distributed') {
            return redirect()->route('catalogue')->with('error', 'You cannot edit a release once it has been approved/distributed.');
        }

        return view('releases.edit', compact('release'));
    }

    public function update(Request $request, Release $release)
    {
        $user = Auth::user();

        if ($release->artist->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        if ($release->distribution_status === 'approved' || $release->distribution_status === 'distributed') {
            return redirect()->route('catalogue')->with('error', 'You cannot edit approved releases.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'genre' => 'required|string|max:100',
            'language' => 'required|string|max:50',
            'copyright_info' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
        ]);

        $data = [
            'title' => $request->title,
            'genre' => $request->genre,
            'language' => $request->language,
            'copyright_info' => $request->copyright_info,
            'distribution_status' => 'pending', // reset to pending if rejected/changed
        ];

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
            $data['cover_image'] = $coverPath;
        }

        $release->update($data);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'edit_release',
            'description' => "Edited release metadata for '{$release->title}'. Status reset to Pending.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->route('releases.show', $release->id)->with('success', 'Release updated successfully and sent back to review queue.');
    }

    public function takedown(Release $release)
    {
        $user = Auth::user();

        if ($release->artist->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        if ($release->distribution_status !== 'distributed') {
            return back()->with('error', 'Only distributed releases can be taken down.');
        }

        $release->distribution_status = 'pending_takedown';
        $release->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'request_takedown',
            'description' => "Requested a takedown for release '{$release->title}'.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->route('catalogue')->with('success', 'Takedown request submitted. Administrators will process removal from digital stores.');
    }

    public function processPayment(Request $request, Release $release)
    {
        $user = Auth::user();

        if ($release->artist->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        if ($release->billing_status === 'paid') {
            return back()->with('warning', 'This release has already been paid for.');
        }

        $request->validate([
            'card_name' => 'required|string',
            'card_number' => 'required|string|min:16',
            'card_expiry' => 'required|string',
            'card_cvc' => 'required|string|min:3',
        ]);

        // Calculate distribution fee
        $fee = 9.99;
        if ($release->type === 'ep') $fee = 19.99;
        if ($release->type === 'album') $fee = 29.99;

        // Verify it isn't exempt
        $hasPremium = $user->subscription && $user->subscription->plan_name === 'Premium' && $user->subscription->status === 'active' && $user->subscription->ends_at->isAfter(now());
        if ($hasPremium) {
            $fee = 0.00;
        }

        DB::beginTransaction();
        try {
            $release->billing_status = 'paid';
            $release->price_paid = $fee;
            $release->save();

            // Create Payment record
            $ref = 'TX-' . Str::upper(Str::random(12));
            $inv = 'INV-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));

            Payment::create([
                'user_id' => $user->id,
                'release_id' => $release->id,
                'amount' => $fee,
                'status' => 'completed',
                'payment_method' => 'card',
                'transaction_reference' => $ref,
                'invoice_number' => $inv,
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'pay_release_fee',
                'description' => "Paid ${$fee} distribution fee for '{$release->title}'. Ref: {$ref}.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            return redirect()->route('releases.show', $release->id)->with('success', 'Payment processed successfully! Your music has been submitted for admin review.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }
}
