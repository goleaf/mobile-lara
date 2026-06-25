<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public bool $hasNetworkError = false;

    public bool $hasDashboardContent = true;

    public function refreshDashboard(): void
    {
        $this->hasNetworkError = false;
        $this->hasDashboardContent = true;
    }

    public function render(): View
    {
        return view('livewire.mobile.dashboard');
    }
}
