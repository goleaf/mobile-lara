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
        <label @if ($fieldId) for="{{ $fieldId }}" @endif class="text-sm font-medium text-app-ink">
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
            'w-full resize-none rounded-lg border bg-white px-3 py-3 text-base text-app-ink shadow-sm outline-none transition placeholder:text-app-muted/70 focus:border-app-accent focus:ring-2 focus:ring-app-accent/20 disabled:cursor-not-allowed disabled:bg-app-bg disabled:text-app-muted',
            'border-red-300 focus:border-red-500 focus:ring-red-500/20' => $errorMessage,
            'border-app-line' => ! $errorMessage,
        ]) }}
    >{{ $slot }}</textarea>

    @if ($errorMessage)
        <p @if ($fieldId) id="{{ $fieldId }}-error" @endif class="text-sm font-medium text-red-600">
            {{ $errorMessage }}
        </p>
    @elseif ($hint)
        <p @if ($fieldId) id="{{ $fieldId }}-hint" @endif class="text-sm text-app-muted">
            {{ $hint }}
        </p>
    @endif
</div>
