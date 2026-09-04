@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageSize = 15;
        let visibleCount = pageSize;
        const allRows = Array.from(document.querySelectorAll('.newsletter-row'));
        const searchInput = document.getElementById('newsletterSearchInput');
        const showMoreBtn = document.getElementById('showMoreBtn');
        const countDisplay = document.getElementById('newsletterCountDisplay');
        const emptySearchResults = document.getElementById('emptySearchResults');
        const statusFilterButtons = Array.from(document.querySelectorAll('.status-filter-btn'));
        let selectedStatusFilter = 'all';

        function updateListVisibility() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let matchingRows = [];

            allRows.forEach(row => {
                const searchData = (row.getAttribute('data-search') || '').toLowerCase();
                const rowStatus = row.getAttribute('data-status') || '';

                const matchesQuery = (query === '' || searchData.includes(query));
                const matchesStatus = (selectedStatusFilter === 'all' || rowStatus === selectedStatusFilter);

                if (matchesQuery && matchesStatus) {
                    matchingRows.push(row);
                } else {
                    row.classList.add('hidden');
                }
            });

            if (query !== '' || selectedStatusFilter !== 'all') {
                matchingRows.forEach((row, idx) => {
                    if (idx < visibleCount) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
                if (countDisplay) countDisplay.textContent = `Showing ${Math.min(visibleCount, matchingRows.length)} of ${matchingRows.length} matching broadcasts`;
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
                    countDisplay.textContent = `Showing ${currentlyShown} of ${total} newsletters`;
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

        statusFilterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                statusFilterButtons.forEach(b => {
                    b.classList.remove('bg-zinc-900', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'shadow-xs');
                    b.classList.add('text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-800');
                });
                this.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-800');
                this.classList.add('bg-zinc-900', 'text-white', 'dark:bg-white', 'dark:text-zinc-950', 'shadow-xs');

                selectedStatusFilter = this.getAttribute('data-status') || 'all';
                visibleCount = pageSize;
                updateListVisibility();
            });
        });

        updateListVisibility();
    });

    function confirmDelete(id, title) {
        document.getElementById('modalNewsletterId').value = id;
        document.getElementById('deleteNewsletterTitle').textContent = title;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function queueSingleNewsletter(id, title) {
        if (confirm(`Queue "${title}" for transmission right now?`)) {
            fetch(`/newsletter/${id}/queue-now`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to queue broadcast.');
                }
            })
            .catch(err => {
                alert('Error: ' + err.message);
            });
        }
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
                        <i class="fas fa-paper-plane text-amber-500"></i>
                        <span>{{ __('Email Newsletters & Broadcasts') }}</span>
                    </h2>
                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80 uppercase">
                        {{ count($newsletters) }} Total
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 pl-5">
                    Compose, schedule, personalize merge tags, and monitor broadcast dispatches.
                </p>
            </div>
            
            <div class="flex items-center gap-2.5">
                <a href="/dispatch" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <i class="fas fa-layer-group text-3xs text-amber-500"></i>
                    <span>Dispatch Studio</span>
                </a>
                <a href="/newsletter-form/new" class="group inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 gap-2">
                    <i class="fas fa-plus text-3xs transition-transform group-hover:rotate-90"></i>
                    <span>Create Newsletter</span>
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
                $totalNewsletters = count($newsletters);
                $totalSent = $newsletters->sum('sent_mails_count');
                $totalQueued = $newsletters->sum('queued_mails_count');
                $readyCount = $newsletters->where('status', 'N')->count();
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Metric 1: Total Broadcasts -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-amber-500/40 dark:hover:border-amber-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Total Broadcasts
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs border border-amber-200/50 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                            {{ $totalNewsletters }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Created email broadcasts
                        </p>
                    </div>
                </div>

                <!-- Metric 2: Delivered Messages -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-emerald-500/40 dark:hover:border-emerald-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Delivered Emails
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs border border-emerald-200/50 dark:border-emerald-500/20 shadow-2xs">
                            <i class="fas fa-circle-check"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight">
                            {{ number_format($totalSent) }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Successfully transmitted dispatches
                        </p>
                    </div>
                </div>

                <!-- Metric 3: In Queue / Pending -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-blue-500/40 dark:hover:border-blue-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            In Transmission Queue
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs border border-blue-200/50 dark:border-blue-500/20 shadow-2xs">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">
                            {{ number_format($totalQueued) }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Awaiting socket delivery
                        </p>
                    </div>
                </div>

                <!-- Metric 4: Ready to Broadcast -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-purple-500/40 dark:hover:border-purple-500/30 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Ready to Send
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs border border-purple-200/50 dark:border-purple-500/20 shadow-2xs">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 tracking-tight">
                            {{ $readyCount }}
                        </div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-2">
                            Configured broadcasts in 'Ready' status
                        </p>
                    </div>
                </div>
            </div>

            <!-- Explanatory Guidance Banner Card -->
            <div class="relative overflow-hidden rounded-2xl border border-amber-200/90 dark:border-amber-500/20 bg-gradient-to-r from-amber-500/[0.08] via-amber-400/[0.03] to-amber-500/[0.06] dark:from-[#111114] dark:via-zinc-900/80 dark:to-[#111114] p-5 sm:p-6 shadow-xs dark:shadow-2xs backdrop-blur-sm transition-all">
                <div class="absolute -right-6 -bottom-6 opacity-10 dark:opacity-5 text-9xl pointer-events-none transform -rotate-12 select-none">
                    <i class="fas fa-paper-plane text-amber-500 dark:text-amber-300"></i>
                </div>
                <div class="flex items-start gap-4 sm:gap-5 relative z-10">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-100/90 text-amber-800 border border-amber-200/90 dark:bg-amber-400/10 dark:text-amber-300 dark:border-amber-400/20 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="font-bold text-sm sm:text-base text-zinc-900 dark:text-white flex items-center gap-2">
                            Broadcast Lifecycle & Delivery Engine
                            <span class="w-2 h-2 rounded-full bg-amber-500 dark:bg-amber-300"></span>
                        </h4>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            Draft rich HTML content, personalize subject lines with merge tags (e.g. <code class="px-1.5 py-0.5 rounded bg-amber-100/80 dark:bg-zinc-800 text-amber-900 dark:text-amber-300 font-mono text-xs">[[firstname]]</code>), select audience target tags, and transition your broadcast to <strong>Ready / New</strong> to queue socket transmission.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Newsletters Directory Card -->
            <div class="bg-white dark:bg-[#111114] rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs overflow-hidden">
                
                @if(count($newsletters) > 0)
                    <!-- Top Toolbar & Filter Strip -->
                    <div class="p-4 sm:p-5 border-b border-zinc-100 dark:border-zinc-800/80 space-y-4 bg-zinc-50/50 dark:bg-zinc-900/30">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Search Bar -->
                            <div class="relative flex-1 max-w-md">
                                <i class="fas fa-search absolute left-3.5 top-3 text-zinc-400 text-xs"></i>
                                <input type="text" id="newsletterSearchInput" placeholder="Search broadcasts by title, campaign, or subject..." 
                                       class="w-full pl-9 pr-4 py-2 bg-white dark:bg-[#09090B] border border-zinc-200/90 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs">
                            </div>

                            <div class="text-3xs font-mono text-zinc-400 dark:text-zinc-500" id="newsletterCountDisplay">
                                Showing {{ min(15, count($newsletters)) }} of {{ count($newsletters) }} newsletters
                            </div>
                        </div>

                        <!-- 1-Click Status Filter Pills -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pt-1 pb-1 scrollbar-none">
                            <span class="text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider shrink-0 mr-1 flex items-center gap-1">
                                <i class="fas fa-filter text-amber-500 text-4xs"></i> Status:
                            </span>
                            <button type="button" data-status="all" class="status-filter-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-xs shrink-0">
                                All ({{ count($newsletters) }})
                            </button>
                            <button type="button" data-status="D" class="status-filter-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 shrink-0 flex items-center gap-1.5">
                                <span>Draft</span>
                                <span class="text-3xs px-1.5 py-0.2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500">{{ $newsletters->where('status', 'D')->count() }}</span>
                            </button>
                            <button type="button" data-status="N" class="status-filter-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 shrink-0 flex items-center gap-1.5">
                                <span>Ready / New</span>
                                <span class="text-3xs px-1.5 py-0.2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500">{{ $newsletters->where('status', 'N')->count() }}</span>
                            </button>
                            <button type="button" data-status="Q" class="status-filter-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 shrink-0 flex items-center gap-1.5">
                                <span>Queuing</span>
                                <span class="text-3xs px-1.5 py-0.2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500">{{ $newsletters->where('status', 'Q')->count() }}</span>
                            </button>
                            <button type="button" data-status="S" class="status-filter-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 shrink-0 flex items-center gap-1.5">
                                <span>Sent</span>
                                <span class="text-3xs px-1.5 py-0.2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500">{{ $newsletters->where('status', 'S')->count() }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Modern Responsive Broadcasts Deck (Zero Horizontal Scroll) -->
                    <div id="newslettersListContainer" class="divide-y divide-zinc-100 dark:divide-zinc-800/70">
                        @foreach ($newsletters as $index => $n)
                            @php
                                $tagLabels = [];
                                if ($n->newsletter_tags) {
                                    foreach($n->newsletter_tags as $nt) {
                                        if ($nt->tag) $tagLabels[] = $nt->tag->label;
                                    }
                                }
                                $tagsString = implode(' ', $tagLabels);
                                $campaignName = $n->campaign->name ?? 'Unassigned Campaign';
                                $searchString = strtolower(($n->title ?? '') . ' ' . $campaignName . ' ' . ($n->subject_template ?? '') . ' ' . $tagsString);
                                
                                $statusConfig = [
                                    'D' => ['label' => 'Draft', 'class' => 'bg-zinc-100 text-zinc-600 border-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700', 'icon' => 'fa-pen'],
                                    'N' => ['label' => 'Ready / New', 'class' => 'bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800', 'icon' => 'fa-sparkles'],
                                    'Q' => ['label' => 'Queuing', 'class' => 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-200 border-amber-200 dark:border-amber-800', 'icon' => 'fa-arrows-rotate fa-spin'],
                                    'S' => ['label' => 'Sent', 'class' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800', 'icon' => 'fa-check'],
                                ];
                                $currStatus = $statusConfig[$n->status] ?? ['label' => $n->status, 'class' => 'bg-zinc-100 text-zinc-600', 'icon' => 'fa-circle'];
                            @endphp

                            <div class="newsletter-row p-4 sm:p-5 hover:bg-zinc-50/70 dark:hover:bg-zinc-900/40 transition-colors flex flex-col lg:flex-row lg:items-center justify-between gap-4"
                                 data-search="{{ $searchString }}" data-status="{{ $n->status }}" data-index="{{ $index }}">
                                
                                <!-- Left Info Pod -->
                                <div class="flex items-start space-x-3.5 min-w-0 flex-1">
                                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-300 border border-amber-500/20 flex items-center justify-center text-sm font-bold shrink-0 shadow-2xs mt-0.5 sm:mt-0">
                                        <i class="fas fa-paper-plane"></i>
                                    </div>

                                    <div class="min-w-0 flex-1 space-y-1.5">
                                        <!-- Title + Status + Campaign -->
                                        <div class="flex items-center flex-wrap gap-2">
                                            <a href="/newsletter-form/{{ $n->id }}" class="font-bold text-sm text-zinc-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-300 transition-colors">
                                                {{ $n->title ?? 'Untitled Broadcast' }}
                                            </a>

                                            <!-- Status Badge -->
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-bold border {{ $currStatus['class'] }}">
                                                <i class="fas {{ $currStatus['icon'] }} mr-1 text-4xs"></i>
                                                {{ $currStatus['label'] }}
                                            </span>

                                            <!-- Campaign Pill -->
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-3xs font-medium bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700/60">
                                                <i class="fas fa-folder mr-1 text-4xs text-amber-500"></i>
                                                {{ $campaignName }}
                                            </span>
                                        </div>

                                        <!-- Subject Line Preview -->
                                        <div class="text-xs text-zinc-600 dark:text-zinc-300 font-medium flex items-center gap-1.5 min-w-0">
                                            <i class="fas fa-envelope-open-text text-amber-500 text-3xs shrink-0"></i>
                                            <span class="truncate">{{ $n->subject_template ?? 'No subject specified' }}</span>
                                        </div>

                                        <!-- Target Tags & Updated Meta -->
                                        <div class="flex items-center flex-wrap gap-x-3 gap-y-1 text-3xs text-zinc-500 dark:text-zinc-400">
                                            <!-- Target Tags -->
                                            <div class="flex items-center flex-wrap gap-1">
                                                <span class="text-zinc-400 font-medium flex items-center gap-1">
                                                    <i class="fas fa-tags text-4xs text-amber-500"></i> Audience:
                                                </span>
                                                @if(count($tagLabels) > 0)
                                                    @foreach($tagLabels as $tagLabel)
                                                        <span class="inline-flex items-center px-2 py-0.2 rounded-full text-3xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-500/30">
                                                            {{ $tagLabel }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-zinc-400 italic">All contacts</span>
                                                @endif
                                            </div>

                                            <span class="text-zinc-300 dark:text-zinc-700">•</span>

                                            <!-- Updated Time -->
                                            <span class="flex items-center gap-1 text-zinc-400">
                                                <i class="fas fa-clock text-4xs"></i>
                                                Updated {{ $n->updated_at ? \Carbon\Carbon::parse($n->updated_at)->diffForHumans(null, true) : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Stats & Actions Pod -->
                                <div class="flex items-center flex-wrap sm:flex-nowrap gap-3 self-end lg:self-center shrink-0 w-full lg:w-auto justify-between lg:justify-end border-t lg:border-t-0 pt-3 lg:pt-0 border-zinc-100 dark:border-zinc-800/80">
                                    <!-- Delivery Stats -->
                                    <div class="flex items-center gap-2">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/70 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-300" title="Delivered Dispatches">
                                            <i class="fas fa-circle-check text-4xs text-emerald-600 dark:text-emerald-400"></i>
                                            <span class="text-3xs font-bold">{{ number_format($n->sent_mails_count ?? 0) }} sent</span>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/50 border border-blue-200/70 dark:border-blue-800/80 text-blue-800 dark:text-blue-300" title="Queued Dispatches">
                                            <i class="fas fa-clock text-4xs text-blue-600 dark:text-blue-400"></i>
                                            <span class="text-3xs font-bold">{{ number_format($n->queued_mails_count ?? 0) }} queued</span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        @if($n->status === 'N' || $n->status === 'D')
                                            <button type="button" onclick="queueSingleNewsletter({{ $n->id }}, '{{ addslashes($n->title ?? 'Broadcast') }}')" title="Queue this broadcast now"
                                                    class="px-3 py-1.5 text-3xs font-bold rounded-xl bg-amber-500 hover:bg-amber-400 text-zinc-950 transition-all shadow-2xs hover:shadow-xs flex items-center gap-1 cursor-pointer">
                                                <i class="fas fa-paper-plane text-3xs"></i>
                                                <span>Queue</span>
                                            </button>
                                        @endif
                                        <a href="/newsletter-form/{{ $n->id }}" title="Edit Newsletter" 
                                           class="px-2.5 py-1.5 text-3xs font-semibold text-zinc-700 dark:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-xl transition-colors flex items-center gap-1">
                                            <i class="fas fa-pen text-3xs text-zinc-400"></i>
                                            <span class="hidden sm:inline">Edit</span>
                                        </a>
                                        <button type="button" onclick="confirmDelete({{ $n->id }}, '{{ addslashes($n->title ?? 'Untitled Broadcast') }}')" title="Delete Newsletter" 
                                                class="p-1.5 text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-colors">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <!-- Search Empty State -->
                    <div id="emptySearchResults" class="hidden p-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 flex items-center justify-center mx-auto mb-3 text-lg">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">No matching newsletters found</h4>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Try adjusting your search criteria or status filter.</p>
                    </div>

                    <!-- Progressive Load More Pagination Footer -->
                    @if(count($newsletters) > 15)
                        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800/80 text-center bg-zinc-50/50 dark:bg-zinc-900/30">
                            <button type="button" id="showMoreBtn" 
                                    class="inline-flex items-center justify-center px-5 py-2 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-2xs hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all gap-2">
                                <i class="fas fa-chevron-down text-3xs"></i>
                                <span id="remainingCountText">Show More Newsletters</span>
                            </button>
                        </div>
                    @endif

                @else
                    <div class="text-center py-16 px-4">
                        <div class="w-14 h-14 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl border border-amber-200/60 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">No Newsletters Created Yet</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto mt-1 mb-5 leading-relaxed">
                            Create your first email broadcast message with personalized merge tags, rich HTML layouts, and audience tag filters.
                        </p>
                        <a href="/newsletter-form/new" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-md transition-all gap-2">
                            <i class="fas fa-plus text-3xs"></i>
                            <span>Create First Newsletter</span>
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
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Delete Newsletter</h3>
                    <p class="text-3xs text-zinc-400">Remove broadcast message</p>
                </div>
            </div>
            <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                Are you sure you want to delete <span id="deleteNewsletterTitle" class="font-bold text-zinc-900 dark:text-white"></span>? 
                This will permanently remove the broadcast draft and delivery history.
            </p>
            <form method="POST" action="/newsletter/delete">
                @csrf
                <input type="hidden" name="newsletter_id" id="modalNewsletterId">
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl transition-colors shadow-2xs">
                        Delete Newsletter
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
