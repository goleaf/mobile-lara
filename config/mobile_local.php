<?php

$databasePath = env('NATIVEPHP_LOCAL_DB_DATABASE');

return [
    'connection' => 'mobile_local',

    'database' => $databasePath ?: storage_path('app/mobile/mobile-local.sqlite'),

    'migrations' => [
        'path' => database_path('migrations/mobile-local'),
    ],

    'health' => [
        'key' => 'nativephp-mobile-local-storage',
    ],

    'storage' => [
        'file_cache_path' => storage_path('framework/cache/data'),
        'export_path' => storage_path('app/mobile/mobile-local-export.json'),
        'file_manager_path' => storage_path('app/mobile/files'),
        'file_export_path' => storage_path('app/mobile/exports'),
        'file_preview_bytes' => 65536,
    ],

    'network' => [
        'fallback_check' => [
            'enabled' => env('NATIVEPHP_NETWORK_FALLBACK_ENABLED', env('APP_ENV') !== 'testing'),
            'url' => env('NATIVEPHP_NETWORK_FALLBACK_URL', 'https://cp.cloudflare.com/generate_204'),
            'timeout_seconds' => env('NATIVEPHP_NETWORK_FALLBACK_TIMEOUT_SECONDS', 2),
            'connect_timeout_seconds' => env('NATIVEPHP_NETWORK_FALLBACK_CONNECT_TIMEOUT_SECONDS', 1),
        ],
    ],

    'settings' => [
        'key' => 'default',
        'theme' => 'system',
        'language' => env('APP_LOCALE', 'en'),
        'notification_preferences' => [
            'push_enabled' => true,
            'email_enabled' => false,
            'marketing_enabled' => false,
        ],
        'sync_settings' => [
            'auto_sync_enabled' => true,
            'background_sync_enabled' => true,
            'wifi_only' => false,
        ],
    ],

    'sync' => [
        'base_url' => null,
        'batch_size' => 25,
        'timeout_seconds' => 10,
        'connect_timeout_seconds' => 5,
        'base_backoff_seconds' => 60,
        'max_backoff_seconds' => 3600,
    ],
];
