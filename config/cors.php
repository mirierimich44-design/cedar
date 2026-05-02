<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // Include all routes the React mobile app needs.
    // In production the app is served from the same origin so CORS never fires,
    // but these paths allow cross-origin dev servers (localhost:5173 etc.) to work
    // if a developer enables VITE_API_URL pointing directly at the backend.
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'mobile-pos-details',
        'products/list',
        'pos',
        'sync/*',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Must be true for Sanctum cookie-based auth to work cross-origin.
    // Note: when this is true, allowed_origins cannot be ['*'] — use specific
    // origins or allowed_origins_patterns if you need cross-origin credentials.
    'supports_credentials' => false,

];
