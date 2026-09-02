@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const points = document.querySelectorAll('.email-chart-point');
        const tooltip = document.getElementById('chartTooltip');
        const tooltipDate = document.getElementById('tooltipDate');
        const tooltipCount = document.getElementById('tooltipCount');

        points.forEach(function (point) {
            point.addEventListener('mouseenter', function (event) {
                if (tooltipDate && tooltipCount && tooltip) {
                    tooltipDate.textContent = point.dataset.label;
                    tooltipCount.textContent = point.dataset.count + ' sent';
                    tooltip.classList.remove('hidden');
                    point.setAttribute('r', '5');
                }
            });

            point.addEventListener('mousemove', function (event) {
                if (tooltip) {
                    const container = point.closest('.relative');
                    const rect = container.getBoundingClientRect();

                    let left = event.clientX - rect.left + 10;
                    let top = event.clientY - rect.top - 45;

                    if (left + tooltip.offsetWidth > rect.width) {
                        left = rect.width - tooltip.offsetWidth - 10;
                    }

                    if (top < 0) {
                        top = 10;
                    }

                    tooltip.style.left = left + 'px';
                    tooltip.style.top = top + 'px';
                }
            });

            point.addEventListener('mouseleave', function () {
                if (tooltip) {
                    tooltip.classList.add('hidden');
                    point.setAttribute('r', '3.5');
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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-amber-300 dark:bg-amber-400"></span>
                    <h2 class="font-bold text-xl text-zinc-900 dark:text-zinc-100 tracking-tight">
                        {{ __('Command Center') }}
                    </h2>
                </div>
                <p class="text-2xs text-zinc-400 dark:text-zinc-500 mt-0.5 pl-4">
                    Welcome, <strong class="text-zinc-700 dark:text-zinc-300 font-medium">{{ Auth::user()->name ?? 'Operator' }}</strong> • <span class="text-zinc-600 dark:text-zinc-400">{{ $activeMailAccounts }} active gateways</span> on schedule
                </p>
            </div>
            
            <!-- Minimal Quick Actions Bar -->
            <div class="flex items-center flex-wrap gap-2">
                <a href="/newsletter-form/new" class="inline-flex items-center px-3.5 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-900 text-xs font-semibold rounded-lg transition-colors gap-1.5 shadow-2xs">
                    <i class="fas fa-plus text-3xs opacity-70"></i>
                    <span>New Broadcast</span>
                </a>
                <a href="/import-contact-form" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-[#111113] text-zinc-700 dark:text-zinc-300 text-xs font-medium rounded-lg border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors gap-1.5 shadow-2xs">
                    <i class="fas fa-file-import text-3xs text-zinc-400"></i>
                    <span>Import Contacts</span>
                </a>
                <a href="/campaign-form/new" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-[#111113] text-zinc-700 dark:text-zinc-300 text-xs font-medium rounded-lg border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors gap-1.5 shadow-2xs">
                    <i class="fas fa-bullhorn text-3xs text-zinc-400"></i>
                    <span>New Campaign</span>
                </a>
                <a href="/account-form/new" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-[#111113] text-zinc-700 dark:text-zinc-300 text-xs font-medium rounded-lg border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors gap-1.5 shadow-2xs">
                    <i class="fas fa-server text-3xs text-zinc-400"></i>
                    <span>Add SMTP</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- 4 Clean, Light-Shaded Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Card 1: Subscribers -->
                <div class="bg-white dark:bg-[#111113] p-5 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Subscribers</span>
                        <span class="p-1 rounded-md bg-amber-50/50 dark:bg-amber-950/20 text-amber-700/80 dark:text-amber-300/80 text-xs">
                            <i class="fas fa-users"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">{{ number_format($contacts) }}</div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-1 flex items-center gap-1">
                            <span>{{ $tagsCount }} audience segments</span>
                        </p>
                    </div>
                </div>

                <!-- Card 2: Newsletters -->
                <div class="bg-white dark:bg-[#111113] p-5 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Newsletters</span>
                        <span class="p-1 rounded-md bg-blue-50/50 dark:bg-blue-950/20 text-blue-700/80 dark:text-blue-300/80 text-xs">
                            <i class="fas fa-paper-plane"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">{{ number_format($newsletters) }}</div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-1">
                            <span class="text-blue-700/80 dark:text-blue-300/80 font-medium">{{ $readyNewsletters }} ready</span> • <span>{{ $draftNewsletters }} draft</span>
                        </p>
                    </div>
                </div>

                <!-- Card 3: Total Delivered -->
                <div class="bg-white dark:bg-[#111113] p-5 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Delivery Success</span>
                        <span class="p-1 rounded-md bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700/80 dark:text-emerald-300/80 text-xs">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">{{ $deliveryRate }}%</div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-1">
                            <span class="text-emerald-700/80 dark:text-emerald-300/80 font-medium">{{ number_format($sentMails) }} sent</span> • <span class="{{ $failedMails > 0 ? 'text-rose-500' : 'text-zinc-400' }}">{{ $failedMails }} errors</span>
                        </p>
                    </div>
                </div>

                <!-- Card 4: Active Gateways -->
                <div class="bg-white dark:bg-[#111113] p-5 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Mail Accounts</span>
                        <span class="p-1 rounded-md bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 dark:text-zinc-400 text-xs">
                            <i class="fas fa-server"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">{{ $activeMailAccounts }} <span class="text-sm font-normal text-zinc-400">/ {{ $mailAccounts }}</span></div>
                        <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block mr-0.5"></span> Active on schedule
                        </p>
                    </div>
                </div>

            </div>

            <!-- Quick Dispatch Studio Launch Card -->
            <div class="bg-white dark:bg-[#111113] p-5 rounded-2xl border border-amber-200/80 dark:border-amber-500/20 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 flex items-center justify-center text-lg border border-amber-200/60 dark:border-amber-500/30 shrink-0 shadow-2xs">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Broadcast Dispatch Studio</h3>
                            <span class="px-2 py-0.5 rounded-full text-3xs font-extrabold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                4-Stage Workflow Ready
                            </span>
                        </div>
                        <p class="text-2xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Inspect your audience, toggle exclusions, preview personalized merge tags, and execute real-time batch transmission with full control.
                        </p>
                    </div>
                </div>
                <a href="/dispatch" class="inline-flex items-center justify-center px-4 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-xs font-bold rounded-xl shadow-2xs transition-all gap-2 shrink-0">
                    <span>Open Dispatch Studio</span>
                    <i class="fas fa-arrow-right text-3xs"></i>
                </a>
            </div>

            <!-- Clean Pipeline Velocity Funnel (Subtle Light-Toned Bar) -->
            <div class="bg-white dark:bg-[#111113] p-4 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 shadow-2xs space-y-2.5">
                <div class="flex items-center justify-between text-2xs text-zinc-400 dark:text-zinc-500 border-b border-zinc-100 dark:border-zinc-800/60 pb-2">
                    <span class="font-medium text-zinc-600 dark:text-zinc-400 flex items-center gap-1.5">
                        <i class="fas fa-stream text-3xs text-amber-500/70"></i> Broadcast Pipeline Velocity
                    </span>
                    <span class="font-mono text-3xs">{{ $startDate->format('M d') }} — {{ $endDate->format('M d, Y') }}</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
                    <div class="p-2.5 rounded-lg bg-zinc-50/60 dark:bg-zinc-900/40 border border-zinc-100 dark:border-zinc-800/60">
                        <span class="text-3xs text-zinc-400 block mb-0.5 font-medium">1. In Drafts</span>
                        <div class="text-lg font-bold text-zinc-800 dark:text-zinc-200">{{ $draftNewsletters }}</div>
                    </div>
                    <div class="p-2.5 rounded-lg bg-blue-50/40 dark:bg-blue-950/20 border border-blue-100/60 dark:border-blue-900/30">
                        <span class="text-3xs text-blue-700/80 dark:text-blue-300/80 block mb-0.5 font-medium">2. Ready / New</span>
                        <div class="text-lg font-bold text-blue-900 dark:text-blue-200">{{ $readyNewsletters }}</div>
                    </div>
                    <div class="p-2.5 rounded-lg bg-amber-50/40 dark:bg-amber-950/20 border border-amber-100/60 dark:border-amber-900/30">
                        <span class="text-3xs text-amber-700/80 dark:text-amber-300/80 block mb-0.5 font-medium">3. In Queue</span>
                        <div class="text-lg font-bold text-amber-900 dark:text-amber-200">{{ $queuedMails }}</div>
                    </div>
                    <div class="p-2.5 rounded-lg bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-100/60 dark:border-emerald-900/30">
                        <span class="text-3xs text-emerald-700/80 dark:text-emerald-300/80 block mb-0.5 font-medium">4. Delivered</span>
                        <div class="text-lg font-bold text-emerald-900 dark:text-emerald-200">{{ $sentMails }}</div>
                    </div>
                </div>
            </div>

            <!-- Analytics Grid: 7-Day Trajectory (8 cols) + Distribution (4 cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- 7-Day Trajectory Area Chart (8 cols) -->
                <div class="lg:col-span-8 bg-white dark:bg-[#111113] p-6 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800/60">
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    Transmission Trajectory
                                </h3>
                                <p class="text-2xs text-zinc-400 dark:text-zinc-500 mt-0.5">
                                    {{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}
                                </p>
                            </div>

                            <!-- Week Navigation -->
                            <div class="flex items-center space-x-1.5">
                                <a href="{{ route('dashboard', ['end_date' => $endDate->copy()->subDays(7)->format('Y-m-d')]) }}" 
                                   class="p-1.5 text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors" title="Previous 7 days">
                                    <i class="fas fa-chevron-left text-2xs"></i>
                                </a>

                                @if($endDate->lt(now()->startOfDay()))
                                    <a href="{{ route('dashboard', ['end_date' => $endDate->copy()->addDays(7)->format('Y-m-d')]) }}" 
                                       class="p-1.5 text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors" title="Next 7 days">
                                        <i class="fas fa-chevron-right text-2xs"></i>
                                    </a>
                                @else
                                    <span class="p-1.5 text-zinc-300 dark:text-zinc-700 cursor-not-allowed">
                                        <i class="fas fa-chevron-right text-2xs"></i>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Minimal SVG Line Chart with Whisper Gradient -->
                        <div class="mt-5 relative">
                            @php
                                $chartWidth = 600;
                                $chartHeight = 180;
                                $leftPadding = 35;
                                $rightPadding = 15;
                                $topPadding = 15;
                                $bottomPadding = 30;

                                $usableWidth = $chartWidth - $leftPadding - $rightPadding;
                                $usableHeight = $chartHeight - $topPadding - $bottomPadding;
                                $pointCount = count($emailCounts);
                                $points = [];
                                $areaPoints = [];

                                foreach ($emailCounts as $index => $value) {
                                    $x = $leftPadding;
                                    if ($pointCount > 1) {
                                        $x += $index * ($usableWidth / ($pointCount - 1));
                                    }
                                    $y = $topPadding + $usableHeight - (($value / $yAxisMax) * $usableHeight);
                                    $points[] = round($x, 2) . ',' . round($y, 2);
                                }

                                $firstX = $leftPadding;
                                $lastX = $leftPadding + $usableWidth;
                                $groundY = $topPadding + $usableHeight;
                                $areaPointsString = "$firstX,$groundY " . implode(' ', $points) . " $lastX,$groundY";
                                $pointsString = implode(' ', $points);
                                $gridSteps = 3;
                            @endphp

                            <!-- Tooltip -->
                            <div id="chartTooltip" class="hidden absolute z-20 bg-zinc-900 dark:bg-zinc-800 text-white text-3xs rounded-lg px-2.5 py-1.5 shadow-md pointer-events-none transition-all duration-100">
                                <div id="tooltipDate" class="text-zinc-400 font-medium"></div>
                                <div id="tooltipCount" class="font-bold text-white mt-0.5"></div>
                            </div>

                            <svg id="emailChart" viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-44" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="whisperAreaGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.10"/>
                                        <stop offset="100%" stop-color="#f59e0b" stop-opacity="0.0"/>
                                    </linearGradient>
                                </defs>

                                <!-- Grid Lines -->
                                @for($i = 0; $i <= $gridSteps; $i++)
                                    @php
                                        $gridValue = round($yAxisMax - ($yAxisMax / $gridSteps) * $i);
                                        $gridY = $topPadding + ($usableHeight / $gridSteps) * $i;
                                    @endphp
                                    <line x1="{{ $leftPadding }}" y1="{{ $gridY }}" x2="{{ $chartWidth - $rightPadding }}" y2="{{ $gridY }}" stroke="currentColor" class="text-zinc-100 dark:text-zinc-800/60" stroke-width="1" />
                                    <text x="5" y="{{ $gridY + 3 }}" font-size="9" class="fill-zinc-400 dark:fill-zinc-500 font-mono">{{ $gridValue }}</text>
                                @endfor

                                <!-- Area & Line -->
                                @if($pointCount > 0)
                                    <polygon points="{{ $areaPointsString }}" fill="url(#whisperAreaGradient)" />
                                    <polyline points="{{ $pointsString }}" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="dark:stroke-amber-400" />
                                @endif

                                <!-- Data Points -->
                                @foreach($emailCounts as $index => $value)
                                    @php
                                        $x = $leftPadding;
                                        if ($pointCount > 1) {
                                            $x += $index * ($usableWidth / ($pointCount - 1));
                                        }
                                        $y = $topPadding + $usableHeight - (($value / $yAxisMax) * $usableHeight);
                                    @endphp
                                    <circle class="email-chart-point cursor-pointer transition-all duration-150" cx="{{ $x }}" cy="{{ $y }}" r="3.5" fill="#d97706" class="dark:fill-amber-400" stroke="#ffffff" stroke-width="1.5" data-date="{{ $emailFullDates[$index] }}" data-label="{{ $emailDates[$index] }}" data-count="{{ $value }}" />
                                @endforeach

                                <!-- Dates on X-Axis -->
                                @foreach($emailDates as $index => $date)
                                    @php
                                        $x = $leftPadding;
                                        if ($pointCount > 1) {
                                            $x += $index * ($usableWidth / ($pointCount - 1));
                                        }
                                    @endphp
                                    <text x="{{ $x }}" y="{{ $chartHeight - 6 }}" text-anchor="middle" font-size="9" class="fill-zinc-400 dark:fill-zinc-500">{{ $date }}</text>
                                @endforeach
                            </svg>
                        </div>
                    </div>

                    <!-- Chart Summary Line -->
                    <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between text-2xs text-zinc-500 dark:text-zinc-400">
                        <span>Total period output: <strong class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ array_sum($emailCounts) }} emails</strong></span>
                        <span>Daily peak: <strong class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ $maxEmailCount ?? max($emailCounts ?? [0]) }}</strong></span>
                    </div>
                </div>

                <!-- Queue & Status Distribution (4 cols) -->
                <div class="lg:col-span-4 bg-white dark:bg-[#111113] p-6 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="pb-4 border-b border-zinc-100 dark:border-zinc-800/60">
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                Queue & Delivery Status
                            </h3>
                            <p class="text-2xs text-zinc-400 dark:text-zinc-500 mt-0.5">
                                Current transmission distribution
                            </p>
                        </div>

                        @php
                            $totalEmails = $sentMails + $queuedMails + $failedMails;
                            $sentPct = $totalEmails > 0 ? round(($sentMails / $totalEmails) * 100, 1) : 0;
                            $queuedPct = $totalEmails > 0 ? round(($queuedMails / $totalEmails) * 100, 1) : 0;
                            $failedPct = $totalEmails > 0 ? round(($failedMails / $totalEmails) * 100, 1) : 0;
                        @endphp

                        <div class="mt-5 space-y-4">
                            <!-- Sent Bar -->
                            <div>
                                <div class="flex justify-between items-center text-xs mb-1">
                                    <span class="text-zinc-600 dark:text-zinc-400 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Delivered
                                    </span>
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $sentMails }} <span class="text-zinc-400 font-normal">({{ $sentPct }}%)</span></span>
                                </div>
                                <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5">
                                    <div class="bg-emerald-400 h-1.5 rounded-full" style="width: {{ $sentPct }}%"></div>
                                </div>
                            </div>

                            <!-- Queued Bar -->
                            <div>
                                <div class="flex justify-between items-center text-xs mb-1">
                                    <span class="text-zinc-600 dark:text-zinc-400 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-300"></span> In Queue
                                    </span>
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $queuedMails }} <span class="text-zinc-400 font-normal">({{ $queuedPct }}%)</span></span>
                                </div>
                                <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5">
                                    <div class="bg-blue-300 dark:bg-blue-400 h-1.5 rounded-full" style="width: {{ $queuedPct }}%"></div>
                                </div>
                            </div>

                            <!-- Failed Bar -->
                            <div>
                                <div class="flex justify-between items-center text-xs mb-1">
                                    <span class="text-zinc-600 dark:text-zinc-400 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Failed / Errors
                                    </span>
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $failedMails }} <span class="text-zinc-400 font-normal">({{ $failedPct }}%)</span></span>
                                </div>
                                <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5">
                                    <div class="bg-rose-400 h-1.5 rounded-full" style="width: {{ $failedPct }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pipeline summary -->
                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between text-2xs text-zinc-500 dark:text-zinc-400">
                        <span>Campaigns active: <strong class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ $campaigns }}</strong></span>
                        <a href="/emails" class="text-zinc-600 dark:text-zinc-300 hover:underline font-medium">View Logs →</a>
                    </div>
                </div>

            </div>

            <!-- Bottom Section: Recent Dispatches (8 cols) + Segments & Gateways (4 cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Recent Dispatches List (8 cols) -->
                <div class="lg:col-span-8 bg-white dark:bg-[#111113] rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 shadow-2xs overflow-hidden">
                    <div class="p-5 border-b border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                            Recent Dispatches
                        </h3>
                        <a href="/emails" class="text-2xs font-medium text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            All Logs →
                        </a>
                    </div>

                    @if(count($recentActivity) > 0)
                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-xs">
                            @foreach($recentActivity as $activity)
                                <div class="p-4 flex items-center justify-between hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                                    <div class="flex items-center space-x-3 truncate">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $activity['status'] === 'sent' ? 'bg-emerald-400' : ($activity['status'] === 'queued' ? 'bg-blue-300' : 'bg-rose-400') }} shrink-0"></div>
                                        <div class="truncate">
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                                {{ $activity['subject'] }}
                                            </div>
                                            <div class="text-3xs text-zinc-400 dark:text-zinc-500 mt-0.5">
                                                To: {{ $activity['recipient'] }} • {{ $activity['campaign_name'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 ml-4">
                                        <span class="text-3xs font-mono text-zinc-400">
                                            {{ $activity['timestamp'] ? \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() : 'Just now' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center text-xs text-zinc-400">
                            No recent dispatch activity recorded yet.
                        </div>
                    @endif
                </div>

                <!-- Segments & SMTP Relays (4 cols) -->
                <div class="lg:col-span-4 space-y-6">

                    <!-- Connected SMTP Relays -->
                    <div class="bg-white dark:bg-[#111113] p-5 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/60 pb-2.5">
                            <h4 class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">
                                Connected SMTP Accounts
                            </h4>
                            <a href="/mail-accounts" class="text-3xs text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">Manage</a>
                        </div>

                        @if(count($mailAccountList) > 0)
                            <div class="space-y-2">
                                @foreach($mailAccountList as $acc)
                                    @php
                                        $isActive = \Carbon\Carbon::parse($acc->active_after)->lte(now());
                                    @endphp
                                    <div class="flex items-center justify-between p-2 rounded-lg bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800/50">
                                        <div class="flex items-center gap-2 truncate">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-400' : 'bg-zinc-300' }}"></span>
                                            <span class="text-xs text-zinc-800 dark:text-zinc-200 truncate">{{ $acc->name }}</span>
                                        </div>
                                        <span class="text-3xs font-mono text-zinc-400">
                                            {{ $isActive ? 'Active' : 'Scheduled' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-zinc-400 italic text-center py-2">No accounts configured.</p>
                        @endif
                    </div>

                    <!-- Audience Segments -->
                    <div class="bg-white dark:bg-[#111113] p-5 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 shadow-2xs space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/60 pb-2.5">
                            <h4 class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">
                                Audience Segments
                            </h4>
                            <a href="/tags" class="text-3xs text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">Tags</a>
                        </div>

                        @if(count($topTags) > 0)
                            <div class="space-y-3">
                                @foreach($topTags as $tag)
                                    @php
                                        $tagPct = $contacts > 0 ? round(($tag->contacts_count / $contacts) * 100, 1) : 0;
                                    @endphp
                                    <div class="space-y-1">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-zinc-700 dark:text-zinc-300 truncate">
                                                {{ $tag->label }}
                                            </span>
                                            <span class="text-zinc-400 text-3xs">{{ $tag->contacts_count }} ({{ $tagPct }}%)</span>
                                        </div>
                                        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1">
                                            <div class="bg-amber-300/80 dark:bg-amber-400/70 h-1 rounded-full" style="width: {{ $tagPct }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-zinc-400 italic text-center py-2">No tags created yet.</p>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>