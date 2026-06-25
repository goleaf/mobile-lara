<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalMediaItem;
use App\Services\Native\AudioRecordingService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
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

    public string $recordingState = 'idle';

    public ?string $recordingStatus = null;

    public ?string $recordingError = null;

    public ?string $recordedPath = null;

    public string $recordedMimeType = 'audio/m4a';

    public ?int $savedMediaItemId = null;

    public ?string $uploadQueueStatus = null;

    #[Validate('nullable|string|max:160')]
    public string $caption = '';

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

        $result = $this->audioRecordings->start($id);

        if ($result['success']) {
            $this->recordingState = 'recording';
            $this->recordingStatus = $result['message'];
            $this->toastInfo($this->recordingStatus, 'Recording started');

            return;
        }

        $this->pendingRecordingId = null;
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
        $this->validateOnly('caption');

        if (! is_string($this->recordedPath) || $this->recordedPath === '') {
            $this->recordingError = 'Record a voice note before saving.';
            $this->toastWarning($this->recordingError, 'Nothing to save');

            return;
        }

        $result = $this->audioRecordings->save(
            path: $this->recordedPath,
            mimeType: $this->recordedMimeType,
            caption: $this->caption,
            recordingId: $this->savedMediaItemId === null ? $this->pendingRecordingId : (string) $this->savedMediaItemId,
        );

        if ($result['success']) {
            $this->savedMediaItemId = $result['media_item_id'] ?? null;
            $this->recordingStatus = $result['message'];
            $this->recordingError = null;
            $this->toastSuccess($this->recordingStatus, 'Voice note saved');

            return;
        }

        $this->recordingError = $result['message'];
        $this->toastError($this->recordingError, 'Save failed');
    }

    public function deleteRecording(?int $mediaItemId = null): void
    {
        $isCurrentRecording = $mediaItemId === null || $mediaItemId === $this->savedMediaItemId;
        $result = $this->audioRecordings->delete(
            mediaItemId: $mediaItemId ?: $this->savedMediaItemId,
            path: $mediaItemId === null ? $this->recordedPath : null,
        );

        if ($result['success']) {
            if ($isCurrentRecording) {
                $this->clearCurrentRecording();
            }

            $this->recordingStatus = $result['message'];
            $this->recordingError = null;
            $this->toastSuccess($this->recordingStatus, 'Voice note deleted');

            return;
        }

        $this->recordingError = $result['message'];
        $this->toastWarning($this->recordingError, 'Delete skipped');
    }

    public function queueUploadPlaceholder(?int $mediaItemId = null): void
    {
        $result = $this->audioRecordings->queueUploadPlaceholder(
            mediaItemId: $mediaItemId ?: $this->savedMediaItemId,
            path: $mediaItemId === null ? $this->recordedPath : null,
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
        $this->pendingRecordingId = null;
        $this->recordingState = 'idle';
        $this->recordingStatus = 'Voice note captured. Save it locally or delete it.';
        $this->recordingError = null;
        $this->savedMediaItemId = null;
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
        $this->savedMediaItemId = null;
        $this->caption = '';
        $this->uploadQueueStatus = null;
        $this->pendingRecordingId = null;
        $this->recordingState = 'idle';
    }

    private function matchesPendingRecording(?string $id): bool
    {
        return is_string($id)
            && is_string($this->pendingRecordingId)
            && hash_equals($this->pendingRecordingId, $id);
    }

    /**
     * @return Collection<int, MobileLocalMediaItem>
     */
    private function voiceNotes(): Collection
    {
        try {
            return $this->audioRecordings->recentVoiceNotes();
        } catch (QueryException|Throwable) {
            return MobileLocalMediaItem::newCollection();
        }
    }

    private function storageAvailable(): bool
    {
        try {
            $this->audioRecordings->recentVoiceNotes(1);

            return true;
        } catch (QueryException|Throwable) {
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
