<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <ul class="menu menu-sm p-0 gap-0.5" aria-label="{{ __('Settings') }}">
            <li>
                <a href="{{ route('profile.edit') }}" wire:navigate
                   class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    {{ __('Profile') }}
                </a>
            </li>
            <li>
                <a href="{{ route('security.edit') }}" wire:navigate
                   class="{{ request()->routeIs('security.edit') ? 'active' : '' }}">
                    {{ __('Security') }}
                </a>
            </li>
            <li>
                <a href="{{ route('appearance.edit') }}" wire:navigate
                   class="{{ request()->routeIs('appearance.edit') ? 'active' : '' }}">
                    {{ __('Appearance') }}
                </a>
            </li>
        </ul>
    </div>

    <div class="divider divider-horizontal md:hidden"></div>

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="text-lg font-semibold">{{ $heading ?? '' }}</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
