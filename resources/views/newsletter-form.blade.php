@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>
<script src="/build/assets/tinymce/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: 'textarea#body_template',
        license_key: 'gpl',
        suffix: '.min',
        height: 480,
        menubar: false,
        plugins: 'table lists link image code',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | align lineheight | bullist numlist | table link image | code removeformat',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
        file_picker_callback (callback, value, meta) {
            let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            let y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

            tinymce.activeEditor.windowManager.openUrl({
                url : '/file-manager/tinymce5',
                title : 'File Manager',
                width : x * 0.8,
                height : y * 0.8,
                onMessage: (api, message) => {
                    callback(message.content, { text: message.text });
                }
            });
        }
    });

    function insertMergeTag(tag) {
        const subjectInput = document.getElementById('subject_template');
        if (!subjectInput) return;

        const start = subjectInput.selectionStart || 0;
        const end = subjectInput.selectionEnd || 0;
        const text = subjectInput.value;
        subjectInput.value = text.substring(0, start) + tag + text.substring(end);
        subjectInput.focus();
        subjectInput.selectionStart = subjectInput.selectionEnd = start + tag.length;

        subjectInput.classList.add('ring-2', 'ring-amber-400', 'dark:ring-amber-300');
        setTimeout(() => subjectInput.classList.remove('ring-2', 'ring-amber-400', 'dark:ring-amber-300'), 500);
    }
</script>
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
<script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1 space-x-2">
                    <a href="/newsletters" class="hover:text-zinc-900 dark:hover:text-amber-200 transition-colors">Newsletters</a>
                    <span>/</span>
                    <span class="text-zinc-900 dark:text-white font-semibold">
                        {{ empty($newsletter->id) ? 'Create Newsletter' : 'Edit Newsletter' }}
                    </span>
                </nav>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas {{ empty($newsletter->id) ? 'fa-pen-fancy' : 'fa-edit' }} text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ empty($newsletter->id) ? __('Compose Email Broadcast') : __('Edit Broadcast: ' . $newsletter->title) }}
                    </h2>
                </div>
            </div>
            <div>
                <a href="/newsletters" class="inline-flex items-center px-3.5 py-2 bg-slate-100 dark:bg-[#141417] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-800 transition-colors gap-2">
                    <i class="fas fa-arrow-left text-2xs"></i>
                    <span>Back to Newsletters</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-8 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form name="save-newsletter" action="/savenewsletter" method="post">
                @csrf
                <input type="hidden" name="newsletter_id" value="{{ $newsletter->id }}" />

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                    <!-- MAIN COMPOSER COLUMN (8 cols) -->
                    <div class="lg:col-span-8 space-y-6">

                        <!-- Subject & Title Card -->
                        <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50/80 text-amber-900 dark:bg-amber-950/30 dark:text-amber-200 font-bold text-2xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/20">
                                        Step 1
                                    </span>
                                    <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                        <i class="fas fa-envelope-open-text text-amber-500/80 dark:text-amber-300/80"></i>
                                        Subject Line & Internal Title
                                    </h3>
                                </div>
                            </div>

                            <!-- Subject Template Field -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider" for="subject_template">
                                        Email Subject Line <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-3xs text-zinc-400">Personalize with tags below</span>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <input class="w-full pl-10 pr-4 py-3 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-sm font-semibold" 
                                           id="subject_template" name="subject_template" type="text" value="{{ $newsletter->subject_template }}" placeholder="e.g. Exclusive Update for [[firstname]]: New Features Announced!" required autofocus>
                                </div>

                                <!-- 1-Click Merge Tag Inserters -->
                                <div class="flex items-center flex-wrap gap-1.5 mt-2.5">
                                    <span class="text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mr-1">
                                        Insert Tag:
                                    </span>
                                    <button type="button" onclick="insertMergeTag('[[firstname]]')" class="px-2 py-0.5 rounded-lg bg-amber-50/80 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200/80 dark:border-amber-500/30 text-3xs font-mono font-bold hover:bg-amber-100 transition-colors">
                                        + [[firstname]]
                                    </button>
                                    <button type="button" onclick="insertMergeTag('[[lastname]]')" class="px-2 py-0.5 rounded-lg bg-amber-50/80 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200/80 dark:border-amber-500/30 text-3xs font-mono font-bold hover:bg-amber-100 transition-colors">
                                        + [[lastname]]
                                    </button>
                                    <button type="button" onclick="insertMergeTag('[[company]]')" class="px-2 py-0.5 rounded-lg bg-amber-50/80 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200/80 dark:border-amber-500/30 text-3xs font-mono font-bold hover:bg-amber-100 transition-colors">
                                        + [[company]]
                                    </button>
                                    <button type="button" onclick="insertMergeTag('[[email]]')" class="px-2 py-0.5 rounded-lg bg-amber-50/80 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200/80 dark:border-amber-500/30 text-3xs font-mono font-bold hover:bg-amber-100 transition-colors">
                                        + [[email]]
                                    </button>
                                </div>
                            </div>

                            <!-- Internal Title -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="title">
                                    Internal Broadcast Title <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <input class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-xs font-medium" 
                                           id="title" name="title" type="text" value="{{ $newsletter->title }}" placeholder="e.g. October 2026 Monthly Digest (Issue #42)" required>
                                </div>
                                <p class="text-2xs text-zinc-500 dark:text-zinc-400 mt-1.5 pl-1">
                                    Used for internal dashboard tracking and campaign reporting.
                                </p>
                            </div>
                        </div>

                        <!-- HTML Body Composer Card -->
                        <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 space-y-4">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50/80 text-amber-900 dark:bg-amber-950/30 dark:text-amber-200 font-bold text-2xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/20">
                                        Step 2
                                    </span>
                                    <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                        <i class="fas fa-file-code text-amber-500/80 dark:text-amber-300/80"></i>
                                        Email Body Content
                                    </h3>
                                </div>
                                <span class="text-3xs text-zinc-400">Rich HTML & File Manager Supported</span>
                            </div>

                            <div>
                                <textarea id="body_template" name="body_template">{{ $newsletter->body_template }}</textarea>
                            </div>
                        </div>

                    </div>

                    <!-- SIDEBAR / SETTINGS COLUMN (4 cols) -->
                    <div class="lg:col-span-4 space-y-6">

                        <!-- Broadcast Settings Card -->
                        <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex items-center justify-between">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-sliders-h text-amber-500/80 dark:text-amber-300/80"></i>
                                    Broadcast Settings
                                </h4>
                            </div>

                            <!-- Campaign Selector -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="campaign_id">
                                    Marketing Campaign <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <select class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-xs font-medium" 
                                            id="campaign_id" name="campaign_id" required>
                                        <option value="">Select Campaign</option>
                                        @foreach($campaigns as $camp)
                                            <option value="{{ $camp->id }}" @if($newsletter->campaign_id == $camp->id) selected @endif>{{ $camp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status Selector -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="status">
                                    Broadcast Status <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                        <i class="fas fa-toggle-on"></i>
                                    </div>
                                    <select class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-xs font-medium" 
                                            id="status" name="status" required>
                                        <option value="D" @if($newsletter->status == 'D' || empty($newsletter->status)) selected @endif>Draft (Not queued)</option>
                                        <option value="N" @if($newsletter->status == 'N') selected @endif>New / Ready (Queue for send)</option>
                                        <option value="Q" @if($newsletter->status == 'Q') selected @endif>Queuing (In delivery)</option>
                                        <option value="S" @if($newsletter->status == 'S') selected @endif>Sent (Completed)</option>
                                    </select>
                                </div>
                                <p class="text-3xs text-zinc-500 dark:text-zinc-400 mt-1.5">
                                    Set to <strong>New / Ready</strong> when you are ready for the dispatch worker to queue emails.
                                </p>
                            </div>

                            <!-- Target Tags Selector -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-2">
                                    Target Audience Segments (Tags)
                                </label>
                                @php
                                    $assignedTagIds = $newsletter->newsletter_tags ? $newsletter->newsletter_tags->pluck('tag_id')->toArray() : [];
                                @endphp
                                @if(count($tags) > 0)
                                    <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto p-1">
                                        @foreach($tags as $tag)
                                            @php $isChecked = in_array($tag->id, $assignedTagIds); @endphp
                                            <label class="cursor-pointer relative flex items-center p-2 rounded-xl border transition-all text-xs font-semibold select-none group {{ $isChecked ? 'bg-amber-50/80 border-amber-300 dark:bg-amber-950/40 dark:border-amber-500/40 text-amber-900 dark:text-amber-200 shadow-2xs' : 'bg-slate-50 dark:bg-[#09090B] border-slate-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800' }}">
                                                <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" {{ $isChecked ? 'checked' : '' }} class="rounded border-zinc-300 dark:border-zinc-700 text-amber-500 focus:ring-amber-400 mr-2">
                                                <span class="truncate">{{ $tag->label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="text-3xs text-zinc-500 dark:text-zinc-400 mt-1.5">
                                        Leave unselected to broadcast to all active contacts.
                                    </p>
                                @else
                                    <div class="p-3 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 text-center text-xs text-zinc-500">
                                        No tags available.
                                    </div>
                                @endif
                            </div>

                            <!-- Action Bar -->
                            <div class="flex items-center justify-end space-x-3 pt-5 border-t border-zinc-100 dark:border-zinc-800">
                                <a href="/newsletters" class="px-5 py-2.5 bg-slate-100 dark:bg-[#141417] hover:bg-slate-200 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-semibold text-xs rounded-xl border border-slate-200 dark:border-zinc-800 transition-colors">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 font-bold text-xs rounded-xl shadow-2xs hover:shadow transition-all gap-2">
                                    <i class="fas fa-check text-xs"></i>
                                    <span>Save Broadcast</span>
                                </button>
                            </div>
                        </div>

                        <!-- Merge Tag Cheat Sheet -->
                        <div class="bg-gradient-to-br from-amber-50/50 via-slate-50/60 to-white dark:from-[#141417] dark:to-[#141417] rounded-2xl p-5 border border-amber-200/60 dark:border-zinc-800 shadow-2xs space-y-3">
                            <h4 class="font-bold text-xs text-zinc-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-magic text-amber-500"></i>
                                Supported Merge Tags
                            </h4>
                            <div class="space-y-1.5 text-2xs">
                                <div class="flex justify-between items-center py-1 border-b border-zinc-100 dark:border-zinc-800">
                                    <code class="font-bold text-amber-700 dark:text-amber-300 font-mono">[[firstname]]</code>
                                    <span class="text-zinc-500">Recipient's first name</span>
                                </div>
                                <div class="flex justify-between items-center py-1 border-b border-zinc-100 dark:border-zinc-800">
                                    <code class="font-bold text-amber-700 dark:text-amber-300 font-mono">[[lastname]]</code>
                                    <span class="text-zinc-500">Recipient's last name</span>
                                </div>
                                <div class="flex justify-between items-center py-1 border-b border-zinc-100 dark:border-zinc-800">
                                    <code class="font-bold text-amber-700 dark:text-amber-300 font-mono">[[company]]</code>
                                    <span class="text-zinc-500">Organization name</span>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <code class="font-bold text-amber-700 dark:text-amber-300 font-mono">[[email]]</code>
                                    <span class="text-zinc-500">Subscriber email</span>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
