<div>
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ __('Users') }}</h1>
            <button class="btn btn-primary" wire:click="openCreate">
                <i class="bi bi-plus"></i> {{ __('New User') }}
            </button>
        </div>

        {{-- Search --}}
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none"></i>
            <input type="text" wire:model.live.debounce.300ms="search"
                   class="input input-bordered w-full pl-9"
                   placeholder="{{ __('Search users…') }}" />
        </div>

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
                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }}">
                                    {{ $user->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 uppercase text-zinc-500">{{ $user->language }}</td>
                            <td class="px-4 py-3 text-end" wire:click.stop>
                                <div class="dropdown dropdown-bottom dropdown-end">
                                    <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                                        <i class="bi bi-three-dots"></i>
                                    </div>
                                    <ul tabindex="0" class="dropdown-content menu bg-white dark:bg-zinc-800 rounded-box z-10 w-48 p-2 shadow-lg border border-zinc-200 dark:border-zinc-700">
                                        <li>
                                            <button wire:click="sendActivationEmail({{ $user->id }})">
                                                <i class="bi bi-envelope"></i> {{ __('Send activation email') }}
                                            </button>
                                        </li>
                                        <li><hr class="my-1 border-zinc-200 dark:border-zinc-700"></li>
                                        <li>
                                            <button class="text-error"
                                                wire:click="deleteUser({{ $user->id }})"
                                                wire:confirm="{{ __('Delete this user?') }}">
                                                <i class="bi bi-trash"></i> {{ __('Delete') }}
                                            </button>
                                        </li>
                                    </ul>
                                </div>
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
                    <h2 class="text-lg font-semibold">{{ __('Edit User') }}</h2>
                    <button class="btn btn-ghost btn-sm" wire:click="$set('showEditPanel', false)">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">{{ __('Name') }}</span></label>
                        <input type="text" wire:model.live.debounce.600ms="editName" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">{{ __('Email') }}</span></label>
                        <input type="email" wire:model.live.debounce.600ms="editEmail" class="input input-bordered w-full" />
                    </div>
                    <div class="flex gap-2">
                        <div class="form-control w-20">
                            <label class="label"><span class="label-text">{{ __('Code') }}</span></label>
                            <input type="text" wire:model.live.debounce.600ms="editPhoneCode" class="input input-bordered w-full" />
                        </div>
                        <div class="form-control flex-1">
                            <label class="label"><span class="label-text">{{ __('Phone') }}</span></label>
                            <input type="text" wire:model.live.debounce.600ms="editPhone" class="input input-bordered w-full" />
                        </div>
                    </div>
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">{{ __('Billing reference') }}</span></label>
                        <input type="text" wire:model.live.debounce.600ms="editBillingReference" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">{{ __('Language') }}</span></label>
                        <select wire:model.live="editLanguage" class="select select-bordered w-full">
                            <option value="en">English</option>
                            <option value="no">Norsk</option>
                            <option value="da">Dansk</option>
                        </select>
                    </div>
                    <label class="label cursor-pointer gap-2 justify-start">
                        <input type="checkbox" wire:model.live="editIsActive" class="checkbox checkbox-sm" />
                        <span class="label-text">{{ __('Active') }}</span>
                    </label>
                    <label class="label cursor-pointer gap-2 justify-start">
                        <input type="checkbox" wire:model.live="editAccessAllCompanies" class="checkbox checkbox-sm" />
                        <span class="label-text">{{ __('Access all companies') }}</span>
                    </label>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Create modal --}}
    <dialog x-data x-init="$watch('$wire.showCreateModal', v => { if(v) $el.showModal(); else $el.close(); })" @close="$wire.showCreateModal = false" class="modal">
        <div class="modal-box w-full max-w-lg">
            <h3 class="font-bold text-lg mb-4">{{ __('New User') }}</h3>
            <div class="flex flex-col gap-4">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Name') }}</span></label>
                    <input type="text" wire:model="newName" class="input input-bordered w-full" required />
                    @error('newName') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Email') }}</span></label>
                    <input type="email" wire:model="newEmail" class="input input-bordered w-full" required />
                    @error('newEmail') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control w-full" x-data="{ show: false }">
                    <label class="label"><span class="label-text">{{ __('Password') }}</span></label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" wire:model="newPassword"
                               class="input input-bordered w-full pr-10" required />
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400">
                            <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                    @error('newPassword') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Language') }}</span></label>
                    <select wire:model="newLanguage" class="select select-bordered w-full">
                        <option value="en">English</option>
                        <option value="no">Norsk</option>
                        <option value="da">Dansk</option>
                    </select>
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Company (optional)') }}</span></label>
                    <select wire:model="newCompanyId" class="select select-bordered w-full">
                        <option value="">— {{ __('None') }} —</option>
                        @foreach($this->companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if($newCompanyId)
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Role') }}</span></label>
                    <select wire:model="newRoleId" class="select select-bordered w-full">
                        <option value="">— {{ __('Select role') }} —</option>
                        @foreach($this->roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            <div class="modal-action">
                <button class="btn btn-ghost" wire:click="$set('showCreateModal', false)">{{ __('Cancel') }}</button>
                <button class="btn btn-primary" wire:click="createUser">{{ __('Create') }}</button>
            </div>
        </div>
    </dialog>
</div>
