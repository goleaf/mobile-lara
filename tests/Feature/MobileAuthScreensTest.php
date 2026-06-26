<?php

use App\Livewire\Mobile\EmailVerification;
use App\Livewire\Mobile\ForgotPassword;
use App\Livewire\Mobile\Login;
use App\Livewire\Mobile\Register;
use App\Livewire\Mobile\ResetPassword;
use App\Models\User;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-auth-screens.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_auth.screen_tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_auth.screen_revoked_tokens',
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    Http::preventStrayRequests();
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('login validates required credentials', function (): void {
    Livewire::test(Login::class)
        ->call('login')
        ->assertHasErrors([
            'email' => 'required',
            'password' => 'required',
        ])
        ->assertSet('toastMessage', 'Fix the highlighted sign-in fields.')
        ->assertSet('toastVariant', 'error');
});

test('login accepts valid credentials and signs the user in', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/login' => Http::response(mobileAuthScreensEnvelope(
            userId: 123,
            name: 'Person Mobile',
            email: 'person@example.com',
            accessToken: 'screen-login-access-token',
            refreshToken: 'screen-login-refresh-token',
        )),
        'https://api-admin.example.test/api/v1/mobile/bootstrap' => Http::response(mobileAuthScreensBootstrapEnvelope()),
    ]);

    $component = Livewire::test(Login::class);

    expect($component->instance()->canSubmit())->toBeFalse();

    $component
        ->set('email', 'person@example.com')
        ->set('password', 'password')
        ->set('remember', true)
        ->assertHasNoErrors();

    expect($component->instance()->canSubmit())->toBeTrue();

    $component
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('mobile.dashboard'))
        ->assertSet('password', '')
        ->assertSet('status', 'Signed in.')
        ->assertSet('toastMessage', 'Signed in.')
        ->assertSet('toastVariant', 'success');

    $user = User::query()->where('email', 'person@example.com')->firstOrFail();

    expect($user->name)->toBe('Person Mobile');

    $this->assertAuthenticatedAs($user);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/login'
        && $request['email'] === 'person@example.com'
        && $request['password'] === 'password');
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/bootstrap'
        && $request->hasHeader('Authorization', 'Bearer screen-login-access-token'));
});

test('login rejects invalid credentials', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/login' => Http::response([
            'success' => false,
            'error' => [
                'code' => 'invalid_credentials',
                'message' => 'The provided mobile credentials are invalid.',
                'category' => 'unauthenticated',
                'next_action' => 'check_credentials',
            ],
            'meta' => ['api_version' => 'v1'],
        ], 401),
    ]);

    Livewire::test(Login::class)
        ->set('email', 'person@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('email')
        ->assertSet('toastMessage', 'The provided mobile credentials are invalid.')
        ->assertSet('toastVariant', 'error');

    $this->assertGuest();
});

test('login validates email in real time', function (): void {
    Livewire::test(Login::class)
        ->set('email', 'not-an-email')
        ->assertHasErrors(['email' => 'email']);
});

test('register validates account fields', function (): void {
    Livewire::test(Register::class)
        ->call('register')
        ->assertHasErrors([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'termsAccepted' => 'accepted',
        ])
        ->assertSet('toastMessage', 'Fix the highlighted account fields.')
        ->assertSet('toastVariant', 'error');
});

test('register accepts valid account details', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/register' => Http::response(mobileAuthScreensEnvelope(
            userId: 456,
            name: 'Mobile User',
            email: 'mobile@example.com',
            accessToken: 'screen-register-access-token',
            refreshToken: 'screen-register-refresh-token',
        ), 201),
        'https://api-admin.example.test/api/v1/mobile/bootstrap' => Http::response(mobileAuthScreensBootstrapEnvelope()),
    ]);

    $component = Livewire::test(Register::class);

    expect($component->instance()->canSubmit())->toBeFalse();

    $component
        ->set('name', 'Mobile User')
        ->set('email', 'mobile@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('termsAccepted', true)
        ->assertHasNoErrors();

    expect($component->instance()->canSubmit())->toBeTrue();

    $component
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('mobile.dashboard'))
        ->assertSet('password', '')
        ->assertSet('password_confirmation', '')
        ->assertSet('status', 'Account created.')
        ->assertSet('toastMessage', 'Account created.')
        ->assertSet('toastVariant', 'success');

    $user = User::query()
        ->where('email', 'mobile@example.com')
        ->firstOrFail();

    expect($user->name)->toBe('Mobile User');

    $this->assertAuthenticatedAs($user);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/register'
        && $request['name'] === 'Mobile User'
        && $request['email'] === 'mobile@example.com'
        && $request['password_confirmation'] === 'password'
        && is_string($request['device_id']));
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/bootstrap'
        && $request->hasHeader('Authorization', 'Bearer screen-register-access-token'));
});

test('register uses api validation errors without creating a local account session', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/register' => Http::response([
            'success' => false,
            'error' => [
                'code' => 'validation_failed',
                'message' => 'The submitted mobile request is invalid.',
                'category' => 'validation',
                'next_action' => 'correct_input',
            ],
            'meta' => [
                'api_version' => 'v1',
                'validation_errors' => [
                    'email' => ['The email has already been taken.'],
                ],
            ],
        ], 422),
    ]);

    Livewire::test(Register::class)
        ->set('name', 'Mobile User')
        ->set('email', 'mobile@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('termsAccepted', true)
        ->call('register')
        ->assertHasErrors('email')
        ->assertSet('toastMessage', 'The submitted mobile request is invalid.')
        ->assertSet('toastVariant', 'error');

    expect(User::query()->where('email', 'mobile@example.com')->exists())->toBeFalse();

    $this->assertGuest();
});

test('register validates email and password confirmation in real time', function (): void {
    Livewire::test(Register::class)
        ->set('email', 'not-an-email')
        ->assertHasErrors(['email' => 'email'])
        ->set('password', 'short')
        ->assertHasErrors('password')
        ->set('password', 'password')
        ->set('password_confirmation', 'different')
        ->assertHasErrors(['password_confirmation' => 'same']);
});

test('register screen omits account helper labels', function (): void {
    Livewire::test(Register::class)
        ->assertDontSee('New account')
        ->assertDontSee('Use a valid email and a secure password.')
        ->assertDontSee('Use at least 8 characters.');
});

test('forgot password validates email before continuing', function (): void {
    $component = Livewire::test(ForgotPassword::class);

    expect($component->instance()->canSubmit())->toBeFalse();

    $component
        ->call('sendResetLink')
        ->assertHasErrors(['email' => 'required'])
        ->assertSet('toastMessage', 'Enter a valid email before continuing.')
        ->assertSet('toastVariant', 'error')
        ->set('email', 'reset@example.com')
        ->assertHasNoErrors();

    expect($component->instance()->canSubmit())->toBeTrue();

    $component
        ->call('sendResetLink')
        ->assertHasNoErrors()
        ->assertSet('status', 'Password reset instructions are ready to send.')
        ->assertSet('toastMessage', 'Password reset instructions are ready to send.')
        ->assertSet('toastVariant', 'success');
});

test('forgot password validates email in real time', function (): void {
    Livewire::test(ForgotPassword::class)
        ->set('email', 'not-an-email')
        ->assertHasErrors(['email' => 'email']);
});

test('reset password validates token email and confirmation', function (): void {
    $component = Livewire::test(ResetPassword::class);

    expect($component->instance()->canSubmit())->toBeFalse();

    $component
        ->call('resetPassword')
        ->assertHasErrors([
            'token' => 'required',
            'email' => 'required',
            'password' => 'required',
        ])
        ->assertSet('toastMessage', 'Fix the highlighted password reset fields.')
        ->assertSet('toastVariant', 'error')
        ->set('token', 'reset-token')
        ->set('email', 'reset@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->assertHasNoErrors();

    expect($component->instance()->canSubmit())->toBeTrue();

    $component
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertSet('password', '')
        ->assertSet('password_confirmation', '')
        ->assertSet('status', 'New password details validated.')
        ->assertSet('toastMessage', 'New password details validated.')
        ->assertSet('toastVariant', 'success');
});

test('reset password validates email and confirmation in real time', function (): void {
    Livewire::test(ResetPassword::class)
        ->set('email', 'not-an-email')
        ->assertHasErrors(['email' => 'email'])
        ->set('password', 'password')
        ->set('password_confirmation', 'different')
        ->assertHasErrors(['password_confirmation' => 'same']);
});

test('email verification validates email before continuing', function (): void {
    Livewire::test(EmailVerification::class)
        ->call('sendVerification')
        ->assertHasErrors(['email' => 'required'])
        ->set('email', 'verify@example.com')
        ->call('sendVerification')
        ->assertHasNoErrors()
        ->assertSet('status', 'Verification email details validated.');
});

/**
 * @return array<string, mixed>
 */
function mobileAuthScreensEnvelope(
    string|int $userId,
    string $name,
    string $email,
    string $accessToken,
    string $refreshToken,
): array {
    return [
        'success' => true,
        'data' => [
            'user' => [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'email_verified_at' => '2026-06-25T12:00:00+00:00',
            ],
            'session' => [
                'id' => 321,
                'device_id' => 'screen-device-id',
                'status' => 'active',
            ],
            'tokens' => [
                'token_type' => 'Bearer',
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'access_token_expires_at' => '2026-06-25T12:15:00+00:00',
                'refresh_token_expires_at' => '2026-07-25T12:00:00+00:00',
            ],
            'next_bootstrap_required' => true,
        ],
        'meta' => [
            'api_version' => 'v1',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileAuthScreensBootstrapEnvelope(): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
            'current_tenant' => null,
            'available_tenants' => [],
            'permissions' => ['status' => 'not_configured', 'roles' => [], 'abilities' => []],
            'features' => ['version' => 'foundation-1', 'items' => []],
            'remote_config' => ['version' => 'foundation-1', 'values' => []],
            'app_version' => ['status' => 'supported'],
            'maintenance' => ['enabled' => false],
            'subscription' => ['status' => 'active'],
            'notification_preferences' => ['in_app_enabled' => true],
            'sync' => ['enabled' => false],
            'unread_notification_count' => 0,
        ],
        'meta' => [
            'api_version' => 'v1',
            'bootstrap_version' => 'foundation-1',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}
