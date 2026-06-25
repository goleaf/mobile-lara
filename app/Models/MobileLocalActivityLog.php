<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'action',
    'entity_type',
    'entity_id',
    'message',
    'metadata',
    'sync_status',
    'created_at',
])]
class MobileLocalActivityLog extends Model
{
    public const UPDATED_AT = null;

    public const SYNC_PENDING = 'pending';

    public const SYNC_SYNCED = 'synced';

    public const SYNC_FAILED = 'failed';

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'action',
        'entity_type',
        'entity_id',
        'message',
        'metadata',
        'sync_status',
        'created_at',
    ];

    protected $connection = 'mobile_local';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'metadata' => '{}',
        'sync_status' => self::SYNC_PENDING,
    ];

    public function scopeFeed(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function scopeForSyncStatus(Builder $query, string $syncStatus): Builder
    {
        return $query->where('sync_status', $syncStatus);
    }

    public function scopeForEntity(Builder $query, string $entityType, int|string $entityId): Builder
    {
        return $query
            ->where('entity_type', $entityType)
            ->where('entity_id', (string) $entityId);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'immutable_datetime',
        ];
    }
}
