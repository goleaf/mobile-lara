<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Tenants"
        description="Platform-owned tenant lifecycle and subscription state controls for mobile access."
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
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Mobile switchable</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['switchable'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Restricted</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['restricted'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.4fr)]">
        <div class="grid gap-6">
            <form wire:submit="save" class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">
                    {{ $editingTenantId ? 'Edit tenant' : 'Create tenant' }}
                </h2>

                @if ($editingTenantId)
                    <button
                        type="button"
                        wire:click="cancelEdit"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        Cancel
                    </button>
                @endif
            </div>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Tenant name
                <input
                    type="text"
                    wire:model.blur="form.name"
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    autocomplete="off"
                >
                @error('form.name')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Slug
                <input
                    type="text"
                    wire:model.blur="form.slug"
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    autocomplete="off"
                >
                @error('form.slug')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Status
                    <select
                        wire:model.change="form.status"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        @forelse ($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @empty
                            <option value="active">Active</option>
                        @endforelse
                    </select>
                    @error('form.status')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Subscription
                    <select
                        wire:model.change="form.subscription_state"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        @forelse ($subscriptionOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @empty
                            <option value="active">Active</option>
                        @endforelse
                    </select>
                    @error('form.subscription_state')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Tenant settings JSON
                <textarea
                    wire:model.blur="form.settings_json"
                    rows="8"
                    class="rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-xs text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                ></textarea>
                @error('form.settings_json')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <button
                type="submit"
                class="h-11 rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
            >
                Save tenant
            </button>
            </form>

            <form wire:submit="saveMembership" class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div>
                    <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">Tenant membership</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Assign existing users to tenant roles and mark their current mobile tenant context.
                    </p>
                </div>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Tenant
                    <select
                        wire:model.change="membershipForm.tenant_id"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        <option value="">Select tenant</option>
                        @forelse ($tenantOptions as $tenantOption)
                            <option value="{{ $tenantOption->id }}">{{ $tenantOption->name }} - {{ $tenantOption->slug }}</option>
                        @empty
                            <option value="">No tenants available</option>
                        @endforelse
                    </select>
                    @error('membershipForm.tenant_id')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    User email
                    <input
                        type="email"
                        wire:model.blur="membershipForm.user_email"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('membershipForm.user_email')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Role
                        <select
                            wire:model.change="membershipForm.role"
                            class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        >
                            @forelse ($roleOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @empty
                                <option value="mobile_user">Mobile user</option>
                            @endforelse
                        </select>
                        @error('membershipForm.role')
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Membership status
                        <select
                            wire:model.change="membershipForm.status"
                            class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        >
                            @forelse ($membershipStatusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @empty
                                <option value="active">Active</option>
                            @endforelse
                        </select>
                        @error('membershipForm.status')
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <label class="flex items-start gap-3 rounded-lg border border-zinc-200 p-3 text-sm font-medium text-zinc-700 dark:border-zinc-800 dark:text-zinc-300">
                    <input
                        type="checkbox"
                        wire:model.change="membershipForm.is_current"
                        class="mt-1 rounded border-zinc-300 text-zinc-950 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                    >
                    <span class="grid gap-1">
                        <span>Set as current tenant for this user</span>
                        <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400">
                            Current tenant controls the first mobile context returned by bootstrap.
                        </span>
                    </span>
                </label>
                @error('membershipForm.is_current')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror

                <button
                    type="submit"
                    class="h-11 rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
                >
                    Save membership
                </button>
            </form>
        </div>

        <div class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Search
                <input
                    type="search"
                    wire:model.blur="search"
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    autocomplete="off"
                >
            </label>

            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-normal text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                            <tr>
                                <th class="px-4 py-3">Tenant</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Subscription</th>
                                <th class="px-4 py-3">Members</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($tenants as $tenant)
                                <tr wire:key="tenant-{{ $tenant->id }}" class="align-top">
                                    <td class="px-4 py-3">
                                        <div class="grid gap-1">
                                            <span class="font-medium text-zinc-950 dark:text-zinc-100">{{ $tenant->name }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $tenant->slug }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $tenant->public_id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.status-badge :tone="$this->statusTone($tenant->status->value)">
                                            {{ str($tenant->status->value)->replace('_', ' ')->title() }}
                                        </x-admin.status-badge>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        {{ str($tenant->subscription_state)->replace('_', ' ')->title() }}
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        {{ $tenant->memberships_count }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            type="button"
                                            wire:click="edit({{ $tenant->id }})"
                                            class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                        >
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        No tenants match this search.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $tenants->links() }}

            <div class="grid gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                <div>
                    <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">Recent memberships</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Latest tenant-role assignments that shape mobile bootstrap access.
                    </p>
                </div>

                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                            <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-normal text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                                <tr>
                                    <th class="px-4 py-3">User</th>
                                    <th class="px-4 py-3">Tenant</th>
                                    <th class="px-4 py-3">Role</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Current</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @forelse ($recentMemberships as $membership)
                                    <tr wire:key="tenant-membership-{{ $membership->id }}" class="align-top">
                                        <td class="px-4 py-3">
                                            <div class="grid gap-1">
                                                <span class="font-medium text-zinc-950 dark:text-zinc-100">{{ $membership->user?->name }}</span>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $membership->user?->email }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="grid gap-1">
                                                <span class="font-medium text-zinc-950 dark:text-zinc-100">{{ $membership->tenant?->name }}</span>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $membership->tenant?->slug }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                            {{ $membership->role?->label() ?? 'Unknown' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-admin.status-badge :tone="$this->membershipStatusTone($membership->status?->value ?? 'unknown')">
                                                {{ str($membership->status?->value ?? 'unknown')->replace('_', ' ')->title() }}
                                            </x-admin.status-badge>
                                        </td>
                                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                            {{ $membership->is_current ? 'Yes' : 'No' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                            No tenant memberships have been assigned yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
