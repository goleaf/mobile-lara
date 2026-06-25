<?php

namespace App\Livewire\Mobile\Conflicts;

use App\Models\MobileLocalOfflineAction;
use App\Services\MobileLocal\OfflineActionRepository;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Conflict detail')]
final class ConflictDetail extends Component
{
    public MobileLocalOfflineAction $offlineAction;

    public ?string $statusMessage = null;

    public string $statusVariant = 'info';

    private OfflineActionRepository $offlineActions;

    public function boot(OfflineActionRepository $offlineActions): void
    {
        $this->offlineActions = $offlineActions;
    }

    public function mount(MobileLocalOfflineAction $offlineAction): void
    {
        $this->offlineAction = $offlineAction;
    }

    public function keepLocal(): void
    {
        $this->offlineAction = $this->offlineActions->keepLocalConflict($this->offlineAction);
        $this->setStatusMessage('Local version will be retried on the next sync.', 'success');
    }

    public function acceptRemote(): void
    {
        $this->offlineAction = $this->offlineActions->acceptRemoteConflict($this->offlineAction);
        $this->setStatusMessage('Remote version accepted and the local action was cancelled.', 'success');
    }

    public function dismissConflict(): void
    {
        $this->offlineAction = $this->offlineActions->dismissConflict($this->offlineAction);
        $this->setStatusMessage('Conflict dismissed.', 'warning');
    }

    public function render(): View
    {
        return view('livewire.mobile.conflicts.conflict-detail', [
            'summaryRows' => $this->summaryRows(),
            'localPayloadJson' => $this->prettyJson($this->offlineAction->payload ?? []),
            'remotePayloadJson' => $this->prettyJson($this->remotePayload()),
            'serverPayloadJson' => $this->prettyJson($this->offlineAction->conflict_payload ?? []),
        ]);
    }

    /**
     * @return list<array{label: string, value: string|null}>
     */
    private function summaryRows(): array
    {
        return [
            ['label' => 'Action', 'value' => $this->offlineAction->action_type],
            ['label' => 'Method', 'value' => $this->offlineAction->method],
            ['label' => 'Endpoint', 'value' => $this->offlineAction->endpoint],
            ['label' => 'Status', 'value' => $this->offlineAction->status],
            ['label' => 'Conflict', 'value' => $this->offlineAction->conflict_status],
            ['label' => 'Local version', 'value' => $this->offlineAction->local_version],
            ['label' => 'Remote version', 'value' => $this->offlineAction->remote_version],
            ['label' => 'Last error', 'value' => $this->offlineAction->last_error],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function remotePayload(): array
    {
        $conflictPayload = $this->offlineAction->conflict_payload ?? [];
        $remotePayload = $conflictPayload['remote'] ?? [];

        return is_array($remotePayload) ? $remotePayload : [];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function prettyJson(array $payload): string
    {
        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    private function setStatusMessage(string $message, string $variant): void
    {
        $this->statusMessage = $message;
        $this->statusVariant = $variant;
    }
}
