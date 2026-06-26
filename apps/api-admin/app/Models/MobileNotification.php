<?php

namespace App\Models;

use Database\Factories\MobileNotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'user_id',
    'public_id',
    'type',
    'title',
    'body',
    'data',
    'deep_link',
    'source',
    'delivery_status',
    'sent_at',
    'read_at',
    'opened_at',
    'deleted_at',
])]
final class MobileNotification extends Model
{
    /** @use HasFactory<MobileNotificationFactory> */
    use HasFactory;

    public const TYPE_INFO = 'info';

    public const TYPE_SUCCESS = 'success';

    public const TYPE_WARNING = 'warning';

    public const TYPE_ERROR = 'error';

    public const SELECT_COLUMNS = [
        'id',
        'tenant_id',
        'user_id',
        'public_id',
        'type',
        'title',
        'body',
        'data',
        'deep_link',
        'source',
        'delivery_status',
        'sent_at',
        'read_at',
        'opened_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected static function booted(): void
    {
        self::creating(function (MobileNotification $notification): void {
            if (! is_string($notification->public_id) || trim($notification->public_id) === '') {
                $notification->public_id = (string) Str::uuid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'sent_at' => 'immutable_datetime',
            'read_at' => 'immutable_datetime',
            'opened_at' => 'immutable_datetime',
            'deleted_at' => 'immutable_datetime',
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

    /**
     * @param  Builder<MobileNotification>  $query
     * @return Builder<MobileNotification>
     */
    public function scopeForMobileInbox(Builder $query, Tenant $tenant, User $user): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with(['tenant:id,public_id'])
            ->where('tenant_id', $tenant->id)
            ->where(function (Builder $query) use ($user): void {
                $query
                    ->where('user_id', $user->id)
                    ->orWhereNull('user_id');
            })
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /**
     * @param  Builder<MobileNotification>  $query
     * @return Builder<MobileNotification>
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * @param  Builder<MobileNotification>  $query
     * @return Builder<MobileNotification>
     */
    public function scopeForMobileUnreadCounter(Builder $query, Tenant $tenant, User $user): Builder
    {
        return $query
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->unread();
    }

    /**
     * @param  Builder<MobileNotification>  $query
     * @return Builder<MobileNotification>
     */
    public function scopeMutableByMobileUser(Builder $query, Tenant $tenant, User $user): Builder
    {
        return $query
            ->forMobileInbox($tenant, $user)
            ->where('user_id', $user->id);
    }

    /**
     * @param  Builder<MobileNotification>  $query
     * @return Builder<MobileNotification>
     */
    public function scopeForType(Builder $query, ?string $type): Builder
    {
        if (! is_string($type) || trim($type) === '') {
            return $query;
        }

        return $query->where('type', trim($type));
    }

    /**
     * @param  Builder<MobileNotification>  $query
     * @return Builder<MobileNotification>
     */
    public function scopeMatchingSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('title', 'like', '%'.$search.'%')
                ->orWhere('body', 'like', '%'.$search.'%')
                ->orWhere('deep_link', 'like', '%'.$search.'%');
        });
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function isUserScoped(): bool
    {
        return $this->user_id !== null;
    }
}
