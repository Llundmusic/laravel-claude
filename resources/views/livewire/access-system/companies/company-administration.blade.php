<div>
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.companies') }}" wire:navigate class="btn btn-ghost btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold">{{ $company->name }}</h1>
            <span class="badge {{ $company->is_active ? 'badge-success' : 'badge-error' }} badge-sm">
                {{ $company->is_active ? __('Active') : __('Inactive') }}
            </span>
        </div>

        <div x-data="{ tab: 'details' }">
            <div role="tablist" class="tabs tabs-border">
                <button role="tab" class="tab" :class="{ 'tab-active': tab === 'details' }" @click="tab = 'details'">{{ __('Details') }}</button>
                <button role="tab" class="tab" :class="{ 'tab-active': tab === 'users' }" @click="tab = 'users'">{{ __('Users') }} ({{ $this->companyUsers->count() }})</button>
                <button role="tab" class="tab" :class="{ 'tab-active': tab === 'departments' }" @click="tab = 'departments'">{{ __('Departments') }} ({{ $this->departments->count() }})</button>
            </div>

            {{-- Details tab --}}
            <div x-show="tab === 'details'" class="mt-4 grid max-w-2xl grid-cols-2 gap-4">
                <div class="col-span-2">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">{{ __('Company name') }}</span></label>
                        <input type="text" wire:model.live.debounce.600ms="name" class="input input-bordered w-full" required />
                    </div>
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Customer number') }}</span></label>
                    <input type="text" wire:model.live.debounce.600ms="customerNumber" class="input input-bordered w-full" />
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Email') }}</span></label>
                    <input type="email" wire:model.live.debounce.600ms="email" class="input input-bordered w-full" />
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Phone code') }}</span></label>
                    <input type="text" wire:model.live.debounce.600ms="phoneCode" class="input input-bordered w-full" />
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Phone') }}</span></label>
                    <input type="text" wire:model.live.debounce.600ms="phone" class="input input-bordered w-full" />
                </div>
                <div class="col-span-2"><div class="divider my-0"></div></div>
                <h2 class="col-span-2 text-sm font-semibold">{{ __('Billing address') }}</h2>
                <div class="col-span-2">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">{{ __('Address line 1') }}</span></label>
                        <input type="text" wire:model.live.debounce.600ms="billingAddressLine1" class="input input-bordered w-full" />
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">{{ __('Address line 2') }}</span></label>
                        <input type="text" wire:model.live.debounce.600ms="billingAddressLine2" class="input input-bordered w-full" />
                    </div>
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Postal code') }}</span></label>
                    <input type="text" wire:model.live.debounce.600ms="billingPostalCode" class="input input-bordered w-full" />
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('City') }}</span></label>
                    <input type="text" wire:model.live.debounce.600ms="billingCity" class="input input-bordered w-full" />
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Country') }}</span></label>
                    <input type="text" wire:model.live.debounce.600ms="billingCountry" class="input input-bordered w-full" />
                </div>
                <div class="col-span-2">
                    <label class="label cursor-pointer gap-2 justify-start">
                        <input type="checkbox" wire:model.live="isActive" class="checkbox checkbox-sm" />
                        <span class="label-text">{{ __('Active') }}</span>
                    </label>
                </div>
            </div>

            {{-- Users tab --}}
            <div x-show="tab === 'users'" class="mt-4 flex flex-col gap-4">
                <div class="flex justify-end">
                    <button class="btn btn-primary btn-sm" wire:click="$set('showAddUserModal', true)">
                        <i class="bi bi-plus"></i> {{ __('Add user') }}
                    </button>
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
                                    <select class="select select-bordered select-sm" wire:change="updateUserRole({{ $user->id }}, $event.target.value)">
                                        @foreach($this->roles as $role)
                                            <option value="{{ $role->id }}" {{ $user->pivot->role_id == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <button class="btn btn-ghost btn-sm text-error"
                                        wire:click="removeUser({{ $user->id }})"
                                        wire:confirm="{{ __('Remove this user from the company?') }}">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-12 text-center text-zinc-400">{{ __('No users assigned.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Departments tab --}}
            <div x-show="tab === 'departments'" class="mt-4 flex flex-col gap-4">
                <div class="flex justify-end">
                    <button class="btn btn-primary btn-sm" wire:click="$set('showAddDeptModal', true)">
                        <i class="bi bi-plus"></i> {{ __('Add department') }}
                    </button>
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
                                    <button class="btn btn-ghost btn-sm text-error"
                                        wire:click="deleteDepartment({{ $dept->id }})"
                                        wire:confirm="{{ __('Delete this department?') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-12 text-center text-zinc-400">{{ __('No departments.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add user modal --}}
    <dialog x-data x-init="$watch('$wire.showAddUserModal', v => { if(v) $el.showModal(); else $el.close(); })" @close="$wire.showAddUserModal = false" class="modal">
        <div class="modal-box w-full max-w-md">
            <h3 class="font-bold text-lg mb-4">{{ __('Add user to company') }}</h3>
            <div class="flex flex-col gap-4">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('User') }}</span></label>
                    <select wire:model="addUserId" class="select select-bordered w-full">
                        <option value="">— {{ __('Select user') }} —</option>
                        @foreach($this->availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Role') }}</span></label>
                    <select wire:model="addRoleId" class="select select-bordered w-full">
                        <option value="">— {{ __('Select role') }} —</option>
                        @foreach($this->roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-action">
                <button class="btn btn-ghost" wire:click="$set('showAddUserModal', false)">{{ __('Cancel') }}</button>
                <button class="btn btn-primary" wire:click="addUser">{{ __('Add') }}</button>
            </div>
        </div>
    </dialog>

    {{-- Add department modal --}}
    <dialog x-data x-init="$watch('$wire.showAddDeptModal', v => { if(v) $el.showModal(); else $el.close(); })" @close="$wire.showAddDeptModal = false" class="modal">
        <div class="modal-box w-full max-w-sm">
            <h3 class="font-bold text-lg mb-4">{{ __('New Department') }}</h3>
            <div class="form-control w-full">
                <label class="label"><span class="label-text">{{ __('Department name') }}</span></label>
                <input type="text" wire:model="newDeptName" class="input input-bordered w-full" required />
            </div>
            <div class="modal-action">
                <button class="btn btn-ghost" wire:click="$set('showAddDeptModal', false)">{{ __('Cancel') }}</button>
                <button class="btn btn-primary" wire:click="addDepartment">{{ __('Create') }}</button>
            </div>
        </div>
    </dialog>
</div>
