<?php

namespace App\Models;

use Database\Factories\MobileSyncEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
}
