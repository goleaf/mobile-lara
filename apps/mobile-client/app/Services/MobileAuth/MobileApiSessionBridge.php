<?php

namespace App\Services\MobileAuth;

use App\Models\User;
use App\Services\MobileApi\MobileApiException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class MobileApiSessionBridge
{
    public function __construct(private readonly MobileSessionService $mobileSessions) {}

    /**
     * @param  array<string, mixed>  $envelope
     */
    public function start(array $envelope, bool $remember = false): User
    {
        $user = $this->syncUser($envelope);

        Auth::login($user, $remember);
        session()->regenerate();
        $this->mobileSessions->recordLogin();

        return $user;
    }

    /**
     * @param  array<string, mixed>  $envelope
     */
    public function syncUser(array $envelope): User
    {
        $payload = $this->userPayload($envelope);
        $email = $this->requiredString($payload['email'] ?? null);
        $name = $this->requiredString($payload['name'] ?? null);
        $emailVerifiedAt = $this->optionalDate($payload['email_verified_at'] ?? null);

        $user = User::query()
            ->select(['id', 'name', 'email', 'email_verified_at', 'password', 'avatar_path'])
            ->where('email', $email)
            ->first();

        $attributes = [
            'name' => $name,
            'email_verified_at' => $emailVerifiedAt,
        ];

        if (! $user instanceof User) {
            $attributes['password'] = Hash::make('api-session-'.Str::random(48));
        }

        /** @var User $syncedUser */
        $syncedUser = User::query()->updateOrCreate(
            ['email' => $email],
            $attributes,
        );

        return $syncedUser;
    }

    /**
     * @param  array<string, mixed>  $envelope
     * @return array<string, mixed>
     */
    private function userPayload(array $envelope): array
    {
        $data = $envelope['data'] ?? [];

        if (! is_array($data) || ! is_array($data['user'] ?? null)) {
            throw MobileApiException::malformedResponse($envelope);
        }

        return $data['user'];
    }

    private function requiredString(mixed $value): string
    {
        if (! is_string($value) || trim($value) === '') {
            throw MobileApiException::malformedResponse();
        }

        return trim($value);
    }

    private function optionalDate(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return CarbonImmutable::parse($value);
    }
}
