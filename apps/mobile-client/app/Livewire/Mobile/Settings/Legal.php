<?php

namespace App\Livewire\Mobile\Settings;

use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileFeatureActions;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileConfig\MobileRemoteConfigStore;
use App\Services\Native\BrowserService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;

#[Title('Legal settings')]
final class Legal extends SettingsSectionPage
{
    use DispatchesToasts;
    use GuardsMobileFeatureActions;

    protected const TITLE = 'Legal settings';

    protected const DESCRIPTION = 'Review policies, accepted consent versions, and sync-ready legal state.';

    protected const STATUS = 'Terms, privacy, consent acceptance, and consent history routes are available.';

    public ?string $legalStatus = null;

    public ?string $legalError = null;

    private BrowserService $browsers;

    private MobileRemoteConfigStore $remoteConfig;

    public function boot(BrowserService $browsers, MobileAccessPolicy $mobileAccessPolicy, MobileRemoteConfigStore $remoteConfig): void
    {
        $this->browsers = $browsers;
        $this->mobileAccessPolicy = $mobileAccessPolicy;
        $this->remoteConfig = $remoteConfig;
    }

    public function openTerms(): void
    {
        $this->openConfiguredLegalUrl(
            url: $this->remoteConfig->legalSettings()['terms_url'],
            missingMessage: 'No remote terms URL is configured. Use the bundled terms screen.',
            successTitle: 'Terms opened',
        );
    }

    public function openPrivacy(): void
    {
        $this->openConfiguredLegalUrl(
            url: $this->remoteConfig->legalSettings()['privacy_url'],
            missingMessage: 'No remote privacy URL is configured. Use the bundled privacy screen.',
            successTitle: 'Privacy opened',
        );
    }

    public function render(): View
    {
        return view('livewire.mobile.settings.legal', [
            'legalBrowserPolicy' => $this->mobileFeatureDecision('native_browser'),
            'legalConfig' => $this->remoteConfig->legalSettings(),
            'legalConfigSnapshot' => $this->remoteConfig->snapshot(),
            'sectionDescription' => self::DESCRIPTION,
            'sectionItems' => $this->sectionItems(),
            'sectionStatus' => self::STATUS,
            'sectionTitle' => self::TITLE,
        ]);
    }

    private function openConfiguredLegalUrl(?string $url, string $missingMessage, string $successTitle): void
    {
        $this->legalStatus = null;
        $this->legalError = null;

        if ($url === null) {
            $this->legalError = $missingMessage;
            $this->toastWarning($missingMessage, 'Legal link unavailable');

            return;
        }

        if ($this->mobileFeatureDenied('native_browser', 'Legal link unavailable')) {
            $this->legalError = $this->mobileFeatureDecision('native_browser')['message'];

            return;
        }

        $result = $this->browsers->openInAppUrl($url);

        if ($result['success']) {
            $this->legalStatus = $result['message'];
            $this->toastSuccess($result['message'], $successTitle);

            return;
        }

        $this->legalError = $result['message'];
        $this->toastWarning($result['message'], 'Legal link unavailable');
    }

    protected const ITEMS = [
        [
            'label' => 'Terms of Service',
            'description' => 'Review the current terms content.',
            'route' => 'mobile.terms',
            'badge' => 'Live',
        ],
        [
            'label' => 'Privacy Policy',
            'description' => 'Review the current privacy policy content.',
            'route' => 'mobile.privacy',
            'badge' => 'Live',
        ],
        [
            'label' => 'Consent acceptance',
            'description' => 'Accept the current local consent version.',
            'route' => 'mobile.consent.accept',
            'badge' => 'Live',
        ],
        [
            'label' => 'Consent history',
            'description' => 'Review locally accepted versions and sync fields.',
            'route' => 'mobile.consent.history',
            'badge' => 'Live',
        ],
    ];
}
