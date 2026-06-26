<section class="safe-x safe-pb flex min-h-full flex-col justify-between gap-8 py-6">
    <div class="space-y-5">
        <div class="rounded-lg border border-app-line bg-app-surface p-5 shadow-[0_18px_38px_-30px_rgba(15,23,42,0.72)] ring-1 ring-white/75">
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
                class="inline-flex min-h-12 touch-manipulation items-center justify-center rounded-lg bg-app-ink px-4 py-3 text-sm font-semibold text-white shadow-[0_14px_28px_-20px_rgba(15,23,42,0.8)] transition duration-150 focus-visible:ring-2 focus-visible:ring-app-accent/25 active:translate-y-px"
            >
                Login
            </a>

            <a
                href="{{ route('mobile.register') }}"
                wire:navigate
                class="inline-flex min-h-12 touch-manipulation items-center justify-center rounded-lg border border-app-line bg-app-surface px-4 py-3 text-sm font-semibold text-app-ink shadow-[0_12px_24px_-20px_rgba(15,23,42,0.45)] transition duration-150 focus-visible:ring-2 focus-visible:ring-app-accent/25 active:translate-y-px"
            >
                Register
            </a>
        </div>
    </div>

    <div class="grid gap-4 text-center">
        <a href="{{ route('mobile.dashboard') }}" wire:navigate class="text-sm font-semibold text-app-accent">
            Continue to dashboard
        </a>

        <div class="grid gap-2 text-xs font-semibold text-app-muted">
            <a href="{{ route('mobile.consent.accept') }}" wire:navigate class="text-app-accent">
                Review consent
            </a>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('mobile.terms') }}" wire:navigate>Terms</a>
                <span aria-hidden="true">·</span>
                <a href="{{ route('mobile.privacy') }}" wire:navigate>Privacy</a>
            </div>
        </div>
    </div>
</section>
