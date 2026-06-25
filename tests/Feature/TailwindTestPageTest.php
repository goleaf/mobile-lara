<?php

test('tailwind mobile test page renders', function () {
    $this->withoutVite();

    $this->get(route('dev.tailwind'))
        ->assertOk()
        ->assertSee('Tailwind Mobile Check')
        ->assertSee('mobile-app-content', false)
        ->assertSee('mobile-toast-region', false)
        ->assertSee('aria-label="Primary tabs"', false)
        ->assertSee('bg-app-accent', false)
        ->assertSee('safe-x', false);
});
