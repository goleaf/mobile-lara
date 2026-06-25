<?php

namespace App\Services\MobileLocal;

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Models\MobileLocalOfflineAction;
use Carbon\CarbonInterface;
use InvalidArgumentException;

final class OfflineFirstActionQueue
{
    public const ACTION_CREATE = 'create';

    public const ACTION_UPDATE = 'update';

    public const ACTION_DELETE = 'delete';

    public function __construct(
        private readonly OfflineActionRepository $offlineActions,
        private readonly MobileNetworkState $networkState,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     */
    public function queueCreate(
        string $endpoint,
        array $payload = [],
        array $headers = [],
        ?CarbonInterface $availableAt = null,
    ): ?MobileLocalOfflineAction {
        return $this->queueWhenUnavailable(
            actionType: self::ACTION_CREATE,
            endpoint: $endpoint,
            method: 'POST',
            payload: $payload,
            headers: $headers,
            availableAt: $availableAt,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     */
    public function queueUpdate(
        string $endpoint,
        array $payload = [],
        array $headers = [],
        ?CarbonInterface $availableAt = null,
    ): ?MobileLocalOfflineAction {
        return $this->queueWhenUnavailable(
            actionType: self::ACTION_UPDATE,
            endpoint: $endpoint,
            method: 'PATCH',
            payload: $payload,
            headers: $headers,
            availableAt: $availableAt,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     */
    public function queueDelete(
        string $endpoint,
        array $payload = [],
        array $headers = [],
        ?CarbonInterface $availableAt = null,
    ): ?MobileLocalOfflineAction {
        return $this->queueWhenUnavailable(
            actionType: self::ACTION_DELETE,
            endpoint: $endpoint,
            method: 'DELETE',
            payload: $payload,
            headers: $headers,
            availableAt: $availableAt,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     */
    public function queueWhenUnavailable(
        string $actionType,
        string $endpoint,
        string $method,
        array $payload = [],
        array $headers = [],
        ?CarbonInterface $availableAt = null,
    ): ?MobileLocalOfflineAction {
        if ($this->networkState->isAvailable()) {
            return null;
        }

        return $this->offlineActions->enqueue(
            actionType: $this->supportedActionType($actionType),
            endpoint: $endpoint,
            method: $method,
            payload: $payload,
            headers: $headers,
            availableAt: $availableAt,
        );
    }

    public function markComplete(
        MobileLocalOfflineAction $offlineAction,
        ?CarbonInterface $completedAt = null,
    ): MobileLocalOfflineAction {
        return $this->offlineActions->markCompleted($offlineAction, $completedAt);
    }

    public function markFailed(
        MobileLocalOfflineAction $offlineAction,
        string $lastError,
        ?CarbonInterface $availableAt = null,
    ): MobileLocalOfflineAction {
        return $this->offlineActions->markFailed($offlineAction, $lastError, $availableAt);
    }

    private function supportedActionType(string $actionType): string
    {
        return match ($actionType) {
            self::ACTION_CREATE,
            self::ACTION_UPDATE,
            self::ACTION_DELETE => $actionType,
            default => throw new InvalidArgumentException('Offline actions must be create, update, or delete mutations.'),
        };
    }
}
