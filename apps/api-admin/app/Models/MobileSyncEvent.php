<?php

namespace App\Models;

use Database\Factories\MobileSyncEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'user_id',
    'mobile_device_session_id',
    'public_id',
    'client_batch_id',
    'client_intent_id',
    'idempotency_key',
    'collection',
    'action',
    'target_public_id',
    'base_sync_version',
    'outcome',
    'error_code',
    'error_message',
    'response_payload',
    'processed_at',
    'acknowledged_at',
])]
class MobileSyncEvent extends Model
{
    /** @use HasFactory<MobileSyncEventFactory> */
    use HasFactory;

    public const OUTCOME_ACCEPTED = 'accepted';

    public const OUTCOME_REJECTED = 'rejected';

    public const OUTCOME_CONFLICT = 'conflict';

    public const SELECT_COLUMNS = [
        'id',
        'tenant_id',
        'user_id',
        'mobile_device_session_id',
        'public_id',
        'client_batch_id',
        'client_intent_id',
        'idempotency_key',
        'collection',
        'action',
        'target_public_id',
        'base_sync_version',
        'outcome',
        'error_code',
        'error_message',
        'response_payload',
        'processed_at',
        'acknowledged_at',
        'created_at',
        'updated_at',
    ];

    protected static function booted(): void
    {
        self::creating(function (MobileSyncEvent $event): void {
            if (! is_string($event->public_id) || trim($event->public_id) === '') {
                $event->public_id = (string) Str::uuid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'response_payload' => 'array',
            'processed_at' => 'immutable_datetime',
            'acknowledged_at' => 'immutable_datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deviceSession(): BelongsTo
    {
        return $this->belongsTo(MobileDeviceSession::class, 'mobile_device_session_id');
    }

    /**
     * @param  Builder<MobileSyncEvent>  $query
     * @return Builder<MobileSyncEvent>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'tenant:id,public_id,name,slug',
                'user:id,name',
                'deviceSession:id,user_id,device_name,platform,app_version',
            ])
            ->orderByDesc('processed_at')
            ->orderByDesc('id');
    }

    /**
     * @param  Builder<MobileSyncEvent>  $query
     * @return Builder<MobileSyncEvent>
     */
    public function scopeForAdminDetail(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'tenant:id,public_id,name,slug',
                'user:id,name',
                'deviceSession:id,user_id,device_name,platform,app_version',
            ]);
    }

    /**
     * @param  Builder<MobileSyncEvent>  $query
     * @return Builder<MobileSyncEvent>
     */
    public function scopeMatchingAdminSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('public_id', 'like', '%'.$search.'%')
                ->orWhere('client_batch_id', 'like', '%'.$search.'%')
                ->orWhere('client_intent_id', 'like', '%'.$search.'%')
                ->orWhere('idempotency_key', 'like', '%'.$search.'%')
                ->orWhere('collection', 'like', '%'.$search.'%')
                ->orWhere('action', 'like', '%'.$search.'%')
                ->orWhere('target_public_id', 'like', '%'.$search.'%')
                ->orWhere('outcome', 'like', '%'.$search.'%')
                ->orWhere('error_code', 'like', '%'.$search.'%')
                ->orWhereHas('tenant', function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('slug', 'like', '%'.$search.'%')
                        ->orWhere('public_id', 'like', '%'.$search.'%');
                })
                ->orWhereHas('deviceSession', function (Builder $query) use ($search): void {
                    $query
                        ->where('device_name', 'like', '%'.$search.'%')
                        ->orWhere('platform', 'like', '%'.$search.'%')
                        ->orWhere('app_version', 'like', '%'.$search.'%');
                });
        });
    }

    /**
     * @param  Builder<MobileSyncEvent>  $query
     * @return Builder<MobileSyncEvent>
     */
    public function scopeForOutcome(Builder $query, ?string $outcome): Builder
    {
        if (! in_array($outcome, [self::OUTCOME_ACCEPTED, self::OUTCOME_REJECTED, self::OUTCOME_CONFLICT], true)) {
            return $query;
        }

        return $query->where('outcome', $outcome);
    }

    public function isAcknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }
}
