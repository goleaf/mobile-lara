<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Create check-in"
        description="Save a local location check-in for offline-first sync."
        :back-href="route('mobile.check-ins.index')"
    />

    @if (! $storageAvailable || $storageError)
        <x-mobile.error-state
            title="Check-in storage unavailable"
            :message="$storageError ?: 'Run the mobile local storage migrations before creating local check-ins.'"
        />
    @endif

    @if (! $checkInPolicy['can_save'])
        <x-mobile.error-state
            title="Check-in creation disabled"
            :message="$checkInPolicy['message']"
        />
    @else
        <x-mobile.card title="Location details" description="Coordinates are stored locally with pending sync status.">
            <form wire:submit="save" class="grid gap-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-mobile.input
                        name="latitude"
                        label="Latitude"
                        type="number"
                        step="0.0000001"
                        inputmode="decimal"
                        placeholder="54.6871570"
                        hint="Range -90 to 90."
                        wire:model.live="latitude"
                    />

                    <x-mobile.input
                        name="longitude"
                        label="Longitude"
                        type="number"
                        step="0.0000001"
                        inputmode="decimal"
                        placeholder="25.2796520"
                        hint="Range -180 to 180."
                        wire:model.live="longitude"
                    />
                </div>

                <x-mobile.input
                    name="accuracy"
                    label="Accuracy"
                    type="number"
                    step="0.01"
                    min="0"
                    inputmode="decimal"
                    placeholder="8.50"
                    hint="Accuracy in meters, if available."
                    wire:model.live="accuracy"
                />

                <x-mobile.textarea
                    name="note"
                    label="Note"
                    rows="4"
                    hint="Optional note, up to 1000 characters."
                    wire:model.live="note"
                />

                <x-mobile.select
                    name="photoId"
                    label="Photo"
                    placeholder="No photo"
                    :options="$photoOptions"
                    hint="Optional local image from the media gallery."
                    wire:model.live="photoId"
                />

                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <p class="text-sm font-semibold text-app-ink ">Sync status</p>
                    <p class="mt-1 text-sm leading-5 text-app-muted ">
                        New check-ins are saved as pending until the offline sync worker sends them to the server.
                    </p>
                </div>

                <x-mobile.submit-button target="save" variant="accent" size="lg" loading-label="Saving check-in">
                    Save check-in
                </x-mobile.submit-button>
            </form>
        </x-mobile.card>
    @endif
</section>
