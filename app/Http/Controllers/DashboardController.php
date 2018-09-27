<?php

namespace App\Http\Controllers;

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
         //$this->middleware('allita.auth');
        if(env('APP_DEBUG_NO_DEVCO') == 'true'){
            Auth::onceUsingId(1); // TEST BRIAN
        }
    }

    public function login()
    {
        return "This feature has been replaced with a DevCo login. Please visit Devco Online to login.";
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

        //return \view('dashboard.index'); //, compact('user')
        return view('dashboard.index', compact('tab', 'loadDetailTab'));
    }

    public function audits(Request $request)
    {
        
        // $request will contain filters
        // $auditFilterMineOnly
        // $auditFilterMineOnly

        $filter = $request->get('filter');

        $auditFilterMineOnly = 1;

        return view('dashboard.audits', compact('filter', 'auditFilterMineOnly'));
    }

    public function reports(Request $request)
    {
        
        //return \view('dashboard.index'); //, compact('user')
        return view('dashboard.reports');
    }

    public function communications(Request $request)
    {
        
        //return \view('dashboard.index'); //, compact('user')
        return view('dashboard.communications');
    }

    public function autocomplete(Request $request)
    {
        /*
        if (Auth::user()->entity_type == 'hfa') {
            $parcels = Parcel::join('states', 'parcels.state_id', 'states.id')
            ->join('property_status_options as hfa_status', 'parcels.hfa_property_status_id', 'hfa_status.id')
            ->join('property_status_options as lb_status', 'parcels.landbank_property_status_id', 'lb_status.id')
            ->leftJoin('import_rows', 'import_rows.row_id', 'parcels.id')
            ->leftJoin('imports', 'imports.id', 'import_rows.import_id')
            ->leftJoin('users', 'users.id', 'imports.user_id')
            ->select('street_address', 'city', 'state_acronym', 'parcels.parcel_id', 'parcels.id', 'lb_status.option_name as lb_status_name', 'hfa_status.option_name as hfa_status_name', 'import_rows.import_id', 'users.name', 'imports.created_at', 'imports.validated')
            ->where('parcel_id', 'LIKE', '%'.$request->search.'%')
            ->orWhere('city', 'like', '%'.$request->search.'%')
            ->orWhere('street_address', 'like', '%'.$request->search.'%')->take(20)->get()->all();
        } else {
            $parcels = Parcel::join('states', 'parcels.state_id', 'states.id')
                        ->join('property_status_options as lb_status', 'parcels.landbank_property_status_id', 'lb_status.id')
                        ->join('property_status_options as hfa_status', 'parcels.hfa_property_status_id', 'hfa_status.id')
                        ->leftJoin('import_rows', 'import_rows.row_id', 'parcels.id')
                        ->leftJoin('imports', 'imports.id', 'import_rows.import_id')
                        ->leftJoin('users', 'users.id', 'imports.user_id')
                        ->select('street_address', 'city', 'state_acronym', 'parcels.parcel_id', 'parcels.id', 'lb_status.option_name as lb_status_name', 'hfa_status.option_name as hfa_status_name', 'import_rows.import_id as import_id', 'users.name as name', 'imports.created_at', 'imports.validated')

                        ->where('parcels.entity_id', Auth::user()->entity_id)
                        ->where(function ($q) use ($request) {
                            //$request = Request::input();
                            $q->where('parcel_id', 'LIKE', '%'.$request->search.'%')
                            ->orWhere('city', 'like', '%'.$request->search.'%')
                            ->orWhere('street_address', 'like', '%'.$request->search.'%');
                        })->take(20)->get()->all();
        }
        $i = 0;
        $results=[];
        foreach ($parcels as $data) {
            $parcels[$i]->created_at_formatted = date('n/j/y \a\t g:h a', strtotime($data->created_at));
            $results[] = [
                        $data->street_address,
                        $data->city,
                        $data->state_acronym,
                        $data->parcel_id,
                        $data->id,
                        $data->lb_status_name,
                        $data->hfa_status_name,
                        $data->import_id,
                        $data->name,
                        $data->created_at,
                        $data->validated,
                        $parcels[$i]->created_at_formatted];
            $i++;
        }
        */

        //Project Id Audit id Main address Property Manager Name Project Name
        $results = [];
        // $results[] = [
        //                 $data->street_address,
        //                 $data->city,
        //                 $data->state_acronym,
        //                 $data->project_id,
        //                 $data->audit_id,
        //                 $data->manager_name,
        //                 $data->project_name
        //             ];
        $results[] = [
                        '123 Street Name',
                        'City Name',
                        'OH',
                        '123456',
                        '654322',
                        'Bob Manager',
                        'Project Name'
                    ];

        $results[] = [
                        '456 Street Name',
                        'City 2 Name',
                        'OH',
                        '789',
                        '987',
                        'John Manager',
                        'Project Name 2'
                    ];

        $results[] = [
                        '456 Street Name',
                        'City 2 Name',
                        'OH',
                        '789',
                        '987',
                        'John Manager',
                        'Project Name 2'
                    ];

        $results[] = [
                        '456 Street Name',
                        'City 2 Name',
                        'OH',
                        '789',
                        '987',
                        'John Manager',
                        'Project Name 2'
                    ];
        
        $results = json_encode($results);
        return $results;
    }

}