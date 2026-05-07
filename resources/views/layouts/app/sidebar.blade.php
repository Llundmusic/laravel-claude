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
        <div class="drawer lg:drawer-open">
            <input id="sidebar-drawer" type="checkbox" class="drawer-toggle" />

            {{-- Main content area --}}
            <div class="drawer-content flex flex-col min-h-screen">
                {{-- Mobile header --}}
                <header class="navbar lg:hidden border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 px-2 py-1 min-h-12">
                    <label for="sidebar-drawer" class="btn btn-ghost btn-sm drawer-button">
                        <i class="bi bi-list text-xl"></i>
                    </label>
                    <div class="flex-1"></div>
                    @auth
                    <div class="dropdown dropdown-bottom dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-sm gap-1">
                            <div class="avatar placeholder">
                                <div class="bg-zinc-700 text-white rounded-full w-7">
                                    <span class="text-xs">{{ auth()->user()->initials() }}</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-down text-xs"></i>
                        </div>
                        <ul tabindex="0" class="dropdown-content menu bg-white dark:bg-zinc-800 rounded-box z-10 w-52 p-2 shadow-lg border border-zinc-200 dark:border-zinc-700">
                            <li class="pointer-events-none px-1 py-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="avatar placeholder">
                                        <div class="bg-zinc-700 text-white rounded-full w-8">
                                            <span class="text-xs">{{ auth()->user()->initials() }}</span>
                                        </div>
                                    </div>
                                    <div class="text-sm leading-tight">
                                        <div class="font-semibold truncate">{{ auth()->user()->name }}</div>
                                        <div class="text-xs text-zinc-500 truncate">{{ auth()->user()->email }}</div>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="my-1 border-zinc-200 dark:border-zinc-700"></li>
                            <li>
                                <a href="{{ route('profile.edit') }}" wire:navigate>
                                    <i class="bi bi-gear"></i> {{ __('Settings') }}
                                </a>
                            </li>
                            <li><hr class="my-1 border-zinc-200 dark:border-zinc-700"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full text-left">
                                        <i class="bi bi-box-arrow-right"></i> {{ __('Log out') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endauth
                </header>

                {{-- Page slot --}}
                {{ $slot }}
            </div>

            {{-- Sidebar --}}
            <div class="drawer-side z-40">
                <label for="sidebar-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                <aside class="flex flex-col min-h-full w-72 bg-zinc-50 border-e border-zinc-200 dark:bg-zinc-900 dark:border-zinc-700">
                    {{-- Logo --}}
                    <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                        <x-app-logo :sidebar="true" href="{{ route('home') }}" wire:navigate />
                        <label for="sidebar-drawer" class="btn btn-ghost btn-sm lg:hidden">
                            <i class="bi bi-x-lg"></i>
                        </label>
                    </div>

                    {{-- Navigation --}}
                    <nav class="flex-1 overflow-y-auto py-4 px-3">
                        <div class="mb-4">
                            <div class="px-2 py-1 text-xs font-medium text-zinc-400 uppercase tracking-wider">{{ __('Navigation') }}</div>
                            <ul class="menu menu-sm p-0 gap-0.5">
                                <li>
                                    <a href="{{ route('home') }}" wire:navigate
                                       class="{{ request()->routeIs('home') ? 'active' : '' }}">
                                        <i class="bi bi-house"></i> {{ __('Dashboard') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('quote-calculations.index') }}" wire:navigate
                                       class="{{ request()->routeIs('quote-calculations.*') ? 'active' : '' }}">
                                        <i class="bi bi-calculator"></i> {{ __('Quote Calculations') }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        @auth
                        <div class="mb-4">
                            <div class="px-2 py-1 text-xs font-medium text-zinc-400 uppercase tracking-wider">{{ __('Settings') }}</div>
                            <ul class="menu menu-sm p-0 gap-0.5">
                                @if(app(\App\Services\AccessLevelService::class)->hasAccess('settings_company'))
                                <li>
                                    <a href="{{ route('company.administration') }}" wire:navigate
                                       class="{{ request()->routeIs('company.administration') ? 'active' : '' }}">
                                        <i class="bi bi-building"></i> {{ __('Company') }}
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a href="{{ route('filters') }}" wire:navigate
                                       class="{{ request()->routeIs('filters') ? 'active' : '' }}">
                                        <i class="bi bi-funnel"></i> {{ __('Saved Filters') }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_users') ||
                            app(\App\Services\AccessLevelService::class)->hasAccess('administration_roles') ||
                            app(\App\Services\AccessLevelService::class)->hasAccess('administration_access_groups') ||
                            app(\App\Services\AccessLevelService::class)->hasAccess('administration_companies'))
                        <div class="mb-4">
                            <div class="px-2 py-1 text-xs font-medium text-zinc-400 uppercase tracking-wider">{{ __('Administration') }}</div>
                            <ul class="menu menu-sm p-0 gap-0.5">
                                @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_users'))
                                <li>
                                    <a href="{{ route('admin.users') }}" wire:navigate
                                       class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                                        <i class="bi bi-people"></i> {{ __('Users') }}
                                    </a>
                                </li>
                                @endif

                                @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_companies'))
                                <li>
                                    <a href="{{ route('admin.companies') }}" wire:navigate
                                       class="{{ request()->routeIs('admin.companies') ? 'active' : '' }}">
                                        <i class="bi bi-shop"></i> {{ __('Companies') }}
                                    </a>
                                </li>
                                @endif

                                @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_roles'))
                                <li>
                                    <a href="{{ route('admin.roles') }}" wire:navigate
                                       class="{{ request()->routeIs('admin.roles') ? 'active' : '' }}">
                                        <i class="bi bi-shield-check"></i> {{ __('Roles') }}
                                    </a>
                                </li>
                                @endif

                                @if(app(\App\Services\AccessLevelService::class)->hasAccess('administration_access_groups'))
                                <li>
                                    <a href="{{ route('admin.access-groups') }}" wire:navigate
                                       class="{{ request()->routeIs('admin.access-groups') ? 'active' : '' }}">
                                        <i class="bi bi-key"></i> {{ __('Access Groups') }}
                                    </a>
                                </li>
                                @endif

                                @if(app(\App\Services\AccessLevelService::class)->hasAccess('admin_activity_log'))
                                <li>
                                    <a href="{{ route('admin.activitylog') }}" wire:navigate
                                       class="{{ request()->routeIs('admin.activitylog') ? 'active' : '' }}">
                                        <i class="bi bi-clipboard-data"></i> {{ __('Activity Log') }}
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                        @endif
                        @endauth
                    </nav>

                    {{-- Bottom section --}}
                    @auth
                    <div class="border-t border-zinc-200 dark:border-zinc-700 p-3 space-y-2">
                        {{-- Company selector --}}
                        @if($userCompanies->count() > 1)
                        <form method="POST" action="{{ route('user.set-active-company') }}" id="company-form">
                            @csrf
                            <select
                                name="company_id"
                                class="select select-bordered select-sm w-full"
                                onchange="document.getElementById('company-form').submit()"
                            >
                                @foreach($userCompanies as $company)
                                    <option value="{{ $company->id }}" {{ $company->id === auth()->user()->active_company_id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        @elseif($userCompanies->count() === 1)
                        <p class="text-xs text-zinc-500 truncate px-1">{{ $userCompanies->first()?->name }}</p>
                        @endif

                        {{-- Dark mode toggle --}}
                        <div class="flex items-center justify-between px-1">
                            <span class="text-sm text-zinc-500">{{ __('Dark mode') }}</span>
                            <input type="checkbox" class="toggle toggle-sm"
                                x-model="darkMode"
                                @change="$wire.toggleDarkMode && $wire.toggleDarkMode()" />
                        </div>

                        {{-- User menu --}}
                        <x-desktop-user-menu class="hidden lg:flex" />
                    </div>
                    @endauth
                </aside>
            </div>
        </div>

        {{-- Toast notifications --}}
        <div
            x-data="{ toasts: [] }"
            @notify.window="
                const t = { id: Date.now(), text: $event.detail.text, variant: $event.detail.variant || 'info' };
                toasts.push(t);
                setTimeout(() => toasts = toasts.filter(x => x.id !== t.id), 4000);
            "
            class="toast toast-top toast-end z-50"
        >
            <template x-for="t in toasts" :key="t.id">
                <div class="alert shadow-lg" :class="{
                    'alert-success': t.variant === 'success',
                    'alert-error': t.variant === 'danger' || t.variant === 'error',
                    'alert-warning': t.variant === 'warning',
                    'alert-info': t.variant === 'info'
                }">
                    <span x-text="t.text"></span>
                </div>
            </template>
        </div>
    </body>
</html>
