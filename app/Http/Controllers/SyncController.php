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

use App\Models\SyncMonitoringStatusTypes;




class SyncController extends Controller
{
    //
    public function sync() {
        //////////////////////////////////////////////////
        /////// MonitoringStatusTypes Sync
        /////

        /// get last modified date inside the database
        /// PHP 7.3 will fix issue with PDO not recognizing the milisecond precision
        /// PHP 7.2X and lower instead drops the milisecond off. 
        /// Most php apps operate at the precision of whole seconds. However Devco operates at a TimeStamp(3) precision.
        /// To get the full time stamp out of the Allita DB, we trick the query into thinking it is a string.
        /// To do this we use the DB::raw() function and use CONCAT on the column.
        /// We also need to select the column so we can order by it to get the newest first. So we apply an alias to the concated field.

        $lastModifiedDate = SyncMonitoringStatusTypes::select(DB::raw("CONCAT(last_edited) as 'last_edited_convert'"),'last_edited')->orderBy('last_edited','desc')->first();
        // if the value is null set a default start date to start the sync.
        if(is_null($lastModifiedDate)) {
            $modified = '10/1/1900';
        }else{
            // format date stored to the format we are looking for...
            // we resync the last second of the data to be sure we get any records that happened to be recorded at the same second.
            $modified = date('m/d/YTg:i:ua',(strtotime($lastModifiedDate->last_edited_convert)-1));
        }
        $apiConnect = new DevcoService();
        if(!is_null($apiConnect)){
            $syncData = $apiConnect->listAddresses(1, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
            $syncData = json_decode($syncData, true);
            $syncPage = 1;
            dd($modified,$syncData);
            if($syncData['meta']['totalPageCount'] > 0){
                do{
                    if($syncPage > 1){
                        //Get Next Page
                        $syncData = $apiConnect->listAddresses($syncPage, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
                        $syncData = json_decode($syncData, true);
                    }
                    foreach($syncData['data'] as $i => $v)
                        {
                            // check if record exists
                            $updateRecord = SyncMonitoringStatusTypes::select('id')->where('monitoring_status_type_key',$v['attributes']['monitoringStatusTypeKey'])->first();

                            if(isset($updateRecord->id)) {
                                // record exists - update it.

                                // convert dates to seconds and miliseconds to see if the current record is newer.
                                $devcoDate = new DateTime($v['attributes']['lastEdited']);
                                $allitaDate = new DateTime($lastModifiedDate->last_edited_convert);
                                $allitaFloat = ".".$allitaDate->format('u');
                                $devcoFloat = ".".$devcoDate->format('u');
                                settype($allitaFloat,'float');
                                settype($devcoFloat, 'float');
                                $devcoDateEval = strtotime($devcoDate->format('Y-m-d H:i:s')) + $devcoFloat;
                                $allitaDateEval = strtotime($allitaDate->format('Y-m-d H:i:s')) + $allitaFloat;
                                //dd($devcoDate->format('Y-m-d H:i:s'),$devcoFloat,$devcoDateEval,$allitaDate->format('Y-m-d H:i:s'),$allitaFloat,$allitaDateEval);

                                if($devcoDateEval > $allitaDateEval){
                                    // record is newer than the one currently on file
                                    SyncMonitoringStatusTypes::where('id',$updateRecord['id'])
                                    ->update([
                                    'monitoring_status_description'=>$v['attributes']['monitoringStatusDescription'],
                                    'last_edited'=>$v['attributes']['lastEdited'],
                                    ]);
                                }
                            } else {
                                SyncMonitoringStatusTypes::create([
                                    'monitoring_status_type_key'=>$v['attributes']['monitoringStatusTypeKey'],
                                'monitoring_status_description'=>$v['attributes']['monitoringStatusDescription'],
                                'last_edited'=>$v['attributes']['lastEdited'],
                                ]);
                            }

                        }
                    $syncPage++;
                }while($syncPage < $syncData['meta']['totalPageCount']);
            }
        }	
    }
}
