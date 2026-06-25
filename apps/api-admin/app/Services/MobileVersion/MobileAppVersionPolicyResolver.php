<?php

namespace App\Services\MobileVersion;

use App\Enums\MobileAppVersionState;
use App\Models\MobileAppVersionPolicy;
use Illuminate\Http\Request;

final class MobileAppVersionPolicyResolver
{
    public function resolve(Request $request): array
    {
        $platform = $this->platform($request);
        $reportedVersion = $this->reportedVersion($request);
        $policy = $this->policy($platform);
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
            'maintenance' => [
                'enabled' => $state === MobileAppVersionState::Maintenance,
                'message' => $policy?->maintenance_message,
                'support_url' => $policy?->support_url,
                'retry_after' => $policy?->retry_after_seconds,
            ],
            'policy_version' => $this->policyVersion($policy, $state, $platform, $reportedVersion),
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

    private function policy(string $platform): ?MobileAppVersionPolicy
    {
        $policies = MobileAppVersionPolicy::query()
            ->activeForPlatform($platform)
            ->get();

        return $policies->firstWhere('platform', $platform)
            ?? $policies->firstWhere('platform', 'all');
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

    private function policyVersion(?MobileAppVersionPolicy $policy, MobileAppVersionState $state, string $platform, ?string $reportedVersion): string
    {
        $payload = json_encode([
            'policy_id' => $policy?->id,
            'updated_at' => $policy?->updated_at?->toIso8601String(),
            'state' => $state->value,
            'platform' => $platform,
            'version' => $reportedVersion,
        ]);

        return 'app-version-'.substr(sha1(is_string($payload) ? $payload : ''), 0, 16);
    }
}
