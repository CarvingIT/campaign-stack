<nav x-data="{ open: false }" class="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-[#0B192C]/85 border-b border-gray-200/60 dark:border-[#1E3E62]/60 transition-all">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14">
            <div class="flex items-center space-x-6">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <x-application-logo class="block h-8 w-auto fill-current text-[#0B192C] dark:text-[#FFC700] transition-transform group-hover:scale-105" />
                    </a>
                </div>

                <!-- Navigation Links (Apple HIG Floating Capsules) -->
                <div class="hidden sm:flex items-center space-x-1 bg-gray-100/50 dark:bg-[#112238]/60 p-1 rounded-full border border-gray-200/40 dark:border-[#1E3E62]/40 backdrop-blur-sm">
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
                    <x-nav-link :href="route('emails')" :active="request()->routeIs('emails')">
                        {{ __('Emails') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-200/60 dark:border-[#1E3E62]/60 text-xs font-semibold rounded-full text-gray-700 dark:text-gray-200 bg-white/60 dark:bg-[#112238]/80 hover:bg-gray-100 dark:hover:bg-[#1E3E62] focus:outline-none backdrop-blur-sm transition-all gap-2 shadow-2xs">
                            <div class="w-5 h-5 rounded-full bg-[#FFC700] text-[#0B192C] flex items-center justify-center text-3xs font-extrabold">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <span>{{ Auth::user()->name }}</span>

                            <div class="ms-0.5">
                                <svg class="fill-current h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
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

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-full text-gray-500 dark:text-gray-300 hover:text-[#0B192C] dark:hover:text-[#FFC700] hover:bg-gray-100 dark:hover:bg-[#1E3E62] focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 dark:bg-[#0B192C]/95 backdrop-blur-md border-b border-gray-200 dark:border-[#1E3E62]">
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
        </div>

        <div class="pt-4 pb-3 border-t border-gray-200 dark:border-[#1E3E62] px-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-[#FFC700] text-[#0B192C] flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="font-bold text-sm text-[#0B192C] dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-xs text-gray-500 dark:text-gray-300">{{ Auth::user()->email }}</div>
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
