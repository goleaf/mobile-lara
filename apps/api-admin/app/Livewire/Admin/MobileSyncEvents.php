<?php

namespace App\Livewire\Admin;

use App\Models\MobileSyncEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Mobile Sync Monitor')]
final class MobileSyncEvents extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $outcome = '';

    public ?int $selectedEventId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedOutcome(): void
    {
        if ($this->outcome !== '' && ! array_key_exists($this->outcome, $this->outcomeOptions())) {
            $this->outcome = '';
        }

        $this->resetPage();
    }

    public function selectEvent(int $eventId): void
    {
        $event = MobileSyncEvent::query()
            ->forAdminDetail()
            ->findOrFail($eventId);

        Gate::authorize('view', $event);

        $this->selectedEventId = $event->id;
    }

    public function clearSelectedEvent(): void
    {
        $this->selectedEventId = null;
    }

    public function render(): View
    {
        Gate::authorize('viewAny', MobileSyncEvent::class);

        return view('livewire.admin.mobile-sync-events', [
            'events' => MobileSyncEvent::query()
                ->forAdminIndex()
                ->forOutcome($this->outcome)
                ->matchingAdminSearch($this->search)
                ->paginate(10)
                ->withQueryString(),
            'summary' => $this->summary(),
            'selectedEvent' => $this->selectedEvent(),
            'outcomeOptions' => $this->outcomeOptions(),
        ]);
    }

    /**
     * @return array{total: int, recent: int, conflicts: int, rejected: int, unacknowledged: int}
     */
    private function summary(): array
    {
        return [
            'total' => MobileSyncEvent::query()->count(),
            'recent' => MobileSyncEvent::query()
                ->where('processed_at', '>=', now()->subDay())
                ->count(),
            'conflicts' => MobileSyncEvent::query()
                ->where('outcome', MobileSyncEvent::OUTCOME_CONFLICT)
                ->count(),
            'rejected' => MobileSyncEvent::query()
                ->where('outcome', MobileSyncEvent::OUTCOME_REJECTED)
                ->count(),
            'unacknowledged' => MobileSyncEvent::query()
                ->whereNull('acknowledged_at')
                ->count(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function outcomeOptions(): array
    {
        return [
            MobileSyncEvent::OUTCOME_ACCEPTED => 'Accepted',
            MobileSyncEvent::OUTCOME_REJECTED => 'Rejected',
            MobileSyncEvent::OUTCOME_CONFLICT => 'Conflict',
        ];
    }

    private function selectedEvent(): ?MobileSyncEvent
    {
        if ($this->selectedEventId === null) {
            return null;
        }

        $event = MobileSyncEvent::query()
            ->forAdminDetail()
            ->find($this->selectedEventId);

        if (! $event instanceof MobileSyncEvent) {
            $this->selectedEventId = null;

            return null;
        }

        Gate::authorize('view', $event);

        return $event;
    }

    public function outcomeTone(string $outcome): string
    {
        return match ($outcome) {
            MobileSyncEvent::OUTCOME_ACCEPTED => 'success',
            MobileSyncEvent::OUTCOME_CONFLICT => 'warning',
            MobileSyncEvent::OUTCOME_REJECTED => 'danger',
            default => 'neutral',
        };
    }

    public function responsePayloadJson(MobileSyncEvent $event): string
    {
        $payload = is_array($event->response_payload) ? $event->response_payload : [];

        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
    }
}
