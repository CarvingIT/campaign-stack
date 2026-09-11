@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageSize = 10;
        let visibleCount = pageSize;
        let currentFilter = 'all';
        const allItems = Array.from(document.querySelectorAll('.account-item'));
        const searchInput = document.getElementById('accountSearchInput');
        const showMoreBtn = document.getElementById('showMoreBtn');
        const countDisplay = document.getElementById('accountCountDisplay');
        const emptySearchResults = document.getElementById('emptySearchResults');
        const filterPills = document.querySelectorAll('.filter-pill');

        function updateListVisibility() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let matchingItems = [];

            allItems.forEach(item => {
                const searchText = item.getAttribute('data-search') || '';
                const itemStatus = item.getAttribute('data-status') || '';
                const itemType = item.getAttribute('data-type') || '';
                
                let matchesFilter = true;
                if (currentFilter === 'active') {
                    matchesFilter = itemStatus === '1';
                } else if (currentFilter === 'inactive') {
                    matchesFilter = itemStatus === '0';
                } else if (currentFilter === 'smtp') {
                    matchesFilter = itemType === 'smtp';
                } else if (currentFilter === 'api') {
                    matchesFilter = itemType === 'api';
                }

                const matchesSearch = query === '' || searchText.includes(query);

                if (matchesFilter && matchesSearch) {
                    matchingItems.push(item);
                } else {
                    item.classList.add('hidden');
                }
            });

            if (query !== '' || currentFilter !== 'all') {
                matchingItems.forEach((item, idx) => {
                    if (idx < visibleCount) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
                if (countDisplay) countDisplay.textContent = `Showing ${Math.min(visibleCount, matchingItems.length)} of ${matchingItems.length} matching accounts`;
                if (emptySearchResults) {
                    emptySearchResults.classList.toggle('hidden', matchingItems.length > 0);
                }
            } else {
                if (emptySearchResults) emptySearchResults.classList.add('hidden');
                matchingItems.forEach((item, idx) => {
                    if (idx < visibleCount) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });

                const total = matchingItems.length;
                const currentlyShown = Math.min(visibleCount, total);

                if (countDisplay) {
                    countDisplay.textContent = `Showing ${currentlyShown} of ${total} mail accounts`;
                }
            }

            if (showMoreBtn) {
                if (matchingItems.length <= visibleCount) {
                    showMoreBtn.style.display = 'none';
                } else {
                    showMoreBtn.style.display = 'inline-flex';
                    const remaining = matchingItems.length - visibleCount;
                    document.getElementById('remainingCountText').textContent = `Show More (${remaining} remaining)`;
                }
            }
        }

        filterPills.forEach(pill => {
            pill.addEventListener('click', function() {
                filterPills.forEach(p => {
                    p.classList.remove('bg-zinc-900', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'shadow-xs');
                    p.classList.add('text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-800');
                });
                this.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-800');
                this.classList.add('bg-zinc-900', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'shadow-xs');
                currentFilter = this.getAttribute('data-filter');
                visibleCount = pageSize;
                updateListVisibility();
            });
        });

        if (showMoreBtn) {
            showMoreBtn.addEventListener('click', function() {
                visibleCount += pageSize;
                updateListVisibility();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                visibleCount = pageSize;
                updateListVisibility();
            });
        }

        updateListVisibility();
    });

    function confirmDelete(id, name) {
        document.getElementById('modalAccountId').value = id;
        document.getElementById('deleteAccountName').textContent = name;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <h2 class="font-extrabold text-2xl text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        <i class="fas fa-server text-amber-500"></i>
                        <span>{{ __('Outbound Mail Accounts & Relays') }}</span>
                    </h2>
                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/80 uppercase">
                        {{ $accounts->where('status', 1)->count() }} of {{ count($accounts) }} Active
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 pl-5">
                    Manage SMTP transmission gateways and API credentials rotated across broadcast dispatches.
                </p>
            </div>
            
            <div class="flex items-center gap-2.5">
                <a href="/dispatch" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#121215] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1.5">
                    <i class="fas fa-layer-group text-3xs text-amber-500"></i>
                    <span>Dispatch Studio</span>
                </a>
                <a href="/account-form/new" class="group inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 gap-2">
                    <i class="fas fa-plus text-3xs transition-transform group-hover:rotate-90"></i>
                    <span>Add Mail Account</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-10 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Notifications -->
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))
                    @php
                        $alertStyles = [
                            'danger' => 'bg-rose-50 text-rose-900 border-rose-200 dark:bg-rose-950/60 dark:text-rose-200 dark:border-rose-900 icon-fa-circle-exclamation',
                            'warning' => 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950/60 dark:text-amber-200 dark:border-amber-900 icon-fa-triangle-exclamation',
                            'success' => 'bg-emerald-50 text-emerald-900 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-200 dark:border-emerald-900 icon-fa-circle-check',
                            'info' => 'bg-blue-50 text-blue-900 border-blue-200 dark:bg-blue-950/60 dark:text-blue-200 dark:border-blue-900 icon-fa-circle-info',
                        ];
                    @endphp
                    <div class="p-3.5 rounded-xl border {{ $alertStyles[$msg] }} flex items-center gap-3 shadow-2xs backdrop-blur-sm" role="alert">
                        <i class="fas {{ explode(' ', $alertStyles[$msg])[count(explode(' ', $alertStyles[$msg]))-1] }} text-sm"></i>
                        <div class="text-xs font-semibold">
                            {{ Session::get('alert-' . $msg) }}
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Metrics Overview Cards -->
            @php
                $activeCount = $accounts->where('status', 1)->count();
                $inactiveCount = count($accounts) - $activeCount;
                $smtpCount = $accounts->filter(fn($a) => strtoupper($a->type) === 'SMTP')->count();
                $apiCount = $accounts->filter(fn($a) => strtoupper($a->type) === 'API')->count();
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Metric 1: Total Accounts -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-amber-500/40 dark:hover:border-amber-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Total Relays
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs border border-amber-200/50 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-server"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                            {{ count($accounts) }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Configured outbound gateways
                        </p>
                    </div>
                </div>

                <!-- Metric 2: Active Senders -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-emerald-500/40 dark:hover:border-emerald-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Active Senders
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs border border-emerald-200/50 dark:border-emerald-500/20 shadow-2xs">
                            <i class="fas fa-tower-broadcast"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight">
                            {{ $activeCount }} <span class="text-base font-normal text-zinc-400">/ {{ count($accounts) }}</span>
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Available in dispatch rotation
                        </p>
                    </div>
                </div>

                <!-- Metric 3: SMTP Servers -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-blue-500/40 dark:hover:border-blue-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            SMTP Relays
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs border border-blue-200/50 dark:border-blue-500/20 shadow-2xs">
                            <i class="fas fa-network-wired"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">
                            {{ $smtpCount }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Standard socket connections
                        </p>
                    </div>
                </div>

                <!-- Metric 4: API Providers -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-purple-500/40 dark:hover:border-purple-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            API Gateways
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs border border-purple-200/50 dark:border-purple-500/20 shadow-2xs">
                            <i class="fas fa-code"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 tracking-tight">
                            {{ $apiCount }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            REST / Direct API channels
                        </p>
                    </div>
                </div>
            </div>

            <!-- Explanatory Guidance Banner Card -->
            <div class="relative overflow-hidden rounded-2xl border border-amber-200/90 dark:border-amber-500/20 bg-gradient-to-r from-amber-500/[0.08] via-amber-400/[0.03] to-amber-500/[0.06] dark:from-[#111114] dark:via-zinc-900/80 dark:to-[#111114] p-5 sm:p-6 shadow-xs dark:shadow-2xs backdrop-blur-sm transition-all">
                <div class="absolute -right-6 -bottom-6 opacity-10 dark:opacity-5 text-9xl pointer-events-none transform -rotate-12 select-none">
                    <i class="fas fa-server text-amber-500 dark:text-amber-300"></i>
                </div>
                <div class="flex items-start gap-4 sm:gap-5 relative z-10">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-100/90 text-amber-800 border border-amber-200/90 dark:bg-amber-400/10 dark:text-amber-300 dark:border-amber-400/20 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="font-bold text-sm sm:text-base text-zinc-900 dark:text-white flex items-center gap-2">
                            Multi-Gateway Relay Architecture & Rotation
                            <span class="w-2 h-2 rounded-full bg-amber-500 dark:bg-amber-300"></span>
                        </h4>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            Campaign Stack balances outbound transmission volume across your active SMTP relays and API accounts. Automatic cooldown timers and failover protection prevent gateway rate-limiting, isolate IP reputations, and maintain optimal inbox deliverability rates.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Accounts Directory Card -->
            <div class="bg-white dark:bg-[#111114] rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs overflow-hidden">
                
                @if(count($accounts) > 0)
                    <!-- Top Toolbar & Filter Strip -->
                    <div class="p-4 sm:p-5 border-b border-zinc-100 dark:border-zinc-800/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-zinc-50/50 dark:bg-zinc-900/30">
                        
                        <!-- Search Bar -->
                        <div class="relative flex-1 max-w-md">
                            <i class="fas fa-search absolute left-3.5 top-3 text-zinc-400 text-xs"></i>
                            <input type="text" id="accountSearchInput" placeholder="Search accounts by name, host, or email..." 
                                   class="w-full pl-9 pr-4 py-2 bg-white dark:bg-[#09090B] border border-zinc-200/90 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs">
                        </div>

                        <!-- Segment Filter Pills & Counter -->
                        <div class="flex items-center justify-between md:justify-end gap-3 flex-wrap">
                            <div class="flex items-center bg-zinc-100 dark:bg-zinc-900 p-1 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80 text-xs">
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-semibold bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-xs transition-all" data-filter="all">
                                    All ({{ count($accounts) }})
                                </button>
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all" data-filter="active">
                                    Active ({{ $activeCount }})
                                </button>
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all" data-filter="inactive">
                                    Inactive ({{ $inactiveCount }})
                                </button>
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all" data-filter="smtp">
                                    SMTP ({{ $smtpCount }})
                                </button>
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all" data-filter="api">
                                    API ({{ $apiCount }})
                                </button>
                            </div>

                            <div class="text-3xs font-mono text-zinc-400 dark:text-zinc-500" id="accountCountDisplay">
                                Showing {{ min(10, count($accounts)) }} of {{ count($accounts) }} accounts
                            </div>
                        </div>
                    </div>

                    <!-- Clean Account Cards List Container -->
                    <div id="accountsListContainer" class="divide-y divide-zinc-100 dark:divide-zinc-800/70">
                        @foreach ($accounts as $index => $c)
                            @php
                                $config_data = is_string($c->config) ? json_decode($c->config) : (object)[];
                                $host_info = @$config_data->ip_address ?? @$config_data->from_address ?? '';
                                $port_info = @$config_data->port ?? '';
                                $enc_info = @$config_data->encryption ?? '';
                                $user_info = @$config_data->from_username ?? @$config_data->username ?? '';
                                
                                $isActive = (int)$c->status === 1;
                                $isCooling = $isActive && !empty($c->active_after) && \Carbon\Carbon::parse($c->active_after)->gt(now());
                                $searchString = strtolower(($c->name ?? '') . ' ' . ($c->type ?? '') . ' ' . ($isActive ? 'active' : 'inactive') . ' ' . $host_info . ' ' . $user_info);
                            @endphp

                            <div class="account-item p-4 sm:p-5 hover:bg-zinc-50/70 dark:hover:bg-zinc-900/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
                                 data-search="{{ $searchString }}" data-status="{{ $c->status }}" data-type="{{ strtolower($c->type ?? '') }}" data-index="{{ $index }}">
                                
                                <!-- Left Info Block -->
                                <div class="flex items-start sm:items-center space-x-3.5 min-w-0 flex-1">
                                    <!-- Gateway Icon Pod -->
                                    <div class="w-11 h-11 rounded-xl {{ strtoupper($c->type) === 'API' ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 border-purple-200/60 dark:border-purple-500/20' : 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-300 border-blue-200/60 dark:border-blue-500/20' }} border flex items-center justify-center text-sm font-bold shrink-0 shadow-2xs">
                                        <i class="fas {{ strtoupper($c->type) === 'API' ? 'fa-code' : 'fa-network-wired' }}"></i>
                                    </div>

                                    <!-- Account Info & Host Meta -->
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center flex-wrap gap-2">
                                            <a href="/account/{{ $c->id }}" class="font-bold text-sm text-zinc-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-300 transition-colors truncate">
                                                {{ $c->name ?? 'Unnamed Account' }}
                                            </a>

                                            <!-- Type Pill -->
                                            <span class="px-2 py-0.5 rounded text-3xs font-extrabold uppercase {{ strtoupper($c->type) === 'API' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800' }}">
                                                {{ $c->type ?? 'SMTP' }}
                                            </span>

                                            <!-- Status Beacon Pill -->
                                            @if($isActive && !$isCooling)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/80">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                                    Active Relay
                                                </span>
                                            @elseif($isCooling)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800/80">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                    Cooldown Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-zinc-100 text-zinc-500 border border-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700">
                                                    Inactive / Disabled
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Host / Port / User Meta Line -->
                                        <div class="mt-1.5 flex items-center flex-wrap gap-x-3 gap-y-1 text-3xs text-zinc-500 dark:text-zinc-400">
                                            @if(!empty($host_info))
                                                <span class="font-mono text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800/80 px-2 py-0.5 rounded border border-zinc-200/60 dark:border-zinc-700/60 flex items-center gap-1">
                                                    <i class="fas fa-plug text-4xs text-zinc-400"></i>
                                                    {{ $host_info }}
                                                </span>
                                            @endif

                                            @if(!empty($port_info))
                                                <span class="font-mono text-zinc-500 dark:text-zinc-400">
                                                    Port {{ $port_info }} {{ !empty($enc_info) ? '(' . strtoupper($enc_info) . ')' : '' }}
                                                </span>
                                            @endif

                                            @if(!empty($user_info))
                                                <span class="text-zinc-400 dark:text-zinc-500">
                                                    • User: {{ $user_info }}
                                                </span>
                                            @endif

                                            <span class="text-zinc-400 dark:text-zinc-500">
                                                • Updated {{ $c->updated_at ? \Carbon\Carbon::parse($c->updated_at)->diffForHumans(null, true) : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Actions Strip -->
                                <div class="flex items-center gap-1.5 self-end md:self-center shrink-0">
                                    <button type="button" onclick="openTestModal({{ $c->id }}, '{{ addslashes($c->name ?? '') }}')" title="Send Verification Test Email"
                                            class="px-2.5 py-1.5 text-3xs font-semibold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-200/80 dark:border-amber-500/30 rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                                        <i class="fas fa-bolt text-3xs text-amber-500"></i>
                                        <span>Test Relay</span>
                                    </button>
                                    <a href="/account/{{ $c->id }}" title="View Account Details" 
                                       class="px-2.5 py-1.5 text-3xs font-semibold text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800/80 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg transition-colors flex items-center gap-1">
                                        <i class="fas fa-eye text-3xs text-zinc-400"></i>
                                        <span>Details</span>
                                    </a>
                                    <a href="/account-form/{{ $c->id }}" title="Edit Account Settings" 
                                       class="px-2.5 py-1.5 text-3xs font-semibold text-zinc-700 dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/80 rounded-lg transition-colors flex items-center gap-1">
                                        <i class="fas fa-pen text-3xs text-zinc-400"></i>
                                        <span>Edit</span>
                                    </a>
                                    <button type="button" onclick="confirmDelete({{ $c->id }}, '{{ addslashes($c->name ?? '') }}')" title="Delete Account" 
                                            class="p-1.5 text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <!-- Search Empty State -->
                    <div id="emptySearchResults" class="hidden p-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 flex items-center justify-center mx-auto mb-3 text-lg">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">No matching mail accounts found</h4>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Try adjusting your search criteria or filter tab.</p>
                    </div>

                    <!-- Progressive Load More Pagination Footer -->
                    @if(count($accounts) > 10)
                        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800/80 text-center bg-zinc-50/50 dark:bg-zinc-900/30">
                            <button type="button" id="showMoreBtn" 
                                    class="inline-flex items-center justify-center px-5 py-2 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-2xs hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all gap-2">
                                <i class="fas fa-chevron-down text-3xs"></i>
                                <span id="remainingCountText">Show More Accounts</span>
                            </button>
                        </div>
                    @endif

                @else
                    <div class="text-center py-16 px-4">
                        <div class="w-14 h-14 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl border border-amber-200/60 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-server"></i>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">No Outbound Gateways Configured</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto mt-1 mb-5 leading-relaxed">
                            Configure an SMTP relay or API gateway to begin transmitting newsletters and broadcast campaigns.
                        </p>
                        <a href="/account-form/new" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-md transition-all gap-2">
                            <i class="fas fa-plus text-3xs"></i>
                            <span>Add Mail Account</span>
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- SMTP Test Connection Modal -->
    <div id="testModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-zinc-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#141417] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 transform transition-all space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                <div class="flex items-center space-x-3 text-amber-600 dark:text-amber-400">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-base border border-amber-200/60 dark:border-amber-500/30">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Test Connection</h3>
                        <p class="text-3xs text-zinc-400" id="testModalAccountName"></p>
                    </div>
                </div>
                <button type="button" onclick="closeTestModal()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div>
                <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="testRecipientEmail">
                    Send Verification Email To <span class="text-rose-500">*</span>
                </label>
                <input type="email" id="testRecipientEmail" value="{{ Auth::user()->email ?? '' }}" placeholder="e.g. your-email@gmail.com" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white text-xs font-medium focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all">
                <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-1">
                    An actual test message will be sent through this SMTP gateway to verify authentication and deliverability.
                </p>
            </div>

            <!-- Result Feedback Box -->
            <div id="testResultBox" class="hidden p-3.5 rounded-xl text-xs leading-snug"></div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeTestModal()" class="px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                    Close
                </button>
                <button type="button" id="btnExecuteTest" onclick="executeSmtpTest()" class="inline-flex items-center px-4 py-2 text-xs font-bold text-zinc-950 bg-amber-500 hover:bg-amber-400 rounded-xl transition-all shadow-md shadow-amber-500/20 gap-1.5">
                    <i id="testIconSpinner" class="fas fa-paper-plane text-3xs"></i>
                    <span>Send Test Email</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Reusable Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-zinc-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#141417] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 transform transition-all space-y-4">
            <div class="flex items-center space-x-3 text-rose-600 dark:text-rose-400">
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 flex items-center justify-center text-base">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Delete Mail Account</h3>
                    <p class="text-3xs text-zinc-400">Confirmation required</p>
                </div>
            </div>
            <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                Are you sure you want to delete <strong id="deleteAccountName" class="text-zinc-900 dark:text-white"></strong>? This action cannot be undone and may affect active campaigns configured to use this sender.
            </p>
            <form method="POST" action="/account/delete">
                @csrf
                <input type="hidden" name="account_id" id="modalAccountId">
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl transition-colors shadow-2xs">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentTestAccountId = null;

        function openTestModal(accountId, accountName) {
            currentTestAccountId = accountId;
            document.getElementById('testModalAccountName').textContent = accountName;
            const resBox = document.getElementById('testResultBox');
            resBox.className = 'hidden';
            resBox.innerHTML = '';
            document.getElementById('testModal').classList.remove('hidden');
        }

        function closeTestModal() {
            document.getElementById('testModal').classList.add('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function executeSmtpTest() {
            const recipient = document.getElementById('testRecipientEmail').value.trim();
            if (!recipient) {
                alert('Please enter a recipient email address.');
                return;
            }

            const btn = document.getElementById('btnExecuteTest');
            const icon = document.getElementById('testIconSpinner');
            const resBox = document.getElementById('testResultBox');

            btn.disabled = true;
            icon.className = 'fas fa-spinner fa-spin text-3xs';
            resBox.className = 'p-3 bg-blue-50 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 dark:border-blue-900 rounded-xl text-xs';
            resBox.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Connecting to SMTP gateway and transmitting verification test...';
            resBox.classList.remove('hidden');

            fetch('/mail-accounts/test-smtp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    account_id: currentTestAccountId,
                    recipient: recipient
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    resBox.className = 'p-3.5 bg-emerald-50 text-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800 rounded-xl text-xs font-medium space-y-1';
                    resBox.innerHTML = `<div class="flex items-center gap-1.5 font-bold text-emerald-800 dark:text-emerald-300"><i class="fas fa-check-circle"></i> Verification Successful!</div><p>${data.message}</p>`;
                } else {
                    resBox.className = 'p-3.5 bg-rose-50 text-rose-900 dark:bg-rose-950/50 dark:text-rose-200 border border-rose-300 dark:border-rose-800 rounded-xl text-xs font-medium space-y-1';
                    resBox.innerHTML = `<div class="flex items-center gap-1.5 font-bold text-rose-800 dark:text-rose-300"><i class="fas fa-circle-exclamation"></i> Verification Failed</div><p class="font-mono text-3xs break-all">${data.message}</p>`;
                }
            })
            .catch(err => {
                resBox.className = 'p-3.5 bg-rose-50 text-rose-900 dark:bg-rose-950/50 dark:text-rose-200 border border-rose-300 dark:border-rose-800 rounded-xl text-xs font-medium';
                resBox.innerHTML = `<i class="fas fa-circle-exclamation mr-1.5"></i> Network error: ${err.message}`;
            })
            .finally(() => {
                btn.disabled = false;
                icon.className = 'fas fa-paper-plane text-3xs';
            });
        }
    </script>
</x-app-layout>
