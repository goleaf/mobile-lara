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
        ['wire:click="save"', 'Save', 'data-loading:pointer-events-none'],
    ],
    'input' => [
        '<x-mobile.input name="email" label="Email" wire:model.live="email" />',
        ['name="email"', 'Email', 'wire:model.live="email"', 'aria-invalid="false"'],
    ],
    'textarea' => [
        '<x-mobile.textarea name="notes" label="Notes" wire:model="notes">Draft</x-mobile.textarea>',
        ['name="notes"', 'Notes', 'wire:model="notes"', 'Draft'],
    ],
    'select' => [
        '<x-mobile.select name="mode" label="Mode" :options="[\'compact\' => \'Compact\']" />',
        ['name="mode"', 'Mode', 'value="compact"', 'Compact'],
    ],
    'card' => [
        '<x-mobile.card title="Card title" description="Card copy">Card body</x-mobile.card>',
        ['Card title', 'Card copy', 'Card body'],
    ],
    'modal' => [
        '<x-mobile.modal :show="true" title="Dialog title">Dialog body</x-mobile.modal>',
        ['role="dialog"', 'aria-modal="true"', 'Dialog title', 'Dialog body'],
    ],
    'badge' => [
        '<x-mobile.badge variant="success" dot>Active</x-mobile.badge>',
        ['Active', 'bg-emerald-50'],
    ],
    'avatar' => [
        '<x-mobile.avatar initials="ML" status="online" />',
        ['ML', 'bg-emerald-500'],
    ],
    'empty state' => [
        '<x-mobile.empty-state title="Nothing here" description="Try another search." />',
        ['Nothing here', 'Try another search.'],
    ],
    'error state' => [
        '<x-mobile.error-state title="Offline" message="Reconnect and try again." />',
        ['Offline', 'Reconnect and try again.'],
    ],
    'loading skeleton' => [
        '<x-mobile.loading-skeleton :lines="2" avatar />',
        ['animate-pulse', 'rounded-full'],
    ],
    'bottom sheet' => [
        '<x-mobile.bottom-sheet :show="true" title="Actions">Sheet body</x-mobile.bottom-sheet>',
        ['role="dialog"', 'Actions', 'Sheet body', 'bottom-0'],
    ],
    'page header' => [
        '<x-mobile.page-header title="Dashboard" description="Overview" back-href="/" />',
        ['Dashboard', 'Overview', 'href="/"', 'aria-label="Back"'],
    ],
]);
