<?php

namespace App\Actions\Admin;

use App\Enums\MobileFeatureState;
use App\Models\SecurityAuditEvent;
use App\Models\TenantFeatureOverride;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;

final class SaveTenantFeatureOverrideAction
{
    public function __construct(private MobileAuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $admin, Request $request, ?TenantFeatureOverride $override = null): TenantFeatureOverride
    {
        $creating = ! $override instanceof TenantFeatureOverride;
        $before = $override instanceof TenantFeatureOverride ? $this->snapshot($override) : null;

        $override = $override instanceof TenantFeatureOverride
            ? tap($override)->update($this->payload($data, $override->metadata ?? []))
            : TenantFeatureOverride::query()->create($this->payload($data));

        $this->audit->record(
            $creating ? 'admin_tenant_feature_override_created' : 'admin_tenant_feature_override_updated',
            $request,
            $admin,
            severity: $this->severity($override),
            metadata: [
                'tenant_feature_override_id' => $override->id,
                'tenant_id' => $override->tenant_id,
                'feature_key' => $override->feature_key,
                'before' => $before,
                'after' => $this->snapshot($override),
            ],
        );

        return $override;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    public function restore(array $snapshot, User $admin, Request $request, SecurityAuditEvent $sourceEvent): TenantFeatureOverride
    {
        $overrideId = $snapshot['id'] ?? null;
        $override = is_int($overrideId) ? TenantFeatureOverride::query()->find($overrideId) : null;
        $before = $override instanceof TenantFeatureOverride ? $this->snapshot($override) : null;

        $override = $override instanceof TenantFeatureOverride
            ? tap($override)->update($this->payloadFromSnapshot($snapshot))
            : TenantFeatureOverride::query()->create($this->payloadFromSnapshot($snapshot));

        $this->audit->record(
            'admin_tenant_feature_override_restored',
            $request,
            $admin,
            severity: $this->severity($override),
            metadata: [
                'tenant_feature_override_id' => $override->id,
                'tenant_id' => $override->tenant_id,
                'feature_key' => $override->feature_key,
                'source_audit_event_id' => $sourceEvent->id,
                'before' => $before,
                'after' => $this->snapshot($override),
            ],
        );

        return $override;
    }

    /**
     * @return array<string, mixed>
     */
    public function snapshot(TenantFeatureOverride $override): array
    {
        return [
            'id' => $override->id,
            'tenant_id' => $override->tenant_id,
            'feature_key' => $override->feature_key,
            'state' => $override->state?->value,
            'reason' => $override->reason,
            'message' => $override->message,
            'offline_behavior' => $override->offline_behavior,
            'metadata' => $this->arrayValue($override->metadata),
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
            'tenant_id' => (int) $data['tenant_id'],
            'feature_key' => $this->requiredString($data['feature_key'] ?? ''),
            'state' => $this->requiredString($data['state'] ?? MobileFeatureState::Disabled->value),
            'reason' => $this->nullableString($data['reason'] ?? null),
            'message' => $this->nullableString($data['message'] ?? null),
            'offline_behavior' => $this->nullableString($data['offline_behavior'] ?? null),
            'metadata' => $metadata,
        ];
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>
     */
    private function payloadFromSnapshot(array $snapshot): array
    {
        return [
            'tenant_id' => (int) $snapshot['tenant_id'],
            'feature_key' => $this->requiredString($snapshot['feature_key'] ?? ''),
            'state' => $this->requiredString($snapshot['state'] ?? MobileFeatureState::Disabled->value),
            'reason' => $this->nullableString($snapshot['reason'] ?? null),
            'message' => $this->nullableString($snapshot['message'] ?? null),
            'offline_behavior' => $this->nullableString($snapshot['offline_behavior'] ?? null),
            'metadata' => $this->arrayValue($snapshot['metadata'] ?? []),
        ];
    }

    private function requiredString(mixed $value): string
    {
        $value = is_string($value) ? trim($value) : '';

        return $value === '' ? MobileFeatureState::Disabled->value : $value;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' ? null : $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function arrayValue(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    private function severity(TenantFeatureOverride $override): string
    {
        return in_array($override->state, [
            MobileFeatureState::Disabled,
            MobileFeatureState::Blocked,
            MobileFeatureState::EmergencyDisabled,
            MobileFeatureState::Hidden,
        ], true) ? 'warning' : 'info';
    }
}
