<section>
    <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm border border-amber-500/20 shadow-2xs">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    {{ __('Security & Password') }}
                </h3>
                <p class="text-3xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Ensure your account is protected with a long, robust passkey.') }}
                </p>
            </div>
        </div>
        <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-3xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700/80 uppercase tracking-wider">
            Authentication
        </span>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                {{ __('Current Password') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500 text-xs">
                    <i class="fas fa-lock"></i>
                </div>
                <input id="update_password_current_password" name="current_password" type="password" 
                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-zinc-200/90 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/60 text-zinc-900 dark:text-zinc-100 text-xs font-semibold focus:bg-white dark:focus:bg-zinc-900 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all shadow-2xs placeholder-zinc-400" 
                       autocomplete="current-password" placeholder="••••••••••••" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="update_password_password" class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                    {{ __('New Password') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500 text-xs">
                        <i class="fas fa-key"></i>
                    </div>
                    <input id="update_password_password" name="password" type="password" 
                           class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-zinc-200/90 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/60 text-zinc-900 dark:text-zinc-100 text-xs font-semibold focus:bg-white dark:focus:bg-zinc-900 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all shadow-2xs placeholder-zinc-400" 
                           autocomplete="new-password" placeholder="Min. 8 characters" />
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                    {{ __('Confirm Password') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500 text-xs">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                           class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-zinc-200/90 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/60 text-zinc-900 dark:text-zinc-100 text-xs font-semibold focus:bg-white dark:focus:bg-zinc-900 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all shadow-2xs placeholder-zinc-400" 
                           autocomplete="new-password" placeholder="Repeat new password" />
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="group relative inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 gap-2">
                <i class="fas fa-lock text-2xs"></i>
                <span>{{ __('Update Credentials') }}</span>
            </button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition
                     x-init="setTimeout(() => show = false, 2500)"
                     class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-3xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/80">
                    <i class="fas fa-circle-check text-emerald-500"></i>
                    <span>{{ __('Password updated') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
