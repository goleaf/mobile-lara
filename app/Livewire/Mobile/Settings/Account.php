<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Account settings')]
final class Account extends SettingsSectionPage
{
    protected const TITLE = 'Account settings';

    protected const DESCRIPTION = 'Manage identity, profile details, sessions, and account lifecycle actions.';

    protected const STATUS = 'Profile, sessions, and account deletion screens are available.';

    protected const ITEMS = [
        [
            'label' => 'Profile details',
            'description' => 'Open the profile screen for display name, email, and account metadata.',
            'route' => 'mobile.profile',
            'badge' => 'Live',
        ],
        [
            'label' => 'Device sessions',
            'description' => 'Review the current device session and remote-session API placeholder.',
            'route' => 'mobile.sessions',
            'badge' => 'Live',
        ],
        [
            'label' => 'Delete account',
            'description' => 'Open the protected account deletion confirmation flow.',
            'route' => 'mobile.account.delete',
            'badge' => 'Protected',
        ],
    ];
}
