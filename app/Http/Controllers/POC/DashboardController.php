<?php

namespace App\Http\Controllers\POC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Session;
use App\LogConverter;

class DashboardController extends Controller
{
	public function __construct()
    {
        // $this->middleware('auth');
    	Auth::onceUsingId(1); // TEST BRIAN
    }

    public function index(Request $request)
    {
        if ($request->query('tab') >= 1) {
            $tab = "dash-subtab-".intval($request->query('tab'));
            $showHowTo = 2;
        } else {
            // default tab to load
            $tab = "dash-subtab-1";
        }

        //// load the sitevisit tab instead
        $routed = \Route::getFacadeRoot()->current()->uri();
        if ($routed == "site_visit_manager") {
            // Give instruction on steps to take for a approved POs.
            $loadDetailTab = 2;
        } else {
            $loadDetailTab = 1;
        }

        $current_user = Auth::user();


$tab = "detail-tab-1";
        //return \view('poc.dashboard.index'); //, compact('user')
        return view('poc.dashboard.index', compact('tab', 'loadDetailTab'));
    }

    public function audits(Request $request)
    {
        
        // $request will contain filters
        // $auditFilterMineOnly
        // $auditFilterMineOnly

        $filter = $request->get('filter');

        return view('poc.dashboard.audits', compact('filter'));
    }

    public function reports(Request $request)
    {
        
        //return \view('poc.dashboard.index'); //, compact('user')
        return view('poc.dashboard.reports');
    }

    public function communications(Request $request)
    {
        
        //return \view('poc.dashboard.index'); //, compact('user')
        return view('poc.dashboard.communications');
    }

}
