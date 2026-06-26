<?php

namespace App\Livewire\Mobile\Settings;

use App\Services\MobileConfig\MobileRemoteConfigStore;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;

#[Title('Sync settings')]
final class Sync extends SettingsSectionPage
{
    protected const TITLE = 'Sync settings';

    protected const DESCRIPTION = 'Prepare background sync, retry behavior, conflicts, and offline state.';

    protected const STATUS = 'Dashboard sync indicators exist; detailed sync controls are placeholders.';

    private MobileRemoteConfigStore $remoteConfig;

    public function boot(MobileRemoteConfigStore $remoteConfig): void
    {
        $this->remoteConfig = $remoteConfig;
    }

    public function render(): View
    {
        $syncConfig = $this->remoteConfig->syncSettings();

        return view('livewire.mobile.settings.section', [
            'sectionTitle' => self::TITLE,
            'sectionDescription' => self::DESCRIPTION,
            'sectionStatus' => $syncConfig['manual_sync_enabled']
                ? "Manual sync is enabled by cached Admin/API config. Current batch limit is {$syncConfig['max_batch_size']}."
                : 'Manual sync is disabled by cached Admin/API config. Local queue status remains visible.',
            'sectionItems' => $this->sectionItems(),
        ]);
    }

    /**
     * @return list<array{key: string, label: string, description: string, url: string|null, badge: string|null}>
     */
    protected function sectionItems(): array
    {
        $syncConfig = $this->remoteConfig->syncSettings();

        return [
            ...parent::sectionItems(),
            [
                'key' => 'admin-api-sync-policy',
                'label' => 'Admin/API sync policy',
                'description' => $syncConfig['manual_sync_enabled']
                    ? "Manual sync can replay up to {$syncConfig['max_batch_size']} queued actions per batch when server sync endpoints are available."
                    : 'Manual sync controls stay disabled until Admin/API config and sync endpoints allow replay.',
                'url' => null,
                'badge' => 'Config',
            ],
        ];
    }

    protected const ITEMS = [
        [
            'label' => 'Dashboard sync status',
            'description' => 'Open the dashboard sync and offline status preview.',
            'route' => 'mobile.dashboard',
            'badge' => 'Live',
        ],
        [
            'label' => 'Conflict inbox',
            'description' => 'Review offline writes that need local or remote resolution.',
            'route' => 'mobile.conflicts.index',
            'badge' => 'Live',
        ],
        [
            'label' => 'Conflict handling',
            'description' => 'Placeholder for merge review and server reconciliation.',
            'badge' => 'Next',
        ],
    ];
}
