<x-layouts::app :title="__('Manage Roles')">
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('Roles') }}</flux:heading>
            <flux:button variant="primary" icon="plus" wire:click="$set('showCreateModal', true)">{{ __('New Role') }}</flux:button>
        </div>

        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search roles…')" clearable />

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
                            <flux:input wire:model="editFields.name" size="sm" />
                        </td>
                        <td class="px-4 py-2">
                            <flux:input wire:model="editFields.description" size="sm" />
                        </td>
                        <td class="px-4 py-2">
                            <flux:checkbox wire:model="editFields.can_be_approver" />
                        </td>
                        <td class="px-4 py-2">
                            <flux:checkbox wire:model="editFields.is_active" :label="__('Active')" />
                        </td>
                        <td class="px-4 py-2 text-end">
                            <div class="flex justify-end gap-2">
                                <flux:button size="sm" variant="primary" wire:click="saveEdit">{{ __('Save') }}</flux:button>
                                <flux:button size="sm" variant="ghost" wire:click="cancelEdit">{{ __('Cancel') }}</flux:button>
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
                            <flux:badge :variant="$role->is_active ? 'success' : 'danger'">
                                {{ $role->is_active ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <flux:dropdown>
                                <flux:button icon="ellipsis-horizontal" variant="ghost" size="sm" />
                                <flux:menu>
                                    <flux:menu.item icon="pencil" wire:click="startEdit({{ $role->id }})">{{ __('Edit') }}</flux:menu.item>
                                    @if(! $role->is_system_role)
                                    <flux:menu.separator />
                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="deleteRole({{ $role->id }})"
                                        wire:confirm="{{ __('Delete this role?') }}">
                                        {{ __('Delete') }}
                                    </flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
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

    <flux:modal wire:model="showCreateModal" class="w-full max-w-md">
        <flux:modal.header>{{ __('New Role') }}</flux:modal.header>
        <flux:modal.body class="flex flex-col gap-4">
            <flux:input wire:model="newName" :label="__('Name')" required />
            <flux:input wire:model="newDescription" :label="__('Description')" />
            <flux:checkbox wire:model="newCanBeApprover" :label="__('Can be approver')" />
            <flux:checkbox wire:model="newIsSystemRole" :label="__('System role')" />
        </flux:modal.body>
        <flux:modal.footer class="flex justify-end gap-3">
            <flux:button wire:click="$set('showCreateModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button wire:click="createRole" variant="primary">{{ __('Create') }}</flux:button>
        </flux:modal.footer>
    </flux:modal>
</x-layouts::app>
