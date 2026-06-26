<?php

namespace App\Services\MobileApi;

use RuntimeException;

final class MobileApiException extends RuntimeException
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public readonly string $mobileCode,
        string $message,
        public readonly string $category,
        public readonly string $nextAction,
        public readonly int $status,
        public readonly array $payload = [],
    ) {
        parent::__construct($message, $status);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload, int $status): self
    {
        $error = is_array($payload['error'] ?? null) ? $payload['error'] : [];

        return new self(
            mobileCode: self::stringValue($error['code'] ?? null, 'mobile_api_error'),
            message: self::stringValue($error['message'] ?? null, 'The mobile API request failed.'),
            category: self::stringValue($error['category'] ?? null, 'server_error'),
            nextAction: self::stringValue($error['next_action'] ?? null, 'retry'),
            status: $status,
            payload: $payload,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function malformedResponse(array $payload = []): self
    {
        return new self(
            mobileCode: 'malformed_mobile_api_response',
            message: 'The mobile API returned an unexpected response.',
            category: 'server_error',
            nextAction: 'retry',
            status: 502,
            payload: $payload,
        );
    }

    public static function connectionFailed(string $message): self
    {
        return new self(
            mobileCode: 'mobile_api_unreachable',
            message: $message,
            category: 'server_error',
            nextAction: 'retry_when_online',
            status: 0,
        );
    }

    public static function missingToken(string $tokenType): self
    {
        return new self(
            mobileCode: "missing_{$tokenType}_token",
            message: 'A valid mobile session is required.',
            category: 'unauthenticated',
            nextAction: 'login',
            status: 401,
        );
    }

    private static function stringValue(mixed $value, string $fallback): string
    {
        return is_string($value) && trim($value) !== '' ? $value : $fallback;
    }
}
