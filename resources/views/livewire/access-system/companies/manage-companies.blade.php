<x-layouts::app :title="__('Companies')">
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('Companies') }}</flux:heading>
            <flux:button variant="primary" icon="plus" wire:click="$set('showCreateModal', true)">{{ __('New Company') }}</flux:button>
        </div>

        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search companies…')" clearable />

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
                            <flux:badge :variant="$company->is_active ? 'success' : 'danger'">
                                {{ $company->is_active ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <flux:dropdown>
                                <flux:button icon="ellipsis-horizontal" variant="ghost" size="sm" />
                                <flux:menu>
                                    <flux:menu.item icon="building-office" :href="route('company.administration', ['company' => $company->id])" wire:navigate>
                                        {{ __('Manage') }}
                                    </flux:menu.item>
                                    <flux:menu.item icon="{{ $company->is_active ? 'eye-slash' : 'eye' }}" wire:click="toggleActive({{ $company->id }})">
                                        {{ $company->is_active ? __('Deactivate') : __('Activate') }}
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="deleteCompany({{ $company->id }})"
                                        wire:confirm="{{ __('Delete this company?') }}">
                                        {{ __('Delete') }}
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
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

    <flux:modal wire:model="showCreateModal" class="w-full max-w-md">
        <flux:modal.header>{{ __('New Company') }}</flux:modal.header>
        <flux:modal.body class="flex flex-col gap-4">
            <flux:input wire:model="newName" :label="__('Name')" required />
            <flux:input wire:model="newCustomerNumber" :label="__('Customer number')" />
            <flux:input wire:model="newEmail" type="email" :label="__('Email')" />
        </flux:modal.body>
        <flux:modal.footer class="flex justify-end gap-3">
            <flux:button wire:click="$set('showCreateModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button wire:click="createCompany" variant="primary">{{ __('Create') }}</flux:button>
        </flux:modal.footer>
    </flux:modal>
</x-layouts::app>
