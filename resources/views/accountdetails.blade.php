@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <!-- Breadcrumbs -->
                <nav class="flex text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1 space-x-2">
                    <a href="/mail-accounts" class="hover:text-zinc-900 dark:hover:text-amber-200 transition-colors">Mail Accounts</a>
                    <span>/</span>
                    <span class="text-zinc-900 dark:text-white font-semibold">Account Details</span>
                </nav>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas fa-server text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ $account->name ?? 'Mail Account Details' }}
                    </h2>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="/mail-accounts" class="inline-flex items-center px-3.5 py-2 bg-slate-100 dark:bg-[#141417] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-800 transition-colors gap-2">
                    <i class="fas fa-arrow-left text-2xs"></i>
                    <span>Back to Accounts</span>
                </a>
                <a href="/account-form/{{ $account->id }}" class="inline-flex items-center px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-xs font-bold rounded-xl shadow-2xs transition-all gap-2">
                    <i class="fas fa-pencil-alt text-2xs"></i>
                    <span>Edit Account</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-8 pt-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @php
                $config = is_string($account->config) ? json_decode($account->config) : (object)[];
            @endphp

            <!-- CARD 1: Sender Identity & Account Overview -->
            <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800 pb-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50/80 dark:bg-amber-950/30 text-amber-800 dark:text-amber-300 border border-amber-200/60 dark:border-amber-500/20 flex items-center justify-center text-xl shrink-0">
                            <i class="fas {{ strtoupper($account->type) === 'API' ? 'fa-code' : 'fa-network-wired' }}"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-zinc-900 dark:text-white">{{ $account->name ?? 'Unnamed Account' }}</h3>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                                Last updated {{ $account->updated_at ? \Carbon\Carbon::parse($account->updated_at)->diffForHumans() : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- Status Badge -->
                        @if($account->status == 1)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                Inactive
                            </span>
                        @endif

                        <!-- Type Badge -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            {{ strtoupper($account->type) }}
                        </span>
                    </div>
                </div>

                @if(strtoupper($account->type) === 'SMTP')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-xs text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1">Default From Name</label>
                            <div class="p-3 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 text-xs text-zinc-900 dark:text-white font-medium">
                                {{ $config->from_username ?? 'Not specified' }}
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1">Default From Email Address</label>
                            <div class="p-3 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 text-xs text-zinc-900 dark:text-white font-medium">
                                {{ $config->from_address ?? 'Not specified' }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- CARD 2: Technical Configuration Settings -->
            <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 space-y-5">
                <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex items-center justify-between">
                    <h4 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-cogs text-amber-500/80 dark:text-amber-300/80"></i>
                        Server Credentials & Technical Specs
                    </h4>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                        {{ strtoupper($account->type) }}
                    </span>
                </div>

                @if(strtoupper($account->type) === 'SMTP')
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block font-bold text-xs text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1">Server Host / IP</label>
                            <div class="p-3 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 font-mono text-xs text-zinc-900 dark:text-white">
                                {{ $config->ip_address ?? 'Not specified' }}
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1">Port</label>
                            <div class="p-3 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 font-mono text-xs text-zinc-900 dark:text-white">
                                {{ $config->port ?? 'Not specified' }}
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1">Username</label>
                            <div class="p-3 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 text-xs text-zinc-900 dark:text-white">
                                {{ $config->username ?? 'Not specified' }}
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1">Encryption</label>
                            <div class="p-3 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 text-xs text-zinc-900 dark:text-white">
                                {{ $config->encryption ?? 'None' }}
                            </div>
                        </div>
                    </div>
                @elseif(strtoupper($account->type) === 'API' && is_array($config))
                    <div class="space-y-3">
                        @foreach($config as $item)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-3.5 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800">
                                <div>
                                    <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider block">Key / Label</span>
                                    <span class="text-xs font-mono text-zinc-900 dark:text-white">{{ $item->key ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider block">Value</span>
                                    <span class="text-xs font-mono text-zinc-900 dark:text-white">{{ $item->value ?? 'N/A' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-zinc-600 dark:text-zinc-400">No additional configuration parameters defined for this account.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
