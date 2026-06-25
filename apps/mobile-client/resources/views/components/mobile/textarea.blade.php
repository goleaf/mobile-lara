@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'hint' => null,
    'error' => null,
    'rows' => 4,
])

@php
    $fieldId = $id ?? $name;
    $errorMessage = $error ?? ($name && isset($errors) ? $errors->first($name) : null);
@endphp

<div class="grid gap-2">
    @if ($label)
        <label @if ($fieldId) for="{{ $fieldId }}" @endif class="text-sm font-medium text-app-ink dark:text-zinc-100">
            {{ $label }}
        </label>
    @endif

    <textarea
        @if ($fieldId) id="{{ $fieldId }}" @endif
        @if ($name) name="{{ $name }}" @endif
        rows="{{ $rows }}"
        @if ($fieldId && $hint && ! $errorMessage) aria-describedby="{{ $fieldId }}-hint" @endif
        @if ($fieldId && $errorMessage) aria-describedby="{{ $fieldId }}-error" @endif
        aria-invalid="{{ $errorMessage ? 'true' : 'false' }}"
        {{ $attributes->class([
            'w-full resize-none rounded-lg border bg-white px-3 py-3 text-base text-app-ink shadow-sm outline-none transition placeholder:text-app-muted/70 focus:border-app-accent focus:ring-2 focus:ring-app-accent/20 disabled:cursor-not-allowed disabled:bg-app-bg disabled:text-app-muted dark:bg-zinc-950 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-emerald-400 dark:focus:ring-emerald-400/20 dark:disabled:bg-zinc-900 dark:disabled:text-zinc-500',
            'border-red-300 focus:border-red-500 focus:ring-red-500/20 dark:border-red-500/70 dark:focus:border-red-400 dark:focus:ring-red-400/20' => $errorMessage,
            'border-app-line dark:border-zinc-700' => ! $errorMessage,
        ]) }}
    >{{ $slot }}</textarea>

    @if ($errorMessage)
        <p @if ($fieldId) id="{{ $fieldId }}-error" @endif class="text-sm font-medium text-red-600 dark:text-red-400">
            {{ $errorMessage }}
        </p>
    @elseif ($hint)
        <p @if ($fieldId) id="{{ $fieldId }}-hint" @endif class="text-sm text-app-muted dark:text-zinc-400">
            {{ $hint }}
        </p>
    @endif
</div>
