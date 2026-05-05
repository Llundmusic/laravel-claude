<?php

namespace App\Http\Livewire\AccessSystem\Companies;

use App\Models\AccessSystem\Company;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Companies')]
class ManageCompanies extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public bool $showCreateModal = false;

    public string $newName = '';

    public string $newCustomerNumber = '';

    public string $newEmail = '';

    #[Computed]
    public function companies()
    {
        return Company::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('customer_number', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->paginate(20);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function createCompany(): void
    {
        $this->validate([
            'newName' => 'required|string|max:255|unique:companies,name',
            'newCustomerNumber' => 'nullable|string|max:100|unique:companies,customer_number',
            'newEmail' => 'nullable|email|unique:companies,email',
        ]);

        Company::create([
            'name' => $this->newName,
            'slug' => Str::slug($this->newName),
            'customer_number' => $this->newCustomerNumber ?: null,
            'email' => $this->newEmail ?: null,
            'is_active' => true,
        ]);

        $this->showCreateModal = false;
        $this->newName = $this->newCustomerNumber = $this->newEmail = '';
        $this->dispatch('toast', message: 'Company created.', variant: 'success');
    }

    public function toggleActive(int $companyId): void
    {
        $company = Company::findOrFail($companyId);
        $company->update(['is_active' => ! $company->is_active]);
    }

    public function deleteCompany(int $companyId): void
    {
        Company::findOrFail($companyId)->delete();
        $this->dispatch('toast', message: 'Company deleted.', variant: 'success');
    }

    public function render()
    {
        return view('livewire.access-system.companies.manage-companies');
    }
}
