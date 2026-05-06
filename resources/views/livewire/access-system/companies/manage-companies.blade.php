<div>
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ __('Companies') }}</h1>
            <button class="btn btn-primary" wire:click="$set('showCreateModal', true)">
                <i class="bi bi-plus"></i> {{ __('New Company') }}
            </button>
        </div>

        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none"></i>
            <input type="text" wire:model.live.debounce.300ms="search"
                   class="input input-bordered w-full pl-9"
                   placeholder="{{ __('Search companies…') }}" />
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 text-left dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Customer #') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Email') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @forelse($this->companies as $company)
                    <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800">
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('company.administration', ['company' => $company->id]) }}" class="hover:underline" wire:navigate>{{ $company->name }}</a>
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $company->customer_number }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $company->email }}</td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $company->is_active ? 'badge-success' : 'badge-error' }}">
                                {{ $company->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="dropdown dropdown-bottom dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                                    <i class="bi bi-three-dots"></i>
                                </div>
                                <ul tabindex="0" class="dropdown-content menu bg-white dark:bg-zinc-800 rounded-box z-10 w-48 p-2 shadow-lg border border-zinc-200 dark:border-zinc-700">
                                    <li>
                                        <a href="{{ route('company.administration', ['company' => $company->id]) }}" wire:navigate>
                                            <i class="bi bi-building"></i> {{ __('Manage') }}
                                        </a>
                                    </li>
                                    <li>
                                        <button wire:click="toggleActive({{ $company->id }})">
                                            <i class="bi bi-{{ $company->is_active ? 'eye-slash' : 'eye' }}"></i>
                                            {{ $company->is_active ? __('Deactivate') : __('Activate') }}
                                        </button>
                                    </li>
                                    <li><hr class="my-1 border-zinc-200 dark:border-zinc-700"></li>
                                    <li>
                                        <button class="text-error"
                                            wire:click="deleteCompany({{ $company->id }})"
                                            wire:confirm="{{ __('Delete this company?') }}">
                                            <i class="bi bi-trash"></i> {{ __('Delete') }}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-zinc-400">{{ __('No companies found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="border-t border-zinc-100 px-4 py-3 dark:border-zinc-700">{{ $this->companies->links() }}</div>
        </div>
    </div>

    <dialog x-data x-init="$watch('$wire.showCreateModal', v => { if(v) $el.showModal(); else $el.close(); })" @close="$wire.showCreateModal = false" class="modal">
        <div class="modal-box w-full max-w-md">
            <h3 class="font-bold text-lg mb-4">{{ __('New Company') }}</h3>
            <div class="flex flex-col gap-4">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Name') }}</span></label>
                    <input type="text" wire:model="newName" class="input input-bordered w-full" required />
                    @error('newName') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Customer number') }}</span></label>
                    <input type="text" wire:model="newCustomerNumber" class="input input-bordered w-full" />
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Email') }}</span></label>
                    <input type="email" wire:model="newEmail" class="input input-bordered w-full" />
                </div>
            </div>
            <div class="modal-action">
                <button class="btn btn-ghost" wire:click="$set('showCreateModal', false)">{{ __('Cancel') }}</button>
                <button class="btn btn-primary" wire:click="createCompany">{{ __('Create') }}</button>
            </div>
        </div>
    </dialog>
</div>
