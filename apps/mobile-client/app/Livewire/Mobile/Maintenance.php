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

#[Title('Maintenance')]
class Maintenance extends Component
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

    public function retryPolicy(): void
    {
        try {
            $this->bootstrap->refresh();
            $this->statusMessage = 'Maintenance policy refreshed.';
            $this->statusVariant = 'success';
        } catch (MobileApiException) {
            $this->statusMessage = 'Could not refresh the maintenance policy. Check your connection and try again.';
            $this->statusVariant = 'warning';
        }

        $this->refreshStateFromCache();
        $this->redirectIfResolved();
    }

    public function logout(): void
    {
        if (($this->appState['can_logout'] ?? false) !== true) {
            $this->statusMessage = 'Logout is disabled by the current maintenance policy.';
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
        return view('livewire.mobile.maintenance', [
            'policyRows' => $this->policyRows(),
        ]);
    }

    private function refreshStateFromCache(): void
    {
        $this->appState = $this->appStates->current();
    }

    private function redirectIfResolved(): void
    {
        if (($this->appState['maintenance_enabled'] ?? false) === true) {
            return;
        }

        if (($this->appState['force_update'] ?? false) === true || ($this->appState['optional_update'] ?? false) === true) {
            $this->redirect(route('mobile.update-required'), true);

            return;
        }

        $this->redirect(route('mobile.dashboard'), true);
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function policyRows(): array
    {
        return [
            ['label' => 'Policy state', 'value' => (string) ($this->appState['label'] ?? 'Unknown')],
            ['label' => 'Retry after', 'value' => (string) ($this->appState['retry_after_label'] ?? 'Not specified')],
            ['label' => 'Current version', 'value' => (string) ($this->appState['current_version'] ?? 'Unknown')],
            ['label' => 'Policy source', 'value' => (string) ($this->appState['policy_source'] ?? 'Not cached')],
            ['label' => 'Policy version', 'value' => (string) ($this->appState['policy_version'] ?? 'Not available')],
        ];
    }
}
