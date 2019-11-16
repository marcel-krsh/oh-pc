<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

class findMissingUnits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'findMissing:units';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $projects = Project::get()->all();
        $zeroCount = 0;
        $lessCount = 0;
        $moreCount = 0;
        $exactCount = 0;
        foreach ($projects as $project) {
            if ($project->total_unit_count != count($project->units)) {
                if (count($project->units) == 0) {
                    $zeroCount++;
                }
                if ($project->total_unit_count > count($project->units)) {
                    $lessCount++;
                }
                if ($project->total_unit_count < count($project->units)) {
                    $moreCount++;
                }
                //$this->info('Project '.$project->project_number.' info says it has '.$project->total_unit_count.' units, but there are '.count($project->units).' units in the database.'.PHP_EOL);
            } else {
                $exactCount++;
            }
            // code...
        }
        $this->info('Projects with matching units to Project Info Count: '.$exactCount.PHP_EOL.'Projects with less units than the Project Info Count: '.$lessCount.PHP_EOL.'Projects with more units more than Project Info Count: '.$moreCount);
    }
}
