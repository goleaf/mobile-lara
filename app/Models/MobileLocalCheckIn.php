<?php

namespace App\Models;

use Database\Factories\MobileLocalCheckInFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'latitude',
    'longitude',
    'accuracy',
    'note',
    'photo_id',
    'sync_status',
    'created_at',
    'updated_at',
])]
class MobileLocalCheckIn extends Model
{
    /** @use HasFactory<MobileLocalCheckInFactory> */
    use HasFactory;

    public const SYNC_PENDING = 'pending';

    public const SYNC_SYNCED = 'synced';

    public const SYNC_FAILED = 'failed';

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'user_id',
        'latitude',
        'longitude',
        'accuracy',
        'note',
        'photo_id',
        'sync_status',
        'created_at',
        'updated_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'check_ins';

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
            ->with(['photo' => fn ($query) => $query->select(MobileLocalMediaItem::SELECT_COLUMNS)])
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function scopeForUser(Builder $query, int|string $userId): Builder
    {
        return $query->where('user_id', (int) $userId);
    }

    public function scopeForSyncStatus(Builder $query, string $syncStatus): Builder
    {
        return $query->where('sync_status', $syncStatus);
    }

    public function scopePendingSync(Builder $query): Builder
    {
        return $query->forSyncStatus(self::SYNC_PENDING);
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(MobileLocalMediaItem::class, 'photo_id');
    }

    public function coordinates(): string
    {
        return number_format((float) $this->latitude, 7, '.', '').', '.number_format((float) $this->longitude, 7, '.', '');
    }

    public function formattedAccuracy(): ?string
    {
        if ($this->accuracy === null) {
            return null;
        }

        return number_format((float) $this->accuracy, 2).' m';
    }

    public function notePreview(int $limit = 120): string
    {
        $note = trim((string) $this->note);

        if ($note === '') {
            return 'No note';
        }

        return Str::of($note)->limit($limit)->toString();
    }

    public function photoLabel(): ?string
    {
        return $this->photo?->displayName();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'accuracy' => 'float',
            'photo_id' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
