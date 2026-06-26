<?php

namespace App\Livewire\Mobile;

use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAppState\MobileAppStateStore;
use App\Services\MobileAuth\MobileAuthApiService;
use App\Services\MobileAuth\MobileSessionService;
use App\Services\MobileBootstrap\MobileBootstrapService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Update required')]
class ForceUpdate extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $appState = [];

    public ?string $statusMessage = null;

    public string $statusVariant = 'success';

    protected MobileAppStateStore $appStates;

    protected MobileBootstrapService $bootstrap;

    protected MobileSessionService $mobileSessions;

    protected MobileAuthApiService $authApi;

    public function boot(
        MobileAppStateStore $appStates,
        MobileBootstrapService $bootstrap,
        MobileSessionService $mobileSessions,
        MobileAuthApiService $authApi,
    ): void {
        $this->appStates = $appStates;
        $this->bootstrap = $bootstrap;
        $this->mobileSessions = $mobileSessions;
        $this->authApi = $authApi;
    }

    public function mount(): void
    {
        $this->refreshStateFromCache();
    }

    public function checkAgain(): void
    {
        try {
            $this->bootstrap->refresh();
            $this->statusMessage = 'App policy refreshed.';
            $this->statusVariant = 'success';
        } catch (MobileApiException) {
            $this->statusMessage = 'Could not refresh the app policy. Check your connection and try again.';
            $this->statusVariant = 'warning';
        }

        $this->refreshStateFromCache();
        $this->redirectIfResolved();
    }

    public function logout(): void
    {
        if (($this->appState['can_logout'] ?? false) !== true) {
            $this->statusMessage = 'Logout is disabled by the current app-version policy.';
            $this->statusVariant = 'warning';

            return;
        }

        try {
            $this->authApi->logout();
        } catch (MobileApiException) {
        }

        $this->mobileSessions->logoutCurrentSession();

        $this->redirect(route('mobile.login'), true);
    }

    public function render(): View
    {
        return view('livewire.mobile.force-update', [
            'versionRows' => $this->versionRows(),
        ]);
    }

    private function refreshStateFromCache(): void
    {
        $this->appState = $this->appStates->current();
    }

    private function redirectIfResolved(): void
    {
        if (($this->appState['maintenance_enabled'] ?? false) === true) {
            $this->redirect(route('mobile.maintenance'), true);

            return;
        }

        if (($this->appState['force_update'] ?? false) === false && ($this->appState['optional_update'] ?? false) === false) {
            $this->redirect(route('mobile.dashboard'), true);
        }
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function versionRows(): array
    {
        return [
            ['label' => 'Current version', 'value' => (string) ($this->appState['current_version'] ?? 'Unknown')],
            ['label' => 'Version code', 'value' => (string) ($this->appState['version_code'] ?? 'Unknown')],
            ['label' => 'Minimum supported', 'value' => (string) ($this->appState['minimum_supported_version'] ?? 'Not specified')],
            ['label' => 'Recommended', 'value' => (string) ($this->appState['minimum_recommended_version'] ?? 'Not specified')],
            ['label' => 'Latest', 'value' => (string) ($this->appState['latest_version'] ?? 'Not specified')],
            ['label' => 'Policy state', 'value' => (string) ($this->appState['label'] ?? 'Unknown')],
        ];
    }
}
