<x-slot:toast>
    <x-mobile.toast :message="$toastMessage" :variant="$toastVariant" />
</x-slot:toast>

<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-6 py-6">
    <x-mobile.page-header
        title="Sign in"
        description="Use your mobile app account to continue."
        back-href="{{ route('mobile.welcome') }}"
    />

    <form wire:submit="login" class="grid gap-5">
        <x-mobile.loading-state target="email, password, remember" message="Checking sign-in details..." />

        <x-mobile.card title="Account access" description="Enter your email and password.">
            <div class="grid gap-4">
                <x-mobile.input
                    name="email"
                    label="Email"
                    type="email"
                    autocomplete="email"
                    inputmode="email"
                    wire:model.live.blur="email"
                />

                <x-mobile.input
                    name="password"
                    label="Password"
                    type="password"
                    autocomplete="current-password"
                    wire:model.live.blur="password"
                />

                <div class="flex items-center justify-between gap-4">
                    <label class="flex min-w-0 items-center gap-3 text-sm font-medium text-app-ink dark:text-zinc-100">
                        <input
                            type="checkbox"
                            wire:model.live="remember"
                            class="size-5 rounded border-app-line text-app-accent focus:ring-app-accent dark:border-zinc-700 dark:bg-zinc-950"
                        >
                        <span>Remember me</span>
                    </label>

                    <a href="{{ route('mobile.password.request') }}" wire:navigate class="shrink-0 text-sm font-semibold text-app-accent dark:text-emerald-300">
                        Forgot?
                    </a>
                </div>

                <div aria-live="polite" class="min-h-6">
                    @if ($status)
                        <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                            {{ $status }}
                        </p>
                    @endif
                </div>
            </div>

            <x-slot:footer>
                <x-mobile.submit-button target="login" loading-label="Signing in..." :disabled="! $this->canSubmit">
                    Sign in
                </x-mobile.submit-button>
            </x-slot:footer>
        </x-mobile.card>
    </form>

    <div class="grid gap-3 text-center text-sm">
        <a href="{{ route('mobile.register') }}" wire:navigate class="font-semibold text-app-accent dark:text-emerald-300">
            Create account
        </a>
        <a href="{{ route('mobile.verification.notice') }}" wire:navigate class="font-semibold text-app-muted dark:text-zinc-400">
            Verify email
        </a>
    </div>
</section>
