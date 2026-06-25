<?php

namespace App\Livewire\Mobile;

use App\Models\MobileLocalRecord;
use App\Services\MobileLocal\RecordActivityTimeline;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Livewire\Component;

class ActivityTimeline extends Component
{
    public MobileLocalRecord $record;

    public int $limit = 80;

    public ?string $storageError = null;

    private RecordActivityTimeline $timeline;

    public function boot(RecordActivityTimeline $timeline): void
    {
        $this->timeline = $timeline;
    }

    public function mount(MobileLocalRecord $record, int $limit = 80): void
    {
        $this->record = $record;
        $this->limit = max(1, min($limit, 120));
    }

    public function refreshTimeline(): void
    {
        $this->storageError = null;
    }

    public function render(): View
    {
        try {
            $rows = $this->timeline->forRecord($this->record, $this->limit);
            $storageAvailable = true;
        } catch (QueryException) {
            $rows = [];
            $storageAvailable = false;
            $this->storageError = 'Activity timeline storage is unavailable. Run the local mobile migrations first.';
        }

        return view('livewire.mobile.activity-timeline', [
            'rows' => $rows,
            'rowCount' => count($rows),
            'storageAvailable' => $storageAvailable && $this->storageError === null,
        ]);
    }
}
