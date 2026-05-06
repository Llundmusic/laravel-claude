<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Name -->
            <div class="form-control w-full">
                <label class="label" for="name"><span class="label-text">{{ __('Name') }}</span></label>
                <input id="name" name="name" type="text" value="{{ old('name') }}"
                       class="input input-bordered w-full" required autofocus autocomplete="name"
                       placeholder="{{ __('Full name') }}" />
                @error('name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Email Address -->
            <div class="form-control w-full">
                <label class="label" for="email"><span class="label-text">{{ __('Email address') }}</span></label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                       class="input input-bordered w-full" required autocomplete="email"
                       placeholder="email@example.com" />
                @error('email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="form-control w-full" x-data="{ show: false }">
                <label class="label" for="password"><span class="label-text">{{ __('Password') }}</span></label>
                <div class="relative">
                    <input id="password" name="password" :type="show ? 'text' : 'password'"
                           class="input input-bordered w-full pr-10" required autocomplete="new-password"
                           placeholder="{{ __('Password') }}" />
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600">
                        <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
                @error('password') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-control w-full" x-data="{ show: false }">
                <label class="label" for="password_confirmation"><span class="label-text">{{ __('Confirm password') }}</span></label>
                <div class="relative">
                    <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'"
                           class="input input-bordered w-full pr-10" required autocomplete="new-password"
                           placeholder="{{ __('Confirm password') }}" />
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600">
                        <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
                @error('password_confirmation') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-full" data-test="register-user-button">
                {{ __('Create account') }}
            </button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" wire:navigate class="link link-hover">{{ __('Log in') }}</a>
        </div>
    </div>
</x-layouts::auth>
