@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const points = document.querySelectorAll('.email-chart-point');
        const tooltip = document.getElementById('chartTooltip');
        const tooltipDate = document.getElementById('tooltipDate');
        const tooltipCount = document.getElementById('tooltipCount');
        const tooltipSub = document.getElementById('tooltipSub');
        const guideLine = document.getElementById('chartGuideLine');
        const svgContainer = document.getElementById('chartContainer');

        points.forEach(function (point) {
            point.addEventListener('mouseenter', function () {
                if (tooltip && tooltipDate && tooltipCount) {
                    tooltipDate.textContent = point.dataset.label;
                    tooltipCount.textContent = Number(point.dataset.count).toLocaleString() + ' emails dispatched';
                    if (tooltipSub) {
                        const pct = point.dataset.pct || '0';
                        tooltipSub.textContent = pct + '% of period output';
                    }
                    tooltip.classList.remove('hidden');
                    point.setAttribute('r', '6');
                }
            });

            point.addEventListener('mousemove', function (event) {
                if (tooltip && svgContainer) {
                    const rect = svgContainer.getBoundingClientRect();
                    let left = event.clientX - rect.left;
                    let top = event.clientY - rect.top - 60;

                    // Guide line repositioning
                    if (guideLine) {
                        const cx = point.getAttribute('cx');
                        guideLine.setAttribute('x1', cx);
                        guideLine.setAttribute('x2', cx);
                        guideLine.style.opacity = '1';
                    }

                    if (left + tooltip.offsetWidth > rect.width - 15) {
                        left = rect.width - tooltip.offsetWidth - 15;
                    }
                    if (left < 15) {
                        left = 15;
                    }
                    if (top < 10) {
                        top = 10;
                    }

                    tooltip.style.left = left + 'px';
                    tooltip.style.top = top + 'px';
                }
            });

            point.addEventListener('mouseleave', function () {
                if (tooltip) {
                    tooltip.classList.add('hidden');
                    point.setAttribute('r', '4');
                }
                if (guideLine) {
                    guideLine.style.opacity = '0';
                }
            });

            point.addEventListener('click', function () {
                const selectedDate = point.dataset.date;
                window.location.href = "{{ route('dashboard') }}?end_date=" + encodeURIComponent(selectedDate);
            });
        });
    });
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Title & Status Telemetry -->
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <h2 class="font-extrabold text-2xl text-zinc-900 dark:text-white tracking-tight">
                        {{ __('Command Center') }}
                    </h2>
                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700/80 tracking-wide uppercase">
                        Real-Time Engine
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400">
                    <span>Operator: <strong class="text-zinc-800 dark:text-zinc-200 font-medium">{{ Auth::user()->name ?? 'Primary Admin' }}</strong></span>
                    <span class="text-zinc-300 dark:text-zinc-700">•</span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fas fa-satellite-dish text-3xs text-amber-500"></i>
                        <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $activeMailAccounts }}</span> of {{ $mailAccounts }} Gateways Active
                    </span>
                    <span class="text-zinc-300 dark:text-zinc-700">•</span>
                    <span class="font-mono text-3xs bg-zinc-100 dark:bg-zinc-800/80 px-2 py-0.5 rounded text-zinc-600 dark:text-zinc-400">
                        {{ now()->format('D, M d, Y') }}
                    </span>
                </div>
            </div>

            <!-- Executive Quick Actions Bar -->
            <div class="flex items-center flex-wrap gap-2 pt-1 lg:pt-0">
                <a href="/newsletter-form/new" class="group relative inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 gap-2 shrink-0">
                    <i class="fas fa-paper-plane text-2xs transition-transform group-hover:-rotate-12"></i>
                    <span>New Broadcast</span>
                </a>
                
                <a href="/dispatch" class="inline-flex items-center justify-center px-3.5 py-2 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-semibold rounded-xl border border-transparent transition-all hover:-translate-y-0.5 gap-2 shadow-2xs">
                    <i class="fas fa-layer-group text-2xs text-amber-400 dark:text-amber-600"></i>
                    <span>Dispatch Studio</span>
                </a>

                <a href="/import-contact-form" class="inline-flex items-center justify-center px-3.5 py-2 bg-white dark:bg-[#121215] text-zinc-700 dark:text-zinc-200 text-xs font-medium rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all gap-1.5 shadow-2xs">
                    <i class="fas fa-file-arrow-up text-3xs text-zinc-400"></i>
                    <span>Import</span>
                </a>

                <a href="/campaign-form/new" class="inline-flex items-center justify-center px-3.5 py-2 bg-white dark:bg-[#121215] text-zinc-700 dark:text-zinc-200 text-xs font-medium rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all gap-1.5 shadow-2xs">
                    <i class="fas fa-bullhorn text-3xs text-zinc-400"></i>
                    <span>Campaign</span>
                </a>

                <a href="/account-form/new" class="inline-flex items-center justify-center px-3 py-2 bg-white dark:bg-[#121215] text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 text-xs font-medium rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs" title="Add SMTP Relay">
                    <i class="fas fa-server text-3xs"></i>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- 4 High-End Glass KPI Cards with Layered Gradients -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Card 1: Subscribers & Audience -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-amber-500/40 dark:hover:border-amber-500/30 transition-all duration-200">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-amber-500/5 dark:bg-amber-400/5 blur-xl group-hover:bg-amber-500/10 transition-colors pointer-events-none"></div>
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
                            {{ number_format($contacts) }}
                        </div>
                        <div class="mt-2.5 flex items-center justify-between text-2xs pt-2 border-t border-zinc-100 dark:border-zinc-800/60">
                            <span class="text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5">
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                {{ $tagsCount }} Audience Segments
                            </span>
                            <a href="/contacts" class="text-amber-600 dark:text-amber-400 hover:underline font-medium">View →</a>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Broadcasts & Staged Output -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-blue-500/40 dark:hover:border-blue-500/30 transition-all duration-200">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-blue-500/5 dark:bg-blue-400/5 blur-xl group-hover:bg-blue-500/10 transition-colors pointer-events-none"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Broadcasts
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs border border-blue-200/50 dark:border-blue-500/20 shadow-2xs">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                            {{ number_format($newsletters) }}
                        </div>
                        <div class="mt-2.5 flex items-center justify-between text-2xs pt-2 border-t border-zinc-100 dark:border-zinc-800/60">
                            <span class="text-zinc-500 dark:text-zinc-400">
                                <strong class="text-blue-600 dark:text-blue-400 font-semibold">{{ $readyNewsletters }} ready</strong> • {{ $draftNewsletters }} drafts
                            </span>
                            <a href="/newsletters" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Drafts →</a>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Deliverability Health Rate -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-emerald-500/40 dark:hover:border-emerald-500/30 transition-all duration-200">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-emerald-500/5 dark:bg-emerald-400/5 blur-xl group-hover:bg-emerald-500/10 transition-colors pointer-events-none"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            Deliverability
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs border border-emerald-200/50 dark:border-emerald-500/20 shadow-2xs">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">{{ $deliveryRate }}%</span>
                            <span class="text-3xs font-semibold px-1.5 py-0.5 rounded {{ $deliveryRate >= 95 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' }}">
                                {{ $deliveryRate >= 95 ? 'Optimal' : 'Caution' }}
                            </span>
                        </div>
                        <div class="mt-2.5 flex items-center justify-between text-2xs pt-2 border-t border-zinc-100 dark:border-zinc-800/60">
                            <span class="text-zinc-500 dark:text-zinc-400">
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ number_format($sentMails) }} sent</span> • <span class="{{ $failedMails > 0 ? 'text-rose-500 font-semibold' : 'text-zinc-400' }}">{{ $failedMails }} errors</span>
                            </span>
                            <a href="/emails" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">Logs →</a>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Mail Relays & Sockets -->
                <div class="relative overflow-hidden group bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:border-zinc-400 dark:hover:border-zinc-600 transition-all duration-200">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-zinc-500/5 dark:bg-zinc-400/5 blur-xl group-hover:bg-zinc-500/10 transition-colors pointer-events-none"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                            SMTP Gateways
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center text-xs border border-zinc-200/50 dark:border-zinc-700/50 shadow-2xs">
                            <i class="fas fa-server"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                            {{ $activeMailAccounts }} <span class="text-base font-normal text-zinc-400">/ {{ $mailAccounts }}</span>
                        </div>
                        <div class="mt-2.5 flex items-center justify-between text-2xs pt-2 border-t border-zinc-100 dark:border-zinc-800/60">
                            <span class="text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5">
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Multi-Relay Rotation Ready
                            </span>
                            <a href="/mail-accounts" class="text-zinc-600 dark:text-zinc-400 hover:underline font-medium">Manage →</a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- High-Impact Mission Control / Dispatch Studio Hub Banner -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500/[0.08] via-amber-400/[0.03] to-amber-500/[0.09] dark:from-zinc-900 dark:via-zinc-950 dark:to-[#0c0c0e] p-6 sm:p-7 border border-amber-200/90 dark:border-amber-500/20 shadow-xs dark:shadow-xl">
                <!-- Background Ambient Lights -->
                <div class="absolute top-0 right-0 w-96 h-96 bg-amber-400/20 dark:bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-10 left-1/3 w-64 h-64 bg-amber-300/20 dark:bg-amber-400/5 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="space-y-2.5 max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-amber-100/90 dark:bg-amber-500/15 border border-amber-300/80 dark:border-amber-500/30 text-amber-900 dark:text-amber-300 text-3xs font-bold uppercase tracking-wider shadow-2xs">
                            <i class="fas fa-bolt text-amber-600 dark:text-amber-400"></i>
                            Dedicated Transmission Deck
                        </div>
                        <h3 class="text-lg sm:text-xl font-extrabold tracking-tight text-zinc-900 dark:text-white">
                            Broadcast Dispatch Studio & Pipeline Engine
                        </h3>
                        <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            Filter recipient segments, enforce exclusions, verify merge variables live, and execute controlled multi-gateway relay dispatch with real-time feedback.
                        </p>
                        <!-- 4-Stage Stepper Pill Strip -->
                        <div class="pt-2 flex flex-wrap items-center gap-2 text-3xs font-medium text-zinc-600 dark:text-zinc-300">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/60 text-zinc-700 dark:text-zinc-300 shadow-2xs">
                                <i class="fas fa-filter text-amber-500 dark:text-amber-400"></i> 1. Select Tags
                            </span>
                            <span class="text-zinc-300 dark:text-zinc-600 font-bold">→</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/60 text-zinc-700 dark:text-zinc-300 shadow-2xs">
                                <i class="fas fa-user-xmark text-amber-500 dark:text-amber-400"></i> 2. Exclude Unsubs
                            </span>
                            <span class="text-zinc-300 dark:text-zinc-600 font-bold">→</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/60 text-zinc-700 dark:text-zinc-300 shadow-2xs">
                                <i class="fas fa-code text-amber-500 dark:text-amber-400"></i> 3. Preview Tags
                            </span>
                            <span class="text-zinc-300 dark:text-zinc-600 font-bold">→</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-700/50 text-emerald-800 dark:text-emerald-300 shadow-2xs font-semibold">
                                <i class="fas fa-circle-play text-emerald-600 dark:text-emerald-400"></i> 4. Queue Dispatch
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                        @if($queuedMails > 0)
                            <form action="{{ route('queue.run-all') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold text-xs shadow-lg shadow-amber-500/20 transition-all gap-2">
                                    <i class="fas fa-play text-3xs"></i>
                                    <span>Run Queue Now ({{ $queuedMails }})</span>
                                </button>
                            </form>
                        @endif

                        <a href="/dispatch" class="group inline-flex items-center justify-center px-5 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-950 font-extrabold text-xs shadow-md shadow-zinc-900/10 dark:shadow-none transition-all gap-2.5">
                            <span>Open Dispatch Studio</span>
                            <i class="fas fa-arrow-right text-3xs transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pipeline Velocity Funnel (Connected Stage Progress Deck) -->
            <div class="bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                            <i class="fas fa-bars-progress"></i>
                        </div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                            Broadcast Pipeline Velocity
                        </h3>
                    </div>
                    <div class="flex items-center gap-3 text-2xs text-zinc-400 font-mono">
                        <span>Window: {{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Stage 1 -->
                    <div class="relative p-4 rounded-xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-200/60 dark:border-zinc-800/60 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                        <div class="flex items-center justify-between text-3xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2">
                            <span>Stage 01</span>
                            <span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                        </div>
                        <div class="text-xs font-medium text-zinc-600 dark:text-zinc-300">In Drafts</div>
                        <div class="mt-1 text-2xl font-black text-zinc-900 dark:text-white">{{ $draftNewsletters }}</div>
                        <p class="mt-2 text-3xs text-zinc-400">Unfinalized newsletter drafts</p>
                    </div>

                    <!-- Stage 2 -->
                    <div class="relative p-4 rounded-xl bg-blue-50/40 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40 hover:border-blue-300 dark:hover:border-blue-800 transition-colors">
                        <div class="flex items-center justify-between text-3xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2">
                            <span>Stage 02</span>
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        </div>
                        <div class="text-xs font-medium text-blue-800 dark:text-blue-300">Ready for Launch</div>
                        <div class="mt-1 text-2xl font-black text-blue-900 dark:text-blue-200">{{ $readyNewsletters }}</div>
                        <p class="mt-2 text-3xs text-blue-600/80 dark:text-blue-400/80">Approved and ready to dispatch</p>
                    </div>

                    <!-- Stage 3 -->
                    <div class="relative p-4 rounded-xl bg-amber-50/40 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/40 hover:border-amber-300 dark:hover:border-amber-800 transition-colors">
                        <div class="flex items-center justify-between text-3xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2">
                            <span>Stage 03</span>
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        </div>
                        <div class="text-xs font-medium text-amber-800 dark:text-amber-300">In Relay Queue</div>
                        <div class="mt-1 text-2xl font-black text-amber-900 dark:text-amber-200">{{ $queuedMails }}</div>
                        <p class="mt-2 text-3xs text-amber-600/80 dark:text-amber-400/80">Awaiting daemon execution</p>
                    </div>

                    <!-- Stage 4 -->
                    <div class="relative p-4 rounded-xl bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 hover:border-emerald-300 dark:hover:border-emerald-800 transition-colors">
                        <div class="flex items-center justify-between text-3xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-2">
                            <span>Stage 04</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        </div>
                        <div class="text-xs font-medium text-emerald-800 dark:text-emerald-300">Dispatched & Sent</div>
                        <div class="mt-1 text-2xl font-black text-emerald-900 dark:text-emerald-200">{{ number_format($sentMails) }}</div>
                        <p class="mt-2 text-3xs text-emerald-600/80 dark:text-emerald-400/80">Confirmed delivered to relays</p>
                    </div>
                </div>
            </div>

            <!-- Analytics Grid: 7-Day Trajectory (8 cols) + Distribution (4 cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- 7-Day Trajectory Area Chart (8 cols) with Smooth Bezier Waves -->
                <div class="lg:col-span-8 bg-white dark:bg-[#111114] p-6 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800/80 gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                                        Transmission Trajectory
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-full text-3xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        7-Day Flow
                                    </span>
                                </div>
                                <p class="text-2xs text-zinc-400 dark:text-zinc-500 mt-0.5">
                                    {{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }} • Click any data node to anchor analytics
                                </p>
                            </div>

                            <!-- Date Range Controls -->
                            <div class="flex items-center space-x-1.5 self-start sm:self-auto bg-zinc-50 dark:bg-zinc-900 p-1 rounded-xl border border-zinc-200/60 dark:border-zinc-800">
                                <a href="{{ route('dashboard', ['end_date' => $endDate->copy()->subDays(7)->format('Y-m-d')]) }}" 
                                   class="px-2.5 py-1 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white rounded-lg hover:bg-white dark:hover:bg-zinc-800 transition-colors text-2xs font-semibold flex items-center gap-1.5" title="Previous 7 days">
                                    <i class="fas fa-chevron-left text-3xs"></i>
                                    <span>Previous</span>
                                </a>

                                @if($endDate->lt(now()->startOfDay()))
                                    <a href="{{ route('dashboard', ['end_date' => $endDate->copy()->addDays(7)->format('Y-m-d')]) }}" 
                                       class="px-2.5 py-1 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white rounded-lg hover:bg-white dark:hover:bg-zinc-800 transition-colors text-2xs font-semibold flex items-center gap-1.5" title="Next 7 days">
                                        <span>Next</span>
                                        <i class="fas fa-chevron-right text-3xs"></i>
                                    </a>
                                @else
                                    <span class="px-2.5 py-1 text-zinc-300 dark:text-zinc-700 cursor-not-allowed text-2xs font-semibold flex items-center gap-1.5">
                                        <span>Latest</span>
                                        <i class="fas fa-chevron-right text-3xs"></i>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- High-End Smooth Bezier Wave SVG Chart -->
                        <div id="chartContainer" class="mt-6 relative">
                            @php
                                $chartWidth = 650;
                                $chartHeight = 190;
                                $leftPadding = 40;
                                $rightPadding = 20;
                                $topPadding = 20;
                                $bottomPadding = 30;

                                $usableWidth = $chartWidth - $leftPadding - $rightPadding;
                                $usableHeight = $chartHeight - $topPadding - $bottomPadding;
                                $pointCount = count($emailCounts);
                                
                                $rawPoints = [];
                                foreach ($emailCounts as $index => $value) {
                                    $x = $leftPadding;
                                    if ($pointCount > 1) {
                                        $x += $index * ($usableWidth / ($pointCount - 1));
                                    }
                                    $y = $topPadding + $usableHeight - (($value / $yAxisMax) * $usableHeight);
                                    $rawPoints[] = ['x' => $x, 'y' => $y, 'val' => $value];
                                }

                                // High-End Smooth Spline Path Generator (Catmull-Rom to Cubic Bezier)
                                $pathD = '';
                                if ($pointCount > 0) {
                                    $pathD = "M {$rawPoints[0]['x']},{$rawPoints[0]['y']}";
                                    for ($i = 0; $i < $pointCount - 1; $i++) {
                                        $p0 = $i > 0 ? $rawPoints[$i - 1] : $rawPoints[$i];
                                        $p1 = $rawPoints[$i];
                                        $p2 = $rawPoints[$i + 1];
                                        $p3 = $i + 2 < $pointCount ? $rawPoints[$i + 2] : $p2;

                                        $cp1x = round($p1['x'] + ($p2['x'] - $p0['x']) / 5.5, 2);
                                        $cp1y = round($p1['y'] + ($p2['y'] - $p0['y']) / 5.5, 2);
                                        $cp2x = round($p2['x'] - ($p3['x'] - $p1['x']) / 5.5, 2);
                                        $cp2y = round($p2['y'] - ($p3['y'] - $p1['y']) / 5.5, 2);

                                        $pathD .= " C $cp1x,$cp1y $cp2x,$cp2y {$p2['x']},{$p2['y']}";
                                    }
                                }

                                $firstX = $leftPadding;
                                $lastX = $leftPadding + $usableWidth;
                                $groundY = $topPadding + $usableHeight;
                                $areaPathD = $pathD . " L $lastX,$groundY L $firstX,$groundY Z";
                                $gridSteps = 3;
                                $periodTotal = array_sum($emailCounts);
                            @endphp

                            <!-- Glassmorphic Tooltip -->
                            <div id="chartTooltip" class="hidden absolute z-30 backdrop-blur-md bg-zinc-950/90 dark:bg-zinc-900/95 text-white text-3xs rounded-xl px-3 py-2 border border-zinc-700/60 shadow-xl pointer-events-none transition-all duration-75">
                                <div id="tooltipDate" class="text-amber-400 font-bold uppercase tracking-wider text-3xs"></div>
                                <div id="tooltipCount" class="font-extrabold text-sm text-white mt-0.5"></div>
                                <div id="tooltipSub" class="text-zinc-400 text-3xs mt-0.5"></div>
                            </div>

                            <svg id="emailChart" viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-48 overflow-visible" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="curveGradientFill" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.32"/>
                                        <stop offset="45%" stop-color="#f59e0b" stop-opacity="0.10"/>
                                        <stop offset="100%" stop-color="#f59e0b" stop-opacity="0.0"/>
                                    </linearGradient>

                                    <linearGradient id="strokeGradient" x1="0" y1="0" x2="1" y2="0">
                                        <stop offset="0%" stop-color="#f59e0b"/>
                                        <stop offset="50%" stop-color="#fbbf24"/>
                                        <stop offset="100%" stop-color="#d97706"/>
                                    </linearGradient>

                                    <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
                                        <feGaussianBlur stdDeviation="3" result="blur" />
                                        <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                                    </filter>
                                </defs>

                                <!-- Grid Lines & Axis Numbers -->
                                @for($i = 0; $i <= $gridSteps; $i++)
                                    @php
                                        $gridValue = round($yAxisMax - ($yAxisMax / $gridSteps) * $i);
                                        $gridY = $topPadding + ($usableHeight / $gridSteps) * $i;
                                    @endphp
                                    <line x1="{{ $leftPadding }}" y1="{{ $gridY }}" x2="{{ $chartWidth - $rightPadding }}" y2="{{ $gridY }}" stroke="currentColor" class="text-zinc-100 dark:text-zinc-800/80" stroke-dasharray="3 3" stroke-width="1" />
                                    <text x="8" y="{{ $gridY + 3 }}" font-size="9" class="fill-zinc-400 dark:fill-zinc-500 font-mono font-medium">{{ number_format($gridValue) }}</text>
                                @endfor

                                <!-- Interactive Vertical Tracking Guide Line -->
                                <line id="chartGuideLine" x1="0" y1="{{ $topPadding }}" x2="0" y2="{{ $groundY }}" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="2 2" opacity="0" class="transition-opacity duration-150 pointer-events-none" />

                                <!-- Smooth Shaded Area -->
                                @if($pointCount > 0)
                                    <path d="{{ $areaPathD }}" fill="url(#curveGradientFill)" />
                                    <path d="{{ $pathD }}" fill="none" stroke="url(#strokeGradient)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                @endif

                                <!-- Interactive Data Point Rings -->
                                @foreach($rawPoints as $index => $pt)
                                    @php
                                        $pctOfTotal = $periodTotal > 0 ? round(($pt['val'] / $periodTotal) * 100, 1) : 0;
                                    @endphp
                                    <g class="cursor-pointer">
                                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="8" fill="transparent" class="email-chart-point" data-date="{{ $emailFullDates[$index] }}" data-label="{{ $emailDates[$index] }}" data-count="{{ $pt['val'] }}" data-pct="{{ $pctOfTotal }}" />
                                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4" fill="#f59e0b" stroke="#ffffff" class="dark:stroke-[#111114]" stroke-width="2" pointer-events="none" />
                                    </g>
                                @endforeach

                                <!-- Dates on X-Axis -->
                                @foreach($rawPoints as $index => $pt)
                                    <text x="{{ $pt['x'] }}" y="{{ $chartHeight - 6 }}" text-anchor="middle" font-size="9.5" class="fill-zinc-400 dark:fill-zinc-500 font-medium tracking-tight">{{ $emailDates[$index] }}</text>
                                @endforeach
                            </svg>
                        </div>
                    </div>

                    <!-- Telemetry Summary Bar -->
                    <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 grid grid-cols-3 gap-2 text-center sm:text-left">
                        <div>
                            <span class="text-3xs text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Period Total</span>
                            <span class="text-xs sm:text-sm font-extrabold text-zinc-900 dark:text-white">{{ number_format($periodTotal) }}</span>
                        </div>
                        <div>
                            <span class="text-3xs text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Peak Daily Output</span>
                            <span class="text-xs sm:text-sm font-extrabold text-zinc-900 dark:text-white">{{ number_format($maxEmailCount ?? max($emailCounts ?? [0])) }}</span>
                        </div>
                        <div>
                            <span class="text-3xs text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Daily Mean</span>
                            <span class="text-xs sm:text-sm font-extrabold text-zinc-900 dark:text-white">{{ number_format(round($periodTotal / max(count($emailCounts), 1))) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Queue & Transmission Health (4 cols) -->
                <div class="lg:col-span-4 bg-white dark:bg-[#111114] p-6 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800/80">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                                    Queue & Health
                                </h3>
                                <p class="text-2xs text-zinc-400 dark:text-zinc-500 mt-0.5">
                                    Live transmission status breakdown
                                </p>
                            </div>
                            <span class="w-2 h-2 rounded-full {{ $failedMails > 0 ? 'bg-amber-400 animate-ping' : 'bg-emerald-400' }}"></span>
                        </div>

                        @php
                            $totalEmails = $sentMails + $queuedMails + $failedMails;
                            $sentPct = $totalEmails > 0 ? round(($sentMails / $totalEmails) * 100, 1) : 0;
                            $queuedPct = $totalEmails > 0 ? round(($queuedMails / $totalEmails) * 100, 1) : 0;
                            $failedPct = $totalEmails > 0 ? round(($failedMails / $totalEmails) * 100, 1) : 0;
                        @endphp

                        <!-- Segmented Volume Progress Bar -->
                        <div class="mt-5 space-y-4">
                            <!-- High-end Multi-Segment Bar -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-3xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    <span>Capacity Distribution</span>
                                    <span>{{ number_format($totalEmails) }} Total Jobs</span>
                                </div>
                                <div class="w-full h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden flex">
                                    <div class="bg-emerald-500 h-full transition-all" style="width: {{ $sentPct }}%" title="Delivered: {{ $sentPct }}%"></div>
                                    <div class="bg-blue-400 h-full transition-all" style="width: {{ $queuedPct }}%" title="Queued: {{ $queuedPct }}%"></div>
                                    <div class="bg-rose-500 h-full transition-all" style="width: {{ $failedPct }}%" title="Failed: {{ $failedPct }}%"></div>
                                </div>
                            </div>

                            <!-- Delivered Row -->
                            <div class="p-2.5 rounded-xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Delivered</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold text-zinc-900 dark:text-white">{{ number_format($sentMails) }}</span>
                                    <span class="text-3xs text-zinc-400 ml-1">({{ $sentPct }}%)</span>
                                </div>
                            </div>

                            <!-- Queued Row -->
                            <div class="p-2.5 rounded-xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                    <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">In Relay Queue</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold text-zinc-900 dark:text-white">{{ number_format($queuedMails) }}</span>
                                    <span class="text-3xs text-zinc-400 ml-1">({{ $queuedPct }}%)</span>
                                </div>
                            </div>

                            <!-- Failed Row -->
                            <div class="p-2.5 rounded-xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                    <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Failed / Errors</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold {{ $failedMails > 0 ? 'text-rose-500' : 'text-zinc-900 dark:text-white' }}">{{ number_format($failedMails) }}</span>
                                    <span class="text-3xs text-zinc-400 ml-1">({{ $failedPct }}%)</span>
                                </div>
                            </div>

                            @if(count($failureCodes) > 0)
                                <div class="pt-2">
                                    <span class="text-3xs font-semibold uppercase tracking-wider text-zinc-400 block mb-1.5">SMTP Error Telemetry</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($failureCodes as $code)
                                            <span class="px-2 py-0.5 rounded text-3xs font-mono bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-900">
                                                Code {{ $code->response_code }}: {{ $code->count }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footnote Links -->
                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-2xs">
                        <span class="text-zinc-500 dark:text-zinc-400">Campaigns active: <strong class="text-zinc-800 dark:text-zinc-200">{{ $campaigns }}</strong></span>
                        <a href="/emails" class="font-bold text-amber-600 dark:text-amber-400 hover:underline">Inspect Logs →</a>
                    </div>
                </div>

            </div>

            <!-- Bottom Section: Live Dispatches (8 cols) + Connected Gateways & Segments (4 cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Recent Dispatches List (8 cols) -->
                <div class="lg:col-span-8 bg-white dark:bg-[#111114] rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                                    <i class="fas fa-tower-broadcast"></i>
                                </div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                                    Live Dispatch Activity
                                </h3>
                            </div>
                            <a href="/emails" class="inline-flex items-center gap-1 text-2xs font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition-colors">
                                <span>Complete Log</span>
                                <i class="fas fa-arrow-right text-3xs"></i>
                            </a>
                        </div>

                        @if(count($recentActivity) > 0)
                            <div class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-xs">
                                @foreach($recentActivity as $activity)
                                    <div class="p-4 flex items-center justify-between hover:bg-zinc-50/60 dark:hover:bg-zinc-900/30 transition-colors gap-3">
                                        <div class="flex items-center space-x-3.5 min-w-0">
                                            <!-- Status Beacon -->
                                            <div class="shrink-0">
                                                @if($activity['status'] === 'sent')
                                                    <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block" title="Delivered"></span>
                                                @elseif($activity['status'] === 'queued')
                                                    <span class="w-2 h-2 rounded-full bg-blue-400 inline-block animate-pulse" title="Queued"></span>
                                                @else
                                                    <span class="w-2 h-2 rounded-full bg-rose-500 inline-block" title="Error"></span>
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                                        {{ $activity['subject'] }}
                                                    </span>
                                                    <span class="hidden sm:inline-block px-2 py-0.5 rounded text-3xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 shrink-0">
                                                        {{ $activity['campaign_name'] }}
                                                    </span>
                                                </div>
                                                <div class="text-3xs text-zinc-400 dark:text-zinc-500 mt-0.5 flex items-center gap-2 truncate">
                                                    <span class="truncate text-zinc-600 dark:text-zinc-400 font-mono">{{ $activity['recipient'] }}</span>
                                                    <span>•</span>
                                                    <span>Via {{ $activity['sender'] }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <span class="text-3xs font-mono text-zinc-400 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 rounded border border-zinc-100 dark:border-zinc-800/80">
                                                {{ $activity['timestamp'] ? \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans(null, true) . ' ago' : 'Just now' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-12 text-center space-y-2">
                                <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800/60 text-zinc-400 flex items-center justify-center mx-auto text-sm">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <p class="text-xs text-zinc-400">No recent dispatch activity recorded yet.</p>
                                <a href="/newsletter-form/new" class="inline-block text-xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">
                                    Launch a broadcast →
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Footer Link -->
                    <div class="p-3 bg-zinc-50/50 dark:bg-zinc-900/30 border-t border-zinc-100 dark:border-zinc-800/80 text-center">
                        <a href="/emails" class="text-2xs font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition-colors">
                            Inspect all email delivery receipts & error logs →
                        </a>
                    </div>
                </div>

                <!-- Segments & SMTP Relays (4 cols) -->
                <div class="lg:col-span-4 space-y-6">

                    <!-- Connected SMTP Relays -->
                    <div class="bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs space-y-3">
                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center text-xs">
                                    <i class="fas fa-network-wired"></i>
                                </div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                                    SMTP Relays
                                </h4>
                            </div>
                            <a href="/mail-accounts" class="text-3xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">Manage</a>
                        </div>

                        @if(count($mailAccountList) > 0)
                            <div class="space-y-2">
                                @foreach($mailAccountList as $acc)
                                    @php
                                        $isActive = (int)$acc->status === 1;
                                        $isCooling = $isActive && !empty($acc->active_after) && \Carbon\Carbon::parse($acc->active_after)->gt(now());
                                    @endphp
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50/60 dark:bg-zinc-900/40 border border-zinc-100 dark:border-zinc-800/60 hover:border-zinc-200 dark:hover:border-zinc-700 transition-colors">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="relative flex h-2 w-2 shrink-0">
                                                @if($isActive && !$isCooling)
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                                @elseif($isCooling)
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-400"></span>
                                                @else
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-zinc-400"></span>
                                                @endif
                                            </span>
                                            <span class="text-xs font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $acc->name }}</span>
                                        </div>
                                        @if($isActive && !$isCooling)
                                            <span class="text-3xs font-mono px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
                                                Active
                                            </span>
                                        @elseif($isCooling)
                                            <span class="text-3xs font-mono px-2 py-0.5 rounded bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">
                                                Cooldown
                                            </span>
                                        @else
                                            <span class="text-3xs font-mono px-2 py-0.5 rounded bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                                Inactive
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 space-y-1">
                                <p class="text-xs text-zinc-400">No relays configured.</p>
                                <a href="/account-form/new" class="text-3xs font-bold text-amber-600 dark:text-amber-400 hover:underline">+ Add Gateway</a>
                            </div>
                        @endif
                    </div>

                    <!-- Top Audience Segments -->
                    <div class="bg-white dark:bg-[#111114] p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs space-y-3">
                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                                    Audience Segments
                                </h4>
                            </div>
                            <a href="/tags" class="text-3xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">All Tags</a>
                        </div>

                        @if(count($topTags) > 0)
                            <div class="space-y-3">
                                @foreach($topTags as $tag)
                                    @php
                                        $tagPct = $contacts > 0 ? round(($tag->contacts_count / $contacts) * 100, 1) : 0;
                                    @endphp
                                    <div class="space-y-1.5">
                                        <div class="flex justify-between items-center text-xs">
                                            <a href="/contacts?tag={{ urlencode($tag->label) }}" class="font-medium text-zinc-800 dark:text-zinc-200 truncate hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                                                {{ $tag->label }}
                                            </a>
                                            <span class="text-zinc-400 text-3xs font-mono">{{ number_format($tag->contacts_count) }} ({{ $tagPct }}%)</span>
                                        </div>
                                        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ $tagPct }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 space-y-1">
                                <p class="text-xs text-zinc-400">No tags created yet.</p>
                                <a href="/tag-form/new" class="text-3xs font-bold text-amber-600 dark:text-amber-400 hover:underline">+ Create Tag</a>
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>