<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use App\Jobs\ComplianceSelectionJob;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Audit;
use App\Models\Unit;
use App\Models\Project;
use App\Models\UnitInspection;
use App\Models\Organization;
use App\Models\BuildingInspection;
use App\Models\ProjectContactRole;
use App\Models\Planning;
use App\Models\Program;
use App\Services\DevcoService;
use App\Models\UnitProgram;
use App\Models\UnitGroup;
use App\Models\OrderingBuilding;
use App\Models\OrderingUnit;
use Illuminate\Support\Facades\Redis;
use App\Models\AmenityInspection;
use App\Models\ProjectProgram;
use Auth;
use DB;

class ComplianceProjectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $audit;
    public $planning;
    public $project;

    public function __construct()
    {
        //make a new audit for this

        // $test = DB::table('jobs')->where('payload', 'like', '%ComplianceProjectionJob%')->first();
        // if (!is_null($test)) {

            $this->audit = null;
            $this->planning = null;
            $this->project = null;
        
       //}



        
    }

    public function fetchAuditUnits(Audit $audit)
    {
        
                                    
        UnitProgram::where('audit_id', $audit->id)->delete();
        
        
        $apiConnect = new DevcoService();
        // paths to the info we need: dd($audit, $audit->project, $audit->project->buildings);
        

        // Get all the units we need to get programs for:

        $buildings = $audit->project->buildings;
        if (!is_null($buildings)) {
        //Process each building
            foreach ($buildings as $building) {
                
                //Get the building's units
                $buildingUnits = $building->units;

                if (!is_null($buildingUnits)) {
                // Process each unit
                    foreach ($buildingUnits as $unit) {
                        
                        // Get the unit's current program designation from DevCo
                        try {
                            $unitProjectPrograms = $apiConnect->getUnitProjectPrograms($unit->unit_key, 1, 'admin@allita.org', 'Updating Unit Program Data', 1, 'SystemServer');
                            $projectPrograms = json_decode($unitProjectPrograms);
                            $projectPrograms =  $projectPrograms->data;

                            if($unit->is_market_rate()){
                                $is_market_rate = 1; 
                            }else{
                                $is_market_rate = 0;
                            }

                            //$records_to_insert = array();

                            //$unitProgramData = $apiConnect->getUnitPrograms($unit->unit_key, 1, 'admin@allita.org', 'Updating Unit Program Data', 1, 'Server');
                            //$unitProgramData = json_decode($unitProgramData, true);
                            //
                            //dd($unitProgramData['data']);
                            //dd($unitProgramData['data'][0]['attributes']['programKey']);
                            foreach ($projectPrograms as $pp) {
                                

                                $pp = $pp->attributes;
                                if(is_null($pp->endDate) && !$is_market_rate){
                                    
                                    $audit->comment = $audit->comment.' | Unit Key:'.$pp->unitKey.', Development Program Key:'.$pp->developmentProgramKey.', Start Date:'.date('m/d/Y',strtotime($pp->startDate));
                                    $audit->comment_system = $audit->comment_system.' | Unit Key:'.$pp->unitKey.', Development Program Key:'.$pp->developmentProgramKey.', Start Date:'.date('m/d/Y',strtotime($pp->startDate));
                                    $audit->save();

                                    //get the matching program from the developmentProgramKey
                                    $program = ProjectProgram::where('project_program_key',$pp->developmentProgramKey)->with('program')->first();
                                    
                                    $audit->comment = $audit->comment.' | '.$program->program->program_name.' '.$program->program_id;
                                    $audit->comment_system = $audit->comment_system.' | '.$program->program->program_name.' '.$program->program_id;
                                    $audit->save();

                                    if (!is_null($program)) {
                                        UnitProgram::insert([
                                            'unit_key'      =>  $unit->unit_key,
                                            'unit_id'       =>  $unit->id,
                                            'program_key'   =>  $program->program_key,
                                            'program_id'    =>  $program->program_id,
                                            'audit_id'      =>  $audit->id,
                                            'monitoring_key'=>  $audit->monitoring_key,
                                            'project_id'    =>  $audit->project_id,
                                            'development_key'=> $audit->development_key,
                                            'created_at'    =>  date("Y-m-d g:h:i", time()),
                                            'updated_at'    =>  date("Y-m-d g:h:i", time()),
                                            'project_program_key' => $pp->developmentProgramKey,
                                            'project_program_id' => $program->id
                                        ]);

                                        if(count($program->program->groups())){
                                            foreach($program->program->groups() as $group){
                                                UnitGroup::insert([
                                                    'unit_key'      =>  $unit->unit_key,
                                                    'unit_id'       =>  $unit->id,
                                                    'group_id'      =>  $group,
                                                    'audit_id'      =>  $audit->id,
                                                    'monitoring_key'=>  $audit->monitoring_key,
                                                    'project_id'    =>  $audit->project_id,
                                                    'development_key'=> $audit->development_key,
                                                    'created_at'    =>  date("Y-m-d g:h:i", time()),
                                                    'updated_at'    =>  date("Y-m-d g:h:i", time())
                                                ]);
                                            }
                                        }
                                        
                                    } else {
                                        $audit->comment = $audit->comment.' | Unable to find program with key '.$pp->developmentProgramKey.' on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key;
                                        $audit->comment_system = $audit->comment_system.' | Unable to find program with key '.$pp->developmentProgramKey.' on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key;
                                        $audit->save();
                                        //Log::info('Unable to find program with key of '.$unitProgram['attributes']['programKey'].' on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key);
                                    }
                                } else {
                                    // market rate?
                                    $program = ProjectProgram::where('project_program_key',$pp->developmentProgramKey)->with('program')->first();
                                    if($is_market_rate){
                                        
                                        $audit->comment_system = $audit->comment_system." | MARKET RATE, CANCELLED:<del>".$program->program->program_name.' '.$program->program_id.'</del>, Start Date:'.date('m/d/Y',strtotime($pp->startDate)).', End Date: '.date('m/d/Y',strtotime($pp->endDate));
                                        $audit->save();
                                    }else{
                                        
                                        $audit->comment_system = $audit->comment_system." | CANCELLED:<del>".$program->program->program_name.' '.$program->program_id.'</del>, Start Date:'.date('m/d/Y',strtotime($pp->startDate)).', End Date: '.date('m/d/Y',strtotime($pp->endDate));
                                        $audit->save();
                                    }
                                    
                                }
                            }
                        } catch (Exception $e) {
                            
                            //dd('Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key);
                            $audit->comment = $audit->comment.' | Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key;
                            $audit->comment_system = $audit->comment_system.' | Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key;
                                    $audit->save();
                        }
                    }
                    $audit->comment_system = $audit->comment_system.' | Finished Loop of Units';
                                    $audit->save();
                                    
                } else {
                    //dd('Could not get building units');
                    $audit->comment = $audit->comment.' | Could not get building units';
                    $audit->comment_system = $audit->comment_system.' | Could not get building units';
                                    $audit->save();
                                    
                }
            }
            $audit->comment_system = $audit->comment_system.' | Returning 1 from Fetch Units';
                                    $audit->save();
                                    
            return 1;
        } else {
            //dd('NO BUILDINGS FOUND TO GET DATA');
            $audit->comment = $audit->comment.' | NO BUILDINGS FOUND TO GET DATA';
            $audit->comment_system = $audit->comment_system.' | NO BUILDINGS FOUND TO GET DATA';
                                    $audit->save();
                                    
        }
    }

    public function adjustedLimit($audit, $n)
    {
        $audit->comment = $audit->comment.' | Running Adjusted Limiter.';
                                    $audit->save();
                                    
        // based on $n units, return the corresponding adjusted sample size
        switch (true) {
            case ($n >= 1 && $n <=4):
                
                $audit->comment = $audit->comment.' | Limiter Count is >= 1 and <=4 - adjusted minimum is '.$n.' of '.$n.'.';
                $audit->save();
                
                return $n;
            break;
            case ($n == 5 || $n == 6):
                $audit->comment = $audit->comment.' | Limiter Count is = 5 or 6 - adjusted minimum is '.$n.' of '.$n.'.';
                $audit->save();
                
                return 5;
            break;
            case ($n == 7):
                
                $audit->comment = $audit->comment.' | Limiter Count is = 7 - adjusted minimum is 6 of 7.';
                $audit->save();
                
                return 6;
            break;
            case ($n == 8 || $n == 9):
                $audit->comment = $audit->comment.' | Limiter Count is = 8 or 9 - adjusted minimum is 7 of '.$n.'.';
                $audit->save();
                
                return 7;

            break;
            case ($n == 10 || $n == 11):
                $audit->comment = $audit->comment.' | Limiter Count is = 10 or 11 - adjusted minimum is 8 of '.$n.'.';
                $audit->save();
                
                return 8;
            break;
            case ($n == 12 || $n == 13):
                $audit->comment = $audit->comment.' | Limiter Count is = 12 or 13 - adjusted minimum is 9 of '.$n.'.';
                $audit->save();
                
                return 9;
            break;
            case ($n >= 14 && $n <= 16):
                $audit->comment = $audit->comment.' | Limiter Count is = 14 or up to 16 - adjusted minimum is 10 of '.$n.'.';
                $audit->save();
                
                return 10;
            break;
            case ($n >= 17 && $n <= 18):
                $audit->comment = $audit->comment.' | Limiter Count is = 17 or up to 18 - adjusted minimum is 11 of '.$n.'.';
                $audit->save();
                
                return 11;
            break;
            case ($n >= 19 && $n <= 21):
                $audit->comment = $audit->comment.' | Limiter Count is = 19 or up to 21 - adjusted minimum is 12 of '.$n.'.';
                $audit->save();
                
                return 12;
            break;
            case ($n >= 22 && $n <= 25):
                $audit->comment = $audit->comment.' | Limiter Count is = 22 or up to 25 - adjusted minimum is 13 of '.$n.'.';
                $audit->save();
                
                return 13;
            break;
            case ($n >= 26 && $n <= 29):
                $audit->comment = $audit->comment.' | Limiter Count is = 26 or up to 29 - adjusted minimum is 14 of '.$n.'.';
                $audit->save();
                
                return 14;
            break;
            case ($n >= 30 && $n <= 34):
                $audit->comment = $audit->comment.' | Limiter Count is = 30 or up to 34 - adjusted minimum is 15 of '.$n.'.';
                $audit->save();
                
                return 15;
            break;
            case ($n >= 35 && $n <= 40):
                $audit->comment = $audit->comment.' | Limiter Count is = 35 or up to 40 - adjusted minimum is 16 of '.$n.'.';
                $audit->save();
                
                return 16;
            break;
            case ($n >= 41 && $n <= 47):
                $audit->comment = $audit->comment.' | Limiter Count is = 41 or up to 47 - adjusted minimum is 17 of '.$n.'.';
                $audit->save();
                
                return 17;
            break;
            case ($n >= 48 && $n <= 56):
                $audit->comment = $audit->comment.' | Limiter Count is = 48 or up to 56 - adjusted minimum is 18 of '.$n.'.';
                $audit->save();
                
                return 18;
            break;
            case ($n >= 57 && $n <= 67):
                $audit->comment = $audit->comment.' | Limiter Count is = 57 or up to 67 - adjusted minimum is 19 of '.$n.'.';
                $audit->save();
                
                return 19;
            break;
            case ($n >= 68 && $n <= 81):
                $audit->comment = $audit->comment.' | Limiter Count is = 68 or up to 81 - adjusted minimum is 20 of '.$n.'.';
                $audit->save();
                
                return 20;
            break;
            case ($n >= 82 && $n <= 101):
                $audit->comment = $audit->comment.' | Limiter Count is = 82 or up to 101 - adjusted minimum is 21 of '.$n.'.';
                $audit->save();
                
                return 21;
            break;
            case ($n >= 102 && $n <= 130):
                $audit->comment = $audit->comment.' | Limiter Count is = 102 or up to 130 - adjusted minimum is 22 of '.$n.'.';
                $audit->save();
                
                return 22;
            break;
            case ($n >= 131 && $n <= 175):
                $audit->comment = $audit->comment.' | Limiter Count is = 131 or up to 175 - adjusted minimum is 23 of '.$n.'.';
                $audit->save();
                
                return 23;
            break;
            case ($n >= 176 && $n <= 257):
                $audit->comment = $audit->comment.' | Limiter Count is = 176 or up to 257 - adjusted minimum is 24 of '.$n.'.';
                $audit->save();
                
                return 24;
            break;
            case ($n >= 258 && $n <= 449):
                $audit->comment = $audit->comment.' | Limiter Count is = 258 or up to 449 - adjusted minimum is 25 of '.$n.'.';
                $audit->save();
                
                return 25;
            break;
            case ($n >= 450 && $n <= 1461):
                $audit->comment = $audit->comment.' | Limiter Count is = 450 or up to 1461 - adjusted minimum is 26 of '.$n.'.';
                $audit->save();
                
                return 26;
            break;
            case ($n >= 1462):
                $audit->comment = $audit->comment.' | Limiter Count is >= 1462 - adjusted minimum is 27 of '.$n.'.';
                $audit->save();
                
                return 27;
            break;
            default:
               
                
                 return 0;
        }
    }

    public function randomSelection($audit, $units, $percentage = 20, $min = 0, $max = 0)
    {
        $audit->comment = $audit->comment.' | Starting randomSelection.';
                $audit->save();
                
        if (count($units)) {
            $total = count($units);

            $needed = ceil($total * $percentage / 100);

            if($needed){
                $audit->comment = $audit->comment.' | Random selection calculated total '.$total.' versus '.$needed.' needed.';
                $audit->save();
            }

            if ($min > $total) {
                $min = $total;
            }
            if ($needed <= $min) {
                $needed = $min;
            }
            if($needed == 0){
                return [];
            }
            

            $audit->comment = $audit->comment.' | Random selection adjusted totals based on '.$percentage.'%: total '.$total.', min '.$min.' and '.$needed.' needed.';
                $audit->save();
                
            $output = [];

            if($needed == 1){
                $output_key = array_rand($units, $needed);
                $output[] = $units[$output_key];
            }else{
                $number_added = 0;
                foreach (array_rand($units, $needed) as $id) {
                    if($number_added < $max || $max == 0){
                        $output[] = $units[$id];
                        $number_added++;
                        
                    }
                }
            }
            
            $audit->comment = $audit->comment.' | Random selection randomized list and returning output to selection process.';
                $audit->save();
                

            return $output;
        } else {
            return [];
            
        }
    }

    public function combineOptimize($audit, $selection)
    {
        //dd($selection);
        // $adjusted_units_count = $this->adjustedLimit(count($units_selected)); dd($adjusted_units_count);
        // array_slice($input, 0, 3)
        // only applies to the first and the last set

        $summary = []; // for stats
        $output = []; // for units

        // create empty array to store ids and priorities
        
        //for each set, run intersect
        //for each intersect result increase priority in the id
        //once all intersects are done, reorder each set by priority
        //make the limited selection
        //combine and fetch all units
        //store units for each program id
        //and create stats
        //
        
        $priority = [];

        // run the intersects
        $array_to_compare = [];
        $array_to_compare_with = [];
        $intersect = [];
        $audit->comment = $audit->comment.' | Combine and optimize starting.';
        $audit->save();
        

        for ($i=0; $i < count($selection); $i++) {
            $array_to_compare = $selection[$i]['units'];
            

            for ($j=0; $j < count($selection); $j++) {
                if ($i != $j) {
                    $array_to_compare_with = $selection[$j]['units'];
                    
                    $intersects = array_intersect($array_to_compare, $array_to_compare_with);
                    

                    foreach ($intersects as $intersect) {
                        if (array_key_exists($intersect, $priority)) {
                            $priority[$intersect] = $priority[$intersect]+1;
                            
                        } else {
                            $priority[$intersect] = 1;
                            
                        }
                    }
                }
            }
        }
        $audit->comment = $audit->comment.' | Combine and optimize created priority table.';
                $audit->save();
                
       // now we have unit_keys in a priority table
        arsort($priority);
        $audit->comment = $audit->comment.' | Combine and optimize sorted the table by priority - highest overlap';
                $audit->save();
                
        for ($i=0; $i < count($selection); $i++) {
            $summary['programs'][$i]['name'] = $selection[$i]['program_name'];
            $summary['programs'][$i]['group'] = $selection[$i]['group_id'];
            $audit->comment = $audit->comment.' | DEBUG COMPLIANCE SELECTION LINE 348: Combine and optimize created the group $summary[\'programs\']['.$i.'][\'group\'] = '.($i + 1);
                $audit->save();
            $summary['programs'][$i]['pool'] = $selection[$i]['pool'];
            $summary['programs'][$i]['program_keys'] = $selection[$i]['program_ids'];
            $summary['programs'][$i]['totals_before_optimization'] = $selection[$i]['totals'];
            $summary['programs'][$i]['units_before_optimization'] = $selection[$i]['units'];
            $summary['programs'][$i]['required_units_file'] = $selection[$i]['required_units'];
            $summary['programs'][$i]['use_limiter'] = $selection[$i]['use_limiter'];
            $summary['programs'][$i]['comments'] = $selection[$i]['comments'];

            // to deal with multiple buildings - each building will have its own selection[$i] with the same group_id
            if(array_key_exists('building_key', $selection[$i])){
                $summary['programs'][$i]['building_key'] = $selection[$i]['building_key'];
            }else{
                $summary['programs'][$i]['building_key'] = '';
            }

            $tmp_selection = []; // used to store selection as we go through the priorities
            $tmp_program_output = []; // used to store the units selected for this program set
            

            $tmp_program_output_total_not_merged = 0;

            if ($selection[$i]['use_limiter'] == 1) {
                $audit->comment = $audit->comment.' | Combine and optimize used limiter on selection['.$i.'].';
                $audit->save();
                
                $needed = $this->adjustedLimit($audit,count($selection[$i]['units']));

                $summary['programs'][$i]['required_units'] = $needed;

                foreach ($priority as $p => $val) {
                    if (in_array($p, $selection[$i]['units']) && count($tmp_selection) < $needed) {
                        $tmp_selection[] = $p;
                    }
                    
                }

                // check if we need more
                if (count($tmp_selection) < $needed) {
                    $audit->comment = $audit->comment.' | Combine and optimize determined the '.count($tmp_selection).' temporary selection is < '.$needed.' needed.';
                    $audit->save();
                    
                    for ($j=0; $j<count($selection[$i]['units']); $j++) {
                        
                        if (!in_array($selection[$i]['units'][$j], $tmp_selection) && count($tmp_selection) < $needed) {
                            $tmp_selection[] = $selection[$i]['units'][$j];
                            $audit->comment = $audit->comment.' | Combine and optimize added $selection['.$i.'][\'units\']['.$j.'] to list.';
                            $audit->save();
                            
                        }
                    }
                    $audit->comment = $audit->comment.' | Combine and optimize finished adding to the list to meet compliance.';
                            $audit->save();
                            
                }

                $tmp_program_output = array_merge($tmp_program_output, $tmp_selection);
                $tmp_program_output_total_not_merged = $tmp_program_output_total_not_merged + count($tmp_selection);
                $output = array_merge($output, $tmp_selection);
                
            } else {
                $summary['programs'][$i]['required_units'] = $selection[$i]['required_units'];
                $tmp_program_output = $selection[$i]['units'];
                $tmp_program_output_total_not_merged = $tmp_program_output_total_not_merged + count($selection[$i]['units']);
                $output = array_merge($output, $selection[$i]['units']);
                
            }

              $summary['programs'][$i]['totals_after_optimization'] = count($tmp_program_output);
              $summary['programs'][$i]['totals_after_optimization_not_merged'] = $tmp_program_output_total_not_merged;
              $audit->comment = $audit->comment.' | Combine and optimize total after optimization is '.count($tmp_program_output).'.';
              $audit->save();
              $summary['programs'][$i]['units_after_optimization'] = $tmp_program_output;
              
        }

        //dd(array_unique($output), $output);

        $summary['ungrouped'] = $output;
        $summary['grouped'] = array_unique($output);

        $audit->comment = $audit->comment.' | Combine and optimize finished process returning to selection process.';
         $audit->save();
         
        return $summary;
    }

    public function selectionProcess(Audit $audit)
    {
        // Summary stats vs Program stats
        // file # is before overlap and optimization
        /*
        SUMMARY STATS:
        Requirement (without overlap)
        - required units (this is given by the selection process)
        - selected (this is counted in the db)
        - needed (this is calculated)
        - to be inspected (this is counted in the db)

        To meet compliance (optimzed and overlap)
        - sample size (this is given by the selection process)
        - completed (this is counted)
        - remaining inspection (this is calculated)

        FOR EACH PROGRAM:
        - required units (this is given by the selection process)
        - selected (this is counted in the db)
        - needed (this is calculated)
        - to be inspected (this is counted in the db)
         */

        $audit->comment = $audit->comment.' | Select Process Started';
        $audit->comment_system = $audit->comment_system.' | Select Process Started for audit '.$audit->id;
            $audit->save();
            
        // is the project processing all the buildings together? or do we have a combination of grouped buildings and single buildings?
        if ($audit->id) {
            //dd($audit);
            $project = Project::where('id', '=', $audit->project_id)->with('programs')->first();
            
            $audit->comment_system = $audit->comment_system.' | project selected in selection process';
            
            $audit->save();
        } else {
            Log::error('Audit '.$audit->id.' does not have a project somehow...');
            $audit->comment_system = $audit->comment_system.' | Error, this audit isn\'t associated with a project somehow...';
            $audit->comment = $audit->comment.' | Error, this audit isn\'t associated with a project somehow...';
            $audit->save();
            
            return "Error, this audit isn't associated with a project somehow...";
            

        }
        $audit->comment_system = $audit->comment_system.' | Select Process Has Selected Project ID '.$audit->project_id;
            $audit->save();
            

        if(!$project) {
            Log::error('Audit '.$audit->id.' does not have a project somehow...');
            $audit->comment_system = $audit->comment_system.' | Error, this audit isn\'t associated with a project somehow...';
            $audit->comment = $audit->comment.' | Error, this audit isn\'t associated with a project somehow...';
            $audit->save();
            
            return "Error, this audit isn't associated with a project somehow...";
        }

        if (!$project->programs) {
            Log::error('Error, the project does not have a program.');
            $audit->comment = $audit->comment.' | Error, the project does not have a program.';
            $audit->comment_system = $audit->comment_system.' | Error, the project does not have a program.';
            $audit->save();
            
            return "Error, this project doesn't have a program.";
            
        }
        $projectProgramIds = array();

        foreach ($project->programs as $program) {
            $projectProgramIds[] = $program->program_key;
            $audit->comment_system = $audit->comment_system.' | Program ID: '.$program->program_key.' Program Name: '.$program->program->program_name;
            $audit->save();
        }
        $audit->comment_system = $audit->comment_system.' | Select Process Checked the Programs and that there are Programs';
            $audit->save();
            

        $total_buildings = $project->total_building_count;
        $total_units = $project->total_unit_count;

        $audit->comment_system = $audit->comment_system.' | Select Process Found '.$total_buildings.' Total Buildings and '.$total_units.' Total Units';
            $audit->save();
            
        //Log::info('509:: total buildings and units '.$total_buildings.', '.$total_units.' respectively.');
        $pm_contact = ProjectContactRole::where('project_id', '=', $audit->project_id)
                                ->where('project_role_key', '=', 21)
                                ->with('organization')
                                ->first();
                                
        //Log::info('514:: pm contact found');

        $audit->comment_system = $audit->comment_system.' | Select Process Selected the PM Contact';
            $audit->save();
            
        $organization_id = null;
        if ($pm_contact) {
            $audit->comment_system = $audit->comment_system.' | Select Process Confirmed PM Contact';
            $audit->save();
            
            if ($pm_contact->organization) {
                $organization_id = $pm_contact->organization->id;
                //Log::info('519:: pm organization identified');
                $audit->comment_system = $audit->comment_system.' | Select Process Updated the Organization ID';
                $audit->save();
                
            }
        }

        
        // save all buildings in building_inspection table
        $buildings = $project->buildings;
        //Log::info('526:: buildings saved.');
        // remove any data
        BuildingInspection::where('audit_id', '=', $audit->id)->delete();
        
        //Log::info('529:: building inspections deleted');
        $audit->comment_system = $audit->comment_system.' | Select Process Deleted all the current building cache for this audit id.';
            $audit->save();
            
            $buildingCount = 0;
        if ($buildings) {
            foreach ($buildings as $building) {
                
                $buildingCount++;
                if ($building->address) {
                    $address = $building->address->line_1;
                    $city = $building->address->city;
                    $state = $building->address->state;
                    $zip = $building->address->zip;
                } else {
                    $address = '';
                    $city = '';
                    $state = '';
                    $zip = '';
                }

                $b = new BuildingInspection([
                    'building_id' => $building->id,
                    'building_key' => $building->building_key,
                    'building_name' => $building->building_name,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'zip' => $zip,
                    'audit_id' => $audit->id,
                    'audit_key' => $audit->monitoring_key,
                    'project_id' => $project->id,
                    'project_key' => $project->project_key,
                    'pm_organization_id' => $organization_id,
                    'auditors' => null,
                    'nlt_count' => 0,
                    'lt_count' => 0,
                    'followup_count' => 0,
                    'complete' => 0,
                    'submitted_date_time' => null
                ]);
                $b->save();
                
                //Log::info('565:: '.$b->id.' building inspection added');
            }
            $audit->comment = $audit->comment.' | Select Process Put in '.$buildingCount.' Buildings';
            $audit->comment_system = $audit->comment_system.' | Select Process Put in '.$buildingCount.' Buildings';
            $audit->save();
            
        }

        $selection = [];

        $program_htc_ids = explode(',', SystemSetting::get('program_htc'));
        $audit->comment_system = $audit->comment_system.' | Line 740 run.';
            $audit->save();
        //
        //
        // 1 - FAF || NSP || TCE || RTCAP || 811 units
        // total for all those programs combined
        //
        //
        

        $comments = [];

        $required_units = 0;

        $program_bundle_ids = explode(',', SystemSetting::get('program_bundle'));
        $audit->comment_system = $audit->comment_system.' | Got the program bundle.';
            $audit->save();
        

        /////// DO NOT DO ANY OF THE FOLLOWING IF THE PROJECT DOES NOT HAVE ONE OF THESE PROGRAMS....

        if(!empty(array_intersect($projectProgramIds, $program_bundle_ids))) {
            $audit->comment_system = $audit->comment_system.' | Project has one of the program bundle ids.';
            $audit->save();


            $program_bundle_names = Program::whereIn('program_key', $program_bundle_ids)->get()->pluck('program_name')->toArray();
            $audit->comment_system = $audit->comment_system.' | Line 758 run.';
            $audit->save();
            
            $program_bundle_names = implode(',', $program_bundle_names);
            $audit->comment_system = $audit->comment_system.' | Line 762 run at '.date('g:h:i a',time());
            $audit->save();
            $units = Unit::whereHas('programs', function ($query) use ($audit, $program_bundle_ids) {
                            $query->where('monitoring_key', '=', $audit->monitoring_key);
                            $query->whereIn('program_key', $program_bundle_ids);
            })->get();
            $audit->comment_system = $audit->comment_system.' | Line 765 run at '.date('g:h:i a',time());
            $audit->save();
            

            // total for all programs combined
            $total = count($units);
            $audit->comment_system = $audit->comment_system.' | Line 775 run: Total set to '.$total;
            $audit->save();

            if($total){
                $audit->comment = $audit->comment.' | Select Process starting Group 1 selection ';
                $audit->save();
                

                $comments[] = 'Pool of units chosen using audit id '.$audit->id.' and a list of programs: '.$program_bundle_names;
                $audit->comment = $audit->comment.' | Pool of units chosen using audit id '.$audit->id.' and a list of programs: '.$program_bundle_names;
            
                $audit->save();
                

                $comments[] = 'Total units in the pool is '.count($units);
                $audit->comment = $audit->comment. ' | Total units in the pool is '.$total;
                $audit->comment_system = $audit->comment_system. ' | Total units in the pool is '.$total;
                $audit->save();
                
                $program_htc_ids = explode(',', SystemSetting::get('program_htc'));
                
                $program_htc_names = Program::whereIn('program_key', $program_htc_ids)->get()->pluck('program_name')->toArray();
                
                $program_htc_names = implode(',', $program_htc_names);
                

                // cannot use overlap like this anymore
                // instead for each unit, check if a HTC program is associated
                // $program_htc_overlap = array_intersect($program_htc_ids, $program_bundle_ids);
                // 
                // $program_htc_overlap_names = Program::whereIn('program_key', $program_htc_overlap)->get()->pluck('program_name')->toArray(); // 30001,30043
                // 
                // $program_htc_overlap_names = implode(',', $program_htc_overlap_names);
                // 
                // $comments[] = 'Identified the program keys that have HTC funding: '.$program_htc_overlap_names;
                // $audit->comment = $audit->comment.' | Identified the program keys that have HTC funding: '.$program_htc_overlap_names;
                // $audit->save();
                // 

                $has_htc_funding = 0;
                $unitProcessCount = 0;
                foreach ($units as $unit) {
                    
                    $audit->comment_system = $audit->comment_system.' | Line 818 run (loop).';
                    $audit->save();
                    if($unit->has_program_from_array($program_htc_ids, $audit->id)){
                        $has_htc_funding = 1;
                        $comments[] = 'The unit key '.$unit->unit_key.' belongs to a program with HTC funding';
                        $audit->comment_system = $audit->comment_system.'The unit key '.$unit->unit_key.' belongs to a program with HTC funding';
                    }
                }
           

                // $number_of_units_required = ceil($total/5);

                // are there units with HTC funding?
                if (!$has_htc_funding) {
                    $comments[] = 'By checking each unit and associated programs with HTC funding, we determined that no HTC funding exists for this pool';
                    $audit->comment = $audit->comment.' | By checking each unit and associated programs with HTC funding, we determined that no HTC funding exists for this pool';

                    $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 20);

                    //$required_units = count($units_selected);
                    $required_units = ceil($total/5);

                    $comments[] = '20% of the pool is randomly selected. Total selected: '.count($units_selected);
                     $audit->comment = $audit->comment.' | 20% of the pool is randomly selected. Total selected: '.count($units_selected);
                     
                    $audit->save();
                    
                
                    $selection[] = [
                        "group_id" => 1,
                        "building_key" => "",
                        "program_name" => "FAF NSP TCE RTCAP 811",
                        "program_ids" => SystemSetting::get('program_bundle'),
                        "pool" => count($units),
                        "units" => $units_selected,
                        "totals" => count($units_selected),
                        "required_units" => $required_units,
                        "use_limiter" => $has_htc_funding, // used to trigger limiter
                        "comments" => $comments
                    ];
                    

                } else {
                    $comments[] = 'By checking each unit and associated programs with HTC funding, we determined that there is HTC funding for this pool';
                    $audit->comment = $audit->comment.' | By checking each unit and associated programs with HTC funding, we determined that there is HTC funding for this pool';
                    $audit->save();
                    

                    // check in project_program->first_year_award_claimed date for the 15 year test
                
                    $first_year = null;

                    // look at HTC programs, get the most recent year for the check
                    $comments[] = 'Going through the HTC programs, we look for the most recent year in the first_year_award_claimed field.';
                    $audit->comment = $audit->comment.' | Going through the HTC programs, we look for the most recent year in the first_year_award_claimed field.';
                    $audit->save();
                    
                    foreach ($project->programs as $program) {
                        
                        if (isset($program_htc_overlap) && in_array($program->program_key, $program_htc_overlap)) {
                            if ($first_year == null || $first_year < $program->first_year_award_claimed) {
                                $first_year = $program->first_year_award_claimed;
                                $comments[] = 'Program key '.$program->program_key.' has the year '.$program->first_year_award_claimed.'.';
                                $audit->comment = $audit->comment.' | Program key '.$program->program_key.' has the year '.$program->first_year_award_claimed.'.';
                                $audit->save();
                                
                            }
                        }
                    }

                    if (idate("Y")-15 > $first_year && $first_year != null) {
                        $first_fifteen_years = 0;
                        $comments[] = 'Based on the year, we determined that the program is not within the first 15 years.';
                        $audit->comment = $audit->comment.' | Based on the year, we determined that the program is not within the first 15 years.';
                        $audit->save();
                        

                    } else {
                        $first_fifteen_years = 1;
                        $comments[] = 'Based on the year,'.$first_year.' we determined that the program is within the first 15 years.';
                        $audit->comment = $audit->comment.' | Based on the year '.$first_year.', we determined that the program is within the first 15 years.';
                        $audit->save();
                        
                    }
                    
                    if ($first_fifteen_years) {
                        // check project for least purchase
                        $leaseProgramKeys = explode(',', SystemSetting::get('lease_purchase'));
                        
                        // $comments[] = 'Check if the programs associated with the project correspond to lease purchase using program keys: '.SystemSetting::get('lease_purchase').'.';
                        // $audit->comment = $audit->comment.' | Check if the programs associated with the project correspond to lease purchase using program keys: '.SystemSetting::get('lease_purchase').'.';
                        // $audit->save();
                        // 

                        /*    
                            foreach ($project->programs as $program) {
                                
                                if (in_array($program->program_key, $leaseProgramKeys)) {
                                    $isLeasePurchase = 1;
                                    $comments[] = 'A program key '.$program->program_key.' confirms that this is a lease purchase.';
                                    $audit->comment = $audit->comment.' | A program key '.$program->program_key.' confirms that this is a lease purchase.';
                                    $audit->save();

                                } else {
                                    $isLeasePurchase = 0;
                                }
                            }


                            if ($isLeasePurchase) {
                                $required_units = $this->adjustedLimit($audit, count($units));

                                $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 0, $required_units);

                                //$required_units = count($units_selected);
                                //$required_units = $number_of_units_required;

                                $comments[] = $required_units.' must be randomly selected. Total selected: '.count($units_selected);
                                $audit->comment = $audit->comment.' | '.$required_units.' must be randomly selected. Total selected: '.count($units_selected);
                                    $audit->save();
                                    
                    
                                $selection[] = [
                                    "group_id" => 1,
                                    "building_key" => "",
                                    "program_name" => "FAF NSP TCE RTCAP 811",
                                    "program_ids" => SystemSetting::get('program_bundle'),
                                    "pool" => count($units),
                                    "units" => $units_selected,
                                    "totals" => count($units_selected),
                                    "required_units" => $required_units,
                                    "use_limiter" => $has_htc_funding, // used to trigger limiter
                                    "comments" => $comments
                                ];
                                
                            } else {
                        */
                        $is_multi_building_project = 0;

                        // eventually we will also be checking for building grouping...

                        // for each of the current programs+project, check if multiple_building_election_key is 2 for multi building project
                        $comments[] = 'Going through each program to determine if the project is a multi building project by looking for multiple_building_election_key=2.';
                        $audit->comment = $audit->comment.' | Going through each program to determine if the project is a multi building project by looking for multiple_building_election_key=2.';
                            $audit->save();
                            

                        foreach ($project->programs as $program) {
                            
                            if (in_array($program->program_key, $program_bundle_ids)) {
                                if ($program->multiple_building_election_key == 2) {
                                    $is_multi_building_project = 1;
                                    $comments[] = 'Program key '.$program->program_key.' showed that the project is a multi building project.';
                                    $audit->comment = $audit->comment.' | Program key '.$program->program_key.' showed that the project is a multi building project.';
                                    $audit->save();
                                    
                                }
                            }
                        }

                        if ($is_multi_building_project) {
                            $audit->comment = $audit->comment.' | This is a multi-building elected project setting the adjusted limit accordingly.';
                            $audit->save();
                            $required_units = $this->adjustedLimit($audit, count($units));
                            $audit->comment = $audit->comment.' | Set the adjusted limit based on the chart to '.$required_units.'.';
                            $audit->save();

                            $units_selected = $this->randomSelection($audit, $units->pluck('unit_key')->toArray(), 0, $required_units);
                            $audit->comment = $audit->comment.' | Performed the random selection from the audit.';
                            $audit->save();
                            

                            //$required_units = count($units_selected);
                            // $required_units = $number_of_units_required;

                            $comments[] = $required_units.' must be randomly selected. Total selected: '.count($units_selected);

                            $audit->comment = $audit->comment.' | '.$required_units.' must be randomly selected. Total selected: '.count($units_selected);
                                    $audit->save();
                                    
            
                            $selection[] = [
                                "group_id" => 1,
                                "building_key" => "",
                                "program_name" => "FAF NSP TCE RTCAP 811",
                                "program_ids" => SystemSetting::get('program_bundle'),
                                "pool" => count($units),
                                "units" => $units_selected,
                                "totals" => count($units_selected),
                                "required_units" => $required_units,
                                "use_limiter" => $has_htc_funding, // used to trigger limiter
                                "comments" => $comments
                            ];
                            
                        } else {
                            $use_limiter = 0; // we apply the limiter for each building

                            $comments[] = 'The project is not a multi building project.';
                            $audit->comment = $audit->comment.' | The project is not a multi building project.';
                                    $audit->save();
                                    
                            // group units by building, then proceed with the random selection
                            // create a new list of units based on building and project key
                            $units_selected = [];

                            $first_building_done = 0; // this is to control the comments to only keep the ones we care about after the first building information is displayed.

                            foreach ($buildings as $building) {
                                
                                if($first_building_done){
                                    $comments = array(); // clear the comments.
                                }else{
                                    $first_building_done = 1;
                                }

                                $units_for_that_building = Unit::where('building_key', '=', $building->building_key)
                                                ->whereHas('programs', function ($query) use ($audit, $program_bundle_ids) {
                                                    $query->where('monitoring_key', '=', $audit->monitoring_key);
                                                    $query->whereIn('program_key', $program_bundle_ids);
                                                })
                                                ->pluck('unit_key')
                                                ->toArray();

                                // $required_units_for_that_building = ceil(count($units_for_that_building)/5);
                                $required_units_for_that_building = $this->adjustedLimit($audit, count($units_for_that_building));

                                $required_units = $required_units_for_that_building;
                                
                                $new_building_selection = $this->randomSelection($audit,$units_for_that_building, 0, $required_units);
                                $units_selected = $new_building_selection;
                                $units_selected_count = count($new_building_selection);

                                $comments[] = $required_units.' of building key '.$building->building_key.' must be randomly selected. Total selected: '.count($new_building_selection).'.';
                                $audit->comment = $audit->comment.' | '.$required_units.' of building key '.$building->building_key.' must be randomly selected. Total selected: '.count($new_building_selection).'.';
                                    $audit->save();
                                    

                                $selection[] = [
                                    "group_id" => 1,
                                    "building_key" => $building->building_key,
                                    "program_name" => "FAF NSP TCE RTCAP 811",
                                    "program_ids" => SystemSetting::get('program_bundle'),
                                    "pool" => count($units),
                                    "units" => $units_selected,
                                    "totals" => count($units_selected),
                                    "required_units" => $required_units,
                                    "use_limiter" => $has_htc_funding, // used to trigger limiter
                                    "comments" => $comments
                                ];
                                
                            }
                        }
                        //}
                    } else {
                        // get required units using limiter
                        // $required_units = $this->adjustedLimit($audit, count($units));

                        $required_units = ceil($total/10); // 10% of units

                        $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 10);

                        // $required_units = count($units_selected);
                        
                        $comments[] = ' 10% are randomly selected. Total selected: '.count($units_selected);
                        $audit->comment = $audit->comment.' | 10% are randomly selected. Total selected: '.count($units_selected);
                                        $audit->save();
                                        

                        $selection[] = [
                            "group_id" => 1,
                            "building_key" => "",
                            "program_name" => "FAF NSP TCE RTCAP 811",
                            "program_ids" => SystemSetting::get('program_bundle'),
                            "pool" => count($units),
                            "units" => $units_selected,
                            "totals" => count($units_selected),
                            "required_units" => $required_units,
                            "use_limiter" => $has_htc_funding, // used to trigger limiter
                            "comments" => $comments
                        ];
                        
                    }
                }
            }else{

                $audit->comment_system = $audit->comment_system.' | Select Process is not working with group 1.';
                $audit->save();
            }
        } else {
            $audit->comment_system = $audit->comment_system.' | This project does not have any in project group.';
            $audit->save();

        }


        //
        //
        // 2 - 811 units
        // 100% selection
        // for units with 811 funding
        //
        //
        
        
        $program_811_ids = explode(',', SystemSetting::get('program_811'));
        

        ///// DO NOT DO ANY OF THE FOLLOWING IF THE PROJECT DOES NOT HAVE 811
        if(!empty(array_intersect($projectProgramIds, $program_811_ids))) {
            $program_811_names = Program::whereIn('program_key', $program_811_ids)->get()->pluck('program_name')->toArray();
            
            $program_811_names = implode(',', $program_811_names);
            
            $comments = [];

            $required_units = 0;

            $units = Unit::whereHas('programs', function ($query) use ($audit, $program_811_ids) {
                                $query->where('audit_id', '=', $audit->id);
                                $query->whereIn('program_key', $program_811_ids);
            })->get();
            

            if(count($units)){

                $required_units = count($units);

                $audit->comment = $audit->comment.' | Select Process starting 811 selection ';
                $audit->save();
                

                $units_selected = $units->pluck('unit_key')->toArray();
                

                $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_811_names;
                $comments[] = 'Total units in the pool is '.count($units);
                $comments[] = '100% of units selected:'.count($units_selected);
                $audit->comment = $audit->comment.' | Select Process Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_811_names.' | Select Process Total units in the pool is '.count($units).' | Select Process 100% of units selected:'.count($units_selected);
                    $audit->save();
                    
                $selection[] = [
                    "group_id" => 2,
                    "program_name" => "811",
                    "program_ids" => SystemSetting::get('program_811'),
                    "pool" => count($units),
                    "units" => $units_selected,
                    "totals" => count($units_selected),
                    "required_units" => $required_units,
                    "use_limiter" => 0,
                    "comments" => $comments
                ];
                

            }else{

                $audit->comment_system = $audit->comment_system.' | Select Process is not working with 811.';
                $audit->save();
            }
        }else{
            $audit->comment_system = $audit->comment_system.' | Select Process is not working with 811.';
            $audit->save();
        }


        //
        //
        // 3 - Medicaid units
        // 100% selection
        //
        //
        
        

        $program_medicaid_ids = explode(',', SystemSetting::get('program_medicaid'));
        

        if(!empty(array_intersect($projectProgramIds, $program_medicaid_ids))) {
            $program_medicaid_names = Program::whereIn('program_key', $program_medicaid_ids)->get()->pluck('program_name')->toArray();
            
            $program_medicaid_names = implode(',', $program_medicaid_names);
            
            $comments = [];

            $required_units = 0;

            $units = Unit::whereHas('programs', function ($query) use ($audit, $program_medicaid_ids) {
                                $query->where('audit_id', '=', $audit->id);
                                $query->whereIn('program_key', $program_medicaid_ids);
            })->get();
            

            if(count($units)){
                $audit->comment = $audit->comment.' | Select Process starting Medicaid selection ';
                $audit->save();
                

                $required_units = count($units);

                $units_selected = $units->pluck('unit_key')->toArray();
                

                $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_medicaid_names;
                $comments[] = 'Total units in the pool is '.count($units);
                $comments[] = '100% of units selected:'.count($units_selected);

                $audit->comment = $audit->comment.' | Select Process Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_medicaid_names.' | Select Process Total units in the pool is '.count($units).' | Select Process 100% of units selected:'.count($units_selected);
                    $audit->save();
                    

                $selection[] = [
                    "group_id" => 3,
                    "program_name" => "Medicaid",
                    "program_ids" => SystemSetting::get('program_medicaid'),
                    "pool" => count($units),
                    "units" => $units_selected,
                    "totals" => count($units_selected),
                    "required_units" => $required_units,
                    "use_limiter" => 0,
                    "comments" => $comments
                ];
                

            }else{
                $audit->comment_system = $audit->comment_system.' | Select Process is not working with Medicaid.';
                $audit->save();
            }
        }else{
            $audit->comment_system = $audit->comment_system.' | Select Process is not working with Medicaid.';
            $audit->save();
        }


        //
        //
        // 4 - HOME
        //
        //
        

        $units_to_check_for_overlap = [];
        $htc_units_subset_for_home = array();

        $program_home_ids = explode(',', SystemSetting::get('program_home'));

        if(!empty(array_intersect($projectProgramIds, $program_home_ids))) {
            $audit->comment_system = $audit->comment_system.' | Started HOME, got ids from system settings.';
            $audit->save();

            $home_award_numbers = ProjectProgram::whereIn('program_key', $program_home_ids)->where('project_id', '=', $audit->project_id)->select('award_number')->groupBy('award_number')->orderBy('award_number', 'ASC')->get();
            $audit->comment_system = $audit->comment_system.' | Got home award numbers.';
            $audit->save();

            foreach($home_award_numbers as $home_award_number){
                // for each award_number, create a different HOME group
                $audit->comment_system = $audit->comment_system.' | Home award number '.$home_award_number.' being processed.';
                $audit->save();
                
                // programs with that award_number
                $program_keys_with_award_number = ProjectProgram::where('award_number','=',$home_award_number->award_number)->where('project_id', '=', $audit->project_id)->pluck('program_key')->toArray(); 
                $audit->comment_system = $audit->comment_system.' | Select programs with that award number.';
                $audit->save();

                $program_home_names = Program::whereIn('program_key', $program_home_ids)
                                                ->whereIn('program_key', $program_keys_with_award_number)
                                                ->get()
                                                ->pluck('program_name')->toArray();

                $audit->comment_system = $audit->comment_system.' | Selected program names.';
                $audit->save();

                
                $program_home_names = implode(',', $program_home_names);
                
                $comments = [];

                $required_units = 0;

                $total_project_units = Project::where('id', '=', $audit->project_id)->first()->units()->count();

                $audit->comment_system = $audit->comment_system.' | Counting project units: '.$total_project_units;
                $audit->save();
                

                $audit->comment_system = $audit->comment_system.' | Selecting Units using using settings at '.date('g:h:i a',time());
                $audit->save();

                $units = Unit::whereHas('programs', function ($query) use ($audit, $program_home_ids, $program_keys_with_award_number) {
                                    $query->where('audit_id', '=', $audit->id);
                                    $query->whereIn('program_key', $program_keys_with_award_number);
                                    $query->whereIn('program_key', $program_home_ids);
                })->get();

                $audit->comment_system = $audit->comment_system.' | Finished selecting units at '.date('g:h:i a',time()).'.';
                $audit->save();
                
                $audit->comment_system = $audit->comment_system.' | Total selected units '.count($units);
                $audit->save();

                if(count($units)){
                    $audit->comment = $audit->comment.' | Select Process starting Home selection for award number '.$home_award_number;
                    $audit->save();
                    

                    $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_home_names.', award number '.$home_award_number;

                    $audit->comment = $audit->comment.' | Select Process Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_home_names.', award number '.$home_award_number;
                    $audit->save();
                    

                    $total_units = count($units);
                    


                    // $program_htc_overlap = array_intersect($program_htc_ids, $program_home_ids);
                    // 
                    // $program_htc_overlap_names = Program::whereIn('program_key', $program_htc_overlap)->get()->pluck('program_name')->toArray();
                    // 
                    // $program_htc_overlap_names = implode(',', $program_htc_overlap_names);
                    // 

                    $units_selected = [];
                    $htc_units_subset_for_all = [];
                    $htc_units_subset = [];
                    
                    $comments[] = 'Total units with HOME funding and award number '.$home_award_number.' is '.$total_units;
                    $comments[] = 'Total units in the project is '.$total_project_units;
                    $audit->comment = $audit->comment.' | Select Process Total units with HOME fundng is '.$total_units.' | Select Process Total units in the project is '.$total_project_units;
                        $audit->save();
                        


                    if (count($units) <= 4) {

                        $required_units = count($units);

                        $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 100);
                        
                        $comments[] = 'Because there are less than 4 HOME units, the selection is 100%. Total selected: '.count($units_selected);
                        $audit->comment = $audit->comment.' | Select Process Because there are less than 4 HOME units, the selection is 100%. Total selected: '.count($units_selected);
                        $audit->save();
                        

                    } else {
                        if (ceil($total_units/2) >= ceil($total_project_units/5)) {

                            $required_units = ceil($total_units/2);

                            $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 0, ceil($total_units/2));
                            

                            $comments[] = 'Because there are more than 4 units and because 20% of project units is smaller than 50% of HOME units, the total selected is '.ceil($total_units/2);
                            $audit->comment = $audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is smaller than 50% of HOME units, the total selected is '.ceil($total_units/2);
                            $audit->save();
                            

                        } else {

                            if(ceil($total_project_units/5) > $total_units){
                                $required_units = $total_units;
                                $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 0, $total_units);
                                
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of HOME units, the total selected is '.$total_units.' which is the total number of units';

                                $audit->comment = $audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of HOME units, the total selected is '.$total_units.' which is the total number of units';
                                $audit->save();
                            }else{
                                $required_units = ceil($total_project_units/5);
                                $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 0, ceil($total_project_units/5));
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of HOME units, the total selected is '.ceil($total_project_units/5);

                                $audit->comment = $audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of HOME units, the total selected is '.ceil($total_project_units/5);
                                $audit->save();
                            }

                            
                            
                        }
                    }

                    foreach ($units_selected as $unit_key) {
                        $has_htc_funding = 0;

                        $unit_selected = Unit::where('unit_key', '=', $unit_key)->first();

                        $comments[] = 'Checking if HTC funding applies to this unit '.$unit_selected->unit_key.' by cross checking with HTC programs';

                        $audit->comment = $audit->comment.' | Select Process Checking if HTC funding applies to this unit '.$unit_selected->unit_key.' by cross checking with HTC programs';
                        $audit->save();
                        
                        
                        // if units have HTC funding add to subset
                        
                        
                        if($unit_selected->has_program_from_array($program_htc_ids, $audit->id)){
                            $has_htc_funding = 1;
                            $comments[] = 'The unit key '.$unit_selected->unit_key.' belongs to a program with HTC funding';
                            $audit->save();
                        }
                        
                        if ($has_htc_funding) {
                            
                            $comments[] = 'We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
                            $audit->comment = $audit->comment.' | Select Process We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
                                $audit->save();
                                
                            $htc_units_subset[] = $unit_selected->unit_key;
                        }
                    }


                    $htc_units_subset_for_home = $htc_units_subset;
                    $units_to_check_for_overlap = array_merge($units_to_check_for_overlap, $units_selected);
                    

                    $selection[] = [
                        "group_id" => 4,
                        "program_name" => "HOME",
                        "program_ids" => SystemSetting::get('program_home'),
                        "pool" => count($units),
                        "units" => $units_selected,
                        "totals" => count($units_selected),
                        "required_units" => $required_units,
                        'htc_subset' => $htc_units_subset,
                        "use_limiter" => 0,
                        "comments" => $comments
                    ];
                    
                }else{
                    $htc_units_subset_for_home = array();
                    $audit->comment_system = $audit->comment_system.' | 1455 Select Process is not working with HOME.';
                    $audit->save();
                }
            }
        }else {
            $htc_units_subset_for_home = array();
            $audit->comment_system = $audit->comment_system.' | 1461 Select Process is not working with Home.';
            $audit->save();
        }


        //
        //
        // 5 - OHTF
        //
        //
        

        $program_ohtf_ids = explode(',', SystemSetting::get('program_ohtf'));
        if(!empty(array_intersect($projectProgramIds, $program_ohtf_ids))) {
            $htc_units_subset_for_ohtf = array();

            $ohtf_award_numbers = ProjectProgram::whereIn('program_key', $program_ohtf_ids)->where('project_id', '=', $audit->project_id)->select('award_number')->groupBy('award_number')->orderBy('award_number', 'ASC')->get();

            foreach($ohtf_award_numbers as $ohtf_award_number){

                // programs with that award_number
                $program_keys_with_award_number = ProjectProgram::where('award_number','=',$ohtf_award_number->award_number)->where('project_id', '=', $audit->project_id)->pluck('program_key')->toArray(); 

                $program_ohtf_names = Program::whereIn('program_key', $program_ohtf_ids)
                                                ->whereIn('program_key', $program_keys_with_award_number)
                                                ->get()
                                                ->pluck('program_name')
                                                ->toArray();
                
                $program_ohtf_names = implode(',', $program_ohtf_names);
                
                $comments = [];

                $required_units = 0;

                $total_project_units = Project::where('id', '=', $audit->project_id)->first()->units()->count();
                

                $units = Unit::whereHas('programs', function ($query) use ($audit, $program_ohtf_ids, $program_keys_with_award_number) {
                                    $query->where('audit_id', '=', $audit->id);
                                    $query->whereIn('program_key', $program_keys_with_award_number);
                                    $query->whereIn('program_key', $program_ohtf_ids);
                })->get();
                


                if(count($units)){
                    $audit->comment = $audit->comment.' | Select Process Starting OHTF for award number '.$ohtf_award_number;
                    $audit->save();
                    

                    $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_ohtf_names.', award number '.$ohtf_award_number;
                    $audit->comment = $audit->comment.' | Select Process Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_ohtf_names.', award number '.$ohtf_award_number;
                    $audit->save();
                    

                    $total_units = count($units);
                    

                    // $program_htc_overlap = array_intersect($program_htc_ids, $program_ohtf_ids);
                    // 
                    // $program_htc_overlap_names = Program::whereIn('program_key', $program_htc_overlap)->get()->pluck('program_name')->toArray();
                    // 
                    // $program_htc_overlap_names = implode(',', $program_htc_overlap_names);
                    // 

                    $units_selected = [];
                    $htc_units_subset = [];

                    $comments[] = 'Total units with OHTF funding and award number '.$ohtf_award_number.' is '.$total_units;
                    $comments[] = 'Total units in the project with a program is '.$total_project_units;

                    $audit->comment = $audit->comment.' | Select Process Total units with OHTF funding is '.$total_units;
                    $audit->save();
                    

                    $audit->comment = $audit->comment.' | Select Process Total units in the project is '.$total_project_units;
                    $audit->save();
                    

                    if (count($units) <= 4) {

                        $required_units = count($units);

                        $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 100);
                        
                        $comments[] = 'Because there are less than 4 OHTF units, the selection is 100%. Total selected: '.count($units_selected);

                        $audit->comment = $audit->comment.' | Select Process Because there are less than 4 OHTF units, the selection is 100%. Total selected: '.count($units_selected);
                        $audit->save();
                        

                    } else {
                        if (ceil($total_units/2) >= ceil($total_project_units/5)) {

                            $required_units = ceil($total_units/2);

                             $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 0, ceil($total_units/2));
                             
                             $comments[] = 'Because there are more than 4 units and because 20% of project units is smaller than 50% of OHTF units, the total selected is '.ceil($total_units/2);

                            $audit->comment = $audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is smaller than 50% of OHTF units, the total selected is '.ceil($total_units/2);
                            $audit->save();
                            
                        } else {

                            if(ceil($total_project_units/5) > $total_units){
                                $required_units = $total_units;
                                $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 0, $total_units);
                                
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of OHTF units, the total selected is '.$total_units. 'which is the total number of units';

                                $audit->comment = $audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of OHTF units, the total selected is '.$total_units. 'which is the total number of units';
                            }else{
                                $required_units = ceil($total_project_units/5);
                                $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 0, ceil($total_project_units/5));
                                
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of OHTF units, the total selected is '.ceil($total_project_units/5);

                                $audit->comment = $audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of OHTF units, the total selected is '.ceil($total_project_units/5);

                            }

                            
                            $audit->save();
                            
                        }
                    }

                    foreach ($units_selected as $unit_key) {
                        $unit_selected = Unit::where('unit_key','=',$unit_key)->first();
                        
                        if($unit_selected){
                            $has_htc_funding = 0;

                            $comments[] = 'Checking if HTC funding applies to this unit '.$unit_selected->unit_key.' by cross checking with HTC programs';

                            $audit->comment = $audit->comment.' | Select Process Checking if HTC funding applies to this unit '.$unit_selected->unit_key.' by cross checking with HTC programs';
                                $audit->save();
                                

                            // if units have HTC funding add to subset
                            if($unit_selected->has_program_from_array($program_htc_ids, $audit->id)){
                                $has_htc_funding = 1;
                                $comments[] = 'The unit key '.$unit_selected->unit_key.' belongs to a program with HTC funding';
                                $audit->save();
                            }

                            if ($has_htc_funding) {
                                $htc_units_subset[] = $unit_selected->unit_key;
                                
                                $comments[] = 'We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
                                $audit->comment = $audit->comment.' | Select Process We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
                                    
                                    $audit->save();
                                    
                            }
                        } else {
                            $audit->comment = $audit->comment.' | Select Process A unit came up null in its values. We recommend checking the completeness of the data in Devco for your units, update any that may be missing data, and then re-run the selection.';
                                    
                                    $audit->save();
                                    
                        }
                    }

                    $htc_units_subset_for_ohtf = $htc_units_subset;
                    $units_to_check_for_overlap = array_merge($units_to_check_for_overlap, $units_selected);
                    

                    $selection[] = [
                        "group_id" => 5,
                        "program_name" => "OHTF",
                        "program_ids" => SystemSetting::get('program_ohtf'),
                        "pool" => count($units),
                        "units" => $units_selected,
                        "totals" => count($units_selected),
                        "required_units" => $required_units,
                        'htc_subset' => $htc_units_subset,
                        "use_limiter" => 0,
                        "comments" => $comments
                    ];
                    
                }else{
                    $htc_units_subset_for_ohtf = array();
                    $audit->comment_system = $audit->comment_system.' | Select Process is not working with OHTF.';
                    $audit->save();
                }
            }
        }else{
            $htc_units_subset_for_ohtf = array();
            $audit->comment_system = $audit->comment_system.' | Select Process is not working with OHTF.';
            $audit->save();
        }


        //
        //
        // 6 - NHTF
        //
        //

        $program_nhtf_ids = explode(',', SystemSetting::get('program_nhtf'));
        if(!empty(array_intersect($projectProgramIds, $program_nhtf_ids))) {
            $htc_units_subset_for_nhtf = array();
            
            $nhtf_award_numbers = ProjectProgram::whereIn('program_key', $program_nhtf_ids)->where('project_id', '=', $audit->project_id)->select('award_number')->groupBy('award_number')->orderBy('award_number', 'ASC')->get();

            foreach($nhtf_award_numbers as $nhtf_award_number){

                // programs with that award_number
                $program_keys_with_award_number = ProjectProgram::where('award_number','=',$nhtf_award_number->award_number)->where('project_id', '=', $audit->project_id)->pluck('program_key')->toArray(); 

                $program_nhtf_names = Program::whereIn('program_key', $program_nhtf_ids)
                                                ->whereIn('program_key', $program_keys_with_award_number)
                                                ->get()
                                                ->pluck('program_name')->toArray();
                
                $program_nhtf_names = implode(',', $program_nhtf_names);
                
                $comments = [];

                $required_units = 0;

                $total_project_units = Project::where('id', '=', $audit->project_id)->first()->units()->count();
                

                $units = Unit::whereHas('programs', function ($query) use ($audit, $program_nhtf_ids, $program_keys_with_award_number) {
                                    $query->where('audit_id', '=', $audit->id);
                                    $query->whereIn('program_key', $program_keys_with_award_number);
                                    $query->whereIn('program_key', $program_nhtf_ids);
                })->get();
                

                if(count($units)){
                    $audit->comment = $audit->comment.' | Select Process Starting NHTF for award number '.$nhtf_award_number;
                    $audit->save();
                    

                    $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_nhtf_names.', award number '.$nhtf_award_number;

                    $audit->comment = $audit->comment.' | Select Process Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_nhtf_names.', award number '.$nhtf_award_number;;
                    $audit->save();
                    

                    $units_selected = [];
                    $htc_units_subset = [];
                    
                    $total_units = count($units);
                    

                    $comments[] = 'Total units with NHTF funding is '.$total_units;
                    $comments[] = 'Total units in the project with a program is '.$total_project_units;

                    $audit->comment = $audit->comment.' | Select Process Total units with NHTF funding is '.$total_units;
                    $audit->save();
                    
                    $audit->comment = $audit->comment.' | Select Process Total units in the project with a program is '.$total_project_units;
                    $audit->save();
                    


                    if (count($units) <= 4) {

                        $required_units = count($units); // 100%

                        $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 100);
                        
                        $comments[] = 'Because there are less than 4 NHTF units, the selection is 100%. Total selected: '.count($units_selected);

                        $audit->comment = $audit->comment.' | Select Process Because there are less than 4 NHTF units, the selection is 100%. Total selected: '.count($units_selected);
                        $audit->save();
                        

                    } else {
                        if (ceil($total_units/2) >= ceil($total_project_units/5)) {

                            $required_units = ceil($total_units/2);

                             $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 0, ceil($total_units/2));
                             
                             $comments[] = 'Because there are more than 4 units and because 20% of project units is smaller than 50% of NHTF units, the total selected is '.ceil($total_units/2);
                             $audit->comment = $audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is smaller than 50% of NHTF units, the total selected is '.ceil($total_units/2);

                            $audit->save();
                            
                        } else {

                            if(ceil($total_project_units/5) > $total_units){
                                $required_units = $total_units;
                                $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 0, $total_units);
                                
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of NHTF units, the total selected is '.$total_units. 'which is the total number of units';

                                $audit->comment = $audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of NHTF units, the total selected is '.$total_units. 'which is the total number of units';
                            }else{
                                $required_units = ceil($total_project_units/5);
                                $units_selected = $this->randomSelection($audit,$units->pluck('unit_key')->toArray(), 0, ceil($total_project_units/5));
                                
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of NHTF units, the total selected is '.ceil($total_project_units/5);

                                $audit->comment = $audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of NHTF units, the total selected is '.ceil($total_project_units/5);

                            }
                            $audit->save();
                            
                        }
                    }

                    foreach ($units_selected as $unit_key) {
                        $unit_selected = Unit::where('unit_key','=',$unit_key)->first();
                        
                        $has_htc_funding = 0;

                        $comments[] = 'Checking if HTC funding applies to this unit '.$unit_selected->unit_key.' by cross checking with HTC programs';

                        $audit->comment = $audit->comment.' | Select Process Checking if HTC funding applies to this unit '.$unit_selected->unit_key.' by cross checking with HTC programs';
                            $audit->save();
                            

                        // if units have HTC funding add to subset
                        //$unit = Unit::where('unit_key', '=', $unit_selected)->first();
                        

                        if($unit_selected->has_program_from_array($program_htc_ids, $audit->id)){
                            $has_htc_funding = 1;
                            $comments[] = 'The unit key '.$unit_selected->unit_key.' belongs to a program with HTC funding';
                            $audit->save();
                        }

                        if ($has_htc_funding) {
                            $comments[] = 'We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';

                            $audit->comment = $audit->comment.' | Select Process We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
                                $audit->save();
                                

                            $htc_units_subset[] = $unit_selected->unit_key;
                        }
                    }

                    $htc_units_subset_for_nhtf = $htc_units_subset;
                    $units_to_check_for_overlap = array_merge($units_to_check_for_overlap, $units_selected);
                    

                    $selection[] = [
                        "group_id" => 6,
                        "program_name" => "NHTF",
                        "program_ids" => SystemSetting::get('program_nhtf'),
                        "pool" => count($units),
                        "units" => $units_selected,
                        "totals" => count($units_selected),
                        "required_units" => $required_units,
                        'htc_subset' => $htc_units_subset,
                        "use_limiter" => 0,
                        "comments" => $comments
                    ];
                    

                    
                }else{
                    
                    $htc_units_subset_for_nhtf = array();
                    $audit->comment_system = $audit->comment_system.' | Select Process is not working with NHTF.';
                    $audit->save();
                }
            }
        }else{
            $htc_units_subset_for_nhtf = array();
            $audit->comment_system = $audit->comment_system.' | Select Process is not working with NHTF.';
            $audit->save();
        }


        // check for HOME, OHTF, NHTF overlap and send to analyst
        // overlap contains the keys of units
        $overlap = array();
        $overlap_list = '';
        for ($i=0; $i<count($units_to_check_for_overlap); $i++) {
            
            for ($j=0; $j<count($units_to_check_for_overlap); $j++) {
                
                if ($units_to_check_for_overlap[$i] == $units_to_check_for_overlap[$j] && $i != $j && !in_array($units_to_check_for_overlap[$i], $overlap)) {
                    $overlap[] = $units_to_check_for_overlap[$i];
                    $overlap_list = $overlap_list . $units_to_check_for_overlap[$i].',';
                    
                }
            }
        }

        $comments[] = 'Overlap list to send to analyst: '.$overlap_list;
        $audit->comment = $audit->comment.' | Overlap list to send to analyst: '.$overlap_list;
        $audit->save();

        //
        //
        // 7 - HTC
        // get totals of all units HTC and select all units without NHTF. OHTF and HOME
        // check in project_program->first_year_award_claimed date for the 15 year test
        // after 15 years: 20% of total
        // $program_htc_ids = SystemSetting::get('program_htc'); // already loaded
        //
        //

        
        $comments = [];

        $required_units = 0; // this is computed, not counted!
        $program_htc_ids = explode(',', SystemSetting::get('program_htc'));
         if(!empty(array_intersect($projectProgramIds, $program_htc_ids))) {
            // total HTC funded units (71)
            $audit->comment = $audit->comment.' | Selecting units with HTC at '.date('g:h:i a',time());
            $audit->save();
            // $all_htc_units = Unit::whereHas('programs', function ($query) use ($audit, $program_htc_ids) {
            //                     $query->where('audit_id', '=', $audit->id);
            //                     $query->whereIn('program_key', $program_htc_ids);
            // })->get();
            $all_htc_units = UnitProgram::whereIn('program_key',$program_htc_ids)->where('audit_id',$audit->id)->get();
            

            $total_htc_units = count($all_htc_units);
            

            if($total_htc_units){
                $use_limiter = 1;

                $audit->comment = $audit->comment.' | Select Process Starting HTC.';
                $audit->save();
                

                $comments[] = 'The total of HTC units is '.$total_htc_units.'.';
                $audit->comment = $audit->comment.' | Select Process The total of HTC units is '.$total_htc_units.'.';
                            $audit->save();
                            

                // HTC without HOME, OHTF, NHTF
                // $program_htc_only_ids = array_diff($program_htc_ids, $program_home_ids, $program_ohtf_ids, $program_nhtf_ids);
                // 

                // $program_htc_only_names = Program::whereIn('program_key', $program_htc_only_ids)->get()->pluck('program_name')->toArray();
                // 
                // $program_htc_only_names = implode(',', $program_htc_only_names);
                // 

                // $comments[] = 'Pool of units chosen among units belonging to HTC programs associated with this audit id '.$audit->id.' excluding HOME, OHTF and NHTF. Programs: '.$program_htc_only_names;
                // 

                // $audit->comment = $audit->comment.' | Select Process Pool of units chosen among units belonging to HTC programs associated with this audit id '.$audit->id.' excluding HOME, OHTF and NHTF. Programs: '.$program_htc_only_names;
                //  $audit->save();
                //  

                $units = [];
                foreach ($all_htc_units as $all_htc_unit) {
                    if($all_htc_unit->unit->has_program_from_array($program_home_ids, $audit->id) || 
                        $all_htc_unit->unit->has_program_from_array($program_ohtf_ids, $audit->id) || 
                        $all_htc_unit->unit->has_program_from_array($program_nhtf_ids, $audit->id)){
                        $units[] = $all_htc_unit->unit_key;
                        
                    }
                }

                $comments[] = 'The total of HTC units that have HOME, OHTF and NHTF is '.count($units).'.';
                $audit->comment = $audit->comment.' | Select Process The total of HTC units that have HOME, OHTF and NHTF is '.count($units).'.';
                $audit->save();
                

                // check in project_program->first_year_award_claimed date for the 15 year test
                
                // how many units do we need in the selection accounting for the ones added from HOME, OHTF, NHTF
                
                $htc_units_subset = array_merge($htc_units_subset_for_home, $htc_units_subset_for_ohtf, $htc_units_subset_for_nhtf);
                

                //$number_of_htc_units_required = ceil($total_htc_units/5);
                //$required_units = $number_of_htc_units_required; // that's it, in all cases, that number is 20% of units

                $units_selected = [];
                $units_selected_count = 0;

                //if ($number_of_htc_units_needed > 0 && count($units) > 0) {
                $first_year = null;

                // look at HTC programs, get the most recent year for the check
                $comments[] = 'Going through the HTC programs, we look for the most recent year in the first_year_award_claimed field.';

                $audit->comment = $audit->comment.' | Select Process Going through the HTC programs, we look for the most recent year in the first_year_award_claimed field.';
                $audit->save();
                

                foreach ($project->programs as $program) {
                    
                    // only select HTC project programs
                    if (in_array($program->program_key, $program_htc_ids)) {
                        if ($first_year == null || $first_year < $program->first_year_award_claimed) {
                            $first_year = $program->first_year_award_claimed;
                            $comments[] = 'Program key '.$program->program_key.' has the year '.$program->first_year_award_claimed.'.';
                            $audit->comment = $audit->comment.' | Select Process Program key '.$program->program_key.' has the year '.$program->first_year_award_claimed.'.';
                            $audit->save();
                            
                        }
                    }
                }

                if (idate("Y")-15 > $first_year && $first_year != null) {
                    $first_fifteen_years = 0;
                    $comments[] = 'Based on the year, we determined that the program is not within the first 15 years.';
                    $audit->comment = $audit->comment.' | Select Process Based on the year, we determined that the program is not within the first 15 years.';
                        $audit->save();
                        
                } else {
                    $first_fifteen_years = 1;
                    $comments[] = 'Based on the year, we determined that the program is within the first 15 years.';
                    $audit->comment = $audit->comment.' | Select Process Based on the year, we determined that the program is within the first 15 years.';
                        $audit->save();
                        
                }
                
                if ($first_fifteen_years) {
                    /*
                        // check project for least purchase
                        $leaseProgramKeys = explode(',', SystemSetting::get('lease_purchase'));
                        
                        $comments[] = 'Check if the programs associated with the project correspond to lease purchase using program keys: '.SystemSetting::get('lease_purchase').'.';

                        $audit->comment = $audit->comment.' | Select Process Check if the programs associated with the project correspond to lease purchase using program keys: '.SystemSetting::get('lease_purchase').'.';
                            $audit->save();
                            
                            $leasePurchaseFound = 0;
                            $isLeasePurchase = 0;
                        foreach ($project->programs as $program) {
                            
                            if (in_array($program->program_key, $leaseProgramKeys)) {
                                $isLeasePurchase = 1;
                                $comments[] = 'A program key '.$program->program_key.' confirms that this is a lease purchase.';
                                $audit->comment = $audit->comment.' | Select Process A program key '.$program->program_key.' confirms that this is a lease purchase.';
                                $audit->save();
                                
                                $leasePurchaseFound = 1;
                            } 
                        }

                        if(!$leasePurchaseFound){
                            $comments[] = 'No lease purchase programs found.';
                            $audit->comment = $audit->comment.' | Select Process No lease purchase programs found.';
                                $audit->save();
                                
                        }

                        if ($isLeasePurchase) {

                            $htc_units_without_overlap = Unit::whereHas('programs', function ($query) use ($audit, $program_htc_only_ids) {
                                                            $query->where('audit_id', '=', $audit->id);
                                                            $query->whereIn('program_key', $program_htc_only_ids);
                                                        })->pluck('unit_key')->toArray();

                            $required_units = $this->adjustedLimit($audit, $total_htc_units);

                            if($required_units <= count($htc_units_subset)){
                                $number_of_htc_units_needed = 0;
                            }else{
                                $number_of_htc_units_needed = $required_units - count($htc_units_subset);
                            }

                            $units_selected = $this->randomSelection($audit,$htc_units_without_overlap, 0, $number_of_htc_units_needed);
                            
                            $units_selected_count = count($units_selected);

                            $comments[] = 'It is a lease purchase. Total selected: '.count($units_selected);
                            $audit->comment = $audit->comment.' | Select Process It is a lease purchase. Total selected: '.count($units_selected);
                                $audit->save();
                                

                            $units_selected = array_merge($units_selected, $htc_units_subset_for_home, $htc_units_subset_for_ohtf, $htc_units_subset_for_nhtf);
                            $units_selected_count = $units_selected_count + count($htc_units_subset_for_home) + count($htc_units_subset_for_ohtf) + count($htc_units_subset_for_nhtf);
                            

                            // $units_selected_count isn't using the array_merge to keep the duplicate

                            $selection[] = [
                                "group_id" => 7,
                                "program_name" => "HTC",
                                "building_key" => "",
                                "program_ids" => SystemSetting::get('program_htc'),
                                // "pool" => count($units),
                                "pool" => $total_htc_units,
                                "units" => $units_selected,
                                "totals" => $units_selected_count,
                                "required_units" => $required_units,
                                "use_limiter" => $use_limiter,
                                "comments" => $comments
                            ];
                            
                        } else {
                    */
                   
                    // we don't check for lease purchases anymore

                    $is_multi_building_project = 0;
                    
                    // for each of the current programs+project, check if multiple_building_election_key is 2 for multi building project
                    $comments[] = 'Going through each program to determine if the project is a multi building project by looking for multiple_building_election_key=2.';
                    $audit->comment = $audit->comment.' | Select Process Going through each program to determine if the project is a multi building project by looking for multiple_building_election_key=2.';
                    $audit->save();
                    

                    foreach ($project->programs as $program) {
                        
                        if (in_array($program->program_key, $program_htc_ids)) {
                            if ($program->multiple_building_election_key == 2) {
                                $is_multi_building_project = 1;
                                $comments[] = 'Program key '.$program->program_key.' showed that the project is a multi building project.';
                                $audit->comment = $audit->comment.' | Select Process Program key '.$program->program_key.' showed that the project is a multi building project.';
                                $audit->save();
                                
                            }
                        }
                    }

                    if ($is_multi_building_project) {

                        $htc_units_without_overlap = Unit::whereHas('programs', function ($query) use ($audit, $program_htc_ids, $program_home_ids, $program_ohtf_ids, $program_nhtf_ids) {
                                                    $query->where('audit_id', '=', $audit->id);
                                                    $query->whereIn('program_key', $program_htc_ids);
                                                    $query->whereNotIn('program_key', $program_home_ids);
                                                    $query->whereNotIn('program_key', $program_ohtf_ids);
                                                    $query->whereNotIn('program_key', $program_nhtf_ids);
                                                })->pluck('unit_key')->toArray();

                        $number_of_htc_units_required = $this->adjustedLimit($audit, $total_htc_units);
                        $required_units = $number_of_htc_units_required;
                        //ceil($total_htc_units/10);

                        if($number_of_htc_units_required <= count($htc_units_subset)){
                            $number_of_htc_units_needed = 0;
                            $comments[] ='There are enough HTC units in the previous selections ('.count($htc_units_subset).') to meet the required number of '.$required_units.' units.';
                            $audit->comment = $audit->comment.'There are enough HTC units in the previous selections ('.count($htc_units_subset).') to meet the required number of '.$required_units.' units.';
                            $audit->save();
                        }else{
                            $number_of_htc_units_needed = $number_of_htc_units_required - count($htc_units_subset);
                            $comments[] = 'There are '.count($htc_units_subset).' that are from the previous selection that are automatically included in the HTC selection. We need to select '.$number_of_htc_units_needed.' more units.';
                            $audit->comment = $audit->comment.'There are '.count($htc_units_subset).' that are from the previous selection that are automatically included in the HTC selection. We need to select '.$number_of_htc_units_needed.' more units.';
                                $audit->save();
                        }

                        $units_selected = $this->randomSelection($audit,$htc_units_without_overlap, 0, $number_of_htc_units_needed);
                        
                        $units_selected_count = count($units_selected);

                        

                        $units_selected = array_merge($units_selected, $htc_units_subset_for_home, $htc_units_subset_for_ohtf, $htc_units_subset_for_nhtf);
                        $units_selected_count = $units_selected_count + count($htc_units_subset_for_home) + count($htc_units_subset_for_ohtf) + count($htc_units_subset_for_nhtf);
                        
                        $comments[] = 'Total units selected including overlap : '.$units_selected_count;
                        $audit->comment = $audit->comment.' | Total units selected including overlap : '.$units_selected_count;
                                $audit->save();
                                

                        // $units_selected_count isn't using the array_merge to keep the duplicate

                        $selection[] = [
                            "group_id" => 7,
                            "program_name" => "HTC",
                            "building_key" => "",
                            "program_ids" => SystemSetting::get('program_htc'),
                            // "pool" => count($units),
                            "pool" => $total_htc_units,
                            "units" => $units_selected,
                            "totals" => $units_selected_count,
                            "required_units" => $required_units,
                            "use_limiter" => $use_limiter,
                            "comments" => $comments
                        ];
                        
                    } else {
                        $use_limiter = 0; // we apply the limiter for each building

                        $comments[] = 'The project is not a multi building project.';
                        $audit->comment = $audit->comment.' | Select Process The project is not a multi building project.';
                                $audit->save();
                                
                        // group units by building, then proceed with the random selection
                        // create a new list of units based on building and project key
                        $units_selected = [];
                        $units_selected_count = 0;

                        $required_units = 0; // in the case of buildings, we need to sum each totals because of the rounding
                        
                        $first_building_done = 0; // this is to control the comments to only keep the ones we care about after the first building information is displayed.

                        foreach ($buildings as $building) {
                            
                            if ($building->units) {

                                if($first_building_done){
                                    $comments = array(); // clear the comments.
                                }else{
                                    $first_building_done = 1;
                                }

                                // how many units from the overlap are in that building
                                // list all the units not in the overlap for that building
                                // 
                                // if the 20% of all building's unit is less than the building's units that are in the overlap, done
                                // otherwise get the missing units

                                // we keep the selection and overlaps UP TO the required number for each building
                                // then we apply the limiter for EACH building

                                // $htc_units_subset_for_home, $htc_units_subset_for_ohtf, $htc_units_subset_for_nhtf

                                $htc_units_for_building = Unit::where('building_key', '=', $building->building_key)
                                                ->whereHas('programs', function ($query) use ($audit, $program_htc_ids) {
                                                    $query->where('audit_id', '=', $audit->id);
                                                    $query->whereIn('program_key', $program_htc_ids);
                                                })
                                                ->pluck('unit_key')
                                                ->toArray();

                                $htc_units_without_overlap = Unit::where('building_key', '=', $building->building_key)
                                                ->whereNotIn('unit_key', $htc_units_subset)
                                                ->whereHas('programs', function ($query) use ($audit, $program_htc_ids) {
                                                    $query->where('audit_id', '=', $audit->id);
                                                    $query->whereIn('program_key', $program_htc_ids);
                                                })
                                                ->pluck('unit_key')
                                                ->toArray();

                                $htc_units_with_overlap = Unit::where('building_key', '=', $building->building_key)
                                                ->whereIn('unit_key', $htc_units_subset)
                                                ->whereHas('programs', function ($query) use ($audit, $program_htc_ids) {
                                                    $query->where('audit_id', '=', $audit->id);
                                                    $query->whereIn('program_key', $program_htc_ids);
                                                })
                                                ->pluck('unit_key')
                                                ->toArray();

                                //$required_units_for_that_building = ceil(count($htc_units_for_building)/5);
                                $required_units_for_that_building = $this->adjustedLimit($audit, count($htc_units_for_building));
                                //$required_units = $required_units + $required_units_for_that_building;
                                
                                $required_units = $required_units_for_that_building;

                                // $htc_units_with_overlap_for_that_building = count($htc_units_for_building) - count($htc_units_without_overlap);
                                $htc_units_with_overlap_for_that_building = count($htc_units_with_overlap);

                                // TEST
                                // $overlap_list = '';
                                // foreach($htc_units_subset as $htc_units_subset_key){
                                //     $overlap_list = $overlap_list . $htc_units_subset_key . ',';
                                // }
                                // $comments[] = 'Overlap: '.$overlap_list;
                                // $audit->comment = $audit->comment.' | Overlap: '.$overlap_list;
                                // $audit->save();

                                // $htc_units_for_building_list = '';
                                // foreach($htc_units_for_building as $htc_units_for_building_key){
                                //     $htc_units_for_building_list = $htc_units_for_building_list . $htc_units_for_building_key. ',';
                                // }
                                // $comments[] = 'htc_units_for_building_list: '.$htc_units_for_building_list;
                                // $audit->comment = $audit->comment.' | htc_units_for_building_list: '.$htc_units_for_building_list;
                                // $audit->save();

                                // $htc_units_with_overlap_list = '';
                                // foreach($htc_units_with_overlap as $htc_units_with_overlap_key){
                                //     $htc_units_with_overlap_list = $htc_units_with_overlap_list . $htc_units_with_overlap_key. ',';
                                // }
                                // $comments[] = 'htc_units_with_overlap_list: '.$htc_units_with_overlap_list;
                                // $audit->comment = $audit->comment.' | htc_units_with_overlap_list: '.$htc_units_with_overlap_list;
                                // $audit->save();
                                // END TEST

                                if($required_units_for_that_building >= $htc_units_with_overlap_for_that_building){
                                    // we are missing some units
                                    $number_of_htc_units_needed_for_that_building = $required_units_for_that_building - $htc_units_with_overlap_for_that_building;
                                }else{
                                    // we have enough units
                                    $number_of_htc_units_needed_for_that_building = 0;
                                }
                                
                                $new_building_selection = $this->randomSelection($audit,$htc_units_without_overlap, 0, $number_of_htc_units_needed_for_that_building);
                                
                                //$units_selected_count = $units_selected_count + count($new_building_selection);
                                $units_selected_count = count($new_building_selection);

                                // if(count($new_building_selection)){
                                //     $units_selected = array_merge($units_selected, $new_building_selection);
                                // }
                                
                                $units_selected = $new_building_selection;
                                
                                $comments[] = 'The total of HTC units for building key '.$building->building_key.' is '.count($htc_units_for_building).'. Required units: '.$required_units_for_that_building.'. Overlap units: '.$htc_units_with_overlap_for_that_building.'. Missing units: '.$number_of_htc_units_needed_for_that_building;

                                $audit->comment = $audit->comment.' | Select Process The total of HTC units for building key '.$building->building_key.' is '.count($htc_units_for_building).'. Required units: '.$required_units_for_that_building.'. Overlap units: '.$htc_units_with_overlap_for_that_building.'. Missing units: '.$number_of_htc_units_needed_for_that_building;

                                $audit->save();
                                

                                $comments[] = 'Randomly selected units in building '.$building->building_key.'. Total selected: '.count($new_building_selection).'.';

                                $audit->comment = $audit->comment.' | Select Process Randomly selected units in building '.$building->building_key.'. Total selected: '.count($new_building_selection).'.';
                                $audit->save();
                                


                                $units_selected = array_merge($units_selected, $htc_units_with_overlap);
                                $units_selected = array_slice($units_selected, 0, $required_units_for_that_building); // cap selection to required number
                                $units_selected_count = $units_selected_count + count($htc_units_with_overlap);
                                

                                // $units_selected_count isn't using the array_merge to keep the duplicate

                                $selection[] = [
                                    "group_id" => 7,
                                    "program_name" => "HTC",
                                    "building_key" => $building->building_key,
                                    "program_ids" => SystemSetting::get('program_htc'),
                                    // "pool" => count($units),
                                    "pool" => $total_htc_units,
                                    "units" => $units_selected,
                                    "totals" => $units_selected_count,
                                    "required_units" => $required_units,
                                    "use_limiter" => $use_limiter,
                                    "comments" => $comments
                                ];
                                
                            }
                        }
                    }
                    //}
                } else {
                    // how many $overlap
                    // if required <= $overlap we don't need to select anymore unit
                    // otherwise we need to take all the units NOT in the overlap and randomly pick required - count(overlap)
                    
                    $htc_units_without_overlap = Unit::whereHas('programs', function ($query) use ($audit, $program_htc_ids) {
                                                        $query->where('audit_id', '=', $audit->id);
                                                        $query->whereIn('program_key', $program_htc_ids);
                                                    })->pluck('unit_key')->toArray();

                    // 10% of units
                    $number_of_htc_units_required = ceil($total_htc_units/10);
                    $required_units = $number_of_htc_units_required;

                    if($number_of_htc_units_required <= count($overlap)){
                        $number_of_htc_units_needed = 0;
                    }else{
                        $number_of_htc_units_needed = $number_of_htc_units_required - count($overlap);
                    }

                    $units_selected = $this->randomSelection($audit,$htc_units_without_overlap, 0, $number_of_htc_units_needed);
                    
                    $units_selected_count = count($units_selected);
                    $comments[] = 'Total selected: '.count($units_selected);

                    $audit->comment = $audit->comment.' | Select Process Total selected: '.count($units_selected);
                                    $audit->save();
                                    

                    $units_selected = array_merge($units_selected, $htc_units_subset_for_home, $htc_units_subset_for_ohtf, $htc_units_subset_for_nhtf);
                    $units_selected = array_slice($units_selected, 0, $number_of_htc_units_required);

                    $units_selected_count = $units_selected_count + count($htc_units_subset_for_home) + count($htc_units_subset_for_ohtf) + count($htc_units_subset_for_nhtf);
                    

                    // $units_selected_count isn't using the array_merge to keep the duplicate

                    $selection[] = [
                        "group_id" => 7,
                        "program_name" => "HTC",
                        "building_key" => '',
                        "program_ids" => SystemSetting::get('program_htc'),
                        // "pool" => count($units),
                        "pool" => $total_htc_units,
                        "units" => $units_selected,
                        "totals" => $units_selected_count,
                        "required_units" => $required_units,
                        "use_limiter" => $use_limiter,
                        "comments" => $comments
                    ];
                    

                }
                //}

                // $comments[] = 'Combining HTC total selected: '.count($units_selected).' + '.count($htc_units_subset_for_home).' + '.count($htc_units_subset_for_ohtf).' + '.count($htc_units_subset_for_nhtf);
                // $audit->comment = $audit->comment.' | Combining HTC total selected: '.count($units_selected).' + '.count($htc_units_subset_for_home).' + '.count($htc_units_subset_for_ohtf).' + '.count($htc_units_subset_for_nhtf);
                //         $audit->save();

                // $htc_units_from_home_list = '';
                // foreach($htc_units_subset_for_home as $htc_unit_for_home){
                //     $htc_units_from_home_list = $htc_units_from_home_list . $htc_unit_for_home;
                // }
                // $comments[] = 'HTC units from HOME: '.$htc_units_from_home_list;
                // $audit->comment = $audit->comment.' | HTC units from HOME: '.$htc_units_from_home_list;
                //         $audit->save();     

                
                
            }else{
                $audit->comment_system = $audit->comment_system.' | Select Process is not working with HTC.';
                $audit->save();
            }
        } else {
            $audit->comment_system = $audit->comment_system.' | 2360 Select Process is not working with HTC.';
            $audit->save();
        }

        

        // combineOptimize returns an array [units, summary]
        $optimized_selection = $this->combineOptimize($audit,$selection);
        
        $audit->comment = $audit->comment.' | Select Process Finished - returning results.';
                                $audit->save();
                                
        return [$optimized_selection, $overlap, $project, $organization_id];
    }
    public function createNewProjectDetails($audit){
        //$project = \App\Models\Project::find($audit->project_id);
        
        //$audit->project->set_project_defaults($audit->id);
        
    }
    public function addAmenityInspections(Audit $audit){
        //Project
        // AmenityInspection::where('audit_id',$audit->id)->delete();

        

        // // make sure we don't have name duplicates
        // foreach ($audit->project->amenities as $pa) {
        //     AmenityInspection::insert([
        //         //'name'=>$pa->amenity->amenity_description,
        //         'audit_id'=>$audit->id,
        //         'monitoring_key'=>$audit->monitoring_key,
        //         'project_id'=>$audit->project_id,
        //         'development_key'=>$audit->development_key,
        //         'amenity_id'=>$pa->amenity_id,
        //         'amenity_key'=>$pa->amenity->amenity_key,

        //     ]);
            
        // }
        // foreach ($audit->project->buildings as $b) {
        //     foreach($b->amenities as $ba){
        //        AmenityInspection::insert([
        //             'audit_id'=>$audit->id,
        //             'monitoring_key'=>$audit->monitoring_key,
        //             'building_key'=>$b->building_key,
        //             'building_id'=>$b->id,
        //             'amenity_id'=>$ba->amenity->id,
        //             'amenity_key'=>$ba->amenity->amenity_key,

        //        ]);
               
        //     }
        // }
        // foreach ($audit->unique_unit_inspections as $u) {
        //     foreach($u->amenities as $ua){
        //        AmenityInspection::insert([
        //             'audit_id'=>$audit->id,
        //             'monitoring_key'=>$audit->monitoring_key,
        //             'unit_key'=>$u->unit_key,
        //             'unit_id'=>$u->unit_id,
        //             'amenity_id'=>$ua->amenity_id,
        //             'amenity_key'=>$ua->amenity->amenity_key,

        //        ]);
               
        //     }
        // }

        //Building

        //Unit
    }
    public function updatePlanning($summary = null)
    {
        // create cached audit
        //
        $planning = $this->planning;
        $audit = $this->audit;
        $project = $this->project;
        $project_id = null;
        $development_key = null;
        $project_ref = '';
        $project_name = null;
        $total_buildings = 0;
        $total_units = 0;
        $total_program_units = 0;
        $total_market_rate_units = 0;

        //get program specific details

        $programs = $project->programs;

        $p = 1;

        foreach ($programs as $program) {
            //set names of columns to be updated
            $program_name ='program_'.$p.'_name';
            $program_multibuilding_election = 'program_'.$p.'_multiple_building_election';
            $program_status = 'program_'.$p.'_project_program_status';
            $program_award_number = 'program_'.$p.'_project_program_award_number';
            $program_guide_year = 'program_'.$p.'_project_program_guide_year';
            $project_program_key = 'program_'.$p.'_project_program_key';
            $funding_program_key = 'program_'.$p.'_funding_program_key';
            $project_program_id = 'program_'.$p.'_project_program_id';
            $program_keyed_in_count = 'program_'.$p.'_keyed_in_count';
            $program_calculated_count = 'program_'.$p.'_calculated_count';
            $program_extended_use = 'program_'.$p.'_first_year_award_claimed';
            $program_site_count = 'program_'.$p.'_site_count';
            $program_file_count = 'program_'.$p.'_file_count';

            // set values
            // get the project program
            $this_program_calculated_count = UnitInspection::where('audit_id',$this->audit->id)->where('program_id',$program->program_id)->count();
            $this_program_site_count = UnitInspection::where('audit_id',$this->audit->id)->where('program_id',$program->program_id)->where('is_site_visit',1)->count();
            $this_program_file_count = UnitInspection::where('audit_id',$this->audit->id)->where('program_id',$program->program_id)->where('is_file_audit',1)->count();


            $planning->update([
                $program_name =>$program->program->program_name,
                $program_multibuilding_election => $program->multiple_building_election_key,
                $program_status => $program->status->status_name,
                $program_award_number => $program->award_number,
                $program_guide_year => $program->guide_l_year,
                $project_program_key => $program->project_program_key,
                $funding_program_key => $program->program->funding_program_key,
                $project_program_id => $program->program_id,
                $program_keyed_in_count => $program->total_unit_count,
                $program_calculated_count => $this_program_calculated_count,
                $program_extended_use => $program->first_year_award_claimed,
                $program_site_count => $this_program_site_count,
                $program_file_count => $this_program_file_count,
            ]);

            $p++;

        }
        

        
                $project_id = $project->id;
                $development_key = $project->project_key;
                $project_ref = $project->project_number;
                $project_name = $project->project_name;
                $total_buildings = $project->total_building_count;
                $total_units = $project->total_unit_count;
                $total_program_units = $project->program_units_total();
                $total_market_rate_units = $project->stats_total_market_rate_units();

            
        
        

        //get optimized counts

        $optimized_site = UnitInspection::where('audit_id',$this->audit->id)->where('is_site_visit',1)->groupBy('unit_key')->count();
        $optimized_file = UnitInspection::where('audit_id',$this->audit->id)->where('is_file_audit',1)->groupBy('unit_key')->count();
        

        
           

            $planning->update([
                'audit_id' => $audit->id,
                'project_id' => $project->id,
                'development_key' => $project->project_key,
                'project_name' => $project->project_name,
                'project_number' => $project_ref,
                'total_building_count' => $total_buildings,
                'total_unit_count' => $total_units,
                'total_program_unit_count' => $total_program_units,
                'total_market_rate_unit_count' => $total_market_rate_units,
                'optimized_site_count' => $optimized_site,
                'optimized_file_count' => $optimized_file,
                'run' => 1

            ]);
        

        // $data = [
        //     'event' => 'NewMessage',
        //     'data' => [
        //         'stats_communication_total' => $stats_communication_total
        //     ]
        // ];

        // Redis::publish('communications', json_encode($data));
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        $planning = Planning::where('run',0)->first();
            $this->planning = $planning;
            $this->planning->project_name = date('m/d/Y g:h a', time())." line 52";
            $this->planning->save();
            $this->project = Project::where('project_key',$planning->development_key)->first();
            $this->extended_use = 0;
            if(!is_null($this->planning) && $this->planning->running == 0){
                if(!is_null($this->planning->audit_id)){
                    $this->audit = Audit::find($this->planning->audit_id);
                    $this->audit->comment .= '| Loaded the audit from a previous run. ';
                }else{
                    $audit = New Audit;
                    $audit->project_id = $this->project->id;
                    $audit->development_key = $this->project->project_key;
                    $audit->monitoring_status_type_key = 10;
                    $audit->save();
                    $planning->audit_id = $audit->id;
                    $planning->save();
                    $this->planning = $planning;
                    $this->audit = $audit;
                    $this->audit->comment .= 'Created the audit';
                    $this->audit->save();


                }
                    $this->planning->update(['running'=>1,'projection_year'=> intval(date('Y',time()))]);
                   
                
            } else if(!is_null($planning)) {

            } else {

            }

        if($planning && $planning->run == 0){
            $audit = $this->audit;
            //LOG HERE if it is a brand new audit run

            //LOG HERE if it is a rerun audit and who asked for it

            
            $audit->comment = 'Audit process starting at '.date('m/d/Y h:i:s A',time());
            $audit->comment_system = 'Audit process starting at '.date('m/d/Y h:i:s A',time());
            $audit->save();
            
            
            // //get the current audit units:
            $audit->comment = $audit->comment.' | Fetching Audit Units';
            $audit->comment_system = $audit->comment_system.' | Running Fetch Audit Units, build UnitProgram';
                                        $audit->save();
                                        
            $this->fetchAuditUnits($audit);
            $audit->comment_system = $audit->comment_system.' | Finished Fetch Units';
                                        $audit->save();
                                        
            $check = \App\Models\UnitProgram::where('audit_id',$audit->id)->count();
            //$check = 1;
            

            if ($check > 0) {
                $audit->comment_system = $audit->comment_system.' | UnitProgram has records, we can start the selection process.';
                                        $audit->save();
                                        
                // run the selection process 10 times and keep the best one
                $best_run = null;
                $best_total = null;
                $overlap = null;
                $project = null;
                $organization_id = null;
                

                $timesToRun = SystemSetting::where('key','times_to_run_compliance_selection')->first();
                $timesToRun = $timesToRun->value;

                for ($i=0; $i<$timesToRun; $i++) {
                    $audit->comment_system = $audit->comment_system.' | Running the selectionProcess for the '.$i.' time';
                                        $audit->save();
                    $summary = $this->selectionProcess($audit);
                    
                    //Log::info('audit '.$i.' run;');

                    
                    $audit->comment_system = $audit->comment_system.' | Finished Selection Process for the '.$i.' time';
                                        $audit->save();
                                        

                    if ($summary && (count($summary[0]['grouped']) < $best_total || $best_run == null)) {
                        $best_run = $summary[0];
                        $overlap = $summary[1];
                        $project = $summary[2];
                        $organization_id = $summary[3];
                        $best_total = count($summary[0]['grouped']);
                        
                    }
                }

                // save all units selected in selection table
                if ($best_run) {
                    
                    
                    //Log::info('best run is selected');
                    foreach ($best_run['programs'] as $program) {

                        // SITE AUDIT
                        $unit_keys = $program['units_after_optimization'];

                        

                        $units = Unit::whereIn('unit_key', $unit_keys)->get();
                        

                        $unit_inspections_inserted = 0;

                        foreach ($units as $unit) {
                            
                            if (in_array($unit->unit_key, $overlap)) {
                                $has_overlap = 1;
                            } else {
                                $has_overlap = 0;
                            }

                            $program_keys = explode(',', $program['program_keys']);
                            

                            foreach ($unit->programs as $unit_program) {
                                if (in_array($unit_program->program_key, $program_keys) && $unit_inspections_inserted < $program['required_units']) {
                                    $u = new UnitInspection([
                                        'group' => $program['name'],
                                        'group_id' => $program['group'],
                                        'unit_id' => $unit->id,
                                        'unit_key' => $unit->unit_key,
                                        'unit_name' => $unit->unit_name,
                                        'building_id' => $unit->building_id,
                                        'building_key' => $unit->building_key,
                                        'audit_id' => $audit->id,
                                        'audit_key' => $audit->monitoring_key,
                                        'project_id' => $project->id,
                                        'project_key' => $project->project_key,
                                        'program_key' => $unit_program->program_key,
                                        'program_id' => $unit_program->program_id,
                                        'pm_organization_id' => $organization_id,
                                        'has_overlap' => $has_overlap,
                                        'is_site_visit' => 1,
                                        'is_file_audit' => 0
                                    ]);
                                    $u->save();
                                    $unit_inspections_inserted++;
                                    
                                }
                            }
                        }

                        // FILE AUDIT
                        $unit_keys = $program['units_before_optimization'];

                        

                        $units = Unit::whereIn('unit_key', $unit_keys)->get();
                        

                        $unit_inspections_inserted = 0;

                        foreach ($units as $unit) {
                            
                            if (in_array($unit->unit_key, $overlap)) {
                                $has_overlap = 1;
                            } else {
                                $has_overlap = 0;
                            }

                            $program_keys = explode(',', $program['program_keys']);
                            

                            
                            foreach ($unit->programs as $unit_program) {
                                if (in_array($unit_program->program_key, $program_keys) && $unit_inspections_inserted < count($program['units_before_optimization'])) {

                                    $u = new UnitInspection([
                                        'group' => $program['name'],
                                        'group_id' => $program['group'],
                                        'unit_id' => $unit->id,
                                        'unit_key' => $unit->unit_key,
                                        'unit_name' => $unit->unit_name,
                                        'building_id' => $unit->building_id,
                                        'building_key' => $unit->building_key,
                                        'audit_id' => $audit->id,
                                        'audit_key' => $audit->monitoring_key,
                                        'project_id' => $project->id,
                                        'project_key' => $project->project_key,
                                        'program_key' => $unit_program->program_key,
                                        'program_id' => $unit_program->program_id,
                                        'pm_organization_id' => $organization_id,
                                        'has_overlap' => $has_overlap,
                                        'is_site_visit' => 0,
                                        'is_file_audit' => 1
                                    ]);
                                    $u->save();
                                    $unit_inspections_inserted++;
                                    
                                }
                            }
                        }

                        
                    }
                }
                //LOG::info('unit inspections should be there.');
                //$this->addAmenityInspections($audit);
                $this->updatePlanning($best_run);    // finally create the audit
                //$this->createNewProjectDetails($audit); // create the project details
                
                // LOG SUCCESS HERE
                $this->audit->compliance_run = 1;
                $this->audit->rerun_compliance = 0;
                $this->audit->comment .= 'Audit process finished at '.date('m/d/Y h:i:s A',time()).'.';
                $this->audit->comment_system .= 'Audit process finished at '.date('m/d/Y h:i:s A',time()).'after '.number_format($this->processes).' processes (not counting sub processes on the framework functions.)';

            $audit->save();

            } else {
                $this->audit->comment_system = "Unable to get program units from devco. Cannot run compliance run and generate the audit.";
                $this->audit->comment = "Unable to get program units from devco. Cannot run compliance run and generate the audit.";
                $this->audit->compliance_run = 0;
                $this->audit->rerun_compliance = 0;
                $this->audit->save();
            }
        }else{
                if(!is_null($this->audit)){
                    $this->audit->comment_system = "Run must be 0 to run this compliance check.";
                    $this->audit->comment = "Run must be 0 to run this compliance check.";
                    $this->audit->compliance_run = 0;
                    $this->audit->rerun_compliance = 0;
                    $this->audit->save();
                } else if(!is_null($this->planning)) {
                    $this->planning->project_name = 'Failed to attach audit, and run was not set to 0.';
                    $this->planning->save();
                }
        }
        
             
    }

    
}
