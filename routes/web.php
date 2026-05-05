<?php

use App\Http\Controllers\CompanyController;
use App\Http\Livewire\AccessSystem\AccessGroups\ManageAccessGroups;
use App\Http\Livewire\AccessSystem\Companies\CompanyAdministration;
use App\Http\Livewire\AccessSystem\Companies\ManageCompanies;
use App\Http\Livewire\AccessSystem\Roles\ManageRoles;
use App\Http\Livewire\AccessSystem\Users\ManageUsers;
use App\Http\Livewire\Admin\ActivityLog;
use App\Http\Livewire\Common\FilterManager;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/noaccess', 'noaccess')->name('noaccess');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/users', ManageUsers::class)->name('admin.users');
    Route::get('/admin/companies', ManageCompanies::class)->name('admin.companies');
    Route::get('/company/administration', CompanyAdministration::class)->name('company.administration');
    Route::get('/admin/roles', ManageRoles::class)->name('admin.roles');
    Route::get('/admin/access-groups', ManageAccessGroups::class)->name('admin.access-groups');
    Route::get('/admin/activitylog', ActivityLog::class)->name('admin.activitylog');
    Route::get('/filters', FilterManager::class)->name('filters');

    Route::post('/user/set-active-company', [CompanyController::class, 'setActiveCompany'])->name('user.set-active-company');
});

require __DIR__.'/settings.php';
