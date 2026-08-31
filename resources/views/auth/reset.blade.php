<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | CollegeMusic Distribution</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="auth-split-wrapper">
        <!-- Left Side decorative visual panel -->
        <div class="auth-split-banner">
            <div class="auth-split-banner-logo">
                <i class="fa-solid fa-music"></i> CollegeMusic
            </div>
            
            <div class="auth-split-banner-content">
                <h1 class="auth-split-banner-title">Reset your account password.</h1>
                <p class="auth-split-banner-text">
                    Enter the 6-digit recovery code shown in your session helper or email, and choose a new secure password.
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
        <div class="auth-split-form-container" style="align-items: flex-start; padding-top: 5rem; padding-bottom: 5rem; overflow-y: auto;">
            <div class="auth-split-form-box">
                <div class="auth-split-header" style="margin-bottom: 1.5rem;">
                    <h2 class="auth-split-title">Reset Password</h2>
                    <p class="auth-split-subtitle">Create a new secure password</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('reset') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="email">Verify Email</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="e.g. john@example.com" value="{{ old('email', session('reset_email')) }}" required>
                        @error('email')
                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="code">Recovery Code</label>
                        <input type="text" id="code" name="code" class="form-input" placeholder="6-digit code" style="text-align: center; letter-spacing: 0.4em; font-size: 1.25rem; font-weight: bold;" required>
                        @error('code')
                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="password">New Password</label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" style="padding-right: 2.75rem;" required>
                            <button type="button" onclick="togglePasswordVisibility('password', this)" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" for="password_confirmation">Confirm New Password</label>
                        <div style="position: relative;">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="••••••••" style="padding-right: 2.75rem;" required>
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-key"></i> Update Password
                    </button>
                </form>

                <div style="margin-top: 2rem; text-align: center; font-size: 0.9rem;">
                    <span style="color: var(--text-secondary);">Go back to</span>
                    <a href="{{ route('login') }}" style="font-weight: 600; margin-left: 0.25rem; color: var(--primary);">Login</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
