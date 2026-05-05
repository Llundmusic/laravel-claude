<x-layouts::app :title="__('Access Groups')">
    <div class="flex h-full flex-1 gap-0 overflow-hidden">
        {{-- Group list --}}
        <div class="flex w-80 shrink-0 flex-col border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between p-4">
                <flux:heading>{{ __('Access Groups') }}</flux:heading>
                <flux:button size="sm" variant="primary" icon="plus" wire:click="$set('showCreateModal', true)" />
            </div>
            <div class="px-4 pb-3">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" size="sm" :placeholder="__('Search…')" clearable />
            </div>
            <div class="flex-1 overflow-y-auto">
                @foreach($this->groups as $group)
                <button
                    wire:click="selectGroup({{ $group->id }})"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm transition hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ $selectedGroupId === $group->id ? 'bg-zinc-100 dark:bg-zinc-800 font-medium' : '' }}"
                >
                    <div>
                        <div class="font-medium">{{ $group->name }}</div>
                        <div class="text-xs text-zinc-400">{{ $group->roles_count }} roles · {{ $group->companies_count }} companies</div>
                    </div>
                    <flux:badge size="sm" :variant="$group->is_active ? 'success' : 'zinc'">
                        {{ $group->is_active ? __('On') : __('Off') }}
                    </flux:badge>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Detail panel --}}
        <div class="flex flex-1 flex-col overflow-y-auto p-6">
            @if($this->selectedGroup)
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading size="xl">{{ $this->selectedGroup->name }}</flux:heading>
                </div>
                <flux:button
                    :variant="$this->selectedGroup->is_active ? 'danger' : 'primary'"
                    size="sm"
                    wire:click="toggleActive({{ $this->selectedGroup->id }})"
                >
                    {{ $this->selectedGroup->is_active ? __('Deactivate') : __('Activate') }}
                </flux:button>
            </div>

            <div class="mb-6 max-w-xl">
                <flux:textarea wire:model.live.debounce.600ms="editDescription" :label="__('Description')" rows="2" />
            </div>

            <div class="grid grid-cols-2 gap-6">
                {{-- Roles --}}
                <div>
                    <flux:heading size="sm" class="mb-3">{{ __('Roles') }}</flux:heading>
                    <div class="flex flex-col gap-2 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        @foreach($this->allRoles as $role)
                        @php $hasRole = $this->selectedGroup->roles->contains('id', $role->id) @endphp
                        <label class="flex cursor-pointer items-center justify-between gap-3">
                            <span class="text-sm">{{ $role->name }}</span>
                            <flux:checkbox
                                wire:click="toggleRole({{ $role->id }})"
                                :checked="$hasRole"
                            />
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Companies --}}
                <div>
                    <flux:heading size="sm" class="mb-3">{{ __('Companies') }}</flux:heading>
                    <div class="flex flex-col gap-2 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        @foreach($this->allCompanies as $company)
                        @php $hasCompany = $this->selectedGroup->companies->contains('id', $company->id) @endphp
                        <label class="flex cursor-pointer items-center justify-between gap-3">
                            <span class="text-sm">{{ $company->name }}</span>
                            <flux:checkbox
                                wire:click="toggleCompany({{ $company->id }})"
                                :checked="$hasCompany"
                            />
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="flex h-full items-center justify-center text-zinc-400">
                <div class="text-center">
                    <i class="bi bi-key text-4xl"></i>
                    <p class="mt-2 text-sm">{{ __('Select a group to manage it') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <flux:modal wire:model="showCreateModal" class="w-full max-w-md">
        <flux:modal.header>{{ __('New Access Group') }}</flux:modal.header>
        <flux:modal.body class="flex flex-col gap-4">
            <flux:input wire:model="newName" :label="__('Name')" placeholder="e.g. administration_users" required />
            <flux:textarea wire:model="newDescription" :label="__('Description')" rows="2" />
        </flux:modal.body>
        <flux:modal.footer class="flex justify-end gap-3">
            <flux:button wire:click="$set('showCreateModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button wire:click="createGroup" variant="primary">{{ __('Create') }}</flux:button>
        </flux:modal.footer>
    </flux:modal>
</x-layouts::app>
