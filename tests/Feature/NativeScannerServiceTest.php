<?php

use App\Services\Native\ScannerService;
use Native\Mobile\PendingScanner;
use Native\Mobile\Scanner;

test('native scanner service reports browser fallback when native runtime is inactive', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        $service = new ScannerService(new NativeScannerServiceFakeScanner);

        expect($service->isAvailable())->toBeFalse()
            ->and($service->singleScan('single-id'))->toMatchArray([
                'success' => false,
                'operation' => 'single_scan',
                'id' => 'single-id',
                'message' => 'Native scanner is unavailable in this browser runtime.',
            ])
            ->and($service->continuousScan('continuous-id'))->toMatchArray([
                'success' => false,
                'operation' => 'continuous_scan',
                'id' => 'continuous-id',
                'message' => 'Native continuous scanner is unavailable in this browser runtime.',
            ])
            ->and($service->supportsContinuousScan())->toBeTrue()
            ->and($service->capabilities())->toHaveCount(2)
            ->and($service->formats())->toHaveCount(8);
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('native scanner service starts single and continuous scanner operations', function (): void {
    config(['nativephp-internal.running' => true]);
    NativeScannerServiceFakeScanner::reset();

    $service = new ScannerService(new NativeScannerServiceFakeScanner);

    expect($service->singleScan('single-id', ['EAN13', 'missing'], 'Find product code'))->toMatchArray([
        'success' => true,
        'operation' => 'single_scan',
        'id' => 'single-id',
        'message' => 'Native scanner opened for a single scan.',
    ]);

    $singlePending = NativeScannerServiceFakeScanner::$pending;

    expect($singlePending?->recordedId)->toBe('single-id')
        ->and($singlePending?->recordedPrompt)->toBe('Find product code')
        ->and($singlePending?->recordedContinuous)->toBeFalse()
        ->and($singlePending?->recordedFormats)->toBe(['ean13'])
        ->and($singlePending?->started)->toBeTrue();

    expect($service->continuousScan('continuous-id', ['all', 'qr']))->toMatchArray([
        'success' => true,
        'operation' => 'continuous_scan',
        'id' => 'continuous-id',
        'message' => 'Native scanner opened for continuous scanning.',
    ]);

    $continuousPending = NativeScannerServiceFakeScanner::$pending;

    expect($continuousPending?->recordedId)->toBe('continuous-id')
        ->and($continuousPending?->recordedPrompt)->toBe('Scan QR codes or barcodes')
        ->and($continuousPending?->recordedContinuous)->toBeTrue()
        ->and($continuousPending?->recordedFormats)->toBe(['all'])
        ->and($continuousPending?->started)->toBeTrue();
});

test('native scanner service normalizes formats results and cancellations', function (): void {
    $service = new ScannerService(new NativeScannerServiceFakeScanner);

    expect($service->normalizeFormats(['CODE128', 'qr', 'invalid']))->toBe(['code128', 'qr'])
        ->and($service->normalizeFormats(['invalid']))->toBe(['qr'])
        ->and($service->normalizeScanResult('ABC-123', 'QR', 'scan-id'))->toMatchArray([
            'data' => 'ABC-123',
            'format' => 'qr',
            'id' => 'scan-id',
        ])
        ->and($service->normalizeCancellation(true, 'permission denied', 'scan-id'))->toMatchArray([
            'cancelled' => true,
            'reason' => 'permission denied',
            'id' => 'scan-id',
        ]);
});

test('native scanner service reports failed launches', function (): void {
    config(['nativephp-internal.running' => true]);
    NativeScannerServiceFakeScanner::reset(shouldThrow: true);

    $service = new ScannerService(new NativeScannerServiceFakeScanner);

    expect($service->continuousScan('continuous-id'))->toMatchArray([
        'success' => false,
        'operation' => 'continuous_scan',
        'id' => 'continuous-id',
        'message' => 'Unable to open the native scanner for continuous scanning.',
    ]);
});

final class NativeScannerServiceFakeScanner extends Scanner
{
    public static ?NativeScannerServiceFakePendingScanner $pending = null;

    private static bool $throws = false;

    public static function reset(bool $shouldThrow = false): void
    {
        self::$pending = null;
        self::$throws = $shouldThrow;
    }

    public static function scan(): PendingScanner
    {
        self::$pending = new NativeScannerServiceFakePendingScanner(self::$throws);

        return self::$pending;
    }
}

final class NativeScannerServiceFakePendingScanner extends PendingScanner
{
    public ?string $recordedPrompt = null;

    public bool $recordedContinuous = false;

    /**
     * @var list<string>
     */
    public array $recordedFormats = [];

    public ?string $recordedId = null;

    public bool $started = false;

    public function __construct(
        private readonly bool $throws = false,
    ) {}

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
        if ($this->throws) {
            throw new RuntimeException('Scanner launch failed.');
        }

        $this->started = true;
    }

    public function __destruct() {}
}
