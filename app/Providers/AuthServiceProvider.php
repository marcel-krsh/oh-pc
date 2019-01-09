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
        
        Gate::define('hfa-sign-disposition', function ($user) {
            $perms = $user->getPerms();
            return in_array('hfa-sign-disposition', $perms);
        });
        Gate::define('hfa-release-disposition', function ($user) {
            $perms = $user->getPerms();
            return in_array('hfa-release-disposition', $perms);
        });
        Gate::define('view-disposition', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_disposition', $perms);
        });
        Gate::define('view_disposition', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_disposition', $perms);
        });
    }
}
