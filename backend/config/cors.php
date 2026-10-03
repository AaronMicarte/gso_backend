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

    // Allow CORS on API routes and SPA auth endpoints (including web auth/* routes).
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'user', 'sanctum/*', 'mfa/*', 'auth/*'],

    'allowed_methods' => ['*'],

    // Allow localhost ports used by the SPA and any other development origin
    // (could alternatively use ['*'] for convenience, just be careful in production).
    'allowed_origins' => [
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://192.168.56.1:3000',
        'http://10.191.64.25:3000',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
        'https://gso-iams.vercel.app'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // IMPORTANT: Must be true for httpOnly cookies to work
    'supports_credentials' => true,

];
