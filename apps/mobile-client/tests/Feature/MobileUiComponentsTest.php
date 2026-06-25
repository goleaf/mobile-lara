<?php

use Illuminate\Support\Facades\Blade;

test('mobile ui components render expected markup', function (string $template, array $expected): void {
    $html = Blade::render($template);

    foreach ($expected as $fragment) {
        expect($html)->toContain($fragment);
    }
})->with([
    'button' => [
        '<x-mobile.button wire:click="save">Save</x-mobile.button>',
        ['wire:click="save"', 'Save', 'data-loading:pointer-events-none', 'dark:bg-zinc-100'],
    ],
    'loading spinner' => [
        '<x-mobile.loading-spinner label="Refreshing" />',
        ['role="status"', 'aria-label="Refreshing"', 'motion-safe:animate-spin'],
    ],
    'loading state' => [
        '<x-mobile.loading-state target="refresh" message="Refreshing data..." />',
        ['wire:loading.delay', 'wire:target="refresh"', 'Refreshing data...'],
    ],
    'submit button' => [
        '<x-mobile.submit-button target="save" loading-label="Saving...">Save</x-mobile.submit-button>',
        ['type="submit"', 'wire:loading.attr="disabled"', 'Saving...', 'Save'],
    ],
    'toast' => [
        '<x-mobile.toast message="Saved" variant="success" />',
        ['role="status"', 'Saved', 'border-emerald-200', 'pointer-events-auto'],
    ],
    'toast warning' => [
        '<x-mobile.toast title="Check" message="Fallback active" variant="warning" />',
        ['role="status"', 'Check', 'Fallback active', 'border-amber-200'],
    ],
    'toast info' => [
        '<x-mobile.toast message="Queued" variant="info" />',
        ['role="status"', 'Queued', 'border-sky-200'],
    ],
    'retry button' => [
        '<x-mobile.retry-button wire:click="retry" target="retry">Try again</x-mobile.retry-button>',
        ['type="button"', 'wire:click="retry"', 'Retrying...', 'Try again'],
    ],
    'page skeleton' => [
        '<x-mobile.page-skeleton :cards="1" />',
        ['animate-pulse', 'aria-hidden="true"', 'dark:bg-zinc-900'],
    ],
    'network error state' => [
        '<x-mobile.network-error-state retry-action="retrySearch" />',
        ['Connection problem', 'wire:click="retrySearch"', 'Try again'],
    ],
    'app header' => [
        '<x-mobile.app-header title="Dashboard" />',
        ['Dashboard', 'aria-label="Notifications"', 'aria-label="Profile"', 'dark:bg-zinc-900'],
    ],
    'bottom navigation' => [
        '<x-mobile.bottom-navigation />',
        ['Dashboard', 'Search', 'Create', 'Notifications', 'Profile', 'mobile-tab-mobile.create'],
    ],
    'input' => [
        '<x-mobile.input name="email" label="Email" wire:model.live="email" />',
        ['name="email"', 'Email', 'wire:model.live="email"', 'aria-invalid="false"', 'dark:bg-zinc-950'],
    ],
    'textarea' => [
        '<x-mobile.textarea name="notes" label="Notes" wire:model="notes">Draft</x-mobile.textarea>',
        ['name="notes"', 'Notes', 'wire:model="notes"', 'Draft', 'dark:bg-zinc-950'],
    ],
    'select' => [
        '<x-mobile.select name="mode" label="Mode" :options="[\'compact\' => \'Compact\']" />',
        ['name="mode"', 'Mode', 'value="compact"', 'Compact', 'dark:bg-zinc-950'],
    ],
    'card' => [
        '<x-mobile.card title="Card title" description="Card copy">Card body</x-mobile.card>',
        ['Card title', 'Card copy', 'Card body', 'dark:bg-zinc-900'],
    ],
    'modal' => [
        '<x-mobile.modal :show="true" title="Dialog title">Dialog body</x-mobile.modal>',
        ['role="dialog"', 'aria-modal="true"', 'Dialog title', 'Dialog body', 'dark:bg-zinc-900'],
    ],
    'badge' => [
        '<x-mobile.badge variant="success" dot>Active</x-mobile.badge>',
        ['Active', 'bg-emerald-50', 'dark:text-emerald-200'],
    ],
    'avatar' => [
        '<x-mobile.avatar initials="ML" status="online" />',
        ['ML', 'bg-emerald-500', 'dark:bg-emerald-400'],
    ],
    'empty state' => [
        '<x-mobile.empty-state title="Nothing here" description="Try another search." />',
        ['Nothing here', 'Try another search.', 'dark:bg-zinc-900'],
    ],
    'error state' => [
        '<x-mobile.error-state title="Offline" message="Reconnect and try again." />',
        ['Offline', 'Reconnect and try again.', 'dark:bg-red-400/10'],
    ],
    'loading skeleton' => [
        '<x-mobile.loading-skeleton :lines="2" avatar />',
        ['animate-pulse', 'rounded-full', 'dark:bg-zinc-900'],
    ],
    'bottom sheet' => [
        '<x-mobile.bottom-sheet :show="true" title="Actions">Sheet body</x-mobile.bottom-sheet>',
        ['role="dialog"', 'Actions', 'Sheet body', 'bottom-0', 'dark:bg-zinc-900'],
    ],
    'page header' => [
        '<x-mobile.page-header title="Dashboard" description="Overview" back-href="/" />',
        ['Dashboard', 'Overview', 'href="/"', 'aria-label="Back"', 'dark:text-zinc-100'],
    ],
]);

test('floating action button renders route mode', function (): void {
    $html = Blade::render(
        '<x-mobile.floating-action-button label="Create" route="mobile.create"><x-slot:icon><span>+</span></x-slot:icon></x-mobile.floating-action-button>',
    );

    expect($html)
        ->toContain('href="'.route('mobile.create').'"')
        ->toContain('wire:navigate')
        ->toContain('aria-label="Create"')
        ->toContain('bottom-24')
        ->toContain('Create');
});

test('floating action button renders action loading and disabled mode', function (): void {
    $html = Blade::render(
        '<x-mobile.floating-action-button label="Refresh" action="refreshDashboard" target="refreshDashboard" disabled loading-label="Refreshing..." />',
    );

    expect($html)
        ->toContain('wire:click="refreshDashboard"')
        ->toContain('wire:loading.attr="disabled"')
        ->toContain('wire:target="refreshDashboard"')
        ->toContain('disabled')
        ->toContain('Refreshing...');
});
