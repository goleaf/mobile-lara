<?php

namespace App\Livewire\Mobile;

use App\Services\MobileConsent\MobileConsentService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Consent history')]
class ConsentHistory extends Component
{
    protected MobileConsentService $mobileConsent;

    public function boot(MobileConsentService $mobileConsent): void
    {
        $this->mobileConsent = $mobileConsent;
    }

    public function render(): View
    {
        return view('livewire.mobile.consent-history', [
            'history' => $this->mobileConsent->acceptedHistory(),
            'syncPayload' => $this->mobileConsent->syncPayload(),
            'hasAcceptedCurrentVersions' => $this->mobileConsent->hasAcceptedCurrentVersions(),
        ]);
    }
}
