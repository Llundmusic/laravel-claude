<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    public string $phoneCode = '';
    public string $phone = '';
    public string $billingReference = '';
    public string $language = 'en';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name             = $user->name;
        $this->email            = $user->email;
        $this->phoneCode        = $user->phone_code ?? '';
        $this->phone            = $user->phone ?? '';
        $this->billingReference = $user->billing_reference ?? '';
        $this->language         = $user->language ?? 'en';
    }

    public function updated(string $property): void
    {
        $user = Auth::user();

        $map = [
            'name'             => 'name',
            'email'            => 'email',
            'phoneCode'        => 'phone_code',
            'phone'            => 'phone',
            'billingReference' => 'billing_reference',
            'language'         => 'language',
        ];

        if (! isset($map[$property])) {
            return;
        }

        $this->validateOnly($property, $this->profileRules($user->id));

        if ($property === 'email' && $user->email !== $this->email) {
            $user->email_verified_at = null;
        }

        $user->update([$map[$property] => $this->$property]);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('home', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        $this->dispatch('notify', text: __('A new verification link has been sent to your email address.'), variant: 'success');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Changes save automatically')">
        <div class="my-6 w-full space-y-6">
            <div class="form-control w-full">
                <label class="label"><span class="label-text">{{ __('Name') }}</span></label>
                <input type="text" wire:model.live.debounce.600ms="name"
                       class="input input-bordered w-full" required autofocus autocomplete="name" />
                @error('name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">{{ __('Email') }}</span></label>
                    <input type="email" wire:model.live.debounce.600ms="email"
                           class="input input-bordered w-full" required autocomplete="email" />
                    @error('email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                @if ($this->hasUnverifiedEmail)
                    <p class="mt-4 text-sm">
                        {{ __('Your email address is unverified.') }}
                        <button class="link link-hover text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                @endif
            </div>

            <div class="flex gap-3">
                <div class="form-control w-24">
                    <label class="label"><span class="label-text">{{ __('Code') }}</span></label>
                    <input type="text" wire:model.live.debounce.600ms="phoneCode" class="input input-bordered w-full" />
                </div>
                <div class="form-control flex-1">
                    <label class="label"><span class="label-text">{{ __('Phone') }}</span></label>
                    <input type="text" wire:model.live.debounce.600ms="phone" class="input input-bordered w-full" />
                </div>
            </div>

            <div class="form-control w-full">
                <label class="label"><span class="label-text">{{ __('Billing reference') }}</span></label>
                <input type="text" wire:model.live.debounce.600ms="billingReference" class="input input-bordered w-full" />
            </div>

            <div class="form-control w-full">
                <label class="label"><span class="label-text">{{ __('Language') }}</span></label>
                <select wire:model.live="language" class="select select-bordered w-full">
                    <option value="en">English</option>
                    <option value="no">Norsk</option>
                    <option value="da">Dansk</option>
                </select>
            </div>
        </div>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
