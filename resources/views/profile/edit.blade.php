@extends('layouts.app')

@section('title', 'Account Settings')
@section('header_title', 'Account Settings')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Settings & Profile</h1>
        <p class="page-subtitle">Manage your personal information, notification preferences, and security</p>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Profile & Notifications Card -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-user-pen"></i> Personal Profile</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem;">
                        @if($user->artist && $user->artist->profile_picture)
                            <img src="{{ asset('storage/' . $user->artist->profile_picture) }}" alt="Avatar" style="width: 80px; height: 80px; border-radius: var(--radius-full); object-fit: cover; border: 2px solid var(--primary);">
                        @else
                            <div style="width: 80px; height: 80px; border-radius: var(--radius-full); background-color: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 1.5rem;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                        
                        <div>
                            <label class="form-label" for="profile_picture">Change Profile Picture</label>
                            <input type="file" id="profile_picture" name="profile_picture" class="form-input">
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Supported formats: JPEG, PNG, JPG (Max 2MB)</small>
                            @error('profile_picture')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($user->isArtist())
                        <div class="form-group">
                            <label class="form-label" for="bio">Artist Biography</label>
                            <textarea id="bio" name="bio" class="form-textarea" rows="4" placeholder="Tell the world about yourself...">{{ old('bio', $user->artist ? $user->artist->bio : '') }}</textarea>
                            @error('bio')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Save Profile
                    </button>
                </form>
            </div>
        </div>

        <!-- Notification Settings Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-bell"></i> Notification Preferences</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.settings') }}" method="POST">
                    @csrf
                    
                    @php
                        $prefs = $user->notification_preferences ?? ['email' => true, 'sms' => false, 'approvals' => true, 'royalties' => true];
                    @endphp

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <input type="checkbox" id="pref_email" name="pref_email" class="form-checkbox" {{ ($prefs['email'] ?? false) ? 'checked' : '' }}>
                        <label for="pref_email" class="form-checkbox-label">Receive email digests and alerts</label>
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <input type="checkbox" id="pref_sms" name="pref_sms" class="form-checkbox" {{ ($prefs['sms'] ?? false) ? 'checked' : '' }}>
                        <label for="pref_sms" class="form-checkbox-label">Receive SMS notifications on mobile</label>
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <input type="checkbox" id="pref_approvals" name="pref_approvals" class="form-checkbox" {{ ($prefs['approvals'] ?? false) ? 'checked' : '' }}>
                        <label for="pref_approvals" class="form-checkbox-label">Notify when music is reviewed (Approved/Rejected)</label>
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <input type="checkbox" id="pref_royalties" name="pref_royalties" class="form-checkbox" {{ ($prefs['royalties'] ?? false) ? 'checked' : '' }}>
                        <label for="pref_royalties" class="form-checkbox-label">Notify when royalty payments are processed</label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-sliders"></i> Update Preferences
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Security & Account Operations -->
    <div>
        <!-- Password Reset Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-lock"></i> Change Password</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-input" placeholder="••••••••" required>
                        @error('current_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">New Password</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-shield"></i> Change Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Deactivation Card -->
        <div class="card" style="border-color: rgba(239, 68, 68, 0.2);">
            <div class="card-header" style="background-color: rgba(239, 68, 68, 0.05); border-bottom: 1px solid rgba(239, 68, 68, 0.15);">
                <h3 class="card-title" style="color: var(--danger);"><i class="fa-solid fa-user-slash"></i> Deactivate Account</h3>
            </div>
            <div class="card-body">
                <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.6;">
                    Deactivating your account will suspend all active distributions and catalog streaming listings. You can reactivate your account at any time by signing back in with your credentials.
                </p>
                <form action="{{ route('profile.deactivate') }}" method="POST" onsubmit="return confirm('Are you absolutely sure you want to deactivate your account?');">
                    @csrf
                    
                    <div class="form-group" style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <input type="checkbox" id="confirm_deactivate" name="confirm_deactivate" class="form-checkbox" required>
                        <label for="confirm_deactivate" class="form-checkbox-label" style="font-size: 0.85rem; line-height: 1.4;">
                            I confirm that I want to deactivate my music distribution profile and take down all catalogs.
                        </label>
                        @error('confirm_deactivate')
                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-power-off"></i> Deactivate My Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
