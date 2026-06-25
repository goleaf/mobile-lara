<?php

namespace App\Services\Native;

use Native\Mobile\PendingScanner;
use Native\Mobile\Scanner;
use Throwable;

final class ScannerService
{
    /**
     * @var list<array{key: string, label: string, description: string, supported: bool}>
     */
    private const CAPABILITIES = [
        [
            'key' => 'single-scan',
            'label' => 'Single scan',
            'description' => 'Scan one QR code or barcode and return the first result.',
            'supported' => true,
        ],
        [
            'key' => 'continuous-scan',
            'label' => 'Continuous scan',
            'description' => 'Keep the native scanner open for multiple scan events when the platform supports it.',
            'supported' => true,
        ],
    ];

    /**
     * @var list<array{value: string, label: string, description: string}>
     */
    private const FORMATS = [
        [
            'value' => 'qr',
            'label' => 'QR code',
            'description' => 'Standard QR codes.',
        ],
        [
            'value' => 'ean13',
            'label' => 'EAN-13',
            'description' => 'Retail product barcodes with 13 digits.',
        ],
        [
            'value' => 'ean8',
            'label' => 'EAN-8',
            'description' => 'Compact retail product barcodes.',
        ],
        [
            'value' => 'code128',
            'label' => 'Code 128',
            'description' => 'High-density alphanumeric barcodes.',
        ],
        [
            'value' => 'code39',
            'label' => 'Code 39',
            'description' => 'Industrial alphanumeric barcodes.',
        ],
        [
            'value' => 'upca',
            'label' => 'UPC-A',
            'description' => 'Retail product barcodes used in North America.',
        ],
        [
            'value' => 'upce',
            'label' => 'UPC-E',
            'description' => 'Compressed UPC barcodes.',
        ],
        [
            'value' => 'all',
            'label' => 'All formats',
            'description' => 'Let the native scanner detect every supported format.',
        ],
    ];

    public function __construct(
        private readonly Scanner $scanner,
    ) {}

    public function isAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    /**
     * @param  list<string>  $formats
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function singleScan(string $id, array $formats = ['qr'], ?string $prompt = null): array
    {
        return $this->startScan(
            operation: 'single_scan',
            id: $id,
            formats: $formats,
            prompt: $prompt,
            continuous: false,
            unavailableMessage: 'Native scanner is unavailable in this browser runtime.',
            startedMessage: 'Native scanner opened for a single scan.',
            failedMessage: 'Unable to open the native scanner.',
        );
    }

    /**
     * @param  list<string>  $formats
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function continuousScan(string $id, array $formats = ['qr'], ?string $prompt = null): array
    {
        return $this->startScan(
            operation: 'continuous_scan',
            id: $id,
            formats: $formats,
            prompt: $prompt,
            continuous: true,
            unavailableMessage: 'Native continuous scanner is unavailable in this browser runtime.',
            startedMessage: 'Native scanner opened for continuous scanning.',
            failedMessage: 'Unable to open the native scanner for continuous scanning.',
        );
    }

    public function supportsContinuousScan(): bool
    {
        return true;
    }

    /**
     * @return list<array{key: string, label: string, description: string, supported: bool}>
     */
    public function capabilities(): array
    {
        return array_map(
            fn (array $capability): array => [
                ...$capability,
                'supported' => $capability['key'] === 'continuous-scan'
                    ? $this->supportsContinuousScan()
                    : (bool) $capability['supported'],
            ],
            self::CAPABILITIES,
        );
    }

    /**
     * @return list<array{value: string, label: string, description: string}>
     */
    public function formats(): array
    {
        return self::FORMATS;
    }

    /**
     * @param  list<string>  $formats
     * @return list<string>
     */
    public function normalizeFormats(array $formats): array
    {
        $allowedFormats = array_column(self::FORMATS, 'value');

        $normalized = array_values(array_unique(array_filter(
            array_map(
                fn (string $format): string => strtolower(trim($format)),
                $formats,
            ),
            fn (string $format): bool => in_array($format, $allowedFormats, true),
        )));

        if (in_array('all', $normalized, true)) {
            return ['all'];
        }

        return $normalized === [] ? ['qr'] : $normalized;
    }

    /**
     * @return array{data: string, format: string, id: string|null, scanned_at: string}
     */
    public function normalizeScanResult(string $data, string $format, ?string $id = null): array
    {
        return [
            'data' => $data,
            'format' => strtolower(trim($format)) ?: 'unknown',
            'id' => $id,
            'scanned_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @return array{cancelled: bool, reason: string|null, id: string|null, cancelled_at: string}
     */
    public function normalizeCancellation(bool $cancelled = true, ?string $reason = null, ?string $id = null): array
    {
        return [
            'cancelled' => $cancelled,
            'reason' => $reason,
            'id' => $id,
            'cancelled_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @param  list<string>  $formats
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    private function startScan(
        string $operation,
        string $id,
        array $formats,
        ?string $prompt,
        bool $continuous,
        string $unavailableMessage,
        string $startedMessage,
        string $failedMessage,
    ): array {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'operation' => $operation,
                'id' => $id,
                'message' => $unavailableMessage,
            ];
        }

        try {
            $this->pendingScanner()
                ->prompt($this->scannerPrompt($prompt, $continuous))
                ->continuous($continuous)
                ->formats($this->normalizeFormats($formats))
                ->id($id)
                ->scan();

            $started = true;
        } catch (Throwable) {
            $started = false;
        }

        return [
            'success' => $started,
            'operation' => $operation,
            'id' => $id,
            'message' => $started ? $startedMessage : $failedMessage,
        ];
    }

    private function pendingScanner(): PendingScanner
    {
        return $this->scanner::scan();
    }

    private function scannerPrompt(?string $prompt, bool $continuous): string
    {
        $cleanPrompt = trim((string) $prompt);

        if ($cleanPrompt !== '') {
            return $cleanPrompt;
        }

        return $continuous ? 'Scan QR codes or barcodes' : 'Scan QR code or barcode';
    }
}
