<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                digits: Array(6).fill(''),
                focusOtp() {
                    this.$nextTick(() => this.$refs.otp?.querySelector('input')?.focus());
                },
                init() {
                    this.$watch('digits', v => { this.code = v.join(''); });
                    if (! this.showRecoveryInput) {
                        this.focusOtp();
                    }
                },
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;
                    this.code = '';
                    this.digits = Array(6).fill('');
                    this.recovery_code = '';
                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : this.focusOtp();
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :title="__('Authentication code')"
                    :description="__('Enter the authentication code provided by your authenticator application.')"
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    :title="__('Recovery code')"
                    :description="__('Please confirm access to your account by entering one of your emergency recovery codes.')"
                />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div class="flex items-center justify-center gap-2 my-5" x-ref="otp">
                            <template x-for="(digit, i) in digits" :key="i">
                                <input
                                    type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                    class="input input-bordered w-12 text-center text-lg font-mono"
                                    x-model="digits[i]"
                                    @input="digits[i] = $event.target.value.replace(/\D/g,'').slice(-1); if(digits[i] && i < 5) $el.nextElementSibling?.focus()"
                                    @keydown.backspace="if(!digits[i] && i > 0) $el.previousElementSibling?.focus()"
                                />
                            </template>
                        </div>
                        <input type="hidden" name="code" x-model="code" />
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                :required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code"
                                class="input input-bordered w-full"
                            />
                        </div>

                        @error('recovery_code')
                            <p class="text-error text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        {{ __('Continue') }}
                    </button>
                </div>

                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center">
                    <span class="opacity-50">{{ __('or you can') }}</span>
                    <div class="inline font-medium underline cursor-pointer opacity-80">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('login using a recovery code') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('login using an authentication code') }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
