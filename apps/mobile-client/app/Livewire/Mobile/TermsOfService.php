<?php

namespace App\Livewire\Mobile;

use App\Services\MobileConsent\MobileConsentService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Terms of Service')]
class TermsOfService extends Component
{
    protected MobileConsentService $mobileConsent;

    public function boot(MobileConsentService $mobileConsent): void
    {
        $this->mobileConsent = $mobileConsent;
    }

    public function render(): View
    {
        return view('livewire.mobile.terms-of-service', [
            'policy' => $this->mobileConsent->policy('terms'),
            'isAccepted' => $this->mobileConsent->hasAcceptedCurrentVersion('terms'),
        ]);
    }
}
