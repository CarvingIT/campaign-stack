@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('label');
        const badgePreview = document.getElementById('previewBadgeText');
        const mockPreview = document.getElementById('mockSubscriberTag');
        const campaignPreview = document.getElementById('campaignBadgeText');

        function updatePreviews() {
            const val = input.value.trim();
            const text = val.length > 0 ? val : 'tag-preview';
            if (badgePreview) badgePreview.textContent = text;
            if (mockPreview) mockPreview.textContent = text;
            if (campaignPreview) campaignPreview.textContent = text;
        }

        if (input) {
            input.addEventListener('input', updatePreviews);
        }

        window.applyTagPreset = function(name) {
            if (input) {
                input.value = name;
                updatePreviews();
                input.classList.add('ring-2', 'ring-amber-400', 'dark:ring-amber-300');
                setTimeout(() => input.classList.remove('ring-2', 'ring-amber-400', 'dark:ring-amber-300'), 600);
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
                    <a href="/tags" class="hover:text-zinc-900 dark:hover:text-amber-200 transition-colors">Tags</a>
                    <span>/</span>
                    <span class="text-zinc-900 dark:text-white font-semibold">
                        {{ empty($tag->id) ? 'Create New Tag' : 'Edit Tag' }}
                    </span>
                </nav>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas {{ empty($tag->id) ? 'fa-plus-circle' : 'fa-edit' }} text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ empty($tag->id) ? __('Create Audience Tag') : __('Edit Tag: ' . $tag->label) }}
                    </h2>
                </div>
            </div>
            <div>
                <a href="/tags" class="inline-flex items-center px-3.5 py-2 bg-slate-100 dark:bg-[#141417] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-800 transition-colors gap-2">
                    <i class="fas fa-arrow-left text-2xs"></i>
                    <span>Back to Tags</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-8 pt-4">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <form name="save-tag" action="/savetag" method="post">
                @csrf
                <input type="hidden" name="tag_id" value="{{ $tag->id }}" />

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

                    <!-- LEFT COLUMN: Tag Configuration & Presets (Equal Height) -->
                    <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 flex flex-col justify-between">
                        
                        <div class="space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3.5 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50/80 text-amber-900 dark:bg-amber-950/30 dark:text-amber-200 font-bold text-2xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/20">
                                        Step 1
                                    </span>
                                    <div>
                                        <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                            <i class="fas fa-tag text-amber-500/80 dark:text-amber-300/80"></i>
                                            Tag Information
                                        </h3>
                                        <p class="text-2xs text-zinc-500 dark:text-zinc-400">Define the unique label for this subscriber segment.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tag Name Field -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="label">
                                    Tag Label / Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <input class="w-full pl-10 pr-4 py-3 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-sm font-semibold" 
                                           id="label" name="label" type="text" value="{{ $tag->label }}" placeholder="e.g. VIP Customers, Newsletter Subscribers, Leads" required autofocus autocomplete="off">
                                </div>
                                <p class="text-2xs text-zinc-500 dark:text-zinc-400 mt-1.5 pl-1">
                                    Use short, descriptive labels to easily segment subscribers.
                                </p>
                            </div>

                            <!-- 1-Click Popular Tag Presets -->
                            <div class="p-4 bg-slate-50/80 dark:bg-[#09090B]/60 rounded-xl border border-slate-200 dark:border-zinc-800 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-bolt text-amber-500 text-3xs"></i>
                                        1-Click Popular Tag Presets
                                    </span>
                                    <span class="text-3xs text-zinc-400">Click to autofill</span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $presets = [
                                            'VIP Customer',
                                            'Newsletter',
                                            'Beta Tester',
                                            'Webinar Lead',
                                            'Free Trial',
                                            'High Intent',
                                            'Enterprise',
                                            'Partner'
                                        ];
                                    @endphp
                                    @foreach($presets as $preset)
                                        <button type="button" onclick="applyTagPreset('{{ $preset }}')" 
                                                class="px-3 py-1.5 bg-white dark:bg-[#141417] hover:bg-amber-50/80 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-medium rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                            <i class="fas fa-plus text-3xs text-amber-500"></i>
                                            <span>{{ $preset }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="flex items-center justify-end space-x-3 pt-5 mt-6 border-t border-zinc-100 dark:border-zinc-800">
                            <a href="/tags" class="px-5 py-2.5 bg-slate-100 dark:bg-[#141417] hover:bg-slate-200 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-semibold text-xs rounded-xl border border-slate-200 dark:border-zinc-800 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 font-bold text-xs rounded-xl shadow-2xs hover:shadow transition-all gap-2">
                                <i class="fas fa-check text-xs"></i>
                                <span>{{ empty($tag->id) ? 'Create Tag' : 'Save Changes' }}</span>
                            </button>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: Live Interactive Contextual Preview (Equal Height) -->
                    <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 flex flex-col justify-between">
                        
                        <div class="space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex items-center justify-between">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-eye text-amber-500/80 dark:text-amber-300/80"></i>
                                    Live Contextual Preview
                                </h4>
                                <span class="px-2 py-0.5 rounded-full text-3xs font-bold uppercase bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    Real-time
                                </span>
                            </div>

                            <!-- Preview 1: Mock Contact Card in Audience List -->
                            <div class="space-y-2">
                                <span class="block text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    1. How it appears on a Contact Row
                                </span>
                                <div class="p-3.5 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 flex items-center justify-between gap-3 shadow-2xs">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 flex items-center justify-center font-bold text-xs shrink-0">
                                            JS
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-xs text-zinc-900 dark:text-white truncate">Jane Smith</div>
                                            <div class="text-3xs text-zinc-500 truncate">jane.smith@example.com</div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-3xs font-bold bg-amber-50/90 text-amber-900 border border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-500/30 shadow-2xs shrink-0">
                                        <i class="fas fa-tag mr-1 text-3xs text-amber-600 dark:text-amber-300"></i>
                                        <span id="mockSubscriberTag">{{ !empty($tag->label) ? $tag->label : 'tag-preview' }}</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Preview 2: Inside Campaign Targeting Selector -->
                            <div class="space-y-2">
                                <span class="block text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    2. Inside Campaign Targeting Selector
                                </span>
                                <div class="p-3.5 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 space-y-2">
                                    <span class="text-3xs text-zinc-500 block">Targeting Audience Segments:</span>
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-3xs font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 shadow-2xs">
                                            All Contacts
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-3xs font-bold bg-amber-100 text-amber-900 border border-amber-300 dark:bg-amber-950/60 dark:text-amber-200 dark:border-amber-500/40 shadow-2xs">
                                            <i class="fas fa-check mr-1 text-3xs"></i>
                                            <span id="campaignBadgeText">{{ !empty($tag->label) ? $tag->label : 'tag-preview' }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview 3: Standalone Pill -->
                            <div class="space-y-1.5">
                                <span class="block text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    3. Standalone Badge Token
                                </span>
                                <div>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50/90 text-amber-900 border border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-500/30 shadow-2xs">
                                        <i class="fas fa-tag mr-1.5 text-xs text-amber-600 dark:text-amber-300"></i>
                                        <span id="previewBadgeText">{{ !empty($tag->label) ? $tag->label : 'tag-preview' }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Symmetrical Bottom Status Note -->
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-3xs text-zinc-500 dark:text-zinc-400">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-info-circle text-amber-500"></i>
                                Updates live across all preview states
                            </span>
                            <span class="font-mono text-zinc-400">#{{ $tag->id ?? 'new' }}</span>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
