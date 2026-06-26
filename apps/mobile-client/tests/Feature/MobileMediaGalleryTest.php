<?php

use App\Livewire\Mobile\MediaGallery;
use App\Models\MobileLocalMediaItem;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
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

test('media gallery share action is hidden and blocked by disabled share policy', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileMediaGalleryPolicyBootstrapEnvelope([
        'native_share' => mobileMediaGalleryPolicyFeature(
            enabled: false,
            state: 'hidden',
            message: 'Media sharing is disabled by admin policy.',
        ),
    ]));

    $mediaItem = MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/media/share-blocked.jpg',
        'caption' => 'Share blocked',
        'sync_status' => MobileLocalMediaItem::SYNC_PENDING,
        'created_at' => CarbonImmutable::now(),
    ]);

    Livewire::test(MediaGallery::class)
        ->assertSee('share-blocked.jpg')
        ->assertDontSee('wire:click="shareMediaItem('.$mediaItem->id.')"', false)
        ->call('shareMediaItem', $mediaItem->id)
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Share unavailable'
                && ($params['message'] ?? null) === 'Media sharing is disabled by admin policy.';
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

/**
 * @param  array<string, array<string, mixed>>  $features
 * @return array<string, mixed>
 */
function mobileMediaGalleryPolicyBootstrapEnvelope(array $features): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
            'current_tenant' => [
                'id' => 'tenant-001',
                'name' => 'North Field Team',
                'status' => 'active',
                'subscription_state' => 'active',
            ],
            'available_tenants' => [],
            'permissions' => [
                'status' => 'resolved',
                'roles' => [],
                'abilities' => [],
                'ability_list' => [],
            ],
            'features' => [
                'version' => 'mobile-media-gallery-policy-test',
                'items' => $features,
            ],
            'remote_config' => ['version' => 'mobile-media-gallery-policy-test', 'values' => []],
            'app_version' => ['status' => 'supported', 'maintenance' => ['enabled' => false]],
            'maintenance' => ['enabled' => false],
            'subscription' => [
                'status' => 'active',
                'features_limited' => false,
                'feature_impacts' => ['paid_features_blocked' => false, 'reason' => null],
            ],
            'notification_preferences' => ['in_app_enabled' => true, 'push_enabled' => false],
            'sync' => ['enabled' => true, 'reason' => null],
            'unread_notification_count' => 0,
        ],
        'meta' => [
            'api_version' => 'v1',
            'bootstrap_version' => 'mobile-media-gallery-policy-test',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileMediaGalleryPolicyFeature(bool $enabled, string $state, ?string $message = null): array
{
    return [
        'state' => $state,
        'visible' => $state !== 'hidden',
        'enabled' => $enabled,
        'reason' => $enabled ? null : 'feature_disabled_by_admin',
        'message' => $message,
        'next_action' => $enabled ? null : 'contact_admin',
        'source' => 'mobile_media_gallery_policy_test',
    ];
}
