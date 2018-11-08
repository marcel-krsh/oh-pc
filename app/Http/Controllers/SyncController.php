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
    	foreach($addresses['data'] as $i => $v)
            {
                echo $v['id'].' '.$v['attributes']->line_1.'<br/>';
            }

		
    }
}
