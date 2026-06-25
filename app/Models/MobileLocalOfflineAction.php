<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'action_type',
    'endpoint',
    'method',
    'payload',
    'headers',
    'status',
    'attempts',
    'last_error',
    'local_version',
    'remote_version',
    'conflict_status',
    'conflict_payload',
    'available_at',
    'created_at',
    'completed_at',
])]
class MobileLocalOfflineAction extends Model
{
    public const UPDATED_AT = null;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const CONFLICT_NONE = 'none';

    public const CONFLICT_PENDING = 'pending';

    public const CONFLICT_RESOLVED = 'resolved';

    public const CONFLICT_DISMISSED = 'dismissed';

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'action_type',
        'endpoint',
        'method',
        'payload',
        'headers',
        'status',
        'attempts',
        'last_error',
        'local_version',
        'remote_version',
        'conflict_status',
        'conflict_payload',
        'available_at',
        'created_at',
        'completed_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'offline_actions';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'method' => 'POST',
        'payload' => '{}',
        'headers' => '{}',
        'status' => self::STATUS_PENDING,
        'attempts' => 0,
        'conflict_status' => self::CONFLICT_NONE,
        'conflict_payload' => '{}',
    ];

    public function scopeQueueOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderBy('available_at')
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public function scopeForStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeDue(Builder $query, string $availableAt): Builder
    {
        return $query
            ->forStatus(self::STATUS_PENDING)
            ->where(function (Builder $query) use ($availableAt): void {
                $query
                    ->whereNull('available_at')
                    ->orWhere('available_at', '<=', $availableAt);
            });
    }

    public function scopeForConflictStatus(Builder $query, string $conflictStatus): Builder
    {
        return $query->where('conflict_status', $conflictStatus);
    }

    public function scopeConflictOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'headers' => 'array',
            'conflict_payload' => 'array',
            'attempts' => 'integer',
            'available_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'completed_at' => 'immutable_datetime',
        ];
    }
}
