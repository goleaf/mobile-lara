<?php

namespace App\Actions\Admin;

use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;

final class SaveTenantAction
{
    public function __construct(private MobileAuditLogger $audit) {}

    /**
     * @param  array{name: string, slug: string, status: string, subscription_state: string, settings_json?: string|null}  $data
     */
    public function handle(array $data, User $admin, Request $request, ?Tenant $tenant = null): Tenant
    {
        $creating = ! $tenant instanceof Tenant;
        $before = $tenant instanceof Tenant ? $this->snapshot($tenant) : null;

        $payload = [
            'name' => trim($data['name']),
            'slug' => str($data['slug'])->lower()->trim()->toString(),
            'status' => $data['status'],
            'subscription_state' => str($data['subscription_state'])->lower()->trim()->toString(),
            'settings' => $this->settingsPayload($data['settings_json'] ?? null),
        ];

        $tenant = $tenant instanceof Tenant
            ? tap($tenant)->update($payload)
            : Tenant::query()->create($payload);

        $this->audit->record(
            $creating ? 'admin_tenant_created' : 'admin_tenant_updated',
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

    /**
     * @return array<string, mixed>
     */
    private function settingsPayload(?string $settingsJson): array
    {
        $settingsJson = is_string($settingsJson) ? trim($settingsJson) : '';

        if ($settingsJson === '') {
            return [];
        }

        $decoded = json_decode($settingsJson, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(Tenant $tenant): array
    {
        return [
            'public_id' => $tenant->public_id,
            'name' => $tenant->name,
            'slug' => $tenant->slug,
            'status' => $tenant->status?->value,
            'subscription_state' => $tenant->subscription_state,
            'settings' => is_array($tenant->settings) ? $tenant->settings : [],
        ];
    }
}
