<?php

namespace App\Livewire\Mobile;

use App\Services\MobileAuth\AppUnlockStateService;
use App\Services\MobileAuth\BiometricUnlockService;
use App\Services\MobileAuth\PinUnlockService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Biometric\Completed;

#[Title('Unlock app')]
class AppUnlock extends Component
{
    public bool $biometricEnabled = false;

    public bool $pinEnabled = false;

    public bool $pinLocked = false;

    public int $pinLockoutSeconds = 0;

    public int $pinRemainingAttempts = 0;

    public string $pin = '';

    public ?string $pendingPromptId = null;

    public ?string $status = null;

    public ?string $error = null;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    protected AppUnlockStateService $unlockState;

    protected BiometricUnlockService $biometricUnlocks;

    protected PinUnlockService $pinUnlocks;

    public function boot(
        AppUnlockStateService $unlockState,
        BiometricUnlockService $biometricUnlocks,
        PinUnlockService $pinUnlocks,
    ): void {
        $this->unlockState = $unlockState;
        $this->biometricUnlocks = $biometricUnlocks;
        $this->pinUnlocks = $pinUnlocks;
    }

    public function mount(): void
    {
        $this->refreshUnlockState();
        $this->pendingPromptId = $this->biometricUnlocks->pendingPromptId();

        if ($this->unlockState->isUnlocked()) {
            $this->status = 'App is already unlocked.';
        }
    }

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'pin' => ['required', 'string', 'regex:/^\d{4,6}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'pin.regex' => 'Use 4 to 6 digits for your PIN.',
        ];
    }

    public function requestUnlock(): void
    {
        $this->clearFeedback();

        if (! $this->biometricUnlocks->isEnabled()) {
            $this->error = 'Biometric unlock is not enabled on this device.';
            $this->showToast($this->error, 'error');

            return;
        }

        if ($this->unlockState->isUnlocked()) {
            $this->redirectToProtectedContent();

            return;
        }

        $promptId = $this->biometricUnlocks->startUnlockPrompt();

        if (! is_string($promptId)) {
            $this->error = 'Unable to start biometric confirmation on this device.';
            $this->showToast($this->error, 'error');

            return;
        }

        $this->pendingPromptId = $promptId;
        $this->status = 'Check your device to continue.';
        $this->showToast($this->status, 'success');
    }

    public function unlockWithPin(): void
    {
        $this->clearFeedback();
        $this->validate();
        $this->refreshUnlockState();

        if (! $this->pinEnabled) {
            $this->error = 'Local PIN unlock is not enabled on this device.';
            $this->showToast($this->error, 'error');

            return;
        }

        if ($this->pinUnlocks->isLockedOut()) {
            $message = $this->pinLockoutMessage();
            $this->addError('pin', $message);
            $this->showToast($message, 'error');

            return;
        }

        if ($this->pinUnlocks->verifyForUnlock($this->pin)) {
            $this->reset('pin');
            $this->redirectToProtectedContent();

            return;
        }

        $this->reset('pin');
        $this->refreshUnlockState();

        $message = $this->pinUnlocks->isLockedOut()
            ? $this->pinLockoutMessage()
            : 'The PIN is incorrect. '.$this->pinRemainingAttempts.' attempts remain.';

        $this->addError('pin', $message);
        $this->showToast($message, 'error');
    }

    #[OnNative(Completed::class)]
    public function handleBiometricCompleted(bool $success, ?string $id = null): void
    {
        if ($this->biometricUnlocks->completeUnlockAttempt($success, $id)) {
            $this->status = 'Biometric unlock confirmed.';
            $this->showToast($this->status, 'success');
            $this->redirectToProtectedContent();

            return;
        }

        $this->pendingPromptId = null;
        $this->error = 'Biometric confirmation failed. Try again.';
        $this->showToast($this->error, 'error');
    }

    public function render(): View
    {
        return view('livewire.mobile.app-unlock');
    }

    private function clearFeedback(): void
    {
        $this->status = null;
        $this->error = null;
        $this->toastMessage = null;
        $this->resetValidation();
    }

    private function showToast(string $message, string $variant): void
    {
        $this->toastMessage = $message;
        $this->toastVariant = $variant;
    }

    private function redirectToProtectedContent(): void
    {
        $target = $this->unlockState->pullIntendedUrl(route('mobile.dashboard'));

        $this->redirect($target, true);
    }

    private function refreshUnlockState(): void
    {
        $this->biometricEnabled = $this->biometricUnlocks->isEnabled();
        $this->pinEnabled = $this->pinUnlocks->hasPin();
        $this->pinLocked = $this->pinUnlocks->isLockedOut();
        $this->pinLockoutSeconds = $this->pinUnlocks->lockoutSecondsRemaining();
        $this->pinRemainingAttempts = $this->pinUnlocks->remainingAttempts();
    }

    private function pinLockoutMessage(): string
    {
        return 'Too many failed PIN attempts. Try again in '.$this->pinUnlocks->lockoutSecondsRemaining().' seconds.';
    }
}
