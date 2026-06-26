<?php

namespace App\Models;

use Database\Factories\RecordCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'public_id',
    'name',
    'slug',
    'color',
    'description',
    'is_active',
])]
class RecordCategory extends Model
{
    /** @use HasFactory<RecordCategoryFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        self::creating(function (RecordCategory $category): void {
            if (! is_string($category->public_id) || trim($category->public_id) === '') {
                $category->public_id = (string) Str::uuid();
            }

            if (! is_string($category->slug) || trim($category->slug) === '') {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(TenantRecord::class);
    }

    /**
     * @param  Builder<RecordCategory>  $query
     * @return Builder<RecordCategory>
     */
    public function scopeForTenant(Builder $query, Tenant|int $tenant): Builder
    {
        return $query
            ->select(['id', 'tenant_id', 'public_id', 'name', 'slug', 'color', 'description', 'is_active'])
            ->where('tenant_id', $tenant instanceof Tenant ? $tenant->id : $tenant);
    }
}
