<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];


    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        
        Gate::define('access_root', function ($user) {
            return $user->root_access();
        });
        Gate::define('access_admin', function ($user) {
            return $user->admin_access();
        });
        Gate::define('access_manager', function ($user) {
            return $user->manager_access();
        });
        Gate::define('access_auditor', function ($user) {
            return $user->auditor_access();
        });
        Gate::define('access_pm', function ($user) {
            return $user->pm_access();
        });
    }
}
