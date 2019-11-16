<?php

namespace App\Console;

use App\Jobs\ComplianceProjectionJob;
use App\Jobs\SyncAddressesJob;
use App\Jobs\SyncAmenityTypesJob;
use App\Jobs\SyncBuildingAmenitiesJob;
use App\Jobs\SyncBuildingsJob;
use App\Jobs\SyncComplianceContactJob;
use App\Jobs\SyncEmailAddressesJob;
use App\Jobs\SyncEmailAddressTypesJob;
use App\Jobs\SyncEventTypesJob;
use App\Jobs\SyncFederalMinimumSetAsidesJob;
use App\Jobs\SyncFinancialTypesJob;
use App\Jobs\SyncHouseholdEventsJob;
use App\Jobs\SyncHouseHoldSizesJob;
use App\Jobs\SyncHouseholdsJob;
use App\Jobs\SyncIdsJob;
use App\Jobs\SyncMonitoringMonitorsJob;
use App\Jobs\SyncMonitoringsJob;
use App\Jobs\SyncMonitoringStatusTypesJob;
use App\Jobs\SyncMultipleBuildingTypesJob;
use App\Jobs\SyncOrganizationsJob;
use App\Jobs\SyncOwnerCertificationYearsJob;
use App\Jobs\SyncPeopleJob;
use App\Jobs\SyncPercentagesJob;
use App\Jobs\SyncPhoneNumbersJob;
use App\Jobs\SyncPhoneNumberTypesJob;
use App\Jobs\SyncProgramDateTypesJob;
use App\Jobs\SyncProgramsJob;
use App\Jobs\SyncProjectActivitiesJob;
use App\Jobs\SyncProjectActivityTypesJob;
use App\Jobs\SyncProjectAmenitiesJob;
use App\Jobs\SyncProjectContactRolesJob;
use App\Jobs\SyncProjectDatesJob;
use App\Jobs\SyncProjectFinancialsJob;
use App\Jobs\SyncProjectProgramsJob;
use App\Jobs\SyncProjectProgramStatusTypesJob;
use App\Jobs\SyncProjectRolesJob;
use App\Jobs\SyncProjectsJob;
use App\Jobs\SyncRentalAssistanceSourcesJob;
use App\Jobs\SyncRentalAssistanceTypesJob;
use App\Jobs\SyncSpecialNeedsJob;
use App\Jobs\SyncUnitAmenitiesJob;
use App\Jobs\SyncUnitBedroomsJob;
use App\Jobs\SyncUnitIdentitiesJob;
use App\Jobs\SyncUnitsJob;
use App\Jobs\SyncUnitStatusJob;
use App\Jobs\SyncUsersJob;
use App\Jobs\SyncUtilityAllowancesJob;
use App\Jobs\SyncUtilityAllowanceTypesJob;
use App\Jobs\update_audit_caches;
use App\Models\Projection;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

        Commands\MakeTestFriendlyCommand::class,
        Commands\SendNotificationsHourly::class,
        Commands\AssetVersionUpdateCommand::class,
    ];

    protected $middleware = [

        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        \RenatoMarinho\LaravelPageSpeed\Middleware\InlineCss::class,
        \RenatoMarinho\LaravelPageSpeed\Middleware\ElideAttributes::class,
        \RenatoMarinho\LaravelPageSpeed\Middleware\InsertDNSPrefetch::class,
        \RenatoMarinho\LaravelPageSpeed\Middleware\RemoveComments::class,
        \RenatoMarinho\LaravelPageSpeed\Middleware\TrimUrls::class,
        \RenatoMarinho\LaravelPageSpeed\Middleware\RemoveQuotes::class,
        \RenatoMarinho\LaravelPageSpeed\Middleware\CollapseWhitespace::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $seconds = 5;
        /// laravel's shortest span is 1 minute - we want to check our audit caches every 5 seconds.
        $schedule->call(function () use ($seconds) {
            $dt = Carbon::now();

            $x = 60 / $seconds;

            do {
                $check = \DB::table('jobs')->where('queue', 'cache_update')->count();

                if ($check < 1) {
                    update_audit_caches::dispatch()->onQueue('cache_update');
                }

                time_sleep_until($dt->addSeconds($seconds)->timestamp);
            } while ($x-- > 0);
        })->everyMinute();

        /////////////////
        ////// SYNC JOBS
        ////

        if (! env('APP_DEBUG_NO_DEVCO') && (intval(date('G', time())) < 22 || intval(date('G', time()) > 3))) {

            // it is befor 10:00 pm and after 3 am -- backup time.

            $test = DB::table('jobs')->where('payload', 'like', '%SyncAddresses%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncAddressesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // Monitoring Status Types
            $test = DB::table('jobs')->where('payload', 'like', '%SyncMonitoringStatusTypes%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncMonitoringStatusTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // People
            $test = DB::table('jobs')->where('payload', 'like', '%SyncPeopleJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncPeopleJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // Project Activities
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProjectActivitiesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProjectActivitiesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // Project Activity Types
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProjectActivityTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProjectActivityTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // Project Roles
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProjectRolesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProjectRolesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // Project Contact Roles
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProjectContactRolesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProjectContactRolesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // Organizations
            $test = DB::table('jobs')->where('payload', 'like', '%SyncOrganizationsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncOrganizationsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // Projects
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProjectsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProjectsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // Amenities
            $test = DB::table('jobs')->where('payload', 'like', '%SyncAmenityTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncAmenityTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // Programs
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProgramsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProgramsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // SyncProjectProgramStatusTypesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProjectProgramStatusTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProjectProgramStatusTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // SyncFinancialTypesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncFinancialTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncFinancialTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncProgramDateTypesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProgramDateTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProgramDateTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // SyncMultipleBuildingTypesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncMultipleBuildingTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncMultipleBuildingTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // SyncPercentages
            $test = DB::table('jobs')->where('payload', 'like', '%SyncPercentagesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncPercentagesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // SyncFederalMinimumSetAsidesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncFederalMinimumSetAsidesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncFederalMinimumSetAsidesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncUnitStatusJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncUnitStatusJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncUnitStatusJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }
            //SyncUnitsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncUnitsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncUnitsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncUnitBedroomsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncUnitBedroomsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncUnitBedroomsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncHouseholdEventsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncHouseholdEventsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncHouseholdEventsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncOwnerCertificationYearsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncOwnerCertificationYearsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncOwnerCertificationYearsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncHouseholdsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncHouseholdsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncHouseholdsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }
            //SyncEventTypesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncEventTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncEventTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncRentalAssistanceSourcesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncRentalAssistanceSourcesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncRentalAssistanceSourcesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncRentalAssistanceTypesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncRentalAssistanceTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncRentalAssistanceTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncUtilityAllowancesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncUtilityAllowancesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncUtilityAllowancesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncMonitoringsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncMonitoringsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncMonitoringsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncProjectAmenitiesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProjectAmenitiesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProjectAmenitiesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncProjectFinancialsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProjectFinancialsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProjectFinancialsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncProjectProgramsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProjectProgramsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProjectProgramsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncUtilityAllowanceTypesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncUtilityAllowanceTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncUtilityAllowanceTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncSpecialNeedsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncSpecialNeedsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncSpecialNeedsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncMonitoringMonitorsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncMonitoringMonitorsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncMonitoringMonitorsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncBuildingsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncBuildingsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncBuildingsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncPhoneNumbersJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncPhoneNumbersJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncPhoneNumbersJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncUsersJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncUsersJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncUsersJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncComplianceContactJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncComplianceContactJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncComplianceContactJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncPhoneNumberTypesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncPhoneNumberTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncPhoneNumberTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncEmailAddressTypesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncEmailAddressTypesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncEmailAddressTypesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }
            //SyncEmailAddressesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncEmailAddressesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncEmailAddressesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncBuildingAmenitiesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncBuildingAmenitiesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncBuildingAmenitiesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }
            //SyncUnitAmenitiesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncUnitAmenitiesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncUnitAmenitiesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }
            //SyncHouseHoldSizesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncHouseHoldSizesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncHouseHoldSizesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncProjectDatesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncProjectDatesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncProjectDatesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }
            //SyncUnitIdentitiesJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncUnitIdentitiesJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncUnitIdentitiesJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            //SyncIdsJob
            $test = DB::table('jobs')->where('payload', 'like', '%SyncIdsJob%')->first();
            if (is_null($test)) {
                $schedule->job(new SyncIdsJob)->everyMinute();
            } else {
                //Log::info('Sync Job Already Started.');
            }

            // //RunProjections
            // $test = DB::table('jobs')->where('payload', 'like', '%ComplianceProjectionJob%')->first();
            // if (is_null($test)) {
            //     $planning = Projection::where('run',0)->first();

            //         $schedule->job(new ComplianceProjectionJob, 'compliance')->everyMinute();

            // } else {
            //     //Log::info('Sync Job Already Started.');
            // }
        }

        //Email scheduling every hour
        $schedule->command('run:hourly_notifications')->hourly();
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
