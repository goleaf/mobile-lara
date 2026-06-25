<?php

return [
    'driver' => 'auto',

    'test' => [
        'title' => 'Test notification',
        'body' => 'Local notification abstraction is connected.',
        'deep_link' => '/mobile/notifications',
    ],

    'native' => [
        'packages' => [
            'nativephp/mobile-local-notifications',
            'nativephp/mobile-local-notification',
            'nativephp/local-notifications',
            'nativephp/local-notification',
            'codingwithrk/nativephp-local-notifications',
            's2br/nativephp-mobile-local-notifications',
        ],

        'classes' => [
            'Native\\Mobile\\LocalNotifications',
            'Native\\Mobile\\LocalNotification',
        ],

        'providers' => [
            'Native\\Mobile\\Providers\\LocalNotificationsServiceProvider',
            'Native\\Mobile\\Providers\\LocalNotificationServiceProvider',
        ],

        'bridge_methods' => [
            'schedule' => 'LocalNotification.Schedule',
            'cancel' => 'LocalNotification.Cancel',
            'list_scheduled' => 'LocalNotification.ListScheduled',
        ],
    ],

    'placeholder' => [
        'list_limit' => 50,
    ],
];
