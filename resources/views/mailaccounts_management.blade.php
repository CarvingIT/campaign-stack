@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageSize = 8;
        let visibleCount = pageSize;
        const allItems = Array.from(document.querySelectorAll('.account-item'));
        const searchInput = document.getElementById('accountSearchInput');
        const showMoreBtn = document.getElementById('showMoreBtn');
        const countDisplay = document.getElementById('accountCountDisplay');
        const emptySearchResults = document.getElementById('emptySearchResults');

        function updateListVisibility() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let matchingItems = [];

            allItems.forEach(item => {
                const searchText = item.getAttribute('data-search') || '';
                if (query === '' || searchText.includes(query)) {
                    matchingItems.push(item);
                } else {
                    item.classList.add('hidden');
                }
            });

            if (query !== '') {
                matchingItems.forEach(item => item.classList.remove('hidden'));
                if (showMoreBtn) showMoreBtn.style.display = 'none';
                if (countDisplay) countDisplay.textContent = `Found ${matchingItems.length} matching account${matchingItems.length === 1 ? '' : 's'}`;
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

                if (showMoreBtn) {
                    if (currentlyShown >= total) {
                        showMoreBtn.style.display = 'none';
                    } else {
                        showMoreBtn.style.display = 'inline-flex';
                        const remaining = total - currentlyShown;
                        document.getElementById('remainingCountText').textContent = `Show More (${remaining} remaining)`;
                    }
                }
            }
        }

        if (showMoreBtn) {
            showMoreBtn.addEventListener('click', function() {
                visibleCount += pageSize;
                updateListVisibility();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', updateListVisibility);
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
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#FFC700] shadow-sm"></span>
                    <h2 class="font-black text-2xl text-[#0B192C] dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas fa-server text-[#FFC700]"></i>
                        {{ __('Mail Accounts') }}
                    </h2>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 pl-5">
                    Configure SMTP servers and API providers used for sending outbound email campaigns.
                </p>
            </div>
            <div>
                <a href="/account-form/new" class="inline-flex items-center px-5 py-2.5 bg-[#FFC700] hover:bg-[#e6b800] text-[#0B192C] text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 gap-2">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Add Mail Account</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-8 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Notifications -->
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))
                    @php
                        $alertStyles = [
                            'danger' => 'bg-red-50 text-red-900 border-red-200 dark:bg-red-950 dark:text-red-200 dark:border-red-800 icon-fa-exclamation-circle',
                            'warning' => 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-800 icon-fa-exclamation-triangle',
                            'success' => 'bg-emerald-50 text-emerald-900 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-800 icon-fa-check-circle',
                            'info' => 'bg-blue-50 text-blue-900 border-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:border-blue-800 icon-fa-info-circle',
                        ];
                    @endphp
                    <div class="p-4 rounded-xl border {{ $alertStyles[$msg] }} flex items-start gap-3 shadow-sm" role="alert">
                        <i class="fas {{ explode(' ', $alertStyles[$msg])[count(explode(' ', $alertStyles[$msg]))-1] }} text-lg mt-0.5"></i>
                        <div class="text-sm font-semibold">
                            {{ Session::get('alert-' . $msg) }}
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Metrics Overview Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-[#112238] p-5 rounded-2xl border border-gray-200 dark:border-[#1E3E62] shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">Total Accounts</p>
                        <h3 class="text-2xl font-black text-[#0B192C] dark:text-white mt-1">{{ count($accounts) }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-[#FFC700]/15 text-[#D9A700] dark:text-[#FFC700] border border-[#FFC700]/30 flex items-center justify-center text-lg">
                        <i class="fas fa-mail-bulk"></i>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#112238] p-5 rounded-2xl border border-gray-200 dark:border-[#1E3E62] shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">Active Senders</p>
                        <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $accounts->where('status', 1)->count() }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-lg">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#112238] p-5 rounded-2xl border border-gray-200 dark:border-[#1E3E62] shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">SMTP Servers</p>
                        <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $accounts->filter(fn($a) => strtoupper($a->type) === 'SMTP')->count() }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-lg">
                        <i class="fas fa-network-wired"></i>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#112238] p-5 rounded-2xl border border-gray-200 dark:border-[#1E3E62] shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">API Providers</p>
                        <h3 class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $accounts->filter(fn($a) => strtoupper($a->type) === 'API')->count() }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800 flex items-center justify-center text-lg">
                        <i class="fas fa-code"></i>
                    </div>
                </div>
            </div>

            <!-- Guidance Banner Card -->
            <div class="bg-gradient-to-r from-[#0B192C] via-[#1E3E62] to-[#0B192C] text-white rounded-2xl p-6 shadow-md border border-[#FFC700]/30 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 opacity-10 text-9xl pointer-events-none">
                    <i class="fas fa-paper-plane text-[#FFC700]"></i>
                </div>
                <div class="flex items-start gap-4 relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-[#FFC700]/20 border border-[#FFC700]/40 flex items-center justify-center text-xl shrink-0 text-[#FFC700]">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-bold text-base text-white flex items-center gap-2">
                            What are Mail Accounts?
                            <span class="w-2 h-2 rounded-full bg-[#FFC700]"></span>
                        </h4>
                        <p class="text-sm text-gray-200 leading-relaxed">
                            Mail accounts store your email dispatch configurations (SMTP servers or API credentials). Campaign Stack rotates active mail accounts to send outbound newsletters, ensuring high deliverability, domain safety, and rate-limit compliance.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Accounts List Container -->
            <div class="bg-white dark:bg-[#112238] rounded-2xl shadow-sm border border-gray-200 dark:border-[#1E3E62] overflow-hidden">
                
                @if(count($accounts) > 0)
                    <!-- Top Toolbar & Search Bar -->
                    <div class="p-5 border-b border-gray-100 dark:border-[#1E3E62] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50 dark:bg-[#0B192C]/60">
                        <div class="relative flex-1 max-w-md">
                            <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 dark:text-gray-300 text-sm"></i>
                            <input type="text" id="accountSearchInput" placeholder="Search accounts by name, status, or connection type..." 
                                   class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#0B192C] border border-gray-300 dark:border-[#1E3E62] rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFC700] focus:border-[#FFC700] transition-all">
                        </div>
                        <div class="text-xs font-semibold text-gray-600 dark:text-gray-300" id="accountCountDisplay">
                            Showing {{ min(8, count($accounts)) }} of {{ count($accounts) }} mail accounts
                        </div>
                    </div>

                    <!-- Modern Card List Items -->
                    <div id="accountsListContainer" class="divide-y divide-gray-100 dark:divide-[#1E3E62]">
                        @foreach ($accounts as $index => $c)
                            @php
                                $config_data = is_string($c->config) ? json_decode($c->config) : (object)[];
                                $host_info = @$config_data->ip_address ?? @$config_data->from_address ?? '';
                                $searchString = strtolower(($c->name ?? '') . ' ' . ($c->type ?? '') . ' ' . ($c->status == 1 ? 'active' : 'inactive') . ' ' . $host_info);
                            @endphp

                            <div class="account-item p-5 hover:bg-gray-50/80 dark:hover:bg-[#1E3E62]/40 transition-all duration-150 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                                 data-search="{{ $searchString }}" data-index="{{ $index }}">
                                
                                <div class="flex items-start sm:items-center space-x-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-[#0B192C] border border-gray-200 dark:border-[#1E3E62] text-[#0B192C] dark:text-[#FFC700] flex items-center justify-center text-lg shrink-0">
                                        <i class="fas {{ strtoupper($c->type) === 'API' ? 'fa-code' : 'fa-network-wired' }}"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <a href="/account/{{ $c->id }}" class="font-bold text-base text-[#0B192C] dark:text-white hover:text-[#D9A700] dark:hover:text-[#FFC700] transition-colors">
                                                {{ $c->name ?? 'Unnamed Account' }}
                                            </a>
                                            @if($c->status == 1)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-700">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-700">
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center flex-wrap gap-x-4 gap-y-1 mt-1 text-xs text-gray-600 dark:text-gray-300">
                                            @if(strtoupper($c->type) === 'SMTP')
                                                <span class="inline-flex items-center text-blue-700 dark:text-blue-300 font-semibold">
                                                    <i class="fas fa-network-wired mr-1 text-3xs"></i> SMTP
                                                </span>
                                            @elseif(strtoupper($c->type) === 'API')
                                                <span class="inline-flex items-center text-purple-700 dark:text-purple-300 font-semibold">
                                                    <i class="fas fa-code mr-1 text-3xs"></i> API
                                                </span>
                                            @else
                                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $c->type }}</span>
                                            @endif

                                            @if(!empty($host_info))
                                                <span class="font-mono text-gray-500 dark:text-gray-300">| {{ $host_info }}</span>
                                            @endif

                                            <span class="text-gray-500 dark:text-gray-400">
                                                | Updated {{ $c->updated_at ? \Carbon\Carbon::parse($c->updated_at)->diffForHumans() : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2 self-end sm:self-center">
                                    <a href="/account/{{ $c->id }}" title="View Details" 
                                       class="px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-[#0B192C] hover:bg-gray-200 dark:hover:bg-[#1E3E62] border border-transparent dark:border-[#1E3E62] rounded-lg transition-colors flex items-center gap-1.5">
                                        <i class="fas fa-eye text-2xs"></i>
                                        <span>Details</span>
                                    </a>
                                    <a href="/account-form/{{ $c->id }}" title="Edit Account" 
                                       class="p-2 text-gray-600 hover:text-[#0B192C] dark:text-gray-300 dark:hover:text-[#FFC700] hover:bg-amber-50 dark:hover:bg-[#1E3E62] rounded-lg transition-colors">
                                        <i class="fas fa-pencil-alt text-sm"></i>
                                    </a>
                                    <button type="button" onclick="confirmDelete({{ $c->id }}, '{{ addslashes($c->name ?? '') }}')" title="Delete Account" 
                                            class="p-2 text-gray-600 hover:text-red-600 dark:text-gray-300 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <!-- Search Empty State -->
                    <div id="emptySearchResults" class="hidden p-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-[#0B192C] text-gray-400 dark:text-gray-300 flex items-center justify-center mx-auto mb-3 text-xl">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="text-base font-bold text-[#0B192C] dark:text-white">No matching accounts found</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">Try adjusting your search criteria.</p>
                    </div>

                    <!-- Progressive Load More Pagination Footer -->
                    @if(count($accounts) > 8)
                        <div class="p-6 border-t border-gray-100 dark:border-[#1E3E62] text-center bg-gray-50/50 dark:bg-[#0B192C]/50">
                            <button type="button" id="showMoreBtn" 
                                    class="inline-flex items-center justify-center px-6 py-2.5 bg-white dark:bg-[#1E3E62] text-[#0B192C] dark:text-[#FFC700] font-bold text-sm rounded-xl border border-gray-300 dark:border-[#FFC700]/30 shadow-sm hover:bg-[#FFC700]/10 transition-all duration-200 gap-2">
                                <i class="fas fa-chevron-down text-xs"></i>
                                <span id="remainingCountText">Show More Accounts</span>
                            </button>
                        </div>
                    @endif

                @else
                    <div class="text-center py-14 px-4">
                        <div class="w-16 h-16 bg-[#FFC700]/15 text-[#D9A700] dark:text-[#FFC700] rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl border border-[#FFC700]/30 shadow-sm">
                            <i class="fas fa-server"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#0B192C] dark:text-white">No Mail Accounts Configured</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 max-w-md mx-auto mt-2 mb-6 leading-relaxed">
                            You need at least one active SMTP server or API mail account to start sending campaign emails.
                        </p>
                        <a href="/account-form/new" class="inline-flex items-center px-5 py-3 bg-[#FFC700] hover:bg-[#e6b800] text-[#0B192C] text-sm font-bold rounded-xl shadow-md transition-all duration-200 gap-2">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Add Your First Mail Account</span>
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Reusable Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-[#0B192C]/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#112238] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 dark:border-[#1E3E62] transform transition-all">
            <div class="flex items-center space-x-3 text-red-600 dark:text-red-400 mb-3">
                <div class="p-3 bg-red-100 dark:bg-red-950 rounded-full border border-red-200 dark:border-red-800">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Delete Mail Account</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                Are you sure you want to delete <span id="deleteAccountName" class="font-bold text-gray-900 dark:text-white"></span>? This action cannot be undone and may affect active campaigns using this sender.
            </p>
            <form method="POST" action="/account/delete">
                @csrf
                <input type="hidden" name="account_id" id="modalAccountId">
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
