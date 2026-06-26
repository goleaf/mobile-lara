<?php

namespace App\Services\MobileAuth;

use App\Contracts\MobileAuth\MobileTokenStore;
use App\Services\MobileApi\MobileApiClient;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileApi\MobileDeviceContext;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;

final class MobileAuthApiService
{
    public function __construct(
        private readonly MobileApiClient $api,
        private readonly MobileDeviceContext $device,
        private readonly MobileTokenStore $store,
        private readonly AccessTokenService $accessTokens,
        private readonly RefreshTokenService $refreshTokens,
        private readonly LogoutService $logout,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function login(string $email, string $password): array
    {
        return $this->storeAuthenticatedEnvelope($this->api->post('/auth/login', [
            'email' => $email,
            'password' => $password,
            ...$this->device->payload(),
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public function register(string $name, string $email, string $password, string $passwordConfirmation): array
    {
        return $this->storeAuthenticatedEnvelope($this->api->post('/auth/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
            ...$this->device->payload(),
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public function refresh(): array
    {
        $refreshToken = $this->refreshTokens->get();

        if (! is_string($refreshToken) || trim($refreshToken) === '') {
            throw MobileApiException::missingToken('refresh');
        }

        return $this->storeAuthenticatedEnvelope($this->api->post('/auth/refresh', [
            'refresh_token' => $refreshToken,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public function logout(): array
    {
        $accessToken = $this->accessTokens->get();

        if (! is_string($accessToken) || trim($accessToken) === '') {
            $this->logout->logout(revokeTokens: false);

            return [];
        }

        try {
            return $this->api->post('/auth/logout', accessToken: $accessToken);
        } finally {
            $this->logout->logout(revokeTokens: false);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function logoutAllDevices(): array
    {
        $accessToken = $this->accessToken();

        try {
            return $this->api->post('/auth/logout-all', accessToken: $accessToken);
        } finally {
            $this->logout->logout(revokeTokens: false);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function currentUser(): array
    {
        return $this->api->get('/auth/user', accessToken: $this->accessToken());
    }

    /**
     * @param  array{name?: string, email?: string, username?: string|null, phone?: string|null, bio?: string|null, location?: string|null, website?: string|null}  $attributes
     * @return array<string, mixed>
     */
    public function updateProfile(array $attributes, UploadedFile|string|null $avatar = null, bool $removeAvatar = false): array
    {
        if ($removeAvatar) {
            $attributes['remove_avatar'] = true;
        }

        if ($avatar === null) {
            return $this->api->patch('/auth/profile', $attributes, $this->accessToken());
        }

        return $this->api->patchMultipart('/auth/profile', $attributes, [
            'avatar' => $this->avatarFilePayload($avatar),
        ], $this->accessToken());
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
     * @return array{contents: string, filename: string, headers: array<string, string>}
     */
    private function avatarFilePayload(UploadedFile|string $avatar): array
    {
        $path = $avatar instanceof UploadedFile
            ? ($avatar->getRealPath() ?: $avatar->getPathname())
            : $avatar;

        if (! is_string($path) || ! is_readable($path) || ! is_file($path)) {
            throw new MobileApiException(
                mobileCode: 'mobile_avatar_unreadable',
                message: 'The selected avatar file could not be read.',
                category: 'validation',
                nextAction: 'choose_file',
                status: 422,
            );
        }

        $contents = file_get_contents($path);

        if (! is_string($contents)) {
            throw new MobileApiException(
                mobileCode: 'mobile_avatar_unreadable',
                message: 'The selected avatar file could not be read.',
                category: 'validation',
                nextAction: 'choose_file',
                status: 422,
            );
        }

        $filename = $avatar instanceof UploadedFile
            ? $avatar->getClientOriginalName()
            : basename($path);
        $mimeType = $avatar instanceof UploadedFile
            ? ($avatar->getMimeType() ?: 'application/octet-stream')
            : (mime_content_type($path) ?: 'application/octet-stream');

        return [
            'contents' => $contents,
            'filename' => $filename,
            'headers' => ['Content-Type' => $mimeType],
        ];
    }

    /**
     * @param  array<string, mixed>  $envelope
     * @return array<string, mixed>
     */
    private function storeAuthenticatedEnvelope(array $envelope): array
    {
        $data = $envelope['data'] ?? [];

        if (! is_array($data)) {
            throw MobileApiException::malformedResponse($envelope);
        }

        $user = $data['user'] ?? [];
        $tokens = $data['tokens'] ?? [];

        if (! is_array($user) || ! is_array($tokens)) {
            throw MobileApiException::malformedResponse($envelope);
        }

        $userId = $user['id'] ?? null;
        $accessToken = $tokens['access_token'] ?? null;
        $refreshToken = $tokens['refresh_token'] ?? null;
        $accessTokenExpiresAt = $tokens['access_token_expires_at'] ?? null;
        $refreshTokenExpiresAt = $tokens['refresh_token_expires_at'] ?? null;

        if (
            (! is_int($userId) && ! is_string($userId))
            || ! is_string($accessToken)
            || ! is_string($refreshToken)
            || ! is_string($accessTokenExpiresAt)
            || ! is_string($refreshTokenExpiresAt)
        ) {
            throw MobileApiException::malformedResponse($envelope);
        }

        $this->store->putTokens(MobileTokenSet::empty()->withAuthValues(
            userId: $userId,
            accessToken: $accessToken,
            refreshToken: $refreshToken,
            accessTokenExpiresAt: CarbonImmutable::parse($accessTokenExpiresAt),
            refreshTokenExpiresAt: CarbonImmutable::parse($refreshTokenExpiresAt),
        ));

        return $envelope;
    }
}
