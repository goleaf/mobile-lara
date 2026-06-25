<?php

namespace App\Livewire\Mobile;

use App\Services\MobileLocal\ActivityLogRepository;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Activity')]
class ActivityFeed extends Component
{
    public int $limit = 20;

    public ?string $syncStatus = null;

    private ActivityLogRepository $activityLogs;

    public function boot(ActivityLogRepository $activityLogs): void
    {
        $this->activityLogs = $activityLogs;
    }

    public function mount(int $limit = 20, ?string $syncStatus = null): void
    {
        $this->limit = max(1, min($limit, 100));
        $this->syncStatus = $syncStatus;
    }

    public function refreshFeed(): void
    {
        //
    }

    public function render(): View
    {
        return view('livewire.mobile.activity-feed', [
            'activities' => $this->activityLogs->recent($this->limit, $this->syncStatus),
        ]);
    }
}
