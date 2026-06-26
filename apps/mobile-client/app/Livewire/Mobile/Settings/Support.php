<?php

namespace App\Livewire\Mobile\Settings;

use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileFeatureActions;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\Native\BrowserService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;

#[Title('Support settings')]
final class Support extends SettingsSectionPage
{
    use DispatchesToasts;
    use GuardsMobileFeatureActions;

    protected const TITLE = 'Support settings';

    protected const DESCRIPTION = 'Prepare help, diagnostics, contact, and troubleshooting entry points.';

    protected const STATUS = 'Support center, diagnostics, and contact paths are wired as mobile-first recovery surfaces.';

    public ?string $supportStatus = null;

    public ?string $supportError = null;

    private BrowserService $browsers;

    public function boot(BrowserService $browsers, MobileAccessPolicy $mobileAccessPolicy): void
    {
        $this->browsers = $browsers;
        $this->mobileAccessPolicy = $mobileAccessPolicy;
    }

    public function openSupportCenter(): void
    {
        $this->supportStatus = null;
        $this->supportError = null;

        if ($this->mobileFeatureDenied('native_browser', 'Support unavailable')) {
            $this->supportError = $this->mobileFeatureDecision('native_browser')['message'];

            return;
        }

        $result = $this->browsers->openSupportCenter();

        if ($result['success']) {
            $this->supportStatus = $result['message'];
            $this->toastSuccess($result['message'], 'Support opened');

            return;
        }

        $this->supportError = $result['message'];
        $this->toastWarning($result['message'], 'Support unavailable');
    }

    public function render(): View
    {
        return view('livewire.mobile.settings.support', [
            'sectionDescription' => self::DESCRIPTION,
            'sectionItems' => $this->sectionItems(),
            'sectionStatus' => self::STATUS,
            'sectionTitle' => self::TITLE,
            'supportBrowserPolicy' => $this->mobileFeatureDecision('native_browser'),
        ]);
    }

    protected const ITEMS = [
        [
            'label' => 'Help center',
            'description' => 'Open the configured support center in the native in-app browser.',
            'badge' => 'Native',
        ],
        [
            'label' => 'Contact support',
            'description' => 'Placeholder for message, logs, and ticket submission.',
            'badge' => 'Next',
        ],
        [
            'label' => 'Diagnostics',
            'description' => 'Open developer diagnostics while support export is pending.',
            'route' => 'mobile.debug',
            'badge' => 'Live',
        ],
    ];
}
