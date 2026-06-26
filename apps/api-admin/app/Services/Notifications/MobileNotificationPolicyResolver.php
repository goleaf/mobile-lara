<?php

namespace App\Services\Notifications;

use App\Models\Tenant;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;

final class MobileNotificationPolicyResolver
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
                'preferences' => $this->preferences(false, false, false, [], 'no_active_tenant'),
                'unread_count' => 0,
                'source' => 'tenant_context',
                'resolved_at' => $now->toIso8601String(),
                'policy_version' => 'notifications-none',
            ];
        }

        $settings = is_array($tenant->settings) ? $tenant->settings : [];
        $notificationSettings = $this->arrayValue(Arr::get($settings, 'notifications'));

        return [
            'status' => 'resolved',
            'preferences' => $this->preferences(
                $this->boolValue($notificationSettings['push_enabled'] ?? null, false),
                $this->boolValue($notificationSettings['in_app_enabled'] ?? null, true),
                $this->boolValue($notificationSettings['email_enabled'] ?? null, false),
                $this->quietHours($notificationSettings['quiet_hours'] ?? []),
                'tenant_notification_settings',
            ),
            'unread_count' => 0,
            'source' => 'tenant_notification_settings',
            'resolved_at' => $now->toIso8601String(),
            'policy_version' => $this->version($tenant, $notificationSettings),
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
            ->select(['id', 'public_id', 'settings', 'updated_at'])
            ->where('public_id', $publicId)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $quietHours
     * @return array<string, mixed>
     */
    private function preferences(bool $pushEnabled, bool $inAppEnabled, bool $emailEnabled, array $quietHours, string $status): array
    {
        return [
            'push_enabled' => $pushEnabled,
            'in_app_enabled' => $inAppEnabled,
            'email_enabled' => $emailEnabled,
            'quiet_hours' => $quietHours,
            'push_registration_required' => $pushEnabled,
            'status' => $status,
        ];
    }

    /**
     * @return array{enabled: bool, starts_at: string|null, ends_at: string|null, timezone: string|null}
     */
    private function quietHours(mixed $value): array
    {
        $value = $this->arrayValue($value);

        return [
            'enabled' => $this->boolValue($value['enabled'] ?? null, false),
            'starts_at' => $this->nullableString($value['starts_at'] ?? null),
            'ends_at' => $this->nullableString($value['ends_at'] ?? null),
            'timezone' => $this->nullableString($value['timezone'] ?? null),
        ];
    }

    private function boolValue(mixed $value, bool $default): bool
    {
        return is_bool($value) ? $value : $default;
    }

    /**
     * @return array<string, mixed>
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

    /**
     * @param  array<string, mixed>  $settings
     */
    private function version(Tenant $tenant, array $settings): string
    {
        $payload = json_encode([
            'tenant_id' => $tenant->id,
            'settings' => $settings,
            'updated_at' => $tenant->updated_at?->toIso8601String(),
        ]);

        return 'notifications-'.substr(sha1(is_string($payload) ? $payload : ''), 0, 16);
    }
}
