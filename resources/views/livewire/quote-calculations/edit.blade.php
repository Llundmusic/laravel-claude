<div>
    <div class="max-w-6xl mx-auto space-y-3 pb-12">

        {{-- Metadata bar --}}
        <div class="flex items-center gap-4 py-2 text-sm text-zinc-500">
            <span>ID: #{{ $quoteId }}</span>
            @php
                $statusLabels = [
                    'draft' => 'Draft', 'ready' => 'Ready', 'calculation_complete' => 'Calculation Complete',
                    'proposed_complete' => 'Proposed', 'market_approved' => 'Market Approved', 'customer_confirmed' => 'Confirmed',
                ];
                $statusColors = [
                    'draft' => 'badge-ghost', 'ready' => 'badge-info', 'calculation_complete' => 'badge-primary',
                    'proposed_complete' => 'badge-warning', 'market_approved' => 'badge-success', 'customer_confirmed' => 'badge-success',
                ];
            @endphp
            <span class="badge badge-sm {{ $statusColors[$status] ?? 'badge-ghost' }}">
                {{ $statusLabels[$status] ?? ucfirst($status) }}
            </span>
            <span class="ml-auto text-xs">{{ __('Changes auto-saved') }}</span>
        </div>

        {{-- A: Header Info --}}
        <details class="collapse collapse-arrow bg-base-200 border border-base-300" open>
            <summary class="collapse-title font-semibold">A. Header Information</summary>
            <div class="collapse-content">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Customer Name') }}</span></label>
                        <input type="text" wire:model.blur="customerName" class="input input-bordered input-sm" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Netlog Number') }}</span></label>
                        <input type="text" wire:model.blur="netlogNumber" class="input input-bordered input-sm" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Net Type') }}</span></label>
                        <input type="text" wire:model.blur="netType" class="input input-bordered input-sm" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Material') }}</span></label>
                        <input type="text" wire:model.blur="material" class="input input-bordered input-sm" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Number of Units') }}</span></label>
                        <input type="number" wire:model.blur="numberOfUnits" class="input input-bordered input-sm" min="0" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Size') }}</span></label>
                        <input type="text" wire:model.blur="size" class="input input-bordered input-sm" />
                    </div>
                </div>
            </div>
        </details>

        {{-- B: Configuration --}}
        <details class="collapse collapse-arrow bg-base-200 border border-base-300" open>
            <summary class="collapse-title font-semibold">B. Configuration</summary>
            <div class="collapse-content">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Sales Region') }}</span></label>
                        <select wire:model.live="salesRegion" class="select select-bordered select-sm">
                            <option value="">— {{ __('Select region') }} —</option>
                            @foreach($this->salesRegions as $region)
                                <option value="{{ $region }}" @selected($salesRegion === $region)>{{ $region }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Factory') }}</span></label>
                        <select wire:model.live="factory" class="select select-bordered select-sm">
                            <option value="">— {{ __('Select factory') }} —</option>
                            <option value="Aurangabad - India">Aurangabad - India</option>
                            <option value="Plunge - Lithuania">Plunge - Lithuania</option>
                            <option value="Siauliai - Lithuania">Siauliai - Lithuania</option>
                            <option value="Amposta - Spain">Amposta - Spain</option>
                            <option value="Hildre - Norway">Hildre - Norway</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Master Currency') }}</span></label>
                        <select wire:model.live="masterCurrency" class="select select-bordered select-sm">
                            @foreach($this->fxCurrencies as $code)
                                <option value="{{ $code }}" @selected($masterCurrency === $code)>{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Inco Terms') }}</span></label>
                        <input type="text" wire:model.blur="incoTerms" class="input input-bordered input-sm" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Rate Mode') }}</span></label>
                        <div class="flex items-center gap-3 pt-1">
                            <span class="text-sm {{ $rateMode === 'live' ? 'font-semibold text-success' : 'text-zinc-400' }}">{{ __('Live') }}</span>
                            <input
                                type="checkbox"
                                class="toggle toggle-sm"
                                @checked($rateMode === 'frozen')
                                wire:click="toggleRateMode"
                            />
                            <span class="text-sm {{ $rateMode === 'frozen' ? 'font-semibold text-warning' : 'text-zinc-400' }}">{{ __('Frozen') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </details>

        {{-- C: Status Tracker --}}
        <details class="collapse collapse-arrow bg-base-200 border border-base-300">
            <summary class="collapse-title font-semibold">C. Status Tracker</summary>
            <div class="collapse-content">
                <div class="flex flex-col divide-y divide-base-300 pt-2">
                    <x-calc.status-step
                        label="{{ __('Ready for Calculation') }}"
                        :checked="$statusReady"
                        :timestamp="$statusReadyAt"
                        step="ready"
                    />
                    <x-calc.status-step
                        label="{{ __('Calculation Complete') }}"
                        :checked="$statusCalculation"
                        :timestamp="$statusCalculationAt"
                        step="calculation"
                    />
                    <x-calc.status-step
                        label="{{ __('Proposed Sales Price & Terms Complete') }}"
                        :checked="$statusProposed"
                        :timestamp="$statusProposedAt"
                        step="proposed"
                    />
                    <x-calc.status-step
                        label="{{ __('Market Price Approved') }}"
                        :checked="$statusApproved"
                        :timestamp="$statusApprovedAt"
                        step="approved"
                    />
                    <x-calc.status-step
                        label="{{ __('Customer Confirmation') }}"
                        :checked="$statusConfirmed"
                        :timestamp="$statusConfirmedAt"
                        step="confirmed"
                    />
                </div>
            </div>
        </details>

        {{-- D: Bill of Materials --}}
        <details class="collapse collapse-arrow bg-base-200 border border-base-300" open>
            <summary class="collapse-title font-semibold">D. Bill of Materials</summary>
            <div class="collapse-content">
                <div class="pt-2 overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr class="text-xs">
                                <th>{{ __('Description') }}</th>
                                <th class="w-24">{{ __('Qty') }}</th>
                                <th class="w-24">{{ __('Unit') }}</th>
                                <th class="w-28">{{ __('Unit Cost') }}</th>
                                <th class="w-24">{{ __('Currency') }}</th>
                                <th class="w-32 text-right">{{ __('Converted') }}</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bomItems as $index => $item)
                                <tr>
                                    <td>
                                        <input type="text" wire:model.blur="bomItems.{{ $index }}.description" class="input input-bordered input-xs w-full" />
                                    </td>
                                    <td>
                                        <input type="number" step="any" wire:model.blur="bomItems.{{ $index }}.qty" class="input input-bordered input-xs w-full" />
                                    </td>
                                    <td>
                                        <select wire:model.live="bomItems.{{ $index }}.unit" class="select select-bordered select-xs w-full">
                                            <option value="kg">kg</option>
                                            <option value="m">m</option>
                                            <option value="pcs">pcs</option>
                                            <option value="set">set</option>
                                            <option value="other">other</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="any" wire:model.blur="bomItems.{{ $index }}.unit_cost" class="input input-bordered input-xs w-full" />
                                    </td>
                                    <td>
                                        <select wire:model.live="bomItems.{{ $index }}.currency" class="select select-bordered select-xs w-full">
                                            @foreach($this->fxCurrencies as $code)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-right">
                                        <span class="font-mono text-xs text-emerald-700 dark:text-emerald-400">
                                            {{ number_format($this->calculations['bom_converted'][$index] ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="removeBomItem({{ $index }})" class="btn btn-ghost btn-xs text-error">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-zinc-400 py-4 text-sm">{{ __('No BOM items. Add one below.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="pt-2">
                                    <button wire:click="addBomItem" class="btn btn-sm btn-outline gap-1">
                                        <i class="bi bi-plus"></i> {{ __('Add Row') }}
                                    </button>
                                </td>
                                <td class="text-right pt-2">
                                    <span class="text-xs text-zinc-500">{{ __('Total') }}</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </details>

        {{-- E: Inbound Freight --}}
        <details class="collapse collapse-arrow bg-base-200 border border-base-300">
            <summary class="collapse-title font-semibold">E. Inbound Freight</summary>
            <div class="collapse-content">
                <div class="pt-2 overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr class="text-xs">
                                <th>{{ __('Description') }}</th>
                                <th class="w-32">{{ __('Cost') }}</th>
                                <th class="w-24">{{ __('Currency') }}</th>
                                <th class="w-32 text-right">{{ __('Converted') }}</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inboundFreightItems as $index => $item)
                                <tr>
                                    <td>
                                        <input type="text" wire:model.blur="inboundFreightItems.{{ $index }}.description" class="input input-bordered input-xs w-full" />
                                    </td>
                                    <td>
                                        <input type="number" step="any" wire:model.blur="inboundFreightItems.{{ $index }}.cost" class="input input-bordered input-xs w-full" />
                                    </td>
                                    <td>
                                        <select wire:model.live="inboundFreightItems.{{ $index }}.currency" class="select select-bordered select-xs w-full">
                                            @foreach($this->fxCurrencies as $code)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-right">
                                        <span class="font-mono text-xs text-emerald-700 dark:text-emerald-400">
                                            {{ number_format($this->calculations['freight_converted'][$index] ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="removeFreightItem({{ $index }})" class="btn btn-ghost btn-xs text-error">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-zinc-400 py-4 text-sm">{{ __('No freight items.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="pt-2">
                                    @if(count($inboundFreightItems) < 3)
                                        <button wire:click="addFreightItem" class="btn btn-sm btn-outline gap-1">
                                            <i class="bi bi-plus"></i> {{ __('Add Row') }}
                                        </button>
                                    @else
                                        <span class="text-xs text-zinc-400">{{ __('Maximum 3 freight items.') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </details>

        {{-- F: Production Costs --}}
        <details class="collapse collapse-arrow bg-base-200 border border-base-300" open>
            <summary class="collapse-title font-semibold">F. Production Costs</summary>
            <div class="collapse-content">
                <div class="pt-2 overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr class="text-xs">
                                <th>{{ __('Description') }}</th>
                                <th class="w-24">{{ __('Direct Hrs') }}</th>
                                <th class="w-28">{{ __('Rate') }}</th>
                                <th class="w-24">{{ __('Currency') }}</th>
                                <th class="w-24">{{ __('Bench Hrs') }}</th>
                                <th class="w-32 text-right">{{ __('Converted') }}</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productionCostItems as $index => $item)
                                <tr>
                                    <td>
                                        <input type="text" wire:model.blur="productionCostItems.{{ $index }}.description" class="input input-bordered input-xs w-full" />
                                    </td>
                                    <td>
                                        <input type="number" step="any" wire:model.blur="productionCostItems.{{ $index }}.direct_hours" class="input input-bordered input-xs w-full" />
                                    </td>
                                    <td>
                                        <input type="number" step="any" wire:model.blur="productionCostItems.{{ $index }}.direct_rate" class="input input-bordered input-xs w-full" />
                                    </td>
                                    <td>
                                        <select wire:model.live="productionCostItems.{{ $index }}.currency" class="select select-bordered select-xs w-full">
                                            @foreach($this->fxCurrencies as $code)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="any" wire:model.blur="productionCostItems.{{ $index }}.benchmark_hours" class="input input-bordered input-xs w-full" />
                                    </td>
                                    <td class="text-right">
                                        <span class="font-mono text-xs text-emerald-700 dark:text-emerald-400">
                                            {{ number_format($this->calculations['prod_converted'][$index] ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="removeProductionItem({{ $index }})" class="btn btn-ghost btn-xs text-error">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-zinc-400 py-4 text-sm">{{ __('No production cost items.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" class="pt-2">
                                    @if(count($productionCostItems) < 2)
                                        <button wire:click="addProductionItem" class="btn btn-sm btn-outline gap-1">
                                            <i class="bi bi-plus"></i> {{ __('Add Row') }}
                                        </button>
                                    @else
                                        <span class="text-xs text-zinc-400">{{ __('Maximum 2 production items.') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    {{-- Hourly rates --}}
                    @if(count($productionCostItems) > 0)
                    <div class="flex gap-6 mt-3 pt-3 border-t border-base-300">
                        <x-calc.readonly-value
                            label="{{ __('Rate with Calc Hrs') }}"
                            :value="$this->calculations['rate_with_calc_hrs']"
                            :currency="$masterCurrency"
                        />
                        <x-calc.readonly-value
                            label="{{ __('Rate with Std Hrs') }}"
                            :value="$this->calculations['rate_with_std_hrs']"
                            :currency="$masterCurrency"
                        />
                    </div>
                    @endif
                </div>
            </div>
        </details>

        {{-- G: Cost Summary --}}
        <details class="collapse collapse-arrow bg-base-200 border border-base-300" open>
            <summary class="collapse-title font-semibold">G. Cost Summary</summary>
            <div class="collapse-content">
                <div class="pt-2">
                    {{-- Production GP input --}}
                    <div class="form-control w-48 mb-4">
                        <label class="label"><span class="label-text">{{ __('Production GP %') }}</span></label>
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.01" min="0" max="100" wire:model.blur="productionGpPercent" class="input input-bordered input-sm flex-1" placeholder="e.g. 25.00" />
                            <span class="text-zinc-500 text-sm">%</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <x-calc.readonly-value
                            label="{{ __('Total Material & Freight') }}"
                            :value="$this->calculations['total_material_freight']"
                            :currency="$masterCurrency"
                        />
                        <x-calc.readonly-value
                            label="{{ __('Direct Production Cost (COGS ExW)') }}"
                            :value="$this->calculations['direct_production_cost']"
                            :currency="$masterCurrency"
                        />
                        <x-calc.readonly-value
                            label="{{ __('Production GP Amount') }}"
                            :value="$this->calculations['production_gp_amount']"
                            :currency="$masterCurrency"
                        />
                        <x-calc.readonly-value
                            label="{{ __('Production Price incl. GP') }}"
                            :value="$this->calculations['production_price_with_gp']"
                            :currency="$masterCurrency"
                        />
                    </div>

                    {{-- Outbound freight + supervision inputs --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 pt-4 border-t border-base-300">
                        <x-calc.currency-pair
                            modelAmount="outboundFreightCost"
                            modelCurrency="outboundFreightCurrency"
                            :currencies="$this->fxCurrencies"
                            label="{{ __('Outbound Freight Cost') }}"
                        />
                        <x-calc.currency-pair
                            modelAmount="supervisionCost"
                            modelCurrency="supervisionCurrency"
                            :currencies="$this->fxCurrencies"
                            label="{{ __('Supervision Cost') }}"
                        />
                    </div>

                    <div class="mt-4">
                        <x-calc.readonly-value
                            label="{{ __('COGS Delivered') }}"
                            :value="$this->calculations['cogs_delivered']"
                            :currency="$masterCurrency"
                        />
                    </div>
                </div>
            </div>
        </details>

        {{-- H: Market View --}}
        <details class="collapse collapse-arrow bg-base-200 border border-base-300" open>
            <summary class="collapse-title font-semibold">H. Market View</summary>
            <div class="collapse-content">
                <div class="pt-2 space-y-4">
                    @if($this->calculations['requires_approval'])
                        <x-calc.approval-alert />
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-calc.currency-pair
                            modelAmount="salesPrice"
                            modelCurrency="salesPriceCurrency"
                            :currencies="$this->fxCurrencies"
                            label="{{ __('Sales Price') }}"
                        />
                        <x-calc.readonly-value
                            label="{{ __('Budget Price') }}"
                            :value="$this->calculations['budget_price'] ?: null"
                            :currency="$masterCurrency"
                        />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <x-calc.readonly-value
                            label="{{ __('GP Amount') }}"
                            :value="$this->calculations['gp_amount']"
                            :currency="$masterCurrency"
                        />
                        <x-calc.readonly-value
                            label="{{ __('GP %') }}"
                            :value="$this->calculations['gp_percent']"
                            format="percent"
                        />
                        <x-calc.readonly-value
                            label="{{ __('Combined GP') }}"
                            :value="$this->calculations['combined_gp']"
                            :currency="$masterCurrency"
                        />
                        <x-calc.readonly-value
                            label="{{ __('Combined GP %') }}"
                            :value="$this->calculations['combined_gp_percent']"
                            format="percent"
                        />
                    </div>
                </div>
            </div>
        </details>

        {{-- I: Delivery Plan --}}
        <details class="collapse collapse-arrow bg-base-200 border border-base-300">
            <summary class="collapse-title font-semibold">I. Delivery Plan</summary>
            <div class="collapse-content">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('First Net Completed Week') }}</span></label>
                        <select wire:model.live="deliveryFirstNetWeek" class="select select-bordered select-sm">
                            <option value="">—</option>
                            @for($w = 1; $w <= 52; $w++)
                                <option value="WK {{ $w }}" @selected($deliveryFirstNetWeek === "WK $w")>WK {{ $w }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Last Net Completed Week') }}</span></label>
                        <select wire:model.live="deliveryLastNetWeek" class="select select-bordered select-sm">
                            <option value="">—</option>
                            @for($w = 1; $w <= 52; $w++)
                                <option value="WK {{ $w }}" @selected($deliveryLastNetWeek === "WK $w")>WK {{ $w }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Nets per Truck') }}</span></label>
                        <input type="number" wire:model.blur="netsPerTruck" class="input input-bordered input-sm" min="0" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Transit Time') }}</span></label>
                        <input type="text" wire:model.blur="transitTime" class="input input-bordered input-sm" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Inco Terms') }}</span></label>
                        <input type="text" class="input input-bordered input-sm bg-base-300" value="{{ $incoTerms ?? '—' }}" readonly />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Est. Week of Delivery') }}</span></label>
                        <select wire:model.live="estWeekOfDelivery" class="select select-bordered select-sm">
                            <option value="">—</option>
                            @for($w = 1; $w <= 52; $w++)
                                <option value="WK {{ $w }}" @selected($estWeekOfDelivery === "WK $w")>WK {{ $w }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Delivery Year') }}</span></label>
                        <select wire:model.live="deliveryYear" class="select select-bordered select-sm">
                            <option value="">—</option>
                            @foreach([2025, 2026, 2027, 2028, 2029, 2030] as $year)
                                <option value="{{ $year }}" @selected($deliveryYear == $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Deliver to Country') }}</span></label>
                        <select wire:model.live="deliverToCountry" class="select select-bordered select-sm">
                            <option value="">—</option>
                            @foreach(['Norway','Scotland/UK','Faroe Islands','Iceland','Canada','Chile','Spain','France','Denmark','Ireland','Australia','Japan','China','Saudi Arabia','Oman','Other'] as $country)
                                <option value="{{ $country }}" @selected($deliverToCountry === $country)>{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Total Net Weight (kg)') }}</span></label>
                        <input type="number" step="any" wire:model.blur="totalNetWeight" class="input input-bordered input-sm" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Weight of Netting (kg)') }}</span></label>
                        <input type="number" step="any" wire:model.blur="weightOfNetting" class="input input-bordered input-sm" />
                    </div>
                    <div class="form-control sm:col-span-2 lg:col-span-3">
                        <label class="label"><span class="label-text">{{ __('Delivery Address') }}</span></label>
                        <textarea wire:model.blur="deliveryAddress" rows="3" class="textarea textarea-bordered text-sm"></textarea>
                    </div>

                    {{-- Estimated Duties --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <x-calc.readonly-value
                            label="{{ __('Estimated Duties') }}"
                            :value="$this->calculations['duty_rate'] !== null ? number_format($this->calculations['duty_rate'] * 100, 2).'%' : 'Check'"
                            format="text"
                        />
                    </div>
                </div>
            </div>
        </details>

    </div>
</div>
