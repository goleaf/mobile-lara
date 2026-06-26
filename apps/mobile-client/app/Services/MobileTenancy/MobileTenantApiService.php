<?php

namespace App\Services\MobileTenancy;

use App\Services\MobileApi\MobileApiClient;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;

final class MobileTenantApiService
{
    public function __construct(
        private readonly MobileApiClient $api,
        private readonly AccessTokenService $accessTokens,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function list(): array
    {
        return $this->api->get('/tenants', accessToken: $this->accessToken());
    }

    /**
     * @return array<string, mixed>
     */
    public function switch(string $tenantId): array
    {
        return $this->api->post('/tenants/current', [
            'tenant_id' => $tenantId,
        ], accessToken: $this->accessToken());
    }

    /**
     * @return array<string, mixed>
     */
    public function invitations(): array
    {
        return $this->api->get('/tenants/invitations', accessToken: $this->accessToken());
    }

    /**
     * @return array<string, mixed>
     */
    public function acceptInvitation(string $tenantId): array
    {
        return $this->api->post('/tenants/invitations/'.rawurlencode($tenantId).'/accept', accessToken: $this->accessToken());
    }

    /**
     * @return array<string, mixed>
     */
    public function declineInvitation(string $tenantId): array
    {
        return $this->api->post('/tenants/invitations/'.rawurlencode($tenantId).'/decline', accessToken: $this->accessToken());
    }

    private function accessToken(): string
    {
        $accessToken = $this->accessTokens->get();

        if (! is_string($accessToken) || trim($accessToken) === '') {
            throw MobileApiException::missingToken('access');
        }

        return $accessToken;
    }
}
