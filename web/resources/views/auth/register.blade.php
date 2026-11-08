<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register – SecureThreatShield</title>
    <meta name="description" content="Create a new SecureThreatShield account.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>

<body
    class="min-h-screen bg-[#0E1326] text-slate-100 antialiased selection:bg-[#00BBFF]/20 selection:text-[#00BBFF] flex flex-col items-center justify-center p-4">

    <div class="w-full max-w-sm bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">

        <!-- Top navigation & Brand -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-[#00BBFF] transition-colors focus:outline-none focus:text-[#00BBFF]"
                aria-label="Back to login">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Back
            </a>
            <img src="{{ asset('images/new-securets-logo-dark.png') }}" alt="SecureThreatShield Logo"
                class="h-7 w-auto">
        </div>

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-lg font-semibold text-white tracking-tight">Create your account</h1>
            <p class="text-xs text-slate-400 mt-1">Get started with ThreatShield developer authentication.</p>
        </div>

        <!-- Register form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Full Name -->
            <div>
                <label for="name" class="block text-xs font-medium text-slate-300 mb-1.5">Full Name</label>
                <input id="name" type="text" name="name" placeholder="Ada Lovelace" value="{{ old('name') }}"
                    autocomplete="name"
                    class="w-full bg-[#0E1326] border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-700/50' }} rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#00BBFF] focus:ring-1 focus:ring-[#00BBFF] transition">
                @error('name')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-medium text-slate-300 mb-1.5">Email Address</label>
                <input id="email" type="email" name="email" placeholder="you@company.com" value="{{ old('email') }}"
                    autocomplete="email"
                    class="w-full bg-[#0E1326] border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-700/50' }} rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#00BBFF] focus:ring-1 focus:ring-[#00BBFF] transition">
                @error('email')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-medium text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password" placeholder="••••••••"
                        autocomplete="new-password"
                        class="w-full bg-[#0E1326] border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-700/50' }} rounded-md pl-3 pr-9 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#00BBFF] focus:ring-1 focus:ring-[#00BBFF] transition">
                    <button type="button" id="togglePassword" aria-label="Toggle password visibility"
                        class="absolute inset-y-0 right-0 px-2.5 flex items-center text-slate-400 hover:text-[#00BBFF] focus:outline-none focus:text-[#00BBFF] transition-colors">
                        <svg id="eyeOpen1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg id="eyeClosed1" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Re-type Password -->
            <div>
                <label for="password_confirmation" class="block text-xs font-medium text-slate-300 mb-1.5">Confirm
                    Password</label>
                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        placeholder="••••••••" autocomplete="new-password"
                        class="w-full bg-[#0E1326] border border-gray-700/50 rounded-md pl-3 pr-9 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#00BBFF] focus:ring-1 focus:ring-[#00BBFF] transition">
                    <button type="button" id="toggleConfirm" aria-label="Toggle confirm password visibility"
                        class="absolute inset-y-0 right-0 px-2.5 flex items-center text-slate-400 hover:text-[#00BBFF] focus:outline-none focus:text-[#00BBFF] transition-colors">
                        <svg id="eyeOpen2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg id="eyeClosed2" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-sm font-medium py-2.5 px-4 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">
                Create Account
            </button>

            <!-- Divider -->
            <div class="relative flex items-center py-1">
                <div class="grow border-t border-gray-700/50"></div>
                <span class="shrink-0 px-3 text-[11px] font-mono uppercase tracking-wider text-slate-500">or</span>
                <div class="grow border-t border-gray-700/50"></div>
            </div>

            <!-- Continue with Google (UI only) -->
            <button type="button"
                class="w-full inline-flex items-center justify-center gap-2.5 bg-[#0E1326] hover:border-[#00BBFF]/60 text-slate-200 text-sm font-medium py-2.5 px-4 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF]">
                <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#EA4335"
                        d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.4 1 3.5 3.6 1.6 7.4l3.7 2.8C6.2 7.2 8.9 5 12 5z" />
                    <path fill="#4285F4"
                        d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.6l3.7 2.9c2.2-2 3.7-5 3.7-8.7z" />
                    <path fill="#FBBC05"
                        d="M5.3 14.8c-.2-.8-.4-1.6-.4-2.5s.2-1.7.4-2.5L1.6 7C.6 9 0 11.2 0 13.5s.6 4.5 1.6 6.5l3.7-2.9z" />
                    <path fill="#34A853"
                        d="M12 24c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3.1 0-5.8-2.1-6.7-5l-3.7 2.9C3.5 21.4 7.4 24 12 24z" />
                </svg>
                Continue with Google
            </button>

            <div class="pt-3 border-t border-gray-700/50 text-center">
                <span class="text-xs text-slate-400">Already have an account?</span>
                <a href="{{ route('login') }}"
                    class="text-xs font-medium text-[#00BBFF] hover:underline ml-1 focus:outline-none">Sign in</a>
            </div>
        </form>
    </div>

    <script>
        // Password field toggle
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            document.getElementById('eyeOpen1').style.display = isPass ? 'block' : 'none';
            document.getElementById('eyeClosed1').style.display = isPass ? 'none' : 'block';
        });

        // Re-type password field toggle
        document.getElementById('toggleConfirm').addEventListener('click', function () {
            const input = document.getElementById('password_confirmation');
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            document.getElementById('eyeOpen2').style.display = isPass ? 'block' : 'none';
            document.getElementById('eyeClosed2').style.display = isPass ? 'none' : 'block';
        });
    </script>

</body>

</html>