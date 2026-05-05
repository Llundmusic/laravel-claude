<?php

use App\Concerns\ProfileValidationRules;
use Flux\Flux;
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
        Flux::toast(text: __('A new verification link has been sent to your email address.'));
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
            <flux:input wire:model.live.debounce.600ms="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model.live.debounce.600ms="email" :label="__('Email')" type="email" required autocomplete="email" />
                @if ($this->hasUnverifiedEmail)
                    <flux:text class="mt-4">
                        {{ __('Your email address is unverified.') }}
                        <flux:link class="cursor-pointer text-sm" wire:click.prevent="resendVerificationNotification">
                            {{ __('Click here to re-send the verification email.') }}
                        </flux:link>
                    </flux:text>
                @endif
            </div>

            <div class="flex gap-3">
                <flux:input wire:model.live.debounce.600ms="phoneCode" :label="__('Code')" class="w-24" />
                <flux:input wire:model.live.debounce.600ms="phone" :label="__('Phone')" class="flex-1" />
            </div>

            <flux:input wire:model.live.debounce.600ms="billingReference" :label="__('Billing reference')" />

            <flux:select wire:model.live="language" :label="__('Language')">
                <option value="en">English</option>
                <option value="no">Norsk</option>
                <option value="da">Dansk</option>
            </flux:select>
        </div>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
