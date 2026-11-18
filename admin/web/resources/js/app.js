const views = [...document.querySelectorAll('[data-view]')];
const navItems = [...document.querySelectorAll('[data-tab]')];
const pageTitle = document.querySelector('#page-title');
const pageDescription = document.querySelector('#page-description');
const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

const viewCopy = {
    dashboard: ['Security Overview', 'Monitor users, sessions, recovery requests, and platform health in real time.'],
    users: ['User Management', 'Registered accounts, subscription tiers, and connected applications.'],
    sessions: ['Active Sessions', 'Authenticated web sessions and paired mobile instances.'],
    recovery: ['Lost Device Recovery', 'Pending instance token revocations and device security actions.'],
    audit: ['Security Audit Trail', 'Live authentication and administrative verification stream.'],
    status: ['System Status', 'Authentication, database, and real-time transport health.'],
};

const escapeHtml = (value = '') => String(value).replace(/[&<>'"]/g, (character) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;',
}[character]));

function switchTab(tab) {
    views.forEach((view) => view.classList.toggle('is-visible', view.id === `view-${tab}`));
    navItems.forEach((item) => item.classList.toggle('is-active', item.dataset.tab === tab));
    if (viewCopy[tab]) {
        pageTitle.textContent = viewCopy[tab][0];
        pageDescription.textContent = viewCopy[tab][1];
    }
    history.replaceState(null, '', tab === 'dashboard' ? window.location.pathname : `#${tab}`);
}

navItems.forEach((item) => item.addEventListener('click', () => switchTab(item.dataset.tab)));

function auditBadge(severity) {
    const className = severity === 'warning' || severity === 'critical' ? 'badge-crimson' : severity === 'info' ? 'badge-blue' : 'badge-slate';
    return `<span class="badge ${className}">${escapeHtml(severity === 'warning' ? 'Action Required' : severity)}</span>`;
}

function renderAudit(rows = []) {
    const html = rows.length ? rows.map((row) => `<tr>
        <td class="font-mono text-xs text-slate-400">${escapeHtml(row.time || '—')}</td>
        <td>${escapeHtml(row.actor)}</td>
        <td>${escapeHtml(row.action)}</td>
        <td class="font-mono text-xs">${escapeHtml(row.target || '—')}</td>
        <td>${auditBadge(row.severity)}</td>
    </tr>`).join('') : '<tr><td colspan="5" class="text-slate-500">No audit events recorded yet.</td></tr>';
    document.querySelector('#audit-snapshot').innerHTML = html;
    const fullAudit = document.querySelector('#audit-full');
    if (fullAudit) fullAudit.innerHTML = html;
}

function bindDeviceActions() {
    document.querySelectorAll('[data-device-action]').forEach((button) => {
        button.addEventListener('click', async () => {
            const row = button.closest('[data-device-row]');
            const deviceId = row.dataset.deviceId;
            const buttons = row.querySelectorAll('[data-device-action]');
            buttons.forEach((actionButton) => { actionButton.disabled = true; });
            button.textContent = 'Working…';

            try {
                const response = await fetch(`/admin/api/devices/${deviceId}/action`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ action: button.dataset.deviceAction }),
                });
                if (!response.ok) throw new Error('The device action could not be completed.');
                const result = await response.json();
                const status = row.querySelector('.device-status');
                status.textContent = result.status.replaceAll('_', ' ');
                status.className = 'badge badge-blue device-status';
                row.classList.add('is-resolved');
                button.textContent = 'Done';
                await loadOverview();
            } catch (error) {
                buttons.forEach((actionButton) => { actionButton.disabled = false; });
                button.textContent = 'Retry';
                window.alert(error.message);
            }
        });
    });
}

function renderDevices(devices = []) {
    const queue = document.querySelector('#device-queue');
    const pending = devices.filter((device) => device.state !== 'active' && device.state !== 'recovered');
    queue.innerHTML = pending.length ? pending.map((device) => `<tr data-device-row data-device-id="${escapeHtml(device.id)}">
        <td><div class="font-medium text-white">${escapeHtml(device.name)}</div><div class="text-xs text-slate-500">${escapeHtml(device.owner)}</div></td>
        <td><div class="font-mono text-xs">${escapeHtml(device.ip)}</div><div class="text-xs text-slate-500">${escapeHtml(device.location)}</div></td>
        <td><span class="badge ${device.state === 'reported_lost' ? 'badge-amber' : 'badge-crimson'} device-status">${escapeHtml(device.status)}</span></td>
        <td><div class="action-group"><button type="button" data-device-action="remote_lock">Lock</button><button type="button" data-device-action="revoke_tokens">Revoke</button><button type="button" data-device-action="mark_recovered">Recover</button></div></td>
    </tr>`).join('') : '<tr><td colspan="4" class="text-slate-500">No devices require action.</td></tr>';
    bindDeviceActions();
}

function renderUsers(users = []) {
    const body = document.querySelector('#view-users .data-table tbody');
    if (!body) return;
    body.innerHTML = users.length ? users.map((user) => `<tr>
        <td><div class="font-medium text-white">${escapeHtml(user.name)}</div><div class="text-xs text-slate-500">${escapeHtml(user.email)}</div></td>
        <td><span class="badge badge-slate">${escapeHtml(user.plan ?? '—')}</span></td>
        <td>${Number(user.apps ?? 0).toLocaleString()}</td>
        <td>${Number(user.devices ?? 0).toLocaleString()} device${Number(user.devices ?? 0) === 1 ? '' : 's'}</td>
        <td class="text-slate-500">${escapeHtml(user.created_at ?? '—')}</td>
    </tr>`).join('') : '<tr><td colspan="5" class="text-slate-500">No active users found.</td></tr>';
}

function renderSessions(sessions = []) {
    const body = document.querySelector('#view-sessions .data-table tbody');
    if (!body) return;
    body.innerHTML = sessions.length ? sessions.map((session) => `<tr>
        <td>${escapeHtml(session.user)}</td>
        <td class="text-slate-400">${escapeHtml(session.device ?? 'Unknown')}</td>
        <td class="text-slate-400">${escapeHtml(session.client)}</td>
        <td class="font-mono text-xs">${escapeHtml(session.ip)}</td>
        <td class="text-slate-500">${escapeHtml(session.last_seen)}</td>
    </tr>`).join('') : '<tr><td colspan="5" class="text-slate-500">No active sessions in the last 24 hours.</td></tr>';
    const count = document.querySelector('#sessions-count') ?? document.querySelector('[data-tab="sessions"] .nav-badge');
    if (count) count.textContent = sessions.length.toLocaleString();
    const summary = document.querySelector('#sessions-summary') ?? document.querySelector('#view-sessions .panel-header .badge');
    if (summary) summary.textContent = `${sessions.length.toLocaleString()} active`;
}

function renderRecovery(devices = []) {
    const container = document.querySelector('#view-recovery .recovery-grid');
    if (!container) return;
    const pending = devices.filter((device) => device.state !== 'active' && device.state !== 'recovered');
    container.innerHTML = pending.length ? pending.map((device) => `<div class="recovery-card">
        <strong>${escapeHtml(device.name)}</strong>
        <span>${escapeHtml(device.owner)}</span>
        <span class="font-mono">${escapeHtml(device.ip)}</span>
        <span>${escapeHtml(device.status)} · ${escapeHtml(device.location)}</span>
        <button type="button" class="primary-button" data-recovery-device="${escapeHtml(device.id)}">Review device</button>
    </div>`).join('') : '<div class="recovery-card"><strong>No pending recovery devices</strong><span>All registered devices are active or recovered.</span></div>';
    const count = document.querySelector('#recovery-count') ?? document.querySelector('[data-tab="recovery"] .nav-badge');
    if (count) count.textContent = pending.length.toLocaleString();
    const summary = document.querySelector('#recovery-summary') ?? document.querySelector('#view-recovery .panel-header .badge');
    if (summary) summary.textContent = `${pending.length.toLocaleString()} pending`;
}

function applyMetrics(metrics) {
    document.querySelector('#metric-users').textContent = Number(metrics.users ?? 0).toLocaleString();
    document.querySelector('#metric-sessions').textContent = Number(metrics.sessions ?? 0).toLocaleString();
    document.querySelector('#metric-devices').textContent = Number(metrics.devices ?? 0).toLocaleString();
    document.querySelector('#metric-health').textContent = metrics.health ?? '—';
}

function applyStatus(system = {}, apiOk = true, apiLatency = null) {
    const db = system.database ?? {};
    const set = (selector, text) => { const el = document.querySelector(selector); if (el) el.textContent = text; };
    set('#status-api', apiOk ? 'Operational' : 'Unavailable');
    set('#status-api-meta', apiLatency !== null ? `/admin/api/overview · ${apiLatency} ms` : '/admin/api/overview');
    set('#status-db', db.status ?? 'Unavailable');
    set('#status-db-meta', db.latency_ms != null ? `${String(db.driver ?? '').toUpperCase()} · ${db.latency_ms} ms` : 'No response');
    set('#sidebar-health', apiOk && db.status === 'Operational' ? 'System Operational' : 'System Degraded');
}

async function loadOverview() {
    const startedAt = performance.now();
    try {
        const response = await fetch('/admin/api/overview', { headers: { Accept: 'application/json' } });
        if (response.status === 401 || response.status === 403) {
            window.location.assign('/admin/login');
            return;
        }
        if (!response.ok) throw new Error('Overview unavailable');
        const data = await response.json();
        applyStatus(data.system, true, Math.round(performance.now() - startedAt));
        applyMetrics(data.metrics);
        renderDevices(data.devices);
        renderUsers(data.users);
        renderSessions(data.sessions);
        renderRecovery(data.devices);
        renderAudit(data.audit);
    } catch (error) {
        applyStatus({}, false);
        console.error(error);
    }
}

document.querySelector('[data-tab="sessions"] .nav-badge')?.replaceChildren('—');
document.querySelector('[data-tab="recovery"] .nav-badge')?.replaceChildren('—');
document.querySelector('#view-sessions .panel-header .badge')?.replaceChildren('— active');
document.querySelector('#view-recovery .panel-header .badge')?.replaceChildren('— pending');

const search = document.querySelector('#global-search');
search?.addEventListener('input', () => {
    const query = search.value.trim().toLowerCase();
    document.querySelectorAll('.data-table tbody tr').forEach((row) => {
        row.hidden = Boolean(query) && !row.textContent.toLowerCase().includes(query);
    });
});
search?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && search.value.trim()) {
        document.querySelectorAll('.data-table tbody tr').forEach((row) => {
            row.hidden = !row.textContent.toLowerCase().includes(search.value.trim().toLowerCase());
        });
    }
});
document.addEventListener('keydown', (event) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        search?.focus();
    }
});

const initialTab = window.location.hash.slice(1);
if (viewCopy[initialTab]) switchTab(initialTab);
loadOverview();
