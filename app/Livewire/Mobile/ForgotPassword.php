<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Forgot password')]
class ForgotPassword extends Component
{
    #[Validate]
    public string $email = '';

    public ?string $status = null;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
        ];
    }

    public function sendResetLink(): void
    {
        $this->clearFeedback();

        try {
            $this->validate();
        } catch (ValidationException $exception) {
            $this->showToast('Enter a valid email before continuing.', 'error');

            throw $exception;
        }

        $this->status = 'Password reset instructions are ready to send.';
        $this->showToast($this->status, 'success');
    }

    public function updated(): void
    {
        $this->clearFeedback();
    }

    #[Computed]
    public function canSubmit(): bool
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false
            && ! $this->getErrorBag()->any();
    }

    public function render(): View
    {
        return view('livewire.mobile.forgot-password');
    }

    private function clearFeedback(): void
    {
        $this->status = null;
        $this->toastMessage = null;
    }

    private function showToast(string $message, string $variant): void
    {
        $this->toastMessage = $message;
        $this->toastVariant = $variant;
    }
}
