<?php

namespace App\Models;

use Database\Factories\MobileLocalAttachmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

#[Fillable([
    'record_id',
    'media_item_id',
    'path',
    'name',
    'mime',
    'type',
    'size',
    'caption',
    'sync_status',
    'upload_status',
    'metadata',
    'deleted_at',
])]
class MobileLocalAttachment extends Model
{
    /** @use HasFactory<MobileLocalAttachmentFactory> */
    use HasFactory;

    use SoftDeletes;

    public const TYPE_IMAGE = 'image';

    public const TYPE_VIDEO = 'video';

    public const TYPE_AUDIO = 'audio';

    public const TYPE_FILE = 'file';

    public const SYNC_PENDING = 'pending';

    public const SYNC_SYNCED = 'synced';

    public const SYNC_FAILED = 'failed';

    public const UPLOAD_QUEUED = 'queued';

    public const UPLOAD_UPLOADED = 'uploaded';

    public const UPLOAD_FAILED = 'failed';

    /**
     * @var list<string>
     */
    public const TYPES = [
        self::TYPE_IMAGE,
        self::TYPE_VIDEO,
        self::TYPE_AUDIO,
        self::TYPE_FILE,
    ];

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'record_id',
        'media_item_id',
        'path',
        'name',
        'mime',
        'type',
        'size',
        'caption',
        'sync_status',
        'upload_status',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'attachments';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => self::TYPE_FILE,
        'sync_status' => self::SYNC_PENDING,
        'upload_status' => self::UPLOAD_QUEUED,
        'metadata' => '{}',
    ];

    /**
     * @return BelongsTo<MobileLocalRecord, $this>
     */
    public function record(): BelongsTo
    {
        return $this->belongsTo(MobileLocalRecord::class, 'record_id');
    }

    /**
     * @return BelongsTo<MobileLocalMediaItem, $this>
     */
    public function mediaItem(): BelongsTo
    {
        return $this->belongsTo(MobileLocalMediaItem::class, 'media_item_id');
    }

    public function scopeForRecord(Builder $query, MobileLocalRecord|int|string $record): Builder
    {
        $recordId = $record instanceof MobileLocalRecord ? $record->getKey() : $record;

        return $query->where('record_id', (int) $recordId);
    }

    public function scopeListOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function displayName(): string
    {
        $name = is_string($this->name) ? trim($this->name) : '';

        return $name !== '' ? $name : (basename($this->path) ?: $this->path);
    }

    public function typeLabel(): string
    {
        return Str::of($this->type)->headline()->toString();
    }

    public function isImage(): bool
    {
        return $this->type === self::TYPE_IMAGE;
    }

    public function formattedSize(): ?string
    {
        if (! is_int($this->size)) {
            return null;
        }

        return Number::fileSize($this->size);
    }

    public function syncLabel(): string
    {
        return match ($this->sync_status) {
            self::SYNC_SYNCED => 'Synced',
            self::SYNC_FAILED => 'Sync failed',
            default => 'Pending sync',
        };
    }

    public function syncVariant(): string
    {
        return match ($this->sync_status) {
            self::SYNC_SYNCED => 'success',
            self::SYNC_FAILED => 'warning',
            default => 'neutral',
        };
    }

    public function uploadLabel(): string
    {
        return match ($this->upload_status) {
            self::UPLOAD_UPLOADED => 'Uploaded',
            self::UPLOAD_FAILED => 'Upload failed',
            default => 'Queued upload',
        };
    }

    public function uploadVariant(): string
    {
        return match ($this->upload_status) {
            self::UPLOAD_UPLOADED => 'success',
            self::UPLOAD_FAILED => 'warning',
            default => 'neutral',
        };
    }

    public function shareText(): string
    {
        return collect([
            $this->displayName(),
            'Type: '.$this->typeLabel(),
            $this->mime ? 'MIME: '.$this->mime : null,
            $this->formattedSize() ? 'Size: '.$this->formattedSize() : null,
            $this->caption,
            'Path: '.$this->path,
        ])
            ->filter()
            ->implode(PHP_EOL);
    }

    /**
     * @return list<array{label: string, value: string|null}>
     */
    public function previewRows(): array
    {
        return [
            ['label' => 'Name', 'value' => $this->displayName()],
            ['label' => 'Type', 'value' => $this->typeLabel()],
            ['label' => 'MIME', 'value' => $this->mime],
            ['label' => 'Size', 'value' => $this->formattedSize()],
            ['label' => 'Sync', 'value' => $this->syncLabel()],
            ['label' => 'Upload', 'value' => $this->uploadLabel()],
            ['label' => 'Media item', 'value' => $this->media_item_id ? "#{$this->media_item_id}" : null],
            ['label' => 'Path', 'value' => $this->path],
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
            'record_id' => 'integer',
            'media_item_id' => 'integer',
            'size' => 'integer',
            'metadata' => 'array',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
            'deleted_at' => 'immutable_datetime',
        ];
    }
}
