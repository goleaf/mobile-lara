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
                'status' => 'Live',
                'detail' => 'Lifecycle, tenant status, subscription state, settings, and isolation controls.',
                'tone' => 'success',
                'route' => 'admin.tenants',
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
                'detail' => 'Tenant and user feature decisions resolved above global defaults.',
                'tone' => 'success',
                'route' => 'admin.mobile.feature-overrides',
            ],
            [
                'label' => 'User overrides',
                'status' => 'Live',
                'detail' => 'User-specific feature decisions resolved above tenant overrides.',
                'tone' => 'success',
                'route' => 'admin.mobile.user-feature-overrides',
            ],
            [
                'label' => 'Remote config',
                'status' => 'Live',
                'detail' => 'Global and tenant mobile defaults with JSON validation, audit, impact preview, and rollback.',
                'tone' => 'success',
                'route' => 'admin.mobile.config',
            ],
            [
                'label' => 'Tenant config',
                'status' => 'Live',
                'detail' => 'Tenant-specific remote config merged above global defaults.',
                'tone' => 'success',
                'route' => 'admin.mobile.tenant-config',
            ],
            [
                'label' => 'App versions',
                'status' => 'Live',
                'detail' => 'Supported, recommended, force update, maintenance, and audited rollback controls.',
                'tone' => 'success',
                'route' => 'admin.mobile.app-versions',
            ],
            [
                'label' => 'Diagnostics',
                'status' => 'Live',
                'detail' => 'Privacy-filtered mobile troubleshooting snapshots uploaded through the API.',
                'tone' => 'success',
                'route' => 'admin.mobile.diagnostics',
            ],
            [
                'label' => 'Offline sync',
                'status' => 'Live',
                'detail' => 'Replay outcomes, conflict records, rejection reasons, and acknowledgement state.',
                'tone' => 'success',
                'route' => 'admin.mobile.sync',
            ],
            [
                'label' => 'Support queue',
                'status' => 'Live',
                'detail' => 'Requester-safe ticket triage, assignment, status, priority, replies, and audit history.',
                'tone' => 'success',
                'route' => 'admin.support',
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
