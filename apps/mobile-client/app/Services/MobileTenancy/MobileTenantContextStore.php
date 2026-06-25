<?php

namespace App\Services\MobileTenancy;

use App\Services\MobileLocal\SettingsRepository;
use Illuminate\Database\QueryException;

final class MobileTenantContextStore
{
    public function __construct(private readonly SettingsRepository $settings) {}

    /**
     * @return array{
     *     current_tenant: array<string, mixed>|null,
     *     available_tenants: array<int, array<string, mixed>>,
     *     cached_at: string|null
     * }
     */
    public function context(): array
    {
        try {
            $settings = $this->settings->get();
        } catch (QueryException $exception) {
            if ($this->isMissingSettingsTable($exception)) {
                return $this->emptyContext();
            }

            throw $exception;
        }

        $envelope = is_array($settings->bootstrap_context) ? $settings->bootstrap_context : [];
        $data = is_array($envelope['data'] ?? null) ? $envelope['data'] : [];
        $currentTenant = is_array($data['current_tenant'] ?? null) ? $data['current_tenant'] : null;
        $availableTenants = is_array($data['available_tenants'] ?? null) ? $data['available_tenants'] : [];

        return [
            'current_tenant' => $currentTenant,
            'available_tenants' => array_values(array_filter(
                $availableTenants,
                static fn (mixed $tenant): bool => is_array($tenant),
            )),
            'cached_at' => $settings->bootstrap_cached_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function currentTenant(): ?array
    {
        return $this->context()['current_tenant'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function availableTenants(): array
    {
        return $this->context()['available_tenants'];
    }

    /**
     * @return array{
     *     current_tenant: null,
     *     available_tenants: array<int, array<string, mixed>>,
     *     cached_at: null
     * }
     */
    private function emptyContext(): array
    {
        return [
            'current_tenant' => null,
            'available_tenants' => [],
            'cached_at' => null,
        ];
    }

    private function isMissingSettingsTable(QueryException $exception): bool
    {
        $message = $exception->getMessage();

        return str_contains($message, 'mobile_local_settings')
            && (str_contains($message, 'no such table') || str_contains($message, 'Base table or view not found'));
    }
}
