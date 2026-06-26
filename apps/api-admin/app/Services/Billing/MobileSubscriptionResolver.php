<?php

namespace App\Services\Billing;

use App\Models\Tenant;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;

final class MobileSubscriptionResolver
{
    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     * @return array<string, mixed>
     */
    public function resolve(array $tenantContext): array
    {
        $tenant = $this->tenantFromContext($tenantContext);
        $now = CarbonImmutable::now();

        if (! $tenant instanceof Tenant) {
            return [
                'status' => 'no_active_tenant',
                'plan' => $this->plan('none', 'No active tenant', 'none'),
                'trial' => [
                    'active' => false,
                    'ends_at' => null,
                    'days_remaining' => null,
                ],
                'features_limited' => true,
                'limits' => [],
                'usage' => [],
                'available_actions' => ['select_tenant', 'support', 'logout'],
                'billing_portal' => [
                    'available' => false,
                    'url' => null,
                    'reason' => 'no_active_tenant',
                ],
                'feature_impacts' => [
                    'paid_features_blocked' => true,
                    'reason' => 'no_active_tenant',
                ],
                'source' => 'tenant_context',
                'resolved_at' => $now->toIso8601String(),
                'subscription_version' => 'subscription-none',
            ];
        }

        $settings = is_array($tenant->settings) ? $tenant->settings : [];
        $status = $this->status($tenant->subscription_state);
        $planKey = $this->planKey($settings);
        $trialEndsAt = $this->dateValue(Arr::get($settings, 'billing.trial_ends_at') ?? Arr::get($settings, 'subscription.trial_ends_at'));
        $billingPortalUrl = $this->nullableString(Arr::get($settings, 'billing.portal_url'));
        $featuresLimited = ! in_array($status, ['active', 'trialing'], true);

        return [
            'status' => $status,
            'plan' => $this->plan(
                $planKey,
                $this->nullableString(Arr::get($settings, 'billing.plan_name')) ?? str($planKey)->replace(['_', '-'], ' ')->title()->toString(),
                $this->nullableString(Arr::get($settings, 'billing.plan_tier')) ?? $planKey,
            ),
            'trial' => [
                'active' => $status === 'trialing',
                'ends_at' => $trialEndsAt?->toIso8601String(),
                'days_remaining' => $trialEndsAt instanceof CarbonImmutable ? max(0, $now->diffInDays($trialEndsAt, false)) : null,
            ],
            'features_limited' => $featuresLimited,
            'limits' => $this->arrayValue(Arr::get($settings, 'billing.limits')),
            'usage' => $this->arrayValue(Arr::get($settings, 'billing.usage')),
            'available_actions' => $this->availableActions($status),
            'billing_portal' => [
                'available' => $billingPortalUrl !== null && in_array($status, ['active', 'trialing', 'past_due', 'expired'], true),
                'url' => $billingPortalUrl,
                'reason' => $billingPortalUrl === null ? 'portal_not_configured' : null,
            ],
            'feature_impacts' => [
                'paid_features_blocked' => $featuresLimited,
                'reason' => $featuresLimited ? 'subscription_'.$status : null,
            ],
            'source' => 'tenant_subscription_state',
            'resolved_at' => $now->toIso8601String(),
            'subscription_version' => $this->version($tenant, $status, $planKey),
        ];
    }

    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     */
    private function tenantFromContext(array $tenantContext): ?Tenant
    {
        $currentTenant = is_array($tenantContext['current_tenant'] ?? null) ? $tenantContext['current_tenant'] : null;
        $publicId = is_string($currentTenant['id'] ?? null) ? $currentTenant['id'] : null;

        if ($publicId === null || trim($publicId) === '') {
            return null;
        }

        return Tenant::query()
            ->select(['id', 'public_id', 'subscription_state', 'settings', 'updated_at'])
            ->where('public_id', $publicId)
            ->first();
    }

    private function status(?string $status): string
    {
        $status = str((string) $status)->lower()->trim()->replace('-', '_')->toString();

        return match ($status) {
            'active', 'trialing', 'trial', 'past_due', 'expired', 'canceled', 'cancelled', 'suspended' => $status === 'trial' ? 'trialing' : ($status === 'cancelled' ? 'canceled' : $status),
            default => 'unknown',
        };
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    private function planKey(array $settings): string
    {
        $plan = Arr::get($settings, 'billing.plan')
            ?? Arr::get($settings, 'billing.plan_key')
            ?? Arr::get($settings, 'subscription.plan')
            ?? Arr::get($settings, 'subscription.plan_key');

        if (! is_string($plan) || trim($plan) === '') {
            return 'foundation';
        }

        return str($plan)->lower()->trim()->replace(' ', '_')->toString();
    }

    /**
     * @return array{key: string, name: string, tier: string}
     */
    private function plan(string $key, string $name, string $tier): array
    {
        return [
            'key' => $key,
            'name' => $name,
            'tier' => $tier,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function availableActions(string $status): array
    {
        return match ($status) {
            'active' => ['view_plan', 'support'],
            'trialing' => ['view_plan', 'upgrade', 'support'],
            'past_due', 'expired' => ['update_billing', 'contact_admin', 'support'],
            'canceled', 'suspended', 'unknown' => ['contact_admin', 'support', 'logout'],
            default => ['support', 'logout'],
        };
    }

    /**
     * @return array<int|string, mixed>
     */
    private function arrayValue(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }

    private function dateValue(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return CarbonImmutable::parse($value);
    }

    private function version(Tenant $tenant, string $status, string $planKey): string
    {
        $payload = json_encode([
            'tenant_id' => $tenant->id,
            'status' => $status,
            'plan_key' => $planKey,
            'updated_at' => $tenant->updated_at?->toIso8601String(),
        ]);

        return 'subscription-'.substr(sha1(is_string($payload) ? $payload : ''), 0, 16);
    }
}
