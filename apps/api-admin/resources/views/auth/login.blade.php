@extends('layouts.auth')

@section('content')
    <section class="w-full rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
        <div class="grid gap-2">
            <h1 class="text-xl font-semibold text-zinc-950 dark:text-zinc-100">Mobile Lara Admin</h1>
            <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-400">Sign in with a platform admin account.</p>
        </div>

        <form method="POST" action="{{ route('admin.login.store') }}" class="mt-6 grid gap-4">
            @csrf

            <label class="grid gap-1.5">
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Email</span>
                <input
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                    class="min-h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-100 dark:focus:ring-zinc-100/10"
                >
                @error('email')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-1.5">
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Password</span>
                <input
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    class="min-h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none transition focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-100 dark:focus:ring-zinc-100/10"
                >
                @error('password')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </label>

            <button type="submit" class="min-h-11 rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-zinc-200">
                Sign In
            </button>
        </form>
    </section>
@endsection
