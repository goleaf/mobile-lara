<?php

namespace App\Models;

use Database\Factories\MobileLocalMediaItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

#[Fillable([
    'path',
    'type',
    'mime',
    'size',
    'width',
    'height',
    'duration',
    'caption',
    'sync_status',
    'related_entity_type',
    'related_entity_id',
    'created_at',
    'updated_at',
])]
class MobileLocalMediaItem extends Model
{
    /** @use HasFactory<MobileLocalMediaItemFactory> */
    use HasFactory;

    public const TYPE_IMAGE = 'image';

    public const TYPE_VIDEO = 'video';

    public const TYPE_AUDIO = 'audio';

    public const TYPE_FILE = 'file';

    public const SYNC_PENDING = 'pending';

    public const SYNC_SYNCED = 'synced';

    public const SYNC_FAILED = 'failed';

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'path',
        'type',
        'mime',
        'size',
        'width',
        'height',
        'duration',
        'caption',
        'sync_status',
        'related_entity_type',
        'related_entity_id',
        'created_at',
        'updated_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'media_items';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'sync_status' => self::SYNC_PENDING,
    ];

    public function scopeGalleryOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeImages(Builder $query): Builder
    {
        return $query->forType(self::TYPE_IMAGE);
    }

    public function scopeVideos(Builder $query): Builder
    {
        return $query->forType(self::TYPE_VIDEO);
    }

    public function scopeForSyncStatus(Builder $query, string $syncStatus): Builder
    {
        return $query->where('sync_status', $syncStatus);
    }

    public function scopePendingSync(Builder $query): Builder
    {
        return $query->forSyncStatus(self::SYNC_PENDING);
    }

    public function scopeForRelatedEntity(Builder $query, string $entityType, int|string $entityId): Builder
    {
        return $query
            ->where('related_entity_type', $entityType)
            ->where('related_entity_id', (string) $entityId);
    }

    public function isImage(): bool
    {
        return $this->type === self::TYPE_IMAGE;
    }

    public function isVideo(): bool
    {
        return $this->type === self::TYPE_VIDEO;
    }

    public function displayName(): string
    {
        return basename($this->path) ?: $this->path;
    }

    public function dimensions(): ?string
    {
        if (! is_int($this->width) || ! is_int($this->height)) {
            return null;
        }

        return "{$this->width} x {$this->height}";
    }

    public function formattedSize(): ?string
    {
        if (! is_int($this->size)) {
            return null;
        }

        return Number::fileSize($this->size);
    }

    public function formattedDuration(): ?string
    {
        if (! is_int($this->duration)) {
            return null;
        }

        $minutes = intdiv($this->duration, 60);
        $seconds = $this->duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function relatedEntityLabel(): ?string
    {
        if (! is_string($this->related_entity_type) && ! is_string($this->related_entity_id)) {
            return null;
        }

        $entityType = $this->related_entity_type ?: 'entity';

        if (! is_string($this->related_entity_id) || $this->related_entity_id === '') {
            return $entityType;
        }

        return "{$entityType} #{$this->related_entity_id}";
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'duration' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
