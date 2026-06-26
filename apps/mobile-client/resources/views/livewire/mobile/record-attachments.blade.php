<div class="grid gap-4">
    <x-mobile.loading-state target="createAttachment,linkMediaItem,previewAttachment,shareAttachment,deleteAttachment,uploadQueuePlaceholder,refreshAttachments" message="Updating attachments..." />

    @if (! $storageAvailable)
        <x-mobile.error-state
            title="Attachments unavailable"
            :message="$storageError ?: 'Run the mobile local storage migrations before managing record attachments.'"
        >
            <x-slot:action>
                <x-mobile.button wire:click="refreshAttachments" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @else
        @if (! $attachmentActionPermissions['manage'])
            <x-mobile.error-state
                title="Attachment management disabled"
                message="Your current workspace role cannot add, link, delete, or queue record attachments from this device."
            />
        @endif

        @if ($attachmentActionPermissions['manage'])
        <x-mobile.card title="Attachment picker" description="Record attachments saved locally and queued for upload.">
            <form wire:submit="createAttachment" class="grid gap-4">
                <div class="grid gap-2">
                    <label for="attachmentUpload" class="text-sm font-medium text-app-ink ">
                        Upload file
                    </label>
                    <input
                        id="attachmentUpload"
                        name="attachmentUpload"
                        type="file"
                        wire:model="attachmentUpload"
                        class="block min-h-12 w-full rounded-lg border border-app-line bg-app-surface px-3.5 py-2 text-sm text-app-ink shadow-[0_12px_24px_-22px_rgba(15,23,42,0.55)] file:mr-3 file:rounded-lg file:border-0 file:bg-app-ink file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white focus:border-app-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-app-accent/20"
                    >
                    <p class="text-sm leading-5 text-app-muted ">
                        Browser uploads are copied into the mobile attachment sandbox. Local paths still work for NativePHP file picker results.
                    </p>
                    <p wire:loading wire:target="attachmentUpload" class="text-sm font-medium text-app-ink ">
                        Preparing file...
                    </p>
                    @error('attachmentUpload')
                        <p class="text-sm font-medium text-red-600 ">{{ $message }}</p>
                    @enderror
                </div>

                <x-mobile.input
                    name="path"
                    label="File path"
                    placeholder="/tmp/mobile-attachments/document.pdf"
                    hint="Optional when uploading a file; required for manual or NativePHP paths."
                    wire:model.live="path"
                />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-mobile.input
                            name="name"
                            label="Display name"
                            placeholder="signed-receipt.pdf"
                            wire:model.live="name"
                        />

                        <x-mobile.input
                            name="mime"
                            label="MIME type"
                            placeholder="application/pdf"
                            wire:model.live="mime"
                        />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-mobile.select
                            name="type"
                            label="Type"
                            :options="$typeOptions"
                            wire:model.live="type"
                        />

                        <x-mobile.input
                            name="size"
                            label="Size in bytes"
                            type="number"
                            min="0"
                            placeholder="125000"
                            wire:model.live="size"
                        />
                    </div>

                    <x-mobile.textarea
                        name="caption"
                        label="Caption"
                        rows="3"
                        placeholder="Why this attachment matters"
                        wire:model.live="caption"
                    />

                    <div class="grid gap-3 sm:grid-cols-4">
                        <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                            <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Attachments</p>
                            <p class="mt-1 text-xl font-semibold text-app-ink ">{{ $attachmentCount }}</p>
                        </div>

                        <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                            <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Queued</p>
                            <p class="mt-1 text-xl font-semibold text-app-ink ">{{ $queuedCount }}</p>
                        </div>

                        <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                            <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Pending</p>
                            <p class="mt-1 text-xl font-semibold text-app-ink ">{{ $pendingCount }}</p>
                        </div>

                        <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                            <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Failed</p>
                            <p class="mt-1 text-xl font-semibold text-app-ink ">{{ $failedCount }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <x-mobile.submit-button target="createAttachment" variant="accent" loading-label="Saving attachment">
                            Save attachment
                        </x-mobile.submit-button>

                        <x-mobile.button wire:click="resetPicker" variant="secondary" full>
                            Clear picker
                        </x-mobile.button>
                    </div>
                </form>
            </x-mobile.card>
        @endif

        @if ($attachmentActionPermissions['manage'])
            <x-mobile.card title="Media picker" description="Recent local media that can be linked to this record.">
                <div class="grid gap-3">
                    @forelse ($mediaItems as $mediaItem)
                    <article
                        wire:key="record-attachment-media-{{ $mediaItem->id }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4  "
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-sm font-semibold text-app-ink ">{{ $mediaItem->displayName() }}</p>
                                <p class="mt-1 text-xs font-medium text-app-muted ">
                                    {{ $mediaItem->mime ?: 'Unknown MIME' }}
                                    @if ($mediaItem->formattedSize())
                                        / {{ $mediaItem->formattedSize() }}
                                    @endif
                                </p>
                            </div>

                            <x-mobile.badge :variant="$mediaItem->isVideo() ? 'accent' : ($mediaItem->isImage() ? 'success' : 'neutral')" size="sm">
                                {{ ucfirst($mediaItem->type) }}
                            </x-mobile.badge>
                        </div>

                        <p class="break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted   ">
                            {{ $mediaItem->path }}
                        </p>

                        <div class="flex justify-end">
                            <x-mobile.button
                                wire:click="linkMediaItem({{ $mediaItem->id }})"
                                wire:loading.attr="disabled"
                                wire:target="linkMediaItem({{ $mediaItem->id }})"
                                variant="secondary"
                                size="sm"
                            >
                                <span wire:loading.remove wire:target="linkMediaItem({{ $mediaItem->id }})">Link media</span>
                                <span wire:loading wire:target="linkMediaItem({{ $mediaItem->id }})">Linking</span>
                            </x-mobile.button>
                        </div>
                    </article>
                    @empty
                    <x-mobile.empty-state
                        title="No media to link"
                        description="Captured or imported media will appear here after it is stored locally."
                    />
                    @endforelse
                </div>
            </x-mobile.card>
        @endif

        @if ($previewAttachment)
            <x-mobile.card title="Attachment preview" description="Selected local attachment details.">
                <div class="grid gap-4">
                    <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-base font-semibold text-app-ink ">{{ $previewAttachment->displayName() }}</p>
                                <p class="mt-1 break-words text-sm leading-5 text-app-muted ">{{ $previewAttachment->path }}</p>
                            </div>

                            <x-mobile.badge :variant="$previewAttachment->isImage() ? 'success' : 'neutral'">
                                {{ $previewAttachment->typeLabel() }}
                            </x-mobile.badge>
                        </div>

                        @if ($previewAttachment->caption)
                            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-app-ink ">{{ $previewAttachment->caption }}</p>
                        @endif
                    </div>

                    <dl class="grid gap-2">
                        @foreach ($previewAttachment->previewRows() as $row)
                            <div wire:key="attachment-preview-{{ $row['label'] }}" class="grid grid-cols-[6rem_1fr] gap-3 rounded-lg border border-app-line bg-app-bg px-3 py-2  ">
                                <dt class="text-xs font-semibold uppercase tracking-normal text-app-muted ">{{ $row['label'] }}</dt>
                                <dd class="min-w-0 break-words text-sm font-medium text-app-ink ">{{ $row['value'] ?: '-' }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="grid gap-2 sm:grid-cols-3">
                        @if ($attachmentActionPermissions['share'])
                            <x-mobile.button
                                wire:click="shareAttachment({{ $previewAttachment->id }})"
                                wire:loading.attr="disabled"
                                wire:target="shareAttachment({{ $previewAttachment->id }})"
                                variant="secondary"
                                size="sm"
                                full
                            >
                                Share
                            </x-mobile.button>
                        @endif

                        <x-mobile.button wire:click="clearPreview" variant="secondary" size="sm" full>
                            Close
                        </x-mobile.button>

                        @if ($attachmentActionPermissions['manage'])
                            <x-mobile.button
                                wire:click="deleteAttachment({{ $previewAttachment->id }})"
                                wire:confirm="Delete this attachment from local storage?"
                                wire:loading.attr="disabled"
                                wire:target="deleteAttachment({{ $previewAttachment->id }})"
                                variant="danger"
                                size="sm"
                                full
                            >
                                Delete
                            </x-mobile.button>
                        @endif
                    </div>
                </div>
            </x-mobile.card>
        @endif

        <x-mobile.card title="Attachment list" description="Newest local attachments first.">
            <x-slot:action>
                <x-mobile.badge variant="neutral">
                    {{ $attachmentCount }} shown
                </x-mobile.badge>
            </x-slot:action>

            <div class="grid gap-3">
                @forelse ($attachments as $attachment)
                    <article
                        wire:key="record-attachment-item-{{ $attachment->id }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4  "
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-sm font-semibold text-app-ink ">{{ $attachment->displayName() }}</p>
                                <p class="mt-1 break-words text-xs font-medium text-app-muted ">{{ $attachment->path }}</p>
                            </div>

                            <x-mobile.badge :variant="$attachment->isImage() ? 'success' : 'neutral'" size="sm">
                                {{ $attachment->typeLabel() }}
                            </x-mobile.badge>
                        </div>

                        @if ($attachment->caption)
                            <p class="text-sm leading-6 text-app-ink ">{{ $attachment->caption }}</p>
                        @endif

                        <div class="flex flex-wrap gap-2">
                            <x-mobile.badge :variant="$attachment->uploadVariant()" size="sm" dot>
                                {{ $attachment->uploadLabel() }}
                            </x-mobile.badge>

                            <x-mobile.badge :variant="$attachment->syncVariant()" size="sm" dot>
                                {{ $attachment->syncLabel() }}
                            </x-mobile.badge>

                            @if ($attachment->formattedSize())
                                <x-mobile.badge variant="neutral" size="sm">
                                    {{ $attachment->formattedSize() }}
                                </x-mobile.badge>
                            @endif

                            @if ($attachment->media_item_id)
                                <x-mobile.badge variant="neutral" size="sm">
                                    Media #{{ $attachment->media_item_id }}
                                </x-mobile.badge>
                            @endif
                        </div>

                        <div class="grid gap-2 sm:grid-cols-3">
                            <x-mobile.button
                                wire:click="previewAttachment({{ $attachment->id }})"
                                wire:loading.attr="disabled"
                                wire:target="previewAttachment({{ $attachment->id }})"
                                variant="secondary"
                                size="sm"
                                full
                            >
                                Preview
                            </x-mobile.button>

                            @if ($attachmentActionPermissions['share'])
                                <x-mobile.button
                                    wire:click="shareAttachment({{ $attachment->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="shareAttachment({{ $attachment->id }})"
                                    variant="secondary"
                                    size="sm"
                                    full
                                >
                                    Share
                                </x-mobile.button>
                            @endif

                            @if ($attachmentActionPermissions['manage'])
                                <x-mobile.button
                                    wire:click="deleteAttachment({{ $attachment->id }})"
                                    wire:confirm="Delete this attachment from local storage?"
                                    wire:loading.attr="disabled"
                                    wire:target="deleteAttachment({{ $attachment->id }})"
                                    variant="danger"
                                    size="sm"
                                    full
                                >
                                    Delete
                                </x-mobile.button>
                            @endif
                        </div>
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No attachments yet"
                        description="Linked files and media will appear here after they are saved locally."
                    />
                @endforelse
            </div>

            <x-slot:footer>
                <div class="grid grid-cols-2 gap-2">
                    <x-mobile.button wire:click="refreshAttachments" variant="secondary" full>
                        Refresh attachments
                    </x-mobile.button>

                    @if ($attachmentActionPermissions['manage'])
                        <x-mobile.button wire:click="uploadQueuePlaceholder" variant="secondary" full>
                            Upload queue placeholder
                        </x-mobile.button>
                    @endif
                </div>
            </x-slot:footer>
        </x-mobile.card>
    @endif
</div>
