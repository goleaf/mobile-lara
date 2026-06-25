<?php

use App\Services\Native\BrowserService;
use Native\Mobile\Browser;

beforeEach(function (): void {
    config([
        'mobile_browser.links.external_url' => 'https://nativephp.com/mobile',
        'mobile_browser.links.in_app_url' => 'https://nativephp.com/docs/mobile/3',
        'mobile_browser.links.oauth_url' => 'https://auth.example.test/oauth/authorize?client_id=mobile-lara&redirect_uri=mobilelara://auth/callback&response_type=code',
        'mobile_browser.links.privacy_policy_url' => 'https://mobile-lara.test/privacy',
        'mobile_browser.links.support_center_url' => 'https://support.example.test/mobile-lara',
        'mobile_browser.links.billing_portal_url' => 'https://billing.example.test/portal',
    ]);
});

test('browser service reports browser fallback when native runtime is inactive', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        $browser = new NativeBrowserServiceFakeBrowser;
        $service = new BrowserService($browser);

        expect($service->isAvailable())->toBeFalse()
            ->and($service->openExternalUrl('https://example.test'))->toMatchArray([
                'success' => false,
                'operation' => 'open_external_url',
                'message' => 'Native external browser is unavailable in this browser runtime.',
                'mode' => 'external',
                'driver' => 'native',
            ])
            ->and($service->openInAppUrl('https://example.test/help'))->toMatchArray([
                'success' => false,
                'operation' => 'open_in_app_url',
                'message' => 'Native in-app browser is unavailable in this browser runtime.',
                'mode' => 'in_app',
                'driver' => 'native',
            ])
            ->and($service->openOAuthUrl('https://auth.example.test/oauth/authorize?client_id=mobile-lara'))->toMatchArray([
                'success' => false,
                'operation' => 'open_oauth_url',
                'message' => 'Native OAuth browser is unavailable in this browser runtime.',
                'mode' => 'auth',
                'driver' => 'native',
            ])
            ->and($browser->openedUrls)->toBeEmpty();
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('browser service opens native browser channels when runtime is active', function (): void {
    config(['nativephp-internal.running' => true]);

    $browser = new NativeBrowserServiceFakeBrowser;
    $service = new BrowserService($browser);

    expect($service->openExternalUrl('https://example.test'))->toMatchArray([
        'success' => true,
        'operation' => 'open_external_url',
        'message' => 'External browser opened.',
        'mode' => 'external',
    ])
        ->and($service->openInAppUrl('https://example.test/help'))->toMatchArray([
            'success' => true,
            'operation' => 'open_in_app_url',
            'message' => 'In-app browser opened.',
            'mode' => 'in_app',
        ])
        ->and($service->openOAuthUrl('https://auth.example.test/oauth/authorize?client_id=mobile-lara'))->toMatchArray([
            'success' => true,
            'operation' => 'open_oauth_url',
            'message' => 'OAuth browser opened.',
            'mode' => 'auth',
        ])
        ->and($service->openPrivacyPolicy())->toMatchArray([
            'success' => true,
            'operation' => 'open_privacy_policy',
            'message' => 'Privacy policy opened.',
            'url' => 'https://mobile-lara.test/privacy',
            'mode' => 'in_app',
        ])
        ->and($service->openSupportCenter())->toMatchArray([
            'success' => true,
            'operation' => 'open_support_center',
            'message' => 'Support center opened.',
            'url' => 'https://support.example.test/mobile-lara',
            'mode' => 'in_app',
        ])
        ->and($service->openBillingPortalPlaceholder())->toMatchArray([
            'success' => true,
            'operation' => 'open_billing_portal_placeholder',
            'message' => 'Billing portal placeholder opened.',
            'url' => 'https://billing.example.test/portal',
            'mode' => 'external',
        ])
        ->and($browser->openedUrls)->toBe([
            ['mode' => 'external', 'url' => 'https://example.test'],
            ['mode' => 'in_app', 'url' => 'https://example.test/help'],
            ['mode' => 'auth', 'url' => 'https://auth.example.test/oauth/authorize?client_id=mobile-lara'],
            ['mode' => 'in_app', 'url' => 'https://mobile-lara.test/privacy'],
            ['mode' => 'in_app', 'url' => 'https://support.example.test/mobile-lara'],
            ['mode' => 'external', 'url' => 'https://billing.example.test/portal'],
        ]);
});

test('browser service reports native open failures', function (): void {
    config(['nativephp-internal.running' => true]);

    $browser = new NativeBrowserServiceFakeBrowser;
    $browser->nextResult = false;
    $service = new BrowserService($browser);

    expect($service->openExternalUrl('https://example.test'))->toMatchArray([
        'success' => false,
        'operation' => 'open_external_url',
        'message' => 'Unable to open the external browser.',
    ]);
});

test('browser service validates urls before opening native browser', function (string $method, string $url, string $message): void {
    config(['nativephp-internal.running' => true]);

    $browser = new NativeBrowserServiceFakeBrowser;
    $service = new BrowserService($browser);

    expect($service->{$method}($url))->toMatchArray([
        'success' => false,
        'message' => $message,
    ])
        ->and($browser->openedUrls)->toBeEmpty();
})->with([
    'external empty url' => ['openExternalUrl', '', 'Browser URL is required.'],
    'external relative url' => ['openExternalUrl', '/privacy', 'Browser URL must be a valid absolute URL.'],
    'in-app javascript url' => ['openInAppUrl', 'javascript:alert(1)', 'Browser URL must be a valid absolute URL.'],
    'oauth custom scheme url' => ['openOAuthUrl', 'mobilelara://auth/callback', 'Browser URL must use http or https.'],
    'oauth ftp url' => ['openOAuthUrl', 'ftp://example.test/file', 'Browser URL must use http or https.'],
]);

test('browser service validates configured links', function (): void {
    config([
        'nativephp-internal.running' => true,
        'mobile_browser.links.billing_portal_url' => 'notaurl',
    ]);

    $service = new BrowserService(new NativeBrowserServiceFakeBrowser);

    expect($service->openBillingPortalPlaceholder())->toMatchArray([
        'success' => false,
        'operation' => 'open_billing_portal_placeholder',
        'message' => 'Browser URL must be a valid absolute URL.',
    ]);
});

final class NativeBrowserServiceFakeBrowser extends Browser
{
    /**
     * @var list<array{mode: string, url: string}>
     */
    public array $openedUrls = [];

    public bool $nextResult = true;

    public function open(string $url): bool
    {
        $this->openedUrls[] = ['mode' => 'external', 'url' => $url];

        return $this->nextResult;
    }

    public function inApp(string $url): bool
    {
        $this->openedUrls[] = ['mode' => 'in_app', 'url' => $url];

        return $this->nextResult;
    }

    public function auth(string $url): bool
    {
        $this->openedUrls[] = ['mode' => 'auth', 'url' => $url];

        return $this->nextResult;
    }
}
