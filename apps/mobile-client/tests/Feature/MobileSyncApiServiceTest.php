<?php

use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileSync\MobileSyncApiService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-26 12:00:00'));

    config([
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_sync_api.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_sync_api.revoked_tokens',
    ]);

    Http::preventStrayRequests();

    app(AccessTokenService::class)->put('sync-api-access-token', CarbonImmutable::now()->addMinutes(15));
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('mobile sync api service calls bootstrap pull push and acknowledge endpoints', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/sync/bootstrap' => Http::response(mobileSyncApiServiceEnvelope([
            'sync_policy' => ['enabled' => true],
        ])),
        'https://api-admin.example.test/api/v1/mobile/sync/pull*' => Http::response(mobileSyncApiServiceEnvelope([
            'server_changes' => ['records' => []],
            'next_cursor' => '2026-06-26T12:00:00+00:00',
        ])),
        'https://api-admin.example.test/api/v1/mobile/sync/push' => Http::response(mobileSyncApiServiceEnvelope([
            'accepted' => [['client_intent_id' => 'intent-001']],
            'rejected' => [],
            'conflicts' => [],
        ])),
        'https://api-admin.example.test/api/v1/mobile/sync/acknowledge' => Http::response(mobileSyncApiServiceEnvelope([
            'acknowledged_count' => 1,
        ])),
    ]);

    $service = app(MobileSyncApiService::class);

    expect($service->bootstrap()['sync_policy']['enabled'])->toBeTrue()
        ->and($service->pull(['cursor' => '2026-06-26T11:00:00+00:00'])['next_cursor'])->toBe('2026-06-26T12:00:00+00:00')
        ->and($service->push(['items' => []])['accepted'][0]['client_intent_id'])->toBe('intent-001')
        ->and($service->acknowledge(['acknowledgements' => []])['acknowledged_count'])->toBe(1);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/sync/bootstrap'
        && $request->hasHeader('Authorization', 'Bearer sync-api-access-token'));
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/sync/pull?cursor=2026-06-26T11%3A00%3A00%2B00%3A00');
    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/sync/push');
    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/sync/acknowledge');
});

test('mobile sync api service requires an access token', function (): void {
    app(AccessTokenService::class)->forget();

    Http::fake();

    app(MobileSyncApiService::class)->bootstrap();
})->throws(MobileApiException::class, 'A valid mobile session is required.');

/**
 * @param  array<string, mixed>  $data
 * @return array<string, mixed>
 */
function mobileSyncApiServiceEnvelope(array $data): array
{
    return [
        'success' => true,
        'data' => $data,
        'meta' => [
            'api_version' => 'v1',
            'server_time' => '2026-06-26T12:00:00+00:00',
        ],
    ];
}
