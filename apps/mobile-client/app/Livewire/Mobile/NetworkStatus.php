<?php

namespace App\Livewire\Mobile;

use App\Contracts\MobileLocal\MobileNetworkState;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class NetworkStatus extends Component
{
    public bool $isOnline = true;

    public string $stateLabel = 'Online';

    public string $stateVariant = 'success';

    public string $connectionTypeLabel = 'Unknown';

    public string $meteredLabel = 'Unknown';

    public string $constrainedLabel = 'Unknown';

    public string $sourceLabel = 'Assumed online';

    public bool $nativeStatusAvailable = false;

    public bool $fallbackCheckUsed = false;

    public ?string $fallbackUrl = null;

    private MobileNetworkState $networkState;

    public function boot(MobileNetworkState $networkState): void
    {
        $this->networkState = $networkState;
    }

    public function mount(): void
    {
        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $status = $this->networkState->status();

        $this->isOnline = $status->isOnline;
        $this->stateLabel = $status->stateLabel();
        $this->stateVariant = $status->variant();
        $this->connectionTypeLabel = $status->connectionTypeLabel();
        $this->meteredLabel = $status->meteredLabel();
        $this->constrainedLabel = $status->constrainedLabel();
        $this->sourceLabel = $status->sourceLabel();
        $this->nativeStatusAvailable = $status->nativeStatusAvailable;
        $this->fallbackCheckUsed = $status->fallbackCheckUsed;
        $this->fallbackUrl = $status->fallbackUrl;
    }

    public function render(): View
    {
        return view('livewire.mobile.network-status', [
            'statusRows' => $this->statusRows(),
        ]);
    }

    /**
     * @return list<array{key: string, label: string, value: string}>
     */
    private function statusRows(): array
    {
        return [
            [
                'key' => 'connection-type',
                'label' => 'Connection type',
                'value' => $this->connectionTypeLabel,
            ],
            [
                'key' => 'metered',
                'label' => 'Metered connection',
                'value' => $this->meteredLabel,
            ],
            [
                'key' => 'constrained',
                'label' => 'Low data mode',
                'value' => $this->constrainedLabel,
            ],
            [
                'key' => 'source',
                'label' => 'Source',
                'value' => $this->sourceLabel,
            ],
        ];
    }
}
