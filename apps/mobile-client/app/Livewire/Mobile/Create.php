<?php

namespace App\Livewire\Mobile;

use App\Services\MobileAccess\MobileAccessPolicy;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create')]
class Create extends Component
{
    private MobileAccessPolicy $accessPolicy;

    public function boot(MobileAccessPolicy $accessPolicy): void
    {
        $this->accessPolicy = $accessPolicy;
    }

    public function render(): View
    {
        return view('livewire.mobile.create', [
            'createActions' => $this->createActions(),
        ]);
    }

    /**
     * @return list<array{label: string, description: string, badge: string, route: string, feature: string, permission?: string}>
     */
    private function createActions(): array
    {
        return $this->accessPolicy->filterActions([
            [
                'label' => 'New record',
                'description' => 'Capture a generic local record and sync it later.',
                'badge' => 'Offline ready',
                'route' => 'mobile.records.create',
                'feature' => 'records',
                'permission' => 'records.create',
            ],
            [
                'label' => 'Scan item',
                'description' => 'Prepare scanner-based creation for NativePHP.',
                'badge' => 'Scanner',
                'route' => 'mobile.scanner',
                'feature' => 'native_scanner',
            ],
            [
                'label' => 'Upload file',
                'description' => 'Queue file input for the mobile file plugin.',
                'badge' => 'Files',
                'route' => 'mobile.files',
                'feature' => 'native_files',
            ],
        ]);
    }
}
