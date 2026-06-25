<?php

namespace App\Actions\Admin;

use App\Models\SecurityAuditEvent;
use App\Models\TenantRemoteConfigOverride;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;

final class SaveTenantRemoteConfigOverrideAction
{
    public function __construct(private MobileAuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $admin, Request $request, ?TenantRemoteConfigOverride $override = null): TenantRemoteConfigOverride
    {
        $creating = ! $override instanceof TenantRemoteConfigOverride;
        $before = $override instanceof TenantRemoteConfigOverride ? $this->snapshot($override) : null;

        $override = $override instanceof TenantRemoteConfigOverride
            ? tap($override)->update($this->payload($data, $override->metadata ?? []))
            : TenantRemoteConfigOverride::query()->create($this->payload($data));

        $this->audit->record(
            $creating ? 'admin_tenant_remote_config_override_created' : 'admin_tenant_remote_config_override_updated',
            $request,
            $admin,
            severity: 'info',
            metadata: [
                'tenant_remote_config_override_id' => $override->id,
                'tenant_id' => $override->tenant_id,
                'config_key' => $override->config_key,
                'before' => $before,
                'after' => $this->snapshot($override),
            ],
        );

        return $override;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    public function restore(array $snapshot, User $admin, Request $request, SecurityAuditEvent $sourceEvent): TenantRemoteConfigOverride
    {
        $overrideId = $snapshot['id'] ?? null;
        $override = is_int($overrideId) ? TenantRemoteConfigOverride::query()->find($overrideId) : null;
        $before = $override instanceof TenantRemoteConfigOverride ? $this->snapshot($override) : null;

        $override = $override instanceof TenantRemoteConfigOverride
            ? tap($override)->update($this->payloadFromSnapshot($snapshot))
            : TenantRemoteConfigOverride::query()->create($this->payloadFromSnapshot($snapshot));

        $this->audit->record(
            'admin_tenant_remote_config_override_restored',
            $request,
            $admin,
            severity: 'info',
            metadata: [
                'tenant_remote_config_override_id' => $override->id,
                'tenant_id' => $override->tenant_id,
                'config_key' => $override->config_key,
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
    public function snapshot(TenantRemoteConfigOverride $override): array
    {
        return [
            'id' => $override->id,
            'tenant_id' => $override->tenant_id,
            'config_key' => $override->config_key,
            'value' => $this->arrayValue($override->value),
            'version' => $override->version,
            'reason' => $override->reason,
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
            'config_key' => $this->requiredString($data['config_key'] ?? ''),
            'value' => $this->decodeValue($data['value_json'] ?? '{}'),
            'version' => $this->requiredString($data['version'] ?? 'tenant-default'),
            'reason' => $this->nullableString($data['reason'] ?? null),
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
            'config_key' => $this->requiredString($snapshot['config_key'] ?? ''),
            'value' => $this->arrayValue($snapshot['value'] ?? []),
            'version' => $this->requiredString($snapshot['version'] ?? 'tenant-default'),
            'reason' => $this->nullableString($snapshot['reason'] ?? null),
            'metadata' => $this->arrayValue($snapshot['metadata'] ?? []),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeValue(mixed $value): array
    {
        $decoded = json_decode(is_string($value) ? $value : '{}', true);

        return is_array($decoded) ? $decoded : [];
    }

    private function requiredString(mixed $value): string
    {
        $value = is_string($value) ? trim($value) : '';

        return $value === '' ? 'tenant-default' : $value;
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
}
