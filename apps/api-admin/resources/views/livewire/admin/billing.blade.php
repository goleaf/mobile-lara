<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Billing Control"
        description="Admin/API-owned subscription state, plan metadata, mobile-safe limits, usage snapshots, and billing portal settings."
    />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Tenants</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['total'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Active</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['active'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Trialing</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['trialing'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Limited</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700 dark:text-amber-300">{{ $summary['limited'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(30rem,0.9fr)]">
        <div class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_14rem]">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Search
                    <input
                        type="search"
                        wire:model.blur="search"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Subscription
                    <select
                        wire:model.change="status"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        <option value="">All states</option>

                        @foreach ($statusOptions as $value => $label)
                            <option wire:key="billing-status-filter-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-normal text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                            <tr>
                                <th class="px-4 py-3">Tenant</th>
                                <th class="px-4 py-3">State</th>
                                <th class="px-4 py-3">Plan</th>
                                <th class="px-4 py-3">Usage</th>
                                <th class="px-4 py-3">Portal</th>
                                <th class="px-4 py-3 text-right">Manage</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($tenants as $tenant)
                                <tr wire:key="billing-tenant-row-{{ $tenant->id }}" class="align-top">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">{{ $tenant->name }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $tenant->slug }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.status-badge :tone="$this->statusTone($tenant->subscription_state)">
                                            {{ str($tenant->subscription_state)->replace('_', ' ')->title() }}
                                        </x-admin.status-badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">{{ $tenant->billingPlanName() }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $tenant->billingPlanKey() }} / {{ $tenant->billingPlanTier() }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        {{ $this->usageSummary($tenant) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.status-badge :tone="$tenant->hasBillingPortal() ? 'success' : 'neutral'">
                                            {{ $tenant->hasBillingPortal() ? 'Portal configured' : 'No portal' }}
                                        </x-admin.status-badge>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            type="button"
                                            wire:click="selectTenant({{ $tenant->id }})"
                                            class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                        >
                                            Manage
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        No tenant billing records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $tenants->links() }}
            </div>
        </div>

        <aside class="grid content-start gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            @if ($selectedTenant)
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">Billing detail</h2>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedTenant->public_id }}</p>
                    </div>

                    <button
                        type="button"
                        wire:click="clearSelectedTenant"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        Close
                    </button>
                </div>

                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <p class="text-sm font-semibold text-zinc-950 dark:text-zinc-100">{{ $selectedTenant->name }}</p>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Mobile bootstrap and `/billing/subscription` will expose this tenant-owned billing snapshot.
                    </p>
                </div>

                <form wire:submit="save" class="grid gap-4">
                    <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Subscription state
                        <select
                            wire:model.change="form.subscription_state"
                            class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        >
                            @foreach ($statusOptions as $value => $label)
                                <option wire:key="billing-state-option-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.subscription_state')
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </label>

                    <div class="grid gap-3 sm:grid-cols-3">
                        <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Plan key
                            <input
                                type="text"
                                wire:model.blur="form.plan"
                                class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                            >
                            @error('form.plan')
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300 sm:col-span-2">
                            Plan name
                            <input
                                type="text"
                                wire:model.blur="form.plan_name"
                                class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                            >
                            @error('form.plan_name')
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Plan tier
                        <input
                            type="text"
                            wire:model.blur="form.plan_tier"
                            class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        >
                        @error('form.plan_tier')
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Trial ends at
                        <input
                            type="text"
                            wire:model.blur="form.trial_ends_at"
                            placeholder="2026-07-10T00:00:00+00:00"
                            class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        >
                        @error('form.trial_ends_at')
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Billing portal URL
                        <input
                            type="url"
                            wire:model.blur="form.portal_url"
                            class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        >
                        @error('form.portal_url')
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </label>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Limits JSON
                            <textarea
                                wire:model.blur="form.limits_json"
                                rows="7"
                                class="rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                            ></textarea>
                            @error('form.limits_json')
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Usage JSON
                            <textarea
                                wire:model.blur="form.usage_json"
                                rows="7"
                                class="rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                            ></textarea>
                            @error('form.usage_json')
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-100">Current mobile usage rows</h3>
                        <div class="mt-3 grid gap-2">
                            @forelse ($this->usageRows($selectedTenant) as $row)
                                <div wire:key="billing-usage-row-{{ $row['key'] }}" class="flex items-center justify-between gap-4 text-sm">
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $row['label'] }}</span>
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ $row['usage'] }} / {{ $row['limit'] }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">No limits or usage snapshot configured.</p>
                            @endforelse
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
                    >
                        Save billing settings
                    </button>
                </form>
            @else
                <div class="rounded-lg border border-zinc-200 p-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                    Select a tenant to manage the mobile-safe billing payload used by bootstrap, feature plan gates, and the mobile billing screen.
                </div>
            @endif
        </aside>
    </div>
</section>
