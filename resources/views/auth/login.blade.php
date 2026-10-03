<x-guest-layout :wide="false">
    <!-- Card Header -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 mb-3 shadow-2xs">
            <i class="fas fa-lock text-lg"></i>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ __('Sign In') }}
        </h1>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
            {{ __('Enter your credentials to access your dispatch console') }}
        </p>
    </div>

    <!-- Session Status / Alert -->
    @if (session('status'))
        <div class="mb-4 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs font-medium text-amber-800 dark:text-amber-200 flex items-center gap-2.5">
            <i class="fas fa-info-circle text-amber-500 shrink-0"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Validation Errors Alert -->
    @if ($errors->any())
        <div class="mb-4 p-3.5 rounded-xl bg-red-500/10 border border-red-500/20 text-xs text-red-600 dark:text-red-400 space-y-1">
            <div class="flex items-center gap-2 font-semibold">
                <i class="fas fa-exclamation-circle text-red-500 shrink-0"></i>
                <span>{{ __('Authentication failed') }}</span>
            </div>
            <ul class="list-disc list-inside text-2xs pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
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
                       placeholder="name@company.com"
                       class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 dark:bg-zinc-900/90 border border-zinc-300 dark:border-zinc-800 rounded-xl text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-amber-500 dark:focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 transition-all shadow-2xs">
            </div>
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
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
                       class="w-full pl-10 pr-11 py-2.5 bg-zinc-50 dark:bg-zinc-900/90 border border-zinc-300 dark:border-zinc-800 rounded-xl text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-amber-500 dark:focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 transition-all shadow-2xs">
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
                    class="group relative w-full h-11 px-4 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-400 dark:hover:bg-amber-300 dark:text-zinc-950 font-bold text-sm rounded-xl transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                <span>{{ __('Sign In') }}</span>
                <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
            </button>
        </div>

        <!-- Register Link -->
        @if (Route::has('register'))
            <div class="pt-4 border-t border-zinc-200/80 dark:border-zinc-800/80 text-center text-xs text-zinc-500 dark:text-zinc-400">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" class="font-semibold text-zinc-900 dark:text-zinc-200 hover:text-amber-600 dark:hover:text-amber-400 underline underline-offset-4 transition-colors ml-1">
                    {{ __('Register now') }}
                </a>
            </div>
        @endif
    </form>

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
                    icon.setAttribute('aria-label', 'Hide password');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    icon.setAttribute('aria-label', 'Show password');
                }
            }
        }
    </script>
</x-guest-layout>
