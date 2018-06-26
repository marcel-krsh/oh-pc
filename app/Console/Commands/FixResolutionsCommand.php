<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ValidationResolutions;
use App\Parcel;
use DB;

/**
 * FixResolutions Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class FixResolutionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:resolutions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix orphaned resolutions. ';

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
        $resolutions = ValidationResolutions::get();
        $bar = $this->output->createProgressBar(count($resolutions));
        foreach ($resolutions as $resolution) {
            $bar->advance();
            $parcel = Parcel::where('id', $resolution->parcel_id)->count();
            if ($parcel < 1) {
                //try and find the parcel based on the resolution_id
                $parcel = Parcel::where('id', $resolution->resolution_id)->count();
            }
            if ($parcel < 1) {
                $this->line('Orphaned Resolution Found'.PHP_EOL);
                $resolution->delete();
                $this->line('Resolution '.$resolution->id.' deleted.'.PHP_EOL);
            }
        }
    }
}
