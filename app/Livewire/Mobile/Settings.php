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
}
