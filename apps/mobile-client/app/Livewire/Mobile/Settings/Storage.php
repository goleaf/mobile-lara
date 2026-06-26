<?php

namespace App\Livewire\Mobile\Settings;

use App\Livewire\Concerns\DispatchesToasts;
use App\Services\MobileConfig\MobileRemoteConfigStore;
use App\Services\MobileLocal\MobileStorageManager;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

#[Title('Storage settings')]
final class Storage extends Component
{
    use DispatchesToasts;

    public bool $confirmingClearCache = false;

    public bool $confirmingResetLocalData = false;

    public ?string $statusMessage = null;

    public string $statusVariant = 'info';

    private MobileStorageManager $storage;

    private MobileRemoteConfigStore $remoteConfig;

    public function boot(MobileStorageManager $storage, MobileRemoteConfigStore $remoteConfig): void
    {
        $this->storage = $storage;
        $this->remoteConfig = $remoteConfig;
    }

    public function confirmClearCache(): void
    {
        $this->confirmingClearCache = true;
    }

    public function cancelClearCache(): void
    {
        $this->confirmingClearCache = false;
    }

    public function clearCache(): void
    {
        if (! $this->confirmingClearCache) {
            $this->confirmClearCache();

            return;
        }

        try {
            $this->storage->clearFileCache();
            $this->setStatusMessage('File cache cleared.', 'success');
            $this->toastSuccess('Cached files were cleared from local storage.', 'Cache cleared');
        } catch (Throwable) {
            $this->setStatusMessage('File cache could not be cleared.', 'error');
            $this->toastError('Check the configured mobile cache path and try again.', 'Storage action failed');
        } finally {
            $this->confirmingClearCache = false;
        }
    }

    public function confirmResetLocalData(): void
    {
        $this->confirmingResetLocalData = true;
    }

    public function cancelResetLocalData(): void
    {
        $this->confirmingResetLocalData = false;
    }

    public function resetLocalData(): void
    {
        if (! $this->confirmingResetLocalData) {
            $this->confirmResetLocalData();

            return;
        }

        try {
            $this->storage->resetLocalData();
            $this->setStatusMessage('Local data reset. The mobile database schema was recreated.', 'success');
            $this->toastSuccess('The local mobile database has been reset.', 'Local data reset');
        } catch (Throwable) {
            $this->setStatusMessage('Local data could not be reset.', 'error');
            $this->toastError('Check the mobile local database configuration and try again.', 'Storage action failed');
        } finally {
            $this->confirmingResetLocalData = false;
        }
    }

    public function exportLocalData(): void
    {
        $message = $this->storage->exportPlaceholderMessage();

        $this->setStatusMessage($message, 'info');
        $this->toastInfo($message, 'Export placeholder');
    }

    public function render(): View
    {
        $snapshot = $this->storage->snapshot();
        $uploadConfig = $this->remoteConfig->uploadSettings();

        return view('livewire.mobile.settings.storage', [
            'storageRows' => [
                [
                    'label' => 'Local database size',
                    'value' => $snapshot['local_database_size'],
                    'description' => 'SQLite file size placeholder for NativePHP local storage.',
                ],
                [
                    'label' => 'File cache size',
                    'value' => $snapshot['file_cache_size'],
                    'description' => 'Laravel file cache estimate placeholder for cached app payloads.',
                ],
                [
                    'label' => 'Export destination',
                    'value' => $snapshot['export_path'],
                    'description' => 'Placeholder path for a future local data export file.',
                ],
                [
                    'label' => 'Attachment upload limit',
                    'value' => $uploadConfig['max_attachment_mb'].' MB',
                    'description' => 'Cached Admin/API upload guidance for future record and support attachments.',
                ],
                [
                    'label' => 'Allowed upload types',
                    'value' => implode(', ', $uploadConfig['allowed_mime_types']),
                    'description' => 'Mobile-safe MIME hints from remote config; API upload validation remains authoritative.',
                ],
            ],
        ]);
    }

    private function setStatusMessage(string $message, string $variant): void
    {
        $this->statusMessage = $message;
        $this->statusVariant = $variant;
    }
}
