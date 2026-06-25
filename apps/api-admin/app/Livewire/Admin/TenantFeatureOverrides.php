<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\SaveTenantFeatureOverrideAction;
use App\Enums\MobileFeatureState;
use App\Models\MobileFeatureFlag;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantFeatureOverride;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Tenant Feature Overrides')]
final class TenantFeatureOverrides extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $editingOverrideId = null;

    /**
     * @var array{tenant_id: string, feature_key: string, state: string, reason: string, message: string, offline_behavior: string, confirmed: bool}
     */
    public array $form = [
        'tenant_id' => '',
        'feature_key' => '',
        'state' => 'disabled',
        'reason' => '',
        'message' => '',
        'offline_behavior' => '',
        'confirmed' => false,
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $overrideId): void
    {
        $override = TenantFeatureOverride::query()
            ->select([
                'id',
                'tenant_id',
                'feature_key',
                'state',
                'reason',
                'message',
                'offline_behavior',
                'metadata',
            ])
            ->findOrFail($overrideId);

        $this->editingOverrideId = $override->id;
        $this->form = [
            'tenant_id' => (string) $override->tenant_id,
            'feature_key' => $override->feature_key,
            'state' => $override->state->value,
            'reason' => $override->reason ?? '',
            'message' => $override->message ?? '',
            'offline_behavior' => $override->offline_behavior ?? '',
            'confirmed' => false,
        ];

        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function save(): void
    {
        $user = auth()->user();

        abort_unless($user instanceof User && $user->is_platform_admin, 403);

        /** @var array{form: array<string, mixed>} $validated */
        $validated = $this->validate($this->rules(), attributes: $this->validationAttributes());

        $override = $this->editingOverrideId === null
            ? null
            : TenantFeatureOverride::query()
                ->select([
                    'id',
                    'tenant_id',
                    'feature_key',
                    'state',
                    'reason',
                    'message',
                    'offline_behavior',
                    'metadata',
                ])
                ->findOrFail($this->editingOverrideId);

        app(SaveTenantFeatureOverrideAction::class)->handle($validated['form'], $user, request(), $override);

        session()->flash('status', 'Tenant feature override saved.');

        $this->resetForm();
        $this->resetPage();
    }

    public function restoreFromAudit(int $auditEventId): void
    {
        $user = auth()->user();

        abort_unless($user instanceof User && $user->is_platform_admin, 403);

        $event = SecurityAuditEvent::query()
            ->select(['id', 'event', 'metadata', 'created_at'])
            ->whereIn('event', $this->auditEventNames())
            ->findOrFail($auditEventId);

        $snapshot = $event->metadata['before'] ?? null;

        if (! is_array($snapshot)) {
            session()->flash('status', 'No restorable feature override snapshot exists for that audit event.');

            return;
        }

        app(SaveTenantFeatureOverrideAction::class)->restore($snapshot, $user, request(), $event);

        session()->flash('status', 'Tenant feature override restored from audit history.');

        $this->resetForm();
        $this->resetPage();
    }

    public function stateTone(string $state): string
    {
        return match ($state) {
            MobileFeatureState::Visible->value,
            MobileFeatureState::Beta->value,
            MobileFeatureState::Deprecated->value,
            MobileFeatureState::OfflineLimited->value => 'success',
            MobileFeatureState::EmergencyDisabled->value,
            MobileFeatureState::Blocked->value,
            MobileFeatureState::UpdateRequired->value => 'danger',
            default => 'neutral',
        };
    }

    public function render(): View
    {
        return view('livewire.admin.tenant-feature-overrides', [
            'overrides' => TenantFeatureOverride::query()
                ->forAdminIndex()
                ->matchingAdminSearch($this->search)
                ->paginate(10)
                ->withQueryString(),
            'tenants' => $this->tenantOptions(),
            'featureOptions' => $this->featureOptions(),
            'stateOptions' => $this->stateOptions(),
            'offlineBehaviorOptions' => $this->offlineBehaviorOptions(),
            'summary' => $this->summary(),
            'impactPreview' => $this->impactPreview(),
            'auditEvents' => $this->auditEvents(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $tenantId = (int) ($this->form['tenant_id'] ?: 0);

        return [
            'form.tenant_id' => ['required', 'integer', Rule::exists('tenants', 'id')],
            'form.feature_key' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:_[a-z0-9]+)*$/',
                Rule::unique('tenant_feature_overrides', 'feature_key')
                    ->where(fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenantId))
                    ->ignore($this->editingOverrideId),
            ],
            'form.state' => ['required', Rule::in($this->stateValues())],
            'form.reason' => ['nullable', 'string', 'max:120'],
            'form.message' => ['nullable', 'string', 'max:240'],
            'form.offline_behavior' => ['nullable', Rule::in(array_keys($this->offlineBehaviorOptions()))],
            'form.confirmed' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.tenant_id' => 'tenant',
            'form.feature_key' => 'feature key',
            'form.state' => 'state',
            'form.reason' => 'reason',
            'form.message' => 'mobile message',
            'form.offline_behavior' => 'offline behavior',
            'form.confirmed' => 'confirmation',
        ];
    }

    private function resetForm(): void
    {
        $this->editingOverrideId = null;
        $this->form = [
            'tenant_id' => '',
            'feature_key' => '',
            'state' => MobileFeatureState::Disabled->value,
            'reason' => '',
            'message' => '',
            'offline_behavior' => '',
            'confirmed' => false,
        ];
    }

    /**
     * @return Collection<int, Tenant>
     */
    private function tenantOptions(): Collection
    {
        return Tenant::query()
            ->select(['id', 'public_id', 'name', 'status'])
            ->orderBy('name')
            ->limit(100)
            ->get();
    }

    /**
     * @return Collection<int, MobileFeatureFlag>
     */
    private function featureOptions(): Collection
    {
        return MobileFeatureFlag::query()
            ->select(['id', 'key', 'name'])
            ->orderBy('key')
            ->limit(100)
            ->get();
    }

    /**
     * @return array<string, string>
     */
    private function stateOptions(): array
    {
        return collect(MobileFeatureState::cases())
            ->mapWithKeys(static fn (MobileFeatureState $state): array => [
                $state->value => str($state->value)->replace('_', ' ')->title()->toString(),
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function stateValues(): array
    {
        return array_keys($this->stateOptions());
    }

    /**
     * @return array<string, string>
     */
    private function offlineBehaviorOptions(): array
    {
        return [
            '' => 'Inherit resolved behavior',
            'online_only' => 'Online only',
            'queueable' => 'Queueable',
            'queue_local_only' => 'Queue local only',
            'device_local' => 'Device local',
            'read_only_cache' => 'Read-only cache',
        ];
    }

    /**
     * @return array{total: int, enabled: int, restricted: int}
     */
    private function summary(): array
    {
        return [
            'total' => TenantFeatureOverride::query()->count(),
            'enabled' => TenantFeatureOverride::query()
                ->whereIn('state', [
                    MobileFeatureState::Visible->value,
                    MobileFeatureState::Beta->value,
                    MobileFeatureState::Deprecated->value,
                    MobileFeatureState::OfflineLimited->value,
                ])
                ->count(),
            'restricted' => TenantFeatureOverride::query()
                ->whereIn('state', [
                    MobileFeatureState::Hidden->value,
                    MobileFeatureState::Disabled->value,
                    MobileFeatureState::Blocked->value,
                    MobileFeatureState::UpdateRequired->value,
                    MobileFeatureState::EmergencyDisabled->value,
                ])
                ->count(),
        ];
    }

    /**
     * @return array{tone: string, headline: string, detail: string}
     */
    private function impactPreview(): array
    {
        $state = $this->form['state'];
        $tenant = $this->selectedTenantName();
        $feature = $this->form['feature_key'] ?: 'selected feature';

        if (in_array($state, [
            MobileFeatureState::Visible->value,
            MobileFeatureState::Beta->value,
            MobileFeatureState::Deprecated->value,
            MobileFeatureState::OfflineLimited->value,
        ], true)) {
            return [
                'tone' => 'success',
                'headline' => 'Tenant users can see this feature state.',
                'detail' => "{$tenant} receives {$feature} as {$state} unless a user override or permission gate blocks it.",
            ];
        }

        return [
            'tone' => in_array($state, [
                MobileFeatureState::Blocked->value,
                MobileFeatureState::EmergencyDisabled->value,
                MobileFeatureState::UpdateRequired->value,
            ], true) ? 'danger' : 'neutral',
            'headline' => 'Tenant users are restricted for this feature.',
            'detail' => "{$tenant} receives {$feature} as {$state}; mobile must hide, disable, or explain the restriction.",
        ];
    }

    private function selectedTenantName(): string
    {
        $tenantId = (int) ($this->form['tenant_id'] ?: 0);

        if ($tenantId === 0) {
            return 'The selected tenant';
        }

        $tenant = Tenant::query()
            ->select(['id', 'name'])
            ->find($tenantId);

        return $tenant?->name ?? 'The selected tenant';
    }

    /**
     * @return Collection<int, SecurityAuditEvent>
     */
    private function auditEvents(): Collection
    {
        return SecurityAuditEvent::query()
            ->select(['id', 'user_id', 'event', 'severity', 'metadata', 'created_at'])
            ->with('user:id,name,email')
            ->whereIn('event', $this->auditEventNames())
            ->latest()
            ->limit(8)
            ->get();
    }

    /**
     * @return array<int, string>
     */
    private function auditEventNames(): array
    {
        return [
            'admin_tenant_feature_override_created',
            'admin_tenant_feature_override_updated',
            'admin_tenant_feature_override_restored',
        ];
    }
}
