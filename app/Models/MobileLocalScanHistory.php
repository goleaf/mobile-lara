<?php

namespace App\Models;

use Database\Factories\MobileLocalScanHistoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'scan_type',
    'raw_value',
    'parsed_value',
    'action_result',
    'status',
    'created_at',
])]
class MobileLocalScanHistory extends Model
{
    /** @use HasFactory<MobileLocalScanHistoryFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    public const TYPE_QR = 'qr';

    public const STATUS_CAPTURED = 'captured';

    public const STATUS_ACTIONED = 'actioned';

    public const STATUS_FAILED = 'failed';

    public const STATUS_IGNORED = 'ignored';

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'scan_type',
        'raw_value',
        'parsed_value',
        'action_result',
        'status',
        'created_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'scan_history';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_CAPTURED,
    ];

    public function scopeHistoryOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function scopeForScanType(Builder $query, string $scanType): Builder
    {
        return $query->where('scan_type', $scanType);
    }

    public function scopeForStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeQrCodes(Builder $query): Builder
    {
        return $query->forScanType(self::TYPE_QR);
    }

    public function scopeBarcodes(Builder $query): Builder
    {
        return $query->where('scan_type', '!=', self::TYPE_QR);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('raw_value', 'like', "%{$search}%")
                ->orWhere('action_result', 'like', "%{$search}%")
                ->orWhere('scan_type', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
        });
    }

    public function parsedType(): string
    {
        $parsedValue = $this->parsed_value;

        return is_array($parsedValue) && is_string($parsedValue['type'] ?? null)
            ? $parsedValue['type']
            : 'unknown';
    }

    public function parsedSummary(int $limit = 120): string
    {
        $parsedValue = $this->parsed_value;

        if (! is_array($parsedValue)) {
            return 'Not parsed';
        }

        $value = $parsedValue['value'] ?? $this->raw_value;

        if (is_scalar($value)) {
            return Str::of((string) $value)->limit($limit)->toString();
        }

        return Str::of((string) json_encode($value))->limit($limit)->toString();
    }

    public function actionResultPreview(int $limit = 120): string
    {
        $actionResult = trim((string) $this->action_result);

        if ($actionResult === '') {
            return 'No action recorded';
        }

        return Str::of($actionResult)->limit($limit)->toString();
    }

    public function scanTypeLabel(): string
    {
        if ($this->scan_type === self::TYPE_QR) {
            return 'QR';
        }

        return Str::of($this->scan_type)->replace(['_', '-'], ' ')->upper()->toString();
    }

    public function statusVariant(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIONED => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_IGNORED => 'neutral',
            default => 'warning',
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'parsed_value' => 'array',
            'created_at' => 'immutable_datetime',
        ];
    }
}
