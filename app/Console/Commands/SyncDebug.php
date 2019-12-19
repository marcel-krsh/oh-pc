<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
use App\Models\User;
use DB;
use DateTime;
use Illuminate\Support\Facades\Hash;
// includes specific to this sync
use App\Models\SyncBuilding;
use App\Models\Building;


class SyncDebug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use this to debug the sync process. Copy and paste in the handle from the sync job to this handle and include the models etc affected. Add in $this->line() items to help debug what is happening during the sync';

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

        $this->line('Starting to sync the Address Sync:'.PHP_EOL);
     
        //////////////////////////////////////////////////
        /////// Building Sync YAY
        /////

        /// get last modified date inside the database
        /// PHP 7.3 will fix issue with PDO not recognizing the milisecond precision
        /// PHP 7.2X and lower instead drops the milisecond off.
        /// Most php apps operate at the precision of whole seconds. However Devco operates at a TimeStamp(3) precision.
        /// To get the full time stamp out of the Allita DB, we trick the query into thinking it is a string.
        /// To do this we use the DB::raw() function and use CONCAT on the column.
        /// We also need to select the column so we can order by it to get the newest first. So we apply an alias to the concated field.

        $lastModifiedDate = SyncBuilding::select(DB::raw("CONCAT(last_edited) as 'last_edited_convert'"), 'last_edited', 'id')->orderBy('last_edited', 'asc')->first();
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
            $syncData = $apiConnect->listBuildings(1, $modified, 1, 'admin@allita.org', 'System Sync Job', 1, 'Server');
            $syncData = json_decode($syncData, true);
            $syncPage = 1;
            //dd($syncData);
            //dd($lastModifiedDate->last_edited_convert,$currentModifiedDateTimeStamp,$modified,$syncData);
            if ($syncData['meta']['totalPageCount'] > 0) {
                $processBar = $this->output->createProgressBar(count($syncData['meta']['totalPageCount']));
                do {
                    $processBar->advance();
                    if ($syncPage > 1) {
                        //Get Next Page
                        $syncData = $apiConnect->listBuildings($syncPage, $modified, 1, 'admin@allita.org', 'System Sync Job', 1, 'Server');
                        $syncData = json_decode($syncData, true);
                        //dd('Page Count is Higher',$syncData,$syncData['meta']['totalPageCount'],$syncPage);
                    }
                    //dd('Page Count is Higher',$syncData,$modified,$syncData,$syncData['meta']['totalPageCount'],$syncPage);
                    
                    foreach ($syncData['data'] as $i => $v) {
                            
                            // check if record exists
                            $updateRecord = SyncBuilding::select('id', 'allita_id', 'last_edited', 'updated_at')->where('building_key', $v['attributes']['buildingKey'])->first();
                            // convert booleans
                             settype($v['attributes']['ownerPaidUtilities'], 'boolean');
                            // settype($v['attributes']['isBuildingHandicapAccessible'], 'boolean');

                            // Set dates older than 1950 to be NULL:
                        if ($v['attributes']['acquisitionDate'] < 1951) {
                            $v['attributes']['acquisitionDate'] = null;
                        }
                        if ($v['attributes']['buildingBuiltDate'] < 1951) {
                            $v['attributes']['buildingBuiltDate'] = null;
                        }
                            // if($v['attributes']['confirmedDate'] < 1951){
                            //     $v['attributes']['confirmedDate'] = NULL;
                            // }
                            // if($v['attributes']['onSiteMonitorEndDate'] < 1951){
                            //     $v['attributes']['onSiteMonitorEndDate'] = NULL;
                            // }
                            //dd($updateRecord,$updateRecord->updated_at);
                        if (isset($updateRecord->id)) {
                            // record exists - get matching table record

                            /// NEW CODE TO UPDATE ALLITA TABLE PART 1
                            $allitaTableRecord = Building::find($updateRecord->allita_id);
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
                                    $countofitems ++;
                                    //$this->line('Updated '.$v['attributes']['buildingKey']. PHP_EOL);
                                    // record is newer than the one currently on file in the allita db.
                                    // update the sync table first
                                    SyncBuilding::where('id', $updateRecord['id'])
                                    ->update([
                                            
                                            
                                            
                                        'development_key'=>$v['attributes']['developmentKey'],
                                        'building_status_key'=>$v['attributes']['buildingStatusKey'],
                                        'building_name'=>$v['attributes']['buildingName'],
                                        'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                        'in_service_date'=>$v['attributes']['inServiceDate'],
                                        'applicable_fraction'=>$v['attributes']['applicableFraction'],
                                        'owner_paid_utilities'=>$v['attributes']['ownerPaidUtilities'],
                                        'acquisition_date'=>$v['attributes']['acquisitionDate'],
                                        'building_built_date'=>$v['attributes']['buildingBuiltDate'],
                                            
                                            
                                            
                                            
                                            
                                        'last_edited'=>$v['attributes']['lastEdited'],
                                    ]);
                                    $UpdateAllitaValues = SyncBuilding::find($updateRecord['id']);
                                    // update the allita db - we use the updated at of the sync table as the last edited value for the actual Allita Table.
                                    $allitaTableRecord->update([
                                            
                                            
                                            
                                        'development_key'=>$v['attributes']['developmentKey'],
                                        'project_id'=>null,
                                        'building_status_key'=>$v['attributes']['buildingStatusKey'],
                                        'building_status_id'=>null,
                                        'building_name'=>$v['attributes']['buildingName'],
                                        'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                        'physical_address_id'=>null,
                                        'in_service_date'=>$v['attributes']['inServiceDate'],
                                        'applicable_fraction'=>$v['attributes']['applicableFraction'],
                                        'owner_paid_utilities'=>$v['attributes']['ownerPaidUtilities'],
                                        'acquisition_date'=>$v['attributes']['acquisitionDate'],
                                        'building_built_date'=>$v['attributes']['buildingBuiltDate'],
                                            
                                            
                                            
                                            
                                            
                                        'last_edited'=>$UpdateAllitaValues->updated_at,
                                    ]);
                                    //dd('inside.');
                                } elseif (is_null($allitaTableRecord)) {
                                    // the allita table record doesn't exist
                                    // create the allita table record and then update the record
                                    // we create it first so we can ensure the correct updated at
                                    // date ends up in the allita table record
                                    // (if we create the sync record first the updated at date would become out of sync with the allita table.)
                                    $this->line(' Added missing '.$v['attributes']['buildingKey']. PHP_EOL);
                                    $allitaTableRecord = Building::create([
                                            
                                            
                                            
                                            
                                        'development_key'=>$v['attributes']['developmentKey'],
                                        'building_status_key'=>$v['attributes']['buildingStatusKey'],
                                        'building_name'=>$v['attributes']['buildingName'],
                                        'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                        'in_service_date'=>$v['attributes']['inServiceDate'],
                                        'applicable_fraction'=>$v['attributes']['applicableFraction'],
                                        'owner_paid_utilities'=>$v['attributes']['ownerPaidUtilities'],
                                        'acquisition_date'=>$v['attributes']['acquisitionDate'],
                                        'building_built_date'=>$v['attributes']['buildingBuiltDate'],
                                            
                                            
                                            
                                            
                                            
                                        'building_key'=>$v['attributes']['buildingKey'],
                                    ]);
                                    // Create the sync table entry with the allita id
                                    $syncTableRecord = SyncBuilding::where('id', $updateRecord['id'])
                                    ->update([
                                            
                                            
                                            
                                            
                                        'development_key'=>$v['attributes']['developmentKey'],
                                        'building_status_key'=>$v['attributes']['buildingStatusKey'],
                                        'building_name'=>$v['attributes']['buildingName'],
                                        'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                        'in_service_date'=>$v['attributes']['inServiceDate'],
                                        'applicable_fraction'=>$v['attributes']['applicableFraction'],
                                        'owner_paid_utilities'=>$v['attributes']['ownerPaidUtilities'],
                                        'acquisition_date'=>$v['attributes']['acquisitionDate'],
                                        'building_built_date'=>$v['attributes']['buildingBuiltDate'],
                                            
                                            
                                            
                                            
                                            
                                        'building_key'=>$v['attributes']['buildingKey'],
                                        'last_edited'=>$v['attributes']['lastEdited'],
                                        'allita_id'=>$allitaTableRecord->id,
                                    ]);

                                    if(!is_object($syncTableRecord)) {
                                        // Record didn't exist???
                                        $syncTableRecord = SyncBuilding::create([
                                                    
                                                'development_key'=>$v['attributes']['developmentKey'],
                                                'building_status_key'=>$v['attributes']['buildingStatusKey'],
                                                'building_name'=>$v['attributes']['buildingName'],
                                                'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                                'in_service_date'=>$v['attributes']['inServiceDate'],
                                                'applicable_fraction'=>$v['attributes']['applicableFraction'],
                                                'owner_paid_utilities'=>$v['attributes']['ownerPaidUtilities'],
                                                'acquisition_date'=>$v['attributes']['acquisitionDate'],
                                                'building_built_date'=>$v['attributes']['buildingBuiltDate'],
                                                'building_key'=>$v['attributes']['buildingKey'],
                                                'last_edited'=>$v['attributes']['lastEdited'],
                                                'allita_id'=>$allitaTableRecord->id,
                                            ]);

                                    }
                                    // Update the Allita Table Record with the Sync Table's updated at date
                                    $allitaTableRecord->update(['last_edited'=>$syncTableRecord->updated_at]);
                                }
                            }
                        } else {
                            // Create the Allita Entry First
                            // We do this so the updated_at value of the Sync Table does not become newer
                            // when we add in the allita_id
                            $this->line(' Added completely missing '.$v['attributes']['buildingKey']. PHP_EOL);

                            $allitaTableRecord = Building::create([
                                    

                                            
                                    'building_key'=>$v['attributes']['buildingKey'],
                                    'development_key'=>$v['attributes']['developmentKey'],
                                    'building_status_key'=>$v['attributes']['buildingStatusKey'],
                                    'building_name'=>$v['attributes']['buildingName'],
                                    'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                    'in_service_date'=>$v['attributes']['inServiceDate'],
                                    'applicable_fraction'=>$v['attributes']['applicableFraction'],
                                    'owner_paid_utilities'=>$v['attributes']['ownerPaidUtilities'],
                                    'acquisition_date'=>$v['attributes']['acquisitionDate'],
                                    'building_built_date'=>$v['attributes']['buildingBuiltDate'],
                                            
                                            
                                            
                                            
                                    
                            'building_key'=>$v['attributes']['buildingKey'],
                            ]);
                            // Create the sync table entry with the allita id
                            $syncTableRecord = SyncBuilding::create([
                                            
                                            
                                            
                                            
                                    'development_key'=>$v['attributes']['developmentKey'],
                                    'building_status_key'=>$v['attributes']['buildingStatusKey'],
                                    'building_name'=>$v['attributes']['buildingName'],
                                    'physical_address_key'=>$v['attributes']['physicalAddressKey'],
                                    'in_service_date'=>$v['attributes']['inServiceDate'],
                                    'applicable_fraction'=>$v['attributes']['applicableFraction'],
                                    'owner_paid_utilities'=>$v['attributes']['ownerPaidUtilities'],
                                    'acquisition_date'=>$v['attributes']['acquisitionDate'],
                                    'building_built_date'=>$v['attributes']['buildingBuiltDate'],
                                            
                                            
                                            
                                            

                                'building_key'=>$v['attributes']['buildingKey'],
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
