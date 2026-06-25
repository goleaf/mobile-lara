<?php

use App\Livewire\Mobile\Settings\Storage;
use App\Models\MobileLocalSetting;
use App\Services\MobileLocal\MobileLocalDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-storage-settings.sqlite');
    $this->fileCachePath = storage_path('framework/testing/mobile-file-cache');
    $this->exportPath = storage_path('framework/testing/mobile-local-export.json');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));
    File::ensureDirectoryExists($this->fileCachePath);

    foreach ([
        $this->mobileLocalDatabasePath,
        "{$this->mobileLocalDatabasePath}-wal",
        "{$this->mobileLocalDatabasePath}-shm",
        "{$this->mobileLocalDatabasePath}-journal",
        $this->exportPath,
    ] as $path) {
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    File::cleanDirectory($this->fileCachePath);

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_local.storage.file_cache_path' => $this->fileCachePath,
        'mobile_local.storage.export_path' => $this->exportPath,
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();
});

afterEach(function (): void {
    DB::purge('mobile_local');

    foreach ([
        $this->mobileLocalDatabasePath,
        "{$this->mobileLocalDatabasePath}-wal",
        "{$this->mobileLocalDatabasePath}-shm",
        "{$this->mobileLocalDatabasePath}-journal",
        $this->exportPath,
    ] as $path) {
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    if (File::isDirectory($this->fileCachePath)) {
        File::deleteDirectory($this->fileCachePath);
    }
});

test('storage settings screen renders storage placeholders and actions', function (): void {
    File::put($this->fileCachePath.'/cache-item.txt', str_repeat('x', 1024));

    Livewire::test(Storage::class)
        ->assertSee('Storage settings')
        ->assertSee('Storage overview')
        ->assertSee('Local database size')
        ->assertSee('File cache size')
        ->assertSee('Export destination')
        ->assertSee('Clear cache')
        ->assertSee('Reset local data')
        ->assertSee('Export local data');
});

test('clear cache requires confirmation before deleting local cache files', function (): void {
    File::ensureDirectoryExists($this->fileCachePath.'/nested');
    File::put($this->fileCachePath.'/.gitignore', '*');
    File::put($this->fileCachePath.'/nested/cache-item.txt', 'cached payload');

    $component = Livewire::test(Storage::class)
        ->call('clearCache')
        ->assertSet('confirmingClearCache', true)
        ->assertSee('Clear file cache?');

    expect(File::exists($this->fileCachePath.'/nested/cache-item.txt'))->toBeTrue();

    $component
        ->call('clearCache')
        ->assertSet('confirmingClearCache', false)
        ->assertSet('statusMessage', 'File cache cleared.');

    expect(File::exists($this->fileCachePath.'/nested/cache-item.txt'))->toBeFalse()
        ->and(File::exists($this->fileCachePath.'/.gitignore'))->toBeTrue()
        ->and(File::isDirectory($this->fileCachePath))->toBeTrue();
});

test('reset local data requires confirmation and recreates the local schema', function (): void {
    migrateMobileLocalDatabase();

    MobileLocalSetting::query()->create([
        'settings_key' => 'default',
        'theme' => 'dark',
        'language' => 'en',
    ]);

    expect(MobileLocalSetting::query()->count())->toBe(1);

    $component = Livewire::test(Storage::class)
        ->call('resetLocalData')
        ->assertSet('confirmingResetLocalData', true)
        ->assertSee('Reset local data?');

    expect(MobileLocalSetting::query()->count())->toBe(1);

    $component
        ->call('resetLocalData')
        ->assertSet('confirmingResetLocalData', false)
        ->assertSet('statusMessage', 'Local data reset. The mobile database schema was recreated.');

    expect(File::exists($this->mobileLocalDatabasePath))->toBeTrue()
        ->and(MobileLocalSetting::query()->count())->toBe(0);
});

test('export local data action shows a placeholder destination', function (): void {
    Livewire::test(Storage::class)
        ->call('exportLocalData')
        ->assertSet('statusMessage', "Export local data placeholder prepared at {$this->exportPath}.")
        ->assertSee('Export local data placeholder prepared');
});

function migrateMobileLocalDatabase(): void
{
    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
}
