<?php

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<dialog id="confirm-user-deletion" class="modal" x-data x-init="$watch('$wire.errors', () => { if(Object.keys($wire.errors).length) $el.showModal(); })">
    <div class="modal-box max-w-lg">
        <form method="POST" wire:submit="deleteUser" class="space-y-6">
            <div>
                <h3 class="font-bold text-lg">{{ __('Are you sure you want to delete your account?') }}</h3>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>
            </div>

            <div class="form-control w-full" x-data="{ show: false }">
                <label class="label"><span class="label-text">{{ __('Password') }}</span></label>
                <div class="relative">
                    <input wire:model="password" :type="show ? 'text' : 'password'"
                           class="input input-bordered w-full pr-10" />
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600">
                        <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
                @error('password') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="modal-action">
                <button type="button" class="btn" onclick="this.closest('dialog').close()">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-error" data-test="confirm-delete-user-button">
                    {{ __('Delete account') }}
                </button>
            </div>
        </form>
    </div>
</dialog>
