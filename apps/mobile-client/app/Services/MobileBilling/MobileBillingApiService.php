<?php

namespace App\Services\MobileBilling;

use App\Services\MobileApi\MobileApiClient;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;

final class MobileBillingApiService
{
    public function __construct(
        private readonly MobileApiClient $api,
        private readonly AccessTokenService $accessTokens,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function subscription(): array
    {
        return $this->data($this->api->get('/billing/subscription', accessToken: $this->accessToken()));
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
