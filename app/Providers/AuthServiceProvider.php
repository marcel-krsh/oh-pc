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
        $this->registerPolicies();
        Gate::define('view-all-parcels', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_parcels', $perms);
        });
        Gate::define('edit-parcel', function ($user) {
            $perms = $user->getPerms();
            return in_array('edit_parcels', $perms);
        });
        Gate::define('add-parcel', function ($user) {
            $perms = $user->getPerms();
            return in_array('add_parcels', $perms);
        });
        Gate::define('delete-parcel', function ($user) {
            $perms = $user->getPerms();
            return in_array('delete_parcel', $perms);
        });
        Gate::define('view-users', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_users', $perms);
        });
        Gate::define('edit-user', function ($user) {
            $perms = $user->getPerms();
            return in_array('edit_users', $perms);
        });
        Gate::define('add-users', function ($user) {
            $perms = $user->getPerms();
            return in_array('add_users', $perms);
        });
        Gate::define('view-invoices', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_invoices', $perms);
        });
        Gate::define('edit-invoice', function ($user) {
            $perms = $user->getPerms();
            return in_array('edit_invoices', $perms);
        });
        Gate::define('add-invoice', function ($user) {
            $perms = $user->getPerms();
            return in_array('add_invoices', $perms);
        });
        Gate::define('cancel-invoice', function ($user) {
            $perms = $user->getPerms();
            return in_array('cancel_invoices', $perms);
        });
        Gate::define('view-purchase-orders', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_pos', $perms);
        });
        Gate::define('admin_tools', function ($user) {
            $perms = $user->getPerms();
            //TODO: Fix this gate
            return in_array('view_admin_tools', $perms);
        });
        Gate::define('view-accounting', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_accounting', $perms);
        });
        Gate::define('edit-account', function ($user) {
            $perms = $user->getPerms();
            return in_array('edit-accounts', $perms);
        });
        Gate::define('view-stats', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_stats', $perms);
        });
        Gate::define('view-request', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_requests', $perms);
        });
        Gate::define('edit-request', function ($user) {
            $perms = $user->getPerms();
            return in_array('edit_requests', $perms);
        });
        Gate::define('add-request', function ($user) {
            $perms = $user->getPerms();
            return in_array('add_requests', $perms);
        });
        Gate::define('cancel-request', function ($user) {
            $perms = $user->getPerms();
            return in_array('cancel_requests', $perms);
        });
        Gate::define('view-pos', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_pos', $perms);
        });
        Gate::define('edit-pos', function ($user) {
            $perms = $user->getPerms();
            return in_array('edit_pos', $perms);
        });
        Gate::define('cancel-pos', function ($user) {
            $perms = $user->getPerms();
            return in_array('cancel_pos', $perms);
        });
        Gate::define('view-roles', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_roles', $perms);
        });
        Gate::define('edit-roles', function ($user) {
            $perms = $user->getPerms();
            return in_array('edit_roles', $perms);
        });
        Gate::define('add-roles', function ($user) {
            $perms = $user->getPerms();
            return in_array('add_roles', $perms);
        });
        Gate::define('delete-roles', function ($user) {
            $perms = $user->getPerms();
            return in_array('delete_roles', $perms);
        });
        Gate::define('view-loc', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_loc', $perms);
        });
        Gate::define('edit-loc', function ($user) {
            $perms = $user->getPerms();
            return in_array('edit_loc', $perms);
        });
        Gate::define('delete-loc', function ($user) {
            $perms = $user->getPerms();
            return in_array('delete_loc', $perms);
        });
        Gate::define('cancel-loc', function ($user) {
            $perms = $user->getPerms();
            return in_array('cancel_loc', $perms);
        });
        Gate::define('request-loc-advance', function ($user) {
            $perms = $user->getPerms();
            return in_array('request_loc_advance', $perms);
        });
        Gate::define('approve-loc-advance', function ($user) {
            $perms = $user->getPerms();
            return in_array('approve_loc_advance', $perms);
        });
        Gate::define('create-payment-batch', function ($user) {
            $perms = $user->getPerms();
            return in_array('create_payment_batch', $perms);
        });
        Gate::define('manage-processes', function ($user) {
            $perms = $user->getPerms();
            return in_array('manage_processes', $perms);
        });
        Gate::define('view-all-history', function ($user) {
            $perms = $user->getPerms();
            return in_array('view_all_history', $perms);
        });
        Gate::define('signator', function ($user) {
            $perms = $user->getPerms();
            return in_array('signator', $perms);
        });

        // Disposition gates
        Gate::define('create-disposition', function ($user) {
            $perms = $user->getPerms();
            return in_array('create-disposition', $perms);
        });
        Gate::define('authorize-disposition-request', function ($user) {
            $perms = $user->getPerms();
            return in_array('authorize-disposition-request', $perms);
        });
        Gate::define('submit-disposition', function ($user) {
            $perms = $user->getPerms();
            return in_array('submit-disposition', $perms);
        });
        Gate::define('hfa-review-disposition', function ($user) {
            $perms = $user->getPerms();
            return in_array('hfa-review-disposition', $perms);
        });
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
