<?php

namespace App\Services\MobileBootstrap;

use App\Services\MobileApi\MobileApiClient;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileLocal\SettingsRepository;

final class MobileBootstrapService
{
    public function __construct(
        private readonly MobileApiClient $api,
        private readonly AccessTokenService $accessTokens,
        private readonly SettingsRepository $settings,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function refresh(): array
    {
        $accessToken = $this->accessTokens->get();

        if (! is_string($accessToken) || trim($accessToken) === '') {
            throw MobileApiException::missingToken('access');
        }

        $envelope = $this->api->get('/bootstrap', accessToken: $accessToken);
        $this->settings->cacheBootstrapContext($envelope);

        return $envelope;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function cached(): ?array
    {
        return $this->settings->bootstrapContext();
    }
}
