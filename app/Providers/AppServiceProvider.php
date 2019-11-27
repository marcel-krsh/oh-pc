<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    //
    if (env('TESTING_ONLY')) {
      // Auth::loginUsingId(7859, true);
    }
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register() {}
}
