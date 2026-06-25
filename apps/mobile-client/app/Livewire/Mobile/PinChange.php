<?php

namespace App\Livewire\Mobile;

use App\Services\MobileAuth\PinUnlockService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Change PIN')]
class PinChange extends Component
{
    public string $currentPin = '';

    public string $pin = '';

    public string $pin_confirmation = '';

    public bool $hasPin = false;

    public ?string $status = null;

    public ?string $error = null;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    protected PinUnlockService $pinUnlocks;

    public function boot(PinUnlockService $pinUnlocks): void
    {
        $this->pinUnlocks = $pinUnlocks;
    }

    public function mount(): void
    {
        $this->hasPin = $this->pinUnlocks->hasPin();
    }

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'currentPin' => ['required', 'string', 'regex:/^\d{4,6}$/'],
            'pin' => ['required', 'string', 'regex:/^\d{4,6}$/', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'currentPin.regex' => 'Use 4 to 6 digits for your current PIN.',
            'pin.regex' => 'Use 4 to 6 digits for your new PIN.',
            'pin.confirmed' => 'The new PIN confirmation does not match.',
        ];
    }

    public function change(): void
    {
        $this->clearFeedback();
        $this->validate();

        if (! $this->pinUnlocks->hasPin()) {
            $this->hasPin = false;
            $this->error = 'Create a PIN before changing it.';
            $this->showToast($this->error, 'error');

            return;
        }

        if ($this->pinUnlocks->isLockedOut()) {
            $this->addError('currentPin', $this->lockoutMessage());
            $this->showToast($this->lockoutMessage(), 'error');

            return;
        }

        if (! $this->pinUnlocks->changePin($this->currentPin, $this->pin)) {
            $message = $this->pinUnlocks->isLockedOut()
                ? $this->lockoutMessage()
                : 'The current PIN is incorrect.';

            $this->addError('currentPin', $message);
            $this->showToast($message, 'error');

            return;
        }

        $this->reset('currentPin', 'pin', 'pin_confirmation');
        $this->redirectRoute('mobile.settings', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.mobile.pin-change', [
            'remainingAttempts' => $this->pinUnlocks->remainingAttempts(),
            'lockoutSeconds' => $this->pinUnlocks->lockoutSecondsRemaining(),
        ]);
    }

    private function clearFeedback(): void
    {
        $this->status = null;
        $this->error = null;
        $this->toastMessage = null;
        $this->resetValidation();
    }

    private function lockoutMessage(): string
    {
        return 'Too many failed PIN attempts. Try again in '.$this->pinUnlocks->lockoutSecondsRemaining().' seconds.';
    }

    private function showToast(string $message, string $variant): void
    {
        $this->toastMessage = $message;
        $this->toastVariant = $variant;
    }
}
