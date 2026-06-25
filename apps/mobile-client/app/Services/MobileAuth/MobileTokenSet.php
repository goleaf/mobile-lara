<?php

namespace App\Services\MobileAuth;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class MobileTokenSet
{
    public function __construct(
        public readonly ?string $userId = null,
        public readonly ?string $accessToken = null,
        public readonly ?string $refreshToken = null,
        public readonly ?CarbonImmutable $accessTokenExpiresAt = null,
        public readonly ?CarbonImmutable $refreshTokenExpiresAt = null,
    ) {}

    public static function empty(): self
    {
        return new self;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            userId: self::stringOrNull($payload['user_id'] ?? null),
            accessToken: self::stringOrNull($payload['access_token'] ?? null),
            refreshToken: self::stringOrNull($payload['refresh_token'] ?? null),
            accessTokenExpiresAt: self::dateOrNull($payload['access_token_expires_at'] ?? null),
            refreshTokenExpiresAt: self::dateOrNull($payload['refresh_token_expires_at'] ?? null),
        );
    }

    /**
     * @return array{
     *     user_id: string|null,
     *     access_token: string|null,
     *     refresh_token: string|null,
     *     access_token_expires_at: string|null,
     *     refresh_token_expires_at: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'access_token_expires_at' => $this->accessTokenExpiresAt?->toIso8601String(),
            'refresh_token_expires_at' => $this->refreshTokenExpiresAt?->toIso8601String(),
        ];
    }

    public function withAccessToken(string $token, CarbonInterface $expiresAt): self
    {
        return new self(
            userId: $this->userId,
            accessToken: $token,
            refreshToken: $this->refreshToken,
            accessTokenExpiresAt: CarbonImmutable::instance($expiresAt),
            refreshTokenExpiresAt: $this->refreshTokenExpiresAt,
        );
    }

    public function withRefreshToken(string $token, CarbonInterface $expiresAt): self
    {
        return new self(
            userId: $this->userId,
            accessToken: $this->accessToken,
            refreshToken: $token,
            accessTokenExpiresAt: $this->accessTokenExpiresAt,
            refreshTokenExpiresAt: CarbonImmutable::instance($expiresAt),
        );
    }

    public function withoutAccessToken(): self
    {
        return new self(
            userId: $this->userId,
            refreshToken: $this->refreshToken,
            refreshTokenExpiresAt: $this->refreshTokenExpiresAt,
        );
    }

    public function withoutRefreshToken(): self
    {
        return new self(
            userId: $this->userId,
            accessToken: $this->accessToken,
            accessTokenExpiresAt: $this->accessTokenExpiresAt,
        );
    }

    public function withUserId(string|int|null $userId): self
    {
        return new self(
            userId: is_null($userId) ? null : (string) $userId,
            accessToken: $this->accessToken,
            refreshToken: $this->refreshToken,
            accessTokenExpiresAt: $this->accessTokenExpiresAt,
            refreshTokenExpiresAt: $this->refreshTokenExpiresAt,
        );
    }

    public function withAuthValues(
        string|int $userId,
        string $accessToken,
        string $refreshToken,
        CarbonInterface $accessTokenExpiresAt,
        CarbonInterface $refreshTokenExpiresAt,
    ): self {
        return new self(
            userId: (string) $userId,
            accessToken: $accessToken,
            refreshToken: $refreshToken,
            accessTokenExpiresAt: CarbonImmutable::instance($accessTokenExpiresAt),
            refreshTokenExpiresAt: CarbonImmutable::instance($refreshTokenExpiresAt),
        );
    }

    public function hasAccessToken(): bool
    {
        return is_string($this->accessToken)
            && trim($this->accessToken) !== ''
            && $this->accessTokenExpiresAt instanceof CarbonImmutable;
    }

    public function hasRefreshToken(): bool
    {
        return is_string($this->refreshToken)
            && trim($this->refreshToken) !== ''
            && $this->refreshTokenExpiresAt instanceof CarbonImmutable;
    }

    private static function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? $value : null;
    }

    private static function dateOrNull(mixed $value): ?CarbonImmutable
    {
        if ($value instanceof CarbonInterface) {
            return CarbonImmutable::instance($value);
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return CarbonImmutable::parse($value);
    }
}
