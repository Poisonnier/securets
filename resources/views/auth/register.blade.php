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

        /* ── Left grey panel ── */
        .panel-left {
            width: 280px;
            background: #D1D5DB;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .panel-left-inner {
            text-align: center;
            color: #9CA3AF;
        }
        .panel-left-inner svg { margin-bottom: 8px; }
        .panel-left-inner p { font-size: 13px; font-weight: 500; }

        /* ── Right blue panel ── */
        .panel-right {
            flex: 1;
            background: #3D5AF1;
            padding: 36px 44px;
            display: flex;
            flex-direction: column;
        }

        /* ── Logo ── */
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }
        .logo-icons {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .hexagon {
            width: 38px;
            height: 38px;
            background: #0F172A;
            clip-path: polygon(50% 0%,93% 25%,93% 75%,50% 100%,7% 75%,7% 25%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .shield-svg { width: 32px; height: 36px; }
        .logo-text { display: flex; flex-direction: column; line-height: 1; }
        .logo-name  { font-size: 18px; font-weight: 700; color: #fff; letter-spacing: -0.3px; }
        .logo-sub   { font-size: 11px; font-weight: 500; color: rgba(255,255,255,0.75); letter-spacing: 0.4px; margin-top: 2px; }

        /* ── Heading ── */
        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 22px;
        }

        /* ── Form ── */
        .form-group {
            margin-bottom: 14px;
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

        /* ── Submit button ── */
        .btn-submit {
            margin-top: 6px;
            width: 100%;
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
        .btn-submit:hover { background: #1628a0; transform: translateY(-1px); }

        /* ── Error / success messages ── */
        .error-msg {
            font-size: 12px;
            color: #FC8181;
            margin-top: 4px;
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
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }
        .logo img { height: 44px; width: auto; }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .panel-left  { display: none; }
            .panel-right { padding: 32px 28px; }
        }
    </style>
</head>
<body>

<div class="card">
    <!-- ═══ LEFT ILLUSTRATION PANEL ═══ -->
    <div class="panel-left" style="background:#0F172A;overflow:hidden;">
        <img src="{{ asset('images/security-illustration.png') }}" alt="Cybersecurity illustration" style="width:100%;height:100%;object-fit:cover;display:block;">
    </div>

    <!-- ═══ RIGHT BLUE PANEL ═══ -->
    <div class="panel-right">

        <!-- Back button (icon only) – above logo -->
        <a href="{{ route('login') }}" class="back-btn" aria-label="Back to login">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
        </a>

        <!-- Logo -->
        <div class="logo">
            <img src="{{ asset('images/securets-logo.png') }}" alt="SecureThreatShield Logo">
        </div>

        <h1 class="page-title">Create your account.</h1>

        <!-- Register form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Full Name -->
            <div class="form-group">
                <label for="name">Full Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    placeholder="Full Name"
                    value="{{ old('name') }}"
                    autocomplete="name"
                    class="{{ $errors->has('name') ? 'input-error' : '' }}"
                >
                @error('name')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
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
                        autocomplete="new-password"
                        class="{{ $errors->has('password') ? 'input-error' : '' }}"
                    >
                    <button type="button" class="eye-btn" id="togglePassword" aria-label="Toggle password visibility">
                        <svg id="eyeOpen1" width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             style="display:none">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="eyeClosed1" width="20" height="20" viewBox="0 0 24 24" fill="none"
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

            <!-- Re-type Password -->
            <div class="form-group">
                <label for="password_confirmation">Re-type Password</label>
                <div class="input-wrap">
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        placeholder="Re-type Password"
                        autocomplete="new-password"
                    >
                    <button type="button" class="eye-btn" id="toggleConfirm" aria-label="Toggle confirm password visibility">
                        <svg id="eyeOpen2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             style="display:none">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="eyeClosed2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">Create Account</button>

        </form>


    </div>
</div>

<script>
    // Password field toggle
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('password');
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        document.getElementById('eyeOpen1').style.display   = isPass ? 'block' : 'none';
        document.getElementById('eyeClosed1').style.display = isPass ? 'none'  : 'block';
    });

    // Re-type password field toggle
    document.getElementById('toggleConfirm').addEventListener('click', function () {
        const input = document.getElementById('password_confirmation');
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        document.getElementById('eyeOpen2').style.display   = isPass ? 'block' : 'none';
        document.getElementById('eyeClosed2').style.display = isPass ? 'none'  : 'block';
    });
</script>

</body>
</html>
