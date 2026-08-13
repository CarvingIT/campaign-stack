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
                    <div class="grid grid-cols-5 gap-4">

                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                            <p class="text-sm text-gray-500">Mail Accounts</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">12</p>
                            <p class="mt-1 text-xs text-gray-500">Connected accounts</p>
                        </div>

                        <div class="bg-green-50 border border-green-100 rounded-lg p-4">
                            <p class="text-sm text-gray-500">Contacts</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">1,248</p>
                            <p class="mt-1 text-xs text-gray-500">Total contacts</p>
                        </div>

                        <div class="bg-orange-50 border border-orange-100 rounded-lg p-4">
                            <p class="text-sm text-gray-500">Campaigns</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">24</p>
                            <p class="mt-1 text-xs text-gray-500">Total campaigns</p>
                        </div>

                        <div class="bg-purple-50 border border-purple-100 rounded-lg p-4">
                            <p class="text-sm text-gray-500">Newsletters</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">18</p>
                            <p class="mt-1 text-xs text-gray-500">Total newsletters</p>
                        </div>

                        <div class="bg-red-50 border border-red-100 rounded-lg p-4">
                            <p class="text-sm text-gray-500">Alerts</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">5</p>
                            <p class="mt-1 text-xs text-gray-500">New alerts</p>
                        </div>

                    </div>

                    <!-- Email charts -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-6">

                        <!-- Emails sent -->
                        <div class="bg-white border border-gray-200 rounded-lg p-5">

                            <h3 class="text-lg font-semibold text-gray-900">
                                Emails Sent
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Emails sent across dates
                            </p>

                            <div class="mt-5">
                                <svg viewBox="0 0 600 250" class="w-full h-52">

                                    <line x1="50" y1="30" x2="570" y2="30"
                                          stroke="#e5e7eb"/>

                                    <line x1="50" y1="80" x2="570" y2="80"
                                          stroke="#e5e7eb"/>

                                    <line x1="50" y1="130" x2="570" y2="130"
                                          stroke="#e5e7eb"/>

                                    <line x1="50" y1="180" x2="570" y2="180"
                                          stroke="#e5e7eb"/>

                                    <text x="10" y="35" font-size="11" fill="#6b7280">800</text>
                                    <text x="10" y="85" font-size="11" fill="#6b7280">600</text>
                                    <text x="10" y="135" font-size="11" fill="#6b7280">400</text>
                                    <text x="10" y="185" font-size="11" fill="#6b7280">200</text>

                                    <polyline
                                        points="60,125 145,90 230,108 315,70 400,55 485,85 570,35"
                                        fill="none"
                                        stroke="#3b82f6"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />

                                    <circle cx="60" cy="125" r="4" fill="#3b82f6"/>
                                    <circle cx="145" cy="90" r="4" fill="#3b82f6"/>
                                    <circle cx="230" cy="108" r="4" fill="#3b82f6"/>
                                    <circle cx="315" cy="70" r="4" fill="#3b82f6"/>
                                    <circle cx="400" cy="55" r="4" fill="#3b82f6"/>
                                    <circle cx="485" cy="85" r="4" fill="#3b82f6"/>
                                    <circle cx="570" cy="35" r="4" fill="#3b82f6"/>

                                    <text x="48" y="215" font-size="11" fill="#6b7280">Aug 6</text>
                                    <text x="133" y="215" font-size="11" fill="#6b7280">Aug 7</text>
                                    <text x="218" y="215" font-size="11" fill="#6b7280">Aug 8</text>
                                    <text x="303" y="215" font-size="11" fill="#6b7280">Aug 9</text>
                                    <text x="383" y="215" font-size="11" fill="#6b7280">Aug 10</text>
                                    <text x="468" y="215" font-size="11" fill="#6b7280">Aug 11</text>
                                    <text x="548" y="215" font-size="11" fill="#6b7280">Aug 12</text>

                                </svg>
                            </div>
                        </div>

                        <!-- Email status -->
                        <div class="bg-white border border-gray-200 rounded-lg p-5">

                            <h3 class="text-lg font-semibold text-gray-900">
                                Email Status
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Distribution of emails by state
                            </p>

                            <div class="flex items-center justify-center gap-8 mt-6">

                                <div class="relative w-36 h-36">

                                    <div class="w-36 h-36 rounded-full"
                                         style="background: conic-gradient(#22c55e 0deg 270deg, #3b82f6 270deg 310deg, #ef4444 310deg 360deg);">
                                    </div>

                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center">
                                            <span class="text-lg font-semibold text-gray-800">
                                                800
                                            </span>
                                        </div>
                                    </div>

                                </div>

                                <div class="space-y-3 text-sm">

                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                        <span class="text-gray-600">Sent</span>
                                        <span class="font-semibold text-gray-900">680</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                        <span class="text-gray-600">Queued</span>
                                        <span class="font-semibold text-gray-900">85</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                        <span class="text-gray-600">Failed</span>
                                        <span class="font-semibold text-gray-900">35</span>
                                    </div>

                                    <div class="border-t pt-2 mt-2">
                                        <p class="text-xs text-gray-500 mb-1">
                                            Failure codes
                                        </p>

                                        <div class="flex justify-between text-xs text-gray-500">
                                            <span>400</span>
                                            <span>18</span>
                                        </div>

                                        <div class="flex justify-between text-xs text-gray-500">
                                            <span>401</span>
                                            <span>9</span>
                                        </div>

                                        <div class="flex justify-between text-xs text-gray-500">
                                            <span>500</span>
                                            <span>8</span>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
