<div>
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <h1 class="text-2xl font-bold">{{ __('Activity Log') }}</h1>

        <div class="flex gap-3">
            <div class="relative flex-1">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none"></i>
                <input type="text" wire:model.live.debounce.300ms="search"
                       class="input input-bordered w-full pl-9"
                       placeholder="{{ __('Search descriptions…') }}" />
            </div>
            <input type="text" wire:model.live="filterSubject"
                   class="input input-bordered w-48"
                   placeholder="{{ __('Filter by model…') }}" />
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 text-left dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('When') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Description') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Subject') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('By') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Changes') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @forelse($this->logs as $log)
                    <tr class="align-top transition hover:bg-zinc-50 dark:hover:bg-zinc-800">
                        <td class="px-4 py-3 text-zinc-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">{{ $log->description }}</td>
                        <td class="px-4 py-3 text-zinc-500">
                            @if($log->subject)
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            @else
                                <span class="text-zinc-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $log->causer?->name ?? '—' }}</td>
                        <td class="px-4 py-3 max-w-xs">
                            @if($log->properties->has('attributes'))
                            <details class="cursor-pointer">
                                <summary class="text-xs text-zinc-400 hover:text-zinc-600">{{ __('View changes') }}</summary>
                                <pre class="mt-1 overflow-auto rounded bg-zinc-100 p-2 text-xs dark:bg-zinc-800">{{ json_encode($log->properties->all(), JSON_PRETTY_PRINT) }}</pre>
                            </details>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-zinc-400">{{ __('No activity found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="border-t border-zinc-100 px-4 py-3 dark:border-zinc-700">{{ $this->logs->links() }}</div>
        </div>
    </div>
</div>
