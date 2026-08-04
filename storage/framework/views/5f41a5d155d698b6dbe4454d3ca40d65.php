<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
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
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> | CollegeMusic Distribution</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
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
    <?php
        $user = auth()->user();
        $notifications = [];
        if ($user) {
            $notifications = \App\Models\AuditLog::where('user_id', $user->id)
                ->whereIn('action', ['release_approved', 'release_rejected', 'release_distributed', 'withdrawal_completed', 'withdrawal_rejected', 'payment_completed'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }
    ?>

    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-header">
                <span class="sidebar-logo"><i class="fa-solid fa-music" style="color: var(--primary);"></i> CollegeMusic</span>
                <button class="menu-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-item <?php echo e(Request::routeIs('dashboard') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link">
                        <i class="fa-solid fa-chart-pie"></i> <?php echo e(__('messages.dashboard')); ?>

                    </a>
                </li>
                
                <?php if($user && ($user->isArtist() || $user->isRecordLabel() || $user->isDistributor())): ?>
                    <li class="sidebar-item <?php echo e(Request::routeIs('catalogue') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('catalogue')); ?>" class="sidebar-link">
                            <i class="fa-solid fa-compact-disc"></i> <?php echo e(__('messages.catalogue')); ?>

                        </a>
                    </li>
                    <li class="sidebar-item <?php echo e(Request::routeIs('releases.create') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('releases.create')); ?>" class="sidebar-link">
                            <i class="fa-solid fa-cloud-arrow-up"></i> <?php echo e(__('messages.distribute')); ?>

                        </a>
                    </li>
                    <li class="sidebar-item <?php echo e(Request::routeIs('finance') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('finance')); ?>" class="sidebar-link">
                            <i class="fa-solid fa-wallet"></i> <?php echo e(__('messages.finance')); ?>

                        </a>
                    </li>
                    <li class="sidebar-item <?php echo e(Request::routeIs('analytics') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('analytics')); ?>" class="sidebar-link">
                            <i class="fa-solid fa-chart-line"></i> <?php echo e(__('messages.analytics')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="sidebar-item <?php echo e(Request::routeIs('search') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('search')); ?>" class="sidebar-link">
                        <i class="fa-solid fa-magnifying-glass"></i> <?php echo e(__('messages.search')); ?>

                    </a>
                </li>

                <li class="sidebar-item <?php echo e(Request::routeIs('profile.edit') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('profile.edit')); ?>" class="sidebar-link">
                        <i class="fa-solid fa-user-gear"></i> <?php echo e(__('messages.settings')); ?>

                    </a>
                </li>

                <?php if($user && $user->isAdmin()): ?>
                    <li class="sidebar-item" style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding: 0 1rem;">
                        <span style="font-size: 0.7rem; font-weight: bold; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;"><?php echo e(__('messages.admin')); ?></span>
                    </li>
                    <li class="sidebar-item <?php echo e(Request::routeIs('admin') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('admin')); ?>" class="sidebar-link">
                            <i class="fa-solid fa-gauge-high"></i> <?php echo e(__('messages.admin')); ?>

                        </a>
                    </li>
                    <li class="sidebar-item <?php echo e(Request::routeIs('admin.users') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('admin.users')); ?>" class="sidebar-link">
                            <i class="fa-solid fa-users"></i> Users Control
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo e(Request::routeIs('admin.releases') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('admin.releases')); ?>" class="sidebar-link">
                            <i class="fa-solid fa-clipboard-check"></i> Release Approvals
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo e(Request::routeIs('admin.artists') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('admin.artists')); ?>" class="sidebar-link">
                            <i class="fa-solid fa-id-card"></i> Artist Verifications
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo e(Request::routeIs('admin.payments') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('admin.payments')); ?>" class="sidebar-link">
                            <i class="fa-solid fa-money-bill-transfer"></i> Payout Processing
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo e(Request::routeIs('admin.logs') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('admin.logs')); ?>" class="sidebar-link">
                            <i class="fa-solid fa-shield-halved"></i> Audit Security Logs
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="sidebar-footer">
                <?php if($user): ?>
                    <div class="user-profile-widget">
                        <?php if($user->artist && $user->artist->profile_picture): ?>
                            <img src="<?php echo e(asset('storage/' . $user->artist->profile_picture)); ?>" alt="Avatar" class="profile-avatar">
                        <?php else: ?>
                            <div class="profile-avatar" style="background-color: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; border: 2px solid var(--border-color);">
                                <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

                            </div>
                        <?php endif; ?>
                        <div class="profile-info">
                            <div class="profile-name"><?php echo e($user->name); ?></div>
                            <div class="profile-role"><?php echo e(str_replace('_', ' ', $user->role)); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="app-content">
            <!-- Header Bar -->
            <header class="app-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="menu-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 500;">
                        <?php echo $__env->yieldContent('header_title', 'Music Distribution Platform'); ?>
                    </h3>
                </div>
                
                <div class="header-actions" style="display: flex; align-items: center; gap: 1rem;">
                    <!-- Theme Switcher -->
                    <button class="theme-toggle-btn" id="themeToggle" onclick="toggleTheme(event)" aria-label="Toggle Theme" title="<?php echo e(__('messages.theme')); ?>">
                        <span class="theme-toggle-circle">
                            <i class="fa-solid fa-moon dark-icon"></i>
                            <i class="fa-solid fa-sun light-icon"></i>
                        </span>
                    </button>

                    <!-- Language Switcher -->
                    <div class="lang-switch-wrapper" title="<?php echo e(__('messages.language')); ?>">
                        <a href="<?php echo e(route('locale.switch', 'en')); ?>" class="lang-switch-btn <?php echo e(App::getLocale() == 'en' ? 'active' : ''); ?>">EN</a>
                        <a href="<?php echo e(route('locale.switch', 'sw')); ?>" class="lang-switch-btn <?php echo e(App::getLocale() == 'sw' ? 'active' : ''); ?>">SW</a>
                        <div class="lang-switch-slider"></div>
                    </div>

                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <div class="notification-bell" onclick="toggleDropdown('notificationsDropdown')">
                            <i class="fa-solid fa-bell"></i>
                            <?php if(count($notifications) > 0): ?>
                                <span class="notification-badge"></span>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-menu" id="notificationsDropdown">
                            <div class="dropdown-header">System Notifications</div>
                            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="dropdown-item">
                                    <?php if(str_contains($notif->action, 'approve')): ?>
                                        <span class="badge-dot" style="background-color: var(--success);"></span>
                                    <?php elseif(str_contains($notif->action, 'reject')): ?>
                                        <span class="badge-dot" style="background-color: var(--danger);"></span>
                                    <?php elseif(str_contains($notif->action, 'payment') || str_contains($notif->action, 'withdrawal')): ?>
                                        <span class="badge-dot" style="background-color: var(--purple);"></span>
                                    <?php else: ?>
                                        <span class="badge-dot" style="background-color: var(--info);"></span>
                                    <?php endif; ?>
                                    <div>
                                        <p style="color: var(--text-primary); font-weight: 500; font-size: 0.8rem; line-height: 1.2;"><?php echo e($notif->description); ?></p>
                                        <span style="font-size: 0.7rem; color: var(--text-muted);"><?php echo e($notif->created_at->diffForHumans()); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="dropdown-item" style="text-align: center; color: var(--text-muted); justify-content: center; padding: 1.5rem;">
                                    No new notifications.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Sign Out Link -->
                    <a href="<?php echo e(route('logout')); ?>" class="btn btn-secondary btn-sm" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #fca5a5;">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </div>
            </header>

            <!-- Main Content Container -->
            <div class="main-container">
                <?php if(session('success')): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <span><?php echo e(session('success')); ?></span>
                    </div>
                <?php endif; ?>

                <?php if(session('warning')): ?>
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span><?php echo e(session('warning')); ?></span>
                    </div>
                <?php endif; ?>

                <?php if(session('danger') || session('error')): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <span><?php echo e(session('danger') ?? session('error')); ?></span>
                    </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
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
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\wamp64\www\College-Music\resources\views/layouts/app.blade.php ENDPATH**/ ?>