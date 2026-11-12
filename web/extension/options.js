const baseUrlInput = document.getElementById('baseUrl');
const tokenInput = document.getElementById('token');
const statusEl = document.getElementById('status');
const sitesEl = document.getElementById('sites');
const saveBtn = document.getElementById('save');
const refreshBtn = document.getElementById('refresh');

function send(message) {
  return new Promise((resolve) => chrome.runtime.sendMessage(message, resolve));
}

function setStatus(text, kind = '') {
  statusEl.textContent = text;
  statusEl.className = kind;
}

function renderSites(integrations) {
  if (!integrations.length) {
    sitesEl.innerHTML = '<li class="empty">No protected sites yet. Add one in Integration Hub.</li>';
    return;
  }

  sitesEl.innerHTML = '';
  integrations.forEach((integration) => {
    const li = document.createElement('li');
    const name = document.createElement('span');
    name.textContent = integration.name || 'Unnamed app';
    const host = document.createElement('span');
    host.className = 'host';
    host.textContent = integration.host || '';
    li.append(name, host);
    sitesEl.appendChild(li);
  });
}

async function load() {
  const state = await send({ type: 'status' });
  if (!state) {
    setStatus('Could not reach the extension background worker.', 'error');
    return;
  }

  baseUrlInput.value = state.baseUrl || '';
  tokenInput.value = state.token || '';
  renderSites(state.integrations || []);

  if (!state.configured) {
    setStatus('Not paired yet. Enter your SecureTS base URL and pairing token.', 'error');
  } else {
    const when = state.syncedAt ? new Date(state.syncedAt).toLocaleString() : 'never';
    setStatus(`Paired. Last synced: ${when}`);
  }
}

saveBtn.addEventListener('click', async () => {
  const baseUrl = baseUrlInput.value.trim().replace(/\/+$/, '');
  if (!baseUrl) {
    setStatus('Enter the SecureTS base URL first.', 'error');
    return;
  }

  await chrome.storage.local.set({
    baseUrl,
    token: tokenInput.value.trim(),
    syncedAt: 0,
  });

  setStatus('Saving…');
  const result = await send({ type: 'refresh' });
  await load();

  if (result && result.ok) setStatus('Saved. Protected sites synced.', 'ok');
  else if (result) setStatus(`Saved, but sync failed (${result.error}).`, 'error');
});

refreshBtn.addEventListener('click', async () => {
  setStatus('Syncing…');
  const result = await send({ type: 'refresh' });
  await load();
  if (result && result.ok) setStatus('Protected sites synced.', 'ok');
  else if (result) setStatus(`Sync failed (${result.error}).`, 'error');
});

load();
