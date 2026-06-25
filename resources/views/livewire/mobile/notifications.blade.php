<section class="safe-x safe-pb flex min-h-full flex-col gap-3 py-6">
    @foreach (['Mobile shell created', 'NativePHP configured', 'Tailwind build passed'] as $notification)
        <article wire:key="notification-{{ $loop->index }}" class="rounded-lg border border-app-line bg-app-surface p-4 shadow-sm">
            <p class="text-base font-semibold text-app-ink">{{ $notification }}</p>
            <p class="mt-1 text-sm text-app-muted">Just now</p>
        </article>
    @endforeach
</section>
