<?php

namespace App\Http\Livewire\QuoteCalculations;

use App\Models\BomItem;
use App\Models\Duty;
use App\Models\FxRate;
use App\Models\GpBudget;
use App\Models\InboundFreightItem;
use App\Models\ProductionCostItem;
use App\Models\QuoteCalculation;
use App\Services\QuoteCalculationCalculator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.fullscreen')]
#[Title('Quote Calculation')]
class QuoteCalculationEdit extends Component
{
    public int $quoteId;

    // Section A: Header
    public ?string $customerName = null;

    public ?string $netlogNumber = null;

    public ?string $netType = null;

    public ?string $material = null;

    public ?int $numberOfUnits = null;

    public ?string $size = null;

    // Section B: Config
    public ?string $salesRegion = null;

    public ?string $factory = null;

    public string $masterCurrency = 'EUR';

    public ?string $incoTerms = null;

    public string $rateMode = 'live';

    // Status (string enum)
    public string $status = 'draft';

    // Status booleans (for checkboxes)
    public bool $statusReady = false;

    public bool $statusCalculation = false;

    public bool $statusProposed = false;

    public bool $statusApproved = false;

    public bool $statusConfirmed = false;

    // Status timestamps (ISO strings for display)
    public ?string $statusReadyAt = null;

    public ?string $statusCalculationAt = null;

    public ?string $statusProposedAt = null;

    public ?string $statusApprovedAt = null;

    public ?string $statusConfirmedAt = null;

    // Financial inputs (productionGpPercent in % display form e.g. 25.52, stored as 0.2552)
    public ?float $productionGpPercent = null;

    public ?float $salesPrice = null;

    public ?string $salesPriceCurrency = 'EUR';

    public ?float $outboundFreightCost = null;

    public ?string $outboundFreightCurrency = 'EUR';

    public ?float $supervisionCost = null;

    public ?string $supervisionCurrency = 'EUR';

    // Delivery
    public ?string $deliveryFirstNetWeek = null;

    public ?string $deliveryLastNetWeek = null;

    public ?int $netsPerTruck = null;

    public ?string $transitTime = null;

    public ?string $estWeekOfDelivery = null;

    public ?int $deliveryYear = null;

    public ?string $deliverToCountry = null;

    public ?string $deliveryAddress = null;

    public ?float $totalNetWeight = null;

    public ?float $weightOfNetting = null;

    // Item arrays
    public array $bomItems = [];

    public array $inboundFreightItems = [];

    public array $productionCostItems = [];

    public function mount(int $id): void
    {
        $quote = QuoteCalculation::with(['bomItems', 'inboundFreightItems', 'productionCostItems'])
            ->findOrFail($id);

        $this->quoteId = $quote->id;
        $this->customerName = $quote->customer_name;
        $this->netlogNumber = $quote->netlog_number;
        $this->netType = $quote->net_type;
        $this->material = $quote->material;
        $this->numberOfUnits = $quote->number_of_units;
        $this->size = $quote->size;
        $this->salesRegion = $quote->sales_region;
        $this->factory = $quote->factory;
        $this->masterCurrency = $quote->master_currency ?? 'EUR';
        $this->incoTerms = $quote->inco_terms;
        $this->rateMode = $quote->rate_mode ?? 'live';
        $this->status = $quote->status ?? 'draft';

        $this->statusReady = $quote->status_ready_at !== null;
        $this->statusCalculation = $quote->status_calculation_at !== null;
        $this->statusProposed = $quote->status_proposed_at !== null;
        $this->statusApproved = $quote->status_approved_at !== null;
        $this->statusConfirmed = $quote->status_confirmed_at !== null;

        $this->statusReadyAt = $quote->status_ready_at?->toDateTimeString();
        $this->statusCalculationAt = $quote->status_calculation_at?->toDateTimeString();
        $this->statusProposedAt = $quote->status_proposed_at?->toDateTimeString();
        $this->statusApprovedAt = $quote->status_approved_at?->toDateTimeString();
        $this->statusConfirmedAt = $quote->status_confirmed_at?->toDateTimeString();

        // Convert stored decimal (0.25) to display percentage (25.00)
        $this->productionGpPercent = $quote->production_gp_percent !== null
            ? round($quote->production_gp_percent * 100, 4)
            : null;

        $this->salesPrice = $quote->sales_price;
        $this->salesPriceCurrency = $quote->sales_price_currency ?? 'EUR';
        $this->outboundFreightCost = $quote->outbound_freight_cost;
        $this->outboundFreightCurrency = $quote->outbound_freight_currency ?? 'EUR';
        $this->supervisionCost = $quote->supervision_cost;
        $this->supervisionCurrency = $quote->supervision_currency ?? 'EUR';

        $this->deliveryFirstNetWeek = $quote->delivery_first_net_week;
        $this->deliveryLastNetWeek = $quote->delivery_last_net_week;
        $this->netsPerTruck = $quote->nets_per_truck;
        $this->transitTime = $quote->transit_time;
        $this->estWeekOfDelivery = $quote->est_week_of_delivery;
        $this->deliveryYear = $quote->delivery_year;
        $this->deliverToCountry = $quote->deliver_to_country;
        $this->deliveryAddress = $quote->delivery_address;
        $this->totalNetWeight = $quote->total_net_weight;
        $this->weightOfNetting = $quote->weight_of_netting;

        $this->bomItems = $quote->bomItems->map(fn ($item) => [
            'description' => $item->description ?? '',
            'qty' => $item->qty,
            'unit' => $item->unit ?? 'kg',
            'unit_cost' => $item->unit_cost,
            'currency' => $item->currency ?? 'EUR',
        ])->values()->all();

        $this->inboundFreightItems = $quote->inboundFreightItems->map(fn ($item) => [
            'description' => $item->description ?? '',
            'cost' => $item->cost,
            'currency' => $item->currency ?? 'EUR',
        ])->values()->all();

        $this->productionCostItems = $quote->productionCostItems->map(fn ($item) => [
            'description' => $item->description ?? '',
            'direct_hours' => $item->direct_hours,
            'direct_rate' => $item->direct_rate,
            'currency' => $item->currency ?? 'EUR',
            'benchmark_hours' => $item->benchmark_hours,
        ])->values()->all();
    }

    public function updated(string $property): void
    {
        if (str_starts_with($property, 'bomItems')) {
            $this->saveBomItems();

            return;
        }

        if (str_starts_with($property, 'inboundFreightItems')) {
            $this->saveFreightItems();

            return;
        }

        if (str_starts_with($property, 'productionCostItems')) {
            $this->saveProductionItems();

            return;
        }

        $this->saveQuote();
    }

    public function updateStatusStep(string $step): void
    {
        $stepMap = [
            'ready' => ['bool' => 'statusReady', 'at' => 'statusReadyAt', 'col' => 'status_ready_at', 'status' => 'ready'],
            'calculation' => ['bool' => 'statusCalculation', 'at' => 'statusCalculationAt', 'col' => 'status_calculation_at', 'status' => 'calculation_complete'],
            'proposed' => ['bool' => 'statusProposed', 'at' => 'statusProposedAt', 'col' => 'status_proposed_at', 'status' => 'proposed_complete'],
            'approved' => ['bool' => 'statusApproved', 'at' => 'statusApprovedAt', 'col' => 'status_approved_at', 'status' => 'market_approved'],
            'confirmed' => ['bool' => 'statusConfirmed', 'at' => 'statusConfirmedAt', 'col' => 'status_confirmed_at', 'status' => 'customer_confirmed'],
        ];

        if (! isset($stepMap[$step])) {
            return;
        }

        $cfg = $stepMap[$step];
        $boolProp = $cfg['bool'];
        $atProp = $cfg['at'];

        $this->$boolProp = ! $this->$boolProp;

        if ($this->$boolProp) {
            if (! $this->$atProp) {
                $this->$atProp = now()->toDateTimeString();
            }
        } else {
            $this->$atProp = null;
        }

        // Determine highest checked status
        $highestStatus = 'draft';
        foreach ($stepMap as $cfg) {
            $bp = $cfg['bool'];
            if ($this->$bp) {
                $highestStatus = $cfg['status'];
            }
        }
        $this->status = $highestStatus;

        QuoteCalculation::where('id', $this->quoteId)->update([
            'status' => $this->status,
            $cfg['col'] => $this->$atProp,
        ]);
    }

    public function toggleRateMode(): void
    {
        if ($this->rateMode === 'live') {
            FxRate::query()->update([
                'frozen_rate' => DB::raw('live_rate'),
                'frozen_at' => now(),
            ]);
            $this->rateMode = 'frozen';
        } else {
            $this->rateMode = 'live';
        }

        QuoteCalculation::where('id', $this->quoteId)->update(['rate_mode' => $this->rateMode]);

        unset($this->calculations);
    }

    public function addBomItem(): void
    {
        $this->bomItems[] = [
            'description' => '',
            'qty' => null,
            'unit' => 'kg',
            'unit_cost' => null,
            'currency' => $this->masterCurrency,
        ];
        $this->saveBomItems();
    }

    public function removeBomItem(int $index): void
    {
        array_splice($this->bomItems, $index, 1);
        $this->bomItems = array_values($this->bomItems);
        $this->saveBomItems();
    }

    public function addFreightItem(): void
    {
        if (count($this->inboundFreightItems) >= 3) {
            return;
        }

        $this->inboundFreightItems[] = [
            'description' => '',
            'cost' => null,
            'currency' => $this->masterCurrency,
        ];
        $this->saveFreightItems();
    }

    public function removeFreightItem(int $index): void
    {
        array_splice($this->inboundFreightItems, $index, 1);
        $this->inboundFreightItems = array_values($this->inboundFreightItems);
        $this->saveFreightItems();
    }

    public function addProductionItem(): void
    {
        if (count($this->productionCostItems) >= 2) {
            return;
        }

        $this->productionCostItems[] = [
            'description' => '',
            'direct_hours' => null,
            'direct_rate' => null,
            'currency' => $this->masterCurrency,
            'benchmark_hours' => null,
        ];
        $this->saveProductionItems();
    }

    public function removeProductionItem(int $index): void
    {
        array_splice($this->productionCostItems, $index, 1);
        $this->productionCostItems = array_values($this->productionCostItems);
        $this->saveProductionItems();
    }

    #[Computed]
    public function calculations(): array
    {
        $rateCol = $this->rateMode === 'frozen' ? 'frozen_rate' : 'live_rate';
        $fxRates = FxRate::pluck($rateCol, 'currency')
            ->map(fn ($r) => (float) ($r ?? 0))
            ->all();

        $gpTarget = $this->salesRegion
            ? GpBudget::where('sales_region', $this->salesRegion)->value('gp_target')
            : null;

        $dutyRecord = ($this->factory && $this->salesRegion)
            ? Duty::where('factory', $this->factory)
                ->where('sales_region', $this->salesRegion)
                ->first()
            : null;

        $dutyRate = $dutyRecord?->duty_rate;

        $data = [
            'master_currency' => $this->masterCurrency,
            'production_gp_percent' => $this->productionGpPercent !== null
                ? $this->productionGpPercent / 100
                : null,
            'sales_price' => $this->salesPrice,
            'sales_price_currency' => $this->salesPriceCurrency,
            'outbound_freight_cost' => $this->outboundFreightCost,
            'outbound_freight_currency' => $this->outboundFreightCurrency,
            'supervision_cost' => $this->supervisionCost,
            'supervision_currency' => $this->supervisionCurrency,
        ];

        return QuoteCalculationCalculator::calculate(
            $data,
            $this->bomItems,
            $this->inboundFreightItems,
            $this->productionCostItems,
            $fxRates,
            $gpTarget !== null ? (float) $gpTarget : null,
            $dutyRate
        );
    }

    #[Computed]
    public function fxCurrencies(): array
    {
        return FxRate::orderBy('currency')->pluck('currency')->all();
    }

    #[Computed]
    public function salesRegions(): array
    {
        return GpBudget::orderBy('sales_region')->pluck('sales_region')->all();
    }

    public function render()
    {
        return view('livewire.quote-calculations.edit');
    }

    private function saveQuote(): void
    {
        QuoteCalculation::where('id', $this->quoteId)->update([
            'customer_name' => $this->customerName,
            'netlog_number' => $this->netlogNumber,
            'net_type' => $this->netType,
            'material' => $this->material,
            'number_of_units' => $this->numberOfUnits,
            'size' => $this->size,
            'sales_region' => $this->salesRegion,
            'factory' => $this->factory,
            'master_currency' => $this->masterCurrency,
            'inco_terms' => $this->incoTerms,
            'rate_mode' => $this->rateMode,
            'status' => $this->status,
            'production_gp_percent' => $this->productionGpPercent !== null
                ? $this->productionGpPercent / 100
                : null,
            'sales_price' => $this->salesPrice,
            'sales_price_currency' => $this->salesPriceCurrency,
            'outbound_freight_cost' => $this->outboundFreightCost,
            'outbound_freight_currency' => $this->outboundFreightCurrency,
            'supervision_cost' => $this->supervisionCost,
            'supervision_currency' => $this->supervisionCurrency,
            'delivery_first_net_week' => $this->deliveryFirstNetWeek,
            'delivery_last_net_week' => $this->deliveryLastNetWeek,
            'nets_per_truck' => $this->netsPerTruck,
            'transit_time' => $this->transitTime,
            'est_week_of_delivery' => $this->estWeekOfDelivery,
            'delivery_year' => $this->deliveryYear,
            'deliver_to_country' => $this->deliverToCountry,
            'delivery_address' => $this->deliveryAddress,
            'total_net_weight' => $this->totalNetWeight,
            'weight_of_netting' => $this->weightOfNetting,
        ]);
    }

    private function saveBomItems(): void
    {
        BomItem::where('quote_calculation_id', $this->quoteId)->delete();
        foreach ($this->bomItems as $index => $item) {
            BomItem::create([
                'quote_calculation_id' => $this->quoteId,
                'sort_order' => $index,
                'description' => $item['description'] ?? null,
                'qty' => $item['qty'] ?? null,
                'unit' => $item['unit'] ?? null,
                'unit_cost' => $item['unit_cost'] ?? null,
                'currency' => $item['currency'] ?? 'EUR',
            ]);
        }
    }

    private function saveFreightItems(): void
    {
        InboundFreightItem::where('quote_calculation_id', $this->quoteId)->delete();
        foreach ($this->inboundFreightItems as $index => $item) {
            InboundFreightItem::create([
                'quote_calculation_id' => $this->quoteId,
                'sort_order' => $index,
                'description' => $item['description'] ?? null,
                'cost' => $item['cost'] ?? null,
                'currency' => $item['currency'] ?? 'EUR',
            ]);
        }
    }

    private function saveProductionItems(): void
    {
        ProductionCostItem::where('quote_calculation_id', $this->quoteId)->delete();
        foreach ($this->productionCostItems as $index => $item) {
            ProductionCostItem::create([
                'quote_calculation_id' => $this->quoteId,
                'sort_order' => $index,
                'description' => $item['description'] ?? null,
                'direct_hours' => $item['direct_hours'] ?? null,
                'direct_rate' => $item['direct_rate'] ?? null,
                'currency' => $item['currency'] ?? 'EUR',
                'benchmark_hours' => $item['benchmark_hours'] ?? null,
            ]);
        }
    }
}
