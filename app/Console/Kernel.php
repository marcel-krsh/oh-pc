<?php

namespace App\Console;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use App\Jobs\SyncAddressesJob;
use App\Jobs\SyncPeopleJob;
use App\Jobs\SyncMonitoringStatusTypesJob;
use App\Jobs\SyncProjectActivitiesJob;
use App\Jobs\SyncProjectActivityTypesJob;
use App\Jobs\SyncProjectRolesJob;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        
        Commands\MakeTestFriendlyCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /////////////////
        ////// SYNC JOBS
        ////

        // @TODO Make it check for tables that need to be updated first.
        // Addresses
        $test = DB::table('jobs')->where('payload','like','%SyncAddresses%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncAddressesJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        // Monitoring Status Types
        $test = DB::table('jobs')->where('payload','like','%SyncMonitoringStatusTypes%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncMonitoringStatusTypesJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        // Monitoring People
        $test = DB::table('jobs')->where('payload','like','%SyncPeopleJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncPeopleJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        // Project Activities
        $test = DB::table('jobs')->where('payload','like','%SyncProjectActivitiesJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncProjectActivitiesJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        // Project Activity Types
        $test = DB::table('jobs')->where('payload','like','%SyncProjectActivityTypesJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncProjectActivityTypesJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        // Project Roles
        $test = DB::table('jobs')->where('payload','like','%SyncProjectRolesJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncProjectRolesJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
