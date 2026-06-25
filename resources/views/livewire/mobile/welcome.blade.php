<section class="safe-x safe-pb flex min-h-full flex-col justify-between gap-8 py-6">
    <div class="space-y-5">
        <div class="rounded-lg border border-app-line bg-app-surface p-5 shadow-sm">
            <p class="text-sm font-medium text-app-muted">Native Laravel</p>
            <h2 class="mt-2 text-2xl font-semibold tracking-normal text-app-ink">Livewire pages, Blade views, mobile shell.</h2>
            <p class="mt-3 text-sm leading-6 text-app-muted">
                A focused route structure is ready for the app flow.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <a
                href="{{ route('mobile.login') }}"
                wire:navigate
                class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 py-3 text-sm font-semibold text-white shadow-sm"
            >
                Login
            </a>

            <a
                href="{{ route('mobile.register') }}"
                wire:navigate
                class="inline-flex min-h-12 items-center justify-center rounded-lg border border-app-line bg-app-surface px-4 py-3 text-sm font-semibold text-app-ink shadow-sm"
            >
                Register
            </a>
        </div>
    </div>

    <a href="{{ route('mobile.dashboard') }}" wire:navigate class="text-center text-sm font-semibold text-app-accent">
        Continue to dashboard
    </a>
</section>
