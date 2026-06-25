<?php

namespace App\Models;

use Database\Factories\MobileLocalNoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'record_id',
    'user_id',
    'body',
    'sync_status',
    'metadata',
    'deleted_at',
])]
class MobileLocalNote extends Model
{
    /** @use HasFactory<MobileLocalNoteFactory> */
    use HasFactory;

    use SoftDeletes;

    public const SYNC_PENDING = 'pending';

    public const SYNC_SYNCED = 'synced';

    public const SYNC_FAILED = 'failed';

    /**
     * @var list<string>
     */
    public const SYNC_STATUSES = [
        self::SYNC_PENDING,
        self::SYNC_SYNCED,
        self::SYNC_FAILED,
    ];

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'record_id',
        'user_id',
        'body',
        'sync_status',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'notes';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'sync_status' => self::SYNC_PENDING,
        'metadata' => '{}',
    ];

    /**
     * @return BelongsTo<MobileLocalRecord, $this>
     */
    public function record(): BelongsTo
    {
        return $this->belongsTo(MobileLocalRecord::class, 'record_id');
    }

    public function scopeForRecord(Builder $query, MobileLocalRecord|int|string $record): Builder
    {
        $recordId = $record instanceof MobileLocalRecord ? $record->getKey() : $record;

        return $query->where('record_id', (int) $recordId);
    }

    public function scopeListOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function syncLabel(): string
    {
        return match ($this->sync_status) {
            self::SYNC_SYNCED => 'Synced',
            self::SYNC_FAILED => 'Sync failed',
            default => 'Pending sync',
        };
    }

    public function syncVariant(): string
    {
        return match ($this->sync_status) {
            self::SYNC_SYNCED => 'success',
            self::SYNC_FAILED => 'warning',
            default => 'neutral',
        };
    }

    public function bodyPreview(int $limit = 180): string
    {
        return Str::of($this->body)
            ->squish()
            ->limit($limit)
            ->toString();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'record_id' => 'integer',
            'user_id' => 'integer',
            'metadata' => 'array',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
            'deleted_at' => 'immutable_datetime',
        ];
    }
}
