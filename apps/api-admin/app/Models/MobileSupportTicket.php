<?php

namespace App\Models;

use Database\Factories\MobileSupportTicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'requester_user_id',
    'assigned_user_id',
    'public_id',
    'subject',
    'status',
    'priority',
    'category',
    'source',
    'support_context',
    'last_message_at',
    'closed_at',
])]
final class MobileSupportTicket extends Model
{
    /** @use HasFactory<MobileSupportTicketFactory> */
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_WAITING_ON_USER = 'waiting_on_user';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_CLOSED = 'closed';

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_NORMAL = 'normal';

    public const PRIORITY_HIGH = 'high';

    public const PRIORITY_URGENT = 'urgent';

    public const SELECT_COLUMNS = [
        'id',
        'tenant_id',
        'requester_user_id',
        'assigned_user_id',
        'public_id',
        'subject',
        'status',
        'priority',
        'category',
        'source',
        'support_context',
        'last_message_at',
        'closed_at',
        'created_at',
        'updated_at',
    ];

    protected static function booted(): void
    {
        self::creating(function (MobileSupportTicket $ticket): void {
            if (! is_string($ticket->public_id) || trim($ticket->public_id) === '') {
                $ticket->public_id = (string) Str::uuid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'support_context' => 'array',
            'last_message_at' => 'immutable_datetime',
            'closed_at' => 'immutable_datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MobileSupportMessage::class);
    }

    /**
     * @param  Builder<MobileSupportTicket>  $query
     * @return Builder<MobileSupportTicket>
     */
    public function scopeForTenant(Builder $query, Tenant|int $tenant): Builder
    {
        return $query->where('tenant_id', $tenant instanceof Tenant ? $tenant->id : $tenant);
    }

    /**
     * @param  Builder<MobileSupportTicket>  $query
     * @return Builder<MobileSupportTicket>
     */
    public function scopeForMobileRequester(Builder $query, Tenant $tenant, User $user): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'tenant:id,public_id,name',
                'assignedAgent:id,name',
            ])
            ->forTenant($tenant)
            ->where('requester_user_id', $user->id);
    }

    /**
     * @param  Builder<MobileSupportTicket>  $query
     * @return Builder<MobileSupportTicket>
     */
    public function scopeForMobileList(Builder $query, Tenant $tenant, User $user): Builder
    {
        return $query
            ->forMobileRequester($tenant, $user)
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
    }

    /**
     * @param  Builder<MobileSupportTicket>  $query
     * @return Builder<MobileSupportTicket>
     */
    public function scopeForMobileDetail(Builder $query, Tenant $tenant, User $user): Builder
    {
        return $query
            ->forMobileRequester($tenant, $user)
            ->with([
                'requester:id,name',
                'assignedAgent:id,name',
                'messages:id,tenant_id,mobile_support_ticket_id,author_user_id,public_id,body,direction,visibility,attachments,diagnostic_report_id,metadata,created_at,updated_at',
                'messages.author:id,name',
            ])
            ->withCount('messages');
    }

    /**
     * @param  Builder<MobileSupportTicket>  $query
     * @return Builder<MobileSupportTicket>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'tenant:id,public_id,name,slug',
                'requester:id,name',
                'assignedAgent:id,name',
            ])
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
    }

    /**
     * @param  Builder<MobileSupportTicket>  $query
     * @return Builder<MobileSupportTicket>
     */
    public function scopeForAdminDetail(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'tenant:id,public_id,name,slug',
                'requester:id,name',
                'assignedAgent:id,name',
                'messages:id,tenant_id,mobile_support_ticket_id,author_user_id,public_id,body,direction,visibility,attachments,diagnostic_report_id,metadata,created_at,updated_at',
                'messages.author:id,name',
            ])
            ->withCount('messages');
    }

    /**
     * @param  Builder<MobileSupportTicket>  $query
     * @return Builder<MobileSupportTicket>
     */
    public function scopeForStatus(Builder $query, ?string $status): Builder
    {
        $status = trim((string) $status);

        if ($status === '' || ! in_array($status, self::statuses(), true)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * @param  Builder<MobileSupportTicket>  $query
     * @return Builder<MobileSupportTicket>
     */
    public function scopeMatchingSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('subject', 'like', '%'.$search.'%')
                ->orWhere('status', 'like', '%'.$search.'%')
                ->orWhere('priority', 'like', '%'.$search.'%')
                ->orWhere('category', 'like', '%'.$search.'%');
        });
    }

    /**
     * @param  Builder<MobileSupportTicket>  $query
     * @return Builder<MobileSupportTicket>
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
                ->orWhere('subject', 'like', '%'.$search.'%')
                ->orWhere('status', 'like', '%'.$search.'%')
                ->orWhere('priority', 'like', '%'.$search.'%')
                ->orWhere('category', 'like', '%'.$search.'%')
                ->orWhereHas('tenant', function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('slug', 'like', '%'.$search.'%');
                })
                ->orWhereHas('requester', function (Builder $query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%');
                });
        });
    }

    public function acceptsUserMessages(): bool
    {
        return ! in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED], true);
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_IN_PROGRESS,
            self::STATUS_WAITING_ON_USER,
            self::STATUS_RESOLVED,
            self::STATUS_CLOSED,
        ];
    }

    /**
     * @return list<string>
     */
    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_NORMAL,
            self::PRIORITY_HIGH,
            self::PRIORITY_URGENT,
        ];
    }
}
