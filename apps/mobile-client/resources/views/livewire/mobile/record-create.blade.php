<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Create record"
        description="Save a generic local record for offline-first workflows."
        :back-href="route('mobile.records.index')"
    />

    @if (! $storageAvailable || $storageError)
        <x-mobile.error-state
            title="Record storage unavailable"
            :message="$storageError ?: 'Run the mobile local storage migrations before creating local records.'"
        />
    @endif

    @if (! $recordActionPermissions['create'])
        <x-mobile.error-state
            title="Record creation disabled"
            message="Your current workspace role cannot create local records from this device."
        />
    @endif

    <x-mobile.card title="Record details" description="Records stay on local SQLite first and can be synced later.">
        <form wire:submit="submitOffline" class="grid gap-4">
            <x-mobile.input
                name="title"
                label="Title"
                placeholder="Customer follow-up"
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

            <div class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-app-ink dark:text-zinc-100">{{ $attachmentPlaceholder['title'] }}</p>
                        <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $attachmentPlaceholder['description'] }}</p>
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

            <div class="grid gap-4 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                <div>
                    <p class="text-sm font-semibold text-app-ink dark:text-zinc-100">Location optional</p>
                    <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                        Save a label or coordinates when this record is tied to a place.
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

            <div class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                <p class="text-sm font-semibold text-app-ink dark:text-zinc-100">Local-first</p>
                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                    Save a draft to keep working locally, or submit offline to queue the record as a pending local change.
                </p>
            </div>

            @if ($recordActionPermissions['create'])
                <div class="grid gap-3 sm:grid-cols-2">
                    <x-mobile.button
                        wire:click="saveDraft"
                        wire:loading.attr="disabled"
                        wire:target="saveDraft"
                        variant="secondary"
                        size="lg"
                        full
                    >
                        <span wire:loading.remove wire:target="saveDraft">Save draft</span>
                        <span wire:loading wire:target="saveDraft">Saving draft</span>
                    </x-mobile.button>

                    <x-mobile.submit-button target="submitOffline" variant="accent" size="lg" loading-label="Submitting offline">
                        Submit offline
                    </x-mobile.submit-button>
                </div>
            @endif
        </form>
    </x-mobile.card>
</section>
