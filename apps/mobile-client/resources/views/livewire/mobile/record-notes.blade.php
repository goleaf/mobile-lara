<div class="grid gap-4">
    <x-mobile.loading-state target="createNote,startEditingNote,updateNote,deleteNote,refreshNotes" message="Updating notes..." />

    @if (! $storageAvailable)
        <x-mobile.error-state
            title="Notes unavailable"
            :message="$storageError ?: 'Run the mobile local storage migrations before managing record notes.'"
        >
            <x-slot:action>
                <x-mobile.button wire:click="refreshNotes" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @else
        <x-mobile.card title="Record notes" description="Compose local notes and track each note's offline sync state.">
            <form wire:submit="createNote" class="grid gap-4">
                <x-mobile.textarea
                    name="body"
                    label="New note"
                    rows="4"
                    placeholder="Add context, follow-up details, or field observations"
                    hint="Saved locally first and marked pending sync."
                    wire:model.live="body"
                />

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg border border-app-line bg-app-bg p-3 dark:border-zinc-800 dark:bg-zinc-950">
                        <p class="text-xs font-semibold uppercase tracking-normal text-app-muted dark:text-zinc-500">Notes</p>
                        <p class="mt-1 text-xl font-semibold text-app-ink dark:text-zinc-100">{{ $noteCount }}</p>
                    </div>

                    <div class="rounded-lg border border-app-line bg-app-bg p-3 dark:border-zinc-800 dark:bg-zinc-950">
                        <p class="text-xs font-semibold uppercase tracking-normal text-app-muted dark:text-zinc-500">Pending</p>
                        <p class="mt-1 text-xl font-semibold text-app-ink dark:text-zinc-100">{{ $pendingCount }}</p>
                    </div>

                    <div class="rounded-lg border border-app-line bg-app-bg p-3 dark:border-zinc-800 dark:bg-zinc-950">
                        <p class="text-xs font-semibold uppercase tracking-normal text-app-muted dark:text-zinc-500">Failed</p>
                        <p class="mt-1 text-xl font-semibold text-app-ink dark:text-zinc-100">{{ $failedCount }}</p>
                    </div>
                </div>

                <x-mobile.submit-button target="createNote" variant="accent" size="lg" loading-label="Saving note">
                    Save note
                </x-mobile.submit-button>
            </form>
        </x-mobile.card>

        <x-mobile.card title="Note list" description="Newest local notes first.">
            <div class="grid gap-3">
                @forelse ($notes as $note)
                    <article
                        wire:key="record-note-{{ $note->id }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        @if ($editingNoteId === $note->id)
                            <form wire:submit="updateNote" class="grid gap-3">
                                <x-mobile.textarea
                                    name="editingBody"
                                    label="Edit note"
                                    rows="4"
                                    wire:model.live="editingBody"
                                />

                                <div class="grid grid-cols-2 gap-2">
                                    <x-mobile.submit-button target="updateNote" variant="accent" loading-label="Saving note">
                                        Save note
                                    </x-mobile.submit-button>

                                    <x-mobile.button wire:click="cancelEditing" variant="secondary" full>
                                        Cancel
                                    </x-mobile.button>
                                </div>
                            </form>
                        @else
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="whitespace-pre-line break-words text-sm leading-6 text-app-ink dark:text-zinc-100">{{ $note->body }}</p>
                                    <p class="mt-2 text-xs font-medium text-app-muted dark:text-zinc-400">
                                        Updated {{ $note->updated_at?->diffForHumans() ?? 'time unknown' }}
                                    </p>
                                </div>

                                <x-mobile.badge :variant="$note->syncVariant()" size="sm" dot>
                                    {{ $note->syncLabel() }}
                                </x-mobile.badge>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <x-mobile.button
                                    wire:click="startEditingNote({{ $note->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="startEditingNote({{ $note->id }})"
                                    variant="secondary"
                                    size="sm"
                                    full
                                >
                                    Edit note
                                </x-mobile.button>

                                <x-mobile.button
                                    wire:click="deleteNote({{ $note->id }})"
                                    wire:confirm="Delete this note from local storage?"
                                    wire:loading.attr="disabled"
                                    wire:target="deleteNote({{ $note->id }})"
                                    variant="danger"
                                    size="sm"
                                    full
                                >
                                    Delete note
                                </x-mobile.button>
                            </div>
                        @endif
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No notes yet"
                        description="Add a note to keep record-specific context available offline."
                    />
                @endforelse
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="refreshNotes" variant="secondary" full>
                    Refresh notes
                </x-mobile.button>
            </x-slot:footer>
        </x-mobile.card>
    @endif
</div>
