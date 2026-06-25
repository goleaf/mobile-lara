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

test('mobile shell scss defines fixed responsive header and footer tracks', function (): void {
    expect(resource_path('css/app.scss'))->toBeFile()
        ->and(resource_path('css/app.css'))->not->toBeFile();

    $css = file_get_contents(resource_path('css/app.scss'));

    expect($css)
        ->toContain("@import url('tailwindcss');")
        ->toContain('@mixin shell-size')
        ->toContain('@mixin shell-bar')
        ->toContain('@mixin safe-area-padding')
        ->toContain('--mobile-shell-header-height')
        ->toContain('--mobile-shell-footer-height')
        ->toContain('.mobile-shell')
        ->toContain('.mobile-shell-header')
        ->toContain('.mobile-shell-footer')
        ->toContain('height: 100dvh;')
        ->toContain('max-height: 100dvh;')
        ->toContain('grid-template-rows: var(--mobile-shell-header-height) auto minmax(0, 1fr) var(--mobile-shell-footer-height);');
});

test('scss entrypoint is wired through postcss tailwind bridge', function (): void {
    expect(file_get_contents(base_path('vite.config.js')))
        ->toContain('resources/css/app.scss')
        ->not->toContain('@tailwindcss/vite')
        ->and(file_get_contents(base_path('postcss.config.mjs')))
        ->toContain("'@tailwindcss/postcss'");
});
