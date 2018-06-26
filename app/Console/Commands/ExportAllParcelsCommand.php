<?php

namespace App\Console\Commands;

use App\Jobs\ParcelsExportJob;
use Illuminate\Console\Command;

/**
 * ExportAllParcels Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class ExportAllParcelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:parcels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all parcels in a xls file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->info('Exporting all parcels will take a few minutes. An email will be sent when ready.');
        $job = new ParcelsExportJob();
        dispatch($job);
    }
}
