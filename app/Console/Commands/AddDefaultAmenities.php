<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\UnitAmenity;

use Illuminate\Console\Command;

class AddDefaultAmenities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addDefault:amenities';

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
        // Get all the audits first:
        $projects = Project::get()->all();
        $this->info(PHP_EOL.'Processing '.count($projects).' Projects'.PHP_EOL);
        

        foreach ($projects as $project) {
            if(count($project->units)>0){
                $this->info('Project ID '.$project->id.' has '.count($project->amenities).' Project Amenities'.PHP_EOL);
                $this->info(count($project->buildings).' Project Buildings with '.count($project->units).' Units total'.PHP_EOL);
            
                $processBar = $this->output->createProgressBar(count($project->units));

                foreach ($project->units as $unit) {
                    //dd($unit->bedroomCount());
                    // Bedrooms
                    $bedroomsAdded = 0;
                    do{
                        $bedroomsAdded++;
                        UnitAmenity::create(
                            [
                            'unit_id'=>$unit->id,
                            'amenity_id'=>'393',
                            'comment' =>'Allita automatically added  bedroom '.$bedreoomsAdded.' based on the total '.$unit->bedroomCount().' bedrooms listed for this unit.'
                            ]
                        );

                    }while($bedroomsAdded < $unit->bedroomCount());

                    // Kitchen
                    UnitAmenity::create(
                            [
                            'unit_id'=>$unit->id,
                            'amenity_id'=>'394',
                            'comment' =>'Allita automatically added this kitchen assuming all units have one.'
                            ]
                        );

                    // Bathroom
                    UnitAmenity::create(
                            [
                            'unit_id'=>$unit->id,
                            'amenity_id'=>'397',
                            'comment' =>'Allita automatically added this bathroom assuming all units have at least one.'
                            ]
                        );
                       
                    }
                    

                    
                    $processBar->advance();
                    $processBar->finish();
                    $this->info(PHP_EOL.'==================================================='.PHP_EOL);
                }
                
                
            }

            
        }
    
    
}
