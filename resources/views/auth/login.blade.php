<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login – SecureThreatShield</title>
    <meta name="description" content="Log in to your SecureThreatShield account.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #0F172A;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* ── Card wrapper ── */
        .card {
            width: 100%;
            max-width: 800px;
            min-height: 480px;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }

        /* ── Left blue panel ── */
        .panel-left {
            flex: 1;
            background: #3D5AF1;
            padding: 40px 44px;
            display: flex;
            flex-direction: column;
        }

        /* Back button – small circle above logo */
        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            margin-bottom: 14px;
            transition: background 0.2s, color 0.2s;
        }
        .back-btn:hover { background: rgba(255,255,255,0.22); color: #fff; }
        /* ── Logo ── */
        .logo {
            margin-bottom: 36px;
        }
        .logo img { height: 44px; width: auto; }

        /* ── Headings ── */
        .welcome-title {
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }
        .welcome-sub {
            font-size: 14px;
            font-weight: 500;
            color: rgba(255,255,255,0.8);
            margin-bottom: 28px;
        }

        /* ── Form ── */
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: rgba(255,255,255,0.7);
            margin-bottom: 5px;
        }
        /* Inner wrapper gives the eye button a clean anchor point */
        .input-wrap {
            position: relative;
        }
        .form-group input {
            width: 100%;
            background: rgba(255,255,255,0.15);
            border: 1.5px solid rgba(255,255,255,0.25);
            border-radius: 8px;
            padding: 12px 44px 12px 14px;
            font-size: 14px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }
        .form-group input::placeholder { color: rgba(255,255,255,0.45); }
        .form-group input:focus {
            border-color: rgba(255,255,255,0.6);
            background: rgba(255,255,255,0.22);
        }
        .form-group input.input-error { border-color: #FC8181; }

        /* ── Eye toggle – centred on the input, not the whole form-group ── */
        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: rgba(255,255,255,0.6);
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .eye-btn:hover { color: #fff; }

        /* ── Forgot password ── */
        .forgot-row {
            display: flex;
            justify-content: flex-end;
            margin-top: -8px;
            margin-bottom: 24px;
        }
        .forgot-link {
            font-size: 12px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #fff; text-decoration: underline; }
        .session-note {
            font-size: 12px;
            color: rgba(255,255,255,0.72);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* ── Buttons ── */
        .btn-row { display: flex; gap: 12px; }
        .btn-login {
            flex: 1;
            background: #1A2FA8;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 13px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-login:hover { background: #1628a0; transform: translateY(-1px); }
        .btn-create {
            flex: 1;
            background: transparent;
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.5);
            border-radius: 8px;
            padding: 13px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background 0.2s, border-color 0.2s, transform 0.15s;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-create:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.8);
            transform: translateY(-1px);
        }

        /* ── Error messages ── */
        .error-msg {
            font-size: 12px;
            color: #FC8181;
            margin-top: 4px;
        }
        .alert-success {
            background: rgba(34,197,94,0.2);
            border: 1px solid rgba(34,197,94,0.5);
            border-radius: 8px;
            color: #86EFAC;
            font-size: 13px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }

        /* ── Right illustration panel ── */
        .panel-right {
            width: 280px;
            background: #0F172A;
            display: flex;
            align-items: stretch;
            flex-shrink: 0;
            overflow: hidden;
        }
        .panel-right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .panel-right { display: none; }
            .panel-left  { padding: 32px 28px; }
        }
    </style>
</head>
<body>

<div class="card">
    <!-- ═══ LEFT BLUE PANEL ═══ -->
    <div class="panel-left">

        <!-- Back button (icon only) – above logo -->
        <a href="/" class="back-btn" aria-label="Go to home">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
        </a>

        <!-- Logo -->
        <div class="logo">
            <img src="{{ asset('images/securets-logo.png') }}" alt="SecureThreatShield Logo">
        </div>

        <!-- Welcome text -->
        <h1 class="welcome-title">Welcome Back!</h1>
        <p class="welcome-sub">Please Log in to your account.</p>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <!-- Login form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="Email Address"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    class="{{ $errors->has('email') ? 'input-error' : '' }}"
                >
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Password"
                        autocomplete="current-password"
                        class="{{ $errors->has('password') ? 'input-error' : '' }}"
                    >
                    <!-- Eye toggle button -->
                    <button type="button" class="eye-btn" id="togglePassword" aria-label="Toggle password visibility">
                        <!-- Eye icon (visible state) -->
                        <svg id="eyeOpen" width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             style="display:none">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <!-- Eye-off icon (hidden state) -->
                        <svg id="eyeClosed" width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <!-- Forgot password -->
            <div class="forgot-row">
                <a href="#" class="forgot-link">Forgot Password?</a>
            </div>

            <p class="session-note">
                For security, this account stays signed in for up to {{ config('session.lifetime') }} minutes after the browser closes.
            </p>

            <!-- Buttons -->
            <div class="btn-row">
                <button type="submit" class="btn-login">Login</button>
                <a href="{{ route('register') }}" class="btn-create">Create Account</a>
            </div>

        </form>
    </div>

    <!-- ═══ RIGHT ILLUSTRATION PANEL ═══ -->
    <div class="panel-right">
        <img src="{{ asset('images/security-illustration.png') }}" alt="Cybersecurity illustration">
    </div>
</div>

<script>
    const toggleBtn   = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeOpen     = document.getElementById('eyeOpen');
    const eyeClosed   = document.getElementById('eyeClosed');

    toggleBtn.addEventListener('click', function () {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        eyeOpen.style.display   = isPassword ? 'block' : 'none';
        eyeClosed.style.display = isPassword ? 'none'  : 'block';
    });
</script>

</body>
</html>
