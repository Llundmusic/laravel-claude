<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full flex-1 flex-col items-center justify-center gap-6 p-6">
        <div class="text-center">
            <flux:heading size="xl">{{ __('Welcome') }}{{ auth()->check() ? ', ' . auth()->user()->name : '' }}</flux:heading>
            @auth
                @if(auth()->user()->activeCompany)
                <flux:text class="mt-2 text-zinc-500">{{ auth()->user()->activeCompany->name }}</flux:text>
                @endif
            @endauth
        </div>

        @auth
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_users'))
            <a href="{{ route('admin.users') }}" wire:navigate
               class="flex flex-col items-center gap-2 rounded-xl border border-zinc-200 bg-white p-6 text-center transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600">
                <i class="bi bi-people text-2xl text-zinc-600 dark:text-zinc-400"></i>
                <span class="text-sm font-medium">{{ __('Users') }}</span>
            </a>
            @endif

            @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_companies'))
            <a href="{{ route('admin.companies') }}" wire:navigate
               class="flex flex-col items-center gap-2 rounded-xl border border-zinc-200 bg-white p-6 text-center transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600">
                <i class="bi bi-building text-2xl text-zinc-600 dark:text-zinc-400"></i>
                <span class="text-sm font-medium">{{ __('Companies') }}</span>
            </a>
            @endif

            @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_roles'))
            <a href="{{ route('admin.roles') }}" wire:navigate
               class="flex flex-col items-center gap-2 rounded-xl border border-zinc-200 bg-white p-6 text-center transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600">
                <i class="bi bi-shield-check text-2xl text-zinc-600 dark:text-zinc-400"></i>
                <span class="text-sm font-medium">{{ __('Roles') }}</span>
            </a>
            @endif

            @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_access_groups'))
            <a href="{{ route('admin.access-groups') }}" wire:navigate
               class="flex flex-col items-center gap-2 rounded-xl border border-zinc-200 bg-white p-6 text-center transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600">
                <i class="bi bi-key text-2xl text-zinc-600 dark:text-zinc-400"></i>
                <span class="text-sm font-medium">{{ __('Access Groups') }}</span>
            </a>
            @endif
        </div>
        @endauth
    </div>
</x-layouts::app>
