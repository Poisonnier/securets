<?php

return [
    // Base URL of the main SecureTS system app, used for the API health check.
    'system_url' => rtrim((string) env('SYSTEM_APP_URL', 'http://localhost:8000'), '/'),
];
