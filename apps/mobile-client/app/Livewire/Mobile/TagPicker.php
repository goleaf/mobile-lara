<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalTag;
use App\Services\MobileLocal\TagRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Livewire\Component;

class TagPicker extends Component
{
    use DispatchesToasts;

    public string $context = 'tags';

    public string $label = 'Tags';

    public string $placeholder = 'Search or create tag';

    public string $search = '';

    /**
     * @var list<array{name: string, slug: string}>
     */
    public array $selected = [];

    public bool $allowCreate = true;

    public bool $multiple = true;

    public ?string $storageError = null;

    private TagRepository $tags;

    public function boot(TagRepository $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @param  list<string>|list<array{name: string, slug?: string}>  $selected
     */
    public function mount(
        array $selected = [],
        string $context = 'tags',
        string $label = 'Tags',
        string $placeholder = 'Search or create tag',
        bool $allowCreate = true,
        bool $multiple = true,
    ): void {
        $this->context = $context;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->allowCreate = $allowCreate;
        $this->multiple = $multiple;
        $this->selected = $this->selectedRows($selected);
    }

    public function addTag(int $tagId): void
    {
        try {
            $tag = $this->tags->find($tagId);
        } catch (ModelNotFoundException) {
            $this->toastWarning('That tag is no longer available locally.', 'Tag unavailable');

            return;
        } catch (QueryException) {
            $this->storageError = 'Tag storage is unavailable. Run the mobile local migrations first.';
            $this->toastError($this->storageError, 'Tag unavailable');

            return;
        }

        $this->selectTag($tag->name, $tag->slug);
        $this->search = '';
    }

    public function createTag(): void
    {
        $validated = $this->validate([
            'search' => ['required', 'string', 'max:40'],
        ]);

        try {
            $tag = $this->tags->findOrCreate($validated['search']);
        } catch (QueryException) {
            $this->storageError = 'Tag storage is unavailable. Run the mobile local migrations first.';
            $this->toastError($this->storageError, 'Tag not created');

            return;
        }

        $this->selectTag($tag->name, $tag->slug);
        $this->search = '';
        $this->toastSuccess('Tag selected locally.', 'Tag ready');
    }

    public function removeTag(string $slug): void
    {
        $slug = $this->tags->slugFor($slug);

        $this->selected = collect($this->selected)
            ->reject(fn (array $tag): bool => ($tag['slug'] ?? '') === $slug)
            ->values()
            ->all();

        $this->dispatchSelection();
    }

    public function clearTags(): void
    {
        $this->selected = [];
        $this->dispatchSelection();
    }

    public function render(): View
    {
        $results = $this->searchResults();

        return view('livewire.mobile.tag-picker', [
            'canCreateTag' => $this->canCreateTag($results),
            'results' => $this->tagRows($results),
            'selectedTags' => $this->selected,
        ]);
    }

    private function selectTag(string $name, string $slug): void
    {
        $row = [
            'name' => $this->tags->normalizeName($name),
            'slug' => $this->tags->slugFor($slug),
        ];

        if ($row['name'] === '' || $row['slug'] === '') {
            return;
        }

        $selected = $this->multiple ? $this->selected : [];

        $selected[] = $row;

        $this->selected = collect($selected)
            ->unique('slug')
            ->values()
            ->all();

        $this->dispatchSelection();
    }

    private function dispatchSelection(): void
    {
        $this->dispatch(
            'tag-picker-updated',
            context: $this->context,
            tags: collect($this->selected)->pluck('name')->values()->all(),
            slugs: collect($this->selected)->pluck('slug')->values()->all(),
        );
    }

    /**
     * @return Collection<int, MobileLocalTag>
     */
    private function searchResults(): Collection
    {
        try {
            return $this->tags->search($this->search, 12)
                ->reject(fn (MobileLocalTag $tag): bool => in_array($tag->slug, $this->selectedSlugs(), true))
                ->values();
        } catch (QueryException) {
            $this->storageError = 'Tag storage is unavailable. Run the mobile local migrations first.';

            return new Collection;
        }
    }

    /**
     * @param  Collection<int, MobileLocalTag>  $tags
     * @return list<array{id: int, name: string, slug: string}>
     */
    private function tagRows(Collection $tags): array
    {
        return $tags
            ->map(fn (MobileLocalTag $tag): array => [
                'id' => (int) $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  list<string>|list<array{name: string, slug?: string}>  $selected
     * @return list<array{name: string, slug: string}>
     */
    private function selectedRows(array $selected): array
    {
        return collect($selected)
            ->map(function (mixed $tag): ?array {
                if (is_string($tag)) {
                    $name = $this->tags->normalizeName($tag);

                    return $name === '' ? null : [
                        'name' => $name,
                        'slug' => $this->tags->slugFor($name),
                    ];
                }

                if (! is_array($tag) || ! is_string($tag['name'] ?? null)) {
                    return null;
                }

                $name = $this->tags->normalizeName($tag['name']);

                return $name === '' ? null : [
                    'name' => $name,
                    'slug' => is_string($tag['slug'] ?? null) && $tag['slug'] !== ''
                        ? $this->tags->slugFor($tag['slug'])
                        : $this->tags->slugFor($name),
                ];
            })
            ->filter()
            ->unique('slug')
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    private function selectedSlugs(): array
    {
        return collect($this->selected)
            ->pluck('slug')
            ->filter(fn (mixed $slug): bool => is_string($slug) && $slug !== '')
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, MobileLocalTag>  $results
     */
    private function canCreateTag(Collection $results): bool
    {
        $name = $this->tags->normalizeName($this->search);
        $slug = $this->tags->slugFor($name);

        return $this->allowCreate
            && $name !== ''
            && ! in_array($slug, $this->selectedSlugs(), true)
            && ! $results->contains(fn (MobileLocalTag $tag): bool => $tag->slug === $slug);
    }
}
