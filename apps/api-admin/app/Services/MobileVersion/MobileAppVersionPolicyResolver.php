<?php

namespace App\Services\MobileVersion;

use App\Enums\MobileAppVersionState;
use App\Models\MobileAppVersionPolicy;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final class MobileAppVersionPolicyResolver
{
    /**
     * @param  array{current_tenant?: array<string, mixed>|null}  $tenantContext
     * @return array<string, mixed>
     */
    public function resolve(Request $request, array $tenantContext = []): array
    {
        $platform = $this->platform($request);
        $reportedVersion = $this->reportedVersion($request);
        $tenant = $this->tenantFromContext($tenantContext);
        $cohortKey = $this->cohortKey($request);
        $policy = $this->policy($platform, $tenant, $cohortKey, $reportedVersion);
        $state = $this->state($policy, $reportedVersion);
        $storeUrls = $this->arrayValue($policy?->store_urls) ?: ['ios' => null, 'android' => null];
        $allowedActions = $this->allowedActions($state, $policy);

        return [
            'state' => $state->value,
            'status' => $state->value,
            'reported_platform' => $platform,
            'reported_version' => $reportedVersion,
            'reported_version_code' => $this->reportedVersionCode($request),
            'minimum_supported_version' => $policy?->minimum_supported_version ?? '1.0.0',
            'minimum_recommended_version' => $policy?->minimum_recommended_version,
            'latest_version' => $policy?->latest_version,
            'optional_update' => in_array($state, [
                MobileAppVersionState::OptionalUpdate,
                MobileAppVersionState::RecommendedUpdate,
                MobileAppVersionState::Deprecated,
            ], true),
            'force_update' => in_array($state, [
                MobileAppVersionState::ForceUpdate,
                MobileAppVersionState::Blocked,
                MobileAppVersionState::InternalOnly,
                MobileAppVersionState::StaleClient,
            ], true),
            'store_url' => $this->storeUrl($storeUrls, $platform),
            'store_urls' => $storeUrls,
            'message' => $this->message($state, $policy),
            'support_url' => $policy?->support_url,
            'retry_after' => $policy?->retry_after_seconds,
            'allowed_actions' => $allowedActions,
            'logout_allowed' => $policy?->logout_allowed ?? true,
            'policy_scope' => $this->policyScope($policy, $tenant, $cohortKey),
            'maintenance' => [
                'enabled' => $state === MobileAppVersionState::Maintenance,
                'message' => $policy?->maintenance_message,
                'support_url' => $policy?->support_url,
                'retry_after' => $policy?->retry_after_seconds,
            ],
            'policy_version' => $this->policyVersion($policy, $state, $platform, $reportedVersion, $cohortKey),
            'policy_source' => $policy instanceof MobileAppVersionPolicy ? 'database_policy' : 'foundation_default',
        ];
    }

    /**
     * @param  array<string, mixed>  $resolved
     * @return array<string, mixed>
     */
    public function endpointPayload(array $resolved): array
    {
        return $resolved;
    }

    private function platform(Request $request): string
    {
        $platform = $request->header('X-Mobile-Platform', 'unknown');

        return is_string($platform) && trim($platform) !== '' ? str($platform)->lower()->toString() : 'unknown';
    }

    private function reportedVersion(Request $request): ?string
    {
        $version = $request->header('X-Mobile-App-Version');

        return is_string($version) && trim($version) !== '' ? trim($version) : null;
    }

    private function reportedVersionCode(Request $request): ?string
    {
        $versionCode = $request->header('X-Mobile-App-Version-Code');

        return is_string($versionCode) && trim($versionCode) !== '' ? trim($versionCode) : null;
    }

    private function cohortKey(Request $request): ?string
    {
        $cohort = $request->header('X-Mobile-Cohort')
            ?: $request->header('X-Mobile-Rollout-Cohort');

        if (! is_string($cohort) || trim($cohort) === '') {
            return null;
        }

        $cohort = str($cohort)->lower()->trim()->toString();

        return preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $cohort) === 1 ? $cohort : null;
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
            ->select(['id', 'public_id', 'name'])
            ->where('public_id', $publicId)
            ->first();
    }

    private function policy(string $platform, ?Tenant $tenant, ?string $cohortKey, ?string $reportedVersion): ?MobileAppVersionPolicy
    {
        $policies = MobileAppVersionPolicy::query()
            ->activeForPlatform($platform)
            ->get()
            ->filter(fn (MobileAppVersionPolicy $policy): bool => $this->policyAppliesToReportedVersion($policy, $reportedVersion))
            ->values();

        if ($tenant instanceof Tenant) {
            $tenantPolicy = $this->firstPolicy($policies, $platform, $tenant->id, null)
                ?? $this->firstPolicy($policies, 'all', $tenant->id, null);

            if ($tenantPolicy instanceof MobileAppVersionPolicy) {
                return $tenantPolicy;
            }
        }

        if ($cohortKey !== null) {
            $cohortPolicy = $this->firstPolicy($policies, $platform, null, $cohortKey)
                ?? $this->firstPolicy($policies, 'all', null, $cohortKey);

            if ($cohortPolicy instanceof MobileAppVersionPolicy) {
                return $cohortPolicy;
            }
        }

        return $this->firstPolicy($policies, $platform, null, null)
            ?? $this->firstPolicy($policies, 'all', null, null);
    }

    /**
     * @param  Collection<int, MobileAppVersionPolicy>  $policies
     */
    private function firstPolicy(Collection $policies, string $platform, ?int $tenantId, ?string $cohortKey): ?MobileAppVersionPolicy
    {
        return $policies->first(
            fn (MobileAppVersionPolicy $policy): bool => $policy->platform === $platform
                && $policy->tenant_id === $tenantId
                && $policy->cohort_key === $cohortKey,
        );
    }

    private function policyAppliesToReportedVersion(MobileAppVersionPolicy $policy, ?string $reportedVersion): bool
    {
        if ($policy->applies_from_version === null && $policy->applies_until_version === null) {
            return true;
        }

        if ($reportedVersion === null) {
            return false;
        }

        if ($policy->applies_from_version !== null && version_compare($reportedVersion, $policy->applies_from_version, '<')) {
            return false;
        }

        if ($policy->applies_until_version !== null && version_compare($reportedVersion, $policy->applies_until_version, '>')) {
            return false;
        }

        return true;
    }

    private function state(?MobileAppVersionPolicy $policy, ?string $reportedVersion): MobileAppVersionState
    {
        if (! $policy instanceof MobileAppVersionPolicy) {
            return MobileAppVersionState::Supported;
        }

        if ($policy->maintenance_enabled) {
            return MobileAppVersionState::Maintenance;
        }

        if ($reportedVersion === null) {
            return MobileAppVersionState::Supported;
        }

        if (in_array($reportedVersion, $this->arrayValue($policy->blocked_versions), true)) {
            return MobileAppVersionState::Blocked;
        }

        if ($policy->force_update || $this->isVersionBelow($reportedVersion, $policy->minimum_supported_version)) {
            return MobileAppVersionState::ForceUpdate;
        }

        if ($policy->minimum_recommended_version !== null && $this->isVersionBelow($reportedVersion, $policy->minimum_recommended_version)) {
            return MobileAppVersionState::OptionalUpdate;
        }

        if ($policy->latest_version !== null && ! $this->isVersionBelow($reportedVersion, $policy->latest_version)) {
            return MobileAppVersionState::Current;
        }

        return MobileAppVersionState::Supported;
    }

    private function isVersionBelow(string $version, ?string $minimum): bool
    {
        if ($minimum === null || trim($minimum) === '') {
            return false;
        }

        return version_compare($version, $minimum, '<');
    }

    /**
     * @return array<int|string, mixed>
     */
    private function arrayValue(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /**
     * @param  array<string, string|null>  $storeUrls
     */
    private function storeUrl(array $storeUrls, string $platform): ?string
    {
        $url = $storeUrls[$platform] ?? null;

        return is_string($url) && trim($url) !== '' ? $url : null;
    }

    /**
     * @return array<int, string>
     */
    private function allowedActions(MobileAppVersionState $state, ?MobileAppVersionPolicy $policy): array
    {
        if ($state === MobileAppVersionState::ForceUpdate) {
            return ['update', 'support', 'logout'];
        }

        if ($state === MobileAppVersionState::Maintenance) {
            return $this->stringList($policy?->allowed_actions) ?: ['retry', 'support', 'logout'];
        }

        if ($state === MobileAppVersionState::Blocked) {
            return ['support', 'logout'];
        }

        return $this->stringList($policy?->allowed_actions) ?: ['continue', 'logout', 'support'];
    }

    /**
     * @return array<int, string>
     */
    private function stringList(mixed $value): array
    {
        return collect($this->arrayValue($value))
            ->filter(static fn (mixed $item): bool => is_string($item) && trim($item) !== '')
            ->values()
            ->all();
    }

    private function message(MobileAppVersionState $state, ?MobileAppVersionPolicy $policy): ?string
    {
        if ($state === MobileAppVersionState::Maintenance) {
            return $policy?->maintenance_message;
        }

        return $policy?->message;
    }

    /**
     * @return array{type: string, platform: string|null, tenant_id: string|null, cohort_key: string|null, applies_from_version: string|null, applies_until_version: string|null}
     */
    private function policyScope(?MobileAppVersionPolicy $policy, ?Tenant $tenant, ?string $cohortKey): array
    {
        if (! $policy instanceof MobileAppVersionPolicy) {
            return [
                'type' => 'foundation',
                'platform' => null,
                'tenant_id' => null,
                'cohort_key' => $cohortKey,
                'applies_from_version' => null,
                'applies_until_version' => null,
            ];
        }

        return [
            'type' => $policy->scopeType(),
            'platform' => $policy->platform,
            'tenant_id' => $policy->tenant_id !== null && $tenant instanceof Tenant ? $tenant->public_id : null,
            'cohort_key' => $policy->cohort_key,
            'applies_from_version' => $policy->applies_from_version,
            'applies_until_version' => $policy->applies_until_version,
        ];
    }

    private function policyVersion(?MobileAppVersionPolicy $policy, MobileAppVersionState $state, string $platform, ?string $reportedVersion, ?string $cohortKey): string
    {
        $payload = json_encode([
            'policy_id' => $policy?->id,
            'policy_scope' => $policy?->scopeType(),
            'tenant_id' => $policy?->tenant_id,
            'cohort_key' => $policy?->cohort_key,
            'applies_from_version' => $policy?->applies_from_version,
            'applies_until_version' => $policy?->applies_until_version,
            'reported_cohort_key' => $cohortKey,
            'updated_at' => $policy?->updated_at?->toIso8601String(),
            'state' => $state->value,
            'platform' => $platform,
            'version' => $reportedVersion,
        ]);

        return 'app-version-'.substr(sha1(is_string($payload) ? $payload : ''), 0, 16);
    }
}
