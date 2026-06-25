<?php

namespace App\Livewire\Mobile\Conflicts;

use App\Models\MobileLocalOfflineAction;
use App\Services\MobileLocal\OfflineActionRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sync conflicts')]
final class ConflictList extends Component
{
    public int $limit = 50;

    private OfflineActionRepository $offlineActions;

    public function boot(OfflineActionRepository $offlineActions): void
    {
        $this->offlineActions = $offlineActions;
    }

    public function mount(int $limit = 50): void
    {
        $this->limit = max(1, min($limit, 100));
    }

    public function refreshConflicts(): void
    {
        //
    }

    public function render(): View
    {
        try {
            $conflicts = $this->offlineActions->conflicts($this->limit);
            $storageAvailable = true;
        } catch (QueryException) {
            $conflicts = MobileLocalOfflineAction::newCollection();
            $storageAvailable = false;
        }

        return view('livewire.mobile.conflicts.conflict-list', [
            'conflicts' => $conflicts,
            'storageAvailable' => $storageAvailable,
        ]);
    }
}
