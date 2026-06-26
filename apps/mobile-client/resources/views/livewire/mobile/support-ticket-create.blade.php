<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="submit" message="Creating support ticket..." />

    <x-mobile.page-header
        title="Create support ticket"
        description="Send a tenant-scoped help request through the Admin/API support queue."
        :back-href="route('mobile.support.index')"
    />

    @if (! $supportPolicy['allowed'])
        <x-mobile.error-state
            title="Support disabled"
            :message="$supportPolicy['message']"
        />
    @else
        <x-mobile.card title="Ticket details" description="Describe the issue and attach diagnostics by id when you have exported one.">
            <form wire:submit="submit" class="grid gap-4">
                <x-mobile.input
                    wire:model.blur="subject"
                    name="subject"
                    label="Subject"
                    placeholder="What do you need help with?"
                />

                <x-mobile.textarea
                    wire:model.blur="body"
                    name="body"
                    label="Message"
                    hint="Avoid passwords, tokens, or private customer data."
                    rows="6"
                />

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-mobile.select
                        wire:model="priority"
                        name="priority"
                        label="Priority"
                        :options="$priorityOptions"
                    />

                    <x-mobile.input
                        wire:model.blur="category"
                        name="category"
                        label="Category"
                        placeholder="sync, records, billing"
                    />
                </div>

                <x-mobile.input
                    wire:model.blur="diagnosticReportId"
                    name="diagnosticReportId"
                    label="Diagnostics report id"
                    hint="Optional. Share only a redacted diagnostics export id."
                    placeholder="diagnostic report id"
                />

                @if ($submissionError)
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 dark:border-amber-300/20 dark:bg-amber-300/10 dark:text-amber-100">
                        {{ $submissionError }}
                    </p>
                @endif

                <x-mobile.button type="submit" variant="primary" full wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit">Create ticket</span>
                    <span wire:loading wire:target="submit">Creating</span>
                </x-mobile.button>
            </form>
        </x-mobile.card>
    @endif
</section>
