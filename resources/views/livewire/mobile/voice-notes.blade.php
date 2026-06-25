<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Voice notes"
        description="Record microphone audio, save it locally, and queue an upload placeholder."
        :back-href="route('mobile.settings.developer')"
    />

    <x-mobile.card
        title="Microphone bridge"
        description="NativePHP microphone events return the recorded file path to this Livewire screen."
    >
        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
            <div class="min-w-0">
                <p class="text-base font-semibold text-app-ink dark:text-zinc-100">
                    {{ $nativeAudioAvailable ? 'Native microphone available' : 'Browser fallback active' }}
                </p>
                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                    {{ $nativeAudioAvailable ? 'Microphone recording requests can be opened on this device.' : 'Open this route inside NativePHP or Jump Bridge to launch native audio controls.' }}
                </p>
            </div>

            <x-mobile.badge :variant="$nativeAudioAvailable ? 'success' : 'warning'" dot>
                {{ $nativeAudioAvailable ? 'Ready' : 'Fallback' }}
            </x-mobile.badge>
        </div>

        <dl class="mt-3 grid grid-cols-2 gap-3">
            <div class="rounded-lg border border-app-line bg-app-bg p-3 dark:border-zinc-800 dark:bg-zinc-950">
                <dt class="text-xs font-semibold uppercase text-app-muted dark:text-zinc-500">State</dt>
                <dd class="mt-1 text-base font-semibold capitalize text-app-ink dark:text-zinc-100">{{ $recordingState }}</dd>
            </div>
            <div class="rounded-lg border border-app-line bg-app-bg p-3 dark:border-zinc-800 dark:bg-zinc-950">
                <dt class="text-xs font-semibold uppercase text-app-muted dark:text-zinc-500">Saved item</dt>
                <dd class="mt-1 text-base font-semibold text-app-ink dark:text-zinc-100">{{ $savedMediaItemId ? '#'.$savedMediaItemId : 'None' }}</dd>
            </div>
        </dl>

        @if ($pendingRecordingId)
            <div class="mt-3 rounded-lg border border-sky-200 bg-sky-50 p-4 dark:border-sky-400/30 dark:bg-sky-400/10">
                <p class="text-sm font-semibold text-sky-950 dark:text-sky-100">Pending recording</p>
                <p class="mt-1 break-words text-sm text-sky-900 dark:text-sky-100/80">{{ $pendingRecordingId }}</p>
            </div>
        @endif

        <x-slot:footer>
            <div aria-live="polite" class="grid min-h-6 gap-2">
                @if ($recordingError)
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 dark:border-amber-300/20 dark:bg-amber-300/10 dark:text-amber-100">
                        {{ $recordingError }}
                    </p>
                @endif

                @if ($recordingStatus)
                    <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                        {{ $recordingStatus }}
                    </p>
                @endif

                @if ($uploadQueueStatus)
                    <p class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-sm font-medium text-sky-900 dark:border-sky-400/30 dark:bg-sky-400/10 dark:text-sky-100">
                        {{ $uploadQueueStatus }}
                    </p>
                @endif
            </div>
        </x-slot:footer>
    </x-mobile.card>

    <x-mobile.card title="Recorder controls" description="Start, pause, resume, stop, and inspect the active native recording.">
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
    </x-mobile.card>

    <x-mobile.card title="Current recording" description="Review the returned native audio path before saving.">
        @if ($recordedPath)
            <div class="grid gap-4">
                <div class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="break-words text-base font-semibold text-app-ink dark:text-zinc-100">{{ basename($recordedPath) }}</p>
                            <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">{{ $recordedMimeType }}</p>
                        </div>

                        <x-mobile.badge variant="accent">Audio</x-mobile.badge>
                    </div>

                    <p class="mt-3 break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
                        {{ $recordedPath }}
                    </p>
                </div>

                <x-mobile.textarea
                    wire:model.live.debounce.300ms="caption"
                    name="caption"
                    label="Caption"
                    hint="Optional, up to 160 characters."
                    :error="$errors->first('caption')"
                    rows="3"
                >{{ $caption }}</x-mobile.textarea>

                <div class="grid grid-cols-2 gap-3">
                    <x-mobile.button wire:click="saveRecording" wire:loading.attr="disabled" wire:target="saveRecording" variant="primary" full>
                        <span wire:loading.remove wire:target="saveRecording">Save</span>
                        <span wire:loading wire:target="saveRecording">Saving</span>
                    </x-mobile.button>

                    <x-mobile.button
                        wire:click="queueUploadPlaceholder"
                        wire:loading.attr="disabled"
                        wire:target="queueUploadPlaceholder"
                        :disabled="! $savedMediaItemId"
                        variant="secondary"
                        full
                    >
                        <span wire:loading.remove wire:target="queueUploadPlaceholder">Queue upload</span>
                        <span wire:loading wire:target="queueUploadPlaceholder">Queueing</span>
                    </x-mobile.button>

                    <x-mobile.button wire:click="deleteRecording" wire:loading.attr="disabled" wire:target="deleteRecording" variant="danger" class="col-span-2" full>
                        <span wire:loading.remove wire:target="deleteRecording">Delete current recording</span>
                        <span wire:loading wire:target="deleteRecording">Deleting</span>
                    </x-mobile.button>
                </div>
            </div>
        @else
            <x-mobile.empty-state
                title="No current recording"
                description="Start a voice note and stop it when you are done. The returned native file path will appear here."
            />
        @endif
    </x-mobile.card>

    <x-mobile.card title="Capabilities" description="Native microphone methods and local app responsibilities exposed here.">
        <div class="grid gap-3">
            @forelse ($audioCapabilities as $capability)
                <div
                    wire:key="audio-capability-{{ $capability['key'] }}"
                    class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                >
                    <div class="flex items-start justify-between gap-4">
                        <span class="min-w-0">
                            <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">{{ $capability['label'] }}</span>
                            <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $capability['description'] }}</span>
                        </span>

                        <x-mobile.badge :variant="$capability['supported'] ? 'success' : 'neutral'">
                            {{ $capability['supported'] ? 'Supported' : 'Unavailable' }}
                        </x-mobile.badge>
                    </div>

                    <p class="break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
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

    <x-mobile.card title="Saved voice notes" description="Recent local audio records from media_items.">
        @if (! $storageAvailable)
            <x-mobile.error-state
                title="Local storage unavailable"
                message="Run the mobile local database migrations before saving voice notes."
            />
        @elseif ($voiceNotes->isNotEmpty())
            <div class="grid gap-3">
                @forelse ($voiceNotes as $voiceNote)
                    <div
                        wire:key="voice-note-{{ $voiceNote->getKey() }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="break-words text-base font-semibold text-app-ink dark:text-zinc-100">{{ $voiceNote->displayName() }}</p>
                                <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">
                                    {{ $voiceNote->caption ?: 'No caption' }}
                                </p>
                            </div>

                            <x-mobile.badge :variant="$voiceNote->sync_status === 'failed' ? 'danger' : ($voiceNote->sync_status === 'synced' ? 'success' : 'warning')">
                                {{ ucfirst($voiceNote->sync_status) }}
                            </x-mobile.badge>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm text-app-muted dark:text-zinc-400">
                            <span>{{ $voiceNote->mime ?: 'audio/m4a' }}</span>
                            <span class="text-right">{{ $voiceNote->formattedSize() ?: 'Unknown size' }}</span>
                        </div>

                        <p class="break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
                            {{ $voiceNote->path }}
                        </p>

                        <div class="grid grid-cols-2 gap-3">
                            <x-mobile.button
                                wire:click="queueUploadPlaceholder({{ $voiceNote->getKey() }})"
                                wire:loading.attr="disabled"
                                wire:target="queueUploadPlaceholder({{ $voiceNote->getKey() }})"
                                variant="secondary"
                                full
                            >
                                Queue upload
                            </x-mobile.button>

                            <x-mobile.button
                                wire:click="deleteRecording({{ $voiceNote->getKey() }})"
                                wire:loading.attr="disabled"
                                wire:target="deleteRecording({{ $voiceNote->getKey() }})"
                                variant="danger"
                                full
                            >
                                Delete
                            </x-mobile.button>
                        </div>
                    </div>
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
