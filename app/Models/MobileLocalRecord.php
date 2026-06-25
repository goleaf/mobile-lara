<?php

namespace App\Models;

use Database\Factories\MobileLocalRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'title',
    'description',
    'status',
    'priority',
    'category_id',
    'user_id',
    'due_at',
    'metadata',
    'archived_at',
    'deleted_at',
    'sync_status',
])]
class MobileLocalRecord extends Model
{
    /** @use HasFactory<MobileLocalRecordFactory> */
    use HasFactory;

    use SoftDeletes;

    public const ENTITY_TYPE = 'record';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_REVIEW = 'review';

    public const STATUS_DONE = 'done';

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_NORMAL = 'normal';

    public const PRIORITY_HIGH = 'high';

    public const PRIORITY_URGENT = 'urgent';

    public const SYNC_PENDING = 'pending';

    public const SYNC_SYNCED = 'synced';

    public const SYNC_FAILED = 'failed';

    /**
     * @var list<string>
     */
    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_ACTIVE,
        self::STATUS_REVIEW,
        self::STATUS_DONE,
    ];

    /**
     * @var list<string>
     */
    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_NORMAL,
        self::PRIORITY_HIGH,
        self::PRIORITY_URGENT,
    ];

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'title',
        'description',
        'status',
        'priority',
        'category_id',
        'user_id',
        'due_at',
        'metadata',
        'archived_at',
        'deleted_at',
        'sync_status',
        'created_at',
        'updated_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'records';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_DRAFT,
        'priority' => self::PRIORITY_NORMAL,
        'metadata' => '{}',
        'sync_status' => self::SYNC_PENDING,
    ];

    public function scopeListOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
    }

    public function scopeActiveRecords(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchivedRecords(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeForStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeForPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeForCategory(Builder $query, int|string $categoryId): Builder
    {
        return $query->where('category_id', (int) $categoryId);
    }

    public function scopeForUser(Builder $query, int|string $userId): Builder
    {
        return $query->where('user_id', (int) $userId);
    }

    public function scopePendingSync(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_PENDING);
    }

    /**
     * @param  list<string>  $slugs
     */
    public function scopeWithTagSlugs(Builder $query, array $slugs): Builder
    {
        if ($slugs === []) {
            return $query;
        }

        return $query->whereHas('tagModels', function (Builder $query) use ($slugs): void {
            $query->whereIn('slug', $slugs);
        });
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('priority', 'like', "%{$search}%")
                ->orWhere('metadata', 'like', "%{$search}%");
        });
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * @return BelongsTo<MobileLocalCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MobileLocalCategory::class, 'category_id');
    }

    /**
     * @return HasMany<MobileLocalNote, $this>
     */
    public function noteItems(): HasMany
    {
        return $this->hasMany(MobileLocalNote::class, 'record_id');
    }

    /**
     * @return HasMany<MobileLocalAttachment, $this>
     */
    public function attachmentItems(): HasMany
    {
        return $this->hasMany(MobileLocalAttachment::class, 'record_id');
    }

    /**
     * @return BelongsToMany<MobileLocalTag, $this, Collection<int, MobileLocalTag>>
     */
    public function tagModels(): BelongsToMany
    {
        return $this->belongsToMany(MobileLocalTag::class, 'record_tag', 'record_id', 'tag_id')
            ->withTimestamps();
    }

    public function statusLabel(): string
    {
        return Str::of($this->status)->replace(['_', '-'], ' ')->title()->toString();
    }

    public function statusVariant(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_REVIEW => 'warning',
            self::STATUS_DONE => 'accent',
            default => 'neutral',
        };
    }

    public function priorityLabel(): string
    {
        return Str::of($this->priority)->replace(['_', '-'], ' ')->title()->toString();
    }

    public function priorityVariant(): string
    {
        return match ($this->priority) {
            self::PRIORITY_HIGH, self::PRIORITY_URGENT => 'warning',
            self::PRIORITY_LOW => 'neutral',
            default => 'accent',
        };
    }

    public function archiveLabel(): string
    {
        return $this->isArchived() ? 'Archived' : 'Active';
    }

    public function archiveVariant(): string
    {
        return $this->isArchived() ? 'warning' : 'success';
    }

    /**
     * @return list<string>
     */
    public function tagList(): array
    {
        if ($this->relationLoaded('tagModels') && $this->tagModels->isNotEmpty()) {
            return $this->tagModels
                ->pluck('name')
                ->filter(fn (mixed $tag): bool => is_string($tag) && trim($tag) !== '')
                ->map(fn (string $tag): string => trim($tag))
                ->values()
                ->all();
        }

        $tags = $this->metadataValue('tags', []);

        if (! is_array($tags)) {
            return [];
        }

        return collect($tags)
            ->filter(fn (mixed $tag): bool => is_string($tag) && trim($tag) !== '')
            ->map(fn (string $tag): string => trim($tag))
            ->values()
            ->all();
    }

    public function tagsLabel(): string
    {
        $tags = $this->tagList();

        return $tags === [] ? 'No tags' : implode(', ', $tags);
    }

    public function categoryLabel(): ?string
    {
        if ($this->relationLoaded('category') && $this->category instanceof MobileLocalCategory) {
            return $this->category->label;
        }

        $metadataLabel = $this->metadataValue('category_label');

        if (is_string($metadataLabel) && trim($metadataLabel) !== '') {
            return trim($metadataLabel);
        }

        return $this->category_id ? "#{$this->category_id}" : null;
    }

    public function descriptionPreview(int $limit = 140): string
    {
        $description = trim((string) $this->description);

        if ($description === '') {
            return 'No description';
        }

        return Str::of($description)->limit($limit)->toString();
    }

    public function summaryPreview(int $limit = 140): string
    {
        return $this->descriptionPreview($limit);
    }

    public function notesPreview(int $limit = 180): string
    {
        $notes = trim($this->notesText() ?? '');

        if ($notes === '') {
            return 'No notes';
        }

        return Str::of($notes)->limit($limit)->toString();
    }

    public function notesText(): ?string
    {
        $notes = $this->metadataValue('notes');

        return is_string($notes) && trim($notes) !== '' ? trim($notes) : null;
    }

    public function metadataValue(string $key, mixed $default = null): mixed
    {
        $metadata = $this->metadata;

        if (! is_array($metadata)) {
            return $default;
        }

        return $metadata[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadataWith(array $values): array
    {
        $metadata = $this->metadata;

        if (! is_array($metadata)) {
            $metadata = [];
        }

        return array_merge($metadata, $values);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'user_id' => 'integer',
            'due_at' => 'immutable_datetime',
            'metadata' => 'array',
            'archived_at' => 'immutable_datetime',
            'deleted_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function summary(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->description);
    }

    /**
     * @return Attribute<list<string>, never>
     */
    protected function tags(): Attribute
    {
        return Attribute::get(fn (): array => $this->tagList());
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function notes(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->notesText());
    }
}
