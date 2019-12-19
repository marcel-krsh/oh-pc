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
use App\Models\SyncEmailAddress;
use App\Models\EmailAddress;

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

        $this->line('Starting to sync the EmailAddress Sync:'.PHP_EOL);
     
        //////////////////////////////////////////////////
        /////// EmailAddress Sync
        /////

        /// get last modified date inside the database
        /// PHP 7.3 will fix issue with PDO not recognizing the milisecond precision
        /// PHP 7.2X and lower instead drops the milisecond off.
        /// Most php apps operate at the precision of whole seconds. However Devco operates at a TimeStamp(3) precision.
        /// To get the full time stamp out of the Allita DB, we trick the query into thinking it is a string.
        /// To do this we use the DB::raw() function and use CONCAT on the column.
        /// We also need to select the column so we can order by it to get the newest first. So we apply an alias to the concated field.

        $lastModifiedDate = SyncEmailAddress::select(DB::raw("CONCAT(last_edited) as 'last_edited_convert'"), 'last_edited', 'id')->orderBy('last_edited', 'desc')->first();
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
        $this->line('Last Edited Raw:'.$lastModifiedDate->last_edited.PHP_EOL);
        $this->line('Last Edited Converted:'.$lastModifiedDate->last_edited_convert.PHP_EOL);
        $this->line('Last Edited Modified for Check:'.$modified.PHP_EOL);


        $apiConnect = new DevcoService();
        if (!is_null($apiConnect)) {
            $this->line('Connected to DEVCO'.PHP_EOL);
            $syncData = $apiConnect->listEmailAddresses(1, $modified, 1, 'admin@allita.org', 'System Sync Job', 1, 'Server');
            $syncData = json_decode($syncData, true);
            $syncPage = 1;

            
            if ($syncData['meta']['totalPageCount'] > 0) {
                $this->line('Updating '.$syncData['meta']['totalPageCount'].' Pages of Data From DEVCO.'.PHP_EOL);
                do {
                    if ($syncPage > 1) {
                        //Get Next Page
                        $syncData = $apiConnect->listEmailAddresses($syncPage, $modified, 1, 'admin@allita.org', 'System Sync Job', 1, 'Server');
                        $syncData = json_decode($syncData, true);
                        //dd('Page Count is Higher',$syncData,$syncData['meta']['totalPageCount'],$syncPage);
                    }
                    //dd('Page Count is Higher',$syncData,$modified,$syncData,$syncData['meta']['totalPageCount'],$syncPage);
                    foreach ($syncData['data'] as $i => $v) {
                            // check if record exists

                        $this->line('Finding Matching Record for email_address_key:'.$v['attributes']['emailAddressKey'].PHP_EOL);
                            $updateRecord = SyncEmailAddress::select('id', 'allita_id', 'last_edited', 'updated_at')->where('email_address_key', $v['attributes']['emailAddressKey'])->first();

                            
                        if (isset($updateRecord->id)) {
                            // record exists - get matching table record
                            $this->line('found the record... updating...'.PHP_EOL);
                            /// NEW CODE TO UPDATE ALLITA TABLE PART 1
                            $allitaTableRecord = EmailAddress::find($updateRecord->allita_id);
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

                            $this->line('Dates:: raw last_edited date from DEVCO: '.$v['attributes']['lastEdited'].' || devcoDate - '.$devcoDate.' ||  allitaDate - '. $allitaDate.' || allitaFloat - '.$allitaFloat.' || devcoFloat - '.$devcoFloat.' || devcoDateEval - '. $devcoDateEval. ' || allitaDateEval'.PHP_EOL);
                                
                            //dd($allitaTableRecord,$devcoDateEval,$allitaDateEval,$allitaTableRecord->last_edited, $updateRecord->updated_at);
                                
                            if ($devcoDateEval > $allitaDateEval) {
                                $this->line('Devco Source is determined to be newer.'.PHP_EOL);
                                if (!is_null($allitaTableRecord) && $allitaTableRecord->last_edited <= $updateRecord->updated_at) {
                                    // record is newer than the one currently on file in the allita db.
                                    // update the sync table first
                                    SyncEmailAddress::where('id', $updateRecord['id'])
                                    ->update([
                                            
                                            
                                            
                                        'email_address_type_key'=>$v['attributes']['emailAddressTypeKey'],
                                            
                                        'email_address'=>$v['attributes']['emailAddress'],
                                            
                                            
                                            
                                            
                                            
                                            
                                        'last_edited'=>$v['attributes']['lastEdited'],
                                    ]);
                                    $UpdateAllitaValues = SyncEmailAddress::find($updateRecord['id']);
                                    // update the allita db - we use the updated at of the sync table as the last edited value for the actual Allita Table.
                                    $allitaTableRecord->update([
                                            
                                            
                                            
                                        'email_address_type_key'=>$v['attributes']['emailAddressTypeKey'],
                                        'email_address_type_id'=>null,
                                        'email_address'=>$v['attributes']['emailAddress'],
                                            
                                            
                                            
                                            
                                            
                                            
                                        'last_edited'=>$UpdateAllitaValues->updated_at,
                                    ]);
                                    //dd('inside.');
                                } elseif (is_null($allitaTableRecord)) {
                                    // the allita table record doesn't exist
                                    // create the allita table record and then update the record
                                    // we create it first so we can ensure the correct updated at
                                    // date ends up in the allita table record
                                    // (if we create the sync record first the updated at date would become out of sync with the allita table.)

                                    $allitaTableRecord = EmailAddress::create([
                                            
                                            
                                            
                                            
                                        'email_address_type_key'=>$v['attributes']['emailAddressTypeKey'],
                                            
                                        'email_address'=>$v['attributes']['emailAddress'],
                                            
                                            
                                            
                                            
                                            
                                            
                                        'email_address_key'=>$v['attributes']['emailAddressKey'],
                                    ]);
                                    // Create the sync table entry with the allita id
                                    $syncTableRecord = SyncEmailAddress::where('id', $updateRecord['id'])
                                    ->update([
                                            
                                            
                                            
                                            
                                        'email_address_type_key'=>$v['attributes']['emailAddressTypeKey'],
                                            
                                        'email_address'=>$v['attributes']['emailAddress'],
                                            
                                            
                                            
                                            
                                            
                                            
                                        'email_address_key'=>$v['attributes']['emailAddressKey'],
                                        'last_edited'=>$v['attributes']['lastEdited'],
                                        'allita_id'=>$allitaTableRecord->id,
                                    ]);
                                    // Update the Allita Table Record with the Sync Table's updated at date
                                    $allitaTableRecord->update(['last_edited'=>$syncTableRecord->updated_at]);
                                }
                            } else {
                                $this->line('Devco Date is not newer.');
                            }
                        } else {
                            $this->line('Record Not Found in Sync Table - Creating a New Record'.PHP_EOL);
                            // Create the Allita Entry First
                            // We do this so the updated_at value of the Sync Table does not become newer
                            // when we add in the allita_id
                            $allitaTableRecord = EmailAddress::create([
                                    

                                            
                                    'email_address_key'=>$v['attributes']['emailAddressKey'],
                                    'email_address_type_key'=>$v['attributes']['emailAddressTypeKey'],
                                            
                                    'email_address'=>$v['attributes']['emailAddress'],
                                            
                                            
                                            
                                            
                                            
                                    
                            'email_address_key'=>$v['attributes']['emailAddressKey'],
                            ]);
                            // Create the sync table entry with the allita id
                            $syncTableRecord = SyncEmailAddress::create([
                                            
                                            
                                            
                                            
                                    'email_address_type_key'=>$v['attributes']['emailAddressTypeKey'],
                                            
                                    'email_address'=>$v['attributes']['emailAddress'],
                                            
                                            
                                            
                                            
                                            

                                'email_address_key'=>$v['attributes']['emailAddressKey'],
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
