<?php

namespace App\Http\Livewire\AccessSystem\Users;

use App\Mail\UserActivated;
use App\Models\AccessSystem\Company;
use App\Models\AccessSystem\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manage Users')]
class ManageUsers extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $sortField = 'name';

    #[Url]
    public string $sortDirection = 'asc';

    public bool $showCreateModal = false;

    public bool $showEditPanel = false;

    // Create form
    public string $newName = '';

    public string $newEmail = '';

    public string $newPassword = '';

    public string $newLanguage = 'en';

    public ?int $newCompanyId = null;

    public ?int $newRoleId = null;

    // Edit panel
    public ?int $editingUserId = null;

    public string $editName = '';

    public string $editEmail = '';

    public string $editPhone = '';

    public string $editPhoneCode = '';

    public string $editBillingReference = '';

    public string $editLanguage = 'en';

    public bool $editIsActive = true;

    public bool $editAccessAllCompanies = false;

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);
    }

    #[Computed]
    public function companies()
    {
        return Company::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function roles()
    {
        return Role::where('is_active', true)->orderBy('name')->get();
    }

    public function sort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function createUser(): void
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newEmail' => 'required|email|unique:users,email',
            'newPassword' => 'required|string|min:8',
            'newLanguage' => 'required|string|max:10',
        ]);

        $user = User::create([
            'name' => $this->newName,
            'email' => $this->newEmail,
            'password' => Hash::make($this->newPassword),
            'language' => $this->newLanguage,
            'is_active' => true,
        ]);

        if ($this->newCompanyId && $this->newRoleId) {
            $user->companies()->attach($this->newCompanyId, ['role_id' => $this->newRoleId]);
            $user->update(['active_company_id' => $this->newCompanyId]);
        }

        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->dispatch('toast', message: 'User created.', variant: 'success');
    }

    public function editUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editPhone = $user->phone ?? '';
        $this->editPhoneCode = $user->phone_code ?? '';
        $this->editBillingReference = $user->billing_reference ?? '';
        $this->editLanguage = $user->language ?? 'en';
        $this->editIsActive = $user->is_active;
        $this->editAccessAllCompanies = $user->access_all_companies;
        $this->showEditPanel = true;
    }

    public function updated(string $property): void
    {
        if (! str_starts_with($property, 'edit') || ! $this->editingUserId) {
            return;
        }

        $this->validateOnly($property, [
            'editName' => 'required|string|max:255',
            'editEmail' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'editPhone' => 'nullable|string|max:30',
            'editLanguage' => 'required|string|max:10',
        ]);

        $map = [
            'editName' => 'name',
            'editEmail' => 'email',
            'editPhone' => 'phone',
            'editPhoneCode' => 'phone_code',
            'editBillingReference' => 'billing_reference',
            'editLanguage' => 'language',
            'editIsActive' => 'is_active',
            'editAccessAllCompanies' => 'access_all_companies',
        ];

        if (! isset($map[$property])) {
            return;
        }

        User::findOrFail($this->editingUserId)->update([$map[$property] => $this->$property]);
    }

    public function deleteUser(int $userId): void
    {
        User::findOrFail($userId)->delete();
        if ($this->editingUserId === $userId) {
            $this->showEditPanel = false;
        }
        $this->dispatch('toast', message: 'User deleted.', variant: 'success');
    }

    public function sendActivationEmail(int $userId): void
    {
        $user = User::findOrFail($userId);
        Mail::to($user->email)->send(new UserActivated($user));
        $this->dispatch('toast', message: 'Activation email sent.', variant: 'success');
    }

    private function resetCreateForm(): void
    {
        $this->newName = $this->newEmail = $this->newPassword = '';
        $this->newLanguage = 'en';
        $this->newCompanyId = $this->newRoleId = null;
    }

    public function render()
    {
        return view('livewire.access-system.users.manage-users');
    }
}
