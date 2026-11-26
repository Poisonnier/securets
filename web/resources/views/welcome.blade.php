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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <a href="#pricing" class="hover:text-[#00BBFF] transition-colors">Pricing</a>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" id="signInBtn" class="text-xs font-medium text-slate-300 hover:text-[#00BBFF] transition-colors focus:outline-none">Log in</a>
                @guest
                    <a href="{{ route('register') }}" id="signUpBtn" class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-3.5 py-2 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">Get Started</a>
                @endguest
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

            {{-- INTERACTIVE GATE PREVIEW SECTION --}}
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

                    {{-- Interactive Sandbox Card --}}
                    <div class="bg-[#0E1326] border border-gray-700/50 rounded-lg p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-700/50 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#00BBFF]"></span>
                                <span class="text-xs font-semibold text-white">Security Challenge</span>
                            </div>
                            <span id="demoStatusBadge" class="text-[11px] font-mono text-[#00BBFF] bg-[#1C2236] border border-gray-700/50 px-2 py-0.5 rounded">Awaiting Approval</span>
                        </div>

                        {{-- Mode Switcher --}}
                        <div id="demoModeTabs" class="grid grid-cols-2 gap-1.5 bg-[#1C2236] border border-gray-700/50 rounded-md p-1">
                            <button type="button" id="tabTotpBtn" class="text-xs font-medium py-1.5 px-2.5 rounded bg-[#4063D7] text-white transition-colors focus:outline-none">
                                6-Digit TOTP
                            </button>
                            <button type="button" id="tabPushBtn" class="text-xs font-medium py-1.5 px-2.5 rounded text-slate-400 hover:text-white transition-colors focus:outline-none">
                                Mobile Push
                            </button>
                        </div>

                        {{-- TOTP Verification Flow --}}
                        <div id="demoTotpPanel" class="space-y-3">
                            <div class="bg-[#1C2236] border border-gray-700/50 rounded-md p-2.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">Active Demo TOTP:</span>
                                    <div class="flex items-center gap-2">
                                        <span id="demoLiveCode" class="font-mono font-semibold text-white tracking-widest">482915</span>
                                        <button type="button" id="autofillCodeBtn" class="text-[11px] font-mono text-[#00BBFF] hover:underline focus:outline-none">
                                            Autofill
                                        </button>
                                    </div>
                                </div>
                                <div class="w-full h-1 bg-[#0E1326] rounded overflow-hidden mt-2">
                                    <div id="demoCountdownBar" class="h-full bg-[#00BBFF] transition-all duration-1000" style="width: 100%;"></div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="totpDigit0" class="text-xs text-slate-400">Enter 6-digit Authenticator Code</label>
                                    <span id="demoTimerLabel" class="text-[11px] font-mono text-slate-500">30s</span>
                                </div>
                                <div class="grid grid-cols-6 gap-2 font-mono text-sm text-center">
                                    @for ($i = 0; $i < 6; $i++)
                                        <input type="text" id="totpDigit{{ $i }}" maxlength="1" inputmode="numeric" autocomplete="off"
                                            data-index="{{ $i }}"
                                            aria-label="Digit {{ $i + 1 }}"
                                            class="demo-totp-input w-full bg-[#1C2236] border border-gray-700/50 focus:border-[#00BBFF] rounded py-2 text-center text-white font-semibold focus:outline-none transition-colors">
                                    @endfor
                                </div>
                                <p id="demoErrorText" hidden class="text-[11px] font-mono text-slate-300 pt-1">
                                    Invalid code. Please match the active 6-digit TOTP above.
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <span class="text-[11px] font-mono text-slate-500">IP: 192.0.2.14 &bull; TLS 1.3</span>
                                <button type="button" id="verifySessionBtn" class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-3.5 py-1.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                                    Verify Session
                                </button>
                            </div>
                        </div>

                        {{-- Simulated Mobile Push Flow --}}
                        <div id="demoPushPanel" hidden class="space-y-3">
                            <div class="bg-[#1C2236] border border-gray-700/50 rounded-md p-3.5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-white">ThreatShield Mobile Prompt</span>
                                    <span id="pushSubStatus" class="text-[11px] font-mono text-[#00BBFF]">Dispatched</span>
                                </div>
                                <p id="pushPromptDesc" class="text-xs text-slate-400 leading-relaxed">
                                    Sign-in challenge sent to your paired iOS / Android device. Approve the request below to simulate out-of-band mobile confirmation.
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[11px] font-mono text-slate-500">Device: iPhone 15 Pro</span>
                                <button type="button" id="simulatePushApproveBtn" class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-3.5 py-1.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                                    Approve on Phone
                                </button>
                            </div>
                        </div>

                        {{-- Verified State Panel --}}
                        <div id="demoVerifiedPanel" hidden class="space-y-3 pt-1">
                            <div class="bg-[#1C2236] border border-[#00BBFF]/50 rounded-md p-4 text-center space-y-1.5">
                                <div class="text-xs font-mono uppercase tracking-wider text-[#00BBFF]">Session Verified</div>
                                <p id="demoVerifiedDetail" class="text-xs text-slate-300">
                                    Cryptographic challenge passed. Target application gate unlocked.
                                </p>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[11px] font-mono text-slate-500">Status: Authenticated</span>
                                <button type="button" id="resetDemoBtn" class="bg-[#1C2236] hover:border-[#00BBFF]/60 text-slate-200 text-xs font-medium px-3.5 py-1.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                                    Reset Demo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- PRICING SECTION --}}
            @php
                $pricingPlans = config('stripe.plans');
            @endphp
            <section id="pricing" class="w-full max-w-4xl mt-16">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 gap-2">
                    <div>
                        <span class="text-xs font-mono uppercase tracking-wider text-[#00BBFF]">Workspace Billing</span>
                        <h2 class="text-xl font-semibold text-white tracking-tight mt-1">Subscription Plans</h2>
                    </div>
                    <p class="text-xs text-slate-400 max-w-md">
                        Scale your application gate capacity and authentication infrastructure tier from prototype to enterprise.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($pricingPlans as $key => $plan)
                        @php
                            $isFree = $key === 'free';
                            $isRecommended = $key === 'pro';
                            $limitLabel = $plan['limit'] ? $plan['limit'] . ' connected applications' : 'Unlimited connected applications';
                            $priceValue = $isFree ? '$0' : explode(' ', $plan['price_label'])[0];
                        @endphp

                        <div class="bg-[#1C2236] border {{ $isRecommended ? 'border-[#4063D7]' : 'border-gray-700/50' }} rounded-lg p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <h3 class="text-sm font-semibold text-white">{{ $plan['label'] }}</h3>
                                    @if ($isRecommended)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono text-white bg-[#4063D7] border border-gray-700/50">
                                            Recommended
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-baseline gap-1 mb-1">
                                    <span class="text-2xl font-mono font-semibold text-white">{{ $priceValue }}</span>
                                    <span class="text-xs text-slate-400">/ month</span>
                                </div>
                                <p class="text-xs font-mono text-slate-400 pb-5 mb-5 border-b border-gray-700/50">
                                    {{ $limitLabel }}
                                </p>

                                <ul class="space-y-2.5 mb-6 text-xs text-slate-400">
                                    <li class="flex items-start gap-2">
                                        <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                        <span>{{ $limitLabel }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                        <span>RFC-6238 TOTP Authenticator</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                        <span>Out-of-band Push Approvals</span>
                                    </li>
                                    @if (!$isFree)
                                        <li class="flex items-start gap-2">
                                            <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                            <span>Priority developer support</span>
                                        </li>
                                    @endif
                                    @if (in_array($key, ['ultra', 'ultra_plus']))
                                        <li class="flex items-start gap-2">
                                            <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                            <span>Advanced login telemetry</span>
                                        </li>
                                    @endif
                                    @if ($key === 'ultra_plus')
                                        <li class="flex items-start gap-2">
                                            <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                            <span>API access &amp; event webhooks</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                            <span>Dedicated account engineer</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <div class="pt-4 border-t border-gray-700/50">
                                @auth
                                    <a href="{{ route('subscription') }}"
                                        class="block w-full text-center py-2 px-3 rounded-md text-xs font-medium {{ $isRecommended ? 'bg-[#4063D7] hover:bg-[#4063D7]/90 text-white' : 'bg-[#0E1326] hover:border-[#00BBFF]/60 text-slate-200' }} border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">
                                        {{ $isFree ? 'Manage Plan' : 'Upgrade to ' . $plan['label'] }}
                                    </a>
                                @else
                                    <a href="{{ route('register') }}"
                                        class="block w-full text-center py-2 px-3 rounded-md text-xs font-medium {{ $isRecommended ? 'bg-[#4063D7] hover:bg-[#4063D7]/90 text-white' : 'bg-[#0E1326] hover:border-[#00BBFF]/60 text-slate-200' }} border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">
                                        {{ $isFree ? 'Get Started Free' : 'Choose ' . $plan['label'] }}
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @endforeach
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

        (function () {
            const inputs = Array.from(document.querySelectorAll('.demo-totp-input'));
            const liveCodeEl = document.getElementById('demoLiveCode');
            const autofillBtn = document.getElementById('autofillCodeBtn');
            const countdownBar = document.getElementById('demoCountdownBar');
            const timerLabel = document.getElementById('demoTimerLabel');
            const errorText = document.getElementById('demoErrorText');
            const verifyBtn = document.getElementById('verifySessionBtn');
            const statusBadge = document.getElementById('demoStatusBadge');
            const tabTotpBtn = document.getElementById('tabTotpBtn');
            const tabPushBtn = document.getElementById('tabPushBtn');
            const totpPanel = document.getElementById('demoTotpPanel');
            const pushPanel = document.getElementById('demoPushPanel');
            const verifiedPanel = document.getElementById('demoVerifiedPanel');
            const verifiedDetail = document.getElementById('demoVerifiedDetail');
            const modeTabs = document.getElementById('demoModeTabs');
            const approvePushBtn = document.getElementById('simulatePushApproveBtn');
            const resetDemoBtn = document.getElementById('resetDemoBtn');

            let activeCode = '482915';
            let remainingSeconds = 30;
            let activeMode = 'totp';

            function generateCode() {
                let code = '';
                for (let i = 0; i < 6; i++) {
                    code += Math.floor(Math.random() * 10).toString();
                }
                activeCode = code;
                liveCodeEl.textContent = activeCode;
            }

            function setMode(mode) {
                activeMode = mode;
                errorText.hidden = true;
                verifiedPanel.hidden = true;
                modeTabs.hidden = false;

                if (mode === 'totp') {
                    totpPanel.hidden = false;
                    pushPanel.hidden = true;
                    tabTotpBtn.className = 'text-xs font-medium py-1.5 px-2.5 rounded bg-[#4063D7] text-white transition-colors focus:outline-none';
                    tabPushBtn.className = 'text-xs font-medium py-1.5 px-2.5 rounded text-slate-400 hover:text-white transition-colors focus:outline-none';
                    statusBadge.textContent = 'Awaiting Approval';
                } else {
                    totpPanel.hidden = true;
                    pushPanel.hidden = false;
                    tabPushBtn.className = 'text-xs font-medium py-1.5 px-2.5 rounded bg-[#4063D7] text-white transition-colors focus:outline-none';
                    tabTotpBtn.className = 'text-xs font-medium py-1.5 px-2.5 rounded text-slate-400 hover:text-white transition-colors focus:outline-none';
                    statusBadge.textContent = 'Awaiting Mobile Approval';
                }
            }

            function showVerified(message) {
                totpPanel.hidden = true;
                pushPanel.hidden = true;
                modeTabs.hidden = true;
                verifiedPanel.hidden = false;
                verifiedDetail.textContent = message;
                statusBadge.textContent = 'Session Verified';
            }

            tabTotpBtn.addEventListener('click', function () {
                setMode('totp');
            });

            tabPushBtn.addEventListener('click', function () {
                setMode('push');
            });

            autofillBtn.addEventListener('click', function () {
                errorText.hidden = true;
                activeCode.split('').forEach(function (digit, idx) {
                    if (inputs[idx]) {
                        inputs[idx].value = digit;
                    }
                });
                if (inputs[5]) {
                    inputs[5].focus();
                }
            });

            inputs.forEach(function (input, idx) {
                input.addEventListener('input', function (e) {
                    errorText.hidden = true;
                    const val = e.target.value.replace(/\D/g, '');
                    e.target.value = val ? val[0] : '';
                    if (e.target.value && idx < inputs.length - 1) {
                        inputs[idx + 1].focus();
                    }
                });

                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && !input.value && idx > 0) {
                        inputs[idx - 1].focus();
                    } else if (e.key === 'Enter') {
                        verifyBtn.click();
                    }
                });

                input.addEventListener('paste', function (e) {
                    e.preventDefault();
                    errorText.hidden = true;
                    const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                    pasted.split('').forEach(function (ch, i) {
                        if (inputs[i]) {
                            inputs[i].value = ch;
                        }
                    });
                    const targetIdx = Math.min(pasted.length, inputs.length - 1);
                    if (inputs[targetIdx]) {
                        inputs[targetIdx].focus();
                    }
                });
            });

            verifyBtn.addEventListener('click', function () {
                const entered = inputs.map(function (el) { return el.value; }).join('');
                if (entered === activeCode) {
                    errorText.hidden = true;
                    showVerified('6-digit TOTP verified against active rotating secret.');
                } else {
                    errorText.hidden = false;
                    statusBadge.textContent = 'Invalid Code';
                }
            });

            approvePushBtn.addEventListener('click', function () {
                showVerified('Out-of-band mobile push challenge approved on iPhone 15 Pro.');
            });

            resetDemoBtn.addEventListener('click', function () {
                inputs.forEach(function (el) { el.value = ''; });
                generateCode();
                remainingSeconds = 30;
                countdownBar.style.width = '100%';
                timerLabel.textContent = '30s';
                setMode(activeMode);
            });

            setInterval(function () {
                remainingSeconds -= 1;
                if (remainingSeconds <= 0) {
                    remainingSeconds = 30;
                    generateCode();
                }
                countdownBar.style.width = ((remainingSeconds / 30) * 100).toFixed(2) + '%';
                timerLabel.textContent = remainingSeconds + 's';
            }, 1000);
        })();
    </script>

</body>
</html>
