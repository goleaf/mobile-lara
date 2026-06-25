<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\SaveMobileAppVersionPolicyAction;
use App\Models\MobileAppVersionPolicy;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('App Version Policies')]
final class AppVersionPolicies extends Component
{
    use WithPagination;

    #[Url(as: 'platform')]
    public string $platformFilter = 'any';

    public ?int $editingPolicyId = null;

    /**
     * @var array{scope_type: string, tenant_id: string, cohort_key: string, platform: string, minimum_supported_version: string, minimum_recommended_version: string, latest_version: string, blocked_versions: string, ios_store_url: string, android_store_url: string, message: string, support_url: string, force_update: bool, maintenance_enabled: bool, maintenance_message: string, retry_after_seconds: string, allowed_actions: string, logout_allowed: bool, is_active: bool, confirmed: bool}
     */
    public array $form = [
        'scope_type' => 'global',
        'tenant_id' => '',
        'cohort_key' => '',
        'platform' => 'all',
        'minimum_supported_version' => '1.0.0',
        'minimum_recommended_version' => '',
        'latest_version' => '',
        'blocked_versions' => '',
        'ios_store_url' => '',
        'android_store_url' => '',
        'message' => '',
        'support_url' => '',
        'force_update' => false,
        'maintenance_enabled' => false,
        'maintenance_message' => '',
        'retry_after_seconds' => '',
        'allowed_actions' => 'continue, logout, support',
        'logout_allowed' => true,
        'is_active' => true,
        'confirmed' => false,
    ];

    public function updatedPlatformFilter(): void
    {
        $this->resetPage();
    }

    public function edit(int $policyId): void
    {
        $policy = MobileAppVersionPolicy::query()
            ->select([
                'id',
                'tenant_id',
                'cohort_key',
                'platform',
                'minimum_supported_version',
                'minimum_recommended_version',
                'latest_version',
                'blocked_versions',
                'store_urls',
                'message',
                'support_url',
                'force_update',
                'maintenance_enabled',
                'maintenance_message',
                'retry_after_seconds',
                'allowed_actions',
                'logout_allowed',
                'is_active',
                'metadata',
            ])
            ->findOrFail($policyId);

        $storeUrls = is_array($policy->store_urls) ? $policy->store_urls : [];

        $this->editingPolicyId = $policy->id;
        $this->form = [
            'scope_type' => $policy->scopeType(),
            'tenant_id' => (string) ($policy->tenant_id ?? ''),
            'cohort_key' => $policy->cohort_key ?? '',
            'platform' => $policy->platform,
            'minimum_supported_version' => $policy->minimum_supported_version,
            'minimum_recommended_version' => $policy->minimum_recommended_version ?? '',
            'latest_version' => $policy->latest_version ?? '',
            'blocked_versions' => implode(PHP_EOL, $this->stringList($policy->blocked_versions)),
            'ios_store_url' => is_string($storeUrls['ios'] ?? null) ? $storeUrls['ios'] : '',
            'android_store_url' => is_string($storeUrls['android'] ?? null) ? $storeUrls['android'] : '',
            'message' => $policy->message ?? '',
            'support_url' => $policy->support_url ?? '',
            'force_update' => $policy->force_update,
            'maintenance_enabled' => $policy->maintenance_enabled,
            'maintenance_message' => $policy->maintenance_message ?? '',
            'retry_after_seconds' => (string) ($policy->retry_after_seconds ?? ''),
            'allowed_actions' => implode(', ', $this->stringList($policy->allowed_actions)),
            'logout_allowed' => $policy->logout_allowed,
            'is_active' => $policy->is_active,
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

        $policy = $this->editingPolicyId === null
            ? null
            : MobileAppVersionPolicy::query()
                ->select([
                    'id',
                    'tenant_id',
                    'cohort_key',
                    'platform',
                    'minimum_supported_version',
                    'minimum_recommended_version',
                    'latest_version',
                    'blocked_versions',
                    'store_urls',
                    'message',
                    'support_url',
                    'force_update',
                    'maintenance_enabled',
                    'maintenance_message',
                    'retry_after_seconds',
                    'allowed_actions',
                    'logout_allowed',
                    'is_active',
                    'metadata',
                ])
                ->findOrFail($this->editingPolicyId);

        app(SaveMobileAppVersionPolicyAction::class)->handle($validated['form'], $user, request(), $policy);

        session()->flash('status', 'App version policy saved.');

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
            session()->flash('status', 'No restorable version snapshot exists for that audit event.');

            return;
        }

        app(SaveMobileAppVersionPolicyAction::class)->restore($snapshot, $user, request(), $event);

        session()->flash('status', 'App version policy restored from audit history.');

        $this->resetForm();
        $this->resetPage();
    }

    public function policyTone(MobileAppVersionPolicy $policy): string
    {
        if (! $policy->is_active) {
            return 'neutral';
        }

        if ($policy->force_update || $policy->maintenance_enabled) {
            return 'danger';
        }

        if ($policy->minimum_recommended_version !== null || $this->stringList($policy->blocked_versions) !== []) {
            return 'warning';
        }

        return 'success';
    }

    public function render(): View
    {
        return view('livewire.admin.app-version-policies', [
            'policies' => MobileAppVersionPolicy::query()
                ->forAdminIndex()
                ->forAdminPlatform($this->platformFilter)
                ->paginate(10)
                ->withQueryString(),
            'platformOptions' => $this->platformOptions(),
            'scopeOptions' => $this->scopeOptions(),
            'tenants' => $this->tenantOptions(),
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
        return [
            'form.scope_type' => ['required', Rule::in(array_keys($this->scopeOptions()))],
            'form.tenant_id' => [
                Rule::requiredIf($this->form['scope_type'] === 'tenant'),
                'nullable',
                'integer',
                Rule::exists('tenants', 'id'),
            ],
            'form.cohort_key' => [
                Rule::requiredIf($this->form['scope_type'] === 'cohort'),
                'nullable',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/',
            ],
            'form.platform' => ['required', Rule::in(array_keys($this->editablePlatformOptions()))],
            'form.minimum_supported_version' => ['required', 'string', 'max:40'],
            'form.minimum_recommended_version' => ['nullable', 'string', 'max:40'],
            'form.latest_version' => ['nullable', 'string', 'max:40'],
            'form.blocked_versions' => ['nullable', 'string', 'max:500'],
            'form.ios_store_url' => ['nullable', 'url', 'max:255'],
            'form.android_store_url' => ['nullable', 'url', 'max:255'],
            'form.message' => ['nullable', 'string', 'max:240'],
            'form.support_url' => ['nullable', 'url', 'max:255'],
            'form.force_update' => ['boolean'],
            'form.maintenance_enabled' => ['boolean'],
            'form.maintenance_message' => ['nullable', 'string', 'max:240'],
            'form.retry_after_seconds' => ['nullable', 'integer', 'min:60', 'max:86400'],
            'form.allowed_actions' => [
                'required',
                'string',
                'max:120',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $invalidActions = array_diff($this->stringList($value), $this->allowedActionOptions());

                    if ($invalidActions !== []) {
                        $fail('Allowed actions may only include continue, update, retry, support, or logout.');
                    }
                },
            ],
            'form.logout_allowed' => ['boolean'],
            'form.is_active' => ['boolean'],
            'form.confirmed' => [$this->requiresConfirmation() ? 'accepted' : 'nullable'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.scope_type' => 'scope',
            'form.tenant_id' => 'tenant',
            'form.cohort_key' => 'cohort key',
            'form.platform' => 'platform',
            'form.minimum_supported_version' => 'minimum supported version',
            'form.minimum_recommended_version' => 'minimum recommended version',
            'form.latest_version' => 'latest version',
            'form.blocked_versions' => 'blocked versions',
            'form.ios_store_url' => 'iOS store URL',
            'form.android_store_url' => 'Android store URL',
            'form.message' => 'mobile message',
            'form.support_url' => 'support URL',
            'form.force_update' => 'force update',
            'form.maintenance_enabled' => 'maintenance mode',
            'form.maintenance_message' => 'maintenance message',
            'form.retry_after_seconds' => 'retry delay',
            'form.allowed_actions' => 'allowed actions',
            'form.logout_allowed' => 'logout allowed',
            'form.is_active' => 'active policy',
            'form.confirmed' => 'confirmation',
        ];
    }

    private function resetForm(): void
    {
        $this->editingPolicyId = null;
        $this->form = [
            'scope_type' => 'global',
            'tenant_id' => '',
            'cohort_key' => '',
            'platform' => 'all',
            'minimum_supported_version' => '1.0.0',
            'minimum_recommended_version' => '',
            'latest_version' => '',
            'blocked_versions' => '',
            'ios_store_url' => '',
            'android_store_url' => '',
            'message' => '',
            'support_url' => '',
            'force_update' => false,
            'maintenance_enabled' => false,
            'maintenance_message' => '',
            'retry_after_seconds' => '',
            'allowed_actions' => 'continue, logout, support',
            'logout_allowed' => true,
            'is_active' => true,
            'confirmed' => false,
        ];
    }

    private function requiresConfirmation(): bool
    {
        return (bool) $this->form['force_update']
            || (bool) $this->form['maintenance_enabled']
            || ! (bool) $this->form['is_active'];
    }

    /**
     * @return array<string, string>
     */
    private function platformOptions(): array
    {
        return [
            'any' => 'All policies',
            ...$this->editablePlatformOptions(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function editablePlatformOptions(): array
    {
        return [
            'all' => 'Global fallback',
            'ios' => 'iOS',
            'android' => 'Android',
            'unknown' => 'Unknown or development',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function scopeOptions(): array
    {
        return [
            'global' => 'Global/platform',
            'tenant' => 'Tenant',
            'cohort' => 'Cohort',
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
     * @return array<int, string>
     */
    private function allowedActionOptions(): array
    {
        return ['continue', 'update', 'retry', 'support', 'logout'];
    }

    /**
     * @return array{total: int, active: int, blocking: int}
     */
    private function summary(): array
    {
        return [
            'total' => MobileAppVersionPolicy::query()->count(),
            'active' => MobileAppVersionPolicy::query()->where('is_active', true)->count(),
            'blocking' => MobileAppVersionPolicy::query()
                ->where('is_active', true)
                ->where(function (Builder $query): void {
                    $query
                        ->where('force_update', true)
                        ->orWhere('maintenance_enabled', true);
                })
                ->count(),
        ];
    }

    /**
     * @return array{tone: string, headline: string, detail: string, actions: string}
     */
    private function impactPreview(): array
    {
        $actions = $this->stringList($this->form['allowed_actions']);

        if ((bool) $this->form['maintenance_enabled']) {
            return [
                'tone' => 'danger',
                'headline' => 'Maintenance blocks normal mobile use.',
                'detail' => $this->scopeDescription('receive the maintenance screen with retry, support, and logout behavior.'),
                'actions' => implode(', ', $actions ?: ['retry', 'support', 'logout']),
            ];
        }

        if ((bool) $this->form['force_update']) {
            return [
                'tone' => 'danger',
                'headline' => 'Force update blocks unsupported clients.',
                'detail' => $this->scopeDescription('must update before continuing through normal mobile navigation.'),
                'actions' => implode(', ', $actions ?: ['update', 'support', 'logout']),
            ];
        }

        if (! (bool) $this->form['is_active']) {
            return [
                'tone' => 'neutral',
                'headline' => 'Inactive policies are ignored by bootstrap.',
                'detail' => 'The resolver skips this row and falls back to another active tenant, cohort, platform, or global policy.',
                'actions' => 'none',
            ];
        }

        if (trim($this->form['minimum_recommended_version']) !== '') {
            return [
                'tone' => 'warning',
                'headline' => 'Older clients receive an optional update prompt.',
                'detail' => $this->scopeDescription('remain usable below the recommended version unless they also fall below the minimum supported version.'),
                'actions' => implode(', ', $actions ?: ['continue', 'logout', 'support']),
            ];
        }

        return [
            'tone' => 'success',
            'headline' => 'Policy allows normal mobile use.',
            'detail' => $this->scopeDescription('continue normally at or above the minimum supported version.'),
            'actions' => implode(', ', $actions ?: ['continue', 'logout', 'support']),
        ];
    }

    private function scopeDescription(string $effect): string
    {
        return $this->scopeLabel().' clients matching '.$this->form['platform'].' '.$effect;
    }

    private function scopeLabel(): string
    {
        if ($this->form['scope_type'] === 'tenant') {
            return $this->selectedTenantName();
        }

        if ($this->form['scope_type'] === 'cohort') {
            return trim($this->form['cohort_key']) !== '' ? 'Cohort '.$this->form['cohort_key'] : 'Selected cohort';
        }

        return 'Global/platform';
    }

    private function selectedTenantName(): string
    {
        $tenantId = (int) ($this->form['tenant_id'] ?: 0);

        if ($tenantId === 0) {
            return 'Selected tenant';
        }

        $tenant = Tenant::query()->select(['id', 'name'])->find($tenantId);

        return $tenant?->name ?? 'Selected tenant';
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
            'admin_mobile_app_version_policy_created',
            'admin_mobile_app_version_policy_updated',
            'admin_mobile_app_version_policy_restored',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function stringList(mixed $value): array
    {
        $items = is_array($value) ? $value : preg_split('/[\r\n,]+/', (string) $value);

        return collect($items)
            ->filter(static fn (mixed $item): bool => is_string($item) && trim($item) !== '')
            ->map(static fn (string $item): string => trim($item))
            ->unique()
            ->values()
            ->all();
    }
}
