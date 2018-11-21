<?php

namespace App\Console;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use App\Jobs\SyncDevco;
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
        //$schedule->command('queue:listen')->everyMinute();

        $test = DB::table('jobs')->where('payload','like','%SyncAddresses%')->first();
        if(is_null($test)) {
            $schedule->job(new SyncAddresses)->everyMinute();
            
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
