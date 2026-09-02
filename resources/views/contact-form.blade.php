@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const salutation = document.getElementById('salutation');
        const firstname = document.getElementById('firstname');
        const lastname = document.getElementById('lastname');
        const email = document.getElementById('email');
        const company = document.getElementById('company');
        const mobile = document.getElementById('mobile');

        const mockInitials = document.getElementById('mockInitials');
        const mockName = document.getElementById('mockName');
        const mockEmail = document.getElementById('mockEmail');
        const mockMobile = document.getElementById('mockMobile');
        const mockCompany = document.getElementById('mockCompany');
        const mockTagsContainer = document.getElementById('mockTagsContainer');

        function updateMockCard() {
            const sal = salutation ? salutation.value.trim() : '';
            const fn = firstname ? firstname.value.trim() : '';
            const ln = lastname ? lastname.value.trim() : '';
            const em = email ? email.value.trim() : '';
            const comp = company ? company.value.trim() : '';
            const mob = mobile ? mobile.value.trim() : '';

            // Name
            let displayName = [sal, fn, ln].filter(Boolean).join(' ');
            if (!displayName) displayName = 'Subscriber Name';
            if (mockName) mockName.textContent = displayName;

            // Email
            if (mockEmail) mockEmail.textContent = em || 'subscriber@example.com';

            // Mobile
            if (mockMobile) {
                if (mob) {
                    mockMobile.innerHTML = `<i class="fas fa-phone mr-1.5 text-3xs opacity-60"></i>${mob}`;
                    mockMobile.style.display = 'flex';
                } else {
                    mockMobile.style.display = 'none';
                }
            }

            // Company
            if (mockCompany) {
                if (comp) {
                    mockCompany.innerHTML = `<i class="fas fa-building mr-1.5 text-3xs opacity-60"></i>${comp}`;
                    mockCompany.style.display = 'inline-flex';
                } else {
                    mockCompany.innerHTML = '<span class="text-zinc-400 text-3xs italic">—</span>';
                    mockCompany.style.display = 'inline-flex';
                }
            }

            // Initials
            let initials = ((fn ? fn[0] : '') + (ln ? ln[0] : '')).toUpperCase();
            if (!initials && em) initials = em[0].toUpperCase();
            if (!initials) initials = 'U';
            if (mockInitials) mockInitials.textContent = initials;
        }

        function updateMockTags() {
            if (!mockTagsContainer) return;
            const checkedBoxes = Array.from(document.querySelectorAll('input[name="tags[]"]:checked'));
            mockTagsContainer.innerHTML = '';

            if (checkedBoxes.length === 0) {
                mockTagsContainer.innerHTML = '<span class="text-zinc-400 text-3xs italic">No tags</span>';
                return;
            }

            checkedBoxes.forEach(cb => {
                const label = cb.getAttribute('data-label') || cb.parentElement.textContent.trim();
                const span = document.createElement('span');
                span.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-semibold bg-amber-50/90 text-amber-900 border border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-500/30 whitespace-nowrap shadow-2xs';
                span.innerHTML = `<i class="fas fa-tag mr-1 text-3xs text-amber-600 dark:text-amber-300"></i>${label}`;
                mockTagsContainer.appendChild(span);
            });
        }

        [salutation, firstname, lastname, email, company, mobile].forEach(el => {
            if (el) {
                el.addEventListener('input', updateMockCard);
                el.addEventListener('change', updateMockCard);
            }
        });

        document.querySelectorAll('input[name="tags[]"]').forEach(cb => {
            cb.addEventListener('change', updateMockTags);
        });

        updateMockCard();
        updateMockTags();
    });
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1 space-x-2">
                    <a href="/contacts" class="hover:text-zinc-900 dark:hover:text-amber-200 transition-colors">Contacts</a>
                    <span>/</span>
                    <span class="text-zinc-900 dark:text-white font-semibold">
                        {{ empty($contact->id) ? 'Create Contact' : 'Edit Contact' }}
                    </span>
                </nav>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 dark:bg-amber-300/80 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas {{ empty($contact->id) ? 'fa-user-plus' : 'fa-user-edit' }} text-amber-500/80 dark:text-amber-300/80"></i>
                        {{ empty($contact->id) ? __('New Subscriber Profile') : __('Edit Contact: ' . trim($contact->firstname . ' ' . $contact->lastname)) }}
                    </h2>
                </div>
            </div>
            <div>
                <a href="/contacts" class="inline-flex items-center px-3.5 py-2 bg-slate-100 dark:bg-[#141417] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-800 transition-colors gap-2">
                    <i class="fas fa-arrow-left text-2xs"></i>
                    <span>Back to Contacts</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-8 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form name="save-contact" action="/savecontact" method="post">
                @csrf
                <input type="hidden" name="contact_id" value="{{ $contact->id }}" />

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

                    <!-- LEFT COLUMN: Contact Profile Details (Equal Height) -->
                    <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 flex flex-col justify-between">
                        
                        <div class="space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3.5 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50/80 text-amber-900 dark:bg-amber-950/30 dark:text-amber-200 font-bold text-2xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/20">
                                        Step 1
                                    </span>
                                    <div>
                                        <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                            <i class="fas fa-id-card text-amber-500/80 dark:text-amber-300/80"></i>
                                            Personal & Company Details
                                        </h3>
                                        <p class="text-2xs text-zinc-500 dark:text-zinc-400">Enter subscriber name, email address, and company metadata.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Salutation -->
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="salutation">
                                        Salutation / Title
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                            <i class="fas fa-user-tag"></i>
                                        </div>
                                        <select name="salutation" id="salutation" class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-xs font-medium">
                                            <option value="">Select Salutation (Optional)</option>
                                            @php
                                                $salutations = ['Mr.','Mrs.','Ms.','Dr.','Prof.'];
                                                foreach($salutations as $s){
                                                    $selected = ($contact->salutation == $s) ? 'selected' : '';
                                                    echo "<option value=\"$s\" $selected>$s</option>";
                                                }
                                            @endphp
                                        </select>
                                    </div>
                                </div>

                                <!-- First Name -->
                                <div>
                                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="firstname">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <input class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-xs font-medium" 
                                               id="firstname" name="firstname" type="text" value="{{ $contact->firstname }}" placeholder="Jane" required autofocus>
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="lastname">
                                        Last Name
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <input class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-xs font-medium" 
                                               id="lastname" name="lastname" type="text" value="{{ $contact->lastname }}" placeholder="Smith">
                                    </div>
                                </div>

                                <!-- Email Address -->
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="email">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <input class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-xs font-medium" 
                                               id="email" name="email" type="email" value="{{ $contact->email }}" placeholder="jane.smith@example.com" required>
                                    </div>
                                </div>

                                <!-- Company -->
                                <div>
                                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="company">
                                        Company / Organization
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <input class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-xs font-medium" 
                                               id="company" name="company" type="text" value="{{ $contact->company }}" placeholder="Acme Corp">
                                    </div>
                                </div>

                                <!-- Mobile / Phone -->
                                <div>
                                    <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="mobile">
                                        Mobile Phone Number
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-sm">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <input class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-amber-300/80 focus:ring-amber-300/80 text-xs font-medium font-mono" 
                                               id="mobile" name="mobile" type="text" value="{{ $contact->mobile }}" placeholder="+91 98765 43210">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="flex items-center justify-end space-x-3 pt-5 mt-6 border-t border-zinc-100 dark:border-zinc-800">
                            <a href="/contacts" class="px-5 py-2.5 bg-slate-100 dark:bg-[#141417] hover:bg-slate-200 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-semibold text-xs rounded-xl border border-slate-200 dark:border-zinc-800 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-amber-100 dark:hover:bg-amber-50 dark:text-zinc-950 font-bold text-xs rounded-xl shadow-2xs hover:shadow transition-all gap-2">
                                <i class="fas fa-check text-xs"></i>
                                <span>{{ empty($contact->id) ? 'Save Contact' : 'Update Contact' }}</span>
                            </button>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: Tag Assignment & High-Density Directory Row Preview (Equal Height) -->
                    <div class="bg-white/95 dark:bg-[#141417] backdrop-blur-md rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] dark:shadow-2xs border border-slate-200/90 dark:border-zinc-800 flex flex-col justify-between space-y-6">
                        
                        <div class="space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3.5 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50/80 text-amber-900 dark:bg-amber-950/30 dark:text-amber-200 font-bold text-2xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/20">
                                        Step 2
                                    </span>
                                    <div>
                                        <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                            <i class="fas fa-tags text-amber-500/80 dark:text-amber-300/80"></i>
                                            Assign Audience Tags
                                        </h3>
                                        <p class="text-2xs text-zinc-500 dark:text-zinc-400">Select audience segments for this subscriber.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Interactive Tags Selector -->
                            <div>
                                <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-2">
                                    Available Tags
                                </label>
                                @php
                                    $contact_tags = $contact->contactTags ?? [];
                                    $contact_tag_ids = [];
                                    foreach($contact_tags as $ct){
                                        $contact_tag_ids[] = $ct->tag_id;
                                    }
                                @endphp

                                @if(count($tags) > 0)
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-52 overflow-y-auto p-1">
                                        @foreach($tags as $t)
                                            @php $isChecked = in_array($t->id, $contact_tag_ids); @endphp
                                            <label class="cursor-pointer relative flex items-center p-2.5 rounded-xl border transition-all text-xs font-semibold select-none group {{ $isChecked ? 'bg-amber-50/80 border-amber-300 dark:bg-amber-950/40 dark:border-amber-500/40 text-amber-900 dark:text-amber-200 shadow-2xs' : 'bg-slate-50 dark:bg-[#09090B] border-slate-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800' }}">
                                                <input type="checkbox" name="tags[]" value="{{ $t->id }}" data-label="{{ $t->label }}" {{ $isChecked ? 'checked' : '' }} class="rounded border-zinc-300 dark:border-zinc-700 text-amber-500 focus:ring-amber-400 mr-2">
                                                <span class="truncate">{{ $t->label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 bg-slate-50 dark:bg-[#09090B] rounded-xl border border-slate-200 dark:border-zinc-800 text-center">
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">No tags created yet.</p>
                                        <a href="/tag-form/new" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline inline-flex items-center gap-1">
                                            <i class="fas fa-plus text-3xs"></i> Create a tag first
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Live Directory Table Row Preview -->
                            <div class="p-4 bg-slate-50/80 dark:bg-[#09090B]/60 rounded-xl border border-slate-200 dark:border-zinc-800 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-table text-amber-500 text-3xs"></i>
                                        Live Contacts Table Row Preview
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-3xs font-bold uppercase bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        Live Sync
                                    </span>
                                </div>

                                <!-- High-Density Mock Table Container -->
                                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-[#141417] shadow-2xs">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="border-b border-zinc-200/80 dark:border-zinc-800 bg-slate-100/60 dark:bg-[#09090B]/80 text-3xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                                <th class="py-2.5 pl-3.5 pr-2">Subscriber</th>
                                                <th class="py-2.5 px-2">Contact Info</th>
                                                <th class="py-2.5 px-2">Company</th>
                                                <th class="py-2.5 pl-2 pr-3.5">Tags</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="hover:bg-slate-50/90 dark:hover:bg-zinc-800/40 transition-colors">
                                                <!-- Subscriber Avatar + Name -->
                                                <td class="py-3 pl-3.5 pr-2 whitespace-nowrap">
                                                    <div class="flex items-center space-x-2.5">
                                                        <div id="mockInitials" class="w-8 h-8 rounded-xl bg-zinc-900 dark:bg-amber-100 text-white dark:text-zinc-950 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                                            U
                                                        </div>
                                                        <div class="min-w-0">
                                                            <div id="mockName" class="font-bold text-xs text-zinc-900 dark:text-white truncate max-w-[110px]">
                                                                Subscriber Name
                                                            </div>
                                                            <span class="text-3xs text-zinc-400 dark:text-zinc-500">
                                                                Just now
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Contact Info -->
                                                <td class="py-3 px-2 whitespace-nowrap">
                                                    <div class="space-y-0.5">
                                                        <div class="inline-flex items-center text-zinc-800 dark:text-zinc-200 font-medium text-xs truncate max-w-[130px]">
                                                            <i class="fas fa-envelope mr-1.5 text-3xs text-amber-500 shrink-0"></i>
                                                            <span id="mockEmail" class="truncate">subscriber@example.com</span>
                                                        </div>
                                                        <div id="mockMobile" class="text-3xs text-zinc-500 font-mono flex items-center" style="display: none;">
                                                            <i class="fas fa-phone mr-1.5 text-3xs opacity-60"></i>
                                                            <span></span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Company -->
                                                <td class="py-3 px-2 whitespace-nowrap">
                                                    <span id="mockCompany" class="inline-flex items-center px-2 py-0.5 rounded-lg text-3xs font-semibold bg-slate-100 text-slate-700 dark:bg-zinc-800 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700">
                                                        <span class="text-zinc-400 text-3xs italic">—</span>
                                                    </span>
                                                </td>

                                                <!-- Assigned Tags -->
                                                <td class="py-3 pl-2 pr-3.5">
                                                    <div id="mockTagsContainer" class="flex flex-wrap items-center gap-1 max-w-[140px]">
                                                        <span class="text-zinc-400 text-3xs italic">No tags</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Symmetrical Bottom Status Note -->
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-3xs text-zinc-500 dark:text-zinc-400">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                                Syncs live with directory table
                            </span>
                            <span class="font-mono text-zinc-400">#{{ $contact->id ?? 'new' }}</span>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
