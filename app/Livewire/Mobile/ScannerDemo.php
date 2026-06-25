<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Services\Native\ScannerService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Scanner\CodeScanned;
use Native\Mobile\Events\Scanner\ScannerCancelled;

#[Title('QR/barcode scanner')]
class ScannerDemo extends Component
{
    use DispatchesToasts;

    public ?string $pendingScanId = null;

    public ?string $pendingScanMode = null;

    public ?string $scanStatus = null;

    public ?string $scanError = null;

    public string $selectedFormat = 'qr';

    public string $prompt = 'Scan QR code or barcode';

    public ?string $latestData = null;

    public ?string $latestFormat = null;

    public ?string $latestScannedAt = null;

    /**
     * @var list<array{key: string, data: string, format: string, scanned_at: string, id: string|null}>
     */
    public array $scanHistory = [];

    private ScannerService $scanners;

    public function boot(ScannerService $scanners): void
    {
        $this->scanners = $scanners;
    }

    public function startSingleScan(): void
    {
        $this->startNativeScan(
            mode: 'single',
            launcher: fn (string $id, array $formats, string $prompt): array => $this->scanners->singleScan($id, $formats, $prompt),
        );
    }

    public function startContinuousScan(): void
    {
        $this->startNativeScan(
            mode: 'continuous',
            launcher: fn (string $id, array $formats, string $prompt): array => $this->scanners->continuousScan($id, $formats, $prompt),
        );
    }

    public function stopContinuousScan(): void
    {
        if ($this->pendingScanMode !== 'continuous') {
            return;
        }

        $this->pendingScanId = null;
        $this->pendingScanMode = null;
        $this->scanStatus = 'Continuous scan tracking stopped in the app.';
        $this->scanError = null;
        $this->toastInfo($this->scanStatus, 'Scanner closed');
    }

    public function clearLatestResult(): void
    {
        $this->latestData = null;
        $this->latestFormat = null;
        $this->latestScannedAt = null;
        $this->scanStatus = 'Latest scan result cleared.';
        $this->scanError = null;
        $this->toastInfo($this->scanStatus, 'Scanner cleared');
    }

    public function clearHistory(): void
    {
        $this->scanHistory = [];
        $this->scanStatus = 'Scan history cleared for this session.';
        $this->scanError = null;
        $this->toastInfo($this->scanStatus, 'History cleared');
    }

    #[OnNative(CodeScanned::class)]
    public function handleCodeScanned(string $data, string $format, ?string $id = null): void
    {
        if (! $this->matchesPendingScan($id)) {
            return;
        }

        $scanResult = $this->scanners->normalizeScanResult($data, $format, $id);

        $this->latestData = $scanResult['data'];
        $this->latestFormat = $scanResult['format'];
        $this->latestScannedAt = $scanResult['scanned_at'];
        $this->scanError = null;

        array_unshift($this->scanHistory, [
            'key' => Str::uuid()->toString(),
            ...$scanResult,
        ]);

        $this->scanHistory = array_slice($this->scanHistory, 0, 20);

        if ($this->pendingScanMode === 'continuous') {
            $this->scanStatus = 'Scan captured. Continuous scanner is still listening.';
            $this->toastSuccess('Scan captured from continuous scanner.', 'Code scanned');

            return;
        }

        $this->pendingScanId = null;
        $this->pendingScanMode = null;
        $this->scanStatus = 'Scan captured.';
        $this->toastSuccess($this->scanStatus, 'Code scanned');
    }

    #[OnNative(ScannerCancelled::class)]
    public function handleScannerCancelled(bool $cancelled = true, ?string $reason = null, ?string $id = null): void
    {
        if (! $cancelled || ! $this->matchesPendingScan($id)) {
            return;
        }

        $cancellation = $this->scanners->normalizeCancellation($cancelled, $reason, $id);
        $message = $cancellation['reason']
            ? "Scanner closed: {$cancellation['reason']}."
            : 'Scanner closed before a code was scanned.';

        $this->pendingScanId = null;
        $this->pendingScanMode = null;

        if (Str::of((string) $cancellation['reason'])->lower()->contains(['permission', 'denied'])) {
            $this->scanStatus = null;
            $this->scanError = $message;
            $this->toastError($message, 'Scanner unavailable');

            return;
        }

        $this->scanStatus = $message;
        $this->scanError = null;
        $this->toastInfo($message, 'Scanner closed');
    }

    public function render(): View
    {
        return view('livewire.mobile.scanner-demo', [
            'formatSelectOptions' => $this->formatSelectOptions(),
            'formatOptions' => $this->scanners->formats(),
            'nativeScannerAvailable' => $this->scanners->isAvailable(),
            'scannerCapabilities' => $this->scanners->capabilities(),
            'scannerActions' => $this->scannerActions(),
        ]);
    }

    /**
     * @param  callable(string, list<string>, string): array{success: bool, operation: string, id: string, message: string}  $launcher
     */
    private function startNativeScan(string $mode, callable $launcher): void
    {
        $format = $this->normalizedSelectedFormat();

        if ($format === null) {
            $this->scanStatus = null;
            $this->scanError = 'Choose a supported barcode format before scanning.';
            $this->toastWarning($this->scanError, 'Scanner format');

            return;
        }

        $this->scanStatus = null;
        $this->scanError = null;

        $id = "scanner-{$mode}-".Str::uuid()->toString();
        $this->pendingScanId = $id;
        $this->pendingScanMode = $mode;

        $result = $launcher($id, [$format], $this->scanPrompt());

        if ($result['success']) {
            $this->scanStatus = $result['message'];
            $this->toastInfo($this->scanStatus, 'Native scanner opened');

            return;
        }

        $this->pendingScanId = null;
        $this->pendingScanMode = null;
        $this->scanError = $result['message'];
        $this->toastWarning($this->scanError, 'Native scanner unavailable');
    }

    private function matchesPendingScan(?string $id): bool
    {
        return is_string($id)
            && is_string($this->pendingScanId)
            && hash_equals($this->pendingScanId, $id);
    }

    private function normalizedSelectedFormat(): ?string
    {
        $formats = $this->scanners->normalizeFormats([$this->selectedFormat]);

        return $formats[0] ?? null;
    }

    private function scanPrompt(): string
    {
        $prompt = trim($this->prompt);

        return $prompt !== '' ? $prompt : 'Scan QR code or barcode';
    }

    /**
     * @return array<string, string>
     */
    private function formatSelectOptions(): array
    {
        $options = [];

        foreach ($this->scanners->formats() as $format) {
            $options[$format['value']] = $format['label'];
        }

        return $options;
    }

    /**
     * @return list<array{label: string, action: string, variant: string, description: string}>
     */
    private function scannerActions(): array
    {
        return [
            [
                'label' => 'Single scan',
                'action' => 'startSingleScan',
                'variant' => 'primary',
                'description' => 'Open the native scanner for one QR code or barcode.',
            ],
            [
                'label' => 'Continuous scan',
                'action' => 'startContinuousScan',
                'variant' => 'accent',
                'description' => 'Keep listening for multiple scan events when supported.',
            ],
        ];
    }
}
