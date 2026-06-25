<x-slot:header>
    <x-mobile.page-header eyebrow="Account" title="Login" description="Return to your mobile workspace." />
</x-slot:header>

<section class="safe-x safe-pb flex min-h-full flex-col justify-between gap-8 py-6">
    <form class="space-y-4 rounded-lg border border-app-line bg-app-surface p-5 shadow-sm">
        <label class="block">
            <span class="text-sm font-medium text-app-ink">Email</span>
            <input
                type="email"
                autocomplete="email"
                class="mt-2 min-h-12 w-full rounded-lg border border-app-line bg-white px-3 text-base text-app-ink outline-none focus:border-app-accent"
            >
        </label>

        <label class="block">
            <span class="text-sm font-medium text-app-ink">Password</span>
            <input
                type="password"
                autocomplete="current-password"
                class="mt-2 min-h-12 w-full rounded-lg border border-app-line bg-white px-3 text-base text-app-ink outline-none focus:border-app-accent"
            >
        </label>

        <button type="button" class="min-h-12 w-full rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm">
            Sign in
        </button>
    </form>

    <a href="{{ route('mobile.register') }}" wire:navigate class="text-center text-sm font-semibold text-app-accent">
        Create account
    </a>
</section>
