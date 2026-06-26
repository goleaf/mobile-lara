<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="startRecording,pauseRecording,resumeRecording,stopRecording,refreshRecordingStatus,saveRecording,showDetail,playVoiceNote,deleteRecording,queueUploadPlaceholder" message="Updating voice notes..." />

    <x-mobile.page-header
        title="Voice notes"
        description="Record microphone audio, review details, play local files, and queue upload placeholders."
        :back-href="route('mobile.settings.developer')"
    />

    <x-mobile.card
        title="Microphone bridge"
        description="NativePHP microphone events return a local file path to this Livewire screen."
    >
        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg p-4  ">
            <div class="min-w-0">
                <p class="text-base font-semibold text-app-ink ">
                    {{ $nativeAudioAvailable ? 'Native microphone available' : 'Browser fallback active' }}
                </p>
                <p class="mt-1 text-sm leading-5 text-app-muted ">
                    {{ $nativeAudioAvailable ? 'Microphone recording requests can be opened on this device.' : 'Open this route inside NativePHP or Jump Bridge to launch native audio controls.' }}
                </p>
            </div>

            <x-mobile.badge :variant="$nativeAudioAvailable ? 'success' : 'warning'" dot>
                {{ $nativeAudioAvailable ? 'Ready' : 'Fallback' }}
            </x-mobile.badge>
        </div>

        <dl class="mt-3 grid grid-cols-2 gap-3">
            <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                <dt class="text-xs font-semibold uppercase text-app-muted ">State</dt>
                <dd class="mt-1 text-base font-semibold capitalize text-app-ink ">{{ $recordingState }}</dd>
            </div>
            <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                <dt class="text-xs font-semibold uppercase text-app-muted ">Saved note</dt>
                <dd class="mt-1 text-base font-semibold text-app-ink ">{{ $savedVoiceNoteId ? '#'.$savedVoiceNoteId : 'None' }}</dd>
            </div>
        </dl>

        @if ($pendingRecordingId)
            <div class="mt-3 rounded-lg border border-sky-200 bg-sky-50 p-4  ">
                <p class="text-sm font-semibold text-sky-950 ">Pending recording</p>
                <p class="mt-1 break-words text-sm text-sky-900 ">{{ $pendingRecordingId }}</p>
            </div>
        @endif

        <x-slot:footer>
            <div aria-live="polite" class="grid min-h-6 gap-2">
                @if ($recordingError)
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900   ">
                        {{ $recordingError }}
                    </p>
                @endif

                @if ($recordingStatus)
                    <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800   ">
                        {{ $recordingStatus }}
                    </p>
                @endif

                @if ($uploadQueueStatus)
                    <p class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-sm font-medium text-sky-900   ">
                        {{ $uploadQueueStatus }}
                    </p>
                @endif
            </div>
        </x-slot:footer>
    </x-mobile.card>

    <x-mobile.card title="Recorder controls" description="Start, pause, resume, stop, and inspect the active native recording.">
        @if (! $voiceNotePolicy['microphone']['allowed'])
            <x-mobile.error-state
                title="Voice note recording disabled"
                :message="$voiceNotePolicy['microphone']['message']"
            />
        @else
            <div class="grid grid-cols-2 gap-3">
                @forelse ($recordingActions as $recordingAction)
                    <x-mobile.button
                        wire:key="voice-note-action-{{ $recordingAction['action'] }}"
                        wire:click="{{ $recordingAction['action'] }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $recordingAction['action'] }}"
                        :variant="$recordingAction['variant']"
                        :disabled="$recordingAction['disabled']"
                        full
                    >
                        <span wire:loading.remove wire:target="{{ $recordingAction['action'] }}">{{ $recordingAction['label'] }}</span>
                        <span wire:loading wire:target="{{ $recordingAction['action'] }}">{{ $recordingAction['loading'] }}</span>
                    </x-mobile.button>
                @empty
                    <x-mobile.empty-state
                        title="No controls"
                        description="Voice note controls are not configured."
                    />
                @endforelse
            </div>
        @endif
    </x-mobile.card>

    <x-mobile.card title="Current recording" description="Review the returned native audio path before saving.">
        @if ($recordedPath)
            <div class="grid gap-4">
                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="break-words text-base font-semibold text-app-ink ">{{ basename($recordedPath) }}</p>
                            <p class="mt-1 text-sm text-app-muted ">{{ $recordedMimeType }}</p>
                        </div>

                        <x-mobile.badge variant="accent">Unsaved</x-mobile.badge>
                    </div>

                    <p class="mt-3 break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted   ">
                        {{ $recordedPath }}
                    </p>

                    @if ($playbackPath === $recordedPath)
                        <audio controls class="mt-3 w-full" src="{{ $recordedPath }}">
                            Your browser cannot play this audio file.
                        </audio>
                    @endif
                </div>

                <x-mobile.textarea
                    wire:model.live.debounce.300ms="transcript"
                    name="transcript"
                    label="Transcript placeholder"
                    hint="Optional placeholder text until speech-to-text sync is implemented."
                    :error="$errors->first('transcript')"
                    rows="3"
                >{{ $transcript }}</x-mobile.textarea>

                <div class="grid grid-cols-2 gap-3">
                    @if ($voiceNotePolicy['microphone']['allowed'])
                        <x-mobile.button wire:click="saveRecording" wire:loading.attr="disabled" wire:target="saveRecording" variant="primary" full>
                            <span wire:loading.remove wire:target="saveRecording">Save</span>
                            <span wire:loading wire:target="saveRecording">Saving</span>
                        </x-mobile.button>
                    @endif

                    <x-mobile.button wire:click="playVoiceNote" wire:loading.attr="disabled" wire:target="playVoiceNote" variant="secondary" full>
                        <span wire:loading.remove wire:target="playVoiceNote">Play</span>
                        <span wire:loading wire:target="playVoiceNote">Opening</span>
                    </x-mobile.button>

                    @if ($voiceNotePolicy['upload_queue']['allowed'])
                        <x-mobile.button
                            wire:click="queueUploadPlaceholder"
                            wire:loading.attr="disabled"
                            wire:target="queueUploadPlaceholder"
                            :disabled="! $savedVoiceNoteId"
                            variant="secondary"
                            full
                        >
                            <span wire:loading.remove wire:target="queueUploadPlaceholder">Queue upload</span>
                            <span wire:loading wire:target="queueUploadPlaceholder">Queueing</span>
                        </x-mobile.button>
                    @endif

                    @if ($voiceNotePolicy['microphone']['allowed'])
                        <x-mobile.button wire:click="deleteRecording" wire:loading.attr="disabled" wire:target="deleteRecording" variant="danger" full>
                            <span wire:loading.remove wire:target="deleteRecording">Delete</span>
                            <span wire:loading wire:target="deleteRecording">Deleting</span>
                        </x-mobile.button>
                    @endif
                </div>
            </div>
        @else
            <x-mobile.empty-state
                title="No current recording"
                description="Start a voice note and stop it when you are done. The returned native file path will appear here."
            />
        @endif
    </x-mobile.card>

    @if ($selectedVoiceNote)
        <x-mobile.card title="Voice note detail" description="Local metadata stored in voice_notes.">
            <x-slot:action>
                <x-mobile.button wire:click="closeDetail" variant="ghost" size="sm">
                    Close
                </x-mobile.button>
            </x-slot:action>

            <div class="grid gap-4">
                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="break-words text-base font-semibold text-app-ink ">{{ $selectedVoiceNote->displayName() }}</p>
                            <p class="mt-1 text-sm text-app-muted ">
                                {{ $selectedVoiceNote->formattedDuration() ?: 'Duration pending' }}
                                / {{ $selectedVoiceNote->created_at?->diffForHumans() ?? 'Time unknown' }}
                            </p>
                        </div>

                        <x-mobile.badge :variant="$selectedVoiceNote->sync_status === 'failed' ? 'danger' : ($selectedVoiceNote->sync_status === 'synced' ? 'success' : 'warning')" dot>
                            {{ $selectedVoiceNote->sync_status }}
                        </x-mobile.badge>
                    </div>

                    <p class="mt-3 break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted   ">
                        {{ $selectedVoiceNote->local_file_path }}
                    </p>

                    <audio controls class="mt-3 w-full" src="{{ $selectedVoiceNote->local_file_path }}">
                        Your browser cannot play this audio file.
                    </audio>
                </div>

                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <p class="text-xs font-semibold uppercase text-app-muted ">Transcript placeholder</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-app-ink ">
                        {{ $selectedVoiceNote->transcript ?: 'Transcript pending' }}
                    </p>
                </div>

                <div class="grid gap-2 text-sm text-app-muted ">
                    <p>Related entity: {{ $selectedVoiceNote->relatedEntityLabel() ?: 'None' }}</p>
                    <p>Created: {{ $selectedVoiceNote->created_at?->toDayDateTimeString() ?? 'Unknown' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <x-mobile.button wire:click="playVoiceNote({{ $selectedVoiceNote->getKey() }})" wire:loading.attr="disabled" wire:target="playVoiceNote({{ $selectedVoiceNote->getKey() }})" variant="primary" full>
                        Play
                    </x-mobile.button>

                    @if ($voiceNotePolicy['upload_queue']['allowed'])
                        <x-mobile.button wire:click="queueUploadPlaceholder({{ $selectedVoiceNote->getKey() }})" wire:loading.attr="disabled" wire:target="queueUploadPlaceholder({{ $selectedVoiceNote->getKey() }})" variant="secondary" full>
                            Queue upload
                        </x-mobile.button>
                    @endif

                    @if ($voiceNotePolicy['microphone']['allowed'])
                        <x-mobile.button wire:click="deleteRecording({{ $selectedVoiceNote->getKey() }})" wire:loading.attr="disabled" wire:target="deleteRecording({{ $selectedVoiceNote->getKey() }})" variant="danger" class="col-span-2" full>
                            Delete voice note
                        </x-mobile.button>
                    @endif
                </div>
            </div>
        </x-mobile.card>
    @endif

    <x-mobile.card title="Capabilities" description="Native microphone methods and local app responsibilities exposed here.">
        <div class="grid gap-3">
            @forelse ($audioCapabilities as $capability)
                <div
                    wire:key="audio-capability-{{ $capability['key'] }}"
                    class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4  "
                >
                    <div class="flex items-start justify-between gap-4">
                        <span class="min-w-0">
                            <span class="block text-base font-semibold text-app-ink ">{{ $capability['label'] }}</span>
                            <span class="mt-1 block text-sm leading-5 text-app-muted ">{{ $capability['description'] }}</span>
                        </span>

                        <x-mobile.badge :variant="$capability['supported'] ? 'success' : 'neutral'">
                            {{ $capability['supported'] ? 'Supported' : 'Unavailable' }}
                        </x-mobile.badge>
                    </div>

                    <p class="break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted   ">
                        {{ $capability['driver'] }}
                    </p>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No capabilities"
                    description="Audio recording capabilities are not available."
                />
            @endforelse
        </div>
    </x-mobile.card>

    <x-mobile.card title="Saved voice notes" description="Recent local audio records from voice_notes.">
        @if (! $storageAvailable)
            <x-mobile.error-state
                title="Voice note storage unavailable"
                message="Run the mobile local database migrations before saving voice notes."
            />
        @elseif ($voiceNotes->isNotEmpty())
            <div class="grid gap-3">
                @forelse ($voiceNotes as $voiceNote)
                    <article
                        wire:key="voice-note-{{ $voiceNote->getKey() }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4  "
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="break-words text-base font-semibold text-app-ink ">{{ $voiceNote->displayName() }}</p>
                                <p class="mt-1 text-sm leading-5 text-app-muted ">
                                    {{ $voiceNote->transcriptPreview() }}
                                </p>
                            </div>

                            <x-mobile.badge :variant="$voiceNote->sync_status === 'failed' ? 'danger' : ($voiceNote->sync_status === 'synced' ? 'success' : 'warning')">
                                {{ ucfirst($voiceNote->sync_status) }}
                            </x-mobile.badge>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <x-mobile.badge variant="neutral" size="sm">
                                {{ $voiceNote->formattedDuration() ?: 'Duration pending' }}
                            </x-mobile.badge>

                            @if ($voiceNote->relatedEntityLabel())
                                <x-mobile.badge variant="neutral" size="sm">
                                    {{ $voiceNote->relatedEntityLabel() }}
                                </x-mobile.badge>
                            @endif

                            <x-mobile.badge variant="neutral" size="sm">
                                {{ $voiceNote->created_at?->diffForHumans() ?? 'Time unknown' }}
                            </x-mobile.badge>
                        </div>

                        <p class="break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted   ">
                            {{ $voiceNote->local_file_path }}
                        </p>

                        <div class="grid grid-cols-2 gap-3">
                            <x-mobile.button wire:click="showDetail({{ $voiceNote->getKey() }})" wire:loading.attr="disabled" wire:target="showDetail({{ $voiceNote->getKey() }})" variant="secondary" full>
                                Detail
                            </x-mobile.button>

                            <x-mobile.button wire:click="playVoiceNote({{ $voiceNote->getKey() }})" wire:loading.attr="disabled" wire:target="playVoiceNote({{ $voiceNote->getKey() }})" variant="primary" full>
                                Play
                            </x-mobile.button>

                            @if ($voiceNotePolicy['upload_queue']['allowed'])
                                <x-mobile.button wire:click="queueUploadPlaceholder({{ $voiceNote->getKey() }})" wire:loading.attr="disabled" wire:target="queueUploadPlaceholder({{ $voiceNote->getKey() }})" variant="secondary" full>
                                    Queue upload
                                </x-mobile.button>
                            @endif

                            @if ($voiceNotePolicy['microphone']['allowed'])
                                <x-mobile.button wire:click="deleteRecording({{ $voiceNote->getKey() }})" wire:loading.attr="disabled" wire:target="deleteRecording({{ $voiceNote->getKey() }})" variant="danger" full>
                                    Delete
                                </x-mobile.button>
                            @endif
                        </div>
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No saved voice notes"
                        description="Saved voice notes will appear here."
                    />
                @endforelse
            </div>
        @else
            <x-mobile.empty-state
                title="No saved voice notes"
                description="Save a recording locally to add it to this list."
            />
        @endif
    </x-mobile.card>
</section>
