<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Session;
use App\LogConverter;

class FindingController extends Controller
{
	public function __construct()
    {
        // $this->middleware('auth');
        if(env('APP_DEBUG_NO_DEVCO') == 'true'){
    	   Auth::onceUsingId(1); // TEST BRIAN
        }
    }

    public function modalFindings()
    {
    	$data = collect([
    		
    	]);
    	return view('modals.findings', compact('data'));
    }

}