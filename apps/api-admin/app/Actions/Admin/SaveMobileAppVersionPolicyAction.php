<?php

namespace App\Actions\Admin;

use App\Models\MobileAppVersionPolicy;
use App\Models\SecurityAuditEvent;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;

final class SaveMobileAppVersionPolicyAction
{
    public function __construct(private MobileAuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $admin, Request $request, ?MobileAppVersionPolicy $policy = null): MobileAppVersionPolicy
    {
        $creating = ! $policy instanceof MobileAppVersionPolicy;
        $before = $policy instanceof MobileAppVersionPolicy ? $this->snapshot($policy) : null;

        $policy = $policy instanceof MobileAppVersionPolicy
            ? tap($policy)->update($this->payload($data, $policy->metadata ?? []))
            : MobileAppVersionPolicy::query()->create($this->payload($data));

        $this->audit->record(
            $creating ? 'admin_mobile_app_version_policy_created' : 'admin_mobile_app_version_policy_updated',
            $request,
            $admin,
            severity: $this->severity($policy),
            metadata: [
                'app_version_policy_id' => $policy->id,
                'platform' => $policy->platform,
                'before' => $before,
                'after' => $this->snapshot($policy),
            ],
        );

        return $policy;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    public function restore(array $snapshot, User $admin, Request $request, SecurityAuditEvent $sourceEvent): MobileAppVersionPolicy
    {
        $policyId = $snapshot['id'] ?? null;
        $policy = is_int($policyId) ? MobileAppVersionPolicy::query()->find($policyId) : null;
        $before = $policy instanceof MobileAppVersionPolicy ? $this->snapshot($policy) : null;

        $policy = $policy instanceof MobileAppVersionPolicy
            ? tap($policy)->update($this->payloadFromSnapshot($snapshot))
            : MobileAppVersionPolicy::query()->create($this->payloadFromSnapshot($snapshot));

        $this->audit->record(
            'admin_mobile_app_version_policy_restored',
            $request,
            $admin,
            severity: $this->severity($policy),
            metadata: [
                'app_version_policy_id' => $policy->id,
                'platform' => $policy->platform,
                'source_audit_event_id' => $sourceEvent->id,
                'before' => $before,
                'after' => $this->snapshot($policy),
            ],
        );

        return $policy;
    }

    /**
     * @return array<string, mixed>
     */
    public function snapshot(MobileAppVersionPolicy $policy): array
    {
        return [
            'id' => $policy->id,
            'platform' => $policy->platform,
            'minimum_supported_version' => $policy->minimum_supported_version,
            'minimum_recommended_version' => $policy->minimum_recommended_version,
            'latest_version' => $policy->latest_version,
            'blocked_versions' => $this->arrayValue($policy->blocked_versions),
            'store_urls' => $this->arrayValue($policy->store_urls),
            'message' => $policy->message,
            'support_url' => $policy->support_url,
            'force_update' => $policy->force_update,
            'maintenance_enabled' => $policy->maintenance_enabled,
            'maintenance_message' => $policy->maintenance_message,
            'retry_after_seconds' => $policy->retry_after_seconds,
            'allowed_actions' => $this->arrayValue($policy->allowed_actions),
            'logout_allowed' => $policy->logout_allowed,
            'is_active' => $policy->is_active,
            'metadata' => $this->arrayValue($policy->metadata),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    private function payload(array $data, array $metadata = []): array
    {
        return [
            'platform' => $this->platform($data['platform'] ?? 'all'),
            'minimum_supported_version' => $this->requiredString($data['minimum_supported_version'] ?? '1.0.0'),
            'minimum_recommended_version' => $this->nullableString($data['minimum_recommended_version'] ?? null),
            'latest_version' => $this->nullableString($data['latest_version'] ?? null),
            'blocked_versions' => $this->stringList($data['blocked_versions'] ?? ''),
            'store_urls' => [
                'ios' => $this->nullableString($data['ios_store_url'] ?? null),
                'android' => $this->nullableString($data['android_store_url'] ?? null),
            ],
            'message' => $this->nullableString($data['message'] ?? null),
            'support_url' => $this->nullableString($data['support_url'] ?? null),
            'force_update' => (bool) ($data['force_update'] ?? false),
            'maintenance_enabled' => (bool) ($data['maintenance_enabled'] ?? false),
            'maintenance_message' => $this->nullableString($data['maintenance_message'] ?? null),
            'retry_after_seconds' => $this->nullableInteger($data['retry_after_seconds'] ?? null),
            'allowed_actions' => $this->stringList($data['allowed_actions'] ?? '') ?: ['continue', 'logout', 'support'],
            'logout_allowed' => (bool) ($data['logout_allowed'] ?? true),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'metadata' => $metadata,
        ];
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>
     */
    private function payloadFromSnapshot(array $snapshot): array
    {
        $storeUrls = $this->arrayValue($snapshot['store_urls'] ?? []);

        return [
            'platform' => $this->platform($snapshot['platform'] ?? 'all'),
            'minimum_supported_version' => $this->requiredString($snapshot['minimum_supported_version'] ?? '1.0.0'),
            'minimum_recommended_version' => $this->nullableString($snapshot['minimum_recommended_version'] ?? null),
            'latest_version' => $this->nullableString($snapshot['latest_version'] ?? null),
            'blocked_versions' => $this->arrayValue($snapshot['blocked_versions'] ?? []),
            'store_urls' => [
                'ios' => $this->nullableString($storeUrls['ios'] ?? null),
                'android' => $this->nullableString($storeUrls['android'] ?? null),
            ],
            'message' => $this->nullableString($snapshot['message'] ?? null),
            'support_url' => $this->nullableString($snapshot['support_url'] ?? null),
            'force_update' => (bool) ($snapshot['force_update'] ?? false),
            'maintenance_enabled' => (bool) ($snapshot['maintenance_enabled'] ?? false),
            'maintenance_message' => $this->nullableString($snapshot['maintenance_message'] ?? null),
            'retry_after_seconds' => $this->nullableInteger($snapshot['retry_after_seconds'] ?? null),
            'allowed_actions' => $this->arrayValue($snapshot['allowed_actions'] ?? []),
            'logout_allowed' => (bool) ($snapshot['logout_allowed'] ?? true),
            'is_active' => (bool) ($snapshot['is_active'] ?? true),
            'metadata' => $this->arrayValue($snapshot['metadata'] ?? []),
        ];
    }

    private function platform(mixed $value): string
    {
        $platform = is_string($value) ? str($value)->lower()->trim()->toString() : 'all';

        return $platform === '' ? 'all' : $platform;
    }

    private function requiredString(mixed $value): string
    {
        $value = is_string($value) ? trim($value) : '';

        return $value === '' ? '1.0.0' : $value;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' ? null : $value;
    }

    private function nullableInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * @return array<int, string>
     */
    private function stringList(mixed $value): array
    {
        $items = is_array($value) ? $value : preg_split('/[\r\n,]+/', (string) $value);

        return collect($items)
            ->filter(static fn (mixed $item): bool => is_string($item) && trim($item) !== '')
            ->map(static fn (string $item): string => trim($item))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int|string, mixed>
     */
    private function arrayValue(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    private function severity(MobileAppVersionPolicy $policy): string
    {
        return $policy->force_update || $policy->maintenance_enabled || ! $policy->is_active ? 'warning' : 'info';
    }
}
