<?php

use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileRecords\MobileRecordApiService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-26 12:00:00'));

    config([
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_record_api.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_record_api.revoked_tokens',
    ]);

    Http::preventStrayRequests();

    app(AccessTokenService::class)->put('record-api-access-token', CarbonImmutable::now()->addMinutes(15));
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('record api service lists records with bearer token', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/records*' => Http::response([
            'success' => true,
            'data' => [
                'records' => [
                    mobileRecordApiPayload('rec-001'),
                ],
            ],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    $response = app(MobileRecordApiService::class)->list(['search' => 'field']);

    expect($response['data']['records'][0]['id'])->toBe('rec-001');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/records?search=field'
        && $request->hasHeader('Authorization', 'Bearer record-api-access-token'));
});

test('record api service sends create update archive and restore requests', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/records' => Http::response(mobileRecordApiEnvelope('rec-created'), 201),
        'https://api-admin.example.test/api/v1/mobile/records/rec-created' => Http::response(mobileRecordApiEnvelope('rec-created')),
        'https://api-admin.example.test/api/v1/mobile/records/rec-created/restore' => Http::response(mobileRecordApiEnvelope('rec-created')),
    ]);

    $service = app(MobileRecordApiService::class);

    $created = $service->create([
        'title' => 'Created record',
        'status' => 'active',
        'priority' => 'high',
    ]);
    $updated = $service->update('rec-created', ['title' => 'Updated record']);
    $archived = $service->archive('rec-created');
    $restored = $service->restore('rec-created');

    expect($created['id'])->toBe('rec-created')
        ->and($updated['id'])->toBe('rec-created')
        ->and($archived['id'])->toBe('rec-created')
        ->and($restored['id'])->toBe('rec-created');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records'
        && $request['title'] === 'Created record'
        && $request->hasHeader('Authorization', 'Bearer record-api-access-token'));
    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records/rec-created'
        && $request['title'] === 'Updated record');
    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records/rec-created');
    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records/rec-created/restore');
});

test('record api service rejects malformed record envelopes', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/records' => Http::response([
            'success' => true,
            'data' => [],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    app(MobileRecordApiService::class)->create(['title' => 'Missing record']);
})->throws(MobileApiException::class, 'The mobile API returned an unexpected response.');

/**
 * @return array<string, mixed>
 */
function mobileRecordApiEnvelope(string $recordId): array
{
    return [
        'success' => true,
        'data' => [
            'record' => mobileRecordApiPayload($recordId),
        ],
        'meta' => ['api_version' => 'v1'],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileRecordApiPayload(string $recordId): array
{
    return [
        'id' => $recordId,
        'tenant_id' => 'tenant-001',
        'title' => 'Server record',
        'status' => 'active',
        'priority' => 'normal',
        'category' => ['id' => 'cat-001', 'name' => 'Field'],
        'tags' => [],
        'notes_count' => 0,
        'attachments_count' => 0,
        'activities_count' => 1,
        'sync_version' => 'server-version-1',
        'updated_at' => '2026-06-26T12:00:00+00:00',
        'created_at' => '2026-06-26T12:00:00+00:00',
        'actions' => [
            'view' => true,
            'update' => true,
            'archive' => true,
            'restore' => false,
            'delete' => false,
            'attachments_manage' => true,
        ],
    ];
}
