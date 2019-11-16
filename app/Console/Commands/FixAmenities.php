<?php

namespace App\Console\Commands;

use App\Models\Amenity;
use App\Models\Building;
use App\Models\BuildingAmenity;
use App\Models\Project;
use App\Models\ProjectAmenity;
use App\Models\UnitAmenity;
use App\Modles\Unit;
use Illuminate\Console\Command;

class FixAmenities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:amenities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the amenities assignment.';

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
        // Get all the audits first:
        $projects = Project::get()->all();
        $this->info(PHP_EOL.'Processing '.count($projects).' Projects'.PHP_EOL);

        foreach ($projects as $project) {
            if (count($project->amenities) > 0) {
                $this->info('Project ID '.$project->id.' has '.count($project->amenities).' Project Amenities'.PHP_EOL);
                $this->info(count($project->buildings).' Project Buildings with '.count($project->units).' Units total'.PHP_EOL);

                $processBar = $this->output->createProgressBar(count($project->amenities));

                $previousPaId = 0;
                foreach ($project->amenities as $pa) {

                   //
                    // if($previousPaId == $pa->amenity->id){
                    //     // determining if there are multiples of these in the DB or not
                    //     $this->error('DUPLICATE ENTRY - ALLOW DUPES'.PHP_EOL);
                    // }
                    // $previousPaId = $pa->amenity->id;
                    if ($pa->amenity->unit) {
                        foreach ($project->units as $unit) {
                            UnitAmenity::create([
                            'unit_id'=>$unit->id,
                            'amenity_id'=>$pa->amenity->id,
                            'comment' =>$pa->comment,
                            ]);
                        }

                        //$this->info('Add to Units'.PHP_EOL);
                    }
                    if ($pa->amenity->building_system || $pa->amenity->building_exterior) {
                        //$this->info('Add to Buildings'.PHP_EOL);
                        foreach ($project->buildings as $building) {
                            BuildingAmenity::create([
                            'building_id'=>$building->id,
                            'amenity_id'=>$pa->amenity->id,
                            'comment' =>$pa->comment,
                            ]);
                        }
                    }
                    if ($pa->amenity->project) {
                        //$this->info('Keep on Project'.PHP_EOL);
                    } else {
                        $pa->update(['deleted_at'=>date('Y-m-d H:i:s', time())]);
                        //$this->info('Remove from Project'.PHP_EOL);
                    }

                    $processBar->advance();
                }

                $processBar->finish();
                $this->info(PHP_EOL.'==================================================='.PHP_EOL);
            }
        }
    }
}
