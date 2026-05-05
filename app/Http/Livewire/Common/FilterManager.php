<?php

namespace App\Http\Livewire\Common;

use App\Models\Common\UserFilter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Saved Filters')]
class FilterManager extends Component
{
    public bool $showCreateModal = false;

    public string $newPageName = '';

    public string $newFilterName = '';

    public string $newFilterValues = '';

    public string $newFilterColumn = '';

    #[Computed]
    public function filters()
    {
        return auth()->user()
            ->filters()
            ->orderBy('order')
            ->orderBy('created_at')
            ->get();
    }

    public function createFilter(): void
    {
        $this->validate([
            'newPageName' => 'required|string|max:255',
            'newFilterName' => 'required|string|max:255',
        ]);

        $maxOrder = auth()->user()->filters()->max('order') ?? -1;

        UserFilter::create([
            'user_id' => auth()->id(),
            'page_name' => $this->newPageName,
            'filter_name' => $this->newFilterName,
            'filter_values' => $this->newFilterValues ? json_decode($this->newFilterValues, true) : null,
            'filter_column' => $this->newFilterColumn ?: null,
            'order' => $maxOrder + 1,
        ]);

        $this->showCreateModal = false;
        $this->newPageName = $this->newFilterName = $this->newFilterValues = $this->newFilterColumn = '';
        unset($this->filters);
        $this->dispatch('toast', message: 'Filter saved.', variant: 'success');
    }

    public function deleteFilter(int $filterId): void
    {
        UserFilter::where('id', $filterId)
            ->where('user_id', auth()->id())
            ->delete();
        unset($this->filters);
    }

    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            UserFilter::where('id', $id)
                ->where('user_id', auth()->id())
                ->update(['order' => $index]);
        }
        unset($this->filters);
    }

    public function render()
    {
        return view('livewire.common.filter-manager');
    }
}
