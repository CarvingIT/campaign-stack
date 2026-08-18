<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                            <p class="text-sm text-gray-500">
                                Mail Accounts
                            </p>

                            <p class="mt-2 text-2xl font-semibold text-gray-900">
                                {{ $mailAccounts }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Connected accounts
                            </p>
                        </div>

                        <div class="bg-green-50 border border-green-100 rounded-lg p-4">
                            <p class="text-sm text-gray-500">
                                Contacts
                            </p>

                            <p class="mt-2 text-2xl font-semibold text-gray-900">
                                {{ $contacts }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Total contacts
                            </p>
                        </div>

                        <div class="bg-orange-50 border border-orange-100 rounded-lg p-4">
                            <p class="text-sm text-gray-500">
                                Campaigns
                            </p>

                            <p class="mt-2 text-2xl font-semibold text-gray-900">
                                {{ $campaigns }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Total campaigns
                            </p>
                        </div>

                        <div class="bg-purple-50 border border-purple-100 rounded-lg p-4">
                            <p class="text-sm text-gray-500">
                                Newsletters
                            </p>

                            <p class="mt-2 text-2xl font-semibold text-gray-900">
                                {{ $newsletters }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Total newsletters
                            </p>
                        </div>

                        <div class="bg-red-50 border border-red-100 rounded-lg p-4">
                            <p class="text-sm text-gray-500">
                                Alerts
                            </p>

                            <p class="mt-2 text-2xl font-semibold text-gray-900">
                                {{ $failedMails }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Failed emails
                            </p>
                        </div>

                    </div>

                    <!-- Charts -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-6">

                        <!-- Emails Sent -->
                        <div class="bg-white border border-gray-200 rounded-lg p-5">

                            <div class="flex items-start justify-between">

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        Emails Sent
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Emails sent over the selected 7 days
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs text-gray-500">
                                        Total in period
                                    </p>

                                    <p class="text-xl font-semibold text-gray-900">
                                        {{ array_sum($emailCounts) }}
                                    </p>
                                </div>

                            </div>

                            <!-- Date Navigation -->
                            <div class="flex items-center justify-between mt-5">

                                <a
                                    href="{{ route('dashboard', [
                                        'end_date' => $endDate->copy()->subDays(7)->format('Y-m-d')
                                    ]) }}"
                                    class="px-3 py-2 text-sm border border-gray-200 rounded-md hover:bg-gray-50 text-gray-700"
                                >
                                    ← Previous 7 days
                                </a>

                                <span class="text-sm font-medium text-gray-600">
                                    {{ $startDate->format('M d') }}
                                    -
                                    {{ $endDate->format('M d, Y') }}
                                </span>

                                @if($endDate->lt(now()->startOfDay()))

                                    <a
                                        href="{{ route('dashboard', [
                                            'end_date' => $endDate->copy()->addDays(7)->format('Y-m-d')
                                        ]) }}"
                                        class="px-3 py-2 text-sm border border-gray-200 rounded-md hover:bg-gray-50 text-gray-700"
                                    >
                                        Next 7 days →
                                    </a>

                                @else

                                    <span class="px-3 py-2 text-sm border border-gray-100 rounded-md text-gray-300">
                                        Next 7 days →
                                    </span>

                                @endif

                            </div>

                            <!-- Chart -->
                            <div class="mt-6 relative">

                                @php
                                    $chartWidth = 520;
                                    $chartHeight = 210;

                                    $leftPadding = 50;
                                    $rightPadding = 15;
                                    $topPadding = 20;
                                    $bottomPadding = 40;

                                    $usableWidth = $chartWidth - $leftPadding - $rightPadding;
                                    $usableHeight = $chartHeight - $topPadding - $bottomPadding;

                                    $pointCount = count($emailCounts);

                                    $points = [];

                                    foreach ($emailCounts as $index => $value) {

                                        $x = $leftPadding;

                                        if ($pointCount > 1) {
                                            $x += $index * (
                                                $usableWidth / ($pointCount - 1)
                                            );
                                        }

                                        $y = $topPadding
                                            + $usableHeight
                                            - (($value / $yAxisMax) * $usableHeight);

                                        $points[] = round($x, 2) . ',' . round($y, 2);
                                    }

                                    $pointsString = implode(' ', $points);
                                    $gridSteps = 5;
                                @endphp

                                <div
                                    id="chartTooltip"
                                    class="hidden absolute z-20 bg-gray-900 text-white text-xs rounded-md px-3 py-2 shadow-lg pointer-events-none"
                                >
                                    <div id="tooltipDate"></div>
                                    <div id="tooltipCount" class="font-semibold mt-1"></div>
                                </div>

                                <svg
                                    id="emailChart"
                                    viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}"
                                    class="w-full h-64"
                                    preserveAspectRatio="none"
                                >

                                    <!-- Horizontal grid -->
                                    @for($i = 0; $i <= $gridSteps; $i++)

                                        @php
                                            $gridValue = round(
                                                $yAxisMax - (
                                                    $yAxisMax / $gridSteps
                                                ) * $i
                                            );

                                            $gridY = $topPadding + (
                                                $usableHeight / $gridSteps
                                            ) * $i;
                                        @endphp

                                        <line
                                            x1="{{ $leftPadding }}"
                                            y1="{{ $gridY }}"
                                            x2="{{ $chartWidth - $rightPadding }}"
                                            y2="{{ $gridY }}"
                                            stroke="#e5e7eb"
                                        />

                                        <text
                                            x="5"
                                            y="{{ $gridY + 4 }}"
                                            font-size="11"
                                            fill="#6b7280"
                                        >
                                            {{ $gridValue }}
                                        </text>

                                    @endfor

                                    <!-- Line -->
                                    @if($pointCount > 0)

                                        <polyline
                                            points="{{ $pointsString }}"
                                            fill="none"
                                            stroke="#3b82f6"
                                            stroke-width="3"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />

                                    @endif

                                    <!-- Data points -->
                                    @foreach($emailCounts as $index => $value)

                                        @php
                                            $x = $leftPadding;

                                            if ($pointCount > 1) {
                                                $x += $index * (
                                                    $usableWidth / ($pointCount - 1)
                                                );
                                            }

                                            $y = $topPadding
                                                + $usableHeight
                                                - (($value / $yAxisMax) * $usableHeight);
                                        @endphp

                                        <circle
                                            class="email-chart-point cursor-pointer"
                                            cx="{{ $x }}"
                                            cy="{{ $y }}"
                                            r="5"
                                            fill="#3b82f6"
                                            data-date="{{ $emailFullDates[$index] }}"
                                            data-label="{{ $emailDates[$index] }}"
                                            data-count="{{ $value }}"
                                        />

                                    @endforeach

                                    <!-- Dates -->
                                    @foreach($emailDates as $index => $date)

                                        @php
                                            $x = $leftPadding;

                                            if ($pointCount > 1) {
                                                $x += $index * (
                                                    $usableWidth / ($pointCount - 1)
                                                );
                                            }
                                        @endphp

                                        <text
                                            x="{{ $x }}"
                                            y="{{ $chartHeight - 8 }}"
                                            text-anchor="middle"
                                            font-size="10"
                                            fill="#6b7280"
                                        >
                                            {{ $date }}
                                        </text>

                                    @endforeach

                                </svg>

                            </div>

                            <p class="text-xs text-gray-400 mt-2 text-center">
                                Hover over a point to view details. Click a point to view that date.
                            </p>

                        </div>

                        <!-- Email Status -->
                        <div class="bg-white border border-gray-200 rounded-lg p-5">

                            <h3 class="text-lg font-semibold text-gray-900">
                                Email Status
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Current email distribution
                            </p>

                            @php
                                $totalEmails = $sentMails + $queuedMails + $failedMails;

                                $sentPercentage = 0;
                                $queuedPercentage = 0;
                                $failedPercentage = 0;

                                if ($totalEmails > 0) {
                                    $sentPercentage = ($sentMails / $totalEmails) * 100;
                                    $queuedPercentage = ($queuedMails / $totalEmails) * 100;
                                    $failedPercentage = ($failedMails / $totalEmails) * 100;
                                }
                            @endphp

                            <div class="flex items-center justify-center gap-8 mt-6">

                                <div class="relative w-36 h-36">

                                    @if($totalEmails > 0)

                                        <div
                                            class="w-36 h-36 rounded-full"
                                            style="
                                                background: conic-gradient(
                                                    #22c55e 0% {{ $sentPercentage }}%,
                                                    #3b82f6 {{ $sentPercentage }}% {{ $sentPercentage + $queuedPercentage }}%,
                                                    #ef4444 {{ $sentPercentage + $queuedPercentage }}% 100%
                                                );
                                            "
                                        ></div>

                                    @else

                                        <div class="w-36 h-36 rounded-full border-8 border-gray-200"></div>

                                    @endif

                                    <div class="absolute inset-0 flex items-center justify-center">

                                        <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center">

                                            <span class="text-lg font-semibold text-gray-800">
                                                {{ $totalEmails }}
                                            </span>

                                        </div>

                                    </div>

                                </div>

                                <div class="space-y-3 text-sm">

                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-green-500"></span>

                                        <span class="text-gray-600">
                                            Sent
                                        </span>

                                        <span class="font-semibold text-gray-900">
                                            {{ $sentMails }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>

                                        <span class="text-gray-600">
                                            Queued
                                        </span>

                                        <span class="font-semibold text-gray-900">
                                            {{ $queuedMails }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-red-500"></span>

                                        <span class="text-gray-600">
                                            Failed
                                        </span>

                                        <span class="font-semibold text-gray-900">
                                            {{ $failedMails }}
                                        </span>
                                    </div>

                                    @if($failureCodes->count() > 0)

                                        <div class="border-t pt-2 mt-2">

                                            <p class="text-xs text-gray-500 mb-1">
                                                Failure codes
                                            </p>

                                            @foreach($failureCodes as $failure)

                                                <div class="flex justify-between text-xs text-gray-500">

                                                    <span>
                                                        {{ $failure->response_code }}
                                                    </span>

                                                    <span>
                                                        {{ $failure->count }}
                                                    </span>

                                                </div>

                                            @endforeach

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const points = document.querySelectorAll('.email-chart-point');
            const tooltip = document.getElementById('chartTooltip');
            const tooltipDate = document.getElementById('tooltipDate');
            const tooltipCount = document.getElementById('tooltipCount');

            points.forEach(function (point) {

                point.addEventListener('mouseenter', function (event) {

                    tooltipDate.textContent = point.dataset.label;
                    tooltipCount.textContent =
                        'Emails sent: ' + point.dataset.count;

                    tooltip.classList.remove('hidden');

                    point.setAttribute('r', '7');

                });

                point.addEventListener('mousemove', function (event) {

                    const container = point.closest('.relative');
                    const rect = container.getBoundingClientRect();

                    let left = event.clientX - rect.left + 10;
                    let top = event.clientY - rect.top - 55;

                    if (left + tooltip.offsetWidth > rect.width) {
                        left = rect.width - tooltip.offsetWidth - 10;
                    }

                    if (top < 0) {
                        top = 10;
                    }

                    tooltip.style.left = left + 'px';
                    tooltip.style.top = top + 'px';

                });

                point.addEventListener('mouseleave', function () {

                    tooltip.classList.add('hidden');

                    point.setAttribute('r', '5');

                });

                point.addEventListener('click', function () {

                    const selectedDate = point.dataset.date;

                    window.location.href =
                        "{{ route('dashboard') }}" +
                        "?end_date=" +
                        encodeURIComponent(selectedDate);

                });

            });

            // Refresh dashboard data every 30 seconds
            setTimeout(function () {
                window.location.reload();
            }, 30000);

        });
    </script>

</x-app-layout>