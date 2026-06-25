<?php

use App\Livewire\Mobile\ScannerDemo;
use App\Services\Native\ScannerService;
use Livewire\Livewire;
use Native\Mobile\PendingScanner;
use Native\Mobile\Scanner;

test('scanner screen renders scanner setup actions capabilities and empty states', function (): void {
    Livewire::test(ScannerDemo::class)
        ->assertSee('QR/barcode scanner')
        ->assertSee('Scanner bridge')
        ->assertSee('Browser fallback active')
        ->assertSee('Scan setup')
        ->assertSee('Single scan')
        ->assertSee('Continuous scan')
        ->assertSee('Latest result')
        ->assertSee('No scan result')
        ->assertSee('Scan history')
        ->assertSee('Capabilities')
        ->assertSee('QR code')
        ->assertSee('Code 128');
});

test('scanner actions report browser fallback state', function (string $action, string $message): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        Livewire::test(ScannerDemo::class)
            ->call($action)
            ->assertSet('pendingScanId', null)
            ->assertSet('pendingScanMode', null)
            ->assertSet('scanError', $message)
            ->assertSee($message)
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'warning'
                    && ($params['title'] ?? null) === 'Native scanner unavailable';
            });
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
})->with([
    'single scan' => ['startSingleScan', 'Native scanner is unavailable in this browser runtime.'],
    'continuous scan' => ['startContinuousScan', 'Native continuous scanner is unavailable in this browser runtime.'],
]);

test('scanner screen starts a native scan when available', function (): void {
    config(['nativephp-internal.running' => true]);
    MobileScannerScreenFakeScanner::reset();

    $this->app->instance(ScannerService::class, new ScannerService(new MobileScannerScreenFakeScanner));

    Livewire::test(ScannerDemo::class)
        ->set('selectedFormat', 'code128')
        ->set('prompt', 'Scan package barcode')
        ->call('startSingleScan')
        ->assertSet('pendingScanMode', 'single')
        ->assertSet('pendingScanId', fn (mixed $id): bool => is_string($id) && str_starts_with($id, 'scanner-single-'))
        ->assertSet('scanStatus', 'Native scanner opened for a single scan.')
        ->assertSee('Native scanner opened for a single scan.');

    expect(MobileScannerScreenFakeScanner::$pending?->recordedPrompt)->toBe('Scan package barcode')
        ->and(MobileScannerScreenFakeScanner::$pending?->recordedContinuous)->toBeFalse()
        ->and(MobileScannerScreenFakeScanner::$pending?->recordedFormats)->toBe(['code128'])
        ->and(MobileScannerScreenFakeScanner::$pending?->started)->toBeTrue();
});

test('scanner screen records single and continuous scan results', function (): void {
    Livewire::test(ScannerDemo::class)
        ->set('pendingScanId', 'single-scan-id')
        ->set('pendingScanMode', 'single')
        ->call('handleCodeScanned', 'https://example.test/item/123', 'QR', 'single-scan-id')
        ->assertSet('pendingScanId', null)
        ->assertSet('pendingScanMode', null)
        ->assertSet('latestData', 'https://example.test/item/123')
        ->assertSet('latestFormat', 'qr')
        ->assertSet('scanStatus', 'Scan captured.')
        ->assertSet('scanHistory', fn (array $history): bool => count($history) === 1
            && $history[0]['data'] === 'https://example.test/item/123'
            && $history[0]['format'] === 'qr')
        ->assertSee('https://example.test/item/123')
        ->set('pendingScanId', 'continuous-scan-id')
        ->set('pendingScanMode', 'continuous')
        ->call('handleCodeScanned', 'ABC-123', 'ean13', 'continuous-scan-id')
        ->assertSet('pendingScanId', 'continuous-scan-id')
        ->assertSet('pendingScanMode', 'continuous')
        ->assertSet('latestData', 'ABC-123')
        ->assertSet('latestFormat', 'ean13')
        ->assertSet('scanStatus', 'Scan captured. Continuous scanner is still listening.')
        ->assertSet('scanHistory', fn (array $history): bool => count($history) === 2
            && $history[0]['data'] === 'ABC-123'
            && $history[0]['format'] === 'ean13');
});

test('scanner screen handles cancellation stop and clear actions', function (): void {
    Livewire::test(ScannerDemo::class)
        ->set('pendingScanId', 'cancel-scan-id')
        ->set('pendingScanMode', 'single')
        ->call('handleScannerCancelled', true, null, 'cancel-scan-id')
        ->assertSet('pendingScanId', null)
        ->assertSet('pendingScanMode', null)
        ->assertSet('scanStatus', 'Scanner closed before a code was scanned.')
        ->set('pendingScanId', 'permission-scan-id')
        ->set('pendingScanMode', 'single')
        ->call('handleScannerCancelled', true, 'permission denied', 'permission-scan-id')
        ->assertSet('pendingScanId', null)
        ->assertSet('scanError', 'Scanner closed: permission denied.')
        ->set('pendingScanId', 'continuous-scan-id')
        ->set('pendingScanMode', 'continuous')
        ->call('stopContinuousScan')
        ->assertSet('pendingScanId', null)
        ->assertSet('pendingScanMode', null)
        ->assertSet('scanStatus', 'Continuous scan tracking stopped in the app.')
        ->set('latestData', 'ABC-123')
        ->set('latestFormat', 'qr')
        ->set('latestScannedAt', '2026-06-25T12:00:00+00:00')
        ->call('clearLatestResult')
        ->assertSet('latestData', null)
        ->assertSet('latestFormat', null)
        ->assertSet('latestScannedAt', null)
        ->set('scanHistory', [[
            'key' => 'history-item',
            'data' => 'ABC-123',
            'format' => 'qr',
            'scanned_at' => '2026-06-25T12:00:00+00:00',
            'id' => 'scan-id',
        ]])
        ->call('clearHistory')
        ->assertSet('scanHistory', []);
});

final class MobileScannerScreenFakeScanner extends Scanner
{
    public static ?MobileScannerScreenFakePendingScanner $pending = null;

    public static function reset(): void
    {
        self::$pending = null;
    }

    public static function scan(): PendingScanner
    {
        self::$pending = new MobileScannerScreenFakePendingScanner;

        return self::$pending;
    }
}

final class MobileScannerScreenFakePendingScanner extends PendingScanner
{
    public ?string $recordedPrompt = null;

    public bool $recordedContinuous = false;

    /**
     * @var list<string>
     */
    public array $recordedFormats = [];

    public ?string $recordedId = null;

    public bool $started = false;

    public function prompt(string $prompt): self
    {
        $this->recordedPrompt = $prompt;

        return $this;
    }

    public function continuous(bool $continuous = true): self
    {
        $this->recordedContinuous = $continuous;

        return $this;
    }

    public function formats(array $formats): self
    {
        $this->recordedFormats = array_values($formats);

        return $this;
    }

    public function id(string $id): self
    {
        $this->recordedId = $id;

        return $this;
    }

    public function scan(): void
    {
        $this->started = true;
    }

    public function __destruct() {}
}
