<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-5 py-6">
    <x-mobile.page-header
        title="{{ $policy['title'] }}"
        description="Current version {{ $policy['version'] }}."
        back-href="{{ route('mobile.consent.accept') }}"
    >
        <x-slot:action>
            <x-mobile.badge :variant="$isAccepted ? 'success' : 'warning'" dot>
                {{ $isAccepted ? 'Accepted' : 'Review' }}
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    <x-mobile.card title="Version details" description="Privacy text prepared for mobile consent capture.">
        <dl class="grid gap-3 text-sm">
            <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  ">
                <dt class="text-app-muted ">Policy key</dt>
                <dd class="font-mono text-xs font-semibold text-app-ink ">{{ $policy['key'] }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  ">
                <dt class="text-app-muted ">Version</dt>
                <dd class="font-semibold text-app-ink ">{{ $policy['version'] }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  ">
                <dt class="text-app-muted ">Effective date</dt>
                <dd class="font-semibold text-app-ink ">{{ $policy['effective_date'] }}</dd>
            </div>
        </dl>
    </x-mobile.card>

    <x-mobile.card title="Summary" description="Important privacy points before accepting.">
        <ul class="grid gap-3">
            @forelse ($policy['summary'] as $summary)
                <li wire:key="privacy-summary-{{ $loop->index }}" class="rounded-lg border border-app-line bg-app-bg p-4 text-sm leading-6 text-app-ink   ">
                    {{ $summary }}
                </li>
            @empty
                <li class="text-sm text-app-muted ">No summary is configured.</li>
            @endforelse
        </ul>
    </x-mobile.card>

    <div class="grid gap-4">
        @forelse ($policy['sections'] as $section)
            <x-mobile.card wire:key="privacy-section-{{ $loop->index }}" title="{{ $section['heading'] }}">
                <p class="text-sm leading-6 text-app-muted ">{{ $section['body'] }}</p>
            </x-mobile.card>
        @empty
            <x-mobile.empty-state title="No privacy content" description="Privacy content will appear when policy sections are configured." />
        @endforelse
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        <a href="{{ route('mobile.terms') }}" wire:navigate class="inline-flex min-h-12 items-center justify-center rounded-lg border border-app-line bg-app-surface px-4 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg    ">
            Terms of Service
        </a>
        <a href="{{ route('mobile.consent.accept') }}" wire:navigate class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90   ">
            Accept consent
        </a>
    </div>
</section>
