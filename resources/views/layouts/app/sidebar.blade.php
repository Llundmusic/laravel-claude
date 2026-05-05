<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: {{ auth()->user()?->darkmode ? 'true' : 'false' }} }"
    x-init="$watch('darkMode', v => document.documentElement.classList.toggle('dark', v))"
    :class="{ dark: darkMode }"
    class="{{ auth()->user()?->darkmode ? 'dark' : '' }}"
>
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('home') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Navigation')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @auth
                    <flux:sidebar.group :heading="__('Settings')" class="grid">
                        @can('view-company-admin')
                        <flux:sidebar.item icon="building-office" :href="route('company.administration')" :current="request()->routeIs('company.administration')" wire:navigate>
                            {{ __('Company') }}
                        </flux:sidebar.item>
                        @endcan

                        <flux:sidebar.item icon="funnel" :href="route('filters')" :current="request()->routeIs('filters')" wire:navigate>
                            {{ __('Saved Filters') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>

                    @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_users') ||
                        app(\App\Services\AccessLevelService::class)->hasAccess('administration_roles') ||
                        app(\App\Services\AccessLevelService::class)->hasAccess('administration_access_groups') ||
                        app(\App\Services\AccessLevelService::class)->hasAccess('administration_companies'))
                    <flux:sidebar.group :heading="__('Administration')" class="grid">
                        @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_users'))
                        <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                            {{ __('Users') }}
                        </flux:sidebar.item>
                        @endif

                        @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_companies'))
                        <flux:sidebar.item icon="building-storefront" :href="route('admin.companies')" :current="request()->routeIs('admin.companies')" wire:navigate>
                            {{ __('Companies') }}
                        </flux:sidebar.item>
                        @endif

                        @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_roles'))
                        <flux:sidebar.item icon="shield-check" :href="route('admin.roles')" :current="request()->routeIs('admin.roles')" wire:navigate>
                            {{ __('Roles') }}
                        </flux:sidebar.item>
                        @endif

                        @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_access_groups'))
                        <flux:sidebar.item icon="key" :href="route('admin.access-groups')" :current="request()->routeIs('admin.access-groups')" wire:navigate>
                            {{ __('Access Groups') }}
                        </flux:sidebar.item>
                        @endif

                        @if(app(\App\Services\AccessLevelService::class)->hasAccess('admin_activity_log'))
                        <flux:sidebar.item icon="clipboard-document-list" :href="route('admin.activitylog')" :current="request()->routeIs('admin.activitylog')" wire:navigate>
                            {{ __('Activity Log') }}
                        </flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                    @endif
                @endauth
            </flux:sidebar.nav>

            <flux:spacer />

            {{-- Company selector --}}
            @auth
                @if($userCompanies->count() > 1)
                <div class="px-3 pb-2">
                    <form method="POST" action="{{ route('user.set-active-company') }}" id="company-form">
                        @csrf
                        <flux:select
                            name="company_id"
                            size="sm"
                            onchange="document.getElementById('company-form').submit()"
                        >
                            @foreach($userCompanies as $company)
                                <option value="{{ $company->id }}" {{ $company->id === auth()->user()->active_company_id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </flux:select>
                    </form>
                </div>
                @elseif($userCompanies->count() === 1)
                <div class="px-4 pb-2">
                    <flux:text size="sm" class="truncate text-zinc-500">{{ $userCompanies->first()?->name }}</flux:text>
                </div>
                @endif

                {{-- Dark mode toggle --}}
                <div class="flex items-center justify-between px-4 pb-3">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Dark mode') }}</flux:text>
                    <flux:switch
                        x-model="darkMode"
                        @change="$wire.toggleDarkMode()"
                    />
                </div>
            @endauth

            @auth
                <x-desktop-user-menu class="hidden lg:block" />
            @endauth
        </flux:sidebar>

        <!-- Mobile header -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
            @auth
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>
                    <flux:menu.separator />
                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
            @endauth
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
