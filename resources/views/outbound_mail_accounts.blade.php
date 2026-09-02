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
			smtpCard.classList.add('border-zinc-900', 'ring-2', 'ring-zinc-900/10', 'bg-zinc-100/80', 'dark:bg-zinc-800/60', 'dark:border-zinc-200');
			smtpCard.classList.remove('border-zinc-200', 'dark:border-zinc-800', 'bg-white', 'dark:bg-[#141417]');
			document.getElementById('smtpCheck').classList.remove('hidden');

			apiCard.classList.remove('border-zinc-900', 'ring-2', 'ring-zinc-900/10', 'bg-zinc-100/80', 'dark:bg-zinc-800/60', 'dark:border-zinc-200');
			apiCard.classList.add('border-zinc-200', 'dark:border-zinc-800', 'bg-white', 'dark:bg-[#141417]');
			document.getElementById('apiCheck').classList.add('hidden');

			smtpForm.style.display = 'block';
			apiForm.style.display = 'none';
		} else {
			apiCard.classList.add('border-zinc-900', 'ring-2', 'ring-zinc-900/10', 'bg-zinc-100/80', 'dark:bg-zinc-800/60', 'dark:border-zinc-200');
			apiCard.classList.remove('border-zinc-200', 'dark:border-zinc-800', 'bg-white', 'dark:bg-[#141417]');
			document.getElementById('apiCheck').classList.remove('hidden');

			smtpCard.classList.remove('border-zinc-900', 'ring-2', 'ring-zinc-900/10', 'bg-zinc-100/80', 'dark:bg-zinc-800/60', 'dark:border-zinc-200');
			smtpCard.classList.add('border-zinc-200', 'dark:border-zinc-800', 'bg-white', 'dark:bg-[#141417]');
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
			btnActive.className = 'flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 shadow-2xs transition-all';
			btnInactive.className = 'flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 bg-zinc-100 dark:bg-[#09090B] text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all';
		} else {
			btnInactive.className = 'flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 bg-amber-600 text-white shadow-2xs transition-all';
			btnActive.className = 'flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 bg-zinc-100 dark:bg-[#09090B] text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all';
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
			el.classList.add('ring-2', 'ring-zinc-800', 'dark:ring-zinc-200');
			setTimeout(() => el.classList.remove('ring-2', 'ring-zinc-800', 'dark:ring-zinc-200'), 1000);
		});
	}

	var apiCount = 0;
	function newMailAccount() {
		var sopra = $('#line_item_new');
		$(sopra).append(`
			<div id="first${apiCount}" class="p-3 bg-zinc-50 dark:bg-[#09090B]/80 rounded-xl border border-zinc-200 dark:border-zinc-800 mb-3 relative group transition-all">
				<div class="grid grid-cols-1 md:grid-cols-2 gap-3 pr-8">
					<div>
						<label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1">Key / Label</label>
						<input class="w-full rounded-lg border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs" type="text" name="label[]" id="label-${apiCount}" placeholder="e.g. api_key">
					</div>
					<div>
						<label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1">Value</label>
						<input class="w-full rounded-lg border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs" type="text" name="value[]" id="value-${apiCount}" placeholder="e.g. SG.xxxxxxxxx">
					</div>
				</div>
				<button type="button" title="Remove Attribute" onclick="removeAccountNode(this.parentNode);" class="absolute right-2 top-2 w-7 h-7 rounded-lg text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-zinc-800 flex items-center justify-center transition-colors">
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
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1 space-x-2">
                    <a href="/mail-accounts" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Mail Accounts</a>
                    <span>/</span>
                    <span class="text-zinc-900 dark:text-white font-semibold">
                        {{ empty($account->id) ? 'Add Outbound Server' : 'Edit Server' }}
                    </span>
                </nav>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-900 dark:bg-zinc-100 shadow-2xs"></span>
                    <h2 class="font-black text-2xl text-zinc-900 dark:text-white leading-tight flex items-center gap-2.5">
                        <i class="fas {{ empty($account->id) ? 'fa-plus-circle' : 'fa-edit' }} text-zinc-800 dark:text-zinc-200"></i>
                        {{ empty($account->id) ? __('New Outbound Mail Account') : __('Edit Outbound Mail Account') }}
                    </h2>
                </div>
            </div>
            <div>
                <a href="/mail-accounts" class="inline-flex items-center px-3.5 py-2 bg-zinc-100 dark:bg-[#141417] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors gap-2">
                    <i class="fas fa-arrow-left text-2xs"></i>
                    <span>Back to Accounts</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-8 pt-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form name="save-account-form" action="/saveaccount" method="post" class="space-y-6">
                @csrf
                <input type="hidden" name="account_id" value="{{ $account->id }}" />
                <input type="hidden" id="account_type" name="account_type" value="{{ strtoupper($account->type ?? 'SMTP') }}" />
                <input type="hidden" id="account_status" name="account_status" value="{{ $account->status ?? '1' }}" />

                @php
                    $config_array = is_string($account->config) ? json_decode($account->config) : (object)[];
                @endphp

                <!-- STEP 1: Sender Identity & Account Profile -->
                <div class="bg-white dark:bg-[#141417] rounded-2xl p-6 shadow-2xs border border-zinc-200/80 dark:border-zinc-800 space-y-5">
                    <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-1 rounded-lg bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 font-bold text-2xs uppercase tracking-wider border border-zinc-200 dark:border-zinc-700">
                                Step 1
                            </span>
                            <div>
                                <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-id-card text-zinc-800 dark:text-zinc-200"></i>
                                    Sender Identity & Profile
                                </h3>
                                <p class="text-2xs text-zinc-500 dark:text-zinc-400">Name this mail account and set default sender headers.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Account Name -->
                        <div>
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="account_name">
                                Account Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-xs">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <input class="w-full pl-9 pr-3 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs font-medium" 
                                       id="account_name" name="account_name" type="text" value="{{ $account->name }}" placeholder="e.g. Primary SendGrid Server" required>
                            </div>
                        </div>

                        <!-- Account Status Toggle -->
                        <div>
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5">
                                Sending Status <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-2 bg-zinc-50 dark:bg-[#09090B] p-1 rounded-xl border border-zinc-200 dark:border-zinc-800">
                                <button type="button" id="statusBtnActive" onclick="setStatusValue('1');" 
                                        class="flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 shadow-2xs transition-all">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Active (Ready)</span>
                                </button>
                                <button type="button" id="statusBtnInactive" onclick="setStatusValue('0');" 
                                        class="flex-1 py-2 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 bg-zinc-100 dark:bg-[#141417] text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all">
                                    <i class="fas fa-pause-circle"></i>
                                    <span>Paused (Disabled)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Default From Name -->
                        <div>
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="account_from_username">
                                Default From Name
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-xs">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <input class="w-full pl-9 pr-3 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs" 
                                       id="account_from_username" name="account_from_username" type="text" value="{{ @$config_array->from_username }}" placeholder="e.g. CampaignStack News">
                            </div>
                        </div>

                        <!-- Default From Address -->
                        <div>
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="account_from_address">
                                Default From Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-xs">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input class="w-full pl-9 pr-3 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs" 
                                       id="account_from_address" name="account_from_address" type="email" value="{{ @$config_array->from_address }}" placeholder="e.g. newsletter@yourdomain.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Connection Protocol Selection -->
                <div class="bg-white dark:bg-[#141417] rounded-2xl p-6 shadow-2xs border border-zinc-200/80 dark:border-zinc-800 space-y-5">
                    <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-1 rounded-lg bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 font-bold text-2xs uppercase tracking-wider border border-zinc-200 dark:border-zinc-700">
                                Step 2
                            </span>
                            <div>
                                <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-plug text-zinc-800 dark:text-zinc-200"></i>
                                    Connection Protocol
                                </h3>
                                <p class="text-2xs text-zinc-500 dark:text-zinc-400">Choose between a standard SMTP mail server or an API HTTP endpoint.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Connection Protocol Choice Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div id="typeCardSMTP" onclick="selectConnectionType('SMTP');" 
                             class="cursor-pointer p-4 rounded-xl border-2 transition-all duration-200 relative flex items-start space-x-3 shadow-2xs hover:shadow">
                            <div class="w-10 h-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 flex items-center justify-center text-lg shrink-0">
                                <i class="fas fa-network-wired"></i>
                            </div>
                            <div class="space-y-0.5 pr-4">
                                <h4 class="font-bold text-sm text-zinc-900 dark:text-white">SMTP Mail Server</h4>
                                <p class="text-2xs text-zinc-600 dark:text-zinc-400 leading-snug">
                                    Connect SendGrid, SES, Mailtrap, Gmail, or custom SMTP servers.
                                </p>
                            </div>
                            <div id="smtpCheck" class="absolute top-3 right-3 text-zinc-900 dark:text-zinc-100 text-base">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>

                        <div id="typeCardAPI" onclick="selectConnectionType('API');" 
                             class="cursor-pointer p-4 rounded-xl border-2 transition-all duration-200 relative flex items-start space-x-3 shadow-2xs hover:shadow">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-950/70 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shrink-0">
                                <i class="fas fa-code"></i>
                            </div>
                            <div class="space-y-0.5 pr-4">
                                <h4 class="font-bold text-sm text-zinc-900 dark:text-white">API Endpoint & Key</h4>
                                <p class="text-2xs text-zinc-600 dark:text-zinc-400 leading-snug">
                                    Configure REST API keys & custom key-value header parameters.
                                </p>
                            </div>
                            <div id="apiCheck" class="absolute top-3 right-3 text-purple-600 dark:text-purple-400 text-base hidden">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Server Credentials & Security Settings -->
                <div id="SMTP" class="content-div bg-white dark:bg-[#141417] rounded-2xl p-6 shadow-2xs border border-zinc-200/80 dark:border-zinc-800 space-y-5">
                    <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-1 rounded-lg bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 font-bold text-2xs uppercase tracking-wider border border-zinc-200 dark:border-zinc-700">
                                Step 3
                            </span>
                            <div>
                                <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-network-wired text-zinc-800 dark:text-zinc-200"></i>
                                    Server Credentials & Security
                                </h3>
                                <p class="text-2xs text-zinc-500 dark:text-zinc-400">Enter host address, port, login credentials, and security protocol.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-blue-50 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-full text-xs font-bold">SMTP</span>
                    </div>

                    <!-- 1-Click Provider Presets bar -->
                    <div class="bg-zinc-50 dark:bg-[#09090B]/80 p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 space-y-2">
                        <label class="block text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">1-Click Provider Presets (Auto-fill Credentials)</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="applyPreset('smtp.sendgrid.net', '587', 'TLS');" 
                                    class="px-3 py-1.5 bg-white dark:bg-[#141417] hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fas fa-bolt text-amber-500 text-2xs"></i> SendGrid
                            </button>
                            <button type="button" onclick="applyPreset('smtp.gmail.com', '587', 'TLS');" 
                                    class="px-3 py-1.5 bg-white dark:bg-[#141417] hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fab fa-google text-red-500 text-2xs"></i> Gmail / Google
                            </button>
                            <button type="button" onclick="applyPreset('email-smtp.us-east-1.amazonaws.com', '587', 'TLS');" 
                                    class="px-3 py-1.5 bg-white dark:bg-[#141417] hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fab fa-aws text-amber-500 text-2xs"></i> Amazon SES
                            </button>
                            <button type="button" onclick="applyPreset('sandbox.smtp.mailtrap.io', '2525', 'TLS');" 
                                    class="px-3 py-1.5 bg-white dark:bg-[#141417] hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fas fa-flask text-emerald-500 text-2xs"></i> Mailtrap
                            </button>
                            <button type="button" onclick="applyPreset('smtp.mailgun.org', '587', 'TLS');" 
                                    class="px-3 py-1.5 bg-white dark:bg-[#141417] hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1.5">
                                <i class="fas fa-paper-plane text-purple-500 text-2xs"></i> Mailgun
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Host IP / Address -->
                        <div>
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="account_ip_address">
                                Server Host / IP Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-xs">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <input class="w-full pl-9 pr-3 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs font-mono" 
                                       id="account_ip_address" name="account_ip_address" type="text" value="{{ @$config_array->ip_address }}" placeholder="e.g. smtp.sendgrid.net">
                            </div>
                        </div>

                        <!-- Port -->
                        <div>
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="account_port">
                                Port Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-xs">
                                    <i class="fas fa-plug"></i>
                                </div>
                                <input class="w-full pl-9 pr-3 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs font-mono" 
                                       id="account_port" name="account_port" type="text" value="{{ @$config_array->port }}" placeholder="587">
                            </div>
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="account_username">
                                Username / Auth User
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-xs">
                                    <i class="fas fa-key"></i>
                                </div>
                                <input class="w-full pl-9 pr-3 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs" 
                                       id="account_username" name="account_username" type="text" value="{{ @$config_array->username }}" placeholder="apikey or username">
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="account_password">
                                Password / Secret Token
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-xs">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input class="w-full pl-9 pr-9 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white placeholder-zinc-400 shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs" 
                                       id="account_password" name="account_password" type="password" value="{{ @$config_array->password }}" placeholder="••••••••">
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                                    <i id="passToggleIcon" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Encryption Protocol -->
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="account_encryption">
                                Encryption Security Protocol
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-400 text-xs">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <select class="w-full pl-9 pr-3 py-2.5 rounded-xl border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs font-medium" 
                                        id="account_encryption" name="account_encryption">
                                    <option value="">Select Security Encryption</option>
                                    <option value="TLS" @if(@$config_array->encryption == 'TLS') selected @endif>TLS (Recommended for Port 587 / STARTTLS)</option>
                                    <option value="SSL" @if(@$config_array->encryption == 'SSL') selected @endif>SSL (Recommended for Port 465)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Parameters Panel -->
                <div id="API" class="content-div bg-white dark:bg-[#141417] rounded-2xl p-6 shadow-2xs border border-zinc-200/80 dark:border-zinc-800 space-y-5" style="display: none;">
                    <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-1 rounded-lg bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 font-bold text-2xs uppercase tracking-wider border border-zinc-200 dark:border-zinc-700">
                                Step 3
                            </span>
                            <div>
                                <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-code text-purple-600 dark:text-purple-400"></i>
                                    API Key & Header Attributes
                                </h3>
                                <p class="text-2xs text-zinc-500 dark:text-zinc-400">Configure custom Key-Value header parameters required for API dispatching.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-purple-50 text-purple-800 dark:bg-purple-950/70 dark:text-purple-300 border border-purple-200 dark:border-purple-800 rounded-full text-xs font-bold">API</span>
                    </div>

                    <div id="existing_api_items" class="space-y-3">
                        @if(strtoupper($account->type) == 'API' && is_array($config_array))
                            @foreach($config_array as $api_acc)
                                <div class="api-attribute-item p-3.5 bg-zinc-50 dark:bg-[#09090B]/80 rounded-xl border border-zinc-200 dark:border-zinc-800 relative group">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pr-8">
                                        <div>
                                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1">Key / Label</label>
                                            <input class="w-full rounded-lg border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs" 
                                                   type="text" name="label[]" value="{{ $api_acc->key ?? '' }}">
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-2xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1">Value</label>
                                            <input class="w-full rounded-lg border-zinc-300 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white shadow-2xs focus:border-zinc-800 dark:focus:border-zinc-200 focus:ring-zinc-800 dark:focus:ring-zinc-200 text-xs" 
                                                   type="text" name="value[]" value="{{ $api_acc->value ?? '' }}">
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeEditAccountNode(this)" title="Remove Attribute" class="absolute right-2 top-2 w-7 h-7 rounded-lg text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-zinc-800 flex items-center justify-center transition-colors">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div id="line_item_new" class="space-y-3"></div>

                    <div>
                        <button type="button" onclick="newMailAccount();" class="inline-flex items-center px-4 py-2 bg-purple-50 dark:bg-purple-950/70 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800 rounded-xl font-bold text-xs hover:bg-purple-100 dark:hover:bg-purple-900/60 transition-colors gap-2">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Add Parameter</span>
                        </button>
                    </div>
                </div>

                <!-- Footer Action Buttons Bar -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <a href="/mail-accounts" class="px-5 py-2.5 bg-zinc-100 dark:bg-[#141417] hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-semibold text-xs rounded-xl border border-zinc-200 dark:border-zinc-800 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs rounded-xl shadow-2xs transition-all gap-2">
                        <i class="fas fa-check text-xs"></i>
                        <span>Save Mail Account</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
