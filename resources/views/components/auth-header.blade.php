@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <h1 class="text-2xl font-bold">{{ $title }}</h1>
    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
</div>
