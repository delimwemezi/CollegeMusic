@extends('layouts.app')

@section('title', 'My Catalogue')
@section('header_title', 'Catalogue Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Music Catalogue</h1>
        <p class="page-subtitle">Manage your uploads, releases, and artist profile details</p>
    </div>
    <div>
        <a href="{{ route('releases.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Distribute New Music
        </a>
    </div>
</div>

<!-- Tabs Toggle Navigation -->
<div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem;">
    <button class="btn btn-secondary tab-btn active" onclick="switchTab('releases-tab', this)" style="border-bottom: 2px solid var(--primary); border-radius: 0; background: none; border-left: none; border-right: none; border-top: none; padding-bottom: 1rem; color: var(--text-primary);">
        <i class="fa-solid fa-compact-disc"></i> Releases & Tracks
    </button>
    <button class="btn btn-secondary tab-btn" onclick="switchTab('profile-tab', this)" style="border-radius: 0; background: none; border: none; padding-bottom: 1rem; color: var(--text-secondary);">
        <i class="fa-solid fa-id-card"></i> Artist Verification & Profile
    </button>
    @if(auth()->user()->isRecordLabel() || auth()->user()->isDistributor())
        <button class="btn btn-secondary tab-btn" onclick="switchTab('artists-tab', this)" style="border-radius: 0; background: none; border: none; padding-bottom: 1rem; color: var(--text-secondary);">
            <i class="fa-solid fa-users"></i> Managed Artists
        </button>
    @endif
</div>

<!-- Tab 1: Releases and Tracks -->
<div id="releases-tab" class="tab-content-panel">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Distributed and Uploaded Releases</h3>
        </div>
        <div class="card-body">
            @if($releases->isEmpty())
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-music" style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-muted);"></i>
                    <p style="font-size: 1rem; margin-bottom: 1.5rem;">You haven't uploaded any music releases yet.</p>
                    <a href="{{ route('releases.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload Your First Release
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Artist</th>
                                <th>Type</th>
                                <th>Genre</th>
                                <th>Release Date</th>
                                <th>Payment Status</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($releases as $release)
                                <tr>
                                    <td>
                                        @if($release->cover_image)
                                            <img src="{{ asset('storage/' . $release->cover_image) }}" alt="Cover" style="width: 48px; height: 48px; border-radius: var(--radius-sm); object-fit: cover;">
                                        @else
                                            <div style="width: 48px; height: 48px; background-color: var(--bg-input); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-record-vinyl" style="color: var(--text-muted);"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $release->title }}</div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted);">{{ count($release->tracks) }} Track(s)</span>
                                    </td>
                                    <td>{{ $release->artist->name }}</td>
                                    <td><span style="text-transform: uppercase; font-size: 0.8rem;">{{ $release->type }}</span></td>
                                    <td>{{ $release->genre }}</td>
                                    <td>{{ $release->release_date ? $release->release_date->format('Y-m-d') : 'Immediate' }}</td>
                                    <td>
                                        @if($release->billing_status === 'paid')
                                            <span class="badge badge-approved"><i class="fa-solid fa-circle-check"></i> Paid</span>
                                        @else
                                            <span class="badge badge-pending"><i class="fa-solid fa-clock"></i> Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($release->distribution_status === 'pending')
                                            <span class="badge badge-pending">Review Pending</span>
                                        @elseif($release->distribution_status === 'approved')
                                            <span class="badge badge-approved">Approved</span>
                                        @elseif($release->distribution_status === 'distributed')
                                            <span class="badge badge-distributed">Distributed</span>
                                        @elseif($release->distribution_status === 'rejected')
                                            <span class="badge badge-rejected" title="Reason: {{ $release->rejection_reason }}">Rejected</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; vertical-align: middle;">
                                        <div style="display: inline-flex; gap: 0.5rem;">
                                            <a href="{{ route('releases.show', $release->id) }}" class="btn btn-secondary btn-sm">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>

                                            <!-- Allow edit if pending/rejected and unpaid or not fully approved -->
                                            @if(($release->distribution_status === 'pending' || $release->distribution_status === 'rejected'))
                                                <a href="{{ route('releases.edit', $release->id) }}" class="btn btn-primary btn-sm" style="background-color: var(--accent); border-color: var(--accent);">
                                                    <i class="fa-solid fa-pen"></i> Edit
                                                </a>
                                            @endif

                                            <!-- Remove/Takedown request -->
                                            @if($release->distribution_status === 'distributed')
                                                <form action="{{ route('releases.takedown', $release->id) }}" method="POST" onsubmit="return confirm('Requesting a takedown will remove your release from all stores. Continue?');" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa-solid fa-trash-can"></i> Takedown
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                
                                @if($release->distribution_status === 'rejected' && $release->rejection_reason)
                                    <tr style="background-color: rgba(239, 68, 68, 0.03);">
                                        <td colspan="9" style="padding: 0.75rem 1.5rem; font-size: 0.85rem; border-top: none;">
                                            <div style="color: #fca5a5; display: flex; align-items: center; gap: 0.5rem;">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <strong>Rejection Reason:</strong> {{ $release->rejection_reason }} (You can edit details and submit again)
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Tab 2: Artist Profile & Verification -->
<div id="profile-tab" class="tab-content-panel" style="display: none;">
    <div class="grid-cols-2">
        <!-- Identity Verification Doc Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-shield-halved"></i> Artist Identity Verification</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1.5rem; padding: 1rem; border-radius: var(--radius-md); background-color: var(--bg-input); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h4 style="font-size: 0.95rem; margin-bottom: 0.25rem;">Verification Status</h4>
                        <p style="color: var(--text-secondary); font-size: 0.85rem;">Required to distribute music to Apple & Spotify</p>
                    </div>
                    <div>
                        @if($artist && $artist->verification_status === 'verified')
                            <span class="badge badge-approved" style="font-size: 0.9rem; padding: 0.4rem 1rem;"><i class="fa-solid fa-circle-check" style="margin-right: 0.35rem;"></i> Verified Account</span>
                        @elseif($artist && $artist->verification_status === 'pending')
                            <span class="badge badge-pending" style="font-size: 0.9rem; padding: 0.4rem 1rem;"><i class="fa-solid fa-hourglass-half" style="margin-right: 0.35rem;"></i> Review Pending</span>
                        @else
                            <span class="badge badge-rejected" style="font-size: 0.9rem; padding: 0.4rem 1rem;"><i class="fa-solid fa-circle-xmark" style="margin-right: 0.35rem;"></i> Unverified</span>
                        @endif
                    </div>
                </div>

                @if(!$artist || $artist->verification_status !== 'verified')
                    {{-- Verification Method Tabs --}}
                    <div style="display: flex; gap: 0; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color);">
                        <button type="button" class="verify-method-tab active" id="tabEmailVerify" onclick="switchVerifyTab('email')" style="flex: 1; padding: 0.75rem 1rem; background: none; border: none; border-bottom: 2px solid var(--primary); margin-bottom: -2px; color: var(--primary); font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <i class="fa-solid fa-envelope"></i> Verify by Email
                        </button>
                        <button type="button" class="verify-method-tab" id="tabDocVerify" onclick="switchVerifyTab('document')" style="flex: 1; padding: 0.75rem 1rem; background: none; border: none; border-bottom: 2px solid transparent; margin-bottom: -2px; color: var(--text-muted); font-weight: 500; font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <i class="fa-solid fa-file-arrow-up"></i> Upload Document
                        </button>
                    </div>

                    {{-- EMAIL VERIFICATION PANEL --}}
                    <div id="panelEmailVerify">
                        <div style="padding: 1rem; border-radius: var(--radius-md); background: linear-gradient(135deg, rgba(59,130,246,0.06), rgba(139,92,246,0.06)); border: 1px solid rgba(59,130,246,0.15); margin-bottom: 1.25rem;">
                            <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5;">
                                <i class="fa-solid fa-circle-info" style="color: var(--primary); margin-right: 0.25rem;"></i>
                                We'll send a 6-digit verification code to your registered email <strong style="color: var(--text-primary);">{{ auth()->user()->email }}</strong>. Enter the code to verify your identity.
                            </p>
                        </div>

                        @if(session('show_email_verify') || session('artist_verify_code'))

                            <form action="{{ route('artist.verify.email.confirm') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label" for="verification_code">Enter 6-Digit Verification Code</label>
                                    <input type="text" id="verification_code" name="verification_code" class="form-input" placeholder="e.g. 123456" maxlength="6" pattern="[0-9]{6}" required style="font-size: 1.5rem; text-align: center; letter-spacing: 0.35em; font-family: monospace; font-weight: bold;">
                                </div>

                                <button type="submit" class="btn btn-success btn-block" style="margin-bottom: 0.75rem;">
                                    <i class="fa-solid fa-circle-check"></i> Confirm Verification Code
                                </button>
                            </form>

                            <form action="{{ route('artist.verify.email.send') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-block" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-rotate-right"></i> Resend Code
                                </button>
                            </form>
                        @else
                            {{-- Initial state — send code button --}}
                            <form action="{{ route('artist.verify.email.send') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa-solid fa-paper-plane"></i> Send Verification Code to Email
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- DOCUMENT UPLOAD PANEL --}}
                    <div id="panelDocVerify" style="display: none;">
                        <form action="{{ route('artist.verify') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="form-label" for="doc_type">Document Type</label>
                                <select id="doc_type" name="doc_type" class="form-select" required>
                                    <option value="National ID">National Identity Card / ID Card</option>
                                    <option value="Passport">International Passport</option>
                                    <option value="Drivers License">Driver's License</option>
                                    <option value="Label Certificate">Label Incorporation Document</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="doc_file">Upload Document Scan</label>
                                <input type="file" id="doc_file" name="doc_file" class="form-input" required>
                                <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.35rem;">Supported formats: PDF, JPEG, PNG (Max 5MB)</small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Submit Documents for Review
                            </button>
                        </form>
                    </div>
                @else
                    <div style="text-align: center; color: var(--success); padding: 2rem 0;">
                        <i class="fa-solid fa-circle-check" style="font-size: 3.5rem; margin-bottom: 1rem;"></i>
                        <h4>Your account is fully verified!</h4>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.25rem;">You have unrestricted access to all major streaming platforms distribution portals.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Social Media configuration -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-share-nodes"></i> Artist Biography & Social Links</h3>
            </div>
            <div class="card-body">
                @if(!$artist)
                    <p style="color: var(--text-muted); text-align: center; padding: 2rem;">Please submit your verification documents first or upload music to create an artist profile.</p>
                @else
                    <form action="{{ route('artist.profile.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="artist_id" value="{{ $artist->id }}">

                        <div class="form-group">
                            <label class="form-label" for="artist_bio">Biography</label>
                            <textarea id="artist_bio" name="bio" class="form-textarea" rows="4" placeholder="Tell the world about your music...">{{ $artist->bio }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contact_info">Public Booking Contact Info</label>
                            <input type="text" id="contact_info" name="contact_info" class="form-input" value="{{ $artist->contact_info }}" placeholder="e.g. bookings@myband.com">
                        </div>

                        @php
                            $socials = $artist->social_links ?? [];
                        @endphp

                        <div class="form-group">
                            <label class="form-label" for="facebook"><i class="fa-brands fa-facebook" style="color: #1877f2; margin-right: 0.25rem;"></i> Facebook Page URL</label>
                            <input type="url" id="facebook" name="facebook" class="form-input" value="{{ $socials['facebook'] ?? '' }}" placeholder="https://facebook.com/myprofile">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="twitter"><i class="fa-brands fa-x-twitter" style="color: #fff; margin-right: 0.25rem;"></i> X (Twitter) URL</label>
                            <input type="url" id="twitter" name="twitter" class="form-input" value="{{ $socials['twitter'] ?? '' }}" placeholder="https://x.com/myprofile">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="instagram"><i class="fa-brands fa-instagram" style="color: #e1306c; margin-right: 0.25rem;"></i> Instagram URL</label>
                            <input type="url" id="instagram" name="instagram" class="form-input" value="{{ $socials['instagram'] ?? '' }}" placeholder="https://instagram.com/myprofile">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="spotify"><i class="fa-brands fa-spotify" style="color: #1ed760; margin-right: 0.25rem;"></i> Spotify Artist URL</label>
                            <input type="url" id="spotify" name="spotify" class="form-input" value="{{ $socials['spotify'] ?? '' }}" placeholder="https://open.spotify.com/artist/... ">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-user-check"></i> Save Artist Profile
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tab 3: Managed Artists (Labels/Distributors only) -->
@if(auth()->user()->isRecordLabel() || auth()->user()->isDistributor())
    <div id="artists-tab" class="tab-content-panel" style="display: none;">
        <div class="grid-cols-2">
            <!-- List of Artists -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registered Artists</h3>
                </div>
                <div class="card-body">
                    @if($artists->isEmpty())
                        <p style="color: var(--text-muted); text-align: center; padding: 2rem;">No artists registered yet. Create one on the right to start uploading catalog releases.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Bio Summary</th>
                                        <th>Verification</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($artists as $managed)
                                        <tr>
                                            <td style="font-weight: bold;">{{ $managed->name }}</td>
                                            <td style="color: var(--text-secondary); font-size: 0.85rem;">
                                                {{ Str::limit($managed->bio ?? 'No bio filled yet.', 50) }}
                                            </td>
                                            <td>
                                                @if($managed->verification_status === 'verified')
                                                    <span class="badge badge-approved">Verified</span>
                                                @elseif($managed->verification_status === 'pending')
                                                    <span class="badge badge-pending">Pending</span>
                                                @else
                                                    <span class="badge badge-rejected">Unverified</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Create Artist Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-user-plus"></i> Register New Managed Artist</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('artist.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="artist_name">Artist Stage Name</label>
                            <input type="text" id="artist_name" name="name" class="form-input" placeholder="e.g. The Beats Maker" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="artist_desc">Artist Biography</label>
                            <textarea id="artist_desc" name="bio" class="form-textarea" rows="3" placeholder="Brief biography of the artist..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contact">Artist Contact Info / Legal Info</label>
                            <input type="text" id="contact" name="contact_info" class="form-input" placeholder="e.g. manager@artist.com">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-plus"></i> Add Artist Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')
<script>
    function switchTab(tabId, btn) {
        // Hide all tab panels
        var panels = document.getElementsByClassName('tab-content-panel');
        for (var i = 0; i < panels.length; i++) {
            panels[i].style.display = 'none';
        }
        
        // Show selected panel
        document.getElementById(tabId).style.display = 'block';

        // Reset styling for all buttons
        var buttons = document.getElementsByClassName('tab-btn');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('active');
            buttons[i].style.borderBottom = 'none';
            buttons[i].style.color = 'var(--text-secondary)';
        }

        // Highlight selected button
        btn.classList.add('active');
        btn.style.borderBottom = '2px solid var(--primary)';
        btn.style.color = 'var(--text-primary)';
    }

    function switchVerifyTab(method) {
        var emailPanel = document.getElementById('panelEmailVerify');
        var docPanel = document.getElementById('panelDocVerify');
        var emailTab = document.getElementById('tabEmailVerify');
        var docTab = document.getElementById('tabDocVerify');

        if (emailPanel && docPanel && emailTab && docTab) {
            if (method === 'email') {
                emailPanel.style.display = 'block';
                docPanel.style.display = 'none';
                
                emailTab.classList.add('active');
                emailTab.style.color = 'var(--primary)';
                emailTab.style.borderBottomColor = 'var(--primary)';
                
                docTab.classList.remove('active');
                docTab.style.color = 'var(--text-muted)';
                docTab.style.borderBottomColor = 'transparent';
            } else {
                emailPanel.style.display = 'none';
                docPanel.style.display = 'block';
                
                emailTab.classList.remove('active');
                emailTab.style.color = 'var(--text-muted)';
                emailTab.style.borderBottomColor = 'transparent';
                
                docTab.classList.add('active');
                docTab.style.color = 'var(--primary)';
                docTab.style.borderBottomColor = 'var(--primary)';
            }
        }
    }
</script>
@endsection
