<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parcel;
use DB;

/**
 * UpdateNextStep Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class UpdateNextStepCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:parcelsnextstep';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update next steps.';

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
        session(['parcelTotal' => Parcel::count()]);
        $this->line('Running parcel checks on '.session('parcelTotal').' parcels.'.PHP_EOL);
        session(['progressCount' => 0 ]);
        Parcel::chunk(100, function ($parcels) {
            $start = session('progressCount') + 1;
            $current = session('progressCount')+ count($parcels);
            session(['progressCount' => $current]);
            $this->line(PHP_EOL.'Chunking Parcel Checks '.$start.' through '.$current.' of '.session('parcelTotal').PHP_EOL);
            
            $parcelbar = $this->output->createProgressBar(count($parcels));
            foreach ($parcels as $data) {
                // run parcel check
                //perform_all_parcel_checks($data);

                // find out next step and cache it in db
                guide_next_pending_step(2, $data->id);

                // Update steps based on current status of items
                $parcelbar->advance();
            }
            $parcelbar->finish();
        });
    }
}
