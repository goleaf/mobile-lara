<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="archiveRecord,restoreRecord,deleteRecord,shareRecord" message="Updating record..." />

    <x-mobile.page-header
        :title="$record->title"
        description="Record detail"
        :back-href="route('mobile.records.index')"
    >
        <x-slot:action>
            <div class="flex max-w-40 flex-wrap justify-end gap-2">
                <x-mobile.badge :variant="$record->statusVariant()" size="sm" dot>
                    {{ $record->statusLabel() }}
                </x-mobile.badge>

                <x-mobile.badge :variant="$record->priorityVariant()" size="sm" dot>
                    {{ $record->priorityLabel() }}
                </x-mobile.badge>
            </div>
        </x-slot:action>
    </x-mobile.page-header>

    @if (! $relatedStorageAvailable)
        <x-mobile.error-state
            title="Related local data unavailable"
            message="Timeline and attachment tables could not be read. Run the mobile local storage migrations before reviewing related rows."
        />
    @endif

    <x-mobile.card title="Overview" description="Current status, priority, ownership, archive, and sync state.">
        <dl class="grid gap-3">
            @foreach ($detailRows as $row)
                <div wire:key="record-detail-row-{{ str($row['label'])->slug() }}" class="grid grid-cols-[7rem_1fr] gap-3 border-b border-app-line pb-3 last:border-b-0 last:pb-0 ">
                    <dt class="text-sm font-medium text-app-muted ">{{ $row['label'] }}</dt>
                    <dd class="min-w-0 break-words text-sm font-semibold text-app-ink ">{{ $row['value'] ?: '-' }}</dd>
                </div>
            @endforeach
        </dl>
    </x-mobile.card>

    <x-mobile.card title="Record" description="Primary local content saved for this record.">
        <div class="grid gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Title</p>
                <p class="mt-1 break-words text-xl font-semibold leading-7 text-app-ink ">{{ $record->title }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                    <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Status</p>
                    <div class="mt-2">
                        <x-mobile.badge :variant="$record->statusVariant()" size="sm" dot>
                            {{ $record->statusLabel() }}
                        </x-mobile.badge>
                    </div>
                </div>

                <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                    <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Priority</p>
                    <div class="mt-2">
                        <x-mobile.badge :variant="$record->priorityVariant()" size="sm" dot>
                            {{ $record->priorityLabel() }}
                        </x-mobile.badge>
                    </div>
                </div>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Description</p>
                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-app-ink ">{{ $record->description ?: 'No description' }}</p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Tags</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @forelse ($tags as $tag)
                        <x-mobile.badge wire:key="record-detail-tag-{{ $tag }}" variant="neutral" size="sm">
                            {{ $tag }}
                        </x-mobile.badge>
                    @empty
                        <x-mobile.badge variant="neutral" size="sm">
                            No tags
                        </x-mobile.badge>
                    @endforelse
                </div>
            </div>
        </div>
    </x-mobile.card>

    <x-mobile.card title="Metadata" description="Structured JSON values stored with the local record.">
        <div class="grid gap-3">
            @forelse ($metadataRows as $metadataRow)
                <div wire:key="record-metadata-{{ $metadataRow['key'] }}" class="rounded-lg border border-app-line bg-app-bg p-3  ">
                    <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">{{ $metadataRow['label'] }}</p>
                    <p class="mt-1 break-words text-sm leading-6 text-app-ink ">{{ $metadataRow['value'] }}</p>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No metadata"
                    description="Structured record metadata will appear here when the record includes JSON values."
                />
            @endforelse
        </div>
    </x-mobile.card>

    <x-mobile.card title="Notes" description="Local notes stored in record metadata.">
        <p class="whitespace-pre-line text-sm leading-6 text-app-ink ">{{ $record->notesText() ?: 'No notes' }}</p>
    </x-mobile.card>

    <livewire:mobile.record-notes
        :record="$record"
        :wire:key="'record-notes-'.$record->id"
    />

    <livewire:mobile.activity-timeline
        :record="$record"
        :wire:key="'record-activity-timeline-'.$record->id"
    />

    <x-mobile.card title="Attachments" description="Local media files related to this record.">
        <div class="grid gap-3">
            @forelse ($attachments as $attachment)
                <article wire:key="record-attachment-{{ $attachment['key'] }}" class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="break-words text-sm font-semibold text-app-ink ">{{ $attachment['name'] }}</p>
                            <p class="mt-1 text-xs font-medium text-app-muted ">{{ $attachment['type'] }}@if ($attachment['meta']) - {{ $attachment['meta'] }} @endif</p>
                        </div>

                        <x-mobile.badge variant="neutral" size="sm">
                            {{ $attachment['sync_status'] }}
                        </x-mobile.badge>
                    </div>

                    @if ($attachment['caption'])
                        <p class="mt-3 text-sm leading-5 text-app-ink ">{{ $attachment['caption'] }}</p>
                    @endif

                    <p class="mt-3 break-all text-xs leading-5 text-app-muted ">{{ $attachment['path'] }}</p>
                </article>
            @empty
                <x-mobile.empty-state
                    title="No attachments"
                    description="Camera, file, or media captures linked to this record will appear here."
                />
            @endforelse
        </div>
    </x-mobile.card>

    <livewire:mobile.record-attachments
        :record="$record"
        :wire:key="'record-attachments-'.$record->id"
    />

    <x-mobile.card :title="$commentsPlaceholder['title']" :description="$commentsPlaceholder['description']">
        <div class="rounded-lg border border-dashed border-app-line bg-app-bg p-4  ">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-semibold text-app-ink ">Comments</p>
                <x-mobile.badge variant="neutral" size="sm">
                    {{ $commentsPlaceholder['badge'] }}
                </x-mobile.badge>
            </div>
            <p class="mt-2 text-sm leading-6 text-app-muted ">No local comments table is connected yet.</p>
        </div>
    </x-mobile.card>

    <x-mobile.card title="Actions" description="Edit, share, archive, restore, or delete this local record.">
        <div class="grid gap-3">
            @if ($recordActionPermissions['update'])
                <a
                    href="{{ route('mobile.records.edit', $record) }}"
                    wire:navigate
                    class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90 active:bg-app-ink/80   "
                >
                    Edit record
                </a>
            @endif

            @if ($recordActionPermissions['share'])
                <x-mobile.button
                    wire:click="shareRecord"
                    wire:loading.attr="disabled"
                    wire:target="shareRecord"
                    variant="accent"
                    full
                >
                    <span wire:loading.remove wire:target="shareRecord">Share record</span>
                    <span wire:loading wire:target="shareRecord">Sharing</span>
                </x-mobile.button>
            @endif

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

            @if (! $recordActionPermissions['update'] && ! $recordActionPermissions['share'] && ! $recordActionPermissions['archive'] && ! $recordActionPermissions['delete'])
                <x-mobile.empty-state
                    title="No record actions available"
                    description="Your current workspace role can view this record but cannot mutate it locally."
                />
            @endif
        </div>
    </x-mobile.card>
</section>
