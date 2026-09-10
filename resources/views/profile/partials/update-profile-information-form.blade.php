<section>
    <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm border border-amber-500/20 shadow-2xs">
                <i class="fas fa-id-card-clip"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    {{ __('Profile Information') }}
                </h3>
                <p class="text-3xs text-zinc-500 dark:text-zinc-400">
                    {{ __("Update your operator identity and primary contact email.") }}
                </p>
            </div>
        </div>
        <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-3xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700/80 uppercase tracking-wider">
            Identity
        </span>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                {{ __('Operator Name') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500 text-xs">
                    <i class="fas fa-user"></i>
                </div>
                <input id="name" name="name" type="text" 
                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-zinc-200/90 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/60 text-zinc-900 dark:text-zinc-100 text-xs font-semibold focus:bg-white dark:focus:bg-zinc-900 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all shadow-2xs placeholder-zinc-400" 
                       value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Operator Name" />
            </div>
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                {{ __('Account Email') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500 text-xs">
                    <i class="fas fa-envelope"></i>
                </div>
                <input id="email" name="email" type="email" 
                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-zinc-200/90 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/60 text-zinc-900 dark:text-zinc-100 text-xs font-semibold focus:bg-white dark:focus:bg-zinc-900 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all shadow-2xs placeholder-zinc-400" 
                       value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="name@company.com" />
            </div>
            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 text-xs text-amber-900 dark:text-amber-200 flex items-start gap-2.5">
                    <i class="fas fa-circle-exclamation text-amber-500 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-medium">{{ __('Your email address is unverified.') }}</p>
                        <button form="send-verification" class="mt-1 font-semibold text-amber-700 dark:text-amber-400 hover:underline inline-flex items-center gap-1">
                            <span>{{ __('Click here to re-send the verification email.') }}</span>
                            <i class="fas fa-arrow-right text-3xs"></i>
                        </button>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-1.5 font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ __('A new verification link has been sent to your email address.') }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="group relative inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 gap-2">
                <i class="fas fa-check text-2xs"></i>
                <span>{{ __('Save Changes') }}</span>
            </button>

            @if (session('status') === 'profile-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition
                     x-init="setTimeout(() => show = false, 2500)"
                     class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-3xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/80">
                    <i class="fas fa-circle-check text-emerald-500"></i>
                    <span>{{ __('Saved successfully') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
