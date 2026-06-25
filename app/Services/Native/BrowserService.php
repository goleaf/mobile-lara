<?php

namespace App\Services\Native;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Native\Mobile\Browser;
use Throwable;

final class BrowserService
{
    private const MODE_EXTERNAL = 'external';

    private const MODE_IN_APP = 'in_app';

    private const MODE_AUTH = 'auth';

    public function __construct(
        private readonly Browser $browser,
    ) {}

    public function isAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    /**
     * @return list<array{key: string, label: string, description: string, supported: bool, driver: string}>
     */
    public function capabilities(): array
    {
        $nativeAvailable = $this->isAvailable();

        return [
            [
                'key' => 'external-url',
                'label' => 'External URL',
                'description' => 'Open a web URL in the device default browser.',
                'supported' => $nativeAvailable,
                'driver' => 'NativePHP Browser.Open',
            ],
            [
                'key' => 'in-app-url',
                'label' => 'In-app URL',
                'description' => 'Open a web URL in SFSafariViewController or Chrome Custom Tabs.',
                'supported' => $nativeAvailable,
                'driver' => 'NativePHP Browser.OpenInApp',
            ],
            [
                'key' => 'oauth-url',
                'label' => 'OAuth URL',
                'description' => 'Open an OAuth authorization URL in the native auth browser.',
                'supported' => $nativeAvailable,
                'driver' => 'NativePHP Browser.OpenAuth',
            ],
            [
                'key' => 'privacy-policy',
                'label' => 'Privacy policy',
                'description' => 'Open the configured mobile privacy policy URL in-app.',
                'supported' => $nativeAvailable,
                'driver' => 'NativePHP Browser.OpenInApp',
            ],
            [
                'key' => 'support-center',
                'label' => 'Support center',
                'description' => 'Open the configured support center URL in-app.',
                'supported' => $nativeAvailable,
                'driver' => 'NativePHP Browser.OpenInApp',
            ],
            [
                'key' => 'billing-portal-placeholder',
                'label' => 'Billing portal',
                'description' => 'Open the placeholder billing portal in the system browser.',
                'supported' => $nativeAvailable,
                'driver' => 'NativePHP Browser.Open',
            ],
        ];
    }

    /**
     * @return array{success: bool, operation: string, message: string, url?: string, mode?: string, driver?: string}
     */
    public function openExternalUrl(string $url): array
    {
        return $this->openUsing(
            operation: 'open_external_url',
            mode: self::MODE_EXTERNAL,
            url: $url,
            unavailableMessage: 'Native external browser is unavailable in this browser runtime.',
            openedMessage: 'External browser opened.',
            failedMessage: 'Unable to open the external browser.',
        );
    }

    /**
     * @return array{success: bool, operation: string, message: string, url?: string, mode?: string, driver?: string}
     */
    public function openInAppUrl(string $url): array
    {
        return $this->openUsing(
            operation: 'open_in_app_url',
            mode: self::MODE_IN_APP,
            url: $url,
            unavailableMessage: 'Native in-app browser is unavailable in this browser runtime.',
            openedMessage: 'In-app browser opened.',
            failedMessage: 'Unable to open the in-app browser.',
        );
    }

    /**
     * @return array{success: bool, operation: string, message: string, url?: string, mode?: string, driver?: string}
     */
    public function openOAuthUrl(string $url): array
    {
        return $this->openUsing(
            operation: 'open_oauth_url',
            mode: self::MODE_AUTH,
            url: $url,
            unavailableMessage: 'Native OAuth browser is unavailable in this browser runtime.',
            openedMessage: 'OAuth browser opened.',
            failedMessage: 'Unable to open the OAuth browser.',
        );
    }

    /**
     * @return array{success: bool, operation: string, message: string, url?: string, mode?: string, driver?: string}
     */
    public function openPrivacyPolicy(): array
    {
        return $this->openUsing(
            operation: 'open_privacy_policy',
            mode: self::MODE_IN_APP,
            url: $this->configuredUrl('privacy_policy_url', route('mobile.privacy')),
            unavailableMessage: 'Native in-app browser is unavailable in this browser runtime.',
            openedMessage: 'Privacy policy opened.',
            failedMessage: 'Unable to open the privacy policy.',
        );
    }

    /**
     * @return array{success: bool, operation: string, message: string, url?: string, mode?: string, driver?: string}
     */
    public function openSupportCenter(): array
    {
        return $this->openUsing(
            operation: 'open_support_center',
            mode: self::MODE_IN_APP,
            url: $this->configuredUrl('support_center_url', route('mobile.settings.support')),
            unavailableMessage: 'Native in-app browser is unavailable in this browser runtime.',
            openedMessage: 'Support center opened.',
            failedMessage: 'Unable to open the support center.',
        );
    }

    /**
     * @return array{success: bool, operation: string, message: string, url?: string, mode?: string, driver?: string}
     */
    public function openBillingPortalPlaceholder(): array
    {
        return $this->openUsing(
            operation: 'open_billing_portal_placeholder',
            mode: self::MODE_EXTERNAL,
            url: $this->configuredUrl('billing_portal_url'),
            unavailableMessage: 'Native external browser is unavailable in this browser runtime.',
            openedMessage: 'Billing portal placeholder opened.',
            failedMessage: 'Unable to open the billing portal placeholder.',
        );
    }

    private function configuredUrl(string $key, ?string $fallback = null): string
    {
        $url = config("mobile_browser.links.{$key}");

        if (is_scalar($url) && trim((string) $url) !== '') {
            return trim((string) $url);
        }

        if (is_string($fallback) && trim($fallback) !== '') {
            return trim($fallback);
        }

        throw new InvalidArgumentException("Browser link [{$key}] is not configured.");
    }

    /**
     * @return array{success: bool, operation: string, message: string, url?: string, mode?: string, driver?: string}
     */
    private function openUsing(
        string $operation,
        string $mode,
        string $url,
        string $unavailableMessage,
        string $openedMessage,
        string $failedMessage,
    ): array {
        try {
            $url = $this->validatedHttpUrl($url);

            if (! $this->isAvailable()) {
                return $this->result(false, $operation, $unavailableMessage, [
                    'url' => $url,
                    'mode' => $mode,
                    'driver' => 'native',
                ]);
            }

            $opened = match ($mode) {
                self::MODE_EXTERNAL => $this->browser->open($url),
                self::MODE_IN_APP => $this->browser->inApp($url),
                self::MODE_AUTH => $this->browser->auth($url),
                default => false,
            };

            if (! $opened) {
                return $this->result(false, $operation, $failedMessage, [
                    'url' => $url,
                    'mode' => $mode,
                    'driver' => 'native',
                ]);
            }

            return $this->result(true, $operation, $openedMessage, [
                'url' => $url,
                'mode' => $mode,
                'driver' => 'native',
            ]);
        } catch (Throwable $exception) {
            return $this->result(false, $operation, $exception->getMessage());
        }
    }

    private function validatedHttpUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            throw new InvalidArgumentException('Browser URL is required.');
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Browser URL must be a valid absolute URL.');
        }

        $scheme = Str::lower((string) parse_url($url, PHP_URL_SCHEME));

        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('Browser URL must use http or https.');
        }

        return $url;
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function result(bool $success, string $operation, string $message, array $extra = []): array
    {
        return [
            'success' => $success,
            'operation' => $operation,
            'message' => $message,
            ...$extra,
        ];
    }
}
