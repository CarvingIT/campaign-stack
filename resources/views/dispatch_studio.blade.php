@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>

<script>
    let currentAudienceData = [];
    let selectedContactIds = new Set();
    let currentNewsletterId = {{ $selectedNewsletter->id ?? 'null' }};
    let isFlushingActive = false;
    let currentStage = 1;
    let maxUnlockedStage = 1;

    document.addEventListener('DOMContentLoaded', function () {
        const selectEl = document.getElementById('studioNewsletterSelect');
        if (!currentNewsletterId && selectEl && selectEl.value) {
            currentNewsletterId = selectEl.value;
        }

        if (currentNewsletterId) {
            loadAudienceForNewsletter(currentNewsletterId);
        }
        
        goToStage(1);
        refreshQueueMetrics();
        setInterval(refreshQueueMetrics, 5000);
    });

    function goToStage(stageNumber) {
        if (stageNumber > maxUnlockedStage + 1) return;

        currentStage = stageNumber;
        if (stageNumber > maxUnlockedStage) {
            maxUnlockedStage = stageNumber;
        }

        // Update Stage Step Visuals
        for (let i = 1; i <= 4; i++) {
            const stepEl = document.getElementById(`stepNav${i}`);
            const badgeEl = document.getElementById(`stepBadge${i}`);
            const panelEl = document.getElementById(`stagePanel${i}`);

            if (!stepEl || !panelEl) continue;

            if (i === currentStage) {
                // Active Step
                stepEl.className = 'p-3.5 rounded-2xl bg-white dark:bg-[#141417] border-2 border-amber-400 dark:border-amber-400 shadow-md flex items-center space-x-3 cursor-pointer transition-all';
                badgeEl.className = 'w-8 h-8 rounded-xl bg-amber-500 text-zinc-950 font-black flex items-center justify-center text-xs shrink-0 shadow-2xs';
                badgeEl.innerHTML = i;
                panelEl.classList.remove('hidden');
            } else if (i < currentStage || i <= maxUnlockedStage) {
                // Completed / Accessible Step
                stepEl.className = 'p-3.5 rounded-2xl bg-white/90 dark:bg-[#141417]/90 border border-emerald-300 dark:border-emerald-800/80 shadow-2xs flex items-center space-x-3 cursor-pointer transition-all hover:bg-emerald-50/40';
                badgeEl.className = 'w-8 h-8 rounded-xl bg-emerald-500 text-white font-bold flex items-center justify-center text-xs shrink-0';
                badgeEl.innerHTML = '<i class="fas fa-check"></i>';
                panelEl.classList.add('hidden');
            } else {
                // Locked Step
                stepEl.className = 'p-3.5 rounded-2xl bg-slate-50/60 dark:bg-[#111113]/60 border border-slate-200 dark:border-zinc-800/60 opacity-60 flex items-center space-x-3 cursor-not-allowed select-none';
                badgeEl.className = 'w-8 h-8 rounded-xl bg-slate-200 dark:bg-zinc-800 text-zinc-400 font-bold flex items-center justify-center text-xs shrink-0';
                badgeEl.innerHTML = i;
                panelEl.classList.add('hidden');
            }
        }

        // Scroll to stage panel smoothly
        const activePanel = document.getElementById(`stagePanel${currentStage}`);
        if (activePanel) {
            activePanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function onNewsletterSelected(selectEl) {
        currentNewsletterId = selectEl.value;
        if (currentNewsletterId) {
            loadAudienceForNewsletter(currentNewsletterId);
        }
    }

    function loadAudienceForNewsletter(newsletterId) {
        if (!newsletterId) return;

        const tbody = document.getElementById('audienceTableBody');
        const loadingState = document.getElementById('audienceLoadingState');
        const countDisplay = document.getElementById('selectedLeadCountDisplay');
        const badge = document.getElementById('audienceCountBadge');

        if (tbody) tbody.innerHTML = '';
        if (loadingState) loadingState.classList.remove('hidden');
        if (badge) badge.textContent = 'Loading...';
        if (countDisplay) countDisplay.textContent = 'Resolving contacts...';

        fetch(`/dispatch/audience?newsletter_id=${encodeURIComponent(newsletterId)}`)
            .then(res => res.json())
            .then(data => {
                if (loadingState) loadingState.classList.add('hidden');
                if (!data.success) {
                    if (tbody) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="p-8 text-center text-red-500 text-xs">
                                    <i class="fas fa-exclamation-triangle text-xl mb-2"></i>
                                    <p>${escapeHtml(data.message || 'Error loading audience.')}</p>
                                </td>
                            </tr>
                        `;
                    }
                    if (badge) badge.textContent = '0 Selected';
                    if (countDisplay) countDisplay.textContent = '0 leads loaded';
                    return;
                }

                currentAudienceData = data.contacts || [];
                selectedContactIds.clear();

                // Select all by default
                currentAudienceData.forEach(c => selectedContactIds.add(c.id));

                renderAudienceTable();
                updateSelectionCounters();
                populatePreviewSelector();

                // Update summary titles across stages
                const stage1Title = document.getElementById('summaryStage1Title');
                if (stage1Title && data.newsletter) {
                    stage1Title.textContent = data.newsletter.title;
                }

                // Auto render preview for first contact
                if (currentAudienceData.length > 0) {
                    renderPersonalizedPreview(currentAudienceData[0].id);
                }
            })
            .catch(err => {
                if (loadingState) loadingState.classList.add('hidden');
                if (tbody) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="p-8 text-center text-red-500 text-xs">
                                <i class="fas fa-exclamation-circle text-xl mb-2"></i>
                                <p>Failed to connect: ${escapeHtml(err.message)}</p>
                            </td>
                        </tr>
                    `;
                }
                if (badge) badge.textContent = '0 Selected';
                if (countDisplay) countDisplay.textContent = 'Network error';
            });
    }

    function renderAudienceTable() {
        const tbody = document.getElementById('audienceTableBody');
        const searchInput = document.getElementById('audienceSearchInput');
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        if (!tbody) return;

        tbody.innerHTML = '';

        const filtered = currentAudienceData.filter(c => {
            if (!query) return true;
            return c.email.toLowerCase().includes(query) ||
                   c.name.toLowerCase().includes(query) ||
                   c.company.toLowerCase().includes(query);
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-8 text-center text-zinc-400 text-xs">
                        <i class="fas fa-search text-xl mb-2 text-zinc-300 dark:text-zinc-600"></i>
                        <p>No matching contacts found in this audience segment.</p>
                    </td>
                </tr>
            `;
            return;
        }

        filtered.forEach(c => {
            const isChecked = selectedContactIds.has(c.id);
            const tr = document.createElement('tr');
            tr.className = `border-b border-zinc-100 dark:border-zinc-800/80 transition-colors ${isChecked ? 'hover:bg-amber-50/40 dark:hover:bg-zinc-800/40' : 'opacity-50 bg-slate-50/50 dark:bg-zinc-900/30'}`;
            
            const tagsHtml = c.tags.map(t => `<span class="px-1.5 py-0.5 rounded text-3xs font-semibold bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-500/30">${escapeHtml(t)}</span>`).join(' ');

            tr.innerHTML = `
                <td class="py-3 pl-4 pr-2">
                    <input type="checkbox" value="${c.id}" ${isChecked ? 'checked' : ''} onchange="toggleContactSelection(${c.id}, this.checked)" class="rounded border-zinc-300 dark:border-zinc-700 text-amber-500 focus:ring-amber-400">
                </td>
                <td class="py-3 px-3">
                    <div class="font-bold text-xs text-zinc-900 dark:text-white flex items-center gap-1.5">
                        <i class="fas fa-user-circle text-zinc-400 text-3xs"></i>
                        <span>${escapeHtml(c.name || 'Unnamed')}</span>
                    </div>
                    <div class="text-3xs text-zinc-500 font-mono pl-4">${escapeHtml(c.email)}</div>
                </td>
                <td class="py-3 px-3 text-xs text-zinc-700 dark:text-zinc-300 font-medium">
                    ${escapeHtml(c.company)}
                </td>
                <td class="py-3 px-3">
                    <div class="flex flex-wrap gap-1">${tagsHtml || '<span class="text-3xs text-zinc-400 italic">No tags</span>'}</div>
                </td>
                <td class="py-3 pl-2 pr-4 text-right whitespace-nowrap">
                    <button type="button" onclick="openQuickEditModal(${c.id})" title="Edit Lead Information" class="p-1.5 text-zinc-500 hover:text-zinc-900 dark:hover:text-amber-200 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors text-xs">
                        <i class="fas fa-pencil-alt text-3xs"></i>
                    </button>
                    <button type="button" onclick="renderPersonalizedPreview(${c.id}); goToStage(3);" title="Preview Email For This Lead" class="p-1.5 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 rounded-lg transition-colors text-xs">
                        <i class="fas fa-eye text-3xs"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function toggleContactSelection(id, checked) {
        if (checked) {
            selectedContactIds.add(id);
        } else {
            selectedContactIds.delete(id);
        }
        updateSelectionCounters();
        renderAudienceTable();
    }

    function selectAllContacts(selectAll) {
        if (selectAll) {
            currentAudienceData.forEach(c => selectedContactIds.add(c.id));
        } else {
            selectedContactIds.clear();
        }
        updateSelectionCounters();
        renderAudienceTable();
    }

    function updateSelectionCounters() {
        const countDisplay = document.getElementById('selectedLeadCountDisplay');
        const badge = document.getElementById('audienceCountBadge');
        const stage4SelectedCount = document.getElementById('stage4TargetCount');
        const total = currentAudienceData.length;
        const selected = selectedContactIds.size;

        if (countDisplay) {
            countDisplay.textContent = `${selected} of ${total} leads selected to receive broadcast`;
        }
        if (badge) {
            badge.textContent = `${selected} Selected`;
        }
        if (stage4SelectedCount) {
            stage4SelectedCount.textContent = `${selected} Leads Selected`;
        }
    }

    function populatePreviewSelector() {
        const selector = document.getElementById('previewContactSelector');
        if (!selector) return;
        selector.innerHTML = '';

        currentAudienceData.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = `${c.name || 'Contact'} (${c.company}) — ${c.email}`;
            selector.appendChild(opt);
        });
    }

    function renderPersonalizedPreview(contactId) {
        const contact = currentAudienceData.find(c => c.id == contactId);
        if (!contact) return;

        const subjectEl = document.getElementById('previewRenderedSubject');
        const bodyEl = document.getElementById('previewRenderedBody');
        const metaEl = document.getElementById('previewRecipientMeta');

        if (subjectEl) subjectEl.textContent = contact.preview_subject || '(No Subject Line)';
        if (bodyEl) bodyEl.innerHTML = contact.preview_body || '<p class="text-zinc-400 italic">No body content</p>';
        if (metaEl) {
            metaEl.innerHTML = `Personalized for: <strong class="text-zinc-900 dark:text-white">${escapeHtml(contact.name)}</strong> (${escapeHtml(contact.company)}) &bull; <span class="font-mono text-amber-600 dark:text-amber-400">${escapeHtml(contact.email)}</span>`;
        }

        const selector = document.getElementById('previewContactSelector');
        if (selector && selector.value != contactId) {
            selector.value = contactId;
        }
    }

    function openQuickEditModal(contactId) {
        const contact = currentAudienceData.find(c => c.id == contactId);
        if (!contact) return;

        document.getElementById('editContactId').value = contact.id;
        document.getElementById('editFirstname').value = contact.firstname || '';
        document.getElementById('editLastname').value = contact.lastname || '';
        document.getElementById('editEmail').value = contact.email || '';
        document.getElementById('editCompany').value = contact.company || '';
        document.getElementById('quickEditModal').classList.remove('hidden');
    }

    function closeQuickEditModal() {
        document.getElementById('quickEditModal').classList.add('hidden');
    }

    function saveQuickEdit(e) {
        e.preventDefault();
        const cid = document.getElementById('editContactId').value;
        const fname = document.getElementById('editFirstname').value;
        const lname = document.getElementById('editLastname').value;
        const email = document.getElementById('editEmail').value;
        const comp = document.getElementById('editCompany').value;

        fetch('/dispatch/update-contact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                contact_id: cid,
                firstname: fname,
                lastname: lname,
                email: email,
                company: comp
            })
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.message || 'HTTP ' + res.status + ' error occurred.');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                closeQuickEditModal();
                loadAudienceForNewsletter(currentNewsletterId);
                appendStudioLog(`[UPDATED] Contact details saved for ${email}`, 'success');
            } else {
                alert(data.message || 'Update failed.');
            }
        })
        .catch(err => alert('Lead Update Notice: ' + err.message));
    }

    function triggerCustomQueue() {
        if (selectedContactIds.size === 0) {
            alert('Please select at least 1 contact to queue.');
            return;
        }

        const btn = document.getElementById('btnStageQueue');
        const icon = document.getElementById('iconStageQueue');
        if (btn) btn.disabled = true;
        if (icon) icon.className = 'fas fa-spinner fa-spin text-xs';

        appendStudioLog(`▶ Staging queue for ${selectedContactIds.size} recipient(s)...`, 'info');

        fetch('/dispatch/queue-custom', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                newsletter_id: currentNewsletterId,
                contact_ids: Array.from(selectedContactIds)
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.logs && Array.isArray(data.logs)) {
                data.logs.forEach(l => appendStudioLog(l));
            } else {
                appendStudioLog(data.message, data.success ? 'success' : 'error');
            }
            refreshQueueMetrics();
            const btnFlush = document.getElementById('btnStudioFlush');
            if (btnFlush) btnFlush.disabled = false;
        })
        .catch(err => appendStudioLog('[ERROR] Queue failed: ' + err.message, 'error'))
        .finally(() => {
            if (btn) btn.disabled = false;
            if (icon) icon.className = 'fas fa-layer-group text-amber-400 dark:text-zinc-950 text-xs';
        });
    }

    function triggerStudioFlush() {
        if (isFlushingActive) return;
        isFlushingActive = true;

        const btn = document.getElementById('btnStudioFlush');
        const icon = document.getElementById('iconStudioFlush');
        if (btn) btn.disabled = true;
        if (icon) icon.className = 'fas fa-spinner fa-spin text-xs';

        appendStudioLog('🚀 Dispatch Engine Initiated (php artisan CS:FlushMailQueue)...', 'info');

        function processBatch() {
            fetch('/queue/flush-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ limit: 15 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.logs && Array.isArray(data.logs)) {
                    data.logs.forEach(l => appendStudioLog(l));
                }

                refreshQueueMetrics();

                if (data.remaining > 0 && data.sent_count > 0) {
                    appendStudioLog(`[DISPATCHING] ${data.remaining} email(s) remaining. Sending next batch...`, 'info');
                    setTimeout(processBatch, 800);
                } else {
                    isFlushingActive = false;
                    if (btn) btn.disabled = false;
                    if (icon) icon.className = 'fas fa-paper-plane text-xs';
                    appendStudioLog('[COMPLETE] Queue batch processed successfully.', 'success');
                }
            })
            .catch(err => {
                isFlushingActive = false;
                if (btn) btn.disabled = false;
                if (icon) icon.className = 'fas fa-paper-plane text-xs';
                appendStudioLog('[ERROR] Dispatch failed: ' + err.message, 'error');
            });
        }

        processBatch();
    }

    function triggerRetryFailed() {
        appendStudioLog('🔄 Resetting failed queue entries...', 'info');
        fetch('/queue/retry-failed', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            appendStudioLog(data.message, 'success');
            refreshQueueMetrics();
        })
        .catch(err => appendStudioLog('[ERROR] Failed to retry: ' + err.message, 'error'));
    }

    function triggerClearQueue() {
        if (!confirm('Are you sure you want to clear all pending emails in the queue?')) return;

        appendStudioLog('🗑 Clearing entire mail queue...', 'warning');
        fetch('/queue/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            appendStudioLog(data.message, 'warning');
            refreshQueueMetrics();
        })
        .catch(err => appendStudioLog('[ERROR] Clear queue failed: ' + err.message, 'error'));
    }

    function appendStudioLog(message, type = 'info') {
        const term = document.getElementById('studioTerminalLogs');
        if (!term) return;

        const time = new Date().toLocaleTimeString();
        let colorClass = 'text-zinc-300';
        let badge = '<span class="text-zinc-500 font-bold">[' + time + ']</span>';

        if (type === 'success' || message.includes('[SUCCESS]') || message.includes('[COMPLETE]')) {
            colorClass = 'text-emerald-400';
        } else if (type === 'error' || message.includes('[FAILED]') || message.includes('[ERROR]')) {
            colorClass = 'text-red-400 font-semibold';
        } else if (type === 'warning' || message.includes('[WARN]')) {
            colorClass = 'text-amber-400';
        } else if (message.includes('[START]') || message.includes('[QUEUED]')) {
            colorClass = 'text-blue-400';
        }

        const line = document.createElement('div');
        line.className = 'flex items-start space-x-2';
        line.innerHTML = `${badge} <span class="${colorClass}">${escapeHtml(message)}</span>`;
        term.appendChild(line);
        term.scrollTop = term.scrollHeight;
    }

    function refreshQueueMetrics() {
        fetch('/queue/status')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const inQueueEl = document.getElementById('studioInQueue');
                    const sentTodayEl = document.getElementById('studioSentToday');
                    const failedEl = document.getElementById('studioFailed');

                    if (inQueueEl) inQueueEl.textContent = data.in_queue;
                    if (sentTodayEl) sentTodayEl.textContent = data.sent_today;
                    if (failedEl) failedEl.textContent = data.failed_queue;
                }
            })
            .catch(() => {});
    }

    function escapeHtml(string) {
        return String(string).replace(/[&<>"'`=\/]/g, function (s) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;',
                '`': '&#x60;',
                '=': '&#x3D;'
            }[s];
        });
    }
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1 space-x-2">
                    <a href="/dashboard" class="hover:text-zinc-900 dark:hover:text-amber-200 transition-colors">Command Center</a>
                    <span>/</span>
                    <span class="text-zinc-900 dark:text-white font-semibold">Dispatch Studio</span>
                </nav>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas fa-layer-group text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ __('Broadcast Dispatch Studio') }}
                    </h2>
                </div>
            </div>

            <!-- Queue KPIs -->
            <div class="flex items-center gap-2">
                <div class="px-3.5 py-1.5 bg-white dark:bg-[#141417] rounded-xl border border-slate-200 dark:border-zinc-800 text-center shadow-2xs">
                    <span class="block text-3xs text-zinc-400 font-bold uppercase tracking-wider">In Queue</span>
                    <span id="studioInQueue" class="font-mono font-black text-amber-600 dark:text-amber-400 text-sm">{{ $inQueue }}</span>
                </div>
                <div class="px-3.5 py-1.5 bg-white dark:bg-[#141417] rounded-xl border border-slate-200 dark:border-zinc-800 text-center shadow-2xs">
                    <span class="block text-3xs text-zinc-400 font-bold uppercase tracking-wider">Sent Today</span>
                    <span id="studioSentToday" class="font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm">{{ $sentToday }}</span>
                </div>
                <div class="px-3.5 py-1.5 bg-white dark:bg-[#141417] rounded-xl border border-slate-200 dark:border-zinc-800 text-center shadow-2xs">
                    <span class="block text-3xs text-zinc-400 font-bold uppercase tracking-wider">Failed</span>
                    <span id="studioFailed" class="font-mono font-black text-red-600 dark:text-red-400 text-sm">{{ $failedCount }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="pb-14 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- 4-Stage Progressive Workflow Stepper Ribbon -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                <!-- Step 1 -->
                <div id="stepNav1" onclick="goToStage(1)" class="p-3.5 rounded-2xl bg-white dark:bg-[#141417] border-2 border-amber-400 shadow-md flex items-center space-x-3 cursor-pointer transition-all">
                    <div id="stepBadge1" class="w-8 h-8 rounded-xl bg-amber-500 text-zinc-950 font-black flex items-center justify-center text-xs shrink-0 shadow-2xs">
                        1
                    </div>
                    <div>
                        <span class="block text-3xs font-bold text-zinc-400 uppercase tracking-wider">Stage 1</span>
                        <span class="font-bold text-zinc-900 dark:text-white">Select Broadcast</span>
                    </div>
                </div>

                <!-- Step 2 -->
                <div id="stepNav2" onclick="goToStage(2)" class="p-3.5 rounded-2xl bg-slate-50/60 dark:bg-[#111113]/60 border border-slate-200 dark:border-zinc-800/60 opacity-60 flex items-center space-x-3 cursor-not-allowed select-none transition-all">
                    <div id="stepBadge2" class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-zinc-800 text-zinc-400 font-bold flex items-center justify-center text-xs shrink-0">
                        2
                    </div>
                    <div>
                        <span class="block text-3xs font-bold text-zinc-400 uppercase tracking-wider">Stage 2</span>
                        <span class="font-bold text-zinc-900 dark:text-white">Review & Edit Leads</span>
                    </div>
                </div>

                <!-- Step 3 -->
                <div id="stepNav3" onclick="goToStage(3)" class="p-3.5 rounded-2xl bg-slate-50/60 dark:bg-[#111113]/60 border border-slate-200 dark:border-zinc-800/60 opacity-60 flex items-center space-x-3 cursor-not-allowed select-none transition-all">
                    <div id="stepBadge3" class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-zinc-800 text-zinc-400 font-bold flex items-center justify-center text-xs shrink-0">
                        3
                    </div>
                    <div>
                        <span class="block text-3xs font-bold text-zinc-400 uppercase tracking-wider">Stage 3</span>
                        <span class="font-bold text-zinc-900 dark:text-white">Personalization Check</span>
                    </div>
                </div>

                <!-- Step 4 -->
                <div id="stepNav4" onclick="goToStage(4)" class="p-3.5 rounded-2xl bg-slate-50/60 dark:bg-[#111113]/60 border border-slate-200 dark:border-zinc-800/60 opacity-60 flex items-center space-x-3 cursor-not-allowed select-none transition-all">
                    <div id="stepBadge4" class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-zinc-800 text-zinc-400 font-bold flex items-center justify-center text-xs shrink-0">
                        4
                    </div>
                    <div>
                        <span class="block text-3xs font-bold text-zinc-400 uppercase tracking-wider">Stage 4</span>
                        <span class="font-bold text-zinc-900 dark:text-white">Queue & Dispatch</span>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- STAGE 1: BROADCAST & SENDER SELECTION PANEL -->
            <!-- ======================================================== -->
            <div id="stagePanel1" class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/90 dark:border-zinc-800 space-y-6">
                <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-lg border border-amber-200/60 dark:border-amber-500/30">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">Stage 1: Select Broadcast Sequence &amp; Mail Relays</h3>
                            <p class="text-2xs text-zinc-500 dark:text-zinc-400">Choose which newsletter campaign to launch and verify outbound SMTP gateway readiness.</p>
                        </div>
                    </div>
                    @if($selectedNewsletter)
                        <a href="/newsletter-form/{{ $selectedNewsletter->id }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-slate-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors gap-1.5">
                            <i class="fas fa-external-link-alt text-3xs"></i>
                            <span>Edit in Composer</span>
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <!-- Broadcast Selector -->
                    <div class="md:col-span-7 space-y-4">
                        <div>
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-2" for="studioNewsletterSelect">
                                Active Newsletter Sequence <span class="text-red-500">*</span>
                            </label>
                            <select id="studioNewsletterSelect" onchange="onNewsletterSelected(this)" class="w-full px-4 py-3 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white text-sm font-semibold focus:ring-amber-300 focus:border-amber-300 shadow-2xs">
                                @foreach($newsletters as $n)
                                    <option value="{{ $n->id }}" @if($selectedNewsletter && $selectedNewsletter->id == $n->id) selected @endif>
                                        {{ $n->title }} — [{{ $n->campaign->name ?? 'General Broadcast' }}] (Status: {{ $n->status }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Info Card -->
                        <div class="p-4 rounded-xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-500/20 text-xs text-amber-900 dark:text-amber-200 space-y-1">
                            <div class="font-bold flex items-center gap-1.5">
                                <i class="fas fa-info-circle text-amber-600 dark:text-amber-400"></i>
                                Selected: <span id="summaryStage1Title">{{ $selectedNewsletter->title ?? 'None' }}</span>
                            </div>
                            <p class="text-2xs opacity-85 leading-relaxed">
                                Proceeding to Stage 2 will automatically resolve and target all contacts associated with this broadcast's industry and audience tags.
                            </p>
                        </div>
                    </div>

                    <!-- Outbound Mail Accounts Status -->
                    <div class="md:col-span-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider">
                                Sender SMTP Gateways
                            </label>
                            <a href="/mail-accounts" class="text-3xs text-amber-600 font-bold hover:underline">Manage</a>
                        </div>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @forelse($outboundAccounts as $acc)
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#09090B] border border-slate-200 dark:border-zinc-800 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 truncate">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="font-semibold text-zinc-800 dark:text-zinc-200 truncate">{{ $acc->name }}</span>
                                    </div>
                                    <span class="text-3xs font-mono font-bold px-1.5 py-0.5 rounded bg-white dark:bg-[#141417] text-zinc-500 border border-slate-200 dark:border-zinc-800">
                                        {{ $acc->type }}
                                    </span>
                                </div>
                            @empty
                                <div class="p-3 bg-red-50 text-red-700 rounded-xl text-xs">
                                    No active mail accounts. <a href="/account-form/new" class="underline font-bold">Add SMTP</a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Stage 1 Footer Actions -->
                <div class="flex items-center justify-end pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <button type="button" onclick="goToStage(2)" class="inline-flex items-center px-6 py-3 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-xs font-bold rounded-xl shadow-2xs transition-all gap-2 cursor-pointer">
                        <span>Confirm Broadcast &amp; Proceed to Audience Leads</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- STAGE 2: AUDIENCE RECIPIENT CONTROL PANEL (REVEALABLE) -->
            <!-- ======================================================== -->
            <div id="stagePanel2" class="hidden bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/90 dark:border-zinc-800 space-y-6">
                <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-lg border border-amber-200/60 dark:border-amber-500/30">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-zinc-900 dark:text-white">Stage 2: Review Audience &amp; Toggle Exclusions</h3>
                                <span id="audienceCountBadge" class="px-2 py-0.5 rounded-full text-3xs font-mono font-bold bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-200 border border-amber-300/80">
                                    Loading...
                                </span>
                            </div>
                            <p class="text-2xs text-zinc-500 dark:text-zinc-400">Inspect the exact leads targeted for this run. Uncheck any lead you wish to exclude or edit typos before sending.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="selectAllContacts(true)" class="px-3 py-1.5 text-xs font-bold rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 text-zinc-700 dark:text-zinc-300 transition-colors">
                            <i class="fas fa-check-square mr-1"></i> Select All
                        </button>
                        <button type="button" onclick="selectAllContacts(false)" class="px-3 py-1.5 text-xs font-bold rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 text-zinc-700 dark:text-zinc-300 transition-colors">
                            <i class="fas fa-square mr-1"></i> Deselect All
                        </button>
                    </div>
                </div>

                <!-- Search and Live Counter Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/80 dark:bg-[#09090B]/60 p-3 rounded-xl border border-slate-200/80 dark:border-zinc-800">
                    <div class="relative flex-1 max-w-sm">
                        <i class="fas fa-search absolute left-3 top-2.5 text-zinc-400 text-xs"></i>
                        <input type="text" id="audienceSearchInput" oninput="renderAudienceTable()" placeholder="Search recipients in this list by name, company, or email..." 
                               class="w-full pl-8 pr-3 py-1.5 bg-white dark:bg-[#141417] border border-zinc-300 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-amber-300">
                    </div>
                    <div id="selectedLeadCountDisplay" class="text-xs font-mono text-zinc-700 dark:text-zinc-300 font-bold">
                        Calculating selected leads...
                    </div>
                </div>

                <!-- Recipient Table Container with Pre-Send Controls -->
                <div class="overflow-x-auto max-h-80 overflow-y-auto border border-zinc-100 dark:border-zinc-800 rounded-xl">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="sticky top-0 z-10 border-b border-zinc-200 dark:border-zinc-800 bg-slate-100/90 dark:bg-[#09090B]/90 backdrop-blur-sm text-3xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                <th class="py-2.5 pl-4 pr-2 w-10">Send</th>
                                <th class="py-2.5 px-3">Lead / Contact</th>
                                <th class="py-2.5 px-3">Company</th>
                                <th class="py-2.5 px-3">Target Tags</th>
                                <th class="py-2.5 pl-2 pr-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="audienceTableBody" class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                        </tbody>
                    </table>
                    <div id="audienceLoadingState" class="p-8 text-center text-zinc-400">
                        <i class="fas fa-spinner fa-spin text-lg mb-2 text-amber-500"></i>
                        <p class="text-xs">Resolving target audience contacts...</p>
                    </div>
                </div>

                <!-- Stage 2 Footer Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <button type="button" onclick="goToStage(1)" class="px-5 py-2.5 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 text-zinc-700 dark:text-zinc-300 text-xs font-bold rounded-xl transition-colors">
                        &larr; Back to Broadcast Selection
                    </button>
                    <button type="button" onclick="goToStage(3)" class="inline-flex items-center px-6 py-3 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-xs font-bold rounded-xl shadow-2xs transition-all gap-2 cursor-pointer">
                        <span>Approve Leads &amp; Verify Personalization</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- STAGE 3: LIVE PERSONALIZATION PREVIEW PANEL (REVEALABLE) -->
            <!-- ======================================================== -->
            <div id="stagePanel3" class="hidden bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/90 dark:border-zinc-800 space-y-6">
                <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-lg border border-amber-200/60 dark:border-amber-500/30">
                            <i class="fas fa-magic"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">Stage 3: Pre-Flight Personalization &amp; Merge Tag Check</h3>
                            <p class="text-2xs text-zinc-500 dark:text-zinc-400">See exactly how your subject line and email body render for real leads with merge tags replaced.</p>
                        </div>
                    </div>

                    <!-- Contact Selector for Preview -->
                    <div class="flex items-center gap-2">
                        <span class="text-3xs text-zinc-400 font-bold uppercase">Inspect Specific Lead:</span>
                        <select id="previewContactSelector" onchange="renderPersonalizedPreview(this.value)" class="px-3 py-1.5 bg-slate-50 dark:bg-[#09090B] border border-zinc-300 dark:border-zinc-800 rounded-xl text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                        </select>
                    </div>
                </div>

                <!-- Rendered Preview Canvas -->
                <div class="p-5 bg-slate-50 dark:bg-[#09090B] rounded-2xl border border-slate-200 dark:border-zinc-800 space-y-4 font-sans">
                    <div id="previewRecipientMeta" class="text-2xs text-zinc-500 border-b border-slate-200 dark:border-zinc-800 pb-2.5"></div>
                    <div>
                        <span class="block text-3xs font-bold text-zinc-400 uppercase tracking-wider mb-1">Rendered Subject Line</span>
                        <div id="previewRenderedSubject" class="font-bold text-sm text-zinc-900 dark:text-white p-3 bg-white dark:bg-[#141417] rounded-xl border border-slate-200 dark:border-zinc-800"></div>
                    </div>
                    <div>
                        <span class="block text-3xs font-bold text-zinc-400 uppercase tracking-wider mb-1">Rendered HTML Email Body</span>
                        <div class="p-5 bg-white text-zinc-900 rounded-xl border border-slate-200 shadow-xs max-h-80 overflow-y-auto">
                            <div id="previewRenderedBody" class="text-sm text-zinc-900 leading-relaxed font-sans"></div>
                        </div>
                    </div>
                </div>

                <!-- Stage 3 Footer Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <button type="button" onclick="goToStage(2)" class="px-5 py-2.5 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 text-zinc-700 dark:text-zinc-300 text-xs font-bold rounded-xl transition-colors">
                        &larr; Back to Audience Leads
                    </button>
                    <button type="button" onclick="goToStage(4)" class="inline-flex items-center px-6 py-3 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-xs font-bold rounded-xl shadow-2xs transition-all gap-2 cursor-pointer">
                        <span>Lock Content &amp; Proceed to Queue &amp; Dispatch</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- STAGE 4: QUEUE STAGING & LIVE TRANSMISSION ENGINE (REVEALABLE) -->
            <!-- ======================================================== -->
            <div id="stagePanel4" class="hidden bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/90 dark:border-zinc-800 space-y-6">
                <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-lg border border-amber-200/60 dark:border-amber-500/30">
                            <i class="fas fa-terminal"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">Stage 4: Queue Staging &amp; Live Batch Dispatch</h3>
                            <p class="text-2xs text-zinc-500 dark:text-zinc-400">Stage only approved leads into the delivery queue, then transmit in live batches with streaming log feedback.</p>
                        </div>
                    </div>

                    <span id="stage4TargetCount" class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-200 border border-amber-300/80">
                        8 Leads Selected
                    </span>
                </div>

                <!-- Action Controls Bar -->
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 dark:bg-[#09090B] p-4 rounded-2xl border border-slate-200 dark:border-zinc-800">
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Step 4A: Stage Queue -->
                        <button type="button" id="btnStageQueue" onclick="triggerCustomQueue();" class="inline-flex items-center px-5 py-3 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-xs font-bold rounded-xl shadow-2xs transition-all gap-2 cursor-pointer disabled:opacity-50">
                            <i id="iconStageQueue" class="fas fa-layer-group text-amber-400 dark:text-zinc-950 text-xs"></i>
                            <span>Step 4A • Stage Leads to Queue</span>
                        </button>

                        <!-- Step 4B: Flush / Dispatch -->
                        <button type="button" id="btnStudioFlush" onclick="triggerStudioFlush();" class="inline-flex items-center px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-2xs transition-all gap-2 cursor-pointer disabled:opacity-50">
                            <i id="iconStudioFlush" class="fas fa-paper-plane text-xs"></i>
                            <span>Step 4B • Dispatch Live Batch Now</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="triggerRetryFailed();" class="px-3.5 py-2.5 bg-white dark:bg-[#141417] hover:bg-slate-100 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 transition-colors gap-1.5 flex items-center cursor-pointer">
                            <i class="fas fa-sync-alt text-2xs text-amber-500"></i>
                            <span>Retry Failed</span>
                        </button>
                        <button type="button" onclick="triggerClearQueue();" class="px-3.5 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-300 text-xs font-semibold rounded-xl border border-red-200 dark:border-red-900/50 transition-colors gap-1.5 flex items-center cursor-pointer">
                            <i class="fas fa-trash-alt text-2xs"></i>
                            <span>Clear Queue</span>
                        </button>
                    </div>
                </div>

                <!-- Live Streaming Terminal Console -->
                <div class="rounded-2xl overflow-hidden border border-zinc-800 bg-[#09090B] shadow-inner font-mono text-xs">
                    <div class="bg-[#141417] px-4 py-2.5 border-b border-zinc-800 flex items-center justify-between select-none">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                            <span class="text-3xs font-mono text-zinc-400 ml-2">dispatch-engine@campaign-stack:~</span>
                        </div>
                        <div class="text-3xs text-emerald-400 font-bold flex items-center gap-1.5">
                            ● LIVE STREAMING
                        </div>
                    </div>

                    <div id="studioTerminalLogs" class="p-4 space-y-1.5 max-h-60 overflow-y-auto text-zinc-300 leading-relaxed font-mono">
                        <div class="text-zinc-500 text-2xs">
                            [READY] Stage 4 Transmission Engine loaded. Click "Step 4A • Stage Leads to Queue", then "Step 4B • Dispatch Live Batch Now".
                        </div>
                    </div>
                </div>

                <!-- Stage 4 Footer Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <button type="button" onclick="goToStage(3)" class="px-5 py-2.5 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 text-zinc-700 dark:text-zinc-300 text-xs font-bold rounded-xl transition-colors">
                        &larr; Back to Personalization Check
                    </button>
                    <a href="/emails" class="inline-flex items-center px-6 py-2.5 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 text-zinc-800 dark:text-zinc-200 text-xs font-bold rounded-xl transition-colors gap-2">
                        <span>View Sent Logs Table</span>
                        <i class="fas fa-external-link-alt text-3xs"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Quick Edit Contact Modal -->
    <div id="quickEditModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-zinc-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#141417] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-zinc-800 transform transition-all space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Edit Lead Before Dispatch</h3>
                <button type="button" onclick="closeQuickEditModal()" class="text-zinc-400 hover:text-zinc-600">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form onsubmit="saveQuickEdit(event)" class="space-y-3 text-xs">
                <input type="hidden" id="editContactId">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase mb-1">First Name</label>
                        <input type="text" id="editFirstname" class="w-full px-3 py-2 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase mb-1">Last Name</label>
                        <input type="text" id="editLastname" class="w-full px-3 py-2 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase mb-1">Email Address</label>
                    <input type="email" id="editEmail" required class="w-full px-3 py-2 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase mb-1">Company / Organization</label>
                    <input type="text" id="editCompany" class="w-full px-3 py-2 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white text-xs">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeQuickEditModal()" class="px-4 py-2 bg-slate-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-zinc-900 text-white dark:bg-amber-100 dark:text-zinc-950 font-bold rounded-xl">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
