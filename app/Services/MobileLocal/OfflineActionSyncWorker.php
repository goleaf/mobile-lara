<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalActivityLog;
use App\Models\MobileLocalOfflineAction;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

final class OfflineActionSyncWorker
{
    public function __construct(
        private readonly OfflineActionRepository $offlineActions,
        private readonly ActivityLogRepository $activityLogs,
    ) {}

    /**
     * @return array{processed: int, completed: int, failed: int}
     */
    public function process(?int $limit = null): array
    {
        $processed = 0;
        $completed = 0;
        $failed = 0;

        foreach ($this->offlineActions->readyForSync($limit ?: $this->batchSize()) as $offlineAction) {
            $processed++;

            if ($this->processAction($offlineAction)) {
                $completed++;

                continue;
            }

            $failed++;
        }

        return [
            'processed' => $processed,
            'completed' => $completed,
            'failed' => $failed,
        ];
    }

    private function processAction(MobileLocalOfflineAction $offlineAction): bool
    {
        $processing = $this->offlineActions->markProcessing($offlineAction);

        try {
            $response = $this->send($processing);

            if ($response->successful()) {
                $completed = $this->offlineActions->markCompleted($processing);
                $this->recordCompleted($completed, $response);

                return true;
            }

            if ($response->status() === 409) {
                $conflict = $this->offlineActions->markConflict(
                    offlineAction: $processing,
                    localVersion: $this->conflictLocalVersion($processing, $response),
                    remoteVersion: $this->conflictRemoteVersion($response),
                    conflictPayload: $this->conflictPayload($processing, $response),
                    lastError: 'Remote changes conflict with the local offline action.',
                );

                $this->recordConflict($conflict, $response);

                return false;
            }

            $this->failAction(
                offlineAction: $processing,
                lastError: $this->responseFailureMessage($response),
                response: $response,
            );

            return false;
        } catch (ConnectionException $exception) {
            $this->failAction(
                offlineAction: $processing,
                lastError: $this->connectionFailureMessage($exception),
            );

            return false;
        } catch (Throwable $exception) {
            $this->failAction(
                offlineAction: $processing,
                lastError: $this->unexpectedFailureMessage($exception),
            );

            return false;
        }
    }

    private function send(MobileLocalOfflineAction $offlineAction): Response
    {
        return Http::acceptJson()
            ->asJson()
            ->withHeaders($this->headers($offlineAction))
            ->timeout($this->timeoutSeconds())
            ->connectTimeout($this->connectTimeoutSeconds())
            ->send($this->method($offlineAction), $this->url($offlineAction), [
                'json' => $offlineAction->payload ?? [],
            ]);
    }

    private function failAction(
        MobileLocalOfflineAction $offlineAction,
        string $lastError,
        ?Response $response = null,
    ): MobileLocalOfflineAction {
        $availableAt = $this->nextAvailableAt($offlineAction);

        $failed = $this->offlineActions->markFailed(
            offlineAction: $offlineAction,
            lastError: $lastError,
            availableAt: $availableAt,
        );

        $this->recordFailed($failed, $lastError, $availableAt, $response);

        return $failed;
    }

    private function recordCompleted(MobileLocalOfflineAction $offlineAction, Response $response): void
    {
        $this->activityLogs->record(
            action: 'offline_action.synced',
            entityType: 'offline_action',
            entityId: $offlineAction->getKey(),
            message: 'Offline action synced successfully.',
            metadata: [
                'action_type' => $offlineAction->action_type,
                'endpoint' => $offlineAction->endpoint,
                'method' => $offlineAction->method,
                'attempts' => $offlineAction->attempts,
                'status_code' => $response->status(),
            ],
            syncStatus: MobileLocalActivityLog::SYNC_SYNCED,
        );
    }

    private function recordConflict(MobileLocalOfflineAction $offlineAction, Response $response): void
    {
        $this->activityLogs->record(
            action: 'offline_action.conflict_detected',
            entityType: 'offline_action',
            entityId: $offlineAction->getKey(),
            message: 'Offline action needs conflict resolution.',
            metadata: [
                'action_type' => $offlineAction->action_type,
                'endpoint' => $offlineAction->endpoint,
                'method' => $offlineAction->method,
                'attempts' => $offlineAction->attempts,
                'local_version' => $offlineAction->local_version,
                'remote_version' => $offlineAction->remote_version,
                'status_code' => $response->status(),
            ],
            syncStatus: MobileLocalActivityLog::SYNC_FAILED,
        );
    }

    private function recordFailed(
        MobileLocalOfflineAction $offlineAction,
        string $lastError,
        CarbonImmutable $availableAt,
        ?Response $response = null,
    ): void {
        $this->activityLogs->record(
            action: 'offline_action.sync_failed',
            entityType: 'offline_action',
            entityId: $offlineAction->getKey(),
            message: 'Offline action sync failed.',
            metadata: [
                'action_type' => $offlineAction->action_type,
                'endpoint' => $offlineAction->endpoint,
                'method' => $offlineAction->method,
                'attempts' => $offlineAction->attempts,
                'last_error' => $lastError,
                'next_retry_at' => $availableAt->toIso8601String(),
                'status_code' => $response?->status(),
            ],
            syncStatus: MobileLocalActivityLog::SYNC_FAILED,
        );
    }

    /**
     * @return array<string, string>
     */
    private function headers(MobileLocalOfflineAction $offlineAction): array
    {
        $headers = [];

        foreach (($offlineAction->headers ?? []) as $key => $value) {
            if (! is_string($key) || $key === '' || is_array($value) || is_object($value)) {
                continue;
            }

            $headers[$key] = (string) $value;
        }

        return $headers;
    }

    private function method(MobileLocalOfflineAction $offlineAction): string
    {
        $method = mb_strtoupper($offlineAction->method);

        return in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true) ? $method : 'POST';
    }

    private function url(MobileLocalOfflineAction $offlineAction): string
    {
        $endpoint = $offlineAction->endpoint;

        if (Str::startsWith($endpoint, ['http://', 'https://'])) {
            return $endpoint;
        }

        $baseUrl = (string) config('mobile_local.sync.base_url', config('app.url'));

        if (trim($baseUrl) === '') {
            $baseUrl = (string) config('app.url');
        }

        return rtrim($baseUrl, '/').'/'.ltrim($endpoint, '/');
    }

    private function nextAvailableAt(MobileLocalOfflineAction $offlineAction): CarbonImmutable
    {
        $nextAttempt = max(1, $offlineAction->attempts + 1);
        $multiplier = 2 ** min(10, $nextAttempt - 1);
        $delay = min($this->maxBackoffSeconds(), $this->baseBackoffSeconds() * $multiplier);

        return CarbonImmutable::now()->addSeconds($delay);
    }

    private function responseFailureMessage(Response $response): string
    {
        return "HTTP {$response->status()} returned while syncing offline action.";
    }

    /**
     * @return array<string, mixed>
     */
    private function conflictPayload(MobileLocalOfflineAction $offlineAction, Response $response): array
    {
        $responsePayload = $response->json();

        if (! is_array($responsePayload)) {
            $responsePayload = [];
        }

        return [
            'local' => $offlineAction->payload ?? [],
            'remote' => data_get($responsePayload, 'remote', data_get($responsePayload, 'remote_payload', [])),
            'server' => $responsePayload,
        ];
    }

    private function conflictLocalVersion(MobileLocalOfflineAction $offlineAction, Response $response): ?string
    {
        $responsePayload = $response->json();
        $localVersion = is_array($responsePayload) ? data_get($responsePayload, 'local_version') : null;

        return is_scalar($localVersion) ? (string) $localVersion : $offlineAction->local_version;
    }

    private function conflictRemoteVersion(Response $response): ?string
    {
        $responsePayload = $response->json();
        $remoteVersion = is_array($responsePayload) ? data_get($responsePayload, 'remote_version') : null;

        return is_scalar($remoteVersion) ? (string) $remoteVersion : null;
    }

    private function connectionFailureMessage(ConnectionException $exception): string
    {
        $message = trim($exception->getMessage());

        return $message === ''
            ? 'Connection failed while syncing offline action.'
            : "Connection failed while syncing offline action: {$message}";
    }

    private function unexpectedFailureMessage(Throwable $exception): string
    {
        $message = trim($exception->getMessage());

        return $message === ''
            ? 'Unexpected error while syncing offline action.'
            : "Unexpected error while syncing offline action: {$message}";
    }

    private function batchSize(): int
    {
        return $this->configInt('batch_size', 25, 1, 100);
    }

    private function timeoutSeconds(): int
    {
        return $this->configInt('timeout_seconds', 10, 1, 120);
    }

    private function connectTimeoutSeconds(): int
    {
        return $this->configInt('connect_timeout_seconds', 5, 1, 60);
    }

    private function baseBackoffSeconds(): int
    {
        return $this->configInt('base_backoff_seconds', 60, 1, 86400);
    }

    private function maxBackoffSeconds(): int
    {
        return $this->configInt('max_backoff_seconds', 3600, 1, 86400);
    }

    private function configInt(string $key, int $default, int $min, int $max): int
    {
        $value = (int) config("mobile_local.sync.{$key}", $default);

        return max($min, min($value, $max));
    }
}
