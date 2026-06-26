<?php

namespace App\Services\MobileAuth;

use App\Auth\MobileApiUser;
use App\Services\MobileApi\MobileApiException;

final class MobileCurrentUserService
{
    public function __construct(
        private readonly MobileAuthApiService $authApi,
        private readonly MobileApiSessionBridge $apiSessions,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function envelope(): array
    {
        return $this->authApi->currentUser();
    }

    public function fromApi(): MobileApiUser
    {
        return $this->apiSessions->syncUser($this->envelope());
    }

    /**
     * @throws MobileApiException
     */
    public function requireFromApi(): MobileApiUser
    {
        return $this->fromApi();
    }
}
