<?php

namespace App\Models;

use Database\Factories\MobileLocalTagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'name',
    'slug',
])]
class MobileLocalTag extends Model
{
    /** @use HasFactory<MobileLocalTagFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'name',
        'slug',
        'created_at',
        'updated_at',
    ];

    protected $connection = 'mobile_local';

    protected $table = 'tags';

    /**
     * @return BelongsToMany<MobileLocalRecord, $this, Collection<int, MobileLocalRecord>>
     */
    public function records(): BelongsToMany
    {
        return $this->belongsToMany(MobileLocalRecord::class, 'record_tag', 'tag_id', 'record_id')
            ->withTimestamps();
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        });
    }
}
