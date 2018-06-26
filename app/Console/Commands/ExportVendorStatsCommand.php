<?php

namespace App\Console\Commands;

use App\Jobs\VendorStatsExportJob;
use App\Program;
use Illuminate\Console\Command;

/**
 * ExportVendorStats Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class ExportVendorStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:vendorstats {--program=0} {--csv=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export vendor stats in a xls file.';

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
        $this->info('Exporting vendor stats will take a few minutes. An email will be sent when ready.');
        if ($this->option('program')) {
            $this->info('You selected program '.$this->option('program'));
            $job = new VendorStatsExportJob(null, null, $this->option('program'), $this->option('csv'));
            dispatch($job);
        } else {
            $this->info('You selected all programs');
            $programs = Program::where('id', '!=', 1)->get();
            $date = date("m-d-Y_g-i-s_a", time());
            foreach ($programs as $program) {
                $name = str_replace(' ', '_', $program->program_name);
                $job = new VendorStatsExportJob(null, null, $program->id, 1, $date);
                dispatch($job);
            }
        }
    }
}
