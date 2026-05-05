<x-layouts::app :title="__('Saved Filters')">
    <div
        class="flex h-full flex-1 flex-col gap-6 p-6"
        x-data="{
            initSortable(el) {
                Sortable.create(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: (evt) => {
                        const ids = Array.from(el.querySelectorAll('[data-id]')).map(el => parseInt(el.dataset.id));
                        $wire.reorder(ids);
                    }
                });
            }
        }"
    >
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('Saved Filters') }}</flux:heading>
            <flux:button variant="primary" icon="plus" wire:click="$set('showCreateModal', true)">{{ __('New Filter') }}</flux:button>
        </div>

        @if($this->filters->isEmpty())
        <div class="flex flex-1 items-center justify-center text-zinc-400">
            <div class="text-center">
                <i class="bi bi-funnel text-4xl"></i>
                <p class="mt-2 text-sm">{{ __('No saved filters yet.') }}</p>
            </div>
        </div>
        @else
        <div
            x-init="initSortable($el)"
            class="flex flex-col gap-2"
        >
            @foreach($this->filters as $filter)
            <div
                data-id="{{ $filter->id }}"
                class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900"
            >
                <i class="bi bi-grip-vertical drag-handle cursor-grab text-zinc-300 dark:text-zinc-600"></i>
                <div class="flex-1">
                    <div class="font-medium">{{ $filter->filter_name }}</div>
                    <div class="text-xs text-zinc-400">{{ $filter->page_name }}</div>
                </div>
                <flux:button
                    icon="trash"
                    variant="ghost"
                    size="sm"
                    wire:click="deleteFilter({{ $filter->id }})"
                    wire:confirm="{{ __('Delete this filter?') }}"
                />
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <flux:modal wire:model="showCreateModal" class="w-full max-w-md">
        <flux:modal.header>{{ __('New Saved Filter') }}</flux:modal.header>
        <flux:modal.body class="flex flex-col gap-4">
            <flux:input wire:model="newPageName" :label="__('Page name')" placeholder="e.g. orders" required />
            <flux:input wire:model="newFilterName" :label="__('Filter name')" required />
            <flux:input wire:model="newFilterColumn" :label="__('Column (optional)')" />
        </flux:modal.body>
        <flux:modal.footer class="flex justify-end gap-3">
            <flux:button wire:click="$set('showCreateModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button wire:click="createFilter" variant="primary">{{ __('Save') }}</flux:button>
        </flux:modal.footer>
    </flux:modal>

</x-layouts::app>

