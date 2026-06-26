<?php

namespace App\Livewire\Mobile;

use App\Actions\MobileAuth\RequestAccountDeletionAction;
use App\Services\MobileAuth\BiometricUnlockService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Biometric\Completed;

#[Title('Delete account')]
class AccountDeletion extends Component
{
    public string $confirmationMethod = 'password';

    public string $password = '';

    public bool $confirmationAccepted = false;

    public bool $biometricConfirmed = false;

    public ?string $pendingPromptId = null;

    public ?string $status = null;

    public ?string $error = null;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    /** @var array<string, string|null> */
    public array $deletionRequest = [];

    protected BiometricUnlockService $biometricUnlocks;

    protected RequestAccountDeletionAction $requestAccountDeletion;

    public function boot(
        BiometricUnlockService $biometricUnlocks,
        RequestAccountDeletionAction $requestAccountDeletion,
    ): void {
        $this->biometricUnlocks = $biometricUnlocks;
        $this->requestAccountDeletion = $requestAccountDeletion;
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function rules(): array
    {
        return [
            'confirmationMethod' => ['required', Rule::in(['password', 'biometric'])],
            'confirmationAccepted' => ['accepted'],
            'password' => [
                Rule::requiredIf($this->confirmationMethod === 'password'),
                'string',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'confirmationAccepted.accepted' => 'Confirm that you understand account deletion is permanent.',
            'password.required' => 'Enter your password to confirm account deletion.',
        ];
    }

    public function updatedConfirmationMethod(): void
    {
        $this->clearFeedback();
        $this->resetValidation();
        $this->reset('password');

        if ($this->confirmationMethod === 'password') {
            $this->biometricConfirmed = false;
            $this->pendingPromptId = null;
        }
    }

    public function confirmWithBiometric(): void
    {
        $this->confirmationMethod = 'biometric';
        $this->clearFeedback();
        $this->resetValidation();

        if (! $this->biometricUnlocks->isEnabled()) {
            $this->error = 'Biometric confirmation is not enabled on this device.';
            $this->addError('confirmationMethod', 'Enable biometric unlock in settings or confirm with your password.');
            $this->showToast($this->error, 'error');

            return;
        }

        $promptId = $this->biometricUnlocks->startUnlockPrompt();

        if (! is_string($promptId)) {
            $this->error = 'Unable to start biometric confirmation on this device.';
            $this->addError('confirmationMethod', $this->error);
            $this->showToast($this->error, 'error');

            return;
        }

        $this->pendingPromptId = $promptId;
        $this->status = 'Check your device to confirm account deletion.';
        $this->showToast($this->status, 'success');
    }

    #[OnNative(Completed::class)]
    public function handleBiometricCompleted(bool $success, ?string $id = null): void
    {
        if ($this->biometricUnlocks->completeUnlockAttempt($success, $id)) {
            $this->pendingPromptId = null;
            $this->biometricConfirmed = true;
            $this->status = 'Biometric confirmation received.';
            $this->showToast($this->status, 'success');

            return;
        }

        $this->pendingPromptId = null;
        $this->biometricConfirmed = false;
        $this->error = 'Biometric confirmation failed. Try again or use your password.';
        $this->addError('confirmationMethod', $this->error);
        $this->showToast($this->error, 'error');
    }

    public function deleteAccount(): void
    {
        $this->clearFeedback();
        $this->validate();

        $confirmedBy = $this->confirmationMethod;

        if ($confirmedBy === 'password' && ! $this->passwordMatchesAuthenticatedUser()) {
            return;
        }

        if ($confirmedBy === 'biometric' && ! $this->biometricConfirmed) {
            $this->error = 'Complete biometric confirmation before deleting your account.';
            $this->addError('confirmationMethod', $this->error);
            $this->showToast($this->error, 'error');

            return;
        }

        $this->deletionRequest = $this->requestAccountDeletion->handle(Auth::user(), $confirmedBy);
        $this->reset('password');
        $this->biometricConfirmed = false;
        $this->status = (string) $this->deletionRequest['message'];
        $this->showToast($this->status, 'success');
    }

    #[Computed]
    public function canSubmit(): bool
    {
        return $this->confirmationAccepted
            && match ($this->confirmationMethod) {
                'password' => trim($this->password) !== '',
                'biometric' => $this->biometricConfirmed,
                default => false,
            };
    }

    public function render(): View
    {
        return view('livewire.mobile.account-deletion');
    }

    private function passwordMatchesAuthenticatedUser(): bool
    {
        $user = Auth::user();

        if (is_null($user)) {
            $this->error = 'Password confirmation requires a signed-in server account.';
            $this->addError('password', $this->error);
            $this->showToast($this->error, 'error');

            return false;
        }

        return true;
    }

    private function clearFeedback(): void
    {
        $this->status = null;
        $this->error = null;
        $this->toastMessage = null;
        $this->deletionRequest = [];
    }

    private function showToast(string $message, string $variant): void
    {
        $this->toastMessage = $message;
        $this->toastVariant = $variant;
    }
}
