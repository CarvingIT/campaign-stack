@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageSize = 15;
        let visibleCount = pageSize;
        const allRows = Array.from(document.querySelectorAll('.contact-row'));
        const searchInput = document.getElementById('contactSearchInput');
        const showMoreBtn = document.getElementById('showMoreBtn');
        const countDisplay = document.getElementById('contactCountDisplay');
        const emptySearchResults = document.getElementById('emptySearchResults');
        const tagFilterButtons = Array.from(document.querySelectorAll('.tag-filter-btn'));
        const urlParams = new URLSearchParams(window.location.search);

        function normalizeTag(str) {
            if (!str) return '';
            const txt = document.createElement('textarea');
            txt.innerHTML = str;
            return txt.value.toLowerCase().replace(/\s+/g, ' ').trim();
        }

        let selectedTagFilter = normalizeTag(@json($selectedTag ?? '')) || normalizeTag(urlParams.get('tag') || 'all');
        if (!selectedTagFilter) selectedTagFilter = 'all';

        function parseRowTags(row) {
            const raw = row.getAttribute('data-tags');
            if (!raw) return [];
            try {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed)) {
                    return parsed.map(t => normalizeTag(t));
                }
            } catch(e) {}
            return raw.split(',').map(s => normalizeTag(s));
        }

        function updateListVisibility() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let matchingRows = [];

            allRows.forEach(row => {
                const searchData = (row.getAttribute('data-search') || '').toLowerCase();
                const rowTags = parseRowTags(row);

                const matchesQuery = (query === '' || searchData.includes(query));
                
                let matchesTag = true;
                if (selectedTagFilter === 'all') {
                    matchesTag = true;
                } else if (selectedTagFilter === 'untagged') {
                    matchesTag = rowTags.length === 0;
                } else {
                    matchesTag = rowTags.includes(selectedTagFilter);
                }

                if (matchesQuery && matchesTag) {
                    matchingRows.push(row);
                } else {
                    row.classList.add('hidden');
                }
            });

            if (query !== '' || selectedTagFilter !== 'all') {
                matchingRows.forEach((row, idx) => {
                    if (idx < visibleCount) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
                if (countDisplay) countDisplay.textContent = `Showing ${Math.min(visibleCount, matchingRows.length)} of ${matchingRows.length} matching contacts`;
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
                    countDisplay.textContent = `Showing ${currentlyShown} of ${total} contacts`;
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

        tagFilterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                tagFilterButtons.forEach(b => {
                    b.classList.remove('bg-zinc-900', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'shadow-xs');
                    b.classList.add('text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-800');
                });
                this.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-800');
                this.classList.add('bg-zinc-900', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'shadow-xs');

                selectedTagFilter = normalizeTag(this.getAttribute('data-tag') || 'all');
                visibleCount = pageSize;
                
                // Update URL without page reload
                const currentUrl = new URL(window.location);
                if (selectedTagFilter === 'all') {
                    currentUrl.searchParams.delete('tag');
                } else {
                    currentUrl.searchParams.set('tag', this.getAttribute('data-tag-raw') || selectedTagFilter);
                }
                window.history.replaceState({}, '', currentUrl);

                updateListVisibility();
            });
        });

        // Initialize active button styling based on initial filter
        if (selectedTagFilter !== 'all') {
            tagFilterButtons.forEach(btn => {
                if (normalizeTag(btn.getAttribute('data-tag')) === selectedTagFilter) {
                    tagFilterButtons.forEach(b => {
                        b.classList.remove('bg-zinc-900', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'shadow-xs');
                        b.classList.add('text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-800');
                    });
                    btn.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-800');
                    btn.classList.add('bg-zinc-900', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'shadow-xs');
                }
            });
        }

        updateListVisibility();
    });

    function confirmDelete(id, name, email) {
        document.getElementById('modalContactId').value = id;
        document.getElementById('deleteContactName').textContent = name || email;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function copyEmail(email, btn) {
        navigator.clipboard.writeText(email).then(() => {
            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-check text-emerald-500';
                setTimeout(() => {
                    icon.className = 'fas fa-copy';
                }, 1200);
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
                        <i class="fas fa-users text-amber-500"></i>
                        <span>{{ __('Contacts & Subscribers') }}</span>
                    </h2>
                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80 uppercase">
                        {{ count($contacts) }} Total
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 pl-5">
                    Manage subscriber directory, audience tag assignments, and recipient targeting metadata.
                </p>
            </div>
            
            <div class="flex items-center gap-2.5">
                <a href="/import-contact-form" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1.5">
                    <i class="fas fa-file-import text-3xs text-amber-500"></i>
                    <span>Import CSV</span>
                </a>
                <a href="/contact-form/new" class="group inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 gap-2">
                    <i class="fas fa-plus text-3xs transition-transform group-hover:rotate-90"></i>
                    <span>Add Contact</span>
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
                $totalContacts = count($contacts);
                $taggedContacts = $contacts->filter(fn($c) => $c->contactTags->count() > 0)->count();
                $untaggedContacts = $totalContacts - $taggedContacts;
                $uniqueCompanies = $contacts->pluck('company')->filter()->unique()->count();
                $activeTagsCount = isset($allTags) ? $allTags->where('contact_tags_count', '>', 0)->count() : 0;
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Metric 1: Total Contacts -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-amber-500/40 dark:hover:border-amber-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Total Subscribers
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs border border-amber-200/50 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                            {{ $totalContacts }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Registered recipient profiles
                        </p>
                    </div>
                </div>

                <!-- Metric 2: Segmented Contacts -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-emerald-500/40 dark:hover:border-emerald-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Tagged Audience
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs border border-emerald-200/50 dark:border-emerald-500/20 shadow-2xs">
                            <i class="fas fa-user-tag"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight">
                            {{ $taggedContacts }} <span class="text-base font-normal text-zinc-400">/ {{ $totalContacts }}</span>
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            {{ $totalContacts > 0 ? round(($taggedContacts / $totalContacts) * 100) : 0 }}% assigned to segments
                        </p>
                    </div>
                </div>

                <!-- Metric 3: Organizations -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-blue-500/40 dark:hover:border-blue-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Companies
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs border border-blue-200/50 dark:border-blue-500/20 shadow-2xs">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">
                            {{ $uniqueCompanies }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Distinct organization domains
                        </p>
                    </div>
                </div>

                <!-- Metric 4: Active Segments -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-purple-500/40 dark:hover:border-purple-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Active Segments
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs border border-purple-200/50 dark:border-purple-500/20 shadow-2xs">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 tracking-tight">
                            {{ $activeTagsCount }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Tags populated with subscribers
                        </p>
                    </div>
                </div>
            </div>

            <!-- Explanatory Guidance Banner Card -->
            <div class="relative overflow-hidden rounded-2xl border border-amber-200/90 dark:border-amber-500/20 bg-gradient-to-r from-amber-500/[0.08] via-amber-400/[0.03] to-amber-500/[0.06] dark:from-[#111114] dark:via-zinc-900/80 dark:to-[#111114] p-5 sm:p-6 shadow-xs dark:shadow-2xs backdrop-blur-sm transition-all">
                <div class="absolute -right-6 -bottom-6 opacity-10 dark:opacity-5 text-9xl pointer-events-none transform -rotate-12 select-none">
                    <i class="fas fa-users text-amber-500 dark:text-amber-300"></i>
                </div>
                <div class="flex items-start gap-4 sm:gap-5 relative z-10">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-100/90 text-amber-800 border border-amber-200/90 dark:bg-amber-400/10 dark:text-amber-300 dark:border-amber-400/20 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="font-bold text-sm sm:text-base text-zinc-900 dark:text-white flex items-center gap-2">
                            Subscriber Directory & Dynamic Segment Targeting
                            <span class="w-2 h-2 rounded-full bg-amber-500 dark:bg-amber-300"></span>
                        </h4>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            Filter subscribers by audience tags or search directly by email, name, and company. When dispatching newsletters in the Dispatch Studio, targeting specific tags guarantees personalized, high-engagement content delivery without manual list exports.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Contacts Directory Card -->
            <div class="bg-white dark:bg-[#111114] rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs overflow-hidden">
                
                @if(count($contacts) > 0)
                    <!-- Top Toolbar & Filter Strip -->
                    <div class="p-4 sm:p-5 border-b border-zinc-100 dark:border-zinc-800/80 space-y-4 bg-zinc-50/50 dark:bg-zinc-900/30">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Search Bar -->
                            <div class="relative flex-1 max-w-md">
                                <i class="fas fa-search absolute left-3.5 top-3 text-zinc-400 text-xs"></i>
                                <input type="text" id="contactSearchInput" placeholder="Search by name, email, company, or tag..." 
                                       class="w-full pl-9 pr-4 py-2 bg-white dark:bg-[#09090B] border border-zinc-200/90 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs">
                            </div>

                            <div class="text-3xs font-mono text-zinc-400 dark:text-zinc-500" id="contactCountDisplay">
                                Showing {{ min(15, count($contacts)) }} of {{ count($contacts) }} contacts
                            </div>
                        </div>

                        <!-- 1-Click Segment Filter Pills -->
                        @if(isset($allTags) && count($allTags) > 0)
                            <div class="flex items-center gap-1.5 overflow-x-auto pt-1 pb-1 scrollbar-none">
                                <span class="text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider shrink-0 mr-1 flex items-center gap-1">
                                    <i class="fas fa-filter text-amber-500 text-4xs"></i> Tag:
                                </span>
                                <button type="button" data-tag="all" class="tag-filter-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ empty($selectedTag) ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }} shrink-0">
                                    All ({{ count($contacts) }})
                                </button>
                                <button type="button" data-tag="untagged" class="tag-filter-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 shrink-0 flex items-center gap-1.5">
                                    <span>Untagged</span>
                                    <span class="text-3xs px-1.5 py-0.2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500">{{ $untaggedContacts }}</span>
                                </button>
                                @foreach($allTags as $tagItem)
                                    @php
                                        $isActiveTag = !empty($selectedTag) && strtolower($selectedTag) === strtolower($tagItem->label);
                                    @endphp
                                    <button type="button" data-tag="{{ strtolower($tagItem->label) }}" data-tag-raw="{{ $tagItem->label }}" 
                                            class="tag-filter-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ $isActiveTag ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }} shrink-0 flex items-center gap-1.5">
                                        <span>{{ $tagItem->label }}</span>
                                        <span class="text-3xs px-1.5 py-0.2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500">{{ $tagItem->contact_tags_count }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- High-Density Contact Table Layout -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 text-3xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    <th class="py-3.5 pl-6 pr-4">Subscriber</th>
                                    <th class="py-3.5 px-4">Contact Info</th>
                                    <th class="py-3.5 px-4">Company</th>
                                    <th class="py-3.5 px-4">Assigned Tags</th>
                                    <th class="py-3.5 pl-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="contactsListContainer" class="divide-y divide-zinc-100 dark:divide-zinc-800/70 text-xs">
                                @foreach ($contacts as $index => $c)
                                    @php
                                        $fullName = trim((@$c->salutation ? $c->salutation . ' ' : '') . (@$c->firstname ?? '') . ' ' . (@$c->lastname ?? ''));
                                        if (empty($fullName)) $fullName = 'Unnamed Contact';
                                        
                                        $tagNames = [];
                                        foreach($c->contactTags as $ct){
                                            if ($ct->tag) $tagNames[] = $ct->tag->label;
                                        }
                                        $tagsString = implode(' ', $tagNames);
                                        $searchString = strtolower($fullName . ' ' . ($c->email ?? '') . ' ' . ($c->company ?? '') . ' ' . ($c->mobile ?? '') . ' ' . $tagsString);
                                        
                                        $initials = strtoupper(substr($c->firstname ?? $c->email ?? 'U', 0, 1) . substr($c->lastname ?? '', 0, 1));
                                        if (empty(trim($initials))) $initials = 'U';
                                    @endphp

                                    <tr class="contact-row hover:bg-zinc-50/70 dark:hover:bg-zinc-900/40 transition-colors group"
                                        data-search="{{ $searchString }}" data-tags='@json($tagNames)' data-index="{{ $index }}">
                                        
                                        <!-- Subscriber (Avatar + Name) -->
                                        <td class="py-3.5 pl-6 pr-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3.5">
                                                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-300 border border-amber-500/20 flex items-center justify-center text-xs font-black shrink-0 shadow-2xs">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <a href="/contact-form/{{ $c->id }}" class="font-bold text-xs text-zinc-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-300 transition-colors block">
                                                        {{ $fullName }}
                                                    </a>
                                                    <span class="text-3xs text-zinc-400 dark:text-zinc-500">
                                                        Updated {{ $c->updated_at ? \Carbon\Carbon::parse($c->updated_at)->diffForHumans(null, true) : 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Contact Info (Email + Mobile) -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="space-y-0.5">
                                                <div class="flex items-center gap-1.5">
                                                    <a href="mailto:{{ $c->email }}" class="text-zinc-800 dark:text-zinc-200 hover:text-amber-600 dark:hover:text-amber-300 font-medium transition-colors">
                                                        {{ $c->email }}
                                                    </a>
                                                    <button type="button" onclick="copyEmail('{{ $c->email }}', this)" title="Copy Email" class="text-zinc-400 hover:text-amber-500 text-4xs transition-colors p-0.5">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                                @if(!empty($c->mobile))
                                                    <div class="text-3xs text-zinc-400 font-mono flex items-center">
                                                        <i class="fas fa-phone mr-1.5 text-4xs opacity-60"></i>
                                                        {{ $c->mobile }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Company -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if(!empty($c->company))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-3xs font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700">
                                                    <i class="fas fa-building mr-1.5 text-4xs opacity-60"></i>
                                                    {{ $c->company }}
                                                </span>
                                            @else
                                                <span class="text-zinc-400 text-3xs italic">—</span>
                                            @endif
                                        </td>

                                        <!-- Assigned Tags -->
                                        <td class="py-3.5 px-4">
                                            @if(count($tagNames) > 0)
                                                <div class="flex flex-wrap items-center gap-1 max-w-xs">
                                                    @foreach($tagNames as $tagLabel)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-500/30 whitespace-nowrap">
                                                            <i class="fas fa-tag mr-1 text-4xs text-amber-500"></i>
                                                            {{ $tagLabel }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-zinc-400 text-3xs italic">No tags assigned</span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-3.5 pl-4 pr-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end space-x-1">
                                                <a href="/contact-form/{{ $c->id }}" title="Edit Contact" 
                                                   class="p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                                    <i class="fas fa-pen text-3xs"></i>
                                                </a>
                                                <button type="button" onclick="confirmDelete({{ $c->id }}, '{{ addslashes($fullName) }}', '{{ addslashes($c->email ?? '') }}')" title="Delete Contact" 
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
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">No matching contacts found</h4>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Try adjusting your search query or selected tag filter.</p>
                    </div>

                    <!-- Progressive Load More Pagination Footer -->
                    @if(count($contacts) > 15)
                        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800/80 text-center bg-zinc-50/50 dark:bg-zinc-900/30">
                            <button type="button" id="showMoreBtn" 
                                    class="inline-flex items-center justify-center px-5 py-2 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-2xs hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all gap-2">
                                <i class="fas fa-chevron-down text-3xs"></i>
                                <span id="remainingCountText">Show More Contacts</span>
                            </button>
                        </div>
                    @endif

                @else
                    <div class="text-center py-16 px-4">
                        <div class="w-14 h-14 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl border border-amber-200/60 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">No Contacts Added Yet</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto mt-1 mb-5 leading-relaxed">
                            Start building your subscriber list by creating contacts manually or uploading a bulk CSV list.
                        </p>
                        <div class="flex items-center justify-center gap-3">
                            <a href="/import-contact-form" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1.5">
                                <i class="fas fa-file-import text-3xs text-amber-500"></i>
                                <span>Import CSV</span>
                            </a>
                            <a href="/contact-form/new" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-md transition-all gap-2">
                                <i class="fas fa-plus text-3xs"></i>
                                <span>Add New Contact</span>
                            </a>
                        </div>
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
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Delete Contact</h3>
                    <p class="text-3xs text-zinc-400">Permanent subscriber deletion</p>
                </div>
            </div>
            <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                Are you sure you want to delete <span id="deleteContactName" class="font-bold text-zinc-900 dark:text-white"></span>? 
                This contact will be permanently removed from all audience tags and recipient broadcast lists.
            </p>
            <form method="POST" action="/contact/delete">
                @csrf
                <input type="hidden" name="contact_id" id="modalContactId">
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl transition-colors shadow-2xs">
                        Delete Contact
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
