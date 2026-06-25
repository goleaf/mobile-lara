<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalScanHistory;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class ScanHistoryRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    public function record(
        string $scanType,
        string $rawValue,
        ?array $parsedValue = null,
        ?string $actionResult = null,
        string $status = MobileLocalScanHistory::STATUS_CAPTURED,
        ?CarbonInterface $createdAt = null,
    ): MobileLocalScanHistory {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalScanHistory::query()->create([
            'scan_type' => $this->normalizeScanType($scanType),
            'raw_value' => $rawValue,
            'parsed_value' => $parsedValue ?: $this->parseValue($rawValue),
            'action_result' => $actionResult,
            'status' => $this->validStatus($status),
            'created_at' => $createdAt ?: CarbonImmutable::now(),
        ]);
    }

    /**
     * @return Collection<int, MobileLocalScanHistory>
     */
    public function recent(
        int $limit = 30,
        ?string $scanType = null,
        ?string $status = null,
        ?string $search = null,
        bool $barcodesOnly = false,
    ): Collection {
        $this->mobileLocalDatabase->ensureFileExists();

        return $this->filteredQuery($scanType, $status, $search, $barcodesOnly)
            ->historyOrder()
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return array{total: int, qr: int, barcodes: int, captured: int, actioned: int, failed: int, ignored: int}
     */
    public function counts(): array
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return [
            'total' => MobileLocalScanHistory::query()->count(),
            'qr' => MobileLocalScanHistory::query()->qrCodes()->count(),
            'barcodes' => MobileLocalScanHistory::query()->barcodes()->count(),
            'captured' => MobileLocalScanHistory::query()->forStatus(MobileLocalScanHistory::STATUS_CAPTURED)->count(),
            'actioned' => MobileLocalScanHistory::query()->forStatus(MobileLocalScanHistory::STATUS_ACTIONED)->count(),
            'failed' => MobileLocalScanHistory::query()->forStatus(MobileLocalScanHistory::STATUS_FAILED)->count(),
            'ignored' => MobileLocalScanHistory::query()->forStatus(MobileLocalScanHistory::STATUS_IGNORED)->count(),
        ];
    }

    public function delete(int|string $scanHistoryId): bool
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $scanHistory = MobileLocalScanHistory::query()
            ->select(MobileLocalScanHistory::SELECT_COLUMNS)
            ->whereKey($scanHistoryId)
            ->first();

        if ($scanHistory === null) {
            return false;
        }

        return (bool) $scanHistory->delete();
    }

    public function clear(?string $scanType = null, ?string $status = null, ?string $search = null, bool $barcodesOnly = false): int
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $query = $this->filteredQuery($scanType, $status, $search, $barcodesOnly);
        $count = (clone $query)->count();

        $query->delete();

        return $count;
    }

    /**
     * @return array{type: string, value: mixed, summary: string}
     */
    public function parseValue(string $rawValue): array
    {
        $value = trim($rawValue);

        if ($value === '') {
            return [
                'type' => 'empty',
                'value' => '',
                'summary' => 'Empty scan value',
            ];
        }

        $decodedJson = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedJson)) {
            return [
                'type' => 'json',
                'value' => $decodedJson,
                'summary' => 'JSON payload',
            ];
        }

        if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
            return [
                'type' => 'url',
                'value' => $value,
                'summary' => (string) (parse_url($value, PHP_URL_HOST) ?: $value),
            ];
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL) !== false) {
            return [
                'type' => 'email',
                'value' => Str::of($value)->lower()->toString(),
                'summary' => 'Email address',
            ];
        }

        if (is_numeric($value)) {
            return [
                'type' => 'number',
                'value' => $value,
                'summary' => 'Numeric value',
            ];
        }

        if (preg_match('/^\+?[0-9][0-9\s().-]{6,}$/', $value) === 1) {
            return [
                'type' => 'phone',
                'value' => $value,
                'summary' => 'Phone number',
            ];
        }

        return [
            'type' => 'text',
            'value' => $value,
            'summary' => Str::of($value)->limit(80)->toString(),
        ];
    }

    private function filteredQuery(?string $scanType, ?string $status, ?string $search, bool $barcodesOnly): Builder
    {
        $query = MobileLocalScanHistory::query();

        if ($barcodesOnly) {
            $query->barcodes();
        } elseif (is_string($scanType) && $scanType !== '') {
            $query->forScanType($this->normalizeScanType($scanType));
        }

        if (is_string($status) && $status !== '') {
            $query->forStatus($this->validStatus($status));
        }

        if (is_string($search) && trim($search) !== '') {
            $query->search($search);
        }

        return $query;
    }

    private function normalizeScanType(string $scanType): string
    {
        $normalized = Str::of($scanType)
            ->lower()
            ->replace([' ', '-'], '_')
            ->trim()
            ->toString();

        return $normalized !== '' ? $normalized : MobileLocalScanHistory::TYPE_QR;
    }

    private function validStatus(string $status): string
    {
        return in_array($status, [
            MobileLocalScanHistory::STATUS_CAPTURED,
            MobileLocalScanHistory::STATUS_ACTIONED,
            MobileLocalScanHistory::STATUS_FAILED,
            MobileLocalScanHistory::STATUS_IGNORED,
        ], true) ? $status : MobileLocalScanHistory::STATUS_CAPTURED;
    }

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
