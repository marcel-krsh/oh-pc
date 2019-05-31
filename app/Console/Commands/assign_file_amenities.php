<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class assign_file_amenities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign_file_amenities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add file amenities to units';

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
        $projects = \App\Models\Project::get();

        forEach($projects as $project){

            $project_id = $project->id;
            $units = $project->units();

             $processBar = $this->output->createProgressBar(count($units));
           
            forEach($units as $unit){
                
                $unit_id = $unit->id;
                
                $amenities = Amenity::where("file", 1 )->where('default',1)->get();

                
                    $amenity_id = null;
                    // $toplevel = $request->get('toplevel');

                    $new_amenities = $amenities;

                    $user = null;

                    if (null !== $audit && $new_amenities !== null) {
                        foreach ($new_amenities as $new_amenity) {

                            // get amenity type
                            $amenity_type = Amenity::where("id", "=", $new_amenity->amenity_id)->first();

                            $name = $amenity_type->amenity_description;

                            // save new amenity
                            if ($unit_id) {

                                $check = 0;

                                $check = UnitAmenity::where('unit_id',$unit_id)->where('amenity_id',$amenity_type->id)->count();

                                if(!$check){
                                    $unitamenity = new UnitAmenity([
                                        'unit_id' => $unit_id,
                                        'amenity_id' => $amenity_type->id,
                                        'comment' => 'manually added by ' . Auth::user()->id,
                                    ]);
                                    $unitamenity->save();
                                }

                               

                            }

                            // project level amenities are handled through OrderingBuilding and CachedBuilding
                        }// end for each new amenity
                    }// end null check
                
            }// end for each unit
        }// end for each project
    }// end handle
}
