<?php

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // In production: set CORS_ALLOWED_ORIGINS in .env to your actual frontend URLs
    // Example: CORS_ALLOWED_ORIGINS=https://Riwaq.com,https://app.Riwaq.com
    // In development: '*' is acceptable
    'allowed_origins' => env('APP_ENV') === 'production'
        ? explode(',', env('CORS_ALLOWED_ORIGINS', 'https://Riwaq.com'))
        : ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
    ],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => false,

];