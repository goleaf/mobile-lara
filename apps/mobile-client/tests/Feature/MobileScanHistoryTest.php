<?php

use App\Livewire\Mobile\ScanHistory;
use App\Models\MobileLocalScanHistory;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-scan-history.sqlite');

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

test('scan history page renders filters search and saved rows', function (): void {
    MobileLocalScanHistory::factory()->create([
        'raw_value' => 'https://example.test/orders/123',
        'parsed_value' => [
            'type' => 'url',
            'value' => 'https://example.test/orders/123',
            'summary' => 'example.test',
        ],
        'created_at' => CarbonImmutable::now(),
    ]);

    MobileLocalScanHistory::factory()->barcode()->actioned()->create([
        'raw_value' => '9780201379624',
        'created_at' => CarbonImmutable::now()->subMinute(),
    ]);

    MobileLocalScanHistory::factory()->barcode()->failed()->create([
        'scan_type' => 'code128',
        'raw_value' => 'FAILED-CODE-128',
        'created_at' => CarbonImmutable::now()->subMinutes(2),
    ]);

    Livewire::test(ScanHistory::class)
        ->assertSee('Scan history')
        ->assertSee('History summary')
        ->assertSee('3 shown')
        ->assertSee('https://example.test/orders/123')
        ->assertSee('9780201379624')
        ->assertSee('FAILED-CODE-128')
        ->call('setFilter', 'barcodes')
        ->assertSet('filter', 'barcodes')
        ->assertSee('2 shown')
        ->assertDontSee('https://example.test/orders/123')
        ->set('search', 'FAILED')
        ->assertSee('1 shown')
        ->assertSee('FAILED-CODE-128')
        ->assertDontSee('9780201379624')
        ->call('setFilter', 'unknown')
        ->assertSet('filter', 'all')
        ->call('clearSearch')
        ->assertSet('search', '')
        ->assertSee('https://example.test/orders/123');
});

test('scan history page deletes one row and clears the current filtered rows', function (): void {
    $qr = MobileLocalScanHistory::factory()->create([
        'raw_value' => 'https://example.test/keep',
    ]);

    $failed = MobileLocalScanHistory::factory()->failed()->create([
        'raw_value' => 'FAILED-CODE-128',
    ]);

    $ignored = MobileLocalScanHistory::factory()->ignored()->create([
        'raw_value' => 'IGNORED-CODE-39',
    ]);

    Livewire::test(ScanHistory::class)
        ->call('deleteScan', $ignored->id)
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Scan deleted';
        })
        ->call('setFilter', 'failed')
        ->assertSee('FAILED-CODE-128')
        ->call('clearHistory')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Scan history cleared';
        });

    expect(MobileLocalScanHistory::query()->whereKey($ignored->id)->exists())->toBeFalse()
        ->and(MobileLocalScanHistory::query()->whereKey($failed->id)->exists())->toBeFalse()
        ->and(MobileLocalScanHistory::query()->whereKey($qr->id)->exists())->toBeTrue();
});

test('scan history page renders empty state without local rows', function (): void {
    Livewire::test(ScanHistory::class)
        ->assertSee('No saved scans')
        ->assertSee('0 shown');
});
