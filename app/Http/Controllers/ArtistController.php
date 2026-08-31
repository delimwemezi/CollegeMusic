<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Release;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ArtistController extends Controller
{
    public function catalogue()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin');
        }

        // If artist profile does not exist yet (e.g. record label/distributor who hasn't initialized one, or first-time artist)
        // ensure artist record exists or handle
        $artist = $user->artist;
        
        if (!$artist && $user->role === 'artist') {
            $artist = Artist::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'verification_status' => 'pending',
            ]);
        }

        if ($user->isRecordLabel() || $user->isDistributor()) {
            // Label/Distributor can manage multiple artists, let's load all artists they created
            // Plus all releases from those artists
            $artists = Artist::where('user_id', $user->id)->get();
            $artistIds = $artists->pluck('id')->toArray();
            $releases = Release::whereIn('artist_id', $artistIds)->with('tracks', 'stores')->orderBy('created_at', 'desc')->get();
        } else {
            // Single artist catalog
            $artists = collect([$artist]);
            $releases = Release::where('artist_id', $artist->id)->with('tracks', 'stores')->orderBy('created_at', 'desc')->get();
        }

        return view('catalogue.index', compact('releases', 'artists', 'artist'));
    }

    public function submitVerification(Request $request)
    {
        $user = Auth::user();
        $artist = $user->artist;

        if (!$artist) {
            $artist = Artist::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'verification_status' => 'pending'
            ]);
        }

        $request->validate([
            'doc_type' => 'required|string',
            'doc_file' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120', // 5MB max
        ]);

        if ($request->hasFile('doc_file')) {
            $path = $request->file('doc_file')->store('verifications', 'public');
            
            $docs = $artist->verification_documents ?? [];
            $docs[] = [
                'type' => $request->doc_type,
                'path' => $path,
                'submitted_at' => now()->toDateTimeString()
            ];

            $artist->verification_documents = $docs;
            $artist->verification_status = 'pending'; // set to pending on submission
            $artist->save();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'submit_artist_verification',
                'description' => "Submitted {$request->doc_type} document for identity verification.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->with('success', 'Verification document submitted successfully! Administrators will review it shortly.');
        }

        return back()->with('error', 'Failed to upload verification document.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'artist_id' => 'required|exists:artists,id',
            'bio' => 'nullable|string|max:1000',
            'contact_info' => 'nullable|string|max:255',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
            'spotify' => 'nullable|url',
        ]);

        $artist = Artist::findOrFail($request->artist_id);

        // Security check: ensure user owns the artist record
        if ($artist->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $artist->bio = $request->bio;
        $artist->contact_info = $request->contact_info;
        $artist->social_links = [
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'instagram' => $request->instagram,
            'spotify' => $request->spotify,
        ];
        $artist->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'update_artist_profile',
            'description' => "Updated artist profile details for '{$artist->name}'.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', 'Artist profile updated successfully!');
    }

    public function storeArtist(Request $request)
    {
        $user = Auth::user();
        if (!$user->isRecordLabel() && !$user->isDistributor()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:artists,name',
            'bio' => 'nullable|string',
            'contact_info' => 'nullable|string',
        ]);

        $artist = Artist::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'bio' => $request->bio,
            'contact_info' => $request->contact_info,
            'verification_status' => 'pending',
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'create_artist',
            'description' => "Label/Distributor created new artist profile '{$artist->name}'.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', 'New artist profile registered successfully! It is pending verification review.');
    }

    /**
     * Send email verification code for artist identity verification.
     */
    public function sendEmailVerification(Request $request)
    {
        $user = Auth::user();
        $artist = $user->artist;

        if (!$artist) {
            $artist = Artist::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'verification_status' => 'unverified'
            ]);
        }

        if ($artist->verification_status === 'verified') {
            return back()->with('info', 'Your artist account is already verified.');
        }

        // Generate a 6-digit verification code
        $code = rand(100000, 999999);

        // Store code in session for verification
        session([
            'artist_verify_code' => $code,
            'artist_verify_email' => $user->email,
            'artist_verify_sent_at' => now()->toDateTimeString(),
        ]);

        // Log the code for demo/testing purposes
        \Illuminate\Support\Facades\Log::info("Artist email verification code for {$user->email}: {$code}");

        // Send verification email
        try {
            Mail::raw("Hello,\n\nYour CollegeMusic artist verification code is: {$code}\n\nUse this code to verify your identity and unlock distribution services.\n\nBest regards,\nCollegeMusic Team", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('CollegeMusic Artist Verification Code');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send artist verification email to {$user->email}: " . $e->getMessage());
        }

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'artist_email_verification_sent',
            'description' => "Email verification code sent to {$user->email} for artist identity verification.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', "Verification code sent to {$user->email}! Enter the 6-digit code below to verify.")
                     ->with('show_email_verify', true);
    }

    /**
     * Confirm the email verification code for artist identity.
     */
    public function confirmEmailVerification(Request $request)
    {
        $user = Auth::user();
        $artist = $user->artist;

        if (!$artist) {
            return back()->with('error', 'No artist profile found. Please create one first.');
        }

        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $storedCode = session('artist_verify_code');
        $storedEmail = session('artist_verify_email');

        if (!$storedCode || !$storedEmail) {
            return back()->with('error', 'No verification code found. Please request a new code.');
        }

        if ($storedEmail !== $user->email) {
            return back()->with('error', 'Email mismatch. Please request a new verification code.');
        }

        if ($request->verification_code != $storedCode) {
            return back()->with('error', 'Invalid verification code. Please check the code and try again.')
                         ->with('show_email_verify', true);
        }

        // Code is valid — mark artist as pending admin review
        $artist->verification_status = 'pending';

        // Store email verification record in documents
        $docs = $artist->verification_documents ?? [];
        $docs[] = [
            'type' => 'Email Verification',
            'email' => $user->email,
            'verified_at' => now()->toDateTimeString(),
        ];
        $artist->verification_documents = $docs;
        $artist->save();

        // Clear session codes
        session()->forget(['artist_verify_code', 'artist_verify_email', 'artist_verify_sent_at']);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'artist_email_verified',
            'description' => "Artist email verification completed for {$user->email}. Awaiting admin approval.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', 'Email verified successfully! Your artist profile is now pending administrator approval.');
    }
}
