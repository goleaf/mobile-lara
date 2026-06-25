<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Login')]
class Login extends Component
{
    #[Validate]
    public string $email = '';

    #[Validate]
    public string $password = '';

    #[Validate]
    public bool $remember = false;

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
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    public function login(): void
    {
        $this->clearFeedback();

        try {
            $this->validate();
        } catch (ValidationException $exception) {
            $this->showToast('Fix the highlighted sign-in fields.', 'error');

            throw $exception;
        }

        $this->reset('password');

        $this->status = 'Sign-in details validated.';
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
            && trim($this->password) !== ''
            && ! $this->getErrorBag()->any();
    }

    public function render(): View
    {
        return view('livewire.mobile.login');
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
