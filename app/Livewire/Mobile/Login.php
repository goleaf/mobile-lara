<?php

namespace App\Livewire\Mobile;

use App\Services\MobileAuth\MobileSessionService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
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

    protected MobileSessionService $mobileSessions;

    public function boot(MobileSessionService $mobileSessions): void
    {
        $this->mobileSessions = $mobileSessions;
    }

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

        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            $this->showToast('These credentials do not match our records.', 'error');

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        session()->regenerate();

        $this->reset('password');
        $this->mobileSessions->recordLogin();

        $this->status = 'Signed in.';
        $this->showToast($this->status, 'success');
        $this->redirect(route('mobile.dashboard'), navigate: true);
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
