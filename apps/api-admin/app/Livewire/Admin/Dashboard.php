<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Mobile Control Dashboard')]
final class Dashboard extends Component
{
    /**
     * @return array<int, array{label: string, status: string, detail: string, tone: string}>
     */
    public function controlAreas(): array
    {
        return [
            [
                'label' => 'Tenant authority',
                'status' => 'Planned',
                'detail' => 'Lifecycle, tenant status, settings, and isolation controls.',
                'tone' => 'neutral',
            ],
            [
                'label' => 'Mobile API',
                'status' => 'Live',
                'detail' => '/api/v1/mobile/status uses the standard response envelope.',
                'tone' => 'success',
            ],
            [
                'label' => 'Feature flags',
                'status' => 'Planned',
                'detail' => 'User, tenant, and global resolution order.',
                'tone' => 'neutral',
            ],
            [
                'label' => 'Remote config',
                'status' => 'Planned',
                'detail' => 'Scoped config, versioning, fallback, rollback, and audit.',
                'tone' => 'neutral',
            ],
            [
                'label' => 'App versions',
                'status' => 'Planned',
                'detail' => 'Supported, recommended, deprecated, blocked, and internal-only builds.',
                'tone' => 'neutral',
            ],
            [
                'label' => 'Offline sync',
                'status' => 'Planned',
                'detail' => 'Replay windows, conflict modes, retry limits, and stale thresholds.',
                'tone' => 'neutral',
            ],
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'controlAreas' => $this->controlAreas(),
        ]);
    }
}
