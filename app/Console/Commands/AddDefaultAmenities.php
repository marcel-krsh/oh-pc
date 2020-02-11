<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\UnitAmenity;
use App\Models\BuildingAmenity;
use App\Models\ProjectAmenity;

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

     public function getProject(){
        $projectInput = $this->ask('Createe default amenities for which project id?');
        $project = Project::find($projectInput);
        if($project){
            
            $this->line(PHP_EOL.$project->project_number.': '.$project->project_name);
            if($this->confirm('Is that the project you wanted?')){
                return $project;
            } else {
                $this->line(PHP_EOL.'Sorry about that --- please try again with a different project number.');
                return null;
            }
        }else{
            $this->line(PHP_EOL.'Sorry I could not find an project matching that number:'.$projectInput);
            return null;
        }
    }

    public function handle()
    {
        //
        // Get all the projects first:
       

        $project = $this->getProject();
        
        if($project !== null){
        $settings = \App\Models\SystemSetting::where('key','bedroom_amenity_id')->first();
        $bedroomId = $settings->value;
        // get all the default amenities 
        $defaultUnitAmenities = \App\Models\Amenity::where('id','<>',$bedroomId)->where('unit_default',1)->get()->all();
        $defaultBuildingAmenities = \App\Models\Amenity::where('building_default',1)->get()->all();
        $defaultProjectAmenities = \App\Models\Amenity::where('project_default',1)->get()->all();

        //dd($defaultProjectAmenities,count($defaultBuildingAmenities),count($defaultUnitAmenities));

        
        
            if(count($project->units)>0){
                $this->line('Project ID '.$project->id.' has '.count($project->amenities).' Project Amenities'.PHP_EOL);
                $this->line(count($project->buildings).' Project Buildings with '.count($project->units).' Units total'.PHP_EOL);
            
                $processBar = $this->output->createProgressBar(count($project->units));

                foreach ($project->units as $unit) {
                    //dd($unit->bedroomCount());
                    // Bedrooms
                    $setBedRoomCount = $unit->bedroomCount();
                    $actualBedroomCount = UnitAmenity::where('unit_id',$unit->id)->where('amenity_id',$bedroomId)->count();
                    $neededBedrooms = $setBedRoomCount - $actualBedroomCount;

                    if($neededBedrooms > 0){
                        $bedroomsAdded = 0;
                        do{
                            $bedroomsAdded++;
                            UnitAmenity::create(
                                [
                                'unit_id'=>$unit->id,
                                'amenity_id'=>$bedroomId,
                                'comment' =>'Allita automatically added  bedroom '.$bedroomsAdded.' based on the total '.$unit->bedroomCount().' bedrooms listed for this unit.'
                                ]
                            );
                            $this->line(PHP_EOL.'Allita automatically added  bedroom '.$bedroomsAdded.' based on the total '.$unit->bedroomCount().' bedrooms listed for this unit.');

                        }while($bedroomsAdded < $neededBedrooms);
                        //$this->info(PHP_EOL.$project->id.' had '.$bedroomsAdded. 'bedrooms added.');
                    }


                    $unitAmenities = $defaultUnitAmenities;

                    foreach ($unitAmenities as $ua) {
                       $check = UnitAmenity::where('unit_id',$unit->id)->where('amenity_id',$ua->id)->count();
                        if($check < 1){
                           UnitAmenity::create(
                                [
                                'unit_id'=>$unit->id,
                                'amenity_id'=>$ua->id,
                                'comment' =>'Allita automatically added this '.$ua->name.' assuming all units have one.'
                                ]
                            );
                           $this->line(PHP_EOL.'Added amenity '.$ua->name.' to unit '.$unit->unit_name.' from unit default list.');
                       }
                    }
                    
                    

                    
                    $processBar->advance();
                   
                } 
                $processBar->finish();
                    
                // add building amenities
                $processBar = $this->output->createProgressBar(count($project->buildings));
            }
            if(count($project->buildings) > 0){
                foreach($project->buildings as $b){
                    $bas = $defaultBuildingAmenities;
                    foreach ($bas as $ba) {
                        $check = BuildingAmenity::where('building_id',$b->id)->where('amenity_id',$ba->id)->count();
                        if($check < 1){
                            BuildingAmenity::create([
                                'building_id'=>$b->id,
                                'amenity_id'=>$ba->id,
                                'comment' =>'Allita automatically added this '.$ua->name.' assuming all buildings have one.'
                            ]);
                            $this->line(PHP_EOL.'Added amenity '.$ua->name.' to building '.$b->building_name.' from building default list.');
                        }
                    }
                    $processBar->advance();

                }
                $processBar->finish();
            }
                // add project amenities
                $pas = $defaultProjectAmenities;
                    $processBar = $this->output->createProgressBar(count($defaultProjectAmenities));
                    foreach ($pas as $pa) {
                        // make sure it isn't on the project already
                        $check = ProjectAmenity::where('project_id',$project->id)->where('amenity_id',$pa->id)->count();
                        if($check < 1){
                            ProjectAmenity::create([
                                'project_id'=>$project->id,
                                'amenity_id'=>$pa->id,
                                'comment' =>'Allita automatically added this '.$pa->name.' assuming all projects have one.'
                            ]);
                            $this->line(PHP_EOL.'Added amenity '.$pa->name.' to the site level, from site default list.')
                        }
                        $processBar->advance();
                    }
                    
                    $processBar->finish();
                $this->info(PHP_EOL.'==================================================='.PHP_EOL);
                
            }
        }

            
        
    
    
}
