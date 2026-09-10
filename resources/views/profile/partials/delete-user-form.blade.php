<section class="space-y-4">
    <div class="border-b border-rose-100 dark:border-rose-950/60 pb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm border border-rose-500/20 shadow-2xs">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    {{ __('Danger Zone') }}
                </h3>
                <p class="text-3xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Irreversible account termination and resource purge.') }}
                </p>
            </div>
        </div>
        <span class="px-2.5 py-0.5 rounded-full text-3xs font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border border-rose-200/80 dark:border-rose-900/60 uppercase tracking-wider">
            Critical
        </span>
    </div>

    <div class="p-4 rounded-xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200/70 dark:border-rose-900/40 text-xs text-zinc-600 dark:text-zinc-400">
        <p class="leading-relaxed">
            {{ __('Once your account is deleted, all campaigns, contacts, email dispatch logs, and SMTP gateway credentials will be permanently erased. Please export your critical broadcast records before proceeding.') }}
        </p>
        <div class="mt-4">
            <button
                type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="inline-flex items-center justify-center px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl transition-all shadow-xs shadow-rose-500/20 gap-2"
            >
                <i class="fas fa-trash-can text-2xs"></i>
                <span>{{ __('Delete Account') }}</span>
            </button>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-7 space-y-4">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800/80 pb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-base border border-rose-500/20 shrink-0">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-zinc-900 dark:text-white">
                        {{ __('Confirm Account Deletion') }}
                    </h2>
                    <p class="text-3xs text-zinc-500 dark:text-zinc-400">
                        {{ __('This action cannot be undone.') }}
                    </p>
                </div>
            </div>

            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                {{ __('Are you completely certain? Enter your account password below to authorize permanent deletion of your profile and data.') }}
            </p>

            <div>
                <label for="password" class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                    {{ __('Authorize Password') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500 text-xs">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-zinc-200/90 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/60 text-zinc-900 dark:text-zinc-100 text-xs font-semibold focus:bg-white dark:focus:bg-zinc-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 transition-all shadow-2xs placeholder-zinc-400"
                        placeholder="{{ __('Enter your current password') }}"
                    />
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-zinc-100 dark:border-zinc-800/80">
                <button type="button" x-on:click="$dispatch('close')" 
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all shadow-2xs">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl transition-all shadow-xs shadow-rose-500/20 gap-2">
                    <i class="fas fa-trash-can text-2xs"></i>
                    <span>{{ __('Permanently Delete') }}</span>
                </button>
            </div>
        </form>
    </x-modal>
</section>
