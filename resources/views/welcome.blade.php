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
    <title>CollegeMusic | Global Music Distribution & Royalty Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            font-family: var(--font-main);
            line-height: 1.6;
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* Utility Components */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.8rem 1.75rem;
            border-radius: var(--radius-full);
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 0.95rem;
            transition: var(--transition);
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #4f46e5);
            color: #fff;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background-color: rgba(255, 255, 255, 0.03);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.07);
            transform: translateY(-2px);
        }

        .gradient-text {
            background: linear-gradient(135deg, #a5b4fc, #818cf8, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
            color: #fff;
        }

        .logo i {
            color: var(--primary);
            animation: float 3s ease-in-out infinite;
        }

        .nav-links {
            display: flex;
            gap: 2.25rem;
            list-style: none;
        }

        .nav-link {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .nav-link:hover, .nav-link.active {
            color: var(--text-primary);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Hero Section */
        .hero {
            padding-top: 180px;
            padding-bottom: 100px;
            position: relative;
            background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.15), transparent 60%);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-title {
            font-family: var(--font-heading);
            font-size: 3.5rem;
            line-height: 1.15;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            max-width: 520px;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 3.5rem;
        }

        /* Dashboard Preview Mockup */
        .mockup-wrapper {
            position: relative;
        }

        .mockup-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .mockup-card::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -150px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.12), transparent 70%);
            pointer-events: none;
        }

        .mockup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .mockup-dots {
            display: flex;
            gap: 0.35rem;
        }

        .mockup-dot {
            width: 8px;
            height: 8px;
            border-radius: var(--radius-full);
            background-color: var(--text-muted);
        }

        .mockup-dot:nth-child(1) { background-color: #ef4444; }
        .mockup-dot:nth-child(2) { background-color: #f59e0b; }
        .mockup-dot:nth-child(3) { background-color: #10b981; }

        .mockup-body {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .mockup-row {
            display: flex;
            gap: 1rem;
        }

        .mockup-box {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1rem;
            flex: 1;
        }

        /* Stats Bar */
        .stats-bar {
            background-color: rgba(17, 24, 39, 0.6);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin-top: 3rem;
            backdrop-filter: blur(10px);
        }

        .stat-item-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .stat-item-val {
            font-family: var(--font-heading);
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
        }

        /* Platforms Showcase */
        .platforms-section {
            padding: 80px 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            background-color: rgba(17, 24, 39, 0.2);
            text-align: center;
        }

        .platforms-title {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
        }

        .platforms-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 3.5rem;
            align-items: center;
        }

        .platform-icon {
            font-size: 2.2rem;
            color: var(--text-secondary);
            opacity: 0.6;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .platform-icon:hover {
            opacity: 1;
            color: #fff;
            transform: scale(1.1);
        }

        .platform-icon span {
            font-size: 0.75rem;
            font-family: var(--font-main);
            font-weight: 500;
        }

        /* Features Section */
        .section-header {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 5rem;
        }

        .section-tag {
            font-family: var(--font-heading);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 0.75rem;
            display: block;
        }

        .section-heading {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .section-desc {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .features {
            padding: 120px 0;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .feature-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 2.25rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 10px 30px -10px rgba(99, 102, 241, 0.15);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background-color: rgba(99, 102, 241, 0.08);
            color: var(--primary);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon {
            background-color: var(--primary);
            color: #fff;
            transform: rotate(5deg);
        }

        .feature-title {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .feature-desc {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* How it works */
        .steps-section {
            padding: 120px 0;
            background: radial-gradient(circle at 0% 100%, rgba(99, 102, 241, 0.05), transparent 50%);
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3rem;
            position: relative;
        }

        .step-item {
            text-align: center;
            position: relative;
        }

        .step-num {
            font-family: var(--font-heading);
            font-size: 4rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.02);
            position: absolute;
            top: -2.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
            line-height: 1;
        }

        .step-icon {
            width: 70px;
            height: 70px;
            border-radius: var(--radius-full);
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--text-primary);
            margin: 0 auto 1.5rem;
            position: relative;
            z-index: 2;
            transition: var(--transition);
        }

        .step-item:hover .step-icon {
            background-color: var(--primary);
            color: #fff;
            border-color: var(--primary);
            transform: scale(1.1);
        }

        .step-title {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .step-desc {
            color: var(--text-secondary);
            font-size: 0.9rem;
            max-width: 280px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        /* Pricing Section */
        .pricing {
            padding: 120px 0;
            background-color: rgba(17, 24, 39, 0.2);
            border-top: 1px solid var(--border-color);
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            align-items: stretch;
        }

        .price-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 3rem 2.25rem;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            position: relative;
        }

        .price-card.featured {
            border-color: var(--primary);
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.05), transparent);
            box-shadow: 0 20px 40px -10px rgba(99, 102, 241, 0.15);
        }

        .price-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background-color: var(--primary);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            letter-spacing: 0.05em;
        }

        .price-header {
            margin-bottom: 2rem;
        }

        .price-name {
            font-family: var(--font-heading);
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .price-cost {
            font-family: var(--font-heading);
            font-size: 3rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .price-cost span {
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .price-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 3rem;
            margin-top: 1rem;
        }

        .price-feature-item {
            font-size: 0.9rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .price-feature-item i {
            color: var(--accent);
            font-size: 1rem;
        }

        .price-btn {
            margin-top: auto;
        }

        /* Testimonials Section */
        .testimonials {
            padding: 120px 0;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .test-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 2.25rem;
            display: flex;
            flex-direction: column;
        }

        .test-quote {
            font-size: 0.95rem;
            color: var(--text-secondary);
            font-style: italic;
            margin-bottom: 2rem;
            flex: 1;
            position: relative;
        }

        .test-quote::before {
            content: '"';
            font-size: 4rem;
            color: rgba(99, 102, 241, 0.08);
            position: absolute;
            top: -2rem;
            left: -1rem;
            line-height: 1;
        }

        .test-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .test-avatar {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-full);
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            border: 2px solid var(--border-color);
        }

        .test-name {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 0.95rem;
            color: #fff;
        }

        .test-role {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: radial-gradient(circle at 100% 100%, rgba(99, 102, 241, 0.08), transparent 60%);
        }

        .cta-box {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(17, 24, 39, 0.8));
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: var(--radius-lg);
            padding: 5rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-box::after {
            content: '';
            position: absolute;
            top: -100px;
            left: -100px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.08), transparent 70%);
        }

        .cta-title {
            font-family: var(--font-heading);
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .cta-desc {
            color: var(--text-secondary);
            max-width: 580px;
            margin: 0 auto 2.5rem;
            font-size: 1.05rem;
        }

        /* Footer */
        .footer {
            background-color: #060911;
            border-top: 1px solid var(--border-color);
            padding: 80px 0 40px;
            color: var(--text-secondary);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr repeat(3, 1fr);
            gap: 4rem;
            margin-bottom: 4rem;
        }

        .footer-logo {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .footer-desc {
            font-size: 0.9rem;
            max-width: 320px;
            margin-bottom: 1.5rem;
        }

        .footer-socials {
            display: flex;
            gap: 1rem;
        }

        .social-link {
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .social-link:hover {
            background-color: var(--primary);
            color: #fff;
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .footer-col-title {
            font-family: var(--font-heading);
            font-size: 0.95rem;
            color: #fff;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .footer-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .footer-menu-link {
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .footer-menu-link:hover {
            color: #fff;
            padding-left: 2px;
        }

        .footer-bottom {
            border-top: 1px solid var(--border-color);
            padding-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }

        /* Responsive Breakpoints */
        @media (max-width: 968px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 3rem;
                text-align: center;
            }
            .hero-subtitle {
                margin-left: auto;
                margin-right: auto;
            }
            .hero-actions {
                justify-content: center;
            }
            .features-grid, .pricing-grid, .testimonials-grid {
                grid-template-columns: 1fr;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }
            .nav-links {
                display: none; /* simple mobile hide */
            }
            .navbar {
                height: 70px;
            }
            .navbar-content {
                padding: 0 1rem;
            }
            .logo {
                font-size: 1.25rem;
            }
            .nav-actions {
                gap: 0.55rem !important;
            }
            .theme-toggle-btn {
                width: 52px;
                height: 30px;
            }
            .theme-toggle-circle {
                width: 24px;
                height: 24px;
            }
            .light-theme .theme-toggle-circle {
                transform: translateX(24px);
            }
            .lang-switch-wrapper {
                width: 74px;
                height: 30px;
            }
            .lang-switch-btn {
                font-size: 0.65rem;
                line-height: 24px;
                height: 24px;
            }
            .lang-switch-slider {
                width: 33px;
                height: 24px;
            }
            html[lang="sw"] .lang-switch-slider {
                transform: translateX(35px);
            }
            .nav-actions .nav-link {
                font-size: 0.85rem;
            }
            .nav-actions .btn {
                padding: 0.45rem 1rem !important;
                font-size: 0.78rem !important;
            }
        }

        @media (max-width: 576px) {
            .logo-text {
                display: none; /* hide brand text on tiny screens to avoid overflow */
            }
            .logo {
                font-size: 1.4rem;
            }
            .nav-actions {
                gap: 0.4rem !important;
            }
            .nav-actions .nav-link {
                font-size: 0.8rem;
            }
            .nav-actions .btn {
                padding: 0.4rem 0.8rem !important;
                font-size: 0.75rem !important;
            }
            .stats-bar {
                flex-direction: column;
                gap: 1.5rem;
                padding: 1.5rem;
            }
            .stats-bar > div[style*="border-left"] {
                display: none !important;
            }
            .stats-bar > div:not(:last-child):not([style*="border-left"]) {
                border-bottom: 1px solid var(--border-color);
                padding-bottom: 1.25rem;
            }
        }


        /* Animated Floating Headphones Background */
        .bg-headphones-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .floating-headphone {
            position: absolute;
            color: rgba(255, 255, 255, 0.03); /* extremely subtle white outline */
            animation: float-headphone-around infinite ease-in-out;
            user-select: none;
        }

        @keyframes float-headphone-around {
            0% {
                transform: translateY(0px) rotate(0deg) scale(1);
                opacity: 0.2;
            }
            50% {
                transform: translateY(-35px) rotate(20deg) scale(1.08);
                opacity: 0.55;
                color: rgba(255, 255, 255, 0.08); /* glowing subtle white change */
            }
            100% {
                transform: translateY(0px) rotate(0deg) scale(1);
                opacity: 0.2;
            }
        }

        /* ==========================================
           Light Mode Overrides & Switcher Styles
           ========================================== */
        
        .light-theme {
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --border-color: rgba(15, 23, 42, 0.08);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
        }
        
        .light-theme body {
            background-color: var(--bg-main);
            color: var(--text-primary);
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

        /* Custom View Transition style for theme reveal */
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

        /* Fallback transition styles for browsers without view transitions */
        .theme-in-transition * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease !important;
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
            animation: rotateMusicSign 2s linear infinite;
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
    </style>
</head>
<body id="top">

    <!-- Sticky Navigation -->
    <nav class="navbar">
        <div class="container navbar-content">
            <a href="{{ route('home') }}" onclick="if(window.location.pathname==='/' || window.location.pathname.endsWith('/')){ window.scrollTo({top:0, behavior:'smooth'}); }" class="logo">
                <i class="fa-solid fa-music"></i> <span class="logo-text">CollegeMusic</span>
            </a>
            <ul class="nav-links">
                <li><a href="#features" class="nav-link">{{ __('messages.features') }}</a></li>
                <li><a href="#platforms" class="nav-link">{{ __('messages.platforms') }}</a></li>
                <li><a href="#pricing" class="nav-link">{{ __('messages.pricing') }}</a></li>
                <li><a href="{{ route('explore') }}" class="nav-link" style="color: var(--primary); font-weight: bold;"><i class="fa-solid fa-compass"></i> {{ __('messages.explore_music') }}</a></li>
            </ul>
            <div class="nav-actions" style="display: flex; align-items: center; gap: 1rem;">
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

                <a href="{{ route('login') }}" class="nav-link" style="margin-right: 0.25rem; font-weight: 600;">{{ __('messages.sign_in') }}</a>
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 0.55rem 1.25rem; font-size: 0.85rem;">{{ __('messages.get_started') }}</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <!-- Floating Headphones Background -->
        <div class="bg-headphones-container">
            <div class="floating-headphone" style="font-size: 8rem; top: 10%; left: 5%; animation-duration: 12s; animation-delay: 0s;">
                <i class="fa-solid fa-headphones"></i>
            </div>
            <div class="floating-headphone" style="font-size: 14rem; top: 25%; right: 5%; animation-duration: 18s; animation-delay: 2s;">
                <i class="fa-solid fa-headphones"></i>
            </div>
            <div class="floating-headphone" style="font-size: 6rem; bottom: 15%; left: 35%; animation-duration: 15s; animation-delay: 1s;">
                <i class="fa-solid fa-headphones"></i>
            </div>
            <div class="floating-headphone" style="font-size: 10rem; top: 45%; left: 48%; animation-duration: 22s; animation-delay: 3s;">
                <i class="fa-solid fa-headphones"></i>
            </div>
            <div class="floating-headphone" style="font-size: 7rem; top: 15%; right: 42%; animation-duration: 14s; animation-delay: 4s;">
                <i class="fa-solid fa-headphones"></i>
            </div>
        </div>

        <div class="container hero-grid" style="position: relative; z-index: 5;">
            <div>
                <h1 class="hero-title">{{ __('messages.distribute_globally_title') }}</h1>
                <p class="hero-subtitle">
                    {{ __('messages.hero_subtitle') }}
                </p>
                <div class="hero-actions">
                    <a href="{{ route('register') }}" class="btn btn-primary"><i class="fa-solid fa-play"></i> {{ __('messages.distribute_now') }}</a>
                    <a href="#features" class="btn btn-secondary">{{ __('messages.learn_more') }}</a>
                </div>
            </div>
            
            <div class="mockup-wrapper">
                <div class="mockup-card">
                    <div class="mockup-header">
                        <div class="mockup-dots">
                            <span class="mockup-dot"></span>
                            <span class="mockup-dot"></span>
                            <span class="mockup-dot"></span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">collegemusic.io/dashboard</div>
                    </div>
                    <div class="mockup-body">
                        <div class="mockup-row">
                            <div class="mockup-box" style="display: flex; flex-direction: column; gap: 0.25rem;">
                                <span style="font-size: 0.65rem; color: var(--text-secondary); text-transform: uppercase;">Total Playbacks</span>
                                <strong style="font-size: 1.4rem; font-family: var(--font-heading);">125,000</strong>
                            </div>
                            <div class="mockup-box" style="display: flex; flex-direction: column; gap: 0.25rem;">
                                <span style="font-size: 0.65rem; color: var(--text-secondary); text-transform: uppercase;">Royalty Balance</span>
                                <strong style="font-size: 1.4rem; font-family: var(--font-heading); color: var(--accent);">$550.00</strong>
                            </div>
                        </div>
                        <div class="mockup-box" style="height: 120px; display: flex; align-items: flex-end; gap: 0.5rem; justify-content: space-between; padding-top: 1.5rem;">
                            <!-- CSS Bar Chart Graph mockup -->
                            <div style="width: 25px; height: 30%; background-color: var(--primary); border-radius: 4px;"></div>
                            <div style="width: 25px; height: 50%; background-color: var(--primary); border-radius: 4px;"></div>
                            <div style="width: 25px; height: 40%; background-color: var(--primary); border-radius: 4px;"></div>
                            <div style="width: 25px; height: 75%; background-color: var(--primary); border-radius: 4px;"></div>
                            <div style="width: 25px; height: 60%; background-color: var(--primary); border-radius: 4px;"></div>
                            <div style="width: 25px; height: 90%; background-color: var(--primary); border-radius: 4px; box-shadow: 0 0 15px var(--primary);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats bar container -->
        <div class="container">
            <div class="stats-bar">
                <div>
                    <div class="stat-item-title">{{ __('messages.stats_creators') }}</div>
                    <div class="stat-item-val">12K+</div>
                </div>
                <div style="border-left: 1px solid var(--border-color);"></div>
                <div>
                    <div class="stat-item-title">{{ __('messages.stats_platforms') }}</div>
                    <div class="stat-item-val">150+</div>
                </div>
                <div style="border-left: 1px solid var(--border-color);"></div>
                <div>
                    <div class="stat-item-title">{{ __('messages.stats_royalties') }}</div>
                    <div class="stat-item-val">$3.2M+</div>
                </div>
            </div>
        </div>
    </header>

    <!-- Ingestion Partners Showcase -->
    <section class="platforms-section" id="platforms">
        <div class="container">
            <h3 class="platforms-title">{{ __('messages.partnership_title') }}</h3>
            <div class="platforms-grid">
                <div class="platform-icon"><i class="fa-brands fa-spotify"></i><span>Spotify</span></div>
                <div class="platform-icon"><i class="fa-brands fa-apple"></i><span>Apple Music</span></div>
                <div class="platform-icon"><i class="fa-brands fa-youtube"></i><span>YouTube Music</span></div>
                <div class="platform-icon"><i class="fa-brands fa-amazon"></i><span>Amazon Music</span></div>
                <div class="platform-icon"><i class="fa-brands fa-deezer"></i><span>Deezer</span></div>
                <div class="platform-icon"><i class="fa-brands fa-tiktok"></i><span>TikTok</span></div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">{{ __('messages.features_tag') }}</span>
                <h2 class="section-heading">{{ __('messages.features_heading') }}</h2>
                <p class="section-desc">{{ __('messages.features_desc') }}</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-earth-americas"></i></div>
                    <h3 class="feature-title">{{ __('messages.feat_global_title') }}</h3>
                    <p class="feature-desc">{{ __('messages.feat_global_desc') }}</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-chart-simple"></i></div>
                    <h3 class="feature-title">{{ __('messages.feat_analytics_title') }}</h3>
                    <p class="feature-desc">{{ __('messages.feat_analytics_desc') }}</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-wallet"></i></div>
                    <h3 class="feature-title">{{ __('messages.feat_royalties_title') }}</h3>
                    <p class="feature-desc">{{ __('messages.feat_royalties_desc') }}</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                    <h3 class="feature-title">{{ __('messages.feat_wizard_title') }}</h3>
                    <p class="feature-desc">{{ __('messages.feat_wizard_desc') }}</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-users-gear"></i></div>
                    <h3 class="feature-title">{{ __('messages.feat_label_title') }}</h3>
                    <p class="feature-desc">{{ __('messages.feat_label_desc') }}</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-file-shield"></i></div>
                    <h3 class="feature-title">{{ __('messages.feat_audit_title') }}</h3>
                    <p class="feature-desc">{{ __('messages.feat_audit_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="steps-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">{{ __('messages.how_tag') }}</span>
                <h2 class="section-heading">{{ __('messages.how_heading') }}</h2>
                <p class="section-desc">{{ __('messages.how_desc') }}</p>
            </div>
            
            <div class="steps-grid">
                <div class="step-item">
                    <div class="step-num">01</div>
                    <div class="step-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <h3 class="step-title">{{ __('messages.how_step1_title') }}</h3>
                    <p class="step-desc">{{ __('messages.how_step1_desc') }}</p>
                </div>
                
                <div class="step-item">
                    <div class="step-num">02</div>
                    <div class="step-icon"><i class="fa-solid fa-tower-broadcast"></i></div>
                    <h3 class="step-title">{{ __('messages.how_step2_title') }}</h3>
                    <p class="step-desc">{{ __('messages.how_step2_desc') }}</p>
                </div>
                
                <div class="step-item">
                    <div class="step-num">03</div>
                    <div class="step-icon"><i class="fa-solid fa-dollar-sign"></i></div>
                    <h3 class="step-title">{{ __('messages.how_step3_title') }}</h3>
                    <p class="step-desc">{{ __('messages.how_step3_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">{{ __('messages.pricing_tag') }}</span>
                <h2 class="section-heading">{{ __('messages.pricing_heading') }}</h2>
                <p class="section-desc">{{ __('messages.pricing_desc') }}</p>
            </div>
            
            <div class="pricing-grid">
                <!-- Plan 1 -->
                <div class="price-card">
                    <div class="price-header">
                        <div class="price-name">{{ __('messages.pricing_free_title') }}</div>
                        <div class="price-cost">$0<span>/year</span></div>
                        <p style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.5rem;">Pay-per-release distribution</p>
                    </div>
                    <ul class="price-features">
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_free_feat1') }}</li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_free_feat2') }}</li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_free_feat3') }}</li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_free_feat4') }}</li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_free_feat5') }}</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-secondary price-btn">{{ __('messages.get_started') }}</a>
                </div>
                
                <!-- Plan 2 -->
                <div class="price-card featured">
                    <span class="price-badge">{{ __('messages.pricing_premium_badge') }}</span>
                    <div class="price-header">
                        <div class="price-name">{{ __('messages.pricing_premium_title') }}</div>
                        <div class="price-cost">{{ __('messages.pricing_premium_cost') }}<span>/year</span></div>
                        <p style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.5rem;">Unlimited free distributions</p>
                    </div>
                    <ul class="price-features">
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> <strong>{{ __('messages.pricing_premium_feat1') }}</strong></li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> <strong>{{ __('messages.pricing_premium_feat2') }}</strong></li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_premium_feat3') }}</li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_premium_feat4') }}</li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_premium_feat5') }}</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-primary price-btn">{{ __('messages.upgrade_account') }}</a>
                </div>
                
                <!-- Plan 3 -->
                <div class="price-card">
                    <div class="price-header">
                        <div class="price-name">{{ __('messages.pricing_label_title') }}</div>
                        <div class="price-cost">{{ __('messages.pricing_label_cost') }}<span>/year</span></div>
                        <p style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.5rem;">Multi-artist management catalog</p>
                    </div>
                    <ul class="price-features">
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_label_feat1') }}</li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_label_feat2') }}</li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_label_feat3') }}</li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_label_feat4') }}</li>
                        <li class="price-feature-item"><i class="fa-solid fa-check"></i> {{ __('messages.pricing_label_feat5') }}</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-secondary price-btn">{{ __('messages.register_label') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">{{ __('messages.success_stories') }}</span>
                <h2 class="section-heading">{{ __('messages.testimonials') }}</h2>
                <p class="section-desc">{{ __('messages.success_stories') }}</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="test-card">
                    <p class="test-quote">
                        CollegeMusic transformed the way I handle my releases. The upload wizard checked my cover dimensions and ISRC automatically, and I received approval in 24 hours. The available balance updates are so transparent.
                    </p>
                    <div class="test-user">
                        <div class="test-avatar">BA</div>
                        <div>
                            <div class="test-name">Burna Boy</div>
                            <div class="test-role">Afrobeats Musician</div>
                        </div>
                    </div>
                </div>

                <div class="test-card">
                    <p class="test-quote">
                        Running a record label with 15 sub-artists used to require complex sheets. CollegeMusic allows me to upload under any artist, track stream geos, and handle withdrawals direct to mobile money accounts easily.
                    </p>
                    <div class="test-user">
                        <div class="test-avatar">DM</div>
                        <div>
                            <div class="test-name">Don Jazzy</div>
                            <div class="test-role">Label Executive (Mavin)</div>
                        </div>
                    </div>
                </div>

                <div class="test-card">
                    <p class="test-quote">
                        The Premium Plan is a no-brainer. For $49.99 a year, I distribute unlimited tracks to Spotify and Apple. I keep 100% of what my streams make. It is the best deal on the internet.
                    </p>
                    <div class="test-user">
                        <div class="test-avatar">TK</div>
                        <div>
                            <div class="test-name">Tems Baby</div>
                            <div class="test-role">Grammy-Winning Singer</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <h2 class="cta-title">{{ __('messages.ready_share') }}</h2>
                <p class="cta-desc">Join thousands of artists and record labels who trust CollegeMusic to grow their music careers.</p>
                <a href="{{ route('register') }}" class="btn btn-primary"><i class="fa-solid fa-rocket"></i> {{ __('messages.create_account') }}</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a href="{{ route('home') }}" onclick="window.scrollTo({top: 0, behavior: 'smooth'});" class="footer-logo" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 0.5rem;" title="Go to Top / Home Page">
                        <i class="fa-solid fa-music" style="color: var(--primary);"></i> CollegeMusic
                    </a>
                    <p class="footer-desc">{{ __('messages.footer_tagline') }}</p>
                    <div class="footer-socials">
                        <a href="#" class="social-link"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fa-brands fa-spotify"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="footer-col-title">Navigation & Shortcuts</h4>
                    <ul class="footer-menu">
                        <li><a href="{{ route('home') }}" onclick="window.scrollTo({top: 0, behavior: 'smooth'});" class="footer-menu-link" style="color: var(--primary); font-weight: 600;"><i class="fa-solid fa-house" style="margin-right: 0.35rem;"></i> Home Page</a></li>
                        <li><a href="{{ route('dashboard') }}" class="footer-menu-link"><i class="fa-solid fa-gauge-high" style="margin-right: 0.35rem;"></i> App Dashboard</a></li>
                        <li><a href="#features" class="footer-menu-link"><i class="fa-solid fa-layer-group" style="margin-right: 0.35rem;"></i> Platform Features</a></li>
                        <li><a href="#pricing" class="footer-menu-link"><i class="fa-solid fa-tags" style="margin-right: 0.35rem;"></i> Distribution Pricing</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="footer-col-title">{{ __('messages.footer_col_distribute') }}</h4>
                    <ul class="footer-menu">
                        <li><a href="{{ route('releases.create') }}" class="footer-menu-link">Singles Upload</a></li>
                        <li><a href="{{ route('releases.create') }}" class="footer-menu-link">Albums / EPs</a></li>
                        <li><a href="{{ route('catalogue') }}" class="footer-menu-link">Catalogue Overview</a></li>
                        <li><a href="{{ route('finance.index') }}" class="footer-menu-link">Royalties & Earnings</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-col-title">{{ __('messages.footer_col_legal') }}</h4>
                    <ul class="footer-menu">
                        <li><a href="#" class="footer-menu-link">Terms of Service</a></li>
                        <li><a href="#" class="footer-menu-link">Privacy Policy</a></li>
                        <li><a href="#" class="footer-menu-link">Copyright Guidelines</a></li>
                        <li><a href="#" class="footer-menu-link">GDPR Compliance</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <span>&copy; {{ date('Y') }} CollegeMusic Inc. {{ __('messages.all_rights_reserved') }} Made for independent music artists & record labels.</span>
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <a href="{{ route('home') }}" onclick="window.scrollTo({top: 0, behavior: 'smooth'});" style="color: var(--primary); text-decoration: none; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem;" title="Go to Home Page">
                        <i class="fa-solid fa-house"></i> Home
                    </a>
                    <a href="javascript:void(0)" onclick="window.scrollTo({top:0, behavior:'smooth'})" style="color: var(--text-secondary); text-decoration: none; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i class="fa-solid fa-arrow-up"></i> Back to Top
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
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
                        
                        const mainContainer = document.querySelector('.container') || document.body;
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
</body>
</html>
