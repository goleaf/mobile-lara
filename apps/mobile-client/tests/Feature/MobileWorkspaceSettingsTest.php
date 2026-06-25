<?php

use App\Livewire\Mobile\Settings\Workspace;
use App\Models\User;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
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

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-workspace-settings.sqlite');

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
        'mobile_auth.storage.session_key' => 'testing.mobile_workspace.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_workspace.revoked_tokens',
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

test('workspace settings renders cached tenant context', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileWorkspaceBootstrapEnvelope('tenant-001'));

    Livewire::test(Workspace::class)
        ->assertSet('selectedTenantId', 'tenant-001')
        ->assertSee('North Field Team')
        ->assertSee('South Field Team')
        ->assertSee('Current')
        ->assertSee('Available');
});

test('workspace settings switches tenant through api and refreshes bootstrap cache', function (): void {
    $this->actingAs(User::factory()->create());

    app(SettingsRepository::class)->cacheBootstrapContext(mobileWorkspaceBootstrapEnvelope('tenant-001'));
    app(AccessTokenService::class)->put('workspace-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/tenants/current' => Http::response([
            'success' => true,
            'data' => [
                'current_tenant' => mobileWorkspaceTenant('tenant-002', current: true),
                'available_tenants' => [
                    mobileWorkspaceTenant('tenant-001', current: false),
                    mobileWorkspaceTenant('tenant-002', current: true),
                ],
                'next_bootstrap_required' => true,
            ],
            'meta' => ['api_version' => 'v1'],
        ]),
        'https://api-admin.example.test/api/v1/mobile/bootstrap' => Http::response(mobileWorkspaceBootstrapEnvelope('tenant-002')),
    ]);

    Livewire::test(Workspace::class)
        ->call('selectTenant', 'tenant-002')
        ->call('switchTenant')
        ->assertSet('selectedTenantId', 'tenant-002')
        ->assertSee('Workspace switched.');

    expect(app(SettingsRepository::class)->bootstrapContext()['data']['current_tenant']['id'])->toBe('tenant-002');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/tenants/current'
        && $request->hasHeader('Authorization', 'Bearer workspace-access-token')
        && $request['tenant_id'] === 'tenant-002');
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/bootstrap'
        && $request->hasHeader('Authorization', 'Bearer workspace-access-token'));
});

/**
 * @return array<string, mixed>
 */
function mobileWorkspaceBootstrapEnvelope(string $currentTenantId): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
            'current_tenant' => mobileWorkspaceTenant($currentTenantId, current: true),
            'available_tenants' => [
                mobileWorkspaceTenant('tenant-001', current: $currentTenantId === 'tenant-001'),
                mobileWorkspaceTenant('tenant-002', current: $currentTenantId === 'tenant-002'),
            ],
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

/**
 * @return array<string, mixed>
 */
function mobileWorkspaceTenant(string $id, bool $current): array
{
    return [
        'id' => $id,
        'name' => $id === 'tenant-001' ? 'North Field Team' : 'South Field Team',
        'slug' => $id,
        'status' => 'active',
        'subscription_state' => 'active',
        'role_summary' => [
            'role' => 'mobile_user',
            'label' => 'Mobile user',
            'membership_status' => 'active',
        ],
        'switchable' => true,
        'current' => $current,
        'disabled_reason' => null,
    ];
}
