<?php

use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileTenancy\MobileTenantApiService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    config([
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_tenant_api.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_tenant_api.revoked_tokens',
    ]);

    Http::preventStrayRequests();
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('tenant api service lists available tenants with bearer token', function (): void {
    app(AccessTokenService::class)->put('tenant-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/tenants' => Http::response(mobileTenantContextEnvelope()),
    ]);

    $response = app(MobileTenantApiService::class)->list();

    expect($response['data']['current_tenant']['id'])->toBe('tenant-001');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/tenants'
        && $request->hasHeader('Authorization', 'Bearer tenant-access-token'));
});

test('tenant api service switches current tenant with bearer token', function (): void {
    app(AccessTokenService::class)->put('tenant-switch-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/tenants/current' => Http::response(mobileTenantContextEnvelope('tenant-002')),
    ]);

    $response = app(MobileTenantApiService::class)->switch('tenant-002');

    expect($response['data']['current_tenant']['id'])->toBe('tenant-002')
        ->and($response['data']['next_bootstrap_required'])->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/tenants/current'
        && $request->hasHeader('Authorization', 'Bearer tenant-switch-access-token')
        && $request['tenant_id'] === 'tenant-002');
});

test('tenant api service lists pending invitations with bearer token', function (): void {
    app(AccessTokenService::class)->put('tenant-invitation-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/tenants/invitations' => Http::response(mobileTenantInvitationEnvelope()),
    ]);

    $response = app(MobileTenantApiService::class)->invitations();

    expect($response['data']['invitations'][0]['tenant']['id'])->toBe('tenant-003');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/tenants/invitations'
        && $request->hasHeader('Authorization', 'Bearer tenant-invitation-access-token'));
});

test('tenant api service accepts and declines invitations with bearer token', function (): void {
    app(AccessTokenService::class)->put('tenant-invitation-action-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/tenants/invitations/tenant-003/accept' => Http::response(mobileTenantInvitationResponseEnvelope('active')),
        'https://api-admin.example.test/api/v1/mobile/tenants/invitations/tenant-004/decline' => Http::response(mobileTenantInvitationResponseEnvelope('declined')),
    ]);

    $accepted = app(MobileTenantApiService::class)->acceptInvitation('tenant-003');
    $declined = app(MobileTenantApiService::class)->declineInvitation('tenant-004');

    expect($accepted['data']['invitation']['role_summary']['membership_status'])->toBe('active')
        ->and($declined['data']['invitation']['role_summary']['membership_status'])->toBe('declined');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/tenants/invitations/tenant-003/accept'
        && $request->hasHeader('Authorization', 'Bearer tenant-invitation-action-token'));
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/tenants/invitations/tenant-004/decline'
        && $request->hasHeader('Authorization', 'Bearer tenant-invitation-action-token'));
});

/**
 * @return array<string, mixed>
 */
function mobileTenantContextEnvelope(string $currentTenantId = 'tenant-001'): array
{
    return [
        'success' => true,
        'data' => [
            'current_tenant' => mobileTenantPayload($currentTenantId, current: true),
            'available_tenants' => [
                mobileTenantPayload('tenant-001', current: $currentTenantId === 'tenant-001'),
                mobileTenantPayload('tenant-002', current: $currentTenantId === 'tenant-002'),
            ],
            'next_bootstrap_required' => true,
        ],
        'meta' => ['api_version' => 'v1'],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileTenantInvitationEnvelope(): array
{
    return [
        'success' => true,
        'data' => [
            'invitations' => [
                mobileTenantInvitationPayload(),
            ],
        ],
        'meta' => ['api_version' => 'v1'],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileTenantInvitationResponseEnvelope(string $status): array
{
    return [
        'success' => true,
        'data' => [
            'invitation' => mobileTenantInvitationPayload($status),
            'tenant_context' => [
                'current_tenant' => $status === 'active' ? mobileTenantPayload('tenant-003', current: true) : null,
                'available_tenants' => $status === 'active' ? [mobileTenantPayload('tenant-003', current: true)] : [],
            ],
            'next_bootstrap_required' => true,
        ],
        'meta' => ['api_version' => 'v1'],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileTenantInvitationPayload(string $status = 'invited'): array
{
    return [
        'tenant' => [
            'id' => 'tenant-003',
            'name' => 'Invited Field Team',
            'slug' => 'invited-field-team',
        ],
        'role_summary' => [
            'role' => 'mobile_user',
            'label' => 'Mobile user',
            'membership_status' => $status,
        ],
        'invited_at' => '2026-06-25T12:00:00+00:00',
        'accepted_at' => $status === 'active' ? '2026-06-25T12:01:00+00:00' : null,
        'declined_at' => $status === 'declined' ? '2026-06-25T12:01:00+00:00' : null,
        'expires_at' => null,
        'available_actions' => $status === 'invited' ? ['accept', 'decline'] : [],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileTenantPayload(string $id, bool $current): array
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
