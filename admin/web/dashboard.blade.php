<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Security Admin — SecureTS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="security-console bg-canvas text-text font-sans antialiased min-h-screen">
    <div class="flex min-h-screen">
        <aside class="security-sidebar w-64 lg:w-[250px] bg-sidebar border-r border-border flex flex-col shrink-0">
            <div class="p-5 border-b border-border">
                <div class="flex items-center gap-3">
                    <div class="shield-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 5 6v5c0 4.8 2.9 8.2 7 10 4.1-1.8 7-5.2 7-10V6l-7-3Z"/><path d="m9.5 12 1.7 1.7 3.5-3.5"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold tracking-[0.18em] text-white">SECURITY ADMIN</div>
                        <div class="flex items-center gap-1.5 mt-1 text-[11px] text-emerald"><span class="status-dot"></span> <span id="sidebar-health">Checking…</span></div>
                    </div>
                </div>
            </div>

            <nav class="p-4 flex flex-col gap-1" aria-label="Security Admin Navigation">
                <button type="button" data-tab="dashboard" class="admin-nav-item is-active"><span class="nav-icon">⌂</span><span>Dashboard</span></button>
                <button type="button" data-tab="users" class="admin-nav-item"><span class="nav-icon">♙</span><span>User Management</span></button>
                <button type="button" data-tab="sessions" class="admin-nav-item"><span class="nav-icon">◫</span><span>Active Sessions</span><span id="sessions-count" class="nav-badge">—</span></button>
                <button type="button" data-tab="recovery" class="admin-nav-item admin-nav-alert"><span class="nav-icon">⌁</span><span>Lost Device Recovery</span><span id="recovery-count" class="nav-badge is-amber">—</span></button>
                <button type="button" data-tab="audit" class="admin-nav-item"><span class="nav-icon">✓</span><span>Security Audit</span></button>
                <button type="button" data-tab="status" class="admin-nav-item"><span class="nav-icon">ϟ</span><span>System Status</span><span class="status-dot ml-auto"></span></button>
            </nav>

            <div class="mt-auto p-5 border-t border-border">
                <div class="text-xs text-white font-medium">Admin</div>
                <div class="text-[11px] text-slate-400 mt-1">Authenticated admin session</div>
            </div>
        </aside>

        <main class="flex-1 min-w-0 p-5 lg:p-8">
            <header class="flex flex-col xl:flex-row xl:items-center justify-between gap-5 mb-7">
                <div>
                    <p class="eyebrow">SECURITY OPERATIONS CENTER</p>
                    <h1 id="page-title" class="text-2xl lg:text-3xl font-semibold tracking-tight mt-2">Security Overview</h1>
                    <p id="page-description" class="text-sm text-slate-400 mt-2">Monitor users, sessions, recovery requests, and platform health in real time.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <label class="search-box">
                        <span class="text-slate-500">⌕</span>
                        <input id="global-search" type="search" placeholder="Search User ID, IP, or token..." aria-label="Search security records">
                        <kbd>Ctrl K</kbd>
                    </label>
                </div>
            </header>

            <div id="view-dashboard" data-view class="admin-view is-visible">
                <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6" aria-label="Security KPIs">
                    <button type="button" data-tab="users" class="kpi-card text-left"><span class="kpi-label">Total / Active Users</span><strong id="metric-users">—</strong><span class="kpi-meta text-emerald">From registered accounts <span>↗</span></span></button>
                    <button type="button" data-tab="sessions" class="kpi-card text-left"><span class="kpi-label">Active Sessions</span><strong id="metric-sessions">—</strong><span class="kpi-meta text-sky-400"><span class="status-dot bg-sky-400"></span> Last 24 hours <span>↗</span></span></button>
                    <button type="button" data-tab="recovery" class="kpi-card kpi-alert text-left"><span class="kpi-label">Pending Lost Devices</span><strong id="metric-devices" class="text-amber">—</strong><span class="kpi-meta text-amber">Requires immediate action <span>↗</span></span></button>
                    <button type="button" data-tab="status" class="kpi-card text-left"><span class="kpi-label">System Health</span><strong id="metric-health">—</strong><span class="kpi-meta text-emerald">Live platform health <span>↗</span></span></button>
                </section>

                <section class="mb-6">
                    <div class="panel">
                        <div class="panel-header"><div><h2>Active Lost Device Queue</h2><p>Reported devices requiring security action</p></div><button type="button" data-tab="recovery" class="text-xs text-primary hover:text-white">View queue →</button></div>
                        <div class="overflow-x-auto"><table class="data-table"><thead><tr><th>Device / Owner</th><th>Last Known IP / Location</th><th>Status</th><th class="text-right">Actions</th></tr></thead><tbody id="device-queue"><tr><td colspan="4" class="text-slate-500">Loading devices…</td></tr>
                        </tbody></table></div>
                    </div>
                </section>

                        <section class="panel"><div class="panel-header"><div><h2>Recent Security Audit</h2><p>Latest authentication and administrative events</p></div><button type="button" data-tab="audit" class="text-xs text-primary hover:text-white">View Full Audit Log →</button></div><div class="overflow-x-auto"><table class="data-table"><thead><tr><th>Timestamp</th><th>Actor</th><th>Action</th><th>Target Resource</th><th>Severity / Status</th></tr></thead><tbody id="audit-snapshot"></tbody></table></div></section>
            </div>

            <div id="view-users" data-view class="admin-view"><section class="panel"><div class="panel-header"><div><h2>User Management</h2><p>Registered accounts, subscription tiers, and connected applications</p></div><input class="table-filter" data-filter="users" type="search" placeholder="Filter by email..."></div><div class="overflow-x-auto"><table class="data-table"><thead><tr><th>Account</th><th>Subscription</th><th>Connected Apps</th><th>Paired Devices</th><th>Joined</th></tr></thead><tbody><tr><td colspan="5" class="text-slate-500">Loading users…</td></tr></tbody></table></div></section></div>
            <div id="view-sessions" data-view class="admin-view"><section class="panel"><div class="panel-header"><div><h2>Active Sessions</h2><p>Authenticated web sessions and paired mobile instances</p></div><span id="sessions-summary" class="badge badge-blue">— active</span></div><div class="overflow-x-auto"><table class="data-table"><thead><tr><th>User</th><th>OS / Device</th><th>Browser / Client</th><th>IP Address</th><th>Last Seen</th></tr></thead><tbody><tr><td colspan="5" class="text-slate-500">Loading sessions…</td></tr></tbody></table></div></section></div>
            <div id="view-recovery" data-view class="admin-view"><section class="panel"><div class="panel-header"><div><h2>Lost Device Recovery</h2><p>Pending instance token revocations</p></div><span id="recovery-summary" class="badge badge-amber">— pending</span></div><div class="recovery-grid"><div class="recovery-card"><span class="text-slate-500">Loading recovery devices…</span></div></div></section></div>
            <div id="view-audit" data-view class="admin-view"><section class="panel"><div class="panel-header"><div><h2>Security Audit Trail</h2><p>Live authentication and verification stream</p></div><span class="live-label"><span class="status-dot"></span> Live</span></div><div class="overflow-x-auto"><table class="data-table"><thead><tr><th>Timestamp</th><th>Actor</th><th>Action</th><th>Target Resource</th><th>Severity / Status</th></tr></thead><tbody id="audit-full"><tr><td colspan="5" class="text-slate-500">Loading audit events…</td></tr></tbody></table></div></section></div>
            <div id="view-status" data-view class="admin-view">
                <section class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div class="panel status-card"><span>Admin API</span><strong id="status-api">—</strong><small id="status-api-meta">Checking overview endpoint</small></div>
                    <div class="panel status-card"><span>Database Health</span><strong id="status-db">—</strong><small id="status-db-meta">Checking Supabase connection</small></div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
