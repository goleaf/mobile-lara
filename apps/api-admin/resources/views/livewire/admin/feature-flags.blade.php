<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Feature Flags"
        description="Global mobile defaults resolved before tenant and user overrides."
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
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['disabled'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.4fr)]">
        <form wire:submit="save" class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">
                    {{ $editingFeatureFlagId ? 'Edit default' : 'Create default' }}
                </h2>

                @if ($editingFeatureFlagId)
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
                Feature key
                <input
                    type="text"
                    wire:model.blur="form.key"
                    @readonly($editingFeatureFlagId)
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 read-only:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800 dark:read-only:bg-zinc-800"
                    autocomplete="off"
                >
                @error('form.key')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Name
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

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    State
                    <select
                        wire:model.change="form.default_state"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        @forelse ($stateOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @empty
                            <option value="disabled">Disabled</option>
                        @endforelse
                    </select>
                    @error('form.default_state')
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
                            <option value="online_only">Online only</option>
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

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Minimum app version
                <input
                    type="text"
                    wire:model.blur="form.minimum_app_version"
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    autocomplete="off"
                >
                @error('form.minimum_app_version')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Required plans
                <input
                    type="text"
                    wire:model.blur="form.required_plans"
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    autocomplete="off"
                >
                @error('form.required_plans')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Allowed cohorts
                <input
                    type="text"
                    wire:model.blur="form.allowed_cohorts"
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    autocomplete="off"
                >
                @error('form.allowed_cohorts')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Allowed platforms
                    <input
                        type="text"
                        wire:model.blur="form.allowed_platforms"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.allowed_platforms')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Allowed devices
                    <input
                        type="text"
                        wire:model.blur="form.allowed_device_ids"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.allowed_device_ids')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <button
                type="submit"
                class="h-11 rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
            >
                Save
            </button>
        </form>

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
                                <th class="px-4 py-3">Feature</th>
                                <th class="px-4 py-3">State</th>
                                <th class="px-4 py-3">Offline</th>
                                <th class="px-4 py-3">Reason</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($featureFlags as $featureFlag)
                                <tr wire:key="feature-flag-{{ $featureFlag->id }}" class="align-top">
                                    <td class="px-4 py-3">
                                        <div class="grid gap-1">
                                            <span class="font-medium text-zinc-950 dark:text-zinc-100">{{ $featureFlag->key }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $featureFlag->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.status-badge :tone="$this->stateTone($featureFlag->default_state->value)">
                                            {{ str($featureFlag->default_state->value)->replace('_', ' ')->title() }}
                                        </x-admin.status-badge>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        {{ str($featureFlag->offline_behavior)->replace('_', ' ')->title() }}
                                    </td>
                                    <td class="max-w-52 px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                        <div class="grid gap-1">
                                            <span>{{ $featureFlag->reason ?: 'none' }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">Plans {{ $this->planGateLabel($featureFlag) }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">Cohorts {{ $this->cohortGateLabel($featureFlag) }}</span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">Devices {{ $this->deviceGateLabel($featureFlag) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            type="button"
                                            wire:click="edit({{ $featureFlag->id }})"
                                            class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                        >
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        No feature flags found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $featureFlags->links() }}
            </div>
        </div>
    </div>
</section>
