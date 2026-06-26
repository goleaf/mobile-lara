<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileFeatureActions;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileBilling\MobileBillingApiService;
use App\Services\MobileLocal\SettingsRepository;
use App\Services\Native\BrowserService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Billing')]
final class Billing extends Component
{
    use DispatchesToasts;
    use GuardsMobileFeatureActions;

    /**
     * @var array<string, mixed>
     */
    public array $subscription = [];

    public ?string $loadError = null;

    public bool $usingCachedSubscription = false;

    private MobileBillingApiService $billing;

    private SettingsRepository $settings;

    private BrowserService $browsers;

    public function boot(
        MobileAccessPolicy $mobileAccessPolicy,
        MobileBillingApiService $billing,
        SettingsRepository $settings,
        BrowserService $browsers,
    ): void {
        $this->mobileAccessPolicy = $mobileAccessPolicy;
        $this->billing = $billing;
        $this->settings = $settings;
        $this->browsers = $browsers;
    }

    public function mount(): void
    {
        $this->refreshSubscription();
    }

    public function refreshSubscription(): void
    {
        $this->loadError = null;
        $this->usingCachedSubscription = false;

        if ($this->mobileFeatureDenied('billing', 'Billing disabled', 'billing.view')) {
            $this->subscription = [];

            return;
        }

        try {
            $this->subscription = $this->billing->subscription();
            $this->cacheSubscription($this->subscription);

            return;
        } catch (MobileApiException $exception) {
            $this->loadError = $exception->getMessage();
            $this->toastWarning($exception->getMessage(), 'Billing unavailable');
        }

        $cached = $this->cachedSubscription();

        if ($cached !== []) {
            $this->subscription = $cached;
            $this->usingCachedSubscription = true;

            return;
        }

        $this->subscription = [];
    }

    public function openBillingPortal(): void
    {
        if ($this->mobileFeatureDenied('native_browser', 'Billing portal unavailable')) {
            return;
        }

        $url = Arr::get($this->subscription, 'billing_portal.url');

        if (! is_string($url) || trim($url) === '') {
            $this->toastWarning('No billing portal URL is configured for this workspace.', 'Billing portal unavailable');

            return;
        }

        $result = $this->browsers->openExternalUrl($url);

        if ($result['success']) {
            $this->toastSuccess($result['message'], 'Billing portal opened');

            return;
        }

        $this->toastWarning($result['message'], 'Billing portal unavailable');
    }

    public function render(): View
    {
        return view('livewire.mobile.billing', [
            'actions' => $this->availableActions(),
            'billingPolicy' => $this->mobileFeatureDecision('billing', 'billing.view'),
            'featureImpact' => $this->featureImpact(),
            'metricRows' => $this->metricRows(),
            'plan' => $this->plan(),
            'portal' => $this->portal(),
            'statusLabel' => $this->statusLabel(),
            'trial' => $this->trial(),
        ]);
    }

    /**
     * @return array{key: string, name: string, tier: string}
     */
    private function plan(): array
    {
        $plan = Arr::get($this->subscription, 'plan');

        if (! is_array($plan)) {
            return [
                'key' => 'unknown',
                'name' => 'Unknown plan',
                'tier' => 'unknown',
            ];
        }

        return [
            'key' => $this->stringValue($plan['key'] ?? null) ?? 'unknown',
            'name' => $this->stringValue($plan['name'] ?? null) ?? 'Unknown plan',
            'tier' => $this->stringValue($plan['tier'] ?? null) ?? 'unknown',
        ];
    }

    /**
     * @return array{active: bool, ends_at: string|null, days_remaining: int|null}
     */
    private function trial(): array
    {
        $trial = Arr::get($this->subscription, 'trial');

        if (! is_array($trial)) {
            return [
                'active' => false,
                'ends_at' => null,
                'days_remaining' => null,
            ];
        }

        $daysRemaining = $trial['days_remaining'] ?? null;

        return [
            'active' => ($trial['active'] ?? null) === true,
            'ends_at' => $this->stringValue($trial['ends_at'] ?? null),
            'days_remaining' => is_int($daysRemaining) ? $daysRemaining : null,
        ];
    }

    /**
     * @return array{available: bool, url: string|null, reason: string|null}
     */
    private function portal(): array
    {
        $portal = Arr::get($this->subscription, 'billing_portal');

        if (! is_array($portal)) {
            return [
                'available' => false,
                'url' => null,
                'reason' => 'portal_not_configured',
            ];
        }

        return [
            'available' => ($portal['available'] ?? null) === true,
            'url' => $this->stringValue($portal['url'] ?? null),
            'reason' => $this->stringValue($portal['reason'] ?? null),
        ];
    }

    /**
     * @return list<array{key: string, label: string, usage: string, limit: string}>
     */
    private function metricRows(): array
    {
        $limits = Arr::get($this->subscription, 'limits');
        $usage = Arr::get($this->subscription, 'usage');
        $limits = is_array($limits) ? $limits : [];
        $usage = is_array($usage) ? $usage : [];
        $keys = array_values(array_unique(array_merge(array_keys($limits), array_keys($usage))));

        return collect($keys)
            ->map(fn (string|int $key): array => [
                'key' => (string) $key,
                'label' => str((string) $key)->replace('_', ' ')->title()->toString(),
                'usage' => $this->scalarLabel($usage[$key] ?? 0),
                'limit' => $this->scalarLabel($limits[$key] ?? 'unlimited'),
            ])
            ->all();
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    private function availableActions(): array
    {
        $actions = Arr::get($this->subscription, 'available_actions');

        if (! is_array($actions)) {
            return [];
        }

        return collect($actions)
            ->filter(fn (mixed $action): bool => is_string($action) && trim($action) !== '')
            ->map(fn (string $action): array => [
                'key' => $action,
                'label' => str($action)->replace('_', ' ')->title()->toString(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{limited: bool, reason: string|null}
     */
    private function featureImpact(): array
    {
        return [
            'limited' => Arr::get($this->subscription, 'features_limited') === true
                || Arr::get($this->subscription, 'feature_impacts.paid_features_blocked') === true,
            'reason' => $this->stringValue(Arr::get($this->subscription, 'feature_impacts.reason')),
        ];
    }

    private function statusLabel(): string
    {
        $status = $this->stringValue($this->subscription['status'] ?? null) ?? 'unknown';

        return str($status)->replace('_', ' ')->title()->toString();
    }

    /**
     * @param  array<string, mixed>  $subscription
     */
    private function cacheSubscription(array $subscription): void
    {
        $context = $this->settings->cachedBootstrapContext() ?? [
            'success' => true,
            'data' => [],
            'meta' => [],
        ];

        if (! is_array($context['data'] ?? null)) {
            $context['data'] = [];
        }

        $context['data']['subscription'] = $subscription;

        $this->settings->cacheBootstrapContext($context);
    }

    /**
     * @return array<string, mixed>
     */
    private function cachedSubscription(): array
    {
        $context = $this->settings->cachedBootstrapContext();
        $subscription = is_array($context) ? Arr::get($context, 'data.subscription') : null;

        return is_array($subscription) ? $subscription : [];
    }

    private function scalarLabel(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return 'n/a';
    }

    private function stringValue(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }
}
