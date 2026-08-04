<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        // Immediately load theme preference to prevent color flashing
        const theme = localStorage.getItem('theme') || 'dark';
        if (theme === 'light') {
            document.documentElement.classList.add('light-theme');
        }
    </script>
    <title>@yield('title', 'Explore Music') | CollegeMusic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        :root {
            --bg-main: #0b0f19;
            --bg-card: #111827;
            --primary: #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.15);
            --accent: #10b981;
            --warning: #f59e0b;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --text-muted: #6b7280;
            --border-color: rgba(255, 255, 255, 0.05);
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --radius-full: 9999px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-heading: 'Outfit', sans-serif;
        }

        /* Light Theme variables override */
        .light-theme {
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --primary: #4f46e5;
            --primary-glow: rgba(79, 70, 229, 0.1);
            --accent: #059669;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border-color: rgba(15, 23, 42, 0.08);
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            font-family: var(--font-main);
            line-height: 1.6;
            overflow-x: hidden;
            padding-top: 100px; /* Offset for sticky navbar */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            width: 100%;
        }

        /* Sticky Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background-color: rgba(11, 15, 25, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            z-index: 1000;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .light-theme .navbar {
            background-color: rgba(248, 250, 252, 0.8);
        }

        .navbar-content {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: var(--font-heading);
            font-size: 1.4rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: var(--text-primary);
        }

        .logo i {
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            gap: 2.25rem;
            list-style: none;
            align-items: center;
        }

        .nav-link {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Theme Toggle styles */
        .theme-toggle-btn {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-full);
            width: 62px;
            height: 34px;
            position: relative;
            cursor: pointer;
            padding: 2px;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .theme-toggle-btn:hover {
            transform: scale(1.05);
            border-color: var(--primary);
        }
        .theme-toggle-btn:active {
            transform: scale(0.95);
        }
        .theme-toggle-circle {
            background-color: var(--primary);
            width: 28px;
            height: 28px;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            position: absolute;
            left: 2px;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.4s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .theme-toggle-circle i {
            font-size: 0.85rem;
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .theme-toggle-circle .light-icon {
            display: none;
        }
        .theme-toggle-circle .dark-icon {
            display: block;
        }
        
        .light-theme .theme-toggle-circle {
            transform: translateX(28px);
            background-color: #f59e0b;
        }
        .light-theme .theme-toggle-circle .dark-icon {
            display: none;
        }
        .light-theme .theme-toggle-circle .light-icon {
            display: block;
        }
        .light-theme .theme-toggle-circle i {
            transform: rotate(360deg);
        }

        /* Language Switcher styles */
        .lang-switch-wrapper {
            position: relative;
            display: flex;
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-full);
            padding: 2px;
            width: 84px;
            height: 34px;
            align-items: center;
            cursor: pointer;
            overflow: hidden;
        }
        .lang-switch-btn {
            flex: 1;
            text-align: center;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-decoration: none;
            z-index: 2;
            transition: color 0.3s ease;
            line-height: 28px;
            height: 28px;
        }
        .lang-switch-btn.active {
            color: #ffffff;
        }
        .lang-switch-slider {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 38px;
            height: 28px;
            background-color: var(--primary);
            border-radius: var(--radius-full);
            z-index: 1;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        html[lang="sw"] .lang-switch-slider {
            transform: translateX(40px);
        }

        /* Language Translation Curtain Sweep Overlay */
        .lang-transition-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 100000;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
        }
        .lang-transition-curtain {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 50% 50%, var(--bg-card), var(--bg-main));
            transform: translateY(100%);
            transition: transform 0.6s cubic-bezier(0.77, 0, 0.175, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-top: 4px solid var(--primary);
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.3);
        }
        .lang-transition-logo {
            font-family: var(--font-heading);
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }
        .lang-transition-logo i {
            color: var(--primary);
            text-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
        }

        .lang-transition-overlay.active {
            visibility: visible;
            pointer-events: all;
        }
        .lang-transition-overlay.active .lang-transition-curtain {
            transform: translateY(0);
        }
        .lang-transition-overlay.active .lang-transition-logo {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.25s;
        }

        .lang-transition-overlay.exit {
            visibility: visible;
            pointer-events: all;
        }
        .lang-transition-overlay.exit .lang-transition-curtain {
            transform: translateY(-100%);
        }
        .lang-transition-overlay.exit .lang-transition-logo {
            opacity: 0;
            transform: translateY(-30px);
        }

        /* Custom transitions support */
        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation: none;
            mix-blend-mode: normal;
        }
        ::view-transition-old(root) {
            z-index: 1;
        }
        ::view-transition-new(root) {
            z-index: 9999;
        }
        .light-theme::view-transition-old(root) {
            z-index: 9999;
        }
        .light-theme::view-transition-new(root) {
            z-index: 1;
        }

        .theme-in-transition * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease !important;
        }

        /* Footer */
        footer {
            margin-top: auto;
            border-top: 1px solid var(--border-color);
            padding: 3rem 0;
            background-color: rgba(17, 24, 39, 0.4);
            text-align: center;
        }

        .footer-logo {
            font-family: var(--font-heading);
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sticky Navigation -->
    <nav class="navbar">
        <div class="container navbar-content">
            <a href="{{ route('home') }}" class="logo">
                <i class="fa-solid fa-music"></i> CollegeMusic
            </a>
            <ul class="nav-links">
                <li><a href="{{ route('home') }}#features" class="nav-link">{{ __('messages.features') }}</a></li>
                <li><a href="{{ route('home') }}#platforms" class="nav-link">{{ __('messages.platforms') }}</a></li>
                <li><a href="{{ route('home') }}#pricing" class="nav-link">{{ __('messages.pricing') }}</a></li>
                <li><a href="{{ route('explore') }}" class="nav-link {{ Request::routeIs('explore') ? 'active' : '' }}" style="color: var(--primary); font-weight: bold;"><i class="fa-solid fa-compass"></i> {{ __('messages.explore_music') }}</a></li>
            </ul>
            <div class="nav-actions">
                <!-- Theme Switcher -->
                <button class="theme-toggle-btn" id="themeToggle" onclick="toggleTheme(event)" aria-label="Toggle Theme" title="{{ __('messages.theme') }}">
                    <span class="theme-toggle-circle">
                        <i class="fa-solid fa-moon dark-icon"></i>
                        <i class="fa-solid fa-sun light-icon"></i>
                    </span>
                </button>

                <!-- Language Switcher -->
                <div class="lang-switch-wrapper" title="{{ __('messages.language') }}">
                    <a href="{{ route('locale.switch', 'en') }}" class="lang-switch-btn {{ App::getLocale() == 'en' ? 'active' : '' }}">EN</a>
                    <a href="{{ route('locale.switch', 'sw') }}" class="lang-switch-btn {{ App::getLocale() == 'sw' ? 'active' : '' }}">SW</a>
                    <div class="lang-switch-slider"></div>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm" style="padding: 0.55rem 1.25rem; font-size: 0.85rem;">
                        <i class="fa-solid fa-chart-pie"></i> {{ __('messages.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-link" style="margin-right: 0.25rem; font-weight: 600;">{{ __('messages.sign_in') }}</a>
                    <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 0.55rem 1.25rem; font-size: 0.85rem;">{{ __('messages.get_started') }}</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-logo">
                <i class="fa-solid fa-music" style="color: var(--primary);"></i> CollegeMusic
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">
                {{ __('messages.footer_tagline') }}
            </p>
            <p style="color: var(--text-muted); font-size: 0.75rem;">
                &copy; {{ date('Y') }} CollegeMusic. {{ __('messages.all_rights_reserved') }}
            </p>
        </div>
    </footer>

    <!-- Theme and Lang transition handling -->
    <script>
        function toggleTheme(event) {
            const html = document.documentElement;
            let x = window.innerWidth / 2;
            let y = window.innerHeight / 2;
            
            if (event && event.clientX && event.clientY) {
                x = event.clientX;
                y = event.clientY;
            }
            
            if (!document.startViewTransition || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                html.classList.add('theme-in-transition');
                html.classList.toggle('light-theme');
                const theme = html.classList.contains('light-theme') ? 'light' : 'dark';
                localStorage.setItem('theme', theme);
                setTimeout(() => {
                    html.classList.remove('theme-in-transition');
                }, 300);
                return;
            }
            
            const endRadius = Math.hypot(
                Math.max(x, window.innerWidth - x),
                Math.max(y, window.innerHeight - y)
            );
            
            const transition = document.startViewTransition(() => {
                html.classList.toggle('light-theme');
                const theme = html.classList.contains('light-theme') ? 'light' : 'dark';
                localStorage.setItem('theme', theme);
            });
            
            transition.ready.then(() => {
                const clipPath = [
                    `circle(0px at ${x}px ${y}px)`,
                    `circle(${endRadius}px at ${x}px ${y}px)`
                ];
                
                document.documentElement.animate(
                    {
                        clipPath: html.classList.contains('light-theme') ? clipPath : [...clipPath].reverse()
                    },
                    {
                        duration: 500,
                        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                        pseudoElement: html.classList.contains('light-theme')
                            ? '::view-transition-new(root)'
                            : '::view-transition-old(root)'
                    }
                );
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const langBtns = document.querySelectorAll('.lang-switch-btn');
            const overlay = document.getElementById('langTransitionOverlay');
            const textSpan = document.getElementById('langTransitionText');
            const slider = document.querySelector('.lang-switch-slider');

            langBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    if (btn.classList.contains('active')) return;
                    
                    e.preventDefault();
                    const targetUrl = btn.getAttribute('href');
                    const isSw = targetUrl.includes('/sw');
                    const targetLocale = isSw ? 'sw' : 'en';
                    
                    if (slider) {
                        slider.style.transform = targetLocale === 'sw' ? 'translateX(40px)' : 'translateX(0px)';
                    }
                    langBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    if (textSpan) {
                        textSpan.innerText = targetLocale === 'sw' ? 'Inatafsiri...' : 'Translating...';
                    }

                    if (overlay) overlay.classList.add('active');
                    sessionStorage.setItem('langSwitching', 'true');
                    sessionStorage.setItem('targetLocale', targetLocale);

                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 600);
                });
            });

            if (sessionStorage.getItem('langSwitching') === 'true') {
                if (overlay) {
                    const curtain = overlay.querySelector('.lang-transition-curtain');
                    if (curtain) curtain.style.transition = 'none';
                    overlay.classList.add('active');
                    
                    setTimeout(() => {
                        if (curtain) curtain.style.transition = '';
                        overlay.classList.remove('active');
                        overlay.classList.add('exit');
                        
                        setTimeout(() => {
                            overlay.classList.remove('exit');
                            sessionStorage.removeItem('langSwitching');
                            sessionStorage.removeItem('targetLocale');
                        }, 600);
                    }, 50);
                } else {
                    sessionStorage.removeItem('langSwitching');
                    sessionStorage.removeItem('targetLocale');
                }
            }
        });
    </script>
    
    <!-- Language transition curtain sweep overlay -->
    <div class="lang-transition-overlay" id="langTransitionOverlay">
        <div class="lang-transition-curtain">
            <div class="lang-transition-logo">
                <i class="fa-solid fa-music"></i>
                <span id="langTransitionText">Translating...</span>
            </div>
        </div>
    </div>
    
    @yield('scripts')
</body>
</html>
