<?php

namespace App\Services\MobileAuth;

use App\Auth\MobileApiUser;
use App\Services\MobileApi\MobileApiException;
use Illuminate\Support\Facades\Auth;

final class MobileApiSessionBridge
{
    public function __construct(private readonly MobileSessionService $mobileSessions) {}

    /**
     * @param  array<string, mixed>  $envelope
     */
    public function start(array $envelope, bool $remember = false): MobileApiUser
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
    public function syncUser(array $envelope): MobileApiUser
    {
        $payload = $this->userPayload($envelope);
        $user = MobileApiUser::fromPayload($payload);

        Auth::setUser($user);

        return $user;
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

        $payload = $data['user'];
        $id = $payload['id'] ?? null;

        if ((! is_int($id) && ! is_string($id)) || trim((string) $id) === '') {
            throw MobileApiException::malformedResponse($envelope);
        }

        return $payload;
    }
}
