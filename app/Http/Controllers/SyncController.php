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

use App\Models\SyncProjectRole;
use App\Models\AllitaProjectRole;



class SyncController extends Controller
{
    //
    public function sync() {
        //////////////////////////////////////////////////
        /////// Project Roles Sync
        /////

        /// get last modified date inside the database
        /// PHP 7.3 will fix issue with PDO not recognizing the milisecond precision
        /// PHP 7.2X and lower instead drops the milisecond off. 
        /// Most php apps operate at the precision of whole seconds. However Devco operates at a TimeStamp(3) precision.
        /// To get the full time stamp out of the Allita DB, we trick the query into thinking it is a string.
        /// To do this we use the DB::raw() function and use CONCAT on the column.
        /// We also need to select the column so we can order by it to get the newest first. So we apply an alias to the concated field.

        $lastModifiedDate = SyncProjectRole::select(DB::raw("CONCAT(last_edited) as 'last_edited_convert'"),'last_edited')->orderBy('last_edited','desc')->first();
        // if the value is null set a default start date to start the sync.
        if(is_null($lastModifiedDate)) {
            $modified = '10/1/1900';
        }else{
            // format date stored to the format we are looking for...
            // we resync the last second of the data to be sure we get any records that happened to be recorded at the same second.
            $currentModifiedDateTimeStamp = strtotime($lastModifiedDate->last_edited_convert);
            settype($currentModifiedDateTimeStamp,'float');
            $currentModifiedDateTimeStamp = $currentModifiedDateTimeStamp - .001;
            $modified = date('m/d/Y g:i:s.u a',$currentModifiedDateTimeStamp);
        }
        $apiConnect = new DevcoService();
        if(!is_null($apiConnect)){
            $syncData = $apiConnect->listDevelopmentRoles(1, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
            $syncData = json_decode($syncData, true);
            $syncPage = 1;
            //dd($syncData);
            //dd($lastModifiedDate->last_edited_convert,$currentModifiedDateTimeStamp1,$currentModifiedDateTimeStamp2,$modified,$syncData);
            if($syncData['meta']['totalPageCount'] > 0){
                do{
                    if($syncPage > 1){
                        //Get Next Page
                        $syncData = $apiConnect->listDevelopmentRoles($syncPage, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
                        $syncData = json_decode($syncData, true);
                    }
                    foreach($syncData['data'] as $i => $v)
                        {
                            // check if record exists
                            $updateRecord = SyncProjectRole::select('id','allita_id','last_edited')->where('project_role_key',$v['attributes']['developmentRoleKey'])->first();
                            

                            if(isset($updateRecord->id)) {
                                // record exists - get matching table record

                                /// NEW CODE TO UPDATE ALLITA TABLE PART 1
                                $allitaTableRecord = AllitaProjectRole::find($updateRecord->$allita_id);
                                /// END NEW CODE PART 1

                                // convert dates to seconds and miliseconds to see if the current record is newer.
                                $devcoDate = new DateTime($v['attributes']['lastEdited']);
                                $allitaDate = new DateTime($lastModifiedDate->last_edited_convert);
                                $allitaFloat = ".".$allitaDate->format('u');
                                $devcoFloat = ".".$devcoDate->format('u');
                                settype($allitaFloat,'float');
                                settype($devcoFloat, 'float');
                                $devcoDateEval = strtotime($devcoDate->format('Y-m-d H:i:s')) + $devcoFloat;
                                $allitaDateEval = strtotime($allitaDate->format('Y-m-d H:i:s')) + $allitaFloat;
                                

                                if($devcoDateEval > $allitaDateEval){
                                    if(!is_null($allitaTableRecord) && $allitaTableRecord->last_edited <= $updateRecord->updated_at){
                                        // record is newer than the one currently on file in the allita db.
                                        // update the sync table first
                                        $UpdateAllitaValues = SyncProjectRole::where('id',$updateRecord['id'])
                                        ->update([
                                        'role_name'=>$v['attributes']['roleName'],
                                        'last_edited'=>$v['attributes']['lastEdited'],
                                        ]);
                                        // update the allita db - we use the updated at of the sync table as the last edited value for the actual Allita Table.
                                        $allitaTableRecord->update([
                                            'role_name'=>$v['attributes']['roleName'],
                                            'last_edited'=>$UpdateAllitaValues->updated_at,
                                        ]);
                                    } elseIf(is_null($allitaTableRecord)){
                                        // the allita table record doesn't exist
                                        // create the allita table record and then update the record
                                        // we create it first so we can ensure the correct updated at 
                                        // date ends up in the allita table record
                                        // (if we create the sync record first the updated at date would become out of sync with the allita table.)

                                        $allitaTableRecord = AllitaProjectRole::create([
                                            'project_role_key'=>$v['attributes']['developmentRoleKey'],
                                            'role_name'=>$v['attributes']['roleName'],
                                        ]);
                                        // Create the sync table entry with the allita id
                                        $syncTableRecord = SyncProjectRole::::where('id',$updateRecord['id'])
                                        ->update([
                                        'role_name'=>$v['attributes']['roleName'],
                                        'last_edited'=>$v['attributes']['lastEdited'],
                                        ]);                                     
                                        // Update the Allita Table Record with the Sync Table's updated at date
                                        $allitaTableRecord->update(['last_edited'=>$syncTableRecord->updated_at]);


                                    }
                                }

                                
                            } else {
                                // Create the Allita Entry First
                                // We do this so the updated_at value of the Sync Table does not become newer
                                // when we add in the allita_id
                                $allitaTableRecord = AllitaProjectRole::create([
                                    'project_role_key'=>$v['attributes']['developmentRoleKey'],
                                    'role_name'=>$v['attributes']['roleName'],
                                ]);
                                // Create the sync table entry with the allita id
                                $syncTableRecord = SyncProjectRole::create([
                                    'project_role_key'=>$v['attributes']['developmentRoleKey'],
                                    'role_name'=>$v['attributes']['roleName'],
                                    'allita_id'=>$allitaTableRecord->id,
                                    'last_edited'=>$v['attributes']['lastEdited'],
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
