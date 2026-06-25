<section class="safe-x safe-pb flex min-h-full flex-col gap-3 py-6">
    @foreach (['Mobile shell created', 'NativePHP configured', 'Tailwind build passed'] as $notification)
        <article wire:key="notification-{{ $loop->index }}" class="rounded-lg border border-app-line bg-app-surface p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-base font-semibold text-app-ink dark:text-zinc-100">{{ $notification }}</p>
            <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">Just now</p>
        </article>
    @endforeach

    <x-mobile.floating-action-button label="Create" route="mobile.create">
        <x-slot:icon>
            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </x-slot:icon>
    </x-mobile.floating-action-button>
</section>
