<?php

use App\Models\MobileLocalCheckIn;
use App\Models\MobileLocalMediaItem;
use App\Services\MobileLocal\CheckInRepository;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-check-ins.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('check ins table stores local location metadata on the mobile connection', function (): void {
    expect(Schema::connection('mobile_local')->hasTable('check_ins'))->toBeTrue()
        ->and(Schema::connection('mobile_local')->hasColumns('check_ins', [
            'user_id',
            'latitude',
            'longitude',
            'accuracy',
            'note',
            'photo_id',
            'sync_status',
            'created_at',
        ]))->toBeTrue();

    $photo = MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/check-ins/photo.jpg',
        'type' => MobileLocalMediaItem::TYPE_IMAGE,
        'caption' => 'Check-in photo',
    ]);

    $checkIn = app(CheckInRepository::class)->record(
        userId: 7,
        latitude: 54.687157,
        longitude: 25.279652,
        accuracy: 6.54,
        note: 'Arrived at the customer site.',
        photoId: $photo->id,
    );

    expect($checkIn)->toBeInstanceOf(MobileLocalCheckIn::class)
        ->and($checkIn->getConnectionName())->toBe('mobile_local')
        ->and($checkIn->getTable())->toBe('check_ins')
        ->and($checkIn->user_id)->toBe(7)
        ->and($checkIn->latitude)->toBe(54.687157)
        ->and($checkIn->longitude)->toBe(25.279652)
        ->and($checkIn->accuracy)->toBe(6.54)
        ->and($checkIn->coordinates())->toBe('54.6871570, 25.2796520')
        ->and($checkIn->formattedAccuracy())->toBe('6.54 m')
        ->and($checkIn->notePreview())->toBe('Arrived at the customer site.')
        ->and($checkIn->sync_status)->toBe(MobileLocalCheckIn::SYNC_PENDING)
        ->and($checkIn->photo_id)->toBe($photo->id)
        ->and($checkIn->created_at?->equalTo(CarbonImmutable::now()))->toBeTrue();

    $this->assertModelExists($checkIn);
});

test('check in repository filters rows and updates sync status', function (): void {
    $repository = app(CheckInRepository::class);

    $newest = $repository->record(
        userId: 7,
        latitude: 54.68,
        longitude: 25.27,
        note: 'Newest check-in',
        createdAt: CarbonImmutable::now(),
    );

    $synced = $repository->record(
        userId: 7,
        latitude: 54.69,
        longitude: 25.28,
        syncStatus: MobileLocalCheckIn::SYNC_SYNCED,
        createdAt: CarbonImmutable::now()->subMinute(),
    );

    $failed = $repository->record(
        userId: 7,
        latitude: 54.70,
        longitude: 25.29,
        syncStatus: MobileLocalCheckIn::SYNC_FAILED,
        createdAt: CarbonImmutable::now()->subMinutes(2),
    );

    $otherUser = $repository->record(
        userId: 12,
        latitude: 54.71,
        longitude: 25.30,
        createdAt: CarbonImmutable::now()->subMinutes(3),
    );

    expect($repository->countsForUser(7))->toBe([
        'total' => 3,
        'pending' => 1,
        'synced' => 1,
        'failed' => 1,
    ])
        ->and($repository->recentForUser(7, limit: 1)->first()?->is($newest))->toBeTrue()
        ->and($repository->recentForUser(7, syncStatus: MobileLocalCheckIn::SYNC_SYNCED)->first()?->is($synced))->toBeTrue()
        ->and($repository->recentForUser(7, syncStatus: MobileLocalCheckIn::SYNC_FAILED)->first()?->is($failed))->toBeTrue()
        ->and($repository->recentForUser(7)->contains(fn (MobileLocalCheckIn $checkIn): bool => $checkIn->is($otherUser)))->toBeFalse()
        ->and($repository->pendingSync())->toHaveCount(2)
        ->and($repository->markSynced($newest)->sync_status)->toBe(MobileLocalCheckIn::SYNC_SYNCED)
        ->and($repository->markFailed($newest)->sync_status)->toBe(MobileLocalCheckIn::SYNC_FAILED);
});
