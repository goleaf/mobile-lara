<?php

return [
    'links' => [
        'external_url' => env('MOBILE_BROWSER_EXTERNAL_URL', 'https://nativephp.com/mobile'),
        'in_app_url' => env('MOBILE_BROWSER_IN_APP_URL', 'https://nativephp.com/docs/mobile/3'),
        'oauth_url' => env('MOBILE_BROWSER_OAUTH_URL', 'https://auth.example.test/oauth/authorize?client_id=mobile-lara&redirect_uri=mobilelara://auth/callback&response_type=code'),
        'privacy_policy_url' => env('MOBILE_BROWSER_PRIVACY_POLICY_URL'),
        'support_center_url' => env('MOBILE_BROWSER_SUPPORT_CENTER_URL'),
        'billing_portal_url' => env('MOBILE_BROWSER_BILLING_PORTAL_URL', 'https://billing.example.test/portal'),
    ],
];
