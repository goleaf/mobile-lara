<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileFeatureActions;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileSupport\MobileSupportApiService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create support ticket')]
class SupportTicketCreate extends Component
{
    use DispatchesToasts;
    use GuardsMobileFeatureActions;

    /**
     * @var list<string>
     */
    private const PRIORITIES = [
        'low',
        'normal',
        'high',
        'urgent',
    ];

    public string $subject = '';

    public string $body = '';

    public string $priority = 'normal';

    public string $category = '';

    public string $diagnosticReportId = '';

    public ?string $submissionError = null;

    private MobileSupportApiService $support;

    public function boot(MobileAccessPolicy $mobileAccessPolicy, MobileSupportApiService $support): void
    {
        $this->mobileAccessPolicy = $mobileAccessPolicy;
        $this->support = $support;
    }

    public function submit(): void
    {
        $this->submissionError = null;

        if ($this->supportDenied('Ticket not created', 'support.create')) {
            $this->submissionError = $this->mobileFeatureDecision('support', 'support.create')['message'];

            return;
        }

        $validated = $this->validate();

        try {
            $ticket = $this->support->createTicket($this->payload($validated));
        } catch (MobileApiException $exception) {
            $this->submissionError = $exception->getMessage();
            $this->toastWarning($exception->getMessage(), 'Ticket not created');

            return;
        }

        $ticketId = is_string($ticket['id'] ?? null) ? $ticket['id'] : null;

        if ($ticketId === null) {
            $this->submissionError = 'The mobile API did not return a support ticket id.';
            $this->toastWarning($this->submissionError, 'Ticket not created');

            return;
        }

        $this->toastSuccess('Support ticket sent to Admin/API.', 'Ticket created');
        $this->redirectRoute('mobile.support.show', ['ticket' => $ticketId], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.mobile.support-ticket-create', [
            'priorityOptions' => $this->priorityOptions(),
            'supportPolicy' => $this->mobileFeatureDecision('support', 'support.create'),
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'string', Rule::in(self::PRIORITIES)],
            'category' => ['nullable', 'string', 'max:80'],
            'diagnosticReportId' => ['nullable', 'string', 'max:120'],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated): array
    {
        $payload = [
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'priority' => $validated['priority'],
            'support_context' => [
                'source' => 'mobile_livewire_support_screen',
            ],
        ];

        if (is_string($validated['category']) && trim($validated['category']) !== '') {
            $payload['category'] = trim($validated['category']);
        }

        if (is_string($validated['diagnosticReportId']) && trim($validated['diagnosticReportId']) !== '') {
            $payload['diagnostic_report_id'] = trim($validated['diagnosticReportId']);
        }

        return $payload;
    }

    /**
     * @return array<string, string>
     */
    private function priorityOptions(): array
    {
        return collect(self::PRIORITIES)
            ->mapWithKeys(fn (string $priority): array => [
                $priority => str($priority)->title()->toString(),
            ])
            ->all();
    }

    private function supportDenied(string $title, string $permission): bool
    {
        return $this->mobileFeatureDenied('support', $title, $permission);
    }
}
