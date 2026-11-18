<?php

return [
    // Base URL of the main SecureTS system app, used for the API health check.
    'system_url' => rtrim((string) env('SYSTEM_APP_URL', 'http://localhost:8000'), '/'),

    // Comma-separated allowlist of user emails permitted to use the admin panel.
    // An empty list denies everyone.
    'emails' => array_values(array_filter(array_map(
        fn (string $email): string => strtolower(trim($email)),
        explode(',', (string) env('ADMIN_EMAILS', ''))
    ))),

    // Lifetime of admin mobile API tokens, in minutes.
    'token_ttl_minutes' => (int) env('ADMIN_TOKEN_TTL_MINUTES', 720),
];
