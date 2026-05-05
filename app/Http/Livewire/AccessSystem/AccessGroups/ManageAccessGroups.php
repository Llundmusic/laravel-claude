<?php

namespace App\Http\Livewire\AccessSystem\AccessGroups;

use App\Models\AccessSystem\AccessGroup;
use App\Models\AccessSystem\Company;
use App\Models\AccessSystem\Role;
use App\Services\AccessLevelService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Access Groups')]
class ManageAccessGroups extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    public ?int $selectedGroupId = null;

    public string $editDescription = '';

    public bool $showCreateModal = false;

    public string $newName = '';

    public string $newDescription = '';

    #[Computed]
    public function groups()
    {
        return AccessGroup::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount(['roles', 'companies'])
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function selectedGroup(): ?AccessGroup
    {
        return $this->selectedGroupId ? AccessGroup::with(['roles', 'companies'])->find($this->selectedGroupId) : null;
    }

    #[Computed]
    public function allRoles()
    {
        return Role::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function allCompanies()
    {
        return Company::where('is_active', true)->orderBy('name')->get();
    }

    public function selectGroup(int $groupId): void
    {
        $this->selectedGroupId = $groupId;
        $group = AccessGroup::find($groupId);
        $this->editDescription = $group?->description ?? '';
        unset($this->selectedGroup);
    }

    public function updatedEditDescription(): void
    {
        if (! $this->selectedGroupId) {
            return;
        }
        AccessGroup::findOrFail($this->selectedGroupId)->update(['description' => $this->editDescription]);
    }

    public function toggleActive(int $groupId): void
    {
        $group = AccessGroup::findOrFail($groupId);
        $group->update(['is_active' => ! $group->is_active]);
        unset($this->groups);
    }

    public function toggleRole(int $roleId): void
    {
        if (! $this->selectedGroupId) {
            return;
        }
        $group = AccessGroup::findOrFail($this->selectedGroupId);
        $assigned = $group->roles()->pluck('roles.id');

        if ($assigned->contains($roleId)) {
            $group->roles()->detach($roleId);
            app(AccessLevelService::class)->clearRoleAssignmentCache($group, [$roleId]);
        } else {
            $group->roles()->attach($roleId);
        }
        unset($this->selectedGroup);
    }

    public function toggleCompany(int $companyId): void
    {
        if (! $this->selectedGroupId) {
            return;
        }
        $group = AccessGroup::findOrFail($this->selectedGroupId);
        $assigned = $group->companies()->pluck('companies.id');

        if ($assigned->contains($companyId)) {
            $group->companies()->detach($companyId);
            app(AccessLevelService::class)->clearCompanyAssignmentCache($group, [$companyId]);
        } else {
            $group->companies()->attach($companyId);
        }
        unset($this->selectedGroup);
    }

    public function createGroup(): void
    {
        $this->validate([
            'newName' => 'required|string|max:255|unique:access_groups,name',
        ]);

        $group = AccessGroup::create([
            'name' => $this->newName,
            'description' => $this->newDescription,
            'is_active' => false,
        ]);

        $this->showCreateModal = false;
        $this->newName = $this->newDescription = '';
        $this->selectGroup($group->id);
        unset($this->groups);
        $this->dispatch('toast', message: 'Access group created.', variant: 'success');
    }

    public function render()
    {
        return view('livewire.access-system.access-groups.manage-access-groups');
    }
}
