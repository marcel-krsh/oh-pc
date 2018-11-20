<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SyncController extends Controller
{
    //
    public function sync() {
    	$test = new DevcoService();
    	$addresses = $test->listAddresses(1, '1/1/2017', 1,'brian@allita.org', 'Brian Greenwood', 1, 'Server');
    	// $addresses = $addresses->data;
    	// forEach($addresses as $address){
    	// 	$output .= $addresses['id']."<br />";
    	// }

    	$addresses = json_decode($addresses, true);
        dd($addresses);
    	foreach($addresses['data'] as $i => $v)
            {
                echo $v['id'].' '.$v['attributes']['line1'].' '.' '.$v['attributes']['city'].' '.$v['attributes']['state'].' '.$v['attributes']['zipCode'].' '.$v['attributes']['zip4'].' '.$v['attributes']['latitude'].' '.$v['attributes']['longitude'].' '.$v['attributes']['addressKey'].' '.$v['attributes']['lastEdited'].'<br/>';
            }

		
    }
}
