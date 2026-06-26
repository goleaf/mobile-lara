<div class="grid gap-3">
    <div class="grid gap-2">
        <label for="tag-picker-{{ $context }}" class="text-sm font-medium text-app-ink ">
            {{ $label }}
        </label>

        <div class="flex gap-2">
            <input
                id="tag-picker-{{ $context }}"
                type="text"
                wire:model.live.debounce.250ms="search"
                placeholder="{{ $placeholder }}"
                aria-invalid="{{ $errors->has('search') ? 'true' : 'false' }}"
                class="min-h-12 w-full rounded-lg border border-app-line bg-white px-3 text-base text-app-ink shadow-sm outline-none transition placeholder:text-app-muted/70 focus:border-app-accent focus:ring-2 focus:ring-app-accent/20      "
            >

            @if ($canCreateTag)
                <x-mobile.button
                    wire:click="createTag"
                    wire:loading.attr="disabled"
                    wire:target="createTag"
                    variant="secondary"
                    class="shrink-0"
                >
                    <span wire:loading.remove wire:target="createTag">Create</span>
                    <span wire:loading wire:target="createTag">Creating</span>
                </x-mobile.button>
            @endif
        </div>

        @error('search')
            <p class="text-sm font-medium text-red-600 ">{{ $message }}</p>
        @enderror

        @if ($storageError)
            <p class="text-sm font-medium text-red-600 ">{{ $storageError }}</p>
        @endif
    </div>

    <div class="flex flex-wrap gap-2">
        @forelse ($selectedTags as $tag)
            <span
                wire:key="tag-picker-{{ $context }}-selected-{{ $tag['slug'] }}"
                class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-app-line bg-app-surface px-3 text-sm font-semibold text-app-ink   "
            >
                <span>{{ $tag['name'] }}</span>
                <button
                    type="button"
                    wire:click="removeTag('{{ $tag['slug'] }}')"
                    class="inline-flex size-6 items-center justify-center rounded-full text-app-muted transition hover:bg-app-line hover:text-app-ink   "
                    aria-label="Remove {{ $tag['name'] }}"
                >
                    &times;
                </button>
            </span>
        @empty
            <x-mobile.badge variant="neutral" size="sm">
                No tags selected
            </x-mobile.badge>
        @endforelse
    </div>

    @if ($selectedTags !== [])
        <div>
            <button
                type="button"
                wire:click="clearTags"
                class="text-sm font-semibold text-app-muted underline-offset-4 hover:text-app-ink hover:underline  "
            >
                Clear tags
            </button>
        </div>
    @endif

    <div class="grid gap-2">
        @forelse ($results as $tag)
            <button
                type="button"
                wire:key="tag-picker-{{ $context }}-result-{{ $tag['id'] }}"
                wire:click="addTag({{ $tag['id'] }})"
                class="flex min-h-11 items-center justify-between gap-3 rounded-lg border border-app-line bg-app-bg px-3 text-left text-sm font-semibold text-app-ink transition hover:bg-app-surface    "
            >
                <span>{{ $tag['name'] }}</span>
                <span class="text-xs font-medium text-app-muted ">Add</span>
            </button>
        @empty
            @if (trim($search) !== '' && ! $canCreateTag && ! $storageError)
                <p class="text-sm text-app-muted ">No matching tags</p>
            @endif
        @endforelse
    </div>
</div>
