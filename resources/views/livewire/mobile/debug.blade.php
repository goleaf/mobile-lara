<section class="safe-x safe-pb flex min-h-full flex-col gap-3 py-6">
    @foreach ([
        'Laravel' => app()->version(),
        'Livewire' => '4.x',
        'NativePHP' => config('nativephp.app_id'),
        'Start URL' => config('nativephp.start_url'),
    ] as $label => $value)
        <div wire:key="debug-{{ $label }}" class="rounded-lg border border-app-line bg-app-surface p-4 shadow-sm">
            <p class="text-sm font-medium text-app-muted">{{ $label }}</p>
            <p class="mt-1 break-words text-base font-semibold text-app-ink">{{ $value }}</p>
        </div>
    @endforeach
</section>
