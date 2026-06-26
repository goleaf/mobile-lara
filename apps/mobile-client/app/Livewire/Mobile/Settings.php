<?php

namespace App\Livewire\Mobile;

use App\Services\MobileAuth\BiometricUnlockService;
use App\Services\MobileAuth\PinUnlockService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Settings')]
class Settings extends Component
{
    public bool $pushNotifications = true;

    public bool $privacyMode = false;

    public bool $compactAppearance = false;

    public bool $storageSaver = true;

    public bool $biometricUnlock = false;

    public bool $hasPinUnlock = false;

    public bool $hasNetworkError = false;

    public bool $hasSettings = true;

    public ?string $settingsStatus = null;

    public ?string $settingsError = null;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    protected BiometricUnlockService $biometricUnlocks;

    protected PinUnlockService $pinUnlocks;

    public function boot(BiometricUnlockService $biometricUnlocks, PinUnlockService $pinUnlocks): void
    {
        $this->biometricUnlocks = $biometricUnlocks;
        $this->pinUnlocks = $pinUnlocks;
    }

    public function mount(): void
    {
        $this->biometricUnlock = $this->biometricUnlocks->isEnabled();
        $this->hasPinUnlock = $this->pinUnlocks->hasPin();
    }

    public function saveSettings(): void
    {
        $this->hasNetworkError = false;
        $this->hasSettings = true;
        $this->clearFeedback();

        if (! $this->biometricUnlocks->setEnabled($this->biometricUnlock)) {
            $this->settingsError = 'Biometric unlock could not be updated on this device.';
            $this->showToast($this->settingsError, 'error');

            return;
        }

        $this->settingsStatus = $this->biometricUnlock
            ? 'Biometric unlock enabled.'
            : 'Biometric unlock disabled.';

        $this->showToast($this->settingsStatus, 'success');
        $this->hasPinUnlock = $this->pinUnlocks->hasPin();
    }

    public function retrySettings(): void
    {
        $this->hasNetworkError = false;
        $this->hasSettings = true;
        $this->hasPinUnlock = $this->pinUnlocks->hasPin();
        $this->clearFeedback();
    }

    public function render(): View
    {
        return view('livewire.mobile.settings', [
            'settingsSections' => $this->hasSettings ? $this->settingsSections() : [],
            'settings' => $this->hasSettings ? [
                ['label' => 'Notifications', 'property' => 'pushNotifications', 'description' => 'Allow mobile push and in-app alerts.'],
                ['label' => 'Privacy', 'property' => 'privacyMode', 'description' => 'Reduce visible details in shared spaces.'],
                ['label' => 'Appearance', 'property' => 'compactAppearance', 'description' => 'Use tighter spacing on smaller screens.'],
                ['label' => 'Storage', 'property' => 'storageSaver', 'description' => 'Prefer lighter mobile payloads.'],
                ['label' => 'Biometric unlock', 'property' => 'biometricUnlock', 'description' => 'Require biometric confirmation before protected screens.'],
            ] : [],
        ]);
    }

    private function clearFeedback(): void
    {
        $this->settingsStatus = null;
        $this->settingsError = null;
        $this->toastMessage = null;
    }

    private function showToast(string $message, string $variant): void
    {
        $this->toastMessage = $message;
        $this->toastVariant = $variant;
    }

    /**
     * @return list<array{key: string, title: string, description: string, route: string, badge: string}>
     */
    private function settingsSections(): array
    {
        return [
            [
                'key' => 'account',
                'title' => 'Account',
                'description' => 'Profile, sessions, and account lifecycle.',
                'route' => 'mobile.settings.account',
                'badge' => 'Core',
            ],
            [
                'key' => 'workspace',
                'title' => 'Workspace',
                'description' => 'Current tenant, available tenants, and API-controlled switching.',
                'route' => 'mobile.settings.workspace',
                'badge' => 'Tenant',
            ],
            [
                'key' => 'security',
                'title' => 'Security',
                'description' => 'PIN, biometrics, unlock, and protected access.',
                'route' => 'mobile.settings.security',
                'badge' => 'Secure',
            ],
            [
                'key' => 'notifications',
                'title' => 'Notifications',
                'description' => 'Push, in-app alerts, quiet hours, and previews.',
                'route' => 'mobile.settings.notifications',
                'badge' => 'Alerts',
            ],
            [
                'key' => 'appearance',
                'title' => 'Appearance',
                'description' => 'Theme, density, text scale, and display preferences.',
                'route' => 'mobile.settings.appearance',
                'badge' => 'UI',
            ],
            [
                'key' => 'storage',
                'title' => 'Storage',
                'description' => 'Offline cache, secure values, and local cleanup.',
                'route' => 'mobile.settings.storage',
                'badge' => 'Device',
            ],
            [
                'key' => 'sync',
                'title' => 'Sync',
                'description' => 'Background sync, retries, conflicts, and offline queue.',
                'route' => 'mobile.settings.sync',
                'badge' => 'Offline',
            ],
            [
                'key' => 'billing',
                'title' => 'Billing',
                'description' => 'Plan, subscription state, limits, usage, and billing portal status.',
                'route' => 'mobile.billing',
                'badge' => 'Plan',
            ],
            [
                'key' => 'permissions',
                'title' => 'Permissions',
                'description' => 'Camera, location, microphone, scanner, files, and biometrics.',
                'route' => 'mobile.settings.permissions',
                'badge' => 'Native',
            ],
            [
                'key' => 'support',
                'title' => 'Support',
                'description' => 'Help, contact, diagnostics, and troubleshooting.',
                'route' => 'mobile.settings.support',
                'badge' => 'Help',
            ],
            [
                'key' => 'legal',
                'title' => 'Legal',
                'description' => 'Terms, privacy, consent acceptance, and consent history.',
                'route' => 'mobile.settings.legal',
                'badge' => 'Policy',
            ],
            [
                'key' => 'developer',
                'title' => 'Developer/debug',
                'description' => 'Runtime diagnostics, NativePHP checks, and debug tools.',
                'route' => 'mobile.settings.developer',
                'badge' => 'Debug',
            ],
        ];
    }
}
