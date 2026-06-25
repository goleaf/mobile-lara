<?php

namespace App\Livewire\Mobile\Settings;

use Illuminate\Contracts\View\View;
use Livewire\Component;

abstract class SettingsSectionPage extends Component
{
    /**
     * @var list<array{label: string, description: string, route?: string|null, badge?: string|null}>
     */
    protected const ITEMS = [];

    protected const TITLE = 'Settings';

    protected const DESCRIPTION = 'Mobile settings placeholder.';

    protected const STATUS = 'This settings section is ready for implementation.';

    public function render(): View
    {
        return view('livewire.mobile.settings.section', [
            'sectionTitle' => static::TITLE,
            'sectionDescription' => static::DESCRIPTION,
            'sectionStatus' => static::STATUS,
            'sectionItems' => $this->sectionItems(),
        ]);
    }

    /**
     * @return list<array{key: string, label: string, description: string, url: string|null, badge: string|null}>
     */
    private function sectionItems(): array
    {
        return array_map(
            static fn (array $item): array => [
                'key' => str($item['label'])->slug()->toString(),
                'label' => $item['label'],
                'description' => $item['description'],
                'url' => isset($item['route']) && is_string($item['route']) ? route($item['route']) : null,
                'badge' => $item['badge'] ?? null,
            ],
            static::ITEMS,
        );
    }
}
