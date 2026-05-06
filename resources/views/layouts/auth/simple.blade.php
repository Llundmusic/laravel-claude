<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
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
                    'alert-info': t.variant === 'info'
                }">
                    <span x-text="t.text"></span>
                </div>
            </template>
        </div>
    </body>
</html>
