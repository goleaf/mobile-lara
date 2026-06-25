<?php

namespace App\Services\Native;

use App\Models\MobileLocalVoiceNote;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\VoiceNoteRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Native\Mobile\Microphone;
use Throwable;

final class AudioRecordingService
{
    private const UPLOAD_ACTION_TYPE = 'voice_note.upload';

    private const UPLOAD_ENDPOINT = '/api/mobile/voice-notes';

    public function __construct(
        private readonly Microphone $microphone,
        private readonly Filesystem $files,
        private readonly VoiceNoteRepository $voiceNotes,
        private readonly OfflineActionRepository $offlineActions,
    ) {}

    public function isAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    /**
     * @return list<array{key: string, label: string, description: string, supported: bool, driver: string}>
     */
    public function capabilities(): array
    {
        $nativeAvailable = $this->isAvailable();

        return [
            [
                'key' => 'record',
                'label' => 'Start recording',
                'description' => 'Open the NativePHP microphone recorder and correlate completion events.',
                'supported' => $nativeAvailable,
                'driver' => 'Microphone.Start',
            ],
            [
                'key' => 'pause',
                'label' => 'Pause recording',
                'description' => 'Pause the active native microphone recording.',
                'supported' => $nativeAvailable,
                'driver' => 'Microphone.Pause',
            ],
            [
                'key' => 'resume',
                'label' => 'Resume recording',
                'description' => 'Resume a paused native microphone recording.',
                'supported' => $nativeAvailable,
                'driver' => 'Microphone.Resume',
            ],
            [
                'key' => 'stop',
                'label' => 'Stop recording',
                'description' => 'Stop recording and wait for the NativePHP recorded event.',
                'supported' => $nativeAvailable,
                'driver' => 'Microphone.Stop',
            ],
            [
                'key' => 'status',
                'label' => 'Recording status',
                'description' => 'Read idle, recording, or paused state from the native bridge.',
                'supported' => $nativeAvailable,
                'driver' => 'Microphone.GetStatus',
            ],
            [
                'key' => 'save',
                'label' => 'Save locally',
                'description' => 'Persist voice-note metadata in the local voice_notes table.',
                'supported' => true,
                'driver' => 'mobile_local.voice_notes',
            ],
            [
                'key' => 'delete',
                'label' => 'Delete locally',
                'description' => 'Remove the local voice note record and the recorded file when present.',
                'supported' => true,
                'driver' => 'local filesystem',
            ],
            [
                'key' => 'upload-queue',
                'label' => 'Queue upload',
                'description' => 'Create an offline action placeholder for server upload.',
                'supported' => true,
                'driver' => 'mobile_local.offline_actions',
            ],
        ];
    }

    /**
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function start(string $id): array
    {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'operation' => 'start',
                'id' => $id,
                'message' => 'Native audio recording is unavailable in this browser runtime.',
            ];
        }

        try {
            $started = $this->microphone
                ->record()
                ->id($id)
                ->remember()
                ->start();
        } catch (Throwable) {
            $started = false;
        }

        return [
            'success' => $started,
            'operation' => 'start',
            'id' => $id,
            'message' => $started
                ? 'Native microphone recording started.'
                : 'Unable to start native microphone recording.',
        ];
    }

    /**
     * @return array{success: bool, operation: string, message: string}
     */
    public function pause(): array
    {
        return $this->control(
            operation: 'pause',
            unavailableMessage: 'Native microphone pause is unavailable in this browser runtime.',
            control: fn (): mixed => $this->microphone->pause(),
            successMessage: 'Native microphone pause requested.',
            failureMessage: 'Unable to pause native microphone recording.',
        );
    }

    /**
     * @return array{success: bool, operation: string, message: string}
     */
    public function resume(): array
    {
        return $this->control(
            operation: 'resume',
            unavailableMessage: 'Native microphone resume is unavailable in this browser runtime.',
            control: fn (): mixed => $this->microphone->resume(),
            successMessage: 'Native microphone resume requested.',
            failureMessage: 'Unable to resume native microphone recording.',
        );
    }

    /**
     * @return array{success: bool, operation: string, message: string}
     */
    public function stop(): array
    {
        return $this->control(
            operation: 'stop',
            unavailableMessage: 'Native microphone stop is unavailable in this browser runtime.',
            control: fn (): mixed => $this->microphone->stop(),
            successMessage: 'Native microphone stop requested.',
            failureMessage: 'Unable to stop native microphone recording.',
        );
    }

    /**
     * @return array{success: bool, operation: string, status: string, message: string}
     */
    public function status(): array
    {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'operation' => 'status',
                'status' => 'idle',
                'message' => 'Native microphone status is unavailable in this browser runtime.',
            ];
        }

        try {
            $status = $this->normalizeStatus($this->microphone->getStatus());
        } catch (Throwable) {
            $status = 'idle';
        }

        return [
            'success' => true,
            'operation' => 'status',
            'status' => $status,
            'message' => "Native microphone status: {$status}.",
        ];
    }

    /**
     * @return array{success: bool, operation: string, path: string|null, message: string}
     */
    public function lastRecording(): array
    {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'operation' => 'last_recording',
                'path' => null,
                'message' => 'Native microphone last recording is unavailable in this browser runtime.',
            ];
        }

        try {
            $path = $this->microphone->getRecording();
        } catch (Throwable) {
            $path = null;
        }

        return [
            'success' => is_string($path) && $path !== '',
            'operation' => 'last_recording',
            'path' => $path,
            'message' => is_string($path) && $path !== ''
                ? 'Last native microphone recording found.'
                : 'No native microphone recording is available yet.',
        ];
    }

    /**
     * @return array{success: bool, operation: string, message: string, voice_note_id?: int, path?: string}
     */
    public function save(
        string $path,
        string $mimeType = 'audio/m4a',
        ?string $transcript = null,
        ?string $recordingId = null,
        ?int $duration = null,
    ): array {
        $path = trim($path);

        if ($path === '' || ! $this->files->isFile($path)) {
            return [
                'success' => false,
                'operation' => 'save',
                'message' => 'A readable voice note file is required before saving.',
            ];
        }

        $voiceNote = $this->voiceNotes->record(
            localFilePath: $path,
            duration: $duration,
            transcript: $this->normalizeTranscript($transcript),
            syncStatus: MobileLocalVoiceNote::SYNC_PENDING,
            relatedEntityType: $recordingId === null ? null : 'native_recording',
            relatedEntityId: $recordingId,
        );

        return [
            'success' => true,
            'operation' => 'save',
            'message' => 'Voice note saved locally.',
            'voice_note_id' => (int) $voiceNote->getKey(),
            'path' => $voiceNote->local_file_path,
        ];
    }

    /**
     * @return array{success: bool, operation: string, message: string, file_deleted?: bool, voice_note_deleted?: bool}
     */
    public function delete(int|string|null $voiceNoteId = null, ?string $path = null): array
    {
        if ($voiceNoteId === null && (! is_string($path) || trim($path) === '')) {
            return [
                'success' => false,
                'operation' => 'delete',
                'message' => 'A voice note record or file path is required before deleting.',
            ];
        }

        $voiceNote = $voiceNoteId === null ? null : $this->findVoiceNote($voiceNoteId);
        $path = $path ?: $voiceNote?->local_file_path;
        $voiceNoteDeleted = false;

        if ($voiceNote instanceof MobileLocalVoiceNote) {
            $voiceNote->delete();
            $voiceNoteDeleted = true;
        }

        $fileDeleted = false;

        if (is_string($path) && trim($path) !== '' && $this->files->isFile($path)) {
            $fileDeleted = $this->files->delete($path);
        }

        return [
            'success' => $voiceNoteDeleted || $fileDeleted,
            'operation' => 'delete',
            'message' => $voiceNoteDeleted || $fileDeleted
                ? 'Voice note deleted locally.'
                : 'Voice note was already missing locally.',
            'file_deleted' => $fileDeleted,
            'voice_note_deleted' => $voiceNoteDeleted,
        ];
    }

    /**
     * @return array{success: bool, operation: string, message: string, offline_action_id?: int}
     */
    public function queueUploadPlaceholder(int|string|null $voiceNoteId = null, ?string $path = null): array
    {
        $voiceNote = $voiceNoteId === null ? null : $this->findVoiceNote($voiceNoteId);
        $path = $path ?: $voiceNote?->local_file_path;

        if ((! $voiceNote instanceof MobileLocalVoiceNote) && (! is_string($path) || trim($path) === '')) {
            return [
                'success' => false,
                'operation' => 'queue_upload',
                'message' => 'A saved voice note record or audio file path is required before queueing upload.',
            ];
        }

        $offlineAction = $this->offlineActions->enqueue(
            actionType: self::UPLOAD_ACTION_TYPE,
            endpoint: self::UPLOAD_ENDPOINT,
            method: 'POST',
            payload: [
                'voice_note_id' => $voiceNote?->getKey(),
                'path' => $path,
                'duration' => $voiceNote?->duration,
                'transcript' => $voiceNote?->transcript,
                'related_entity_type' => $voiceNote?->related_entity_type,
                'related_entity_id' => $voiceNote?->related_entity_id,
                'placeholder' => true,
            ],
        );

        return [
            'success' => true,
            'operation' => 'queue_upload',
            'message' => 'Voice note upload placeholder queued.',
            'offline_action_id' => (int) $offlineAction->getKey(),
        ];
    }

    /**
     * @return Collection<int, MobileLocalVoiceNote>
     */
    public function recentVoiceNotes(int $limit = 12): Collection
    {
        return $this->voiceNotes->recent(limit: max(1, min($limit, 50)));
    }

    public function voiceNote(int|string $voiceNoteId): ?MobileLocalVoiceNote
    {
        return $this->findVoiceNote($voiceNoteId);
    }

    /**
     * @param  callable(): mixed  $control
     * @return array{success: bool, operation: string, message: string}
     */
    private function control(
        string $operation,
        string $unavailableMessage,
        callable $control,
        string $successMessage,
        string $failureMessage,
    ): array {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'operation' => $operation,
                'message' => $unavailableMessage,
            ];
        }

        try {
            $control();
            $success = true;
        } catch (Throwable) {
            $success = false;
        }

        return [
            'success' => $success,
            'operation' => $operation,
            'message' => $success ? $successMessage : $failureMessage,
        ];
    }

    private function findVoiceNote(int|string $voiceNoteId): ?MobileLocalVoiceNote
    {
        try {
            return $this->voiceNotes->find($voiceNoteId);
        } catch (Throwable) {
            return null;
        }
    }

    private function normalizeStatus(string $status): string
    {
        return in_array($status, ['idle', 'recording', 'paused'], true) ? $status : 'idle';
    }

    private function normalizeTranscript(?string $transcript): ?string
    {
        $transcript = trim((string) $transcript);

        return $transcript === '' ? null : Str::limit($transcript, 10_000, '');
    }
}
