<div {{ $attributes }} class="dropdown dropdown-top dropdown-start w-full">
    <div tabindex="0" role="button" class="flex items-center gap-2 w-full px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer" data-test="sidebar-menu-button">
        <div class="avatar placeholder">
            <div class="bg-zinc-700 text-white rounded-full w-8">
                <span class="text-xs">{{ auth()->user()->initials() }}</span>
            </div>
        </div>
        <div class="flex-1 text-start text-sm leading-tight min-w-0">
            <div class="font-semibold truncate">{{ auth()->user()->name }}</div>
            <div class="text-xs text-zinc-500 truncate">{{ auth()->user()->email }}</div>
        </div>
        <i class="bi bi-chevron-expand text-xs text-zinc-400"></i>
    </div>

    <ul tabindex="0" class="dropdown-content menu bg-white dark:bg-zinc-800 rounded-box z-10 w-56 p-2 shadow-lg border border-zinc-200 dark:border-zinc-700 mb-1">
        <li>
            <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-2">
                <i class="bi bi-gear"></i>
                {{ __('Settings') }}
            </a>
        </li>
        <li><hr class="my-1 border-zinc-200 dark:border-zinc-700"></li>
        <li>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="flex items-center gap-2 w-full text-left" data-test="logout-button">
                    <i class="bi bi-box-arrow-right"></i>
                    {{ __('Log out') }}
                </button>
            </form>
        </li>
    </ul>
</div>
