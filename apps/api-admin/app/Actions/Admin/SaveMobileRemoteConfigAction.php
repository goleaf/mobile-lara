<?php

namespace App\Actions\Admin;

use App\Models\MobileRemoteConfig;
use App\Models\SecurityAuditEvent;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;

final class SaveMobileRemoteConfigAction
{
    public function __construct(private MobileAuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $admin, Request $request, ?MobileRemoteConfig $config = null): MobileRemoteConfig
    {
        $creating = ! $config instanceof MobileRemoteConfig;
        $before = $config instanceof MobileRemoteConfig ? $this->snapshot($config) : null;

        $config = $config instanceof MobileRemoteConfig
            ? tap($config)->update($this->payload($data, $config->metadata ?? []))
            : MobileRemoteConfig::query()->create($this->payload($data));

        $this->audit->record(
            $creating ? 'admin_mobile_remote_config_created' : 'admin_mobile_remote_config_updated',
            $request,
            $admin,
            severity: $config->is_sensitive ? 'warning' : 'info',
            metadata: [
                'mobile_remote_config_id' => $config->id,
                'config_key' => $config->key,
                'before' => $before,
                'after' => $this->snapshot($config),
            ],
        );

        return $config;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    public function restore(array $snapshot, User $admin, Request $request, SecurityAuditEvent $sourceEvent): MobileRemoteConfig
    {
        $configId = $snapshot['id'] ?? null;
        $config = is_int($configId) ? MobileRemoteConfig::query()->find($configId) : null;
        $before = $config instanceof MobileRemoteConfig ? $this->snapshot($config) : null;

        $config = $config instanceof MobileRemoteConfig
            ? tap($config)->update($this->payloadFromSnapshot($snapshot))
            : MobileRemoteConfig::query()->create($this->payloadFromSnapshot($snapshot));

        $this->audit->record(
            'admin_mobile_remote_config_restored',
            $request,
            $admin,
            severity: $config->is_sensitive ? 'warning' : 'info',
            metadata: [
                'mobile_remote_config_id' => $config->id,
                'config_key' => $config->key,
                'source_audit_event_id' => $sourceEvent->id,
                'before' => $before,
                'after' => $this->snapshot($config),
            ],
        );

        return $config;
    }

    /**
     * @return array<string, mixed>
     */
    public function snapshot(MobileRemoteConfig $config): array
    {
        return [
            'id' => $config->id,
            'key' => $config->key,
            'category' => $config->category,
            'value' => $this->arrayValue($config->value),
            'version' => $config->version,
            'description' => $config->description,
            'is_sensitive' => $config->is_sensitive,
            'metadata' => $this->arrayValue($config->metadata),
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
            'key' => $this->requiredString($data['key'] ?? ''),
            'category' => 'mobile',
            'value' => $this->decodeValue($data['value_json'] ?? '{}'),
            'version' => $this->requiredString($data['version'] ?? 'global-default'),
            'description' => $this->nullableString($data['description'] ?? null),
            'is_sensitive' => (bool) ($data['is_sensitive'] ?? false),
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
            'key' => $this->requiredString($snapshot['key'] ?? ''),
            'category' => 'mobile',
            'value' => $this->arrayValue($snapshot['value'] ?? []),
            'version' => $this->requiredString($snapshot['version'] ?? 'global-default'),
            'description' => $this->nullableString($snapshot['description'] ?? null),
            'is_sensitive' => (bool) ($snapshot['is_sensitive'] ?? false),
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

        return $value === '' ? 'global-default' : $value;
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
