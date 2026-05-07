<?php

namespace App\Http\Livewire\QuoteCalculations;

use App\Models\FxRate;
use App\Models\GpBudget;
use App\Models\QuoteCalculation;
use App\Services\QuoteCalculationCalculator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Quote Calculations')]
class QuoteCalculationIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function newCalculation(): void
    {
        $quote = QuoteCalculation::create(['status' => 'draft']);
        $this->redirect(route('quote-calculations.edit', $quote->id), navigate: true);
    }

    #[Computed]
    public function quotes()
    {
        return QuoteCalculation::with(['bomItems', 'inboundFreightItems', 'productionCostItems'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('customer_name', 'like', "%{$this->search}%")
                    ->orWhere('netlog_number', 'like', "%{$this->search}%")
                    ->orWhere('net_type', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function fxRates(): array
    {
        return FxRate::pluck('live_rate', 'currency')->map(fn ($r) => (float) $r)->all();
    }

    #[Computed]
    public function frozenRates(): array
    {
        return FxRate::pluck('frozen_rate', 'currency')
            ->map(fn ($r) => (float) ($r ?? 0))
            ->all();
    }

    #[Computed]
    public function gpBudgets(): array
    {
        return GpBudget::pluck('gp_target', 'sales_region')->all();
    }

    public function calculationsFor(QuoteCalculation $quote): array
    {
        $rates = $quote->rate_mode === 'frozen' ? $this->frozenRates : $this->fxRates;
        $gpTarget = $this->gpBudgets[$quote->sales_region] ?? null;

        return QuoteCalculationCalculator::calculate(
            $quote->toArray(),
            $quote->bomItems->toArray(),
            $quote->inboundFreightItems->toArray(),
            $quote->productionCostItems->toArray(),
            $rates,
            $gpTarget ? (float) $gpTarget : null
        );
    }

    public function render()
    {
        return view('livewire.quote-calculations.index');
    }
}
