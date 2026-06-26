<?php

namespace App\Actions\Admin;

use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

final class SaveTenantBillingAction
{
    public function __construct(private MobileAuditLogger $audit) {}

    /**
     * @param  array{subscription_state: string, plan: string, plan_name: string, plan_tier: string, trial_ends_at?: string|null, portal_url?: string|null, limits_json?: string|null, usage_json?: string|null}  $data
     */
    public function handle(Tenant $tenant, array $data, User $admin, Request $request): Tenant
    {
        $before = $this->snapshot($tenant);
        $settings = is_array($tenant->settings) ? $tenant->settings : [];

        $settings['billing'] = [
            'plan' => $this->normalizedKey($data['plan']),
            'plan_name' => trim($data['plan_name']),
            'plan_tier' => $this->normalizedKey($data['plan_tier']),
            'trial_ends_at' => $this->nullableString($data['trial_ends_at'] ?? null),
            'portal_url' => $this->nullableString($data['portal_url'] ?? null),
            'limits' => $this->jsonObject($data['limits_json'] ?? null),
            'usage' => $this->jsonObject($data['usage_json'] ?? null),
        ];

        $tenant->update([
            'subscription_state' => $this->normalizedKey($data['subscription_state']),
            'settings' => $settings,
        ]);

        $tenant->refresh();

        $this->audit->record(
            'admin_billing_updated',
            $request,
            $admin,
            severity: 'info',
            metadata: [
                'tenant_id' => $tenant->id,
                'tenant_public_id' => $tenant->public_id,
                'before' => $before,
                'after' => $this->snapshot($tenant),
            ],
        );

        return $tenant;
    }

    private function normalizedKey(string $value): string
    {
        return str($value)->lower()->trim()->replace([' ', '-'], '_')->toString();
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }

    /**
     * @return array<string, mixed>
     */
    private function jsonObject(?string $json): array
    {
        $json = is_string($json) ? trim($json) : '';

        if ($json === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) && ! array_is_list($decoded) ? $decoded : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(Tenant $tenant): array
    {
        $settings = is_array($tenant->settings) ? $tenant->settings : [];

        return [
            'public_id' => $tenant->public_id,
            'subscription_state' => $tenant->subscription_state,
            'billing' => Arr::get($settings, 'billing', []),
        ];
    }
}
