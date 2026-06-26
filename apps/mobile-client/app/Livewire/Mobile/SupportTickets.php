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

#[Title('Support tickets')]
class SupportTickets extends Component
{
    use DispatchesToasts;
    use GuardsMobileFeatureActions;

    private const STATUS_ALL = 'all';

    /**
     * @var list<string>
     */
    private const STATUSES = [
        self::STATUS_ALL,
        'open',
        'in_progress',
        'waiting_on_user',
        'resolved',
        'closed',
    ];

    public string $status = self::STATUS_ALL;

    public string $search = '';

    public int $perPage = 15;

    /**
     * @var list<array<string, mixed>>
     */
    public array $tickets = [];

    public ?string $loadError = null;

    private MobileSupportApiService $support;

    public function boot(MobileAccessPolicy $mobileAccessPolicy, MobileSupportApiService $support): void
    {
        $this->mobileAccessPolicy = $mobileAccessPolicy;
        $this->support = $support;
    }

    public function mount(string $status = self::STATUS_ALL, string $search = ''): void
    {
        $this->status = $this->validStatus($status);
        $this->search = trim($search);

        $this->loadTickets();
    }

    public function setStatus(string $status): void
    {
        $this->status = $this->validStatus($status);
        $this->loadTickets();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->loadTickets();
    }

    public function loadTickets(): void
    {
        $this->loadError = null;

        if ($this->supportDenied('Refresh unavailable', 'support.view')) {
            $this->tickets = [];

            return;
        }

        try {
            $data = $this->support->listTickets($this->query());
        } catch (MobileApiException $exception) {
            $this->tickets = [];
            $this->loadError = $exception->getMessage();
            $this->toastWarning($exception->getMessage(), 'Support unavailable');

            return;
        }

        $tickets = $data['tickets'] ?? [];
        $this->tickets = is_array($tickets) ? array_values(array_filter($tickets, 'is_array')) : [];
    }

    public function render(): View
    {
        return view('livewire.mobile.support-tickets', [
            'statusOptions' => $this->statusOptions(),
            'supportPolicy' => $this->mobileFeatureDecision('support', 'support.view'),
            'ticketCount' => count($this->tickets),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function query(): array
    {
        $query = [
            'per_page' => $this->perPage,
        ];

        if ($this->status !== self::STATUS_ALL) {
            $query['status'] = $this->status;
        }

        if (trim($this->search) !== '') {
            $query['search'] = trim($this->search);
        }

        return $query;
    }

    /**
     * @return list<array{key: string, label: string, active: bool}>
     */
    private function statusOptions(): array
    {
        return collect(self::STATUSES)
            ->map(fn (string $status): array => [
                'key' => $status,
                'label' => str($status)->replace('_', ' ')->title()->toString(),
                'active' => $this->status === $status,
            ])
            ->all();
    }

    private function validStatus(string $status): string
    {
        return in_array($status, self::STATUSES, true) ? $status : self::STATUS_ALL;
    }

    private function supportDenied(string $title, string $permission): bool
    {
        return $this->mobileFeatureDenied('support', $title, $permission);
    }
}
