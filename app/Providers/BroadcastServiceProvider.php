<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Broadcast::routes();
        Broadcast::routes(['middleware' => ['auth']]);

        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
            //Auth::onceUsingId(286); // TEST BRIAN
        }

        //Broadcast::routes(['middleware' => ['auth:api']]);

         require base_path('routes/channels.php');
    }
}
