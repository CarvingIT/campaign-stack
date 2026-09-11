@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageSize = 12;
        let visibleCount = pageSize;
        let currentFilter = 'all';
        const allItems = Array.from(document.querySelectorAll('.tag-item'));
        const searchInput = document.getElementById('tagSearchInput');
        const showMoreBtn = document.getElementById('showMoreBtn');
        const countDisplay = document.getElementById('tagCountDisplay');
        const emptySearchResults = document.getElementById('emptySearchResults');
        const filterPills = document.querySelectorAll('.filter-pill');

        function updateListVisibility() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let matchingItems = [];

            allItems.forEach(item => {
                const searchText = item.getAttribute('data-search') || '';
                const contactCount = parseInt(item.getAttribute('data-count') || '0', 10);
                
                let matchesFilter = true;
                if (currentFilter === 'active') {
                    matchesFilter = contactCount > 0;
                } else if (currentFilter === 'empty') {
                    matchesFilter = contactCount === 0;
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
                if (countDisplay) countDisplay.textContent = `Showing ${Math.min(visibleCount, matchingItems.length)} of ${matchingItems.length} matching tags`;
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
                    countDisplay.textContent = `Showing ${currentlyShown} of ${total} tags`;
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

    function confirmDelete(id, label, count) {
        document.getElementById('modalTagId').value = id;
        document.getElementById('deleteTagLabel').textContent = label;
        document.getElementById('deleteTagCount').textContent = count;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function copyTagToClipboard(label) {
        navigator.clipboard.writeText(label).then(() => {
            const toast = document.getElementById('copyToast');
            if (toast) {
                toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
                toast.classList.add('opacity-100', 'translate-y-0');
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                    toast.classList.remove('opacity-100', 'translate-y-0');
                }, 1800);
            }
        });
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
                        <i class="fas fa-tags text-amber-500"></i>
                        <span>{{ __('Audience Segments & Tags') }}</span>
                    </h2>
                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80 uppercase">
                        {{ count($tags) }} Total
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 pl-5">
                    Organize contacts into high-precision segments for targeted broadcast campaigns and delivery filters.
                </p>
            </div>
            
            <div class="flex items-center gap-2.5">
                <a href="/contacts" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#121215] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1.5">
                    <i class="fas fa-users text-3xs text-zinc-400"></i>
                    <span>All Contacts</span>
                </a>
                <a href="/tag-form/new" class="group inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 gap-2">
                    <i class="fas fa-plus text-3xs transition-transform group-hover:rotate-90"></i>
                    <span>Create New Tag</span>
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
                $totalContactsTagged = $tags->sum('contact_tags_count');
                $tagsWithContacts = $tags->where('contact_tags_count', '>', 0)->count();
                $emptyTags = count($tags) - $tagsWithContacts;
                $effectiveContacts = $totalContacts ?? \App\Models\Contact::count();
                $avgContacts = count($tags) > 0 ? round($totalContactsTagged / count($tags), 1) : 0;
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Metric 1: Total Tags -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-amber-500/40 dark:hover:border-amber-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Total Tags
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs border border-amber-200/50 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                            {{ number_format(count($tags)) }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Audience categories configured
                        </p>
                    </div>
                </div>

                <!-- Metric 2: Total Contact Associations -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-emerald-500/40 dark:hover:border-emerald-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Tagged Contacts
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs border border-emerald-200/50 dark:border-emerald-500/20 shadow-2xs">
                            <i class="fas fa-user-tag"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                            {{ number_format($totalContactsTagged) }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Across {{ number_format($effectiveContacts) }} unique subscribers
                        </p>
                    </div>
                </div>

                <!-- Metric 3: Active Segments -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-blue-500/40 dark:hover:border-blue-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Populated Tags
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs border border-blue-200/50 dark:border-blue-500/20 shadow-2xs">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">
                            {{ $tagsWithContacts }} <span class="text-base font-normal text-zinc-400">/ {{ count($tags) }}</span>
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            {{ $emptyTags }} empty {{ $emptyTags === 1 ? 'tag' : 'tags' }} unassigned
                        </p>
                    </div>
                </div>

                <!-- Metric 4: Average Density -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-purple-500/40 dark:hover:border-purple-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Avg Segment Size
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs border border-purple-200/50 dark:border-purple-500/20 shadow-2xs">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                            {{ $avgContacts }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Subscribers per tag average
                        </p>
                    </div>
                </div>
            </div>

            <!-- Explanatory Guidance Banner Card -->
            <div class="relative overflow-hidden rounded-2xl border border-amber-200/90 dark:border-amber-500/20 bg-gradient-to-r from-amber-500/[0.08] via-amber-400/[0.03] to-amber-500/[0.06] dark:from-[#111114] dark:via-zinc-900/80 dark:to-[#111114] p-5 sm:p-6 shadow-xs dark:shadow-2xs backdrop-blur-sm transition-all">
                <div class="absolute -right-6 -bottom-6 opacity-10 dark:opacity-5 text-9xl pointer-events-none transform -rotate-12 select-none">
                    <i class="fas fa-tags text-amber-500 dark:text-amber-300"></i>
                </div>
                <div class="flex items-start gap-4 sm:gap-5 relative z-10">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-100/90 text-amber-800 border border-amber-200/90 dark:bg-amber-400/10 dark:text-amber-300 dark:border-amber-400/20 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="font-bold text-sm sm:text-base text-zinc-900 dark:text-white flex items-center gap-2">
                            How do Tags & Audience Segments work?
                            <span class="w-2 h-2 rounded-full bg-amber-500 dark:bg-amber-300"></span>
                        </h4>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            Tags dynamically segment your subscriber base into targeted audiences (e.g. <em>CTO & Tech Leaders</em>, <em>SaaS Founders</em>, <em>Webinar Leads</em>). When launching broadcasts in the Dispatch Studio or creating campaigns, select tags to send relevant messages with higher engagement and inbox deliverability.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Tags Directory Card -->
            <div class="bg-white dark:bg-[#111114] rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs overflow-hidden">
                
                @if(count($tags) > 0)
                    <!-- Top Toolbar & Filter Strip -->
                    <div class="p-4 sm:p-5 border-b border-zinc-100 dark:border-zinc-800/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-zinc-50/50 dark:bg-zinc-900/30">
                        
                        <!-- Search Bar -->
                        <div class="relative flex-1 max-w-md">
                            <i class="fas fa-search absolute left-3.5 top-3 text-zinc-400 text-xs"></i>
                            <input type="text" id="tagSearchInput" placeholder="Filter tags by label name..." 
                                   class="w-full pl-9 pr-4 py-2 bg-white dark:bg-[#09090B] border border-zinc-200/90 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs">
                        </div>

                        <!-- Segment Filter Pills & Counter -->
                        <div class="flex items-center justify-between md:justify-end gap-3 flex-wrap">
                            <div class="flex items-center bg-zinc-100 dark:bg-zinc-900 p-1 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80 text-xs">
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-semibold bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-xs transition-all" data-filter="all">
                                    All ({{ count($tags) }})
                                </button>
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all" data-filter="active">
                                    Populated ({{ $tagsWithContacts }})
                                </button>
                                <button type="button" class="filter-pill px-3 py-1 rounded-lg font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all" data-filter="empty">
                                    Empty ({{ $emptyTags }})
                                </button>
                            </div>

                            <div class="text-3xs font-mono text-zinc-400 dark:text-zinc-500" id="tagCountDisplay">
                                Showing {{ min(12, count($tags)) }} of {{ count($tags) }} tags
                            </div>
                        </div>
                    </div>

                    <!-- Clean Tag Cards List Container -->
                    <div id="tagsListContainer" class="divide-y divide-zinc-100 dark:divide-zinc-800/70">
                        @foreach ($tags as $index => $t)
                            @php
                                $contactsCount = $t->contact_tags_count ?? 0;
                                $searchString = strtolower(($t->label ?? '') . ' ' . $contactsCount . ' contacts');
                                $sharePct = $effectiveContacts > 0 ? round(($contactsCount / $effectiveContacts) * 100, 1) : 0;
                            @endphp

                            <div class="tag-item p-4 sm:p-5 hover:bg-zinc-50/70 dark:hover:bg-zinc-900/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
                                 data-search="{{ $searchString }}" data-count="{{ $contactsCount }}" data-index="{{ $index }}">
                                
                                <!-- Left Info Block -->
                                <div class="flex items-start sm:items-center space-x-3.5 min-w-0 flex-1">
                                    <!-- Hash Tag Icon Pod -->
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-500/20 text-amber-700 dark:text-amber-300 flex items-center justify-center text-sm font-bold shrink-0 shadow-2xs">
                                        <i class="fas fa-hashtag"></i>
                                    </div>

                                    <!-- Tag Name & Audience Metrics -->
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center flex-wrap gap-2">
                                            <a href="/tag-form/{{ $t->id }}" class="font-bold text-sm text-zinc-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-300 transition-colors truncate">
                                                {{ $t->label ?? 'Unnamed Tag' }}
                                            </a>

                                            <!-- Copy Tag Button -->
                                            <button type="button" onclick="copyTagToClipboard('{{ addslashes($t->label ?? '') }}')" title="Copy Tag Label" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 text-3xs transition-colors p-1">
                                                <i class="far fa-copy"></i>
                                            </button>

                                            <!-- Contact Reach Pill -->
                                            @if($contactsCount > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/80">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                    {{ number_format($contactsCount) }} {{ ($contactsCount == 1) ? 'contact' : 'contacts' }}
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono ml-1">({{ $sharePct }}%)</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-zinc-100 text-zinc-500 border border-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700">
                                                    0 contacts · Unassigned
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Progress Bar & Subtitle -->
                                        <div class="mt-2 flex items-center gap-3 max-w-md">
                                            <div class="flex-1 bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ min(100, $sharePct) }}%"></div>
                                            </div>
                                            <span class="text-3xs font-mono text-zinc-400 dark:text-zinc-500 shrink-0">
                                                ID #{{ $t->id }} • {{ $t->updated_at ? \Carbon\Carbon::parse($t->updated_at)->diffForHumans(null, true) : 'Active' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Actions Strip -->
                                <div class="flex items-center gap-1.5 self-end md:self-center shrink-0">
                                    <a href="/contacts?tag={{ urlencode($t->label) }}" class="px-2.5 py-1.5 text-3xs font-semibold text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800/80 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg transition-colors flex items-center gap-1" title="View contacts with tag {{ $t->label }}">
                                        <i class="fas fa-users text-3xs text-zinc-400"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="/tag-form/{{ $t->id }}" class="px-2.5 py-1.5 text-3xs font-semibold text-zinc-700 dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/80 rounded-lg transition-colors flex items-center gap-1">
                                        <i class="fas fa-pen text-3xs text-zinc-400"></i>
                                        <span>Edit</span>
                                    </a>
                                    <button type="button" onclick="confirmDelete({{ $t->id }}, '{{ addslashes($t->label ?? '') }}', {{ $contactsCount }})" title="Delete Tag" 
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
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">No matching tags found</h4>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Try searching with a different keyword or filter tab.</p>
                    </div>

                    <!-- Progressive Load More Pagination Footer -->
                    @if(count($tags) > 12)
                        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800/80 text-center bg-zinc-50/50 dark:bg-zinc-900/30">
                            <button type="button" id="showMoreBtn" 
                                    class="inline-flex items-center justify-center px-5 py-2 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-2xs hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all gap-2">
                                <i class="fas fa-chevron-down text-3xs"></i>
                                <span id="remainingCountText">Show More Tags</span>
                            </button>
                        </div>
                    @endif

                @else
                    <div class="text-center py-16 px-4">
                        <div class="w-14 h-14 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl border border-amber-200/60 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">No Audience Tags Created Yet</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto mt-1 mb-5 leading-relaxed">
                            Create subscriber tags to segment your contacts for high-deliverability broadcast campaigns.
                        </p>
                        <a href="/tag-form/new" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-md transition-all gap-2">
                            <i class="fas fa-plus text-3xs"></i>
                            <span>Create Your First Tag</span>
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
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 flex items-center justify-center text-base">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Delete Tag</h3>
                    <p class="text-3xs text-zinc-400">Confirmation required</p>
                </div>
            </div>
            <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                Are you sure you want to delete <strong id="deleteTagLabel" class="text-zinc-900 dark:text-white"></strong>? 
                This tag is assigned to <strong id="deleteTagCount" class="text-zinc-900 dark:text-white">0</strong> contact(s). The contacts themselves will not be deleted, but they will lose this segment assignment.
            </p>
            <form method="POST" action="/tag/delete">
                @csrf
                <input type="hidden" name="tag_id" id="modalTagId">
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl transition-colors shadow-2xs">
                        Delete Tag
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Copy Toast Notification -->
    <div id="copyToast" class="fixed bottom-6 right-6 z-50 transition-all duration-200 transform translate-y-2 opacity-0 pointer-events-none bg-zinc-900 dark:bg-white text-white dark:text-zinc-950 text-xs font-semibold px-4 py-2 rounded-xl shadow-xl border border-zinc-700 dark:border-zinc-200 flex items-center gap-2">
        <i class="fas fa-check text-emerald-400 dark:text-emerald-600 text-3xs"></i>
        <span>Tag copied to clipboard</span>
    </div>
</x-app-layout>
