<x-layouts::app :title="__('Access Denied')">
    <div class="flex h-full flex-1 flex-col items-center justify-center gap-4 p-6">
        <i class="bi bi-lock text-5xl text-zinc-300 dark:text-zinc-600"></i>
        <h1 class="text-2xl font-bold">{{ __('Access Denied') }}</h1>
        <p class="text-center text-sm text-zinc-500">
            {{ __('You do not have permission to access this page.') }}
        </p>
        <a href="{{ route('home') }}" wire:navigate class="btn btn-primary">
            <i class="bi bi-house"></i>
            {{ __('Back to dashboard') }}
        </a>
    </div>
</x-layouts::app>
