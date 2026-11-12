// SecureTS Gate — background service worker (MV3).
//
// Job: when the browser navigates to the top frame of a URL the user protects in
// SecureTS, send that tab to the SecureTS gate instead. The gate runs the normal
// approval flow (auto-approved on the first visit, phone approval afterwards) and
// hands the tab back to the target URL once it is approved.

const CACHE_TTL_MS = 5 * 60 * 1000;
const EXT_PARAM = 'ext';

// ── Settings & cached config ─────────────────────────────────────────────────

async function getSettings() {
  const { baseUrl = '', token = '' } = await chrome.storage.local.get(['baseUrl', 'token']);
  return { baseUrl: String(baseUrl).replace(/\/+$/, ''), token: String(token) };
}

async function getCache() {
  const { integrations = [], syncedAt = 0 } = await chrome.storage.local.get(['integrations', 'syncedAt']);
  return { integrations: Array.isArray(integrations) ? integrations : [], syncedAt };
}

async function refreshConfig(force = false) {
  const { baseUrl, token } = await getSettings();
  if (!baseUrl || !token) return { ok: false, error: 'not-configured' };

  const { syncedAt } = await getCache();
  if (!force && Date.now() - syncedAt < CACHE_TTL_MS) return { ok: true, cached: true };

  try {
    const res = await fetch(`${baseUrl}/api/extension/config`, {
      headers: { 'X-Extension-Token': token, Accept: 'application/json' },
    });
    if (!res.ok) return { ok: false, error: `http-${res.status}` };

    const data = await res.json();
    await chrome.storage.local.set({
      integrations: Array.isArray(data.integrations) ? data.integrations : [],
      syncedAt: Date.now(),
    });
    return { ok: true };
  } catch (error) {
    return { ok: false, error: String(error && error.message ? error.message : error) };
  }
}

// ── Per-tab state (survives service-worker restarts) ─────────────────────────

async function getTabState() {
  const { tabState = {} } = await chrome.storage.session.get('tabState');
  return tabState;
}

async function setTabState(state) {
  await chrome.storage.session.set({ tabState: state });
}

async function updateTab(tabId, mutate) {
  const state = await getTabState();
  const tab = state[tabId] || { lastUrl: '', released: [] };
  mutate(tab);
  state[tabId] = tab;
  await setTabState(state);
}

// ── URL helpers ──────────────────────────────────────────────────────────────

function hostOf(url) {
  try {
    return new URL(url).hostname.toLowerCase();
  } catch (_) {
    return '';
  }
}

function pathOf(url) {
  try {
    return new URL(url).pathname;
  } catch (_) {
    return '';
  }
}

function hostMatches(host, registered) {
  if (!host || !registered) return false;
  return host === registered || host.endsWith(`.${registered}`);
}

function isGateUrl(url, registeredHost, gateUrl) {
  return hostMatches(hostOf(url), registeredHost) && pathOf(url) === pathOf(gateUrl);
}

// ── Gate decisions ───────────────────────────────────────────────────────────

chrome.webNavigation.onBeforeNavigate.addListener(async (details) => {
  if (details.frameId !== 0) return;
  if (!/^https?:/i.test(details.url)) return;

  await refreshConfig(false);

  const { integrations } = await getCache();
  if (!integrations.length) return;

  const host = hostOf(details.url);
  const match = integrations.find((item) => hostMatches(host, String(item.host || '').toLowerCase()));
  if (!match || !match.gate_url) return;

  const registeredHost = String(match.host).toLowerCase();

  // Never intercept the SecureTS gate itself.
  if (isGateUrl(details.url, registeredHost, match.gate_url)) return;

  const state = await getTabState();
  const tab = state[details.tabId] || { lastUrl: '', released: [] };

  // This tab was already approved for this site — let internal navigation through.
  if (tab.released.includes(registeredHost)) return;

  // The gate just approved the visit and sent us here. Release the tab once
  // instead of bouncing it back to the gate.
  if (tab.lastUrl && isGateUrl(tab.lastUrl, registeredHost, match.gate_url)) {
    await updateTab(details.tabId, (current) => {
      current.released = Array.from(new Set([...current.released, registeredHost]));
    });
    return;
  }

  const gateUrl = new URL(match.gate_url);
  gateUrl.searchParams.set(EXT_PARAM, '1');
  await chrome.tabs.update(details.tabId, { url: gateUrl.toString() });
});

// Track the last committed top-frame URL so the gate hand-back is recognisable.
chrome.webNavigation.onCommitted.addListener(async (details) => {
  if (details.frameId !== 0) return;
  await updateTab(details.tabId, (tab) => {
    tab.lastUrl = details.url;
  });
});

chrome.tabs.onRemoved.addListener(async (tabId) => {
  const state = await getTabState();
  if (!(tabId in state)) return;
  delete state[tabId];
  await setTabState(state);
});

// ── Wiring ───────────────────────────────────────────────────────────────────

chrome.action.onClicked.addListener(() => chrome.runtime.openOptionsPage());

chrome.runtime.onInstalled.addListener(() => refreshConfig(true));
chrome.runtime.onStartup.addListener(() => refreshConfig(true));

chrome.runtime.onMessage.addListener((message, _sender, sendResponse) => {
  if (message && message.type === 'refresh') {
    refreshConfig(true).then(sendResponse);
    return true;
  }

  if (message && message.type === 'status') {
    (async () => {
      const { baseUrl, token } = await getSettings();
      const { integrations, syncedAt } = await getCache();
      sendResponse({
        baseUrl,
        token,
        configured: Boolean(baseUrl && token),
        integrations,
        syncedAt,
      });
    })();
    return true;
  }

  return false;
});
