<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Campaign Stack') }}</title>

        <link rel="icon" type="image/x-icon" href="/i/campaign-stack-100.png">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Instant Flash-Free Theme Initializer -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <style>
            /* Smooth Top-Right View Transition */
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
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('js')
    </head>
    <body class="font-sans antialiased text-zinc-900 dark:text-zinc-100 bg-slate-50 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(254,243,199,0.35),rgba(255,255,255,0))] dark:bg-[#09090B]">
        <!-- Realistic Ambient Light Wave Element -->
        <div id="themeLightWave"></div>

        <div class="min-h-screen bg-slate-50 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(254,243,199,0.35),rgba(255,255,255,0))] dark:bg-[#09090B]">
            @include('layouts.navigation')

            <!-- Page Heading (Fluid Integrated Header) -->
            @isset($header)
                <div class="max-w-7xl mx-auto pt-6 pb-2 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
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
