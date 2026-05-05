<x-layouts::app :title="$company->name">
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center gap-3">
            <flux:button icon="arrow-left" variant="ghost" size="sm" :href="route('admin.companies')" wire:navigate />
            <flux:heading size="xl">{{ $company->name }}</flux:heading>
            <flux:badge :variant="$company->is_active ? 'success' : 'danger'" size="sm">
                {{ $company->is_active ? __('Active') : __('Inactive') }}
            </flux:badge>
        </div>

        <flux:tab.group>
            <flux:tabs>
                <flux:tab name="details" wire:click="$set('activeTab', 'details')">{{ __('Details') }}</flux:tab>
                <flux:tab name="users" wire:click="$set('activeTab', 'users')">{{ __('Users') }} ({{ $this->companyUsers->count() }})</flux:tab>
                <flux:tab name="departments" wire:click="$set('activeTab', 'departments')">{{ __('Departments') }} ({{ $this->departments->count() }})</flux:tab>
            </flux:tabs>

            <flux:tab.panel name="details">
                <div class="mt-4 grid max-w-2xl grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <flux:input wire:model.live.debounce.600ms="name" :label="__('Company name')" required />
                    </div>
                    <flux:input wire:model.live.debounce.600ms="customerNumber" :label="__('Customer number')" />
                    <flux:input wire:model.live.debounce.600ms="email" type="email" :label="__('Email')" />
                    <flux:input wire:model.live.debounce.600ms="phoneCode" :label="__('Phone code')" />
                    <flux:input wire:model.live.debounce.600ms="phone" :label="__('Phone')" />
                    <div class="col-span-2"><flux:separator /></div>
                    <flux:heading size="sm" class="col-span-2">{{ __('Billing address') }}</flux:heading>
                    <div class="col-span-2">
                        <flux:input wire:model.live.debounce.600ms="billingAddressLine1" :label="__('Address line 1')" />
                    </div>
                    <div class="col-span-2">
                        <flux:input wire:model.live.debounce.600ms="billingAddressLine2" :label="__('Address line 2')" />
                    </div>
                    <flux:input wire:model.live.debounce.600ms="billingPostalCode" :label="__('Postal code')" />
                    <flux:input wire:model.live.debounce.600ms="billingCity" :label="__('City')" />
                    <flux:input wire:model.live.debounce.600ms="billingCountry" :label="__('Country')" />
                    <div class="col-span-2">
                        <flux:checkbox wire:model.live="isActive" :label="__('Active')" />
                    </div>
                </div>
            </flux:tab.panel>

            <flux:tab.panel name="users">
                <div class="mt-4 flex flex-col gap-4">
                    <div class="flex justify-end">
                        <flux:button variant="primary" icon="plus" wire:click="$set('showAddUserModal', true)">{{ __('Add user') }}</flux:button>
                    </div>
                    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 text-left dark:bg-zinc-800">
                                <tr>
                                    <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                                    <th class="px-4 py-3 font-medium">{{ __('Email') }}</th>
                                    <th class="px-4 py-3 font-medium">{{ __('Role') }}</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                                @forelse($this->companyUsers as $user)
                                <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                    <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-zinc-500">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        <flux:select size="sm" wire:change="updateUserRole({{ $user->id }}, $event.target.value)">
                                            @foreach($this->roles as $role)
                                                <option value="{{ $role->id }}" {{ $user->pivot->role_id == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </flux:select>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <flux:button size="sm" variant="ghost" icon="x-mark"
                                            wire:click="removeUser({{ $user->id }})"
                                            wire:confirm="{{ __('Remove this user from the company?') }}" />
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-4 py-12 text-center text-zinc-400">{{ __('No users assigned.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </flux:tab.panel>

            <flux:tab.panel name="departments">
                <div class="mt-4 flex flex-col gap-4">
                    <div class="flex justify-end">
                        <flux:button variant="primary" icon="plus" wire:click="$set('showAddDeptModal', true)">{{ __('Add department') }}</flux:button>
                    </div>
                    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 text-left dark:bg-zinc-800">
                                <tr>
                                    <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                                    <th class="px-4 py-3 font-medium">{{ __('Email') }}</th>
                                    <th class="px-4 py-3 font-medium">{{ __('Purchasers') }}</th>
                                    <th class="px-4 py-3 font-medium">{{ __('Approvers') }}</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                                @forelse($this->departments as $dept)
                                <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                    <td class="px-4 py-3 font-medium">{{ $dept->name }}</td>
                                    <td class="px-4 py-3 text-zinc-500">{{ $dept->email }}</td>
                                    <td class="px-4 py-3 text-zinc-500">{{ $dept->purchasers->count() }}</td>
                                    <td class="px-4 py-3 text-zinc-500">{{ $dept->approvers->count() }}</td>
                                    <td class="px-4 py-3 text-end">
                                        <flux:button size="sm" variant="ghost" icon="trash"
                                            wire:click="deleteDepartment({{ $dept->id }})"
                                            wire:confirm="{{ __('Delete this department?') }}" />
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-4 py-12 text-center text-zinc-400">{{ __('No departments.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </flux:tab.panel>
        </flux:tab.group>
    </div>

    {{-- Add user modal --}}
    <flux:modal wire:model="showAddUserModal" class="w-full max-w-md">
        <flux:modal.header>{{ __('Add user to company') }}</flux:modal.header>
        <flux:modal.body class="flex flex-col gap-4">
            <flux:select wire:model="addUserId" :label="__('User')">
                <option value="">— {{ __('Select user') }} —</option>
                @foreach($this->availableUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </flux:select>
            <flux:select wire:model="addRoleId" :label="__('Role')">
                <option value="">— {{ __('Select role') }} —</option>
                @foreach($this->roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </flux:select>
        </flux:modal.body>
        <flux:modal.footer class="flex justify-end gap-3">
            <flux:button wire:click="$set('showAddUserModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button wire:click="addUser" variant="primary">{{ __('Add') }}</flux:button>
        </flux:modal.footer>
    </flux:modal>

    {{-- Add department modal --}}
    <flux:modal wire:model="showAddDeptModal" class="w-full max-w-sm">
        <flux:modal.header>{{ __('New Department') }}</flux:modal.header>
        <flux:modal.body>
            <flux:input wire:model="newDeptName" :label="__('Department name')" required />
        </flux:modal.body>
        <flux:modal.footer class="flex justify-end gap-3">
            <flux:button wire:click="$set('showAddDeptModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button wire:click="addDepartment" variant="primary">{{ __('Create') }}</flux:button>
        </flux:modal.footer>
    </flux:modal>
</x-layouts::app>
