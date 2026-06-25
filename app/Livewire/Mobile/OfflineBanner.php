<?php

namespace App\Livewire\Mobile;

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Livewire\Concerns\DispatchesToasts;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\OfflineActionSyncWorker;
use App\Services\MobileLocal\SettingsRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Livewire\Component;

final class OfflineBanner extends Component
{
    use DispatchesToasts;

    public bool $isOffline = false;

    public string $connectionTypeLabel = 'Unknown';

    public string $meteredLabel = 'Unknown';

    public int $pendingActionCount = 0;

    public bool $storageAvailable = true;

    public ?string $statusMessage = null;

    private MobileNetworkState $networkState;

    private OfflineActionRepository $offlineActions;

    private OfflineActionSyncWorker $syncWorker;

    private SettingsRepository $settings;

    public function boot(
        MobileNetworkState $networkState,
        OfflineActionRepository $offlineActions,
        OfflineActionSyncWorker $syncWorker,
        SettingsRepository $settings,
    ): void {
        $this->networkState = $networkState;
        $this->offlineActions = $offlineActions;
        $this->syncWorker = $syncWorker;
        $this->settings = $settings;
    }

    public function mount(): void
    {
        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $networkStatus = $this->networkState->status();

        $this->isOffline = $networkStatus->isOffline();
        $this->connectionTypeLabel = $networkStatus->connectionTypeLabel();
        $this->meteredLabel = $networkStatus->meteredLabel();

        try {
            $this->pendingActionCount = $this->offlineActions->pendingCount();
            $this->storageAvailable = true;
        } catch (QueryException) {
            $this->pendingActionCount = 0;
            $this->storageAvailable = false;
        }
    }

    public function retrySync(): void
    {
        $this->refreshStatus();

        if ($this->isOffline) {
            $this->statusMessage = 'Still offline. Pending actions will sync when a connection returns.';
            $this->toastWarning($this->statusMessage, 'Offline');

            return;
        }

        if (! $this->storageAvailable) {
            $this->statusMessage = 'Local sync storage is not ready yet.';
            $this->toastError('Run the mobile local storage migrations before syncing.', 'Sync storage unavailable');

            return;
        }

        $pendingActionCount = $this->pendingActionCount;
        $result = $pendingActionCount > 0
            ? $this->syncWorker->process()
            : ['processed' => 0, 'completed' => 0, 'failed' => 0];

        $this->settings->markSynced();

        if ($result['failed'] > 0) {
            $this->statusMessage = "Connection restored. Synced {$result['completed']} {$this->actionLabel($result['completed'])}; {$result['failed']} need retry.";
            $this->toastWarning($this->statusMessage, 'Sync needs retry');
        } elseif ($result['completed'] > 0) {
            $this->statusMessage = "Connection restored. Synced {$result['completed']} pending {$this->actionLabel($result['completed'])}.";
            $this->toastSuccess($this->statusMessage, 'Connection restored');
        } else {
            $this->statusMessage = 'Connection restored. Local sync is up to date.';
            $this->toastSuccess($this->statusMessage, 'Connection restored');
        }

        $this->refreshStatus();
    }

    public function render(): View
    {
        return view('livewire.mobile.offline-banner', [
            'pendingActionLabel' => $this->pendingActionLabel(),
        ]);
    }

    private function pendingActionLabel(): string
    {
        if (! $this->storageAvailable) {
            return 'Pending actions unavailable';
        }

        return "{$this->pendingActionCount} pending {$this->actionLabel($this->pendingActionCount)}";
    }

    private function actionLabel(int $count): string
    {
        return $count === 1 ? 'action' : 'actions';
    }
}
