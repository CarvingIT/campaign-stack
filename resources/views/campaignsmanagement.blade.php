@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageSize = 15;
        let visibleCount = pageSize;
        const allRows = Array.from(document.querySelectorAll('.campaign-row'));
        const searchInput = document.getElementById('campaignSearchInput');
        const showMoreBtn = document.getElementById('showMoreBtn');
        const countDisplay = document.getElementById('campaignCountDisplay');
        const emptySearchResults = document.getElementById('emptySearchResults');

        function updateListVisibility() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let matchingRows = [];

            allRows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';

                if (query === '' || searchData.includes(query)) {
                    matchingRows.push(row);
                } else {
                    row.classList.add('hidden');
                }
            });

            if (query !== '') {
                matchingRows.forEach(row => row.classList.remove('hidden'));
                if (showMoreBtn) showMoreBtn.style.display = 'none';
                if (countDisplay) countDisplay.textContent = `Found ${matchingRows.length} matching campaign${matchingRows.length === 1 ? '' : 's'}`;
                if (emptySearchResults) {
                    emptySearchResults.classList.toggle('hidden', matchingRows.length > 0);
                }
            } else {
                if (emptySearchResults) emptySearchResults.classList.add('hidden');
                matchingRows.forEach((row, idx) => {
                    if (idx < visibleCount) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });

                const total = matchingRows.length;
                const currentlyShown = Math.min(visibleCount, total);

                if (countDisplay) {
                    countDisplay.textContent = `Showing ${currentlyShown} of ${total} campaigns`;
                }

                if (showMoreBtn) {
                    if (currentlyShown >= total) {
                        showMoreBtn.style.display = 'none';
                    } else {
                        showMoreBtn.style.display = 'inline-flex';
                        const remaining = total - currentlyShown;
                        document.getElementById('remainingCountText').textContent = `Show More (${remaining} remaining)`;
                    }
                }
            }
        }

        if (showMoreBtn) {
            showMoreBtn.addEventListener('click', function() {
                visibleCount += pageSize;
                updateListVisibility();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', updateListVisibility);
        }

        updateListVisibility();
    });

    function confirmDelete(id, name, count) {
        document.getElementById('modalCampaignId').value = id;
        document.getElementById('deleteCampaignName').textContent = name;
        document.getElementById('deleteNewsletterCount').textContent = count;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas fa-bullhorn text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ __('Marketing Campaigns') }}
                    </h2>
                </div>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1 pl-5">
                    Organize your email broadcasts, newsletters, automated workflows, and marketing programs.
                </p>
            </div>
            <div>
                <a href="/campaign-form/new" class="inline-flex items-center px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition-all duration-200 gap-2">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Create Campaign</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-8 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Notifications -->
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))
                    @php
                        $alertStyles = [
                            'danger' => 'bg-red-50/90 text-red-900 border-red-200 dark:bg-red-950/70 dark:text-red-200 dark:border-red-800/80 icon-fa-exclamation-circle',
                            'warning' => 'bg-amber-50/90 text-amber-900 border-amber-200 dark:bg-amber-950/70 dark:text-amber-200 dark:border-amber-800/80 icon-fa-exclamation-triangle',
                            'success' => 'bg-emerald-50/90 text-emerald-900 border-emerald-200 dark:bg-emerald-950/70 dark:text-emerald-200 dark:border-emerald-800/80 icon-fa-check-circle',
                            'info' => 'bg-amber-50/70 text-amber-900 border-amber-200/70 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-800/60 icon-fa-info-circle',
                        ];
                    @endphp
                    <div class="p-4 rounded-xl border {{ $alertStyles[$msg] }} flex items-start gap-3 shadow-2xs backdrop-blur-sm" role="alert">
                        <i class="fas {{ explode(' ', $alertStyles[$msg])[count(explode(' ', $alertStyles[$msg]))-1] }} text-lg mt-0.5"></i>
                        <div class="text-sm font-semibold">
                            {{ Session::get('alert-' . $msg) }}
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Metrics Overview Cards -->
            @php
                $totalCampaigns = count($campaigns);
                $totalNewsletters = $campaigns->sum('newsletters_count');
                $activeCampaigns = $campaigns->where('newsletters_count', '>', 0)->count();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-white via-white to-slate-50/90 dark:from-[#141417] dark:to-[#141417] p-5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs flex items-center justify-between transition-all duration-300 hover:border-amber-300/40">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Campaigns</p>
                        <h3 class="text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $totalCampaigns }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-100/70 dark:bg-amber-950/30 text-amber-900 dark:text-amber-300 border border-amber-200/80 dark:border-amber-500/20 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-white via-white to-slate-50/90 dark:from-[#141417] dark:to-[#141417] p-5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs flex items-center justify-between transition-all duration-300 hover:border-emerald-300/40">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Associated Newsletters</p>
                        <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalNewsletters }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/70 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-white via-white to-slate-50/90 dark:from-[#141417] dark:to-[#141417] p-5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs flex items-center justify-between transition-all duration-300 hover:border-blue-300/40">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Active Programs</p>
                        <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $activeCampaigns }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/70 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800/70 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>

            <!-- Guidance Banner Card -->
            <div class="bg-gradient-to-r from-zinc-900 via-zinc-800 to-zinc-900 text-white rounded-2xl p-6 shadow-md border border-amber-500/20 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 opacity-10 text-9xl pointer-events-none">
                    <i class="fas fa-bullhorn text-amber-300"></i>
                </div>
                <div class="flex items-start gap-4 relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-amber-400/10 border border-amber-400/20 flex items-center justify-center text-xl shrink-0 text-amber-300">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-bold text-base text-white flex items-center gap-2">
                            How Campaigns Work
                            <span class="w-2 h-2 rounded-full bg-amber-300"></span>
                        </h4>
                        <p class="text-sm text-zinc-300 leading-relaxed">
                            Campaigns group related email newsletters into strategic marketing tracks (e.g. Weekly Digests, Product Launches, Onboarding Workflows). Create a campaign first, then attach broadcast messages to it.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Campaigns High-Density Table Container -->
            <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/90 dark:border-zinc-800 overflow-hidden">
                
                @if(count($campaigns) > 0)
                    <!-- Top Toolbar & Search Bar -->
                    <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/80 dark:bg-[#09090B]/60">
                        <div class="relative flex-1 max-w-md">
                            <i class="fas fa-search absolute left-3.5 top-3 text-zinc-400 dark:text-zinc-400 text-sm"></i>
                            <input type="text" id="campaignSearchInput" placeholder="Search campaigns by name or type..." 
                                   class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#09090B] border border-zinc-300 dark:border-zinc-800 rounded-xl text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-300/80 focus:border-amber-300/80 transition-all">
                        </div>
                        <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400" id="campaignCountDisplay">
                            Showing {{ min(15, count($campaigns)) }} of {{ count($campaigns) }} campaigns
                        </div>
                    </div>

                    <!-- Modern High-Density Table Layout -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-zinc-200/80 dark:border-zinc-800 bg-slate-100/60 dark:bg-[#09090B]/80 text-3xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    <th class="py-3.5 pl-6 pr-4">Campaign Name</th>
                                    <th class="py-3.5 px-4">Campaign Type</th>
                                    <th class="py-3.5 px-4">Newsletters Linked</th>
                                    <th class="py-3.5 px-4">Last Updated</th>
                                    <th class="py-3.5 pl-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="campaignsListContainer" class="divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
                                @foreach ($campaigns as $index => $c)
                                    @php
                                        $attributes = json_decode($c->other_attributes);
                                        $campaignType = $attributes->type ?? 'General Broadcast';
                                        $searchString = strtolower(($c->name ?? '') . ' ' . $campaignType . ' ' . ($c->newsletters_count ?? 0) . ' newsletters');
                                    @endphp

                                    <tr class="campaign-row hover:bg-slate-50/90 dark:hover:bg-zinc-800/40 transition-colors group"
                                        data-search="{{ $searchString }}" data-index="{{ $index }}">
                                        
                                        <!-- Campaign Name + Avatar -->
                                        <td class="py-3.5 pl-6 pr-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-9 h-9 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 text-amber-700 dark:text-amber-200 border border-amber-200/60 dark:border-amber-500/20 flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                                    <i class="fas fa-bullhorn"></i>
                                                </div>
                                                <div>
                                                    <a href="/campaign-form/{{ $c->id }}" class="font-bold text-sm text-zinc-900 dark:text-white hover:text-zinc-600 dark:hover:text-amber-200 transition-colors block">
                                                        {{ $c->name }}
                                                    </a>
                                                    <span class="font-mono text-zinc-400 dark:text-zinc-500 text-3xs">
                                                        ID #{{ $c->id }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Campaign Type -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-3xs font-semibold bg-slate-100 text-slate-700 dark:bg-zinc-800 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700">
                                                <i class="fas fa-layer-group mr-1.5 text-3xs opacity-60"></i>
                                                {{ ucfirst($campaignType) }}
                                            </span>
                                        </td>

                                        <!-- Newsletters Linked -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $c->newsletters_count > 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/70 dark:text-emerald-300 dark:border-emerald-800/80' : 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700' }}">
                                                <i class="fas fa-paper-plane mr-1.5 text-3xs opacity-70"></i>
                                                {{ $c->newsletters_count ?? 0 }} {{ ($c->newsletters_count == 1) ? 'newsletter' : 'newsletters' }}
                                            </span>
                                        </td>

                                        <!-- Last Updated -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="text-zinc-600 dark:text-zinc-400 text-xs">
                                                {{ $c->updated_at ? \Carbon\Carbon::parse($c->updated_at)->diffForHumans() : 'N/A' }}
                                            </span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-3.5 pl-4 pr-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end space-x-1.5">
                                                <a href="/campaign-form/{{ $c->id }}" title="Edit Campaign" 
                                                   class="p-1.5 text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-amber-200 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                                    <i class="fas fa-pencil-alt text-xs"></i>
                                                </a>
                                                <button type="button" onclick="confirmDelete({{ $c->id }}, '{{ addslashes($c->name ?? '') }}', {{ $c->newsletters_count ?? 0 }})" title="Delete Campaign" 
                                                        class="p-1.5 text-zinc-600 hover:text-red-600 dark:text-zinc-300 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Search Empty State -->
                    <div id="emptySearchResults" class="hidden p-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-[#09090B] text-zinc-400 dark:text-zinc-400 flex items-center justify-center mx-auto mb-3 text-xl">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="text-base font-bold text-zinc-900 dark:text-white">No matching campaigns found</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Try adjusting your search query.</p>
                    </div>

                    <!-- Progressive Load More Pagination Footer -->
                    @if(count($campaigns) > 15)
                        <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 text-center bg-slate-50/60 dark:bg-[#09090B]/50">
                            <button type="button" id="showMoreBtn" 
                                    class="inline-flex items-center justify-center px-6 py-2.5 bg-white dark:bg-[#141417] text-zinc-800 dark:text-zinc-200 font-bold text-sm rounded-xl border border-zinc-300 dark:border-zinc-800 shadow-2xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all duration-200 gap-2">
                                <i class="fas fa-chevron-down text-xs"></i>
                                <span id="remainingCountText">Show More Campaigns</span>
                            </button>
                        </div>
                    @endif

                @else
                    <div class="text-center py-14 px-4">
                        <div class="w-16 h-16 bg-amber-50/80 dark:bg-amber-950/30 text-amber-800 dark:text-amber-300 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl border border-amber-200/60 dark:border-amber-500/20 shadow-2xs">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white">No Campaigns Created Yet</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 max-w-md mx-auto mt-2 mb-6 leading-relaxed">
                            Create your first email marketing campaign to organize and schedule your newsletter broadcasts.
                        </p>
                        <a href="/campaign-form/new" class="inline-flex items-center px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 text-sm font-bold rounded-xl shadow-2xs transition-all duration-200 gap-2">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Create Your First Campaign</span>
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Reusable Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-zinc-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#141417] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-zinc-800 transform transition-all">
            <div class="flex items-center space-x-3 text-red-600 dark:text-red-400 mb-3">
                <div class="p-3 bg-red-100 dark:bg-red-950 rounded-full border border-red-200 dark:border-red-800">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Delete Campaign</h3>
            </div>
            <p class="text-sm text-zinc-600 dark:text-zinc-300 mb-6 leading-relaxed">
                Are you sure you want to delete <span id="deleteCampaignName" class="font-bold text-zinc-900 dark:text-white"></span>? 
                This campaign is currently associated with <span id="deleteNewsletterCount" class="font-bold text-zinc-900 dark:text-white">0</span> newsletter(s).
            </p>
            <form method="POST" action="/campaign/delete">
                @csrf
                <input type="hidden" name="campaign_id" id="modalCampaignId">
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-slate-100 dark:bg-zinc-800 rounded-xl hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-2xs">
                        Delete Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
