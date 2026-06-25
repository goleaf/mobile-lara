<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Reset password')]
class ResetPassword extends Component
{
    #[Validate]
    public string $token = '';

    #[Validate]
    public string $email = '';

    #[Validate]
    public string $password = '';

    #[Validate]
    public string $password_confirmation = '';

    public ?string $status = null;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    public function mount(?string $token = null): void
    {
        $this->token = $token ?? '';
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', PasswordRule::defaults()],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    public function resetPassword(): void
    {
        $this->clearFeedback();

        try {
            $this->validate();
        } catch (ValidationException $exception) {
            $this->showToast('Fix the highlighted password reset fields.', 'error');

            throw $exception;
        }

        $this->reset('password', 'password_confirmation');

        $this->status = 'New password details validated.';
        $this->showToast($this->status, 'success');
    }

    public function updated(): void
    {
        $this->clearFeedback();
    }

    #[Computed]
    public function canSubmit(): bool
    {
        return trim($this->token) !== ''
            && filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false
            && mb_strlen($this->password) >= 8
            && $this->password === $this->password_confirmation
            && ! $this->getErrorBag()->any();
    }

    public function render(): View
    {
        return view('livewire.mobile.reset-password');
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
