<?php

namespace App\Livewire\Mobile;

use App\Services\MobileAuth\PinUnlockService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Confirm PIN')]
class PinConfirm extends Component
{
    public string $pin = '';

    public bool $hasPendingSetup = false;

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
        $this->hasPendingSetup = $this->pinUnlocks->hasPendingCreation();
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

    public function confirm(): void
    {
        $this->clearFeedback();
        $this->validate();

        if (! $this->pinUnlocks->hasPendingCreation()) {
            $this->hasPendingSetup = false;
            $this->error = 'Start PIN setup before confirming it.';
            $this->showToast($this->error, 'error');

            return;
        }

        if (! $this->pinUnlocks->confirmCreation($this->pin)) {
            $this->addError('pin', 'The confirmation PIN did not match.');
            $this->error = 'PIN confirmation failed.';
            $this->showToast($this->error, 'error');

            return;
        }

        $this->reset('pin');
        $this->redirectRoute('mobile.settings', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.mobile.pin-confirm');
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
}
