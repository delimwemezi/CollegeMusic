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
    <title>@yield('title', 'Dashboard') | CollegeMusic Distribution</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Extra inline helper classes for dropdowns and widgets */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 100;
            min-width: 280px;
            margin-top: 0.5rem;
            padding: 0.5rem 0;
            animation: fadeIn 0.2s ease;
        }
        .dropdown-menu.show {
            display: block;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            transition: var(--transition-fast);
        }
        .dropdown-item:hover {
            background-color: rgba(255,255,255,0.03);
            color: var(--text-primary);
        }
        .dropdown-item:last-child {
            border-bottom: none;
        }
        .dropdown-header {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }
        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: var(--radius-full);
            display: inline-block;
        }
    </style>
</head>
<body>
    @php
        $user = auth()->user();
        $notifications = [];
        if ($user) {
            $notifications = \App\Models\AuditLog::where('user_id', $user->id)
                ->whereIn('action', ['release_approved', 'release_rejected', 'release_distributed', 'withdrawal_completed', 'withdrawal_rejected', 'payment_completed'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }
    @endphp

    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-header">
                <span class="sidebar-logo"><i class="fa-solid fa-music" style="color: var(--primary);"></i> CollegeMusic</span>
                <button class="menu-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="sidebar-link">
                        <i class="fa-solid fa-chart-pie"></i> {{ __('messages.dashboard') }}
                    </a>
                </li>
                
                @if($user && ($user->isArtist() || $user->isRecordLabel() || $user->isDistributor()))
                    <li class="sidebar-item {{ Request::routeIs('catalogue') ? 'active' : '' }}">
                        <a href="{{ route('catalogue') }}" class="sidebar-link">
                            <i class="fa-solid fa-compact-disc"></i> {{ __('messages.catalogue') }}
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('releases.create') ? 'active' : '' }}">
                        <a href="{{ route('releases.create') }}" class="sidebar-link">
                            <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('messages.distribute') }}
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('finance') ? 'active' : '' }}">
                        <a href="{{ route('finance') }}" class="sidebar-link">
                            <i class="fa-solid fa-wallet"></i> {{ __('messages.finance') }}
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('analytics') ? 'active' : '' }}">
                        <a href="{{ route('analytics') }}" class="sidebar-link">
                            <i class="fa-solid fa-chart-line"></i> {{ __('messages.analytics') }}
                        </a>
                    </li>
                @endif
                
                <li class="sidebar-item {{ Request::routeIs('search') ? 'active' : '' }}">
                    <a href="{{ route('search') }}" class="sidebar-link">
                        <i class="fa-solid fa-magnifying-glass"></i> {{ __('messages.search') }}
                    </a>
                </li>

                <li class="sidebar-item {{ Request::routeIs('profile.edit') ? 'active' : '' }}">
                    <a href="{{ route('profile.edit') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-gear"></i> {{ __('messages.settings') }}
                    </a>
                </li>

                @if($user && $user->isAdmin())
                    <li class="sidebar-item" style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding: 0 1rem;">
                        <span style="font-size: 0.7rem; font-weight: bold; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('messages.admin') }}</span>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('admin') ? 'active' : '' }}">
                        <a href="{{ route('admin') }}" class="sidebar-link">
                            <i class="fa-solid fa-gauge-high"></i> {{ __('messages.admin') }}
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('admin.users') ? 'active' : '' }}">
                        <a href="{{ route('admin.users') }}" class="sidebar-link">
                            <i class="fa-solid fa-users"></i> Users Control
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('admin.releases') ? 'active' : '' }}">
                        <a href="{{ route('admin.releases') }}" class="sidebar-link">
                            <i class="fa-solid fa-clipboard-check"></i> Release Approvals
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('admin.artists') ? 'active' : '' }}">
                        <a href="{{ route('admin.artists') }}" class="sidebar-link">
                            <i class="fa-solid fa-id-card"></i> Artist Verifications
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('admin.payments') ? 'active' : '' }}">
                        <a href="{{ route('admin.payments') }}" class="sidebar-link">
                            <i class="fa-solid fa-money-bill-transfer"></i> Payout Processing
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('admin.logs') ? 'active' : '' }}">
                        <a href="{{ route('admin.logs') }}" class="sidebar-link">
                            <i class="fa-solid fa-shield-halved"></i> Audit Security Logs
                        </a>
                    </li>
                @endif
            </ul>

            <div class="sidebar-footer">
                @if($user)
                    <div class="user-profile-widget">
                        @if($user->artist && $user->artist->profile_picture)
                            <img src="{{ asset('storage/' . $user->artist->profile_picture) }}" alt="Avatar" class="profile-avatar">
                        @else
                            <div class="profile-avatar" style="background-color: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; border: 2px solid var(--border-color);">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                        <div class="profile-info">
                            <div class="profile-name">{{ $user->name }}</div>
                            <div class="profile-role">{{ str_replace('_', ' ', $user->role) }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="app-content">
            <!-- Header Bar -->
            <header class="app-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="menu-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 500;">
                        @yield('header_title', 'Music Distribution Platform')
                    </h3>
                </div>
                
                <div class="header-actions" style="display: flex; align-items: center; gap: 1rem;">
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

                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <div class="notification-bell" onclick="toggleDropdown('notificationsDropdown')">
                            <i class="fa-solid fa-bell"></i>
                            @if(count($notifications) > 0)
                                <span class="notification-badge"></span>
                            @endif
                        </div>
                        <div class="dropdown-menu" id="notificationsDropdown">
                            <div class="dropdown-header">System Notifications</div>
                            @forelse($notifications as $notif)
                                <div class="dropdown-item">
                                    @if(str_contains($notif->action, 'approve'))
                                        <span class="badge-dot" style="background-color: var(--success);"></span>
                                    @elseif(str_contains($notif->action, 'reject'))
                                        <span class="badge-dot" style="background-color: var(--danger);"></span>
                                    @elseif(str_contains($notif->action, 'payment') || str_contains($notif->action, 'withdrawal'))
                                        <span class="badge-dot" style="background-color: var(--purple);"></span>
                                    @else
                                        <span class="badge-dot" style="background-color: var(--info);"></span>
                                    @endif
                                    <div>
                                        <p style="color: var(--text-primary); font-weight: 500; font-size: 0.8rem; line-height: 1.2;">{{ $notif->description }}</p>
                                        <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $notif->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="dropdown-item" style="text-align: center; color: var(--text-muted); justify-content: center; padding: 1.5rem;">
                                    No new notifications.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Sign Out Link -->
                    <a href="{{ route('logout') }}" class="btn btn-secondary btn-sm" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #fca5a5;">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </div>
            </header>

            <!-- Main Content Container -->
            <div class="main-container">
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

                @if(session('danger') || session('error'))
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <span>{{ session('danger') ?? session('error') }}</span>
                    </div>
                @endif

                @yield('content')

                <!-- Dashboard Footer with Home Shortcut -->
                <footer class="app-dashboard-footer" style="margin-top: 3.5rem; padding: 1.5rem 0 1rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: var(--text-muted); flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <span>&copy; {{ date('Y') }} CollegeMusic Global Music Distribution. All rights reserved.</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1.25rem;">
                        <a href="{{ url('/') }}" style="color: var(--primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;" title="Go to CollegeMusic Home Page">
                            <i class="fa-solid fa-house"></i> Home Page
                        </a>
                        <span style="color: var(--border-color);">•</span>
                        <a href="{{ route('dashboard') }}" style="color: var(--text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <i class="fa-solid fa-gauge-high"></i> Dashboard
                        </a>
                        <span style="color: var(--border-color);">•</span>
                        <a href="{{ route('catalogue') }}" style="color: var(--text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <i class="fa-solid fa-compact-disc"></i> Catalogue
                        </a>
                        <span style="color: var(--border-color);">•</span>
                        <a href="{{ route('profile.edit') }}" style="color: var(--text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <i class="fa-solid fa-user-gear"></i> Settings
                        </a>
                        <span style="color: var(--border-color);">•</span>
                        <a href="javascript:window.scrollTo({top:0, behavior:'smooth'})" style="color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                            <i class="fa-solid fa-arrow-up"></i> Top
                        </a>
                    </div>
                </footer>
            </div>
        </main>
    </div>

    <!-- Toggle Handlers for sidebar and dropdowns -->
    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById('appSidebar');
            sidebar.classList.toggle('open');
        }

        function toggleDropdown(id) {
            var dropdown = document.getElementById(id);
            dropdown.classList.toggle('show');
            
            // Close other dropdowns
            window.onclick = function(event) {
                if (!event.target.matches('.notification-bell') && !event.target.matches('.fa-bell')) {
                    var dropdowns = document.getElementsByClassName("dropdown-menu");
                    for (var i = 0; i < dropdowns.length; i++) {
                        var openDropdown = dropdowns[i];
                        if (openDropdown.classList.contains('show')) {
                            openDropdown.classList.remove('show');
                        }
                    }
                }
            }
        }

        function toggleTheme(event) {
            const html = document.documentElement;
            
            // Try to get click coordinates or fallback to the button's center coordinates
            let x = window.innerWidth / 2;
            let y = window.innerHeight / 2;
            
            if (event && event.clientX && event.clientY) {
                x = event.clientX;
                y = event.clientY;
            } else {
                const btn = document.getElementById('themeToggle');
                if (btn) {
                    const rect = btn.getBoundingClientRect();
                    x = rect.left + rect.width / 2;
                    y = rect.top + rect.height / 2;
                }
            }
            
            // Fallback for browsers that do not support View Transitions or prefer-reduced-motion
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

        // Language Switcher curtain transition controller
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
                    
                    // Trigger pill slide animation locally immediately
                    if (slider) {
                        if (targetLocale === 'sw') {
                            slider.style.transform = 'translateX(40px)';
                        } else {
                            slider.style.transform = 'translateX(0px)';
                        }
                    }
                    langBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    // Set localized curtain sweep message
                    if (textSpan) {
                        textSpan.innerText = targetLocale === 'sw' ? 'Inatafsiri...' : 'Translating...';
                    }

                    // Show overlay sweep
                    if (overlay) {
                        overlay.classList.add('active');
                    }

                    sessionStorage.setItem('langSwitching', 'true');
                    sessionStorage.setItem('targetLocale', targetLocale);

                    // Redirect after curtain covers screen
                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 600);
                });
            });

            // Handle incoming entrance sweep on new page load
            if (sessionStorage.getItem('langSwitching') === 'true') {
                if (overlay) {
                    const curtain = overlay.querySelector('.lang-transition-curtain');
                    if (curtain) {
                        curtain.style.transition = 'none';
                    }
                    overlay.classList.add('active');
                    
                    setTimeout(() => {
                        if (curtain) {
                            curtain.style.transition = '';
                        }
                        overlay.classList.remove('active');
                        overlay.classList.add('exit');
                        
                        const mainContainer = document.querySelector('.main-container') || document.body;
                        if (mainContainer) {
                            mainContainer.animate(
                                [
                                    { opacity: 0, transform: 'translateY(15px)' },
                                    { opacity: 1, transform: 'translateY(0)' }
                                ],
                                {
                                    duration: 500,
                                    easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)'
                                }
                            );
                        }
                        
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
