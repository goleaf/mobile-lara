<section @class([
    'safe-x flex min-h-full flex-col gap-5 pt-6',
    'pb-56' => $hasSelection,
    'safe-pb pb-6' => ! $hasSelection,
])>
    <x-mobile.loading-state target="refreshRecords,setFilter,archiveRecord,restoreRecord,deleteRecord,selectAllVisible,archiveSelected,deleteSelected,changeSelectedStatus,changeSelectedCategory,clearSelection,search,clearSearch,clearTagFilter" message="Updating records..." />

    <x-mobile.page-header
        title="Records"
        description="Local-first generic records stored on this device."
        :back-href="route('mobile.dashboard')"
    >
        <x-slot:action>
            <x-mobile.badge variant="neutral">
                {{ $recordsCount }} shown
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    @if (! $storageAvailable)
        <x-mobile.error-state
            title="Records unavailable"
            message="Run the mobile local storage migrations before viewing local records."
        >
            <x-slot:action>
                <x-mobile.button wire:click="refreshRecords" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @else
        <x-mobile.card title="Record summary" description="Local totals for current, archived, and completed records.">
            <div class="grid grid-cols-2 gap-3">
                @forelse ($metrics as $metric)
                    <div
                        wire:key="records-metric-{{ $metric['label'] }}"
                        class="rounded-lg border border-app-line bg-app-bg p-4  "
                    >
                        <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">
                            {{ $metric['label'] }}
                        </p>
                        <p class="mt-2 text-2xl font-semibold tracking-normal text-app-ink ">
                            {{ $metric['value'] }}
                        </p>
                        <p class="mt-1 text-xs font-medium text-app-muted ">
                            {{ $metric['description'] }}
                        </p>
                    </div>
                @empty
                    <x-mobile.empty-state
                        title="No metrics"
                        description="Record totals will appear after local storage is available."
                    />
                @endforelse
            </div>
        </x-mobile.card>

        <x-mobile.card title="Find records" description="Filter by archive state, status, priority, tags, title, notes, or description.">
            <div class="grid gap-4">
                <x-mobile.input
                    wire:model.live.debounce.300ms="search"
                    name="search"
                    label="Search records"
                    placeholder="Search title, tag, note, or status"
                />

                <livewire:mobile.tag-picker
                    :context="$tagFilterContext"
                    label="Filter by tag"
                    placeholder="Search tags to filter records"
                    :selected="$tagFilterValues"
                    :wire:key="$tagFilterContext"
                />

                <div class="flex gap-2 overflow-x-auto pb-1">
                    @forelse ($filters as $filterOption)
                        <button
                            type="button"
                            wire:key="records-filter-{{ $filterOption['key'] }}"
                            wire:click="setFilter('{{ $filterOption['key'] }}')"
                            @class([
                                'inline-flex min-h-10 shrink-0 items-center gap-2 rounded-lg border px-3 text-sm font-semibold transition',
                                'border-app-ink bg-app-ink text-white   ' => $filterOption['active'],
                                'border-app-line bg-app-bg text-app-ink hover:bg-app-surface    ' => ! $filterOption['active'],
                            ])
                        >
                            <span>{{ $filterOption['label'] }}</span>
                            <span class="rounded-full bg-current/10 px-1.5 py-0.5 text-[11px]">
                                {{ $filterOption['count'] }}
                            </span>
                        </button>
                    @empty
                        <span class="text-sm font-medium text-app-muted ">No filters available</span>
                    @endforelse
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    <x-mobile.button wire:click="clearSearch" variant="secondary" full>
                        Clear search
                    </x-mobile.button>

                    <x-mobile.button wire:click="clearTagFilter" variant="secondary" full>
                        Clear tag
                    </x-mobile.button>

                    <div class="grid grid-cols-2 gap-2">
                        <a
                            href="{{ route('mobile.records.categories') }}"
                            wire:navigate
                            class="inline-flex min-h-12 items-center justify-center rounded-lg border border-app-line bg-app-surface px-3 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg    "
                        >
                            Categories
                        </a>

                        @if ($recordActionPermissions['create'])
                            <a
                                href="{{ route('mobile.records.create') }}"
                                wire:navigate
                                class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-accent px-3 text-sm font-semibold text-app-accent-ink shadow-sm transition hover:bg-app-accent/90 active:bg-app-accent/80   "
                            >
                                New record
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </x-mobile.card>

        <x-mobile.card title="Records table" description="Newest local changes first.">
            <x-slot:action>
                <div class="flex flex-wrap justify-end gap-2">
                    @if ($selectedCount > 0)
                        <x-mobile.badge variant="accent" size="sm">
                            {{ $selectedCount }} selected
                        </x-mobile.badge>
                    @endif

                    <x-mobile.button
                        wire:click="selectAllVisible"
                        wire:loading.attr="disabled"
                        wire:target="selectAllVisible"
                        variant="secondary"
                        size="sm"
                    >
                        {{ $allVisibleSelected ? 'All selected' : 'Select all' }}
                    </x-mobile.button>
                </div>
            </x-slot:action>

            <div class="grid gap-3">
                @forelse ($records as $record)
                    <article
                        wire:key="record-row-{{ $record->id }}"
                        @class([
                            'grid gap-3 rounded-lg border p-4 transition',
                            'border-app-accent bg-app-accent/10  ' => $selectedRecordKeys[$record->id] ?? false,
                            'border-app-line bg-app-bg  ' => ! ($selectedRecordKeys[$record->id] ?? false),
                        ])
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <label class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-app-line bg-app-surface  ">
                                    <input
                                        type="checkbox"
                                        value="{{ $record->id }}"
                                        wire:model.live="selectedRecordIds"
                                        class="size-5 rounded border-app-line text-app-accent focus:ring-app-accent/30  "
                                    >
                                    <span class="sr-only">Select {{ $record->title }}</span>
                                </label>

                                <div class="min-w-0">
                                    <a
                                        href="{{ route('mobile.records.show', $record) }}"
                                        wire:navigate
                                        class="block break-words text-base font-semibold text-app-ink underline-offset-4 hover:underline "
                                    >
                                        {{ $record->title }}
                                    </a>
                                    <p class="mt-1 text-sm leading-5 text-app-muted ">
                                        Updated {{ $record->updated_at?->diffForHumans() ?? 'time unknown' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <x-mobile.badge :variant="$record->statusVariant()" dot>
                                    {{ $record->statusLabel() }}
                                </x-mobile.badge>

                                <x-mobile.badge :variant="$record->archiveVariant()" size="sm">
                                    {{ $record->archiveLabel() }}
                                </x-mobile.badge>
                            </div>
                        </div>

                        <p class="text-sm leading-6 text-app-muted ">
                            {{ $record->descriptionPreview() }}
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <x-mobile.badge :variant="$record->priorityVariant()" size="sm" dot>
                                {{ $record->priorityLabel() }}
                            </x-mobile.badge>

                            @if ($record->due_at)
                                <x-mobile.badge variant="neutral" size="sm">
                                    Due {{ $record->due_at->format('M j') }}
                                </x-mobile.badge>
                            @endif

                            @if ($record->categoryLabel())
                                <x-mobile.badge variant="neutral" size="sm">
                                    {{ $record->categoryLabel() }}
                                </x-mobile.badge>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @forelse ($record->tagList() as $tag)
                                <x-mobile.badge wire:key="record-{{ $record->id }}-tag-{{ $tag }}" variant="neutral" size="sm">
                                    {{ $tag }}
                                </x-mobile.badge>
                            @empty
                                <x-mobile.badge variant="neutral" size="sm">
                                    No tags
                                </x-mobile.badge>
                            @endforelse
                        </div>

                        <div class="rounded-lg border border-dashed border-app-line bg-app-surface p-3  ">
                            <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Notes</p>
                            <p class="mt-1 text-sm leading-5 text-app-ink ">{{ $record->notesPreview() }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            <a
                                href="{{ route('mobile.records.show', $record) }}"
                                wire:navigate
                                class="inline-flex min-h-10 items-center justify-center rounded-lg border border-app-line bg-app-surface px-3 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg    "
                            >
                                Detail
                            </a>

                            @if ($recordActionPermissions['update'])
                                <a
                                    href="{{ route('mobile.records.edit', $record) }}"
                                    wire:navigate
                                    class="inline-flex min-h-10 items-center justify-center rounded-lg border border-app-line bg-app-surface px-3 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg    "
                                >
                                    Edit
                                </a>
                            @endif

                            @if ($recordActionPermissions['archive'] && $record->isArchived())
                                <x-mobile.button
                                    wire:click="restoreRecord({{ $record->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="restoreRecord({{ $record->id }})"
                                    variant="accent"
                                    size="sm"
                                    full
                                >
                                    <span wire:loading.remove wire:target="restoreRecord({{ $record->id }})">Restore</span>
                                    <span wire:loading wire:target="restoreRecord({{ $record->id }})">Restoring</span>
                                </x-mobile.button>
                            @elseif ($recordActionPermissions['archive'])
                                <x-mobile.button
                                    wire:click="archiveRecord({{ $record->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="archiveRecord({{ $record->id }})"
                                    variant="secondary"
                                    size="sm"
                                    full
                                >
                                    <span wire:loading.remove wire:target="archiveRecord({{ $record->id }})">Archive</span>
                                    <span wire:loading wire:target="archiveRecord({{ $record->id }})">Archiving</span>
                                </x-mobile.button>
                            @endif

                            @if ($recordActionPermissions['delete'])
                                <x-mobile.button
                                    wire:click="deleteRecord({{ $record->id }})"
                                    wire:confirm="Delete this record from local storage?"
                                    wire:loading.attr="disabled"
                                    wire:target="deleteRecord({{ $record->id }})"
                                    variant="danger"
                                    size="sm"
                                    full
                                >
                                    <span wire:loading.remove wire:target="deleteRecord({{ $record->id }})">Delete</span>
                                    <span wire:loading wire:target="deleteRecord({{ $record->id }})">Deleting</span>
                                </x-mobile.button>
                            @endif
                        </div>
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No records"
                        description="Create a local record to start tracking generic offline-first data."
                    />
                @endforelse
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="refreshRecords" variant="secondary" full>
                    Refresh records
                </x-mobile.button>
            </x-slot:footer>
        </x-mobile.card>

        @if ($hasSelection)
            <div class="fixed inset-x-0 bottom-0 z-40 border-t border-app-line bg-app-surface/95 shadow-2xl backdrop-blur  ">
                <div class="safe-x mx-auto grid max-w-3xl gap-3 px-4 pt-3 pb-[calc(env(safe-area-inset-bottom)+0.75rem)]">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-app-ink ">Bulk actions</p>
                            <p class="text-xs font-medium text-app-muted ">{{ $selectedCount }} selected</p>
                        </div>

                        <x-mobile.button wire:click="clearSelection" variant="ghost" size="sm">
                            Clear
                        </x-mobile.button>
                    </div>

                    @if ($recordActionPermissions['archive'] || $recordActionPermissions['delete'])
                        <div class="grid grid-cols-2 gap-2">
                            @if ($recordActionPermissions['archive'])
                                <x-mobile.button
                                    wire:click="archiveSelected"
                                    wire:loading.attr="disabled"
                                    wire:target="archiveSelected"
                                    variant="secondary"
                                    size="sm"
                                    full
                                >
                                    Archive selected
                                </x-mobile.button>
                            @endif

                            @if ($recordActionPermissions['delete'])
                                <x-mobile.button
                                    wire:click="deleteSelected"
                                    wire:confirm="Delete selected records from local storage?"
                                    wire:loading.attr="disabled"
                                    wire:target="deleteSelected"
                                    variant="danger"
                                    size="sm"
                                    full
                                >
                                    Delete selected
                                </x-mobile.button>
                            @endif
                        </div>
                    @endif

                    @if ($recordActionPermissions['update'])
                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2 sm:grid-cols-[1fr_auto]">
                                <label class="sr-only" for="bulkStatus">Change status</label>
                                <select
                                    id="bulkStatus"
                                    wire:model.live="bulkStatus"
                                    class="min-h-11 rounded-lg border border-app-line bg-app-surface px-3.5 text-sm font-semibold text-app-ink shadow-[0_12px_24px_-22px_rgba(15,23,42,0.55)] focus:border-app-accent focus:bg-white focus:ring-2 focus:ring-app-accent/20"
                                >
                                    @foreach ($bulkStatusOptions as $statusValue => $statusLabel)
                                        <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                                    @endforeach
                                </select>

                                <x-mobile.button
                                    wire:click="changeSelectedStatus"
                                    wire:loading.attr="disabled"
                                    wire:target="changeSelectedStatus"
                                    variant="accent"
                                    size="sm"
                                >
                                    Change status
                                </x-mobile.button>
                            </div>

                            <div class="grid gap-2 sm:grid-cols-[1fr_auto]">
                                <label class="sr-only" for="bulkCategoryId">Change category</label>
                                <select
                                    id="bulkCategoryId"
                                    wire:model.live="bulkCategoryId"
                                    class="min-h-11 rounded-lg border border-app-line bg-app-surface px-3.5 text-sm font-semibold text-app-ink shadow-[0_12px_24px_-22px_rgba(15,23,42,0.55)] focus:border-app-accent focus:bg-white focus:ring-2 focus:ring-app-accent/20"
                                >
                                    @foreach ($bulkCategoryOptions as $categoryValue => $categoryLabel)
                                        <option value="{{ $categoryValue }}">{{ $categoryLabel }}</option>
                                    @endforeach
                                </select>

                                <x-mobile.button
                                    wire:click="changeSelectedCategory"
                                    wire:loading.attr="disabled"
                                    wire:target="changeSelectedCategory"
                                    variant="secondary"
                                    size="sm"
                                >
                                    Change category
                                </x-mobile.button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif
</section>
