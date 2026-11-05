<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SecureTS – ThreatShield</title>
    <meta name="description" content="SecureTS ThreatShield – Multi-Factor Authentication and threat protection platform. Fast, secure, and passwordless.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-[#0E1326] text-slate-100 antialiased selection:bg-[#00BBFF]/20 selection:text-[#00BBFF]">

    {{-- ═══════════════════════════════ MAIN SITE ═══════════════════════════════ --}}
    <div id="main-site" class="min-h-screen flex flex-col bg-[#0E1326]">

        {{-- NAVBAR --}}
        <nav class="sticky top-0 z-40 h-14 px-6 bg-[#0E1326]/90 backdrop-blur border-b border-gray-700/50 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="/" class="flex items-center gap-2.5">
                    <img src="/images/securets-logo.png" alt="SecureThreatShield Logo" class="h-7 w-auto">
                </a>

                <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-400">
                    <a href="#about" class="hover:text-[#00BBFF] transition-colors">About</a>
                    <a href="#features" class="hover:text-[#00BBFF] transition-colors">Features</a>
                    <a href="{{ route('integrations') }}" class="hover:text-[#00BBFF] transition-colors">Integration Hub</a>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 bg-[#1C2236] border border-gray-700/50 rounded-md px-3 py-1.5 text-xs text-slate-400 focus-within:border-[#00BBFF] focus-within:ring-1 focus-within:ring-[#00BBFF] transition">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-500">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" placeholder="Search docs..." class="bg-transparent border-none outline-none text-slate-200 placeholder-slate-500 w-28 text-xs">
                </div>
                @auth
                    <a href="{{ route('integrations') }}" id="signInBtn" class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-3.5 py-2 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">Integration Hub</a>
                @else
                    <a href="{{ route('login') }}" id="signInBtn" class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-3.5 py-2 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">Sign In</a>
                @endauth
            </div>
        </nav>

        {{-- HERO --}}
        <main class="flex-1 flex flex-col items-center justify-center px-6 py-20">
            <section id="about" class="w-full max-w-2xl text-center">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-[#1C2236] border border-gray-700/50 text-[#00BBFF] text-xs font-mono mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#00BBFF]"></span>
                    AuthKit-Grade Passwordless &amp; MFA Infrastructure
                </div>

                <h1 class="text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight mb-4">
                    Developer-First ThreatShield Authentication
                </h1>

                <p class="text-sm sm:text-base text-slate-400 max-w-lg mx-auto leading-relaxed mb-8">
                    Drop-in multi-factor authentication, zero-credential verification, and phishing-immune session security built for modern engineering teams.
                </p>

                <div class="flex items-center justify-center gap-3 flex-wrap">
                    <a href="{{ route('integrations') }}" id="tryBtn" class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-sm font-medium px-5 py-2.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">
                        Try it now
                    </a>
                    <button type="button" id="discoverBtn" class="inline-flex items-center gap-2 bg-[#1C2236] hover:border-[#00BBFF]/60 text-slate-200 text-sm font-medium px-5 py-2.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">
                        Discover
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[#00BBFF]">
                            <path d="M12 5v14M5 12l7 7 7-7"/>
                        </svg>
                    </button>
                </div>
            </section>

            {{-- STATS BAR --}}
            <div id="features" class="w-full max-w-4xl grid grid-cols-2 sm:grid-cols-4 gap-3 mt-14">
                <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-4 text-left">
                    <div class="text-sm font-mono font-semibold text-[#00BBFF] mb-1">&lt;500ms</div>
                    <div class="text-xs text-slate-400">Auth Latency</div>
                </div>
                <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-4 text-left">
                    <div class="text-sm font-mono font-semibold text-white mb-1">0 Passwords</div>
                    <div class="text-xs text-slate-400">DB Credentials</div>
                </div>
                <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-4 text-left">
                    <div class="text-sm font-mono font-semibold text-white mb-1">100%</div>
                    <div class="text-xs text-slate-400">No Payment Method</div>
                </div>
                <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-4 text-left">
                    <div class="text-sm font-mono font-semibold text-[#00BBFF] mb-1">Protected</div>
                    <div class="text-xs text-slate-400">Phishing immune</div>
                </div>
            </div>

            {{-- 2FA FEATURE CARDS SECTION --}}
            <section class="w-full max-w-4xl mt-16">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 gap-2">
                    <div>
                        <span class="text-xs font-mono uppercase tracking-wider text-[#00BBFF]">2FA Primitives</span>
                        <h2 class="text-xl font-semibold text-white tracking-tight mt-1">Built for High-Assurance Verification</h2>
                    </div>
                    <p class="text-xs text-slate-400 max-w-sm">
                        Modular two-factor authentication controls designed to protect sensitive routes and external integrations.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Feature 1 --}}
                    <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-5 flex flex-col justify-between">
                        <div>
                            <div class="w-8 h-8 rounded-md bg-[#0E1326] border border-gray-700/50 flex items-center justify-center text-[#00BBFF] mb-4">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-white mb-1.5">Mobile Push Approvals</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Approve or deny sign-in attempts in real time from the ThreatShield mobile authenticator with instant long-polling synchronization.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-700/50 flex items-center justify-between text-[11px] font-mono text-slate-400">
                            <span>Out-of-Band 2FA</span>
                            <span class="text-[#00BBFF]">Active</span>
                        </div>
                    </div>

                    {{-- Feature 2 --}}
                    <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-5 flex flex-col justify-between">
                        <div>
                            <div class="w-8 h-8 rounded-md bg-[#0E1326] border border-gray-700/50 flex items-center justify-center text-[#00BBFF] mb-4">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-white mb-1.5">RFC 6238 TOTP &amp; Gate Tokens</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Rotating 30-second one-time passcodes paired with isolated public gate tokens so raw TOTP secrets never leave the server.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-700/50 flex items-center justify-between text-[11px] font-mono text-slate-400">
                            <span>Zero-Secret Exposure</span>
                            <span class="text-[#00BBFF]">SHA-1 / 6-Digit</span>
                        </div>
                    </div>

                    {{-- Feature 3 --}}
                    <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-5 flex flex-col justify-between">
                        <div>
                            <div class="w-8 h-8 rounded-md bg-[#0E1326] border border-gray-700/50 flex items-center justify-center text-[#00BBFF] mb-4">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-white mb-1.5">Threat &amp; Session Telemetry</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Full audit trails of every gate challenge, IP origin, and device fingerprint with instant lockouts on declined login events.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-700/50 flex items-center justify-between text-[11px] font-mono text-slate-400">
                            <span>Audit Log</span>
                            <span class="text-[#00BBFF]">Real-Time</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- DEVELOPER WORKFLOW / AUTHKIT PREVIEW SECTION --}}
            <section class="w-full max-w-4xl mt-12">
                <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div>
                        <span class="text-xs font-mono uppercase tracking-wider text-[#00BBFF]">Hosted Gate Architecture</span>
                        <h3 class="text-lg font-semibold text-white tracking-tight mt-1 mb-2">
                            Protect Any App in Three Steps
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Link your application in the Integration Hub, share the hosted gate endpoint, and verify multi-factor challenges without writing custom cryptography.
                        </p>
                        <ul class="space-y-2 text-xs text-slate-300">
                            <li class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded bg-[#0E1326] border border-gray-700/50 text-[#00BBFF] font-mono text-[10px] flex items-center justify-center">1</span>
                                Register an integration to generate a scoped <code class="font-mono text-[#00BBFF]">gate_token</code>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded bg-[#0E1326] border border-gray-700/50 text-[#00BBFF] font-mono text-[10px] flex items-center justify-center">2</span>
                                Route users through <code class="font-mono text-[#00BBFF]">/i/{gate_token}</code> for 2FA verification
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded bg-[#0E1326] border border-gray-700/50 text-[#00BBFF] font-mono text-[10px] flex items-center justify-center">3</span>
                                Approve via mobile push or 6-digit TOTP code
                            </li>
                        </ul>
                    </div>

                    <div class="bg-[#0E1326] border border-gray-700/50 rounded-md p-4 font-mono text-xs text-slate-300 space-y-2">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-700/50 text-[11px] text-slate-500">
                            <span>POST /i/{gate_token}</span>
                            <span class="text-[#00BBFF]">200 OK</span>
                        </div>
                        <pre class="overflow-x-auto text-[11px] leading-relaxed text-slate-300"><code>{
  "integration": "prod-dashboard",
  "challenge": "totp_or_push",
  "status": "approved",
  "latency_ms": 142,
  "verified_by": "ThreatShield Mobile"
}</code></pre>
                    </div>
                </div>
            </section>
        </main>

        {{-- FOOTER --}}
        <footer class="border-t border-gray-700/50 py-5 px-6 text-center text-xs text-slate-500">
            <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <span>SecureTS ThreatShield &mdash; Developer 2FA &amp; Session Protection</span>
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-slate-400 hover:text-[#00BBFF] transition-colors">Sign In</a>
                    <a href="{{ route('register') }}" class="text-slate-400 hover:text-[#00BBFF] transition-colors">Create Account</a>
                </div>
            </div>
        </footer>

    </div>

    <script>
        document.getElementById('discoverBtn').addEventListener('click', function () {
            document.getElementById('features').scrollIntoView({ behavior: 'smooth' });
        });
    </script>

</body>
</html>
