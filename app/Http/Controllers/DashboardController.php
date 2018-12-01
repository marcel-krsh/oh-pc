<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Session;
use App\Models\SystemSetting;
use App\LogConverter;
use App\Models\CachedAudit;
use Carbon;
use App\Models\CommunicationRecipient;

use Illuminate\Support\Facades\Redis;

class DashboardController extends Controller
{
    public function __construct()
    {
        // $this->middleware('allita.auth');
        if(env('APP_DEBUG_NO_DEVCO') == 'true'){
            Auth::onceUsingId(1); // TEST BRIAN
            //Auth::onceUsingId(286); // TEST BRIAN
            
            // this is normally setup upon login
            $current_user = Auth::user();
            if($current_user->socket_id === null){
                // create a socket id and store in user table
                $token = str_random(10);
                $current_user->socket_id = $token;
                $current_user->save();
            }
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

        $stats_communication_total = CommunicationRecipient::where('user_id', $current_user->id)
                    ->where('seen', 0)
                    ->count();

        //return \view('dashboard.index'); //, compact('user')
        return view('dashboard.index', compact('tab', 'loadDetailTab', 'stats_communication_total', 'current_user'));
    }

    public function audits(Request $request)
    {
        
        // $request will contain filters
        // $auditFilterMineOnly
        // $auditFilterMineOnly

        $filter = $request->get('filter');
        $filter_id = $request->get('filterId');

        if(session()->has('audit-sort-by')){
            $sort_by = session('audit-sort-by');

            if(session()->has('audit-sort-order') && session('audit-sort-order') != 'undefined'){
                $sort_order = session('audit-sort-order');
            }else{
                session(['audit-sort-order', 0]);
                $sort_order = 0;
            }
        }else{
            session(['audit-sort-by', 'audit-sort-project']);
            $sort_by = 'audit-sort-project';

            session(['audit-sort-order', 1]);
            $sort_order = 1;
        }

        $auditFilterMineOnly = 1;

        switch ($sort_by) {
            case "audit-sort-lead":
                $sort_by_field = 'lead';
                break;
            case "audit-sort-project":
                $sort_by_field = 'project_ref';
                break;
            case "audit-sort-project-name":
                $sort_by_field = 'title';
                break;
            case "audit-sort-pm":
                $sort_by_field = 'pm';
                break;
            case "audit-sort-address":
                $sort_by_field = 'address';
                break;
            case "audit-sort-city":
                $sort_by_field = 'city';
                break;
            case "audit-sort-state":
                $sort_by_field = 'state';
                break;
            case "audit-sort-zip":
                $sort_by_field = 'zip';
                break;
            case "audit-sort-scheduled-date":
                $sort_by_field = 'inspection_schedule_date';
                break;
            case "audit-sort-assigned-areas":
                $sort_by_field = 'total_items';
                break;
            case "audit-sort-total-areas":
                $sort_by_field = 'inspectable_items';
                break;
            case "audit-sort-compliance-status":
                $sort_by_field = 'audit_compliance_status';
                break;
            case "audit-sort-followup-date":
                $sort_by_field = 'followup_date';
                break;
            case "audit-sort-finding-file":
                $sort_by_field = 'file_audit_status';
                break;
            case "audit-sort-finding-nlt":
                $sort_by_field = 'nlt_audit_status';
                break;
            case "audit-sort-finding-lt":
                $sort_by_field = 'lt_audit_status';
                break;
            case "audit-sort-finding-sd":
                $sort_by_field = 'smoke_audit_status';
                break;
            case "audit-sort-status-auditor":
                $sort_by_field = 'auditor_status';
                break;
            case "audit-sort-status-message":
                $sort_by_field = 'message';
                break;
            case "audit-sort-status-document":
                $sort_by_field = 'document';
                break;
            case "audit-sort-status-history":
                $sort_by_field = 'history_status';
                break;
            case "audit-sort-next-task":
                $sort_by_field = 'step_status';
                break;
            default:
               $sort_by_field = 'id';
        }

        if($sort_order){
            $sort_order_query = "asc";
        }else{
            $sort_order_query = "desc";
        }

        $audits = CachedAudit::orderBy($sort_by_field, $sort_order_query)->get();

        // $sortOrder is either 1 or 0
        // $sortBy can be 'audit-sort-lead'

        // statuses
        // no-action action-required in-progress action-needed ok-actionable readonly
        // $audits = collect([
        //     [
        //         'id' => '123', // this is the audit id, when displayed, add leading zeros?
        //         'audit_id' => '19200114', // this is the project id from Devco, rename project_id
        //         'status' => 'critical',
        //         'lead' => 1, // id
        //         'lead_json' => [ // data in json format
        //                 'id' => 1, 
        //                 'name' => 'Brian Greenwood',
        //                 'initials' => 'BG',
        //                 'color' => 'green',
        //                 'status' => 'warning'
        //             ],
        //         'title' => 'Great American Apartments',
        //         'subtitle' => 'THE NOT SO LONG PROPERTY MANAGER NAME', // PM name, not subtitle! 
        //         'address' => '3045 Cumberland Woods Street, Suite 20',
        //         'city' => 'COLUMBUS',
        //         'state' => 'OH',
        //         'zip' => '43219',
        //         'total_buildings' => '10',
        //         'inspection_status' => 'action-needed',
        //         'inspection_status_text' => 'Inspection in progress',
        //         'inspection_schedule_date' => '12/21', // combine and use one date for both fields
        //         'inspection_schedule_year' => '2018',
        //         'inspection_schedule_text' => 'Inspection in progress',
        //         'inspectable_items' => '10',
        //         'total_items' => '21',
        //         'audit_compliance_icon' => 'a-circle-checked',
        //         'audit_compliance_status' => 'ok-actionable', 
        //         'audit_compliance_status_text' => 'Audit Compliant',
        //         'followup_status' => 'ok-actionable',
        //         'followup_status_text' => 'No followups',
        //         'followup_date' => '12/10', // combine and use one date for both fields
        //         'followup_year' => '2018',
        //         'file_audit_icon' => 'a-folder',
        //         'file_audit_status' => 'ok-actionable',
        //         'file_audit_status_text' => '',
        //         'nlt_audit_icon' => 'a-booboo',
        //         'nlt_audit_status' => 'action-required',
        //         'nlt_audit_status_text' => '',
        //         'lt_audit_icon' => 'a-skull',
        //         'lt_audit_status' => 'in-progress',
        //         'lt_audit_status_text' => '',
        //         'smoke_audit_icon' => 'a-flames',
        //         'smoke_audit_status' => 'action-needed',
        //         'smoke_audit_status_text' => '',
        //         'auditor_status_icon' => 'a-avatar',
        //         'auditor_status' => 'action-required',
        //         'auditor_status_text' => 'Auditors / schedule conflicts / unasigned items',
        //         'message_status_icon' => 'a-envelope-4',
        //         'message_status' => '',
        //         'message_status_text' => '',
        //         'document_status_icon' => 'a-files',
        //         'document_status' => '',
        //         'document_status_text' => 'Document status',
        //         'history_status_icon' => 'a-person-clock',
        //         'history_status' => '',
        //         'history_status_text' => 'NO/VIEW HISTORY',
        //         'step_status_icon' => 'a-calendar-7',
        //         'step_status' => 'no-action',
        //         'step_status_text' => ''
        //     ],
        //     [
        //         'id' => '456',
        //         'audit_id' => '19200115',
        //         'status' => 'no-action',
        //         'lead' => [
        //                 'name' => 'Brian Greenwood',
        //                 'initials' => 'BG',
        //                 'color' => 'green',
        //                 'status' => 'warning'
        //             ],
        //         'title' => 'Great American Apartments',
        //         'subtitle' => 'THE NOT SO LONG PROPERTY MANAGER NAME',
        //         'address' => '3045 Cumberland Woods Street, Suite 20',
        //         'city' => 'COLUMBUS',
        //         'state' => 'OH',
        //         'zip' => '43219',
        //         'total_buildings' => '10',
        //         'inspection_status' => 'action-needed',
        //         'inspection_status_text' => 'Inspection in progress',
        //         'inspection_schedule_date' => '12/21',
        //         'inspection_schedule_year' => '2018',
        //         'inspection_schedule_text' => 'Inspection in progress',
        //         'inspectable_items' => '10',
        //         'total_items' => '21',
        //         'audit_compliance_icon' => 'a-circle-checked',
        //         'audit_compliance_status' => 'ok-actionable', 
        //         'audit_compliance_status_text' => 'Audit Compliant',
        //         'followup_status' => 'ok-actionable',
        //         'followup_status_text' => 'No followups',
        //         'followup_date' => '12/10',
        //         'followup_year' => '2018',
        //         'file_audit_icon' => 'a-folder',
        //         'file_audit_status' => 'ok-actionable',
        //         'file_audit_status_text' => '',
        //         'nlt_audit_icon' => 'a-booboo',
        //         'nlt_audit_status' => 'action-required',
        //         'nlt_audit_status_text' => '',
        //         'lt_audit_icon' => 'a-skull',
        //         'lt_audit_status' => 'in-progress',
        //         'lt_audit_status_text' => '',
        //         'smoke_audit_icon' => 'a-flames',
        //         'smoke_audit_status' => 'action-needed',
        //         'smoke_audit_status_text' => '',
        //         'auditor_status_icon' => 'a-avatar',
        //         'auditor_status' => 'action-required',
        //         'auditor_status_text' => 'Auditors / schedule conflicts / unasigned items',
        //         'message_status_icon' => 'a-envelope-4',
        //         'message_status' => '',
        //         'message_status_text' => '',
        //         'document_status_icon' => 'a-files',
        //         'document_status' => '',
        //         'document_status_text' => 'Document status',
        //         'history_status_icon' => 'a-person-clock',
        //         'history_status' => '',
        //         'history_status_text' => 'NO/VIEW HISTORY',
        //         'step_status_icon' => 'a-calendar-7',
        //         'step_status' => 'no-action',
        //         'step_status_text' => ''
        //     ],
        //     [
        //         'id' => '789',
        //         'audit_id' => '19200116',
        //         'status' => 'action-needed',
        //         'lead' => [
        //                 'name' => 'Brian Greenwood',
        //                 'initials' => 'BG',
        //                 'color' => 'green',
        //                 'status' => 'warning'
        //             ],
        //         'title' => 'Great American Apartments',
        //         'subtitle' => 'THE NOT SO LONG PROPERTY MANAGER NAME',
        //         'address' => '3045 Cumberland Woods Street, Suite 20',
        //         'city' => 'COLUMBUS',
        //         'state' => 'OH',
        //         'zip' => '43219',
        //         'total_buildings' => '10',
        //         'inspection_status' => 'action-needed',
        //         'inspection_status_text' => 'Inspection in progress',
        //         'inspection_schedule_date' => '12/21',
        //         'inspection_schedule_year' => '2018',
        //         'inspection_schedule_text' => 'Inspection in progress',
        //         'inspectable_items' => '10',
        //         'total_items' => '21',
        //         'audit_compliance_icon' => 'a-circle-checked',
        //         'audit_compliance_status' => 'ok-actionable', 
        //         'audit_compliance_status_text' => 'Audit Compliant',
        //         'followup_status' => 'ok-actionable',
        //         'followup_status_text' => 'No followups',
        //         'followup_date' => '12/10',
        //         'followup_year' => '2018',
        //         'file_audit_icon' => 'a-folder',
        //         'file_audit_status' => 'ok-actionable',
        //         'file_audit_status_text' => '',
        //         'nlt_audit_icon' => 'a-booboo',
        //         'nlt_audit_status' => 'action-required',
        //         'nlt_audit_status_text' => '',
        //         'lt_audit_icon' => 'a-skull',
        //         'lt_audit_status' => 'in-progress',
        //         'lt_audit_status_text' => '',
        //         'smoke_audit_icon' => 'a-flames',
        //         'smoke_audit_status' => 'action-needed',
        //         'smoke_audit_status_text' => '',
        //         'auditor_status_icon' => 'a-avatar',
        //         'auditor_status' => 'action-required',
        //         'auditor_status_text' => 'Auditors / schedule conflicts / unasigned items',
        //         'message_status_icon' => 'a-envelope-4',
        //         'message_status' => '',
        //         'message_status_text' => '',
        //         'document_status_icon' => 'a-files',
        //         'document_status' => '',
        //         'document_status_text' => 'Document status',
        //         'history_status_icon' => 'a-person-clock',
        //         'history_status' => '',
        //         'history_status_text' => 'NO/VIEW HISTORY',
        //         'step_status_icon' => 'a-calendar-7',
        //         'step_status' => 'no-action',
        //         'step_status_text' => ''
        //     ],
        //     [
        //         'id' => '222',
        //         'audit_id' => '19200133',
        //         'status' => 'critical',
        //         'lead' => [
        //                 'name' => 'Brian Greenwood',
        //                 'initials' => 'BG',
        //                 'color' => 'green',
        //                 'status' => 'warning'
        //             ],
        //         'title' => 'Great American Apartments',
        //         'subtitle' => 'THE NOT SO LONG PROPERTY MANAGER NAME',
        //         'address' => '3045 Cumberland Woods Street, Suite 20',
        //         'city' => 'COLUMBUS',
        //         'state' => 'OH',
        //         'zip' => '43219',
        //         'total_buildings' => '10',
        //         'inspection_status' => 'action-needed',
        //         'inspection_status_text' => 'Inspection in progress',
        //         'inspection_schedule_date' => '12/21',
        //         'inspection_schedule_year' => '2018',
        //         'inspection_schedule_text' => 'Inspection in progress',
        //         'inspectable_items' => '10',
        //         'total_items' => '21',
        //         'audit_compliance_icon' => 'a-circle-checked',
        //         'audit_compliance_status' => 'ok-actionable', 
        //         'audit_compliance_status_text' => 'Audit Compliant',
        //         'followup_status' => 'ok-actionable',
        //         'followup_status_text' => 'No followups',
        //         'followup_date' => '12/10',
        //         'followup_year' => '2018',
        //         'file_audit_icon' => 'a-folder',
        //         'file_audit_status' => 'ok-actionable',
        //         'file_audit_status_text' => '',
        //         'nlt_audit_icon' => 'a-booboo',
        //         'nlt_audit_status' => 'action-required',
        //         'nlt_audit_status_text' => '',
        //         'lt_audit_icon' => 'a-skull',
        //         'lt_audit_status' => 'in-progress',
        //         'lt_audit_status_text' => '',
        //         'smoke_audit_icon' => 'a-flames',
        //         'smoke_audit_status' => 'action-needed',
        //         'smoke_audit_status_text' => '',
        //         'auditor_status_icon' => 'a-avatar',
        //         'auditor_status' => 'action-required',
        //         'auditor_status_text' => 'Auditors / schedule conflicts / unasigned items',
        //         'message_status_icon' => 'a-envelope-4',
        //         'message_status' => '',
        //         'message_status_text' => '',
        //         'document_status_icon' => 'a-files',
        //         'document_status' => '',
        //         'document_status_text' => 'Document status',
        //         'history_status_icon' => 'a-person-clock',
        //         'history_status' => '',
        //         'history_status_text' => 'NO/VIEW HISTORY',
        //         'step_status_icon' => 'a-calendar-7',
        //         'step_status' => 'no-action',
        //         'step_status_text' => ''
        //     ],
        //     [
        //         'id' => '445',
        //         'audit_id' => '19200234',
        //         'status' => 'ok-actionable',
        //         'lead' => [
        //                 'name' => 'Brian Greenwood',
        //                 'initials' => 'BG',
        //                 'color' => 'green',
        //                 'status' => 'warning'
        //             ],
        //         'title' => 'Great American Apartments',
        //         'subtitle' => 'THE NOT SO LONG PROPERTY MANAGER NAME',
        //         'address' => '3045 Cumberland Woods Street, Suite 20',
        //         'city' => 'COLUMBUS',
        //         'state' => 'OH',
        //         'zip' => '43219',
        //         'total_buildings' => '10',
        //         'inspection_status' => 'action-needed',
        //         'inspection_status_text' => 'Inspection in progress',
        //         'inspection_schedule_date' => '12/21',
        //         'inspection_schedule_year' => '2018',
        //         'inspection_schedule_text' => 'Inspection in progress',
        //         'inspectable_items' => '10',
        //         'total_items' => '21',
        //         'audit_compliance_icon' => 'a-circle-checked',
        //         'audit_compliance_status' => 'ok-actionable', 
        //         'audit_compliance_status_text' => 'Audit Compliant',
        //         'followup_status' => 'ok-actionable',
        //         'followup_status_text' => 'No followups',
        //         'followup_date' => '12/10',
        //         'followup_year' => '2018',
        //         'file_audit_icon' => 'a-folder',
        //         'file_audit_status' => 'ok-actionable',
        //         'file_audit_status_text' => '',
        //         'nlt_audit_icon' => 'a-booboo',
        //         'nlt_audit_status' => 'action-required',
        //         'nlt_audit_status_text' => '',
        //         'lt_audit_icon' => 'a-skull',
        //         'lt_audit_status' => 'in-progress',
        //         'lt_audit_status_text' => '',
        //         'smoke_audit_icon' => 'a-flames',
        //         'smoke_audit_status' => 'action-needed',
        //         'smoke_audit_status_text' => '',
        //         'auditor_status_icon' => 'a-avatar',
        //         'auditor_status' => 'action-required',
        //         'auditor_status_text' => 'Auditors / schedule conflicts / unasigned items',
        //         'message_status_icon' => 'a-envelope-4',
        //         'message_status' => '',
        //         'message_status_text' => '',
        //         'document_status_icon' => 'a-files',
        //         'document_status' => '',
        //         'document_status_text' => 'Document status',
        //         'history_status_icon' => 'a-person-clock',
        //         'history_status' => '',
        //         'history_status_text' => 'NO/VIEW HISTORY',
        //         'step_status_icon' => 'a-calendar-7',
        //         'step_status' => 'no-action',
        //         'step_status_text' => ''
        //     ],
        //     [
        //         'id' => '334',
        //         'audit_id' => '19200221',
        //         'status' => 'action-needed',
        //         'lead' => [
        //                 'name' => 'Brian Greenwood',
        //                 'initials' => 'BG',
        //                 'color' => 'green',
        //                 'status' => 'warning'
        //             ],
        //         'title' => 'Great American Apartments',
        //         'subtitle' => 'THE NOT SO LONG PROPERTY MANAGER NAME',
        //         'address' => '3045 Cumberland Woods Street, Suite 20',
        //         'city' => 'COLUMBUS',
        //         'state' => 'OH',
        //         'zip' => '43219',
        //         'total_buildings' => '10',
        //         'inspection_status' => 'action-needed',
        //         'inspection_status_text' => 'Inspection in progress',
        //         'inspection_schedule_date' => '12/21',
        //         'inspection_schedule_year' => '2018',
        //         'inspection_schedule_text' => 'Inspection in progress',
        //         'inspectable_items' => '10',
        //         'total_items' => '21',
        //         'audit_compliance_icon' => 'a-circle-checked',
        //         'audit_compliance_status' => 'ok-actionable', 
        //         'audit_compliance_status_text' => 'Audit Compliant',
        //         'followup_status' => 'ok-actionable',
        //         'followup_status_text' => 'No followups',
        //         'followup_date' => '12/10',
        //         'followup_year' => '2018',
        //         'file_audit_icon' => 'a-folder',
        //         'file_audit_status' => 'ok-actionable',
        //         'file_audit_status_text' => '',
        //         'nlt_audit_icon' => 'a-booboo',
        //         'nlt_audit_status' => 'action-required',
        //         'nlt_audit_status_text' => '',
        //         'lt_audit_icon' => 'a-skull',
        //         'lt_audit_status' => 'in-progress',
        //         'lt_audit_status_text' => '',
        //         'smoke_audit_icon' => 'a-flames',
        //         'smoke_audit_status' => 'action-needed',
        //         'smoke_audit_status_text' => '',
        //         'auditor_status_icon' => 'a-avatar',
        //         'auditor_status' => 'action-required',
        //         'auditor_status_text' => 'Auditors / schedule conflicts / unasigned items',
        //         'message_status_icon' => 'a-envelope-4',
        //         'message_status' => '',
        //         'message_status_text' => '',
        //         'document_status_icon' => 'a-files',
        //         'document_status' => '',
        //         'document_status_text' => 'Document status',
        //         'history_status_icon' => 'a-person-clock',
        //         'history_status' => '',
        //         'history_status_text' => 'NO/VIEW HISTORY',
        //         'step_status_icon' => 'a-calendar-7',
        //         'step_status' => 'no-action',
        //         'step_status_text' => ''
        //     ]
        // ]);

        return view('dashboard.audits', compact('filter', 'auditFilterMineOnly', 'audits', 'sort_by', 'sort_order'));
    }

    public function reports(Request $request)
    {
        
        //return \view('dashboard.index'); //, compact('user')
        return view('dashboard.reports');
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