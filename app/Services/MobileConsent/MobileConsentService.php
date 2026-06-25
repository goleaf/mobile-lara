<?php

namespace App\Services\MobileConsent;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

final class MobileConsentService
{
    public function __construct(
        private readonly Session $session,
        private readonly Request $request,
    ) {}

    /**
     * @return array<string, array<string, mixed>>
     */
    public function policies(): array
    {
        $policies = config('mobile_consent.policies', []);

        return is_array($policies) ? $policies : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function policy(string $key): array
    {
        $policy = $this->policies()[$key] ?? [];

        return is_array($policy) ? $policy : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function acceptLatest(?CarbonInterface $acceptedAt = null): array
    {
        $acceptedAt = $this->immutableTime($acceptedAt);
        $records = $this->acceptedRecords();

        foreach ($this->policies() as $policyKey => $policy) {
            $policyKey = (string) $policyKey;
            $records[$policyKey] = $this->buildRecord($policyKey, $policy, $acceptedAt);
        }

        $this->session->put($this->sessionKey(), $records);

        return array_values($records);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function acceptedRecords(): array
    {
        $records = $this->session->get($this->sessionKey(), []);

        return is_array($records) ? $this->normalizeRecords($records) : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function acceptedHistory(): array
    {
        return array_values($this->acceptedRecords());
    }

    /**
     * @return array{
     *     endpoint: string,
     *     method: string,
     *     records: array<int, array<string, mixed>>
     * }
     */
    public function syncPayload(): array
    {
        return [
            'endpoint' => (string) config('mobile_consent.sync.endpoint', 'POST /api/mobile/consents'),
            'method' => 'POST',
            'records' => $this->acceptedHistory(),
        ];
    }

    public function hasAcceptedCurrentVersions(): bool
    {
        foreach ($this->policies() as $policyKey => $policy) {
            if (! $this->hasAcceptedCurrentVersion((string) $policyKey, $policy)) {
                return false;
            }
        }

        return true;
    }

    public function hasAcceptedCurrentVersion(string $policyKey, ?array $policy = null): bool
    {
        $policy ??= $this->policy($policyKey);
        $records = $this->acceptedRecords();
        $record = $records[$policyKey] ?? null;

        return is_array($record)
            && ($record['version'] ?? null) === ($policy['version'] ?? null);
    }

    private function buildRecord(string $policyKey, array $policy, CarbonImmutable $acceptedAt): array
    {
        $acceptedAtLabel = $acceptedAt
            ->timezone((string) config('app.timezone', 'UTC'))
            ->format('M j, Y g:i A');

        return [
            'id' => $policyKey.'-'.$policy['version'],
            'policy_key' => $policyKey,
            'policy_title' => (string) $policy['title'],
            'version' => (string) $policy['version'],
            'effective_date' => (string) $policy['effective_date'],
            'accepted_at' => $acceptedAt->toIso8601String(),
            'accepted_at_label' => $acceptedAtLabel,
            'locale' => app()->getLocale(),
            'app_version' => (string) config('nativephp.version', '1.0.0'),
            'app_version_code' => (string) config('nativephp.version_code', '1'),
            'device_session_reference' => $this->deviceSessionReference(),
            'device_label' => $this->deviceLabel(),
            'server_user_id' => is_null(Auth::id()) ? null : (string) Auth::id(),
            'sync_status' => (string) config('mobile_consent.sync.status', 'pending_server_sync'),
            'sync_endpoint' => (string) config('mobile_consent.sync.endpoint', 'POST /api/mobile/consents'),
        ];
    }

    /**
     * @param  array<string, mixed>  $records
     * @return array<string, array<string, mixed>>
     */
    private function normalizeRecords(array $records): array
    {
        $normalized = [];

        foreach ($records as $policyKey => $record) {
            if (! is_string($policyKey) || ! is_array($record)) {
                continue;
            }

            $acceptedAt = $this->parseAcceptedAt($record['accepted_at'] ?? null);
            $record['accepted_at_label'] = $acceptedAt?->timezone((string) config('app.timezone', 'UTC'))->format('M j, Y g:i A')
                ?? 'Not recorded';

            $normalized[$policyKey] = $record;
        }

        return $normalized;
    }

    private function parseAcceptedAt(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    private function deviceSessionReference(): string
    {
        $sessionId = $this->session->getId();

        if (! is_string($sessionId) || trim($sessionId) === '') {
            return 'pending-session';
        }

        return Str::upper(substr(hash('sha256', $sessionId), 0, 12));
    }

    private function deviceLabel(): string
    {
        $userAgent = Str::lower((string) $this->request->userAgent());

        return match (true) {
            Str::contains($userAgent, 'android') => 'Android app session',
            Str::contains($userAgent, ['iphone', 'ipad', 'ios']) => 'iOS app session',
            Str::contains($userAgent, 'mobile') => 'Mobile browser session',
            default => 'Mobile app session',
        };
    }

    private function immutableTime(?CarbonInterface $time): CarbonImmutable
    {
        return $time instanceof CarbonInterface
            ? CarbonImmutable::instance($time)
            : CarbonImmutable::now();
    }

    private function sessionKey(): string
    {
        return (string) config('mobile_consent.storage.session_key', 'mobile_consent.accepted_versions');
    }
}
