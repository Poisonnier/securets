@extends('layouts.app', ['activeNav' => 'dashboard'])

@section('title', 'Dashboard – SecureThreatShield')
@section('description', 'SecureThreatShield Dashboard – threat protection overview.')

@section('content')
    @php
        $connectedAppsCount = $connectedAppsCount ?? Auth::user()?->integrations()->count() ?? 0;
        $recentLoginRequests = $recentLoginRequests ?? Auth::user()?->loginEvents()->with('integration')->latest()->limit(5)->get() ?? collect();
    @endphp

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-5 border-b border-gray-700/50">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-mono uppercase tracking-wider text-[#00BBFF]">Workspace Overview</span>
            </div>
            <h1 class="text-xl font-semibold text-white tracking-tight mt-1">Developer Dashboard</h1>
            <p class="text-xs text-slate-400 mt-1">Real-time authentication telemetry, connected application gates, and verification events.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-[#1C2236] border border-gray-700/50 text-xs font-mono text-slate-300">
                <span class="w-2 h-2 rounded-full {{ ($protectionStatus ?? 'Standby') === 'Active' ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                Status: {{ $protectionStatus ?? 'Standby' }}
            </span>
        </div>
    </div>

    {{-- Minimalist Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-400">Connected Apps</span>
                <span class="text-[11px] font-mono text-[#00BBFF]">GATE</span>
            </div>
            <div class="text-2xl font-mono font-semibold text-white">{{ $connectedAppsCount }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Active protection status: <span class="text-slate-200 font-mono">{{ $protectionStatus }}</span></p>
        </div>

        <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-400">Pending Approvals</span>
                <span class="text-[11px] font-mono text-amber-400">QUEUE</span>
            </div>
            <div class="text-2xl font-mono font-semibold text-white">{{ $pendingAlertsCount }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Awaiting mobile push response</p>
        </div>

        <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-400">Threats Blocked</span>
                <span class="text-[11px] font-mono text-emerald-400">DEFENSE</span>
            </div>
            <div class="text-2xl font-mono font-semibold text-white">{{ $threatsBlocked }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Declined out-of-band challenges</p>
        </div>

        <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-400">Total Login Events</span>
                <span class="text-[11px] font-mono text-slate-400">&lt;500ms</span>
            </div>
            <div class="text-2xl font-mono font-semibold text-white">{{ $alertsCount }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Recorded verification requests</p>
        </div>
    </div>

    {{-- System Operational Banner Card --}}
    <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-5 mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3.5">
            <div class="w-9 h-9 rounded-md bg-[#0E1326] border border-gray-700/50 flex items-center justify-center shrink-0">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="{{ $pendingAlertsCount > 0 ? '#FACC15' : '#00BBFF' }}"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
            </div>
            <div>
                @if ($pendingAlertsCount > 0)
                    <h2 class="text-sm font-semibold text-amber-300">
                        {{ $pendingAlertsCount }} Pending Approval{{ $pendingAlertsCount === 1 ? '' : 's' }}
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Review waiting login attempt{{ $pendingAlertsCount === 1 ? '' : 's' }} on your SecureTS mobile authenticator.
                    </p>
                @else
                    <h2 class="text-sm font-semibold text-white">All Systems Operational</h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        ThreatShield is actively monitoring your account. No pending threats detected.
                    </p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <span class="text-[11px] font-mono text-slate-400 bg-[#0E1326] border border-gray-700/50 px-2.5 py-1 rounded-md">
                Auth Latency: <span class="text-[#00BBFF]">&lt;500ms</span>
            </span>
        </div>
    </div>

    {{-- Recent Login Requests Table Card --}}
    <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-700/50 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-white">Recent Login Requests</h2>
                <p class="text-xs text-slate-400 mt-0.5">Latest authentication challenges and session verification logs.</p>
            </div>
            <span class="text-[11px] font-mono text-slate-400">
                Showing {{ $recentLoginRequests->count() }} event{{ $recentLoginRequests->count() === 1 ? '' : 's' }}
            </span>
        </div>

        @if ($recentLoginRequests->isEmpty())
            <div class="px-5 py-10 text-center">
                <p class="text-xs font-mono text-slate-400">No login verification requests recorded yet.</p>
                <p class="text-[11px] text-slate-500 mt-1">Events will stream here as users authenticate through your application gates.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-700/50 bg-[#0E1326]/50 text-[11px] font-mono uppercase tracking-wider text-slate-400">
                            <th class="py-2.5 px-5 font-medium">Application</th>
                            <th class="py-2.5 px-5 font-medium">Identifier</th>
                            <th class="py-2.5 px-5 font-medium">IP / Origin</th>
                            <th class="py-2.5 px-5 font-medium">Status</th>
                            <th class="py-2.5 px-5 font-medium text-right">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50 text-xs">
                        @foreach ($recentLoginRequests as $event)
                            <tr class="hover:bg-[#0E1326]/40 transition-colors">
                                <td class="py-3 px-5 font-medium text-white">
                                    {{ $event->integration?->app_name ?? $event->app_name ?? 'External Application' }}
                                </td>
                                <td class="py-3 px-5 font-mono text-slate-300">
                                    {{ $event->username ?? $event->email ?? '—' }}
                                </td>
                                <td class="py-3 px-5 font-mono text-slate-400">
                                    {{ $event->ip_address ?? '—' }}
                                </td>
                                <td class="py-3 px-5">
                                    @php
                                        $status = strtolower($event->status ?? 'pending');
                                        $badgeClasses = match ($status) {
                                            'approved' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30',
                                            'declined' => 'bg-red-500/10 text-red-300 border-red-500/30',
                                            default    => 'bg-amber-500/10 text-amber-300 border-amber-500/30',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono border {{ $badgeClasses }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="py-3 px-5 font-mono text-slate-400 text-right">
                                    {{ $event->created_at?->diffForHumans() ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
