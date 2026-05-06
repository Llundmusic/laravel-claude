<div>
    <div class="flex h-full flex-1 gap-0 overflow-hidden">
        {{-- Group list --}}
        <div class="flex w-80 shrink-0 flex-col border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between p-4">
                <h2 class="font-semibold">{{ __('Access Groups') }}</h2>
                <button class="btn btn-primary btn-sm" wire:click="$set('showCreateModal', true)">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
            <div class="px-4 pb-3">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           class="input input-bordered input-sm w-full pl-9"
                           placeholder="{{ __('Search…') }}" />
                </div>
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
                    <span class="badge badge-sm {{ $group->is_active ? 'badge-success' : 'badge-ghost' }}">
                        {{ $group->is_active ? __('On') : __('Off') }}
                    </span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Detail panel --}}
        <div class="flex flex-1 flex-col overflow-y-auto p-6">
            @if($this->selectedGroup)
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">{{ $this->selectedGroup->name }}</h1>
                <button
                    class="btn btn-sm {{ $this->selectedGroup->is_active ? 'btn-error' : 'btn-primary' }}"
                    wire:click="toggleActive({{ $this->selectedGroup->id }})"
                >
                    {{ $this->selectedGroup->is_active ? __('Deactivate') : __('Activate') }}
                </button>
            </div>

            <div class="mb-6 max-w-xl">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Description') }}</span></label>
                    <textarea wire:model.live.debounce.600ms="editDescription" class="textarea textarea-bordered w-full" rows="2"></textarea>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                {{-- Roles --}}
                <div>
                    <h3 class="text-sm font-semibold mb-3">{{ __('Roles') }}</h3>
                    <div class="flex flex-col gap-2 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        @foreach($this->allRoles as $role)
                        @php $hasRole = $this->selectedGroup->roles->contains('id', $role->id) @endphp
                        <label class="flex cursor-pointer items-center justify-between gap-3">
                            <span class="text-sm">{{ $role->name }}</span>
                            <input type="checkbox"
                                class="checkbox checkbox-sm"
                                wire:click="toggleRole({{ $role->id }})"
                                {{ $hasRole ? 'checked' : '' }}
                            />
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Companies --}}
                <div>
                    <h3 class="text-sm font-semibold mb-3">{{ __('Companies') }}</h3>
                    <div class="flex flex-col gap-2 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        @foreach($this->allCompanies as $company)
                        @php $hasCompany = $this->selectedGroup->companies->contains('id', $company->id) @endphp
                        <label class="flex cursor-pointer items-center justify-between gap-3">
                            <span class="text-sm">{{ $company->name }}</span>
                            <input type="checkbox"
                                class="checkbox checkbox-sm"
                                wire:click="toggleCompany({{ $company->id }})"
                                {{ $hasCompany ? 'checked' : '' }}
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

    <dialog x-data x-init="$watch('$wire.showCreateModal', v => { if(v) $el.showModal(); else $el.close(); })" @close="$wire.showCreateModal = false" class="modal">
        <div class="modal-box w-full max-w-md">
            <h3 class="font-bold text-lg mb-4">{{ __('New Access Group') }}</h3>
            <div class="flex flex-col gap-4">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Name') }}</span></label>
                    <input type="text" wire:model="newName" class="input input-bordered w-full"
                           placeholder="e.g. administration_users" required />
                    @error('newName') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Description') }}</span></label>
                    <textarea wire:model="newDescription" class="textarea textarea-bordered w-full" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-action">
                <button class="btn btn-ghost" wire:click="$set('showCreateModal', false)">{{ __('Cancel') }}</button>
                <button class="btn btn-primary" wire:click="createGroup">{{ __('Create') }}</button>
            </div>
        </div>
    </dialog>
</div>
