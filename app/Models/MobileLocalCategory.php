<?php

namespace App\Models;

use Database\Factories\MobileLocalCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'label',
    'slug',
    'color',
    'sort_order',
])]
class MobileLocalCategory extends Model
{
    /** @use HasFactory<MobileLocalCategoryFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'label',
        'slug',
        'color',
        'sort_order',
        'created_at',
        'updated_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'categories';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'color' => '#64748b',
        'sort_order' => 0,
    ];

    /**
     * @return HasMany<MobileLocalRecord, $this>
     */
    public function records(): HasMany
    {
        return $this->hasMany(MobileLocalRecord::class, 'category_id');
    }

    public function scopeListOrder(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->orderBy('id');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('label', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        });
    }

    public function recordCountLabel(): string
    {
        $count = (int) ($this->records_count ?? 0);

        return $count === 1 ? '1 record' : "{$count} records";
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
