<?php

namespace App\Models;

use Database\Factories\MobileLocalVoiceNoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'local_file_path',
    'duration',
    'transcript',
    'sync_status',
    'related_entity_type',
    'related_entity_id',
    'created_at',
    'updated_at',
])]
class MobileLocalVoiceNote extends Model
{
    /** @use HasFactory<MobileLocalVoiceNoteFactory> */
    use HasFactory;

    public const SYNC_PENDING = 'pending';

    public const SYNC_SYNCED = 'synced';

    public const SYNC_FAILED = 'failed';

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'local_file_path',
        'duration',
        'transcript',
        'sync_status',
        'related_entity_type',
        'related_entity_id',
        'created_at',
        'updated_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'voice_notes';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'sync_status' => self::SYNC_PENDING,
    ];

    public function scopeRecentOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
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

    public function displayName(): string
    {
        return basename($this->local_file_path) ?: $this->local_file_path;
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

    public function transcriptPreview(int $limit = 120): string
    {
        $transcript = trim((string) $this->transcript);

        if ($transcript === '') {
            return 'Transcript pending';
        }

        return str($transcript)->limit($limit)->toString();
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
            'duration' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
