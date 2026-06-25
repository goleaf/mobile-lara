<?php

use App\Models\MobileLocalMediaItem;
use App\Services\MobileLocal\MediaItemRepository;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-media-items.sqlite');

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

test('media items table stores local media metadata on the mobile connection', function (): void {
    expect(Schema::connection('mobile_local')->hasTable('media_items'))->toBeTrue()
        ->and(Schema::connection('mobile_local')->hasColumns('media_items', [
            'path',
            'type',
            'mime',
            'size',
            'width',
            'height',
            'duration',
            'caption',
            'sync_status',
            'related_entity_type',
            'related_entity_id',
        ]))->toBeTrue();

    $mediaItem = app(MediaItemRepository::class)->record(
        path: '/tmp/native-camera/profile-photo.heic',
        type: 'unknown',
        mime: 'image/heic',
        size: 456_789,
        width: 1200,
        height: 900,
        caption: 'Profile camera capture',
        relatedEntityType: 'profile',
        relatedEntityId: 42,
    );

    expect($mediaItem)->toBeInstanceOf(MobileLocalMediaItem::class)
        ->and($mediaItem->getConnectionName())->toBe('mobile_local')
        ->and($mediaItem->getTable())->toBe('media_items')
        ->and($mediaItem->path)->toBe('/tmp/native-camera/profile-photo.heic')
        ->and($mediaItem->type)->toBe(MobileLocalMediaItem::TYPE_IMAGE)
        ->and($mediaItem->mime)->toBe('image/heic')
        ->and($mediaItem->size)->toBe(456_789)
        ->and($mediaItem->dimensions())->toBe('1200 x 900')
        ->and($mediaItem->formattedSize())->toBeString()
        ->and($mediaItem->caption)->toBe('Profile camera capture')
        ->and($mediaItem->sync_status)->toBe(MobileLocalMediaItem::SYNC_PENDING)
        ->and($mediaItem->related_entity_type)->toBe('profile')
        ->and($mediaItem->related_entity_id)->toBe('42')
        ->and($mediaItem->created_at?->equalTo(CarbonImmutable::now()))->toBeTrue();

    $this->assertModelExists($mediaItem);
});

test('media item repository filters gallery rows and updates sync status', function (): void {
    $repository = app(MediaItemRepository::class);

    $image = $repository->record(
        path: '/tmp/gallery/newest-photo.jpg',
        type: MobileLocalMediaItem::TYPE_IMAGE,
        mime: 'image/jpeg',
        createdAt: CarbonImmutable::now(),
    );

    $video = $repository->record(
        path: '/tmp/gallery/older-video.mp4',
        type: MobileLocalMediaItem::TYPE_VIDEO,
        mime: 'video/mp4',
        duration: 125,
        syncStatus: MobileLocalMediaItem::SYNC_SYNCED,
        createdAt: CarbonImmutable::now()->subMinute(),
    );

    $failed = $repository->record(
        path: '/tmp/gallery/failed-import.pdf',
        type: 'document',
        mime: 'application/pdf',
        syncStatus: MobileLocalMediaItem::SYNC_FAILED,
        relatedEntityType: 'document',
        relatedEntityId: 'abc',
        createdAt: CarbonImmutable::now()->subMinutes(2),
    );

    expect($repository->counts())->toBe([
        'total' => 3,
        'images' => 1,
        'videos' => 1,
        'pending' => 1,
        'failed' => 1,
    ])
        ->and($repository->recent(limit: 1)->first()?->is($image))->toBeTrue()
        ->and($repository->recent(type: MobileLocalMediaItem::TYPE_VIDEO))->toHaveCount(1)
        ->and($repository->recent(syncStatus: MobileLocalMediaItem::SYNC_FAILED)->first()?->is($failed))->toBeTrue()
        ->and($repository->pendingSync()->first()?->is($image))->toBeTrue()
        ->and($video->formattedDuration())->toBe('2:05')
        ->and(MobileLocalMediaItem::query()->forRelatedEntity('document', 'abc')->first()?->is($failed))->toBeTrue()
        ->and($repository->markSynced($image)->sync_status)->toBe(MobileLocalMediaItem::SYNC_SYNCED)
        ->and($repository->markFailed($image)->sync_status)->toBe(MobileLocalMediaItem::SYNC_FAILED);
});
