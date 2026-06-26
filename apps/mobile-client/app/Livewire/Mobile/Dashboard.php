<?php

namespace App\Livewire\Mobile;

use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileAppState\MobileAppStateStore;
use App\Services\MobileConfig\MobileRemoteConfigStore;
use App\Services\MobileLocal\SettingsRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public bool $hasNetworkError = false;

    public bool $hasDashboardContent = true;

    public bool $isOffline = false;

    private MobileAccessPolicy $accessPolicy;

    private MobileAppStateStore $appStates;

    private MobileRemoteConfigStore $remoteConfig;

    private SettingsRepository $settings;

    public function boot(
        MobileAccessPolicy $accessPolicy,
        MobileAppStateStore $appStates,
        MobileRemoteConfigStore $remoteConfig,
        SettingsRepository $settings,
    ): void {
        $this->accessPolicy = $accessPolicy;
        $this->appStates = $appStates;
        $this->remoteConfig = $remoteConfig;
        $this->settings = $settings;
    }

    public function refreshDashboard(): void
    {
        $this->hasNetworkError = false;
        $this->hasDashboardContent = true;
        $this->isOffline = false;
    }

    public function render(): View
    {
        return view('livewire.mobile.dashboard', [
            'greetingName' => $this->greetingName(),
            'quickStats' => $this->quickStats(),
            'recentActivities' => $this->recentActivities(),
            'quickActions' => $this->quickActions(),
            'syncStatus' => $this->syncStatus(),
            'offlineStatus' => $this->offlineStatus(),
            'notificationPreview' => $this->notificationPreview(),
            'appState' => $this->appStates->current(),
        ]);
    }

    private function greetingName(): string
    {
        try {
            $name = Arr::get($this->settings->cachedBootstrapContext() ?? [], 'data.user.name');
        } catch (Throwable) {
            $name = null;
        }

        if (! is_string($name) || trim($name) === '') {
            return 'Mobile user';
        }

        return Str::of($name)->trim()->before(' ')->toString();
    }

    /**
     * @return array<int, array{key: string, label: string, value: string, description: string, variant: string}>
     */
    private function quickStats(): array
    {
        $stats = [
            [
                'key' => 'local_records',
                'label' => 'Local records',
                'value' => '12',
                'description' => 'Available offline',
                'variant' => 'success',
            ],
            [
                'key' => 'sync_status',
                'label' => 'Pending sync',
                'value' => '3',
                'description' => 'Queued changes',
                'variant' => 'warning',
            ],
            [
                'key' => 'notifications',
                'label' => 'New alerts',
                'value' => '2',
                'description' => 'Need review',
                'variant' => 'accent',
            ],
            [
                'key' => 'storage',
                'label' => 'Local cache',
                'value' => '72%',
                'description' => 'Device storage',
                'variant' => 'neutral',
            ],
        ];

        $widgets = $this->remoteConfig->dashboardWidgets();

        return array_values(array_filter(
            $stats,
            fn (array $stat): bool => in_array($stat['key'], $widgets, true),
        )) ?: $stats;
    }

    /**
     * @return array<int, array{id: string, title: string, description: string, time_label: string, variant: string}>
     */
    private function recentActivities(): array
    {
        return [
            [
                'id' => 'secure-storage-ready',
                'title' => 'Secure storage ready',
                'description' => 'Auth token store is prepared for NativePHP secure storage.',
                'time_label' => '2 min ago',
                'variant' => 'success',
            ],
            [
                'id' => 'profile-cache-updated',
                'title' => 'API profile ready',
                'description' => 'Profile data is loaded from the Admin/API account contract.',
                'time_label' => '18 min ago',
                'variant' => 'neutral',
            ],
            [
                'id' => 'offline-index-built',
                'title' => 'Offline index built',
                'description' => 'Search and settings screens are available without a live API.',
                'time_label' => 'Today',
                'variant' => 'accent',
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function quickActions(): array
    {
        return $this->accessPolicy->filterActions([
            [
                'key' => 'search',
                'label' => 'Search',
                'description' => 'Find mobile routes and cached records.',
                'route' => 'mobile.search',
            ],
            [
                'key' => 'activity',
                'label' => 'Activity',
                'description' => 'Review local device events.',
                'route' => 'mobile.activity',
            ],
            [
                'key' => 'records',
                'label' => 'Records',
                'description' => 'Manage local-first generic records.',
                'route' => 'mobile.records.index',
                'feature' => 'records',
                'permission' => 'records.view',
            ],
            [
                'key' => 'notifications',
                'label' => 'Notifications',
                'description' => 'Review alerts waiting on this device.',
                'route' => 'mobile.notifications',
                'feature' => 'notifications',
                'permission' => 'notifications.view',
            ],
            [
                'key' => 'sessions',
                'label' => 'Sessions',
                'description' => 'Check device session and logout tools.',
                'route' => 'mobile.sessions',
            ],
            [
                'key' => 'settings',
                'label' => 'Settings',
                'description' => 'Manage biometrics, PIN, and consent.',
                'route' => 'mobile.settings',
            ],
        ]);
    }

    /**
     * @return array{label: string, description: string, last_synced_label: string, queued_changes: int, next_sync_label: string, variant: string}
     */
    private function syncStatus(): array
    {
        return [
            'label' => 'Synced',
            'description' => 'Fake API sync completed successfully.',
            'last_synced_label' => '2 minutes ago',
            'queued_changes' => 3,
            'next_sync_label' => 'Automatic when the app is active',
            'variant' => 'success',
        ];
    }

    /**
     * @return array{label: string, description: string, cached_screens: int, updated_label: string, variant: string}
     */
    private function offlineStatus(): array
    {
        if ($this->isOffline) {
            return [
                'label' => 'Offline',
                'description' => 'Using cached dashboard data until a connection returns.',
                'cached_screens' => 7,
                'updated_label' => 'Cache refreshed today',
                'variant' => 'warning',
            ];
        }

        return [
            'label' => 'Online',
            'description' => 'Offline cache is ready if the network drops.',
            'cached_screens' => 7,
            'updated_label' => 'Cache refreshed today',
            'variant' => 'success',
        ];
    }

    /**
     * @return array<int, array{id: string, title: string, body: string, time_label: string, unread: bool}>
     */
    private function notificationPreview(): array
    {
        return [
            [
                'id' => 'background-sync',
                'title' => 'Background sync prepared',
                'body' => 'Queued work will be connected to the mobile API later.',
                'time_label' => 'Now',
                'unread' => true,
            ],
            [
                'id' => 'biometric-unlock',
                'title' => 'Biometric unlock available',
                'body' => 'Enable it from settings after device verification.',
                'time_label' => '1 hr ago',
                'unread' => false,
            ],
        ];
    }
}
