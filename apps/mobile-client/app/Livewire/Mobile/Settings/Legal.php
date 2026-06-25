<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Legal settings')]
final class Legal extends SettingsSectionPage
{
    protected const TITLE = 'Legal settings';

    protected const DESCRIPTION = 'Review policies, accepted consent versions, and sync-ready legal state.';

    protected const STATUS = 'Terms, privacy, consent acceptance, and consent history routes are available.';

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
