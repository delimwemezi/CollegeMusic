<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        // Update profile picture
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
            
            // If user has an artist profile, update it there
            if ($user->artist) {
                $user->artist->profile_picture = $path;
                $user->artist->save();
            }
        }

        $user->update($data);

        // If artist profile bio was provided, save it to artist
        if ($request->has('bio') && $user->artist) {
            $user->artist->bio = $request->bio;
            $user->artist->save();
        }

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'update_profile',
            'description' => 'User updated profile details.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'change_password',
            'description' => 'User changed password.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $prefs = [
            'email' => $request->has('pref_email'),
            'sms' => $request->has('pref_sms'),
            'approvals' => $request->has('pref_approvals'),
            'royalties' => $request->has('pref_royalties'),
        ];

        $user->notification_preferences = $prefs;
        $user->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'update_settings',
            'description' => 'User updated notification preferences.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', 'Notification preferences updated successfully!');
    }

    public function deactivate(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'confirm_deactivate' => 'required|accepted',
        ]);

        // Deactivate user
        $user->status = 'deactivated';
        $user->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'deactivate_account',
            'description' => 'User deactivated account.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Your account has been deactivated successfully.');
    }
}
