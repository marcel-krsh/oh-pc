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
//use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Console\Scheduling\Schedule;
use Log;
use Event;
use Auth;
use App\Models\Audit;
use App\Models\Project;
use App\Models\ProjectProgram;
use App\Models\SyncProject;
use App\Jobs\CreateTestAuditJob;

use File;
use Storage;

class SyncController extends Controller
{
    public function __construct(){
        $this->allitapc();
    }
    public function testapi(Request $request) {
        
       //////////////////////////////////////////////////
        /////// Project Sync
        /////

        /// get last modified date inside the database
        /// PHP 7.3 will fix issue with PDO not recognizing the milisecond precision
        /// PHP 7.2X and lower instead drops the milisecond off.
        /// Most php apps operate at the precision of whole seconds. However Devco operates at a TimeStamp(3) precision.
        /// To get the full time stamp out of the Allita DB, we trick the query into thinking it is a string.
        /// To do this we use the DB::raw() function and use CONCAT on the column.
        /// We also need to select the column so we can order by it to get the newest first. So we apply an alias to the concated field.

        $lastModifiedDate = SyncProject::select(DB::raw("CONCAT(last_edited) as 'last_edited_convert'"), 'last_edited', 'id')->orderBy('last_edited', 'desc')->first();
        // if the value is null set a default start date to start the sync.
        if (is_null($lastModifiedDate)) {
            $modified = '10/1/1900';
        } else {
            // format date stored to the format we are looking for...
            // we resync the last second of the data to be sure we get any records that happened to be recorded at the same second.
            $currentModifiedDateTimeStamp = strtotime($lastModifiedDate->last_edited_convert);
            settype($currentModifiedDateTimeStamp, 'float');
            $currentModifiedDateTimeStamp = $currentModifiedDateTimeStamp - .001;
            $modified = date('m/d/Y G:i:s.u', $currentModifiedDateTimeStamp);
            //dd($lastModifiedDate, $modified);
        }
        $apiConnect = new DevcoService();
        if (!is_null($apiConnect)) {
            $syncData = $apiConnect->listDevelopments(1, $modified, 1, 'admin@allita.org', 'System Sync Job', 1, 'Server');
            $syncData = json_decode($syncData, true);
            $syncPage = 1;
            //dd($syncData);
            //dd($lastModifiedDate->last_edited_convert,$currentModifiedDateTimeStamp,$modified,$syncData);
            if ($syncData['meta']['totalPageCount'] > 0) {
                do {
                    if ($syncPage > 1) {
                        //Get Next Page
                        $syncData = $apiConnect->listDevelopments($syncPage, $modified, 1, 'admin@allita.org', 'System Sync Job', 1, 'Server');
                        $syncData = json_decode($syncData, true);
                        //dd('Page Count is Higher',$syncData);
                    }
                    foreach ($syncData['data'] as $i => $v) {
                            // check if record exists
                            $updateRecord = SyncProject::select('id', 'allita_id', 'last_edited', 'updated_at')->where('project_key', $v['attributes']['developmentKey'])->first();
                            // convert booleans
                            //settype($v['attributes']['isActive'], 'boolean');
                            //dd($updateRecord,$updateRecord->updated_at);
                        if (isset($updateRecord->id)) {
                            // record exists - get matching table record

                            /// NEW CODE TO UPDATE ALLITA TABLE PART 1
                            $allitaTableRecord = Project::find($updateRecord->allita_id);
                            /// END NEW CODE PART 1

                            // convert dates to seconds and miliseconds to see if the current record is newer.
                            $devcoDate = new DateTime($v['attributes']['lastEdited']);
                            $allitaDate = new DateTime($updateRecord->last_edited);
                            
                            $allitaFloat = ".".$allitaDate->format('u');
                            $devcoFloat = ".".$devcoDate->format('u');
                            settype($allitaFloat, 'float');
                            settype($devcoFloat, 'float');
                            $devcoDateEval = strtotime($devcoDate->format('Y-m-d G:i:s')) + $devcoFloat;
                            $allitaDateEval = strtotime($allitaDate->format('Y-m-d G:i:s')) + $allitaFloat;
                                
                            //dd($allitaTableRecord,$devcoDateEval,$allitaDateEval,$allitaTableRecord->last_edited, $updateRecord->updated_at);
                                
                            if ($devcoDateEval > $allitaDateEval) {
                                if (!is_null($allitaTableRecord) && $allitaTableRecord->last_edited <= $updateRecord->updated_at) {
                                    // record is newer than the one currently on file in the allita db.
                                    // update the sync table first
                                    SyncProject::where('id', $updateRecord['id'])
                                    ->update([
                                        'project_name'=>$v['attributes']['developmentName'],
                                        'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                        'default_phone_number_key'=>$v['attributes']['defaultPhoneNumberKey'],
                                        'default_fax_number_key'=>$v['attributes']['defaultFaxNumberKey'],
                                        'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                        'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                        'project_number'=>$v['attributes']['projectNumber'],
                                        'sample_size'=>$v['attributes']['sampleSize'],
                                            
                                        'last_edited'=>$v['attributes']['lastEdited'],
                                    ]);
                                    $UpdateAllitaValues = SyncProject::find($updateRecord['id']);
                                    // update the allita db - we use the updated at of the sync table as the last edited value for the actual Allita Table.
                                    $allitaTableRecord->update([
                                        'project_name'=>$v['attributes']['developmentName'],
                                        'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                        'default_phone_number_key'=>$v['attributes']['defaultPhoneNumberKey'],
                                        'default_fax_number_key'=>$v['attributes']['defaultFaxNumberKey'],
                                        'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                        'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                        'project_number'=>$v['attributes']['projectNumber'],
                                        'sample_size'=>$v['attributes']['sampleSize'],

                                        'last_edited'=>$UpdateAllitaValues->updated_at,
                                    ]);
                                    //dd('inside.');
                                } elseif (is_null($allitaTableRecord)) {
                                    // the allita table record doesn't exist
                                    // create the allita table record and then update the record
                                    // we create it first so we can ensure the correct updated at
                                    // date ends up in the allita table record
                                    // (if we create the sync record first the updated at date would become out of sync with the allita table.)

                                    $allitaTableRecord = Project::create([
                                        'project_name'=>$v['attributes']['developmentName'],
                                        'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                        'default_phone_number_key'=>$v['attributes']['defaultPhoneNumberKey'],
                                        'default_fax_number_key'=>$v['attributes']['defaultFaxNumberKey'],
                                        'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                        'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                        'project_number'=>$v['attributes']['projectNumber'],
                                        'sample_size'=>$v['attributes']['sampleSize'],
                                            
                                        'project_key'=>$v['attributes']['developmentKey'],
                                    ]);
                                    // Create the sync table entry with the allita id
                                    $syncTableRecord = SyncProject::where('id', $updateRecord['id'])
                                    ->update([
                                        'project_name'=>$v['attributes']['developmentName'],
                                        'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                        'default_phone_number_key'=>$v['attributes']['defaultPhoneNumberKey'],
                                        'default_fax_number_key'=>$v['attributes']['defaultFaxNumberKey'],
                                        'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                        'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                        'project_number'=>$v['attributes']['projectNumber'],
                                        'sample_size'=>$v['attributes']['sampleSize'],
                                            
                                        'project_key'=>$v['attributes']['developmentKey'],
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
                            $allitaTableRecord = Project::create([
                            'project_name'=>$v['attributes']['developmentName'],
                                    'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                    'default_phone_number_key'=>$v['attributes']['defaultPhoneNumberKey'],
                                    'default_fax_number_key'=>$v['attributes']['defaultFaxNumberKey'],
                                    'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                    'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                    'project_number'=>$v['attributes']['projectNumber'],
                                    'sample_size'=>$v['attributes']['sampleSize'],
                                    'project_key'=>$v['attributes']['developmentKey'],
                            ]);
                            // Create the sync table entry with the allita id
                            $syncTableRecord = SyncProject::create([
                                    'project_name'=>$v['attributes']['developmentName'],
                                    'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                    'default_phone_number_key'=>$v['attributes']['defaultPhoneNumberKey'],
                                    'default_fax_number_key'=>$v['attributes']['defaultFaxNumberKey'],
                                    'total_unit_count'=>$v['attributes']['totalUnitCount'],
                                    'total_building_count'=>$v['attributes']['totalBuildingCount'],
                                    'project_number'=>$v['attributes']['projectNumber'],
                                    'sample_size'=>$v['attributes']['sampleSize'],

                                'project_key'=>$v['attributes']['developmentKey'],
                                'last_edited'=>$v['attributes']['lastEdited'],
                                'allita_id'=>$allitaTableRecord->id,
                            ]);
                            // Update the Allita Table Record with the Sync Table's updated at date
                            $allitaTableRecord->update(['last_edited'=>$syncTableRecord->updated_at]);
                        }
                    }
                    $syncPage++;
                } while ($syncPage <= $syncData['meta']['totalPageCount']);
            }
        }

    }
    //
    public function getDocs(string $projectNumber, string $searchString = null, int $deviceId=0 , string $deviceName='System'){
        $apiConnect = new DevcoService();
        $documentList = $apiConnect->getProjectDocuments($projectNumber, $searchString, Auth::user()->id, Auth::user()->email, Auth::user()->name, $deviceId, $deviceName);
        dd($documentList,'Third doc id:'.$documentList->included[2]->id,'Page count:'.$documentList->meta->totalPageCount,'File type of third doc:'.$documentList->included[2]->attributes->fields->DWEXTENSION,'Document Class/Category:'.$documentList->included[2]->attributes->fields->DOCUMENTCLASS,'Userid passed:'. Auth::user()->id,'User email passed:'.Auth::user()->email,'Username Passed:'.Auth::user()->name,'Device id and Device name:'.$deviceId.','.$deviceName);

    }

    public function getDoc(int $documentId, int $deviceId=0 , string $deviceName='System'){
        $apiConnect = new DevcoService();
        $file = $apiConnect->getDocument($documentId, Auth::user()->id, Auth::user()->email, Auth::user()->name, $deviceId, $deviceName);
        //dd($stream);

        // Create filepath
        // $filepath = 'temp/'. $documentId . '.pdf';

        // Storage::put($filepath, File::get($file));
        // $file = Storage::get($filepath);
            
        //     header('Content-Description: File Transfer');
        //     header('Content-Type: application/octet-stream');
        //     header('Content-Disposition: attachment; filename='.$document->filename);
        //     header('Content-Transfer-Encoding: binary');
        //     header('Expires: 0');
        //     header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        //     header('Pragma: public');
        //     header('Content-Length: '. Storage::size($filepath));
            
        //     Storage::delete($filepath);
        //     return $file;

        // return response()->stream(function ($stream) {
        //       //Can add logic to chunk the file here if you want but the point is to stream data
        //       readfile($stream);
        //  },200, [ "Content-Type" => "application/pdf",  
        //            "Content-Disposition" => "attachment; filename=\"filename.pdf\"" 
        // ]);

    }

    public function associate($model, $lookUpModel, $associations)
    {
        foreach ($associations as $associate) {
            $updates = $model::select($associate['look_up_reference'])
                        ->whereNull($associate['null_field'])
                        ->where($associate['look_up_reference'], $associate['condition_operator'], $associate['condition'])
                        ->groupBy($associate['look_up_reference'])
                        //->toSQL();
                        ->get()->all();
            //dd($updates);
            foreach ($updates as $update) {
                //lookup model
                //dd($update,$update->{$associate['look_up_reference']});
                $key = $lookUpModel::select($associate['look_up_foreign_key'])
                ->where($associate['lookup_field'], $update->{$associate['look_up_reference']})
                ->first();
                if (!is_null($key)) {
                    $model::whereNull($associate['null_field'])
                        ->where(
                            $associate['look_up_reference'],
                            $update->{$associate['look_up_reference']}
                        )
                        ->update([
                                  $associate['null_field'] => $key->{$associate['look_up_foreign_key']}
                                                                    ]);
                } else {
                    //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->{$associate['look_up_reference']}.' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the model.');
                    Log::info(date('m/d/Y H:i:s ::', time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->{$associate['look_up_reference']}.' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the model.<hr />');
                }
            }
        }
    }

    public function sync(Request $request)
    {


        //Audit::where('audit_id',$request->get('development_key'))->update(['audit_status_id'=>4]);
        //TEST EVENT
        $testaudit = Audit::where('development_key', '=', $request->get('development_key'))->where('monitoring_status_type_key', '=', 4)->orderBy('start_date', 'desc')->first();
        CreateTestAuditJob::dispatch($testaudit)->onQueue('cache');
    }

    public function getApiRoute($model,$crud,$user='system',$userEmail='admin@allita.org',$userName='AllitaPC',$deviceId=1,$deviceName='AllitaPCServer',$metadata=array()) {
        $NOTAVARIABLE = 'REPLACE THIS VARIABLE IN THE CODE WITH REAL STUFF';
        /// create the connection 
        $apiConnect = new DevcoService();
        if (!is_null($apiConnect)) {
            switch ($crud) {
                case 'create':
                    switch ($model) {
                        case 'Amenity':
                            $apiMethod = 'addAmenity';
                            $syncData = $apiConnect->$apiMethod($metadata,$user,$userEmail,$userName,$deviceId,$deviceName);
                            $syncData = json_decode($syncData, true);
                            dd($syncData);
                            return $syncData['meta']['...Key'];
                            break;

                        case 'AuditAuditors':
                            $apiMethod = 'addMonitoringMonitor';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            return $syncData['meta']['...Key'];
                            break;

                        case 'ProjectAmenity':
                            $apiMethod = 'addProjectAmenity';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            return $syncData['meta']['...Key'];
                            break;

                        case 'UnitAmenity':
                            $apiMethod = 'addUnitAmenity';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            return $syncData['meta']['...Key'];
                            break;

                        case 'BuildingAmenity':
                            $apiMethod = 'addBuildingAmenity';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            return $syncData['meta']['...Key'];
                            break;

                        default:
                            # code...
                            break;
                    }
                    break;

                case 'update':
                    switch ($model){
                        case 'Address':
                            $apiMethod = 'updateAddress';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'Amenity':
                            //dd($model,$user,$userEmail,$userName,$deviceId,$deviceName,$metadata);
                            $apiMethod = 'updateAmenity';
                            // api reference:
                            //int $amenities_id, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null
                            // Amenity can update the description Only
                            $passingData['AmenityDescription']=$metadata['amenity_description'];
                            //
                            $syncData = $apiConnect->$apiMethod($metadata['amenity_type_key'], $passingData, $user, $userEmail, $userName, $deviceId, $deviceName);
                            $syncData = json_decode($syncData, true);
                            dd($syncData);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'Building':
                            $apiMethod = 'updateBuilding';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'BuildingStatus':
                            $apiMethod = 'updateBuildingStatus';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'ComplianceContact':
                            $apiMethod = 'updateComplianceContact';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'Project':
                            $apiMethod = 'updateDevelopment';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'ProjectAmenity':
                            $apiMethod = 'updateDevelopmentAmenity';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'ProjectDate':
                            $apiMethod = 'updateDevelopmentDate';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'ProjectProgram':
                            $apiMethod = 'updateDevelopmentProgram';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'Audit':
                            $apiMethod = 'updateAudit';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'Organization':
                            $apiMethod = 'updateOrganization';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'People':
                            $apiMethod = 'updatePerson';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'Unit':
                            $apiMethod = 'updateUnit';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'UnitStatus':
                            $apiMethod = 'updateUnitStatus';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'UnitAmenity':
                            $apiMethod = 'updateUnitAmenity';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'BuildingAmenity':
                            $apiMethod = 'updateBuildingAmenity';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;
                    }

                case 'delete':
                    case 'Amenity':
                            $apiMethod = 'addAmenity';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'AuditAuditors':
                            $apiMethod = 'addMonitoringMonitor';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'ProjectAmenity':
                            $apiMethod = 'addProjectAmenity';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'UnitAmenity':
                            $apiMethod = 'addUnitAmenity';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;

                        case 'BuildingAmenity':
                            $apiMethod = 'addBuildingAmenity';
                            $syncData = $apiConnect->$apiMethod($NOTAVARIABLE);
                            $syncData = json_decode($syncData, true);
                            if($syncData['meta']['...value..']){
                                return true;
                            } else {
                                return false;
                            }
                            break;
                
                default:
                    # code...
                    break;


            }
        } else {
            return 'Unable to connect to devco service to perform update.';
        }
    }

    public function crudDevco($model, $referenceId, $crud='update'){
        //////////////////////////////////////////////////
        /////// Amenity CRUD back to devco
       
        if(!is_null($model) && strtoupper($model) !== 'NULL'){
            if(!is_null($referenceId) && strtoupper($referenceId) !== 'NULL'){
                $originalModel = $model;
                $model = 'App\\Models\\'.$model;
                $model = $model::find($referenceId);
                if(is_null($model)){
                    return "Sync Devco: Supplied reference ".$referenceId." does not exist in the model ".$originalModel;
                }
                //dd($model);  
            } else {
                return "Sync Devco: No reference id specified.";
            }
            
        } else {
            return "Sync Devco: No reference model specified.";
            
        }
        if(Auth::check()){
            $user = Auth::user()->devco_key;
            $userEmail = Auth::user()->email;
            $userName = Auth::user()->name;

            $deviceName = session('deviceName');
            if(is_null($deviceName)){
                $deviceName = 'UserTriggeredEventOnServer';
            }
            $deviceId = session('deviceId');
            if(is_null($deviceId)){
                $deviceId='1';
            }
        } else {
            $user = NULL;
            $userEmail = NULL;
            $userName = NULL;
            $deviceName = NULL;
            $deviceId = NULL;
        }

        switch (strtolower($crud)) {
            case 'update':
                    // update devco using key
                    $metadata = $model->toArray();
                    $this->getApiRoute($originalModel,strtolower($crud),$user,$userEmail,$userName,$deviceId,$deviceName,$metadata);
                    // update the sync table key
                break;

            case 'create':
                    // add to devco
                    dd('create triggered');
                    // get key

                    // add to sync table
                break;
            
            case 'delete':
                    // delete from devco
                    dd('delete triggered');
                    // delete from sync table
                break;

            default:
                # code...
                dd('CRUD NOT AVAILABLE');
                break;
        }

    }

    public function brianTest(Request $request)
    {
        $test = \App\Models\Project::where('id', $request->get('project_id'))->first();
        ;

        dd('Project Model', 'projectProgramUnitCounts:<br />', $test, $test->projectProgramUnitCounts());
    }
}
