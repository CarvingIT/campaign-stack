@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageSize = 15;
        let visibleCount = pageSize;
        let currentFilter = 'all';
        const allRows = Array.from(document.querySelectorAll('.campaign-row'));
        const searchInput = document.getElementById('campaignSearchInput');
        const showMoreBtn = document.getElementById('showMoreBtn');
        const countDisplay = document.getElementById('campaignCountDisplay');
        const emptySearchResults = document.getElementById('emptySearchResults');
        const filterPills = Array.from(document.querySelectorAll('.filter-pill'));

        function updateListVisibility() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let matchingRows = [];

            allRows.forEach(row => {
                const searchData = (row.getAttribute('data-search') || '').toLowerCase();
                const count = parseInt(row.getAttribute('data-count') || '0', 10);
                const type = (row.getAttribute('data-type') || '').toLowerCase();

                let matchesFilter = true;
                if (currentFilter === 'active') {
                    matchesFilter = count > 0;
                } else if (currentFilter === 'empty') {
                    matchesFilter = count === 0;
                } else if (currentFilter !== 'all') {
                    matchesFilter = type === currentFilter;
                }

                const matchesQuery = (query === '' || searchData.includes(query));

                if (matchesFilter && matchesQuery) {
                    matchingRows.push(row);
                } else {
                    row.classList.add('hidden');
                }
            });

            if (query !== '' || currentFilter !== 'all') {
                matchingRows.forEach((row, idx) => {
                    if (idx < visibleCount) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
                if (countDisplay) countDisplay.textContent = `Showing ${Math.min(visibleCount, matchingRows.length)} of ${matchingRows.length} matching campaigns`;
                if (emptySearchResults) {
                    emptySearchResults.classList.toggle('hidden', matchingRows.length > 0);
                }
            } else {
                if (emptySearchResults) emptySearchResults.classList.add('hidden');
                matchingRows.forEach((row, idx) => {
                    if (idx < visibleCount) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });

                const total = matchingRows.length;
                const currentlyShown = Math.min(visibleCount, total);

                if (countDisplay) {
                    countDisplay.textContent = `Showing ${currentlyShown} of ${total} campaigns`;
                }
            }

            if (showMoreBtn) {
                if (matchingRows.length <= visibleCount) {
                    showMoreBtn.style.display = 'none';
                } else {
                    showMoreBtn.style.display = 'inline-flex';
                    const remaining = matchingRows.length - visibleCount;
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

    function confirmDelete(id, name, count) {
        document.getElementById('modalCampaignId').value = id;
        document.getElementById('deleteCampaignName').textContent = name;
        document.getElementById('deleteNewsletterCount').textContent = count;
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
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                    </span>
                    <h2 class="font-extrabold text-2xl text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        <i class="fas fa-bullhorn text-amber-500"></i>
                        <span>{{ __('Marketing Campaigns & Tracks') }}</span>
                    </h2>
                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80 uppercase">
                        {{ count($campaigns) }} Total
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 pl-5">
                    Organize newsletters, automated sequences, and strategic broadcast tracks into structured marketing programs.
                </p>
            </div>
            
            <div class="flex items-center gap-2.5">
                <a href="/newsletters" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1.5">
                    <i class="fas fa-paper-plane text-3xs text-amber-500"></i>
                    <span>Newsletters</span>
                </a>
                <a href="/campaign-form/new" class="group inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 gap-2">
                    <i class="fas fa-plus text-3xs transition-transform group-hover:rotate-90"></i>
                    <span>Create Campaign</span>
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
                $totalCampaigns = count($campaigns);
                $totalNewsletters = $campaigns->sum('newsletters_count');
                $activeCampaigns = $campaigns->where('newsletters_count', '>', 0)->count();
                $emptyCampaigns = $totalCampaigns - $activeCampaigns;
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Metric 1: Total Campaigns -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-amber-500/40 dark:hover:border-amber-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Total Campaigns
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs border border-amber-200/50 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                            {{ $totalCampaigns }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Configured marketing tracks
                        </p>
                    </div>
                </div>

                <!-- Metric 2: Associated Newsletters -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-emerald-500/40 dark:hover:border-emerald-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Linked Newsletters
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs border border-emerald-200/50 dark:border-emerald-500/20 shadow-2xs">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight">
                            {{ $totalNewsletters }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Dispatches across all tracks
                        </p>
                    </div>
                </div>

                <!-- Metric 3: Active Tracks -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-blue-500/40 dark:hover:border-blue-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Active Tracks
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs border border-blue-200/50 dark:border-blue-500/20 shadow-2xs">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">
                            {{ $activeCampaigns }} <span class="text-base font-normal text-zinc-400">/ {{ $totalCampaigns }}</span>
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Tracks with published dispatches
                        </p>
                    </div>
                </div>

                <!-- Metric 4: Draft / Empty Tracks -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-purple-500/40 dark:hover:border-purple-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Draft Tracks
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs border border-purple-200/50 dark:border-purple-500/20 shadow-2xs">
                            <i class="fas fa-folder-open"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 tracking-tight">
                            {{ $emptyCampaigns }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Awaiting newsletter assignments
                        </p>
                    </div>
                </div>
            </div>

            <!-- Explanatory Guidance Banner Card -->
            <div class="relative overflow-hidden rounded-2xl border border-amber-200/90 dark:border-amber-500/20 bg-gradient-to-r from-amber-500/[0.08] via-amber-400/[0.03] to-amber-500/[0.06] dark:from-[#111114] dark:via-zinc-900/80 dark:to-[#111114] p-5 sm:p-6 shadow-xs dark:shadow-2xs backdrop-blur-sm transition-all">
                <div class="absolute -right-6 -bottom-6 opacity-10 dark:opacity-5 text-9xl pointer-events-none transform -rotate-12 select-none">
                    <i class="fas fa-bullhorn text-amber-500 dark:text-amber-300"></i>
                </div>
                <div class="flex items-start gap-4 sm:gap-5 relative z-10">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-100/90 text-amber-800 border border-amber-200/90 dark:bg-amber-400/10 dark:text-amber-300 dark:border-amber-400/20 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="font-bold text-sm sm:text-base text-zinc-900 dark:text-white flex items-center gap-2">
                            Campaign Architecture & Newsletter Grouping
                            <span class="w-2 h-2 rounded-full bg-amber-500 dark:bg-amber-300"></span>
                        </h4>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            Campaigns group related email newsletters into thematic marketing tracks (e.g. Weekly Digests, Product Announcements, Onboarding Drips). Create a campaign to establish your marketing pillar, then compose and attach newsletters to track aggregated engagement over time.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Campaigns Directory Card -->
            <div class="bg-white dark:bg-[#111114] rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs overflow-hidden">
                
                @if(count($campaigns) > 0)
                    <!-- Top Toolbar & Filter Strip -->
                    <div class="p-4 sm:p-5 border-b border-zinc-100 dark:border-zinc-800/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-zinc-50/50 dark:bg-zinc-900/30">
                        
                        <!-- Search Bar -->
                        <div class="relative flex-1 max-w-md">
                            <i class="fas fa-search absolute left-3.5 top-3 text-zinc-400 text-xs"></i>
                            <input type="text" id="campaignSearchInput" placeholder="Search campaigns by name, classification, or ID..." 
                                   class="w-full pl-9 pr-4 py-2 bg-white dark:bg-[#09090B] border border-zinc-200/90 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs">
                        </div>

                        <!-- Filter Pills & Counter -->
                        <div class="flex items-center justify-between md:justify-end gap-3 flex-wrap">
                            <div class="flex items-center bg-zinc-100 dark:bg-zinc-900 p-1 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80 text-xs">
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-semibold bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-xs transition-all" data-filter="all">
                                    All ({{ count($campaigns) }})
                                </button>
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all" data-filter="active">
                                    Active ({{ $activeCampaigns }})
                                </button>
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all" data-filter="empty">
                                    Empty ({{ $emptyCampaigns }})
                                </button>
                            </div>

                            <div class="text-3xs font-mono text-zinc-400 dark:text-zinc-500" id="campaignCountDisplay">
                                Showing {{ min(15, count($campaigns)) }} of {{ count($campaigns) }} campaigns
                            </div>
                        </div>
                    </div>

                    <!-- High-Density Table Layout -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 text-3xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    <th class="py-3.5 pl-6 pr-4">Campaign Track</th>
                                    <th class="py-3.5 px-4">Classification</th>
                                    <th class="py-3.5 px-4">Linked Newsletters</th>
                                    <th class="py-3.5 px-4">Last Activity</th>
                                    <th class="py-3.5 pl-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="campaignsListContainer" class="divide-y divide-zinc-100 dark:divide-zinc-800/70 text-xs">
                                @foreach ($campaigns as $index => $c)
                                    @php
                                        $attributes = json_decode($c->other_attributes);
                                        $campaignType = $attributes->type ?? 'broadcast';
                                        $searchString = strtolower(($c->name ?? '') . ' ' . $campaignType . ' ' . ($c->newsletters_count ?? 0) . ' newsletters');
                                        
                                        $typeIcons = [
                                            'broadcast' => 'fa-bullhorn',
                                            'newsletter' => 'fa-newspaper',
                                            'promotional' => 'fa-tag',
                                            'onboarding' => 'fa-user-check',
                                            'announcement' => 'fa-bell',
                                            'reengagement' => 'fa-arrows-rotate',
                                        ];
                                        $iconClass = $typeIcons[strtolower($campaignType)] ?? 'fa-bullhorn';
                                    @endphp

                                    <tr class="campaign-row hover:bg-zinc-50/70 dark:hover:bg-zinc-900/40 transition-colors group"
                                        data-search="{{ $searchString }}" data-count="{{ $c->newsletters_count ?? 0 }}" data-type="{{ strtolower($campaignType) }}" data-index="{{ $index }}">
                                        
                                        <!-- Campaign Name + Icon Pod -->
                                        <td class="py-3.5 pl-6 pr-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3.5">
                                                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-300 border border-amber-500/20 flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">
                                                    <i class="fas {{ $iconClass }}"></i>
                                                </div>
                                                <div>
                                                    <a href="/campaign-form/{{ $c->id }}" class="font-bold text-xs text-zinc-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-300 transition-colors block">
                                                        {{ $c->name }}
                                                    </a>
                                                    <span class="font-mono text-zinc-400 dark:text-zinc-500 text-3xs">
                                                        Track ID #{{ $c->id }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Campaign Classification -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-3xs font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700 capitalize">
                                                <i class="fas fa-layer-group mr-1.5 text-4xs opacity-60"></i>
                                                {{ ucfirst($campaignType) }}
                                            </span>
                                        </td>

                                        <!-- Newsletters Linked -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if(($c->newsletters_count ?? 0) > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-3xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/80">
                                                    <i class="fas fa-paper-plane mr-1.5 text-4xs text-emerald-500"></i>
                                                    {{ $c->newsletters_count }} {{ $c->newsletters_count === 1 ? 'Newsletter' : 'Newsletters' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-3xs font-semibold bg-zinc-100 text-zinc-500 border border-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700">
                                                    0 Newsletters (Draft)
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Last Activity -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="text-zinc-500 dark:text-zinc-400 text-3xs">
                                                {{ $c->updated_at ? \Carbon\Carbon::parse($c->updated_at)->diffForHumans(null, true) : 'N/A' }}
                                            </span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-3.5 pl-4 pr-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end space-x-1">
                                                <a href="/newsletter-form/new?campaign_id={{ $c->id }}" title="Create Newsletter in Campaign" 
                                                   class="p-1.5 text-zinc-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 rounded-lg transition-colors">
                                                    <i class="fas fa-plus text-3xs"></i>
                                                </a>
                                                <a href="/campaign-form/{{ $c->id }}" title="Edit Campaign" 
                                                   class="p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                                    <i class="fas fa-pen text-3xs"></i>
                                                </a>
                                                <button type="button" onclick="confirmDelete({{ $c->id }}, '{{ addslashes($c->name ?? '') }}', {{ $c->newsletters_count ?? 0 }})" title="Delete Campaign" 
                                                        class="p-1.5 text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                                                    <i class="fas fa-trash-can text-3xs"></i>
                                                </button>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Search Empty State -->
                    <div id="emptySearchResults" class="hidden p-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 flex items-center justify-center mx-auto mb-3 text-lg">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">No matching campaigns found</h4>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Try adjusting your search query or filter tab.</p>
                    </div>

                    <!-- Progressive Load More Pagination Footer -->
                    @if(count($campaigns) > 15)
                        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800/80 text-center bg-zinc-50/50 dark:bg-zinc-900/30">
                            <button type="button" id="showMoreBtn" 
                                    class="inline-flex items-center justify-center px-5 py-2 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-2xs hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all gap-2">
                                <i class="fas fa-chevron-down text-3xs"></i>
                                <span id="remainingCountText">Show More Campaigns</span>
                            </button>
                        </div>
                    @endif

                @else
                    <div class="text-center py-16 px-4">
                        <div class="w-14 h-14 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl border border-amber-200/60 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">No Marketing Campaigns Yet</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto mt-1 mb-5 leading-relaxed">
                            Create your first email marketing campaign to organize and schedule your newsletter dispatches into strategic tracks.
                        </p>
                        <a href="/campaign-form/new" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-md transition-all gap-2">
                            <i class="fas fa-plus text-3xs"></i>
                            <span>Create First Campaign</span>
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Reusable Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-zinc-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#141417] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 transform transition-all space-y-4">
            <div class="flex items-center space-x-3 text-rose-600 dark:text-rose-400">
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/40 flex items-center justify-center text-base border border-rose-200 dark:border-rose-900">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Delete Campaign</h3>
                    <p class="text-3xs text-zinc-400">Remove marketing track</p>
                </div>
            </div>
            <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                Are you sure you want to delete <span id="deleteCampaignName" class="font-bold text-zinc-900 dark:text-white"></span>? 
                This campaign currently has <span id="deleteNewsletterCount" class="font-bold text-zinc-900 dark:text-white">0</span> linked newsletter(s).
            </p>
            <form method="POST" action="/campaign/delete">
                @csrf
                <input type="hidden" name="campaign_id" id="modalCampaignId">
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl transition-colors shadow-2xs">
                        Delete Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
