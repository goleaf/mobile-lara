<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalRecord;
use App\Models\MobileLocalTag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

final class TagRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    public function findOrCreate(string $name): MobileLocalTag
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $normalizedName = $this->normalizeName($name);
        $slug = $this->slugFor($normalizedName);

        return MobileLocalTag::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => $normalizedName],
        );
    }

    /**
     * @throws ModelNotFoundException<MobileLocalTag>
     */
    public function find(int|string $tagId): MobileLocalTag
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalTag::query()
            ->select(MobileLocalTag::SELECT_COLUMNS)
            ->whereKey($tagId)
            ->firstOrFail();
    }

    /**
     * @param  list<string>  $names
     * @return Collection<int, MobileLocalTag>
     */
    public function findOrCreateMany(array $names): Collection
    {
        $tags = new Collection;

        foreach ($this->normalizeNames($names) as $name) {
            $tags->push($this->findOrCreate($name));
        }

        return $tags;
    }

    /**
     * @param  list<string>  $names
     */
    public function syncRecordTags(MobileLocalRecord $record, array $names): MobileLocalRecord
    {
        $tags = $this->findOrCreateMany($names);

        $record->tagModels()->sync($tags->modelKeys());

        return $record->load('tagModels');
    }

    /**
     * @return Collection<int, MobileLocalTag>
     */
    public function search(string $search = '', int $limit = 12): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalTag::query()
            ->select(MobileLocalTag::SELECT_COLUMNS)
            ->search($search)
            ->orderBy('name')
            ->limit(max(1, min($limit, 50)))
            ->get();
    }

    /**
     * @param  list<string>  $names
     * @return list<string>
     */
    public function normalizeNames(array $names): array
    {
        return collect($names)
            ->filter(fn (mixed $name): bool => is_string($name))
            ->map(fn (string $name): string => $this->normalizeName($name))
            ->filter(fn (string $name): bool => $name !== '')
            ->unique(fn (string $name): string => $this->slugFor($name))
            ->take(12)
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $slugs
     * @return list<string>
     */
    public function normalizeSlugs(array $slugs): array
    {
        return collect($slugs)
            ->filter(fn (mixed $slug): bool => is_string($slug))
            ->map(fn (string $slug): string => Str::of($slug)->slug()->limit(90, '')->toString())
            ->filter(fn (string $slug): bool => $slug !== '')
            ->unique()
            ->take(12)
            ->values()
            ->all();
    }

    public function normalizeName(string $name): string
    {
        return Str::of($name)
            ->squish()
            ->lower()
            ->limit(40, '')
            ->toString();
    }

    public function slugFor(string $name): string
    {
        return Str::of($name)
            ->slug()
            ->limit(90, '')
            ->toString();
    }
}
