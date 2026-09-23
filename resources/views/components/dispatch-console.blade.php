<div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 space-y-6">
    <!-- Header & Live Status -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800 pb-4">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-lg border border-amber-200/60 dark:border-amber-500/30 shadow-2xs">
                <i class="fas fa-terminal"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-black text-zinc-900 dark:text-white">Dispatch Control Center</h3>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-3xs font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Engine Ready
                    </span>
                </div>
                <p class="text-2xs text-zinc-500 dark:text-zinc-400">Trigger queue workers, dispatch live batches, and observe real-time transmission telemetry without terminal commands.</p>
            </div>
        </div>

        <!-- Real-time Queue Counters -->
        <div class="flex items-center gap-2 bg-slate-50 dark:bg-[#09090B] p-1.5 rounded-xl border border-slate-200 dark:border-zinc-800 text-xs">
            <div class="px-3 py-1 bg-white dark:bg-[#141417] rounded-lg border border-slate-200 dark:border-zinc-800 text-center shadow-2xs">
                <span class="block text-3xs text-zinc-400 font-bold uppercase tracking-wider">In Queue</span>
                <span id="consoleInQueueCount" class="font-mono font-black text-amber-600 dark:text-amber-400 text-sm">--</span>
            </div>
            <div class="px-3 py-1 bg-white dark:bg-[#141417] rounded-lg border border-slate-200 dark:border-zinc-800 text-center shadow-2xs">
                <span class="block text-3xs text-zinc-400 font-bold uppercase tracking-wider">Ready Broadcasts</span>
                <span id="consoleReadyNewslettersCount" class="font-mono font-black text-blue-600 dark:text-blue-400 text-sm">--</span>
            </div>
            <div class="px-3 py-1 bg-white dark:bg-[#141417] rounded-lg border border-slate-200 dark:border-zinc-800 text-center shadow-2xs">
                <span class="block text-3xs text-zinc-400 font-bold uppercase tracking-wider">Failed</span>
                <span id="consoleFailedCount" class="font-mono font-black text-red-600 dark:text-red-400 text-sm">--</span>
            </div>
        </div>
    </div>

    <!-- 1-Click Control Buttons Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Queue Newsletters Button -->
            <button type="button" id="btnRunQueue" onclick="triggerQueueAll();" class="inline-flex items-center px-4 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-xs font-bold rounded-xl shadow-2xs transition-all gap-2 cursor-pointer disabled:opacity-50">
                <i id="iconRunQueue" class="fas fa-layer-group text-amber-400 dark:text-zinc-950 text-xs"></i>
                <span>Queue Ready Broadcasts</span>
                <span class="text-3xs opacity-60 font-mono px-1.5 py-0.5 rounded bg-zinc-800 dark:bg-amber-200">CS:queue-mails</span>
            </button>

            <!-- Flush / Dispatch Queue Button -->
            <button type="button" id="btnFlushQueue" onclick="triggerFlushQueue();" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-2xs transition-all gap-2 cursor-pointer disabled:opacity-50">
                <i id="iconFlushQueue" class="fas fa-paper-plane text-xs"></i>
                <span>Dispatch Emails Now</span>
                <span class="text-3xs opacity-80 font-mono px-1.5 py-0.5 rounded bg-emerald-700">CS:FlushMailQueue</span>
            </button>

            <!-- Pacing Delay Control -->
            <div class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 text-xs" title="Pause duration in milliseconds between sending emails">
                <i class="fas fa-gauge-high text-amber-500 text-2xs"></i>
                <span class="text-3xs font-semibold text-zinc-600 dark:text-zinc-400">Pacing:</span>
                <select id="consolePacingSelect" class="bg-transparent text-xs font-mono font-bold text-zinc-900 dark:text-white border-0 p-0 focus:ring-0 cursor-pointer">
                    <option value="0">0ms (Max Speed)</option>
                    <option value="30" selected>30ms (Safe)</option>
                    <option value="100">100ms (Gentle)</option>
                    <option value="500">500ms (Warmup)</option>
                </select>
            </div>

            <!-- Retry Failed Button -->
            <button type="button" id="btnRetryFailed" onclick="triggerRetryFailed();" class="inline-flex items-center px-3.5 py-2.5 bg-slate-100 dark:bg-[#09090B] hover:bg-slate-200 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 transition-colors gap-2 cursor-pointer">
                <i class="fas fa-sync-alt text-2xs text-amber-500"></i>
                <span>Retry Failed</span>
            </button>
        </div>

        <div class="flex items-center gap-2">
            <!-- Clear Queue Button -->
            <button type="button" onclick="triggerClearQueue();" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-950/40 dark:hover:bg-red-900/40 dark:text-red-300 text-xs font-semibold rounded-xl border border-red-200 dark:border-red-900/50 transition-colors gap-1.5 flex items-center cursor-pointer">
                <i class="fas fa-trash-alt text-2xs"></i>
                <span>Clear Queue</span>
            </button>
            <button type="button" onclick="clearConsoleLog();" title="Clear log screen" class="p-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors">
                <i class="fas fa-eraser text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Interactive Live Terminal Window -->
    <div class="rounded-xl overflow-hidden border border-zinc-800 bg-[#09090B] shadow-inner font-mono text-xs">
        <!-- Terminal Title Bar -->
        <div class="bg-[#141417] px-4 py-2 border-b border-zinc-800 flex items-center justify-between select-none">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500/80"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                <span class="text-3xs font-mono text-zinc-400 ml-2">dispatch-engine@campaign-stack:~</span>
            </div>
            <div class="text-3xs text-zinc-500 flex items-center gap-2">
                <span id="consoleAutoScrollBadge" class="text-emerald-400 font-bold">● LIVE STREAMING</span>
            </div>
        </div>

        <!-- Terminal Output Stream -->
        <div id="dispatchTerminalLogs" class="p-4 space-y-1.5 max-h-64 overflow-y-auto text-zinc-300 leading-relaxed font-mono">
            <div class="text-zinc-500 text-2xs">
                [INIT] Campaign Stack Dispatch Console loaded. Click "Queue Ready Broadcasts" or "Dispatch Emails Now" above to start.
            </div>
        </div>
    </div>
</div>

<script>
    let isFlushingActive = false;

    document.addEventListener('DOMContentLoaded', function() {
        refreshQueueMetrics();
        setInterval(refreshQueueMetrics, 5000);
    });

    function refreshQueueMetrics() {
        fetch('/queue/status', { credentials: 'same-origin' })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const inQueueEl = document.getElementById('consoleInQueueCount');
                    const readyEl = document.getElementById('consoleReadyNewslettersCount');
                    const failedEl = document.getElementById('consoleFailedCount');

                    if (inQueueEl) inQueueEl.textContent = data.in_queue;
                    if (readyEl) readyEl.textContent = data.ready_newsletters;
                    if (failedEl) failedEl.textContent = data.failed_queue;
                }
            })
            .catch(() => {});
    }

    function appendTerminalLog(message, type = 'info') {
        const term = document.getElementById('dispatchTerminalLogs');
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

    function clearConsoleLog() {
        const term = document.getElementById('dispatchTerminalLogs');
        if (term) {
            term.innerHTML = '<div class="text-zinc-500 text-2xs">[CLEARED] Terminal screen wiped. Ready for actions.</div>';
        }
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

    function triggerQueueAll() {
        const btn = document.getElementById('btnRunQueue');
        const icon = document.getElementById('iconRunQueue');
        if (btn) btn.disabled = true;
        if (icon) icon.className = 'fas fa-spinner fa-spin text-xs';

        appendTerminalLog('▶ Executing Queue Engine (php artisan CS:queue-mails)...', 'info');

        fetch('/queue/run-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.logs && Array.isArray(data.logs)) {
                data.logs.forEach(l => appendTerminalLog(l));
            } else {
                appendTerminalLog(data.message, data.success ? 'success' : 'error');
            }
            refreshQueueMetrics();
        })
        .catch(err => {
            appendTerminalLog('[ERROR] Queue execution failed: ' + err.message, 'error');
        })
        .finally(() => {
            if (btn) btn.disabled = false;
            if (icon) icon.className = 'fas fa-layer-group text-amber-400 dark:text-zinc-950 text-xs';
        });
    }

    function triggerFlushQueue() {
        if (isFlushingActive) return;
        isFlushingActive = true;

        const btn = document.getElementById('btnFlushQueue');
        const icon = document.getElementById('iconFlushQueue');
        if (btn) btn.disabled = true;
        if (icon) icon.className = 'fas fa-spinner fa-spin text-xs';

        const pauseMs = parseInt(document.getElementById('consolePacingSelect')?.value || '30', 10);
        appendTerminalLog(`🚀 Dispatch Engine Activated (php artisan CS:FlushMailQueue --pause-ms=${pauseMs})...`, 'info');

        function processNextBatch() {
            fetch('/queue/flush-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({ limit: 15, pause_ms: pauseMs })
            })
            .then(res => res.json())
            .then(data => {
                if (data.logs && Array.isArray(data.logs)) {
                    data.logs.forEach(l => appendTerminalLog(l));
                }

                refreshQueueMetrics();

                if (data.remaining > 0 && data.sent_count > 0) {
                    appendTerminalLog(`[CONTINUING] ${data.remaining} email(s) remaining in queue. Dispatching next batch...`, 'info');
                    setTimeout(processNextBatch, 800);
                } else {
                    isFlushingActive = false;
                    if (btn) btn.disabled = false;
                    if (icon) icon.className = 'fas fa-paper-plane text-xs';
                    appendTerminalLog('[DISPATCH COMPLETE] All pending emails in this queue run processed.', 'success');
                }
            })
            .catch(err => {
                isFlushingActive = false;
                if (btn) btn.disabled = false;
                if (icon) icon.className = 'fas fa-paper-plane text-xs';
                appendTerminalLog('[ERROR] Dispatch worker failed: ' + err.message, 'error');
            });
        }

        processNextBatch();
    }

    function triggerRetryFailed() {
        appendTerminalLog('🔄 Resetting failed queue entries...', 'info');
        fetch('/queue/retry-failed', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            appendTerminalLog(data.message, 'success');
            refreshQueueMetrics();
        })
        .catch(err => appendTerminalLog('[ERROR] Failed to retry: ' + err.message, 'error'));
    }

    function triggerClearQueue() {
        if (!confirm('Are you sure you want to clear all pending emails in the queue?')) return;

        appendTerminalLog('🗑 Clearing entire mail queue...', 'warning');
        fetch('/queue/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            appendTerminalLog(data.message, 'warning');
            refreshQueueMetrics();
        })
        .catch(err => appendTerminalLog('[ERROR] Clear queue failed: ' + err.message, 'error'));
    }
</script>
