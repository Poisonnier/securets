<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in to {{ $integration->name }} – SecureTS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Inter, sans-serif;
            background: #0E1326;
            color: #FFFFFF;
            min-height: 100vh;
            padding: 24px;
        }
        .layout {
            width: 100%;
            max-width: 1180px;
            min-height: calc(100vh - 48px);
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(360px, 440px) minmax(0, 1fr);
            gap: 24px;
            align-items: stretch;
        }
        .card,
        .preview {
            background: #1C2236;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
        }
        .card {
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .preview {
            padding: 24px;
            display: flex;
            flex-direction: column;
            min-height: 560px;
        }
        .app { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .app img { width: 44px; height: 44px; border-radius: 8px; background: #0E1326; border: 1px solid rgba(255,255,255,0.08); }
        .eyebrow {
            color: #00BBFF;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        h1 { font-size: 28px; margin-bottom: 8px; }
        p { color: rgba(255,255,255,0.6); font-size: 14px; line-height: 1.6; margin-bottom: 20px; }
        label { display: block; font-size: 12px; color: rgba(255,255,255,0.6); margin-bottom: 6px; }
        input {
            width: 100%;
            background: #0E1326;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            padding: 12px 14px;
            color: #FFFFFF;
            font-size: 14px;
            margin-bottom: 14px;
        }
        button {
            width: 100%;
            background: #4063D7;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            padding: 14px;
            color: #FFFFFF;
            font-weight: 600;
            cursor: pointer;
        }
        button:disabled { opacity: 0.6; cursor: default; }
        .status strong { display: block; font-size: 18px; margin: 12px 0 8px; }
        a { color: #00BBFF; }
        .err { color: #FFFFFF; font-size: 13px; margin-bottom: 12px; min-height: 1.2em; }
        .hidden { display: none !important; }
        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
            background: #0E1326;
            border: 1px solid rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.6);
        }
        .status-chip.approved,
        .status-chip.auto {
            border-color: #00BBFF;
            color: #00BBFF;
        }
        .status-chip.declined {
            color: #FFFFFF;
        }
        .status-chip.pending {
            border-color: #4063D7;
            color: #FFFFFF;
        }
        .preview-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }
        .preview-header h2 {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .preview-header p {
            margin-bottom: 0;
            max-width: 560px;
        }
        .preview-stage {
            flex: 1;
            min-height: 360px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.08);
            background: #0E1326;
            position: relative;
        }
        .preview-placeholder,
        .launch-card {
            width: 100%;
            height: 100%;
        }
        .preview-placeholder,
        .launch-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 36px;
            gap: 14px;
            color: #FFFFFF;
        }
        .preview-placeholder svg {
            opacity: 0.7;
        }
        .preview-placeholder strong,
        .launch-card strong {
            font-size: 20px;
        }
        .preview-placeholder span,
        .launch-card span {
            color: rgba(255,255,255,0.6);
            line-height: 1.7;
            max-width: 460px;
        }
        .launch-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            background: #1C2236;
            border: 1px solid #00BBFF;
            color: #00BBFF;
        }
        .launch-domain {
            font-family: monospace;
            font-size: 13px;
            color: rgba(255,255,255,0.6);
            background: #1C2236;
            border: 1px solid rgba(255,255,255,0.08);
            padding: 6px 12px;
            border-radius: 8px;
        }
        .launch-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
        }
        .launch-primary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            background: #4063D7;
            border: 1px solid rgba(255,255,255,0.08);
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 600;
        }
        .launch-countdown-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: rgba(255,255,255,0.6);
        }
        .launch-cancel-btn {
            width: auto;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 500;
            background: #1C2236;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            color: #FFFFFF;
            cursor: pointer;
        }
        .preview-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 18px;
            flex-wrap: wrap;
        }
        .preview-hint {
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            line-height: 1.6;
        }
        .preview-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            background: #0E1326;
            border: 1px solid rgba(255,255,255,0.08);
            color: #FFFFFF;
            font-weight: 600;
        }
        @media (max-width: 960px) {
            .layout {
                grid-template-columns: 1fr;
                min-height: auto;
            }
            .preview {
                min-height: 420px;
            }
        }
    </style>
</head>
<body
    data-attempt-url="{{ $integration->gateUrl() }}"
    data-is-first="{{ $isFirst ? '1' : '0' }}"
    data-app-name="{{ $integration->name }}"
    data-ext="{{ $ext ? '1' : '0' }}"
>
<div class="layout">
    <div class="card">
        <div class="app">
            <img src="{{ $integration->favicon }}" alt="" onerror="this.style.display='none'">
            <div>
                <div style="font-weight:600">{{ $integration->name }}</div>
                <div style="font-size:12px;color:rgba(255,255,255,0.6)">Protected by SecureTS</div>
            </div>
        </div>

        <div id="firstPanel" class="{{ $isFirst ? '' : 'hidden' }}">
            <div class="eyebrow">SecureTS Gate</div>
            <h1>First gate visit</h1>
            <p>SecureTS does not sign in to this site or stop direct visits to it. The first visit through this gate is auto-approved; later visits through this link wait for phone approval. Direct sign-ins on the external site bypass this gate. Do not enter your external account password here.</p>
            <div class="err" id="firstError"></div>
            <form id="firstForm">
                <button type="submit" id="firstBtn">Continue to SecureTS check</button>
            </form>
        </div>

        <div id="waitPanel" class="status {{ $isFirst ? 'hidden' : '' }}">
            <div class="eyebrow">SecureTS Gate</div>
            <h1>Waiting for approval</h1>
            <p>Open the SecureTS app on your phone and choose Approve or Decline. After approval, {{ $integration->name }} may open beside this page if it allows embedding. SecureTS does not sign in to the external site.</p>
            <strong id="waitStatus">Contacting your device...</strong>
        </div>

        <div id="donePanel" class="status hidden">
            <div class="eyebrow">SecureTS Gate</div>
            <strong id="doneTitle"></strong>
            <p id="doneText"></p>
            <p><a id="doneLink" href="{{ $integration->url }}" target="_blank" rel="noopener noreferrer">Continue to {{ $integration->name }}</a></p>
        </div>
    </div>

    <aside class="preview" aria-live="polite">
        <div class="preview-header">
            <div>
                <div class="eyebrow" style="margin-bottom:8px">App Preview</div>
                <h2 id="previewTitle">Secure launch area</h2>
                <p id="previewText">The protected app will open here after SecureTS finishes the current login check.</p>
            </div>
            <div class="status-chip" id="previewStatus">Awaiting action</div>
        </div>

        <div class="preview-stage">
            <div class="preview-placeholder" id="previewPlaceholder">
                <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="14" rx="2"></rect>
                    <path d="M8 20h8"></path>
                    <path d="M12 18v2"></path>
                </svg>
                <strong id="previewPlaceholderTitle">Waiting for a decision</strong>
                <span id="previewPlaceholderText">Approve the request from the mobile app to unlock {{ $integration->name }}, or decline it to keep the app blocked.</span>
            </div>

            <div id="verifiedLaunchCard" class="launch-card hidden">
                <div class="launch-badge">Session Verified</div>
                <strong id="launchAppName">{{ $integration->name }}</strong>
                <div id="launchAppDomain" class="launch-domain">{{ $integration->url }}</div>
                <span>Your SecureTS verification challenge passed. Continue directly to the destination application below.</span>
                <div class="launch-actions">
                    <a id="launchContinueBtn" href="{{ $integration->url }}" target="_blank" rel="noopener noreferrer" class="launch-primary-btn">
                        Continue to App ↗
                    </a>
                    <div id="launchCountdownWrap" class="launch-countdown-row">
                        <span id="launchCountdownText">Auto-redirecting in 2s…</span>
                        <button type="button" id="cancelAutoRedirectBtn" class="launch-cancel-btn">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="preview-actions">
            <div class="preview-hint" id="previewHint">
                Some sites block embedded previews. If that happens, use the launch button once approval is complete.
            </div>
            <a
                id="previewLink"
                class="preview-link hidden"
                href="{{ $integration->url }}"
                target="_blank"
                rel="noopener noreferrer"
            >
                Open {{ $integration->name }}
            </a>
        </div>
    </aside>
</div>

<script>
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const settings   = document.body.dataset;
const ATTEMPT_URL = settings.attemptUrl;
const IS_FIRST   = settings.isFirst === '1';
const APP_NAME   = settings.appName;
const EXT        = settings.ext === '1';

function show(id) {
    ['firstPanel', 'waitPanel', 'donePanel'].forEach((p) => {
        document.getElementById(p).classList.toggle('hidden', p !== id);
    });
}

const previewStatus           = document.getElementById('previewStatus');
const previewTitle            = document.getElementById('previewTitle');
const previewText             = document.getElementById('previewText');
const previewPlaceholder      = document.getElementById('previewPlaceholder');
const previewPlaceholderTitle = document.getElementById('previewPlaceholderTitle');
const previewPlaceholderText  = document.getElementById('previewPlaceholderText');
const verifiedLaunchCard      = document.getElementById('verifiedLaunchCard');
const launchAppDomain         = document.getElementById('launchAppDomain');
const launchContinueBtn       = document.getElementById('launchContinueBtn');
const launchCountdownText     = document.getElementById('launchCountdownText');
const cancelAutoRedirectBtn   = document.getElementById('cancelAutoRedirectBtn');
const previewLink             = document.getElementById('previewLink');

let redirectTimer = null;
let redirectSeconds = 2;

function clearAutoRedirect() {
    if (redirectTimer) {
        clearInterval(redirectTimer);
        redirectTimer = null;
    }
}

cancelAutoRedirectBtn?.addEventListener('click', () => {
    clearAutoRedirect();
    launchCountdownText.textContent = 'Auto-redirect cancelled.';
    cancelAutoRedirectBtn.classList.add('hidden');
});

function startAutoRedirect(targetUrl) {
    clearAutoRedirect();
    if (!isSafeHttpUrl(targetUrl)) return;
    redirectSeconds = 2;
    cancelAutoRedirectBtn.classList.remove('hidden');
    launchCountdownText.textContent = `Auto-redirecting in ${redirectSeconds}s…`;

    redirectTimer = setInterval(() => {
        redirectSeconds -= 1;
        if (redirectSeconds <= 0) {
            clearAutoRedirect();
            launchCountdownText.textContent = 'Redirecting…';
            window.location.assign(targetUrl);
            return;
        }
        launchCountdownText.textContent = `Auto-redirecting in ${redirectSeconds}s…`;
    }, 1000);
}

function isSafeHttpUrl(url) {
    if (!url || typeof url !== 'string') return false;
    try {
        const parsed = new URL(url, window.location.origin);
        return parsed.protocol === 'http:' || parsed.protocol === 'https:';
    } catch (_) {
        return false;
    }
}

function setPreviewState(status, redirectUrl = null) {
    previewStatus.className = 'status-chip ' + status;

    if (status === 'approved' || status === 'auto') {
        previewStatus.textContent = status === 'auto' ? 'Auto-approved' : 'Approved';
        previewTitle.textContent  = APP_NAME + ' is ready';
        previewText.textContent   = 'This SecureTS gate visit is approved. Launch the external application below or wait for automatic redirection.';
        previewPlaceholder.classList.add('hidden');
        verifiedLaunchCard.classList.remove('hidden');
        previewLink.classList.remove('hidden');
        if (isSafeHttpUrl(redirectUrl)) {
            launchAppDomain.textContent = redirectUrl;
            launchContinueBtn.href = redirectUrl;
            previewLink.href = redirectUrl;
            startAutoRedirect(redirectUrl);
        }
        return;
    }

    clearAutoRedirect();
    verifiedLaunchCard.classList.add('hidden');
    previewLink.classList.add('hidden');

    if (status === 'declined' || status === 'expired') {
        previewStatus.textContent        = status === 'declined' ? 'Declined' : 'Timed out';
        previewTitle.textContent         = status === 'declined' ? 'Login declined' : 'Request expired';
        previewText.textContent          = status === 'declined'
            ? 'SecureTS blocked this attempt, so the protected app stays unavailable on the web side.'
            : 'No response was received within the time limit. Please start a new login attempt.';
        previewPlaceholderTitle.textContent = status === 'declined' ? 'Access denied' : 'Session expired';
        previewPlaceholderText.textContent  = status === 'declined'
            ? 'This login request was declined from the mobile app. Start a new login attempt if you still need to open ' + APP_NAME + '.'
            : 'The approval window has closed. Return to the gate page to try again.';
        previewPlaceholder.classList.remove('hidden');
        return;
    }

    previewStatus.textContent        = status === 'pending' ? 'Waiting for approval' : 'Awaiting action';
    previewTitle.textContent         = 'Secure launch area';
    previewText.textContent          = 'The protected app will open here after SecureTS finishes the current login check.';
    previewPlaceholderTitle.textContent = status === 'pending' ? 'Waiting for approval' : 'Waiting for a decision';
    previewPlaceholderText.textContent  = 'Approve the request from the mobile app to load ' + APP_NAME + ' beside this login page, or decline it to keep the app blocked.';
    previewPlaceholder.classList.remove('hidden');
}

async function attempt(body) {
    const res = await fetch(ATTEMPT_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(body),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || Object.values(data.errors || {}).flat()[0] || 'Request failed');
    return data;
}

function finish(data) {
    const doneLink = document.getElementById('doneLink');

    if (data.status === 'auto' || data.status === 'approved') {
        // Extension flow: keep this tab and continue straight to the target site.
        if (EXT && isSafeHttpUrl(data.redirect_url)) {
            window.location.replace(data.redirect_url);
            return;
        }
        document.getElementById('doneTitle').textContent = 'Approved';
        document.getElementById('doneText').textContent  = 'This SecureTS gate visit is approved. SecureTS does not sign you in to the external site; direct visits to it bypass this gate.';
        doneLink.classList.remove('hidden');
        if (isSafeHttpUrl(data.redirect_url)) doneLink.href = data.redirect_url;
        setPreviewState(data.status, data.redirect_url);
        show('donePanel');
        return;
    }
    if (data.status === 'declined') {
        document.getElementById('doneTitle').textContent = 'Declined';
        document.getElementById('doneText').textContent  = 'This login was denied from your phone and the web side stays blocked.';
        doneLink.classList.add('hidden');
        setPreviewState('declined');
        show('donePanel');
        return;
    }
    if (data.status === 'expired') {
        document.getElementById('doneTitle').textContent = 'Timed out';
        document.getElementById('doneText').textContent  = 'No response was received in time. Return to the gate page to start a new attempt.';
        doneLink.classList.add('hidden');
        setPreviewState('expired');
        show('donePanel');
        return;
    }

    // Still pending — start/continue long-polling
    setPreviewState('pending');
    show('waitPanel');
    longPoll(data.event_id);
}

// ── Long-Poll ──────────────────────────────────────────────────────────────────
// Keeps a single HTTP request open for up to 40 s.  When the server responds:
//   • resolved status → call finish()
//   • still pending   → reconnect immediately for another 40-second window
// An AbortController lets us cleanly cancel on page unload.
let activeController = null;

async function longPoll(eventId) {
    // Cancel any previous in-flight request
    activeController?.abort();
    const controller = new AbortController();
    activeController = controller;

    const waitUrl = ATTEMPT_URL.replace(/\/$/, '') + '/events/' + eventId + '/wait';

    const attemptPoll = async () => {
        if (controller.signal.aborted) return;
        try {
            const res = await fetch(waitUrl, {
                headers: { 'Accept': 'application/json' },
                signal: controller.signal,
            });

            if (!res.ok) {
                // Network hiccup — retry after 3 s
                await new Promise((r) => setTimeout(r, 3000));
                return attemptPoll();
            }

            const data = await res.json();
            document.getElementById('waitStatus').textContent = 'Status: ' + data.status;
            setPreviewState(data.status, data.redirect_url);

            if (data.status !== 'pending') {
                finish(data);   // Resolved: approved / declined / expired
            } else {
                // Server timed out (40 s) — reconnect immediately
                return attemptPoll();
            }
        } catch (err) {
            if (err.name === 'AbortError') return;  // User navigated away
            // Any other error: retry after 5 s to avoid a tight error loop
            await new Promise((r) => setTimeout(r, 5000));
            return attemptPoll();
        }
    };

    attemptPoll();
}

// Abort open long-poll connections when the user leaves the page
window.addEventListener('beforeunload', () => activeController?.abort());

// ── Event wiring ──────────────────────────────────────────────────────────────
document.getElementById('firstForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('firstBtn');
    const err = document.getElementById('firstError');
    err.textContent = '';
    btn.disabled    = true;
    try {
        const data = await attempt({});
        finish(data);
    } catch (ex) {
        err.textContent = ex.message;
        btn.disabled    = false;
    }
});

if (!IS_FIRST) {
    setPreviewState('pending');
    attempt({}).then(finish).catch((ex) => {
        document.getElementById('waitStatus').textContent = ex.message;
    });
} else if (EXT) {
    // Extension flow: the first gate visit needs no button press either.
    attempt({}).then(finish).catch((ex) => {
        document.getElementById('firstError').textContent = ex.message;
    });
} else {
    setPreviewState('idle');
}
</script>
</body>
</html>
