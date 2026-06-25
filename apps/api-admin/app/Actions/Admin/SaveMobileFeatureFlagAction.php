<?php

namespace App\Actions\Admin;

use App\Models\MobileFeatureFlag;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;

final class SaveMobileFeatureFlagAction
{
    public function __construct(private MobileAuditLogger $audit) {}

    /**
     * @param  array{key: string, name: string, default_state: string, reason?: string|null, message?: string|null, minimum_app_version?: string|null, offline_behavior: string}  $data
     */
    public function handle(array $data, User $admin, Request $request, ?MobileFeatureFlag $featureFlag = null): MobileFeatureFlag
    {
        $creating = ! $featureFlag instanceof MobileFeatureFlag;
        $before = $featureFlag instanceof MobileFeatureFlag ? $this->snapshot($featureFlag) : null;

        $payload = [
            'key' => $data['key'],
            'name' => $data['name'],
            'default_state' => $data['default_state'],
            'reason' => $this->nullableString($data['reason'] ?? null),
            'message' => $this->nullableString($data['message'] ?? null),
            'minimum_app_version' => $this->nullableString($data['minimum_app_version'] ?? null),
            'offline_behavior' => $data['offline_behavior'],
            'metadata' => $featureFlag?->metadata ?? [],
        ];

        $featureFlag = $featureFlag instanceof MobileFeatureFlag
            ? tap($featureFlag)->update($payload)
            : MobileFeatureFlag::query()->create($payload);

        $this->audit->record(
            $creating ? 'admin_mobile_feature_flag_created' : 'admin_mobile_feature_flag_updated',
            $request,
            $admin,
            severity: 'info',
            metadata: [
                'feature_flag_id' => $featureFlag->id,
                'feature_key' => $featureFlag->key,
                'before' => $before,
                'after' => $this->snapshot($featureFlag),
            ],
        );

        return $featureFlag;
    }

    private function nullableString(?string $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' ? null : $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(MobileFeatureFlag $featureFlag): array
    {
        return [
            'key' => $featureFlag->key,
            'name' => $featureFlag->name,
            'default_state' => $featureFlag->default_state?->value,
            'reason' => $featureFlag->reason,
            'message' => $featureFlag->message,
            'minimum_app_version' => $featureFlag->minimum_app_version,
            'offline_behavior' => $featureFlag->offline_behavior,
        ];
    }
}
