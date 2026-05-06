<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        {{-- Top navbar --}}
        <header class="navbar border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 px-4">
            {{-- Mobile sidebar toggle --}}
            <label for="mobile-sidebar" class="btn btn-ghost btn-sm lg:hidden mr-2">
                <i class="bi bi-list text-xl"></i>
            </label>

            <x-app-logo href="{{ route('home') }}" wire:navigate />

            {{-- Desktop nav links --}}
            <nav class="hidden lg:flex items-center gap-1 ml-4">
                <a href="{{ route('home') }}" wire:navigate
                   class="btn btn-ghost btn-sm {{ request()->routeIs('home') ? 'btn-active' : '' }}">
                    <i class="bi bi-grid"></i> {{ __('home') }}
                </a>
            </nav>

            <div class="flex-1"></div>

            {{-- Right navbar actions --}}
            <div class="flex items-center gap-1">
                <div class="tooltip tooltip-bottom" data-tip="{{ __('Search') }}">
                    <a href="#" class="btn btn-ghost btn-sm">
                        <i class="bi bi-search text-base"></i>
                    </a>
                </div>
                <div class="tooltip tooltip-bottom hidden lg:block" data-tip="{{ __('Repository') }}">
                    <a href="https://github.com/laravel/livewire-starter-kit" target="_blank" class="btn btn-ghost btn-sm">
                        <i class="bi bi-git text-base"></i>
                    </a>
                </div>
                <div class="tooltip tooltip-bottom hidden lg:block" data-tip="{{ __('Documentation') }}">
                    <a href="https://laravel.com/docs/starter-kits#livewire" target="_blank" class="btn btn-ghost btn-sm">
                        <i class="bi bi-book text-base"></i>
                    </a>
                </div>

                <x-desktop-user-menu />
            </div>
        </header>

        {{-- Mobile sidebar --}}
        <div class="drawer lg:hidden">
            <input id="mobile-sidebar" type="checkbox" class="drawer-toggle" />
            <div class="drawer-side z-40">
                <label for="mobile-sidebar" aria-label="close sidebar" class="drawer-overlay"></label>
                <aside class="flex flex-col min-h-full w-72 bg-zinc-50 border-e border-zinc-200 dark:bg-zinc-900 dark:border-zinc-700 p-4">
                    <x-app-logo :sidebar="true" href="{{ route('home') }}" wire:navigate class="mb-4" />
                    <ul class="menu menu-sm p-0 gap-0.5">
                        <li>
                            <a href="{{ route('home') }}" wire:navigate class="{{ request()->routeIs('home') ? 'active' : '' }}">
                                <i class="bi bi-grid"></i> {{ __('home') }}
                            </a>
                        </li>
                        <li>
                            <a href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                                <i class="bi bi-git"></i> {{ __('Repository') }}
                            </a>
                        </li>
                        <li>
                            <a href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                                <i class="bi bi-book"></i> {{ __('Documentation') }}
                            </a>
                        </li>
                    </ul>
                </aside>
            </div>
        </div>

        {{ $slot }}

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
