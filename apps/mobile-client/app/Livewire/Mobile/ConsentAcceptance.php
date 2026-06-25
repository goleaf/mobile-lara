<?php

namespace App\Livewire\Mobile;

use App\Services\MobileConsent\MobileConsentService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Consent')]
class ConsentAcceptance extends Component
{
    public bool $termsAccepted = false;

    public bool $privacyAccepted = false;

    public ?string $status = null;

    public ?string $toastMessage = null;

    public string $toastVariant = 'success';

    protected MobileConsentService $mobileConsent;

    public function boot(MobileConsentService $mobileConsent): void
    {
        $this->mobileConsent = $mobileConsent;
    }

    public function mount(): void
    {
        $this->termsAccepted = $this->mobileConsent->hasAcceptedCurrentVersion('terms');
        $this->privacyAccepted = $this->mobileConsent->hasAcceptedCurrentVersion('privacy');
    }

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'termsAccepted' => ['accepted'],
            'privacyAccepted' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'termsAccepted.accepted' => 'Accept the current Terms of Service version.',
            'privacyAccepted.accepted' => 'Accept the current Privacy Policy version.',
        ];
    }

    public function acceptConsents(): void
    {
        $this->clearFeedback();
        $this->validate();

        $this->mobileConsent->acceptLatest();

        $this->status = 'Consent accepted locally. Ready to sync when the server API is connected.';
        $this->showToast($this->status, 'success');
    }

    #[Computed]
    public function canSubmit(): bool
    {
        return $this->termsAccepted && $this->privacyAccepted;
    }

    public function render(): View
    {
        return view('livewire.mobile.consent-acceptance', [
            'policies' => $this->mobileConsent->policies(),
            'hasAcceptedCurrentVersions' => $this->mobileConsent->hasAcceptedCurrentVersions(),
            'syncPayload' => $this->mobileConsent->syncPayload(),
        ]);
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
