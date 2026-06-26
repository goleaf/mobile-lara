<?php

use App\Livewire\Mobile\FileManager;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->fileManagerPath = storage_path('framework/testing/mobile-file-manager/files');
    $this->fileExportPath = storage_path('framework/testing/mobile-file-manager/exports');
    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-file-manager/mobile-local.sqlite');

    File::deleteDirectory(storage_path('framework/testing/mobile-file-manager'));
    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'nativephp-internal.running' => false,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_local.storage.file_manager_path' => $this->fileManagerPath,
        'mobile_local.storage.file_export_path' => $this->fileExportPath,
        'mobile_local.storage.file_preview_bytes' => 65536,
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
});

afterEach(function (): void {
    File::deleteDirectory(storage_path('framework/testing/mobile-file-manager'));
});

test('file manager screen renders file operations and fallback state', function (): void {
    Livewire::test(FileManager::class)
        ->assertSee('File manager')
        ->assertSee('File bridge')
        ->assertSee('Browser fallback active')
        ->assertSee('Editor')
        ->assertSee('Copy and move')
        ->assertSee('Import')
        ->assertSee('Capabilities')
        ->assertSee('Local files')
        ->assertSee('No local files');
});

test('file manager writes reads copies moves exports shares and deletes files', function (): void {
    Livewire::test(FileManager::class)
        ->set('filePath', 'notes/demo.txt')
        ->set('fileContents', 'Demo file contents')
        ->call('writeCurrentFile')
        ->assertSet('selectedPath', 'notes/demo.txt')
        ->assertSee('File written locally.')
        ->assertSee('demo.txt')
        ->call('readCurrentFile')
        ->assertSet('fileContents', 'Demo file contents')
        ->set('copyTo', 'copies/demo-copy.txt')
        ->call('copyCurrentFile')
        ->assertSee('File copied locally.')
        ->set('moveTo', 'archive/demo-moved.txt')
        ->call('moveCurrentFile')
        ->assertSet('filePath', 'archive/demo-moved.txt')
        ->assertSee('File moved locally.')
        ->call('exportFile')
        ->assertSee('File exported locally.')
        ->call('shareFile')
        ->assertSee('Native file sharing is unavailable in this browser runtime.')
        ->call('deleteFile', 'archive/demo-moved.txt')
        ->assertSee('File deleted locally.');

    expect(File::exists($this->fileExportPath.'/demo-moved.txt'))->toBeTrue()
        ->and(File::missing($this->fileManagerPath.'/archive/demo-moved.txt'))->toBeTrue();
});

test('file manager operations are hidden and blocked by disabled files policy', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileFileManagerPolicyBootstrapEnvelope([
        'native_files' => mobileFileManagerPolicyFeature(
            enabled: false,
            state: 'disabled',
            message: 'File access is disabled by admin policy.',
        ),
    ]));

    Livewire::test(FileManager::class)
        ->assertSee('File manager disabled')
        ->assertSee('File actions disabled')
        ->assertSee('File import disabled')
        ->assertSee('Local files disabled')
        ->assertDontSee('wire:click="copyCurrentFile"', false)
        ->assertDontSee('wire:click="moveCurrentFile"', false)
        ->assertDontSee('wire:click="refreshFiles"', false)
        ->set('filePath', 'notes/blocked.txt')
        ->set('fileContents', 'Blocked file contents')
        ->call('writeCurrentFile')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'File write unavailable'
                && ($params['message'] ?? null) === 'File access is disabled by admin policy.';
        })
        ->call('deleteFile', 'notes/blocked.txt')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'File delete unavailable';
        });

    expect(File::missing($this->fileManagerPath.'/notes/blocked.txt'))->toBeTrue();
});

test('file manager share action is hidden and blocked by disabled share policy', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileFileManagerPolicyBootstrapEnvelope([
        'native_files' => mobileFileManagerPolicyFeature(enabled: true, state: 'visible'),
        'native_share' => mobileFileManagerPolicyFeature(
            enabled: false,
            state: 'disabled',
            message: 'Native sharing is disabled by admin policy.',
        ),
    ]));

    Livewire::test(FileManager::class)
        ->set('filePath', 'notes/share.txt')
        ->set('fileContents', 'Share policy file')
        ->call('writeCurrentFile')
        ->assertSee('share.txt')
        ->assertDontSee('wire:click="shareFile', false)
        ->call('shareFile', 'notes/share.txt')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'File share unavailable'
                && ($params['message'] ?? null) === 'Native sharing is disabled by admin policy.';
        });

    expect(File::exists($this->fileManagerPath.'/notes/share.txt'))->toBeTrue();
});

test('file manager imports uploaded files into the local sandbox', function (): void {
    Livewire::test(FileManager::class)
        ->set('importDirectory', 'docs')
        ->set('importUpload', UploadedFile::fake()->create('Import Note.txt', 1, 'text/plain'))
        ->call('importFile')
        ->assertSet('filePath', 'docs/import-note.txt')
        ->assertSee('File imported locally.')
        ->assertSee('import-note.txt');

    expect(File::exists($this->fileManagerPath.'/docs/import-note.txt'))->toBeTrue();
});

/**
 * @param  array<string, array<string, mixed>>  $features
 * @return array<string, mixed>
 */
function mobileFileManagerPolicyBootstrapEnvelope(array $features = []): array
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
                'version' => 'file-policy',
                'items' => array_replace([
                    'native_files' => mobileFileManagerPolicyFeature(enabled: true, state: 'visible'),
                    'native_share' => mobileFileManagerPolicyFeature(enabled: true, state: 'visible'),
                ], $features),
            ],
            'remote_config' => ['version' => 'file-policy', 'values' => []],
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
            'bootstrap_version' => 'file-policy',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileFileManagerPolicyFeature(bool $enabled, string $state, ?string $message = null): array
{
    return [
        'state' => $state,
        'visible' => $state !== 'hidden',
        'enabled' => $enabled,
        'reason' => $enabled ? null : 'feature_disabled_by_admin',
        'message' => $message,
        'next_action' => $enabled ? null : 'contact_admin',
        'source' => 'test_policy',
    ];
}
