<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Records Management"
        description="Tenant-scoped mobile records, categories, tags, notes, attachments, activity, and reversible archive state."
    />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Total</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['total'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Active</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['active'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Review</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700 dark:text-amber-300">{{ $summary['review'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Archived</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['archived'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(30rem,0.85fr)]">
        <div class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_13rem_12rem_12rem_auto]">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Search
                    <input
                        type="search"
                        wire:model.blur="search"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Tenant
                    <select
                        wire:model.change="tenantId"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        <option value="">All tenants</option>

                        @foreach ($tenants as $tenant)
                            <option wire:key="records-tenant-filter-{{ $tenant->id }}" value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Status
                    <select
                        wire:model.change="status"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        <option value="">All statuses</option>

                        @foreach ($statusOptions as $value => $label)
                            <option wire:key="records-status-filter-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Archive
                    <select
                        wire:model.change="archived"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        @foreach ($archiveOptions as $value => $label)
                            <option wire:key="records-archive-filter-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="grid content-end">
                    <button
                        type="button"
                        wire:click="createRecord"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
                    >
                        New record
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-normal text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                            <tr>
                                <th class="px-4 py-3">Record</th>
                                <th class="px-4 py-3">Tenant</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">State</th>
                                <th class="px-4 py-3">Counts</th>
                                <th class="px-4 py-3 text-right">Review</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($records as $record)
                                <tr wire:key="tenant-record-row-{{ $record->id }}" class="align-top">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">{{ $record->title }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $record->public_id }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">{{ $record->tenant?->name ?: 'Unknown tenant' }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $record->tenant?->slug ?: 'none' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">{{ $record->category?->name ?: 'Uncategorized' }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $record->tags->pluck('name')->take(3)->implode(', ') ?: 'No tags' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.status-badge :tone="$this->statusTone($record->status)">
                                            {{ str($record->status)->replace('_', ' ')->title() }}
                                        </x-admin.status-badge>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <x-admin.status-badge :tone="$this->priorityTone($record->priority)">
                                                {{ str($record->priority)->title() }}
                                            </x-admin.status-badge>

                                            @if ($record->isArchived())
                                                <x-admin.status-badge tone="neutral">Archived</x-admin.status-badge>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        <p>{{ $this->countLabel($record->notes_count, 'note') }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $this->countLabel($record->attachments_count, 'attachment') }} / {{ $this->countLabel($record->activities_count, 'event') }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            type="button"
                                            wire:click="selectRecord({{ $record->id }})"
                                            class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                        >
                                            Review
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        No records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $records->links() }}
            </div>
        </div>

        <aside class="grid content-start gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            @if ($isCreating || $editingRecordId !== null)
                <form wire:submit="save" class="grid gap-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">{{ $editingRecordId === null ? 'Create record' : 'Edit record' }}</h2>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Admin changes are audited and replay to mobile through the API and sync contract.</p>
                        </div>

                        <button
                            type="button"
                            wire:click="clearPanel"
                            class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        >
                            Close
                        </button>
                    </div>

                    <div class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                        <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Tenant
                            <select
                                wire:model.change="form.tenant_id"
                                @disabled($editingRecordId !== null)
                                class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 disabled:bg-zinc-100 disabled:text-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800 dark:disabled:bg-zinc-900"
                            >
                                <option value="">Choose tenant</option>

                                @foreach ($tenants as $tenant)
                                    <option wire:key="records-form-tenant-{{ $tenant->id }}" value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                @endforeach
                            </select>
                            @error('form.tenant_id')
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Title
                            <input
                                type="text"
                                wire:model.blur="form.title"
                                class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                            >
                            @error('form.title')
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Description
                            <textarea
                                wire:model.blur="form.description"
                                rows="4"
                                class="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                            ></textarea>
                            @error('form.description')
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </label>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Status
                                <select
                                    wire:model.change="form.status"
                                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                                >
                                    @foreach ($statusOptions as $value => $label)
                                        <option wire:key="records-form-status-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.status')
                                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Priority
                                <select
                                    wire:model.change="form.priority"
                                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                                >
                                    @foreach ($priorityOptions as $value => $label)
                                        <option wire:key="records-form-priority-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.priority')
                                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                        <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Category
                            <input
                                type="text"
                                wire:model.blur="form.category_name"
                                class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                            >
                            @error('form.category_name')
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Tags
                            <input
                                type="text"
                                wire:model.blur="form.tags"
                                class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                            >
                            @error('form.tags')
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Append note
                            <textarea
                                wire:model.blur="form.note"
                                rows="4"
                                class="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                            ></textarea>
                            @error('form.note')
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </label>

                        <button
                            type="submit"
                            class="inline-flex min-h-11 items-center justify-center rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
                        >
                            Save record
                        </button>
                    </div>
                </form>
            @elseif ($selectedRecord)
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">Record detail</h2>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedRecord->public_id }}</p>
                    </div>

                    <button
                        type="button"
                        wire:click="clearPanel"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        Close
                    </button>
                </div>

                <div class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-100">{{ $selectedRecord->title }}</h3>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedRecord->tenant?->name ?: 'Unknown tenant' }}</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <x-admin.status-badge :tone="$this->statusTone($selectedRecord->status)">
                                {{ str($selectedRecord->status)->replace('_', ' ')->title() }}
                            </x-admin.status-badge>
                            <x-admin.status-badge :tone="$this->priorityTone($selectedRecord->priority)">
                                {{ str($selectedRecord->priority)->title() }}
                            </x-admin.status-badge>
                        </div>
                    </div>

                    @if ($selectedRecord->description)
                        <p class="break-words text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $selectedRecord->description }}</p>
                    @endif

                    <dl class="grid gap-3 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Category</dt>
                            <dd class="mt-1 text-zinc-950 dark:text-zinc-100">{{ $selectedRecord->category?->name ?: 'Uncategorized' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Updated by</dt>
                            <dd class="mt-1 text-zinc-950 dark:text-zinc-100">{{ $selectedRecord->updater?->name ?: $selectedRecord->creator?->name ?: 'Unknown' }}</dd>
                        </div>
                    </dl>

                    <div class="flex flex-wrap gap-2">
                        @forelse ($selectedRecord->tags as $tag)
                            <span wire:key="selected-record-tag-{{ $tag->id }}" class="rounded-full border border-zinc-200 px-2.5 py-1 text-xs font-medium text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                                {{ $tag->name }}
                            </span>
                        @empty
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">No tags assigned.</span>
                        @endforelse
                    </div>

                    <div class="grid gap-2 sm:grid-cols-3">
                        <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Notes</p>
                            <p class="mt-1 text-lg font-semibold text-zinc-950 dark:text-zinc-100">{{ $selectedRecord->notes_count }}</p>
                        </div>
                        <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Attachments</p>
                            <p class="mt-1 text-lg font-semibold text-zinc-950 dark:text-zinc-100">{{ $selectedRecord->attachments_count }}</p>
                        </div>
                        <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Activity</p>
                            <p class="mt-1 text-lg font-semibold text-zinc-950 dark:text-zinc-100">{{ $selectedRecord->activities_count }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="editRecord({{ $selectedRecord->id }})"
                            class="inline-flex min-h-10 items-center justify-center rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
                        >
                            Edit
                        </button>

                        @if ($selectedRecord->isArchived())
                            <button
                                type="button"
                                wire:click="restoreSelected"
                                class="inline-flex min-h-10 items-center justify-center rounded-lg border border-zinc-300 px-4 text-sm font-semibold text-zinc-800 transition hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                            >
                                Restore
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="archiveSelected"
                                class="inline-flex min-h-10 items-center justify-center rounded-lg border border-zinc-300 px-4 text-sm font-semibold text-zinc-800 transition hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                            >
                                Archive
                            </button>
                        @endif
                    </div>
                </div>

                <div class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-100">Notes</h3>
                    <div class="grid max-h-72 gap-3 overflow-auto pr-1">
                        @forelse ($selectedRecord->notes as $note)
                            <article wire:key="selected-record-note-{{ $note->id }}" class="grid gap-2 rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-800">
                                <p class="font-semibold text-zinc-950 dark:text-zinc-100">{{ $note->author?->name ?: 'Unknown author' }}</p>
                                <p class="break-words leading-6 text-zinc-600 dark:text-zinc-300">{{ $note->body }}</p>
                            </article>
                        @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">No notes found.</p>
                        @endforelse
                    </div>
                </div>

                <div class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-100">Attachments</h3>
                    <div class="grid gap-2">
                        @forelse ($selectedRecord->attachments as $attachment)
                            <div wire:key="selected-record-attachment-{{ $attachment->id }}" class="rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-800">
                                <p class="font-semibold text-zinc-950 dark:text-zinc-100">{{ $attachment->file_name }}</p>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $attachment->mime_type ?: 'unknown type' }} / {{ $attachment->status }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">No attachments found.</p>
                        @endforelse
                    </div>
                </div>

                <div class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-100">Activity</h3>
                    <div class="grid max-h-72 gap-2 overflow-auto pr-1">
                        @forelse ($selectedRecord->activities as $activity)
                            <div wire:key="selected-record-activity-{{ $activity->id }}" class="rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-800">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="font-semibold text-zinc-950 dark:text-zinc-100">{{ $activity->action }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $activity->created_at?->diffForHumans() }}</p>
                                </div>
                                <p class="mt-1 break-words text-zinc-600 dark:text-zinc-300">{{ $activity->description }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">No activity found.</p>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="rounded-lg border border-zinc-200 p-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                    Select a record to inspect tenant scope, timeline, notes, attachments, and reversible archive state.
                </div>
            @endif
        </aside>
    </div>
</section>
