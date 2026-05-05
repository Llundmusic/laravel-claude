<?php

namespace App\Http\Livewire\Admin;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.app')]
#[Title('Activity Log')]
class ActivityLog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $filterSubject = '';

    #[Computed]
    public function logs()
    {
        return Activity::query()
            ->with(['causer', 'subject'])
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->filterSubject, fn ($q) => $q->where('subject_type', 'like', "%{$this->filterSubject}%"))
            ->latest()
            ->paginate(30);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSubject(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.activity-log');
    }
}
