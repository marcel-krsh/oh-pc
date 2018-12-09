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

use App\Models\SyncOwnerCertificationYear;
use App\Models\OwnerCertificationYear;



class SyncController extends Controller
{
    //
    public function sync() {
        //////////////////////////////////////////////////
        /////// OwnerCertificationYear Sync
        /////

        /// get last modified date inside the database
        /// PHP 7.3 will fix issue with PDO not recognizing the milisecond precision
        /// PHP 7.2X and lower instead drops the milisecond off. 
        /// Most php apps operate at the precision of whole seconds. However Devco operates at a TimeStamp(3) precision.
        /// To get the full time stamp out of the Allita DB, we trick the query into thinking it is a string.
        /// To do this we use the DB::raw() function and use CONCAT on the column.
        /// We also need to select the column so we can order by it to get the newest first. So we apply an alias to the concated field.

        $lastModifiedDate = SyncOwnerCertificationYear::select(DB::raw("CONCAT(last_edited) as 'last_edited_convert'"),'last_edited','id')->orderBy('last_edited','desc')->first();
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
            $syncData = $apiConnect->listOwnerCertificationYears(1, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
            $syncData = json_decode($syncData, true);
            $syncPage = 1;
            dd($syncData);
            //dd($lastModifiedDate->last_edited_convert,$currentModifiedDateTimeStamp,$modified,$syncData);
            if($syncData['meta']['totalPageCount'] > 0){
                do{
                    if($syncPage > 1){
                        //Get Next Page
                        $syncData = $apiConnect->listOwnerCertificationYears($syncPage, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
                        $syncData = json_decode($syncData, true);
                        //dd('Page Count is Higher',$syncData,$syncData['meta']['totalPageCount'],$syncPage);
                    }
                    //dd('Page Count is Higher',$syncData,$modified,$syncData,$syncData['meta']['totalPageCount'],$syncPage);
                    foreach($syncData['data'] as $i => $v)
                        {
                            // check if record exists
                            $updateRecord = SyncOwnerCertificationYear::select('id','allita_id','last_edited','updated_at')->where('house_hold_event_key',$v['attributes']['houseHoldEventKey'])->first();
                            // convert booleans
                            // settype($v['attributes']['isActive'], 'boolean');
                            // settype($v['attributes']['isOwnerCertificationYearHandicapAccessible'], 'boolean');
                            //dd($updateRecord,$updateRecord->updated_at);
                            if(isset($updateRecord->id)) {
                                // record exists - get matching table record

                                /// NEW CODE TO UPDATE ALLITA TABLE PART 1
                                $allitaTableRecord = OwnerCertificationYear::find($updateRecord->allita_id);
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
                                        SyncOwnerCertificationYear::where('id',$updateRecord['id'])
                                        ->update([
                                            
                                            
                                            'owner_certification_year_key'=>$v['attributes']['ownerCertificationYearKey'],
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            'house_hold_key'=>$v['attributes']['houseHoldKey'],
                                            'unit_key'=>$v['attributes']['unitKey'],
                                            'event_date'=>$v['attributes']['eventDate'],
                                            'event_type_key'=>$v['attributes']['eventTypeKey'],
                                            'unit_status_key'=>$v['attributes']['unitStatusKey'],
                                            'current_income'=>$v['attributes']['currentIncome'],
                                            'rent_level_key'=>$v['attributes']['rentLevelKey'],
                                            'income_level_key'=>$v['attributes']['incomeLevelKey'],
                                            'tenant_rent_portion'=>$v['attributes']['tenantRentPortion'],
                                            'rental_assistance_amount'=>$v['attributes']['rentalAssistanceAmount'],
                                            'utility_allowance'=>$v['attributes']['utilityAllowance'],
                                            'household_count'=>$v['attributes']['householdCount'],
                                            'student_count'=>$v['attributes']['studentCount'],
                                            'all_student_house'=>$v['attributes']['allStudentHouse'],
                                            'utility_allowance_key'=>$v['attributes']['utilityAllowanceKey'],
                                            'rental_assistance_type_key'=>$v['attributes']['rentalAssistanceTypeKey'],
                                            'rental_assistance_source_key'=>$v['attributes']['rentalAssistanceSourceKey'],
                                            'notes'=>$v['attributes']['notes'],
                                            'unit_identity_key'=>$v['attributes']['unitIdentityKey'],
                                            'certification_date'=>$v['attributes']['certificationDate'],
                                            
                                            
                                            'last_edited'=>$v['attributes']['lastEdited'],
                                        ]);
                                        $UpdateAllitaValues = SyncOwnerCertificationYear::find($updateRecord['id']);
                                        // update the allita db - we use the updated at of the sync table as the last edited value for the actual Allita Table.
                                        $allitaTableRecord->update([
                                            
                                            
                                            'owner_certification_year_key'=>$v['attributes']['ownerCertificationYearKey'],
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            'house_hold_key'=>$v['attributes']['houseHoldKey'],
                                            'unit_key'=>$v['attributes']['unitKey'],
                                            'event_date'=>$v['attributes']['eventDate'],
                                            'event_type_key'=>$v['attributes']['eventTypeKey'],
                                            'unit_status_key'=>$v['attributes']['unitStatusKey'],
                                            'current_income'=>$v['attributes']['currentIncome'],
                                            'rent_level_key'=>$v['attributes']['rentLevelKey'],
                                            'income_level_key'=>$v['attributes']['incomeLevelKey'],
                                            'tenant_rent_portion'=>$v['attributes']['tenantRentPortion'],
                                            'rental_assistance_amount'=>$v['attributes']['rentalAssistanceAmount'],
                                            'utility_allowance'=>$v['attributes']['utilityAllowance'],
                                            'household_count'=>$v['attributes']['householdCount'],
                                            'student_count'=>$v['attributes']['studentCount'],
                                            'all_student_house'=>$v['attributes']['allStudentHouse'],
                                            'utility_allowance_key'=>$v['attributes']['utilityAllowanceKey'],
                                            'rental_assistance_type_key'=>$v['attributes']['rentalAssistanceTypeKey'],
                                            'rental_assistance_source_key'=>$v['attributes']['rentalAssistanceSourceKey'],
                                            'notes'=>$v['attributes']['notes'],
                                            'unit_identity_key'=>$v['attributes']['unitIdentityKey'],
                                            'certification_date'=>$v['attributes']['certificationDate'],
                                            
                                            
                                            'last_edited'=>$UpdateAllitaValues->updated_at,
                                        ]);
                                        //dd('inside.');
                                    } elseIf(is_null($allitaTableRecord)){
                                        // the allita table record doesn't exist
                                        // create the allita table record and then update the record
                                        // we create it first so we can ensure the correct updated at 
                                        // date ends up in the allita table record
                                        // (if we create the sync record first the updated at date would become out of sync with the allita table.)

                                        $allitaTableRecord = OwnerCertificationYear::create([
                                            
                                            
                                            
                                            'owner_certification_year_key'=>$v['attributes']['ownerCertificationYearKey'],
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            'house_hold_key'=>$v['attributes']['houseHoldKey'],
                                            'unit_key'=>$v['attributes']['unitKey'],
                                            'event_date'=>$v['attributes']['eventDate'],
                                            'event_type_key'=>$v['attributes']['eventTypeKey'],
                                            'unit_status_key'=>$v['attributes']['unitStatusKey'],
                                            'current_income'=>$v['attributes']['currentIncome'],
                                            'rent_level_key'=>$v['attributes']['rentLevelKey'],
                                            'income_level_key'=>$v['attributes']['incomeLevelKey'],
                                            'tenant_rent_portion'=>$v['attributes']['tenantRentPortion'],
                                            'rental_assistance_amount'=>$v['attributes']['rentalAssistanceAmount'],
                                            'utility_allowance'=>$v['attributes']['utilityAllowance'],
                                            'household_count'=>$v['attributes']['householdCount'],
                                            'student_count'=>$v['attributes']['studentCount'],
                                            'all_student_house'=>$v['attributes']['allStudentHouse'],
                                            'utility_allowance_key'=>$v['attributes']['utilityAllowanceKey'],
                                            'rental_assistance_type_key'=>$v['attributes']['rentalAssistanceTypeKey'],
                                            'rental_assistance_source_key'=>$v['attributes']['rentalAssistanceSourceKey'],
                                            'notes'=>$v['attributes']['notes'],
                                            'unit_identity_key'=>$v['attributes']['unitIdentityKey'],
                                            'certification_date'=>$v['attributes']['certificationDate'],
                                            
                                            
                                            'house_hold_event_key'=>$v['attributes']['houseHoldEventKey'],
                                        ]);
                                        // Create the sync table entry with the allita id
                                        $syncTableRecord = SyncOwnerCertificationYear::where('id',$updateRecord['id'])
                                        ->update([
                                            
                                            
                                            
                                            'owner_certification_year_key'=>$v['attributes']['ownerCertificationYearKey'],
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            'house_hold_key'=>$v['attributes']['houseHoldKey'],
                                            'unit_key'=>$v['attributes']['unitKey'],
                                            'event_date'=>$v['attributes']['eventDate'],
                                            'event_type_key'=>$v['attributes']['eventTypeKey'],
                                            'unit_status_key'=>$v['attributes']['unitStatusKey'],
                                            'current_income'=>$v['attributes']['currentIncome'],
                                            'rent_level_key'=>$v['attributes']['rentLevelKey'],
                                            'income_level_key'=>$v['attributes']['incomeLevelKey'],
                                            'tenant_rent_portion'=>$v['attributes']['tenantRentPortion'],
                                            'rental_assistance_amount'=>$v['attributes']['rentalAssistanceAmount'],
                                            'utility_allowance'=>$v['attributes']['utilityAllowance'],
                                            'household_count'=>$v['attributes']['householdCount'],
                                            'student_count'=>$v['attributes']['studentCount'],
                                            'all_student_house'=>$v['attributes']['allStudentHouse'],
                                            'utility_allowance_key'=>$v['attributes']['utilityAllowanceKey'],
                                            'rental_assistance_type_key'=>$v['attributes']['rentalAssistanceTypeKey'],
                                            'rental_assistance_source_key'=>$v['attributes']['rentalAssistanceSourceKey'],
                                            'notes'=>$v['attributes']['notes'],
                                            'unit_identity_key'=>$v['attributes']['unitIdentityKey'],
                                            'certification_date'=>$v['attributes']['certificationDate'],
                                            
                                            
                                            'house_hold_event_key'=>$v['attributes']['houseHoldEventKey'],
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
                                $allitaTableRecord = OwnerCertificationYear::create([
                                    

                                            'house_hold_event_key'=>$v['attributes']['houseHoldEventKey'],
                                            'owner_certification_year_key'=>$v['attributes']['ownerCertificationYearKey'],
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            'house_hold_key'=>$v['attributes']['houseHoldKey'],
                                            'unit_key'=>$v['attributes']['unitKey'],
                                            'event_date'=>$v['attributes']['eventDate'],
                                            'event_type_key'=>$v['attributes']['eventTypeKey'],
                                            'unit_status_key'=>$v['attributes']['unitStatusKey'],
                                            'current_income'=>$v['attributes']['currentIncome'],
                                            'rent_level_key'=>$v['attributes']['rentLevelKey'],
                                            'income_level_key'=>$v['attributes']['incomeLevelKey'],
                                            'tenant_rent_portion'=>$v['attributes']['tenantRentPortion'],
                                            'rental_assistance_amount'=>$v['attributes']['rentalAssistanceAmount'],
                                            'utility_allowance'=>$v['attributes']['utilityAllowance'],
                                            'household_count'=>$v['attributes']['householdCount'],
                                            'student_count'=>$v['attributes']['studentCount'],
                                            'all_student_house'=>$v['attributes']['allStudentHouse'],
                                            'utility_allowance_key'=>$v['attributes']['utilityAllowanceKey'],
                                            'rental_assistance_type_key'=>$v['attributes']['rentalAssistanceTypeKey'],
                                            'rental_assistance_source_key'=>$v['attributes']['rentalAssistanceSourceKey'],
                                            'notes'=>$v['attributes']['notes'],
                                            'unit_identity_key'=>$v['attributes']['unitIdentityKey'],
                                            'certification_date'=>$v['attributes']['certificationDate'],
                                            
                                    
                                    'house_hold_event_key'=>$v['attributes']['houseHoldEventKey'],
                                ]);
                                // Create the sync table entry with the allita id
                                $syncTableRecord = SyncOwnerCertificationYear::create([
                                            
                                            
                                            'house_hold_event_key'=>$v['attributes']['houseHoldEventKey'],
                                            'owner_certification_year_key'=>$v['attributes']['ownerCertificationYearKey'],
                                            'project_key'=>$v['attributes']['developmentKey'],
                                            'house_hold_key'=>$v['attributes']['houseHoldKey'],
                                            'unit_key'=>$v['attributes']['unitKey'],
                                            'event_date'=>$v['attributes']['eventDate'],
                                            'event_type_key'=>$v['attributes']['eventTypeKey'],
                                            'unit_status_key'=>$v['attributes']['unitStatusKey'],
                                            'current_income'=>$v['attributes']['currentIncome'],
                                            'rent_level_key'=>$v['attributes']['rentLevelKey'],
                                            'income_level_key'=>$v['attributes']['incomeLevelKey'],
                                            'tenant_rent_portion'=>$v['attributes']['tenantRentPortion'],
                                            'rental_assistance_amount'=>$v['attributes']['rentalAssistanceAmount'],
                                            'utility_allowance'=>$v['attributes']['utilityAllowance'],
                                            'household_count'=>$v['attributes']['householdCount'],
                                            'student_count'=>$v['attributes']['studentCount'],
                                            'all_student_house'=>$v['attributes']['allStudentHouse'],
                                            'utility_allowance_key'=>$v['attributes']['utilityAllowanceKey'],
                                            'rental_assistance_type_key'=>$v['attributes']['rentalAssistanceTypeKey'],
                                            'rental_assistance_source_key'=>$v['attributes']['rentalAssistanceSourceKey'],
                                            'notes'=>$v['attributes']['notes'],
                                            'unit_identity_key'=>$v['attributes']['unitIdentityKey'],
                                            'certification_date'=>$v['attributes']['certificationDate'],
                                            

                                        'house_hold_event_key'=>$v['attributes']['houseHoldEventKey'],
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
