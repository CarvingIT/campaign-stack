@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script>
    function copyText(text, btnId) {
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById(btnId);
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-emerald-500 text-3xs"></i> <span class="text-emerald-500">Copied</span>';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                }, 1500);
            }
        });
    }

    function openTestModal() {
        document.getElementById('testModal').classList.remove('hidden');
        document.getElementById('testResultBox').className = 'hidden';
        document.getElementById('testResultBox').textContent = '';
    }

    function closeTestModal() {
        document.getElementById('testModal').classList.add('hidden');
    }

    function runSmtpTest() {
        const recipient = document.getElementById('testRecipientEmail').value.trim();
        const accountId = "{{ $account->id }}";
        const resultBox = document.getElementById('testResultBox');
        const btn = document.getElementById('runTestSubmitBtn');

        if (!recipient) {
            alert('Please enter a destination test email address.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-3xs"></i> Testing Gateway...';

        resultBox.className = 'p-3.5 rounded-xl text-xs leading-snug bg-blue-50 text-blue-900 dark:bg-blue-950/60 dark:text-blue-200 border border-blue-200 dark:border-blue-900 flex items-center gap-2';
        resultBox.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Establishing socket handshake and transmitting verification dispatch...';

        fetch('/mail-accounts/test-smtp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                account_id: accountId,
                recipient: recipient
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane text-3xs"></i> Send Test Message';

            if (data.success) {
                resultBox.className = 'p-3.5 rounded-xl text-xs leading-snug bg-emerald-50 text-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-900 flex items-start gap-2.5';
                resultBox.innerHTML = `<i class="fas fa-circle-check text-emerald-500 text-sm mt-0.5 shrink-0"></i> <div><strong>Success!</strong> ${data.message}</div>`;
            } else {
                resultBox.className = 'p-3.5 rounded-xl text-xs leading-snug bg-rose-50 text-rose-900 dark:bg-rose-950/60 dark:text-rose-200 border border-rose-200 dark:border-rose-900 flex items-start gap-2.5';
                resultBox.innerHTML = `<i class="fas fa-circle-exclamation text-rose-500 text-sm mt-0.5 shrink-0"></i> <div><strong>Connection Failed:</strong> ${data.message}</div>`;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane text-3xs"></i> Send Test Message';
            resultBox.className = 'p-3.5 rounded-xl text-xs leading-snug bg-rose-50 text-rose-900 dark:bg-rose-950/60 dark:text-rose-200 border border-rose-200 dark:border-rose-900 flex items-start gap-2.5';
            resultBox.innerHTML = `<i class="fas fa-circle-exclamation text-rose-500 text-sm mt-0.5 shrink-0"></i> <div><strong>Request Error:</strong> Network or server failure.</div>`;
        });
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
                    <span class="text-amber-600 dark:text-amber-400">Gateway Telemetry</span>
                </nav>
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 dark:bg-amber-400 shadow-2xs"></span>
                    <h2 class="font-extrabold text-2xl text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        <i class="fas fa-server text-amber-500"></i>
                        <span>{{ $account->name ?? 'Mail Gateway' }}</span>
                    </h2>
                </div>
            </div>
            
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="/mail-accounts" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1.5">
                    <i class="fas fa-arrow-left text-3xs"></i>
                    <span>Back to Relays</span>
                </a>
                @if(strtoupper($account->type) === 'SMTP')
                    <button type="button" onclick="openTestModal()" class="inline-flex items-center px-3.5 py-2 bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-xs font-bold rounded-xl border border-amber-200/80 dark:border-amber-500/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-all shadow-2xs gap-1.5">
                        <i class="fas fa-bolt text-3xs text-amber-500"></i>
                        <span>Test Relay</span>
                    </button>
                @endif
                <a href="/account-form/{{ $account->id }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 transition-all gap-1.5">
                    <i class="fas fa-pen text-3xs"></i>
                    <span>Edit Gateway</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-10 pt-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @php
                $config = is_string($account->config) ? json_decode($account->config) : (object)[];
                $isActive = (int)$account->status === 1;
                $isCooling = $isActive && !empty($account->active_after) && \Carbon\Carbon::parse($account->active_after)->gt(now());
            @endphp

            <!-- CARD 1: Identity & Telemetry Overview -->
            <div class="bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800/80 pb-5">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-2xl {{ strtoupper($account->type) === 'API' ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 border-purple-200/60 dark:border-purple-500/20' : 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-300 border-blue-200/60 dark:border-blue-500/20' }} border flex items-center justify-center text-xl shrink-0 shadow-2xs">
                            <i class="fas {{ strtoupper($account->type) === 'API' ? 'fa-code' : 'fa-network-wired' }}"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h3 class="text-xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                                    {{ $account->name ?? 'Unnamed Gateway' }}
                                </h3>
                            </div>
                            <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-1">
                                Relay ID #{{ $account->id }} • Last updated {{ $account->updated_at ? \Carbon\Carbon::parse($account->updated_at)->diffForHumans() : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <!-- Status & Type Capsules -->
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($isActive && !$isCooling)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/80">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                Active in Rotation
                            </span>
                        @elseif($isCooling)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800/80">
                                <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span>
                                Cooldown Active (until {{ \Carbon\Carbon::parse($account->active_after)->format('H:i:s') }})
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-600 border border-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700">
                                Paused / Inactive
                            </span>
                        @endif

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase {{ strtoupper($account->type) === 'API' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800' }}">
                            {{ strtoupper($account->type) }}
                        </span>
                    </div>
                </div>

                @if(strtoupper($account->type) === 'SMTP')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800">
                            <span class="block text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">
                                Default From Name
                            </span>
                            <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $config->from_username ?? 'Not specified' }}
                            </div>
                        </div>

                        <div class="p-4 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                            <div>
                                <span class="block text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">
                                    Default From Email
                                </span>
                                <div class="text-sm font-semibold font-mono text-zinc-900 dark:text-white">
                                    {{ $config->from_address ?? 'Not specified' }}
                                </div>
                            </div>
                            @if(!empty($config->from_address))
                                <button type="button" id="copyFromBtn" onclick="copyText('{{ $config->from_address }}', 'copyFromBtn')" 
                                        class="p-2 text-zinc-400 hover:text-amber-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors text-xs" title="Copy Address">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- CARD 2: Technical Specifications & Server Configuration -->
            <div class="bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 space-y-5">
                <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-3 flex items-center justify-between">
                    <h4 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-gears text-amber-500 text-xs"></i>
                        Transmission Specifications & Socket Credentials
                    </h4>
                    <span class="text-3xs font-mono text-zinc-400 dark:text-zinc-500 uppercase">
                        Protocol: {{ strtoupper($account->type) }}
                    </span>
                </div>

                @if(strtoupper($account->type) === 'SMTP')
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Host IP -->
                        <div class="p-4 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800 relative group">
                            <span class="block text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">
                                Host / IP Address
                            </span>
                            <div class="font-mono text-xs font-semibold text-zinc-900 dark:text-white break-all">
                                {{ $config->ip_address ?? 'Not specified' }}
                            </div>
                            @if(!empty($config->ip_address))
                                <button type="button" id="copyHostBtn" onclick="copyText('{{ $config->ip_address }}', 'copyHostBtn')" 
                                        class="absolute top-3 right-3 text-zinc-400 hover:text-amber-500 text-3xs transition-colors" title="Copy Host">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @endif
                        </div>

                        <!-- Port -->
                        <div class="p-4 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800">
                            <span class="block text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">
                                Transmission Port
                            </span>
                            <div class="font-mono text-xs font-semibold text-zinc-900 dark:text-white">
                                {{ $config->port ?? '587' }}
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="p-4 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800 relative group">
                            <span class="block text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">
                                Username / Auth
                            </span>
                            <div class="font-mono text-xs font-semibold text-zinc-900 dark:text-white truncate">
                                {{ $config->username ?? 'Not specified' }}
                            </div>
                            @if(!empty($config->username))
                                <button type="button" id="copyUserBtn" onclick="copyText('{{ $config->username }}', 'copyUserBtn')" 
                                        class="absolute top-3 right-3 text-zinc-400 hover:text-amber-500 text-3xs transition-colors" title="Copy User">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @endif
                        </div>

                        <!-- Encryption -->
                        <div class="p-4 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800">
                            <span class="block text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">
                                Encryption Protocol
                            </span>
                            <div class="text-xs font-semibold text-zinc-900 dark:text-white">
                                {{ !empty($config->encryption) ? strtoupper($config->encryption) : 'None / Plain' }}
                            </div>
                        </div>
                    </div>
                @elseif(strtoupper($account->type) === 'API' && is_array($config))
                    <div class="space-y-3">
                        @foreach($config as $item)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800">
                                <div>
                                    <span class="text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">Header / Key</span>
                                    <span class="text-xs font-semibold font-mono text-zinc-900 dark:text-white">{{ $item->key ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-3xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">Configured Value</span>
                                    <span class="text-xs font-mono text-zinc-600 dark:text-zinc-400">•••••••••••••••• (Hidden for security)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">No additional configuration parameters defined for this account.</p>
                @endif
            </div>

        </div>
    </div>

    <!-- SMTP Test Connection Modal -->
    <div id="testModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-zinc-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#141417] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 transform transition-all space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                <div class="flex items-center space-x-3 text-amber-600 dark:text-amber-400">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-base border border-amber-200/60 dark:border-amber-500/30">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Test Connection</h3>
                        <p class="text-3xs text-zinc-400">{{ $account->name }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeTestModal()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div>
                <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-200 uppercase tracking-wider mb-1.5" for="testRecipientEmail">
                    Send Verification Email To <span class="text-rose-500">*</span>
                </label>
                <input type="email" id="testRecipientEmail" value="{{ Auth::user()->email ?? '' }}" placeholder="e.g. your-email@gmail.com" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 dark:bg-[#09090B] dark:text-white text-xs font-medium focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 transition-all">
                <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-1">
                    An actual test message will be sent through this SMTP gateway to verify authentication and deliverability.
                </p>
            </div>

            <!-- Result Feedback Box -->
            <div id="testResultBox" class="hidden p-3.5 rounded-xl text-xs leading-snug"></div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeTestModal()" class="px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                    Cancel
                </button>
                <button type="button" id="runTestSubmitBtn" onclick="runSmtpTest()" class="px-4 py-2 text-xs font-bold text-zinc-950 bg-amber-500 hover:bg-amber-400 rounded-xl transition-all flex items-center gap-1.5 shadow-2xs">
                    <i class="fas fa-paper-plane text-3xs"></i>
                    <span>Send Test Message</span>
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
