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
        $test = DB::table('jobs')->where('payload','like','%SyncDevco%')->first();
        if( is_null($test)) {
            Log::info('Count is '.DB::table('jobs')->where('payload','like','%App\\Jobs\\SyncDevco%')->count());      
            $schedule->job(new SyncDevco)->everyMinute();
        } else {
            Log::info('Sync Job Already Started. '.$test);
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
