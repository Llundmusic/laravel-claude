<div>
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
            <h1 class="text-2xl font-bold">{{ __('Saved Filters') }}</h1>
            <button class="btn btn-primary" wire:click="$set('showCreateModal', true)">
                <i class="bi bi-plus"></i> {{ __('New Filter') }}
            </button>
        </div>

        @if($this->filters->isEmpty())
        <div class="flex flex-1 items-center justify-center text-zinc-400">
            <div class="text-center">
                <i class="bi bi-funnel text-4xl"></i>
                <p class="mt-2 text-sm">{{ __('No saved filters yet.') }}</p>
            </div>
        </div>
        @else
        <div x-init="initSortable($el)" class="flex flex-col gap-2">
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
                <button class="btn btn-ghost btn-sm"
                    wire:click="deleteFilter({{ $filter->id }})"
                    wire:confirm="{{ __('Delete this filter?') }}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Create modal --}}
    <dialog x-data x-init="$watch('$wire.showCreateModal', v => { if(v) $el.showModal(); else $el.close(); })" @close="$wire.showCreateModal = false" class="modal">
        <div class="modal-box w-full max-w-md">
            <h3 class="font-bold text-lg mb-4">{{ __('New Saved Filter') }}</h3>
            <div class="flex flex-col gap-4">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Page name') }}</span></label>
                    <input type="text" wire:model="newPageName" class="input input-bordered w-full"
                           placeholder="e.g. orders" required />
                    @error('newPageName') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Filter name') }}</span></label>
                    <input type="text" wire:model="newFilterName" class="input input-bordered w-full" required />
                    @error('newFilterName') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Column (optional)') }}</span></label>
                    <input type="text" wire:model="newFilterColumn" class="input input-bordered w-full" />
                </div>
            </div>
            <div class="modal-action">
                <button class="btn btn-ghost" wire:click="$set('showCreateModal', false)">{{ __('Cancel') }}</button>
                <button class="btn btn-primary" wire:click="createFilter">{{ __('Save') }}</button>
            </div>
        </div>
    </dialog>
</div>
