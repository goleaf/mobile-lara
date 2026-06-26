<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="saveCategory,editCategory,deleteCategory,reorderPlaceholder,refreshCategories" message="Updating categories..." />

    <x-mobile.page-header
        title="Record categories"
        description="Manage local labels used by generic records."
        :back-href="route('mobile.records.index')"
    >
        <x-slot:action>
            <x-mobile.badge variant="neutral">
                {{ $categoryCount }} total
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    @if (! $storageAvailable)
        <x-mobile.error-state
            title="Categories unavailable"
            :message="$storageError ?: 'Run the mobile local storage migrations before managing categories.'"
        >
            <x-slot:action>
                <x-mobile.button wire:click="refreshCategories" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @else
        <x-mobile.card
            :title="$editing ? 'Edit category' : 'Create category'"
            description="Categories keep records scannable in the local-first mobile flow."
        >
            <form wire:submit="saveCategory" class="grid gap-4">
                <x-mobile.input
                    name="label"
                    label="Label"
                    placeholder="Field visit"
                    hint="Required, up to 80 characters."
                    wire:model.live="label"
                />

                <div class="grid grid-cols-[1fr_3rem] items-end gap-3">
                    <x-mobile.input
                        name="color"
                        label="Color"
                        placeholder="#059669"
                        hint="Use a six-digit hex color."
                        wire:model.live="color"
                    />

                    <div
                        class="mb-7 size-12 rounded-lg border border-app-line shadow-sm "
                        style="background-color: {{ $colorPreview }}"
                        aria-hidden="true"
                    ></div>
                </div>

                <x-mobile.input
                    name="sortOrder"
                    label="Sort order"
                    type="number"
                    min="0"
                    max="9999"
                    hint="Optional numeric position. Drag reorder is a placeholder for now."
                    wire:model.live="sortOrder"
                />

                <div class="grid gap-3 sm:grid-cols-2">
                    <x-mobile.submit-button
                        target="saveCategory"
                        variant="accent"
                        size="lg"
                        :loading-label="$editing ? 'Saving category' : 'Creating category'"
                    >
                        {{ $editing ? 'Save category' : 'Create category' }}
                    </x-mobile.submit-button>

                    @if ($editing)
                        <x-mobile.button wire:click="cancelEdit" variant="secondary" size="lg" full>
                            Cancel edit
                        </x-mobile.button>
                    @else
                        <x-mobile.button wire:click="reorderPlaceholder" variant="secondary" size="lg" full>
                            Reorder placeholder
                        </x-mobile.button>
                    @endif
                </div>
            </form>
        </x-mobile.card>

        <x-mobile.card title="Category summary" description="Counts are calculated in one local query with record totals attached.">
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Categories</p>
                    <p class="mt-2 text-2xl font-semibold tracking-normal text-app-ink ">{{ $categoryCount }}</p>
                    <p class="mt-1 text-xs font-medium text-app-muted ">Local labels</p>
                </div>

                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Records</p>
                    <p class="mt-2 text-2xl font-semibold tracking-normal text-app-ink ">{{ $recordCount }}</p>
                    <p class="mt-1 text-xs font-medium text-app-muted ">Assigned locally</p>
                </div>
            </div>
        </x-mobile.card>

        <x-mobile.card title="Categories" description="Edit labels, colors, and delete categories from local storage.">
            <div class="grid gap-3">
                @forelse ($categories as $category)
                    <article
                        wire:key="record-category-{{ $category->id }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4  "
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <span
                                    class="mt-1 size-4 shrink-0 rounded-full border border-black/10 "
                                    style="background-color: {{ $category->color }}"
                                    aria-hidden="true"
                                ></span>

                                <div class="min-w-0">
                                    <p class="break-words text-base font-semibold text-app-ink ">{{ $category->label }}</p>
                                    <p class="mt-1 break-all text-xs font-medium text-app-muted ">{{ $category->slug }}</p>
                                </div>
                            </div>

                            <x-mobile.badge variant="neutral" size="sm">
                                #{{ $category->sort_order }}
                            </x-mobile.badge>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <x-mobile.badge variant="accent" size="sm" dot>
                                {{ $category->color }}
                            </x-mobile.badge>

                            <x-mobile.badge variant="neutral" size="sm">
                                {{ $category->recordCountLabel() }}
                            </x-mobile.badge>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <x-mobile.button
                                wire:click="editCategory({{ $category->id }})"
                                wire:loading.attr="disabled"
                                wire:target="editCategory({{ $category->id }})"
                                variant="secondary"
                                size="sm"
                                full
                            >
                                Edit
                            </x-mobile.button>

                            <x-mobile.button
                                wire:click="reorderPlaceholder"
                                variant="ghost"
                                size="sm"
                                full
                            >
                                Reorder
                            </x-mobile.button>

                            <x-mobile.button
                                wire:click="deleteCategory({{ $category->id }})"
                                wire:confirm="Delete this category and leave matching records uncategorized?"
                                wire:loading.attr="disabled"
                                wire:target="deleteCategory({{ $category->id }})"
                                variant="danger"
                                size="sm"
                                full
                            >
                                Delete
                            </x-mobile.button>
                        </div>
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No categories"
                        description="Create a category to organize records."
                    />
                @endforelse
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="refreshCategories" variant="secondary" full>
                    Refresh categories
                </x-mobile.button>
            </x-slot:footer>
        </x-mobile.card>
    @endif
</section>
