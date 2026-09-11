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
                    render: function (data, type, row) {
                        return `<div class="flex items-center space-x-2.5 py-1">
                                    <div class="w-8 h-8 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 text-amber-700 dark:text-amber-200 border border-amber-200/60 dark:border-amber-500/20 flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="font-bold text-sm text-zinc-900 dark:text-white max-w-sm truncate cursor-pointer hover:text-amber-600 dark:hover:text-amber-300 transition-colors" onclick="inspectEmail('${row.id}')">
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
                    render: function (data, type, row) {
                        if (!data) return '<span class="text-zinc-400 text-3xs italic">Unknown</span>';
                        const companyInfo = row.recipient_company ? `<span class="text-3xs text-zinc-400">(${row.recipient_company})</span>` : '';
                        return `<div>
                                    <a href="mailto:${data}" class="inline-flex items-center text-zinc-800 dark:text-zinc-200 hover:text-amber-600 dark:hover:text-amber-200 font-medium transition-colors text-xs">
                                        <i class="fas fa-user-circle mr-1.5 text-3xs text-amber-500"></i>${data}
                                    </a>
                                    ${companyInfo ? '<div class="pl-4">' + companyInfo + '</div>' : ''}
                                </div>`;
                    }
                },
                {
                    data: 'sender_mail_account',
                    name: 'sender_mail_account',
                    render: function (data, type, row) {
                        if (row.type === 'failed') {
                            return `<span class="inline-flex items-center px-2 py-0.5 rounded-md text-3xs font-semibold bg-red-50 text-red-800 dark:bg-red-950/70 dark:text-red-300 border border-red-200 dark:border-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1 text-3xs"></i>Failed (Attempt ${row.attempt || 1})
                                    </span>`;
                        }
                        if (!data || data === 'Not assigned yet' || row.type === 'queued') {
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
                },
                {
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    render: function (data) {
                        return `<button type="button" onclick="inspectEmail('${data}')" class="px-2.5 py-1 text-3xs font-semibold text-zinc-700 dark:text-zinc-200 bg-slate-100 dark:bg-[#09090B] hover:bg-slate-200 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 rounded-lg transition-colors flex items-center gap-1">
                                    <i class="fas fa-search-plus text-3xs"></i>
                                    <span>Inspect</span>
                                </button>`;
                    }
                }
            ],
            drawCallback: function (settings) {
                const total = settings._iRecordsDisplay || 0;
                const shown = Math.min(currentLength, total);

                if (countDisplay) {
                    countDisplay.textContent = `Showing ${shown} of ${total} emails`;
                }

                if (showMoreContainer && showMoreBtn && remainingCountText) {
                    if (shown >= total || total === 0) {
                        showMoreContainer.classList.add('hidden');
                    } else {
                        showMoreContainer.classList.remove('hidden');
                        const remaining = total - shown;
                        remainingCountText.textContent = `Show More (${remaining} remaining)`;
                    }
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
            }
        });

        // Handle Status Filter Switching
        statusFilterButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                statusFilterButtons.forEach(b => {
                    b.classList.remove('bg-zinc-900', 'text-white', 'dark:bg-amber-100', 'dark:text-zinc-950');
                    b.classList.add('bg-white', 'dark:bg-[#141417]', 'text-zinc-700', 'dark:text-zinc-300', 'hover:bg-slate-100', 'dark:hover:bg-zinc-800');
                });

                this.classList.remove('bg-white', 'dark:bg-[#141417]', 'text-zinc-700', 'dark:text-zinc-300', 'hover:bg-slate-100', 'dark:hover:bg-zinc-800');
                this.classList.add('bg-zinc-900', 'text-white', 'dark:bg-amber-100', 'dark:text-zinc-950');

                currentStatus = this.getAttribute('data-status');
                currentLength = pageSize;
                table.page.len(currentLength).draw();
            });
        });

        // Search Input Handling
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentLength = pageSize;
                    table.page.len(currentLength).draw();
                }, 300);
            });
        }

        // Show More Button Handler
        if (showMoreBtn) {
            showMoreBtn.addEventListener('click', function () {
                currentLength += pageSize;
                table.page.len(currentLength).draw(false);
            });
        }
    });

    function inspectEmail(id) {
        const modal = document.getElementById('emailInspectorModal');
        const modalBody = document.getElementById('inspectorModalContent');
        if (!modal) return;

        modal.classList.remove('hidden');
        modalBody.innerHTML = `
            <div class="p-12 text-center text-zinc-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-3 text-amber-500"></i>
                <p class="text-xs">Fetching transmission details and payload...</p>
            </div>
        `;

        fetch(`/emails/${id}/details`, { credentials: 'same-origin' })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    modalBody.innerHTML = `<div class="p-6 text-red-500 text-xs">${data.message || 'Error loading email'}</div>`;
                    return;
                }

                let statusBadge = '';
                if (data.status === 'sent') {
                    statusBadge = '<span class="px-2.5 py-1 rounded-full text-3xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/70 dark:text-emerald-300">DISPATCHED SUCCESS</span>';
                } else if (data.status === 'failed') {
                    statusBadge = '<span class="px-2.5 py-1 rounded-full text-3xs font-extrabold bg-red-100 text-red-800 border border-red-300 dark:bg-red-950/70 dark:text-red-300">DELIVERY FAILED</span>';
                } else {
                    statusBadge = '<span class="px-2.5 py-1 rounded-full text-3xs font-extrabold bg-blue-100 text-blue-800 border border-blue-300 dark:bg-blue-950/70 dark:text-blue-300">QUEUED (PENDING)</span>';
                }

                let errorSection = '';
                if (data.error) {
                    errorSection = `
                        <div class="p-3.5 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 rounded-xl space-y-1">
                            <div class="text-2xs font-bold text-red-800 dark:text-red-300 flex items-center gap-1.5">
                                <i class="fas fa-exclamation-triangle"></i> SMTP Error Telemetry (Code: ${data.response_code || 500}, Attempts: ${data.attempt || 1})
                            </div>
                            <div class="font-mono text-3xs text-red-700 dark:text-red-300 break-all leading-relaxed">${data.error}</div>
                        </div>
                    `;
                }

                modalBody.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                            <div class="flex items-center space-x-2">
                                ${statusBadge}
                                <span class="text-3xs text-zinc-400 font-mono">${data.timestamp}</span>
                            </div>
                            <div class="text-2xs text-zinc-500">
                                Sender: <strong class="text-zinc-800 dark:text-zinc-200">${data.sender}</strong>
                            </div>
                        </div>

                        ${errorSection}

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 dark:bg-[#09090B] p-3.5 rounded-xl border border-slate-200 dark:border-zinc-800 text-xs">
                            <div>
                                <span class="block text-3xs font-bold text-zinc-400 uppercase tracking-wider">Recipient Contact</span>
                                <span class="font-bold text-zinc-900 dark:text-white">${data.recipient_name || 'Contact'}</span>
                                <div class="text-2xs font-mono text-zinc-600 dark:text-zinc-400">${data.recipient}</div>
                            </div>
                            <div>
                                <span class="block text-3xs font-bold text-zinc-400 uppercase tracking-wider">Organization / Campaign</span>
                                <span class="font-semibold text-zinc-800 dark:text-zinc-200">${data.recipient_company || 'Independent Lead'}</span>
                                <div class="text-2xs text-amber-700 dark:text-amber-300 font-medium">${data.campaign_name}</div>
                            </div>
                        </div>

                        <div>
                            <span class="block text-3xs font-bold text-zinc-400 uppercase tracking-wider mb-1">Dispatched Subject Line</span>
                            <div class="p-3 bg-white dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 font-bold text-xs text-zinc-900 dark:text-white">
                                ${data.subject}
                            </div>
                        </div>

                        <div>
                            <span class="block text-3xs font-bold text-zinc-400 uppercase tracking-wider mb-1">Personalized Email Body</span>
                            <div class="p-4 bg-white text-zinc-900 rounded-xl border border-slate-200 shadow-xs text-xs leading-relaxed max-h-72 overflow-y-auto font-sans">
                                ${data.body}
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(err => {
                modalBody.innerHTML = `<div class="p-6 text-red-500 text-xs">Failed to load email details: ${err.message}</div>`;
            });
    }

    function closeEmailInspector() {
        document.getElementById('emailInspectorModal').classList.add('hidden');
    }
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1 space-x-2">
                    <a href="/dashboard" class="hover:text-zinc-900 dark:hover:text-amber-200 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-zinc-900 dark:text-white font-semibold">Emails & Dispatch Logs</span>
                </nav>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas fa-paper-plane text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ __('Transmission Logs & Dispatch Control') }}
                    </h2>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <a href="/dispatch" class="inline-flex items-center px-4 py-2 bg-amber-50 dark:bg-amber-950/50 hover:bg-amber-100 text-amber-900 dark:text-amber-200 border border-amber-300/80 dark:border-amber-500/30 text-xs font-bold rounded-xl shadow-2xs transition-all gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <i class="fas fa-layer-group text-xs"></i>
                    <span>Dispatch Studio</span>
                </a>
                <a href="/newsletter-form/new" class="inline-flex items-center px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-xs font-bold rounded-xl shadow-2xs transition-all gap-2">
                    <i class="fas fa-plus text-2xs"></i>
                    <span>New Broadcast</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-8 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Main High-Density Table Container -->
            <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/90 dark:border-zinc-800 overflow-hidden">
                
                <!-- Top Toolbar: Search Bar + 1-Click Status Filter -->
                <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 space-y-4 bg-slate-50/80 dark:bg-[#09090B]/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="relative flex-1 max-w-md">
                            <i class="fas fa-search absolute left-3.5 top-3 text-zinc-400 dark:text-zinc-400 text-sm"></i>
                            <input type="text" id="emailSearchInput" placeholder="Search logs by recipient, subject, or campaign..." 
                                   class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#09090B] border border-zinc-300 dark:border-zinc-800 rounded-xl text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-300/80 focus:border-amber-300/80 transition-all">
                        </div>
                        <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400" id="emailCountDisplay">
                            Loading transmission telemetry...
                        </div>
                    </div>

                    <!-- 1-Click Status Filter Buttons -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pt-1 pb-1">
                        <span class="text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider shrink-0 mr-1">
                            <i class="fas fa-filter text-3xs mr-1 text-amber-500"></i> Status:
                        </span>
                        <button type="button" data-status="all" class="email-status-btn px-3 py-1 rounded-lg text-xs font-bold transition-all bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 shadow-2xs shrink-0">
                            All Logs
                        </button>
                        <button type="button" data-status="sent" class="email-status-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 shadow-2xs shrink-0 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Dispatched (Sent)</span>
                        </button>
                        <button type="button" data-status="queued" class="email-status-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 shadow-2xs shrink-0 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span>In Queue</span>
                        </button>
                        <button type="button" data-status="failed" class="email-status-btn px-3 py-1 rounded-lg text-xs font-semibold transition-all bg-white dark:bg-[#141417] text-zinc-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 shadow-2xs shrink-0 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <span>Delivery Failed</span>
                        </button>
                    </div>
                </div>

                <!-- Table View -->
                <div id="tableContainer" class="overflow-x-auto">
                    <table id="emails" class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-zinc-200/80 dark:border-zinc-800 bg-slate-100/60 dark:bg-[#09090B]/80 text-3xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                <th class="py-3.5 pl-6 pr-4">Subject Line</th>
                                <th class="py-3.5 px-4">Campaign</th>
                                <th class="py-3.5 px-4">Recipient</th>
                                <th class="py-3.5 px-4">Delivery Status / Gateway</th>
                                <th class="py-3.5 px-4">Timestamp</th>
                                <th class="py-3.5 pl-4 pr-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
                        </tbody>
                    </table>
                </div>

                <!-- Empty Search Results State -->
                <div id="emptySearchResults" class="hidden p-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-[#09090B] text-zinc-400 flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 class="text-base font-bold text-zinc-900 dark:text-white">No transmission logs found</h4>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Try adjusting your filters or click "Queue Ready Broadcasts" in the Dispatch Console above.</p>
                </div>

                <!-- Progressive Load More Pagination Footer -->
                <div id="showMoreContainer" class="hidden p-6 border-t border-zinc-100 dark:border-zinc-800 text-center bg-slate-50/60 dark:bg-[#09090B]/50">
                    <button type="button" id="showMoreBtn" 
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-white dark:bg-[#141417] text-zinc-800 dark:text-zinc-200 font-bold text-sm rounded-xl border border-zinc-300 dark:border-zinc-800 shadow-2xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all duration-200 gap-2">
                        <i class="fas fa-chevron-down text-xs"></i>
                        <span id="remainingCountText">Show More Emails</span>
                    </button>
                </div>

            </div>

        </div>
    </div>

    <!-- Email Inspector & Diagnostics Modal -->
    <div id="emailInspectorModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-zinc-900/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#141417] rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-zinc-800 transform transition-all space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-sm border border-amber-200/60 dark:border-amber-500/30">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Email Transmission Telemetry</h3>
                        <p class="text-3xs text-zinc-500">Inspection & Live Rendered Content</p>
                    </div>
                </div>
                <button type="button" onclick="closeEmailInspector()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div id="inspectorModalContent">
                <!-- Populated dynamically via fetch -->
            </div>

            <div class="flex justify-end pt-2 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" onclick="closeEmailInspector()" class="px-5 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-slate-100 dark:bg-zinc-800 rounded-xl hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors">
                    Close Inspector
                </button>
            </div>
        </div>
    </div>
</x-app-layout>