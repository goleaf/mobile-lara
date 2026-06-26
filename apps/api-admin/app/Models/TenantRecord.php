<?php

namespace App\Models;

use Database\Factories\TenantRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'created_by_user_id',
    'updated_by_user_id',
    'record_category_id',
    'public_id',
    'title',
    'description',
    'status',
    'priority',
    'metadata',
    'sync_version',
    'archived_at',
])]
class TenantRecord extends Model
{
    /** @use HasFactory<TenantRecordFactory> */
    use HasFactory;

    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_REVIEW = 'review';

    public const STATUS_DONE = 'done';

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_NORMAL = 'normal';

    public const PRIORITY_HIGH = 'high';

    public const PRIORITY_URGENT = 'urgent';

    public const SELECT_COLUMNS = [
        'id',
        'tenant_id',
        'created_by_user_id',
        'updated_by_user_id',
        'record_category_id',
        'public_id',
        'title',
        'description',
        'status',
        'priority',
        'metadata',
        'sync_version',
        'archived_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected static function booted(): void
    {
        self::creating(function (TenantRecord $record): void {
            if (! is_string($record->public_id) || trim($record->public_id) === '') {
                $record->public_id = (string) Str::uuid();
            }

            if (! is_string($record->sync_version) || trim($record->sync_version) === '') {
                $record->sync_version = (string) Str::uuid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'archived_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RecordCategory::class, 'record_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(RecordTag::class, 'record_record_tag')->withTimestamps();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(RecordNote::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(RecordAttachment::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(RecordActivity::class);
    }

    /**
     * @param  Builder<TenantRecord>  $query
     * @return Builder<TenantRecord>
     */
    public function scopeForTenant(Builder $query, Tenant|int $tenant): Builder
    {
        return $query->where('tenant_id', $tenant instanceof Tenant ? $tenant->id : $tenant);
    }

    /**
     * @param  Builder<TenantRecord>  $query
     * @return Builder<TenantRecord>
     */
    public function scopeForMobileList(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'category:id,tenant_id,public_id,name,slug,color',
                'tags:id,tenant_id,public_id,name,slug,color',
            ])
            ->withCount(['notes', 'attachments', 'activities'])
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
    }

    /**
     * @param  Builder<TenantRecord>  $query
     * @return Builder<TenantRecord>
     */
    public function scopeForMobileDetail(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'tenant:id,public_id,name',
                'category:id,tenant_id,public_id,name,slug,color',
                'tags:id,tenant_id,public_id,name,slug,color',
                'notes:id,tenant_id,tenant_record_id,author_user_id,public_id,body,visibility,created_at,updated_at',
                'notes.author:id,name,email',
                'attachments:id,tenant_id,tenant_record_id,uploaded_by_user_id,public_id,local_id,file_name,mime_type,size_bytes,status,metadata,created_at,updated_at',
                'activities:id,tenant_id,tenant_record_id,actor_user_id,action,description,metadata,created_at',
                'activities.actor:id,name,email',
            ])
            ->withCount(['notes', 'attachments', 'activities']);
    }

    /**
     * @param  Builder<TenantRecord>  $query
     * @return Builder<TenantRecord>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'tenant:id,public_id,name,slug,status,subscription_state',
                'category:id,tenant_id,public_id,name,slug,color',
                'creator:id,name',
                'updater:id,name',
                'tags:id,tenant_id,public_id,name,slug,color',
            ])
            ->withCount(['notes', 'attachments', 'activities'])
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
    }

    /**
     * @param  Builder<TenantRecord>  $query
     * @return Builder<TenantRecord>
     */
    public function scopeForAdminDetail(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'tenant:id,public_id,name,slug,status,subscription_state',
                'category:id,tenant_id,public_id,name,slug,color,description,is_active',
                'creator:id,name',
                'updater:id,name',
                'tags:id,tenant_id,public_id,name,slug,color',
                'notes:id,tenant_id,tenant_record_id,author_user_id,public_id,body,visibility,metadata,created_at,updated_at',
                'notes.author:id,name',
                'attachments:id,tenant_id,tenant_record_id,uploaded_by_user_id,public_id,local_id,file_name,mime_type,size_bytes,status,metadata,created_at,updated_at',
                'attachments.uploader:id,name',
                'activities:id,tenant_id,tenant_record_id,actor_user_id,action,description,metadata,created_at',
                'activities.actor:id,name',
            ])
            ->withCount(['notes', 'attachments', 'activities']);
    }

    /**
     * @param  Builder<TenantRecord>  $query
     * @return Builder<TenantRecord>
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
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhere('status', 'like', '%'.$search.'%')
                ->orWhere('priority', 'like', '%'.$search.'%');
        });
    }

    /**
     * @param  Builder<TenantRecord>  $query
     * @return Builder<TenantRecord>
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
                ->orWhere('title', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhere('status', 'like', '%'.$search.'%')
                ->orWhere('priority', 'like', '%'.$search.'%')
                ->orWhereHas('tenant', function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('slug', 'like', '%'.$search.'%');
                })
                ->orWhereHas('category', function (Builder $query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orWhereHas('tags', function (Builder $query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%');
                });
        });
    }

    /**
     * @param  Builder<TenantRecord>  $query
     * @return Builder<TenantRecord>
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
     * @param  Builder<TenantRecord>  $query
     * @return Builder<TenantRecord>
     */
    public function scopeArchivedFilter(Builder $query, ?string $archived): Builder
    {
        return match ($archived) {
            'only' => $query->whereNotNull('archived_at'),
            'with' => $query,
            default => $query->whereNull('archived_at'),
        };
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_ACTIVE,
            self::STATUS_REVIEW,
            self::STATUS_DONE,
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
