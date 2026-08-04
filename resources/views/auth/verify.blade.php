<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account | CollegeMusic Distribution</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-split-wrapper">
        <!-- Left Side decorative visual panel -->
        <div class="auth-split-banner">
            <div class="auth-split-banner-logo">
                <i class="fa-solid fa-music"></i> CollegeMusic
            </div>
            
            <div class="auth-split-banner-content">
                <h1 class="auth-split-banner-title">Security & Catalog Safety Verification</h1>
                <p class="auth-split-banner-text">
                    We verify every account to prevent copyright duplicate submissions, secure artist earnings, and satisfy global DSP ingestion terms.
                </p>
                
                <div class="auth-split-testimonial">
                    <p class="auth-split-testimonial-quote">
                        "Running a record label with 15 sub-artists used to require complex sheets. SoundBridge allows me to upload under any artist, track stream geos, and handle withdrawals direct to mobile money accounts easily."
                    </p>
                    <div class="auth-split-testimonial-author">
                        Don Jazzy &bull; Label Executive (Mavin)
                    </div>
                </div>
            </div>
            
            <div class="auth-split-banner-footer">
                &copy; 2026 CollegeMusic Distribution System.
            </div>
        </div>
        
        <!-- Right Side form panel -->
        <div class="auth-split-form-container">
            <div class="auth-split-form-box">
                <div class="auth-split-header">
                    <h2 class="auth-split-title">Verify Account</h2>
                    <p class="auth-split-subtitle">Submit the 6-digit code sent to your email</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if(session('debug_verification_code'))
                    <div style="background-color: rgba(99, 102, 241, 0.08); border: 1px dashed var(--primary); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; text-align: center; font-size: 0.85rem;">
                        <i class="fa-solid fa-bug" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        <strong style="color: var(--text-primary);">Demo Code Helper:</strong> 
                        <span style="color: var(--primary); font-family: monospace; font-size: 1.15rem; font-weight: bold; margin-left: 0.5rem; letter-spacing: 0.1em;">{{ session('debug_verification_code') }}</span>
                    </div>
                @endif

                <form action="{{ route('verify') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Verify Email</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="e.g. john@example.com" value="{{ old('email', session('verify_email')) }}" required>
                        @error('email')
                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="code">Verification Code</label>
                        <input type="text" id="code" name="code" class="form-input" placeholder="6-digit code" style="text-align: center; letter-spacing: 0.4em; font-size: 1.25rem; font-weight: bold;" required>
                        @error('code')
                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                        <i class="fa-solid fa-circle-check"></i> Verify & Log In
                    </button>
                </form>

                <div style="margin-top: 2rem; text-align: center; font-size: 0.9rem;">
                    <span style="color: var(--text-secondary);">Incorrect registration details?</span>
                    <a href="{{ route('register') }}" style="font-weight: 600; margin-left: 0.25rem; color: var(--primary);">Start Over</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
