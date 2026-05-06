<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <div class="form-control w-full">
                <label class="label" for="email"><span class="label-text">{{ __('Email address') }}</span></label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                       class="input input-bordered w-full" required autofocus autocomplete="email"
                       placeholder="email@example.com" />
                @error('email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="form-control w-full" x-data="{ show: false }">
                <div class="flex items-center justify-between">
                    <label class="label" for="password"><span class="label-text">{{ __('Password') }}</span></label>
                    @if (Route::has('password.request'))
                        <a class="link link-hover text-sm" href="{{ route('password.request') }}" wire:navigate>
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" name="password" :type="show ? 'text' : 'password'"
                           class="input input-bordered w-full pr-10" required autocomplete="current-password"
                           :placeholder="show ? '' : '••••••••'" />
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600">
                        <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
                @error('password') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Remember Me -->
            <label class="label cursor-pointer gap-2 justify-start">
                <input type="checkbox" name="remember" class="checkbox checkbox-sm"
                       {{ old('remember') ? 'checked' : '' }} />
                <span class="label-text">{{ __('Remember me') }}</span>
            </label>

            <button type="submit" class="btn btn-primary w-full" data-test="login-button">
                {{ __('Log in') }}
            </button>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <a href="{{ route('register') }}" wire:navigate class="link link-hover">{{ __('Sign up') }}</a>
            </div>
        @endif
    </div>
</x-layouts::auth>
