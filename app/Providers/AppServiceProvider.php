<?php

namespace App\Providers;

use App\Services\AccessLevelService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AccessLevelService::class);
    }

    public function boot(): void
    {
        $this->configureDefaults();
        $this->shareViewData();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(app()->isProduction());

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)->mixedCase()->letters()->numbers()->symbols()->uncompromised()
            : null,
        );
    }

    protected function shareViewData(): void
    {
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $view->with([
                    'authUser' => $user,
                    'activeCompany' => $user->activeCompany,
                    'userCompanies' => $user->companies()->orderBy('name')->get(),
                ]);
            }
        });
    }
}
