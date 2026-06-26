<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-6 py-6">
    <x-mobile.page-header
        title="Verify email"
        description="Confirm where the mobile verification link should be sent."
        back-href="{{ route('mobile.login') }}"
    />

    <form wire:submit="sendVerification" class="grid gap-5">
        <x-mobile.card title="Email verification" description="Validate your email before requesting a verification link.">
            <div class="grid gap-4">
                <x-mobile.input
                    name="email"
                    label="Email"
                    type="email"
                    autocomplete="email"
                    inputmode="email"
                    wire:model.blur="email"
                />

                <div aria-live="polite" class="min-h-6">
                    @if ($status)
                        <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800   ">
                            {{ $status }}
                        </p>
                    @endif
                </div>
            </div>

            <x-slot:footer>
                <x-mobile.submit-button target="sendVerification" loading-label="Checking email...">
                    Send verification
                </x-mobile.submit-button>
            </x-slot:footer>
        </x-mobile.card>
    </form>

    <a href="{{ route('mobile.login') }}" wire:navigate class="text-center text-sm font-semibold text-app-accent ">
        Back to login
    </a>
</section>
