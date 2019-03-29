<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use DateTime;
use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\SystemSetting;
use Auth;
use App\Models\Project;
use App\Models\ProjectProgram;

class UnitToProjectProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unit:project_program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Once';

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
        $cannotRun = '';
        $cannotRunCount = 0;
        $canRun = '';
        $canRunCount = 0;
        $output = '';
        $go = 1;
        $project_key = $this->ask('Enter project_key or just press return to run all.');

        if($project_key){
            $projectKeyEval = '=';
            $projectKeyVal = $project_key;
        } else {
            $projectKeyEval = '>';
            $projectKeyVal = 0;
        }
        
        $this->line(PHP_EOL.PHP_EOL."ASSIGN PROJECT PROGRAM KEYS TO UNITS".PHP_EOL.PHP_EOL);

        $projects = Project::with('units')->with('programs')->with('all_other_programs')
        ->where('id','!=','44392')
        ->where('id','!=','44985')
        ->where('id','!=','45026')
        ->where('id','!=','45247')
        ->where('id','!=','45529')
        ->where('id','!=','45570')
        ->where('id','!=','45803')
        ->where('id','!=','45920')
        ->where('id','!=','45920')
        ->where('project_key',$projectKeyEval, $projectKeyVal)
        ->get();

        $this->line('PROCESSING '.count($projects).' PROJECTS'.PHP_EOL.PHP_EOL);

        
        $projectCount = 0;
        foreach($projects as $project){
            $projectCount++;
            if(count($project->programs)>0 && count($project->units)>0){
                $this->line("Project {$project->project_number}, ID: {$project->id}, Key: {$project->project_key}".PHP_EOL.PHP_EOL);
                //dd($project,$project->programs);
                $programs = $project->programs;
                $units = $project->units;
                $currentFundingKey = 0;
                $duplicateFundingKey = 0;
                $fundingKeys = '';
                $fundingKeys = array();
                $projectPrograms = '';
                $otherFundingKeys= '';
                $otherFundingKeys = array();
                $programFundingKeyToProgramKey = '';
                $programFundingKeyToProgramKey = array();
                
                foreach($programs as $program){
                    // put the funding keys into an array
                    $projectPrograms .= $program->program->program_name." - funding program key: {$program->program->funding_program_key} | award number: {$program->award_number}<br />";
                    $fundingKeys[] = $program->program->funding_program_key;
                    $programFundingKeyToProgramKey['key'.$program->program->funding_program_key] = $program->program_key;
                }
                // sort the funding keys
                sort($fundingKeys);
                foreach ($fundingKeys as $key) {
                    if($key != $currentFundingKey){
                        $currentFundingKey = $key;
                    } else {
                        $duplicateFundingKey++;
                    }
                }
                


                

                if($duplicateFundingKey == 0 || $duplicateFundingKey > 0 ){
                    // REMOVE THE > 0 to check for duplicates again
                    // We resolved and adjusted the script to exclude projects with duplicates that cannot have units assigned based on funding key duplication.
                    // no duplicates within our group
                    // current funding keys is based on our programs we inspect.
                    // we need to check and make sure none of the programs we don't inspect
                    // have the same funding key as one of our programs... fun fun
                    foreach($project->all_other_programs as $program){
                        // put the funding keys into an array
                        $projectPrograms .= 'NOT INSPECTED:'.$program->program->program_name." - funding program key: {$program->program->funding_program_key} | award number: {$program->award_number}<br />";
                        $otherFundingKeys[] = $program->program->funding_program_key;
                    }


                    // now lets see if any of our other active programs have funding keys that match our inspected programs

                    if(count(array_intersect($otherFundingKeys, $fundingKeys)) == 0) {

                        $output .= $projectPrograms;
                        //no programs have duplicate funding keys - we are good to go on assumptions.
                        $apiConnect = new DevcoService();
                        $unitCount = 0;
                        $projectUnits = $this->output->createProgressBar(count($units));
                        $this->line(PHP_EOL);
                        foreach($units as $unit){
                            if($go == 1){
                                //$projectUnits->advance();
                                $this->line("UNIT KEY: {$unit->unit_key} || ");
                                $unitCount++;
                                // get the unit's programs based on funding keys (not reliable, but with the above test passed, we can work on the assumption this is accurate.)
                                $unitProgram = $apiConnect->getUnitPrograms($unit->unit_key, 1, 'admin@allita.org','SystemUser', 1, 'SystemServer');

                                $unitPrograms = json_decode($unitProgram, true);
                                $unitPrograms = $unitPrograms['data'];
                                //sleep(1);
                                if(is_array($unitPrograms) && count($unitPrograms) > 0){
                                    foreach($unitPrograms as $up){
                                        if(in_array($up['attributes']['fundingProgramKey'], $fundingKeys)){
                                            // we are skipping programs that are not active or not inspected.
                                            
                                            // need to double check that it is not possible that the funding id is unique to our inspected programs - that it is not on a program we don't inspect

                                            $programKey =  $programFundingKeyToProgramKey['key'.$up['attributes']['fundingProgramKey']];
                                            //get project program key

                                            $projectProgramKey = ProjectProgram::select('project_program_key')->where('project_id',$project->id)->where('program_key',$programKey)->first();

                                            
                                            //dd($unit,$up,$unitCount,$canRunCount,$programKey);
                                            // insert the record into the program unit table using the api
                                            $this->line(" DevelopmentProgramKey: {$projectProgramKey->project_program_key} || FundingProgramKey: {$up['attributes']['fundingProgramKey']}");
                                            $push = $apiConnect->putUnitProgram($unit->unit_key,$projectProgramKey->project_program_key,$up['attributes']['fundingProgramKey'],$up['attributes']['startDate'],$up['attributes']['endDate'], 1, 'admin@allita.org','SystemUser', 1, 'SystemServer'); 

                                            //sleep(1);


                                            
                                        }
                                        
                                    }
                                } else {
                                    $this->line('No Program Data To Update');
                                }
                            }
                            

                        }
                        $this->line(PHP_EOL);
                        $projectUnits->finish();
                    } else {
                        $cannotRun .='Project id:'.$project->id.' with devco reference '.$project->project_number.' (AKA: '.$project->project_name.') has '.count(array_intersect($otherFundingKeys, $fundingKeys)).' programs with duplicate funding keys that OVERLAP with our inspected programs - thus we cannot reliably assign programs to units.<br />'.$projectPrograms.'=======================================================================================';
                        $cannotRunCount++;

                    }
                } else {
                    $cannotRun .='Project id:'.$project->id.' with devco reference '.$project->project_number.' (AKA: '.$project->project_name.') has '.($duplicateFundingKey +1).' programs with duplicate funding keys<br />'.$projectPrograms.'=======================================================================================';
                    $cannotRunCount++;
                }

                $this->line($canRun.'=======================================================================================');
                $canRunCount++;
            }
            $this->line(PHP_EOL.'FINISHED '.$projectCount.'/'.count($projects).PHP_EOL);

        }

        $this->line('Total good to run '.$canRunCount.PHP_EOL.PHP_EOL.'Cannot Run:'.$cannotRunCount.'.PHP_EOL.PHP_EOL.'.$cannotRun);

    
    }
}
