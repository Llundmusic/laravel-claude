<x-layouts::app :title="__('Access Denied')">
    <div class="flex h-full flex-1 flex-col items-center justify-center gap-4 p-6">
        <i class="bi bi-lock text-5xl text-zinc-300 dark:text-zinc-600"></i>
        <flux:heading size="xl">{{ __('Access Denied') }}</flux:heading>
        <flux:text class="text-center text-zinc-500">
            {{ __('You do not have permission to access this page.') }}
        </flux:text>
        <flux:button :href="route('home')" wire:navigate variant="primary" icon="home">
            {{ __('Back to dashboard') }}
        </flux:button>
    </div>
</x-layouts::app>
