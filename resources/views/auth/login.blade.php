<x-guest-layout :wide="true">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
        <!-- Left Showcase Column: Brand Story, Capabilities & Telemetry -->
        <div class="lg:col-span-7 flex flex-col justify-center space-y-6 lg:pr-6">
            <!-- Platform Badge -->
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/25 shadow-2xs backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>CampaignStack 2.0 • Dispatch Engine</span>
                </div>
            </div>

            <!-- Hero Headline -->
            <div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-zinc-900 dark:text-white leading-[1.15]">
                    High-throughput outbound delivery with
                    <span class="bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 bg-clip-text text-transparent">
                        intelligent failover.
                    </span>
                </h1>
                <p class="mt-4 text-sm sm:text-base text-zinc-600 dark:text-zinc-400 max-w-xl leading-relaxed">
                    Orchestrate multi-SMTP routing, automated quota balancing, dispatch queues, and granular contact segmentation through a single high-performance console.
                </p>
            </div>

            <!-- Capabilities Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <!-- Card 1: Gateways -->
                <div class="p-4 rounded-2xl bg-white/70 dark:bg-[#141417]/70 border border-slate-200/80 dark:border-zinc-800/80 backdrop-blur-sm shadow-2xs hover:border-amber-400/40 transition-all">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm mb-3">
                        <i class="fas fa-server"></i>
                    </div>
                    <h3 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Multi-SMTP</h3>
                    <p class="text-2xs text-zinc-500 dark:text-zinc-400 mt-1 leading-normal">
                        Dynamic gateway routing with live server health checks.
                    </p>
                </div>

                <!-- Card 2: Dispatch Studio -->
                <div class="p-4 rounded-2xl bg-white/70 dark:bg-[#141417]/70 border border-slate-200/80 dark:border-zinc-800/80 backdrop-blur-sm shadow-2xs hover:border-amber-400/40 transition-all">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm mb-3">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Zero-Stall</h3>
                    <p class="text-2xs text-zinc-500 dark:text-zinc-400 mt-1 leading-normal">
                        Concurrent worker queues designed for massive broadcast volume.
                    </p>
                </div>

                <!-- Card 3: Security & Telemetry -->
                <div class="p-4 rounded-2xl bg-white/70 dark:bg-[#141417]/70 border border-slate-200/80 dark:border-zinc-800/80 backdrop-blur-sm shadow-2xs hover:border-amber-400/40 transition-all">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm mb-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Telemetry</h3>
                    <p class="text-2xs text-zinc-500 dark:text-zinc-400 mt-1 leading-normal">
                        Real-time delivery status, latency tracking, and metrics.
                    </p>
                </div>
            </div>

            <!-- Platform Operational Pill -->
            <div class="pt-2 flex flex-wrap items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400">
                <div class="inline-flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="font-semibold text-zinc-800 dark:text-zinc-200">Dispatch Network Operational</span>
                </div>
                <span>•</span>
                <span>TLS 1.3 Encrypted</span>
                <span>•</span>
                <span>RFC 5322 Compliant</span>
            </div>
        </div>

        <!-- Right Column: High-Craft Login Card -->
        <div class="lg:col-span-5 w-full max-w-md mx-auto">
            <div class="relative bg-white/95 dark:bg-[#141417]/95 backdrop-blur-2xl border border-slate-200/90 dark:border-zinc-800/90 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.07)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] rounded-3xl p-6 sm:p-8 transition-all">
                <!-- Top ambient hairline glow -->
                <div class="absolute top-0 inset-x-8 h-px bg-gradient-to-r from-transparent via-amber-400/40 to-transparent"></div>

                <!-- Card Header -->
                <div class="flex items-start justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400/20 via-amber-500/10 to-transparent border border-amber-400/30 dark:border-amber-400/20 flex items-center justify-center mb-4 shadow-2xs">
                            <img src="/i/campaignstack-icon.png" alt="CampaignStack Icon" class="w-7 h-7 object-contain" />
                        </div>
                        <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                            Sign In
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Enter your credentials to access the dispatch console.
                        </p>
                    </div>
                </div>

                <!-- Session Status / Alert -->
                @if (session('status'))
                    <div class="mt-4 p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/25 text-xs font-medium text-amber-800 dark:text-amber-200 flex items-center gap-2">
                        <i class="fas fa-info-circle text-amber-500 shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mt-4 p-3.5 rounded-xl bg-red-500/10 border border-red-500/25 text-xs font-medium text-red-600 dark:text-red-400 space-y-1">
                        <div class="flex items-center gap-2 font-semibold">
                            <i class="fas fa-exclamation-circle text-red-500 shrink-0"></i>
                            <span>Authentication failed</span>
                        </div>
                        <ul class="list-disc list-inside text-2xs pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-2xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                            {{ __('Email Address') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500 text-sm">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input id="email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   placeholder="operator@campaignstack.io"
                                   class="w-full pl-10 pr-4 py-2.5 bg-zinc-50/80 dark:bg-zinc-900/80 border border-zinc-300/80 dark:border-zinc-800 rounded-xl text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-amber-500 dark:focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 transition-all shadow-2xs">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-2xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                {{ __('Password') }}
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500 text-sm">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input id="password"
                                   type="password"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••••••"
                                   class="w-full pl-10 pr-11 py-2.5 bg-zinc-50/80 dark:bg-zinc-900/80 border border-zinc-300/80 dark:border-zinc-800 rounded-xl text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-amber-500 dark:focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 transition-all shadow-2xs">
                            <button type="button"
                                    id="passwordToggleBtn"
                                    onclick="togglePasswordVisibility()"
                                    aria-label="Toggle password visibility"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors focus:outline-none">
                                <i id="passwordToggleIcon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input id="remember_me"
                                   type="checkbox"
                                   name="remember"
                                   class="w-4 h-4 rounded text-amber-500 bg-zinc-100 dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700 focus:ring-amber-500/30 focus:ring-offset-0 transition-colors">
                            <span class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                                {{ __('Remember me on this device') }}
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                                class="group relative w-full h-11 px-4 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-400 dark:hover:bg-amber-300 dark:text-zinc-950 font-bold text-sm rounded-xl transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                            <span>{{ __('Sign In to Console') }}</span>
                            <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                        </button>
                    </div>

                    <!-- Register Link -->
                    @if (Route::has('register'))
                        <div class="pt-4 border-t border-zinc-200/80 dark:border-zinc-800/80 text-center text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __("Don't have an account?") }}
                            <a href="{{ route('register') }}" class="font-bold text-zinc-900 dark:text-zinc-200 hover:text-amber-600 dark:hover:text-amber-400 underline underline-offset-4 transition-colors ml-1">
                                {{ __('Register now') }}
                            </a>
                        </div>
                    @endif
                </form>

                <!-- Security Encrypted Footnote -->
                <div class="mt-4 pt-3 flex items-center justify-center gap-2 text-2xs text-zinc-400 dark:text-zinc-500 border-t border-zinc-100 dark:border-zinc-900">
                    <i class="fas fa-shield-alt text-amber-500/80 text-xs"></i>
                    <span>Encrypted 256-bit SSL Session</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Visibility Toggle Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('passwordToggleIcon');
            if (passwordInput && icon) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }
    </script>
</x-guest-layout>
