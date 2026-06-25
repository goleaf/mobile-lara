<?php

namespace App\Livewire\Mobile;

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Livewire\Concerns\DispatchesToasts;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Livewire\Component;

final class SyncStatus extends Component
{
    use DispatchesToasts;

    public bool $syncInProgress = false;

    public ?string $statusMessage = null;

    public string $statusVariant = 'info';

    private OfflineActionRepository $offlineActions;

    private SettingsRepository $settings;

    private MobileNetworkState $networkState;

    public function boot(
        OfflineActionRepository $offlineActions,
        SettingsRepository $settings,
        MobileNetworkState $networkState,
    ): void {
        $this->offlineActions = $offlineActions;
        $this->settings = $settings;
        $this->networkState = $networkState;
    }

    public function syncNow(): void
    {
        $this->syncInProgress = true;

        try {
            if (! $this->networkState->isAvailable()) {
                $this->setStatusMessage('Sync is paused until the network is available.', 'warning');
                $this->toastWarning('Your pending actions will stay on this device until you are back online.', 'Offline');

                return;
            }

            $pendingActionCount = $this->offlineActions->pendingCount();
            $this->settings->markSynced();

            $message = $pendingActionCount > 0
                ? "Sync requested for {$pendingActionCount} pending {$this->actionLabel($pendingActionCount)}."
                : 'Local sync is up to date.';

            $this->setStatusMessage($message, 'success');
            $this->toastSuccess($message, 'Sync requested');
        } catch (QueryException) {
            $this->setStatusMessage('Local sync storage is not ready yet.', 'error');
            $this->toastError('Run the mobile local storage migrations before syncing.', 'Sync storage unavailable');
        } finally {
            $this->syncInProgress = false;
        }
    }

    public function render(): View
    {
        return view('livewire.mobile.sync-status', $this->snapshot());
    }

    /**
     * @return array{
     *     isOnline: bool,
     *     storageAvailable: bool,
     *     networkLabel: string,
     *     networkVariant: string,
     *     networkDescription: string,
     *     summaryVariant: string,
     *     summaryDescription: string,
     *     pendingActionCount: int,
     *     failedSyncCount: int,
     *     lastSyncLabel: string,
     *     canSync: bool
     * }
     */
    private function snapshot(): array
    {
        $networkStatus = $this->networkState->status();
        $isOnline = $networkStatus->isOnline;

        try {
            $pendingActionCount = $this->offlineActions->pendingCount();
            $failedSyncCount = $this->offlineActions->failedCount();
            $lastSyncAt = $this->settings->get()->last_sync_at;
            $storageAvailable = true;
        } catch (QueryException) {
            $pendingActionCount = 0;
            $failedSyncCount = 0;
            $lastSyncAt = null;
            $storageAvailable = false;
        }

        return [
            'isOnline' => $isOnline,
            'storageAvailable' => $storageAvailable,
            'networkLabel' => $isOnline ? 'Online' : 'Offline',
            'networkVariant' => $networkStatus->variant(),
            'networkDescription' => $networkStatus->connectionTypeLabel().' / '.$networkStatus->meteredLabel(),
            'summaryVariant' => $this->summaryVariant($storageAvailable, $pendingActionCount, $failedSyncCount),
            'summaryDescription' => $this->summaryDescription($storageAvailable, $isOnline, $pendingActionCount, $failedSyncCount),
            'pendingActionCount' => $pendingActionCount,
            'failedSyncCount' => $failedSyncCount,
            'lastSyncLabel' => $this->lastSyncLabel($lastSyncAt),
            'canSync' => $isOnline && $storageAvailable,
        ];
    }

    private function setStatusMessage(string $message, string $variant): void
    {
        $this->statusMessage = $message;
        $this->statusVariant = $variant;
    }

    private function lastSyncLabel(?CarbonInterface $lastSyncAt): string
    {
        if (! $lastSyncAt instanceof CarbonInterface) {
            return 'Never synced';
        }

        return $lastSyncAt->diffForHumans();
    }

    private function summaryVariant(bool $storageAvailable, int $pendingActionCount, int $failedSyncCount): string
    {
        if (! $storageAvailable || $failedSyncCount > 0) {
            return 'danger';
        }

        if ($pendingActionCount > 0) {
            return 'warning';
        }

        return 'success';
    }

    private function summaryDescription(
        bool $storageAvailable,
        bool $isOnline,
        int $pendingActionCount,
        int $failedSyncCount,
    ): string {
        if (! $storageAvailable) {
            return 'Local sync storage needs migrations before queue status is available.';
        }

        if (! $isOnline) {
            return 'Changes will stay queued on this device until the network returns.';
        }

        if ($failedSyncCount > 0) {
            return 'Some actions failed during sync and need a retry.';
        }

        if ($pendingActionCount > 0) {
            return 'Pending actions are ready for manual sync.';
        }

        return 'Local queue is clear and ready.';
    }

    private function actionLabel(int $count): string
    {
        return $count === 1 ? 'action' : 'actions';
    }
}
