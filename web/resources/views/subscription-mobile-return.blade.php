<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} – SecureThreatShield</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0E1326] text-white antialiased flex items-center justify-center p-6 text-center">
    <div class="w-full max-w-md bg-[#1C2236] border border-gray-700/50 rounded-lg p-6" id="mobile-return" data-redirect="{{ $redirect }}">
        <div class="text-4xl mb-4">{{ $icon }}</div>
        <h1 class="text-lg font-semibold text-white mb-2">{{ $title }}</h1>
        <p class="text-xs text-slate-400 leading-relaxed mb-6">{{ $message }}</p>

        @if ($redirect)
            <a class="inline-block bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-5 py-2.5 rounded-md border border-gray-700/50 transition-colors" href="{{ $redirect }}">
                Return to App
            </a>
        @endif
    </div>

    <script>
        var card     = document.getElementById('mobile-return');
        var redirect = card ? card.dataset.redirect : '';

        if (redirect) {
            window.location.href = redirect;
        }
    </script>
</body>
</html>
