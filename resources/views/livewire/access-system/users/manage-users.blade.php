<x-layouts::app :title="__('Manage Users')">
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('Users') }}</flux:heading>
            <flux:button variant="primary" icon="plus" wire:click="openCreate">
                {{ __('New User') }}
            </flux:button>
        </div>

        {{-- Search --}}
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search users…')" clearable />

        <div class="flex flex-1 gap-6">
            {{-- Table --}}
            <div class="flex-1 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 text-left dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 font-medium">
                                <button wire:click="sort('name')" class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                                    {{ __('Name') }}
                                    @if($sortField === 'name')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </button>
                            </th>
                            <th class="px-4 py-3 font-medium">
                                <button wire:click="sort('email')" class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                                    {{ __('Email') }}
                                    @if($sortField === 'email')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </button>
                            </th>
                            <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Language') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                        @forelse($this->users as $user)
                        <tr
                            class="cursor-pointer transition hover:bg-zinc-50 dark:hover:bg-zinc-800 {{ $editingUserId === $user->id ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}"
                            wire:click="editUser({{ $user->id }})"
                        >
                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <flux:badge :variant="$user->is_active ? 'success' : 'danger'">
                                    {{ $user->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                            </td>
                            <td class="px-4 py-3 uppercase text-zinc-500">{{ $user->language }}</td>
                            <td class="px-4 py-3 text-end" wire:click.stop>
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-horizontal" variant="ghost" size="sm" />
                                    <flux:menu>
                                        <flux:menu.item icon="envelope" wire:click="sendActivationEmail({{ $user->id }})">
                                            {{ __('Send activation email') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger"
                                            wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="{{ __('Delete this user?') }}">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-zinc-400">{{ __('No users found.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="border-t border-zinc-100 px-4 py-3 dark:border-zinc-700">
                    {{ $this->users->links() }}
                </div>
            </div>

            {{-- Edit Panel --}}
            @if($showEditPanel)
            <div class="w-96 shrink-0 rounded-xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading>{{ __('Edit User') }}</flux:heading>
                    <flux:button icon="x-mark" variant="ghost" size="sm" wire:click="$set('showEditPanel', false)" />
                </div>
                <div class="flex flex-col gap-4">
                    <flux:input wire:model.live.debounce.600ms="editName" :label="__('Name')" />
                    <flux:input wire:model.live.debounce.600ms="editEmail" type="email" :label="__('Email')" />
                    <div class="flex gap-2">
                        <flux:input wire:model.live.debounce.600ms="editPhoneCode" :label="__('Code')" class="w-20" />
                        <flux:input wire:model.live.debounce.600ms="editPhone" :label="__('Phone')" class="flex-1" />
                    </div>
                    <flux:input wire:model.live.debounce.600ms="editBillingReference" :label="__('Billing reference')" />
                    <flux:select wire:model.live="editLanguage" :label="__('Language')">
                        <option value="en">English</option>
                        <option value="no">Norsk</option>
                        <option value="da">Dansk</option>
                    </flux:select>
                    <flux:checkbox wire:model.live="editIsActive" :label="__('Active')" />
                    <flux:checkbox wire:model.live="editAccessAllCompanies" :label="__('Access all companies')" />
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Create modal --}}
    <flux:modal wire:model="showCreateModal" class="w-full max-w-lg">
        <flux:modal.header>{{ __('New User') }}</flux:modal.header>
        <flux:modal.body class="flex flex-col gap-4">
            <flux:input wire:model="newName" :label="__('Name')" required />
            <flux:input wire:model="newEmail" type="email" :label="__('Email')" required />
            <flux:input wire:model="newPassword" type="password" :label="__('Password')" required />
            <flux:select wire:model="newLanguage" :label="__('Language')">
                <option value="en">English</option>
                <option value="no">Norsk</option>
                <option value="da">Dansk</option>
            </flux:select>
            <flux:select wire:model="newCompanyId" :label="__('Company (optional)')">
                <option value="">— {{ __('None') }} —</option>
                @foreach($this->companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </flux:select>
            @if($newCompanyId)
            <flux:select wire:model="newRoleId" :label="__('Role')">
                <option value="">— {{ __('Select role') }} —</option>
                @foreach($this->roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </flux:select>
            @endif
        </flux:modal.body>
        <flux:modal.footer class="flex justify-end gap-3">
            <flux:button wire:click="$set('showCreateModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button wire:click="createUser" variant="primary">{{ __('Create') }}</flux:button>
        </flux:modal.footer>
    </flux:modal>
</x-layouts::app>
