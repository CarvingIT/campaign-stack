<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Campaign Stack') }}</title>

        <link rel="icon" type="image/png" href="/i/campaignstack-icon.png">
        <link rel="apple-touch-icon" href="/i/campaignstack-icon.png">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800|plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Instant Flash-Free Theme Initializer (Defaults to System Dark Mode if enabled) -->
        <script>
            (function() {
                const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const storedTheme = localStorage.getItem('theme');
                const isExplicit = localStorage.getItem('theme_explicit') === 'true';

                let useDark = false;
                if (isExplicit) {
                    useDark = storedTheme === 'dark';
                } else {
                    useDark = systemPrefersDark;
                }

                if (useDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                // Instant Flash-Free Navigation Layout Initializer
                const storedLayout = localStorage.getItem('nav_layout') || 'horizontal';
                if (storedLayout === 'vertical') {
                    document.documentElement.classList.add('layout-vertical');
                } else {
                    document.documentElement.classList.remove('layout-vertical');
                }

                // Instant Flash-Free Sidebar Minimized Initializer
                if (localStorage.getItem('sidebar_minimized') === 'true') {
                    document.documentElement.classList.add('sidebar-minimized');
                } else {
                    document.documentElement.classList.remove('sidebar-minimized');
                }

                if (window.matchMedia) {
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                        if (localStorage.getItem('theme_explicit') !== 'true') {
                            if (e.matches) {
                                document.documentElement.classList.add('dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                            }
                            window.dispatchEvent(new Event('theme-changed'));
                        }
                    });
                }
            })();
        </script>

        <style>
            /* Smooth Top-Right View Transition for Theme Switcher */
            ::view-transition-old(root),
            ::view-transition-new(root) {
                animation: none;
                mix-blend-mode: normal;
            }
            ::view-transition-old(root) {
                z-index: 1;
            }
            ::view-transition-new(root) {
                z-index: 999999;
            }

            /* Realistic Soft Light Ray Wave */
            #themeLightWave {
                position: fixed;
                pointer-events: none;
                z-index: 999998;
                opacity: 0;
                transform: scale(0);
                border-radius: 50%;
                filter: blur(40px);
                transition: transform 0.65s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.5s ease-out;
            }

            /* Luxurious, Appreciable Apple Page Transition */
            @keyframes appleHeaderMount {
                0% {
                    opacity: 0;
                    transform: translate3d(0, 10px, 0) scale3d(0.992, 0.992, 1);
                }
                100% {
                    opacity: 1;
                    transform: translate3d(0, 0, 0) scale3d(1, 1, 1);
                }
            }

            @keyframes appleContentMount {
                0% {
                    opacity: 0;
                    transform: translate3d(0, 16px, 0) scale3d(0.988, 0.988, 1);
                }
                100% {
                    opacity: 1;
                    transform: translate3d(0, 0, 0) scale3d(1, 1, 1);
                }
            }

            .page-header-mount {
                animation: appleHeaderMount 0.46s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                will-change: opacity, transform;
                transform-origin: 50% 0%;
                backface-visibility: hidden;
            }

            .page-content-mount {
                animation: appleContentMount 0.54s cubic-bezier(0.16, 1, 0.3, 1) 0.06s backwards;
                will-change: opacity, transform;
                transform-origin: 50% 0%;
                backface-visibility: hidden;
            }

            /* Sleek Minimal Global Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            ::-webkit-scrollbar-track {
                background: transparent;
            }

            ::-webkit-scrollbar-thumb {
                background: rgba(148, 163, 184, 0.4);
                border-radius: 9999px;
                transition: background 0.2s ease;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: rgba(148, 163, 184, 0.7);
            }

            .dark ::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.18);
            }

            .dark ::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.35);
            }

            /* Firefox Support */
            * {
                scrollbar-width: thin;
                scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
            }

            .dark * {
                scrollbar-color: rgba(255, 255, 255, 0.18) transparent;
            }

            /* Luxurious Icon Reveal Animation */
            @keyframes iconReveal {
                0% {
                    opacity: 0;
                    transform: scale(0.82);
                }
                100% {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            .fa, .fas, .far, .fab {
                animation: iconReveal 0.42s cubic-bezier(0.16, 1, 0.3, 1) both;
                will-change: transform, opacity;
                backface-visibility: hidden;
            }

            @media (prefers-reduced-motion: reduce) {
                .page-header-mount,
                .page-content-mount,
                .fa, .fas, .far, .fab {
                    animation: none !important;
                    transform: none !important;
                }
            }

            /* Navigation Layout Modes: Horizontal (Top Nav) vs Vertical (SaaS Sidebar) */
            .layout-vertical #horizontalNav {
                display: none !important;
            }
            .layout-vertical #verticalNavContainer {
                display: block !important;
            }
            .layout-vertical #pageTransitionContainer {
                padding-top: 3.5rem;
                transition: padding-left 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            }
            @media (min-width: 1024px) {
                .layout-vertical #pageTransitionContainer {
                    padding-left: 16rem;
                }
                .layout-vertical.sidebar-minimized #pageTransitionContainer {
                    padding-left: 4.5rem;
                }
                .layout-vertical #verticalSidebar {
                    width: 16rem;
                    transition: width 0.22s cubic-bezier(0.16, 1, 0.3, 1);
                }
                .layout-vertical.sidebar-minimized #verticalSidebar {
                    width: 4.5rem;
                }
                .layout-vertical #verticalNavContainer header {
                    left: 16rem;
                    transition: left 0.22s cubic-bezier(0.16, 1, 0.3, 1);
                }
                .layout-vertical.sidebar-minimized #verticalNavContainer header {
                    left: 4.5rem;
                }
            }

            /* Minimized (Icon-Only) Sidebar Elements */
            .layout-vertical.sidebar-minimized .sidebar-label,
            .layout-vertical.sidebar-minimized .sidebar-category-title,
            .layout-vertical.sidebar-minimized .sidebar-badge,
            .layout-vertical.sidebar-minimized .sidebar-full-logo,
            .layout-vertical.sidebar-minimized .sidebar-user-details,
            .layout-vertical.sidebar-minimized .sidebar-broadcast-text,
            .layout-vertical.sidebar-minimized .sidebar-prefs-title,
            .layout-vertical.sidebar-minimized .sidebar-status-card {
                display: none !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-min-logo,
            .layout-vertical.sidebar-minimized .sidebar-min-status {
                display: block !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-link {
                justify-content: center !important;
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-broadcast-btn {
                width: 2.5rem !important;
                height: 2.5rem !important;
                padding: 0 !important;
                margin-left: auto !important;
                margin-right: auto !important;
                border-radius: 0.75rem !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-user-dock {
                justify-content: center !important;
                padding: 0.35rem !important;
                width: 2.5rem !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-prefs-bar {
                flex-direction: column !important;
                align-items: center !important;
                gap: 0.4rem !important;
                padding: 0 !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-prefs-buttons {
                flex-direction: column !important;
                gap: 0.4rem !important;
                margin: 0 auto !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-prefs-buttons button {
                width: 2.25rem !important;
                height: 2.25rem !important;
                margin: 0 auto !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-header {
                justify-content: center !important;
                padding-left: 0.25rem !important;
                padding-right: 0.25rem !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-toggle-btn {
                position: absolute !important;
                right: -0.875rem !important;
                top: 0.875rem !important;
                z-index: 60 !important;
                background-color: #ffffff !important;
                border: 1px solid rgba(228, 228, 231, 0.9) !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
            }
            .dark .layout-vertical.sidebar-minimized .sidebar-toggle-btn {
                background-color: #18181b !important;
                border: 1px solid rgba(63, 63, 70, 0.8) !important;
            }

            /* Directional Arrow Logic as per UI/UX Standards */
            .sidebar-collapse-icon {
                display: inline-block !important;
            }
            .sidebar-expand-icon {
                display: none !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-collapse-icon {
                display: none !important;
            }
            .layout-vertical.sidebar-minimized .sidebar-expand-icon {
                display: inline-block !important;
            }
        </style>

        <!-- Instant Icon Font Preload & Global Stylesheet -->
        <link rel="preload" href="/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('js')
    </head>
    <body class="font-sans antialiased text-zinc-900 dark:text-zinc-100 bg-slate-50 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(254,243,199,0.35),rgba(255,255,255,0))] dark:bg-[#09090B] dark:bg-none">
        <!-- Realistic Ambient Light Wave Element -->
        <div id="themeLightWave"></div>

        <div class="min-h-screen bg-slate-50 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(254,243,199,0.35),rgba(255,255,255,0))] dark:bg-[#09090B] dark:bg-none">
            @include('layouts.navigation')
            @include('layouts.sidebar')

            <!-- High-End Staged Route Content Container -->
            <div id="pageTransitionContainer">
                <!-- Page Heading (Fluid Integrated Header) -->
                @isset($header)
                    <div class="max-w-7xl mx-auto pt-6 pb-2 px-4 sm:px-6 lg:px-8 page-header-mount">
                        {{ $header }}
                    </div>
                @endisset

                <!-- Page Content -->
                <main class="page-content-mount">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Top-Right Corner Sunrise / Twilight Ray Transition Handler -->
        <script>
            window.toggleTheme = function () {
                const isDark = document.documentElement.classList.contains('dark');
                const nextTheme = isDark ? 'light' : 'dark';

                // Top-right corner coordinates
                const x = window.innerWidth;
                const y = 0;

                // Generous overshoot radius to ensure complete coverage across all screen sizes
                const endRadius = Math.ceil(Math.hypot(window.innerWidth, window.innerHeight) * 1.35);

                const performToggle = () => {
                    if (nextTheme === 'dark') {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    }
                    localStorage.setItem('theme_explicit', 'true');
                    window.dispatchEvent(new Event('theme-changed'));
                };

                // Luminous Ambient Light Glow Wave from Top-Right Corner
                const wave = document.getElementById('themeLightWave');
                if (wave) {
                    const diameter = endRadius * 2.2;
                    wave.style.width = diameter + 'px';
                    wave.style.height = diameter + 'px';
                    wave.style.left = (x - diameter / 2) + 'px';
                    wave.style.top = (y - diameter / 2) + 'px';

                    if (nextTheme === 'light') {
                        wave.style.background = 'radial-gradient(circle at center, rgba(251, 191, 36, 0.45) 0%, rgba(254, 243, 199, 0.3) 40%, rgba(255, 255, 255, 0) 70%)';
                    } else {
                        wave.style.background = 'radial-gradient(circle at center, rgba(147, 197, 253, 0.35) 0%, rgba(59, 130, 246, 0.15) 40%, rgba(9, 9, 11, 0) 70%)';
                    }

                    wave.style.opacity = '1';
                    wave.style.transform = 'scale(0.1)';

                    requestAnimationFrame(() => {
                        wave.style.transform = 'scale(1)';
                        setTimeout(() => {
                            wave.style.opacity = '0';
                            setTimeout(() => {
                                wave.style.transform = 'scale(0)';
                            }, 300);
                        }, 400);
                    });
                }

                // Native View Transition with Smooth Top-Right Sweep
                if (document.startViewTransition && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    const transition = document.startViewTransition(() => {
                        performToggle();
                    });

                    transition.ready.then(() => {
                        document.documentElement.animate(
                            {
                                clipPath: [
                                    `circle(0px at 100% 0%)`,
                                    `circle(${endRadius}px at 100% 0%)`
                                ]
                            },
                            {
                                duration: 600,
                                easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
                                pseudoElement: '::view-transition-new(root)'
                            }
                        );
                    });
                } else {
                    setTimeout(() => {
                        performToggle();
                    }, 200);
                }
            };

            // Global Navigation Layout Switcher Handler
            window.toggleNavLayout = function () {
                const isVertical = document.documentElement.classList.contains('layout-vertical');
                const nextLayout = isVertical ? 'horizontal' : 'vertical';

                if (nextLayout === 'vertical') {
                    document.documentElement.classList.add('layout-vertical');
                    localStorage.setItem('nav_layout', 'vertical');
                } else {
                    document.documentElement.classList.remove('layout-vertical');
                    localStorage.setItem('nav_layout', 'horizontal');
                }

                window.dispatchEvent(new Event('layout-changed'));
            };

            // Global Sidebar Minimize Toggle Handler
            window.toggleSidebarMinimize = function () {
                const isMinimized = document.documentElement.classList.contains('sidebar-minimized');
                if (isMinimized) {
                    document.documentElement.classList.remove('sidebar-minimized');
                    localStorage.setItem('sidebar_minimized', 'false');
                } else {
                    document.documentElement.classList.add('sidebar-minimized');
                    localStorage.setItem('sidebar_minimized', 'true');
                }
                window.dispatchEvent(new Event('sidebar-toggled'));
            };
        </script>
    </body>
</html>
