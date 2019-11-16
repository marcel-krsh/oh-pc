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

        foreach ($projects as $project) {
            $project_id = $project->id;
            $units = $project->units;

            $processBar = $this->output->createProgressBar(count($units));

            foreach ($units as $unit) {
                $unit_id = $unit->id;

                $amenities = \App\Models\Amenity::where('file', 1)->where('unit_default', 1)->get();

                $amenity_id = null;
                // $toplevel = $request->get('toplevel');

                $user = null;

                if ($amenities !== null) {
                    foreach ($amenities as $amenity_type) {
                        $name = $amenity_type->amenity_description;

                        // save new amenity
                        if ($unit_id) {
                            $check = 0;

                            $check = \App\Models\UnitAmenity::where('unit_id', $unit_id)->where('amenity_id', $amenity_type->id)->count();

                            if (! $check) {
                                $unitamenity = new \App\Models\UnitAmenity([
                                        'unit_id' => $unit_id,
                                        'amenity_id' => $amenity_type->id,
                                        'comment' => 'Added by system.',
                                    ]);
                                $unitamenity->save();
                            } else {
                                $this->line('unit_id:'.$unit->id.' has one of '.$amenity_type->id.PHP_EOL);
                            }
                        }
                        // project level amenities are handled through OrderingBuilding and CachedBuilding
                    }// end for each new amenity
                }// end null check
               $processBar->advance();
            }// end for each unit
        }// end for each project
    }

    // end handle
}
