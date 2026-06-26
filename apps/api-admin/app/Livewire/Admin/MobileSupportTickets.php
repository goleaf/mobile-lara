<?php

namespace App\Livewire\Admin;

use App\Actions\Support\CreateAdminSupportReplyAction;
use App\Actions\Support\UpdateAdminSupportTicketAction;
use App\Models\MobileSupportTicket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Support Queue')]
final class MobileSupportTickets extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    public ?int $selectedTicketId = null;

    public string $statusDraft = '';

    public string $priorityDraft = '';

    public string $assignedUserIdDraft = '';

    public string $replyBody = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        if ($this->status !== '' && ! in_array($this->status, MobileSupportTicket::statuses(), true)) {
            $this->status = '';
        }

        $this->resetPage();
    }

    public function selectTicket(int $ticketId): void
    {
        $ticket = MobileSupportTicket::query()
            ->forAdminDetail()
            ->findOrFail($ticketId);

        Gate::authorize('view', $ticket);

        $this->selectedTicketId = $ticket->id;
        $this->syncDrafts($ticket);
    }

    public function clearSelectedTicket(): void
    {
        $this->selectedTicketId = null;
        $this->resetDrafts();
    }

    public function saveTicketState(UpdateAdminSupportTicketAction $tickets): void
    {
        $ticket = $this->selectedTicket();

        if (! $ticket instanceof MobileSupportTicket) {
            return;
        }

        Gate::authorize('update', $ticket);

        $validated = $this->validate([
            'statusDraft' => ['required', 'string', Rule::in(MobileSupportTicket::statuses())],
            'priorityDraft' => ['required', 'string', Rule::in(MobileSupportTicket::priorities())],
            'assignedUserIdDraft' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        /** @var User $admin */
        $admin = Auth::user();
        $updatedTicket = $tickets->handle($ticket, $admin, [
            'status' => $validated['statusDraft'],
            'priority' => $validated['priorityDraft'],
            'assigned_user_id' => $this->nullableInt($validated['assignedUserIdDraft']),
        ]);

        $this->syncDrafts($updatedTicket);
        $this->dispatch('admin-notify', type: 'success', message: 'Support ticket updated.');
    }

    public function sendReply(CreateAdminSupportReplyAction $replies): void
    {
        $ticket = $this->selectedTicket();

        if (! $ticket instanceof MobileSupportTicket) {
            return;
        }

        Gate::authorize('update', $ticket);

        $validated = $this->validate([
            'replyBody' => ['required', 'string', 'max:5000'],
        ]);

        /** @var User $admin */
        $admin = Auth::user();
        $updatedTicket = $replies->handle($ticket, $admin, $validated['replyBody']);

        $this->replyBody = '';
        $this->syncDrafts($updatedTicket);
        $this->dispatch('admin-notify', type: 'success', message: 'Support reply sent.');
    }

    public function render(): View
    {
        Gate::authorize('viewAny', MobileSupportTicket::class);

        return view('livewire.admin.mobile-support-tickets', [
            'agentOptions' => $this->agentOptions(),
            'priorityOptions' => $this->priorityOptions(),
            'selectedTicket' => $this->selectedTicket(),
            'statusOptions' => $this->statusOptions(),
            'summary' => $this->summary(),
            'tickets' => MobileSupportTicket::query()
                ->forAdminIndex()
                ->forStatus($this->status)
                ->matchingAdminSearch($this->search)
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    /**
     * @return array{total: int, open: int, in_progress: int, waiting_on_user: int, resolved: int, closed: int}
     */
    private function summary(): array
    {
        return [
            'total' => MobileSupportTicket::query()->count(),
            'open' => MobileSupportTicket::query()->where('status', MobileSupportTicket::STATUS_OPEN)->count(),
            'in_progress' => MobileSupportTicket::query()->where('status', MobileSupportTicket::STATUS_IN_PROGRESS)->count(),
            'waiting_on_user' => MobileSupportTicket::query()->where('status', MobileSupportTicket::STATUS_WAITING_ON_USER)->count(),
            'resolved' => MobileSupportTicket::query()->where('status', MobileSupportTicket::STATUS_RESOLVED)->count(),
            'closed' => MobileSupportTicket::query()->where('status', MobileSupportTicket::STATUS_CLOSED)->count(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return collect(MobileSupportTicket::statuses())
            ->mapWithKeys(fn (string $status): array => [
                $status => str($status)->replace('_', ' ')->title()->toString(),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function priorityOptions(): array
    {
        return collect(MobileSupportTicket::priorities())
            ->mapWithKeys(fn (string $priority): array => [
                $priority => str($priority)->title()->toString(),
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function agentOptions(): array
    {
        return User::query()
            ->select(['id', 'name'])
            ->where('is_platform_admin', true)
            ->orderBy('name')
            ->limit(100)
            ->pluck('name', 'id')
            ->all();
    }

    private function selectedTicket(): ?MobileSupportTicket
    {
        if ($this->selectedTicketId === null) {
            return null;
        }

        $ticket = MobileSupportTicket::query()
            ->forAdminDetail()
            ->find($this->selectedTicketId);

        if (! $ticket instanceof MobileSupportTicket) {
            $this->clearSelectedTicket();

            return null;
        }

        Gate::authorize('view', $ticket);

        return $ticket;
    }

    private function syncDrafts(MobileSupportTicket $ticket): void
    {
        $this->statusDraft = $ticket->status;
        $this->priorityDraft = $ticket->priority;
        $this->assignedUserIdDraft = $ticket->assigned_user_id === null ? '' : (string) $ticket->assigned_user_id;
    }

    private function resetDrafts(): void
    {
        $this->statusDraft = '';
        $this->priorityDraft = '';
        $this->assignedUserIdDraft = '';
        $this->replyBody = '';
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    public function statusTone(string $status): string
    {
        return match ($status) {
            MobileSupportTicket::STATUS_OPEN => 'warning',
            MobileSupportTicket::STATUS_IN_PROGRESS => 'warning',
            MobileSupportTicket::STATUS_WAITING_ON_USER => 'neutral',
            MobileSupportTicket::STATUS_RESOLVED => 'success',
            MobileSupportTicket::STATUS_CLOSED => 'neutral',
            default => 'neutral',
        };
    }

    public function priorityTone(string $priority): string
    {
        return match ($priority) {
            MobileSupportTicket::PRIORITY_LOW => 'neutral',
            MobileSupportTicket::PRIORITY_NORMAL => 'neutral',
            MobileSupportTicket::PRIORITY_HIGH => 'warning',
            MobileSupportTicket::PRIORITY_URGENT => 'danger',
            default => 'neutral',
        };
    }
}
