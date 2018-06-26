<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Disposition;
use DB;

/**
 * UpdateStatusesDispositions Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class UpdateStatusesDispositionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:statuses-dispositions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update disposition statuses using logic.';

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
        session(['dispositionTotal' => Disposition::count()]);
        $this->line('Running disposition checks on '.session('dispositionTotal').' dispositions.'.PHP_EOL);
        session(['progressCount' => 0 ]);
        Disposition::chunk(500, function ($dispositions) {
            $start = session('progressCount') + 1;
            $current = session('progressCount')+ count($dispositions);
            session(['progressCount' => $current]);
            $this->line(PHP_EOL.'Chunking Dispositions Checks '.$start.' through '.$current.' of '.session('dispositionTotal').PHP_EOL);
            
            $dispositionbar = $this->output->createProgressBar(count($dispositions));
            foreach ($dispositions as $data) {
                // run parcel check
                perform_all_disposition_checks($data, 1); // 1 is for no email to prevent emails to be sets to all LB during checks
                guide_next_pending_step(1, $data->id); // update next step

                // Update steps based on current status of items
                $dispositionbar->advance();
            }
            $dispositionbar->finish();
        });
    }
}
