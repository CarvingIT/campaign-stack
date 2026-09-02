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
        let selectedTagFilter = 'all';

        function updateListVisibility() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let matchingRows = [];

            allRows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                const rowTags = row.getAttribute('data-tags') || '';

                const matchesQuery = (query === '' || searchData.includes(query));
                const matchesTag = (selectedTagFilter === 'all' || rowTags.includes(selectedTagFilter));

                if (matchesQuery && matchesTag) {
                    matchingRows.push(row);
                } else {
                    row.classList.add('hidden');
                }
            });

            if (query !== '' || selectedTagFilter !== 'all') {
                matchingRows.forEach(row => row.classList.remove('hidden'));
                if (showMoreBtn) showMoreBtn.style.display = 'none';
                if (countDisplay) countDisplay.textContent = `Found ${matchingRows.length} matching contact${matchingRows.length === 1 ? '' : 's'}`;
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

        tagFilterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                tagFilterButtons.forEach(b => {
                    b.classList.remove('bg-zinc-900', 'text-white', 'dark:bg-amber-100', 'dark:text-zinc-950');
                    b.classList.add('bg-white', 'dark:bg-[#141417]', 'text-zinc-700', 'dark:text-zinc-300');
                });
                this.classList.remove('bg-white', 'dark:bg-[#141417]', 'text-zinc-700', 'dark:text-zinc-300');
                this.classList.add('bg-zinc-900', 'text-white', 'dark:bg-amber-100', 'dark:text-zinc-950');

                selectedTagFilter = this.getAttribute('data-tag') || 'all';
                updateListVisibility();
            });
        });

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
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas fa-users text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ __('Contacts & Subscribers') }}
                    </h2>
                </div>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1 pl-5">
                    Manage your email subscriber directory, audience tags, and recipient lists.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="/import-contact-form" class="inline-flex items-center px-4 py-2.5 bg-slate-100 dark:bg-[#141417] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-800 transition-colors gap-2 shadow-2xs">
                    <i class="fas fa-file-import text-amber-500 text-xs"></i>
                    <span>Import CSV</span>
                </a>
                <a href="/contact-form/new" class="inline-flex items-center px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition-all duration-200 gap-2">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Add Contact</span>
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
                            'danger' => 'bg-red-50/90 text-red-900 border-red-200 dark:bg-red-950/70 dark:text-red-200 dark:border-red-800/80 icon-fa-exclamation-circle',
                            'warning' => 'bg-amber-50/90 text-amber-900 border-amber-200 dark:bg-amber-950/70 dark:text-amber-200 dark:border-amber-800/80 icon-fa-exclamation-triangle',
                            'success' => 'bg-emerald-50/90 text-emerald-900 border-emerald-200 dark:bg-emerald-950/70 dark:text-emerald-200 dark:border-emerald-800/80 icon-fa-check-circle',
                            'info' => 'bg-amber-50/70 text-amber-900 border-amber-200/70 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-800/60 icon-fa-info-circle',
                        ];
                    @endphp
                    <div class="p-4 rounded-xl border {{ $alertStyles[$msg] }} flex items-start gap-3 shadow-2xs backdrop-blur-sm" role="alert">
                        <i class="fas {{ explode(' ', $alertStyles[$msg])[count(explode(' ', $alertStyles[$msg]))-1] }} text-lg mt-0.5"></i>
                        <div class="text-sm font-semibold">
                            {{ Session::get('alert-' . $msg) }}
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Metrics Overview Cards -->
            @php
                $totalContacts = count($contacts);
                $taggedContacts = $contacts->filter(fn($c) => $c->contactTags->count() > 0)->count();
                $uniqueCompanies = $contacts->pluck('company')->filter()->unique()->count();
                
                // Extract all unique tags for filter bar
                $allTags = [];
                foreach($contacts as $c) {
                    foreach($c->contactTags as $ct) {
                        if ($ct->tag) {
                            $allTags[$ct->tag->label] = ($allTags[$ct->tag->label] ?? 0) + 1;
                        }
                    }
                }
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-white via-white to-slate-50/90 dark:from-[#141417] dark:to-[#141417] p-5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs flex items-center justify-between transition-all duration-300 hover:border-amber-300/40">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Contacts</p>
                        <h3 class="text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $totalContacts }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-100/70 dark:bg-amber-950/30 text-amber-900 dark:text-amber-300 border border-amber-200/80 dark:border-amber-500/20 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fas fa-users"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-white via-white to-slate-50/90 dark:from-[#141417] dark:to-[#141417] p-5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs flex items-center justify-between transition-all duration-300 hover:border-emerald-300/40">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tagged Subscribers</p>
                        <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $taggedContacts }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/70 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fas fa-user-tag"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-white via-white to-slate-50/90 dark:from-[#141417] dark:to-[#141417] p-5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs flex items-center justify-between transition-all duration-300 hover:border-blue-300/40">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Unique Companies</p>
                        <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $uniqueCompanies }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/70 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800/70 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>

            <!-- Main Contacts Table Card -->
            <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/90 dark:border-zinc-800 overflow-hidden">
                
                @if(count($contacts) > 0)
                    <!-- Top Toolbar: Search Bar + 1-Click Tag Filters -->
                    <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 space-y-4 bg-slate-50/80 dark:bg-[#09090B]/60">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="relative flex-1 max-w-md">
                                <i class="fas fa-search absolute left-3.5 top-3 text-zinc-400 dark:text-zinc-400 text-sm"></i>
                                <input type="text" id="contactSearchInput" placeholder="Search contacts by name, email, company, or tag..." 
                                       class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#09090B] border border-zinc-300 dark:border-zinc-800 rounded-xl text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-300/80 focus:border-amber-300/80 transition-all">
                            </div>
                            <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400" id="contactCountDisplay">
                                Showing {{ min(15, count($contacts)) }} of {{ count($contacts) }} contacts
                            </div>
                        </div>

                        <!-- 1-Click Tag Filter Chips -->
                        @if(count($allTags) > 0)
                            <div class="flex items-center gap-1.5 overflow-x-auto pt-1 pb-1">
                                <span class="text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider shrink-0 mr-1">
                                    <i class="fas fa-filter text-3xs mr-1 text-amber-500"></i> Filter Tag:
                                </span>
                                <button type="button" data-tag="all" class="tag-filter-btn px-3 py-1 rounded-lg text-xs font-bold transition-all bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 shadow-2xs shrink-0">
                                    All ({{ count($contacts) }})
                                </button>
                                @foreach($allTags as $tagLabel => $count)
                                    <button type="button" data-tag="{{ strtolower($tagLabel) }}" class="tag-filter-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 shadow-2xs shrink-0 flex items-center gap-1.5">
                                        <span>{{ $tagLabel }}</span>
                                        <span class="text-3xs px-1.5 py-0.2 rounded-full bg-slate-100 dark:bg-zinc-800 text-zinc-500">{{ $count }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Modern High-Density Table Layout -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-zinc-200/80 dark:border-zinc-800 bg-slate-100/60 dark:bg-[#09090B]/80 text-3xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    <th class="py-3.5 pl-6 pr-4">Subscriber</th>
                                    <th class="py-3.5 px-4">Contact Info</th>
                                    <th class="py-3.5 px-4">Company</th>
                                    <th class="py-3.5 px-4">Assigned Tags</th>
                                    <th class="py-3.5 pl-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="contactsListContainer" class="divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
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

                                    <tr class="contact-row hover:bg-slate-50/90 dark:hover:bg-zinc-800/40 transition-colors group"
                                        data-search="{{ $searchString }}" data-tags="{{ strtolower($tagsString) }}" data-index="{{ $index }}">
                                        
                                        <!-- Subscriber (Avatar + Name) -->
                                        <td class="py-3.5 pl-6 pr-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-9 h-9 rounded-xl bg-zinc-900 dark:bg-amber-100 text-white dark:text-zinc-950 flex items-center justify-center text-xs font-black shrink-0 shadow-2xs">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <a href="/contact-form/{{ $c->id }}" class="font-bold text-sm text-zinc-900 dark:text-white hover:text-zinc-600 dark:hover:text-amber-200 transition-colors block">
                                                        {{ $fullName }}
                                                    </a>
                                                    <span class="text-3xs text-zinc-400 dark:text-zinc-500">
                                                        Updated {{ $c->updated_at ? \Carbon\Carbon::parse($c->updated_at)->diffForHumans() : 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Contact Info (Email + Mobile) -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="space-y-0.5">
                                                <a href="mailto:{{ $c->email }}" class="inline-flex items-center text-zinc-800 dark:text-zinc-200 hover:text-amber-600 dark:hover:text-amber-200 font-medium transition-colors">
                                                    <i class="fas fa-envelope mr-1.5 text-3xs text-amber-500"></i>
                                                    {{ $c->email }}
                                                </a>
                                                @if(!empty($c->mobile))
                                                    <div class="text-3xs text-zinc-500 font-mono flex items-center">
                                                        <i class="fas fa-phone mr-1.5 text-3xs opacity-60"></i>
                                                        {{ $c->mobile }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Company -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if(!empty($c->company))
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-3xs font-semibold bg-slate-100 text-slate-700 dark:bg-zinc-800 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700">
                                                    <i class="fas fa-building mr-1.5 text-3xs opacity-60"></i>
                                                    {{ $c->company }}
                                                </span>
                                            @else
                                                <span class="text-zinc-400 text-3xs italic">—</span>
                                            @endif
                                        </td>

                                        <!-- Assigned Tags -->
                                        <td class="py-3.5 px-4">
                                            @if(count($tagNames) > 0)
                                                <div class="flex flex-wrap items-center gap-1.5 max-w-xs">
                                                    @foreach($tagNames as $tagLabel)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-amber-50/90 text-amber-900 border border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-500/30 whitespace-nowrap">
                                                            <i class="fas fa-tag mr-1 text-3xs text-amber-600 dark:text-amber-300"></i>
                                                            {{ $tagLabel }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-zinc-400 text-3xs italic">No tags</span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-3.5 pl-4 pr-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end space-x-1.5">
                                                <a href="/contact-form/{{ $c->id }}" title="Edit Contact" 
                                                   class="p-1.5 text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-amber-200 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                                    <i class="fas fa-pencil-alt text-xs"></i>
                                                </a>
                                                <button type="button" onclick="confirmDelete({{ $c->id }}, '{{ addslashes($fullName) }}', '{{ addslashes($c->email ?? '') }}')" title="Delete Contact" 
                                                        class="p-1.5 text-zinc-600 hover:text-red-600 dark:text-zinc-300 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                                    <i class="fas fa-trash-alt text-xs"></i>
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
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-[#09090B] text-zinc-400 dark:text-zinc-400 flex items-center justify-center mx-auto mb-3 text-xl">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="text-base font-bold text-zinc-900 dark:text-white">No matching contacts found</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Try adjusting your search criteria or tag filter.</p>
                    </div>

                    <!-- Progressive Load More Pagination Footer -->
                    @if(count($contacts) > 15)
                        <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 text-center bg-slate-50/60 dark:bg-[#09090B]/50">
                            <button type="button" id="showMoreBtn" 
                                    class="inline-flex items-center justify-center px-6 py-2.5 bg-white dark:bg-[#141417] text-zinc-800 dark:text-zinc-200 font-bold text-sm rounded-xl border border-zinc-300 dark:border-zinc-800 shadow-2xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all duration-200 gap-2">
                                <i class="fas fa-chevron-down text-xs"></i>
                                <span id="remainingCountText">Show More Contacts</span>
                            </button>
                        </div>
                    @endif

                @else
                    <div class="text-center py-14 px-4">
                        <div class="w-16 h-16 bg-amber-50/80 dark:bg-amber-950/30 text-amber-800 dark:text-amber-300 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl border border-amber-200/60 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white">No Contacts Added Yet</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 max-w-md mx-auto mt-2 mb-6 leading-relaxed">
                            Start building your subscriber list by creating contacts manually or uploading a bulk CSV list.
                        </p>
                        <div class="flex items-center justify-center gap-3">
                            <a href="/import-contact-form" class="inline-flex items-center px-4 py-2.5 bg-slate-100 dark:bg-[#141417] text-zinc-700 dark:text-zinc-200 text-sm font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-800 transition-colors gap-2">
                                <i class="fas fa-file-import text-amber-500 text-xs"></i>
                                <span>Import CSV</span>
                            </a>
                            <a href="/contact-form/new" class="inline-flex items-center px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-sm font-bold rounded-xl shadow-2xs transition-all duration-200 gap-2">
                                <i class="fas fa-plus text-xs"></i>
                                <span>Add New Contact</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Reusable Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-zinc-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#141417] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-zinc-800 transform transition-all">
            <div class="flex items-center space-x-3 text-red-600 dark:text-red-400 mb-3">
                <div class="p-3 bg-red-100 dark:bg-red-950 rounded-full border border-red-200 dark:border-red-800">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Delete Contact</h3>
            </div>
            <p class="text-sm text-zinc-600 dark:text-zinc-300 mb-6 leading-relaxed">
                Are you sure you want to delete <span id="deleteContactName" class="font-bold text-zinc-900 dark:text-white"></span>? 
                This contact will be permanently removed from all subscriber broadcast lists.
            </p>
            <form method="POST" action="/contact/delete">
                @csrf
                <input type="hidden" name="contact_id" id="modalContactId">
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-slate-100 dark:bg-zinc-800 rounded-xl hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-2xs">
                        Delete Contact
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
