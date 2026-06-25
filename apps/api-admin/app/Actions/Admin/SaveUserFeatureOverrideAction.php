<?php

namespace App\Actions\Admin;

use App\Enums\MobileFeatureState;
use App\Models\SecurityAuditEvent;
use App\Models\User;
use App\Models\UserFeatureOverride;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;

final class SaveUserFeatureOverrideAction
{
    public function __construct(private MobileAuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $admin, Request $request, ?UserFeatureOverride $override = null): UserFeatureOverride
    {
        $creating = ! $override instanceof UserFeatureOverride;
        $before = $override instanceof UserFeatureOverride ? $this->snapshot($override) : null;

        $override = $override instanceof UserFeatureOverride
            ? tap($override)->update($this->payload($data, $override->metadata ?? []))
            : UserFeatureOverride::query()->create($this->payload($data));

        $this->audit->record(
            $creating ? 'admin_user_feature_override_created' : 'admin_user_feature_override_updated',
            $request,
            $admin,
            severity: $this->severity($override),
            metadata: [
                'user_feature_override_id' => $override->id,
                'tenant_id' => $override->tenant_id,
                'user_id' => $override->user_id,
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
    public function restore(array $snapshot, User $admin, Request $request, SecurityAuditEvent $sourceEvent): UserFeatureOverride
    {
        $overrideId = $snapshot['id'] ?? null;
        $override = is_int($overrideId) ? UserFeatureOverride::query()->find($overrideId) : null;
        $before = $override instanceof UserFeatureOverride ? $this->snapshot($override) : null;

        $override = $override instanceof UserFeatureOverride
            ? tap($override)->update($this->payloadFromSnapshot($snapshot))
            : UserFeatureOverride::query()->create($this->payloadFromSnapshot($snapshot));

        $this->audit->record(
            'admin_user_feature_override_restored',
            $request,
            $admin,
            severity: $this->severity($override),
            metadata: [
                'user_feature_override_id' => $override->id,
                'tenant_id' => $override->tenant_id,
                'user_id' => $override->user_id,
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
    public function snapshot(UserFeatureOverride $override): array
    {
        return [
            'id' => $override->id,
            'tenant_id' => $override->tenant_id,
            'user_id' => $override->user_id,
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
            'user_id' => (int) $data['user_id'],
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
            'user_id' => (int) $snapshot['user_id'],
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

    private function severity(UserFeatureOverride $override): string
    {
        return in_array($override->state, [
            MobileFeatureState::Disabled,
            MobileFeatureState::Blocked,
            MobileFeatureState::EmergencyDisabled,
            MobileFeatureState::Hidden,
        ], true) ? 'warning' : 'info';
    }
}
