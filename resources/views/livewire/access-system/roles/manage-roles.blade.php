<div>
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ __('Roles') }}</h1>
            <button class="btn btn-primary" wire:click="$set('showCreateModal', true)">
                <i class="bi bi-plus"></i> {{ __('New Role') }}
            </button>
        </div>

        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none"></i>
            <input type="text" wire:model.live.debounce.300ms="search"
                   class="input input-bordered w-full pl-9"
                   placeholder="{{ __('Search roles…') }}" />
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 text-left dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Description') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Approver') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @forelse($this->roles as $role)
                    <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800">
                        @if($editingRoleId === $role->id)
                        <td class="px-4 py-2">
                            <input type="text" wire:model="editFields.name" class="input input-bordered input-sm w-full" />
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" wire:model="editFields.description" class="input input-bordered input-sm w-full" />
                        </td>
                        <td class="px-4 py-2">
                            <input type="checkbox" wire:model="editFields.can_be_approver" class="checkbox checkbox-sm" />
                        </td>
                        <td class="px-4 py-2">
                            <label class="label cursor-pointer gap-2 justify-start">
                                <input type="checkbox" wire:model="editFields.is_active" class="checkbox checkbox-sm" />
                                <span class="label-text">{{ __('Active') }}</span>
                            </label>
                        </td>
                        <td class="px-4 py-2 text-end">
                            <div class="flex justify-end gap-2">
                                <button class="btn btn-primary btn-sm" wire:click="saveEdit">{{ __('Save') }}</button>
                                <button class="btn btn-ghost btn-sm" wire:click="cancelEdit">{{ __('Cancel') }}</button>
                            </div>
                        </td>
                        @else
                        <td class="px-4 py-3 font-medium">{{ $role->name }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $role->description }}</td>
                        <td class="px-4 py-3">
                            @if($role->can_be_approver)
                                <i class="bi bi-check-circle-fill text-green-500"></i>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $role->is_active ? 'badge-success' : 'badge-error' }}">
                                {{ $role->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="dropdown dropdown-bottom dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                                    <i class="bi bi-three-dots"></i>
                                </div>
                                <ul tabindex="0" class="dropdown-content menu bg-white dark:bg-zinc-800 rounded-box z-10 w-40 p-2 shadow-lg border border-zinc-200 dark:border-zinc-700">
                                    <li>
                                        <button wire:click="startEdit({{ $role->id }})">
                                            <i class="bi bi-pencil"></i> {{ __('Edit') }}
                                        </button>
                                    </li>
                                    @if(! $role->is_system_role)
                                    <li><hr class="my-1 border-zinc-200 dark:border-zinc-700"></li>
                                    <li>
                                        <button class="text-error"
                                            wire:click="deleteRole({{ $role->id }})"
                                            wire:confirm="{{ __('Delete this role?') }}">
                                            <i class="bi bi-trash"></i> {{ __('Delete') }}
                                        </button>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-zinc-400">{{ __('No roles found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="border-t border-zinc-100 px-4 py-3 dark:border-zinc-700">{{ $this->roles->links() }}</div>
        </div>
    </div>

    <dialog x-data x-init="$watch('$wire.showCreateModal', v => { if(v) $el.showModal(); else $el.close(); })" @close="$wire.showCreateModal = false" class="modal">
        <div class="modal-box w-full max-w-md">
            <h3 class="font-bold text-lg mb-4">{{ __('New Role') }}</h3>
            <div class="flex flex-col gap-4">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Name') }}</span></label>
                    <input type="text" wire:model="newName" class="input input-bordered w-full" required />
                    @error('newName') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Description') }}</span></label>
                    <input type="text" wire:model="newDescription" class="input input-bordered w-full" />
                </div>
                <label class="label cursor-pointer gap-2 justify-start">
                    <input type="checkbox" wire:model="newCanBeApprover" class="checkbox checkbox-sm" />
                    <span class="label-text">{{ __('Can be approver') }}</span>
                </label>
                <label class="label cursor-pointer gap-2 justify-start">
                    <input type="checkbox" wire:model="newIsSystemRole" class="checkbox checkbox-sm" />
                    <span class="label-text">{{ __('System role') }}</span>
                </label>
            </div>
            <div class="modal-action">
                <button class="btn btn-ghost" wire:click="$set('showCreateModal', false)">{{ __('Cancel') }}</button>
                <button class="btn btn-primary" wire:click="createRole">{{ __('Create') }}</button>
            </div>
        </div>
    </dialog>
</div>
