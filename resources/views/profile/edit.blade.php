<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-3xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1 space-x-2 uppercase tracking-wider">
                    <a href="/dashboard" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Console</a>
                    <span>/</span>
                    <span class="text-amber-600 dark:text-amber-400">Settings</span>
                    <span>/</span>
                    <span class="text-zinc-700 dark:text-zinc-300">Operator Profile</span>
                </nav>
                <div class="flex items-center gap-2.5">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <h2 class="font-extrabold text-2xl text-zinc-900 dark:text-white tracking-tight">
                        {{ __('Account & Security Controls') }}
                    </h2>
                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-500/30 uppercase tracking-wide">
                        Verified Operator
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                    <span>Operator: <strong class="text-zinc-800 dark:text-zinc-200 font-medium">{{ Auth::user()->name }}</strong></span>
                    <span class="text-zinc-300 dark:text-zinc-700">•</span>
                    <span>{{ Auth::user()->email }}</span>
                    <span class="text-zinc-300 dark:text-zinc-700">•</span>
                    <span class="font-mono text-3xs bg-zinc-100 dark:bg-zinc-800/80 px-2 py-0.5 rounded text-zinc-600 dark:text-zinc-400">
                        Active Session
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="/dashboard" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1.5">
                    <i class="fas fa-arrow-left text-3xs"></i>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- LEFT COLUMN: Operator Identity Card & Quick Navigation -->
                <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-20">
                    <div class="relative overflow-hidden bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80">
                        <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-amber-500/5 dark:bg-amber-400/5 blur-2xl pointer-events-none"></div>

                        <!-- Monogram & Badge -->
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 text-zinc-950 font-black text-2xl flex items-center justify-center shadow-lg shadow-amber-500/20 border-2 border-white dark:border-zinc-800 shrink-0">
                                {{ strtoupper(substr(Auth::user()->name ?? 'O', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-base text-zinc-900 dark:text-white truncate">
                                    {{ Auth::user()->name }}
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                    {{ Auth::user()->email }}
                                </p>
                                <div class="mt-1.5 flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-4xs font-bold bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/60 dark:border-amber-500/30 uppercase tracking-wider">
                                        Primary Admin
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Telemetry Metadata List -->
                        <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-800/80 space-y-2.5 text-xs">
                            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-shield-halved text-emerald-500 text-3xs"></i>
                                    <span>Auth State</span>
                                </span>
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Protected</span>
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-calendar-check text-amber-500 text-3xs"></i>
                                    <span>Registered</span>
                                </span>
                                <span class="font-mono text-3xs font-semibold text-zinc-700 dark:text-zinc-300">
                                    {{ Auth::user()->created_at ? Auth::user()->created_at->format('M d, Y') : 'Active' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-envelope-circle-check text-sky-500 text-3xs"></i>
                                    <span>Email Verification</span>
                                </span>
                                <span class="font-semibold text-zinc-700 dark:text-zinc-300">
                                    @if(Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! Auth::user()->hasVerifiedEmail())
                                        <span class="text-amber-500 font-semibold">Pending</span>
                                    @else
                                        <span class="text-emerald-500 font-semibold">Verified</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Quick Navigation Jumps -->
                        <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-800/80 space-y-1">
                            <span class="block text-4xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2">
                                Jump to Section
                            </span>
                            <a href="#profile-info" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-white transition-all">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-id-card-clip text-amber-500 text-3xs"></i>
                                    <span>Identity & Contact</span>
                                </span>
                                <i class="fas fa-chevron-right text-4xs opacity-50"></i>
                            </a>
                            <a href="#security-creds" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-white transition-all">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-key text-amber-500 text-3xs"></i>
                                    <span>Password & Security</span>
                                </span>
                                <i class="fas fa-chevron-right text-4xs opacity-50"></i>
                            </a>
                            <a href="#danger-zone" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50/60 dark:hover:bg-rose-950/30 transition-all">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-triangle-exclamation text-rose-500 text-3xs"></i>
                                    <span>Danger Zone</span>
                                </span>
                                <i class="fas fa-chevron-right text-4xs opacity-50"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Settings Forms -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Card 1: Profile Information -->
                    <div id="profile-info" class="bg-white dark:bg-[#111114] rounded-2xl p-6 sm:p-7 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <!-- Card 2: Security & Password -->
                    <div id="security-creds" class="bg-white dark:bg-[#111114] rounded-2xl p-6 sm:p-7 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80">
                        @include('profile.partials.update-password-form')
                    </div>

                    <!-- Card 3: Danger Zone -->
                    <div id="danger-zone" class="bg-white dark:bg-[#111114] rounded-2xl p-6 sm:p-7 shadow-xs border border-rose-200/70 dark:border-rose-950/60">
                        @include('profile.partials.delete-user-form')
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
