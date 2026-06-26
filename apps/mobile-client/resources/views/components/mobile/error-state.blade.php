@props([
    'title' => 'Something went wrong',
    'message' => null,
])

<section {{ $attributes->class(['rounded-lg border border-red-200/90 bg-red-50/90 px-5 py-6 text-center shadow-[0_14px_32px_-28px_rgba(127,29,29,0.55)] ring-1 ring-white/60 backdrop-blur    ']) }}>
    <div class="mx-auto grid size-12 place-items-center rounded-lg border border-red-200/80 bg-white text-red-600 shadow-sm    ">
        @isset($icon)
            {{ $icon }}
        @else
            <span class="text-xl font-semibold">!</span>
        @endisset
    </div>

    <h2 class="mt-4 text-base font-semibold text-red-900 ">{{ $title }}</h2>

    @if ($message)
        <p class="mt-2 text-sm leading-6 text-red-700 ">{{ $message }}</p>
    @endif

    @isset($action)
        <div class="mt-5">
            {{ $action }}
        </div>
    @endisset
</section>
