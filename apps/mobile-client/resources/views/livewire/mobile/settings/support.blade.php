<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        :title="$sectionTitle"
        :description="$sectionDescription"
        :back-href="route('mobile.settings')"
    />

    <x-mobile.card title="Current status" :description="$sectionStatus">
        <div class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
            <p class="text-sm font-semibold text-app-ink dark:text-zinc-100">Section route ready</p>
            <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                This page is wired as a focused Livewire settings destination.
            </p>
        </div>
    </x-mobile.card>

    <x-mobile.card title="Support center" description="Open tenant-safe help and recovery links without turning local state into support authority.">
        <div class="grid gap-3">
            @if ($supportBrowserPolicy['allowed'])
                <x-mobile.button
                    wire:click="openSupportCenter"
                    wire:loading.attr="disabled"
                    wire:target="openSupportCenter"
                    variant="primary"
                    full
                >
                    <span wire:loading.remove wire:target="openSupportCenter">Open support center</span>
                    <span wire:loading wire:target="openSupportCenter">Opening support</span>
                </x-mobile.button>
            @else
                <x-mobile.empty-state
                    title="Support browser disabled"
                    :description="$supportBrowserPolicy['message']"
                />
            @endif

            <div aria-live="polite" class="grid min-h-6 gap-3">
                @if ($supportError)
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 dark:border-amber-300/20 dark:bg-amber-300/10 dark:text-amber-100">
                        {{ $supportError }}
                    </p>
                @elseif ($supportStatus)
                    <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                        {{ $supportStatus }}
                    </p>
                @endif
            </div>
        </div>
    </x-mobile.card>

    <x-mobile.card title="Entries" description="Open connected screens or track the next placeholder implementation.">
        <div class="grid gap-3">
            @forelse ($sectionItems as $item)
                @if ($item['url'])
                    <a
                        wire:key="settings-entry-{{ $item['key'] }}"
                        href="{{ $item['url'] }}"
                        wire:navigate
                        class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 text-left transition hover:bg-app-surface dark:border-zinc-800 dark:bg-zinc-950 dark:hover:bg-zinc-900"
                    >
                        <span class="min-w-0">
                            <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">{{ $item['label'] }}</span>
                            <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $item['description'] }}</span>
                        </span>

                        <span class="flex shrink-0 items-center gap-2">
                            @if ($item['badge'])
                                <x-mobile.badge variant="accent">
                                    {{ $item['badge'] }}
                                </x-mobile.badge>
                            @endif

                            <span aria-hidden="true" class="text-lg font-semibold text-app-muted dark:text-zinc-500">›</span>
                        </span>
                    </a>
                @else
                    <div
                        wire:key="settings-entry-{{ $item['key'] }}"
                        class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-dashed border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <span class="min-w-0">
                            <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">{{ $item['label'] }}</span>
                            <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $item['description'] }}</span>
                        </span>

                        <x-mobile.badge variant="neutral">
                            {{ $item['badge'] ?? 'Placeholder' }}
                        </x-mobile.badge>
                    </div>
                @endif
            @empty
                <x-mobile.empty-state
                    title="No entries yet"
                    description="This settings section is ready for future controls."
                />
            @endforelse
        </div>
    </x-mobile.card>
</section>
