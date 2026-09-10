@props(['wide' => false])
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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

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
            * {
                scrollbar-width: thin;
                scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
            }
            .dark * {
                scrollbar-color: rgba(255, 255, 255, 0.18) transparent;
            }

            @keyframes fadeInScale {
                0% {
                    opacity: 0;
                    transform: scale(0.98) translateY(10px);
                }
                100% {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }

            .auth-card-mount {
                animation: fadeInScale 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
        </style>

        <!-- Instant Icon Font Preload & Global Stylesheet -->
        <link rel="preload" href="/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-zinc-900 dark:text-zinc-100 bg-slate-50 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(254,243,199,0.35),rgba(255,255,255,0))] dark:bg-[#09090B] dark:bg-none min-h-screen flex flex-col justify-between selection:bg-amber-400 selection:text-zinc-950 transition-colors duration-300">
        <!-- Realistic Ambient Light Wave Element -->
        <div id="themeLightWave"></div>

        <!-- Ambient Glow Backdrops -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-amber-400/10 dark:bg-amber-500/5 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -right-32 w-96 h-96 bg-orange-400/10 dark:bg-amber-500/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Header Bar -->
        <header class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5 group focus:outline-none">
                <x-application-logo class="h-8 sm:h-9 w-auto transition-transform group-hover:scale-105" />
            </a>

            <div class="flex items-center gap-3">
                @if (Route::has('login') && !request()->routeIs('login'))
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800/60 transition-colors">
                        {{ __('Sign In') }}
                    </a>
                @endif

                @if (Route::has('register') && !request()->routeIs('register'))
                    <a href="{{ route('register') }}" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800/60 transition-colors">
                        {{ __('Register') }}
                    </a>
                @endif

                <!-- Minimal Theme Toggle Button -->
                <button type="button" id="guest-theme-toggle" onclick="window.toggleTheme(event)" title="Toggle Dark / Light Mode" aria-label="Toggle Dark / Light Mode"
                        class="theme-toggle-btn inline-flex items-center justify-center w-8 h-8 rounded-full border border-zinc-200/80 dark:border-zinc-800 bg-white/90 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all shadow-2xs focus:outline-none">
                    <!-- Light Mode (Moon Icon) -->
                    <span class="dark:hidden inline-flex items-center justify-center">
                        <svg class="w-4 h-4 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </span>
                    <!-- Dark Mode (Sun Icon) -->
                    <span class="hidden dark:inline-flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                </button>
            </div>
        </header>

        <!-- Main Content Slot -->
        <main class="relative z-10 flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
            @if ($wide)
                <div class="w-full max-w-6xl mx-auto auth-card-mount">
                    {{ $slot }}
                </div>
            @else
                <div class="w-full sm:max-w-md mx-auto auth-card-mount">
                    <div class="bg-white/90 dark:bg-[#141417]/90 backdrop-blur-xl border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_8px_30px_rgb(0,0,0,0.06)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.35)] rounded-2xl p-6 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            @endif
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-zinc-500 dark:text-zinc-500 flex flex-col sm:flex-row items-center justify-between gap-2 border-t border-zinc-200/60 dark:border-zinc-800/60">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>CampaignStack Outbound Dispatch Platform</span>
            </div>
            <div>
                &copy; {{ date('Y') }} CampaignStack. All rights reserved.
            </div>
        </footer>

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
        </script>
    </body>
</html>
