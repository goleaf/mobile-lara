@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'hint' => null,
    'error' => null,
    'placeholder' => null,
    'options' => [],
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

    <select
        @if ($fieldId) id="{{ $fieldId }}" @endif
        @if ($name) name="{{ $name }}" @endif
        @if ($fieldId && $hint && ! $errorMessage) aria-describedby="{{ $fieldId }}-hint" @endif
        @if ($fieldId && $errorMessage) aria-describedby="{{ $fieldId }}-error" @endif
        aria-invalid="{{ $errorMessage ? 'true' : 'false' }}"
        {{ $attributes->class([
            'min-h-12 w-full rounded-lg border bg-white px-3 text-base text-app-ink shadow-sm outline-none transition focus:border-app-accent focus:ring-2 focus:ring-app-accent/20 disabled:cursor-not-allowed disabled:bg-app-bg disabled:text-app-muted dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-emerald-400 dark:focus:ring-emerald-400/20 dark:disabled:bg-zinc-900 dark:disabled:text-zinc-500',
            'border-red-300 focus:border-red-500 focus:ring-red-500/20 dark:border-red-500/70 dark:focus:border-red-400 dark:focus:ring-red-400/20' => $errorMessage,
            'border-app-line dark:border-zinc-700' => ! $errorMessage,
        ]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ is_int($optionValue) ? $optionLabel : $optionValue }}">
                {{ $optionLabel }}
            </option>
        @endforeach

        {{ $slot }}
    </select>

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
