<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="App Version Policies"
        description="Minimum versions, force updates, maintenance mode, and mobile-safe fallback actions."
    />

    @if (session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Total</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['total'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Active</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['active'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Blocking</p>
            <p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-300">{{ $summary['blocking'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.4fr)]">
        <form wire:submit="save" class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">
                    {{ $editingPolicyId ? 'Edit policy' : 'Create policy' }}
                </h2>

                @if ($editingPolicyId)
                    <button
                        type="button"
                        wire:click="cancelEdit"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        Cancel
                    </button>
                @endif
            </div>

            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950">
                <x-admin.status-badge :tone="$impactPreview['tone']">
                    Impact preview
                </x-admin.status-badge>

                <p class="mt-3 text-sm font-semibold text-zinc-950 dark:text-zinc-100">{{ $impactPreview['headline'] }}</p>
                <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $impactPreview['detail'] }}</p>
                <p class="mt-2 text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">
                    Allowed actions: {{ $impactPreview['actions'] }}
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Scope
                    <select
                        wire:model.change="form.scope_type"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        @forelse ($scopeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @empty
                            <option value="global">Global/platform</option>
                        @endforelse
                    </select>
                    @error('form.scope_type')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Platform
                    <select
                        wire:model.change="form.platform"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        @forelse ($platformOptions as $value => $label)
                            @if ($value !== 'any')
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endif
                        @empty
                            <option value="all">Global fallback</option>
                        @endforelse
                    </select>
                    @error('form.platform')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            @if ($form['scope_type'] === 'tenant')
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Tenant
                    <select
                        wire:model.change="form.tenant_id"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        <option value="">Choose tenant</option>
                        @forelse ($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @empty
                            <option value="">No tenants available</option>
                        @endforelse
                    </select>
                    @error('form.tenant_id')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            @elseif ($form['scope_type'] === 'cohort')
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Cohort key
                    <input
                        type="text"
                        wire:model.blur="form.cohort_key"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.cohort_key')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Applies from
                    <input
                        type="text"
                        wire:model.blur="form.applies_from_version"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.applies_from_version')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Applies through
                    <input
                        type="text"
                        wire:model.blur="form.applies_until_version"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.applies_until_version')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Minimum supported
                    <input
                        type="text"
                        wire:model.blur="form.minimum_supported_version"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.minimum_supported_version')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Recommended
                    <input
                        type="text"
                        wire:model.blur="form.minimum_recommended_version"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.minimum_recommended_version')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Latest
                    <input
                        type="text"
                        wire:model.blur="form.latest_version"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.latest_version')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Blocked versions
                <textarea
                    wire:model.blur="form.blocked_versions"
                    rows="3"
                    class="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                ></textarea>
                @error('form.blocked_versions')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    iOS store URL
                    <input
                        type="url"
                        wire:model.blur="form.ios_store_url"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.ios_store_url')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Android store URL
                    <input
                        type="url"
                        wire:model.blur="form.android_store_url"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.android_store_url')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Mobile message
                <textarea
                    wire:model.blur="form.message"
                    rows="2"
                    class="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                ></textarea>
                @error('form.message')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Support URL
                    <input
                        type="url"
                        wire:model.blur="form.support_url"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.support_url')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Retry delay
                    <input
                        type="number"
                        min="60"
                        max="86400"
                        wire:model.blur="form.retry_after_seconds"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.retry_after_seconds')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Allowed actions
                <input
                    type="text"
                    wire:model.blur="form.allowed_actions"
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    autocomplete="off"
                >
                @error('form.allowed_actions')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <div class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                <label class="flex items-start gap-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model.change="form.force_update" class="mt-1 rounded border-zinc-300 text-zinc-950 focus:ring-zinc-500 dark:border-zinc-700">
                    Force update for this policy scope
                </label>

                <label class="flex items-start gap-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model.change="form.maintenance_enabled" class="mt-1 rounded border-zinc-300 text-zinc-950 focus:ring-zinc-500 dark:border-zinc-700">
                    Enable maintenance mode
                </label>

                <label class="flex items-start gap-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model.change="form.logout_allowed" class="mt-1 rounded border-zinc-300 text-zinc-950 focus:ring-zinc-500 dark:border-zinc-700">
                    Allow logout from blocked states
                </label>

                <label class="flex items-start gap-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model.change="form.is_active" class="mt-1 rounded border-zinc-300 text-zinc-950 focus:ring-zinc-500 dark:border-zinc-700">
                    Policy is active
                </label>
            </div>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Maintenance message
                <textarea
                    wire:model.blur="form.maintenance_message"
                    rows="2"
                    class="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                ></textarea>
                @error('form.maintenance_message')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <label class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200">
                <input type="checkbox" wire:model.change="form.confirmed" class="mt-1 rounded border-amber-400 text-amber-700 focus:ring-amber-500 dark:border-amber-700">
                I confirm the mobile effect of this policy.
            </label>
            @error('form.confirmed')
                <span class="-mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror

            <button
                type="submit"
                class="h-11 rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
            >
                Save policy
            </button>
        </form>

        <div class="grid gap-4">
            <div class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Platform filter
                    <select
                        wire:model.change="platformFilter"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        @forelse ($platformOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @empty
                            <option value="any">All policies</option>
                        @endforelse
                    </select>
                </label>

                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                            <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-normal text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                                <tr>
                                    <th class="px-4 py-3">Scope</th>
                                    <th class="px-4 py-3">Platform</th>
                                    <th class="px-4 py-3">Versions</th>
                                    <th class="px-4 py-3">State</th>
                                    <th class="px-4 py-3">Mobile effect</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @forelse ($policies as $policy)
                                    <tr wire:key="app-version-policy-{{ $policy->id }}" class="align-top">
                                        <td class="px-4 py-3">
                                            <div class="grid gap-1">
                                                <span class="font-medium text-zinc-950 dark:text-zinc-100">{{ str($policy->scopeType())->headline() }}</span>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    @if ($policy->scopeType() === 'tenant')
                                                        {{ $policy->tenant?->name ?? 'Deleted tenant' }}
                                                    @elseif ($policy->scopeType() === 'cohort')
                                                        {{ $policy->cohort_key }}
                                                    @else
                                                        all tenants
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="grid gap-1">
                                                <span class="font-medium text-zinc-950 dark:text-zinc-100">{{ str($policy->platform)->upper() }}</span>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $policy->is_active ? 'active' : 'inactive' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                            <div class="grid gap-1">
                                                <span>Min {{ $policy->minimum_supported_version }}</span>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">Rec {{ $policy->minimum_recommended_version ?: 'none' }} · Latest {{ $policy->latest_version ?: 'none' }}</span>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">Range {{ $policy->applies_from_version ?: 'any' }} to {{ $policy->applies_until_version ?: 'any' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-admin.status-badge :tone="$this->policyTone($policy)">
                                                @if ($policy->maintenance_enabled)
                                                    Maintenance
                                                @elseif ($policy->force_update)
                                                    Force update
                                                @elseif (! $policy->is_active)
                                                    Inactive
                                                @else
                                                    Supported
                                                @endif
                                            </x-admin.status-badge>
                                        </td>
                                        <td class="max-w-60 px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                            {{ $policy->maintenance_enabled ? ($policy->maintenance_message ?: 'maintenance screen') : ($policy->message ?: 'normal resolver rules') }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button
                                                type="button"
                                                wire:click="edit({{ $policy->id }})"
                                                class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                            >
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                            No app version policies found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    {{ $policies->links() }}
                </div>
            </div>

            <div class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">Audit history</h2>

                <div class="grid gap-3">
                    @forelse ($auditEvents as $event)
                        <div wire:key="app-version-audit-{{ $event->id }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="grid gap-1">
                                    <p class="text-sm font-medium text-zinc-950 dark:text-zinc-100">
                                        {{ str($event->event)->replace('_', ' ')->title() }}
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $event->created_at?->toDayDateTimeString() }} · {{ $event->user?->email ?: 'system' }}
                                    </p>
                                </div>

                                <x-admin.status-badge :tone="$event->severity === 'warning' ? 'danger' : 'neutral'">
                                    {{ $event->severity }}
                                </x-admin.status-badge>
                            </div>

                            @if (is_array($event->metadata['before'] ?? null))
                                <button
                                    type="button"
                                    wire:click="restoreFromAudit({{ $event->id }})"
                                    wire:confirm="Restore this policy from the previous audit snapshot?"
                                    class="mt-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                >
                                    Restore previous snapshot
                                </button>
                            @endif
                        </div>
                    @empty
                        <p class="rounded-lg border border-zinc-200 p-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                            No app version policy changes have been audited yet.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
