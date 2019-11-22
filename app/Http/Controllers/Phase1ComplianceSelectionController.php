<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\Models\Projection;
use App\Models\CachedAudit;
use App\Models\Program;
use App\Services\DevcoService;
use App\Models\UnitProgram;
use App\Models\UnitGroup;
use App\Models\OrderingBuilding;
use App\Models\OrderingUnit;
use Illuminate\Support\Facades\Redis;
use App\Models\AmenityInspection;
use App\Models\ProjectProgram;
use App\Models\ScheduleDay;
use Auth;
use DB;

class Phase1ComplianceSelection extends Controller
{
    public $audit;
    public $projection;
    public $project;
    public $units;
    public $full_audit;
    // public $program_1_2016_site_count;
    // public $program_1_2016_file_count;
    // public $program_1_2016_percentage_used;

    // public $program_2_2016_site_count;
    // public $program_2_2016_file_count;
    // public $program_2_2016_percentage_used;

    // public $program_3_2016_site_count;
    // public $program_3_2016_file_count;
    // public $program_3_2016_percentage_used;

    // public $program_4_2016_site_count;
    // public $program_4_2016_file_count;
    // public $program_4_2016_percentage_used;

    

    public $program_percentages;
    public $program_htc_ids;
    public $program_bundle_ids;
    public $program_home_ids;
    public $program_ohtf_ids;
    public $program_nhtf_ids;
    public $program_811_ids;
    public $program_medicaid_ids;



    public function __construct()
    {
        //make a new audit for this

        // $test = DB::table('jobs')->where('payload', 'like', '%ComplianceProjectionJob%')->first();
        // if (!is_null($test)) {

            $this->audit = null;
            $this->projection = null;
            $this->project = null;
            $this->units = null;
            $this->full_audit = 1; // set to 1 to run full audit

            //$this->program_1_2016_site_count = null;
            //$this->program_1_2016_file_count = null;
            //$this->program_1_2016_percentage_used = 'NA';
            $this->program_1_2019_percentage_used = 'NA';

            //$this->program_2_2016_site_count = null;
            //$this->program_2_2016_file_count = null;
            //$this->program_2_2016_percentage_used = 'NA';
            $this->program_2_2019_percentage_used = 'NA';

            //$this->program_3_2016_site_count = null;
            //$this->program_3_2016_file_count = null;
            //$this->program_3_2016_percentage_used = 'NA';
            $this->program_3_2019_percentage_used = 'NA';

            //$this->program_4_2016_site_count = null;
            //$this->program_4_2016_file_count = null;
            //$this->program_4_2016_percentage_used = 'NA';
            $this->program_4_2019_percentage_used = 'NA';

            $this->program_percentages = array();
            $this->program_htc_ids = null;
            $this->program_bundle_ids = null;
            $this->program_home_ids = null;
            $this->program_ohtf_ids = null;
            $this->program_nhtf_ids = null;
            $this->program_811_ids = null;
            $this->program_medicaid_ids = null;
                
       //}



        
    }

    public function fetchAuditUnits()
    {
        
                                    
        UnitProgram::where('audit_id', $this->audit->id)->delete();
        UnitInspection::where('audit_id', $this->audit->id)->delete();
        //dd('Deleted!');
        
        $apiConnect = new DevcoService();
        // paths to the info we need: //dd($this->audit, $this->audit->project, $this->audit->project->buildings);
        

        // Get all the units we need to get programs for:

            if($this->project && count($this->project->units) > 0){
                    foreach ($this->project->units as $unit) {
                        
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
                            ////dd($unitProgramData['data']);
                            ////dd($unitProgramData['data'][0]['attributes']['programKey']);
                            $upinserts = array();
                            $uginserts = array();

                            
                            foreach ($projectPrograms as $pp) {
                                

                                $pp = $pp->attributes;
                                if(is_null($pp->endDate) && !$is_market_rate){
                                    
                                    $this->audit->comment = $this->audit->comment.' | Unit Key:'.$pp->unitKey.', Development Program Key:'.$pp->developmentProgramKey.', Start Date:'.date('m/d/Y',strtotime($pp->startDate));
                                    $this->audit->comment_system = $this->audit->comment_system.' | Unit Key:'.$pp->unitKey.', Development Program Key:'.$pp->developmentProgramKey.', Start Date:'.date('m/d/Y',strtotime($pp->startDate));
                                    ////$this->audit->save();

                                    //get the matching program from the developmentProgramKey
                                    $program = ProjectProgram::where('project_program_key',$pp->developmentProgramKey)->with('program')->first();
                                    
                                    $this->audit->comment = $this->audit->comment.' | '.$program->program->program_name.' '.$program->program_id;
                                    $this->audit->comment_system = $this->audit->comment_system.' | '.$program->program->program_name.' '.$program->program_id;
                                    ////$this->audit->save();

                                    if (!is_null($program)) {
                                        $upinserts[] =[
                                            'unit_key'      =>  $unit->unit_key,
                                            'unit_id'       =>  $unit->id,
                                            'program_key'   =>  $program->program_key,
                                            'program_id'    =>  $program->program_id,
                                            'audit_id'      =>  $this->audit->id,
                                            'monitoring_key'=>  $this->audit->monitoring_key,
                                            'project_id'    =>  $this->audit->project_id,
                                            'development_key'=> $this->audit->development_key,
                                            'created_at'    =>  date("Y-m-d g:h:i", time()),
                                            'updated_at'    =>  date("Y-m-d g:h:i", time()),
                                            'project_program_key' => $pp->developmentProgramKey,
                                            'project_program_id' => $program->id
                                        ];

                                        if(count($program->program->groups())){
                                            foreach($program->program->groups() as $group){
                                                $uginserts[] =[
                                                    'unit_key'      =>  $unit->unit_key,
                                                    'unit_id'       =>  $unit->id,
                                                    'group_id'      =>  $group,
                                                    'audit_id'      =>  $this->audit->id,
                                                    'monitoring_key'=>  $this->audit->monitoring_key,
                                                    'project_id'    =>  $this->audit->project_id,
                                                    'development_key'=> $this->audit->development_key,
                                                    'created_at'    =>  date("Y-m-d g:h:i", time()),
                                                    'updated_at'    =>  date("Y-m-d g:h:i", time())
                                                ];
                                            }
                                        }
                                        
                                    

                                    } else {
                                        $this->audit->comment = $this->audit->comment.' | Unable to find program with key '.$pp->developmentProgramKey.' on unit_key'.$unit->unit_key.' for audit'.$this->audit->monitoring_key;
                                        $this->audit->comment_system = $this->audit->comment_system.' | Unable to find program with key '.$pp->developmentProgramKey.' on unit_key'.$unit->unit_key.' for audit'.$this->audit->monitoring_key;
                                        ////$this->audit->save();
                                        //Log::info('Unable to find program with key of '.$unitProgram['attributes']['programKey'].' on unit_key'.$unit->unit_key.' for audit'.$this->audit->monitoring_key);
                                    }
                                } else {
                                    // market rate?
                                    $program = ProjectProgram::where('project_program_key',$pp->developmentProgramKey)->with('program')->first();
                                    if($is_market_rate){
                                        
                                        $this->audit->comment_system = $this->audit->comment_system." | MARKET RATE, CANCELLED:<del>".$program->program->program_name.' '.$program->program_id.'</del>, Start Date:'.date('m/d/Y',strtotime($pp->startDate)).', End Date: '.date('m/d/Y',strtotime($pp->endDate));
                                        ////$this->audit->save();
                                    }else{
                                        
                                        $this->audit->comment_system = $this->audit->comment_system." | CANCELLED:<del>".$program->program->program_name.' '.$program->program_id.'</del>, Start Date:'.date('m/d/Y',strtotime($pp->startDate)).', End Date: '.date('m/d/Y',strtotime($pp->endDate));
                                        ////$this->audit->save();
                                    }
                                    
                                }
                            }
                            // insert here
                            if(count($upinserts)){
                                UnitProgram::insert($upinserts);
                            }
                            if(count($uginserts)){
                                UnitGroup::insert($uginserts);
                            } 
                             unset($upinserts);
                             unset($uginserts);

                        } catch (Exception $e) {
                            
                            ////dd('Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$this->audit->monitoring_key);
                            $this->audit->comment = $this->audit->comment.' | Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$this->audit->monitoring_key;
                            $this->audit->comment_system = $this->audit->comment_system.' | Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$this->audit->id;
                                   // //$this->audit->save();
                        }
                    }
                    $this->units = UnitProgram::where('audit_id',$this->audit->id)->with('unit')->get();
                    $this->audit->comment_system = $this->audit->comment_system.' | Finished Loop of Units';
                    //$this->audit->save();

                    ////dd($this->units); //on 27 20.32 sec
                }else{
                    dd('Project definition:',$this->project,'Units',$this->project->units);
                }
                                    
                
            
    }

    public function adjustedLimit($n, $program_number = 0, $program_year = 0)
    {
        $this->audit->comment = $this->audit->comment.' | Running Adjusted Limiter.';
                                   ////$this->audit->save();
                                    
        // based on $n units, return the corresponding adjusted sample size
        switch (true) {
            case ($n >= 1 && $n <=4):
                
                $this->audit->comment = $this->audit->comment.' | Limiter Count is >= 1 and <=4 - adjusted minimum is '.$n.' of '.$n.'.';
                //$this->audit->save();
                
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: $n given - value: $n given as required amount.";
                }
                return $n;
            break;
            case ($n == 5 || $n == 6):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 5 or 6 - adjusted minimum is '.$n.' of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 5 given - value: $n given as required amount.";
                }
                return 5;
            break;
            case ($n == 7):
                
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 7 - adjusted minimum is 6 of 7.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 6 given - value: $n given as required amount.";
                }
                return 6;
            break;
            case ($n == 8 || $n == 9):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 8 or 9 - adjusted minimum is 7 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 7 given - value: $n given as required amount.";
                }
                return 7;

            break;
            case ($n == 10 || $n == 11):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 10 or 11 - adjusted minimum is 8 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 8 given - value: $n given as required amount.";
                }
                return 8;
            break;
            case ($n == 12 || $n == 13):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 12 or 13 - adjusted minimum is 9 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 9 given - value: $n given as required amount.";
                }
                return 9;
            break;
            case ($n >= 14 && $n <= 16):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 14 or up to 16 - adjusted minimum is 10 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 10 given - value: $n given as required amount.";
                }
                return 10;
            break;
            case ($n >= 17 && $n <= 18):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 17 or up to 18 - adjusted minimum is 11 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 11 given - value: $n given as required amount.";
                }
                return 11;
            break;
            case ($n >= 19 && $n <= 21):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 19 or up to 21 - adjusted minimum is 12 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 12 given - value: $n given as required amount.";
                }
                return 12;
            break;
            case ($n >= 22 && $n <= 25):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 22 or up to 25 - adjusted minimum is 13 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 13 given - value: $n given as required amount.";
                }
                return 13;
            break;
            case ($n >= 26 && $n <= 29):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 26 or up to 29 - adjusted minimum is 14 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 14 given - value: $n given as required amount.";
                }
                return 14;
            break;
            case ($n >= 30 && $n <= 34):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 30 or up to 34 - adjusted minimum is 15 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 15 given - value: $n given as required amount.";
                }
                return 15;
            break;
            case ($n >= 35 && $n <= 40):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 35 or up to 40 - adjusted minimum is 16 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 16 given - value: $n given as required amount.";
                }
                return 16;
            break;
            case ($n >= 41 && $n <= 47):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 41 or up to 47 - adjusted minimum is 17 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 17 given - value: $n given as required amount.";
                }
                return 17;
            break;
            case ($n >= 48 && $n <= 56):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 48 or up to 56 - adjusted minimum is 18 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 18 given - value: $n given as required amount.";
                }
                return 18;
            break;
            case ($n >= 57 && $n <= 67):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 57 or up to 67 - adjusted minimum is 19 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 19 given - value: $n given as required amount.";
                }
                return 19;
            break;
            case ($n >= 68 && $n <= 81):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 68 or up to 81 - adjusted minimum is 20 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 20 given - value: $n given as required amount.";
                }
                return 20;
            break;
            case ($n >= 82 && $n <= 101):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 82 or up to 101 - adjusted minimum is 21 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 21 given - value: $n given as required amount.";
                }
                return 21;
            break;
            case ($n >= 102 && $n <= 130):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 102 or up to 130 - adjusted minimum is 22 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 22 given - value: $n given as required amount.";
                }
                return 22;
            break;
            case ($n >= 131 && $n <= 175):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 131 or up to 175 - adjusted minimum is 23 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 23 given - value: $n given as required amount.";
                }
                return 23;
            break;
            case ($n >= 176 && $n <= 257):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 176 or up to 257 - adjusted minimum is 24 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 24 given - value: $n given as required amount.";
                }
                return 24;
            break;
            case ($n >= 258 && $n <= 449):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 258 or up to 449 - adjusted minimum is 25 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 25 given - value: $n given as required amount.";
                }
                return 25;
            break;
            case ($n >= 450 && $n <= 1461):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is = 450 or up to 1461 - adjusted minimum is 26 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 26 given - value: $n given as required amount.";
                }
                return 26;
            break;
            case ($n >= 1462):
                $this->audit->comment = $this->audit->comment.' | Limiter Count is >= 1462 - adjusted minimum is 27 of '.$n.'.';
                //$this->audit->save();
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: 27 given - value: $n given as required amount.";
                }
                return 27;
            break;
            default:
               
                if($program_number && $program_year){
                    $variableName = 'program_'.$program_number.'_'.$program_year.'_percentage_used';
                    $this->$variableName = "Limiter used - value: $n given - value: 0 given as required amount.";
                }
                 return 0;
        }
    }

    public function randomSelection($units, $percentage = 20, $min = 0, $max = 0)
    {
        $this->audit->comment = $this->audit->comment.' | Starting random selection.';
               // //$this->audit->save();
                
        if ((is_array($units) || is_object($units)) && count($units)) {
            $total = count($units);

            $needed = ceil($total * $percentage / 100);

            if($needed){
                $this->audit->comment = $this->audit->comment.' | Random selection calculated total '.$total.' versus '.$needed.' needed.';
               // //$this->audit->save();
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
            

            $this->audit->comment = $this->audit->comment.' | Random selection adjusted totals based on '.$percentage.'%: total '.$total.', min '.$min.' and '.$needed.' needed.';
               // //$this->audit->save();
                
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
            
            $this->audit->comment = $this->audit->comment.' | Random selection randomized list and returning output to selection process.';
               //$this->audit->save();
                

            return $output;
        } else {
            $this->audit->comment = $this->audit->comment.' | No units were passed in for random selection.';
            //$this->audit->save();
            return [];
            
        }
    }

    public function combineOptimize($selection)
    {
        ////dd($selection);
        // $adjusted_units_count = $this->adjustedLimit(count($units_selected)); //dd($adjusted_units_count);
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
        $this->audit->comment = $this->audit->comment.' | Combine and optimize starting.';
        ////$this->audit->save();
        
        //dd($selection);
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
        $this->audit->comment = $this->audit->comment.' | Combine and optimize created priority table.';
        ////$this->audit->save();
                
       // now we have unit_keys in a priority table
        arsort($priority);
        $this->audit->comment = $this->audit->comment.' | Combine and optimize sorted the table by priority - highest overlap';
        ////$this->audit->save();
                
        for ($i=0; $i < count($selection); $i++) {
            $summary['programs'][$i]['name'] = $selection[$i]['program_name'];
            $summary['programs'][$i]['group'] = $selection[$i]['group_id'];
            $this->audit->comment = $this->audit->comment.' | DEBUG COMPLIANCE SELECTION LINE 348: Combine and optimize created the group $summary[\'programs\']['.$i.'][\'group\'] = '.($i + 1);
            ////$this->audit->save();
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
                $this->audit->comment = $this->audit->comment.' | Combine and optimize used limiter on selection['.$i.'].';
                ////$this->audit->save();
                
                $needed = $this->adjustedLimit(count($selection[$i]['units']));

                $summary['programs'][$i]['required_units'] = $needed;

                foreach ($priority as $p => $val) {
                    if (in_array($p, $selection[$i]['units']) && count($tmp_selection) < $needed) {
                        $tmp_selection[] = $p;
                    }
                    
                }

                // check if we need more
                if (count($tmp_selection) < $needed) {
                    $this->audit->comment = $this->audit->comment.' | Combine and optimize determined the '.count($tmp_selection).' temporary selection is < '.$needed.' needed.';
                    ////$this->audit->save();
                    
                    for ($j=0; $j<count($selection[$i]['units']); $j++) {
                        
                        if (!in_array($selection[$i]['units'][$j], $tmp_selection) && count($tmp_selection) < $needed) {
                            $tmp_selection[] = $selection[$i]['units'][$j];
                            $this->audit->comment = $this->audit->comment.' | Combine and optimize added $selection['.$i.'][\'units\']['.$j.'] to list.';
                            ////$this->audit->save();
                            
                        }
                    }
                    $this->audit->comment = $this->audit->comment.' | Combine and optimize finished adding to the list to meet compliance.';
                            ////$this->audit->save();
                            
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
              $this->audit->comment = $this->audit->comment.' | Combine and optimize total after optimization is '.count($tmp_program_output).'.';
              ////$this->audit->save();
              $summary['programs'][$i]['units_after_optimization'] = $tmp_program_output;
              
        }

        ////dd(array_unique($output), $output);

        $summary['ungrouped'] = $output;
        $summary['grouped'] = array_unique($output);

        $this->audit->comment = $this->audit->comment.' | Combine and optimize finished process returning to selection process.';
         //$this->audit->save();
         
        return $summary;
    }

    public function selectionProcess()
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

        $this->audit->comment = $this->audit->comment.' | Select Process Started';
        $this->audit->comment_system = $this->audit->comment_system.' | Select Process Started for audit '.$this->audit->id;
            ////$this->audit->save();
            
        // is the project processing all the buildings together? or do we have a combination of grouped buildings and single buildings?
        
        $this->audit->comment_system = $this->audit->comment_system.' | Select Process Has Selected Project ID '.$this->audit->project_id;
            ////$this->audit->save();
            

        if(!$this->project) {
            Log::error('Audit '.$this->audit->id.' does not have a project somehow...');
            $this->audit->comment_system = $this->audit->comment_system.' | Error, this audit isn\'t associated with a project somehow...';
            $this->audit->comment = $this->audit->comment.' | Error, this audit isn\'t associated with a project somehow...';
            //$this->audit->save();
            
            return "Error, this audit isn't associated with a project somehow...";
        }

        if (!$this->project->programs) {
            Log::error('Error, the project does not have a program.');
            $this->audit->comment = $this->audit->comment.' | Error, the project does not have a program.';
            $this->audit->comment_system = $this->audit->comment_system.' | Error, the project does not have a program.';
            ////$this->audit->save();
            
            return "Error, this project doesn't have a program.";
            
        }

        $projectProgramIds = $this->project->programs->pluck('program_key')->all();
        ////dd($projectProgramIds);
        
        $this->audit->comment_system = $this->audit->comment_system.' | Select Process Checked the Programs and that there are Programs';
            ////$this->audit->save();
            

        
        

        $this->audit->comment_system = $this->audit->comment_system.' | Select Process Found '.$this->project->total_building_count.' Total Buildings and '.$this->project->total_unit_count.' Total Units';
            ////$this->audit->save();
            
        //Log::info('509:: total buildings and units '.$this->project->total_building_count.', '.$this->project->total_unit_count.' respectively.');
        if($this->full_audit){
            $pm_contact = ProjectContactRole::where('project_id', '=', $this->audit->project_id)
                                    ->where('project_role_key', '=', 21)
                                    ->with('organization')
                                    ->first();
                                    
            //Log::info('514:: pm contact found');

            $this->audit->comment_system = $this->audit->comment_system.' | Select Process Selected the PM Contact';
                ////$this->audit->save();
                
            $organization_id = null;
            if ($pm_contact) {
                $this->audit->comment_system = $this->audit->comment_system.' | Select Process Confirmed PM Contact';
                ////$this->audit->save();
                
                if ($pm_contact->organization) {
                    $organization_id = $pm_contact->organization->id;
                    //Log::info('519:: pm organization identified');
                    $this->audit->comment_system = $this->audit->comment_system.' | Select Process Updated the Organization ID';
                    ////$this->audit->save();
                    
                }
            }
        } else {
            $this->audit->comment_system = $this->audit->comment_system.' | Select Process Skipped PM and Organization as this is a simplified audit entry';
                ////$this->audit->save();
        }

        
        
        if($this->full_audit){
            // save all buildings in building_inspection table
            
            //Log::info('526:: buildings saved.');
            // remove any data
            BuildingInspection::where('audit_id', '=', $this->audit->id)->delete();
            
            //Log::info('529:: building inspections deleted');
            $this->audit->comment_system = $this->audit->comment_system.' | Select Process Deleted all the current building cache for this audit id.';
                ////$this->audit->save();
                
                $buildingCount = 0; 
            if ($this->project->buildings) {
                $buildingCount = count($this->project->buildings);
                foreach ($this->project->buildings as $building) {
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

                    $b[] = [
                        'building_id' => $building->id,
                        'building_key' => $building->building_key,
                        'building_name' => $building->building_name,
                        'address' => $address,
                        'city' => $city,
                        'state' => $state,
                        'zip' => $zip,
                        'audit_id' => $this->audit->id,
                        'audit_key' => $this->audit->monitoring_key,
                        'project_id' => $this->project->id,
                        'project_key' => $this->project->project_key,
                        'pm_organization_id' => $organization_id,
                        'auditors' => null,
                        'nlt_count' => 0,
                        'lt_count' => 0,
                        'followup_count' => 0,
                        'complete' => 0,
                        'submitted_date_time' => null
                    ];

                    
                    //Log::info('565:: '.$b->id.' building inspection added');
                }
                // insert the array enmasse to make it faster.
                BuildingInspection::insert($b);
                
                $this->audit->comment = $this->audit->comment.' | Select Process Put in '.$buildingCount.' Buildings';
                $this->audit->comment_system = $this->audit->comment_system.' | Select Process Put in '.$buildingCount.' Buildings';
                ////$this->audit->save();
                
            } else {
                $this->audit->comment = $this->audit->comment.' | Select Process Found 0 Active Buildings';
                $this->audit->comment_system = $this->audit->comment_system.' | Select Process Found 0 Active Buildings';
                ////$this->audit->save();
            }
        } else {
             $this->audit->comment_system = $this->audit->comment_system.' | Select Process Skipped Building Inspection Creation as this is a simplified audit entry.';
                ////$this->audit->save();
        }

        $selection = [];

        $this->program_htc_ids = explode(',', SystemSetting::get('program_htc'));
        
        //
        //
        // 1 - FAF || NSP || TCE || RTCAP || 811 units
        // total for all those programs combined
        //
        //
        //$this->audit->save();

        $comments = [];

        $required_units = 0;

        $this->program_bundle_ids = explode(',', SystemSetting::get('program_bundle'));
        
        

        /////// DO NOT DO ANY OF THE FOLLOWING IF THE PROJECT DOES NOT HAVE ONE OF THESE PROGRAMS....

        if(!empty(array_intersect($projectProgramIds, $this->program_bundle_ids))) {
            $this->audit->comment_system = $this->audit->comment_system.' | Project has one of the program bundle ids.';
            ////$this->audit->save();


            $program_bundle_names = $this->project->programs->whereIn('program_key', $this->program_bundle_ids)->pluck('program.program_name')->all();
            $this->audit->comment_system = $this->audit->comment_system.' | Built Program Names.';
            ////$this->audit->save();
            
            $program_bundle_names = implode(',', $program_bundle_names);
            

            $units = $this->units->whereIn('program_key',$this->program_bundle_ids)->where('audit_id',$this->audit->id);
            //$unitTest = $this->units->where('unit_key',382905);
            //dd($projectProgramIds,$this->units,$units,$this->audit->id,$this->program_bundle_ids,$unitTest);
            if(!is_null($units)){
                $total = count($units);
                $this->audit->comment_system = $this->audit->comment_system.' | Obtained '.$total.' units within the program bundle. '.date('g:h:i a',time());
                ////$this->audit->save();
            }else{
                $total = 0;
                $this->audit->comment_system = $this->audit->comment_system.' | Obtained '.$total.' units within the program bundle. '.date('g:h:i a',time());
                ////$this->audit->save();

            }
            

            ////dd('Line 767 - Ran and got program bundles - ',$program_bundle_names,$this->project->programs);
            $this->program_percentages['BUNDLE']['percent']='NA';
            //$this->program_percentages['BUNDLE']['_2016_count'] = null;
            if($total){
                $this->audit->comment = $this->audit->comment.' | Select Process starting Group 1 selection ';
                ////$this->audit->save();
                

                $comments[] = 'Pool of units chosen using audit id '.$this->audit->id.' and a list of programs: '.$program_bundle_names;
                $this->audit->comment = $this->audit->comment.' | Pool of units chosen using audit id '.$this->audit->id.' and a list of programs: '.$program_bundle_names;
            
                ////$this->audit->save();
                

                $comments[] = 'Total units in the pool is '.$total;
                $this->audit->comment = $this->audit->comment. ' | Total units in the pool is '.$total;
                $this->audit->comment_system = $this->audit->comment_system. ' | Total units in the pool is '.$total;
                ////$this->audit->save();
                
                $this->program_htc_ids = explode(',', SystemSetting::get('program_htc'));
                
                //OLD
                // $program_htc_names = Program::whereIn('program_key', $this->program_htc_ids)->get()->pluck('program_name')->toArray();
                //NO NEW QUERY
                $program_htc_names = $this->project->programs->whereIn('program_key',$this->program_htc_ids)->pluck('program.program_name')->all();
                
                $program_htc_names = implode(',', $program_htc_names);
                

                // cannot use overlap like this anymore
                // instead for each unit, check if a HTC program is associated
                // $program_htc_overlap = array_intersect($this->program_htc_ids, $this->program_bundle_ids);
                // 
                // $program_htc_overlap_names = Program::whereIn('program_key', $program_htc_overlap)->get()->pluck('program_name')->toArray(); // 30001,30043
                // 
                // $program_htc_overlap_names = implode(',', $program_htc_overlap_names);
                // 
                // $comments[] = 'Identified the program keys that have HTC funding: '.$program_htc_overlap_names;
                // $this->audit->comment = $this->audit->comment.' | Identified the program keys that have HTC funding: '.$program_htc_overlap_names;
                // //$this->audit->save();
                // 

                $has_htc_funding = 0;
                $unitProcessCount = 0;
                
                ////dd('811 Time to get to the htc funding check',$has_htc_funding);
                foreach ($units as $unit) {
                    
                    
                    if($unit->unit->has_program_from_array($this->program_htc_ids, $this->audit->id)){
                        $has_htc_funding = 1;
                        $comments[] = 'The unit key '.$unit->unit_key.' belongs to a program with HTC funding';
                        $this->audit->comment_system = $this->audit->comment_system.'The unit key '.$unit->unit_key.' belongs to a program with HTC funding';
                    }
                }

                ////dd('822 Ran the htc funding check',$has_htc_funding);
           

                // $number_of_units_required = ceil($total/5);

                // are there units with HTC funding?
                if (!$has_htc_funding) {
                    $comments[] = 'By checking each unit and associated programs with HTC funding, we determined that no HTC funding exists for this pool';
                    $this->audit->comment = $this->audit->comment.' | By checking each unit and associated programs with HTC funding, we determined that no HTC funding exists for this pool';

                    $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 20);
                    //dd('852 Random Unit Selection output:',$units_selected);
                    //$required_units = count($units_selected);
                    $required_units = ceil($total/5);

                    $this->program_percentages['BUNDLE']['percent'] = '20% of Bundle Total';
                    //$this->program_percentages['BUNDLE']['_2016_count'] = $required_units;


                    $comments[] = '20% of the pool is randomly selected. Total selected: '.count($units_selected);
                     $this->audit->comment = $this->audit->comment.' | 20% of the pool is randomly selected. Total selected: '.count($units_selected);
                     
                    ////$this->audit->save();
                    
                
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
                    $this->audit->comment = $this->audit->comment.' | By checking each unit and associated programs with HTC funding, we determined that there is HTC funding for this pool';
                    ////$this->audit->save();
                    

                    // check in project_program->first_year_award_claimed date for the 15 year test
                
                    $first_year = null;

                    // look at HTC programs, get the most recent year for the check
                    $comments[] = 'Going through the HTC programs, we look for the most recent year in the first_year_award_claimed field.';
                    $this->audit->comment = $this->audit->comment.' | Going through the HTC programs, we look for the most recent year in the first_year_award_claimed field.';
                    ////$this->audit->save();
                    
                    foreach ($this->project->programs as $program) {
                        
                        if (isset($program_htc_overlap) && in_array($program->program_key, $program_htc_overlap)) {
                            if ($first_year == null || $first_year < $program->first_year_award_claimed) {
                                $first_year = $program->first_year_award_claimed;
                                $comments[] = 'Program key '.$program->program_key.' has the year '.$program->first_year_award_claimed.'.';
                                $this->audit->comment = $this->audit->comment.' | Program key '.$program->program_key.' has the year '.$program->first_year_award_claimed.'.';
                                ////$this->audit->save();
                                
                            }
                        }
                    }

                    if (idate("Y")-14 > $first_year && $first_year != null) {
                        $first_fifteen_years = 0;
                        $comments[] = 'Based on the year, we determined that the program is not within the first 15 years.';
                        $this->audit->comment = $this->audit->comment.' | Based on the year, we determined that the program is not within the first 15 years.';
                        ////$this->audit->save();
                        

                    } else {
                        $first_fifteen_years = 1;
                        $comments[] = 'Based on the year,'.$first_year.' we determined that the program is within the first 15 years.';
                        $this->audit->comment = $this->audit->comment.' | Based on the year '.$first_year.', we determined that the program is within the first 15 years.';
                        ////$this->audit->save();
                        
                    }
                    
                    if ($first_fifteen_years) {
                        // check project for least purchase
                        $leaseProgramKeys = explode(',', SystemSetting::get('lease_purchase'));
                        
                        // $comments[] = 'Check if the programs associated with the project correspond to lease purchase using program keys: '.SystemSetting::get('lease_purchase').'.';
                        // $this->audit->comment = $this->audit->comment.' | Check if the programs associated with the project correspond to lease purchase using program keys: '.SystemSetting::get('lease_purchase').'.';
                        // //$this->audit->save();
                        // 

                        /*    
                            foreach ($this->project->programs as $program) {
                                
                                if (in_array($program->program_key, $leaseProgramKeys)) {
                                    $isLeasePurchase = 1;
                                    $comments[] = 'A program key '.$program->program_key.' confirms that this is a lease purchase.';
                                    $this->audit->comment = $this->audit->comment.' | A program key '.$program->program_key.' confirms that this is a lease purchase.';
                                    //$this->audit->save();

                                } else {
                                    $isLeasePurchase = 0;
                                }
                            }


                            if ($isLeasePurchase) {
                                $required_units = $this->adjustedLimit(count($units));

                                $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, $required_units);

                                //$required_units = count($units_selected);
                                //$required_units = $number_of_units_required;

                                $comments[] = $required_units.' must be randomly selected. Total selected: '.count($units_selected);
                                $this->audit->comment = $this->audit->comment.' | '.$required_units.' must be randomly selected. Total selected: '.count($units_selected);
                                    //$this->audit->save();
                                    
                    
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
                        $this->audit->comment = $this->audit->comment.' | Going through each program to determine if the project is a multi building project by looking for multiple_building_election_key=2.';
                            ////$this->audit->save();
                            

                        foreach ($this->project->programs as $program) {
                            
                            if (in_array($program->program_key, $this->program_bundle_ids)) {
                                if ($program->multiple_building_election_key == 2) {
                                    $is_multi_building_project = 1;
                                    $comments[] = 'Program key '.$program->program_key.' showed that the project is a multi building project.';
                                    $this->audit->comment = $this->audit->comment.' | Program key '.$program->program_key.' showed that the project is a multi building project.';
                                    ////$this->audit->save();
                                    
                                }
                            }
                        }

                        if ($is_multi_building_project) {
                            $this->audit->comment = $this->audit->comment.' | This is a multi-building elected project setting the adjusted limit accordingly.';
                            ////$this->audit->save();
                            $required_units = $this->adjustedLimit(count($units));

                            //$_2016_total = count($units)/5;
                            $this->program_percentages['BUNDLE']['percent'] = '20% of Bundle Total';
                            //$this->program_percentages['BUNDLE']['_2016_count'] = $_2016_total;

                            $this->audit->comment = $this->audit->comment.' | Set the adjusted limit based on the chart to '.$required_units.'.';
                            ////$this->audit->save();

                            $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, $required_units);
                            //dd('1002 Random Unit Selection output:',$units_selected);
                            $this->audit->comment = $this->audit->comment.' | Performed the random selection from the audit.';
                            ////$this->audit->save();
                            

                            //$required_units = count($units_selected);
                            // $required_units = $number_of_units_required;

                            $comments[] = $required_units.' must be randomly selected. Total selected: '.count($units_selected);

                            $this->audit->comment = $this->audit->comment.' | '.$required_units.' must be randomly selected. Total selected: '.count($units_selected);
                                    ////$this->audit->save();
                                    
            
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
                            $this->audit->comment = $this->audit->comment.' | The project is not a multi building project.';
                                    ////$this->audit->save();
                                    
                            // group units by building, then proceed with the random selection
                            // create a new list of units based on building and project key
                            $units_selected = [];

                            $first_building_done = 0; // this is to control the comments to only keep the ones we care about after the first building information is displayed.
                            //$_2016_total = 0;

                            foreach ($this->project->buildings as $building) {
                                
                                if($first_building_done){
                                    $comments = array(); // clear the comments.
                                }else{
                                    $first_building_done = 1;
                                }

                                // $units_for_that_building = Unit::where('building_key', '=', $building->building_key)
                                //                 ->whereHas('programs', function ($query) {
                                //                     $query->where('monitoring_key', '=', $this->audit->monitoring_key);
                                //                     $query->whereIn('program_key', $this->program_bundle_ids);
                                //                 })
                                //                 ->pluck('unit_key')
                                //                 ->toArray();

                                $units_for_that_building = $this->units->whereIn('program_key',$this->program_bundle_ids)->where('unit.building_key',$building->building_key)->pluck('unit_key')->all();

                                //dd('1182 -- VERIFY THIS QUERY WORKS - ',$units_for_that_building,$units_for_that_building2);
                                /// comment out original and rename variable if the revised query works.

                                // $required_units_for_that_building = ceil(count($units_for_that_building)/5);
                                $required_units_for_that_building = $this->adjustedLimit(count($units_for_that_building));

                                $required_units = $required_units_for_that_building;
                                //$_2016_total += count($units_for_that_building)/5;
                                $this->program_percentages['BUNDLE']['percent'] = '20% of Bundle Total Per Building';
                               // $this->program_percentages['BUNDLE']['_2016_count'] = $_2016_total;

                                $new_building_selection = $this->randomSelection($units_for_that_building, 0, $required_units);
                                //dd('1064 Random Unit Selection output:'.$new_building_selection);
                                $units_selected = $new_building_selection;
                                $units_selected_count = count($new_building_selection);

                                $comments[] = $required_units.' of building key '.$building->building_key.' must be randomly selected. Total selected: '.count($new_building_selection).'.';
                                $this->audit->comment = $this->audit->comment.' | '.$required_units.' of building key '.$building->building_key.' must be randomly selected. Total selected: '.count($new_building_selection).'.';
                                    ////$this->audit->save();
                                    

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
                        // $required_units = $this->adjustedLimit(count($units));

                        $required_units = ceil($total/10); // 10% of units
                        $this->program_percentages['BUNDLE']['percent'] = '10% of Bundle';
                        //$this->program_percentages['BUNDLE']['_2016_count'] = $required_units;

                        $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 10);
                        //dd('1096 Random Unit Selection output:',$units_selected);

                        // $required_units = count($units_selected);
                        
                        $comments[] = ' 10% are randomly selected. Total selected: '.count($units_selected);
                        $this->audit->comment = $this->audit->comment.' | 10% are randomly selected. Total selected: '.count($units_selected);
                                        //$this->audit->save();
                                        

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

                $this->audit->comment_system = $this->audit->comment_system.' | Select Process is not working with group 1.';
                //$this->audit->save();
            }
        } else {
            $this->audit->comment_system = $this->audit->comment_system.' | This project does not have any programs in the program bundle group.';
            //$this->audit->save();

        }


        //
        //
        // 2 - 811 units
        // 100% selection
        // for units with 811 funding
        //
        //
        
        
        $this->program_811_ids = explode(',', SystemSetting::get('program_811'));
        

        ///// DO NOT DO ANY OF THE FOLLOWING IF THE PROJECT DOES NOT HAVE 811
        if(!empty(array_intersect($projectProgramIds, $this->program_811_ids))) {
            //$program_811_names = Program::whereIn('program_key', $this->program_811_ids)->get()->pluck('program_name')->toArray();
            $program_811_names = $this->project->programs->whereIn('program_key',  $this->program_811_ids)
                                                ->pluck('program.program_name')->toArray();
            
            $program_811_names = implode(',', $program_811_names);
            
            $comments = [];

            $required_units = 0;

            // $units = Unit::whereHas('programs', function ($query) use ($this->program_811_ids) {
            //                     $query->where('audit_id', '=', $this->audit->id);
            //                     $query->whereIn('program_key', $this->program_811_ids);
            // })->get();
            $units = $this->units->whereIn('program_key',$this->program_811_ids);
            $this->program_percentages['811']['percent']='NA';
            //$this->program_percentages['811']['_2016_count'] = null;

            if(count($units)){

                $required_units = count($units);
                $this->program_percentages['811']['percent']='100%';
                //$this->program_percentages['811']['_2016_count']=$required_units;

                $this->audit->comment = $this->audit->comment.' | Select Process starting 811 selection ';
                //$this->audit->save();
                

                $units_selected = $units->pluck('unit_key')->toArray();
                

                $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$this->audit->id.'. Programs: '.$program_811_names;
                $comments[] = 'Total units in the pool is '.count($units);
                $comments[] = '100% of units selected:'.count($units_selected);
                $this->audit->comment = $this->audit->comment.' | Select Process Pool of units chosen among units belonging to programs associated with this audit id '.$this->audit->id.'. Programs: '.$program_811_names.' | Select Process Total units in the pool is '.count($units).' | Select Process 100% of units selected:'.count($units_selected);
                    //$this->audit->save();
                    
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

                $this->audit->comment_system = $this->audit->comment_system.' | Select Process is not working with 811.';
                //$this->audit->save();
            }
        }else{
            $this->audit->comment_system = $this->audit->comment_system.' | Select Process is not working with 811.';
            //$this->audit->save();
        }


        //
        //
        // 3 - Medicaid units
        // 100% selection
        //
        //
        
        

        $this->program_medicaid_ids = explode(',', SystemSetting::get('program_medicaid'));
        

        if(!empty(array_intersect($projectProgramIds, $this->program_medicaid_ids))) {
            //$program_medicaid_names = Program::whereIn('program_key', $this->program_medicaid_ids)->get()->pluck('program_name')->toArray();
            $program_medicaid_names = $this->project->programs->whereIn('program_key',  $this->program_medicaid_ids)
                                                ->pluck('program.program_name')->toArray();
            $program_medicaid_names = implode(',', $program_medicaid_names);
            
            $comments = [];

            $required_units = 0;

            // $units = Unit::whereHas('programs', function ($query) use ($this->program_medicaid_ids) {
            //                     $query->where('audit_id', '=', $this->audit->id);
            //                     $query->whereIn('program_key', $this->program_medicaid_ids);
            // })->get();
            $units = $this->units->whereIn('program_key',$this->program_medicaid_ids);
            
            $this->program_percentages['MEDICAID']['percent']='NA';
            //$this->program_percentages['MEDICAID']['_2016_count'] = null;
            if(count($units)){
                $this->audit->comment = $this->audit->comment.' | Select Process starting Medicaid selection ';
                //$this->audit->save();
                

                $required_units = count($units);
                $this->program_percentages['MEDICAID']['percent']='100%';
                //$this->program_percentages['MEDICAID']['_2016_count']=$_2016_total;

                $units_selected = $units->pluck('unit_key')->toArray();
                

                $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$this->audit->id.'. Programs: '.$program_medicaid_names;
                $comments[] = 'Total units in the pool is '.count($units);
                $comments[] = '100% of units selected:'.count($units_selected);

                $this->audit->comment = $this->audit->comment.' | Select Process Pool of units chosen among units belonging to programs associated with this audit id '.$this->audit->id.'. Programs: '.$program_medicaid_names.' | Select Process Total units in the pool is '.count($units).' | Select Process 100% of units selected:'.count($units_selected);
                    //$this->audit->save();
                    

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
                $this->audit->comment_system = $this->audit->comment_system.' | Select Process is not working with Medicaid.';
                //$this->audit->save();
            }
        }else{
            $this->audit->comment_system = $this->audit->comment_system.' | Select Process is not working with Medicaid.';
            //$this->audit->save();
        }


        //
        //
        // 4 - HOME
        //
        //
        

        $units_to_check_for_overlap = [];
        $htc_units_subset_for_home = array();

        $this->program_home_ids = explode(',', SystemSetting::get('program_home'));

        if(!empty(array_intersect($projectProgramIds, $this->program_home_ids))) {
            $this->audit->comment_system = $this->audit->comment_system.' | Started HOME, got ids from system settings.';
            //$this->audit->save();

            //$home_award_numbers = ProjectProgram::whereIn('program_key', $this->program_home_ids)->where('project_id', '=', $this->audit->project_id)->select('award_number')->groupBy('award_number')->orderBy('award_number', 'ASC')->get();
            $home_award_numbers = $this->project->programs->whereIn('program_key', $this->program_home_ids)->pluck('award_number');
            ////dd('1286 - home award time to get new home award numbers.');

            $this->audit->comment_system = $this->audit->comment_system.' | Got home award numbers.';
            //$this->audit->save();

            foreach($home_award_numbers as $home_award_number){
                // for each award_number, create a different HOME group
                $this->audit->comment_system = $this->audit->comment_system.' | Home award number '.$home_award_number.' being processed.';
                ////$this->audit->save();
                ////dd($home_award_number);
                // programs with that award_number
                $program_keys_with_award_number = $this->project->programs->where('award_number',$home_award_number)->pluck('program_key')->all(); 
                $this->audit->comment_system = $this->audit->comment_system.' | Select programs with that award number.';
                ////$this->audit->save();
                ////dd('1301 current',$program_keys_with_award_number);

                $program_home_names = $this->project->programs->whereIn('program_key', $this->program_home_ids)
                                                ->whereIn('program_key', $program_keys_with_award_number)
                                                ->pluck('program.program_name')->toArray();

                $this->audit->comment_system = $this->audit->comment_system.' | Selected program names.';
                ////$this->audit->save();

                
                $program_home_names = implode(',', $program_home_names);
                ////dd('1312 current',$program_home_names);
                $comments = [];

                $required_units = 0;

                $total_project_units = $this->project->stats_total_units();

                ////dd($total_project_units);

                $this->audit->comment_system = $this->audit->comment_system.' | Counting project units: '.$total_project_units;
                ////$this->audit->save();
                

                $this->audit->comment_system = $this->audit->comment_system.' | Selecting Units using using settings at '.date('g:h:i a',time());
                ////$this->audit->save();

                // $units = Unit::whereHas('programs', function ($query) use ($this->program_home_ids, $program_keys_with_award_number) {
                //                     $query->where('audit_id', '=', $this->audit->id);
                //                     $query->whereIn('program_key', $program_keys_with_award_number);
                //                     $query->whereIn('program_key', $this->program_home_ids);
                // })->get();

                $units = $this->units->whereIn('program_key',$program_keys_with_award_number)->whereIn('program_key', $this->program_home_ids);



                $this->audit->comment_system = $this->audit->comment_system.' | Finished selecting units at '.date('g:h:i a',time()).'.';
                ////$this->audit->save();
                
                $this->audit->comment_system = $this->audit->comment_system.' | Total selected units '.count($units);
                ////$this->audit->save();
                ////dd('1336', $units);
                $this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['percent']='NA';
                //$this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['_2016_count'] = null;
                //dd('HOME'.str_replace(' ','',str_replace('-', '', $home_award_number)),$this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['_2016_count']);
                if((is_array($units) || is_object($units)) && count($units)){
                    $this->audit->comment = $this->audit->comment.' | Select Process starting Home selection for award number '.$home_award_number;
                    ////$this->audit->save();
                    

                    $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$this->audit->id.'. Programs: '.$program_home_names.', award number '.$home_award_number;

                    $this->audit->comment = $this->audit->comment.' | Select Process Pool of units chosen among units belonging to programs associated with this audit id '.$this->audit->id.'. Programs: '.$program_home_names.', award number '.$home_award_number;
                    ////$this->audit->save();
                    

                    $total_unit_count = count($units);
                    


                    // $program_htc_overlap = array_intersect($program_htc_ids, $this->program_home_ids);
                    // 
                    // $program_htc_overlap_names = Program::whereIn('program_key', $program_htc_overlap)->get()->pluck('program_name')->toArray();
                    // 
                    // $program_htc_overlap_names = implode(',', $program_htc_overlap_names);
                    // 

                    $units_selected = [];
                    $htc_units_subset_for_all = [];
                    $htc_units_subset = [];
                    
                    $comments[] = 'Total units with HOME funding and award number '.$home_award_number.' is '.$total_unit_count;
                    $comments[] = 'Total units in the project is '.$total_project_units;
                    $this->audit->comment = $this->audit->comment.' | Select Process Total units with HOME fundng is '.$total_unit_count.' | Select Process Total units in the project is '.$total_project_units;
                        ////$this->audit->save();
                        
                    // $project_program_key = $this->project->programs->whereIn('program_key',$this->program_home_ids)->pluck('project_program_key')->all();
                    // $project_program_key = implode(',', $project_program_key);
                    
                    if (count($units) <= 4) {

                        $required_units = count($units);
                        $this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['percent']='100% of Home';
                        //$this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['_2016_count'] = $required_units;
                        $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 100);
                        //dd('1373 Random Unit Selection output:',$units_selected);

                        
                        
                        $comments[] = 'Because there are less than 4 HOME units, the selection is 100%. Total selected: '.count($units_selected);
                        $this->audit->comment = $this->audit->comment.' | Select Process Because there are less than 4 HOME units, the selection is 100%. Total selected: '.count($units_selected);
                        ////$this->audit->save();
                        

                    } else {
                        if (ceil($this->project->total_unit_count/2) >= ceil($total_project_units/5)) {

                            $required_units = ceil($this->project->total_unit_count/2);
                            $this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['percent']='50% of Home';
                            //$this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['_2016_count'] = $required_units;

                            $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($this->project->total_unit_count/2));
                            //dd('1386 Random Unit Selection output:',$units_selected);
                            

                            $comments[] = 'Because there are more than 4 units and because 20% of project units is smaller than 50% of HOME units, the total selected is '.ceil($this->project->total_unit_count/2);
                            $this->audit->comment = $this->audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is smaller than 50% of HOME units, the total selected is '.ceil($this->project->total_unit_count/2);
                            ////$this->audit->save();
                            

                        } else {

                            if(ceil($total_project_units/5) > $this->project->total_unit_count){
                                $required_units = $this->project->total_unit_count;
                                $this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['percent']='100% of Home';
                                //$this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['_2016_count'] = $required_units;
                                $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, $this->project->total_unit_count);
                                //dd('1399 Random Unit Selection output:',$units_selected);
                                
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of HOME units, the total selected is '.$this->project->total_unit_count.' which is the total number of units';

                                $this->audit->comment = $this->audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of HOME units, the total selected is '.$this->project->total_unit_count.' which is the total number of units';
                                ////$this->audit->save();
                            }else{
                                $required_units = ceil($total_project_units/5);
                                $this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['percent']='20% of Project';
                                //$this->program_percentages['HOME'.str_replace(' ','',str_replace('-', '', $home_award_number))]['_2016_count'] = $required_units;
                                //$units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($total_project_units/5));
                                $units_selected = $units->random(ceil($total_project_units/5))->pluck('unit_key')->all();
                                //dd('1408 Random Unit Selection output:',$units_selected);
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of HOME units, the total selected is '.ceil($total_project_units/5);

                                $this->audit->comment = $this->audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of HOME units, the total selected is '.ceil($total_project_units/5);
                                ////$this->audit->save();
                            }

                            
                            
                        }
                    }
                    //$this->audit->save();

                    foreach ($units_selected as $unit_key) {
                        $has_htc_funding = 0;

                        //$unit_selected = Unit::where('unit_key', '=', $unit_key)->first();
                        $unit_selected = $this->units->whereIn('program_key',$this->program_htc_ids)->where('unit_key',$unit_key)->count();

                        $comments[] = 'Checking if HTC funding applies to this unit '.$unit_key.' by cross checking with HTC programs';

                        $this->audit->comment = $this->audit->comment.' | Select Process Checking if HTC funding applies to this unit '.$unit_key.' by cross checking with HTC programs';
                        ////$this->audit->save();
                        
                        
                        // if units have HTC funding add to subset
                        
                        
                        // if($unit_selected->has_program_from_array($program_htc_ids, $this->audit->id)){
                        //     $has_htc_funding = 1;
                            
                        //     ////$this->audit->save();
                        // }
                        
                        if ($unit_selected) {
                            $comments[] = 'The unit key '.$unit_key.' belongs to a program with HTC funding';
                            $comments[] = 'We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
                            $this->audit->comment = $this->audit->comment.' | Select Process We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
                                ////$this->audit->save();
                                
                            $htc_units_subset[] = $unit_key;
                        }
                    }
                    //$this->audit->save();


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
                    //dd('1480 Finished Home');
                }else{
                    $htc_units_subset_for_home = array();
                    $this->audit->comment_system = $this->audit->comment_system.' | 1455 Select Process is not working with HOME.';
                    //$this->audit->save();
                }
            }
        }else {
            $htc_units_subset_for_home = array();
            $this->audit->comment_system = $this->audit->comment_system.' | 1461 Select Process is not working with Home.';
            //$this->audit->save();
        }


        //
        //
        // 5 - OHTF
        //
        //
        

        $this->program_ohtf_ids = explode(',', SystemSetting::get('program_ohtf'));
        if(!empty(array_intersect($projectProgramIds, $this->program_ohtf_ids))) {
            //dd('1503 Entering OHTF');
            $htc_units_subset_for_ohtf = array();

            //$ohtf_award_numbers = ProjectProgram::whereIn('program_key', $this->program_ohtf_ids)->where('project_id', '=', $this->audit->project_id)->select('award_number')->groupBy('award_number')->orderBy('award_number', 'ASC')->get();
            $ohtf_award_numbers = $this->project->programs->whereIn('program_key', $this->program_ohtf_ids)->pluck('award_number');
            

            foreach($ohtf_award_numbers as $ohtf_award_number){

                // programs with that award_number
                //$program_keys_with_award_number = ProjectProgram::where('award_number','=',$ohtf_award_number->award_number)->where('project_id', '=', $this->audit->project_id)->pluck('program_key')->toArray(); 
                $program_keys_with_award_number = $this->project->programs->where('award_number',$ohtf_award_number)->pluck('program_key')->all(); 
                

                // $program_ohtf_names = Program::whereIn('program_key', $this->program_ohtf_ids)
                //                                 ->whereIn('program_key', $program_keys_with_award_number)
                //                                 ->get()
                //                                 ->pluck('program_name')
                //                                 ->toArray();
                $program_ohtf_names = $this->project->programs->whereIn('program_key', $this->program_ohtf_ids)
                                                ->whereIn('program_key', $program_keys_with_award_number)
                                                ->pluck('program.program_name')->toArray();
                
                $program_ohtf_names = implode(',', $program_ohtf_names);
                
                $comments = [];

                $required_units = 0;

                $total_project_units = $this->project->stats_total_units();


                // $units = Unit::whereHas('programs', function ($query) use ($this->program_ohtf_ids, $program_keys_with_award_number) {
                //                     $query->where('audit_id', '=', $this->audit->id);
                //                     $query->whereIn('program_key', $program_keys_with_award_number);
                //                     $query->whereIn('program_key', $this->program_ohtf_ids);
                // })->get();

                $units = $this->units->whereIn('program_key',$program_keys_with_award_number)->whereIn('program_key', $this->program_ohtf_ids);

                

                $this->program_percentages['OHTF'.str_replace(' ','',str_replace('-', '', $ohtf_award_number))]['percent']='NA';
                //$this->program_percentages['OHTF'.str_replace(' ','',str_replace('-', '', $ohtf_award_number))]['_2016_count'] = null;
                if((is_array($units) || is_object($units)) && count($units)){
                    $this->audit->comment = $this->audit->comment.' | Select Process Starting OHTF for award number '.$ohtf_award_number;
                    ////$this->audit->save();
                    

                    $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$this->audit->id.'. Programs: '.$program_ohtf_names.', award number '.$ohtf_award_number;
                    $this->audit->comment = $this->audit->comment.' | Select Process Pool of units chosen among units belonging to programs associated with this audit id '.$this->audit->id.'. Programs: '.$program_ohtf_names.', award number '.$ohtf_award_number;
                    ////$this->audit->save();
                    

                    $this->project->total_unit_count = count($units);
                    

                    // $program_htc_overlap = array_intersect($program_htc_ids, $this->program_ohtf_ids);
                    // 
                    // $program_htc_overlap_names = Program::whereIn('program_key', $program_htc_overlap)->get()->pluck('program_name')->toArray();
                    // 
                    // $program_htc_overlap_names = implode(',', $program_htc_overlap_names);
                    // 

                    $units_selected = [];
                    $htc_units_subset = [];

                    $comments[] = 'Total units with OHTF funding and award number '.$ohtf_award_number.' is '.$this->project->total_unit_count;
                    $comments[] = 'Total units in the project with a program is '.$total_project_units;

                    $this->audit->comment = $this->audit->comment.' | Select Process Total units with OHTF funding is '.$this->project->total_unit_count;
                    ////$this->audit->save();
                    

                    $this->audit->comment = $this->audit->comment.' | Select Process Total units in the project is '.$total_project_units;
                    ////$this->audit->save();
                    

                    if (count($units) <= 4) {

                        $required_units = count($units);
                        $this->program_percentages['OHTF'.str_replace(' ','',str_replace('-', '', $ohtf_award_number))]['percent']='100% of OHTF';
                        //$this->program_percentages['OHTF'.str_replace(' ','',str_replace('-', '', $ohtf_award_number))]['_2016_count'] = $required_units;

                        $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 100);
                        //dd('1561 Random Unit Selection output:',$units_selected);
                        
                        $comments[] = 'Because there are less than 4 OHTF units, the selection is 100%. Total selected: '.count($units_selected);

                        $this->audit->comment = $this->audit->comment.' | Select Process Because there are less than 4 OHTF units, the selection is 100%. Total selected: '.count($units_selected);
                        ////$this->audit->save();
                        

                    } else {
                        if (ceil($this->project->total_unit_count/2) >= ceil($total_project_units/5)) {

                            $required_units = ceil($this->project->total_unit_count/2);

                            $this->program_percentages['OHTF'.str_replace(' ','',str_replace('-', '', $ohtf_award_number))]['percent']='50% of OHTF';
                           // $this->program_percentages['OHTF'.str_replace(' ','',str_replace('-', '', $ohtf_award_number))]['_2016_count'] = $required_units;

                             $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($this->project->total_unit_count/2));
                             //dd('1574 Random Unit Selection output:',$units_selected);
                             
                             $comments[] = 'Because there are more than 4 units and because 20% of project units is smaller than 50% of OHTF units, the total selected is '.ceil($this->project->total_unit_count/2);

                            $this->audit->comment = $this->audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is smaller than 50% of OHTF units, the total selected is '.ceil($this->project->total_unit_count/2);
                            ////$this->audit->save();
                            
                        } else {

                            if(ceil($total_project_units/5) > $this->project->total_unit_count){
                                $required_units = $this->project->total_unit_count;
                                $this->program_percentages['OHTF'.str_replace(' ','',str_replace('-', '', $ohtf_award_number))]['percent']='100% of OHTF';
                              //  $this->program_percentages['OHTF'.str_replace(' ','',str_replace('-', '', $ohtf_award_number))]['_2016_count'] = $required_units;
                                $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, $this->project->total_unit_count);
                                //dd('1587 Random Unit Selection output:',$units_selected);
                                
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of OHTF units, the total selected is '.$this->project->total_unit_count. 'which is the total number of units';

                                $this->audit->comment = $this->audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of OHTF units, the total selected is '.$this->project->total_unit_count. 'which is the total number of units';
                            }else{
                                $required_units = ceil($total_project_units/5);
                                $this->program_percentages['OHTF'.str_replace(' ','',str_replace('-', '', $ohtf_award_number))]['percent']='20% of Project';
                              //  $this->program_percentages['OHTF'.str_replace(' ','',str_replace('-', '', $ohtf_award_number))]['_2016_count'] = $required_units;
                                $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($total_project_units/5));
                                //dd('1595 Random Unit Selection output:',$units_selected);
                                
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of OHTF units, the total selected is '.ceil($total_project_units/5);

                                $this->audit->comment = $this->audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of OHTF units, the total selected is '.ceil($total_project_units/5);

                            }

                            
                            ////$this->audit->save();
                            
                        }
                    }

                    foreach ($units_selected as $unit_key) {
                        $unit_selected = Unit::where('unit_key','=',$unit_key)->first();
                        
                        if($unit_selected){
                            $has_htc_funding = 0;

                            $comments[] = 'Checking if HTC funding applies to this unit '.$unit_selected->unit_key.' by cross checking with HTC programs';

                            $this->audit->comment = $this->audit->comment.' | Select Process Checking if HTC funding applies to this unit '.$unit_selected->unit_key.' by cross checking with HTC programs';
                                ////$this->audit->save();
                                

                            // if units have HTC funding add to subset
                            if($unit_selected->has_program_from_array($this->program_htc_ids, $this->audit->id)){
                                $has_htc_funding = 1;
                                $comments[] = 'The unit key '.$unit_selected->unit_key.' belongs to a program with HTC funding';
                                ////$this->audit->save();
                            }

                            if ($has_htc_funding) {
                                $htc_units_subset[] = $unit_selected->unit_key;
                                
                                $comments[] = 'We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
                                $this->audit->comment = $this->audit->comment.' | Select Process We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
                                    
                                    ////$this->audit->save();
                                    
                            }
                        } else {
                            $this->audit->comment = $this->audit->comment.' | Select Process A unit came up null in its values. We recommend checking the completeness of the data in Devco for your units, update any that may be missing data, and then re-run the selection.';
                                    
                                    //$this->audit->save();
                                    
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
                    $this->audit->comment_system = $this->audit->comment_system.' | Select Process is not working with OHTF.';
                    //$this->audit->save();
                }
            }
        }else{
            $htc_units_subset_for_ohtf = array();
            $this->audit->comment_system = $this->audit->comment_system.' | Select Process is not working with OHTF.';
            //$this->audit->save();
        }


        //
        //
        // 6 - NHTF
        //
        //

        $this->program_nhtf_ids = explode(',', SystemSetting::get('program_nhtf'));
        if(!empty(array_intersect($projectProgramIds, $this->program_nhtf_ids))) {
            //dd('1503 Entering NHTF');
            $htc_units_subset_for_nhtf = array();
            
            //$nhtf_award_numbers = ProjectProgram::whereIn('program_key', $this->program_nhtf_ids)->where('project_id', '=', $this->audit->project_id)->select('award_number')->groupBy('award_number')->orderBy('award_number', 'ASC')->get();
            $nhtf_award_numbers = $this->project->programs->whereIn('program_key', $this->program_nhtf_ids)->pluck('award_number');
            

            foreach($nhtf_award_numbers as $nhtf_award_number){

                // programs with that award_number
                //$program_keys_with_award_number = ProjectProgram::where('award_number','=',$nhtf_award_number->award_number)->where('project_id', '=', $this->audit->project_id)->pluck('program_key')->toArray();
                $program_keys_with_award_number = $this->project->programs->where('award_number',$nhtf_award_number)->pluck('program_key')->all(); 
                 

                // $program_nhtf_names = Program::whereIn('program_key', $this->program_nhtf_ids)
                //                                 ->whereIn('program_key', $program_keys_with_award_number)
                //                                 ->get()
                //                                 ->pluck('program_name')->toArray();
                $program_nhtf_names = $this->project->programs->whereIn('program_key', $this->program_nhtf_ids)
                                                ->whereIn('program_key', $program_keys_with_award_number)
                                                ->pluck('program.program_name')->toArray();
                
                $program_nhtf_names = implode(',', $program_nhtf_names);
                
                $comments = [];

                $required_units = 0;

                $total_project_units = $this->project->stats_total_units();


                // $units = Unit::whereHas('programs', function ($query) use ($this->program_nhtf_ids, $program_keys_with_award_number) {
                //                     $query->where('audit_id', '=', $this->audit->id);
                //                     $query->whereIn('program_key', $program_keys_with_award_number);
                //                     $query->whereIn('program_key', $this->program_nhtf_ids);
                // })->get();
                
                $units = $this->units->whereIn('program_key',$program_keys_with_award_number)->whereIn('program_key', $this->program_nhtf_ids);
                $this->program_percentages['NHTF'.str_replace(' ','',str_replace('-', '', $nhtf_award_number))]['percent']='NA';
                //$this->program_percentages['NHTF'.str_replace(' ','',str_replace('-', '', $nhtf_award_number))]['_2016_count'] = null;
                if((is_array($units) || is_object($units)) && count($units)){
                    $this->audit->comment = $this->audit->comment.' | Select Process Starting NHTF for award number '.$nhtf_award_number;
                    //$this->audit->save();
                    

                    $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$this->audit->id.'. Programs: '.$program_nhtf_names.', award number '.$nhtf_award_number;

                    $this->audit->comment = $this->audit->comment.' | Select Process Pool of units chosen among units belonging to programs associated with this audit id '.$this->audit->id.'. Programs: '.$program_nhtf_names.', award number '.$nhtf_award_number;;
                    //$this->audit->save();
                    

                    $units_selected = [];
                    $htc_units_subset = [];
                    
                    $this->project->total_unit_count = count($units);
                    

                    $comments[] = 'Total units with NHTF funding is '.$this->project->total_unit_count;
                    $comments[] = 'Total units in the project with a program is '.$total_project_units;

                    $this->audit->comment = $this->audit->comment.' | Select Process Total units with NHTF funding is '.$this->project->total_unit_count;
                    //$this->audit->save();
                    
                    $this->audit->comment = $this->audit->comment.' | Select Process Total units in the project with a program is '.$total_project_units;
                    //$this->audit->save();
                    


                    if (count($units) <= 4) {

                        $required_units = count($units); // 100%

                        $this->program_percentages['NHTF'.str_replace(' ','',str_replace('-', '', $nhtf_award_number))]['percent'] = '100% of NHTF';
                       // $this->program_percentages['NHTF'.str_replace(' ','',str_replace('-', '', $nhtf_award_number))]['_2016_count'] = $required_units;

                        $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 100);
                        //dd('1746 Random Unit Selection output:',$units_selected);
                        
                        $comments[] = 'Because there are less than 4 NHTF units, the selection is 100%. Total selected: '.count($units_selected);

                        $this->audit->comment = $this->audit->comment.' | Select Process Because there are less than 4 NHTF units, the selection is 100%. Total selected: '.count($units_selected);
                        //$this->audit->save();
                        

                    } else {
                        if (ceil($this->project->total_unit_count/2) >= ceil($total_project_units/5)) {

                            $required_units = ceil($this->project->total_unit_count/2);
                            $this->program_percentages['NHTF'.str_replace(' ','',str_replace('-', '', $nhtf_award_number))]['percent']='50% of NHTF';
                           // $this->program_percentages['NHTF'.str_replace(' ','',str_replace('-', '', $nhtf_award_number))]['_2016_count'] = $required_units;

                             $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($this->project->total_unit_count/2));
                             //dd('1760 Random Unit Selection output:',$units_selected);
                             
                             $comments[] = 'Because there are more than 4 units and because 20% of project units is smaller than 50% of NHTF units, the total selected is '.ceil($this->project->total_unit_count/2);
                             $this->audit->comment = $this->audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is smaller than 50% of NHTF units, the total selected is '.ceil($this->project->total_unit_count/2);

                            //$this->audit->save();
                            
                        } else {

                            if(ceil($total_project_units/5) > $this->project->total_unit_count){
                                $required_units = $this->project->total_unit_count;
                                $this->program_percentages['NHTF'.str_replace(' ','',str_replace('-', '', $nhtf_award_number))]['percent']='100% of NHTF';
                               // $this->program_percentages['NHTF'.str_replace(' ','',str_replace('-', '', $nhtf_award_number))]['_2016_count'] = $required_units;
                                $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, $this->project->total_unit_count);
                                //dd('1772 Random Unit Selection output:',$units_selected);
                                
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of NHTF units, the total selected is '.$this->project->total_unit_count. 'which is the total number of units';

                                $this->audit->comment = $this->audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of NHTF units, the total selected is '.$this->project->total_unit_count. 'which is the total number of units';
                            }else{
                                $required_units = ceil($total_project_units/5);
                                $this->program_percentages['NHTF'.str_replace(' ','',str_replace('-', '', $nhtf_award_number))]['percent']='20% of Project';
                               // $this->program_percentages['NHTF'.str_replace(' ','',str_replace('-', '', $nhtf_award_number))]['_2016_count'] = $required_units;
                                $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($total_project_units/5));
                                //dd('1780 Random Unit Selection output:',$units_selected);
                                
                                $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of NHTF units, the total selected is '.ceil($total_project_units/5);

                                $this->audit->comment = $this->audit->comment.' | Select Process Because there are more than 4 units and because 20% of project units is greater than 50% of NHTF units, the total selected is '.ceil($total_project_units/5);

                            }
                            //$this->audit->save();
                            
                        }
                    }

                    foreach ($units_selected as $unit_key) {
                        //dd('2021 does optimization work here?');
                        $unit_selected = $this->units->where('unit_key',$unit_key);
                        
                        $has_htc_funding = 0;

                        $comments[] = 'Checking if HTC funding applies to this unit '.$unit_key.' by cross checking with HTC programs';

                        $this->audit->comment = $this->audit->comment.' | Select Process Checking if HTC funding applies to this unit '.$unit_key.' by cross checking with HTC programs';
                            //$this->audit->save();
                            

                        // if units have HTC funding add to subset
                        //$unit = Unit::where('unit_key', '=', $unit_selected)->first();
                        

                        if(count($unit_selected->whereIn('program_key',$this->program_htc_ids))){
                            $has_htc_funding = 1;
                            $comments[] = 'The unit key '.$unit_key.' belongs to a program with HTC funding';
                            //$this->audit->save();
                        }

                        if ($has_htc_funding) {
                            $comments[] = 'We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';

                            $this->audit->comment = $this->audit->comment.' | Select Process We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
                                //$this->audit->save();
                                

                            $htc_units_subset[] = $unit_key;
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
                    $this->audit->comment_system = $this->audit->comment_system.' | 1807 Select Process is not working with NHTF.';
                    //$this->audit->save();
                }
            }
        }else{
            $htc_units_subset_for_nhtf = array();
            $this->audit->comment_system = $this->audit->comment_system.' | 1813 Select Process is not working with NHTF.';
            //$this->audit->save();
        }


        // check for HOME, OHTF, NHTF overlap and send to analyst
        // overlap contains the keys of units
        //dd('1872 - Overlap check');
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
        //dd('1888 overlap finished');

        $comments[] = 'Overlap list to send to analyst: '.$overlap_list;
        $this->audit->comment = $this->audit->comment.' | Overlap list to send to analyst: '.$overlap_list;
        //$this->audit->save();

        //
        //
        // 7 - HTC
        // get totals of all units HTC and select all units without NHTF. OHTF and HOME
        // check in project_program->first_year_award_claimed date for the 15 year test
        // after 15 years: 20% of total
        // $this->program_htc_ids = SystemSetting::get('program_htc'); // already loaded
        //
        //

        
        $comments = [];

        $required_units = 0; // this is computed, not counted!
        $this->program_htc_ids = explode(',', SystemSetting::get('program_htc'));
         if(!empty(array_intersect($projectProgramIds, $this->program_htc_ids))) {

         //dd('1907 Entering HTC '); // 16 seconds! for 27
            // total HTC funded units (71)
            $this->audit->comment = $this->audit->comment.' | Selecting units with HTC at '.date('g:h:i a',time());
            ////$this->audit->save();
            // $all_htc_units = Unit::whereHas('programs', function ($query) use ($this->audit, $this->program_htc_ids) {
            //                     $query->where('audit_id', '=', $this->audit->id);
            //                     $query->whereIn('program_key', $this->program_htc_ids);
            // })->get();
            $all_htc_units = $this->units->whereIn('program_key',$this->program_htc_ids)->where('audit_id',$this->audit->id);
            
            if(is_object($all_htc_units) || is_array($all_htc_units)){
                $total_htc_units = count($all_htc_units);
            } else {
                $total_htc_units = 0;
            }
            //dd('Finished Selecting HTC'); //16.7 seconds

            if($total_htc_units){
                $use_limiter = 1;

                $this->audit->comment = $this->audit->comment.' | Select Process Starting HTC.';
                ////$this->audit->save();
                

                $comments[] = 'The total of HTC units is '.$total_htc_units.'.';
                $this->audit->comment = $this->audit->comment.' | Select Process The total of HTC units is '.$total_htc_units.'.';
                            ////$this->audit->save();
                            

                // HTC without HOME, OHTF, NHTF
                // $program_htc_only_ids = array_diff($this->program_htc_ids, $this->program_home_ids, $this->program_ohtf_ids, $this->program_nhtf_ids);
                // 

                // $program_htc_only_names = Program::whereIn('program_key', $program_htc_only_ids)->get()->pluck('program_name')->toArray();
                // 
                // $program_htc_only_names = implode(',', $program_htc_only_names);
                // 

                // $comments[] = 'Pool of units chosen among units belonging to HTC programs associated with this audit id '.$this->audit->id.' excluding HOME, OHTF and NHTF. Programs: '.$program_htc_only_names;
                // 

                // $this->audit->comment = $this->audit->comment.' | Select Process Pool of units chosen among units belonging to HTC programs associated with this audit id '.$this->audit->id.' excluding HOME, OHTF and NHTF. Programs: '.$program_htc_only_names;
                //  //$this->audit->save();
                //  

                $units = [];
                foreach ($all_htc_units as $all_htc_unit) {
                    if($this->units->whereIn('program_key',$this->program_home_ids)->where('unit_key',$all_htc_unit->unit_key)->count() || 
                       $this->units->whereIn('program_key',$this->program_ohtf_ids)->where('unit_key',$all_htc_unit->unit_key)->count() || 
                       $this->units->whereIn('program_key',$this->program_nhtf_ids)->where('unit_key',$all_htc_unit->unit_key)->count()){
                        $units[] = $all_htc_unit->unit_key;
                    }
                }
                //dd('1964 finished looping units... maybe we convert keys of htc units to be an array and do something there?'); 16.26 seconds


                $comments[] = 'The total of HTC units that have HOME, OHTF and NHTF is '.count($units).'.';
                $this->audit->comment = $this->audit->comment.' | Select Process The total of HTC units that have HOME, OHTF and NHTF is '.count($units).'.';
                ////$this->audit->save();
                

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

                $this->audit->comment = $this->audit->comment.' | Select Process Going through the HTC programs, we look for the most recent year in the first_year_award_claimed field.';
                ////$this->audit->save();
                

                foreach ($this->project->programs->whereIn('program_key', $this->program_htc_ids) as $program) {
                    
                    // only select HTC project programs
                    
                        if ($first_year == null || $first_year < $program->first_year_award_claimed) {
                            $first_year = $program->first_year_award_claimed;
                            $comments[] = 'Program key '.$program->program_key.' has the year '.$program->first_year_award_claimed.'.';
                            $this->audit->comment = $this->audit->comment.' | Select Process Program key '.$program->program_key.' has the year '.$program->first_year_award_claimed.'.';
                            ////$this->audit->save();
                            
                        } 
                    
                }
                //dd('finished checking years'); //16.8 seconds

                if (idate("Y")-14 > $first_year && $first_year != null) {
                    $first_fifteen_years = 0;
                    $comments[] = 'Based on the year, we determined that the program is not within the first 15 years.';
                    $this->audit->comment = $this->audit->comment.' | Select Process Based on the year, we determined that the program is not within the first 15 years.';
                        ////$this->audit->save();
                   
                        
                } else {
                    $first_fifteen_years = 1;
                    $comments[] = 'Based on the year, we determined that the program is within the first 15 years.';
                    $this->audit->comment = $this->audit->comment.' | Select Process Based on the year, we determined that the program is within the first 15 years.';
                        ////$this->audit->save();
                        
                }
                
                if ($first_fifteen_years) {
                    /*
                        // check project for least purchase
                        $leaseProgramKeys = explode(',', SystemSetting::get('lease_purchase'));
                        
                        $comments[] = 'Check if the programs associated with the project correspond to lease purchase using program keys: '.SystemSetting::get('lease_purchase').'.';

                        $this->audit->comment = $this->audit->comment.' | Select Process Check if the programs associated with the project correspond to lease purchase using program keys: '.SystemSetting::get('lease_purchase').'.';
                            //$this->audit->save();
                            
                            $leasePurchaseFound = 0;
                            $isLeasePurchase = 0;
                        foreach ($this->project->programs as $program) {
                            
                            if (in_array($program->program_key, $leaseProgramKeys)) {
                                $isLeasePurchase = 1;
                                $comments[] = 'A program key '.$program->program_key.' confirms that this is a lease purchase.';
                                $this->audit->comment = $this->audit->comment.' | Select Process A program key '.$program->program_key.' confirms that this is a lease purchase.';
                                //$this->audit->save();
                                
                                $leasePurchaseFound = 1;
                            } 
                        }

                        if(!$leasePurchaseFound){
                            $comments[] = 'No lease purchase programs found.';
                            $this->audit->comment = $this->audit->comment.' | Select Process No lease purchase programs found.';
                                //$this->audit->save();
                                
                        }

                        if ($isLeasePurchase) {

                            $htc_units_without_overlap = Unit::whereHas('programs', function ($query) use ($this->audit, $program_htc_only_ids) {
                                                            $query->where('audit_id', '=', $this->audit->id);
                                                            $query->whereIn('program_key', $program_htc_only_ids);
                                                        })->pluck('unit_key')->toArray();

                            $required_units = $this->adjustedLimit($total_htc_units);

                            if($required_units <= count($htc_units_subset)){
                                $number_of_htc_units_needed = 0;
                            }else{
                                $number_of_htc_units_needed = $required_units - count($htc_units_subset);
                            }

                            $units_selected = $this->randomSelection($htc_units_without_overlap, 0, $number_of_htc_units_needed);
                            
                            $units_selected_count = count($units_selected);

                            $comments[] = 'It is a lease purchase. Total selected: '.count($units_selected);
                            $this->audit->comment = $this->audit->comment.' | Select Process It is a lease purchase. Total selected: '.count($units_selected);
                                //$this->audit->save();
                                

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
                    $this->audit->comment = $this->audit->comment.' | Select Process Going through each program to determine if the project is a multi building project by looking for multiple_building_election_key=2.';
                    ////$this->audit->save();
                    

                    foreach ($this->project->programs->whereIn('project_key',$this->program_htc_ids) as $program) {
                        
                        
                            if ($program->multiple_building_election_key == 2) {
                                $is_multi_building_project = 1;
                                $comments[] = 'Program key '.$program->program_key.' showed that the project IS a multi building project.';
                                $this->audit->comment = $this->audit->comment.' | Select Process Program key '.$program->program_key.' showed that the project IS a multi building project.';
                                ////$this->audit->save();
                                
                            } else {
                                $comments[] = 'Program key '.$program->program_key.' showed that the project is NOT a multi building project.';
                                $this->audit->comment = $this->audit->comment.' | Select Process Program key '.$program->program_key.' showed that the project is NOT a multi building project.';
                            }
                        
                    }
                    //dd('2127 Finished checking multibuilding');
                    if ($is_multi_building_project) {
                        //dd('2119 section needs optimized');
                        $htc_units_without_overlap = $this->units->whereIn('program_key', $this->program_htc_ids)
                                                    ->whereNotIn('program_key', $this->program_home_ids)
                                                    ->whereNotIn('program_key', $this->program_ohtf_ids)
                                                    ->whereNotIn('program_key', $this->program_nhtf_ids)
                                                    ->pluck('unit_key')->all();

                        $number_of_htc_units_required = $this->adjustedLimit($total_htc_units);
                        $required_units = $number_of_htc_units_required;
                        //ceil($total_htc_units/10);
                        //$_2016_total = $total_htc_units/5;
                        $this->program_percentages['HTC']['percent']='20% of HTC';
                       // $this->program_percentages['HTC']['_2016_count'] = $_2016_total;

                        if($number_of_htc_units_required <= count($htc_units_subset)){
                            $number_of_htc_units_needed = 0;
                            $comments[] ='There are enough HTC units in the previous selections ('.count($htc_units_subset).') to meet the required number of '.$required_units.' units.';
                            $this->audit->comment = $this->audit->comment.'There are enough HTC units in the previous selections ('.count($htc_units_subset).') to meet the required number of '.$required_units.' units.';
                            ////$this->audit->save();
                        }else{
                            $number_of_htc_units_needed = $number_of_htc_units_required - count($htc_units_subset);
                            $comments[] = 'There are '.count($htc_units_subset).' that are from the previous selection that are automatically included in the HTC selection. We need to select '.$number_of_htc_units_needed.' more units.';
                            $this->audit->comment = $this->audit->comment.'There are '.count($htc_units_subset).' that are from the previous selection that are automatically included in the HTC selection. We need to select '.$number_of_htc_units_needed.' more units.';
                                ////$this->audit->save();
                        }

                        $units_selected = $this->randomSelection($htc_units_without_overlap, 0, $number_of_htc_units_needed);
                        //dd('2128 Random Unit Selection output:',$units_selected);
                        
                        $units_selected_count = count($units_selected);

                        

                        $units_selected = array_merge($units_selected, $htc_units_subset_for_home, $htc_units_subset_for_ohtf, $htc_units_subset_for_nhtf);
                        $units_selected_count = $units_selected_count + count($htc_units_subset_for_home) + count($htc_units_subset_for_ohtf) + count($htc_units_subset_for_nhtf);
                        
                        $comments[] = 'Total units selected including overlap : '.$units_selected_count;
                        $this->audit->comment = $this->audit->comment.' | Total units selected including overlap : '.$units_selected_count;
                                ////$this->audit->save();
                                

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
                    //$this->audit->save();
                        
                    } else {
                        $use_limiter = 0; // we apply the limiter for each building

                        $comments[] = 'The project is not a multi building project.';
                        $this->audit->comment = $this->audit->comment.' | Select Process The project is not a multi building project.';
                               // //$this->audit->save();
                                
                        // group units by building, then proceed with the random selection
                        // create a new list of units based on building and project key
                        $units_selected = [];
                        $units_selected_count = 0;

                        $required_units = 0; // in the case of buildings, we need to sum each totals because of the rounding
                        
                        $first_building_done = 0; // this is to control the comments to only keep the ones we care about after the first building information is displayed.
                        //$_2016_total = 0;

                        foreach ($this->project->buildings as $building) {
                            
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
                                //dd('2212 - this section needs optimized!');
                                $htc_units_for_building = $this->units->where('unit.building_key', $building->building_key)
                                                    ->whereIn('program_key', $this->program_htc_ids)
                                                    ->pluck('unit_key')
                                                    ->all();

                                $htc_units_without_overlap = $this->units->where('unit.building_key', $building->building_key)
                                                ->whereNotIn('unit_key', $htc_units_subset)
                                                ->whereIn('program_key', $this->program_htc_ids)
                                                ->pluck('unit_key')
                                                ->all();

                                $htc_units_with_overlap = $this->units->where('unit.building_key', '=', $building->building_key)
                                                ->whereIn('unit_key', $htc_units_subset)
                                                ->whereIn('program_key', $this->program_htc_ids)
                                                ->pluck('unit_key')
                                                ->all();
                                //dd('2420 Check optimization', $htc_units_for_building, $htc_units_without_overlap, $htc_units_with_overlap );
                                //$required_units_for_that_building = ceil(count($htc_units_for_building)/5);
                                $required_units_for_that_building = $this->adjustedLimit(count($htc_units_for_building));
                                //$required_units = $required_units + $required_units_for_that_building;

                                //$_2016_total += (count($htc_units_for_building) / 5);
                                $this->program_percentages['HTC']['percent']='20% of HTC Per Building';
                                //$this->program_percentages['HTC']['_2016_count'] = $_2016_total;
                                
                                $required_units = $required_units_for_that_building;

                                // $htc_units_with_overlap_for_that_building = count($htc_units_for_building) - count($htc_units_without_overlap);
                                $htc_units_with_overlap_for_that_building = count($htc_units_with_overlap);

                                // TEST
                                // $overlap_list = '';
                                // foreach($htc_units_subset as $htc_units_subset_key){
                                //     $overlap_list = $overlap_list . $htc_units_subset_key . ',';
                                // }
                                // $comments[] = 'Overlap: '.$overlap_list;
                                // $this->audit->comment = $this->audit->comment.' | Overlap: '.$overlap_list;
                                // //$this->audit->save();

                                // $htc_units_for_building_list = '';
                                // foreach($htc_units_for_building as $htc_units_for_building_key){
                                //     $htc_units_for_building_list = $htc_units_for_building_list . $htc_units_for_building_key. ',';
                                // }
                                // $comments[] = 'htc_units_for_building_list: '.$htc_units_for_building_list;
                                // $this->audit->comment = $this->audit->comment.' | htc_units_for_building_list: '.$htc_units_for_building_list;
                                // //$this->audit->save();

                                // $htc_units_with_overlap_list = '';
                                // foreach($htc_units_with_overlap as $htc_units_with_overlap_key){
                                //     $htc_units_with_overlap_list = $htc_units_with_overlap_list . $htc_units_with_overlap_key. ',';
                                // }
                                // $comments[] = 'htc_units_with_overlap_list: '.$htc_units_with_overlap_list;
                                // $this->audit->comment = $this->audit->comment.' | htc_units_with_overlap_list: '.$htc_units_with_overlap_list;
                                // //$this->audit->save();
                                // END TEST

                                if($required_units_for_that_building >= $htc_units_with_overlap_for_that_building){
                                    // we are missing some units
                                    $number_of_htc_units_needed_for_that_building = $required_units_for_that_building - $htc_units_with_overlap_for_that_building;
                                }else{
                                    // we have enough units
                                    $number_of_htc_units_needed_for_that_building = 0;
                                }
                                
                                $new_building_selection = $this->randomSelection($htc_units_without_overlap, 0, $number_of_htc_units_needed_for_that_building);
                                //dd('2265 Random Unit Selection output:'.$new_building_selection);
                                
                                //$units_selected_count = $units_selected_count + count($new_building_selection);
                                $units_selected_count = count($new_building_selection);

                                // if(count($new_building_selection)){
                                //     $units_selected = array_merge($units_selected, $new_building_selection);
                                // }
                                
                                $units_selected = $new_building_selection;
                                
                                $comments[] = 'The total of HTC units for building key '.$building->building_key.' is '.count($htc_units_for_building).'. Required units: '.$required_units_for_that_building.'. Overlap units: '.$htc_units_with_overlap_for_that_building.'. Missing units: '.$number_of_htc_units_needed_for_that_building;

                                $this->audit->comment = $this->audit->comment.' | Select Process The total of HTC units for building key '.$building->building_key.' is '.count($htc_units_for_building).'. Required units: '.$required_units_for_that_building.'. Overlap units: '.$htc_units_with_overlap_for_that_building.'. Missing units: '.$number_of_htc_units_needed_for_that_building;

                                //$this->audit->save();
                                

                                $comments[] = 'Randomly selected units in building '.$building->building_key.'. Total selected: '.count($new_building_selection).'.';

                                $this->audit->comment = $this->audit->comment.' | Select Process Randomly selected units in building '.$building->building_key.'. Total selected: '.count($new_building_selection).'.';
                                //$this->audit->save();
                                


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
                    $use_limiter = 0;
                    
                    $htc_units_without_overlap = $this->units->whereIn('program_key', $this->program_htc_ids)
                                                    ->whereNotIn('program_key', $this->program_home_ids)
                                                    ->whereNotIn('program_key', $this->program_ohtf_ids)
                                                    ->whereNotIn('program_key', $this->program_nhtf_ids)
                                                    ->pluck('unit_key')->all();
                    //dd('2350 Optimized!');
                    // 10% of units
                    $number_of_htc_units_required = ceil($total_htc_units/10);
                    $required_units = $number_of_htc_units_required;

                   // $_2016_total = $required_units;



                    $this->program_percentages['HTC']['percent']='10% of HTC';
                    //$this->program_percentages['HTC']['_2016_count'] = $_2016_total;

                    if($number_of_htc_units_required <= count($overlap)){
                        $number_of_htc_units_needed = 0;
                    }else{
                        $number_of_htc_units_needed = $number_of_htc_units_required - count($overlap);
                    }

                    $units_selected = $this->randomSelection($htc_units_without_overlap, 0, $number_of_htc_units_needed);
                    //dd('2265 Random Unit Selection output:',$units_selected);
                    
                    $units_selected_count = count($units_selected);
                    $comments[] = 'Total selected: '.count($units_selected);

                    $this->audit->comment = $this->audit->comment.' | Select Process Total selected: '.count($units_selected);
                                    //$this->audit->save();
                                    

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
                    //dd($_2016_total,$required_units,$selection,$number_of_htc_units_needed,$htc_units_without_overlap,$overlap);

                }
                //}

                // $comments[] = 'Combining HTC total selected: '.count($units_selected).' + '.count($htc_units_subset_for_home).' + '.count($htc_units_subset_for_ohtf).' + '.count($htc_units_subset_for_nhtf);
                // $this->audit->comment = $this->audit->comment.' | Combining HTC total selected: '.count($units_selected).' + '.count($htc_units_subset_for_home).' + '.count($htc_units_subset_for_ohtf).' + '.count($htc_units_subset_for_nhtf);
                //         //$this->audit->save();

                // $htc_units_from_home_list = '';
                // foreach($htc_units_subset_for_home as $htc_unit_for_home){
                //     $htc_units_from_home_list = $htc_units_from_home_list . $htc_unit_for_home;
                // }
                // $comments[] = 'HTC units from HOME: '.$htc_units_from_home_list;
                // $this->audit->comment = $this->audit->comment.' | HTC units from HOME: '.$htc_units_from_home_list;
                //         //$this->audit->save();     

                
                
            }else{
                $this->audit->comment_system = $this->audit->comment_system.' | Select Process is not working with HTC.';
                //$this->audit->save();
            }
        } else {
            $this->audit->comment_system = $this->audit->comment_system.' | 2360 Select Process is not working with HTC.';
            //$this->audit->save();
        }

        

        // combineOptimize returns an array [units, summary]
        $optimized_selection = $this->combineOptimize($selection);
        
        $this->audit->comment = $this->audit->comment.' | Select Process Finished - returning results.';
                                //$this->audit->save();
                                
        return [$optimized_selection, $overlap, $this->project, $organization_id];
    }
    public function createNewProjectDetails(){
        //$this->project = \App\Models\Project::find($this->audit->project_id);
        
        $this->audit->project->set_project_defaults($this->audit->id);
        
    }
    public function addAmenityInspections(){
        //Project
        AmenityInspection::where('audit_id',$this->audit->id)->delete();

        

        // // make sure we don't have name duplicates
        foreach ($this->audit->project->amenities as $pa) {
            AmenityInspection::insert([
                //'name'=>$pa->amenity->amenity_description,
                'audit_id'=>$this->audit->id,
                'monitoring_key'=>$this->audit->monitoring_key,
                'project_id'=>$this->audit->project_id,
                'development_key'=>$this->audit->development_key,
                'amenity_id'=>$pa->amenity_id,
                'amenity_key'=>$pa->amenity->amenity_key,

            ]);
            
        }
        foreach ($this->audit->project->buildings as $b) {
            foreach($b->amenities as $ba){
               AmenityInspection::insert([
                    'audit_id'=>$this->audit->id,
                    'monitoring_key'=>$this->audit->monitoring_key,
                    'building_key'=>$b->building_key,
                    'building_id'=>$b->id,
                    'amenity_id'=>$ba->amenity->id,
                    'amenity_key'=>$ba->amenity->amenity_key,

               ]);
               
            }
        }
        foreach ($this->audit->unique_unit_inspections as $u) {
            foreach($u->amenities as $ua){
               AmenityInspection::insert([
                    'audit_id'=>$this->audit->id,
                    'monitoring_key'=>$this->audit->monitoring_key,
                    'unit_key'=>$u->unit_key,
                    'unit_id'=>$u->unit_id,
                    'amenity_id'=>$ua->amenity_id,
                    'amenity_key'=>$ua->amenity->amenity_key,

               ]);
               
            }
        }

        //Building

        //Unit
   }
   public function createNewCachedAudit($summary = null)
    {
        // create cached audit
        //
        
        $project_id = null;
        $project_ref = '';
        $project_name = null;
        $total_buildings = 0;
        $lead = null;
        $total_items = null;
        $lead_json = '{ "id": null, "name": "", "initials": "", "color": "", "status": "" }';
        //$this->processes++;

        // project address
        $address = '';
        $city = '';
        $state = '';
        $zip = '';

        $estimated_time = null;
        $estimated_time_needed = null;


        if ($this->audit->user_key) {
            $lead_user = User::where('devco_key', '=', $this->audit->user_key)->first();
            //$this->processes++;
        }else{
            $lead_user = null;
            //$this->processes++;
        }

        if ($lead_user) {
            $lead = $lead_user->id;
            // $words = explode(" ", $lead_user->name);
            // $initials = "";
            // foreach ($words as $w) {
            //     $initials .= $w[0];
            // }
            // $initials = substr($initials, 0, 2); // keep the first two letters only

            $data = [
                "id" => $lead_user->id,
                "name" => $lead_user->full_name(),
                "initials" => $lead_user->initials(),
                "color" => $lead_user->badge_color,
                "status" => ""
            ];
            $lead_json = json_encode($data);
        }

        if ($this->audit->project_id) {
            $project = $this->audit->project;
            if ($project) {
                $project_id = $project->id;
                $project_ref = $project->project_number;
                $project_name = $project->project_name;
                $total_buildings = $project->total_building_count;

                if ($project->address) {
                    $address = $project->address->line_1;
                    $city = $project->address->city;
                    $state = $project->address->state;
                    $zip = $project->address->zip;
                }
            }
        }
        //create inspection days if they are set in devco

        if(null !== $this->audit->start_date){
            $auditInspectionDate = date('Y-m-d H:i:s', strtotime($this->audit->start_date));
            //insert the date into the schedule
            $scheduleCheck = ScheduleDay::where('date', $auditInspectionDate)->where('audit_id',$this->audit->id)->count();
            if($scheduleCheck < 1){
                $schedule = new ScheduleDay;
                $schedule->audit_id = $this->audit->id;
                $schedule->date = $auditInspectionDate;
                $schedule->save();
            }
            $inspection_schedule_text = 'DATE SET FROM DEVCO';
            $inspection_status_text = 'CLICK TO RESCHEDULE';
            $inspection_status = 'ok-actionable'; 
            $inspection_icon = 'a-mobile-clock'; 
        } else {
            $auditInspectionDate = null;
            $inspection_schedule_text = 'SCHEDULED AUDITS/TOTAL AUDITS';
            $inspection_status = 'action-needed'; 
            $inspection_icon = 'a-mobile-clock'; 

        }
        

        // inspection status and schedule date set to default when creating a new audit
         
        $inspection_schedule_date = $auditInspectionDate; // Y-m-d H:i:s
        

        // in project_roles
        // primary owner: project_role_key = 20, id = 98
        // primary manager: project_role_key = 21, id = 161
        $pm_name = '';
        $pm_contact = ProjectContactRole::where('project_key', '=', $this->audit->development_key)
                                ->where('project_role_key', '=', 21)
                                ->with('organization.address')
                                ->first();

        if ($pm_contact) {
            if ($pm_contact->organization) {
                $pm_name = $pm_contact->organization->organization_name;
            }
            if ($pm_name == '') {
                if ($pm_contact->person) {
                    $pm_name = $pm_contact->person->first_name." ".$pm_contact->person->last_name;
                }
            }
        }
        

        // inspection_schedule_json needs to be populated TBD

        // if no organization put contact name under the project name

        // build amenities array using amenity_inspections table

        // save summary
        $this->audit->selection_summary = json_encode($summary);
        //$this->audit->save();

        // create or update
        $cached_audit = CachedAudit::where('audit_id','=',$this->audit->id)->first();

        // total items is the total number of units added during the selection process
        

        if($cached_audit){
            // when updating a cachedaudit, run the status test
            $total_items = $this->audit->total_items(); 
            // $inspection_schedule_checks = $cached_audit->checkStatus('schedules');
            // $inspection_status_text = $inspection_schedule_checks['inspection_status_text']; 
            // $inspection_schedule_date = $inspection_schedule_checks['inspection_schedule_date'];
            // $inspection_schedule_text = $inspection_schedule_checks['inspection_schedule_text'];
            // $inspection_status = $inspection_schedule_checks['inspection_status']; 
            // $inspection_icon = $inspection_schedule_checks['inspection_icon'];
            

            $inspection_status_text = $cached_audit->inspection_status_text;
            $inspection_schedule_text = $cached_audit->inspection_schedule_text;
             

            // to change existing records (tooltip wording)
            if($inspection_schedule_text == 'CLICK TO SCHEDULE AUDIT') {
                $inspection_schedule_text = 'SCHEDULED AUDITS/TOTAL AUDITS';
            }
            
            $inspection_schedule_date = $cached_audit->inspection_schedule_date;
            $inspection_status = $cached_audit->inspection_status; 
            $inspection_icon = $cached_audit->inspection_icon;

            //if($inspection_schedule_checks['status'] == 'critical'){
            //    $status = 'critical'; // TBD critical/other
            //}else{
                $status = ''; // TBD critical/other
            //}
            
            // current step
            $step = $cached_audit->current_step();
            if(!$step){
                $step_id = 1;
                $step_icon = 'a-home-question';
                $step_status_text = 'REVIEW INSPECTABLE AREAS';
            }else{
                $step_id = $step->id;
                $step_icon = $step->icon;
                $step_status_text = $step->step_help;
            }

            $cached_audit->update([
                'audit_id' => $this->audit->id,
                'audit_key' => $this->audit->monitoring_key,
                'project_id' => $project->id,
                'project_key' => $this->audit->development_key,
                'project_ref' => $project_ref,
                'status' => $status,
                'lead' => $lead,
                'lead_json' => $lead_json,
                'title' => $project_name,
                'pm' => $pm_name,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'total_buildings' => $total_buildings,
                'inspection_icon' => $inspection_icon,
                'inspection_status' => $inspection_status, 
                'inspection_status_text' => $inspection_status_text,
                'inspection_schedule_text' => $inspection_schedule_text,
                'inspection_schedule_date' => $inspection_schedule_date,
                'inspection_schedule_json' => null, // TBD
                'inspectable_items' => 0,
                'total_items' => $total_items,
                'audit_compliance_icon' => 'a-circle-checked',
                'audit_compliance_status' => 'ok-actionable',
                'audit_compliance_status_text' => 'AUDIT COMPLIANT',
                'followup_status' => '',
                'followup_status_text' => 'NO FOLLOWUPS',
                'file_audit_icon' => 'a-folder',
                'file_audit_status' => '',
                'file_audit_status_text' => 'CLICK TO ADD A FINDING',
                'nlt_audit_icon' => 'a-booboo',
                'nlt_audit_status' => '',
                'nlt_audit_status_text' => 'CLICK TO ADD A FINDING',
                'lt_audit_icon' => 'a-skull',
                'lt_audit_status' => '',
                'lt_audit_status_text' => 'CLICK TO ADD A FINDING',
                'smoke_audit_icon' => 'a-flames',
                'smoke_audit_status' => '',
                'smoke_audit_status_text' => 'CLICK TO ADD A FINDING',
                'auditor_status_icon' => 'a-avatar-fail',
                'auditor_status' => 'action-required',
                'auditor_status_text' => 'ASSIGN AUDITORS',
                'message_status_icon' => 'a-envelope-4',
                'message_status' => '',
                'message_status_text' => '',
                'document_status_icon' => 'a-files',
                'document_status' => '',
                'document_status_text' => 'DOCUMENT STATUS',
                'history_status_icon' => 'a-person-clock',
                'history_status' => '',
                'history_status_text' => 'NO/VIEW HISTORY',
                'step_id' => $step_id,
                'step_status_icon' => $step_icon,
                'step_status' => 'no-action',
                'step_status_text' => $step_status_text,
                'estimated_time' => $cached_audit->estimated_time,
                'estimated_time_needed' => $cached_audit->estimated_time_needed,
                //'amenities_json' => json_encode($amenities)
            ]);
        }else{
            

            $cached_audit = new CachedAudit([
                'audit_id' => $this->audit->id,
                'audit_key' => $this->audit->monitoring_key,
                'project_id' => $project_id,
                'project_key' => $this->audit->development_key,
                'project_ref' => $project_ref,
                'status' => '',
                'lead' => $lead,
                'lead_json' => $lead_json,
                'title' => $project_name,
                'pm' => $pm_name,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'total_buildings' => $total_buildings,
                'inspection_icon' => $inspection_icon,
                'inspection_status' => $inspection_status, 
                'inspection_status_text' => $inspection_status_text,
                'inspection_schedule_text' => $inspection_schedule_text,
                'inspection_schedule_date' => $inspection_schedule_date,
                'inspection_schedule_json' => null, // TBD
                'inspectable_items' => $this->audit->amenity_inspections->count(),
                'total_items' => $this->audit->total_items(),
                'audit_compliance_icon' => 'a-circle-checked',
                'audit_compliance_status' => 'ok-actionable',
                'audit_compliance_status_text' => 'AUDIT COMPLIANT',
                'followup_status' => '',
                'followup_status_text' => 'NO FOLLOWUPS',
                'file_audit_icon' => 'a-folder',
                'file_audit_status' => '',
                'file_audit_status_text' => 'CLICK TO ADD A FINDING',
                'nlt_audit_icon' => 'a-booboo',
                'nlt_audit_status' => '',
                'nlt_audit_status_text' => 'CLICK TO ADD A FINDING',
                'lt_audit_icon' => 'a-skull',
                'lt_audit_status' => '',
                'lt_audit_status_text' => 'CLICK TO ADD A FINDING',
                'smoke_audit_icon' => 'a-flames',
                'smoke_audit_status' => '',
                'smoke_audit_status_text' => 'CLICK TO ADD A FINDING',
                'auditor_status_icon' => 'a-avatar-fail',
                'auditor_status' => 'action-required',
                'auditor_status_text' => 'ASSIGN AUDITORS',
                'message_status_icon' => 'a-envelope-4',
                'message_status' => '',
                'message_status_text' => '',
                'document_status_icon' => 'a-files',
                'document_status' => '',
                'document_status_text' => 'DOCUMENT STATUS',
                'history_status_icon' => 'a-person-clock',
                'history_status' => '',
                'history_status_text' => 'NO/VIEW HISTORY',
                'step_id' => 1,
                'step_status_icon' => 'a-home-question',
                'step_status' => 'no-action',
                'step_status_text' => 'REVIEW INSPECTABLE AREAS',
                'estimated_time' => $estimated_time,
                'estimated_time_needed' => $estimated_time_needed,
                //'amenities_json' => json_encode($amenities)
            ]);
            $cached_audit->save();
        }

        // $data = [
        //     'event' => 'NewMessage',
        //     'data' => [
        //         'stats_communication_total' => $stats_communication_total
        //     ]
        // ];

        // Redis::publish('communications', json_encode($data));
    }









    public function updateProjection($summary = null)
    {
        // create cached audit
        //
        $projection = $this->projection;
        $audit = $this->audit;
        $project = $this->project;
        $project_id = null;
        $development_key = null;
        $project_ref = '';
        $project_name = null;
        $total_building_count = 0;
        $total_unit_count = 0;
        $total_program_units = 0;
        $total_market_rate_units = 0;

        //get program specific details

        $programs = $this->project->programs;

        $p = 1;

        foreach ($programs as $program) {

            // determine program type
            if(in_array($program->program_key, $this->program_bundle_ids)){
                $program_type = 'BUNDLE';
                
            }
            if(in_array($program->program_key, $this->program_htc_ids)){
                $program_type = 'HTC';
            }
            if(in_array($program->program_key, $this->program_811_ids)){
                $program_type = '811';
            }
            if(in_array($program->program_key, $this->program_home_ids)){

                $program_type = 'HOME'.str_replace(' ','',str_replace('-', '', $program->award_number));
            }
            if(in_array($program->program_key, $this->program_ohtf_ids)){
                $program_type = 'OHTF'.str_replace(' ','',str_replace('-', '', $program->award_number));
            }
            if(in_array($program->program_key, $this->program_nhtf_ids)){
                $program_type = 'NHTF'.str_replace(' ','',str_replace('-', '', $program->award_number));
            }
            if(in_array($program->program_key, $this->program_medicaid_ids)){
                $program_type = 'MEDICAID';
            }

            //set names of columns to be updated
            $project_program_key = 'program_'.$p.'_project_program_key';
            $funding_program_key = 'program_'.$p.'_funding_program_key';
            $program_key = 'program_'.$p.'_program_key';

            $project_program_id = 'program_'.$p.'_project_program_id';
            $program_id = 'program_'.$p.'_program_id';

            $program_name ='program_'.$p.'_name';
            $program_multibuilding_election = 'program_'.$p.'_multiple_building_election';
            $program_status = 'program_'.$p.'_project_program_status';
            $program_award_number = 'program_'.$p.'_project_program_award_number';
            $program_guide_year = 'program_'.$p.'_project_program_guide_year';
            $program_extended_use = 'program_'.$p.'_first_year_award_claimed';
            
            
            $program_keyed_in_count = 'program_'.$p.'_keyed_in_unit_count';
            $program_calculated_count = 'program_'.$p.'_calculated_unit_count';
            //$program_2016_percentage_used = 'program_'.$p.'_2016_percentage_used';

            //$program_2016_site_count = 'program_'.$p.'_2016_site_count';
            $program_2019_site_count = 'program_'.$p.'_2019_site_count';
            $program_2019_site_difference_percent = 'program_'.$p.'_2019_site_difference_percent';
            $program_2019_buildings_with_unit_inspections = 'program_'.$p.'_2019_buildings_with_unit_inspections';

            //$program_2016_file_count = 'program_'.$p.'_2016_file_count';
            $program_2019_file_count = 'program_'.$p.'_2019_file_count';
            $program_2019_file_difference_percent = 'program_'.$p.'_2019_file_difference_percent';

            // set values
            // get the project program
            $this_program_calculated_count = $this->units->where('program_id',$program->program_id)->count();
            $this_program_site_count = UnitInspection::where('audit_id',$this->audit->id)->where('program_id',$program->program_id)->where('is_site_visit',1)->count();
            $this_program_file_count = UnitInspection::where('audit_id',$this->audit->id)->where('program_id',$program->program_id)->where('is_file_audit',1)->count();
            // if(!is_null($this->program_percentages[$program_type]['_2016_count'])){
            //     $percent_difference = ($this_program_site_count * 100) / $this->program_percentages[$program_type]['_2016_count'];
            // } else {
            //     $percent_difference = "NA - NO UNITS";
            // }
            if(!is_null($program->multiple_building_status)){
                $mbs = $program->multiple_building_status->election_description;
            }else{
                $mbs = "NOT SET";
            }
            //dd($this->program_percentages[$program_type]);
            $projection->update([

                $project_program_key => $program->project_program_key,
                $funding_program_key => $program->program->funding_program_key,
                $program_key => $program->program_key,

                $project_program_id => $program->id,
                $program_id => $program->program_id,

                $program_name =>$program->program->program_name,
                $program_multibuilding_election =>  $mbs,
                $program_status => $program->status->status_name,
                $program_award_number => $program->award_number,
                $program_guide_year => $program->guide_l_year,
                $program_extended_use => $program->first_year_award_claimed,
                
                
                $program_keyed_in_count =>  $program->total_unit_count,
                $program_calculated_count => $this_program_calculated_count,
                //$program_2016_percentage_used => $this->program_percentages[$program_type]['percent'],

                //$program_2016_site_count => $this->program_percentages[$program_type]['_2016_count'],
                $program_2019_site_count => $this_program_site_count,
                $program_2019_site_difference_percent =>  $percent_difference.'%',

                //$program_2016_file_count => $this->program_percentages[$program_type]['_2016_count'],
                $program_2019_file_count => $this_program_file_count,
                $program_2019_file_difference_percent => $percent_difference.'%'

            ]);

            $p++;

        }
        

        
                
            $inspections = UnitInspection::where('audit_id',$this->audit->id)->get();

            $optimized_site = $inspections->where('is_site_visit',1)->groupBy('unit_key')->count();
            $optimized_file = $inspections->where('is_file_audit',1)->groupBy('unit_key')->count();

            $_2019_buildings_with_unit_inspections = $inspections->where('is_site_visit',1)->groupBy('building_key')->count();      
            

            $projection->update([
                'audit_id' => $this->audit->id,
                'project_id' => $this->project->id,
                'development_key' => $this->project->project_key,
                'project_name' =>  $this->project->project_name,
                'project_number' =>  $this->project->project_name,
                'total_building_count' => $this->project->total_building_count,
                'total_unit_count' => $this->project->stats_total_units(),
                'total_program_unit_count' => $this->units->groupBy('unit_key')->count(),
                'total_market_rate_unit_count' => $this->project->stats_total_market_rate_units(),
                'optimized_2019_site_count' => $optimized_site,
                'optimized_2019_file_count' => $optimized_file,
                '2019_buildings_with_unit_inspections' => $_2019_buildings_with_unit_inspections,
                'run' => 1,
                'running' => 0

            ]);

            //dd($optimized_site,$optimized_file,$inspections);
            echo $this->projection->id."<br />";

        // $data = [
        //     'event' => 'NewMessage',
        //     'data' => [
        //         'stats_communication_total' => $stats_communication_total
        //     ]
        // ];

        // Redis::publish('communications', json_encode($data));this->
   }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function runSimpleCompliance($audit)
    {
        $audit = Audit::find($audit);
        //dd($audit);
        //$this->projection = Projection::where('run',0)->first();
            if(null !== $audit and null !== $audit->project){
                $this->audit = $audit;
                $this->project = $this->audit->project;
                $this->extended_use = 0;

                //dd($this->audit,$this->project);
                
                //LOG HERE if it is a brand new audit run
                //LOG HERE if it is a rerun audit and who asked for it
                
                $this->audit->comment = 'Audit process starting at '.date('m/d/Y h:i:s A',time());
                $this->audit->comment_system = 'Audit process starting at '.date('m/d/Y h:i:s A',time());
                //$this->audit->save();
                //Remove all associated amenity inspections
                \App\Models\AmenityInspection::where('audit_id',$this->audit->id)->delete();
                $this->audit->comment_system = $this->audit->comment_system.' | Deleted AmenityInspections';
                //$this->audit->save();
                //$this->processes++;
                //Remove Unit Inspections
                \App\Models\UnitInspection::where('audit_id',$this->audit->id)->delete();
                $this->audit->comment_system = $this->audit->comment_system.' | Deleted Unit Inspections';
                //$this->audit->save();
                //$this->processes++;
                //Remove Project Details for this Audit
                \App\Models\ProjectDetail::where('audit_id',$this->audit->id)->delete();
                $this->audit->comment_system = $this->audit->comment_system.' | Deleted Project Details';
                //$this->audit->save();
                //$this->processes++;
                //Remove the Cached Audit
                \App\Models\CachedAudit::where('audit_id', '=', $this->audit->id)->delete();
                $this->audit->comment_system = $this->audit->comment_system.' | Removed the CachedAudit';
                //$this->audit->save();
                //$this->processes++;

                //Remove the Ordering Building
                \App\Models\OrderingBuilding::where('audit_id', '=', $this->audit->id)->delete();
                $this->audit->comment_system = $this->audit->comment_system.' | Removed the OrderingBuilding';
                //$this->audit->save();
                //$this->processes++;

                //Remove the Ordering Unit
                \App\Models\OrderingUnit::where('audit_id', '=', $this->audit->id)->delete();
                $this->audit->comment_system = $this->audit->comment_system.' | Removed the OrderingUnit';
                //$this->audit->save();
                //$this->processes++;

                // //get the current audit units:
                $this->audit->comment = $this->audit->comment.' | Fetching Audit Units';
                $this->audit->comment_system = $this->audit->comment_system.' | Running Fetch Audit Units, build UnitProgram';
                //$this->audit->save();
                //$this->processes++;
                $this->fetchAuditUnits($audit);
                $this->audit->comment_system = $this->audit->comment_system.' | Finished Fetch Units';
                //$this->audit->save();
                //$this->processes++;

                
                // //get the current audit units:
                $this->audit->comment = $this->audit->comment.' | Fetching Audit Units';
                $this->audit->comment_system = $this->audit->comment_system.' | Running Fetch Audit Units, build UnitProgram';
                                            //$this->audit->save();
                                            
                $this->fetchAuditUnits($this->audit);
                $this->audit->comment_system = $this->audit->comment_system.' | Finished Fetch Units';
                                            //$this->audit->save();
                                            
                
                //$check = 1;
                

                if ($this->units->count()) {
                    $this->audit->comment_system = $this->audit->comment_system.' | UnitProgram has records, we can start the selection process.';
                                            //$this->audit->save();
                                            
                    // run the selection process 10 times and keep the best one
                    $best_run = null;
                    $best_total = null;
                    $overlap = null;
                    $project = null;
                    $organization_id = null;
                    

                    //$timesToRun = SystemSetting::where('key','times_to_run_compliance_selection')->first();

                    //$timesToRun = $timesToRun->value;
                    $timesToRun = 1;

                    for ($i=0; $i<$timesToRun; $i++) {
                        $this->audit->comment_system = $this->audit->comment_system.' | Starting selection run # '.$i.'.';
                                            //$this->audit->save();
                        $summary = $this->selectionProcess($this->audit);
                        
                        //Log::info('audit '.$i.' run;');
                        $timesRun = $i + 1;
                        
                        $this->audit->comment_system = $this->audit->comment_system.' | Finished Selection Run #'.$timesRun.'.';
                                            //$this->audit->save();
                                            

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

                            $units = $this->project->units->whereIn('unit_key', $unit_keys);

                            //$units = Unit:->get();
                            
                            //dd($unit_keys,$units[0]->programs->where('audit_id',$this->audit->id));
                            $unit_inspections_inserted = 0;

                            foreach ($units as $unit) {
                                //dd($units->groupBy('unit_key'),$units,$unit);
                                if (in_array($unit->unit_key, $overlap)) {
                                    $has_overlap = 1;
                                } else {
                                    $has_overlap = 0;
                                }

                                $program_keys = explode(',', $program['program_keys']);
                                

                                foreach ($unit->programs->where('audit_id',$this->audit->id) as $unit_program) {
                                    if (in_array($unit_program->program_key, $program_keys) && $unit_inspections_inserted < $program['required_units']) {
                                        $u = new UnitInspection([
                                            'group' => $program['name'],
                                            'group_id' => $program['group'],
                                            'unit_id' => $unit->id,
                                            'unit_key' => $unit->unit_key,
                                            'unit_name' => $unit->unit_name,
                                            'building_id' => $unit->building_id,
                                            'building_key' => $unit->building_key,
                                            'audit_id' => $this->audit->id,
                                            'audit_key' => $this->audit->monitoring_key,
                                            'project_id' => $project->id,
                                            'project_key' => $project->project_key,
                                            'program_key' => $unit_program->program_key,
                                            'program_id' => $unit_program->program_id,
                                            'pm_organization_id' => $organization_id,
                                            'has_overlap' => $has_overlap,
                                            'is_site_visit' => 1,
                                            'is_file_audit' => 0,
                                            'unit_program_id' => $unit_program->id
                                        ]);
                                        $u->save();
                                        $unit_inspections_inserted++;
                                        
                                    }
                                }
                            }

                            // FILE AUDIT
                            $unit_keys = $program['units_before_optimization'];

                            

                           $units = $this->project->units->whereIn('unit_key', $unit_keys);
                            

                            $unit_inspections_inserted = 0;

                            foreach ($units as $unit) {
                                
                                if (in_array($unit->unit_key, $overlap)) {
                                    $has_overlap = 1;
                                } else {
                                    $has_overlap = 0;
                                }

                                $program_keys = explode(',', $program['program_keys']);
                                

                                
                                foreach ($unit->programs->where('audit_id',$this->audit->id) as $unit_program) {
                                    if (in_array($unit_program->program_key, $program_keys) && $unit_inspections_inserted < count($program['units_before_optimization'])) {

                                        $u = new UnitInspection([
                                            'group' => $program['name'],
                                            'group_id' => $program['group'],
                                            'unit_id' => $unit->id,
                                            'unit_key' => $unit->unit_key,
                                            'unit_name' => $unit->unit_name,
                                            'building_id' => $unit->building_id,
                                            'building_key' => $unit->building_key,
                                            'audit_id' => $this->audit->id,
                                            'audit_key' => $this->audit->monitoring_key,
                                            'project_id' => $project->id,
                                            'project_key' => $project->project_key,
                                            'program_key' => $unit_program->program_key,
                                            'program_id' => $unit_program->program_id,
                                            'pm_organization_id' => $organization_id,
                                            'has_overlap' => $has_overlap,
                                            'is_site_visit' => 0,
                                            'is_file_audit' => 1,
                                            'unit_program_id' => $unit_program->id
                                        ]);
                                        $u->save();
                                        $unit_inspections_inserted++;
                                        
                                    }
                                }
                            }

                            
                        }
                    }
                    //LOG::info('unit inspections should be there.');
                    $this->addAmenityInspections($this->audit);
                    $this->createNewCachedAudit($best_run);    // finally create the audit
                    $this->createNewProjectDetails($this->audit); // create the project details
                    
                    // LOG SUCCESS HERE
                    $this->audit->compliance_run = 1;
                    $this->audit->rerun_compliance = 0;
                    $this->audit->comment .= 'Audit process finished at '.date('m/d/Y h:i:s A',time()).'.';
                    $this->audit->comment_system .= 'Audit process finished at '.date('m/d/Y h:i:s A',time());

                //$this->audit->save();

                } else {
                    $this->audit->comment_system = "Unable to get program units from devco. Cannot run compliance run and generate the audit.";
                    $this->audit->comment = "Unable to get program units from devco. Cannot run compliance run and generate the audit.";
                    $this->audit->compliance_run = 0;
                    $this->audit->rerun_compliance = 0;
                    //$this->audit->save();
                }
                $this->audit->save();  
            } else {
                return 'No audit found.';
             
            }
          
    }
}
