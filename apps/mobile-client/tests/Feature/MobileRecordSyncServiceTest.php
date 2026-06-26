<?php

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Models\MobileLocalRecord;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\MobileNetworkStatus;
use App\Services\MobileLocal\RecordRepository;
use App\Services\MobileRecords\MobileRecordSyncService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-26 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-record-sync.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_record_sync.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_record_sync.revoked_tokens',
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    Http::preventStrayRequests();

    $this->networkState = new MobileRecordSyncServiceFakeNetworkState(available: true);
    $this->app->instance(MobileNetworkState::class, $this->networkState);

    app(AccessTokenService::class)->put('record-sync-access-token', CarbonImmutable::now()->addMinutes(15));
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('record sync service creates server record and stores server metadata locally', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/records' => Http::response(mobileRecordSyncEnvelope('srv-record-001'), 201),
    ]);

    $record = mobileRecordSyncLocalRecord();

    $result = app(MobileRecordSyncService::class)->create($record);

    expect($result->synced)->toBeTrue()
        ->and($result->record->sync_status)->toBe(MobileLocalRecord::SYNC_SYNCED)
        ->and($result->record->serverRecordId())->toBe('srv-record-001')
        ->and($result->record->metadataValue('api_sync_error'))->toBeNull()
        ->and($result->record->metadataValue('server_record')['sync_version'])->toBe('sync-v1');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records'
        && $request->hasHeader('Authorization', 'Bearer record-sync-access-token')
        && $request['title'] === 'Local field inspection'
        && $request['status'] === MobileLocalRecord::STATUS_ACTIVE
        && $request['priority'] === MobileLocalRecord::PRIORITY_HIGH
        && $request['metadata']['mobile_local']['id'] === (string) $record->getKey());
});

test('record sync service updates existing server records and keeps notes local', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/records/srv-existing' => Http::response(mobileRecordSyncEnvelope('srv-existing', 'sync-v2')),
    ]);

    $record = mobileRecordSyncLocalRecord([
        'server_record' => [
            'id' => 'srv-existing',
            'sync_version' => 'sync-v1',
        ],
    ]);

    $result = app(MobileRecordSyncService::class)->save($record);

    expect($result->synced)->toBeTrue()
        ->and($result->record->serverRecordId())->toBe('srv-existing')
        ->and($result->record->metadataValue('server_record')['sync_version'])->toBe('sync-v2');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records/srv-existing'
        && $request['title'] === 'Local field inspection'
        && ! isset($request['note']));
});

test('record sync service leaves local record pending while offline', function (): void {
    $this->networkState->available = false;
    Http::fake();

    $record = mobileRecordSyncLocalRecord();

    $result = app(MobileRecordSyncService::class)->create($record);

    expect($result->synced)->toBeFalse()
        ->and($result->failed())->toBeFalse()
        ->and($result->record->sync_status)->toBe(MobileLocalRecord::SYNC_PENDING)
        ->and($result->record->serverRecordId())->toBeNull();

    Http::assertNothingSent();
});

test('record sync service records api failures for retry context', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/records' => Http::response([
            'success' => false,
            'error' => [
                'code' => 'permission_denied',
                'message' => 'Records are disabled for this tenant.',
                'category' => 'permission',
                'next_action' => 'contact_admin',
            ],
            'meta' => ['api_version' => 'v1'],
        ], 403),
    ]);

    $record = mobileRecordSyncLocalRecord();

    $result = app(MobileRecordSyncService::class)->create($record);

    expect($result->synced)->toBeFalse()
        ->and($result->failed())->toBeTrue()
        ->and($result->record->sync_status)->toBe(MobileLocalRecord::SYNC_FAILED)
        ->and($result->record->metadataValue('api_sync_error')['code'])->toBe('permission_denied')
        ->and($result->record->metadataValue('api_sync_error')['message'])->toBe('Records are disabled for this tenant.');
});

/**
 * @param  array<string, mixed>  $metadata
 */
function mobileRecordSyncLocalRecord(array $metadata = []): MobileLocalRecord
{
    return app(RecordRepository::class)->create(
        title: 'Local field inspection',
        description: 'Created on the mobile device.',
        status: MobileLocalRecord::STATUS_ACTIVE,
        priority: MobileLocalRecord::PRIORITY_HIGH,
        categoryId: 1,
        userId: null,
        dueAt: null,
        tags: ['field', 'urgent'],
        notes: 'Inspect before noon.',
        metadata: $metadata,
    );
}

/**
 * @return array<string, mixed>
 */
function mobileRecordSyncEnvelope(string $recordId, string $syncVersion = 'sync-v1'): array
{
    return [
        'success' => true,
        'data' => [
            'record' => [
                'id' => $recordId,
                'tenant_id' => 'tenant-001',
                'title' => 'Server field inspection',
                'status' => 'active',
                'priority' => 'high',
                'category' => ['id' => 'cat-001', 'name' => 'General'],
                'tags' => [],
                'notes_count' => 1,
                'attachments_count' => 0,
                'activities_count' => 1,
                'sync_version' => $syncVersion,
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
            ],
        ],
        'meta' => ['api_version' => 'v1'],
    ];
}

final class MobileRecordSyncServiceFakeNetworkState implements MobileNetworkState
{
    public function __construct(public bool $available) {}

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function status(): MobileNetworkStatus
    {
        return new MobileNetworkStatus(isOnline: $this->available);
    }
}
