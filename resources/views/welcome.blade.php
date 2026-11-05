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
                    <img src="{{ asset('images/new-securets-logo-dark.png') }}" alt="SecureThreatShield Logo" class="h-7 w-auto">
                </a>

                <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-400">
                    <a href="#about" class="hover:text-[#00BBFF] transition-colors">About</a>
                    <a href="#features" class="hover:text-[#00BBFF] transition-colors">Features</a>
                    <a href="#demo" class="hover:text-[#00BBFF] transition-colors">Live Demo</a>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 bg-[#1C2236] border border-gray-700/50 rounded-md px-3 py-1.5 text-xs text-slate-400 focus-within:border-[#00BBFF] focus-within:ring-1 focus-within:ring-[#00BBFF] transition">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-500">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" placeholder="Search docs..." class="bg-transparent border-none outline-none text-slate-200 placeholder-slate-500 w-28 text-xs">
                </div>
                <a href="{{ route('login') }}" id="signInBtn" class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-3.5 py-2 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">Sign In</a>
            </div>
        </nav>

        {{-- HERO & ABOUT --}}
        <main class="flex-1 flex flex-col items-center justify-center px-6 py-20">
            <section id="about" class="w-full max-w-3xl text-center">
                <h1 class="text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight mb-4">
                    Developer-First ThreatShield Authentication
                </h1>

                <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed mb-4">
                    SecureTS ThreatShield is a modern multi-factor authentication and session defense platform engineered for high-assurance applications. Protect sensitive endpoints, administrative dashboards, and user workflows with drop-in verification gates that eliminate credential theft and session hijacking.
                </p>

                <p class="text-xs sm:text-sm text-slate-400 max-w-xl mx-auto leading-relaxed mb-8">
                    Built for developers who need real-time out-of-band mobile push approvals, RFC-compliant TOTP verification, and granular threat telemetry without maintaining complex cryptographic infrastructure.
                </p>

                <div class="flex items-center justify-center gap-3 flex-wrap">
                    <a href="#demo" id="tryBtn" class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-sm font-medium px-5 py-2.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">
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
                    <div class="text-sm font-mono font-semibold text-[#00BBFF] mb-1">&lt; 500ms</div>
                    <div class="text-xs text-slate-400">Global Verification Latency</div>
                </div>
                <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-4 text-left">
                    <div class="text-sm font-mono font-semibold text-white mb-1">Zero-Secret</div>
                    <div class="text-xs text-slate-400">Isolated Gate Token Architecture</div>
                </div>
                <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-4 text-left">
                    <div class="text-sm font-mono font-semibold text-white mb-1">99.99%</div>
                    <div class="text-xs text-slate-400">Challenge Delivery Uptime</div>
                </div>
                <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-4 text-left">
                    <div class="text-sm font-mono font-semibold text-[#00BBFF] mb-1">Phishing-Immune</div>
                    <div class="text-xs text-slate-400">Out-of-Band Session Defense</div>
                </div>
            </div>

            {{-- 2FA FEATURE CARDS SECTION --}}
            <section class="w-full max-w-4xl mt-16">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 gap-2">
                    <div>
                        <span class="text-xs font-mono uppercase tracking-wider text-[#00BBFF]">Core Capabilities</span>
                        <h2 class="text-xl font-semibold text-white tracking-tight mt-1">Built for High-Assurance Verification</h2>
                    </div>
                    <p class="text-xs text-slate-400 max-w-md">
                        Comprehensive two-factor authentication primitives, automated anomaly detection, and tamper-evident session controls designed to safeguard every authentication request.
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
                                Approve or deny sign-in attempts in real time from the ThreatShield mobile authenticator with contextual request metadata and instant synchronization.
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
                                Rotating 30-second one-time passcodes paired with isolated public gate tokens and replay protection so raw cryptographic secrets never leave the server.
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
                                Granular audit trails of every gate challenge, IP origin, and device fingerprint with automated session revocation on suspicious or declined login events.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-700/50 flex items-center justify-between text-[11px] font-mono text-slate-400">
                            <span>Audit Log</span>
                            <span class="text-[#00BBFF]">Real-Time</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- STATIC UI DEMO SECTION --}}
            <section id="demo" class="w-full max-w-4xl mt-12">
                <div class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div>
                        <span class="text-xs font-mono uppercase tracking-wider text-[#00BBFF]">Interactive Gate Preview</span>
                        <h3 class="text-lg font-semibold text-white tracking-tight mt-1 mb-2">
                            Experience Seamless 2FA Verification
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            See how ThreatShield intercepts sensitive actions with a clean, low-friction verification prompt—supporting both instant mobile push approvals and 6-digit TOTP codes.
                        </p>
                        <div class="space-y-2 text-xs text-slate-300">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span>Push notification dispatched to registered device</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#00BBFF]"></span>
                                <span>Fallback 6-digit TOTP code verification ready</span>
                            </div>
                        </div>
                    </div>

                    {{-- Static UI Demo Card --}}
                    <div class="bg-[#0E1326] border border-gray-700/50 rounded-lg p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-700/50 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#00BBFF]"></span>
                                <span class="text-xs font-semibold text-white">Security Challenge</span>
                            </div>
                            <span class="text-[11px] font-mono text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded">Awaiting Approval</span>
                        </div>

                        <div class="space-y-2">
                            <div class="text-xs text-slate-400">Enter 6-digit Authenticator Code</div>
                            <div class="grid grid-cols-6 gap-2 font-mono text-sm text-center">
                                <div class="bg-[#1C2236] border border-[#00BBFF] rounded py-2 text-white font-semibold">4</div>
                                <div class="bg-[#1C2236] border border-[#00BBFF] rounded py-2 text-white font-semibold">8</div>
                                <div class="bg-[#1C2236] border border-[#00BBFF] rounded py-2 text-white font-semibold">2</div>
                                <div class="bg-[#1C2236] border border-gray-700/50 rounded py-2 text-slate-500">&bull;</div>
                                <div class="bg-[#1C2236] border border-gray-700/50 rounded py-2 text-slate-500">&bull;</div>
                                <div class="bg-[#1C2236] border border-gray-700/50 rounded py-2 text-slate-500">&bull;</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[11px] font-mono text-slate-500">IP: 192.0.2.14 &bull; TLS 1.3</span>
                            <span class="bg-[#4063D7]/80 text-white text-xs font-medium px-3 py-1.5 rounded-md border border-gray-700/50 cursor-default">
                                Verify Session
                            </span>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        {{-- FOOTER --}}
        <footer class="border-t border-gray-700/50 bg-[#0E1326] py-10 px-6 text-xs text-slate-400">
            <div class="max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-4 gap-8 mb-8">
                {{-- Brand & About --}}
                <div class="sm:col-span-2 space-y-2.5">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/new-securets-logo-dark.png') }}" alt="SecureThreatShield Logo" class="h-6 w-auto">
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-xs">
                        SecureTS ThreatShield delivers developer-first multi-factor authentication, real-time push approvals, and resilient session protection.
                    </p>
                </div>

                {{-- Company --}}
                <div class="space-y-2">
                    <h4 class="text-xs font-semibold text-white uppercase tracking-wider">Company</h4>
                    <ul class="space-y-1.5 text-slate-400">
                        <li><a href="#about" class="hover:text-[#00BBFF] transition-colors">About Us</a></li>
                        <li><a href="#features" class="hover:text-[#00BBFF] transition-colors">Security Architecture</a></li>
                        <li><a href="#demo" class="hover:text-[#00BBFF] transition-colors">Product Demo</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div class="space-y-2">
                    <h4 class="text-xs font-semibold text-white uppercase tracking-wider">Contact</h4>
                    <ul class="space-y-1.5 text-slate-400">
                        <li><a href="mailto:support@securets.io" class="hover:text-[#00BBFF] transition-colors">support@securets.io</a></li>
                        <li><a href="mailto:security@securets.io" class="hover:text-[#00BBFF] transition-colors">security@securets.io</a></li>
                        <li class="text-slate-500">San Francisco, CA</li>
                    </ul>
                </div>
            </div>

            <div class="max-w-4xl mx-auto pt-6 border-t border-gray-700/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-slate-500">&copy; {{ date('Y') }} SecureTS ThreatShield. All rights reserved.</span>

                {{-- Placeholder Social Icons --}}
                <div class="flex items-center gap-4 text-slate-400">
                    <a href="#" aria-label="GitHub" class="hover:text-[#00BBFF] transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/>
                            <path d="M9 18c-4.51 2-5-2-7-2"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="X (Twitter)" class="hover:text-[#00BBFF] transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4l11.733 16h4.267l-11.733 -16z"/>
                            <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="LinkedIn" class="hover:text-[#00BBFF] transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                            <rect x="2" y="9" width="4" height="12"/>
                            <circle cx="4" cy="4" r="2"/>
                        </svg>
                    </a>
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
