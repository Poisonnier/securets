<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard – SecureThreatShield')</title>
    <meta name="description" content="@yield('description', 'SecureThreatShield developer authentication workspace.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen bg-[#0E1326] text-slate-100 antialiased selection:bg-[#00BBFF]/20 selection:text-[#00BBFF] flex flex-col">

    {{-- Top Minimalist Navigation --}}
    <header class="sticky top-0 z-40 h-14 px-6 bg-[#1C2236] border-b border-gray-700/50 flex items-center justify-between">
        <div class="flex items-center gap-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0 focus:outline-none" aria-label="SecureThreatShield home">
                <img src="{{ asset('images/new-securets-logo-dark.png') }}" alt="SecureThreatShield" class="h-7 w-auto">
            </a>

            <nav class="flex items-center gap-1" aria-label="Primary navigation">
                <a href="{{ route('dashboard') }}"
                   class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors {{ ($activeNav ?? '') === 'dashboard' || request()->routeIs('dashboard') ? 'bg-[#4063D7]/20 text-[#00BBFF] border border-[#4063D7]/40' : 'text-slate-400 hover:text-slate-100 hover:bg-[#0E1326]/60 border border-transparent' }}">
                    Dashboard
                </a>
                <a href="{{ route('integrations') }}"
                   class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors {{ ($activeNav ?? '') === 'integrations' || ($activeNav ?? '') === 'apps' || request()->routeIs('integrations*') ? 'bg-[#4063D7]/20 text-[#00BBFF] border border-[#4063D7]/40' : 'text-slate-400 hover:text-slate-100 hover:bg-[#0E1326]/60 border border-transparent' }}">
                    My Apps
                </a>
                <a href="{{ route('subscription') }}"
                   class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors {{ ($activeNav ?? '') === 'subscription' || request()->routeIs('subscription*') ? 'bg-[#4063D7]/20 text-[#00BBFF] border border-[#4063D7]/40' : 'text-slate-400 hover:text-slate-100 hover:bg-[#0E1326]/60 border border-transparent' }}">
                    Subscription
                </a>
            </nav>
        </div>

        <div class="relative flex items-center gap-3">
            <button type="button" id="profile-trigger" aria-label="Open profile" aria-expanded="false"
                class="w-8 h-8 rounded-md bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-semibold flex items-center justify-center border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                {{ Auth::user()->initials() }}
            </button>

            {{-- Account Dropdown --}}
            <aside id="account-menu" aria-label="Account menu" aria-hidden="true"
                class="hidden absolute right-0 top-11 z-50 w-64 bg-[#1C2236] border border-gray-700/50 rounded-lg p-3">
                <div class="pb-2.5 mb-2.5 border-b border-gray-700/50 px-1">
                    <strong class="block text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</strong>
                    <span class="block text-[11px] font-mono text-slate-400 truncate mt-0.5">{{ Auth::user()->email }}</span>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('profile') }}"
                       class="flex items-center justify-between w-full px-2.5 py-2 text-xs font-medium text-slate-200 hover:text-white hover:bg-[#0E1326] rounded-md border border-transparent hover:border-gray-700/50 transition-colors">
                        <span>Profile details</span>
                        <span aria-hidden="true" class="text-slate-500">›</span>
                    </a>
                    <a href="{{ route('settings') }}"
                       class="flex items-center justify-between w-full px-2.5 py-2 text-xs font-medium text-slate-200 hover:text-white hover:bg-[#0E1326] rounded-md border border-transparent hover:border-gray-700/50 transition-colors">
                        <span>Settings</span>
                        <span aria-hidden="true" class="text-slate-500">›</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="pt-1">
                        @csrf
                        <button type="submit"
                            class="flex items-center justify-center w-full px-2.5 py-2 text-xs font-medium text-red-300 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 rounded-md transition-colors cursor-pointer">
                            Sign out
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </header>

    {{-- Main Workspace Content --}}
    <main class="flex-1 w-full max-w-5xl mx-auto px-6 py-10">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <script>
        (() => {
            const trigger = document.getElementById('profile-trigger');
            const menu = document.getElementById('account-menu');
            if (!trigger || !menu) return;

            const closeMenu = () => {
                menu.classList.add('hidden');
                menu.setAttribute('aria-hidden', 'true');
                trigger.setAttribute('aria-expanded', 'false');
            };

            trigger.addEventListener('click', () => {
                const isHidden = menu.classList.toggle('hidden');
                menu.setAttribute('aria-hidden', String(isHidden));
                trigger.setAttribute('aria-expanded', String(!isHidden));
            });

            document.addEventListener('click', (event) => {
                if (!menu.contains(event.target) && !trigger.contains(event.target)) closeMenu();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeMenu();
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
