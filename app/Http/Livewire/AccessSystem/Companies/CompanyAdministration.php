<?php

namespace App\Http\Livewire\AccessSystem\Companies;

use App\Models\AccessSystem\Company;
use App\Models\AccessSystem\Department;
use App\Models\AccessSystem\Role;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Company Administration')]
class CompanyAdministration extends Component
{
    public Company $company;

    public string $activeTab = 'details';

    // Company detail fields (auto-save)
    public string $name = '';

    public string $slug = '';

    public string $customerNumber = '';

    public string $email = '';

    public string $phoneCode = '';

    public string $phone = '';

    public string $billingAddressLine1 = '';

    public string $billingAddressLine2 = '';

    public string $billingPostalCode = '';

    public string $billingCity = '';

    public string $billingCountry = '';

    public bool $isActive = true;

    // Add user form
    public bool $showAddUserModal = false;

    public ?int $addUserId = null;

    public ?int $addRoleId = null;

    // Add department form
    public bool $showAddDeptModal = false;

    public string $newDeptName = '';

    public function mount(Company $company): void
    {
        $this->company = $company;
        $this->fill([
            'name' => $company->name,
            'slug' => $company->slug,
            'customerNumber' => $company->customer_number ?? '',
            'email' => $company->email ?? '',
            'phoneCode' => $company->phone_code ?? '',
            'phone' => $company->phone ?? '',
            'billingAddressLine1' => $company->billing_address_line1 ?? '',
            'billingAddressLine2' => $company->billing_address_line2 ?? '',
            'billingPostalCode' => $company->billing_postal_code ?? '',
            'billingCity' => $company->billing_city ?? '',
            'billingCountry' => $company->billing_country ?? '',
            'isActive' => $company->is_active,
        ]);
    }

    public function updated(string $property): void
    {
        $map = [
            'name' => 'name',
            'slug' => 'slug',
            'customerNumber' => 'customer_number',
            'email' => 'email',
            'phoneCode' => 'phone_code',
            'phone' => 'phone',
            'billingAddressLine1' => 'billing_address_line1',
            'billingAddressLine2' => 'billing_address_line2',
            'billingPostalCode' => 'billing_postal_code',
            'billingCity' => 'billing_city',
            'billingCountry' => 'billing_country',
            'isActive' => 'is_active',
        ];

        if (! isset($map[$property])) {
            return;
        }

        $this->company->update([$map[$property] => $this->$property]);
    }

    #[Computed]
    public function companyUsers()
    {
        return $this->company->users()->with('activeCompany')->orderBy('name')->get();
    }

    #[Computed]
    public function departments()
    {
        return $this->company->departments()->orderBy('name')->get();
    }

    #[Computed]
    public function availableUsers()
    {
        $assigned = $this->company->users()->pluck('users.id');

        return User::whereNotIn('id', $assigned)->orderBy('name')->get();
    }

    #[Computed]
    public function roles()
    {
        return Role::where('is_active', true)->orderBy('name')->get();
    }

    public function addUser(): void
    {
        $this->validate([
            'addUserId' => 'required|integer|exists:users,id',
            'addRoleId' => 'required|integer|exists:roles,id',
        ]);

        $this->company->users()->attach($this->addUserId, ['role_id' => $this->addRoleId]);

        // Set active company if not set
        $user = User::find($this->addUserId);
        if (! $user->active_company_id) {
            $user->update(['active_company_id' => $this->company->id]);
        }

        $this->showAddUserModal = false;
        $this->addUserId = $this->addRoleId = null;
        unset($this->companyUsers, $this->availableUsers);
        $this->dispatch('toast', message: 'User added.', variant: 'success');
    }

    public function updateUserRole(int $userId, int $roleId): void
    {
        $this->company->users()->updateExistingPivot($userId, ['role_id' => $roleId]);
    }

    public function removeUser(int $userId): void
    {
        $this->company->users()->detach($userId);
        unset($this->companyUsers, $this->availableUsers);
        $this->dispatch('toast', message: 'User removed.', variant: 'success');
    }

    public function addDepartment(): void
    {
        $this->validate(['newDeptName' => 'required|string|max:255']);
        Department::create(['company_id' => $this->company->id, 'name' => $this->newDeptName]);
        $this->newDeptName = '';
        $this->showAddDeptModal = false;
        unset($this->departments);
        $this->dispatch('toast', message: 'Department added.', variant: 'success');
    }

    public function deleteDepartment(int $deptId): void
    {
        Department::findOrFail($deptId)->delete();
        unset($this->departments);
        $this->dispatch('toast', message: 'Department deleted.', variant: 'success');
    }

    public function render()
    {
        return view('livewire.access-system.companies.company-administration');
    }
}
