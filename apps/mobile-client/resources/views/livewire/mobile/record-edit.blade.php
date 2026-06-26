<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Edit record"
        :description="$record->title"
        :back-href="route('mobile.records.show', $record)"
    />

    @if (! $storageAvailable || $storageError)
        <x-mobile.error-state
            title="Record storage unavailable"
            :message="$storageError ?: 'Run the mobile local storage migrations before editing local records.'"
        />
    @endif

    @if (! $recordActionPermissions['update'])
        <x-mobile.error-state
            title="Record editing disabled"
            message="Your current workspace role cannot update this local record from this device."
        />
    @endif

    <x-mobile.card title="Record details" description="Update the local copy and mark it pending for future sync.">
        <form wire:submit="save" class="grid gap-4">
            <div
                wire:dirty
                wire:target="title,description,status,priority,categoryId,dueAt,tags,notes,locationName,latitude,longitude"
                class="rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm leading-5 text-amber-900   "
            >
                <p class="font-semibold">Unsaved changes</p>
                <p class="mt-1">Save or save as draft before leaving this record.</p>
            </div>

            <x-mobile.input
                name="title"
                label="Title"
                hint="Required, up to 160 characters."
                wire:model.live="title"
            />

            <x-mobile.textarea
                name="description"
                label="Description"
                rows="3"
                hint="Optional short description."
                wire:model.live="description"
            />

            <x-mobile.select
                name="categoryId"
                label="Category"
                placeholder="Choose category"
                :options="$categoryOptions"
                hint="Required local category."
                wire:model.live="categoryId"
            />

            <x-mobile.select
                name="status"
                label="Status"
                :options="$statusOptions"
                wire:model.live="status"
            />

            <x-mobile.select
                name="priority"
                label="Priority"
                :options="$priorityOptions"
                wire:model.live="priority"
            />

            <x-mobile.input
                name="dueAt"
                label="Due date"
                type="datetime-local"
                hint="Optional local due date."
                wire:model.live="dueAt"
            />

            <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-app-ink ">{{ $attachmentPlaceholder['title'] }}</p>
                        <p class="mt-1 text-sm leading-5 text-app-muted ">{{ $attachmentPlaceholder['description'] }}</p>
                    </div>

                    <x-mobile.badge variant="neutral" size="sm">
                        {{ $attachmentPlaceholder['badge'] }}
                    </x-mobile.badge>
                </div>
            </div>

            <livewire:mobile.tag-picker
                :context="$tagPickerContext"
                label="Tags"
                placeholder="Search or create tag"
                :selected="$tagValues"
                :wire:key="$tagPickerContext"
            />

            <div class="grid gap-4 rounded-lg border border-app-line bg-app-bg p-4  ">
                <div>
                    <p class="text-sm font-semibold text-app-ink ">Location optional</p>
                    <p class="mt-1 text-sm leading-5 text-app-muted ">
                        Keep, change, or clear the place metadata for this local record.
                    </p>
                </div>

                <x-mobile.input
                    name="locationName"
                    label="Location label"
                    placeholder="Warehouse, client site, or area"
                    wire:model.live="locationName"
                />

                <div class="grid grid-cols-2 gap-3">
                    <x-mobile.input
                        name="latitude"
                        label="Latitude"
                        type="number"
                        step="any"
                        placeholder="54.6872"
                        wire:model.live="latitude"
                    />

                    <x-mobile.input
                        name="longitude"
                        label="Longitude"
                        type="number"
                        step="any"
                        placeholder="25.2797"
                        wire:model.live="longitude"
                    />
                </div>
            </div>

            <x-mobile.textarea
                name="notes"
                label="Notes"
                rows="5"
                hint="Optional notes, up to 5000 characters."
                wire:model.live="notes"
            />

            <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                <p class="text-sm font-semibold text-app-ink ">Archive state</p>
                <p class="mt-1 text-sm leading-5 text-app-muted ">
                    Current state: {{ $record->archiveLabel() }}. Archive and restore actions keep the record pending for sync.
                </p>
            </div>

            @if ($recordActionPermissions['update'])
                <div class="grid gap-3 sm:grid-cols-2">
                    <x-mobile.button
                        wire:click="saveAsDraft"
                        wire:loading.attr="disabled"
                        wire:target="saveAsDraft"
                        variant="secondary"
                        size="lg"
                        full
                    >
                        <span wire:loading.remove wire:target="saveAsDraft">Save as draft</span>
                        <span wire:loading wire:target="saveAsDraft">Saving draft</span>
                    </x-mobile.button>

                    <x-mobile.submit-button target="save" variant="accent" size="lg" loading-label="Saving record">
                        Save changes
                    </x-mobile.submit-button>
                </div>
            @endif

            <div class="grid gap-3 border-t border-app-line pt-4 ">
                @if ($recordActionPermissions['archive'] && $record->isArchived())
                    <x-mobile.button
                        wire:click="restoreRecord"
                        wire:loading.attr="disabled"
                        wire:target="restoreRecord"
                        variant="secondary"
                        full
                    >
                        <span wire:loading.remove wire:target="restoreRecord">Restore record</span>
                        <span wire:loading wire:target="restoreRecord">Restoring</span>
                    </x-mobile.button>
                @elseif ($recordActionPermissions['archive'])
                    <x-mobile.button
                        wire:click="archiveRecord"
                        wire:confirm="Archive this record locally?"
                        wire:loading.attr="disabled"
                        wire:target="archiveRecord"
                        variant="secondary"
                        full
                    >
                        <span wire:loading.remove wire:target="archiveRecord">Archive record</span>
                        <span wire:loading wire:target="archiveRecord">Archiving</span>
                    </x-mobile.button>
                @endif

                @if ($recordActionPermissions['delete'])
                    <x-mobile.button
                        wire:click="deleteRecord"
                        wire:confirm="Delete this record from local storage?"
                        wire:loading.attr="disabled"
                        wire:target="deleteRecord"
                        variant="danger"
                        full
                    >
                        <span wire:loading.remove wire:target="deleteRecord">Delete record</span>
                        <span wire:loading wire:target="deleteRecord">Deleting</span>
                    </x-mobile.button>
                @endif
            </div>
        </form>
    </x-mobile.card>
</section>
