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
use Illuminate\Support\Facades\Hash;

use App\Models\SyncAddress;
//use App\Models\Address;

class SyncAddresses implements ShouldQueue
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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //////////////////////////////////////////////////
        /////// Address Sync
        /////

        /// get last modified date inside the database
        $lastModifiedDate = SyncAddress::select('last_edited')->orderBy('last_edited','desc')->first();
        // if the value is null set a default start date to start the sync.
        if(is_null($lastModifiedDate)) {
            $modified = '10/1/1900';
        }else{
            // format date stored to the format we are looking for...
            // we resync the last second of the data to be sure we get any records that happened to be recorded at the same second.
            $modified = date('m/d/Y g:i:sa',(strtotime($lastModifiedDate->last_edited)-1));
        }
        $apiConnect = new DevcoService();
        if(!is_null($apiConnect)){
            $syncData = $apiConnect->listAddresses(1, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
            $syncData = json_decode($syncData, true);
            $syncPage = 1;
            do{
                if($syncPage > 1){
                    //Get Next Page
                    $syncData = $apiConnect->listAddresses($syncPage, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
                    $syncData = json_decode($syncData, true);
                }
                foreach($syncData['data'] as $i => $v)
                    {
                        // check if record exists
                        $updateRecord = SyncAddress::select('id')->where('devco_id',$v['addressKey'])->first();

                        if(isset($updateRecord->id)) {
                            // record exists - update it.
                            if(strtotime($v['attributes']['lastEdited']) > strtotime($lastModifiedDate->last_edited)){
                                // record is newer than the one currently on file
                                SyncAddress::where('id',$updateRecord['id'])
                                ->update([
                                    'line_1'=>$v['attributes']['line1'],
                                    'line_2'=>$v['attributes']['line2'],
                                    'city'=>$v['attributes']['city'],
                                    'state'=>$v['attributes']['state'],
                                    'zip'=>$v['attributes']['zipCode'],
                                    'zip_4'=>$v['attributes']['zip4'],
                                    'longitude'=>$v['attributes']['latitude'],
                                    'latitude'=>$v['attributes']['longitude'],
                                    'last_edited'=>$v['attributes']['lastEdited'],
                                ]);
                            }
                        } else {
                            SyncAddress::create([
                                'devco_id'=>$v['attributes']['addressKey'],
                                'line_1'=>$v['attributes']['line1'],
                                'line_2'=>$v['attributes']['line2'],
                                'city'=>$v['attributes']['city'],
                                'state'=>$v['attributes']['state'],
                                'zip'=>$v['attributes']['zipCode'],
                                'zip_4'=>$v['attributes']['zip4'],
                                'longitude'=>$v['attributes']['latitude'],
                                'latitude'=>$v['attributes']['longitude'],
                                'last_edited'=>$v['attributes']['lastEdited'],
                            ]);
                        }

                    }
                $syncPage++;
            }while($syncPage < $syncData['meta']['totalPageCount']);
        }
    }
}
