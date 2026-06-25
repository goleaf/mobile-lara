<?php

namespace App\Models;

use Database\Factories\MobileLocalNotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'title',
    'body',
    'type',
    'data',
    'read_at',
    'opened_at',
    'deep_link',
    'created_at',
])]
class MobileLocalNotification extends Model
{
    /** @use HasFactory<MobileLocalNotificationFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    public const TYPE_INFO = 'info';

    public const TYPE_SUCCESS = 'success';

    public const TYPE_WARNING = 'warning';

    public const TYPE_ERROR = 'error';

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'title',
        'body',
        'type',
        'data',
        'read_at',
        'opened_at',
        'deep_link',
        'created_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'local_notifications';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => self::TYPE_INFO,
        'data' => '{}',
    ];

    public function scopeInboxOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeOpened(Builder $query): Builder
    {
        return $query->whereNotNull('opened_at');
    }

    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
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
                ->orWhere('body', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%")
                ->orWhere('deep_link', 'like', "%{$search}%");
        });
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function isOpened(): bool
    {
        return $this->opened_at !== null;
    }

    public function bodyPreview(int $limit = 140): string
    {
        return Str::of((string) $this->body)->trim()->limit($limit)->toString();
    }

    public function typeLabel(): string
    {
        return Str::of((string) $this->type)->replace(['_', '-'], ' ')->title()->toString();
    }

    public function typeVariant(): string
    {
        return match ($this->type) {
            self::TYPE_SUCCESS => 'success',
            self::TYPE_WARNING => 'warning',
            self::TYPE_ERROR => 'danger',
            default => 'accent',
        };
    }

    public function deepLinkLabel(int $limit = 48): ?string
    {
        $deepLink = trim((string) $this->deep_link);

        if ($deepLink === '') {
            return null;
        }

        $host = parse_url($deepLink, PHP_URL_HOST);

        return Str::of((string) ($host ?: $deepLink))->limit($limit)->toString();
    }

    /**
     * @return list<array{key: string, value: string}>
     */
    public function dataEntries(int $limit = 4): array
    {
        $data = $this->data;

        if (! is_array($data)) {
            return [];
        }

        $entries = [];

        foreach (array_slice($data, 0, $limit, true) as $key => $value) {
            $entries[] = [
                'key' => Str::of((string) $key)->replace(['_', '-'], ' ')->title()->toString(),
                'value' => Str::of($this->displayValue($value))->limit(90)->toString(),
            ];
        }

        return $entries;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'immutable_datetime',
            'opened_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
        ];
    }

    private function displayValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return (string) json_encode($value);
    }
}
