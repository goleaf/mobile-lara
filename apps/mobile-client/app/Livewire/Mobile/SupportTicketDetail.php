<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileFeatureActions;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileSupport\MobileSupportApiService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Support ticket')]
class SupportTicketDetail extends Component
{
    use DispatchesToasts;
    use GuardsMobileFeatureActions;

    public string $ticket;

    /**
     * @var array<string, mixed>
     */
    public array $ticketData = [];

    public string $messageBody = '';

    public ?string $loadError = null;

    public ?string $messageError = null;

    private MobileSupportApiService $support;

    public function boot(MobileAccessPolicy $mobileAccessPolicy, MobileSupportApiService $support): void
    {
        $this->mobileAccessPolicy = $mobileAccessPolicy;
        $this->support = $support;
    }

    public function mount(string $ticket): void
    {
        $this->ticket = $ticket;
        $this->loadTicket();
    }

    public function loadTicket(): void
    {
        $this->loadError = null;

        if ($this->supportDenied('Support unavailable', 'support.view')) {
            $this->ticketData = [];

            return;
        }

        try {
            $this->ticketData = $this->support->showTicket($this->ticket);
        } catch (MobileApiException $exception) {
            $this->ticketData = [];
            $this->loadError = $exception->getMessage();
            $this->toastWarning($exception->getMessage(), 'Support unavailable');
        }
    }

    public function sendMessage(): void
    {
        $this->messageError = null;

        if ($this->supportDenied('Message not sent', 'support.create')) {
            $this->messageError = $this->mobileFeatureDecision('support', 'support.create')['message'];

            return;
        }

        if (! $this->canAddMessage()) {
            $this->messageError = 'This support ticket cannot receive new mobile messages.';
            $this->toastWarning($this->messageError, 'Message not sent');

            return;
        }

        $validated = $this->validate([
            'messageBody' => ['required', 'string', 'max:5000'],
        ]);

        try {
            $this->ticketData = $this->support->addMessage($this->ticket, [
                'body' => $validated['messageBody'],
            ]);
        } catch (MobileApiException $exception) {
            $this->messageError = $exception->getMessage();
            $this->toastWarning($exception->getMessage(), 'Message not sent');

            return;
        }

        $this->messageBody = '';
        $this->toastSuccess('Message sent to support.', 'Message sent');
    }

    public function render(): View
    {
        return view('livewire.mobile.support-ticket-detail', [
            'canAddMessage' => $this->canAddMessage(),
            'ticketMessages' => $this->ticketMessages(),
            'supportPolicy' => $this->mobileFeatureDecision('support', 'support.view'),
            'ticketTitle' => $this->ticketTitle(),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function ticketMessages(): array
    {
        $messages = $this->ticketData['messages'] ?? [];

        return is_array($messages) ? array_values(array_filter($messages, 'is_array')) : [];
    }

    private function canAddMessage(): bool
    {
        return ($this->ticketData['allowed_actions']['add_message'] ?? false) === true;
    }

    private function ticketTitle(): string
    {
        return is_string($this->ticketData['subject'] ?? null) && trim($this->ticketData['subject']) !== ''
            ? $this->ticketData['subject']
            : 'Support ticket';
    }

    private function supportDenied(string $title, string $permission): bool
    {
        return $this->mobileFeatureDenied('support', $title, $permission);
    }
}
