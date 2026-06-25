@extends('layouts.app')

@section('title', 'Tailwind Mobile Check')

@section('header')
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-app-muted">Mobile shell</p>
            <h1 class="text-xl font-semibold tracking-normal text-app-ink">Tailwind Mobile Check</h1>
        </div>

        <span class="rounded-full bg-app-accent px-3 py-1 text-sm font-semibold text-app-accent-ink shadow-sm">
            Vite
        </span>
    </div>
@endsection

@section('body')
    <section class="safe-x safe-pb flex min-h-full flex-col gap-6 py-6">
        <section class="rounded-lg border border-app-line bg-app-surface p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-lg font-semibold text-app-ink">Blade assets loaded</h2>
                    <p class="text-sm leading-6 text-app-muted">
                        This page is rendered by Laravel Blade and styled from the shared Tailwind entry.
                    </p>
                </div>

                <span class="mt-1 size-3 rounded-full bg-app-warm"></span>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3">
            <div class="rounded-lg border border-app-line bg-app-surface p-4">
                <p class="text-2xl font-semibold text-app-ink">4.x</p>
                <p class="mt-1 text-sm text-app-muted">Tailwind</p>
            </div>

            <div class="rounded-lg border border-app-line bg-app-surface p-4">
                <p class="text-2xl font-semibold text-app-ink">Livewire</p>
                <p class="mt-1 text-sm text-app-muted">Blade-ready</p>
            </div>
        </section>

        <a
            href="{{ route('dev.tailwind') }}"
            class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-5 py-3 text-center text-sm font-semibold text-white shadow-sm transition hover:bg-app-accent focus-visible:bg-app-accent"
        >
            Refresh check page
        </a>
    </section>
@endsection

@section('bottomNavigation')
    <div class="grid grid-cols-3 gap-2 text-center text-xs font-medium text-app-muted">
        <span class="rounded-lg bg-app-bg px-3 py-2 text-app-ink">Home</span>
        <span class="px-3 py-2">Scan</span>
        <span class="px-3 py-2">Profile</span>
    </div>
@endsection

@section('toast')
    <div class="rounded-lg border border-app-line bg-app-surface px-4 py-3 text-sm font-medium text-app-ink shadow-lg">
        Mobile layout ready
    </div>
@endsection
