<?php

use App\Models\User;
use App\Services\MobileAuth\MobileTokenAuthenticator;
use App\Services\MobileAuth\MobileTokenIssuer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

test('mobile token authenticator returns active access token and ignores revoked sessions', function (): void {
    $user = User::factory()->create();
    $request = Request::create('/api/v1/mobile/auth/login', 'POST', server: [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_USER_AGENT' => 'Pest',
    ]);

    $tokenSet = app(MobileTokenIssuer::class)->issue($user, [
        'device_id' => 'test-device',
        'device_name' => 'Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ], $request);

    $authenticator = app(MobileTokenAuthenticator::class);
    $accessToken = $authenticator->authenticateAccessToken($tokenSet['tokens']['access_token']);

    expect($accessToken)->not->toBeNull()
        ->and($accessToken->user->is($user))->toBeTrue()
        ->and($accessToken->last_used_at)->not->toBeNull();

    app(MobileTokenIssuer::class)->revokeSession($tokenSet['session']);

    expect($authenticator->authenticateAccessToken($tokenSet['tokens']['access_token']))->toBeNull();
});
