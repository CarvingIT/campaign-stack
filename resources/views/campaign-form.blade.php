@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const typeSelect = document.getElementById('campaign_type');

        const mockName = document.getElementById('mockCampaignName');
        const mockType = document.getElementById('mockCampaignType');

        function updatePreviews() {
            const nameVal = nameInput ? nameInput.value.trim() : '';
            const typeVal = typeSelect ? typeSelect.value : 'Broadcast';

            if (mockName) {
                mockName.textContent = nameVal.length > 0 ? nameVal : 'Campaign Name';
            }

            if (mockType) {
                mockType.textContent = typeVal ? typeVal.charAt(0).toUpperCase() + typeVal.slice(1) : 'Broadcast';
            }
        }

        if (nameInput) nameInput.addEventListener('input', updatePreviews);
        if (typeSelect) typeSelect.addEventListener('change', updatePreviews);

        window.applyCampaignPreset = function(name, type) {
            if (nameInput) {
                nameInput.value = name;
                if (typeSelect && type) {
                    typeSelect.value = type;
                }
                updatePreviews();
                nameInput.classList.add('ring-2', 'ring-amber-500');
                setTimeout(() => nameInput.classList.remove('ring-2', 'ring-amber-500'), 600);
            }
        };
    });
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-3xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1 space-x-2 uppercase tracking-wider">
                    <a href="/campaigns" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Campaigns</a>
                    <span>/</span>
                    <span class="text-amber-600 dark:text-amber-400">
                        {{ empty($campaign->id) ? 'Create Campaign' : 'Edit Campaign' }}
                    </span>
                </nav>
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 dark:bg-amber-400 shadow-2xs"></span>
                    <h2 class="font-extrabold text-2xl text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        <i class="fas {{ empty($campaign->id) ? 'fa-plus-circle' : 'fa-pen-to-square' }} text-amber-500"></i>
                        <span>{{ empty($campaign->id) ? __('Create Marketing Campaign') : __('Edit Campaign: ' . $campaign->name) }}</span>
                    </h2>
                </div>
            </div>
            <div>
                <a href="/campaigns" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1.5">
                    <i class="fas fa-arrow-left text-3xs"></i>
                    <span>Back to Campaigns</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-10 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form name="save-campaign" action="/savecampaign" method="post">
                @csrf
                <input type="hidden" name="campaign_id" value="{{ $campaign->id }}" />

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

                    <!-- LEFT COLUMN: Campaign Configuration -->
                    <div class="bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 flex flex-col justify-between">
                        
                        <div class="space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-3.5 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 font-extrabold text-3xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/30">
                                        Step 1
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                            <i class="fas fa-bullhorn text-amber-500 text-xs"></i>
                                            Campaign Track Specifications
                                        </h3>
                                        <p class="text-3xs text-zinc-500 dark:text-zinc-400">Set the campaign name and classification track.</p>
                                    </div>
                                </div>
                            </div>

                            @php
                                $attributes = json_decode($campaign->other_attributes ?? '{}');
                                $currentType = $attributes->type ?? 'broadcast';
                            @endphp

                            <!-- Campaign Name -->
                            <div>
                                <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5" for="name">
                                    Campaign Name <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                    <input class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 text-xs font-semibold focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs" 
                                           id="name" name="name" type="text" value="{{ $campaign->name }}" placeholder="e.g. Q3 Product Launch, Weekly Digest, Black Friday Sale" required autofocus autocomplete="off">
                                </div>
                                <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-1 pl-1">
                                    A descriptive campaign name to identify your broadcast series.
                                </p>
                            </div>

                            <!-- Campaign Type -->
                            <div>
                                <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5" for="campaign_type">
                                    Campaign Classification Type
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <select name="campaign_type" id="campaign_type" class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white text-xs font-medium focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs">
                                        <option value="broadcast" {{ $currentType == 'broadcast' ? 'selected' : '' }}>General Broadcast</option>
                                        <option value="newsletter" {{ $currentType == 'newsletter' ? 'selected' : '' }}>Newsletter Series</option>
                                        <option value="promotional" {{ $currentType == 'promotional' ? 'selected' : '' }}>Promotional & Sales Blast</option>
                                        <option value="onboarding" {{ $currentType == 'onboarding' ? 'selected' : '' }}>Customer Onboarding</option>
                                        <option value="announcement" {{ $currentType == 'announcement' ? 'selected' : '' }}>Event / Product Announcement</option>
                                        <option value="reengagement" {{ $currentType == 'reengagement' ? 'selected' : '' }}>Subscriber Re-engagement</option>
                                    </select>
                                </div>
                            </div>

                            <!-- 1-Click Popular Campaign Presets -->
                            <div class="p-4 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-bolt text-amber-500 text-3xs"></i>
                                        1-Click Popular Campaign Presets
                                    </span>
                                    <span class="text-3xs text-zinc-400">Click to autofill</span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $campaignPresets = [
                                            ['name' => 'Weekly Newsletter', 'type' => 'newsletter'],
                                            ['name' => 'Q3 Product Launch', 'type' => 'announcement'],
                                            ['name' => 'Black Friday Sale', 'type' => 'promotional'],
                                            ['name' => 'Customer Welcome Series', 'type' => 'onboarding'],
                                            ['name' => 'Feature Announcement', 'type' => 'announcement'],
                                            ['name' => 'Monthly Company Digest', 'type' => 'newsletter'],
                                        ];
                                    @endphp
                                    @foreach($campaignPresets as $p)
                                        <button type="button" onclick="applyCampaignPreset('{{ $p['name'] }}', '{{ $p['type'] }}')" 
                                                class="px-2.5 py-1.5 bg-white dark:bg-[#111114] hover:bg-amber-50/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/80 text-zinc-700 dark:text-zinc-200 text-xs font-medium rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                            <i class="fas fa-plus text-4xs text-amber-500"></i>
                                            <span>{{ $p['name'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="flex items-center justify-end space-x-3 pt-5 mt-6 border-t border-zinc-100 dark:border-zinc-800/80">
                            <a href="/campaigns" class="px-5 py-2.5 bg-white dark:bg-[#111114] hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-semibold text-xs rounded-xl border border-zinc-200/80 dark:border-zinc-800 transition-colors shadow-2xs">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-bold text-xs rounded-xl shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 transition-all gap-2">
                                <i class="fas fa-check text-xs"></i>
                                <span>{{ empty($campaign->id) ? 'Create Campaign' : 'Save Changes' }}</span>
                            </button>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: Live Contextual Campaign Studio Preview -->
                    <div class="bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 flex flex-col justify-between space-y-6">
                        
                        <div class="space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-3.5 flex items-center justify-between">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-eye text-amber-500 text-xs"></i>
                                    Live Campaign Directory Preview
                                </h4>
                                <span class="px-2 py-0.5 rounded-full text-3xs font-bold uppercase bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    Real-time
                                </span>
                            </div>

                            <!-- Preview 1: High Density Table Row Preview -->
                            <div class="space-y-2">
                                <span class="block text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    1. How it appears in Campaigns Table
                                </span>
                                <div class="overflow-x-auto rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/70 dark:bg-[#09090B] shadow-2xs">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 text-3xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                                <th class="py-2.5 pl-3.5 pr-2">Campaign Track</th>
                                                <th class="py-2.5 px-2">Type</th>
                                                <th class="py-2.5 px-2">Newsletters</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-900/40 transition-colors">
                                                <td class="py-3 pl-3.5 pr-2 whitespace-nowrap">
                                                    <div class="flex items-center space-x-2.5">
                                                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-300 border border-amber-500/20 flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">
                                                            <i class="fas fa-bullhorn"></i>
                                                        </div>
                                                        <div>
                                                            <div id="mockCampaignName" class="font-bold text-xs text-zinc-900 dark:text-white truncate max-w-[130px]">
                                                                {{ !empty($campaign->name) ? $campaign->name : 'Campaign Name' }}
                                                            </div>
                                                            <span class="text-3xs text-zinc-400 dark:text-zinc-500">Just now</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-2 whitespace-nowrap">
                                                    <span id="mockCampaignType" class="inline-flex items-center px-2 py-0.5 rounded-md text-3xs font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700">
                                                        {{ ucfirst($currentType) }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-2 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-zinc-100 text-zinc-500 border border-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700">
                                                        <i class="fas fa-paper-plane mr-1 text-4xs opacity-60"></i>
                                                        0 newsletters
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Preview 2: Inside Newsletter Builder Campaign Selector -->
                            <div class="space-y-2">
                                <span class="block text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    2. Inside Newsletter Broadcast Selector
                                </span>
                                <div class="p-3.5 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800 space-y-2">
                                    <span class="text-3xs text-zinc-400 dark:text-zinc-500 block">Assigned Marketing Track:</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-300 flex items-center justify-center text-3xs font-bold">
                                            <i class="fas fa-folder"></i>
                                        </div>
                                        <span class="text-xs font-bold text-zinc-900 dark:text-white">
                                            Campaign Track / {{ !empty($campaign->name) ? $campaign->name : 'Campaign Name' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Symmetrical Bottom Status Note -->
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-3xs text-zinc-500 dark:text-zinc-400">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                                Ready for newsletter grouping
                            </span>
                            <span class="font-mono text-zinc-400">#{{ $campaign->id ?? 'new' }}</span>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
