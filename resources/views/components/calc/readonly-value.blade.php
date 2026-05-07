@props(['label', 'value' => null, 'currency' => '', 'format' => 'number'])

<div class="flex flex-col gap-0.5">
    <span class="text-xs text-zinc-500">{{ $label }}</span>
    <div class="rounded bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 px-3 py-1.5 text-sm font-mono font-medium text-emerald-800 dark:text-emerald-300">
        @if($value === null || $value === '')
            <span class="text-zinc-400">—</span>
        @elseif($format === 'percent')
            {{ number_format((float)$value * 100, 2) }}%
        @elseif($format === 'text')
            {{ $value }}
        @else
            {{ number_format((float)$value, 2) }}{{ $currency ? ' '.$currency : '' }}
        @endif
    </div>
</div>
