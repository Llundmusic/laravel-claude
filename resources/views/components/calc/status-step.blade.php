@props(['label', 'checked' => false, 'timestamp' => null, 'step'])

<div class="flex items-start gap-3 py-2">
    <input
        type="checkbox"
        class="checkbox checkbox-sm mt-0.5 checkbox-success"
        wire:click="updateStatusStep('{{ $step }}')"
        @checked($checked)
    />
    <div class="flex flex-col">
        <span class="text-sm font-medium {{ $checked ? 'text-success' : '' }}">{{ $label }}</span>
        @if($timestamp)
            <span class="text-xs text-zinc-400">{{ \Carbon\Carbon::parse($timestamp)->format('d M Y H:i') }}</span>
        @endif
    </div>
</div>
