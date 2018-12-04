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
use App\Jobs\SyncProjectContactRolesJob;
use App\Jobs\SyncOrganizationsJob;
use App\Jobs\SyncProjectsJob;
use App\Jobs\SyncAmenityTypesJob;
use App\Jobs\SyncProgramsJob;
use App\Jobs\SyncProjectProgramStatusTypesJob;
use App\Jobs\SyncFinancialTypesJob;
use App\Jobs\SyncProgramDateTypesJob;

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

        // People
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

        // Project Contact Roles
        $test = DB::table('jobs')->where('payload','like','%SyncProjectContactRolesJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncProjectContactRolesJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        // Organizations
        $test = DB::table('jobs')->where('payload','like','%SyncOrganizationsJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncOrganizationsJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        // Projects
        $test = DB::table('jobs')->where('payload','like','%SyncProjectsJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncProjectsJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        // Amenities
        $test = DB::table('jobs')->where('payload','like','%SyncAmenityTypesJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncAmenityTypesJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        // Programs
        $test = DB::table('jobs')->where('payload','like','%SyncProgramsJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncProgramsJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }
        
        // SyncProjectProgramStatusTypesJob
        $test = DB::table('jobs')->where('payload','like','%SyncProjectProgramStatusTypesJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncProjectProgramStatusTypesJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        // SyncFinancialTypesJob
        $test = DB::table('jobs')->where('payload','like','%SyncFinancialTypesJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncFinancialTypesJob)->everyMinute();
            
        } else {
            //Log::info('Sync Job Already Started.');
        }

        //SyncProgramDateTypesJob
        $test = DB::table('jobs')->where('payload','like','%SyncFinancialTypesJob%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncFinancialTypesJob)->everyMinute();
            
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
