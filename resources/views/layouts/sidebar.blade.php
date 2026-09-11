<!-- Professional SaaS Vertical Sidebar Navigation & Top Bar Wrapper -->
<div id="verticalNavContainer" x-data="{ mobileSidebarOpen: false }" class="hidden">
    
    <!-- Mobile Off-Canvas Backdrop Overlay -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 bg-zinc-950/60 backdrop-blur-xs z-50 lg:hidden"
         style="display: none;"></div>

    <!-- Main Sidebar Panel (Fixed Desktop + Off-Canvas Mobile) -->
    <aside id="verticalSidebar" 
           :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-[#09090B] border-r border-zinc-200/80 dark:border-zinc-800/80 flex flex-col justify-between transition-all duration-250 ease-in-out shadow-lg lg:shadow-none">
        
        <!-- Top Section: Brand Header & Navigation List -->
        <div class="flex-1 flex flex-col min-h-0 overflow-y-auto overflow-x-hidden">
            
            <!-- Brand Logo & Minimize Toggle Header -->
            <div class="sidebar-header h-14 px-3 flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center group overflow-hidden" title="Campaign Stack">
                    <!-- Full Logo (Shown when expanded) -->
                    <div class="sidebar-full-logo">
                        <x-application-logo class="h-7 w-auto transition-transform group-hover:scale-102" />
                    </div>
                    <!-- Icon Only Logo (Shown when minimized) -->
                    <div class="sidebar-min-logo hidden mx-auto">
                        <img src="/i/campaignstack-icon.png" alt="Campaign Stack" class="h-7 w-7 object-contain transition-transform group-hover:scale-110" />
                    </div>
                </a>

                <!-- Desktop Minimize / Collapse Button with Proper UX Directional Chevrons -->
                <button type="button" 
                        onclick="window.toggleSidebarMinimize()" 
                        title="Collapse sidebar" 
                        aria-label="Toggle Sidebar Collapse"
                        class="sidebar-toggle-btn hidden lg:inline-flex items-center justify-center w-7 h-7 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800/70 transition-colors">
                    <!-- Point LEFT when expanded (meaning: collapse) -->
                    <i class="fas fa-angles-left text-3xs sidebar-collapse-icon"></i>
                    <!-- Point RIGHT when collapsed (meaning: expand) -->
                    <i class="fas fa-angles-right text-3xs sidebar-expand-icon hidden"></i>
                </button>

                <!-- Mobile Drawer Close Button -->
                <button type="button" @click="mobileSidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Fast Action Launcher: New Broadcast -->
            <div class="p-3">
                <a href="/newsletter-form/new" 
                   title="New Broadcast"
                   class="sidebar-broadcast-btn group w-full inline-flex items-center justify-center px-3.5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-xs shadow-amber-500/15 hover:shadow-md hover:shadow-amber-500/25 transition-all gap-2">
                    <i class="fas fa-paper-plane text-2xs transition-transform group-hover:-rotate-12"></i>
                    <span class="sidebar-broadcast-text truncate">New Broadcast</span>
                </a>
            </div>

            <!-- Navigation Links Structured by Categorized Workflows -->
            <nav class="flex-1 px-2.5 py-1 space-y-4 text-xs font-medium">
                
                <!-- Category 1: Overview -->
                <div class="space-y-1">
                    <span class="sidebar-category-title px-3 text-3xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                        Overview
                    </span>
                    
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       title="Dashboard"
                       class="sidebar-link group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800/90 dark:text-white border border-zinc-200/80 dark:border-zinc-700/60 font-semibold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fas fa-gauge-high w-4 text-center shrink-0 {{ request()->routeIs('dashboard') ? 'text-amber-500 dark:text-amber-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300' }}"></i>
                            <span class="sidebar-label truncate">{{ __('Dashboard') }}</span>
                        </div>
                        @if(request()->routeIs('dashboard'))
                            <span class="sidebar-badge w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-amber-400"></span>
                        @endif
                    </a>

                    <!-- Dispatch Studio -->
                    <a href="{{ route('dispatch') }}" 
                       title="Dispatch Studio"
                       class="sidebar-link group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('dispatch') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800/90 dark:text-white border border-zinc-200/80 dark:border-zinc-700/60 font-semibold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fas fa-bolt w-4 text-center shrink-0 {{ request()->routeIs('dispatch') ? 'text-amber-500 dark:text-amber-400' : 'text-amber-500/80 dark:text-amber-400/80' }}"></i>
                            <span class="sidebar-label truncate">{{ __('Dispatch Studio') }}</span>
                        </div>
                        <span class="sidebar-badge inline-flex items-center px-1.5 py-0.5 rounded-full text-3xs font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                            Live
                        </span>
                    </a>
                </div>

                <!-- Category 2: Audience & Infrastructure -->
                <div class="space-y-1">
                    <span class="sidebar-category-title px-3 text-3xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                        Audience & Relays
                    </span>

                    <!-- Contacts -->
                    <a href="{{ route('contacts') }}" 
                       title="Contacts"
                       class="sidebar-link group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('contacts') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800/90 dark:text-white border border-zinc-200/80 dark:border-zinc-700/60 font-semibold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fas fa-users w-4 text-center shrink-0 {{ request()->routeIs('contacts') ? 'text-amber-500 dark:text-amber-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300' }}"></i>
                            <span class="sidebar-label truncate">{{ __('Contacts') }}</span>
                        </div>
                    </a>

                    <!-- Tags -->
                    <a href="{{ route('tags') }}" 
                       title="Tags & Segments"
                       class="sidebar-link group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('tags') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800/90 dark:text-white border border-zinc-200/80 dark:border-zinc-700/60 font-semibold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fas fa-tags w-4 text-center shrink-0 {{ request()->routeIs('tags') ? 'text-amber-500 dark:text-amber-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300' }}"></i>
                            <span class="sidebar-label truncate">{{ __('Tags & Segments') }}</span>
                        </div>
                    </a>

                    <!-- Mail Accounts -->
                    <a href="{{ route('mail-accounts') }}" 
                       title="SMTP Gateways"
                       class="sidebar-link group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('mail-accounts') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800/90 dark:text-white border border-zinc-200/80 dark:border-zinc-700/60 font-semibold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fas fa-server w-4 text-center shrink-0 {{ request()->routeIs('mail-accounts') ? 'text-amber-500 dark:text-amber-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300' }}"></i>
                            <span class="sidebar-label truncate">{{ __('SMTP Gateways') }}</span>
                        </div>
                    </a>
                </div>

                <!-- Category 3: Broadcast Engine -->
                <div class="space-y-1">
                    <span class="sidebar-category-title px-3 text-3xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                        Broadcast Pipeline
                    </span>

                    <!-- Campaigns -->
                    <a href="{{ route('campaigns') }}" 
                       title="Campaigns"
                       class="sidebar-link group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('campaigns') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800/90 dark:text-white border border-zinc-200/80 dark:border-zinc-700/60 font-semibold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fas fa-bullhorn w-4 text-center shrink-0 {{ request()->routeIs('campaigns') ? 'text-amber-500 dark:text-amber-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300' }}"></i>
                            <span class="sidebar-label truncate">{{ __('Campaigns') }}</span>
                        </div>
                    </a>

                    <!-- Newsletters -->
                    <a href="{{ route('newsletters') }}" 
                       title="Newsletters"
                       class="sidebar-link group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('newsletters') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800/90 dark:text-white border border-zinc-200/80 dark:border-zinc-700/60 font-semibold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fas fa-paper-plane w-4 text-center shrink-0 {{ request()->routeIs('newsletters') ? 'text-amber-500 dark:text-amber-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300' }}"></i>
                            <span class="sidebar-label truncate">{{ __('Newsletters') }}</span>
                        </div>
                    </a>

                    <!-- Emails / Activity Log -->
                    <a href="{{ route('emails') }}" 
                       title="Delivery Logs"
                       class="sidebar-link group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('emails') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800/90 dark:text-white border border-zinc-200/80 dark:border-zinc-700/60 font-semibold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fas fa-envelope-open-text w-4 text-center shrink-0 {{ request()->routeIs('emails') ? 'text-amber-500 dark:text-amber-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300' }}"></i>
                            <span class="sidebar-label truncate">{{ __('Delivery Logs') }}</span>
                        </div>
                    </a>

                    <!-- Account Settings -->
                    <a href="{{ route('profile.edit') }}" 
                       title="Settings & Profile"
                       class="sidebar-link group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('profile.edit') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800/90 dark:text-white border border-zinc-200/80 dark:border-zinc-700/60 font-semibold shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fas fa-gear w-4 text-center shrink-0 {{ request()->routeIs('profile.edit') ? 'text-amber-500 dark:text-amber-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300' }}"></i>
                            <span class="sidebar-label truncate">{{ __('Account Settings') }}</span>
                        </div>
                    </a>
                </div>

            </nav>

            <!-- Dynamic SaaS System Health & Telemetry Pill Deck (Fills empty gap with cute minimal widgets) -->
            <div class="sidebar-status-card px-3 pt-2 pb-1 mt-auto space-y-2">
                
                <!-- Dynamic Status Pill -->
                @if(($sidebarFailedCount ?? 0) > 0)
                    <a href="/emails" 
                       title="Inspect {{ $sidebarFailedCount }} delivery errors"
                       class="group flex items-center justify-between px-3 py-1.5 rounded-xl bg-rose-50/80 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 border border-rose-200/70 dark:border-rose-800/60 transition-all text-3xs">
                        <div class="flex items-center gap-2 text-rose-700 dark:text-rose-300 min-w-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                            <span class="font-bold truncate">{{ $sidebarFailedCount }} Delivery Errors</span>
                        </div>
                        <span class="text-3xs font-semibold text-rose-600 dark:text-rose-400 group-hover:translate-x-0.5 transition-transform">Fix →</span>
                    </a>
                @elseif(($sidebarQueuedCount ?? 0) > 0)
                    <a href="/dispatch" 
                       title="{{ $sidebarQueuedCount }} emails currently in dispatch queue"
                       class="group flex items-center justify-between px-3 py-1.5 rounded-xl bg-amber-50/80 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/60 border border-amber-200/70 dark:border-amber-800/60 transition-all text-3xs">
                        <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 min-w-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <span class="font-bold truncate">{{ $sidebarQueuedCount }} Queued Outbound</span>
                        </div>
                        <span class="text-3xs font-semibold text-amber-700 dark:text-amber-400 group-hover:translate-x-0.5 transition-transform">Run →</span>
                    </a>
                @elseif(($sidebarActiveRelays ?? 0) === 0)
                    <a href="/mail-accounts" 
                       title="No active SMTP relays configured"
                       class="group flex items-center justify-between px-3 py-1.5 rounded-xl bg-amber-50/80 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/60 border border-amber-200/70 dark:border-amber-800/60 transition-all text-3xs">
                        <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 min-w-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            <span class="font-bold truncate">Gateways Inactive</span>
                        </div>
                        <span class="text-3xs font-semibold text-amber-700 dark:text-amber-400 group-hover:translate-x-0.5 transition-transform">+ Add</span>
                    </a>
                @else
                    <a href="/dispatch" 
                       title="All relays active and operational"
                       class="group flex items-center justify-between px-3 py-1.5 rounded-full bg-zinc-100/70 hover:bg-zinc-100 dark:bg-zinc-900/60 dark:hover:bg-zinc-800/80 border border-zinc-200/60 dark:border-zinc-800/60 transition-all text-3xs">
                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400 min-w-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span class="font-medium truncate">All systems humming</span>
                        </div>
                        <span class="text-3xs text-amber-500/80 group-hover:scale-110 transition-transform">✨</span>
                    </a>
                @endif

                <!-- Cute & Minimal Gateway Health Chip (Fills lower void naturally) -->
                <div class="p-2.5 rounded-xl bg-zinc-50/60 dark:bg-zinc-900/40 border border-zinc-200/50 dark:border-zinc-800/60 space-y-1.5">
                    <div class="flex items-center justify-between text-3xs text-zinc-500 dark:text-zinc-400">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-satellite-dish text-amber-500 text-3xs"></i>
                            <span class="font-medium">Active Gateways</span>
                        </span>
                        <span class="font-mono font-bold text-zinc-700 dark:text-zinc-300">{{ $sidebarActiveRelays ?? 0 }} / {{ $sidebarTotalRelays ?? 0 }}</span>
                    </div>
                    
                    <!-- Clean 1.5px Progress Pulse Bar -->
                    @php
                        $totalR = $sidebarTotalRelays ?? 0;
                        $activeR = $sidebarActiveRelays ?? 0;
                        $relayPct = $totalR > 0 ? min(round(($activeR / $totalR) * 100), 100) : 0;
                    @endphp
                    <div class="w-full bg-zinc-200/60 dark:bg-zinc-800 rounded-full h-1 overflow-hidden">
                        <div class="h-1 rounded-full {{ $relayPct > 0 ? 'bg-emerald-400' : 'bg-amber-400' }} transition-all duration-300" style="width: {{ $relayPct }}%"></div>
                    </div>

                    <div class="flex items-center justify-between text-3xs pt-0.5">
                        <span class="text-zinc-400 dark:text-zinc-500 font-medium tracking-tight">Multi-relay sync</span>
                        <a href="/mail-accounts" class="group/manage inline-flex items-center gap-1 font-semibold text-zinc-500 hover:text-amber-600 dark:text-zinc-400 dark:hover:text-amber-400 transition-colors">
                            <span>Manage</span>
                            <i class="fas fa-arrow-right text-[8px] opacity-70 group-hover/manage:opacity-100 group-hover/manage:translate-x-0.5 transition-all"></i>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Minimized Mode Dynamic Status Dot -->
            <div class="sidebar-min-status hidden py-2 text-center">
                @if(($sidebarFailedCount ?? 0) > 0)
                    <a href="/emails" title="{{ $sidebarFailedCount }} delivery errors! Click to inspect." class="inline-flex items-center justify-center p-1 rounded-full hover:bg-rose-100 dark:hover:bg-rose-950 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                    </a>
                @elseif(($sidebarQueuedCount ?? 0) > 0)
                    <a href="/dispatch" title="{{ $sidebarQueuedCount }} queued outbound. Click to run." class="inline-flex items-center justify-center p-1 rounded-full hover:bg-amber-100 dark:hover:bg-amber-950 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    </a>
                @else
                    <a href="/dispatch" title="All systems humming ✨ ({{ $sidebarActiveRelays ?? 0 }} gateways active)" class="inline-flex items-center justify-center p-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    </a>
                @endif
            </div>

        </div>

        <!-- Bottom User Dock & Layout Controls (Stacked cleanly when minimized) -->
        <div class="p-2 border-t border-zinc-100 dark:border-zinc-800/80 shrink-0 space-y-1.5 bg-zinc-50/50 dark:bg-zinc-950/40">
            
            <!-- Controls Bar: Layout Switcher + Theme Toggle -->
            <div class="sidebar-prefs-bar flex items-center justify-between px-1">
                <span class="sidebar-prefs-title text-3xs text-zinc-400 dark:text-zinc-500 font-semibold uppercase tracking-wider">Prefs</span>
                
                <div class="sidebar-prefs-buttons flex items-center space-x-1.5">
                    <!-- Layout Switcher Button (Switch to Horizontal Topbar) -->
                    <button type="button" 
                            onclick="window.toggleNavLayout()" 
                            title="Switch to Topbar Nav Layout" 
                            aria-label="Switch to Topbar Nav Layout"
                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all shadow-2xs">
                        <i class="fas fa-table-columns text-3xs text-amber-500 dark:text-amber-400"></i>
                    </button>

                    <!-- Theme Toggle -->
                    <button type="button" 
                            onclick="window.toggleTheme(event)" 
                            title="Toggle Dark / Light Mode" 
                            aria-label="Toggle Dark / Light Mode"
                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all shadow-2xs">
                        <span class="dark:hidden inline-flex items-center justify-center">
                            <i class="fas fa-moon text-3xs text-zinc-700"></i>
                        </span>
                        <span class="hidden dark:inline-flex items-center justify-center">
                            <i class="fas fa-sun text-3xs text-amber-400"></i>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Operator Profile Identity Pill -->
            <div class="sidebar-user-dock p-1.5 rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/70 dark:border-zinc-800/80 flex items-center justify-between shadow-2xs">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 min-w-0 flex-1 hover:opacity-85 transition-opacity" title="{{ Auth::user()->name ?? 'Operator' }}">
                    <div class="w-7 h-7 rounded-lg bg-zinc-900 dark:bg-zinc-800 text-zinc-100 dark:text-amber-400 border border-zinc-200/80 dark:border-zinc-700/60 flex items-center justify-center text-xs font-bold shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="sidebar-user-details min-w-0 flex-1">
                        <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">
                            {{ Auth::user()->name ?? 'Operator' }}
                        </div>
                        <div class="text-3xs text-zinc-400 dark:text-zinc-500 truncate">
                            {{ Auth::user()->email ?? '' }}
                        </div>
                    </div>
                </a>

                <!-- Log Out Action Button -->
                <form method="POST" action="{{ route('logout') }}" class="sidebar-user-details shrink-0">
                    @csrf
                    <button type="submit" title="Log Out" class="p-1 rounded-md text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                        <i class="fas fa-arrow-right-from-bracket text-2xs"></i>
                    </button>
                </form>
            </div>

        </div>

    </aside>

    <!-- Top Utility Bar for Vertical Mode (Mobile Trigger + Breadcrumbs + Fast Actions) -->
    <header class="fixed top-0 inset-x-0 z-40 h-14 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md border-b border-zinc-200/60 dark:border-zinc-800/80 flex items-center justify-between px-4 sm:px-6 transition-all">
        <div class="flex items-center gap-3">
            <!-- Mobile Drawer Hamburger -->
            <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden p-2 rounded-lg text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <i class="fas fa-bars text-sm"></i>
            </button>

            <!-- Breadcrumb Path Status -->
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                <span class="inline-flex items-center gap-1.5 text-zinc-800 dark:text-zinc-200 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Campaign Stack
                </span>
                <span class="text-zinc-300 dark:text-zinc-700">/</span>
                <span class="text-zinc-600 dark:text-zinc-300 font-medium capitalize">
                    {{ str_replace(['-', '_'], ' ', request()->route()->getName() ?? 'Dashboard') }}
                </span>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Minimize / Expand Toggle Button in Topbar with UX chevrons -->
            <button type="button" 
                    onclick="window.toggleSidebarMinimize()" 
                    title="Toggle Sidebar Collapse" 
                    class="hidden lg:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-xs font-medium transition-all shadow-2xs">
                <i class="fas fa-angles-left text-3xs text-amber-500 dark:text-amber-400 sidebar-collapse-icon"></i>
                <i class="fas fa-angles-right text-3xs text-amber-500 dark:text-amber-400 sidebar-expand-icon hidden"></i>
                <span class="text-3xs font-medium">Sidebar</span>
            </button>

            <!-- Layout Switcher Button (Switch to Horizontal Topbar) -->
            <button type="button" 
                    onclick="window.toggleNavLayout()" 
                    title="Switch to Horizontal Topbar Layout" 
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-xs font-medium transition-all shadow-2xs">
                <i class="fas fa-table-columns text-3xs text-amber-500 dark:text-amber-400"></i>
                <span class="hidden sm:inline text-3xs">Top Nav</span>
            </button>

            <!-- Quick Theme Toggle in Top Bar -->
            <button type="button" 
                    onclick="window.toggleTheme(event)" 
                    title="Toggle Dark / Light Mode" 
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all shadow-2xs">
                <span class="dark:hidden inline-flex items-center justify-center">
                    <i class="fas fa-moon text-2xs text-zinc-700"></i>
                </span>
                <span class="hidden dark:inline-flex items-center justify-center">
                    <i class="fas fa-sun text-2xs text-amber-400"></i>
                </span>
            </button>
        </div>
    </header>

</div>
