<?php

use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileBootstrap\MobileBootstrapService;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-bootstrap-service.sqlite');

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
        'mobile_auth.storage.session_key' => 'testing.mobile_bootstrap.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_bootstrap.revoked_tokens',
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

test('bootstrap service fetches and caches the current operating context', function (): void {
    app(AccessTokenService::class)->put('bootstrap-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/bootstrap' => Http::response(mobileBootstrapEnvelope()),
    ]);

    $envelope = app(MobileBootstrapService::class)->refresh();
    $settings = app(SettingsRepository::class)->get();

    expect($envelope['data']['features']['version'])->toBe('foundation-1')
        ->and($settings->bootstrap_context)->toBe($envelope)
        ->and($settings->bootstrap_cached_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and(app(MobileBootstrapService::class)->cached())->toBe($envelope);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/bootstrap'
        && $request->hasHeader('Authorization', 'Bearer bootstrap-access-token'));
});

/**
 * @return array<string, mixed>
 */
function mobileBootstrapEnvelope(): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
            'current_tenant' => null,
            'available_tenants' => [],
            'permissions' => ['status' => 'not_configured', 'roles' => [], 'abilities' => []],
            'features' => [
                'version' => 'foundation-1',
                'items' => ['records' => ['state' => 'disabled', 'enabled' => false]],
            ],
            'remote_config' => ['version' => 'foundation-1', 'values' => []],
            'app_version' => ['status' => 'supported'],
            'maintenance' => ['enabled' => false],
            'subscription' => ['status' => 'active'],
            'notification_preferences' => ['in_app_enabled' => true],
            'sync' => ['enabled' => false, 'reason' => 'sync_api_pending'],
            'unread_notification_count' => 0,
        ],
        'meta' => [
            'api_version' => 'v1',
            'bootstrap_version' => 'foundation-1',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}
