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
     * @return array<int, array{label: string, status: string, detail: string, tone: string, route?: string}>
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
                'status' => 'Live',
                'detail' => 'Global defaults plus tenant overrides with audit-backed mobile-safe resolution.',
                'tone' => 'success',
                'route' => 'admin.mobile.features',
            ],
            [
                'label' => 'Feature overrides',
                'status' => 'Live',
                'detail' => 'Tenant-scoped feature decisions resolved above global defaults.',
                'tone' => 'success',
                'route' => 'admin.mobile.feature-overrides',
            ],
            [
                'label' => 'Remote config',
                'status' => 'Live',
                'detail' => 'Global mobile defaults with JSON validation, audit, impact preview, and rollback.',
                'tone' => 'success',
                'route' => 'admin.mobile.config',
            ],
            [
                'label' => 'App versions',
                'status' => 'Live',
                'detail' => 'Supported, recommended, force update, maintenance, and audited rollback controls.',
                'tone' => 'success',
                'route' => 'admin.mobile.app-versions',
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
