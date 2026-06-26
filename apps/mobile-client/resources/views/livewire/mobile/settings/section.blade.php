<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        :title="$sectionTitle"
        :description="$sectionDescription"
        :back-href="route('mobile.settings')"
    />

    <x-mobile.card title="Current status" :description="$sectionStatus">
        <div class="rounded-lg border border-app-line bg-app-bg p-4">
            <p class="text-sm font-semibold text-app-ink">Section route ready</p>
            <p class="mt-1 text-sm leading-5 text-app-muted">
                This page is wired as a focused Livewire settings destination.
            </p>
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
                        class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 text-left transition duration-150 hover:bg-app-surface focus-visible:ring-2 focus-visible:ring-app-accent/25 active:translate-y-px"
                    >
                        <span class="min-w-0">
                            <span class="block text-base font-semibold text-app-ink">{{ $item['label'] }}</span>
                            <span class="mt-1 block text-sm leading-5 text-app-muted">{{ $item['description'] }}</span>
                        </span>

                        <span class="flex shrink-0 items-center gap-2">
                            @if ($item['badge'])
                                <x-mobile.badge variant="accent">
                                    {{ $item['badge'] }}
                                </x-mobile.badge>
                            @endif

                            <span aria-hidden="true" class="text-lg font-semibold text-app-muted">›</span>
                        </span>
                    </a>
                @else
                    <div
                        wire:key="settings-entry-{{ $item['key'] }}"
                        class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-dashed border-app-line bg-app-bg px-4 py-3"
                    >
                        <span class="min-w-0">
                            <span class="block text-base font-semibold text-app-ink">{{ $item['label'] }}</span>
                            <span class="mt-1 block text-sm leading-5 text-app-muted">{{ $item['description'] }}</span>
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
