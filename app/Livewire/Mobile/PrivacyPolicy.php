<?php

namespace App\Livewire\Mobile;

use App\Services\MobileConsent\MobileConsentService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Privacy Policy')]
class PrivacyPolicy extends Component
{
    protected MobileConsentService $mobileConsent;

    public function boot(MobileConsentService $mobileConsent): void
    {
        $this->mobileConsent = $mobileConsent;
    }

    public function render(): View
    {
        return view('livewire.mobile.privacy-policy', [
            'policy' => $this->mobileConsent->policy('privacy'),
            'isAccepted' => $this->mobileConsent->hasAcceptedCurrentVersion('privacy'),
        ]);
    }
}
