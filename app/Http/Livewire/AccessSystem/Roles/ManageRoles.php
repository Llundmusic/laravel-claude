<?php

namespace App\Http\Livewire\AccessSystem\Roles;

use App\Models\AccessSystem\Role;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manage Roles')]
class ManageRoles extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public bool $showCreateModal = false;

    public ?int $editingRoleId = null;

    // Create form
    public string $newName = '';

    public string $newDescription = '';

    public bool $newCanBeApprover = false;

    public bool $newIsSystemRole = false;

    // Inline edit (auto-save fields per row - stored as editXxx_{id})
    public array $editFields = [];

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(20);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function startEdit(int $roleId): void
    {
        $role = Role::findOrFail($roleId);
        $this->editingRoleId = $roleId;
        $this->editFields = [
            'name' => $role->name,
            'description' => $role->description ?? '',
            'can_be_approver' => $role->can_be_approver,
            'is_active' => $role->is_active,
        ];
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editFields.name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($this->editingRoleId)],
        ]);

        Role::findOrFail($this->editingRoleId)->update($this->editFields);
        $this->editingRoleId = null;
        $this->editFields = [];
        $this->dispatch('toast', message: 'Role updated.', variant: 'success');
    }

    public function cancelEdit(): void
    {
        $this->editingRoleId = null;
        $this->editFields = [];
    }

    public function createRole(): void
    {
        $this->validate([
            'newName' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $this->newName,
            'description' => $this->newDescription,
            'can_be_approver' => $this->newCanBeApprover,
            'is_system_role' => $this->newIsSystemRole,
            'is_active' => true,
        ]);

        $this->showCreateModal = false;
        $this->newName = $this->newDescription = '';
        $this->newCanBeApprover = $this->newIsSystemRole = false;
        $this->dispatch('toast', message: 'Role created.', variant: 'success');
    }

    public function deleteRole(int $roleId): void
    {
        Role::findOrFail($roleId)->delete();
        $this->dispatch('toast', message: 'Role deleted.', variant: 'success');
    }

    public function render()
    {
        return view('livewire.access-system.roles.manage-roles');
    }
}
