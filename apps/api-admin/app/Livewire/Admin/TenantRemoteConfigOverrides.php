<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\SaveTenantRemoteConfigOverrideAction;
use App\Models\MobileRemoteConfig;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantRemoteConfigOverride;
use App\Models\User;
use Closure;
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
#[Title('Tenant Remote Config')]
final class TenantRemoteConfigOverrides extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $editingOverrideId = null;

    /**
     * @var array{tenant_id: string, config_key: string, value_json: string, version: string, reason: string, confirmed: bool}
     */
    public array $form = [
        'tenant_id' => '',
        'config_key' => '',
        'value_json' => "{\n    \"enabled\": true\n}",
        'version' => 'tenant-default',
        'reason' => '',
        'confirmed' => false,
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $overrideId): void
    {
        $override = TenantRemoteConfigOverride::query()
            ->select(['id', 'tenant_id', 'config_key', 'value', 'version', 'reason', 'metadata'])
            ->findOrFail($overrideId);

        $this->editingOverrideId = $override->id;
        $this->form = [
            'tenant_id' => (string) $override->tenant_id,
            'config_key' => $override->config_key,
            'value_json' => $this->prettyJson($override->value),
            'version' => $override->version,
            'reason' => $override->reason ?? '',
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
        $admin = auth()->user();

        abort_unless($admin instanceof User && $admin->is_platform_admin, 403);

        /** @var array{form: array<string, mixed>} $validated */
        $validated = $this->validate($this->rules(), attributes: $this->validationAttributes());

        $override = $this->editingOverrideId === null
            ? null
            : TenantRemoteConfigOverride::query()
                ->select(['id', 'tenant_id', 'config_key', 'value', 'version', 'reason', 'metadata'])
                ->findOrFail($this->editingOverrideId);

        app(SaveTenantRemoteConfigOverrideAction::class)->handle($validated['form'], $admin, request(), $override);

        session()->flash('status', 'Tenant remote config override saved.');

        $this->resetForm();
        $this->resetPage();
    }

    public function restoreFromAudit(int $auditEventId): void
    {
        $admin = auth()->user();

        abort_unless($admin instanceof User && $admin->is_platform_admin, 403);

        $event = SecurityAuditEvent::query()
            ->select(['id', 'event', 'metadata', 'created_at'])
            ->whereIn('event', $this->auditEventNames())
            ->findOrFail($auditEventId);

        $snapshot = $event->metadata['before'] ?? null;

        if (! is_array($snapshot)) {
            session()->flash('status', 'No restorable tenant config snapshot exists for that audit event.');

            return;
        }

        app(SaveTenantRemoteConfigOverrideAction::class)->restore($snapshot, $admin, request(), $event);

        session()->flash('status', 'Tenant remote config override restored from audit history.');

        $this->resetForm();
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.admin.tenant-remote-config-overrides', [
            'overrides' => TenantRemoteConfigOverride::query()
                ->forAdminIndex()
                ->matchingAdminSearch($this->search)
                ->paginate(10)
                ->withQueryString(),
            'tenants' => $this->tenantOptions(),
            'configOptions' => $this->configOptions(),
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
            'form.config_key' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:_[a-z0-9]+)*$/',
                Rule::unique('tenant_remote_config_overrides', 'config_key')
                    ->where(fn (QueryBuilder $query): QueryBuilder => $query->where('tenant_id', $tenantId))
                    ->ignore($this->editingOverrideId),
            ],
            'form.value_json' => [
                'required',
                'string',
                'max:4000',
                'json',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $decoded = $this->decodedValue($value);

                    if ($decoded === null || array_is_list($decoded)) {
                        $fail('The override value must be a JSON object.');
                    }
                },
            ],
            'form.version' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/'],
            'form.reason' => ['nullable', 'string', 'max:120'],
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
            'form.config_key' => 'config key',
            'form.value_json' => 'override JSON',
            'form.version' => 'version',
            'form.reason' => 'reason',
            'form.confirmed' => 'confirmation',
        ];
    }

    private function resetForm(): void
    {
        $this->editingOverrideId = null;
        $this->form = [
            'tenant_id' => '',
            'config_key' => '',
            'value_json' => "{\n    \"enabled\": true\n}",
            'version' => 'tenant-default',
            'reason' => '',
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
     * @return Collection<int, MobileRemoteConfig>
     */
    private function configOptions(): Collection
    {
        return MobileRemoteConfig::query()
            ->select(['id', 'key', 'description'])
            ->where('category', 'mobile')
            ->orderBy('key')
            ->limit(100)
            ->get();
    }

    /**
     * @return array{total: int, tenants: int}
     */
    private function summary(): array
    {
        return [
            'total' => TenantRemoteConfigOverride::query()->count(),
            'tenants' => TenantRemoteConfigOverride::query()->distinct('tenant_id')->count('tenant_id'),
        ];
    }

    /**
     * @return array{tone: string, headline: string, detail: string, keys: string}
     */
    private function impactPreview(): array
    {
        $decoded = $this->decodedValue($this->form['value_json']);
        $tenant = $this->selectedTenantName();
        $configKey = $this->form['config_key'] ?: 'selected config';

        if ($decoded === null || array_is_list($decoded)) {
            return [
                'tone' => 'danger',
                'headline' => 'Override JSON is not mobile-safe yet.',
                'detail' => 'Tenant overrides must be JSON objects so they can merge over global config and foundation defaults.',
                'keys' => 'none',
            ];
        }

        return [
            'tone' => 'success',
            'headline' => 'Tenant clients receive this override.',
            'detail' => "{$tenant} receives {$configKey} merged above global remote config during bootstrap and /config calls.",
            'keys' => implode(', ', array_keys($decoded)),
        ];
    }

    private function selectedTenantName(): string
    {
        $tenantId = (int) ($this->form['tenant_id'] ?: 0);

        if ($tenantId === 0) {
            return 'The selected tenant';
        }

        $tenant = Tenant::query()->select(['id', 'name'])->find($tenantId);

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
            'admin_tenant_remote_config_override_created',
            'admin_tenant_remote_config_override_updated',
            'admin_tenant_remote_config_override_restored',
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodedValue(mixed $value): ?array
    {
        $decoded = json_decode(is_string($value) ? $value : '', true);

        return is_array($decoded) ? $decoded : null;
    }

    private function prettyJson(mixed $value): string
    {
        $encoded = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return is_string($encoded) ? $encoded : '{}';
    }
}
