<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
use App\Models\User;
use DB;
use DateTime;
use Illuminate\Support\Facades\Hash;

use App\Models\SyncMonitoring;
use App\Models\Audit;

class SyncMonitoringsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public $tries = 5;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //////////////////////////////////////////////////
        /////// Audit Sync
        /////

        /// get last modified date inside the database
        /// PHP 7.3 will fix issue with PDO not recognizing the milisecond precision
        /// PHP 7.2X and lower instead drops the milisecond off.
        /// Most php apps operate at the precision of whole seconds. However Devco operates at a TimeStamp(3) precision.
        /// To get the full time stamp out of the Allita DB, we trick the query into thinking it is a string.
        /// To do this we use the DB::raw() function and use CONCAT on the column.
        /// We also need to select the column so we can order by it to get the newest first. So we apply an alias to the concated field.

        $lastModifiedDate = SyncMonitoring::select(DB::raw("CONCAT(last_edited) as 'last_edited_convert'"), 'last_edited', 'id')->orderBy('last_edited', 'desc')->first();
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
            $syncData = $apiConnect->listMonitorings(1, $modified, 1, 'admin@allita.org', 'System Sync Job', 1, 'Server');
            $syncData = json_decode($syncData, true);
            $syncPage = 1;
            //dd($syncData);
            //dd($lastModifiedDate->last_edited_convert,$currentModifiedDateTimeStamp,$modified,$syncData);
            if ($syncData['meta']['totalPageCount'] > 0) {
                do {
                    if ($syncPage > 1) {
                        //Get Next Page
                        $syncData = $apiConnect->listMonitorings($syncPage, $modified, 1, 'admin@allita.org', 'System Sync Job', 1, 'Server');
                        $syncData = json_decode($syncData, true);
                        //dd('Page Count is Higher',$syncData,$syncData['meta']['totalPageCount'],$syncPage);
                    }
                    //dd('Page Count is Higher',$syncData,$modified,$syncData,$syncData['meta']['totalPageCount'],$syncPage);
                    foreach ($syncData['data'] as $i => $v) {
                            // check if record exists
                            $updateRecord = SyncMonitoring::select('id', 'allita_id', 'last_edited', 'updated_at')->where('monitoring_key', $v['attributes']['monitoringKey'])->first();
                            // convert booleans
                            // settype($v['attributes']['isActive'], 'boolean');
                            // settype($v['attributes']['isAuditHandicapAccessible'], 'boolean');

                            // Set dates older than 1950 to be NULL:
                        if ($v['attributes']['startDate'] < 1951) {
                            $v['attributes']['startDate'] = null;
                        }
                        if ($v['attributes']['completedDate'] < 1951) {
                            $v['attributes']['completedDate'] = null;
                        }
                        if ($v['attributes']['confirmedDate'] < 1951) {
                            $v['attributes']['confirmedDate'] = null;
                        }
                        if ($v['attributes']['onSiteMonitorEndDate'] < 1951) {
                            $v['attributes']['onSiteMonitorEndDate'] = null;
                        }
                            //dd($updateRecord,$updateRecord->updated_at);
                        if (isset($updateRecord->id)) {
                            // record exists - get matching table record

                            /// NEW CODE TO UPDATE ALLITA TABLE PART 1
                            $allitaTableRecord = Audit::find($updateRecord->allita_id);
                            /// END NEW CODE PART 1

                            // convert dates to seconds and miliseconds to see if the current record is newer.
                            $devcoDate = new DateTime($v['attributes']['lastEdited']);
                            $allitaDate = new DateTime($lastModifiedDate->last_edited_convert);
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
                                    SyncMonitoring::where('id', $updateRecord['id'])
                                    ->update([
                                            
                                            
                                            
                                        'development_key'=>$v['attributes']['developmentKey'],
                                            
                                        'development_program_key'=>$v['attributes']['developmentProgramKey'],
                                        'monitoring_type_key'=>$v['attributes']['monitoringTypeKey'],
                                        'start_date'=>$v['attributes']['startDate'],
                                        'completed_date'=>$v['attributes']['completedDate'],
                                        'contact_person_key'=>$v['attributes']['contactPersonKey'],
                                        'contact_title'=>$v['attributes']['contactTitle'],
                                        'confirmed_date'=>$v['attributes']['confirmedDate'],
                                        'monitoring_status_type_key'=>$v['attributes']['monitoringStatusTypeKey'],
                                        'comment'=>$v['attributes']['comment'],
                                        'entered_by_user_key'=>$v['attributes']['enteredByUserKey'],
                                        'user_key'=>$v['attributes']['userKey'],
                                        'on_site_monitor_end_date'=>$v['attributes']['onSiteMonitorEndDate'],
                                        'status_results'=>$v['attributes']['statusResults'],
                                            
                                            
                                            
                                            
                                        'last_edited'=>$v['attributes']['lastEdited'],
                                    ]);
                                    $UpdateAllitaValues = SyncMonitoring::find($updateRecord['id']);
                                    // update the allita db - we use the updated at of the sync table as the last edited value for the actual Allita Table.
                                    $allitaTableRecord->update([
                                            
                                            
                                            
                                        'development_key'=>$v['attributes']['developmentKey'],
                                            
                                        'development_program_key'=>$v['attributes']['developmentProgramKey'],
                                        'project_program_id'=>null,
                                        'monitoring_type_key'=>$v['attributes']['monitoringTypeKey'],
                                        'monditoring_type_id'=>null,
                                        'start_date'=>$v['attributes']['startDate'],
                                        'completed_date'=>$v['attributes']['completedDate'],
                                        'contact_person_key'=>$v['attributes']['contactPersonKey'],
                                        'person_id'=>null,
                                        'contact_title'=>$v['attributes']['contactTitle'],
                                        'confirmed_date'=>$v['attributes']['confirmedDate'],
                                        'monitoring_status_type_key'=>$v['attributes']['monitoringStatusTypeKey'],

                                        'comment'=>$v['attributes']['comment'],
                                        'entered_by_user_key'=>$v['attributes']['enteredByUserKey'],
                                        'entered_by_user_id'=>null,
                                        'user_key'=>$v['attributes']['userKey'],
                                        'user_id'=>null,
                                        'on_site_monitor_end_date'=>$v['attributes']['onSiteMonitorEndDate'],
                                        'status_results'=>$v['attributes']['statusResults'],
                                            
                                        'last_edited'=>$UpdateAllitaValues->updated_at,
                                    ]);
                                    //dd('inside.');
                                } elseif (is_null($allitaTableRecord)) {
                                    // the allita table record doesn't exist
                                    // create the allita table record and then update the record
                                    // we create it first so we can ensure the correct updated at
                                    // date ends up in the allita table record
                                    // (if we create the sync record first the updated at date would become out of sync with the allita table.)

                                    $allitaTableRecord = Audit::create([
                                            
                                            
                                            
                                            
                                        'development_key'=>$v['attributes']['developmentKey'],
                                            
                                        'development_program_key'=>$v['attributes']['developmentProgramKey'],
                                        'monitoring_type_key'=>$v['attributes']['monitoringTypeKey'],
                                        'start_date'=>$v['attributes']['startDate'],
                                        'completed_date'=>$v['attributes']['completedDate'],
                                        'contact_person_key'=>$v['attributes']['contactPersonKey'],
                                        'contact_title'=>$v['attributes']['contactTitle'],
                                        'confirmed_date'=>$v['attributes']['confirmedDate'],
                                        'monitoring_status_type_key'=>$v['attributes']['monitoringStatusTypeKey'],
                                        'comment'=>$v['attributes']['comment'],
                                        'entered_by_user_key'=>$v['attributes']['enteredByUserKey'],
                                        'user_key'=>$v['attributes']['userKey'],
                                        'on_site_monitor_end_date'=>$v['attributes']['onSiteMonitorEndDate'],
                                        'status_results'=>$v['attributes']['statusResults'],                                            
                                        'monitoring_key'=>$v['attributes']['monitoringKey'],
                                    ]);
                                    // Create the sync table entry with the allita id
                                    $syncTableRecord = SyncMonitoring::where('id', $updateRecord['id'])
                                    ->update([
                                            
                                            
                                            
                                            
                                        'development_key'=>$v['attributes']['developmentKey'],
                                            
                                        'development_program_key'=>$v['attributes']['developmentProgramKey'],
                                        'monitoring_type_key'=>$v['attributes']['monitoringTypeKey'],
                                        'start_date'=>$v['attributes']['startDate'],
                                        'completed_date'=>$v['attributes']['completedDate'],
                                        'contact_person_key'=>$v['attributes']['contactPersonKey'],
                                        'contact_title'=>$v['attributes']['contactTitle'],
                                        'confirmed_date'=>$v['attributes']['confirmedDate'],
                                        'monitoring_status_type_key'=>$v['attributes']['monitoringStatusTypeKey'],
                                        'comment'=>$v['attributes']['comment'],
                                        'entered_by_user_key'=>$v['attributes']['enteredByUserKey'],
                                        'user_key'=>$v['attributes']['userKey'],
                                        'on_site_monitor_end_date'=>$v['attributes']['onSiteMonitorEndDate'],
                                        'status_results'=>$v['attributes']['statusResults'],
                                        'monitoring_key'=>$v['attributes']['monitoringKey'],
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
                            $allitaTableRecord = Audit::create([
                                    

                                            
                                    'monitoring_key'=>$v['attributes']['monitoringKey'],
                                    'development_key'=>$v['attributes']['developmentKey'],
                                            
                                    'development_program_key'=>$v['attributes']['developmentProgramKey'],
                                    'monitoring_type_key'=>$v['attributes']['monitoringTypeKey'],
                                    'start_date'=>$v['attributes']['startDate'],
                                    'completed_date'=>$v['attributes']['completedDate'],
                                    'contact_person_key'=>$v['attributes']['contactPersonKey'],
                                    'contact_title'=>$v['attributes']['contactTitle'],
                                    'confirmed_date'=>$v['attributes']['confirmedDate'],
                                    'monitoring_status_type_key'=>$v['attributes']['monitoringStatusTypeKey'],
                                    'comment'=>$v['attributes']['comment'],
                                    'entered_by_user_key'=>$v['attributes']['enteredByUserKey'],
                                    'user_key'=>$v['attributes']['userKey'],
                                    'on_site_monitor_end_date'=>$v['attributes']['onSiteMonitorEndDate'],
                                    'status_results'=>$v['attributes']['statusResults'],
                                            
                                            
                                            
                                    
                            'monitoring_key'=>$v['attributes']['monitoringKey'],
                            ]);
                            // Create the sync table entry with the allita id
                            $syncTableRecord = SyncMonitoring::create([
                                            
                                            
                                            
                                            
                                    'development_key'=>$v['attributes']['developmentKey'],
                                            
                                    'development_program_key'=>$v['attributes']['developmentProgramKey'],
                                    'monitoring_type_key'=>$v['attributes']['monitoringTypeKey'],
                                    'start_date'=>$v['attributes']['startDate'],
                                    'completed_date'=>$v['attributes']['completedDate'],
                                    'contact_person_key'=>$v['attributes']['contactPersonKey'],
                                    'contact_title'=>$v['attributes']['contactTitle'],
                                    'confirmed_date'=>$v['attributes']['confirmedDate'],
                                    'monitoring_status_type_key'=>$v['attributes']['monitoringStatusTypeKey'],
                                    'comment'=>$v['attributes']['comment'],
                                    'entered_by_user_key'=>$v['attributes']['enteredByUserKey'],
                                    'user_key'=>$v['attributes']['userKey'],
                                    'on_site_monitor_end_date'=>$v['attributes']['onSiteMonitorEndDate'],
                                    'status_results'=>$v['attributes']['statusResults'],
                                            
                                            
                                            

                                'monitoring_key'=>$v['attributes']['monitoringKey'],
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
}
