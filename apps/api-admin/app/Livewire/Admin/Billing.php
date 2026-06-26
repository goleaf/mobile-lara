<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\SaveTenantBillingAction;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Billing Control')]
final class Billing extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    public ?int $selectedTenantId = null;

    /**
     * @var array{subscription_state: string, plan: string, plan_name: string, plan_tier: string, trial_ends_at: string, portal_url: string, limits_json: string, usage_json: string}
     */
    public array $form = [
        'subscription_state' => 'active',
        'plan' => 'foundation',
        'plan_name' => 'Foundation',
        'plan_tier' => 'foundation',
        'trial_ends_at' => '',
        'portal_url' => '',
        'limits_json' => '{}',
        'usage_json' => '{}',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->status = array_key_exists($this->status, $this->subscriptionOptions()) ? $this->status : '';
        $this->resetPage();
    }

    public function selectTenant(int $tenantId): void
    {
        $tenant = Tenant::query()
            ->forAdminBillingDetail()
            ->findOrFail($tenantId);

        Gate::authorize('update', $tenant);

        $this->selectedTenantId = $tenant->id;
        $this->form = $this->formFromTenant($tenant);
        $this->resetValidation();
    }

    public function clearSelectedTenant(): void
    {
        $this->selectedTenantId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function save(): void
    {
        $admin = auth()->user();

        abort_unless($admin instanceof User, 403);
        abort_unless($this->selectedTenantId !== null, 404);

        $tenant = Tenant::query()
            ->forAdminBillingDetail()
            ->findOrFail($this->selectedTenantId);

        Gate::authorize('update', $tenant);

        /** @var array{form: array{subscription_state: string, plan: string, plan_name: string, plan_tier: string, trial_ends_at?: string|null, portal_url?: string|null, limits_json?: string|null, usage_json?: string|null}} $validated */
        $validated = $this->validate($this->rules(), attributes: $this->validationAttributes());

        $tenant = app(SaveTenantBillingAction::class)->handle($tenant, $validated['form'], $admin, request());

        $this->form = $this->formFromTenant($tenant);

        $this->dispatch('admin-notify', type: 'success', message: 'Billing settings updated.');
    }

    public function statusTone(string $status): string
    {
        return match ($status) {
            'active', 'trialing' => 'success',
            'past_due', 'expired' => 'warning',
            'suspended', 'canceled' => 'danger',
            default => 'neutral',
        };
    }

    public function usageSummary(Tenant $tenant): string
    {
        $limits = $tenant->billingLimits();
        $usage = $tenant->billingUsage();

        if ($limits === [] && $usage === []) {
            return 'No usage limits';
        }

        $key = array_key_first($limits) ?? array_key_first($usage);

        if (! is_string($key) && ! is_int($key)) {
            return 'No usage limits';
        }

        $usageValue = $usage[$key] ?? 0;
        $limitValue = $limits[$key] ?? 'unlimited';

        return $this->scalarLabel($usageValue).' / '.$this->scalarLabel($limitValue);
    }

    /**
     * @return list<array{key: string, label: string, usage: string, limit: string}>
     */
    public function usageRows(Tenant $tenant): array
    {
        $limits = $tenant->billingLimits();
        $usage = $tenant->billingUsage();
        $keys = array_values(array_unique(array_merge(array_keys($limits), array_keys($usage))));

        return collect($keys)
            ->map(fn (string|int $key): array => [
                'key' => (string) $key,
                'label' => str((string) $key)->replace('_', ' ')->title()->toString(),
                'usage' => $this->scalarLabel($usage[$key] ?? 0),
                'limit' => $this->scalarLabel($limits[$key] ?? 'unlimited'),
            ])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.admin.billing', [
            'selectedTenant' => $this->selectedTenant(),
            'statusOptions' => $this->subscriptionOptions(),
            'summary' => $this->summary(),
            'tenants' => Tenant::query()
                ->forAdminBillingIndex()
                ->matchingAdminSearch($this->search)
                ->forSubscriptionState($this->status)
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.subscription_state' => ['required', Rule::in(array_keys($this->subscriptionOptions()))],
            'form.plan' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'form.plan_name' => ['required', 'string', 'max:120'],
            'form.plan_tier' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'form.trial_ends_at' => ['nullable', 'date'],
            'form.portal_url' => ['nullable', 'url', 'max:500'],
            'form.limits_json' => $this->jsonObjectRules(),
            'form.usage_json' => $this->jsonObjectRules(),
        ];
    }

    /**
     * @return list<mixed>
     */
    private function jsonObjectRules(): array
    {
        return [
            'nullable',
            'string',
            'max:3000',
            function (string $attribute, mixed $value, callable $fail): void {
                $value = is_string($value) ? trim($value) : '';

                if ($value === '') {
                    return;
                }

                $decoded = json_decode($value, true);

                if (! is_array($decoded) || array_is_list($decoded)) {
                    $fail('The '.$attribute.' field must be a JSON object.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.subscription_state' => 'subscription state',
            'form.plan' => 'plan key',
            'form.plan_name' => 'plan name',
            'form.plan_tier' => 'plan tier',
            'form.trial_ends_at' => 'trial end date',
            'form.portal_url' => 'billing portal URL',
            'form.limits_json' => 'limits JSON',
            'form.usage_json' => 'usage JSON',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function subscriptionOptions(): array
    {
        return [
            'trialing' => 'Trialing',
            'active' => 'Active',
            'past_due' => 'Past due',
            'expired' => 'Expired',
            'suspended' => 'Suspended',
            'canceled' => 'Canceled',
        ];
    }

    /**
     * @return array{total: int, active: int, trialing: int, limited: int}
     */
    private function summary(): array
    {
        return [
            'total' => Tenant::query()->count(),
            'active' => Tenant::query()->where('subscription_state', 'active')->count(),
            'trialing' => Tenant::query()->where('subscription_state', 'trialing')->count(),
            'limited' => Tenant::query()->whereIn('subscription_state', ['past_due', 'expired', 'suspended', 'canceled'])->count(),
        ];
    }

    private function selectedTenant(): ?Tenant
    {
        if ($this->selectedTenantId === null) {
            return null;
        }

        $tenant = Tenant::query()
            ->forAdminBillingDetail()
            ->find($this->selectedTenantId);

        return $tenant instanceof Tenant ? $tenant : null;
    }

    /**
     * @return array{subscription_state: string, plan: string, plan_name: string, plan_tier: string, trial_ends_at: string, portal_url: string, limits_json: string, usage_json: string}
     */
    private function formFromTenant(Tenant $tenant): array
    {
        $billing = $tenant->billingSettings();

        return [
            'subscription_state' => $tenant->subscription_state,
            'plan' => $tenant->billingPlanKey(),
            'plan_name' => $tenant->billingPlanName(),
            'plan_tier' => $tenant->billingPlanTier(),
            'trial_ends_at' => is_string($billing['trial_ends_at'] ?? null) ? (string) $billing['trial_ends_at'] : '',
            'portal_url' => is_string($billing['portal_url'] ?? null) ? (string) $billing['portal_url'] : '',
            'limits_json' => json_encode($tenant->billingLimits(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}',
            'usage_json' => json_encode($tenant->billingUsage(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}',
        ];
    }

    private function resetForm(): void
    {
        $this->form = [
            'subscription_state' => 'active',
            'plan' => 'foundation',
            'plan_name' => 'Foundation',
            'plan_tier' => 'foundation',
            'trial_ends_at' => '',
            'portal_url' => '',
            'limits_json' => '{}',
            'usage_json' => '{}',
        ];
    }

    private function scalarLabel(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return 'n/a';
    }
}
