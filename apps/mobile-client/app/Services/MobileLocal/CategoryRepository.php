<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

final class CategoryRepository
{
    /**
     * @var list<array{label: string, color: string, sort_order: int}>
     */
    private const DEFAULT_CATEGORIES = [
        ['label' => 'General', 'color' => '#64748b', 'sort_order' => 10],
        ['label' => 'Work', 'color' => '#2563eb', 'sort_order' => 20],
        ['label' => 'Client', 'color' => '#7c3aed', 'sort_order' => 30],
        ['label' => 'Field', 'color' => '#059669', 'sort_order' => 40],
        ['label' => 'Support', 'color' => '#f97316', 'sort_order' => 50],
    ];

    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    /**
     * @return Collection<int, MobileLocalCategory>
     */
    public function list(): Collection
    {
        $this->ensureDefaults();

        return MobileLocalCategory::query()
            ->listOrder()
            ->withCount('records')
            ->get();
    }

    /**
     * @return array<string, string>
     */
    public function options(): array
    {
        return $this->list()
            ->mapWithKeys(fn (MobileLocalCategory $category): array => [
                (string) $category->id => $category->label,
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function defaultOptions(): array
    {
        return collect(self::DEFAULT_CATEGORIES)
            ->mapWithKeys(fn (array $category, int $index): array => [
                (string) ($index + 1) => $category['label'],
            ])
            ->all();
    }

    /**
     * @throws ModelNotFoundException<MobileLocalCategory>
     */
    public function find(int|string $categoryId): MobileLocalCategory
    {
        $this->ensureDefaults();

        return MobileLocalCategory::query()
            ->select(MobileLocalCategory::SELECT_COLUMNS)
            ->whereKey($categoryId)
            ->firstOrFail();
    }

    public function create(string $label, string $color, ?int $sortOrder = null): MobileLocalCategory
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $label = $this->normalizeLabel($label);

        return MobileLocalCategory::query()->create([
            'label' => $label,
            'slug' => $this->uniqueSlug($label),
            'color' => $this->normalizeColor($color),
            'sort_order' => $sortOrder ?? $this->nextSortOrder(),
        ]);
    }

    public function update(MobileLocalCategory $category, string $label, string $color, ?int $sortOrder = null): MobileLocalCategory
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $label = $this->normalizeLabel($label);

        $category->forceFill([
            'label' => $label,
            'slug' => $this->uniqueSlug($label, $category->getKey()),
            'color' => $this->normalizeColor($color),
            'sort_order' => $sortOrder ?? $category->sort_order,
        ])->save();

        return $this->find($category->getKey());
    }

    public function delete(MobileLocalCategory $category): bool
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $category->records()->update(['category_id' => null]);

        return (bool) $category->delete();
    }

    public function labelFor(int|string|null $categoryId): string
    {
        if ($categoryId === null || (int) $categoryId < 1) {
            return 'General';
        }

        return $this->options()[(string) $categoryId] ?? 'General';
    }

    public function ensureDefaults(): void
    {
        $this->mobileLocalDatabase->ensureFileExists();

        if (MobileLocalCategory::query()->exists()) {
            return;
        }

        foreach (self::DEFAULT_CATEGORIES as $category) {
            MobileLocalCategory::query()->create([
                'label' => $category['label'],
                'slug' => $this->slugFor($category['label']),
                'color' => $category['color'],
                'sort_order' => $category['sort_order'],
            ]);
        }
    }

    public function normalizeLabel(string $label): string
    {
        $label = Str::of($label)
            ->squish()
            ->limit(80, '')
            ->toString();

        return $label === '' ? 'Untitled category' : $label;
    }

    public function normalizeColor(string $color): string
    {
        $color = Str::of($color)->squish()->lower()->toString();

        return preg_match('/^#[0-9a-f]{6}$/', $color) === 1 ? $color : '#64748b';
    }

    public function slugFor(string $label): string
    {
        $slug = Str::of($label)
            ->slug()
            ->limit(80, '')
            ->toString();

        return $slug === '' ? 'category' : $slug;
    }

    private function nextSortOrder(): int
    {
        $maxSortOrder = MobileLocalCategory::query()->max('sort_order');

        return is_numeric($maxSortOrder) ? ((int) $maxSortOrder) + 10 : 10;
    }

    private function uniqueSlug(string $label, int|string|null $ignoreCategoryId = null): string
    {
        $baseSlug = $this->slugFor($label);
        $slug = $baseSlug;
        $suffix = 2;

        while ($this->slugExists($slug, $ignoreCategoryId)) {
            $slug = Str::of($baseSlug)
                ->limit(76, '')
                ->append("-{$suffix}")
                ->toString();
            $suffix++;
        }

        return $slug;
    }

    private function slugExists(string $slug, int|string|null $ignoreCategoryId = null): bool
    {
        return MobileLocalCategory::query()
            ->where('slug', $slug)
            ->when(
                $ignoreCategoryId !== null,
                fn ($query) => $query->whereKeyNot($ignoreCategoryId),
            )
            ->exists();
    }
}
