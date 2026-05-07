<div>
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ __('Quote Calculations') }}</h1>
            <button class="btn btn-primary" wire:click="newCalculation">
                <i class="bi bi-plus"></i> {{ __('New Calculation') }}
            </button>
        </div>

        {{-- Search --}}
        <div class="relative max-w-sm">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none"></i>
            <input
                type="text"
                wire:model.live.debounce.600ms="search"
                class="input input-bordered w-full pl-9"
                placeholder="{{ __('Search customer, netlog, net type…') }}"
            />
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 text-left dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('Customer') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Netlog #') }}</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">{{ __('Net Type') }}</th>
                        <th class="px-4 py-3 font-medium hidden lg:table-cell">{{ __('Factory') }}</th>
                        <th class="px-4 py-3 font-medium hidden lg:table-cell">{{ __('Region') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                        <th class="px-4 py-3 font-medium text-right hidden sm:table-cell">{{ __('Sales Price') }}</th>
                        <th class="px-4 py-3 font-medium text-right hidden xl:table-cell">{{ __('GP%') }}</th>
                        <th class="px-4 py-3 font-medium text-right hidden xl:table-cell">{{ __('Combined GP%') }}</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">{{ __('Created') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @forelse($this->quotes as $quote)
                        @php
                            $calcs = $this->calculationsFor($quote);
                            $statusColors = [
                                'draft'                => 'badge-ghost',
                                'ready'                => 'badge-info',
                                'calculation_complete' => 'badge-primary',
                                'proposed_complete'    => 'badge-warning',
                                'market_approved'      => 'badge-success',
                                'customer_confirmed'   => 'badge-success',
                            ];
                            $statusLabels = [
                                'draft'                => 'Draft',
                                'ready'                => 'Ready',
                                'calculation_complete' => 'Calculated',
                                'proposed_complete'    => 'Proposed',
                                'market_approved'      => 'Approved',
                                'customer_confirmed'   => 'Confirmed',
                            ];
                        @endphp
                        <tr
                            class="cursor-pointer transition hover:bg-zinc-50 dark:hover:bg-zinc-800"
                            onclick="window.location.href='{{ route('quote-calculations.edit', $quote->id) }}'"
                        >
                            <td class="px-4 py-3 font-medium">
                                {{ $quote->customer_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-zinc-500">
                                {{ $quote->netlog_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell text-zinc-500">
                                {{ $quote->net_type ?? '—' }}
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell text-zinc-500">
                                {{ $quote->factory ?? '—' }}
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell text-zinc-500">
                                {{ $quote->sales_region ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge badge-sm {{ $statusColors[$quote->status] ?? 'badge-ghost' }}">
                                    {{ __($statusLabels[$quote->status] ?? ucfirst($quote->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right hidden sm:table-cell font-mono">
                                @if($quote->sales_price)
                                    {{ number_format($quote->sales_price, 2) }}
                                    <span class="text-zinc-400 text-xs">{{ $quote->sales_price_currency }}</span>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right hidden xl:table-cell font-mono">
                                @if($calcs['sales_price_converted'] > 0)
                                    {{ number_format($calcs['gp_percent'] * 100, 1) }}%
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right hidden xl:table-cell font-mono">
                                @if($calcs['sales_price_converted'] > 0)
                                    {{ number_format($calcs['combined_gp_percent'] * 100, 1) }}%
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell text-zinc-500 text-xs">
                                {{ $quote->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-zinc-400">
                                {{ __('No quote calculations found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="border-t border-zinc-100 px-4 py-3 dark:border-zinc-700">
                {{ $this->quotes->links() }}
            </div>
        </div>
    </div>
</div>
