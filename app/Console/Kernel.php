<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AssignVendorsCommand::class,
        Commands\ExportAllParcelsCommand::class,
        Commands\ExportVendorStatsCommand::class,
        Commands\FindOrphansCommand::class,
        Commands\FixAdvanceAmountsCommand::class,
        Commands\FixAmountsCommand::class,
        Commands\FixGreeningCommand::class,
        Commands\FixMapLinksCommand::class,
        Commands\FixMessagesCommand::class,
        Commands\FixOrphansCommand::class,
        Commands\FixResolutionsCommand::class,
        Commands\ImportSFCommand::class,
        Commands\MakeTestFriendlyCommand::class,
        Commands\ProcessPDFsCommand::class,
        Commands\UpdateDatesCommand::class,
        Commands\UpdateDispositionItemsCommand::class,
        Commands\UpdateInvoicePaymentDataCacheCommand::class,
        Commands\UpdateNextStepCommand::class,
        Commands\UpdateRetainageDocumentsCommand::class,
        Commands\UpdateSiteVisitsCommand::class,
        Commands\UpdateStatusesCommand::class,
        Commands\UpdateStatusesDispositionsCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // run export all parcels quarterly
        $schedule->command('export:parcels')->quarterly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
