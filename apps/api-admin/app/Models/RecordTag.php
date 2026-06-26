<?php

namespace App\Models;

use Database\Factories\RecordTagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'public_id',
    'name',
    'slug',
    'color',
])]
class RecordTag extends Model
{
    /** @use HasFactory<RecordTagFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        self::creating(function (RecordTag $tag): void {
            if (! is_string($tag->public_id) || trim($tag->public_id) === '') {
                $tag->public_id = (string) Str::uuid();
            }

            if (! is_string($tag->slug) || trim($tag->slug) === '') {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function records(): BelongsToMany
    {
        return $this->belongsToMany(TenantRecord::class, 'record_record_tag')->withTimestamps();
    }

    /**
     * @param  Builder<RecordTag>  $query
     * @return Builder<RecordTag>
     */
    public function scopeForTenant(Builder $query, Tenant|int $tenant): Builder
    {
        return $query
            ->select(['id', 'tenant_id', 'public_id', 'name', 'slug', 'color'])
            ->where('tenant_id', $tenant instanceof Tenant ? $tenant->id : $tenant);
    }
}
