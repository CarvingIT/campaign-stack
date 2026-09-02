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
                nameInput.classList.add('ring-2', 'ring-amber-400', 'dark:ring-amber-300');
                setTimeout(() => nameInput.classList.remove('ring-2', 'ring-amber-400', 'dark:ring-amber-300'), 600);
            }
        };
    });
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1 space-x-2">
                    <a href="/campaigns" class="hover:text-zinc-900 dark:hover:text-amber-200 transition-colors">Campaigns</a>
                    <span>/</span>
                    <span class="text-zinc-900 dark:text-white font-semibold">
                        {{ empty($campaign->id) ? 'Create Campaign' : 'Edit Campaign' }}
                    </span>
                </nav>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas {{ empty($campaign->id) ? 'fa-plus-circle' : 'fa-edit' }} text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ empty($campaign->id) ? __('Create Marketing Campaign') : __('Edit Campaign: ' . $campaign->name) }}
                    </h2>
                </div>
            </div>
            <div>
                <a href="/campaigns" class="inline-flex items-center px-3.5 py-2 bg-slate-100 dark:bg-[#141417] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-800 transition-colors gap-2">
                    <i class="fas fa-arrow-left text-2xs"></i>
                    <span>Back to Campaigns</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-8 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form name="save-campaign" action="/savecampaign" method="post">
                @csrf
                <input type="hidden" name="campaign_id" value="{{ $campaign->id }}" />

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

                    <!-- LEFT COLUMN: Campaign Configuration (Equal Height) -->
                    <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 flex flex-col justify-between">
                        
                        <div class="space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3.5 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50/80 text-amber-900 dark:bg-amber-950/30 dark:text-amber-200 font-bold text-2xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/20">
                                        Step 1
                                    </span>
                                    <div>
                                        <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                            <i class="fas fa-bullhorn text-amber-500/80 dark:text-amber-300/80"></i>
                                            Campaign Details
                                        </h3>
                                        <p class="text-2xs text-zinc-500 dark:text-zinc-400">Set the campaign name and classification type.</p>
                                    </div>
                                </div>
                            </div>

                            @php
                                $attributes = json_decode($campaign->other_attributes ?? '{}');
                                $currentType = $attributes->type ?? 'broadcast';
                            @endphp

                            <!-- Campaign Name -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="name">
                                    Campaign Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                    <input class="w-full pl-10 pr-4 py-3 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-sm font-semibold" 
                                           id="name" name="name" type="text" value="{{ $campaign->name }}" placeholder="e.g. Q3 Product Launch, Weekly Digest, Black Friday Sale" required autofocus autocomplete="off">
                                </div>
                                <p class="text-2xs text-zinc-500 dark:text-zinc-400 mt-1.5 pl-1">
                                    A descriptive campaign name to identify your broadcast series.
                                </p>
                            </div>

                            <!-- Campaign Type -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="campaign_type">
                                    Campaign Classification Type
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <select name="campaign_type" id="campaign_type" class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-xs font-medium">
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
                            <div class="p-4 bg-slate-50/80 dark:bg-[#09090B]/60 rounded-xl border border-slate-200 dark:border-zinc-800 space-y-2.5">
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
                                                class="px-3 py-1.5 bg-white dark:bg-[#141417] hover:bg-amber-50/80 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-medium rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                            <i class="fas fa-plus text-3xs text-amber-500"></i>
                                            <span>{{ $p['name'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="flex items-center justify-end space-x-3 pt-5 mt-6 border-t border-zinc-100 dark:border-zinc-800">
                            <a href="/campaigns" class="px-5 py-2.5 bg-slate-100 dark:bg-[#141417] hover:bg-slate-200 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-semibold text-xs rounded-xl border border-slate-200 dark:border-zinc-800 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 font-bold text-xs rounded-xl shadow-2xs hover:shadow transition-all gap-2">
                                <i class="fas fa-check text-xs"></i>
                                <span>{{ empty($campaign->id) ? 'Create Campaign' : 'Save Changes' }}</span>
                            </button>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: Live Contextual Campaign Studio Preview (Equal Height) -->
                    <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 flex flex-col justify-between">
                        
                        <div class="space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex items-center justify-between">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-eye text-amber-500/80 dark:text-amber-300/80"></i>
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
                                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/60 dark:bg-[#09090B]/60 shadow-2xs">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="border-b border-zinc-200/80 dark:border-zinc-800 bg-slate-100/60 dark:bg-[#09090B]/80 text-3xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                                <th class="py-2.5 pl-3.5 pr-2">Campaign Name</th>
                                                <th class="py-2.5 px-2">Type</th>
                                                <th class="py-2.5 px-2">Newsletters</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="hover:bg-slate-50/90 dark:hover:bg-zinc-800/40 transition-colors">
                                                <td class="py-3 pl-3.5 pr-2 whitespace-nowrap">
                                                    <div class="flex items-center space-x-2.5">
                                                        <div class="w-8 h-8 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 text-amber-700 dark:text-amber-200 border border-amber-200/60 dark:border-amber-500/20 flex items-center justify-center text-xs shrink-0 shadow-2xs">
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
                                                    <span id="mockCampaignType" class="inline-flex items-center px-2 py-0.5 rounded-lg text-3xs font-semibold bg-slate-100 text-slate-700 dark:bg-zinc-800 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700">
                                                        {{ ucfirst($currentType) }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-2 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-slate-100 text-slate-700 dark:bg-zinc-800 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700">
                                                        <i class="fas fa-paper-plane mr-1 text-3xs opacity-60"></i>
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
                                <div class="p-3.5 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 space-y-2">
                                    <span class="text-3xs text-zinc-500 block">Assigned Marketing Track:</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 flex items-center justify-center text-3xs font-bold">
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
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-3xs text-zinc-500 dark:text-zinc-400">
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
