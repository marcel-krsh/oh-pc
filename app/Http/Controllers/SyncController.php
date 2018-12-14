<?php

namespace App\Http\Controllers;
use DB;
use DateTime;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Log;

use App\Models\Project;
use App\Models\Building;
use App\Models\Program;
use App\Models\UnitProgram;
use App\Models\Audit;




class SyncController extends Controller
{
    //
    public function sync() {
        //////////////////////////////////////////////////
        /////// Get the project for audit
        /////
        ///// bring your own audit
        $audit = Audit::where('development_key',247660)->orderBY('start_date','desc')->first();

        // remove any current programs on units for this audit

        UnitProgram::where('audit_id',$audit->id)->delete();
        
        $apiConnect = new DevcoService();
        // paths to the info we need: dd($audit, $audit->project, $audit->project->buildings);

        // Get all the units we need to get programs for:

        $buildings = $audit->project->buildings;
        if(!is_null($buildings)){
        //Process each building
            foreach ($buildings as $building) {
                //Get the building's units
                $buildingUnits = $building->units;

                if(!is_null($buildingUnits)){
                // Process each unit
                    foreach ($buildingUnits as $unit) {
                        // Get the unit's current program designation from DevCo
                        try{
                            $unitProgramData = $apiConnect->getUnitPrograms($unit->unit_key, 1,'admin@allita.org', 'Updating Unit Program Data', 1, 'Server');
                            $unitProgramData = json_decode($unitProgramData, true);
                            //dd($unitProgramData['data']);
                            //dd($unitProgramData['data'][0]['attributes']['programKey']);
                            foreach ($unitProgramData['data'] as $unitProgram) {
                               //dd('Unit Program Id - '.$unitProgram['attributes']['programKey']);

                                $program = Program::where('program_key',$unitProgram['attributes']['programKey'])->first();
                                if(!is_null($program)){
                                    UnitProgram::insert([
                                        'development_key'   =>  $audit->development_key,
                                        'project_id'        =>  $audit->project_id,
                                        'unit_key'          =>  $unit->unit_key,
                                        'unit_id'           =>  $unit->id,
                                        'program_key'       =>  $program->program_key,
                                        'program_id'        =>  $program->id,
                                        'audit_id'          =>  $audit->id,
                                        'monitoring_key'    =>  $audit->monitoring_key,
                                        'created_at'        =>  date("Y-m-d H:i:s", time()),
                                        'updated_at'        =>  date("Y-m-d H:i:s", time())
                                    ]);
                                }else{
                                   Log::info('Unable to find program with key of '.$unitProgram['attributes']['programKey'].' on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key); 
                                }
                            }
                        
                        } catch(Exception $e){
                            Log::info('Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key);
                        }
                    }
                }
            }
        }
    }
}
