<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Password | CollegeMusic Distribution</title>
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
                <h1 class="auth-split-banner-title">Restore your account credentials.</h1>
                <p class="auth-split-banner-text">
                    Follow the standard password recovery sequence. We will send a secure token code to verify your ownership.
                </p>
                
                <div class="auth-split-testimonial">
                    <p class="auth-split-testimonial-quote">
                        "The Premium Plan is a no-brainer. For $49.99 a year, I distribute unlimited tracks to Spotify and Apple. I keep 100% of what my streams make. It is the best deal on the internet."
                    </p>
                    <div class="auth-split-testimonial-author">
                        Tems Baby &bull; Grammy-Winning Singer
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
                    <h2 class="auth-split-title">Recover Password</h2>
                    <p class="auth-split-subtitle">Get a code to reset your account password</p>
                </div>

                <form action="{{ route('recover') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="login">Email Address or Phone Number</label>
                        <input type="text" id="login" name="login" class="form-input" placeholder="e.g. john@example.com or +1234567" value="{{ old('login') }}" required>
                        @error('login')
                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                        <i class="fa-solid fa-paper-plane"></i> Send Recovery Code
                    </button>
                </form>

                <div style="margin-top: 2rem; text-align: center; font-size: 0.9rem;">
                    <span style="color: var(--text-secondary);">Remember your password?</span>
                    <a href="{{ route('login') }}" style="font-weight: 600; margin-left: 0.25rem; color: var(--primary);">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
