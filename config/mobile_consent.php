<?php

return [
    'storage' => [
        'session_key' => 'mobile_consent.accepted_versions',
    ],

    'sync' => [
        'endpoint' => 'POST /api/mobile/consents',
        'status' => 'pending_server_sync',
    ],

    'policies' => [
        'terms' => [
            'key' => 'terms',
            'title' => 'Terms of Service',
            'version' => '2026.06.25',
            'effective_date' => '2026-06-25',
            'summary' => [
                'Use the mobile app responsibly and only for lawful purposes.',
                'Keep your account, unlock methods, and local device access secure.',
                'Service features may change while the NativePHP mobile build is being completed.',
            ],
            'sections' => [
                [
                    'heading' => 'Account access',
                    'body' => 'You are responsible for keeping your account credentials, local PIN, biometric unlock, and device access secure. Notify support if you suspect unauthorized access.',
                ],
                [
                    'heading' => 'Mobile features',
                    'body' => 'Native device features such as biometrics, secure storage, camera, scanner, and location may require device permissions. You control those permissions through the operating system.',
                ],
                [
                    'heading' => 'Service changes',
                    'body' => 'The app may add, remove, or change features as the mobile stack evolves. Material changes will be reflected in a new terms version before acceptance is requested again.',
                ],
            ],
        ],

        'privacy' => [
            'key' => 'privacy',
            'title' => 'Privacy Policy',
            'version' => '2026.06.25',
            'effective_date' => '2026-06-25',
            'summary' => [
                'The app stores selected auth and consent values locally on the device.',
                'Future server sync will include consent version, timestamp, locale, app version, and device reference.',
                'Sensitive mobile auth values are prepared for NativePHP secure storage.',
            ],
            'sections' => [
                [
                    'heading' => 'Local data',
                    'body' => 'The app may keep local settings such as accepted policy versions, unlock preferences, and mobile auth values so the mobile experience works across app sessions.',
                ],
                [
                    'heading' => 'Server sync',
                    'body' => 'When the consent API is connected, acceptance records can be synced with policy key, version, accepted timestamp, locale, app version, and device session reference.',
                ],
                [
                    'heading' => 'Device permissions',
                    'body' => 'Device capabilities are requested only when a feature needs them. You can review or revoke operating system permissions from the device settings.',
                ],
            ],
        ],
    ],
];
