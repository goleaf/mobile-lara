<?php

use App\Livewire\Admin\MobileDiagnosticReports;
use App\Models\MobileDeviceSession;
use App\Models\MobileDiagnosticReport;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest diagnostics report requests redirect to login', function (): void {
    $this->get('/admin/mobile/diagnostics')
        ->assertRedirect('/login');
});

test('non platform admins cannot view diagnostics reports', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/mobile/diagnostics')
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    expect(Gate::forUser($user)->allows('viewAny', MobileDiagnosticReport::class))->toBeFalse();
});

test('platform admins can view privacy filtered diagnostics reports', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();
    [$report, $mobileUser] = adminDiagnosticsReport();

    $this->actingAs($admin)
        ->get('/admin/mobile/diagnostics')
        ->assertOk()
        ->assertSeeLivewire(MobileDiagnosticReports::class)
        ->assertSee('Mobile Diagnostics')
        ->assertSee($report->tenant?->name)
        ->assertSee($report->deviceSession?->device_name)
        ->assertSee('2 failed')
        ->assertDontSee($mobileUser->email);

    expect(Gate::forUser($admin)->allows('viewAny', MobileDiagnosticReport::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('view', $report))->toBeTrue();
});

test('platform admins can review a diagnostics report snapshot without raw secrets', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    [$report, $mobileUser] = adminDiagnosticsReport();

    Livewire::actingAs($admin)
        ->test(MobileDiagnosticReports::class)
        ->call('selectReport', $report->id)
        ->assertSet('selectedReportId', $report->id)
        ->assertSee('Report detail')
        ->assertSee('1 pending / 2 failed / 1 conflicts')
        ->assertSee('[redacted-email]')
        ->assertSee('server_side_redaction')
        ->assertDontSee($mobileUser->email)
        ->assertDontSee('support-secret-token');
});

test('platform admins can search diagnostics by tenant and device context', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    [$matchingReport] = adminDiagnosticsReport(
        tenantAttributes: ['name' => 'Alpha Field Team', 'slug' => 'alpha-field-team'],
        deviceAttributes: ['device_name' => 'Rugged Phone'],
    );
    [$otherReport] = adminDiagnosticsReport(
        tenantAttributes: ['name' => 'Beta Warehouse', 'slug' => 'beta-warehouse'],
        deviceAttributes: ['device_name' => 'Tablet Station'],
    );

    Livewire::actingAs($admin)
        ->test(MobileDiagnosticReports::class)
        ->set('search', 'Alpha Field')
        ->assertSee($matchingReport->tenant?->name)
        ->assertDontSee($otherReport->tenant?->name)
        ->set('search', 'Tablet Station')
        ->assertSee($otherReport->deviceSession?->device_name)
        ->assertDontSee($matchingReport->deviceSession?->device_name);
});

/**
 * @param  array<string, mixed>  $tenantAttributes
 * @param  array<string, mixed>  $deviceAttributes
 * @return array{0: MobileDiagnosticReport, 1: User}
 */
function adminDiagnosticsReport(array $tenantAttributes = [], array $deviceAttributes = []): array
{
    $tenant = Tenant::factory()->create([
        'name' => $tenantAttributes['name'] ?? 'Diagnostics Tenant',
        'slug' => $tenantAttributes['slug'] ?? 'diagnostics-tenant',
    ]);
    $mobileUser = User::factory()->create([
        'name' => 'Mobile Reporter',
        'email' => fake()->unique()->safeEmail(),
    ]);
    $deviceSession = MobileDeviceSession::factory()
        ->for($mobileUser)
        ->create([
            'device_name' => $deviceAttributes['device_name'] ?? 'Browser Phone',
            'platform' => $deviceAttributes['platform'] ?? 'ios',
            'app_version' => '1.0.0',
        ]);

    $report = MobileDiagnosticReport::factory()
        ->for($tenant)
        ->for($mobileUser, 'user')
        ->for($deviceSession, 'deviceSession')
        ->create([
            'app_version' => '1.0.0',
            'redactions_applied' => ['tokens', 'headers', 'payloads', 'server_side_redaction'],
            'snapshot' => [
                'generated_at' => '2026-06-25T12:00:00+00:00',
                'app' => [
                    'app_version' => '1.0.0',
                    'api_base_url' => 'https://api-admin.test/api/v1/mobile',
                ],
                'tenant' => [
                    'tenant_id' => $tenant->public_id,
                    'status' => 'active',
                    'subscription_state' => 'active',
                ],
                'features' => [
                    'version' => 'diagnostics-feature-v1',
                ],
                'remote_config' => [
                    'version' => 'support-config-v1',
                    'support_context' => [
                        'secret_token' => '[redacted]',
                        'contact_email' => '[redacted-email]',
                    ],
                ],
                'network' => [
                    'state' => 'Online',
                ],
                'sync' => [
                    'pending_actions' => 1,
                    'failed_actions' => 2,
                    'conflict_actions' => 1,
                ],
                'device' => [
                    'device_model' => 'Browser runtime',
                    'os_version' => 'Browser runtime',
                ],
                'redactions_applied' => ['tokens', 'headers', 'payloads', 'server_side_redaction'],
            ],
            'failed_sync_actions_count' => 2,
            'received_at' => now(),
        ]);

    return [$report->load(['tenant', 'deviceSession']), $mobileUser];
}
