<?php

namespace App\Services;

class QuoteCalculationCalculator
{
    /**
     * FX conversion formula: amount * master_rate / item_rate
     * Rates are stored as "1 EUR = X currency" so EUR rate = 1.
     *
     * @param  array<string, mixed>  $data  Quote fields (snake_case keys matching DB columns)
     * @param  array<int, array<string, mixed>>  $bomItems
     * @param  array<int, array<string, mixed>>  $freightItems
     * @param  array<int, array<string, mixed>>  $productionItems
     * @param  array<string, float>  $fxRates  Currency code => rate (1 EUR = X)
     * @param  float|null  $gpTarget  Region GP target (e.g. 0.25 for 25%)
     * @param  float|null|string  $dutyRate  null="Check", float=rate as decimal
     * @return array<string, mixed>
     */
    public static function calculate(
        array $data,
        array $bomItems,
        array $freightItems,
        array $productionItems,
        array $fxRates,
        ?float $gpTarget,
        mixed $dutyRate = 'not_set'
    ): array {
        $masterCurrency = $data['master_currency'] ?? 'EUR';
        $masterRate = (float) ($fxRates[$masterCurrency] ?? 1.0);

        // BOM items: qty * unit_cost * master_rate / item_rate
        $bomConverted = [];
        foreach ($bomItems as $i => $item) {
            $itemRate = (float) ($fxRates[$item['currency'] ?? 'EUR'] ?? 1.0);
            $qty = (float) ($item['qty'] ?? 0);
            $unitCost = (float) ($item['unit_cost'] ?? 0);
            $bomConverted[$i] = $itemRate > 0 ? $qty * $unitCost * $masterRate / $itemRate : 0.0;
        }

        // Inbound freight: cost * master_rate / item_rate
        $freightConverted = [];
        foreach ($freightItems as $i => $item) {
            $itemRate = (float) ($fxRates[$item['currency'] ?? 'EUR'] ?? 1.0);
            $cost = (float) ($item['cost'] ?? 0);
            $freightConverted[$i] = $itemRate > 0 ? $cost * $masterRate / $itemRate : 0.0;
        }

        // Production costs: hours * rate * master_rate / item_rate
        $prodConverted = [];
        foreach ($productionItems as $i => $item) {
            $itemRate = (float) ($fxRates[$item['currency'] ?? 'EUR'] ?? 1.0);
            $hours = (float) ($item['direct_hours'] ?? 0);
            $rate = (float) ($item['direct_rate'] ?? 0);
            $prodConverted[$i] = $itemRate > 0 ? $hours * $rate * $masterRate / $itemRate : 0.0;
        }

        $totalMaterialFreight = array_sum($bomConverted) + array_sum($freightConverted);
        $totalProductionCost = array_sum($prodConverted);
        $directProductionCost = $totalMaterialFreight + $totalProductionCost;

        // Production GP
        $productionGpPct = (float) ($data['production_gp_percent'] ?? 0);
        $productionGpAmount = ($productionGpPct > 0 && $productionGpPct < 1)
            ? ($directProductionCost / (1 - $productionGpPct)) - $directProductionCost
            : 0.0;
        $productionPriceWithGp = $directProductionCost + $productionGpAmount;

        // Outbound freight + supervision (converted)
        $obFreightRate = (float) ($fxRates[$data['outbound_freight_currency'] ?? $masterCurrency] ?? $masterRate);
        $obFreightConverted = $obFreightRate > 0
            ? (float) ($data['outbound_freight_cost'] ?? 0) * $masterRate / $obFreightRate
            : 0.0;

        $supRate = (float) ($fxRates[$data['supervision_currency'] ?? $masterCurrency] ?? $masterRate);
        $supConverted = $supRate > 0
            ? (float) ($data['supervision_cost'] ?? 0) * $masterRate / $supRate
            : 0.0;

        $cogsDelivered = $productionPriceWithGp + $obFreightConverted + $supConverted;

        // Sales price
        $spCurrencyRate = (float) ($fxRates[$data['sales_price_currency'] ?? $masterCurrency] ?? $masterRate);
        $salesPriceConverted = $spCurrencyRate > 0
            ? (float) ($data['sales_price'] ?? 0) * $masterRate / $spCurrencyRate
            : 0.0;

        // GP metrics
        $gpAmount = $salesPriceConverted - $cogsDelivered;
        $gpPercent = $salesPriceConverted > 0 ? $gpAmount / $salesPriceConverted : 0.0;

        $combinedGp = $gpAmount + $productionGpAmount;
        $combinedGpPercent = $salesPriceConverted > 0 ? $combinedGp / $salesPriceConverted : 0.0;

        // Budget price
        $budgetPrice = ($gpTarget !== null && $gpTarget > 0 && $gpTarget < 1)
            ? $cogsDelivered / (1 - $gpTarget)
            : 0.0;
        $requiresApproval = $budgetPrice > 0 && $salesPriceConverted < $budgetPrice * 0.95;

        // Rate calculations
        $totalDirectHours = array_sum(array_column($productionItems, 'direct_hours') ?: []);
        $totalBenchmarkHours = array_sum(array_column($productionItems, 'benchmark_hours') ?: []);
        $prodGpBase = $totalProductionCost + $productionGpAmount;
        $rateWithCalcHrs = $totalDirectHours > 0 ? $prodGpBase / $totalDirectHours : 0.0;
        $rateWithStdHrs = $totalBenchmarkHours > 0 ? $prodGpBase / $totalBenchmarkHours : 0.0;

        return [
            'bom_converted' => $bomConverted,
            'freight_converted' => $freightConverted,
            'prod_converted' => $prodConverted,
            'total_material_freight' => $totalMaterialFreight,
            'total_production_cost' => $totalProductionCost,
            'direct_production_cost' => $directProductionCost,
            'production_gp_amount' => $productionGpAmount,
            'production_price_with_gp' => $productionPriceWithGp,
            'outbound_freight_converted' => $obFreightConverted,
            'supervision_converted' => $supConverted,
            'cogs_delivered' => $cogsDelivered,
            'sales_price_converted' => $salesPriceConverted,
            'gp_amount' => $gpAmount,
            'gp_percent' => $gpPercent,
            'combined_gp' => $combinedGp,
            'combined_gp_percent' => $combinedGpPercent,
            'budget_price' => $budgetPrice,
            'requires_approval' => $requiresApproval,
            'rate_with_calc_hrs' => $rateWithCalcHrs,
            'rate_with_std_hrs' => $rateWithStdHrs,
            'duty_rate' => $dutyRate === 'not_set' ? null : $dutyRate,
        ];
    }
}
