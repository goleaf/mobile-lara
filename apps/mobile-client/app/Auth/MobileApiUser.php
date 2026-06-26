<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

final class MobileApiUser implements Authenticatable
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(private array $attributes)
    {
        $this->attributes['password'] ??= '';
        $this->attributes['remember_token'] ??= null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self([
            'id' => self::requiredIdentifier($payload['id'] ?? null),
            'name' => self::nullableString($payload['name'] ?? null) ?? 'Mobile user',
            'email' => self::nullableString($payload['email'] ?? null),
            'username' => self::nullableString($payload['username'] ?? null),
            'phone' => self::nullableString($payload['phone'] ?? null),
            'bio' => self::nullableString($payload['bio'] ?? null),
            'location' => self::nullableString($payload['location'] ?? null),
            'website' => self::nullableString($payload['website'] ?? null),
            'avatar_path' => self::nullableString($payload['avatar_path'] ?? null),
            'avatar_url' => self::nullableString($payload['avatar_url'] ?? null),
            'email_verified_at' => self::nullableString($payload['email_verified_at'] ?? null),
        ]);
    }

    public static function minimal(mixed $identifier): self
    {
        return new self([
            'id' => self::requiredIdentifier($identifier),
            'name' => 'Mobile user',
            'email' => null,
        ]);
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->attributes[$this->getAuthIdentifierName()];
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getRememberToken(): ?string
    {
        $token = $this->attributes[$this->getRememberTokenName()] ?? null;

        return is_string($token) ? $token : null;
    }

    public function setRememberToken($value): void
    {
        $this->attributes[$this->getRememberTokenName()] = is_string($value) ? $value : null;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function stringAttribute(string $key, ?string $fallback = null): ?string
    {
        $value = $this->getAttribute($key);

        return is_string($value) && trim($value) !== '' ? trim($value) : $fallback;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    public function __isset(string $key): bool
    {
        return array_key_exists($key, $this->attributes) && $this->attributes[$key] !== null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    private static function requiredIdentifier(mixed $value): string
    {
        if ((is_int($value) || is_string($value)) && trim((string) $value) !== '') {
            return (string) $value;
        }

        return '0';
    }

    private static function nullableString(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }
}
