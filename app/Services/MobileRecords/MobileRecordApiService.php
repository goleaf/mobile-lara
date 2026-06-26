<?php

namespace App\Services\MobileRecords;

use App\Services\MobileApi\MobileApiClient;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;

final class MobileRecordApiService
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
        return $this->api->get('/records', $query, $this->accessToken());
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->recordFromEnvelope($this->api->post('/records', $payload, $this->accessToken()));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function update(string $recordId, array $payload): array
    {
        return $this->recordFromEnvelope($this->api->patch("/records/{$recordId}", $payload, $this->accessToken()));
    }

    /**
     * @return array<string, mixed>
     */
    public function archive(string $recordId): array
    {
        return $this->recordFromEnvelope($this->api->delete("/records/{$recordId}", accessToken: $this->accessToken()));
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(string $recordId): array
    {
        return $this->archive($recordId);
    }

    /**
     * @return array<string, mixed>
     */
    public function restore(string $recordId): array
    {
        return $this->recordFromEnvelope($this->api->post("/records/{$recordId}/restore", accessToken: $this->accessToken()));
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
    private function recordFromEnvelope(array $envelope): array
    {
        $record = $envelope['data']['record'] ?? null;

        if (! is_array($record)) {
            throw MobileApiException::malformedResponse($envelope);
        }

        return $record;
    }
}
