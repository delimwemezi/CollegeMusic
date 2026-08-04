@php
    // Determine initial form state based on route and errors
    $activeState = request()->is('register') ? 'register' : 'login';
    if ($errors->any()) {
        if ($errors->has('name') || $errors->has('email') || $errors->has('phone') || $errors->has('role') || $errors->has('password_confirmation')) {
            $activeState = 'register';
        } else {
            $activeState = 'login';
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="authPageTitle">@if($activeState === 'register') Create Account @else Login @endif | CollegeMusic Distribution</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Ambient Background Glows */
        .glow-blob {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12), transparent 70%);
            filter: blur(80px);
            z-index: 1;
            pointer-events: none;
            animation: floatGlow 25s infinite alternate ease-in-out;
        }
        .blob-1 {
            top: -15%;
            left: -15%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15), transparent 70%);
            animation-duration: 20s;
        }
        .blob-2 {
            bottom: -15%;
            right: -15%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1), transparent 70%);
            animation-duration: 25s;
            animation-delay: -5s;
        }
        .blob-3 {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, rgba(139, 92, 246, 0.08), transparent 70%);
            animation-duration: 30s;
            animation-delay: -10s;
        }

        @keyframes floatGlow {
            0% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(50px, -50px) scale(1.1); }
            100% { transform: translate(-30px, 40px) scale(0.9); }
        }

        /* Custom scrollbars for side panels */
        .auth-form-panel::-webkit-scrollbar {
            width: 6px;
        }
        .auth-form-panel::-webkit-scrollbar-track {
            background: transparent;
        }
        .auth-form-panel::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 3px;
        }
        .auth-form-panel::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Testimonial styles overrides for premium aesthetic */
        .auth-split-testimonial {
            background: rgba(255, 255, 255, 0.02) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="auth-split-wrapper {{ $activeState === 'register' ? 'active-register' : 'active-login' }}" id="authWrapper">
        <!-- Ambient Glowing Backgrounds -->
        <div class="glow-blob blob-1"></div>
        <div class="glow-blob blob-2"></div>
        <div class="glow-blob blob-3"></div>

        <!-- Sliding Overlay Banner -->
        <div class="auth-split-banner">
            <!-- Floating graphic elements -->
            <div class="floating-auth-icon" style="font-size: 8rem; top: 12%; right: 10%; animation-duration: 12s; animation-delay: 0s;"><i class="fa-solid fa-music"></i></div>
            <div class="floating-auth-icon" style="font-size: 10rem; bottom: 8%; left: 10%; animation-duration: 16s; animation-delay: 1s;"><i class="fa-solid fa-headphones"></i></div>
            <div class="floating-auth-icon" style="font-size: 6rem; top: 48%; right: 40%; animation-duration: 14s; animation-delay: 3s;"><i class="fa-solid fa-volume-high"></i></div>

            <!-- Header logo -->
            <div class="auth-split-banner-logo">
                <i class="fa-solid fa-music"></i> CollegeMusic
            </div>
            
            <!-- Sliding Banner Text/Quotes -->
            <div class="banner-text-slider">
                <!-- Login Banner Content -->
                <div class="banner-content-panel login-banner-content">
                    <h1 class="auth-split-banner-title">The gateway to global streaming channels.</h1>
                    <p class="auth-split-banner-text">
                        Ship your music to Spotify, Apple, Amazon, and 150+ digital stores while keeping 100% of your earnings.
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

                <!-- Register Banner Content -->
                <div class="banner-content-panel register-banner-content">
                    <h1 class="auth-split-banner-title">Start your music distribution journey.</h1>
                    <p class="auth-split-banner-text">
                        Establish your profile, organize catalogs, distribute tracks, and track royalties across the globe.
                    </p>
                    
                    <div class="auth-split-testimonial">
                        <p class="auth-split-testimonial-quote">
                            "SoundBridge transformed the way I handle my releases. The upload wizard checked my cover dimensions and ISRC automatically, and I received approval in 24 hours. The available balance updates are so transparent."
                        </p>
                        <div class="auth-split-testimonial-author">
                            Burna Boy &bull; Afrobeats Musician
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer copyright -->
            <div class="auth-split-banner-footer">
                &copy; 2026 CollegeMusic Distribution System.
            </div>
        </div>

        <!-- FORM SIDE PANELS -->
        
        <!-- Registration Panel (Left side, covers left 50% space, revealed when banner slides to the right) -->
        <div class="auth-form-panel register-panel">
            <div class="auth-split-form-box">
                <div class="auth-split-header">
                    <h2 class="auth-split-title">Create Account</h2>
                    <p class="auth-split-subtitle">Sign up to distribute your music catalogs</p>
                </div>

                @if(session('success') && $activeState === 'register')
                    <div class="alert alert-success animate-fade-up">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="auth-form">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="reg_name">Full Name</label>
                        <input type="text" id="reg_name" name="name" class="form-input" placeholder="e.g. John Doe" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reg_email">Email Address</label>
                        <input type="email" id="reg_email" name="email" class="form-input" placeholder="e.g. john@example.com" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reg_phone">Phone Number</label>
                        <input type="text" id="reg_phone" name="phone" class="form-input" placeholder="e.g. +1234567890" value="{{ old('phone') }}" required>
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reg_role">Account Type</label>
                        <select id="reg_role" name="role" class="form-select" required>
                            <option value="artist" {{ old('role') == 'artist' ? 'selected' : '' }}>Artist</option>
                            <option value="record_label" {{ old('role') == 'record_label' ? 'selected' : '' }}>Record Label</option>
                        </select>
                        @error('role')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reg_password">Password</label>
                        <div style="position: relative;">
                            <input type="password" id="reg_password" name="password" class="form-input" placeholder="••••••••" style="padding-right: 2.75rem;" required>
                            <button type="button" onclick="togglePasswordVisibility('reg_password', this)" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            @if($activeState === 'register')
                                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                            @endif
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reg_password_confirmation">Confirm Password</label>
                        <div style="position: relative;">
                            <input type="password" id="reg_password_confirmation" name="password_confirmation" class="form-input" placeholder="••••••••" style="padding-right: 2.75rem;" required>
                            <button type="button" onclick="togglePasswordVisibility('reg_password_confirmation', this)" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-user-plus"></i> Register Account
                    </button>
                </form>

                <div class="auth-switch-text">
                    <span style="color: var(--text-secondary);">Already have an account?</span>
                    <a href="{{ route('login') }}" class="switch-link" id="switchToLogin">Sign In</a>
                </div>
            </div>
        </div>

        <!-- Login Panel (Right side, covers right 50% space, revealed when banner slides to the left) -->
        <div class="auth-form-panel login-panel">
            <div class="auth-split-form-box">
                <div class="auth-split-header">
                    <h2 class="auth-split-title">Welcome Back</h2>
                    <p class="auth-split-subtitle">Sign in to manage your music catalog</p>
                </div>

                @if(session('success') && $activeState === 'login')
                    <div class="alert alert-success animate-fade-up">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning animate-fade-up">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="auth-form">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="login_field">Email Address or Phone</label>
                        <input type="text" id="login_field" name="login" class="form-input" placeholder="e.g. john@example.com or +1234567" value="{{ old('login') }}" required>
                        @error('login')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <label class="form-label" for="login_password" style="margin-bottom: 0;">Password</label>
                            <a href="{{ route('recover.show') }}" style="font-size: 0.85rem; font-weight: 500; color: var(--primary);">Forgot password?</a>
                        </div>
                        <div style="position: relative;">
                            <input type="password" id="login_password" name="password" class="form-input" placeholder="••••••••" style="padding-right: 2.75rem;" required>
                            <button type="button" onclick="togglePasswordVisibility('login_password', this)" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            @if($activeState === 'login')
                                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                            @endif
                        @enderror
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem;">
                        <input type="checkbox" id="remember" name="remember" class="form-checkbox">
                        <label for="remember" class="form-checkbox-label" style="color: var(--text-secondary); font-size: 0.85rem;">Keep me logged in</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-right-to-bracket"></i> Sign In
                    </button>
                </form>

                <div class="auth-switch-text">
                    <span style="color: var(--text-secondary);">New to CollegeMusic?</span>
                    <a href="{{ route('register') }}" class="switch-link" id="switchToRegister">Create an Account</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-Side State Transition Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const authWrapper = document.getElementById('authWrapper');
            const switchToRegisterBtn = document.getElementById('switchToRegister');
            const switchToLoginBtn = document.getElementById('switchToLogin');
            const pageTitle = document.getElementById('authPageTitle');

            // Route URLs
            const LOGIN_PATH = "{{ route('login') }}";
            const REGISTER_PATH = "{{ route('register') }}";

            // Page Titles
            const LOGIN_TITLE = "Login | CollegeMusic Distribution";
            const REGISTER_TITLE = "Create Account | CollegeMusic Distribution";

            function showForm(state, updateHistory = true) {
                if (state === 'register') {
                    authWrapper.classList.remove('active-login');
                    authWrapper.classList.add('active-register');
                    pageTitle.innerText = REGISTER_TITLE;
                    document.title = REGISTER_TITLE;
                    
                    if (updateHistory) {
                        history.pushState({ state: 'register' }, REGISTER_TITLE, REGISTER_PATH);
                    }
                } else {
                    authWrapper.classList.remove('active-register');
                    authWrapper.classList.add('active-login');
                    pageTitle.innerText = LOGIN_TITLE;
                    document.title = LOGIN_TITLE;
                    
                    if (updateHistory) {
                        history.pushState({ state: 'login' }, LOGIN_TITLE, LOGIN_PATH);
                    }
                }
            }

            // Click Handlers
            switchToRegisterBtn.addEventListener('click', (e) => {
                e.preventDefault();
                showForm('register');
            });

            switchToLoginBtn.addEventListener('click', (e) => {
                e.preventDefault();
                showForm('login');
            });

            // Back/forward navigation integration
            window.addEventListener('popstate', (e) => {
                if (e.state && e.state.state) {
                    showForm(e.state.state, false);
                } else {
                    // Fallback to URL detection
                    const path = window.location.pathname;
                    if (path.includes('register')) {
                        showForm('register', false);
                    } else {
                        showForm('login', false);
                    }
                }
            });

            // Init history state
            const initialState = authWrapper.classList.contains('active-register') ? 'register' : 'login';
            const initialTitle = initialState === 'register' ? REGISTER_TITLE : LOGIN_TITLE;
            history.replaceState({ state: initialState }, initialTitle, window.location.href);
        });

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
