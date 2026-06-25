<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Remote Config"
        description="Global mobile defaults merged below tenant overrides and above foundation fallback values."
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
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Exposed</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['exposed'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Sensitive</p>
            <p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-300">{{ $summary['sensitive'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.4fr)]">
        <form wire:submit="save" class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">
                    {{ $editingConfigId ? 'Edit config' : 'Create config' }}
                </h2>

                @if ($editingConfigId)
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
                    JSON keys: {{ $impactPreview['keys'] ?: 'none' }}
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Config key
                    <input
                        type="text"
                        wire:model.blur="form.key"
                        @readonly($editingConfigId)
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 read-only:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800 dark:read-only:bg-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.key')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Version
                    <input
                        type="text"
                        wire:model.blur="form.version"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                    @error('form.version')
                        <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Description
                <input
                    type="text"
                    wire:model.blur="form.description"
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    autocomplete="off"
                >
                @error('form.description')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Config JSON
                <textarea
                    wire:model.blur="form.value_json"
                    rows="12"
                    class="font-mono rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                ></textarea>
                @error('form.value_json')
                    <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <label class="flex items-start gap-3 rounded-lg border border-zinc-200 p-4 text-sm font-medium text-zinc-700 dark:border-zinc-800 dark:text-zinc-300">
                <input type="checkbox" wire:model.change="form.is_sensitive" class="mt-1 rounded border-zinc-300 text-zinc-950 focus:ring-zinc-500 dark:border-zinc-700">
                Mark as sensitive and exclude from mobile resolver output
            </label>

            <label class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200">
                <input type="checkbox" wire:model.change="form.confirmed" class="mt-1 rounded border-amber-400 text-amber-700 focus:ring-amber-500 dark:border-amber-700">
                I confirm the mobile effect of this remote config.
            </label>
            @error('form.confirmed')
                <span class="-mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror

            <button
                type="submit"
                class="h-11 rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
            >
                Save config
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
                                    <th class="px-4 py-3">Key</th>
                                    <th class="px-4 py-3">Version</th>
                                    <th class="px-4 py-3">Exposure</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @forelse ($configs as $config)
                                    <tr wire:key="remote-config-{{ $config->id }}" class="align-top">
                                        <td class="px-4 py-3">
                                            <span class="font-medium text-zinc-950 dark:text-zinc-100">{{ $config->key }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                            {{ $config->version }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-admin.status-badge :tone="$this->configTone($config)">
                                                {{ $config->is_sensitive ? 'Sensitive' : 'Mobile' }}
                                            </x-admin.status-badge>
                                        </td>
                                        <td class="max-w-64 px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                            {{ $config->description ?: 'none' }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button
                                                type="button"
                                                wire:click="edit({{ $config->id }})"
                                                class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                            >
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                            No remote config rows found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    {{ $configs->links() }}
                </div>
            </div>

            <div class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">Audit history</h2>

                <div class="grid gap-3">
                    @forelse ($auditEvents as $event)
                        <div wire:key="remote-config-audit-{{ $event->id }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
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
                                    wire:confirm="Restore this config from the previous audit snapshot?"
                                    class="mt-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                >
                                    Restore previous snapshot
                                </button>
                            @endif
                        </div>
                    @empty
                        <p class="rounded-lg border border-zinc-200 p-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                            No remote config changes have been audited yet.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
