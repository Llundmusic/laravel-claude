<x-layouts::auth :title="__('Reset password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <div class="form-control w-full">
                <label class="label" for="email"><span class="label-text">{{ __('Email') }}</span></label>
                <input id="email" name="email" type="email" value="{{ request('email') }}"
                       class="input input-bordered w-full" required autocomplete="email" />
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
            </div>

            <button type="submit" class="btn btn-primary w-full" data-test="reset-password-button">
                {{ __('Reset password') }}
            </button>
        </form>
    </div>
</x-layouts::auth>
