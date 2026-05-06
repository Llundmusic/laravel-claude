<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <h2 class="sr-only">{{ __('Appearance settings') }}</h2>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div
            x-data="{
                mode: localStorage.getItem('appearance') || 'system',
                applyTheme(theme) {
                    localStorage.setItem('appearance', theme);
                    this.mode = theme;
                    const dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.classList.toggle('dark', dark);
                }
            }"
            x-init="applyTheme(mode)"
            class="join"
        >
            <button @click="applyTheme('light')" :class="{ 'btn-active': mode === 'light' }" class="btn btn-sm join-item">
                <i class="bi bi-sun"></i> {{ __('Light') }}
            </button>
            <button @click="applyTheme('dark')" :class="{ 'btn-active': mode === 'dark' }" class="btn btn-sm join-item">
                <i class="bi bi-moon"></i> {{ __('Dark') }}
            </button>
            <button @click="applyTheme('system')" :class="{ 'btn-active': mode === 'system' }" class="btn btn-sm join-item">
                <i class="bi bi-display"></i> {{ __('System') }}
            </button>
        </div>
    </x-pages::settings.layout>
</section>
