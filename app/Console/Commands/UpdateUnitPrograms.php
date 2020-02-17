<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UnitProgram;
use App\Models\UnitGroup;
use App\Models\Building;
use App\Models\Audit;
use App\Models\Unit;
use App\Models\Program;
use App\Models\ProjectProgram;
use App\Services\DevcoService;
use App\Models\CachedAudit;
 
class UpdateUnitPrograms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:unitprograms';

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
    public function getAudit(){
        $auditInput = $this->ask('Update unit programs for which audit number?');
        $audit = Audit::find($auditInput);
        if($audit){
            
            $this->line(PHP_EOL.$audit->project->project_number.': '.$audit->project->project_name.' Audit:'.$audit->id);
            if($this->confirm('Is that the audit you wanted?')){
                return $audit;
            } else {
                $this->line(PHP_EOL.'Sorry about that --- please try again with a different audit number.');
                return null;
            }
        }else{
            $this->line(PHP_EOL.'Sorry I could not find an audit matching that number:'.$auditInput);
            return null;
        }
    }

    public function updateAudit($audit){
        $newInserts = 0;
        $recordsSkipped = 0;
        $noProjectPrograms = array();
        $inserts = array();
        $updateProjectProgramKeys = 0;
        if(!is_null($audit)){
            $this->line(PHP_EOL.'Checking audit '.$audit->id);
            $projectUnits = $audit->project->units;

            $this->line(PHP_EOL.'There are a total of '.count($projectUnits).' project units.');

            $unitPrograms = UnitProgram::where('audit_id',$audit->id)->groupBy('unit_key')->get();
            forEach($unitPrograms as $u){
                $this->line('   • '.$u->unit_key.''. PHP_EOL);
            }

            $this->line(count($unitPrograms).' have a unit program entry'. PHP_EOL);

            if($this->confirm('Continue to rerun program unit additions?')){

                $audit->comment_system = $audit->comment_system.' | '.date('m/d/Y g:h:i a',time()).' Running update:unitprograms from the console to get missing unit to program status saved in UnitProgram for this audit.';
                $audit->save();
                                            
                
                                            
                
                
                
                $apiConnect = new DevcoService();
                // paths to the info we need: dd($audit, $audit->project, $audit->project->buildings);
                

                // Get all the units we need to get programs for:

                $buildings = $audit->project->buildings;
                if (!is_null($buildings)) {
                //Process each building
                    $this->line(PHP_EOL.'Processing '.count($buildings).' buildings');
                    $audit->comment_system = $audit->comment_system.' | Processing '.count($buildings).' buildings';
                    $audit->save();
                    foreach ($buildings as $building) {

                        $this->line('=======================================================================================');
                        $this->line('=======================================================================================');
                        $this->line(PHP_EOL.'Processing '.$building->building_key.': '.$building->building_name.' building');
                        $audit->comment_system = $audit->comment_system.' | Processing '.$building->building_key.': '.$building->building_name.' building';
                        $audit->save();
                        //Get the building's units
                        $buildingUnits = $building->units;

                        if (!is_null($buildingUnits)) {
                                $this->line(PHP_EOL.'Processing '.count($buildingUnits).' units in this building.');
                                $audit->comment_system = $audit->comment_system.' | Processing '.count($buildingUnits).' units in this building.';
                                $audit->save();
                            // Process each unit
                            foreach ($buildingUnits as $unit) {

                                $this->line('===============================================');
                                $this->line(PHP_EOL.'120 Processing unit key:'.$unit->unit_key.':'.$unit->unit_name.' unit.');
                                $audit->comment_system = $audit->comment_system.' | Processing unit key:'.$unit->unit_key.':'.$unit->unit_name.' unit.';
                                $audit->save();
                                // Get the unit's current program designation from DevCo
                                try {
                                    $unitProjectPrograms = $apiConnect->getUnitProjectPrograms($unit->unit_key, 1, 'admin@allita.org', 'Getting Project Unit Program Data', 1, 'SystemServer');
                                    if(!is_null($unitProjectPrograms)){
                                       $this->line(PHP_EOL.'127 Successfully pulled project program data from DEVCO for unit key:'.$unit->unit_key.':'.$unit->unit_name.' unit.');
                                        $audit->comment_system = $audit->comment_system.' | Successfully pulled project program data from DEVCO unit key:'.$unit->unit_key.':'.$unit->unit_name.' unit.';
                                        $audit->save(); 
                                    } else {
                                        $this->error(PHP_EOL.'No project program data was returned from DEVCO for unit key:'.$unit->unit_key.':'.$unit->unit_name.' unit.');
                                        $audit->comment_system = $audit->comment_system.' | !!!! NO PROJECT PROGRAM DATA !!! from DEVCO unit key:'.$unit->unit_key.':'.$unit->unit_name.' unit.';
                                        $audit->save(); 
                                    }
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
                                    if(!is_array($projectPrograms) || count($projectPrograms)< 1){
                                        $noProjectPrograms[] = $unit->unit_key;
                                        $this->error(PHP_EOL.'NO PROJECT PROGRAM DATA ACTUALLY RETURNED FOR UNIT KEY '.$unit->unit_key);
                                        //dd($projectPrograms);
                                        $audit->comment_system = $audit->comment_system.' | !!! NO PROJECT PROGRAM DATA RETURNED from DEVCO for unit key:'.$unit->unit_key.':'.$unit->unit_name.'.';
                                        $audit->comment = $audit->comment.' | !!! NO PROJECT PROGRAM DATA RETURNED from DEVCO for unit key:'.$unit->unit_key.':'.$unit->unit_name.'.';
                                        $audit->save(); 
                                    } else {
                                        $this->line(PHP_EOL.'There are '.count($projectPrograms).' project programs for this unit.');
                                    }
                                    foreach ($projectPrograms as $pp) {
                                        
                                        $this->line(PHP_EOL.'159 Initializing loop of projectPrograms');
                                        $pp = $pp->attributes;
                                        if(is_null($pp->endDate) && !$is_market_rate){
                                            $this->line(PHP_EOL.'Unit Key:'.$pp->unitKey.', Development Program Key:'.$pp->developmentProgramKey.', Start Date:'.date('m/d/Y',strtotime($pp->startDate)));
                                            $audit->comment = $audit->comment.' | Unit Key:'.$pp->unitKey.', Development Program Key:'.$pp->developmentProgramKey.', Start Date:'.date('m/d/Y',strtotime($pp->startDate));

                                            $audit->comment_system = $audit->comment_system.' | Unit Key:'.$pp->unitKey.', Development Program Key:'.$pp->developmentProgramKey.', Start Date:'.date('m/d/Y',strtotime($pp->startDate));
                                            $audit->save();

                                            //get the matching program from the developmentProgramKey
                                            $program = ProjectProgram::where('project_program_key',$pp->developmentProgramKey)->with('program')->first();
                                            if(!is_null($program)){
                                                $this->line(PHP_EOL.$program->program->program_name.' '.$program->program_id);
                                                $audit->comment = $audit->comment.' | '.$program->program->program_name.' '.$program->program_id;
                                                $audit->comment_system = $audit->comment_system.' | '.$program->program->program_name.' '.$program->program_id;
                                                $audit->save();

                                                // $record[] = [
                                                //     'project_id' => $project->id,
                                                //     'project_key' => $project->project_key,
                                                //     'unit_id' => $unit->id,
                                                //     'unit_key' => $unit->unit_key,
                                                //     'program_id' => $program->program_id
                                                // ];
                                                $this->line(PHP_EOL.'Checking if this exists in the records...');
                                                $check = UnitProgram::where('unit_key',$unit->unit_key)->where('program_key',$program->program_key)->where('audit_id',$audit->id)->where('development_key',$audit->development_key)->where('project_program_key',$pp->developmentProgramKey)->first();
                                                $updateProjectProgramKey = UnitProgram::where('unit_key',$unit->unit_key)->where('program_key',$program->program_key)->where('audit_id',$audit->id)->first();

                                                if (!is_null($program) && is_null($check)) {
                                                    $this->line(PHP_EOL.'Unit Program does not exist - inserting record.');
                                                    if(!is_null($updateProjectProgramKey)) {
                                                        $this->line('Update missing project program key...');
                                                        $inserts[] = 'unit_key: '.$unit->unit_key.' | program_key: '.$program->program_key.' | development_key: '.$audit->development_key.' | project_prgram_key: '.$pp->developmentProgramKey;
                                                        $updateProjectProgramKeys++;
                                                        
                                                        UnitProgram::where('unit_key',$unit->unit_key)->where('program_key',$program->program_key)->where('audit_id',$audit->id)->update(['project_program_key' => $pp->developmentProgramKey]);
                                                        $updateProjectProgramKey->save();
                                                        $audit->comment = $audit->comment.' | Updating missing project program data';
                                                        $audit->comment_system = $audit->comment_system.' | Updating missing project program data';
                                                        $audit->save();
                                                    } else {
                                                        $this->line(PHP_EOL.' • unit_key : '.$unit->unit_key.PHP_EOL.' • program_key : '.$program->program_key.PHP_EOL.' • audit_id : '.$audit->id.PHP_EOL.' • development_key : '.$audit->development_key.PHP_EOL.' • project_program_key : '.$pp->developmentProgramKey);
                                                        $newInserts++;
                                                    
                                                        $audit->comment = $audit->comment.' | Inserting missing unit program data';
                                                        $audit->comment_system = $audit->comment_system.' | Inserting missing unit program data';
                                                        $audit->save();
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
                                                    }
                                                } else {
                                                    $recordsSkipped ++;
                                                    $this->error(PHP_EOL.'Unit Program Record Exists:');
                                                    $audit->comment = $audit->comment.' | Unit Program Record Exists. Skipping insertion.';
                                                    $audit->comment_system = $audit->comment_system.' | Unit Program Record Exists. Skipping insertion.';
                                                    $audit->save();
                                                }
                                                
                                            } else {
                                                $this->error(PHP_EOL.'Unable to find project program with key '.$pp->developmentProgramKey.' on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key
                                                 );
                                                $audit->comment = $audit->comment.' | Unable to find project program with key '.$pp->developmentProgramKey.' on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key;
                                                $audit->comment_system = $audit->comment_system.' | Unable to find project program with key '.$pp->developmentProgramKey.' on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key;
                                                $audit->save();
                                                //Log::info('Unable to find program with key of '.$unitProgram['attributes']['programKey'].' on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key);
                                            }
                                        } else {
                                            // market rate?
                                            $program = ProjectProgram::where('project_program_key',$pp->developmentProgramKey)->with('program')->first();
                                            if($is_market_rate){
                                                $this->line(PHP_EOL."MARKET RATE, CANCELLED:".$program->program->program_name.' '.$program->program_id.', Start Date:'.date('m/d/Y',strtotime($pp->startDate)).', End Date: '.date('m/d/Y',strtotime($pp->endDate)));

                                                $audit->comment_system = $audit->comment_system." | MARKET RATE, CANCELLED:<del>".$program->program->program_name.' '.$program->program_id.'</del>, Start Date:'.date('m/d/Y',strtotime($pp->startDate)).', End Date: '.date('m/d/Y',strtotime($pp->endDate));
                                                $audit->save();
                                            }else{
                                                $this->line(PHP_EOL." | CANCELLED:<del>".$program->program->program_name.' '.$program->program_id.'</del>, Start Date:'.date('m/d/Y',strtotime($pp->startDate)).', End Date: '.date('m/d/Y',strtotime($pp->endDate)));

                                                $audit->comment_system = $audit->comment_system." | CANCELLED:<del>".$program->program->program_name.' '.$program->program_id.'</del>, Start Date:'.date('m/d/Y',strtotime($pp->startDate)).', End Date: '.date('m/d/Y',strtotime($pp->endDate));
                                                $audit->save();
                                            }
                                            
                                        }
                                    }
                                } catch (Exception $e) {
                                    
                                    //dd('Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key);
                                    $this->error(PHP_EOL." | Unable to get the unit programs on unit_key".$unit->unit_key.' for audit'.$audit->monitoring_key);

                                    $audit->comment = $audit->comment.' | Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key;
                                    $audit->comment_system = $audit->comment_system.' | Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key;
                                            $audit->save();
                                }
                            }
                            $this->line(PHP_EOL.'Finished Loop of Units.');
                            $audit->comment_system = $audit->comment_system.' | Finished Loop of Units';
                                            $audit->save();
                                            
                        } else {
                            //dd('Could not get building units');
                            $this->error(PHP_EOL.'Could not get building units');
                            $audit->comment = $audit->comment.' | Could not get building units';
                            $audit->comment_system = $audit->comment_system.' | Could not get building units';
                                            $audit->save();
                                            
                        }
                    }
                    $this->line(PHP_EOL.'Finished script');
                    $audit->comment = $audit->comment.' | Finished running script at '.date('g:h:i a',time()).'.';
                    $audit->comment_system = $audit->comment_system.' | Finished running script at '.date('g:h:i a',time()).'.';
                    $audit->save();
                                            
                    //return 1;
                } else {
                    //dd('NO BUILDINGS FOUND TO GET DATA');
                    $this->error('No buildings found to get data');
                    $audit->comment = $audit->comment.' | NO BUILDINGS FOUND TO GET DATA';
                    $audit->comment_system = $audit->comment_system.' | NO BUILDINGS FOUND TO GET DATA';
                                            $audit->save();
                                            
                }
                $this->line($newInserts.' Unit Program Records Inserted'.PHP_EOL.$recordsSkipped.' Units skipped that were already loaded'.PHP_EOL.$updateProjectProgramKeys.' Records Updated with Missing Project Program Keys.');
                $this->line('=============================================');
                $this->line('UPDATED PROJECT PROGRAM KEYS ON');
                forEach($inserts as $n){
                    $this->line('   • UnitKey: '.$n);
                }
                $this->line('=============================================');
                $this->line('UNITS THAT HAVE NO UNIT PROGRAM DATA IN DEVCO');
                forEach($noProjectPrograms as $n){
                    $this->line('   • UnitKey: '.$n);
                }

            }
        }
    }

    public function handle()
    {
        $audit = null;
        $auditApproved = 0;
        if($this->confirm('Would you like to check all the audits?')){
            $audits = CachedAudit::get();
            foreach($audits as $audit){
                $audit = Audit::find($audit->audit_id);
                $this->updateAudit($audit);
            }
        } else {
            $audit = $this->getAudit();
            $this->updateAudit($audit);
        }
        


        
    }


}
