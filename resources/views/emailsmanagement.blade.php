@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<link rel="stylesheet" href="/css/jquery.dataTables.min.css" />
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        const statusFilterButtons = Array.from(document.querySelectorAll('.email-status-btn'));
        const searchInput = document.getElementById('emailSearchInput');
        const showMoreBtn = document.getElementById('showMoreBtn');
        const countDisplay = document.getElementById('emailCountDisplay');
        const remainingCountText = document.getElementById('remainingCountText');
        const showMoreContainer = document.getElementById('showMoreContainer');
        const emptySearchResults = document.getElementById('emptySearchResults');
        const tableContainer = document.getElementById('tableContainer');

        const pageSize = 15;
        let currentLength = pageSize;
        let currentStatus = 'all';

        const table = $('#emails').DataTable({
            processing: true,
            serverSide: true,
            dom: 't', // Only show table, custom controls used outside
            ajax: {
                url: "{{ route('emails.data') }}",
                type: "GET",
                data: function (d) {
                    d.status = currentStatus;
                    d.search = { value: searchInput ? searchInput.value.trim() : '' };
                }
            },
            pageLength: currentLength,
            order: [[4, 'desc']],
            autoWidth: false,
            columns: [
                {
                    data: 'subject',
                    name: 'subject',
                    render: function (data) {
                        return `<div class="flex items-center space-x-2.5 py-1">
                                    <div class="w-8 h-8 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 text-amber-700 dark:text-amber-200 border border-amber-200/60 dark:border-amber-500/20 flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="font-bold text-sm text-zinc-900 dark:text-white max-w-sm truncate">
                                        ${data || 'No Subject'}
                                    </div>
                                </div>`;
                    }
                },
                {
                    data: 'campaign_name',
                    name: 'campaign_name',
                    render: function (data) {
                        if (!data) return '<span class="text-zinc-400 text-3xs italic">—</span>';
                        return `<span class="inline-flex items-center px-2 py-0.5 rounded-md text-3xs font-semibold bg-slate-100 text-slate-700 dark:bg-zinc-800 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700">
                                    <i class="fas fa-folder mr-1 text-3xs opacity-60"></i>${data}
                                </span>`;
                    }
                },
                {
                    data: 'recipient',
                    name: 'recipient',
                    render: function (data) {
                        if (!data) return '<span class="text-zinc-400 text-3xs italic">Unknown</span>';
                        return `<a href="mailto:${data}" class="inline-flex items-center text-zinc-800 dark:text-zinc-200 hover:text-amber-600 dark:hover:text-amber-200 font-medium transition-colors">
                                    <i class="fas fa-user-circle mr-1.5 text-3xs text-amber-500"></i>${data}
                                </a>`;
                    }
                },
                {
                    data: 'sender_mail_account',
                    name: 'sender_mail_account',
                    render: function (data) {
                        if (!data || data === 'Not assigned yet') {
                            return `<span class="inline-flex items-center px-2 py-0.5 rounded-md text-3xs font-semibold bg-slate-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 border border-slate-200 dark:border-zinc-700">
                                        <i class="fas fa-clock mr-1 text-3xs opacity-60"></i>Pending Queue
                                    </span>`;
                        }
                        return `<span class="inline-flex items-center px-2 py-0.5 rounded-md text-3xs font-semibold bg-emerald-50 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <i class="fas fa-server mr-1 text-3xs opacity-70"></i>${data}
                                </span>`;
                    }
                },
                {
                    data: 'timestamp',
                    name: 'timestamp',
                    render: function (data) {
                        if (!data) return '<span class="text-zinc-400 text-3xs italic">N/A</span>';
                        const d = new Date(data);
                        return `<span class="text-zinc-600 dark:text-zinc-400 font-mono text-3xs">${d.toLocaleString()}</span>`;
                    }
                }
            ],
            drawCallback: function (settings) {
                const total = settings._iRecordsDisplay || 0;
                const shown = Math.min(currentLength, total);

                if (countDisplay) {
                    countDisplay.textContent = `Showing ${shown} of ${total} emails`;
                }

                if (emptySearchResults && tableContainer) {
                    if (total === 0) {
                        emptySearchResults.classList.remove('hidden');
                        tableContainer.classList.add('hidden');
                    } else {
                        emptySearchResults.classList.add('hidden');
                        tableContainer.classList.remove('hidden');
                    }
                }

                if (showMoreContainer) {
                    if (shown >= total || total === 0) {
                        showMoreContainer.style.display = 'none';
                    } else {
                        showMoreContainer.style.display = 'block';
                        const remaining = total - shown;
                        if (remainingCountText) {
                            remainingCountText.textContent = `Show More (${remaining} remaining)`;
                        }
                    }
                }
            }
        });

        if (showMoreBtn) {
            showMoreBtn.addEventListener('click', function () {
                currentLength += pageSize;
                table.page.len(currentLength).draw(false);
            });
        }

        let searchTimeout = null;
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function () {
                    currentLength = pageSize;
                    table.page.len(currentLength).draw();
                }, 300);
            });
        }

        statusFilterButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                statusFilterButtons.forEach(b => {
                    b.classList.remove('bg-zinc-900', 'text-white', 'dark:bg-amber-100', 'dark:text-zinc-950');
                    b.classList.add('bg-white', 'dark:bg-[#141417]', 'text-zinc-700', 'dark:text-zinc-300');
                });
                this.classList.remove('bg-white', 'dark:bg-[#141417]', 'text-zinc-700', 'dark:text-zinc-300');
                this.classList.add('bg-zinc-900', 'text-white', 'dark:bg-amber-100', 'dark:text-zinc-950');

                currentStatus = this.getAttribute('data-status') || 'all';
                currentLength = pageSize;
                table.page.len(currentLength).draw();
            });
        });

        window.reloadEmailTable = function () {
            currentLength = pageSize;
            table.page.len(currentLength).draw();
        };
    });
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas fa-inbox text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ __('Dispatched Emails & Delivery Logs') }}
                    </h2>
                </div>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1 pl-5">
                    Monitor real-time outgoing email delivery, queue status, and recipient dispatch logs.
                </p>
            </div>
            <div>
                <button type="button" onclick="reloadEmailTable()" class="inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-[#141417] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-800 transition-colors gap-2 shadow-2xs">
                    <i class="fas fa-sync-alt text-amber-500 text-xs"></i>
                    <span>Refresh Logs</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="pb-8 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Metrics Overview Cards -->
            @php
                $sentCount = \App\Models\SentMail::count();
                $queuedCount = \App\Models\MailQueue::where('status', 'Q')->whereNull('error')->count();
                $failedCount = \App\Models\MailQueue::where('status', 'Q')->whereNotNull('error')->count();
                $totalLogged = $sentCount + $queuedCount + $failedCount;
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-white via-white to-slate-50/90 dark:from-[#141417] dark:to-[#141417] p-5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs flex items-center justify-between transition-all duration-300 hover:border-amber-300/40">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Logged</p>
                        <h3 class="text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $totalLogged }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-100/70 dark:bg-amber-950/30 text-amber-900 dark:text-amber-300 border border-amber-200/80 dark:border-amber-500/20 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-white via-white to-slate-50/90 dark:from-[#141417] dark:to-[#141417] p-5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs flex items-center justify-between transition-all duration-300 hover:border-emerald-300/40">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Delivered (Sent)</p>
                        <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $sentCount }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/70 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-white via-white to-slate-50/90 dark:from-[#141417] dark:to-[#141417] p-5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs flex items-center justify-between transition-all duration-300 hover:border-blue-300/40">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">In Queue</p>
                        <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $queuedCount }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/70 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800/70 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-white via-white to-slate-50/90 dark:from-[#141417] dark:to-[#141417] p-5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs flex items-center justify-between transition-all duration-300 hover:border-red-300/40">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Failed / Errors</p>
                        <h3 class="text-2xl font-black {{ $failedCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-white' }} mt-1">{{ $failedCount }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-red-50 dark:bg-red-950/70 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/70 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

            <!-- Guidance Banner Card -->
            <div class="relative overflow-hidden rounded-2xl border border-amber-200/80 dark:border-amber-500/20 bg-gradient-to-r from-amber-500/[0.08] via-amber-400/[0.03] to-amber-500/[0.06] dark:from-[#141417] dark:via-zinc-900/90 dark:to-[#141417] p-5 sm:p-6 shadow-[0_4px_20px_-4px_rgba(245,158,11,0.08)] dark:shadow-2xs backdrop-blur-sm transition-all duration-300">
                <div class="absolute -right-6 -bottom-6 opacity-15 dark:opacity-10 text-9xl pointer-events-none transform -rotate-12 select-none">
                    <i class="fas fa-mail-bulk text-amber-500 dark:text-amber-300"></i>
                </div>
                <div class="flex items-start gap-4 sm:gap-5 relative z-10">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-100/90 text-amber-800 border border-amber-200/90 dark:bg-amber-400/10 dark:text-amber-300 dark:border-amber-400/20 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="font-bold text-base sm:text-lg text-zinc-900 dark:text-white flex items-center gap-2">
                            Delivery Audit Trail &amp; SMTP Engine Logs
                            <span class="w-2 h-2 rounded-full bg-amber-500 dark:bg-amber-300"></span>
                        </h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            Every email generated by the queue dispatcher is tracked with full recipient information, outbound mail account assignment, and delivery timestamps. Failed deliveries capture exact SMTP server response codes.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main High-Density Server-Side Data Table Container -->
            <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/90 dark:border-zinc-800 overflow-hidden">
                
                <!-- Top Toolbar: Search Bar + 1-Click Status Filter -->
                <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 space-y-4 bg-slate-50/80 dark:bg-[#09090B]/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="relative flex-1 max-w-md">
                            <i class="fas fa-search absolute left-3.5 top-3 text-zinc-400 dark:text-zinc-400 text-sm"></i>
                            <input type="text" id="emailSearchInput" placeholder="Search email logs by subject, campaign, or recipient..." 
                                   class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#09090B] border border-zinc-300 dark:border-zinc-800 rounded-xl text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-300/80 focus:border-amber-300/80 transition-all">
                        </div>
                        <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400" id="emailCountDisplay">
                            Showing 0 of 0 emails
                        </div>
                    </div>

                    <!-- 1-Click Status Filter Bar -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pt-1 pb-1">
                        <span class="text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider shrink-0 mr-1">
                            <i class="fas fa-filter text-3xs mr-1 text-amber-500"></i> Filter:
                        </span>
                        <button type="button" data-status="all" class="email-status-btn px-3 py-1 rounded-lg text-xs font-bold transition-all bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 shadow-2xs shrink-0">
                            All Dispatches
                        </button>
                        <button type="button" data-status="sent" class="email-status-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 shadow-2xs shrink-0 flex items-center gap-1.5">
                            <i class="fas fa-check-circle text-emerald-500 text-3xs"></i>
                            <span>Sent / Delivered</span>
                            <span class="text-3xs px-1.5 py-0.2 rounded-full bg-slate-100 dark:bg-zinc-800 text-zinc-500">{{ $sentCount }}</span>
                        </button>
                        <button type="button" data-status="queued" class="email-status-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 shadow-2xs shrink-0 flex items-center gap-1.5">
                            <i class="fas fa-clock text-blue-500 text-3xs"></i>
                            <span>Queued</span>
                            <span class="text-3xs px-1.5 py-0.2 rounded-full bg-slate-100 dark:bg-zinc-800 text-zinc-500">{{ $queuedCount }}</span>
                        </button>
                        <button type="button" data-status="failed" class="email-status-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 shadow-2xs shrink-0 flex items-center gap-1.5">
                            <i class="fas fa-exclamation-triangle text-red-500 text-3xs"></i>
                            <span>Failed / Errors</span>
                            <span class="text-3xs px-1.5 py-0.2 rounded-full bg-slate-100 dark:bg-zinc-800 text-zinc-500">{{ $failedCount }}</span>
                        </button>
                    </div>
                </div>

                <!-- Server-Side DataTable Container -->
                <div id="tableContainer" class="overflow-x-auto">
                    <table id="emails" class="w-full text-left border-collapse text-xs" style="width:100%">
                        <thead>
                            <tr class="border-b border-zinc-200/80 dark:border-zinc-800 bg-slate-100/60 dark:bg-[#09090B]/80 text-3xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                <th class="py-3.5 pl-6 pr-4">Subject</th>
                                <th class="py-3.5 px-4">Campaign</th>
                                <th class="py-3.5 px-4">Recipient</th>
                                <th class="py-3.5 px-4">Sender Mail Account</th>
                                <th class="py-3.5 pl-4 pr-6">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        </tbody>
                    </table>
                </div>

                <!-- Search Empty State -->
                <div id="emptySearchResults" class="hidden p-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-[#09090B] text-zinc-400 dark:text-zinc-400 flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 class="text-base font-bold text-zinc-900 dark:text-white">No matching emails found</h4>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Try adjusting your search query or status filter.</p>
                </div>

                <!-- Progressive "Show More" Pagination Footer (Consistent with Contacts/Newsletters/Tags) -->
                <div id="showMoreContainer" class="p-6 border-t border-zinc-100 dark:border-zinc-800 text-center bg-slate-50/60 dark:bg-[#09090B]/50">
                    <button type="button" id="showMoreBtn" 
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-white dark:bg-[#141417] text-zinc-800 dark:text-zinc-200 font-bold text-sm rounded-xl border border-zinc-300 dark:border-zinc-800 shadow-2xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all duration-200 gap-2">
                        <i class="fas fa-chevron-down text-xs"></i>
                        <span id="remainingCountText">Show More Emails</span>
                    </button>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>