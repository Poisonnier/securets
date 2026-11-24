@extends('layouts.app', ['activeNav' => 'settings'])

@section('title', 'Settings – SecureThreatShield')
@section('description', 'Review account preferences, security telemetry, and paired mobile devices.')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-5 border-b border-gray-700/50">
        <div>
            <span class="text-[11px] font-mono uppercase tracking-wider text-[#00BBFF]">Workspace Configuration</span>
            <h1 class="text-xl font-semibold text-white tracking-tight mt-1">Settings</h1>
            <p class="text-xs text-slate-400 mt-1">Review account preferences, security telemetry, and paired mobile devices.</p>
        </div>
    </div>

    <div id="settingsFlash" hidden class="mb-6 px-4 py-3 rounded-md border text-xs font-medium bg-[#1C2236] border-[#00BBFF]/40 text-[#00BBFF]"></div>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 rounded-md border text-xs font-medium bg-[#1C2236] border-[#00BBFF]/40 text-[#00BBFF]" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 rounded-md border text-xs font-medium bg-[#1C2236] border-gray-700/50 text-slate-200" role="alert">
            Please check the form for errors.
        </div>
    @endif

    <div class="space-y-6">
        {{-- Account Security Telemetry Card --}}
        <section class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
            <div class="mb-5">
                <h2 class="text-sm font-semibold text-white">Account Security &amp; Protection</h2>
                <p class="text-xs text-slate-400 mt-0.5">Live status of your authentication guards and linked devices.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-[#0E1326] border border-gray-700/50 rounded-md p-4">
                    <span class="block text-[11px] font-mono uppercase tracking-wider text-slate-400">Protection Status</span>
                    <div class="mt-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-mono text-[#00BBFF] bg-[#1C2236] border border-gray-700/50">
                            Active
                        </span>
                    </div>
                </div>
                <div class="bg-[#0E1326] border border-gray-700/50 rounded-md p-4">
                    <span class="block text-[11px] font-mono uppercase tracking-wider text-slate-400">Paired Mobile Devices</span>
                    <div id="deviceCountLabel" class="text-base font-mono font-semibold text-white mt-2">
                        {{ $deviceCount }} {{ Str::plural('device', $deviceCount) }}
                    </div>
                </div>
                <div class="bg-[#0E1326] border border-gray-700/50 rounded-md p-4">
                    <span class="block text-[11px] font-mono uppercase tracking-wider text-slate-400">Active Integrations</span>
                    <div class="text-base font-mono font-semibold text-white mt-2">
                        <a href="{{ route('integrations') }}" class="text-[#00BBFF] hover:underline">
                            {{ $integrationCount }} {{ Str::plural('app', $integrationCount) }} &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Paired Devices Management Section (PRD W-04) --}}
        <section class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
            <div class="mb-5">
                <h2 class="text-sm font-semibold text-white">Paired Devices</h2>
                <p class="text-xs text-slate-400 mt-0.5">Registered mobile authenticators receiving out-of-band push approval challenges.</p>
            </div>

            @php
                $pairedDevices = $devices ?? collect();
            @endphp

            @if ($pairedDevices->isEmpty())
                <div id="noDevicesEmpty" class="bg-[#0E1326] border border-gray-700/50 rounded-md p-6 text-center">
                    <p class="text-xs font-mono text-slate-400">No paired mobile devices registered yet.</p>
                    <p class="text-[11px] text-slate-500 mt-1">Sign in on the SecureTS mobile authenticator app to pair your phone.</p>
                </div>
            @else
                <div id="devicesList" class="divide-y divide-gray-700/50 border border-gray-700/50 rounded-md bg-[#0E1326]">
                    @foreach ($pairedDevices as $device)
                        <div id="device-row-{{ $device->id }}" class="flex items-center justify-between gap-4 px-4 py-3.5">
                            <div>
                                <div class="text-xs font-semibold text-white">{{ $device->device_name ?? 'Unknown Device' }}</div>
                                <div class="text-[11px] font-mono text-slate-400 mt-0.5">
                                    Last active: {{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}
                                </div>
                            </div>
                            <button type="button"
                                    data-device-id="{{ $device->id }}"
                                    data-device-name="{{ $device->device_name ?? 'Unknown Device' }}"
                                    onclick="revokeDevice(this)"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium text-slate-200 bg-[#1C2236] hover:border-[#00BBFF]/60 border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                                Revoke
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Preferences Form --}}
        <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <section class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
                <div class="mb-5">
                    <h2 class="text-sm font-semibold text-white">Notification Preferences</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Configure which security alerts and MFA approval prompts you receive.</p>
                </div>

                <div class="divide-y divide-gray-700/50">
                    <div class="flex items-center justify-between gap-4 pb-4">
                        <div>
                            <h3 class="text-xs font-semibold text-white">Email Security Alerts</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Send an immediate email alert when an unrecognized login attempt or gate access occurs.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0" aria-label="Toggle email security alerts">
                            <input type="checkbox" name="email_notifications" value="1" class="sr-only peer"
                                {{ old('email_notifications', $preferences['email_notifications'] ?? true) ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-[#0E1326] border border-gray-700/50 rounded-full peer peer-checked:bg-[#4063D7] peer-checked:border-[#4063D7] peer-focus:ring-2 peer-focus:ring-[#00BBFF] after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-4">
                        <div>
                            <h3 class="text-xs font-semibold text-white">Mobile Push Notifications</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Send instant biometric / one-tap approval prompts to your registered ThreatShield mobile devices.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0" aria-label="Toggle mobile push notifications">
                            <input type="checkbox" name="mobile_push" value="1" class="sr-only peer"
                                {{ old('mobile_push', $preferences['mobile_push'] ?? true) ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-[#0E1326] border border-gray-700/50 rounded-full peer peer-checked:bg-[#4063D7] peer-checked:border-[#4063D7] peer-focus:ring-2 peer-focus:ring-[#00BBFF] after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                </div>
            </section>

            <div>
                <button type="submit"
                    class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-4 py-2.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                    Save Preferences
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
const SETTINGS_CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

async function revokeDevice(btn) {
    const id = btn.dataset.deviceId;
    const name = btn.dataset.deviceName || 'this device';
    if (!window.confirm(`Revoke "${name}"? It will no longer receive push approval prompts.`)) return;

    btn.disabled = true;
    btn.textContent = 'Revoking…';

    try {
        const res = await fetch(`/web/devices/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': SETTINGS_CSRF,
                'Accept': 'application/json',
            },
        });
        if (!res.ok) throw new Error();

        document.getElementById('device-row-' + id)?.remove();
        const flash = document.getElementById('settingsFlash');
        if (flash) {
            flash.hidden = false;
            flash.textContent = 'Paired device revoked.';
        }
    } catch (_) {
        btn.disabled = false;
        btn.textContent = 'Revoke';
    }
}
</script>
@endpush
