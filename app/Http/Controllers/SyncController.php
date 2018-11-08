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
    	$addresses = $test->listAddresses(1, 'january 1, 2019', 1,'brian@allita.org', 'Brian Greenwood', 1, 'Server');
    	return $addresses;

		
    }
}
