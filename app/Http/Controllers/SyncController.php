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

use App\Models\SyncProjectProgram;
use App\Models\ProjectProgram;



class SyncController extends Controller
{
    //
    public function sync() {
        //////////////////////////////////////////////////
        /////// ProjectProgram Sync
        /////

        /// get last modified date inside the database
        /// PHP 7.3 will fix issue with PDO not recognizing the milisecond precision
        /// PHP 7.2X and lower instead drops the milisecond off. 
        /// Most php apps operate at the precision of whole seconds. However Devco operates at a TimeStamp(3) precision.
        /// To get the full time stamp out of the Allita DB, we trick the query into thinking it is a string.
        /// To do this we use the DB::raw() function and use CONCAT on the column.
        /// We also need to select the column so we can order by it to get the newest first. So we apply an alias to the concated field.

        $lastModifiedDate = SyncProjectProgram::select(DB::raw("CONCAT(last_edited) as 'last_edited_convert'"),'last_edited','id')->orderBy('last_edited','desc')->first();
        // if the value is null set a default start date to start the sync.
        if(is_null($lastModifiedDate)) {
            $modified = '10/1/1900';
        }else{
            // format date stored to the format we are looking for...
            // we resync the last second of the data to be sure we get any records that happened to be recorded at the same second.
            $currentModifiedDateTimeStamp = strtotime($lastModifiedDate->last_edited_convert);
            settype($currentModifiedDateTimeStamp,'float');
            $currentModifiedDateTimeStamp = $currentModifiedDateTimeStamp - .001;
            $modified = date('m/d/Y G:i:s.u',$currentModifiedDateTimeStamp);
            //dd($lastModifiedDate, $modified);
        }
        $apiConnect = new DevcoService();
        if(!is_null($apiConnect)){
            $syncData = $apiConnect->listProjectPrograms(1, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
            $syncData = json_decode($syncData, true);
            $syncPage = 1;
            //dd($syncData);
            //dd($lastModifiedDate->last_edited_convert,$currentModifiedDateTimeStamp,$modified,$syncData);
            if($syncData['meta']['totalPageCount'] > 0){
                do{
                    if($syncPage > 1){
                        //Get Next Page
                        $syncData = $apiConnect->listProjectPrograms($syncPage, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
                        $syncData = json_decode($syncData, true);
                        //dd('Page Count is Higher',$syncData,$syncData['meta']['totalPageCount'],$syncPage);
                    }
                    //dd('Page Count is Higher',$syncData,$modified,$syncData,$syncData['meta']['totalPageCount'],$syncPage);
                    foreach($syncData['data'] as $i => $v)
                        {
                            // check if record exists
                            $updateRecord = SyncProjectProgram::select('id','allita_id','last_edited','updated_at')->where('project_program_key',$v['attributes']['developmentProgramKey'])->first();
                            // convert booleans
                             settype($v['attributes']['floatingUnits'], 'boolean');
                            // settype($v['attributes']['isProjectProgramHandicapAccessible'], 'boolean');

                            // Set dates older than 1950 to be NULL:
                            // if($v['attributes']['comment'] < 1951){
                            //     $v['attributes']['comment'] = NULL;
                            // }
                            // if($v['attributes']['completedDate'] < 1951){
                            //     $v['attributes']['completedDate'] = NULL;
                            // }
                            // if($v['attributes']['confirmedDate'] < 1951){
                            //     $v['attributes']['confirmedDate'] = NULL;
                            // }
                            // if($v['attributes']['onSiteMonitorEndDate'] < 1951){
                            //     $v['attributes']['onSiteMonitorEndDate'] = NULL;
                            // }
                            //dd($updateRecord,$updateRecord->updated_at);
                            if(isset($updateRecord->id)) {
                                // record exists - get matching table record

                                /// NEW CODE TO UPDATE ALLITA TABLE PART 1
                                $allitaTableRecord = ProjectProgram::find($updateRecord->allita_id);
                                /// END NEW CODE PART 1

                                // convert dates to seconds and miliseconds to see if the current record is newer.
                                $devcoDate = new DateTime($v['attributes']['lastEdited']);
                                $allitaDate = new DateTime($lastModifiedDate->last_edited_convert);
                                $allitaFloat = ".".$allitaDate->format('u');
                                $devcoFloat = ".".$devcoDate->format('u');
                                settype($allitaFloat,'float');
                                settype($devcoFloat, 'float');
                                $devcoDateEval = strtotime($devcoDate->format('Y-m-d G:i:s')) + $devcoFloat;
                                $allitaDateEval = strtotime($allitaDate->format('Y-m-d G:i:s')) + $allitaFloat;
                                
                                //dd($allitaTableRecord,$devcoDateEval,$allitaDateEval,$allitaTableRecord->last_edited, $updateRecord->updated_at);
                                
                                if($devcoDateEval > $allitaDateEval){
                                    if(!is_null($allitaTableRecord) && $allitaTableRecord->last_edited <= $updateRecord->updated_at){


                                        // record is newer than the one currently on file in the allita db.
                                        // update the sync table first
                                        SyncProjectProgram::where('id',$updateRecord['id'])
                                        ->update([
                                            
                                            
                                            
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            
                                            'program_key'=>$v['attributes']['programKey'],
                                            'development_program_status_type_key'=>$v['attributes']['developmentProgramStatusTypeKey'],
                                            'award_number'=>$v['attributes']['awardNumber'],
                                            'application_number'=>$v['attributes']['applicationNumber'],
                                            'assisted_units_anticipated'=>$v['attributes']['assistedUnitsAnticipated'],
                                            'assisted_units_actual'=>$v['attributes']['assistedUnitsActual'],
                                            'floating_units'=>$v['attributes']['floatingUnits'],
                                            'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                            'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                            'first_year_award_claimed'=>$v['attributes']['firstYearAwardClaimed'],
                                            'federal_minimum_set_aside_key'=>$v['attributes']['federalMinimumSetAsideKey'],
                                            'special_needs_units'=>$v['attributes']['special_needs_units'],
                                            'non_special_needs_units'=>$v['attributes']['non_special_needs_units'],
                                            'multiple_building_election_key'=>$v['attributes']['multipleBuildingElectionKey'],
                                            'employee_unit_count'=>$v['attributes']['employeeUnitCount'],
                                            'guide_l_year'=>$v['attributes']['guideLYear'],
                                            
                                            
                                            
                                            
                                            
                                            'last_edited'=>$v['attributes']['lastEdited'],
                                        ]);
                                        $UpdateAllitaValues = SyncProjectProgram::find($updateRecord['id']);
                                        // update the allita db - we use the updated at of the sync table as the last edited value for the actual Allita Table.
                                        $allitaTableRecord->update([
                                            
                                            
                                            
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            
                                            'program_key'=>$v['attributes']['programKey'],
                                            'development_program_status_type_key'=>$v['attributes']['developmentProgramStatusTypeKey'],
                                            'award_number'=>$v['attributes']['awardNumber'],
                                            'application_number'=>$v['attributes']['applicationNumber'],
                                            'assisted_units_anticipated'=>$v['attributes']['assistedUnitsAnticipated'],
                                            'assisted_units_actual'=>$v['attributes']['assistedUnitsActual'],
                                            'floating_units'=>$v['attributes']['floatingUnits'],
                                            'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                            'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                            'first_year_award_claimed'=>$v['attributes']['firstYearAwardClaimed'],
                                            'federal_minimum_set_aside_key'=>$v['attributes']['federalMinimumSetAsideKey'],
                                            'special_needs_units'=>$v['attributes']['special_needs_units'],
                                            'non_special_needs_units'=>$v['attributes']['non_special_needs_units'],
                                            'multiple_building_election_key'=>$v['attributes']['multipleBuildingElectionKey'],
                                            'employee_unit_count'=>$v['attributes']['employeeUnitCount'],
                                            'guide_l_year'=>$v['attributes']['guideLYear'],
                                            
                                            
                                            
                                            
                                            
                                            'last_edited'=>$UpdateAllitaValues->updated_at,
                                        ]);
                                        //dd('inside.');
                                    } elseIf(is_null($allitaTableRecord)){
                                        // the allita table record doesn't exist
                                        // create the allita table record and then update the record
                                        // we create it first so we can ensure the correct updated at 
                                        // date ends up in the allita table record
                                        // (if we create the sync record first the updated at date would become out of sync with the allita table.)

                                        $allitaTableRecord = ProjectProgram::create([
                                            
                                            
                                            
                                            
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            
                                            'program_key'=>$v['attributes']['programKey'],
                                            'development_program_status_type_key'=>$v['attributes']['developmentProgramStatusTypeKey'],
                                            'award_number'=>$v['attributes']['awardNumber'],
                                            'application_number'=>$v['attributes']['applicationNumber'],
                                            'assisted_units_anticipated'=>$v['attributes']['assistedUnitsAnticipated'],
                                            'assisted_units_actual'=>$v['attributes']['assistedUnitsActual'],
                                            'floating_units'=>$v['attributes']['floatingUnits'],
                                            'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                            'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                            'first_year_award_claimed'=>$v['attributes']['firstYearAwardClaimed'],
                                            'federal_minimum_set_aside_key'=>$v['attributes']['federalMinimumSetAsideKey'],
                                            'special_needs_units'=>$v['attributes']['special_needs_units'],
                                            'non_special_needs_units'=>$v['attributes']['non_special_needs_units'],
                                            'multiple_building_election_key'=>$v['attributes']['multipleBuildingElectionKey'],
                                            'employee_unit_count'=>$v['attributes']['employeeUnitCount'],
                                            'guide_l_year'=>$v['attributes']['guideLYear'],
                                            
                                            
                                            
                                            
                                            
                                            'project_program_key'=>$v['attributes']['developmentProgramKey'],
                                        ]);
                                        // Create the sync table entry with the allita id
                                        $syncTableRecord = SyncProjectProgram::where('id',$updateRecord['id'])
                                        ->update([
                                            
                                            
                                            
                                            
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            
                                            'program_key'=>$v['attributes']['programKey'],
                                            'development_program_status_type_key'=>$v['attributes']['developmentProgramStatusTypeKey'],
                                            'award_number'=>$v['attributes']['awardNumber'],
                                            'application_number'=>$v['attributes']['applicationNumber'],
                                            'assisted_units_anticipated'=>$v['attributes']['assistedUnitsAnticipated'],
                                            'assisted_units_actual'=>$v['attributes']['assistedUnitsActual'],
                                            'floating_units'=>$v['attributes']['floatingUnits'],
                                            'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                            'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                            'first_year_award_claimed'=>$v['attributes']['firstYearAwardClaimed'],
                                            'federal_minimum_set_aside_key'=>$v['attributes']['federalMinimumSetAsideKey'],
                                            'special_needs_units'=>$v['attributes']['special_needs_units'],
                                            'non_special_needs_units'=>$v['attributes']['non_special_needs_units'],
                                            'multiple_building_election_key'=>$v['attributes']['multipleBuildingElectionKey'],
                                            'employee_unit_count'=>$v['attributes']['employeeUnitCount'],
                                            'guide_l_year'=>$v['attributes']['guideLYear'],
                                            
                                            
                                            
                                            
                                            
                                            'project_program_key'=>$v['attributes']['developmentProgramKey'],
                                            'last_edited'=>$v['attributes']['lastEdited'],
                                            'allita_id'=>$allitaTableRecord->id,
                                        ]);                                     
                                        // Update the Allita Table Record with the Sync Table's updated at date
                                        $allitaTableRecord->update(['last_edited'=>$syncTableRecord->updated_at]);


                                    }
                                }

                                
                            } else {
                                // Create the Allita Entry First
                                // We do this so the updated_at value of the Sync Table does not become newer
                                // when we add in the allita_id
                                $allitaTableRecord = ProjectProgram::create([
                                    

                                            
                                            'project_program_key'=>$v['attributes']['developmentProgramKey'],
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            
                                            'program_key'=>$v['attributes']['programKey'],
                                            'development_program_status_type_key'=>$v['attributes']['developmentProgramStatusTypeKey'],
                                            'award_number'=>$v['attributes']['awardNumber'],
                                            'application_number'=>$v['attributes']['applicationNumber'],
                                            'assisted_units_anticipated'=>$v['attributes']['assistedUnitsAnticipated'],
                                            'assisted_units_actual'=>$v['attributes']['assistedUnitsActual'],
                                            'floating_units'=>$v['attributes']['floatingUnits'],
                                            'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                            'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                            'first_year_award_claimed'=>$v['attributes']['firstYearAwardClaimed'],
                                            'federal_minimum_set_aside_key'=>$v['attributes']['federalMinimumSetAsideKey'],
                                            'special_needs_units'=>$v['attributes']['special_needs_units'],
                                            'non_special_needs_units'=>$v['attributes']['non_special_needs_units'],
                                            'multiple_building_election_key'=>$v['attributes']['multipleBuildingElectionKey'],
                                            'employee_unit_count'=>$v['attributes']['employeeUnitCount'],
                                            'guide_l_year'=>$v['attributes']['guideLYear'],
                                            
                                            
                                            
                                            
                                    
                                    'project_program_key'=>$v['attributes']['developmentProgramKey'],
                                ]);
                                // Create the sync table entry with the allita id
                                $syncTableRecord = SyncProjectProgram::create([
                                            
                                            
                                            
                                            
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            
                                            'program_key'=>$v['attributes']['programKey'],
                                            'development_program_status_type_key'=>$v['attributes']['developmentProgramStatusTypeKey'],
                                            'award_number'=>$v['attributes']['awardNumber'],
                                            'application_number'=>$v['attributes']['applicationNumber'],
                                            'assisted_units_anticipated'=>$v['attributes']['assistedUnitsAnticipated'],
                                            'assisted_units_actual'=>$v['attributes']['assistedUnitsActual'],
                                            'floating_units'=>$v['attributes']['floatingUnits'],
                                            'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                            'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                            'first_year_award_claimed'=>$v['attributes']['firstYearAwardClaimed'],
                                            'federal_minimum_set_aside_key'=>$v['attributes']['federalMinimumSetAsideKey'],
                                            'special_needs_units'=>$v['attributes']['special_needs_units'],
                                            'non_special_needs_units'=>$v['attributes']['non_special_needs_units'],
                                            'multiple_building_election_key'=>$v['attributes']['multipleBuildingElectionKey'],
                                            'employee_unit_count'=>$v['attributes']['employeeUnitCount'],
                                            'guide_l_year'=>$v['attributes']['guideLYear'],
                                            
                                            
                                            
                                            

                                        'project_program_key'=>$v['attributes']['developmentProgramKey'],
                                        'last_edited'=>$v['attributes']['lastEdited'],
                                        'allita_id'=>$allitaTableRecord->id,
                                ]);
                                // Update the Allita Table Record with the Sync Table's updated at date
                                $allitaTableRecord->update(['last_edited'=>$syncTableRecord->updated_at]);


                            }

                        }
                    $syncPage++;
                }while($syncPage <= $syncData['meta']['totalPageCount']);
            }
        }
    }
}
