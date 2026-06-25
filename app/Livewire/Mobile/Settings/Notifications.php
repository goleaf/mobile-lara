<?php

namespace App\Livewire\Mobile\Settings;

use Livewire\Attributes\Title;

#[Title('Notification settings')]
final class Notifications extends SettingsSectionPage
{
    protected const TITLE = 'Notification settings';

    protected const DESCRIPTION = 'Prepare mobile push, in-app alerts, quiet hours, and notification previews.';

    protected const STATUS = 'The notifications inbox exists; preference controls are placeholders.';

    protected const ITEMS = [
        [
            'label' => 'Notification inbox',
            'description' => 'Open current notification previews and unread states.',
            'route' => 'mobile.notifications',
            'badge' => 'Live',
        ],
        [
            'label' => 'Push preferences',
            'description' => 'Placeholder for push categories, quiet hours, and delivery channels.',
            'badge' => 'Next',
        ],
        [
            'label' => 'In-app toasts',
            'description' => 'Placeholder for success, warning, info, and persistent notification preferences.',
            'badge' => 'Next',
        ],
    ];
}
