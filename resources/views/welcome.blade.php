<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CampaignStack') }} | High-Volume Multi-Provider Email Infrastructure</title>
        <meta name="description" content="Orchestrate customer broadcasts across multiple email gateways with intelligent failover, algorithmic dispatch pacing, and guaranteed inbox delivery.">

        <link rel="icon" type="image/x-icon" href="/i/campaign-stack-100.png">

        <!-- Fonts: Plus Jakarta Sans & Figtree -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Instant Flash-Free Theme Initializer -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <style>
            /* Systematic Professional Typography Scale */
            body {
                font-family: 'Plus Jakarta Sans', 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                letter-spacing: -0.012em;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            h1, h2, h3, h4 {
                letter-spacing: -0.03em;
                font-feature-settings: "cv02", "cv03", "cv04", "cv11";
            }
            .text-2xs { font-size: 0.6875rem; line-height: 1rem; }
            .text-3xs { font-size: 0.625rem; line-height: 0.875rem; }

            /* View Transitions */
            ::view-transition-old(root),
            ::view-transition-new(root) {
                animation: none;
                mix-blend-mode: normal;
            }
            ::view-transition-old(root) { z-index: 1; }
            ::view-transition-new(root) { z-index: 999999; }

            /* Realistic Ray Wave */
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

            /* Scrollbar */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.4); border-radius: 9999px; }
            .dark ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.18); }

            /* Seamless Moving Infinite Precision Grid */
            @keyframes movingGrid {
                0% { background-position: 0 0; }
                100% { background-position: 32px 32px; }
            }
            .saas-grid-pattern {
                background-image: linear-gradient(to right, rgba(148, 163, 184, 0.09) 1px, transparent 1px),
                                  linear-gradient(to bottom, rgba(148, 163, 184, 0.09) 1px, transparent 1px);
                background-size: 32px 32px;
                animation: movingGrid 7s linear infinite;
                mask-image: radial-gradient(ellipse 80% 60% at 50% 15%, #000 60%, transparent 100%);
                -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 15%, #000 60%, transparent 100%);
                will-change: background-position;
            }
            .dark .saas-grid-pattern {
                background-image: linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                                  linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            }

            /* Shimmer Effect */
            @keyframes shimmerFlow {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
            .shimmer-pill {
                background: linear-gradient(90deg, rgba(251, 191, 36, 0.1) 0%, rgba(251, 191, 36, 0.25) 50%, rgba(251, 191, 36, 0.1) 100%);
                background-size: 200% 100%;
                animation: shimmerFlow 3.5s infinite linear;
            }

            /* Fluid Shimmering Text Gradient (Immersive Animated Headline) */
            @keyframes textGradientFlow {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .shimmering-title-gradient {
                background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 30%, #fef08a 52%, #f59e0b 78%, #d97706 100%);
                background-size: 240% auto;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: textGradientFlow 5.5s ease-in-out infinite;
                display: inline-block;
            }

            /* Cinematic Hero Staged Entrance with Deep 3D Spatial Zoom */
            @keyframes heroEntrance {
                0% {
                    opacity: 0;
                    transform: perspective(1200px) translate3d(0, 36px, -70px) scale(0.91);
                    filter: blur(6px);
                }
                100% {
                    opacity: 1;
                    transform: perspective(1200px) translate3d(0, 0, 0) scale(1);
                    filter: blur(0px);
                }
            }
            .hero-reveal-1 {
                animation: heroEntrance 1.05s cubic-bezier(0.19, 1, 0.22, 1) 0.03s both;
            }
            .hero-reveal-2 {
                animation: heroEntrance 1.15s cubic-bezier(0.19, 1, 0.22, 1) 0.12s both;
            }
            .hero-reveal-3 {
                animation: heroEntrance 1.15s cubic-bezier(0.19, 1, 0.22, 1) 0.22s both;
            }
            .hero-reveal-4 {
                animation: heroEntrance 1.15s cubic-bezier(0.19, 1, 0.22, 1) 0.32s both;
            }
            .hero-reveal-5 {
                animation: heroEntrance 1.15s cubic-bezier(0.19, 1, 0.22, 1) 0.42s both;
            }

            /* Scroll Depth Zoom Container */
            #hero-text-container {
                will-change: transform, opacity;
                transform-origin: center top;
                transform-style: preserve-3d;
            }

            /* Infinite Horizontal Relay Carousel */
            @keyframes infiniteMarquee {
                0% { transform: translate3d(0, 0, 0); }
                100% { transform: translate3d(-50%, 0, 0); }
            }
            .marquee-track {
                display: flex;
                width: max-content;
                animation: infiniteMarquee 42s linear infinite;
                will-change: transform;
            }
            .marquee-track:hover {
                animation-play-state: paused;
            }
            .marquee-mask {
                mask-image: linear-gradient(to right, transparent, black 3%, black 97%, transparent);
                -webkit-mask-image: linear-gradient(to right, transparent, black 3%, black 97%, transparent);
            }

            /* 3D Curved Orbital Horizon Relay Cards */
            .relay-orbital-card {
                transform-style: preserve-3d;
                will-change: transform, opacity;
                backface-visibility: hidden;
            }

            /* Clean FAQ Accordion */
            summary::-webkit-details-marker {
                display: none;
            }
            details[open] summary ~ * {
                animation: faqSweep 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            }
            @keyframes faqSweep {
                0% { opacity: 0; transform: translateY(-6px); }
                100% { opacity: 1; transform: translateY(0); }
            }

            /* Sleek Studio Display Mockup */
            .studio-display-frame {
                transform-style: preserve-3d;
                will-change: transform;
                box-shadow: 0 20px 50px -15px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.06);
            }
            .dark .studio-display-frame {
                box-shadow: 0 30px 80px -20px rgba(0,0,0,0.8), 0 0 0 1px rgba(255,255,255,0.08);
            }

            /* Cursor Spotlight Follower */
            .mouse-spotlight {
                pointer-events: none;
                position: absolute;
                width: 350px;
                height: 350px;
                background: radial-gradient(circle, rgba(251, 191, 36, 0.15) 0%, transparent 70%);
                border-radius: 50%;
                transform: translate(-50%, -50%);
                opacity: 0;
                transition: opacity 0.3s ease;
                z-index: 1;
            }

            /* Card Mouse Tracking Highlight */
            .interactive-card {
                position: relative;
                overflow: hidden;
            }
            .interactive-card::before {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(400px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(251, 191, 36, 0.08), transparent 70%);
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
            }
            .interactive-card:hover::before {
                opacity: 1;
            }

            /* Modern, High-Performance Native CSS Animations (Zero-Lag, 120fps) */
            .reveal-on-scroll {
                opacity: 0;
                transform: translateY(22px);
                transition: opacity 0.45s cubic-bezier(0.16, 1, 0.3, 1), transform 0.45s cubic-bezier(0.16, 1, 0.3, 1);
                will-change: opacity, transform;
            }
            .reveal-on-scroll.is-revealed {
                opacity: 1;
                transform: translateY(0);
            }

            /* Lightweight Ambient Badge Floating (Pure CSS, 0 JS) */
            @keyframes badgeFloatLeft {
                0%, 100% { transform: translateY(0px) rotate(-3deg); }
                50% { transform: translateY(-5px) rotate(-2.5deg); }
            }
            @keyframes badgeFloatRight {
                0%, 100% { transform: translateY(0px) rotate(3deg); }
                50% { transform: translateY(-5px) rotate(3.5deg); }
            }
            .animate-float-badge-left {
                animation: badgeFloatLeft 4.5s ease-in-out infinite;
            }
            .animate-float-badge-right {
                animation: badgeFloatRight 4.5s ease-in-out 1.2s infinite;
            }
        </style>

        <!-- Icon Font -->
        <link rel="preload" href="/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-zinc-900 dark:text-zinc-100 bg-slate-50 dark:bg-[#09090B] min-h-screen selection:bg-amber-400 selection:text-zinc-950 transition-colors duration-300 overflow-x-hidden">
        <!-- Realistic Ambient Light Wave Element -->
        <div id="themeLightWave"></div>

        <!-- Ambient Background Grid & Glows -->
        <div class="fixed inset-0 pointer-events-none z-0">
            <div class="absolute inset-0 saas-grid-pattern"></div>
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[850px] h-[550px] bg-gradient-to-b from-amber-400/18 via-amber-500/5 to-transparent rounded-full blur-3xl dark:from-amber-500/12 pointer-events-none"></div>
            <div class="absolute top-[40%] -left-64 w-[450px] h-[450px] bg-amber-400/8 dark:bg-amber-500/5 rounded-full blur-3xl"></div>
            <div class="absolute top-[65%] -right-64 w-[450px] h-[450px] bg-orange-400/8 dark:bg-amber-500/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Sticky Blended Navbar -->
        <header id="mainNav" class="sticky top-0 z-50 backdrop-blur-xl bg-slate-50/80 dark:bg-[#09090B]/80 border-b border-zinc-200/50 dark:border-zinc-800/40 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-18">
                    <!-- Brand Logo -->
                    <div class="flex items-center gap-8">
                        <a href="/" class="flex items-center gap-3 group focus:outline-none">
                            <x-application-logo class="h-8 sm:h-9 w-auto transition-transform group-hover:scale-105" />
                        </a>

                        <!-- Navigation Links -->
                        <nav class="hidden md:flex items-center gap-1 text-xs font-semibold text-zinc-600 dark:text-zinc-300">
                            <a href="#hero-mockup" class="px-3.5 py-1.5 rounded-lg hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/70 dark:hover:bg-zinc-800/60 transition-colors">
                                Showcase
                            </a>
                            <a href="#failover-sim" class="px-3.5 py-1.5 rounded-lg hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/70 dark:hover:bg-zinc-800/60 transition-colors">
                                Failover
                            </a>
                            <a href="#features" class="px-3.5 py-1.5 rounded-lg hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/70 dark:hover:bg-zinc-800/60 transition-colors">
                                Capabilities
                            </a>
                            <a href="#studio-deepdive" class="px-3.5 py-1.5 rounded-lg hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/70 dark:hover:bg-zinc-800/60 transition-colors">
                                Studio
                            </a>
                            <a href="#faq" class="px-3.5 py-1.5 rounded-lg hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/70 dark:hover:bg-zinc-800/60 transition-colors">
                                FAQ
                            </a>
                        </nav>
                    </div>

                    <!-- Right CTAs & Theme Toggle -->
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="window.toggleTheme(event)" title="Toggle Dark / Light Mode" aria-label="Toggle Dark / Light Mode"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-white/90 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all shadow-2xs focus:outline-none">
                            <span class="dark:hidden inline-flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </span>
                            <span class="hidden dark:inline-flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                        </button>

                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3.5 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-400 dark:hover:bg-amber-300 dark:text-zinc-950 text-xs font-bold rounded-xl shadow-xs hover:shadow-md transition-all duration-200 gap-1.5">
                                <i class="fas fa-gauge text-3xs"></i>
                                <span>Console</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                Sign In
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-3.5 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-400 dark:hover:bg-amber-300 dark:text-zinc-950 text-xs font-bold rounded-xl shadow-xs hover:shadow-md transition-all duration-200 gap-1">
                                    <span>Get Started</span>
                                    <i class="fas fa-arrow-right text-3xs"></i>
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10">

            <!-- REFINED SAAS HERO SECTION -->
            <section class="pt-8 sm:pt-14 pb-16 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto text-center">
                <div id="hero-text-container">
                    <!-- Clean Engineered Announcement Pill (Staged Entrance 1) -->
                    <div class="hero-reveal-1 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/80 shadow-2xs mb-6">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-zinc-700 dark:text-zinc-300">Multi-Provider Email Relay</span>
                        <span class="text-zinc-400">/</span>
                        <span class="text-amber-600 dark:text-amber-400 font-semibold">Automatic Backup Failover</span>
                    </div>

                    <!-- Clean, Powerful Headline (Staged Entrance 2 with Depth Zoom) -->
                    <div class="hero-reveal-2 max-w-4xl mx-auto">
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-zinc-900 dark:text-white leading-[1.15]">
                            Send high-volume emails with
                            <span class="inline-flex items-center align-middle gap-1.5 px-3 py-1 my-1 rounded-xl bg-amber-500/10 dark:bg-amber-400/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 font-mono text-[0.82em] font-extrabold tracking-normal shadow-xs">
                                <i class="fas fa-shield-check text-xs"></i> 100% uptime
                            </span>
                            and zero provider lock-in.
                        </h1>
                    </div>

                    <!-- Subtitle (Staged Entrance 3) -->
                    <p class="hero-reveal-3 mt-5 text-sm sm:text-lg text-zinc-600 dark:text-zinc-300 max-w-2xl mx-auto leading-relaxed font-normal">
                        Connect Amazon SES, Brevo, Google Workspace, or your own server. If one provider hits a daily limit or slows down, CampaignStack automatically switches to your backup in milliseconds so your emails always land in the primary inbox.
                    </p>

                    <!-- CTA Cluster (Staged Entrance 4) -->
                    <div class="hero-reveal-4 mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-400 dark:hover:bg-amber-300 dark:text-zinc-950 text-xs sm:text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 gap-2">
                                <span>Open Dispatch Console</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-400 dark:hover:bg-amber-300 dark:text-zinc-950 text-xs sm:text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 gap-2">
                                <span>Get Started Free</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        @endauth

                        <a href="#failover-sim" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800/80 text-xs sm:text-sm font-semibold rounded-xl shadow-2xs hover:shadow-xs transition-all duration-200 gap-2">
                            <i class="fas fa-play-circle text-amber-500"></i>
                            <span>See How Failover Works</span>
                        </a>
                    </div>

                    <!-- Plain-English Micro Trust Badges (Staged Entrance 5) -->
                    <div class="hero-reveal-5 mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        <span class="flex items-center gap-1.5"><i class="fas fa-rotate text-amber-500"></i> Auto-switches to backup in 8ms</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-shield-check text-emerald-500"></i> Safe pacing protects sender score</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-lock text-blue-500"></i> 100% Data privacy and control</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-check text-zinc-400"></i> Free setup, no credit card required</span>
                    </div>
                </div>

                <!-- APPLE PRO DISPLAY / STUDIO MONITOR HERO SHOWCASE (100% SOFTWARE FOCUS) -->
                <div id="hero-mockup" class="mt-8 relative mx-auto max-w-6xl text-center [perspective:1400px]">

                    <!-- Left 3D Floating Telemetry Pill (Desktop, Pure CSS Float) -->
                    <div class="hidden lg:flex items-center gap-2.5 absolute left-2 xl:left-4 top-1/4 -translate-y-1/2 px-3.5 py-2.5 rounded-2xl bg-white/85 dark:bg-zinc-900/85 backdrop-blur-md border border-zinc-200/80 dark:border-zinc-800/80 shadow-xl text-left animate-float-badge-left hover:rotate-0 transition-all duration-300 z-30 group">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xs">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5 text-2xs font-bold text-zinc-900 dark:text-white">
                                <span>99.98% Deliverability</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                            </div>
                            <p class="text-3xs text-zinc-500 font-mono">Zero IP Quota Stalls</p>
                        </div>
                    </div>

                    <!-- Right 3D Floating Failover Pill (Desktop, Pure CSS Float) -->
                    <div class="hidden lg:flex items-center gap-2.5 absolute right-2 xl:right-4 top-1/3 -translate-y-1/2 px-3.5 py-2.5 rounded-2xl bg-white/85 dark:bg-zinc-900/85 backdrop-blur-md border border-zinc-200/80 dark:border-zinc-800/80 shadow-xl text-left animate-float-badge-right hover:rotate-0 transition-all duration-300 z-30 group">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xs">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5 text-2xs font-bold text-zinc-900 dark:text-white">
                                <span>8ms Auto-Failover</span>
                                <span class="px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-500 text-3xs font-mono font-bold">ACTIVE</span>
                            </div>
                            <p class="text-3xs text-zinc-500 font-mono">5 Relays Load-Balanced</p>
                        </div>
                    </div>

                    <!-- Module Tab Switcher Above Monitor -->
                    <div class="flex items-center justify-center flex-wrap gap-2 mb-4 relative z-20">
                        <button type="button" onclick="switchMockupTab('dashboard')" id="tabBtn-dashboard" class="px-3.5 py-1.5 rounded-lg text-2xs font-bold transition-all bg-amber-500 text-zinc-950 shadow-xs">
                            <i class="fas fa-chart-line mr-1"></i> Command Center
                        </button>
                        <button type="button" onclick="switchMockupTab('dispatch')" id="tabBtn-dispatch" class="px-3.5 py-1.5 rounded-lg text-2xs font-bold transition-all bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-300 hover:border-amber-400">
                            <i class="fas fa-paper-plane mr-1 text-amber-500"></i> Dispatch Studio
                        </button>
                        <button type="button" onclick="switchMockupTab('accounts')" id="tabBtn-accounts" class="px-3.5 py-1.5 rounded-lg text-2xs font-bold transition-all bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-300 hover:border-amber-400">
                            <i class="fas fa-server mr-1 text-blue-500"></i> Relay Matrix
                        </button>
                        <button type="button" onclick="switchMockupTab('audience')" id="tabBtn-audience" class="px-3.5 py-1.5 rounded-lg text-2xs font-bold transition-all bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-300 hover:border-amber-400">
                            <i class="fas fa-users mr-1 text-emerald-500"></i> Audience Tags
                        </button>
                    </div>

                    <!-- 3D Tilting Apple Studio Display Desktop Monitor Frame -->
                    <div id="interactiveHeroMockup" class="studio-display-frame relative transition-all duration-300 mx-auto max-w-4xl text-left [transform-style:preserve-3d]">
                        <!-- Spotlight Hover Tracking Glow -->
                        <div id="mouseFollowerGlow" class="mouse-spotlight"></div>

                        <!-- Monitor Screen Bezel (Apple Studio Display / Pro Display XDR) -->
                        <div class="rounded-[20px] sm:rounded-[24px] border-[8px] sm:border-[10px] border-[#18181f] dark:border-[#22222b] bg-[#0c0c10] relative overflow-hidden shadow-[0_25px_65px_-12px_rgba(0,0,0,0.65)] ring-1 ring-white/10 z-10">
                            <!-- FaceTime HD Camera Dot & Sensor -->
                            <div class="absolute top-1.5 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-30 pointer-events-none">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#050508] border border-white/20"></span>
                                <span class="w-1 h-1 rounded-full bg-emerald-400/80 animate-pulse"></span>
                            </div>

                            <!-- In-App Browser Titlebar -->
                            <div class="px-4 py-2.5 bg-slate-100/90 dark:bg-zinc-900/90 border-b border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-red-400/90 inline-block"></span>
                                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-400/90 inline-block"></span>
                                        <span class="w-2.5 h-2.5 rounded-full bg-green-400/90 inline-block"></span>
                                    </div>
                                    <span id="mockupUrlBar" class="text-3xs font-mono text-zinc-500">app.campaignstack.io/dashboard</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span id="mockupStatusText" class="text-3xs font-bold text-emerald-600 dark:text-emerald-400">5 Gateways Active</span>
                                </div>
                            </div>

                            <!-- Retina Screen Surface -->
                            <div class="bg-white dark:bg-[#101014] text-zinc-900 dark:text-zinc-100 p-2 sm:p-2.5 relative z-10 overflow-hidden">
                                <!-- Screen 1: Command Center (Real High-Res Screenshot, 100% Readable) -->
                                <div id="screen-dashboard" class="mockup-tab-panel">
                                    <div class="relative rounded-xl overflow-hidden border border-zinc-200/60 dark:border-zinc-800/80 shadow-sm group">
                                        <img src="/i/dashboard_screenshot.png" alt="CampaignStack Command Center Dashboard" class="w-full h-auto object-cover object-top select-none transition-transform duration-500 group-hover:scale-[1.01]" />
                                        <div class="absolute bottom-2.5 right-2.5 px-2.5 py-1 rounded-lg bg-zinc-900/85 text-white text-3xs backdrop-blur-md font-mono border border-white/10 shadow-lg flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span>Live Platform Telemetry • Arjun Sharma</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Screen 2: Dispatch Studio -->
                                <div id="screen-dispatch" class="mockup-tab-panel hidden p-4 sm:p-5 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                                <h4 class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-white">Active Queue: Product Launch Broadcast (#NL-042)</h4>
                                            </div>
                                            <p class="text-3xs text-zinc-500 mt-0.5">14,391 of 18,450 Delivered • Paced at 120 msgs/min</p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-3xs font-bold">
                                            <i class="fas fa-check-circle mr-1"></i> 0 Stalls
                                        </span>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="w-full h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full w-[78%] transition-all"></div>
                                    </div>

                                    <!-- Relays Status Compact Strip -->
                                    <div class="grid grid-cols-3 gap-3 pt-1">
                                        <div class="p-3 rounded-xl border border-emerald-300 dark:border-emerald-800/60 bg-emerald-50/40 dark:bg-emerald-950/20">
                                            <div class="flex justify-between items-center text-3xs font-bold">
                                                <span>Brevo Relay</span>
                                                <span class="text-emerald-600">300/300 Cap</span>
                                            </div>
                                            <p class="text-3xs text-amber-600 font-semibold mt-0.5">Auto-Rollover Complete</p>
                                        </div>

                                        <div class="p-3 rounded-xl border border-amber-300 dark:border-amber-800/80 bg-amber-50/40 dark:bg-amber-950/20 ring-1 ring-amber-400/40">
                                            <div class="flex justify-between items-center text-3xs font-bold">
                                                <span>Scaleway TEM</span>
                                                <span class="text-amber-600">Sending Now</span>
                                            </div>
                                            <p class="text-3xs text-emerald-600 font-semibold mt-0.5">Latency: 118ms</p>
                                        </div>

                                        <div class="p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40">
                                            <div class="flex justify-between items-center text-3xs font-bold">
                                                <span>Amazon SES</span>
                                                <span class="text-zinc-400">Standby</span>
                                            </div>
                                            <p class="text-3xs text-zinc-500 mt-0.5">High Volume Ready</p>
                                        </div>
                                    </div>

                                    <!-- Micro Event Stream -->
                                    <div class="p-3 rounded-xl bg-zinc-900 text-zinc-300 font-mono text-3xs space-y-1 overflow-hidden">
                                        <div class="text-zinc-500">[00:32:11] 250 OK: Delivery verified to priya.nair@corp.in</div>
                                        <div class="text-emerald-400">[00:32:12] ENGINE: Pacing delay 300ms observed. Zero bounce flags.</div>
                                    </div>
                                </div>

                                <!-- Screen 3: Relay Matrix -->
                                <div id="screen-accounts" class="mockup-tab-panel hidden p-4 sm:p-5 space-y-3">
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span>Mail Relays & Multi-SMTP Gateways</span>
                                        <span class="text-3xs text-emerald-600">All Handshakes Validated</span>
                                    </div>
                                    <div class="space-y-2 text-2xs">
                                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-server text-amber-500 text-xs"></i>
                                                <span class="font-bold">Amazon SES Mumbai</span>
                                            </div>
                                            <span class="text-emerald-600 font-bold text-3xs">142ms • TLS 587</span>
                                        </div>
                                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-paper-plane text-blue-500 text-xs"></i>
                                                <span class="font-bold">Brevo Primary Relay</span>
                                            </div>
                                            <span class="text-emerald-600 font-bold text-3xs">98ms • TLS 587</span>
                                        </div>
                                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <i class="fab fa-google text-red-500 text-xs"></i>
                                                <span class="font-bold">Google Workspace Relay</span>
                                            </div>
                                            <span class="text-emerald-600 font-bold text-3xs">112ms • SSL 465</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Screen 4: Audience Tags -->
                                <div id="screen-audience" class="mockup-tab-panel hidden p-4 sm:p-5 space-y-3">
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span>Audience Tags & Segmentation</span>
                                        <span class="text-3xs text-zinc-500">24,500 Total Contacts</span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-2.5 py-1 rounded-md bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 text-3xs font-bold">VIP Buyers (4,120)</span>
                                        <span class="px-2.5 py-1 rounded-md bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 text-3xs font-bold">Bangalore Founders (2,890)</span>
                                        <span class="px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 text-3xs font-bold">Festive Shoppers (11,400)</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-3xs space-y-1.5">
                                        <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                            <span>Rajesh Mehta (mehta-logistics.in)</span>
                                            <span class="text-amber-600 font-bold">VIP Buyer</span>
                                        </div>
                                        <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                            <span>Pooja Deshmukh (fintechcloud.co)</span>
                                            <span class="text-blue-600 font-bold">B2B Lead</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Apple Studio Display Milled Aluminum Stand -->
                        <div class="relative z-0">
                            <!-- Stand Neck -->
                            <div class="w-20 sm:w-28 h-9 sm:h-12 bg-gradient-to-b from-[#b8bac4] to-[#9294a0] dark:from-[#32323e] dark:to-[#1e1e26] mx-auto shadow-md relative -mt-1 rounded-b-sm border-x border-black/10 dark:border-white/10">
                                <!-- Cable Management Ring -->
                                <div class="w-4 sm:w-5 h-4 sm:h-5 rounded-full bg-[#121218] dark:bg-[#0c0c10] mx-auto top-2.5 sm:top-3.5 relative border border-black/20 dark:border-white/15 shadow-inner"></div>
                            </div>

                            <!-- Flat Aluminum Desk Foot -->
                            <div class="w-44 sm:w-60 h-2.5 sm:h-3.5 bg-gradient-to-r from-[#b0b2be] via-[#d0d2dd] to-[#b0b2be] dark:from-[#202028] dark:via-[#353542] dark:to-[#202028] rounded-b-md mx-auto shadow-xl border-t border-white/40 dark:border-white/10 relative"></div>

                            <!-- Soft Desk Contact Shadow -->
                            <div class="w-52 sm:w-72 h-3.5 bg-black/20 dark:bg-black/60 blur-md mx-auto rounded-full mt-0.5"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FULL-BLEED DEDICATED RELAY ECOSYSTEM CAROUSEL (100% Edge-to-Edge with 3D Horizon Arc) -->
            <section id="orbital-relay-section" class="w-full py-16 border-y border-zinc-200/60 dark:border-zinc-800/60 overflow-hidden relative">
                <!-- Deep Cosmic Horizon & Gravitational Core (The Axis it revolves around) -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <!-- Subtle Horizon Axis Beam -->
                    <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-px bg-gradient-to-r from-transparent via-amber-500/25 dark:via-amber-400/20 to-transparent"></div>
                    <!-- Central Gravitational Core Ambient Glow -->
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[580px] h-[110px] bg-amber-500/[0.04] dark:bg-amber-400/[0.03] blur-3xl rounded-full"></div>
                    <!-- Left Void Vignette -->
                    <div class="absolute left-0 inset-y-0 w-24 sm:w-36 bg-gradient-to-r from-white dark:from-[#0c0c0e] to-transparent z-20 pointer-events-none"></div>
                    <!-- Right Void Vignette -->
                    <div class="absolute right-0 inset-y-0 w-24 sm:w-36 bg-gradient-to-l from-white dark:from-[#0c0c0e] to-transparent z-20 pointer-events-none"></div>
                </div>

                <div class="max-w-7xl mx-auto px-4 text-center mb-7 relative z-10">
                    <p class="text-3xs uppercase tracking-widest font-extrabold text-zinc-400 dark:text-zinc-500">
                        POWERED BY INDUSTRY-STANDARD RELAYS & PROTOCOLS
                    </p>
                </div>
                
                <div id="orbital-relay-stage" class="w-full marquee-mask relative overflow-hidden py-4 [perspective:1200px] [transform-style:preserve-3d]">
                    <div class="marquee-track gap-5 sm:gap-6 [transform-style:preserve-3d]">
                        @php
                        $relays = [
                            ['name' => 'Amazon SES', 'icon' => 'fab fa-aws', 'color' => 'text-amber-500 bg-amber-500/10'],
                            ['name' => 'Brevo', 'icon' => 'fas fa-paper-plane', 'color' => 'text-blue-500 bg-blue-500/10'],
                            ['name' => 'Google Workspace', 'icon' => 'fab fa-google', 'color' => 'text-red-500 bg-red-500/10'],
                            ['name' => 'Microsoft 365', 'icon' => 'fab fa-microsoft', 'color' => 'text-blue-600 bg-blue-600/10'],
                            ['name' => 'Postmark', 'icon' => 'fas fa-bolt-lightning', 'color' => 'text-yellow-500 bg-yellow-500/10'],
                            ['name' => 'Mailgun', 'icon' => 'fas fa-fire', 'color' => 'text-red-600 bg-red-600/10'],
                            ['name' => 'Scaleway', 'icon' => 'fas fa-cloud', 'color' => 'text-purple-500 bg-purple-500/10'],
                            ['name' => 'SendGrid', 'icon' => 'fas fa-envelope-open-text', 'color' => 'text-blue-400 bg-blue-400/10'],
                            ['name' => 'Resend', 'icon' => 'fas fa-cube', 'color' => 'text-zinc-800 dark:text-zinc-200 bg-zinc-200 dark:bg-zinc-800'],
                            ['name' => 'ZeptoMail', 'icon' => 'fas fa-shield-halved', 'color' => 'text-amber-500 bg-amber-500/10'],
                            ['name' => 'Private SMTP', 'icon' => 'fas fa-server', 'color' => 'text-emerald-500 bg-emerald-500/10'],
                        ];
                        $allRelays = array_merge($relays, $relays);
                        @endphp

                        @foreach($allRelays as $relay)
                            <div class="relay-orbital-card flex items-center gap-3 px-5 py-3 rounded-2xl bg-white dark:bg-[#141418] border border-zinc-200/90 dark:border-zinc-800/90 shadow-xs hover:border-amber-400/60 transition-all shrink-0 cursor-default group">
                                <div class="w-10 h-10 rounded-xl {{ $relay['color'] }} flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="{{ $relay['icon'] }}"></i>
                                </div>
                                <span class="text-sm font-bold text-zinc-900 dark:text-white whitespace-nowrap">{{ $relay['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- INTERACTIVE FAILOVER SIMULATOR (#failover-sim) -->
            <section id="failover-sim" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 gsap-reveal">
                <div class="rounded-3xl p-6 sm:p-8 bg-white/90 dark:bg-[#121215]/90 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-lg relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800/80 pb-5 mb-6">
                        <div>
                            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-md text-3xs font-bold uppercase tracking-wider bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                <i class="fas fa-bolt"></i> Interactive Failover Simulator
                            </div>
                            <h3 class="text-lg sm:text-xl font-black text-zinc-900 dark:text-white mt-1">
                                Watch Intelligent Failover Protect Your Broadcast
                            </h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                If an email provider hits quota or experiences latency, CampaignStack auto-reroutes traffic in milliseconds.
                            </p>
                        </div>

                        <!-- Switch -->
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">Simulate Server Outage:</span>
                            <button id="simToggleBtn" onclick="toggleSimFailover()" type="button" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-zinc-300 dark:bg-zinc-700 transition-colors duration-200 ease-in-out focus:outline-none">
                                <span id="simToggleThumb" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Flow Visualizer -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                        <div class="lg:col-span-3 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-center">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 mx-auto flex items-center justify-center text-base mb-2">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-white">Customer Queue</h4>
                            <p class="text-3xs text-zinc-500">15,000 Contacts</p>
                            <div class="mt-2 inline-flex items-center gap-1 text-3xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-1.5 py-0.5 rounded">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>Pacing: Active</span>
                            </div>
                        </div>

                        <div class="lg:col-span-1 hidden lg:flex justify-center text-zinc-400 text-sm">
                            <i class="fas fa-arrow-right"></i>
                        </div>

                        <div class="lg:col-span-4 space-y-2">
                            <div id="simGatewayA" class="p-3 rounded-xl border transition-all duration-300 border-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-xs">A</div>
                                    <div>
                                        <p class="text-xs font-bold text-zinc-900 dark:text-white">Relay A: Brevo</p>
                                        <p id="simStatusA" class="text-3xs text-emerald-600 dark:text-emerald-400">Status: Healthy • Active Route</p>
                                    </div>
                                </div>
                                <span id="simBadgeA" class="text-3xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/60 px-1.5 py-0.5 rounded">PRIMARY</span>
                            </div>

                            <div id="simGatewayB" class="p-3 rounded-xl border transition-all duration-300 border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-xs">B</div>
                                    <div>
                                        <p class="text-xs font-bold text-zinc-900 dark:text-white">Relay B: AWS SES</p>
                                        <p id="simStatusB" class="text-3xs text-zinc-500">Status: Standby • Armed</p>
                                    </div>
                                </div>
                                <span id="simBadgeB" class="text-3xs font-bold text-zinc-500 bg-zinc-200 dark:bg-zinc-800 px-1.5 py-0.5 rounded">STANDBY</span>
                            </div>
                        </div>

                        <div class="lg:col-span-1 hidden lg:flex justify-center text-zinc-400 text-sm">
                            <i class="fas fa-arrow-right"></i>
                        </div>

                        <div class="lg:col-span-3 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-center">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 mx-auto flex items-center justify-center text-base mb-2">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-white">Customer Inboxes</h4>
                            <p id="simDeliveryReport" class="text-3xs text-emerald-600 dark:text-emerald-400 font-semibold">100% Delivered • 0 Lost Emails</p>
                            <div class="mt-2 inline-flex items-center gap-1 text-3xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.5 rounded">
                                <i class="fas fa-shield-check"></i>
                                <span>Zero Broadcast Disruption</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CORE CAPABILITIES (#features) WITH BIDIRECTIONAL SCROLL & MOUSE HOVER -->
            <section id="features" class="py-16 sm:py-20 border-t border-zinc-200/60 dark:border-zinc-800/60">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-14 gsap-reveal">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Core Architecture</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white mt-2">
                            Engineered for Total Reliability & Inbox Placement
                        </h2>
                        <p class="mt-3 text-sm sm:text-base text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Traditional platforms hold your list hostage. CampaignStack gives you full independence and bulletproof delivery.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="interactive-card gsap-reveal p-6 sm:p-8 rounded-2xl bg-white/90 dark:bg-[#141417]/90 border border-slate-200/90 dark:border-zinc-800/90 shadow-sm hover:border-amber-400/60 hover:-translate-y-1 transition-all duration-300">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-lg mb-4">
                                <i class="fas fa-server"></i>
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Multi-Gateway Automatic Failover</h3>
                            <p class="mt-2.5 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Connect multiple gateways. If one provider slows down or reaches a daily cap, CampaignStack seamlessly rolls over to your backup server without interrupting your broadcast.
                            </p>
                            <div class="mt-5 pt-3.5 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-xs">
                                <span class="font-semibold text-amber-600 dark:text-amber-400">Zero dropped emails</span>
                                <span class="text-zinc-400 font-mono">Auto-circuit breaker</span>
                            </div>
                        </div>

                        <div class="interactive-card gsap-reveal p-6 sm:p-8 rounded-2xl bg-white/90 dark:bg-[#141417]/90 border border-slate-200/90 dark:border-zinc-800/90 shadow-sm hover:border-emerald-400/60 hover:-translate-y-1 transition-all duration-300">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-lg mb-4">
                                <i class="fas fa-gauge-high"></i>
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Reputation Pacing Dial</h3>
                            <p class="mt-2.5 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Avoid the "Promotions" and "Spam" tabs. Send messages spaced with calibrated delay intervals to mirror authentic sender patterns and protect domain trust.
                            </p>
                            <div class="mt-5 pt-3.5 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-xs">
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">Custom rate limits</span>
                                <span class="text-zinc-400 font-mono">Spam tab avoidance</span>
                            </div>
                        </div>

                        <div class="interactive-card gsap-reveal p-6 sm:p-8 rounded-2xl bg-white/90 dark:bg-[#141417]/90 border border-slate-200/90 dark:border-zinc-800/90 shadow-sm hover:border-blue-400/60 hover:-translate-y-1 transition-all duration-300">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-lg mb-4">
                                <i class="fas fa-users-viewfinder"></i>
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Audience Tagging & Segmentation</h3>
                            <p class="mt-2.5 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Organize contacts with flexible tag taxonomies. Filter your subscribers dynamically per broadcast without duplicating contact lists or paying extra per subscriber.
                            </p>
                            <div class="mt-5 pt-3.5 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-xs">
                                <span class="font-semibold text-blue-600 dark:text-blue-400">Smart exclusions</span>
                                <span class="text-zinc-400 font-mono">Zero contact tax</span>
                            </div>
                        </div>

                        <div class="interactive-card gsap-reveal p-6 sm:p-8 rounded-2xl bg-white/90 dark:bg-[#141417]/90 border border-slate-200/90 dark:border-zinc-800/90 shadow-sm hover:border-purple-400/60 hover:-translate-y-1 transition-all duration-300">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center text-lg mb-4">
                                <i class="fas fa-terminal"></i>
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Real-Time Telemetry & Inspector</h3>
                            <p class="mt-2.5 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Complete transparency into every message. Audit worker logs, SMTP handshake codes, retry queues, and open/click telemetry directly in your console.
                            </p>
                            <div class="mt-5 pt-3.5 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-xs">
                                <span class="font-semibold text-purple-600 dark:text-purple-400">Audit handshake logs</span>
                                <span class="text-zinc-400 font-mono">Instant queue retries</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4-STAGE STUDIO WORKFLOW (#studio-deepdive) -->
            <section id="studio-deepdive" class="py-16 sm:py-20 bg-zinc-100/60 dark:bg-[#0d0d10] border-t border-zinc-200/60 dark:border-zinc-800/60">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Section Header (Clear & Intuitive) -->
                    <div class="text-center max-w-2xl mx-auto mb-12 gsap-reveal">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Step-by-Step Workflow</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white mt-2">
                            How High-Volume Sending Works in 4 Simple Steps
                        </h2>
                        <p class="mt-3 text-sm sm:text-base text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            CampaignStack's Dispatch Studio guides you from contact selection to delivery verification, ensuring you never make accidental sending mistakes.
                        </p>
                    </div>

                    <!-- Interactive Split Studio UI (Symmetric Height Alignment) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                        <!-- Left Steps Navigation (5 cols) -->
                        <div class="lg:col-span-5 flex flex-col justify-between gap-3 h-full">
                            <!-- Step 1 Tab -->
                            <button type="button" onclick="selectStudioStep(1)" id="studio-step-btn-1" class="w-full flex-1 text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 bg-amber-500/10 border-amber-400 dark:bg-amber-400/10 dark:border-amber-400/50 shadow-xs flex items-start gap-3.5 group cursor-pointer ring-1 ring-amber-400/40">
                                <span id="studio-step-num-1" class="w-7 h-7 shrink-0 rounded-lg bg-amber-500 text-zinc-950 font-black flex items-center justify-center text-xs shadow-xs">1</span>
                                <div>
                                    <h4 class="text-base font-bold text-zinc-900 dark:text-white group-hover:text-amber-500 transition-colors">1. Pick Your Audience</h4>
                                    <p class="mt-1 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">Filter contacts with custom tags, review subscriber counts, and automatically exclude unsubscribes.</p>
                                </div>
                            </button>

                            <!-- Step 2 Tab -->
                            <button type="button" onclick="selectStudioStep(2)" id="studio-step-btn-2" class="w-full flex-1 text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 bg-white dark:bg-zinc-900/80 border-zinc-200 dark:border-zinc-800 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 flex items-start gap-3.5 group cursor-pointer">
                                <span id="studio-step-num-2" class="w-7 h-7 shrink-0 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-black flex items-center justify-center text-xs">2</span>
                                <div>
                                    <h4 class="text-base font-bold text-zinc-900 dark:text-white group-hover:text-amber-500 transition-colors">2. Assign Email Relays</h4>
                                    <p class="mt-1 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">Choose which provider sends first (e.g. Amazon SES) and set your automatic backup relay (e.g. Brevo).</p>
                                </div>
                            </button>

                            <!-- Step 3 Tab -->
                            <button type="button" onclick="selectStudioStep(3)" id="studio-step-btn-3" class="w-full flex-1 text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 bg-white dark:bg-zinc-900/80 border-zinc-200 dark:border-zinc-800 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 flex items-start gap-3.5 group cursor-pointer">
                                <span id="studio-step-num-3" class="w-7 h-7 shrink-0 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-black flex items-center justify-center text-xs">3</span>
                                <div>
                                    <h4 class="text-base font-bold text-zinc-900 dark:text-white group-hover:text-amber-500 transition-colors">3. Set Sending Speed</h4>
                                    <p class="mt-1 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">Adjust pacing delays between emails so you mirror authentic human sending and protect inbox placement.</p>
                                </div>
                            </button>

                            <!-- Step 4 Tab -->
                            <button type="button" onclick="selectStudioStep(4)" id="studio-step-btn-4" class="w-full flex-1 text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 bg-white dark:bg-zinc-900/80 border-zinc-200 dark:border-zinc-800 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 flex items-start gap-3.5 group cursor-pointer">
                                <span id="studio-step-num-4" class="w-7 h-7 shrink-0 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-black flex items-center justify-center text-xs">4</span>
                                <div>
                                    <h4 class="text-base font-bold text-zinc-900 dark:text-white group-hover:text-amber-500 transition-colors">4. Launch & Monitor Live</h4>
                                    <p class="mt-1 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">Watch queue workers deliver in real time with live progress gauges, pause buttons, and error counters.</p>
                                </div>
                            </button>
                        </div>

                        <!-- Right Interactive Live Preview Card (7 cols, Symmetrical Height) -->
                        <div class="lg:col-span-7 rounded-3xl p-6 sm:p-8 bg-white dark:bg-[#121216] border border-zinc-200/90 dark:border-zinc-800/90 shadow-xl relative flex flex-col justify-between h-full">
                            <!-- In-Preview Header -->
                            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 pb-4 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-white">Studio Preview</span>
                                </div>
                                <span id="studio-preview-step-badge" class="px-2.5 py-1 rounded-md text-xs font-mono font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">Stage 1 of 4</span>
                            </div>

                            <!-- Screen 1: Audience Selection Preview (Rich & Animated) -->
                            <div id="studio-step-preview-1" class="flex-1 flex flex-col justify-center space-y-3.5 py-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                        <h5 class="text-sm font-bold text-zinc-900 dark:text-white">Audience Segmentation Engine</h5>
                                    </div>
                                    <span class="text-xs font-mono font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">18,450 Ready</span>
                                </div>

                                <!-- Filter Search Simulation Bar -->
                                <div class="relative">
                                    <div class="w-full bg-zinc-100 dark:bg-zinc-800/70 border border-zinc-200/80 dark:border-zinc-700/60 rounded-xl py-2 px-3 text-xs flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400 font-mono">
                                            <i class="fas fa-filter text-amber-500 text-2xs"></i>
                                            <span>active_tags: <strong class="text-zinc-800 dark:text-zinc-200">vip, tech, founders</strong></span>
                                        </div>
                                        <span class="text-2xs font-semibold font-mono text-zinc-400">0 unsubscribes filtered</span>
                                    </div>
                                </div>

                                <!-- Active Filter Chips with live glow -->
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-500/15 border border-amber-500/30 text-amber-700 dark:text-amber-300 text-xs font-bold flex items-center gap-1.5 shadow-2xs">
                                        <i class="fas fa-check text-2xs"></i> VIP Buyers (4,120)
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg bg-blue-500/15 border border-blue-500/30 text-blue-700 dark:text-blue-300 text-xs font-bold flex items-center gap-1.5 shadow-2xs">
                                        <i class="fas fa-check text-2xs"></i> Active Founders (2,890)
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 text-xs font-bold flex items-center gap-1.5 shadow-2xs">
                                        <i class="fas fa-check text-2xs"></i> Newsletter (11,440)
                                    </span>
                                </div>

                                <!-- Live Streamed Verified Contact Feed -->
                                <div class="rounded-xl border border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/60 dark:bg-zinc-900/40 p-2.5 space-y-1.5 text-xs font-mono">
                                    <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                                        <span class="truncate max-w-[200px] flex items-center gap-1.5"><i class="fas fa-envelope text-2xs text-amber-500"></i> ankit.s@payflow.in</span>
                                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-2xs">MX Validated • 0.4ms</span>
                                    </div>
                                    <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                                        <span class="truncate max-w-[200px] flex items-center gap-1.5"><i class="fas fa-envelope text-2xs text-amber-500"></i> priya@saascart.io</span>
                                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-2xs">Primary Score 99.8%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Screen 2: Relay Allocation Preview (Rich & Animated) -->
                            <div id="studio-step-preview-2" class="hidden flex-1 flex flex-col justify-center space-y-3 py-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                                        <h5 class="text-sm font-bold text-zinc-900 dark:text-white">Active Multi-Relay Route Map</h5>
                                    </div>
                                    <span class="text-xs font-mono font-semibold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full">Circuit Breaker: Armed</span>
                                </div>

                                <!-- Priority 1 Gateway -->
                                <div class="p-3.5 rounded-xl border border-emerald-400/60 bg-emerald-50/50 dark:bg-emerald-950/20 flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold font-mono bg-emerald-500 text-white">ROUTE 1</span>
                                        <div>
                                            <p class="text-xs font-bold text-zinc-900 dark:text-white flex items-center gap-1.5">
                                                <span>Amazon SES (Mumbai)</span>
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            </p>
                                            <p class="text-2xs text-zinc-500 font-mono">Quota: 85,210 / 100,000 • 24ms TLS Latency</p>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono">PRIMARY</span>
                                </div>

                                <!-- Priority 2 Backup Gateway -->
                                <div class="p-3.5 rounded-xl border border-amber-400/50 bg-amber-50/40 dark:bg-amber-950/20 flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold font-mono bg-amber-500 text-zinc-950">ROUTE 2</span>
                                        <div>
                                            <p class="text-xs font-bold text-zinc-900 dark:text-white flex items-center gap-1.5">
                                                <span>Brevo Dedicated Relay</span>
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                                            </p>
                                            <p class="text-2xs text-zinc-500 font-mono">Standby • Triggers if Route 1 throttles (8ms)</p>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400 font-mono">STANDBY</span>
                                </div>

                                <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800/60 flex items-center justify-between text-2xs font-mono text-zinc-500">
                                    <span>Failover Trigger: Rate Limit / 429 Error</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">0 Dropped Emails Guaranteed</span>
                                </div>
                            </div>

                            <!-- Screen 3: Cadence & Pacing Preview (Rich & Animated) -->
                            <div id="studio-step-preview-3" class="hidden flex-1 flex flex-col justify-center space-y-3.5 py-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                                        <h5 class="text-sm font-bold text-zinc-900 dark:text-white">Reputation Pacing & Delay Dial</h5>
                                    </div>
                                    <span id="pacing-risk-badge" class="text-xs font-mono font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">Risk: 0.0% Optimal</span>
                                </div>

                                <!-- Interactive Speed Presets -->
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" onclick="setPacingSpeed(60, 'Conservative (60/min)', 1000, '3h 12m', '0.0% Safe', 'w-1/3')" class="p-2 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-amber-400 text-left transition-all cursor-pointer group">
                                        <p class="text-2xs text-zinc-400">Warm-up</p>
                                        <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 group-hover:text-amber-500 font-mono">60 / min</p>
                                    </button>
                                    <button type="button" onclick="setPacingSpeed(120, 'Balanced (120/min)', 500, '2h 34m', '0.0% Optimal', 'w-2/3')" class="p-2 rounded-xl border border-amber-400 bg-amber-500/10 dark:bg-amber-400/10 text-left transition-all cursor-pointer group">
                                        <p class="text-2xs text-amber-600 dark:text-amber-400 font-semibold">Recommended</p>
                                        <p class="text-xs font-bold text-zinc-900 dark:text-white font-mono">120 / min</p>
                                    </button>
                                    <button type="button" onclick="setPacingSpeed(300, 'High Volume (300/min)', 200, '1h 01m', '0.1% Monitored', 'w-full')" class="p-2 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-amber-400 text-left transition-all cursor-pointer group">
                                        <p class="text-2xs text-zinc-400">Turbo</p>
                                        <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 group-hover:text-amber-500 font-mono">300 / min</p>
                                    </button>
                                </div>

                                <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800/80 space-y-2">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="font-semibold text-zinc-700 dark:text-zinc-300">Paced Transmission Cadence:</span>
                                        <span id="pacing-speed-text" class="font-mono font-bold text-amber-600 dark:text-amber-400 text-sm">120 emails / minute</span>
                                    </div>
                                    <div class="w-full h-2.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                                        <div id="pacing-bar-fill" class="h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-full w-2/3 transition-all duration-300"></div>
                                    </div>
                                    <div class="flex justify-between text-2xs text-zinc-500 font-mono pt-0.5">
                                        <span id="pacing-delay-text">500ms delay between emails</span>
                                        <span id="pacing-time-text">Estimated time: ~2h 34m</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Screen 4: Live Delivery Progress Preview (Rich & Animated Ticker) -->
                            <div id="studio-step-preview-4" class="hidden flex-1 flex flex-col justify-center space-y-3.5 py-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="relative flex h-2.5 w-2.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                        </span>
                                        <h5 class="text-sm font-bold text-zinc-900 dark:text-white">Live Broadcast Telemetry</h5>
                                    </div>
                                    <span class="text-xs font-mono font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full flex items-center gap-1">
                                        <i class="fas fa-bolt text-2xs"></i> 18 msgs/sec
                                    </span>
                                </div>

                                <!-- Dynamic Progress Meter -->
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-xs font-mono">
                                        <span id="live-progress-count" class="text-zinc-600 dark:text-zinc-300 font-semibold">14,391 of 18,450 Delivered</span>
                                        <span id="live-progress-percent" class="font-bold text-emerald-600 dark:text-emerald-400">78% Complete</span>
                                    </div>
                                    <div class="w-full h-3 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden p-0.5 border border-zinc-200/50 dark:border-zinc-700/50">
                                        <div id="live-progress-bar" class="h-full bg-gradient-to-r from-amber-400 via-amber-500 to-emerald-400 rounded-full w-[78%] transition-all duration-500 shadow-sm"></div>
                                    </div>
                                </div>

                                <!-- Real-time SMTP Handshake Console Stream -->
                                <div class="p-3 rounded-xl bg-zinc-950 text-zinc-300 font-mono text-2xs space-y-1 border border-zinc-800 shadow-inner">
                                    <div class="flex items-center justify-between text-zinc-500 pb-1 border-b border-zinc-800/80">
                                        <span>SMTP WORKER CONSOLE</span>
                                        <span class="text-emerald-400">0 ERRORS</span>
                                    </div>
                                    <div class="text-zinc-400 truncate">[01:20:45] 250 OK: id=CS-82910 delivered to founder@fintechcloud.in (22ms)</div>
                                    <div class="text-emerald-400 truncate">[01:20:46] WORKER #3: Throttle delay 500ms observed • In-box guaranteed</div>
                                </div>
                            </div>

                            <!-- Bottom Next / Prev Controls -->
                            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between">
                                <button type="button" onclick="prevStudioStep()" class="px-3.5 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-800 text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer">
                                    &larr; Previous Stage
                                </button>
                                <button type="button" onclick="nextStudioStep()" class="px-4 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-zinc-950 text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                                    <span>Next Stage</span>
                                    <i class="fas fa-arrow-right text-2xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ Section (#faq) -->
            <section id="faq" class="py-16 sm:py-20 border-t border-zinc-200/60 dark:border-zinc-800/60">
                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-14 gsap-reveal">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Clear Answers</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white mt-2">Frequently Asked Questions</h2>
                        <p class="mt-3 text-sm sm:text-base text-zinc-600 dark:text-zinc-400 leading-relaxed">Everything you need to know about CampaignStack's dispatch architecture.</p>
                    </div>

                    <div class="space-y-3.5">
                        @php
                        $faqs = [
                            [
                                'q' => 'Do I need coding or technical skills to use CampaignStack?',
                                'a' => 'Not at all. Setting up a mail account is as simple as entering your SMTP credentials (host, port, username, password) or connecting via API. Import contacts via CSV, compose your message, and hit send.',
                                'open' => true,
                            ],
                            [
                                'q' => 'What is multi-gateway failover and why does it matter?',
                                'a' => 'Most businesses rely on a single email provider. If that provider experiences an outage, reaches a daily sending cap, or flags an account, your entire business communication halts. With CampaignStack, you connect backup gateways. If server A has an issue, CampaignStack automatically reroutes traffic through server B in milliseconds with zero dropped emails.',
                                'open' => false,
                            ],
                            [
                                'q' => 'How does algorithmic pacing protect my domain reputation?',
                                'a' => 'Blasting thousands of emails simultaneously alerts spam filters and hurts deliverability. CampaignStack paces each email with natural, configurable delays (e.g. 500ms), ensuring high inbox placement and protecting your domain sender score.',
                                'open' => false,
                            ],
                            [
                                'q' => 'Can I send from my company\'s custom domain?',
                                'a' => 'Absolutely. You can send using any verified sender address on your domain (e.g. newsletter@yourbusiness.com). You retain 100% brand authenticity, DKIM alignment, and SPF compliance.',
                                'open' => false,
                            ],
                            [
                                'q' => 'How much does it cost compared to Mailchimp or Klaviyo?',
                                'a' => 'Traditional tools charge high monthly fees per contact. CampaignStack allows you to plug in generous free and low-cost relays (like Amazon SES at $0.10 per 1,000 emails or Brevo\'s free tier), typically reducing email infrastructure bills by 80% to 90%.',
                                'open' => false,
                            ],
                        ];
                        @endphp

                        @foreach($faqs as $faq)
                            <details name="faq-accordion" {{ $faq['open'] ? 'open' : '' }} class="group rounded-2xl bg-white dark:bg-[#141417] border border-zinc-200/80 dark:border-zinc-800/80 transition-all duration-200 hover:border-amber-400/50 open:border-amber-400/60 open:shadow-xs overflow-hidden">
                                <summary class="list-none flex items-center justify-between p-5 sm:p-6 cursor-pointer select-none font-bold text-sm sm:text-base text-zinc-900 dark:text-white transition-colors">
                                    <span>{{ $faq['q'] }}</span>
                                    <span class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800/90 flex items-center justify-center shrink-0 ml-4 group-open:bg-amber-500 group-open:text-zinc-950 transition-colors">
                                        <i class="fas fa-chevron-down text-2xs transition-transform duration-300 group-open:rotate-180"></i>
                                    </span>
                                </summary>
                                <div class="px-5 sm:px-6 pb-5 sm:pb-6 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed border-t border-zinc-100 dark:border-zinc-800/60 pt-4">
                                    {{ $faq['a'] }}
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Bottom CTA Banner -->
            <section class="py-16 sm:py-20 border-t border-zinc-200/60 dark:border-zinc-800/60">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="relative rounded-3xl p-8 sm:p-14 bg-gradient-to-br from-zinc-900 via-zinc-900 to-zinc-950 text-white overflow-hidden shadow-2xl border border-zinc-800 text-center">
                        <div class="absolute -top-24 -right-24 w-72 h-72 bg-amber-500/20 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-yellow-500/10 rounded-full blur-3xl pointer-events-none"></div>

                        <div class="relative z-10 max-w-xl mx-auto space-y-5">
                            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                                Ready to take full control of your email dispatch?
                            </h2>
                            <p class="text-sm sm:text-base text-zinc-300 leading-relaxed max-w-lg mx-auto">
                                Join businesses orchestrating their outbound email infrastructure with high delivery rates and zero vendor lock-in.
                            </p>
                            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 bg-amber-400 hover:bg-amber-300 text-zinc-950 text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 gap-2">
                                        <span>Open Dispatch Console</span>
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 bg-amber-400 hover:bg-amber-300 text-zinc-950 text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 gap-2">
                                        <span>Launch Dispatch Console</span>
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-zinc-200/70 dark:border-zinc-800/80 bg-white/60 dark:bg-[#09090B]/60 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="space-y-3 md:col-span-2">
                        <a href="/" class="flex items-center gap-3">
                            <x-application-logo class="h-7 w-auto" />
                        </a>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm leading-relaxed">
                            CampaignStack is an intelligent outbound email orchestration platform offering multi-gateway routing, automatic failover, and paced dispatch for growing businesses.
                        </p>
                        <div class="flex items-center gap-2 text-2xs text-emerald-600 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>All Dispatch Workers & Telemetry Operational</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">Platform</p>
                        <ul class="space-y-1.5 text-xs text-zinc-600 dark:text-zinc-400">
                            <li><a href="#hero-mockup" class="hover:text-amber-500 transition-colors">Showcase</a></li>
                            <li><a href="#failover-sim" class="hover:text-amber-500 transition-colors">Failover Engine</a></li>
                            <li><a href="#features" class="hover:text-amber-500 transition-colors">Capabilities</a></li>
                            <li><a href="#studio-deepdive" class="hover:text-amber-500 transition-colors">Studio Workflow</a></li>
                        </ul>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">Console</p>
                        <ul class="space-y-1.5 text-xs text-zinc-600 dark:text-zinc-400">
                            <li><a href="{{ route('login') }}" class="hover:text-amber-500 transition-colors">Sign In</a></li>
                            @if (Route::has('register'))
                                <li><a href="{{ route('register') }}" class="hover:text-amber-500 transition-colors">Register Operator</a></li>
                            @endif
                            <li><a href="#faq" class="hover:text-amber-500 transition-colors">FAQ</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-zinc-200/60 dark:border-zinc-800/60 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-zinc-500">
                    <p>&copy; {{ date('Y') }} CampaignStack. All rights reserved.</p>
                    <p class="flex items-center gap-1.5">
                        <i class="fas fa-shield-alt text-amber-500/80"></i>
                        <span>High-Throughput Dispatch Infrastructure</span>
                    </p>
                </div>
            </div>
        </footer>

        <!-- INTERACTIVE SCRIPTS: 3D MOUSE PARALLAX, SPOTLIGHT & BIDIRECTIONAL SCROLL -->
        <script>
            // 0. Deep Spatial Scroll Camera Engine (Silky LERP Inertia, 120fps)
            const heroTextContainer = document.getElementById('hero-text-container');
            const heroDisplayMockup = document.getElementById('hero-mockup');
            const leftFloatBadge = document.querySelector('.animate-float-badge-left');
            const rightFloatBadge = document.querySelector('.animate-float-badge-right');

            let targetScrollY = window.scrollY;
            let currentScrollY = window.scrollY;
            let isScrollLoopRunning = false;

            function lerp(start, end, factor) {
                return start + (end - start) * factor;
            }

            function updateSpatialCamera() {
                // Smooth damped inertia (factor 0.085 provides buttery Apple-like deceleration)
                currentScrollY = lerp(currentScrollY, targetScrollY, 0.085);

                if (currentScrollY <= 800) {
                    const y = currentScrollY;

                    // Deep optical receding camera for hero text
                    if (heroTextContainer) {
                        const textScale = Math.max(0.86, 1 - y * 0.00028);
                        const textTranslateY = y * 0.22;
                        const textTranslateZ = -y * 0.75;
                        const textRotateX = Math.min(6, y * 0.009);
                        const textOpacity = Math.max(0, 1 - y * 0.0019);

                        heroTextContainer.style.transform = `perspective(1200px) translate3d(0, ${textTranslateY.toFixed(2)}px, ${textTranslateZ.toFixed(2)}px) rotateX(${textRotateX.toFixed(2)}deg) scale(${textScale.toFixed(4)})`;
                        heroTextContainer.style.opacity = textOpacity.toFixed(3);
                    }

                    // Deep spatial rising zoom for Apple Studio Monitor
                    if (heroDisplayMockup) {
                        const mScale = Math.min(1.04, 0.96 + y * 0.00014);
                        const mTranslateY = Math.max(-20, -y * 0.08);
                        const mRotateX = Math.max(0, 3.5 - y * 0.007);

                        heroDisplayMockup.style.transform = `perspective(1400px) translate3d(0, ${mTranslateY.toFixed(2)}px, 0) scale(${mScale.toFixed(4)}) rotateX(${mRotateX.toFixed(2)}deg)`;
                    }

                    // Layered Parallax on Side Telemetry Badges
                    if (leftFloatBadge) {
                        leftFloatBadge.style.transform = `translate3d(0, ${(-y * 0.16).toFixed(1)}px, 0)`;
                    }
                    if (rightFloatBadge) {
                        rightFloatBadge.style.transform = `translate3d(0, ${(y * 0.12).toFixed(1)}px, 0)`;
                    }
                }

                // Continue loop while moving, pause when idle
                if (Math.abs(targetScrollY - currentScrollY) > 0.05) {
                    requestAnimationFrame(updateSpatialCamera);
                } else {
                    currentScrollY = targetScrollY;
                    isScrollLoopRunning = false;
                }
            }

            window.addEventListener('scroll', () => {
                targetScrollY = window.scrollY;
                if (!isScrollLoopRunning) {
                    isScrollLoopRunning = true;
                    requestAnimationFrame(updateSpatialCamera);
                }
            }, { passive: true });

            // Initialize camera position on first render
            updateSpatialCamera();

            // 1. Mockup Tab Switcher
            const tabs = ['dashboard', 'dispatch', 'accounts', 'audience'];
            const tabMeta = {
                dashboard: { url: 'app.campaignstack.io/dashboard', status: '5 Gateways Active' },
                dispatch: { url: 'app.campaignstack.io/dispatch-studio', status: 'Live Dispatch: 120 msgs/min' },
                accounts: { url: 'app.campaignstack.io/mail-accounts', status: '4 Relays Armed & Validated' },
                audience: { url: 'app.campaignstack.io/contacts', status: '24,500 Contacts Synced' }
            };

            function switchMockupTab(activeTab) {
                tabs.forEach(tab => {
                    const screen = document.getElementById(`screen-${tab}`);
                    const btn = document.getElementById(`tabBtn-${tab}`);
                    if (!screen || !btn) return;

                    if (tab === activeTab) {
                        screen.classList.remove('hidden');
                        btn.className = "px-3.5 py-1.5 rounded-lg text-2xs font-bold transition-all bg-amber-500 text-zinc-950 shadow-xs";
                    } else {
                        screen.classList.add('hidden');
                        btn.className = "px-3.5 py-1.5 rounded-lg text-2xs font-bold transition-all bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-300 hover:border-amber-400";
                    }
                });

                const urlBar = document.getElementById('mockupUrlBar');
                const statusText = document.getElementById('mockupStatusText');
                if (urlBar && tabMeta[activeTab]) urlBar.textContent = tabMeta[activeTab].url;
                if (statusText && tabMeta[activeTab]) statusText.textContent = tabMeta[activeTab].status;
            }

            // 1.5. Dispatch Studio Interactive Stepper
            let currentStudioStep = 1;
            function selectStudioStep(step) {
                currentStudioStep = step;
                const badge = document.getElementById('studio-preview-step-badge');
                if (badge) badge.textContent = `Stage ${step} of 4`;

                [1, 2, 3, 4].forEach(i => {
                    const btn = document.getElementById(`studio-step-btn-${i}`);
                    const num = document.getElementById(`studio-step-num-${i}`);
                    const preview = document.getElementById(`studio-step-preview-${i}`);
                    if (!btn || !preview) return;

                    if (i === step) {
                        btn.className = "w-full flex-1 text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 bg-amber-500/10 border-amber-400 dark:bg-amber-400/10 dark:border-amber-400/50 shadow-xs flex items-start gap-3.5 group cursor-pointer ring-1 ring-amber-400/40";
                        if (num) num.className = "w-7 h-7 shrink-0 rounded-lg bg-amber-500 text-zinc-950 font-black flex items-center justify-center text-xs shadow-xs";
                        preview.classList.remove('hidden');
                    } else {
                        btn.className = "w-full flex-1 text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 bg-white dark:bg-zinc-900/80 border-zinc-200 dark:border-zinc-800 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 flex items-start gap-3.5 group cursor-pointer";
                        if (num) num.className = "w-7 h-7 shrink-0 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-black flex items-center justify-center text-xs";
                        preview.classList.add('hidden');
                    }
                });
            }

            function nextStudioStep() {
                const next = currentStudioStep >= 4 ? 1 : currentStudioStep + 1;
                selectStudioStep(next);
            }

            function prevStudioStep() {
                const prev = currentStudioStep <= 1 ? 4 : currentStudioStep - 1;
                selectStudioStep(prev);
            }

            // Pacing Speed Interactive Presets
            function setPacingSpeed(speed, title, delayMs, estTime, riskBadge, barWidth) {
                const speedText = document.getElementById('pacing-speed-text');
                const delayText = document.getElementById('pacing-delay-text');
                const timeText = document.getElementById('pacing-time-text');
                const barFill = document.getElementById('pacing-bar-fill');
                const risk = document.getElementById('pacing-risk-badge');

                if (speedText) speedText.textContent = `${speed} emails / minute`;
                if (delayText) delayText.textContent = `${delayMs}ms delay between emails`;
                if (timeText) timeText.textContent = `Estimated time: ~${estTime}`;
                if (risk) risk.textContent = `Risk: ${riskBadge}`;
                if (barFill) {
                    barFill.className = `h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-full transition-all duration-300 ${barWidth}`;
                }
            }

            // Screen 4 Live Delivery Ticker (Alive & Organic)
            let liveDelivered = 14391;
            setInterval(() => {
                if (currentStudioStep === 4) {
                    liveDelivered += Math.floor(Math.random() * 5) + 3;
                    if (liveDelivered > 18450) liveDelivered = 14391;
                    const percent = Math.min(100, Math.floor((liveDelivered / 18450) * 100));
                    
                    const countEl = document.getElementById('live-progress-count');
                    const percentEl = document.getElementById('live-progress-percent');
                    const barEl = document.getElementById('live-progress-bar');

                    if (countEl) countEl.textContent = `${liveDelivered.toLocaleString()} of 18,450 Delivered`;
                    if (percentEl) percentEl.textContent = `${percent}% Complete`;
                    if (barEl) barEl.style.width = `${percent}%`;
                }
            }, 1400);

            // 2. Interactive Failover Simulation Engine
            let simIsFailed = false;
            function toggleSimFailover() {
                simIsFailed = !simIsFailed;
                const thumb = document.getElementById('simToggleThumb');
                const gwA = document.getElementById('simGatewayA');
                const gwB = document.getElementById('simGatewayB');
                const statusA = document.getElementById('simStatusA');
                const statusB = document.getElementById('simStatusB');
                const badgeA = document.getElementById('simBadgeA');
                const badgeB = document.getElementById('simBadgeB');
                const report = document.getElementById('simDeliveryReport');

                if (simIsFailed) {
                    thumb.classList.remove('translate-x-0');
                    thumb.classList.add('translate-x-5');

                    gwA.className = "p-3 rounded-xl border transition-all duration-300 border-red-400 bg-red-50/50 dark:bg-red-950/20 flex items-center justify-between";
                    statusA.className = "text-3xs text-red-500 font-semibold";
                    statusA.textContent = "Status: Quota Cap Reached (Simulated)";
                    badgeA.className = "text-3xs font-bold text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/60 px-1.5 py-0.5 rounded";
                    badgeA.textContent = "CIRCUIT TRIPPED";

                    gwB.className = "p-3 rounded-xl border transition-all duration-300 border-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20 flex items-center justify-between ring-1 ring-emerald-400/50";
                    statusB.className = "text-3xs text-emerald-600 dark:text-emerald-400 font-semibold";
                    statusB.textContent = "Status: Route Absorbed in 8ms";
                    badgeB.className = "text-3xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/60 px-1.5 py-0.5 rounded";
                    badgeB.textContent = "DISPATCHING";

                    report.textContent = "100% Delivered via Backup Relay • 0 Dropped Messages";
                } else {
                    thumb.classList.remove('translate-x-5');
                    thumb.classList.add('translate-x-0');

                    gwA.className = "p-3 rounded-xl border transition-all duration-300 border-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20 flex items-center justify-between";
                    statusA.className = "text-3xs text-emerald-600 dark:text-emerald-400";
                    statusA.textContent = "Status: Healthy • Active Route";
                    badgeA.className = "text-3xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/60 px-1.5 py-0.5 rounded";
                    badgeA.textContent = "PRIMARY";

                    gwB.className = "p-3 rounded-xl border transition-all duration-300 border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 flex items-center justify-between";
                    statusB.className = "text-3xs text-zinc-500";
                    statusB.textContent = "Status: Standby • Armed";
                    badgeB.className = "text-3xs font-bold text-zinc-500 bg-zinc-200 dark:bg-zinc-800 px-1.5 py-0.5 rounded";
                    badgeB.textContent = "STANDBY";

                    report.textContent = "100% Delivered • 0 Lost Emails";
                }
            }

            // 3. Fast, Snappy Native Tilt & Interactive Spotlight Tracking (Zero-GSAP)
            document.addEventListener('DOMContentLoaded', () => {
                const mockupCard = document.getElementById('interactiveHeroMockup');
                const glow = document.getElementById('mouseFollowerGlow');

                if (mockupCard) {
                    mockupCard.addEventListener('mousemove', (e) => {
                        const rect = mockupCard.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;

                        const rotateX = ((y - rect.height / 2) / (rect.height / 2)) * -5;
                        const rotateY = ((x - rect.width / 2) / (rect.width / 2)) * 5;

                        mockupCard.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg)`;

                        if (glow) {
                            glow.style.left = `${x}px`;
                            glow.style.top = `${y}px`;
                            glow.style.opacity = '1';
                        }
                    });

                    mockupCard.addEventListener('mouseleave', () => {
                        mockupCard.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
                        if (glow) glow.style.opacity = '0';
                    });
                }

                // 4. Lightweight Mouse Glow on Interactive Cards
                document.querySelectorAll('.interactive-card').forEach((card) => {
                    card.addEventListener('mousemove', (e) => {
                        const rect = card.getBoundingClientRect();
                        card.style.setProperty('--mouse-x', `${e.clientX - rect.left}px`);
                        card.style.setProperty('--mouse-y', `${e.clientY - rect.top}px`);
                    });
                });

                // 5. Ultra-Fast Native Bidirectional Scroll Reveal (Forward & Reverse, 120fps)
                const revealElements = document.querySelectorAll('.reveal-on-scroll, .gsap-reveal');
                if ('IntersectionObserver' in window) {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('is-revealed');
                            } else {
                                // Reverse scroll animation: reset smoothly when scrolled away
                                entry.target.classList.remove('is-revealed');
                            }
                        });
                    }, {
                        threshold: 0.12,
                        rootMargin: '0px 0px -40px 0px'
                    });

                    revealElements.forEach(el => {
                        el.classList.add('reveal-on-scroll');
                        observer.observe(el);
                    });
                } else {
                    revealElements.forEach(el => el.classList.add('is-revealed'));
                }
            });

            // 6. Sunrise / Twilight Ray Transition Handler
            window.toggleTheme = function () {
                const isDark = document.documentElement.classList.contains('dark');
                const nextTheme = isDark ? 'light' : 'dark';

                const x = window.innerWidth;
                const y = 0;
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

            // 3. 3D Orbital Horizon Curved Carousel Engine
            const orbitalCards = document.querySelectorAll('.relay-orbital-card');
            const orbitalStage = document.getElementById('orbital-relay-stage');

            function renderOrbitalArc() {
                if (orbitalStage && orbitalCards.length) {
                    const stageRect = orbitalStage.getBoundingClientRect();
                    const centerX = stageRect.left + stageRect.width / 2;
                    const halfWidth = stageRect.width / 2;

                    // Only compute when stage is visible in the viewport
                    if (stageRect.bottom > -50 && stageRect.top < window.innerHeight + 50) {
                        orbitalCards.forEach(card => {
                            const cardRect = card.getBoundingClientRect();
                            const cardCenter = cardRect.left + cardRect.width / 2;
                            // Normalized distance from screen center (-1.0 to +1.0)
                            const d = (cardCenter - centerX) / (halfWidth || 1);

                            // Smooth 3D Cylindrical Horizon Arc Math:
                            // Pushes backward into the void along Z-axis by up to -250px at edges
                            const z = -Math.pow(d, 2) * 250;
                            // Curves smoothly inward up to 26 degrees around the central horizon
                            const rotY = -d * 26;
                            // Subtle pitch elevation into the void
                            const rotX = Math.pow(d, 2) * 3.5;
                            // Gentle distance scale falloff
                            const scale = Math.max(0.85, 1 - Math.abs(d) * 0.14);
                            // Dissolves into the void as it wraps around the edges
                            const opacity = Math.max(0.2, 1 - Math.pow(Math.abs(d), 2.2) * 0.8);

                            card.style.transform = `perspective(1000px) translate3d(0, 0, ${z.toFixed(1)}px) rotateY(${rotY.toFixed(1)}deg) rotateX(${rotX.toFixed(1)}deg) scale(${scale.toFixed(3)})`;
                            card.style.opacity = opacity.toFixed(2);
                        });
                    }
                }
                requestAnimationFrame(renderOrbitalArc);
            }
            requestAnimationFrame(renderOrbitalArc);
        </script>
    </body>
</html>
