<?php

namespace App\Models;

use Database\Factories\MobileLocalNotificationScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'notification_id',
    'title',
    'body',
    'type',
    'data',
    'deep_link',
    'scheduled_at',
    'status',
    'driver',
    'native_id',
    'cancelled_at',
    'created_at',
])]
class MobileLocalNotificationSchedule extends Model
{
    /** @use HasFactory<MobileLocalNotificationScheduleFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'notification_id',
        'title',
        'body',
        'type',
        'data',
        'deep_link',
        'scheduled_at',
        'status',
        'driver',
        'native_id',
        'cancelled_at',
        'created_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'local_notification_schedules';

    protected $primaryKey = 'notification_id';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => MobileLocalNotification::TYPE_INFO,
        'data' => '{}',
        'status' => self::STATUS_SCHEDULED,
        'driver' => 'placeholder',
    ];

    public function scopeScheduleOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderBy('scheduled_at')
            ->orderByDesc('created_at')
            ->orderBy('notification_id');
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeForDriver(Builder $query, string $driver): Builder
    {
        return $query->where('driver', $driver);
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * @return array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     type: string,
     *     data: array<string, mixed>,
     *     deep_link: string|null,
     *     scheduled_at: string|null,
     *     status: string,
     *     driver: string,
     *     native_id: string|null,
     *     created_at: string|null,
     *     cancelled_at: string|null
     * }
     */
    public function toNotificationPayload(): array
    {
        return [
            'id' => (string) $this->notification_id,
            'title' => (string) $this->title,
            'body' => (string) $this->body,
            'type' => (string) $this->type,
            'data' => is_array($this->data) ? $this->data : [],
            'deep_link' => $this->deep_link,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'status' => (string) $this->status,
            'driver' => (string) $this->driver,
            'native_id' => $this->native_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'scheduled_at' => 'immutable_datetime',
            'cancelled_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
        ];
    }
}
