<nav id="horizontalNav" x-data="{ open: false }" class="sticky top-0 z-50 backdrop-blur-md bg-white/90 dark:bg-zinc-900/90 dark:backdrop-blur-xl border-b border-zinc-200/80 dark:border-zinc-800/80 transition-all">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14">
            <!-- Left: Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center group">
                    <x-application-logo class="h-8 w-auto transition-transform group-hover:scale-105" />
                </a>
            </div>

            <!-- Center: Navigation Links (Sleek SaaS Segmented Capsule) -->
            <div class="hidden md:flex flex-1 justify-center items-center px-4">
                <div class="flex items-center gap-0.5 bg-zinc-100/80 dark:bg-zinc-800/60 p-1 rounded-full border border-zinc-200/70 dark:border-zinc-700/60 shadow-2xs backdrop-blur-sm">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('mail-accounts')" :active="request()->routeIs('mail-accounts')">
                        {{ __('Mail Accounts') }}
                    </x-nav-link>
                    <x-nav-link :href="route('tags')" :active="request()->routeIs('tags')">
                        {{ __('Tags') }}
                    </x-nav-link>
                    <x-nav-link :href="route('contacts')" :active="request()->routeIs('contacts')">
                        {{ __('Contacts') }}
                    </x-nav-link>
                    <x-nav-link :href="route('campaigns')" :active="request()->routeIs('campaigns')">
                        {{ __('Campaigns') }}
                    </x-nav-link>
                    <x-nav-link :href="route('newsletters')" :active="request()->routeIs('newsletters')">
                        {{ __('Newsletters') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dispatch')" :active="request()->routeIs('dispatch')">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                            <span>{{ __('Dispatch Studio') }}</span>
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('emails')" :active="request()->routeIs('emails')">
                        {{ __('Emails') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Area: Theme Toggle + Layout Switcher + Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center space-x-2 shrink-0">
                
                <!-- Layout Switcher Button (Switch to Vertical Sidebar Nav) -->
                <button type="button" id="desktop-layout-toggle" onclick="window.toggleNavLayout()" title="Switch to Sidebar Navigation Layout" aria-label="Switch to Sidebar Navigation Layout"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-zinc-200/80 dark:border-zinc-800 bg-white/90 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all shadow-2xs focus:outline-none">
                    <i class="fas fa-bars-staggered text-3xs text-amber-500 dark:text-amber-400"></i>
                </button>

                <!-- Minimal Theme Toggle Button with Crisp SVG Icons -->
                <button type="button" id="desktop-theme-toggle" onclick="window.toggleTheme(event)" title="Toggle Dark / Light Mode" aria-label="Toggle Dark / Light Mode"
                        class="theme-toggle-btn inline-flex items-center justify-center w-8 h-8 rounded-full border border-zinc-200/80 dark:border-zinc-800 bg-white/90 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all shadow-2xs focus:outline-none">
                    <!-- Light Mode (Moon Icon to switch to Dark) -->
                    <span class="dark:hidden inline-flex items-center justify-center">
                        <svg class="w-4 h-4 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </span>
                    <!-- Dark Mode (Sun Icon to switch to Light) -->
                    <span class="hidden dark:inline-flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                </button>

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-1.5 border border-zinc-200/80 dark:border-zinc-800 text-xs font-semibold rounded-full text-zinc-700 dark:text-zinc-200 bg-white/90 dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 focus:outline-none backdrop-blur-sm transition-all gap-2 shadow-2xs">
                            <div class="w-5 h-5 rounded-full bg-zinc-900 dark:bg-amber-100 text-white dark:text-zinc-950 flex items-center justify-center text-3xs font-extrabold">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <span>{{ Auth::user()->name ?? 'User' }}</span>

                            <div class="ms-0.5">
                                <svg class="fill-current h-3.5 w-3.5 text-zinc-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile: Theme Toggle + Layout Switcher + Hamburger -->
            <div class="-me-2 flex items-center space-x-1.5 sm:hidden">
                <!-- Mobile Layout Switcher -->
                <button type="button" onclick="window.toggleNavLayout()" title="Switch to Sidebar Navigation Layout" aria-label="Switch to Sidebar Navigation Layout"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all shadow-2xs focus:outline-none">
                    <i class="fas fa-bars-staggered text-3xs text-amber-500 dark:text-amber-400"></i>
                </button>

                <button type="button" onclick="window.toggleTheme(event)" title="Toggle Dark / Light Mode" aria-label="Toggle Dark / Light Mode"
                        class="theme-toggle-btn inline-flex items-center justify-center w-8 h-8 rounded-full border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all shadow-2xs focus:outline-none">
                    <span class="dark:hidden inline-flex items-center justify-center">
                        <svg class="w-4 h-4 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </span>
                    <span class="hidden dark:inline-flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                </button>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-full text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 dark:bg-[#09090B]/95 backdrop-blur-md border-b border-zinc-200/60 dark:border-zinc-800">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('mail-accounts')" :active="request()->routeIs('mail-accounts')">
                {{ __('Mail Accounts') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tags')" :active="request()->routeIs('tags')">
                {{ __('Tags') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contacts')" :active="request()->routeIs('contacts')">
                {{ __('Contacts') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('campaigns')" :active="request()->routeIs('campaigns')">
                {{ __('Campaigns') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('newsletters')" :active="request()->routeIs('newsletters')">
                {{ __('Newsletters') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dispatch')" :active="request()->routeIs('dispatch')">
                {{ __('Dispatch Studio') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('emails')" :active="request()->routeIs('emails')">
                {{ __('Emails') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-3 border-t border-zinc-200/60 dark:border-zinc-800 px-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-zinc-900 dark:bg-amber-100 text-white dark:text-zinc-950 flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="font-bold text-sm text-zinc-900 dark:text-white">{{ Auth::user()->name ?? 'User' }}</div>
                    <div class="font-medium text-xs text-zinc-500 dark:text-zinc-400">{{ Auth::user()->email ?? '' }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
