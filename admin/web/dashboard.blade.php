<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureTS — Admin Console</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-text font-sans antialiased selection:bg-primary selection:text-text min-h-screen flex">

    <!-- 1. Sidebar Navigation -->
    <aside class="w-64 bg-surface border-r border-border flex flex-col justify-between shrink-0">
        <div class="p-6 flex flex-col gap-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center font-semibold text-sm tracking-tight">
                        TS
                    </div>
                    <span class="font-semibold text-base tracking-tight">SecureTS</span>
                </div>
                <span class="text-xs font-medium text-text-muted px-2 py-0.5 rounded-lg border border-border">
                    Admin
                </span>
            </div>

            <nav aria-label="Main Navigation" class="flex flex-col gap-1">
                <a href="#dashboard" class="flex items-center gap-3 px-3 py-2.5 min-h-11 rounded-lg bg-primary text-text font-medium text-sm transition-colors duration-150 ease-out">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="#users" class="flex items-center gap-3 px-3 py-2.5 min-h-11 rounded-lg text-text-muted hover:text-text hover:bg-background font-medium text-sm transition-colors duration-150 ease-out">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    <span>User Management</span>
                </a>

                <a href="#sessions" class="flex items-center gap-3 px-3 py-2.5 min-h-11 rounded-lg text-text-muted hover:text-text hover:bg-background font-medium text-sm transition-colors duration-150 ease-out">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                    </svg>
                    <span>Active Sessions</span>
                </a>

                <a href="#recovery" class="flex items-center justify-between px-3 py-2.5 min-h-11 rounded-lg text-text-muted hover:text-text hover:bg-background font-medium text-sm transition-colors duration-150 ease-out">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                        <span>Lost Device Recovery</span>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-accent">
                        <span class="w-2 h-2 rounded-full bg-accent"></span>
                        3
                    </span>
                </a>

                <a href="#audit" class="flex items-center gap-3 px-3 py-2.5 min-h-11 rounded-lg text-text-muted hover:text-text hover:bg-background font-medium text-sm transition-colors duration-150 ease-out">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    <span>Security Audit</span>
                </a>

                <a href="#status" class="flex items-center gap-3 px-3 py-2.5 min-h-11 rounded-lg text-text-muted hover:text-text hover:bg-background font-medium text-sm transition-colors duration-150 ease-out">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                    <span>System Status</span>
                </a>
            </nav>
        </div>

        <div class="p-6 border-t border-border flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-sm font-medium text-text">n.abrenica@securets.io</span>
                <span class="text-xs text-text-muted">Root Administrator</span>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-8 flex flex-col gap-8 overflow-y-auto">

        <!-- Header -->
        <header class="flex items-center justify-between border-b border-border pb-6">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-text">Platform Operations</h1>
                <p class="text-sm text-text-muted mt-1">Real-time authentication telemetry, device recovery, and tenant access control.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-mono tabular-nums text-text-muted px-3 py-2 rounded-lg bg-surface border border-border">
                    Reverb WS: Connected
                </span>
            </div>
        </header>

        <!-- 2. Global System Status (Top Section) -->
        <section id="status" aria-label="Global System Status" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- API Latency Widget -->
            <div class="bg-surface border border-border rounded-xl p-6 flex flex-col justify-between gap-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-text-muted">Access Request API Latency (p95)</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-accent">
                        <span class="w-2 h-2 rounded-full bg-accent"></span>
                        Operational
                    </span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-semibold font-mono tabular-nums text-text">142 ms</span>
                    <span class="text-xs text-text-muted">Target: &lt; 2000 ms</span>
                </div>
            </div>

            <!-- Database Health Widget -->
            <div class="bg-surface border border-border rounded-xl p-6 flex flex-col justify-between gap-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-text-muted">Database Health (Supabase Primary)</span>
                    <span class="text-xs font-medium text-text">Healthy</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-semibold font-mono tabular-nums text-text">99.98%</span>
                    <span class="text-xs font-mono tabular-nums text-text-muted">Pool: 18 / 100 active</span>
                </div>
            </div>

            <!-- Emergency Lockdown Toggle -->
            <div class="bg-surface border border-border rounded-xl p-6 flex flex-col justify-between gap-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-sm font-semibold text-text">Emergency Lockdown</h2>
                        <p class="text-xs text-text-muted mt-1">Pause all new Target App authentication requests globally.</p>
                    </div>
                    <button
                        type="button"
                        role="switch"
                        aria-checked="false"
                        aria-label="Toggle Emergency Lockdown"
                        onclick="const active = this.getAttribute('aria-checked') === 'true'; this.setAttribute('aria-checked', String(!active)); this.classList.toggle('bg-primary', !active); this.classList.toggle('bg-background', active); this.firstElementChild.classList.toggle('translate-x-5', !active); this.firstElementChild.classList.toggle('translate-x-0', active);"
                        class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer items-center rounded-lg border border-border bg-background p-1 transition-colors duration-150 ease-out focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                        <span class="pointer-events-none inline-block h-5 w-5 translate-x-0 rounded-md bg-text transition-transform duration-150 ease-out"></span>
                    </button>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-border">
                    <span class="text-xs text-text-muted">Current Mode</span>
                    <span class="text-xs font-mono tabular-nums text-text">Standard Enforcement</span>
                </div>
            </div>
        </section>

        <!-- 3. Lost Device Recovery & Active Sessions (Middle Section) -->
        <section id="recovery" aria-label="Lost Device Recovery and Active Sessions" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Pending Lost Device Requests -->
            <div class="bg-surface border border-border rounded-xl p-6 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-text">Lost Device Recovery</h2>
                        <p class="text-xs text-text-muted mt-0.5">Pending instance token revocations</p>
                    </div>
                    <span class="text-xs font-mono tabular-nums font-semibold text-accent px-2.5 py-1 rounded-lg border border-border">
                        3 Pending
                    </span>
                </div>

                <div class="flex flex-col gap-4">
                    <!-- Request 1 -->
                    <div class="p-4 rounded-lg bg-background border border-border flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-text">elena.rostova@acme.dev</p>
                                <p class="text-xs text-text-muted mt-0.5">iPhone 16 Pro • Token <span class="font-mono tabular-nums">inst_9f8a...31c</span></p>
                            </div>
                            <span class="text-xs font-mono tabular-nums text-text-muted">4m ago</span>
                        </div>
                        <div class="flex items-center gap-2 pt-2 border-t border-border">
                            <button type="button" class="flex-1 min-h-11 px-3 py-2 rounded-lg bg-primary text-text text-xs font-medium transition-colors duration-150 ease-out">
                                Approve &amp; Revoke
                            </button>
                            <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-text-muted hover:text-text text-xs font-medium transition-colors duration-150 ease-out">
                                Dismiss
                            </button>
                        </div>
                    </div>

                    <!-- Request 2 -->
                    <div class="p-4 rounded-lg bg-background border border-border flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-text">m.chen@FinVault.io</p>
                                <p class="text-xs text-text-muted mt-0.5">Pixel 9 Pro • Token <span class="font-mono tabular-nums">inst_4b2d...89e</span></p>
                            </div>
                            <span class="text-xs font-mono tabular-nums text-text-muted">19m ago</span>
                        </div>
                        <div class="flex items-center gap-2 pt-2 border-t border-border">
                            <button type="button" class="flex-1 min-h-11 px-3 py-2 rounded-lg border border-border text-text hover:bg-surface text-xs font-medium transition-colors duration-150 ease-out">
                                Approve &amp; Revoke
                            </button>
                            <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-text-muted hover:text-text text-xs font-medium transition-colors duration-150 ease-out">
                                Dismiss
                            </button>
                        </div>
                    </div>

                    <!-- Request 3 -->
                    <div class="p-4 rounded-lg bg-background border border-border flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-text">d.okafor@nimbus.co</p>
                                <p class="text-xs text-text-muted mt-0.5">iPhone 15 • Token <span class="font-mono tabular-nums">inst_7c1e...04a</span></p>
                            </div>
                            <span class="text-xs font-mono tabular-nums text-text-muted">42m ago</span>
                        </div>
                        <div class="flex items-center gap-2 pt-2 border-t border-border">
                            <button type="button" class="flex-1 min-h-11 px-3 py-2 rounded-lg border border-border text-text hover:bg-surface text-xs font-medium transition-colors duration-150 ease-out">
                                Approve &amp; Revoke
                            </button>
                            <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-text-muted hover:text-text text-xs font-medium transition-colors duration-150 ease-out">
                                Dismiss
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active User Sessions Table -->
            <div id="sessions" class="xl:col-span-2 bg-surface border border-border rounded-xl p-6 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-text">Active Sessions</h2>
                        <p class="text-xs text-text-muted mt-0.5">Authenticated web sessions and paired mobile instances</p>
                    </div>
                    <span class="text-xs text-text-muted font-mono tabular-nums">Showing 4 of 128 active</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-border text-xs font-medium text-text-muted">
                                <th class="pb-3 pr-4">User</th>
                                <th class="pb-3 px-4">OS / Device</th>
                                <th class="pb-3 px-4">Browser / Client</th>
                                <th class="pb-3 px-4">IP Address</th>
                                <th class="pb-3 pl-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-sm">
                            <tr>
                                <td class="py-4 pr-4 font-medium text-text">s.lindqvist@polar.sh</td>
                                <td class="py-4 px-4 text-text-muted">macOS 15.1</td>
                                <td class="py-4 px-4 text-text-muted">Safari 18.1</td>
                                <td class="py-4 px-4 font-mono tabular-nums text-xs text-text-muted">185.220.101.44</td>
                                <td class="py-4 pl-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text hover:bg-background transition-colors duration-150 ease-out">
                                            Force Logout
                                        </button>
                                        <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text-muted hover:text-text hover:bg-background transition-colors duration-150 ease-out">
                                            Revoke Device
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 pr-4 font-medium text-text">k.takahashi@kuro.jp</td>
                                <td class="py-4 px-4 text-text-muted">Windows 11</td>
                                <td class="py-4 px-4 text-text-muted">Firefox 132.0</td>
                                <td class="py-4 px-4 font-mono tabular-nums text-xs text-text-muted">203.0.113.89</td>
                                <td class="py-4 pl-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text hover:bg-background transition-colors duration-150 ease-out">
                                            Force Logout
                                        </button>
                                        <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text-muted hover:text-text hover:bg-background transition-colors duration-150 ease-out">
                                            Revoke Device
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 pr-4 font-medium text-text">a.verma@lattice.io</td>
                                <td class="py-4 px-4 text-text-muted">iOS 18.1 (iPhone)</td>
                                <td class="py-4 px-4 text-text-muted">SecureTS Mobile 2.4</td>
                                <td class="py-4 px-4 font-mono tabular-nums text-xs text-text-muted">198.51.100.17</td>
                                <td class="py-4 pl-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text hover:bg-background transition-colors duration-150 ease-out">
                                            Force Logout
                                        </button>
                                        <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text-muted hover:text-text hover:bg-background transition-colors duration-150 ease-out">
                                            Revoke Device
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 pr-4 font-medium text-text">j.miller@linear.app</td>
                                <td class="py-4 px-4 text-text-muted">Ubuntu 24.04 LTS</td>
                                <td class="py-4 px-4 text-text-muted">Chrome 130.0</td>
                                <td class="py-4 px-4 font-mono tabular-nums text-xs text-text-muted">192.0.2.214</td>
                                <td class="py-4 pl-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text hover:bg-background transition-colors duration-150 ease-out">
                                            Force Logout
                                        </button>
                                        <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text-muted hover:text-text hover:bg-background transition-colors duration-150 ease-out">
                                            Revoke Device
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 4. Security Audit Trail & 5. User Management (Bottom Section) -->
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- 4. Security Audit Trail -->
            <div id="audit" class="bg-surface border border-border rounded-xl p-6 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-text">Security Audit Trail</h2>
                        <p class="text-xs text-text-muted mt-0.5">Live authentication and verification stream</p>
                    </div>
                    <span class="text-xs font-mono tabular-nums text-text-muted">Live</span>
                </div>

                <div class="flex flex-col gap-4">
                    <!-- Critical Alert: Multiple failed TOTP attempts -->
                    <div class="p-4 rounded-lg bg-background border border-accent flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-accent">
                                <span class="w-2 h-2 rounded-full bg-accent"></span>
                                Action Required
                            </span>
                            <span class="text-xs font-mono tabular-nums text-text-muted">10:42:18 UTC</span>
                        </div>
                        <p class="text-sm font-semibold text-text">Multiple failed TOTP attempts</p>
                        <p class="text-xs text-text-muted">
                            6 consecutive RFC 6238 verification failures for <span class="text-text font-medium">r.vance@citadel.org</span> via Target App <span class="font-mono">int_prod_88a</span> (IP <span class="font-mono tabular-nums">91.132.14.88</span>).
                        </p>
                    </div>

                    <!-- Standard Event 1 -->
                    <div class="p-4 rounded-lg bg-background border border-border flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-text">Push Approval Denied</span>
                            <span class="text-xs font-mono tabular-nums text-text-muted">10:39:04 UTC</span>
                        </div>
                        <p class="text-xs text-text-muted">
                            Owner explicitly denied access request <span class="font-mono tabular-nums">req_01jc8...9k</span> from <span class="font-mono tabular-nums">45.155.205.11</span>.
                        </p>
                    </div>

                    <!-- Standard Event 2 -->
                    <div class="p-4 rounded-lg bg-background border border-border flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-text">Target App Secret Rotated</span>
                            <span class="text-xs font-mono tabular-nums text-text-muted">10:21:50 UTC</span>
                        </div>
                        <p class="text-xs text-text-muted">
                            API secret hashed and rotated for integration <span class="font-mono tabular-nums">int_ prod_21f</span> by <span class="text-text">s.lindqvist@polar.sh</span>.
                        </p>
                    </div>

                    <!-- Standard Event 3 -->
                    <div class="p-4 rounded-lg bg-background border border-border flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-text">Access Request Expired</span>
                            <span class="text-xs font-mono tabular-nums text-text-muted">10:14:12 UTC</span>
                        </div>
                        <p class="text-xs text-text-muted">
                            60s TTL elapsed for <span class="font-mono tabular-nums">req_01jc8...4m</span>; automatically resolved as <span class="font-mono">denied</span>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 5. User Management -->
            <div id="users" class="xl:col-span-2 bg-surface border border-border rounded-xl p-6 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-text">User Management</h2>
                        <p class="text-xs text-text-muted mt-0.5">Registered accounts, subscription tiers, and Target App integrations</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input
                            type="search"
                            placeholder="Filter by email..."
                            aria-label="Filter users by email"
                            class="min-h-11 px-3 py-2 rounded-lg bg-background border border-border text-sm text-text placeholder:text-text-muted focus:outline-none focus:border-primary"
                        >
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-border text-xs font-medium text-text-muted">
                                <th class="pb-3 pr-4">Account</th>
                                <th class="pb-3 px-4">Subscription Tier</th>
                                <th class="pb-3 px-4">Connected Apps</th>
                                <th class="pb-3 px-4">Paired Devices</th>
                                <th class="pb-3 pl-4 text-right">Account Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border text-sm">
                            <tr>
                                <td class="py-4 pr-4">
                                    <div class="font-medium text-text">elena.rostova@acme.dev</div>
                                    <div class="text-xs text-text-muted font-mono tabular-nums">usr_01h9x...72k</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-lg border border-border text-xs font-medium text-text">Enterprise</span>
                                </td>
                                <td class="py-4 px-4 font-mono tabular-nums text-text">14</td>
                                <td class="py-4 px-4 font-mono tabular-nums text-text-muted">3 devices</td>
                                <td class="py-4 pl-4 text-right">
                                    <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text hover:bg-background transition-colors duration-150 ease-out">
                                        Suspend Account
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 pr-4">
                                    <div class="font-medium text-text">s.lindqvist@polar.sh</div>
                                    <div class="text-xs text-text-muted font-mono tabular-nums">usr_01h8m...19p</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-lg border border-border text-xs font-medium text-text">Scale</span>
                                </td>
                                <td class="py-4 px-4 font-mono tabular-nums text-text">6</td>
                                <td class="py-4 px-4 font-mono tabular-nums text-text-muted">2 devices</td>
                                <td class="py-4 pl-4 text-right">
                                    <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text hover:bg-background transition-colors duration-150 ease-out">
                                        Suspend Account
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 pr-4">
                                    <div class="font-medium text-text">r.vance@citadel.org</div>
                                    <div class="text-xs text-text-muted font-mono tabular-nums">usr_01h7c...55q</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-lg border border-border text-xs font-medium text-text">Scale</span>
                                </td>
                                <td class="py-4 px-4 font-mono tabular-nums text-text">8</td>
                                <td class="py-4 px-4 font-mono tabular-nums text-text-muted">1 device</td>
                                <td class="py-4 pl-4 text-right">
                                    <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text hover:bg-background transition-colors duration-150 ease-out">
                                        Suspend Account
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 pr-4">
                                    <div class="font-medium text-text">k.takahashi@kuro.jp</div>
                                    <div class="text-xs text-text-muted font-mono tabular-nums">usr_01h5z...08n</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-lg border border-border text-xs font-medium text-text-muted">Starter</span>
                                </td>
                                <td class="py-4 px-4 font-mono tabular-nums text-text">2</td>
                                <td class="py-4 px-4 font-mono tabular-nums text-text-muted">1 device</td>
                                <td class="py-4 pl-4 text-right">
                                    <button type="button" class="min-h-11 px-3 py-2 rounded-lg border border-border text-xs font-medium text-text hover:bg-background transition-colors duration-150 ease-out">
                                        Suspend Account
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

</body>
</html>
