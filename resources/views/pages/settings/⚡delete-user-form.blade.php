<?php

use Livewire\Component;

new class extends Component {}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h2 class="text-lg font-semibold">{{ __('Delete account') }}</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <button class="btn btn-error" data-test="delete-user-button"
            @click="document.getElementById('confirm-user-deletion').showModal()">
        {{ __('Delete account') }}
    </button>

    <livewire:pages::settings.delete-user-modal />
</section>
