<?php

use App\Livewire\Mobile\Debug;
use Livewire\Livewire;

test('debug screen renders native dialog examples', function (): void {
    Livewire::test(Debug::class)
        ->assertSee('Runtime')
        ->assertSee('Native dialogs')
        ->assertSee('Prompt default value')
        ->assertSee('Alert')
        ->assertSee('Confirm')
        ->assertSee('Prompt')
        ->assertSee('Toast')
        ->assertSee('Snackbar');
});

test('debug dialog actions update the last payload', function (string $action, string $type, string $status): void {
    $component = Livewire::test(Debug::class)
        ->call($action)
        ->assertSet('dialogStatus', $status)
        ->assertSee($status)
        ->assertSee('Last payload');

    expect($component->instance()->dialogResult)
        ->toBeArray()
        ->and($component->instance()->dialogResult['type'])->toBe($type)
        ->and($component->instance()->dialogResult['dispatched'])->toBe(function_exists('nativephp_call'));
})->with([
    'alert' => ['showAlertExample', 'alert', 'Alert dialog requested.'],
    'confirm' => ['showConfirmExample', 'confirm', 'Confirm dialog requested.'],
    'prompt' => ['showPromptExample', 'prompt', 'Prompt fallback requested.'],
    'toast' => ['showToastExample', 'toast', 'Toast notification requested.'],
    'snackbar' => ['showSnackbarExample', 'snackbar', 'Snackbar notification requested.'],
]);

test('debug prompt example validates the default value', function (): void {
    Livewire::test(Debug::class)
        ->set('promptValue', str_repeat('x', 81))
        ->call('showPromptExample')
        ->assertHasErrors(['promptValue' => 'max']);
});
