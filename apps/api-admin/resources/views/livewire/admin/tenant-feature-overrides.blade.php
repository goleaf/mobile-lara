<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Tenant Feature Overrides"
        description="Tenant-scoped feature decisions resolved above global defaults and below user overrides."
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
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Enabled</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['enabled'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Restricted</p>
            <p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-300">{{ $summary['restricted'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.4fr)]">
        <form wire:submit="save" class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">
                    {{ $editingOverrideId ? 'Edit override' : 'Create override' }}
                </h2>

                @if ($editingOverrideId)
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
            </div>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Tenant
                <select
                    wire:model.change="form.tenant_id"
                    @disabled($editingOverrideId)
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 disabled:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800 dark:disabled:bg-zinc-800"
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

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Feature key
                <input
                    type="text"
                    wire:model.blur="form.feature_key"
                    list="tenant-feature-keys"
                    @readonly($editingOverrideId)
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 read-only:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800 dark:read-only:bg-zinc-800"
                    autocomplete="off"
                >
                <datalist id="tenant-feature-keys">
                    @forelse ($featureOptions as $feature)
                        <option value="{{ $feature->key }}">{{ $feature->name }}</option>
                    @empty
                        <option value="records">Records</option>
                    @endforelse
                </datalist>
                @error('form.feature_key')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    State
                    <select
                        wire:model.change="form.state"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        @forelse ($stateOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @empty
                            <option value="disabled">Disabled</option>
                        @endforelse
                    </select>
                    @error('form.state')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Offline
                    <select
                        wire:model.change="form.offline_behavior"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        @forelse ($offlineBehaviorOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @empty
                            <option value="">Inherit resolved behavior</option>
                        @endforelse
                    </select>
                    @error('form.offline_behavior')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Reason
                <input
                    type="text"
                    wire:model.blur="form.reason"
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    autocomplete="off"
                >
                @error('form.reason')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Mobile message
                <textarea
                    wire:model.blur="form.message"
                    rows="3"
                    class="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                ></textarea>
                @error('form.message')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <label class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200">
                <input type="checkbox" wire:model.change="form.confirmed" class="mt-1 rounded border-amber-400 text-amber-700 focus:ring-amber-500 dark:border-amber-700">
                I confirm this tenant-level mobile feature effect.
            </label>
            @error('form.confirmed')
                <span class="-mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror

            <button
                type="submit"
                class="h-11 rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
            >
                Save override
            </button>
        </form>

        <div class="grid gap-4">
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
                                    <th class="px-4 py-3">Feature</th>
                                    <th class="px-4 py-3">State</th>
                                    <th class="px-4 py-3">Reason</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @forelse ($overrides as $override)
                                    <tr wire:key="tenant-feature-override-{{ $override->id }}" class="align-top">
                                        <td class="px-4 py-3">
                                            <div class="grid gap-1">
                                                <span class="font-medium text-zinc-950 dark:text-zinc-100">{{ $override->tenant?->name ?? 'Deleted tenant' }}</span>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $override->tenant?->public_id ?? 'none' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                            {{ $override->feature_key }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-admin.status-badge :tone="$this->stateTone($override->state->value)">
                                                {{ str($override->state->value)->replace('_', ' ')->title() }}
                                            </x-admin.status-badge>
                                        </td>
                                        <td class="max-w-60 px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                            {{ $override->reason ?: 'none' }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button
                                                type="button"
                                                wire:click="edit({{ $override->id }})"
                                                class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                            >
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                            No tenant feature overrides found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    {{ $overrides->links() }}
                </div>
            </div>

            <div class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">Audit history</h2>

                <div class="grid gap-3">
                    @forelse ($auditEvents as $event)
                        <div wire:key="tenant-feature-audit-{{ $event->id }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
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
                                    wire:confirm="Restore this tenant feature override from the previous audit snapshot?"
                                    class="mt-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                >
                                    Restore previous snapshot
                                </button>
                            @endif
                        </div>
                    @empty
                        <p class="rounded-lg border border-zinc-200 p-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                            No tenant feature override changes have been audited yet.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
