<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Create"
        description="Start a mobile workflow from the primary action tab."
    />

    <x-mobile.card title="Quick create" description="Start a local-first creation workflow.">
        <div class="grid gap-3">
            @forelse ($createActions as $item)
                <a
                    href="{{ route($item['route']) }}"
                    wire:navigate
                    wire:key="create-action-{{ $loop->index }}"
                    class="flex min-h-16 w-full items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 text-left transition duration-150 hover:bg-app-surface focus-visible:ring-2 focus-visible:ring-app-accent/25 active:translate-y-px"
                >
                    <span class="min-w-0">
                        <span class="block text-base font-semibold text-app-ink ">{{ $item['label'] }}</span>
                        <span class="mt-1 block text-sm leading-5 text-app-muted ">{{ $item['description'] }}</span>
                    </span>
                    <x-mobile.badge variant="neutral" size="sm">
                        {{ $item['badge'] }}
                    </x-mobile.badge>
                </a>
            @empty
                <x-mobile.empty-state
                    title="No create actions"
                    description="Admin/API policy has not enabled any create workflow for this workspace."
                />
            @endforelse
        </div>
    </x-mobile.card>
</section>
