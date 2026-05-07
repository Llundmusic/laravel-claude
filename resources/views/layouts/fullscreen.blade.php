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

        {{-- Top bar --}}
        <header class="navbar border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 px-4 min-h-14 sticky top-0 z-30">
            <div class="flex items-center gap-3 flex-1">
                <a href="{{ route('quote-calculations.index') }}" wire:navigate
                   class="btn btn-ghost btn-sm gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span class="hidden sm:inline">{{ __('Quote Calculations') }}</span>
                </a>
                <div class="divider divider-horizontal mx-0 h-6"></div>
                <h1 class="text-base font-semibold">{{ $title ?? __('Quote Calculation') }}</h1>
            </div>
            @auth
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <span class="text-xs text-zinc-500">{{ __('Dark') }}</span>
                    <input type="checkbox" class="toggle toggle-sm" x-model="darkMode" />
                </label>
                <x-desktop-user-menu />
            </div>
            @endauth
        </header>

        {{-- Page content --}}
        <main class="p-4 lg:p-6">
            {{ $slot }}
        </main>

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
