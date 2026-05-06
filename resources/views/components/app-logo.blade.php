@props([
    'sidebar' => false,
])

<a {{ $attributes }} class="flex items-center gap-2 font-semibold text-sm">
    <span class="flex aspect-square size-8 items-center justify-center rounded-md bg-zinc-900 dark:bg-white">
        <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
    </span>
    <span class="truncate">{{ config('app.name', 'Laravel') }}</span>
</a>
