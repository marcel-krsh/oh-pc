<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use App\Models\SyncAddress;
use App\Models\Address;



class SyncController extends Controller
{
    //
    public function sync() {
        //////////////////////////////////////////////////
        /////// Address Sync
        /////

        /// get last modified date inside the database
        $lastModifiedDate = SyncAddress::select('updated_at')->orderBy('updated_at','desc')->first();
        // if the value is null set a default start date to start the sync.
        if(is_null($lastModifiedDate)) {
            $modified = '10/1/1900';
        }else{
            $modified = $lastModifiedDate->last_edited;
        }
    	$apiConnect = new DevcoService();
        if(!is_null($apiConnect)){
            $syncData = $apiConnect->listAddresses(1, $modified, 1,'admin@allita.org', 'System Sync Job', 1, 'Server');
            $syncData = json_decode($syncData, true);
            $syncPage = 1;
            do{
                //dd($syncData);
                echo "<hr />PAGE ".$syncPage."<hr />";
                foreach($syncData['data'] as $i => $v)
                    {
                        // check if record exists
                        $updateRecord = Address::select('id')->where('devco_id',$v['id'])->first();

                        if(isset($updateRecord->id)) {
                            // record exists - update it.
                            Address::where('id',$updateRecord['id'])
                            ->update([
                                'line_1'=>$v['attributes']['line1'],
                                'line_2'=>$v['attributes']['line2'],
                                'city'=>$v['attributes']['city'],
                                'state'=>$v['attributes']['state'],
                                'zip'=>$v['attributes']['zipCode'],
                                'zip_4'=>$v['attributes']['zip4'],
                                'longitude'=>$v['attributes']['latitude'],
                                'latitude'=>$v['attributes']['longitude'],
                                'address_key'=>$v['attributes']['addressKey'],
                                'last_edited'=>$v['attributes']['lastEdited'],
                            ]);
                        } else {
                            Address::insert([
                                'line_1'=>$v['attributes']['line1'],
                                'line_2'=>$v['attributes']['line2'],
                                'city'=>$v['attributes']['city'],
                                'state'=>$v['attributes']['state'],
                                'zip'=>$v['attributes']['zipCode'],
                                'zip_4'=>$v['attributes']['zip4'],
                                'longitude'=>$v['attributes']['latitude'],
                                'latitude'=>$v['attributes']['longitude'],
                                'address_key'=>$v['attributes']['addressKey'],
                                'last_edited'=>$v['attributes']['lastEdited'],
                            ]);
                        }

                        //echo $v['id'].' '.$v['attributes']['line1'].' '.' '.$v['attributes']['city'].' '.$v['attributes']['state'].' '.$v['attributes']['zipCode'].' '.$v['attributes']['zip4'].' '.$v['attributes']['latitude'].' '.$v['attributes']['longitude'].' '.$v['attributes']['addressKey'].' '.$v['attributes']['lastEdited'].'<br/>';
                    }
                $syncPage++;
            }while($syncPage < $syncData['meta']['totalPageCount']);
        }

		
    }
}
