@extends('layouts.app', ['activeNav' => 'apps'])

@section('title', 'Integration Hub – SecureThreatShield')
@section('description', 'Manage connected applications and TOTP verification gates.')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-5 border-b border-gray-700/50">
        <div>
            <span class="text-[11px] font-mono uppercase tracking-wider text-[#00BBFF]">Application Gates</span>
            <h1 class="text-xl font-semibold text-white tracking-tight mt-1">Integration Hub</h1>
            <p class="text-xs text-slate-400 mt-1">Connect external applications, manage TOTP generation status, and monitor session telemetry.</p>
        </div>

        <button type="button" onclick="openAddModal()"
            class="inline-flex items-center gap-2 bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-4 py-2.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer shrink-0">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add Application
        </button>
    </div>

    {{-- Flash Notification Banner --}}
    <div id="flash" hidden class="mb-6 px-4 py-3 rounded-md border text-xs font-medium"></div>

    {{-- Minimalist Tabs --}}
    <div class="flex items-center gap-2 mb-6 border-b border-gray-700/50 pb-3">
        <button type="button" id="tab-btn-apps" onclick="showTab('apps', this)"
            class="tab-btn px-3 py-1.5 rounded-md text-xs font-medium transition-colors bg-[#4063D7]/20 text-[#00BBFF] border border-[#4063D7]/40 cursor-pointer">
            Connected Apps
        </button>
        <button type="button" id="tab-btn-history" onclick="showTab('history', this)"
            class="tab-btn px-3 py-1.5 rounded-md text-xs font-medium transition-colors text-slate-400 hover:text-slate-100 hover:bg-[#1C2236] border border-transparent cursor-pointer">
            Login History
        </button>
        <button type="button" id="tab-btn-extension" onclick="showTab('extension', this)"
            class="tab-btn px-3 py-1.5 rounded-md text-xs font-medium transition-colors text-slate-400 hover:text-slate-100 hover:bg-[#1C2236] border border-transparent cursor-pointer">
            Browser Extension
        </button>
    </div>

    {{-- TAB: Connected Apps --}}
    <div id="tab-apps" class="tab-panel">
        @if($integrations->isEmpty())
            <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-12 text-center">
                <div class="w-10 h-10 rounded-md bg-[#0E1326] border border-gray-700/50 flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-white">No apps integrated yet</h2>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Click <span class="text-slate-200 font-medium">+ Add Application</span> to register your first application gate and begin generating TOTP tokens.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($integrations as $app)
                    <div id="card-{{ $app->id }}"
                         data-app-id="{{ $app->id }}"
                         data-app-name="{{ $app->name }}"
                         data-app-url="{{ $app->url }}"
                         class="app-card bg-[#1C2236] border border-gray-700/50 rounded-lg p-5 flex flex-col justify-between gap-4">

                        {{-- Card Top: App Identity & Clean TOTP Toggle --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-md bg-[#0E1326] border border-gray-700/50 flex items-center justify-center shrink-0 overflow-hidden">
                                    <img src="{{ $app->favicon }}" alt="{{ $app->name }}" class="w-6 h-6 object-contain" onerror="this.src='/images/new-securets-logo-dark.png'">
                                </div>
                                <div class="min-w-0">
                                    <h3 id="app-name-{{ $app->id }}" class="text-sm font-semibold text-white truncate">{{ $app->name }}</h3>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span id="status-dot-{{ $app->id }}" class="w-1.5 h-1.5 rounded-full {{ $app->is_active ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                                        <span id="status-text-{{ $app->id }}" class="text-[11px] font-mono {{ $app->is_active ? 'text-emerald-400' : 'text-amber-400' }}">
                                            {{ $app->is_active ? 'TOTP Active' : 'TOTP Halted' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- TOTP Generation Toggle Switch --}}
                            <div class="flex flex-col items-end shrink-0">
                                <label class="relative inline-flex items-center cursor-pointer" title="Toggle TOTP generation">
                                    <input type="checkbox"
                                           class="sr-only peer"
                                           {{ $app->is_active ? 'checked' : '' }}
                                           data-app-id="{{ $app->id }}"
                                           onchange="toggleApp(this)">
                                    <div class="w-9 h-5 bg-[#0E1326] border border-gray-700/50 rounded-full peer peer-checked:bg-[#4063D7] peer-checked:border-[#4063D7] peer-focus:ring-2 peer-focus:ring-[#00BBFF] after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:after:translate-x-4"></div>
                                </label>
                                <span class="text-[10px] font-mono text-slate-400 mt-1">TOTP Switch</span>
                            </div>
                        </div>

                        {{-- TOTP Code & Halted State Box --}}
                        <div id="totp-wrap-{{ $app->id }}" class="bg-[#0E1326] border border-gray-700/50 rounded-md p-3.5">
                            {{-- Active TOTP Display --}}
                            <div id="totp-active-{{ $app->id }}" class="{{ $app->is_active ? '' : 'hidden' }}" data-totp-app="{{ $app->id }}">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-400">Current TOTP Token</span>
                                        <div id="code-{{ $app->id }}" class="text-xl font-mono font-semibold tracking-widest text-[#00BBFF] mt-0.5">──────</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-500">Expires</span>
                                        <span id="timer-{{ $app->id }}" class="text-xs font-mono text-slate-300">30s</span>
                                    </div>
                                </div>
                                <div class="w-full h-1 bg-[#1C2236] rounded-full overflow-hidden mt-2.5">
                                    <div id="bar-{{ $app->id }}" class="h-full bg-[#4063D7] transition-all duration-1000 ease-linear" style="width: 100%"></div>
                                </div>
                            </div>

                            {{-- Halted TOTP Display --}}
                            <div id="totp-halted-{{ $app->id }}" class="{{ $app->is_active ? 'hidden' : '' }} flex items-center justify-between py-1">
                                <div>
                                    <span class="block text-[10px] font-mono uppercase tracking-wider text-amber-400">TOTP Generation Halted</span>
                                    <p class="text-xs text-slate-400 mt-0.5">Verification tokens and login gate challenges are paused while switched off.</p>
                                </div>
                                <span class="text-xs font-mono font-semibold text-slate-500 shrink-0 ml-3">PAUSED</span>
                            </div>
                        </div>

                        {{-- Card Actions --}}
                        <div class="flex items-center gap-2 pt-1 border-t border-gray-700/50">
                            <button type="button"
                                    data-copy-value="{{ $app->gateUrl() }}"
                                    data-copy-msg="Login gate link copied to clipboard."
                                    onclick="copyFromButton(this)"
                                    class="flex-1 bg-[#0E1326] hover:border-[#00BBFF]/60 text-slate-200 text-xs font-medium py-2 px-3 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                                Copy Gate Link
                            </button>

                            <button type="button"
                                    data-app-id="{{ $app->id }}"
                                    onclick="openEditModalFromCard(this)"
                                    class="flex-1 bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium py-2 px-3 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                                Edit Details
                            </button>

                            <button type="button"
                                    data-app-id="{{ $app->id }}"
                                    onclick="removeApp(this)"
                                    class="px-3 py-2 rounded-md text-xs font-medium text-red-300 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 transition-colors focus:outline-none focus:ring-2 focus:ring-red-400 cursor-pointer">
                                Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- TAB: Login History --}}
    <div id="tab-history" class="tab-panel hidden">
        <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700/50 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-white">Login Verification History</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Read-only log of resolved authentication events across your applications.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-700/50 bg-[#0E1326]/50 text-[11px] font-mono uppercase tracking-wider text-slate-400">
                            <th class="py-2.5 px-5 font-medium">App</th>
                            <th class="py-2.5 px-5 font-medium">Device / IP</th>
                            <th class="py-2.5 px-5 font-medium">Status</th>
                            <th class="py-2.5 px-5 font-medium text-right">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody" class="divide-y divide-gray-700/50 text-xs">
                        <tr>
                            <td colspan="4" class="py-10 px-5 text-center font-mono text-slate-400">Loading verification events...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TAB: Browser Extension --}}
    <div id="tab-extension" class="tab-panel hidden">
        <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
            <span class="text-[11px] font-mono uppercase tracking-wider text-[#00BBFF]">Browser Extension</span>
            <h2 class="text-sm font-semibold text-white mt-1">Gate direct visits in your browser</h2>
            <p class="text-xs text-slate-400 mt-1 max-w-2xl">
                Load the SecureTS extension in Chrome or Edge and pair it with the token below. Every direct visit to one of
                your protected URLs is then routed through its SecureTS gate instead of loading the app straight away.
            </p>

            <div class="mt-5">
                <label for="extensionToken" class="block text-xs font-medium text-slate-300 mb-1.5">Extension pairing token</label>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <input type="text" id="extensionToken" readonly value="Loading…"
                        class="flex-1 bg-[#0E1326] border border-gray-700/50 rounded-md px-3 py-2 text-xs font-mono text-slate-200 focus:outline-none focus:border-[#4063D7]">
                    <button type="button" onclick="copyExtensionToken()"
                        class="bg-[#0E1326] hover:border-[#00BBFF]/60 text-slate-200 text-xs font-medium py-2 px-3 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                        Copy Token
                    </button>
                    <button type="button" onclick="rotateExtensionToken()"
                        class="bg-[#0E1326] hover:border-red-400/60 text-red-300 text-xs font-medium py-2 px-3 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-red-400 cursor-pointer">
                        Regenerate
                    </button>
                </div>
                <p class="text-[11px] text-slate-500 mt-2">
                    This token only lets the extension read your protected URLs and gate links. Regenerating it signs out every
                    browser that is already paired.
                </p>
            </div>

            <div class="mt-6 pt-5 border-t border-gray-700/50">
                <span class="block text-xs font-medium text-slate-300 mb-2">Install the extension</span>
                <ol class="text-xs text-slate-400 space-y-1.5 list-decimal list-inside">
                    <li>Open <span class="font-mono text-slate-200">chrome://extensions</span> and turn on <span class="text-slate-200">Developer mode</span>.</li>
                    <li>Choose <span class="text-slate-200">Load unpacked</span> and select the <span class="font-mono text-slate-200">web/extension</span> folder in the repository.</li>
                    <li>Open the extension options, paste your SecureTS base URL and the token above, then save.</li>
                    <li>Restart the Laravel server after the migration, then verify the token shows in the box above.</li>
                </ol>
                <p class="text-[11px] text-slate-500 mt-3">
                    First gate visit through the extension is auto-approved. Later visits wait for Approve or Decline on your phone.
                </p>
            </div>
        </div>
    </div>

    {{-- MODAL: Add New Application --}}
    <div id="addModal" class="hidden fixed inset-0 z-50 bg-[#0E1326]/80 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-[#00BBFF]">New Application</span>
                    <h2 class="text-base font-semibold text-white mt-0.5">Connect Application</h2>
                </div>
                <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-white text-sm font-mono px-2 py-1 cursor-pointer" aria-label="Close modal">✕</button>
            </div>

            <p class="text-xs text-slate-400 mb-4">Register an external application’s login URL to provision a dedicated verification gate and TOTP generator.</p>

            <div id="addError" class="hidden mb-4 px-3 py-2 rounded-md bg-red-500/10 border border-red-500/30 text-red-300 text-xs"></div>

            <div class="space-y-4">
                <div>
                    <label for="appName" class="block text-xs font-medium text-slate-300 mb-1.5">Application Name</label>
                    <input type="text" id="appName" placeholder="e.g. Google Workspace"
                        class="w-full bg-[#0E1326] border border-gray-700/50 rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#4063D7] focus:ring-1 focus:ring-[#4063D7] transition">
                </div>
                <div>
                    <label for="appUrl" class="block text-xs font-medium text-slate-300 mb-1.5">Application Login URL</label>
                    <input type="url" id="appUrl" placeholder="https://accounts.google.com"
                        class="w-full bg-[#0E1326] border border-gray-700/50 rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#4063D7] focus:ring-1 focus:ring-[#4063D7] transition font-mono">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-gray-700/50">
                <button type="button" onclick="closeAddModal()"
                    class="px-4 py-2 rounded-md text-xs font-medium text-slate-300 bg-[#0E1326] hover:text-white border border-gray-700/50 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="button" id="addSubmitBtn" onclick="addApp()"
                    class="px-4 py-2 rounded-md text-xs font-medium text-white bg-[#4063D7] hover:bg-[#4063D7]/90 border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                    Add Application
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL: Edit Application Details (Name Only, URL Strictly Read-Only) --}}
    <div id="editModal" class="hidden fixed inset-0 z-50 bg-[#0E1326]/80 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-[#00BBFF]">Configuration</span>
                    <h2 class="text-base font-semibold text-white mt-0.5">Edit Application Details</h2>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white text-sm font-mono px-2 py-1 cursor-pointer" aria-label="Close modal">✕</button>
            </div>

            <p class="text-xs text-slate-400 mb-4">Update the display name for this application. The target URL is bound to the gate token and is read-only.</p>

            <div id="editError" class="hidden mb-4 px-3 py-2 rounded-md bg-red-500/10 border border-red-500/30 text-red-300 text-xs"></div>

            <input type="hidden" id="editAppId">

            <div class="space-y-4">
                <div>
                    <label for="editAppName" class="block text-xs font-medium text-slate-300 mb-1.5">Application Name</label>
                    <input type="text" id="editAppName" maxlength="100"
                        class="w-full bg-[#0E1326] border border-gray-700/50 rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#4063D7] focus:ring-1 focus:ring-[#4063D7] transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="editAppUrl" class="block text-xs font-medium text-slate-400">Target URL</label>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-slate-500">Read-Only</span>
                    </div>
                    <input type="url" id="editAppUrl" readonly disabled
                        class="w-full bg-[#0E1326]/60 border border-gray-700/40 rounded-md px-3 py-2 text-xs font-mono text-slate-500 cursor-not-allowed select-none">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-gray-700/50">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 rounded-md text-xs font-medium text-slate-300 bg-[#0E1326] hover:text-white border border-gray-700/50 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="button" id="editSubmitBtn" onclick="saveAppDetails()"
                    class="px-4 py-2 rounded-md text-xs font-medium text-white bg-[#4063D7] hover:bg-[#4063D7]/90 border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL: Remove Application Confirmation --}}
    <div id="removeModal" class="hidden fixed inset-0 z-50 bg-[#0E1326]/80 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-red-400">Destructive Action</span>
                    <h2 class="text-base font-semibold text-white mt-0.5">Remove Application</h2>
                </div>
                <button type="button" onclick="closeRemoveModal()" class="text-slate-400 hover:text-white text-sm font-mono px-2 py-1 cursor-pointer" aria-label="Close modal">✕</button>
            </div>

            <input type="hidden" id="removeAppId">

            <p class="text-xs text-slate-300 leading-relaxed mb-4">
                Are you sure you want to remove <strong id="removeAppName" class="text-white font-semibold">this application</strong>? This will permanently revoke its verification gate, halt TOTP generation, and delete associated login history.
            </p>

            <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-gray-700/50">
                <button type="button" onclick="closeRemoveModal()"
                    class="px-4 py-2 rounded-md text-xs font-medium text-slate-300 bg-[#0E1326] hover:text-white border border-gray-700/50 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="button" id="confirmRemoveBtn" onclick="confirmRemoveApp()"
                    class="px-4 py-2 rounded-md text-xs font-medium text-red-200 bg-red-500/20 hover:bg-red-500/30 border border-red-500/40 transition-colors focus:outline-none focus:ring-2 focus:ring-red-400 cursor-pointer">
                    Confirm Remove
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL: Plan Limit Upgrade Overlay --}}
    <div id="upgradeOverlay" class="hidden fixed inset-0 z-50 bg-[#0E1326]/80 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#1C2236] border border-gray-700/50 rounded-lg p-6 text-center">
            <div class="flex justify-end">
                <button type="button" onclick="closeUpgradeOverlay()" class="text-slate-400 hover:text-white text-sm font-mono px-2 py-1 cursor-pointer" aria-label="Close">&times;</button>
            </div>
            <span id="upgradePlanBadge" class="inline-block px-2.5 py-1 rounded text-[11px] font-mono uppercase tracking-wider bg-[#4063D7]/20 text-[#00BBFF] border border-[#4063D7]/40 mb-3">
                Free Plan
            </span>
            <h2 class="text-base font-semibold text-white mb-2">Application Limit Reached</h2>
            <p id="upgradeMsg" class="text-xs text-slate-400 leading-relaxed mb-6">
                Your current plan allows up to <strong>3</strong> connected applications. Upgrade your workspace plan to connect additional application gates.
            </p>
            <a href="{{ route('subscription') }}"
               class="inline-flex items-center justify-center w-full bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium py-2.5 px-4 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">
                View Subscription Plans →
            </a>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const flashEl = document.getElementById('flash');

function setFlash(type, message) {
    flashEl.hidden = false;
    const styles = {
        success: 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300',
        error:   'bg-red-500/10 border-red-500/30 text-red-300',
        loading: 'bg-[#4063D7]/15 border-[#4063D7]/40 text-[#00BBFF]',
    };
    flashEl.className = `mb-6 px-4 py-3 rounded-md border text-xs font-medium ${styles[type] || styles.loading}`;
    flashEl.textContent = message;
}

function showTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.className = 'tab-btn px-3 py-1.5 rounded-md text-xs font-medium transition-colors text-slate-400 hover:text-slate-100 hover:bg-[#1C2236] border border-transparent cursor-pointer';
    });
    document.getElementById('tab-' + name).classList.remove('hidden');
    btn.className = 'tab-btn px-3 py-1.5 rounded-md text-xs font-medium transition-colors bg-[#4063D7]/20 text-[#00BBFF] border border-[#4063D7]/40 cursor-pointer';
    if (name === 'history') loadHistory();
    if (name === 'extension') loadExtensionToken();
}

// ── Add Application Modal ────────────────────────────────────────────────────
function openAddModal() {
    const errEl = document.getElementById('addError');
    errEl.textContent = '';
    errEl.classList.add('hidden');
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

async function addApp() {
    const name = document.getElementById('appName').value.trim();
    const url  = document.getElementById('appUrl').value.trim();
    const errEl = document.getElementById('addError');
    errEl.textContent = '';
    errEl.classList.add('hidden');

    if (!name || !url) {
        errEl.textContent = 'Please enter both the application name and login URL.';
        errEl.classList.remove('hidden');
        return;
    }

    const btn = document.getElementById('addSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Adding…';

    try {
        const res = await fetch('/web/integrations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ name, url }),
        });
        const body = await res.json().catch(() => ({}));
        if (res.ok) {
            sessionStorage.setItem('hubFlash', JSON.stringify({
                type: 'success',
                message: 'Application connected successfully.',
            }));
            window.location.reload();
            return;
        }
        if (res.status === 403 && body.upgrade_required) {
            closeAddModal();
            const planLabel = (body.plan ?? 'free').replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
            document.getElementById('upgradePlanBadge').textContent = planLabel + ' Plan';
            document.getElementById('upgradeMsg').innerHTML =
                `Your <strong>${planLabel}</strong> plan allows up to <strong>${body.limit ?? 3}</strong> application${(body.limit ?? 3) !== 1 ? 's' : ''}. Upgrade to connect more applications.`;
            document.getElementById('upgradeOverlay').classList.remove('hidden');
            return;
        }
        const firstError = body.errors ? Object.values(body.errors)[0][0] : null;
        errEl.textContent = firstError || body.message || 'Could not add this application.';
        errEl.classList.remove('hidden');
    } catch (_) {
        errEl.textContent = 'Network error – please check your connection.';
        errEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Add Application';
    }
}

// ── Edit Details Modal (Name Only, URL Read-Only) ────────────────────────────
function openEditModalFromCard(button) {
    const id = button.dataset.appId;
    const card = document.getElementById('card-' + id);
    if (!card) return;

    document.getElementById('editAppId').value = id;
    document.getElementById('editAppName').value = card.dataset.appName || '';
    document.getElementById('editAppUrl').value = card.dataset.appUrl || '';

    const errEl = document.getElementById('editError');
    errEl.textContent = '';
    errEl.classList.add('hidden');

    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editAppName').focus();
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

async function saveAppDetails() {
    const id = document.getElementById('editAppId').value;
    const name = document.getElementById('editAppName').value.trim();
    const errEl = document.getElementById('editError');
    errEl.textContent = '';
    errEl.classList.add('hidden');

    if (!name) {
        errEl.textContent = 'Application name is required.';
        errEl.classList.remove('hidden');
        return;
    }

    const btn = document.getElementById('editSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Saving…';

    try {
        const res = await fetch(`/web/integrations/${id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ name }),
        });
        const body = await res.json().catch(() => ({}));
        if (!res.ok) {
            const firstError = body.errors ? Object.values(body.errors)[0][0] : null;
            errEl.textContent = firstError || body.message || 'Could not update application name.';
            errEl.classList.remove('hidden');
            return;
        }

        const card = document.getElementById('card-' + id);
        if (card) {
            card.dataset.appName = body.name || name;
        }
        const nameEl = document.getElementById('app-name-' + id);
        if (nameEl) {
            nameEl.textContent = body.name || name;
        }

        closeEditModal();
        setFlash('success', 'Application details updated.');
    } catch (_) {
        errEl.textContent = 'Network error – could not save changes.';
        errEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Changes';
    }
}

// ── Remove Confirmation Modal ────────────────────────────────────────────────
function removeApp(button) {
    openRemoveModalFromCard(button);
}

function openRemoveModalFromCard(button) {
    const id = button.dataset.appId;
    const card = document.getElementById('card-' + id);
    if (!card) return;

    document.getElementById('removeAppId').value = id;
    document.getElementById('removeAppName').textContent = card.dataset.appName || 'this application';
    document.getElementById('removeModal').classList.remove('hidden');
}

function closeRemoveModal() {
    document.getElementById('removeModal').classList.add('hidden');
}

async function confirmRemoveApp() {
    const id = document.getElementById('removeAppId').value;
    if (!id) return;

    const btn = document.getElementById('confirmRemoveBtn');
    btn.disabled = true;
    btn.textContent = 'Removing…';

    try {
        const res = await fetch(`/web/integrations/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error();

        closeRemoveModal();
        document.getElementById('card-' + id)?.remove();
        setFlash('success', 'Application removed from your workspace.');
        if (!document.querySelector('.app-card')) window.location.reload();
    } catch (_) {
        closeRemoveModal();
        setFlash('error', 'Could not remove this application.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Confirm Remove';
    }
}

// ── TOTP Generation Toggle ───────────────────────────────────────────────────
function syncCardActiveState(appId, isActive) {
    const dot = document.getElementById('status-dot-' + appId);
    const text = document.getElementById('status-text-' + appId);
    const activePanel = document.getElementById('totp-active-' + appId);
    const haltedPanel = document.getElementById('totp-halted-' + appId);

    if (dot) {
        dot.className = `w-1.5 h-1.5 rounded-full ${isActive ? 'bg-emerald-400' : 'bg-amber-400'}`;
    }
    if (text) {
        text.className = `text-[11px] font-mono ${isActive ? 'text-emerald-400' : 'text-amber-400'}`;
        text.textContent = isActive ? 'TOTP Active' : 'TOTP Halted';
    }
    if (activePanel && haltedPanel) {
        if (isActive) {
            activePanel.classList.remove('hidden');
            haltedPanel.classList.add('hidden');
            loadTotpForApp(appId);
        } else {
            activePanel.classList.add('hidden');
            haltedPanel.classList.remove('hidden');
        }
    }
}

async function toggleApp(checkbox) {
    const id = checkbox.dataset.appId;
    const isNowActive = checkbox.checked;
    syncCardActiveState(id, isNowActive);

    try {
        const res = await fetch(`/web/integrations/${id}/toggle`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error();
        setFlash('success', isNowActive ? 'TOTP generation enabled for this application.' : 'TOTP generation halted for this application.');
    } catch (_) {
        checkbox.checked = !isNowActive;
        syncCardActiveState(id, !isNowActive);
        setFlash('error', 'Could not update TOTP status.');
    }
}

function copyFromButton(button) {
    const value = button.dataset.copyValue ?? '';
    const msg = button.dataset.copyMsg ?? 'Copied!';
    navigator.clipboard.writeText(value)
        .then(() => setFlash('success', msg))
        .catch(() => setFlash('error', 'Could not copy to clipboard.'));
}

// ── Browser Extension Pairing ────────────────────────────────────────────────
let extensionTokenCache = null;

async function loadExtensionToken() {
    const input = document.getElementById('extensionToken');
    if (!input) return;

    try {
        const res = await fetch('/web/extension-token', { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error();
        const data = await res.json();
        extensionTokenCache = data.token ?? null;
        input.value = data.token ?? 'Unavailable';
    } catch (_) {
        input.value = 'Unavailable';
        setFlash('error', 'Could not load the extension pairing token.');
    }
}

function copyExtensionToken() {
    if (!extensionTokenCache) {
        setFlash('error', 'Pairing token is not loaded yet.');
        return;
    }
    navigator.clipboard.writeText(extensionTokenCache)
        .then(() => setFlash('success', 'Extension pairing token copied.'))
        .catch(() => setFlash('error', 'Could not copy to clipboard.'));
}

async function rotateExtensionToken() {
    try {
        const res = await fetch('/web/extension-token/rotate', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error();
        const data = await res.json();
        extensionTokenCache = data.token ?? null;
        const input = document.getElementById('extensionToken');
        if (input) input.value = data.token ?? 'Unavailable';
        setFlash('success', 'New pairing token generated. Paired browsers must be updated.');
    } catch (_) {
        setFlash('error', 'Could not regenerate the pairing token.');
    }
}

function escapeHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ── TOTP Polling (Only for Active Apps) ──────────────────────────────────────
async function loadTotpForApp(appId) {
    const activePanel = document.getElementById('totp-active-' + appId);
    if (!activePanel || activePanel.classList.contains('hidden')) return;

    try {
        const res = await fetch(`/web/integrations/${appId}/totp`, {
            headers: { 'Accept': 'application/json' },
        });
        const data = await res.json();
        const codeEl  = document.getElementById('code-' + appId);
        const timerEl = document.getElementById('timer-' + appId);
        const barEl   = document.getElementById('bar-' + appId);
        if (codeEl) {
            codeEl.textContent = data.code ?? '──────';
            const pct = ((data.expires_in ?? 30) / 30) * 100;
            if (barEl) barEl.style.width = pct + '%';
            if (timerEl) timerEl.textContent = (data.expires_in ?? 30) + 's';
        }
    } catch (_) {}
}

function startTotpPolling() {
    const run = () => {
        document.querySelectorAll('[data-totp-app]').forEach(el => {
            if (!el.classList.contains('hidden')) {
                loadTotpForApp(el.dataset.totpApp);
            }
        });
    };
    run();
    setInterval(run, 1000);
}

// ── Login History ────────────────────────────────────────────────────────────
async function loadHistory() {
    const tbody = document.getElementById('historyBody');
    if (!tbody) return;

    try {
        const res = await fetch('/web/login-events', {
            headers: { 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error();
        const data = await res.json();

        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="py-10 px-5 text-center font-mono text-slate-400">No login verification history recorded yet.</td></tr>';
            return;
        }

        const badgeStyles = {
            approved: 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30',
            declined: 'bg-red-500/10 text-red-300 border-red-500/30',
            pending:  'bg-amber-500/10 text-amber-300 border-amber-500/30',
            auto:     'bg-slate-500/10 text-slate-300 border-slate-500/30',
            expired:  'bg-slate-500/10 text-slate-400 border-slate-500/30',
        };

        tbody.innerHTML = data.map(e => {
            const id = Number(e.id) || 0;
            const status = String(e.status || 'auto').toLowerCase();
            const badge = badgeStyles[status] || badgeStyles.auto;
            const appName = escapeHtml(e.integration?.name ?? '–');
            const device = escapeHtml(e.device ?? '');
            const ip = e.ip_address ? ' · ' + escapeHtml(e.ip_address) : '';
            const createdAt = escapeHtml(new Date(e.created_at).toLocaleString());

            return `
            <tr id="event-${id}" class="hover:bg-[#0E1326]/40 transition-colors">
                <td class="py-3 px-5 font-medium text-white">${appName}</td>
                <td class="py-3 px-5 font-mono text-slate-400">${device}${ip}</td>
                <td class="py-3 px-5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono border ${badge}">${escapeHtml(status)}</span>
                </td>
                <td class="py-3 px-5 font-mono text-slate-400 text-right">${createdAt}</td>
            </tr>`;
        }).join('');
    } catch (_) {
        tbody.innerHTML = '<tr><td colspan="4" class="py-10 px-5 text-center font-mono text-red-400">Could not load login history.</td></tr>';
    }
}

function closeUpgradeOverlay() {
    document.getElementById('upgradeOverlay').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const saved = sessionStorage.getItem('hubFlash');
    if (saved) {
        sessionStorage.removeItem('hubFlash');
        try {
            const flash = JSON.parse(saved);
            setFlash(flash.type, flash.message);
        } catch (_) {}
    }

    startTotpPolling();

    ['addModal', 'editModal', 'removeModal', 'upgradeOverlay'].forEach(modalId => {
        const el = document.getElementById(modalId);
        if (el) {
            el.addEventListener('click', function (e) {
                if (e.target === this) this.classList.add('hidden');
            });
        }
    });
});
</script>
@endpush
