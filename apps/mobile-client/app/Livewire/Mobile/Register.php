<?php

namespace App\Livewire\Mobile;

use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\MobileApiSessionBridge;
use App\Services\MobileAuth\MobileAuthApiService;
use App\Services\MobileBootstrap\MobileBootstrapService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Register')]
class Register extends Component
{
    #[Validate]
    public string $name = '';

    #[Validate]
    public string $email = '';

    #[Validate]
    public string $password = '';

    #[Validate]
    public string $password_confirmation = '';

    #[Validate]
    public bool $termsAccepted = false;

    public ?string $status = null;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    protected MobileAuthApiService $authApi;

    protected MobileApiSessionBridge $apiSessions;

    protected MobileBootstrapService $bootstrap;

    public function boot(
        MobileAuthApiService $authApi,
        MobileApiSessionBridge $apiSessions,
        MobileBootstrapService $bootstrap,
    ): void {
        $this->authApi = $authApi;
        $this->apiSessions = $apiSessions;
        $this->bootstrap = $bootstrap;
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', PasswordRule::defaults()],
            'password_confirmation' => ['required', 'same:password'],
            'termsAccepted' => ['accepted'],
        ];
    }

    public function register(): void
    {
        $this->clearFeedback();

        try {
            $this->validate();
        } catch (ValidationException $exception) {
            $this->showToast('Fix the highlighted account fields.', 'error');

            throw $exception;
        }

        try {
            $envelope = $this->authApi->register(
                name: $this->name,
                email: $this->email,
                password: $this->password,
                passwordConfirmation: $this->password_confirmation,
            );
            $this->apiSessions->start($envelope);
            $this->bootstrap->refresh();
        } catch (MobileApiException $exception) {
            $this->showToast($exception->getMessage(), 'error');

            throw ValidationException::withMessages([
                'email' => $exception->getMessage(),
            ]);
        }

        $this->reset('password', 'password_confirmation');

        $this->status = 'Account created.';
        $this->showToast($this->status, 'success');
        $this->redirect(route('mobile.dashboard'), navigate: true);
    }

    public function updated(string $propertyName): void
    {
        $this->clearFeedback();
        $this->validateOnly($propertyName);

        if ($propertyName === 'password' && $this->password_confirmation !== '') {
            $this->validateOnly('password_confirmation');
        }
    }

    #[Computed]
    public function canSubmit(): bool
    {
        return trim($this->name) !== ''
            && filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false
            && mb_strlen($this->password) >= 8
            && $this->password === $this->password_confirmation
            && $this->termsAccepted
            && ! $this->getErrorBag()->any();
    }

    public function render(): View
    {
        return view('livewire.mobile.register');
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
