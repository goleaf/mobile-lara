<?php

use App\Enums\TenantUserRole;
use App\Models\MobileDiagnosticReport;
use App\Models\MobileRemoteConfig;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('mobile users can upload privacy-safe diagnostics when enabled by remote config', function (): void {
    [$user, $tenant] = mobileDiagnosticsUserWithTenant();
    mobileDiagnosticsEnabled();
    $accessToken = mobileDiagnosticsAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/diagnostics', mobileDiagnosticsPayload($tenant, [
            'support_ticket_id' => 'ticket-123',
            'client_reference' => 'local-diagnostics-001',
        ]))
        ->assertCreated()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->whereType('data.diagnostic_id', 'string')
            ->where('data.support_ticket_id', 'ticket-123')
            ->where('data.next_action', 'view_support_ticket')
            ->where('meta.diagnostics_version', 'foundation-diagnostics-1')
            ->etc()
        );

    $report = MobileDiagnosticReport::query()->firstOrFail();
    $snapshotJson = json_encode($report->snapshot);

    expect($report->tenant_id)->toBe($tenant->id)
        ->and($report->user_id)->toBe($user->id)
        ->and($report->support_ticket_id)->toBe('ticket-123')
        ->and($report->api_base_url)->toBe('https://api-admin.test/api/v1/mobile')
        ->and($report->failed_sync_actions_count)->toBe(1)
        ->and($report->redactions_applied)->toContain('server_side_redaction')
        ->and($report->snapshot['remote_config']['support_context']['secret_token'])->toBe('[redacted]')
        ->and($report->snapshot['failed_sync_actions'][0]['last_error'])->toBe('Remote rejected Bearer [redacted] for [redacted-email].')
        ->and($snapshotJson)->not->toContain('api-secret')
        ->and($snapshotJson)->not->toContain('mobile@example.test')
        ->and($snapshotJson)->not->toContain('support-private@example.test')
        ->and($snapshotJson)->not->toContain('queued-secret-token')
        ->and($snapshotJson)->not->toContain('abcdef123456');

    $audit = SecurityAuditEvent::query()->where('event', 'mobile_diagnostics_uploaded')->firstOrFail();

    expect($audit->metadata['tenant_public_id'])->toBe($tenant->public_id)
        ->and($audit->metadata['diagnostic_report_id'])->toBe($report->public_id)
        ->and($audit->metadata['support_ticket_id'])->toBe('ticket-123')
        ->and($audit->metadata['failed_sync_actions_count'])->toBe(1);
});

test('mobile diagnostics upload fails closed when remote config disables diagnostics', function (): void {
    [$user, $tenant] = mobileDiagnosticsUserWithTenant();
    $accessToken = mobileDiagnosticsAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/diagnostics', mobileDiagnosticsPayload($tenant))
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'diagnostics_disabled')
        ->assertJsonPath('error.next_action', 'contact_admin');

    expect(MobileDiagnosticReport::query()->exists())->toBeFalse();
});

test('mobile diagnostics upload rejects mismatched tenant context', function (): void {
    [$user, $tenant] = mobileDiagnosticsUserWithTenant();
    mobileDiagnosticsEnabled();
    $accessToken = mobileDiagnosticsAccessToken($this, $user);
    $otherTenant = Tenant::factory()->create();

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/diagnostics', mobileDiagnosticsPayload($otherTenant))
        ->assertStatus(409)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'diagnostics_tenant_mismatch')
        ->assertJsonPath('error.next_action', 'refresh_bootstrap');

    expect(MobileDiagnosticReport::query()->exists())->toBeFalse();
});

test('mobile diagnostics upload requires an active tenant context', function (): void {
    $user = User::factory()->create([
        'email' => 'diagnostics-invited@example.test',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create();

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->invited()
        ->current()
        ->create();

    mobileDiagnosticsEnabled();
    $accessToken = mobileDiagnosticsAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/diagnostics', mobileDiagnosticsPayload($tenant))
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'no_active_tenant')
        ->assertJsonPath('error.next_action', 'select_tenant');

    expect(MobileDiagnosticReport::query()->exists())->toBeFalse();
});

test('mobile diagnostics upload rejects raw failed action payload and headers', function (): void {
    [$user, $tenant] = mobileDiagnosticsUserWithTenant();
    mobileDiagnosticsEnabled();
    $accessToken = mobileDiagnosticsAccessToken($this, $user);
    $payload = mobileDiagnosticsPayload($tenant);
    $payload['snapshot']['failed_sync_actions'][0]['payload'] = ['secret' => 'should-not-appear'];
    $payload['snapshot']['failed_sync_actions'][0]['headers'] = ['Authorization' => 'Bearer should-not-appear'];

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/diagnostics', $payload)
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'validation_failed');

    expect(MobileDiagnosticReport::query()->exists())->toBeFalse();
});

test('contract catalogue marks diagnostics upload route as implemented', function (): void {
    $this->getJson('/api/v1/mobile/contracts')
        ->assertOk()
        ->assertJsonPath('data.contracts.13.key', 'diagnostics')
        ->assertJsonPath('data.contracts.13.status', 'partial')
        ->assertJsonPath('data.contracts.13.routes.0.path', '/diagnostics')
        ->assertJsonPath('data.contracts.13.routes.0.status', 'implemented');
});

/**
 * @return array{0: User, 1: Tenant}
 */
function mobileDiagnosticsUserWithTenant(TenantUserRole $role = TenantUserRole::MobileUser): array
{
    $user = User::factory()->create([
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create();

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role($role)
        ->create();

    return [$user, $tenant];
}

function mobileDiagnosticsEnabled(): void
{
    MobileRemoteConfig::factory()->create([
        'key' => 'support',
        'value' => [
            'url' => 'https://support.example.test',
            'diagnostics_enabled' => true,
        ],
    ]);
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function mobileDiagnosticsPayload(Tenant $tenant, array $overrides = []): array
{
    return array_replace_recursive([
        'client_reference' => null,
        'support_ticket_id' => null,
        'snapshot' => [
            'generated_at' => '2026-06-25T12:00:00+00:00',
            'app' => [
                'app_version' => '1.0.0',
                'api_base_url' => 'https://mobile-user:api-secret@api-admin.test/api/v1/mobile?debug=1',
                'laravel_version' => '13.x',
                'livewire_version' => '4.x',
                'nativephp_mobile_version' => '1.x',
                'nativephp_running' => false,
                'nativephp_app_id' => 'com.mobilelara.app',
                'nativephp_start_url' => 'https://mobile-user:api-secret@mobile-lara.test',
            ],
            'user' => [
                'authenticated' => true,
                'id' => 42,
                'source' => 'bootstrap_cache',
            ],
            'tenant' => [
                'tenant_id' => $tenant->public_id,
                'status' => 'active',
                'subscription_state' => 'active',
            ],
            'features' => [
                'version' => 'diagnostics-feature-v1',
                'items' => [
                    'records' => [
                        'state' => 'visible',
                        'enabled' => true,
                        'visible' => true,
                        'reason' => null,
                        'source' => 'tenant_override',
                    ],
                ],
            ],
            'remote_config' => [
                'version' => 'remote-config-v1',
                'values' => [
                    'support' => [
                        'url' => 'https://support.example.test',
                        'diagnostics_enabled' => true,
                    ],
                ],
                'support_context' => [
                    'secret_token' => 'support-secret-token',
                    'contact_email' => 'support-private@example.test',
                ],
            ],
            'network' => [
                'state' => 'Online',
                'source' => 'assumed',
            ],
            'sync' => [
                'enabled' => true,
                'pending_actions' => 0,
                'failed_actions' => 1,
                'conflict_actions' => 0,
            ],
            'failed_sync_actions' => [
                [
                    'id' => 10,
                    'action_type' => 'records.update',
                    'method' => 'PATCH',
                    'endpoint' => '/api/v1/mobile/records/record-123?email=mobile@example.test',
                    'attempts' => 2,
                    'last_error' => 'Remote rejected Bearer abcdef123456 for mobile@example.test.',
                    'conflict_status' => 'none',
                    'created_at' => '2026-06-25T11:58:00+00:00',
                    'available_at' => '2026-06-25T12:03:00+00:00',
                ],
            ],
            'device' => [
                'device_model' => 'Browser runtime',
                'os_version' => 'Browser runtime',
                'battery_status' => 'Unavailable',
                'charging_status' => 'Unavailable',
            ],
            'redactions_applied' => ['tokens', 'headers', 'payloads'],
        ],
    ], $overrides);
}

function mobileDiagnosticsAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'diagnostics-device-001',
        'device_name' => 'Diagnostics Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
