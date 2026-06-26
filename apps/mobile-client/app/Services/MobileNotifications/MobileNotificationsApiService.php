<?php

namespace App\Services\MobileNotifications;

use App\Services\MobileApi\MobileApiClient;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;

final class MobileNotificationsApiService
{
    public function __construct(
        private readonly MobileApiClient $api,
        private readonly AccessTokenService $accessTokens,
    ) {}

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function list(array $query = []): array
    {
        return $this->data($this->api->get('/notifications', $query, $this->accessToken()));
    }

    /**
     * @return array<string, mixed>
     */
    public function markRead(string $notificationId): array
    {
        return $this->data($this->api->patch("/notifications/{$notificationId}/read", accessToken: $this->accessToken()));
    }

    /**
     * @return array<string, mixed>
     */
    public function markAllRead(): array
    {
        return $this->data($this->api->patch('/notifications/read-all', accessToken: $this->accessToken()));
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(string $notificationId): array
    {
        return $this->data($this->api->delete("/notifications/{$notificationId}", accessToken: $this->accessToken()));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function registerPushToken(array $payload): array
    {
        return $this->data($this->api->post('/notifications/push-tokens', $payload, $this->accessToken()));
    }

    /**
     * @return array<string, mixed>
     */
    public function revokePushToken(string $pushTokenId): array
    {
        return $this->data($this->api->delete("/notifications/push-tokens/{$pushTokenId}", accessToken: $this->accessToken()));
    }

    private function accessToken(): string
    {
        $accessToken = $this->accessTokens->get();

        if (! is_string($accessToken) || trim($accessToken) === '') {
            throw MobileApiException::missingToken('access');
        }

        return $accessToken;
    }

    /**
     * @param  array<string, mixed>  $envelope
     * @return array<string, mixed>
     */
    private function data(array $envelope): array
    {
        $data = $envelope['data'] ?? null;

        if (! is_array($data)) {
            throw MobileApiException::malformedResponse($envelope);
        }

        return $data;
    }
}
