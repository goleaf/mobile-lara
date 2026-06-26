<?php

return [
    'access_token_ttl_minutes' => (int) env('MOBILE_AUTH_ACCESS_TOKEN_TTL_MINUTES', 15),
    'refresh_token_ttl_minutes' => (int) env('MOBILE_AUTH_REFRESH_TOKEN_TTL_MINUTES', 43200),
    'revocation_ttl_minutes' => (int) env('MOBILE_AUTH_REVOCATION_TTL_MINUTES', 43200),

    'storage' => [
        'driver' => env('MOBILE_AUTH_TOKEN_STORE', 'native_secure_storage'),
        'session_key' => 'mobile_auth.tokens',
        'revoked_session_key' => 'mobile_auth.revoked_tokens',
        'secure_key_prefix' => env('MOBILE_AUTH_SECURE_KEY_PREFIX', 'mobile_auth'),
    ],

    'api' => [
        'base_url' => env('MOBILE_API_BASE_URL', 'https://mobile-lara-api-admin.test/api/v1/mobile'),
        'timeout_seconds' => (int) env('MOBILE_API_TIMEOUT_SECONDS', 10),
        'connect_timeout_seconds' => (int) env('MOBILE_API_CONNECT_TIMEOUT_SECONDS', 3),
    ],

    'device' => [
        'session_key' => 'mobile_auth.device_id',
    ],

    'pin' => [
        'min_length' => 4,
        'max_length' => 6,
        'max_attempts' => 5,
        'lockout_seconds' => 300,
    ],
];
