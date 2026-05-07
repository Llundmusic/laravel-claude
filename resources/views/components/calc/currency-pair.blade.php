@props(['modelAmount', 'modelCurrency', 'currencies' => [], 'label' => '', 'step' => 'any'])

<div class="flex flex-col gap-0.5">
    @if($label)
        <span class="text-xs text-zinc-500">{{ $label }}</span>
    @endif
    <div class="flex gap-1">
        <input
            type="number"
            step="{{ $step }}"
            wire:model.blur="{{ $modelAmount }}"
            class="input input-bordered input-sm flex-1 min-w-0"
            placeholder="0.00"
        />
        <select
            wire:model.live="{{ $modelCurrency }}"
            class="select select-bordered select-sm w-24 shrink-0"
        >
            <option value="">—</option>
            @foreach($currencies as $code)
                <option value="{{ $code }}">{{ $code }}</option>
            @endforeach
        </select>
    </div>
</div>
