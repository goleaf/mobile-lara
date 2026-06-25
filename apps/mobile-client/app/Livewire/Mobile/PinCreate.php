<?php

namespace App\Livewire\Mobile;

use App\Services\MobileAuth\PinUnlockService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create PIN')]
class PinCreate extends Component
{
    public string $pin = '';

    public ?string $status = null;

    public ?string $error = null;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    protected PinUnlockService $pinUnlocks;

    public function boot(PinUnlockService $pinUnlocks): void
    {
        $this->pinUnlocks = $pinUnlocks;
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

    public function create(): void
    {
        $this->clearFeedback();
        $this->validate();

        if ($this->pinUnlocks->hasPin()) {
            $this->error = 'A local PIN already exists.';
            $this->showToast($this->error, 'error');

            return;
        }

        $this->pinUnlocks->startCreation($this->pin);
        $this->reset('pin');

        $this->redirectRoute('mobile.pin.confirm', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.mobile.pin-create');
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
