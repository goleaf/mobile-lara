<?php

use App\Livewire\Mobile\MediaGallery;
use App\Models\MobileLocalMediaItem;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-media-gallery.sqlite');

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

test('media gallery renders stored local media and summary metrics', function (): void {
    MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/media/camera-one.jpg',
        'caption' => 'Camera capture',
        'sync_status' => MobileLocalMediaItem::SYNC_PENDING,
        'created_at' => CarbonImmutable::now(),
    ]);

    MobileLocalMediaItem::factory()->video()->synced()->create([
        'path' => '/tmp/media/clip-one.mp4',
        'caption' => 'Gallery video',
        'created_at' => CarbonImmutable::now()->subMinute(),
    ]);

    MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/media/import-one.pdf',
        'type' => MobileLocalMediaItem::TYPE_FILE,
        'mime' => 'application/pdf',
        'caption' => 'Failed import',
        'sync_status' => MobileLocalMediaItem::SYNC_FAILED,
        'related_entity_type' => 'document',
        'related_entity_id' => 'doc-7',
        'created_at' => CarbonImmutable::now()->subMinutes(2),
    ]);

    Livewire::test(MediaGallery::class)
        ->assertSee('Media gallery')
        ->assertSee('Library summary')
        ->assertSee('3 shown')
        ->assertSee('camera-one.jpg')
        ->assertSee('clip-one.mp4')
        ->assertSee('import-one.pdf')
        ->assertSee('Share')
        ->assertSee('document #doc-7')
        ->assertSeeInOrder([
            'camera-one.jpg',
            'clip-one.mp4',
            'import-one.pdf',
        ])
        ->call('refreshGallery')
        ->assertSee('camera-one.jpg');
});

test('media gallery share button reports browser fallback outside NativePHP', function (): void {
    $mediaItem = MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/media/share-target.jpg',
        'caption' => 'Share target',
        'sync_status' => MobileLocalMediaItem::SYNC_PENDING,
        'created_at' => CarbonImmutable::now(),
    ]);

    Livewire::test(MediaGallery::class)
        ->call('shareMediaItem', $mediaItem->id)
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Share unavailable'
                && ($params['message'] ?? null) === 'Native text sharing is unavailable in this browser runtime.';
        });
});

test('media gallery filters by type and sync state', function (): void {
    MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/media/photo-filter.jpg',
        'sync_status' => MobileLocalMediaItem::SYNC_PENDING,
    ]);

    MobileLocalMediaItem::factory()->video()->create([
        'path' => '/tmp/media/video-filter.mp4',
        'sync_status' => MobileLocalMediaItem::SYNC_SYNCED,
    ]);

    MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/media/failed-filter.jpg',
        'sync_status' => MobileLocalMediaItem::SYNC_FAILED,
    ]);

    Livewire::test(MediaGallery::class)
        ->call('setFilter', 'images')
        ->assertSet('filter', 'images')
        ->assertSee('photo-filter.jpg')
        ->assertSee('failed-filter.jpg')
        ->assertDontSee('video-filter.mp4')
        ->call('setFilter', 'videos')
        ->assertSee('video-filter.mp4')
        ->assertDontSee('photo-filter.jpg')
        ->call('setFilter', 'failed')
        ->assertSee('failed-filter.jpg')
        ->assertDontSee('photo-filter.jpg')
        ->call('setFilter', 'unknown')
        ->assertSet('filter', 'all')
        ->assertSee('photo-filter.jpg')
        ->assertSee('video-filter.mp4');
});

test('media gallery renders an empty state without local media', function (): void {
    Livewire::test(MediaGallery::class)
        ->assertSee('No media items')
        ->assertSee('0 shown');
});
