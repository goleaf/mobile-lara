<?php

use App\Models\MobileLocalHealthCheck;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\MobileLocalStorageHealthCheck;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-health.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.health.key' => 'testing-nativephp-mobile-local-storage',
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('mobile local sqlite connection and migration path are configured', function (): void {
    expect(config('mobile_local.connection'))->toBe('mobile_local')
        ->and(config('database.connections.mobile_local.driver'))->toBe('sqlite')
        ->and(config('database.connections.mobile_local.database'))->toBe($this->mobileLocalDatabasePath)
        ->and(config('mobile_local.migrations.path'))->toBe(database_path('migrations/mobile-local'))
        ->and(File::exists($this->mobileLocalDatabasePath))->toBeTrue();
});

test('mobile local health check writes and reads sqlite data', function (): void {
    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    $report = app(MobileLocalStorageHealthCheck::class)->run('local-storage-probe');

    expect($report->ok)->toBeTrue()
        ->and($report->connection)->toBe('mobile_local')
        ->and($report->databasePath)->toBe($this->mobileLocalDatabasePath)
        ->and(MobileLocalHealthCheck::query()
            ->where('check_key', 'testing-nativephp-mobile-local-storage')
            ->value('check_value'))->toBe('local-storage-probe');
});

test('mobile local health command reports success after migrations run', function (): void {
    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    $this->artisan('mobile:local-health')
        ->assertExitCode(0);
});
