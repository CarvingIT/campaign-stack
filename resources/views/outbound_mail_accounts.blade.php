@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>
<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {
		const initialType = "{{ strtoupper($account->type ?? 'SMTP') }}" || 'SMTP';
		selectConnectionType(initialType === 'API' ? 'API' : 'SMTP');

		const initialStatus = "{{ $account->status ?? '1' }}";
		setStatusValue(initialStatus === '0' ? '0' : '1');
	});

	function selectConnectionType(type) {
		document.getElementById('account_type').value = type;
		
		const smtpCard = document.getElementById('typeCardSMTP');
		const apiCard = document.getElementById('typeCardAPI');
		const smtpForm = document.getElementById('SMTP');
		const apiForm = document.getElementById('API');

		if (type === 'SMTP') {
			smtpCard.className = 'cursor-pointer p-4 rounded-2xl border-2 transition-all duration-200 relative flex items-start space-x-3.5 border-amber-500/80 dark:border-amber-400 bg-amber-50/40 dark:bg-amber-950/20 shadow-xs';
			document.getElementById('smtpCheck').classList.remove('hidden');

			apiCard.className = 'cursor-pointer p-4 rounded-2xl border transition-all duration-200 relative flex items-start space-x-3.5 border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-[#111114] hover:border-zinc-300 dark:hover:border-zinc-700';
			document.getElementById('apiCheck').classList.add('hidden');

			smtpForm.style.display = 'block';
			apiForm.style.display = 'none';
		} else {
			apiCard.className = 'cursor-pointer p-4 rounded-2xl border-2 transition-all duration-200 relative flex items-start space-x-3.5 border-purple-500/80 dark:border-purple-400 bg-purple-50/40 dark:bg-purple-950/20 shadow-xs';
			document.getElementById('apiCheck').classList.remove('hidden');

			smtpCard.className = 'cursor-pointer p-4 rounded-2xl border transition-all duration-200 relative flex items-start space-x-3.5 border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-[#111114] hover:border-zinc-300 dark:hover:border-zinc-700';
			document.getElementById('smtpCheck').classList.add('hidden');

			apiForm.style.display = 'block';
			smtpForm.style.display = 'none';
		}
	}

	function setStatusValue(val) {
		document.getElementById('account_status').value = val;
		const btnActive = document.getElementById('statusBtnActive');
		const btnInactive = document.getElementById('statusBtnInactive');

		if (val === '1') {
			btnActive.className = 'flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-xs transition-all';
			btnInactive.className = 'flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 transition-all';
		} else {
			btnInactive.className = 'flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 bg-rose-600 text-white shadow-xs transition-all';
			btnActive.className = 'flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 transition-all';
		}
	}

	function applyPreset(host, port, encryption) {
		const hostInput = document.getElementById('account_ip_address');
		const portInput = document.getElementById('account_port');
		const encInput = document.getElementById('account_encryption');

		hostInput.value = host;
		portInput.value = port;
		encInput.value = encryption;

		[hostInput, portInput, encInput].forEach(el => {
			el.classList.add('ring-2', 'ring-amber-500');
			setTimeout(() => el.classList.remove('ring-2', 'ring-amber-500'), 1000);
		});
	}

	var apiCount = 0;
	function newMailAccount() {
		var sopra = $('#line_item_new');
		$(sopra).append(`
			<div id="first${apiCount}" class="p-3.5 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800 mb-3 relative group transition-all">
				<div class="grid grid-cols-1 md:grid-cols-2 gap-3 pr-8">
					<div>
						<label class="block font-semibold text-3xs text-zinc-600 dark:text-zinc-300 uppercase tracking-wider mb-1">Key / Label</label>
						<input class="w-full rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#111114] dark:text-white placeholder-zinc-400 text-xs focus:ring-2 focus:ring-purple-400/80 focus:border-purple-400" type="text" name="label[]" id="label-${apiCount}" placeholder="e.g. api_key">
					</div>
					<div>
						<label class="block font-semibold text-3xs text-zinc-600 dark:text-zinc-300 uppercase tracking-wider mb-1">Value</label>
						<input class="w-full rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#111114] dark:text-white placeholder-zinc-400 text-xs focus:ring-2 focus:ring-purple-400/80 focus:border-purple-400 font-mono" type="text" name="value[]" id="value-${apiCount}" placeholder="e.g. SG.xxxxxxxxx">
					</div>
				</div>
				<button type="button" title="Remove Attribute" onclick="removeAccountNode(this.parentNode);" class="absolute right-2 top-2 w-7 h-7 rounded-lg text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center justify-center transition-colors">
					<i class="fas fa-times text-xs"></i>
				</button>
			</div>
		`);
		apiCount++;
	}

	function removeAccountNode(parentNode) {
		parentNode.remove();
	}

	function removeEditAccountNode(button) {
		const div = button.closest('.api-attribute-item');
		if (div) div.remove();
	}

	function togglePasswordVisibility() {
		const passInput = document.getElementById('account_password');
		const icon = document.getElementById('passToggleIcon');
		if (passInput.type === 'password') {
			passInput.type = 'text';
			icon.classList.remove('fa-eye');
			icon.classList.add('fa-eye-slash');
		} else {
			passInput.type = 'password';
			icon.classList.remove('fa-eye-slash');
			icon.classList.add('fa-eye');
		}
	}
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-3xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1 space-x-2 uppercase tracking-wider">
                    <a href="/mail-accounts" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Mail Accounts</a>
                    <span>/</span>
                    <span class="text-amber-600 dark:text-amber-400">
                        {{ empty($account->id) ? 'Add Gateway' : 'Edit Gateway' }}
                    </span>
                </nav>
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 dark:bg-amber-400 shadow-2xs"></span>
                    <h2 class="font-extrabold text-2xl text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        <i class="fas {{ empty($account->id) ? 'fa-plus-circle' : 'fa-edit' }} text-amber-500"></i>
                        <span>{{ empty($account->id) ? __('Configure Outbound Gateway') : __('Edit Outbound Gateway') }}</span>
                    </h2>
                </div>
            </div>
            <div>
                <a href="/mail-accounts" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-2">
                    <i class="fas fa-arrow-left text-3xs"></i>
                    <span>Back to Relays</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-10 pt-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form name="save-account-form" action="/saveaccount" method="post" class="space-y-6">
                @csrf
                <input type="hidden" name="account_id" value="{{ $account->id }}" />
                <input type="hidden" id="account_type" name="account_type" value="{{ strtoupper($account->type ?? 'SMTP') }}" />
                <input type="hidden" id="account_status" name="account_status" value="{{ $account->status ?? '1' }}" />

                @php
                    $config_array = is_string($account->config) ? json_decode($account->config) : (object)[];
                @endphp

                <!-- STEP 1: Sender Identity & Account Profile -->
                <div class="bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 space-y-5">
                    <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-3.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 font-extrabold text-3xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/30">
                                Step 1
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-id-card text-amber-500 text-xs"></i>
                                    Gateway Identity & Profile
                                </h3>
                                <p class="text-3xs text-zinc-500 dark:text-zinc-400">Name this relay and configure default sender headers for broadcast dispatches.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Account Name -->
                        <div>
                            <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5" for="account_name">
                                Gateway Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                    <i class="fas fa-server"></i>
                                </div>
                                <input class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 text-xs font-medium focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs" 
                                       id="account_name" name="account_name" type="text" value="{{ $account->name }}" placeholder="e.g. Primary SendGrid SMTP Relay" required>
                            </div>
                        </div>

                        <!-- Account Status Toggle -->
                        <div>
                            <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                                Transmission Status <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center p-1 bg-zinc-100 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800">
                                <button type="button" id="statusBtnActive" onclick="setStatusValue('1');" 
                                        class="flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-xs transition-all">
                                    <i class="fas fa-tower-broadcast text-3xs"></i>
                                    <span>Active (In Rotation)</span>
                                </button>
                                <button type="button" id="statusBtnInactive" onclick="setStatusValue('0');" 
                                        class="flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 transition-all">
                                    <i class="fas fa-pause-circle text-3xs"></i>
                                    <span>Paused (Disabled)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Default From Name -->
                        <div>
                            <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5" for="account_from_username">
                                Default From Name
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                    <i class="fas fa-user"></i>
                                </div>
                                <input class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 text-xs focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs" 
                                       id="account_from_username" name="account_from_username" type="text" value="{{ @$config_array->from_username }}" placeholder="e.g. Campaign Stack Dispatcher">
                            </div>
                        </div>

                        <!-- Default From Address -->
                        <div>
                            <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5" for="account_from_address">
                                Default From Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 text-xs font-mono focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs" 
                                       id="account_from_address" name="account_from_address" type="email" value="{{ @$config_array->from_address }}" placeholder="e.g. dispatch@yourdomain.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Connection Protocol Selection -->
                <div class="bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 space-y-5">
                    <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-3.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 font-extrabold text-3xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/30">
                                Step 2
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-plug text-amber-500 text-xs"></i>
                                    Transmission Protocol
                                </h3>
                                <p class="text-3xs text-zinc-500 dark:text-zinc-400">Choose between a standard SMTP socket connection or a REST API gateway.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Connection Protocol Choice Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div id="typeCardSMTP" onclick="selectConnectionType('SMTP');" 
                             class="cursor-pointer p-4 rounded-2xl border-2 transition-all duration-200 relative flex items-start space-x-3.5">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm shrink-0 border border-blue-200/60 dark:border-blue-500/20 shadow-2xs">
                                <i class="fas fa-network-wired"></i>
                            </div>
                            <div class="space-y-0.5 pr-4">
                                <h4 class="font-bold text-xs text-zinc-900 dark:text-white">SMTP Socket Relay</h4>
                                <p class="text-3xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Connect SendGrid, Amazon SES, Mailgun, Google Workspace, or custom Postfix/Exim servers.
                                </p>
                            </div>
                            <div id="smtpCheck" class="absolute top-3.5 right-3.5 text-amber-500 text-sm">
                                <i class="fas fa-circle-check"></i>
                            </div>
                        </div>

                        <div id="typeCardAPI" onclick="selectConnectionType('API');" 
                             class="cursor-pointer p-4 rounded-2xl border transition-all duration-200 relative flex items-start space-x-3.5">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm shrink-0 border border-purple-200/60 dark:border-purple-500/20 shadow-2xs">
                                <i class="fas fa-code"></i>
                            </div>
                            <div class="space-y-0.5 pr-4">
                                <h4 class="font-bold text-xs text-zinc-900 dark:text-white">API Endpoint & Tokens</h4>
                                <p class="text-3xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Send via REST endpoints using custom HTTP headers, Authorization tokens, and key-values.
                                </p>
                            </div>
                            <div id="apiCheck" class="absolute top-3.5 right-3.5 text-purple-500 text-sm hidden">
                                <i class="fas fa-circle-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Server Credentials & Security Settings -->
                <div id="SMTP" class="content-div bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 space-y-5">
                    <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-3.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 font-extrabold text-3xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/30">
                                Step 3
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-network-wired text-amber-500 text-xs"></i>
                                    SMTP Server Credentials & Security
                                </h3>
                                <p class="text-3xs text-zinc-500 dark:text-zinc-400">Configure host address, port, login credentials, and TLS/SSL encryption.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-full text-3xs font-extrabold uppercase">
                            SMTP
                        </span>
                    </div>

                    <!-- 1-Click Provider Presets bar -->
                    <div class="bg-zinc-50/70 dark:bg-[#09090B] p-3.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 space-y-2">
                        <label class="block text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            1-Click Provider Presets (Auto-fill host & port)
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="applyPreset('smtp.sendgrid.net', '587', 'TLS');" 
                                    class="px-2.5 py-1.5 bg-white dark:bg-[#111114] hover:bg-amber-50/60 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/80 text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fas fa-bolt text-amber-500 text-3xs"></i> SendGrid
                            </button>
                            <button type="button" onclick="applyPreset('smtp.gmail.com', '587', 'TLS');" 
                                    class="px-2.5 py-1.5 bg-white dark:bg-[#111114] hover:bg-amber-50/60 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/80 text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fab fa-google text-rose-500 text-3xs"></i> Google / Gmail
                            </button>
                            <button type="button" onclick="applyPreset('email-smtp.us-east-1.amazonaws.com', '587', 'TLS');" 
                                    class="px-2.5 py-1.5 bg-white dark:bg-[#111114] hover:bg-amber-50/60 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/80 text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fab fa-aws text-amber-500 text-3xs"></i> Amazon SES
                            </button>
                            <button type="button" onclick="applyPreset('sandbox.smtp.mailtrap.io', '2525', 'TLS');" 
                                    class="px-2.5 py-1.5 bg-white dark:bg-[#111114] hover:bg-amber-50/60 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/80 text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fas fa-flask text-emerald-500 text-3xs"></i> Mailtrap
                            </button>
                            <button type="button" onclick="applyPreset('smtp.mailgun.org', '587', 'TLS');" 
                                    class="px-2.5 py-1.5 bg-white dark:bg-[#111114] hover:bg-amber-50/60 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/80 text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fas fa-paper-plane text-purple-500 text-3xs"></i> Mailgun
                            </button>
                            <button type="button" onclick="applyPreset('smtp.postmarkapp.com', '587', 'TLS');" 
                                    class="px-2.5 py-1.5 bg-white dark:bg-[#111114] hover:bg-amber-50/60 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/80 text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fas fa-envelope-open-text text-yellow-500 text-3xs"></i> Postmark
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Host IP / Address -->
                        <div>
                            <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5" for="account_ip_address">
                                Server Host / IP Address <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <input class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 text-xs font-mono focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs" 
                                       id="account_ip_address" name="account_ip_address" type="text" value="{{ @$config_array->ip_address }}" placeholder="e.g. smtp.sendgrid.net">
                            </div>
                        </div>

                        <!-- Port -->
                        <div>
                            <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5" for="account_port">
                                Port Number <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                    <i class="fas fa-network-wired"></i>
                                </div>
                                <input class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 text-xs font-mono focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs" 
                                       id="account_port" name="account_port" type="text" value="{{ @$config_array->port }}" placeholder="587">
                            </div>
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5" for="account_username">
                                Username / Auth User <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                    <i class="fas fa-user-lock"></i>
                                </div>
                                <input class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 text-xs font-mono focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs" 
                                       id="account_username" name="account_username" type="text" value="{{ @$config_array->username }}" placeholder="apikey or username">
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5" for="account_password">
                                Password / Secret API Key <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input class="w-full pl-9 pr-10 py-2.5 rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 text-xs font-mono focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs" 
                                       id="account_password" name="account_password" type="password" value="{{ @$config_array->password }}" placeholder="••••••••••••••••">
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                                    <i id="passToggleIcon" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Encryption Protocol -->
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5" for="account_encryption">
                                Encryption Security Protocol
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                    <i class="fas fa-shield-halved"></i>
                                </div>
                                <select class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white text-xs font-medium focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all shadow-2xs" 
                                        id="account_encryption" name="account_encryption">
                                    <option value="">Select Security Encryption (None / Plain)</option>
                                    <option value="TLS" @if(@$config_array->encryption == 'TLS') selected @endif>TLS (STARTTLS - Standard for Port 587)</option>
                                    <option value="SSL" @if(@$config_array->encryption == 'SSL') selected @endif>SSL (Implicit SSL - Standard for Port 465)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Parameters Panel -->
                <div id="API" class="content-div bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 space-y-5" style="display: none;">
                    <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-3.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-0.5 rounded-md bg-purple-50 text-purple-800 dark:bg-purple-950/40 dark:text-purple-300 font-extrabold text-3xs uppercase tracking-wider border border-purple-200/60 dark:border-purple-500/30">
                                Step 3
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-code text-purple-500 text-xs"></i>
                                    API Key & Custom HTTP Headers
                                </h3>
                                <p class="text-3xs text-zinc-500 dark:text-zinc-400">Configure key-value header credentials required by your external REST endpoint.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800 rounded-full text-3xs font-extrabold uppercase">
                            API
                        </span>
                    </div>

                    <div id="existing_api_items" class="space-y-3">
                        @if(strtoupper($account->type) == 'API' && is_array($config_array))
                            @foreach($config_array as $api_acc)
                                <div class="api-attribute-item p-3.5 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800 relative group">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pr-8">
                                        <div>
                                            <label class="block font-semibold text-3xs text-zinc-600 dark:text-zinc-300 uppercase tracking-wider mb-1">Key / Label</label>
                                            <input class="w-full rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#111114] dark:text-white text-xs focus:ring-2 focus:ring-purple-400/80 focus:border-purple-400" 
                                                   type="text" name="label[]" value="{{ $api_acc->key ?? '' }}" placeholder="e.g. Authorization">
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-3xs text-zinc-600 dark:text-zinc-300 uppercase tracking-wider mb-1">Value</label>
                                            <input class="w-full rounded-xl border-zinc-200 dark:border-zinc-800 dark:bg-[#111114] dark:text-white text-xs font-mono focus:ring-2 focus:ring-purple-400/80 focus:border-purple-400" 
                                                   type="text" name="value[]" value="{{ $api_acc->value ?? '' }}" placeholder="e.g. Bearer SG.xxxx">
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeEditAccountNode(this)" title="Remove Attribute" class="absolute right-2 top-2 w-7 h-7 rounded-lg text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center justify-center transition-colors">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div id="line_item_new" class="space-y-3"></div>

                    <div>
                        <button type="button" onclick="newMailAccount();" class="inline-flex items-center px-4 py-2 bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800 rounded-xl font-bold text-xs hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors gap-2">
                            <i class="fas fa-plus text-3xs"></i>
                            <span>Add Custom Parameter</span>
                        </button>
                    </div>
                </div>

                <!-- Footer Action Buttons Bar -->
                <div class="flex items-center justify-between pt-4 border-t border-zinc-200/80 dark:border-zinc-800">
                    <a href="/mail-accounts" class="px-5 py-2.5 bg-white dark:bg-[#111114] hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-semibold text-xs rounded-xl border border-zinc-200/80 dark:border-zinc-800 transition-colors shadow-2xs">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-bold text-xs rounded-xl shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 transition-all gap-2">
                        <i class="fas fa-check text-xs"></i>
                        <span>Save Outbound Gateway</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
