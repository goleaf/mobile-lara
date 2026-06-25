<?php

use App\Livewire\Mobile\Login;
use App\Livewire\Mobile\Sessions;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileAuth\AppUnlockStateService;
use App\Services\MobileAuth\MobileSessionService;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    config([
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_auth.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_auth.revoked_tokens',
        'nativephp.version' => '9.8.7',
        'nativephp.version_code' => 42,
    ]);

    session()->forget([
        MobileSessionService::LAST_LOGIN_AT_SESSION_KEY,
        config('mobile_auth.storage.session_key'),
        config('mobile_auth.storage.revoked_session_key'),
    ]);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('sessions page renders current device details and remote api placeholder', function (): void {
    session()->put(
        MobileSessionService::LAST_LOGIN_AT_SESSION_KEY,
        CarbonImmutable::now()->toIso8601String(),
    );

    Livewire::withHeaders(['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Mobile'])
        ->test(Sessions::class)
        ->assertSee('Current device session')
        ->assertSee('iOS app session')
        ->assertSee('Last login time')
        ->assertSee('Jun 25, 2026 12:00 PM')
        ->assertSee('App version')
        ->assertSee('9.8.7')
        ->assertSee('(42)')
        ->assertSee('Logout')
        ->assertSee('Remote sessions')
        ->assertSee('GET /api/mobile/sessions')
        ->assertSee('Placeholder');
});

test('mobile login records the last login time for the sessions page', function (): void {
    User::factory()->create([
        'email' => 'person@example.com',
    ]);

    Livewire::test(Login::class)
        ->set('email', 'person@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('mobile.dashboard'))
        ->assertSet('status', 'Signed in.');

    expect(session()->get(MobileSessionService::LAST_LOGIN_AT_SESSION_KEY))
        ->toBe('2026-06-25T12:00:00+00:00');
});

test('sessions logout redirects and clears local mobile session state', function (): void {
    $this->actingAs(User::factory()->create());

    app(AppUnlockStateService::class)->unlock();
    app(AccessTokenService::class)->put('logout-access-token', CarbonImmutable::now()->addMinutes(15));

    session()->put(
        MobileSessionService::LAST_LOGIN_AT_SESSION_KEY,
        CarbonImmutable::now()->toIso8601String(),
    );

    Livewire::test(Sessions::class)
        ->call('logout')
        ->assertRedirect(route('mobile.login'));

    expect(session()->has(MobileSessionService::LAST_LOGIN_AT_SESSION_KEY))->toBeFalse()
        ->and(app(AccessTokenService::class)->get())->toBeNull()
        ->and(app(AppUnlockStateService::class)->isUnlocked())->toBeFalse();

    $this->assertGuest();
});
