<?php

namespace App\Services\MobileSync;

use App\Services\MobileApi\MobileApiClient;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;

final class MobileSyncApiService
{
    public function __construct(
        private readonly MobileApiClient $api,
        private readonly AccessTokenService $accessTokens,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function bootstrap(): array
    {
        return $this->data($this->api->get('/sync/bootstrap', accessToken: $this->accessToken()));
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function pull(array $query = []): array
    {
        return $this->data($this->api->get('/sync/pull', $query, $this->accessToken()));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function push(array $payload): array
    {
        return $this->data($this->api->post('/sync/push', $payload, $this->accessToken()));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function acknowledge(array $payload): array
    {
        return $this->data($this->api->post('/sync/acknowledge', $payload, $this->accessToken()));
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
