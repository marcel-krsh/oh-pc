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
    	$addresses = $test->listAddresses(1, '2019-04-23T08:50:19.637', 1,'brian@allita.org', 'Brian Greenwood', 1, 'Server');
    	forEach($addresses as $address){
    		$output .= $addresses['id']."<br />";
    	}
    	return $output;

		
    }
}
