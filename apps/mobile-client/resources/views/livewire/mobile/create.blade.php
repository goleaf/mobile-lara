<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Create"
        description="Start a mobile workflow from the primary action tab."
    />

    <x-mobile.card title="Quick create" description="Start a local-first creation workflow.">
        <div class="grid gap-3">
            @foreach ([
                ['label' => 'New record', 'description' => 'Capture a generic local record and sync it later.', 'badge' => 'Offline ready', 'route' => 'mobile.records.create'],
                ['label' => 'Scan item', 'description' => 'Prepare scanner-based creation for NativePHP.', 'badge' => 'Scanner', 'route' => 'mobile.scanner'],
                ['label' => 'Upload file', 'description' => 'Queue file input for the mobile file plugin.', 'badge' => 'Files', 'route' => 'mobile.files'],
            ] as $item)
                <a
                    href="{{ route($item['route']) }}"
                    wire:navigate
                    wire:key="create-action-{{ $loop->index }}"
                    class="flex min-h-16 w-full items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 text-left transition hover:bg-app-surface dark:border-zinc-800 dark:bg-zinc-950 dark:hover:bg-zinc-900"
                >
                    <span class="min-w-0">
                        <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">{{ $item['label'] }}</span>
                        <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $item['description'] }}</span>
                    </span>
                    <x-mobile.badge variant="neutral" size="sm">
                        {{ $item['badge'] }}
                    </x-mobile.badge>
                </a>
            @endforeach
        </div>
    </x-mobile.card>
</section>
