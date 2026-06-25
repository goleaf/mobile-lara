<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalVoiceNote;
use App\Services\Native\AudioRecordingService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Microphone\MicrophoneCancelled;
use Native\Mobile\Events\Microphone\MicrophoneRecorded;
use Throwable;

#[Title('Voice notes')]
class VoiceNotes extends Component
{
    use DispatchesToasts;

    public ?string $pendingRecordingId = null;

    public ?string $recordingReferenceId = null;

    public string $recordingState = 'idle';

    public ?string $recordingStatus = null;

    public ?string $recordingError = null;

    public ?string $recordedPath = null;

    public string $recordedMimeType = 'audio/m4a';

    public ?int $savedVoiceNoteId = null;

    public ?int $selectedVoiceNoteId = null;

    public ?int $playbackVoiceNoteId = null;

    public ?string $playbackPath = null;

    public ?string $uploadQueueStatus = null;

    #[Validate('nullable|string|max:5000')]
    public string $transcript = '';

    private AudioRecordingService $audioRecordings;

    public function boot(AudioRecordingService $audioRecordings): void
    {
        $this->audioRecordings = $audioRecordings;
    }

    public function startRecording(): void
    {
        $this->recordingStatus = null;
        $this->recordingError = null;
        $this->uploadQueueStatus = null;

        $id = 'voice-note-'.Str::uuid()->toString();
        $this->pendingRecordingId = $id;
        $this->recordingReferenceId = $id;

        $result = $this->audioRecordings->start($id);

        if ($result['success']) {
            $this->recordingState = 'recording';
            $this->recordingStatus = $result['message'];
            $this->toastInfo($this->recordingStatus, 'Recording started');

            return;
        }

        $this->pendingRecordingId = null;
        $this->recordingReferenceId = null;
        $this->recordingState = 'idle';
        $this->recordingError = $result['message'];
        $this->toastWarning($this->recordingError, 'Native audio unavailable');
    }

    public function pauseRecording(): void
    {
        $this->applyControlResult(
            result: $this->audioRecordings->pause(),
            stateWhenSuccessful: 'paused',
            toastTitle: 'Recording paused',
        );
    }

    public function resumeRecording(): void
    {
        $this->applyControlResult(
            result: $this->audioRecordings->resume(),
            stateWhenSuccessful: 'recording',
            toastTitle: 'Recording resumed',
        );
    }

    public function stopRecording(): void
    {
        $this->applyControlResult(
            result: $this->audioRecordings->stop(),
            stateWhenSuccessful: 'stopping',
            toastTitle: 'Recording stopped',
        );
    }

    public function refreshRecordingStatus(): void
    {
        $result = $this->audioRecordings->status();

        if ($result['success']) {
            $this->recordingState = $result['status'];
            $this->recordingStatus = $result['message'];
            $this->recordingError = null;
            $this->toastInfo($this->recordingStatus, 'Microphone status');

            return;
        }

        $this->recordingError = $result['message'];
        $this->toastWarning($this->recordingError, 'Status unavailable');
    }

    public function saveRecording(): void
    {
        $this->validateOnly('transcript');

        if (! is_string($this->recordedPath) || $this->recordedPath === '') {
            $this->recordingError = 'Record a voice note before saving.';
            $this->toastWarning($this->recordingError, 'Nothing to save');

            return;
        }

        $result = $this->audioRecordings->save(
            path: $this->recordedPath,
            mimeType: $this->recordedMimeType,
            transcript: $this->transcript,
            recordingId: $this->recordingReferenceId,
        );

        if ($result['success']) {
            $this->savedVoiceNoteId = $result['voice_note_id'] ?? null;
            $this->selectedVoiceNoteId = $this->savedVoiceNoteId;
            $this->recordingStatus = $result['message'];
            $this->recordingError = null;
            $this->toastSuccess($this->recordingStatus, 'Voice note saved');

            return;
        }

        $this->recordingError = $result['message'];
        $this->toastError($this->recordingError, 'Save failed');
    }

    public function showDetail(int $voiceNoteId): void
    {
        $voiceNote = $this->audioRecordings->voiceNote($voiceNoteId);

        if (! $voiceNote instanceof MobileLocalVoiceNote) {
            $this->recordingError = 'Voice note is no longer available on this device.';
            $this->toastWarning($this->recordingError, 'Detail unavailable');

            return;
        }

        $this->selectedVoiceNoteId = (int) $voiceNote->getKey();
        $this->recordingError = null;
    }

    public function closeDetail(): void
    {
        $this->selectedVoiceNoteId = null;
        $this->playbackVoiceNoteId = null;
        $this->playbackPath = null;
    }

    public function playVoiceNote(?int $voiceNoteId = null): void
    {
        if ($voiceNoteId === null && is_string($this->recordedPath) && $this->recordedPath !== '') {
            $this->playbackVoiceNoteId = null;
            $this->playbackPath = $this->recordedPath;
            $this->recordingStatus = 'Current recording is ready for playback.';
            $this->toastInfo($this->recordingStatus, 'Playback ready');

            return;
        }

        $voiceNoteId ??= $this->selectedVoiceNoteId;
        $voiceNote = $voiceNoteId === null ? null : $this->audioRecordings->voiceNote($voiceNoteId);

        if (! $voiceNote instanceof MobileLocalVoiceNote) {
            $this->recordingError = 'Voice note is no longer available for playback.';
            $this->toastWarning($this->recordingError, 'Playback unavailable');

            return;
        }

        $this->selectedVoiceNoteId = (int) $voiceNote->getKey();
        $this->playbackVoiceNoteId = (int) $voiceNote->getKey();
        $this->playbackPath = $voiceNote->local_file_path;
        $this->recordingStatus = 'Voice note is ready for playback.';
        $this->recordingError = null;
        $this->toastInfo($this->recordingStatus, 'Playback ready');
    }

    public function deleteRecording(?int $voiceNoteId = null): void
    {
        $isCurrentRecording = $voiceNoteId === null || $voiceNoteId === $this->savedVoiceNoteId;
        $result = $this->audioRecordings->delete(
            voiceNoteId: $voiceNoteId ?: $this->savedVoiceNoteId,
            path: $voiceNoteId === null ? $this->recordedPath : null,
        );

        if ($result['success']) {
            if ($isCurrentRecording) {
                $this->clearCurrentRecording();
            }

            if ($voiceNoteId !== null && $this->selectedVoiceNoteId === $voiceNoteId) {
                $this->closeDetail();
            }

            $this->recordingStatus = $result['message'];
            $this->recordingError = null;
            $this->toastSuccess($this->recordingStatus, 'Voice note deleted');

            return;
        }

        $this->recordingError = $result['message'];
        $this->toastWarning($this->recordingError, 'Delete skipped');
    }

    public function queueUploadPlaceholder(?int $voiceNoteId = null): void
    {
        $result = $this->audioRecordings->queueUploadPlaceholder(
            voiceNoteId: $voiceNoteId ?: $this->savedVoiceNoteId,
            path: $voiceNoteId === null ? $this->recordedPath : null,
        );

        if ($result['success']) {
            $this->uploadQueueStatus = $result['message'];
            $this->recordingError = null;
            $this->toastSuccess($this->uploadQueueStatus, 'Upload queued');

            return;
        }

        $this->recordingError = $result['message'];
        $this->toastWarning($this->recordingError, 'Queue skipped');
    }

    #[OnNative(MicrophoneRecorded::class)]
    public function handleMicrophoneRecorded(string $path, string $mimeType = 'audio/m4a', ?string $id = null): void
    {
        if (! $this->matchesPendingRecording($id)) {
            return;
        }

        $this->recordedPath = $path;
        $this->recordedMimeType = $mimeType;
        $this->recordingReferenceId = $id;
        $this->pendingRecordingId = null;
        $this->recordingState = 'idle';
        $this->recordingStatus = 'Voice note captured. Save it locally or delete it.';
        $this->recordingError = null;
        $this->savedVoiceNoteId = null;
        $this->selectedVoiceNoteId = null;
        $this->playbackVoiceNoteId = null;
        $this->playbackPath = null;
        $this->uploadQueueStatus = null;
        $this->toastSuccess($this->recordingStatus, 'Voice note ready');
    }

    #[OnNative(MicrophoneCancelled::class)]
    public function handleMicrophoneCancelled(bool $cancelled = true, ?string $id = null): void
    {
        if (! $cancelled || ! $this->matchesPendingRecording($id)) {
            return;
        }

        $this->pendingRecordingId = null;
        $this->recordingReferenceId = null;
        $this->recordingState = 'idle';
        $this->recordingStatus = 'Voice note recording cancelled.';
        $this->recordingError = null;
        $this->toastInfo($this->recordingStatus, 'Recording closed');
    }

    public function render(): View
    {
        return view('livewire.mobile.voice-notes', [
            'audioCapabilities' => $this->audioRecordings->capabilities(),
            'nativeAudioAvailable' => $this->audioRecordings->isAvailable(),
            'voiceNotes' => $this->voiceNotes(),
            'selectedVoiceNote' => $this->selectedVoiceNote(),
            'storageAvailable' => $this->storageAvailable(),
            'recordingActions' => $this->recordingActions(),
        ]);
    }

    /**
     * @param  array{success: bool, operation: string, message: string}  $result
     */
    private function applyControlResult(array $result, string $stateWhenSuccessful, string $toastTitle): void
    {
        if ($result['success']) {
            $this->recordingState = $stateWhenSuccessful;
            $this->recordingStatus = $result['message'];
            $this->recordingError = null;
            $this->toastInfo($this->recordingStatus, $toastTitle);

            return;
        }

        $this->recordingError = $result['message'];
        $this->toastWarning($this->recordingError, 'Native audio unavailable');
    }

    private function clearCurrentRecording(): void
    {
        $this->recordedPath = null;
        $this->recordedMimeType = 'audio/m4a';
        $this->savedVoiceNoteId = null;
        $this->transcript = '';
        $this->uploadQueueStatus = null;
        $this->pendingRecordingId = null;
        $this->recordingReferenceId = null;
        $this->recordingState = 'idle';
        $this->playbackPath = null;
    }

    private function matchesPendingRecording(?string $id): bool
    {
        return is_string($id)
            && is_string($this->pendingRecordingId)
            && hash_equals($this->pendingRecordingId, $id);
    }

    /**
     * @return Collection<int, MobileLocalVoiceNote>
     */
    private function voiceNotes(): Collection
    {
        try {
            return $this->audioRecordings->recentVoiceNotes();
        } catch (Throwable) {
            return new Collection;
        }
    }

    private function selectedVoiceNote(): ?MobileLocalVoiceNote
    {
        if ($this->selectedVoiceNoteId === null) {
            return null;
        }

        return $this->audioRecordings->voiceNote($this->selectedVoiceNoteId);
    }

    private function storageAvailable(): bool
    {
        try {
            $this->audioRecordings->recentVoiceNotes(1);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @return list<array{label: string, action: string, variant: string, disabled: bool, loading: string, description: string}>
     */
    private function recordingActions(): array
    {
        return [
            [
                'label' => 'Start',
                'action' => 'startRecording',
                'variant' => 'primary',
                'disabled' => in_array($this->recordingState, ['recording', 'paused', 'stopping'], true),
                'loading' => 'Starting',
                'description' => 'Begin a new native microphone recording.',
            ],
            [
                'label' => 'Pause',
                'action' => 'pauseRecording',
                'variant' => 'secondary',
                'disabled' => $this->recordingState !== 'recording',
                'loading' => 'Pausing',
                'description' => 'Pause the active recording.',
            ],
            [
                'label' => 'Resume',
                'action' => 'resumeRecording',
                'variant' => 'secondary',
                'disabled' => $this->recordingState !== 'paused',
                'loading' => 'Resuming',
                'description' => 'Continue recording after a pause.',
            ],
            [
                'label' => 'Stop',
                'action' => 'stopRecording',
                'variant' => 'danger',
                'disabled' => ! in_array($this->recordingState, ['recording', 'paused'], true),
                'loading' => 'Stopping',
                'description' => 'Finish recording and wait for a returned audio file.',
            ],
            [
                'label' => 'Refresh',
                'action' => 'refreshRecordingStatus',
                'variant' => 'ghost',
                'disabled' => false,
                'loading' => 'Checking',
                'description' => 'Read current microphone status from the native bridge.',
            ],
        ];
    }
}
