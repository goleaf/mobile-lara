<?php

namespace App\Actions\Diagnostics;

use App\Models\MobileDeviceSession;
use App\Models\MobileDiagnosticReport;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class StoreMobileDiagnosticReportAction
{
    /**
     * @var list<string>
     */
    private const ALLOWED_SNAPSHOT_KEYS = [
        'generated_at',
        'app',
        'user',
        'tenant',
        'features',
        'remote_config',
        'network',
        'sync',
        'failed_sync_actions',
        'device',
        'redactions_applied',
    ];

    /**
     * @var list<string>
     */
    private const REDACTED_FIELDS = [
        'access_token',
        'api_key',
        'api_token',
        'authorization',
        'bearer',
        'client_secret',
        'cookie',
        'credential',
        'credentials',
        'email',
        'headers',
        'password',
        'payload',
        'private_key',
        'refresh_token',
        'secret',
        'token',
    ];

    public function __construct(private readonly MobileAuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, Tenant $tenant, User $user, MobileDeviceSession $session, Request $request): MobileDiagnosticReport
    {
        return DB::transaction(function () use ($data, $tenant, $user, $session, $request): MobileDiagnosticReport {
            $snapshot = $this->sanitizedSnapshot(is_array($data['snapshot'] ?? null) ? $data['snapshot'] : []);
            $redactionsApplied = $this->redactionsApplied($snapshot);

            $report = MobileDiagnosticReport::query()->create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'mobile_device_session_id' => $session->id,
                'app_version' => $this->stringOrNull(Arr::get($snapshot, 'app.app_version')),
                'api_base_url' => $this->safeUrl($this->stringOrNull(Arr::get($snapshot, 'app.api_base_url'))),
                'support_ticket_id' => $this->stringOrNull($data['support_ticket_id'] ?? null),
                'redactions_applied' => $redactionsApplied,
                'snapshot' => [
                    ...$snapshot,
                    'redactions_applied' => $redactionsApplied,
                    'server_context' => [
                        'tenant_id' => $tenant->public_id,
                        'user_id' => $user->getKey(),
                        'device_session_id' => $session->getKey(),
                    ],
                ],
                'failed_sync_actions_count' => $this->failedSyncActionsCount($snapshot),
                'generated_at' => $this->dateOrNull(Arr::get($snapshot, 'generated_at')),
                'received_at' => CarbonImmutable::now(),
            ]);

            $this->audit->record('mobile_diagnostics_uploaded', $request, $user, $session, metadata: [
                'tenant_public_id' => $tenant->public_id,
                'diagnostic_report_id' => $report->public_id,
                'diagnostic_public_id' => $report->public_id,
                'support_ticket_id' => $report->support_ticket_id,
                'failed_sync_actions_count' => $report->failed_sync_actions_count,
                'redactions_applied' => $redactionsApplied,
                'client_reference' => $this->stringOrNull($data['client_reference'] ?? null),
            ]);

            return $report;
        });
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>
     */
    private function sanitizedSnapshot(array $snapshot): array
    {
        $sanitized = [];

        foreach (self::ALLOWED_SNAPSHOT_KEYS as $key) {
            if (array_key_exists($key, $snapshot)) {
                $sanitized[$key] = $this->redact($snapshot[$key], $key);
            }
        }

        return $sanitized;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @return list<string>
     */
    private function redactionsApplied(array $snapshot): array
    {
        $clientRedactions = Arr::get($snapshot, 'redactions_applied', []);
        $redactions = is_array($clientRedactions) ? $clientRedactions : [];

        return collect($redactions)
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value): string => Str::of($value)->lower()->snake()->limit(80, '')->toString())
            ->push('server_keys')
            ->push('server_strings')
            ->push('server_side_redaction')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    private function failedSyncActionsCount(array $snapshot): int
    {
        $count = Arr::get($snapshot, 'sync.failed_actions');

        if (is_int($count) && $count >= 0) {
            return $count;
        }

        $failedActions = Arr::get($snapshot, 'failed_sync_actions', []);

        return is_array($failedActions) ? count($failedActions) : 0;
    }

    private function dateOrNull(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return CarbonImmutable::parse($value);
    }

    private function stringOrNull(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : Str::limit($value, 255, '');
    }

    private function redact(mixed $value, ?string $key = null): mixed
    {
        if ($key !== null && $this->isSensitiveKey($key)) {
            return '[redacted]';
        }

        if ($key !== null && in_array($this->normalizedKey($key), ['api_base_url', 'nativephp_start_url'], true)) {
            return $this->safeUrl(is_string($value) ? $value : null);
        }

        if (is_array($value)) {
            $redacted = [];

            foreach ($value as $childKey => $childValue) {
                $redacted[$childKey] = $this->redact($childValue, is_string($childKey) ? $childKey : null);
            }

            return $redacted;
        }

        return is_string($value) ? $this->redactedString($value) : $value;
    }

    private function isSensitiveKey(string $key): bool
    {
        $key = $this->normalizedKey($key);

        foreach (self::REDACTED_FIELDS as $field) {
            if ($key === $field || str_contains($key, "_{$field}") || str_contains($key, "{$field}_")) {
                return true;
            }
        }

        return false;
    }

    private function redactedString(string $value): string
    {
        $value = preg_replace('/Bearer\s+[A-Za-z0-9._\-]+/i', 'Bearer [redacted]', $value) ?: $value;
        $value = preg_replace('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', '[redacted-email]', $value) ?: $value;

        return Str::limit($value, 500, '...');
    }

    private function normalizedKey(string $key): string
    {
        return Str::of(Str::snake($key))->lower()->replace(['-', ' ', '.'], '_')->toString();
    }

    private function safeUrl(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $parts = parse_url(trim($url));

        if (! is_array($parts)) {
            return null;
        }

        $scheme = is_string($parts['scheme'] ?? null) ? $parts['scheme'].'://' : '';
        $host = is_string($parts['host'] ?? null) ? $parts['host'] : '';
        $port = is_int($parts['port'] ?? null) ? ':'.$parts['port'] : '';
        $path = is_string($parts['path'] ?? null) ? $parts['path'] : '';

        return $host === '' ? null : "{$scheme}{$host}{$port}{$path}";
    }
}
