<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CampaignStack') }} — The Autonomous Multi-Gateway Email Dispatch Platform</title>
        <meta name="description" content="Orchestrate customer broadcasts across multiple email gateways with intelligent failover, algorithmic dispatch pacing, and guaranteed inbox delivery.">

        <link rel="icon" type="image/x-icon" href="/i/campaign-stack-100.png">

        <!-- Fonts -->
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

            /* Subtle Precision Grid */
            .saas-grid-pattern {
                background-image: linear-gradient(to right, rgba(148, 163, 184, 0.08) 1px, transparent 1px),
                                  linear-gradient(to bottom, rgba(148, 163, 184, 0.08) 1px, transparent 1px);
                background-size: 32px 32px;
                mask-image: radial-gradient(ellipse 75% 55% at 50% 0%, #000 65%, transparent 100%);
                -webkit-mask-image: radial-gradient(ellipse 75% 55% at 50% 0%, #000 65%, transparent 100%);
            }
            .dark .saas-grid-pattern {
                background-image: linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                                  linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
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
        </style>

        <!-- Icon Font & GSAP Animation Suite -->
        <link rel="preload" href="/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="/css/all.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

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
                <!-- Shimmering Announcement Pill -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-semibold bg-white dark:bg-zinc-900 border border-amber-400/40 dark:border-amber-400/30 shadow-xs backdrop-blur-md mb-6 shimmer-pill">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span class="text-zinc-800 dark:text-zinc-200">CampaignStack 2.0</span>
                    <span class="text-amber-600 dark:text-amber-400 font-bold">• Autonomous Multi-SMTP Failover &rarr;</span>
                </div>

                <!-- Headline -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-zinc-900 dark:text-white leading-[1.1] max-w-4xl mx-auto">
                    The Autonomous Outbound
                    <span class="block mt-1 bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 bg-clip-text text-transparent">
                        Email Dispatch Platform.
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="mt-4 text-sm sm:text-lg text-zinc-600 dark:text-zinc-300 max-w-2xl mx-auto leading-relaxed font-normal">
                    Connect multiple email relays—Amazon SES, Brevo, Google Workspace, or custom SMTPs. Eliminate single-vendor outages with intelligent auto-failover, algorithmic pacing, and guaranteed primary inbox placement.
                </p>

                <!-- CTA Cluster -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
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
                        <span>See Failover In Action</span>
                    </a>
                </div>

                <!-- Micro Trust Badges -->
                <div class="mt-6 flex flex-wrap items-center justify-center gap-x-5 gap-y-1.5 text-2xs font-semibold text-zinc-500 dark:text-zinc-400">
                    <span class="flex items-center gap-1.5"><i class="fas fa-bolt text-amber-500"></i> 8ms Failover Latency</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-shield-halved text-emerald-500"></i> RFC 5322 & TLS 1.3</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-database text-blue-500"></i> 100% Data Sovereignty</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-check text-emerald-500"></i> No Credit Card Required</span>
                </div>                <!-- PHOTOREALISTIC APPLE MACBOOK PRO HARDWARE SHOWCASE WITH 3D MOUSE PARALLAX -->
                <!-- PHOTOREALISTIC 3D FLOATING APPLE MACBOOK PRO (TRANSPARENT ISOLATED) -->
                <div id="hero-mockup" class="mt-8 relative mx-auto max-w-6xl text-center [perspective:1400px]">
                    <!-- Immersive Ambient Aurora Bloom Behind Laptop (Pure GPU CSS, Zero-Weight) -->
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] max-w-4xl h-[420px] bg-gradient-to-r from-amber-500/15 via-orange-500/10 to-indigo-500/15 dark:from-amber-500/20 dark:via-amber-400/10 dark:to-purple-600/15 rounded-full blur-[110px] pointer-events-none -z-10"></div>

                    <!-- Left 3D Floating Telemetry Pill (Desktop) -->
                    <div class="hidden lg:flex items-center gap-2.5 absolute left-2 xl:left-6 top-1/3 -translate-y-1/2 px-3.5 py-2 rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border border-zinc-200/80 dark:border-zinc-800/80 shadow-xl text-left -rotate-3 hover:rotate-0 transition-all duration-300 z-20 group">
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

                    <!-- Right 3D Floating Failover Pill (Desktop) -->
                    <div class="hidden lg:flex items-center gap-2.5 absolute right-2 xl:right-6 top-2/5 -translate-y-1/2 px-3.5 py-2 rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border border-zinc-200/80 dark:border-zinc-800/80 shadow-xl text-left rotate-3 hover:rotate-0 transition-all duration-300 z-20 group">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xs">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5 text-2xs font-bold text-zinc-900 dark:text-white">
                                <span>8ms Auto-Failover</span>
                                <span class="px-1 py-0.2 rounded bg-amber-500/20 text-amber-500 text-3xs font-mono">ACTIVE</span>
                            </div>
                            <p class="text-3xs text-zinc-500 font-mono">5 Relays Load-Balanced</p>
                        </div>
                    </div>

                    <!-- 3D Tilting Transparent MacBook Pro -->
                    <div id="interactiveHeroMockup" class="relative transition-all duration-300 mx-auto group select-none [transform-style:preserve-3d]">
                        <!-- Spotlight Hover Tracking Glow -->
                        <div id="mouseFollowerGlow" class="mouse-spotlight"></div>

                        <!-- 3D Floating Hardware Mockup (Transparent WebP) -->
                        <div class="relative drop-shadow-[0_25px_45px_rgba(0,0,0,0.35)] dark:drop-shadow-[0_30px_60px_rgba(0,0,0,0.75)] transition-transform duration-500 group-hover:scale-[1.015]">
                            <img src="/i/macbook_pro_transparent.webp" 
                                 alt="CampaignStack Command Center on Apple MacBook Pro" 
                                 class="w-full max-w-4xl mx-auto h-auto object-contain select-none" />

                            <!-- Floating Glass Live Telemetry Badge -->
                            <div class="absolute top-[8%] right-[8%] sm:top-[10%] sm:right-[10%] px-3 py-1.5 rounded-xl bg-zinc-950/85 text-white text-3xs sm:text-2xs backdrop-blur-md font-mono border border-white/15 shadow-xl flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="font-bold text-zinc-100 hidden sm:inline">Live Command Center</span>
                                <span class="text-zinc-400">Arjun Sharma</span>
                                <span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold">5 Relays Active</span>
                            </div>
                        </div>

                        <!-- Keynote Stage Horizon Beam & Layered Ambient Shadows -->
                        <div class="w-[85%] h-px bg-gradient-to-r from-transparent via-amber-400/40 dark:via-amber-400/30 to-transparent mx-auto -mt-6"></div>
                        <div class="w-[78%] h-7 bg-black/25 dark:bg-black/75 blur-2xl mx-auto rounded-full mt-1"></div>
                        <div class="w-[60%] h-4 bg-amber-500/10 dark:bg-amber-400/5 blur-xl mx-auto rounded-full -mt-2"></div>
                    </div>
                </div>

                <!-- Trusted Relay Marquee -->
                <div class="mt-12 pt-8 border-t border-zinc-200/60 dark:border-zinc-800/60 text-center">
                    <p class="text-3xs uppercase tracking-widest font-extrabold text-zinc-400 dark:text-zinc-500 mb-4">
                        POWERED BY INDUSTRY-STANDARD RELAYS
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-5 sm:gap-10 opacity-75 hover:opacity-100 transition-opacity">
                        <div class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300 font-bold text-xs">
                            <i class="fab fa-aws text-sm text-amber-500"></i> Amazon SES
                        </div>
                        <div class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300 font-bold text-xs">
                            <i class="fas fa-paper-plane text-sm text-blue-500"></i> Brevo
                        </div>
                        <div class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300 font-bold text-xs">
                            <i class="fab fa-google text-sm text-red-500"></i> Google Workspace
                        </div>
                        <div class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300 font-bold text-xs">
                            <i class="fas fa-cloud text-sm text-purple-500"></i> Scaleway
                        </div>
                        <div class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300 font-bold text-xs">
                            <i class="fas fa-bolt text-sm text-blue-400"></i> SendGrid
                        </div>
                        <div class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300 font-bold text-xs">
                            <i class="fas fa-server text-sm text-emerald-500"></i> Private SMTP
                        </div>
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
                    <div class="text-center max-w-2xl mx-auto mb-12 gsap-reveal">
                        <span class="text-xs font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">Core Architecture</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white mt-1.5">
                            Engineered for Total Reliability & Inbox Placement
                        </h2>
                        <p class="mt-2 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">
                            Traditional platforms hold your list hostage. CampaignStack gives you full independence and bulletproof delivery.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="interactive-card gsap-reveal p-6 sm:p-7 rounded-2xl bg-white/90 dark:bg-[#141417]/90 border border-slate-200/90 dark:border-zinc-800/90 shadow-sm hover:border-amber-400/60 hover:-translate-y-1 transition-all duration-300">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-lg mb-4">
                                <i class="fas fa-server"></i>
                            </div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">Multi-Gateway Automatic Failover</h3>
                            <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Connect multiple gateways. If one provider slows down or reaches a daily cap, CampaignStack seamlessly rolls over to your backup server without interrupting your broadcast.
                            </p>
                            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-2xs">
                                <span class="font-semibold text-amber-600 dark:text-amber-400">Zero dropped broadcasts</span>
                                <span class="text-zinc-400 text-3xs">Auto-circuit breaker</span>
                            </div>
                        </div>

                        <div class="interactive-card gsap-reveal p-6 sm:p-7 rounded-2xl bg-white/90 dark:bg-[#141417]/90 border border-slate-200/90 dark:border-zinc-800/90 shadow-sm hover:border-emerald-400/60 hover:-translate-y-1 transition-all duration-300">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-lg mb-4">
                                <i class="fas fa-gauge-high"></i>
                            </div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">Algorithmic Dispatch Pacing</h3>
                            <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Avoid Gmail & Outlook spam flags. Queue workers deliver messages with calibrated cadence, protecting domain reputation and ensuring primary inbox arrival.
                            </p>
                            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-2xs">
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">Domain warm-up calibrated</span>
                                <span class="text-zinc-400 text-3xs">Spam tab avoidance</span>
                            </div>
                        </div>

                        <div class="interactive-card gsap-reveal p-6 sm:p-7 rounded-2xl bg-white/90 dark:bg-[#141417]/90 border border-slate-200/90 dark:border-zinc-800/90 shadow-sm hover:border-blue-400/60 hover:-translate-y-1 transition-all duration-300">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-lg mb-4">
                                <i class="fas fa-tags"></i>
                            </div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">Flexible Audience Segmentation</h3>
                            <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Organize contacts with dynamic tags (VIP Buyers, Leads, Festive Shoppers). Send targeted updates without paying penalties for duplicate contacts across lists.
                            </p>
                            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-2xs">
                                <span class="font-semibold text-blue-600 dark:text-blue-400">1-click CSV/Excel import</span>
                                <span class="text-zinc-400 text-3xs">Zero duplicate contact charges</span>
                            </div>
                        </div>

                        <div class="interactive-card gsap-reveal p-6 sm:p-7 rounded-2xl bg-white/90 dark:bg-[#141417]/90 border border-slate-200/90 dark:border-zinc-800/90 shadow-sm hover:border-purple-400/60 hover:-translate-y-1 transition-all duration-300">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center text-lg mb-4">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">Real-Time Telemetry & Inspector</h3>
                            <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Complete transparency into every message. Audit worker logs, SMTP handshake codes, retry queues, and open/click telemetry directly in your console.
                            </p>
                            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-2xs">
                                <span class="font-semibold text-purple-600 dark:text-purple-400">Audit handshake logs</span>
                                <span class="text-zinc-400 text-3xs">Instant queue retries</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4-STAGE STUDIO WORKFLOW (#studio-deepdive) -->
            <section id="studio-deepdive" class="py-16 sm:py-20 bg-zinc-100/60 dark:bg-[#0d0d10] border-t border-zinc-200/60 dark:border-zinc-800/60">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-12 gsap-reveal">
                        <span class="text-xs font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">Exclusive Workflow</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white mt-1.5">
                            The 4-Stage Broadcast Dispatch Studio
                        </h2>
                        <p class="mt-2 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">
                            A dedicated pre-send staging environment engineered so you never make mistakes before launching high-volume broadcasts.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="interactive-card gsap-reveal p-5 rounded-2xl bg-white dark:bg-[#141417] border border-zinc-200 dark:border-zinc-800 shadow-2xs space-y-2.5">
                            <span class="w-6 h-6 rounded-lg bg-amber-500 text-zinc-950 font-black flex items-center justify-center text-xs">1</span>
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Audience Pre-Flight</h4>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Review recipient lists, verify valid domain formats, and filter by tag groups before a single email is touched.
                            </p>
                        </div>

                        <div class="interactive-card gsap-reveal p-5 rounded-2xl bg-white dark:bg-[#141417] border border-zinc-200 dark:border-zinc-800 shadow-2xs space-y-2.5">
                            <span class="w-6 h-6 rounded-lg bg-amber-500 text-zinc-950 font-black flex items-center justify-center text-xs">2</span>
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Gateway Orchestration</h4>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Choose which outbound relays will handle this broadcast. Set primary vs failover precedence with 1 click.
                            </p>
                        </div>

                        <div class="interactive-card gsap-reveal p-5 rounded-2xl bg-white dark:bg-[#141417] border border-zinc-200 dark:border-zinc-800 shadow-2xs space-y-2.5">
                            <span class="w-6 h-6 rounded-lg bg-amber-500 text-zinc-950 font-black flex items-center justify-center text-xs">3</span>
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Cadence & Pacing Dial</h4>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Configure milliseconds between sends. Calibrated to match your sender reputation and provider limits.
                            </p>
                        </div>

                        <div class="interactive-card gsap-reveal p-5 rounded-2xl bg-white dark:bg-[#141417] border border-zinc-200 dark:border-zinc-800 shadow-2xs space-y-2.5">
                            <span class="w-6 h-6 rounded-lg bg-amber-500 text-zinc-950 font-black flex items-center justify-center text-xs">4</span>
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Live Flush & Audit</h4>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Watch the workers deliver in real time with live progress gauges, pause/resume switches, and instant retry tools.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ Section (#faq) -->
            <section id="faq" class="py-16 sm:py-20 border-t border-zinc-200/60 dark:border-zinc-800/60">
                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12 gsap-reveal">
                        <span class="text-xs font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">Clear Answers</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white mt-1">Frequently Asked Questions</h2>
                        <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">Everything you need to know about CampaignStack's dispatch architecture.</p>
                    </div>

                    <div class="space-y-3.5">
                        <div class="gsap-reveal p-5 rounded-2xl bg-white dark:bg-[#141417] border border-zinc-200/80 dark:border-zinc-800/80">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Do I need coding or technical skills to use CampaignStack?</h3>
                            <p class="mt-1.5 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Not at all. Setting up a mail account is as simple as copying your SMTP host, port, username, and password from your provider and pasting them into the setup form. Import contacts via CSV, compose your newsletter, and hit send.
                            </p>
                        </div>

                        <div class="gsap-reveal p-5 rounded-2xl bg-white dark:bg-[#141417] border border-zinc-200/80 dark:border-zinc-800/80">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">What is multi-gateway failover and why does it matter?</h3>
                            <p class="mt-1.5 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Most businesses rely on a single email provider. If that provider experiences an outage, reaches a daily sending cap, or flags an account, your entire business communication halts. With CampaignStack, you connect backup gateways. If server A has an issue, CampaignStack automatically reroutes traffic through server B with zero dropped emails.
                            </p>
                        </div>

                        <div class="gsap-reveal p-5 rounded-2xl bg-white dark:bg-[#141417] border border-zinc-200/80 dark:border-zinc-800/80">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">How does algorithmic pacing protect my domain reputation?</h3>
                            <p class="mt-1.5 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Blasting thousands of emails in a few seconds triggers spam filters. CampaignStack paces each email with natural, human-like delays, ensuring high inbox placement and protecting your domain score.
                            </p>
                        </div>

                        <div class="gsap-reveal p-5 rounded-2xl bg-white dark:bg-[#141417] border border-zinc-200/80 dark:border-zinc-800/80">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Can I send from my company's custom domain?</h3>
                            <p class="mt-1.5 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Absolutely. You can send using any verified sender email address on your domain (e.g. `newsletter@yourbusiness.com`). You retain 100% brand authenticity.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bottom CTA Banner -->
            <section class="py-16 sm:py-20 border-t border-zinc-200/60 dark:border-zinc-800/60">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="relative rounded-3xl p-8 sm:p-12 bg-gradient-to-br from-zinc-900 via-zinc-900 to-zinc-950 text-white overflow-hidden shadow-2xl border border-zinc-800 text-center">
                        <div class="absolute -top-24 -right-24 w-72 h-72 bg-amber-500/20 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-yellow-500/10 rounded-full blur-3xl pointer-events-none"></div>

                        <div class="relative z-10 max-w-xl mx-auto space-y-5">
                            <h2 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                                Ready to take full control of your email dispatch?
                            </h2>
                            <p class="text-xs sm:text-sm text-zinc-400 leading-relaxed">
                                Join businesses orchestrating their outbound email infrastructure with high delivery rates and zero vendor lock-in.
                            </p>
                            <div class="pt-1 flex flex-col sm:flex-row items-center justify-center gap-3">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 bg-amber-400 hover:bg-amber-300 text-zinc-950 text-xs sm:text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 gap-2">
                                        <span>Open Dispatch Console</span>
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 bg-amber-400 hover:bg-amber-300 text-zinc-950 text-xs sm:text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 gap-2">
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

            // 3. Mouse 3D Tilt & Interactive Spotlight Tracking
            document.addEventListener('DOMContentLoaded', () => {
                const mockupCard = document.getElementById('interactiveHeroMockup');
                const glow = document.getElementById('mouseFollowerGlow');

                if (mockupCard) {
                    mockupCard.addEventListener('mousemove', (e) => {
                        const rect = mockupCard.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;

                        const centerX = rect.width / 2;
                        const centerY = rect.height / 2;
                        const rotateX = ((y - centerY) / centerY) * -7;
                        const rotateY = ((x - centerX) / centerX) * 7;

                        gsap.to(mockupCard, {
                            rotateX: rotateX,
                            rotateY: rotateY,
                            duration: 0.35,
                            ease: 'power2.out',
                            transformPerspective: 1000
                        });

                        if (glow) {
                            glow.style.left = `${x}px`;
                            glow.style.top = `${y}px`;
                            glow.style.opacity = '1';
                        }
                    });

                    mockupCard.addEventListener('mouseleave', () => {
                        gsap.to(mockupCard, {
                            rotateX: 0,
                            rotateY: 0,
                            duration: 0.8,
                            ease: 'power2.out',
                            transformPerspective: 1000
                        });
                        if (glow) glow.style.opacity = '0';
                    });
                }

                // 4. Mouse Glow Position on Interactive Cards
                document.querySelectorAll('.interactive-card').forEach((card) => {
                    card.addEventListener('mousemove', (e) => {
                        const rect = card.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        card.style.setProperty('--mouse-x', `${x}px`);
                        card.style.setProperty('--mouse-y', `${y}px`);
                    });
                });

                // 5. MacBook Lid Opening on Scroll + Bidirectional Scroll Animations
                if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                    gsap.registerPlugin(ScrollTrigger);

                    // Smooth 3D Lid Opening Scrub
                    const mockup = document.getElementById('interactiveHeroMockup');
                    if (mockup) {
                        gsap.fromTo(mockup,
                            {
                                rotateX: 38,
                                scale: 0.88,
                                transformOrigin: 'bottom center',
                                transformPerspective: 1200
                            },
                            {
                                rotateX: 0,
                                scale: 1,
                                ease: 'power1.out',
                                scrollTrigger: {
                                    trigger: '#hero-mockup',
                                    start: 'top 95%',
                                    end: 'center 50%',
                                    scrub: 1.2
                                }
                            }
                        );
                    }

                    gsap.utils.toArray('.gsap-reveal').forEach((el) => {
                        gsap.fromTo(el,
                            { opacity: 0, y: 28 },
                            {
                                opacity: 1,
                                y: 0,
                                duration: 0.6,
                                ease: 'power2.out',
                                scrollTrigger: {
                                    trigger: el,
                                    start: 'top 88%',
                                    end: 'bottom 12%',
                                    toggleActions: 'play reverse play reverse'
                                }
                            }
                        );
                    });
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
        </script>
    </body>
</html>
