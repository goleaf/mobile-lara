<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\SaveTenantAction;
use App\Actions\Admin\SaveTenantMembershipAction;
use App\Enums\TenantStatus;
use App\Enums\TenantUserRole;
use App\Enums\TenantUserStatus;
use App\Models\Tenant;
use App\Models\TenantUser;
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
#[Title('Tenants')]
final class Tenants extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $editingTenantId = null;

    /**
     * @var array{name: string, slug: string, status: string, subscription_state: string, settings_json: string}
     */
    public array $form = [
        'name' => '',
        'slug' => '',
        'status' => 'active',
        'subscription_state' => 'active',
        'settings_json' => '{}',
    ];

    /**
     * @var array{tenant_id: string, user_email: string, role: string, status: string, is_current: bool}
     */
    public array $membershipForm = [
        'tenant_id' => '',
        'user_email' => '',
        'role' => 'mobile_user',
        'status' => 'active',
        'is_current' => false,
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFormName(string $name): void
    {
        if ($this->editingTenantId === null && trim($this->form['slug']) === '') {
            $this->form['slug'] = str($name)->slug()->toString();
        }
    }

    public function edit(int $tenantId): void
    {
        $tenant = Tenant::query()
            ->select([
                'id',
                'name',
                'slug',
                'status',
                'subscription_state',
                'settings',
            ])
            ->findOrFail($tenantId);

        Gate::authorize('update', $tenant);

        $this->editingTenantId = $tenant->id;
        $this->form = [
            'name' => $tenant->name,
            'slug' => $tenant->slug,
            'status' => $tenant->status->value,
            'subscription_state' => $tenant->subscription_state,
            'settings_json' => json_encode($tenant->settings ?: [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}',
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

        abort_unless($user instanceof User, 403);

        /** @var array{form: array{name: string, slug: string, status: string, subscription_state: string, settings_json: string}} $validated */
        $validated = $this->validate($this->rules(), attributes: $this->validationAttributes());

        $tenant = $this->editingTenantId === null
            ? null
            : Tenant::query()
                ->select([
                    'id',
                    'public_id',
                    'name',
                    'slug',
                    'status',
                    'subscription_state',
                    'settings',
                ])
                ->findOrFail($this->editingTenantId);

        Gate::authorize($tenant instanceof Tenant ? 'update' : 'create', $tenant ?? Tenant::class);

        app(SaveTenantAction::class)->handle($validated['form'], $user, request(), $tenant);

        session()->flash('status', 'Tenant saved.');

        $this->resetForm();
        $this->resetPage();
    }

    public function saveMembership(): void
    {
        $admin = auth()->user();

        abort_unless($admin instanceof User, 403);

        /** @var array{membershipForm: array{tenant_id: int|string, user_email: string, role: string, status: string, is_current: bool}} $validated */
        $validated = $this->validate($this->membershipRules(), attributes: $this->validationAttributes());
        $membershipForm = $validated['membershipForm'];

        $tenant = Tenant::query()
            ->select([
                'id',
                'public_id',
                'name',
                'slug',
                'status',
                'subscription_state',
                'settings',
            ])
            ->findOrFail((int) $membershipForm['tenant_id']);

        Gate::authorize('update', $tenant);

        $member = User::query()
            ->select([
                'id',
                'name',
                'email',
            ])
            ->where('email', $membershipForm['user_email'])
            ->firstOrFail();

        app(SaveTenantMembershipAction::class)->handle($tenant, $member, [
            'role' => $membershipForm['role'],
            'status' => $membershipForm['status'],
            'is_current' => (bool) $membershipForm['is_current'],
        ], $admin, request());

        session()->flash('status', 'Tenant membership saved.');

        $this->membershipForm = [
            'tenant_id' => (string) $tenant->id,
            'user_email' => '',
            'role' => TenantUserRole::MobileUser->value,
            'status' => TenantUserStatus::Active->value,
            'is_current' => false,
        ];
    }

    public function statusTone(string $status): string
    {
        return match ($status) {
            TenantStatus::Active->value,
            TenantStatus::Onboarding->value,
            TenantStatus::Limited->value => 'success',
            TenantStatus::Suspended->value,
            TenantStatus::Disabled->value,
            TenantStatus::Archived->value => 'danger',
            TenantStatus::Maintenance->value => 'warning',
            default => 'neutral',
        };
    }

    public function membershipStatusTone(string $status): string
    {
        return match ($status) {
            TenantUserStatus::Active->value => 'success',
            TenantUserStatus::Invited->value => 'warning',
            TenantUserStatus::Declined->value,
            TenantUserStatus::Suspended->value => 'danger',
            default => 'neutral',
        };
    }

    public function render(): View
    {
        return view('livewire.admin.tenants', [
            'membershipStatusOptions' => $this->membershipStatusOptions(),
            'recentMemberships' => TenantUser::query()->forAdminRecentList()->get(),
            'roleOptions' => $this->roleOptions(),
            'statusOptions' => $this->statusOptions(),
            'subscriptionOptions' => $this->subscriptionOptions(),
            'summary' => $this->summary(),
            'tenantOptions' => Tenant::query()->forAdminOptions()->get(),
            'tenants' => Tenant::query()
                ->forAdminIndex()
                ->matchingAdminSearch($this->search)
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function membershipRules(): array
    {
        return [
            'membershipForm.tenant_id' => ['required', 'integer', Rule::exists('tenants', 'id')],
            'membershipForm.user_email' => ['required', 'email', Rule::exists('users', 'email')],
            'membershipForm.role' => ['required', Rule::in(array_keys($this->roleOptions()))],
            'membershipForm.status' => ['required', Rule::in(array_keys($this->membershipStatusOptions()))],
            'membershipForm.is_current' => ['boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:160'],
            'form.slug' => [
                'required',
                'string',
                'max:120',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('tenants', 'slug')->ignore($this->editingTenantId),
            ],
            'form.status' => ['required', Rule::in(array_keys($this->statusOptions()))],
            'form.subscription_state' => ['required', Rule::in(array_keys($this->subscriptionOptions()))],
            'form.settings_json' => [
                'nullable',
                'string',
                'max:5000',
                function (string $attribute, mixed $value, callable $fail): void {
                    $value = is_string($value) ? trim($value) : '';

                    if ($value === '') {
                        return;
                    }

                    $decoded = json_decode($value, true);

                    if (! is_array($decoded) || array_is_list($decoded)) {
                        $fail('The tenant settings must be a JSON object.');
                    }
                },
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.name' => 'tenant name',
            'form.slug' => 'tenant slug',
            'form.status' => 'tenant status',
            'form.subscription_state' => 'subscription state',
            'form.settings_json' => 'tenant settings',
            'membershipForm.tenant_id' => 'tenant',
            'membershipForm.user_email' => 'member email',
            'membershipForm.role' => 'member role',
            'membershipForm.status' => 'member status',
            'membershipForm.is_current' => 'current tenant flag',
        ];
    }

    private function resetForm(): void
    {
        $this->editingTenantId = null;
        $this->form = [
            'name' => '',
            'slug' => '',
            'status' => TenantStatus::Active->value,
            'subscription_state' => 'active',
            'settings_json' => '{}',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return collect(TenantStatus::cases())
            ->mapWithKeys(static fn (TenantStatus $status): array => [
                $status->value => str($status->value)->replace('_', ' ')->title()->toString(),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function roleOptions(): array
    {
        return collect(TenantUserRole::cases())
            ->mapWithKeys(static fn (TenantUserRole $role): array => [
                $role->value => $role->label(),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function membershipStatusOptions(): array
    {
        return collect(TenantUserStatus::cases())
            ->mapWithKeys(static fn (TenantUserStatus $status): array => [
                $status->value => str($status->value)->replace('_', ' ')->title()->toString(),
            ])
            ->all();
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
     * @return array{total: int, switchable: int, restricted: int}
     */
    private function summary(): array
    {
        $switchableStatuses = [
            TenantStatus::Active->value,
            TenantStatus::Onboarding->value,
            TenantStatus::Limited->value,
        ];

        return [
            'total' => Tenant::query()->count(),
            'switchable' => Tenant::query()->whereIn('status', $switchableStatuses)->count(),
            'restricted' => Tenant::query()->whereNotIn('status', $switchableStatuses)->count(),
        ];
    }
}
