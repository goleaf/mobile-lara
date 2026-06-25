@props([
    'title' => 'Something went wrong',
    'message' => null,
])

<section {{ $attributes->class(['rounded-lg border border-red-200 bg-red-50 px-5 py-6 text-center']) }}>
    <div class="mx-auto grid size-12 place-items-center rounded-full bg-white text-red-600 shadow-sm">
        @isset($icon)
            {{ $icon }}
        @else
            <span class="text-xl font-semibold">!</span>
        @endisset
    </div>

    <h2 class="mt-4 text-base font-semibold text-red-900">{{ $title }}</h2>

    @if ($message)
        <p class="mt-2 text-sm leading-6 text-red-700">{{ $message }}</p>
    @endif

    @isset($action)
        <div class="mt-5">
            {{ $action }}
        </div>
    @endisset
</section>
