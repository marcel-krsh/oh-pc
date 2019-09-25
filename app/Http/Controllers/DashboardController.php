<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CachedAudit;
use App\Models\CommunicationRecipient;
use App\Models\GuideStep;
use App\Models\Report;
use App\Models\User;
use Auth;
use Carbon;
use Illuminate\Http\Request;
use Session;
use View;

class DashboardController extends Controller
{
  public function __construct()
  {
    $this->middleware('allita.auth');
    // if (env('APP_DEBUG_NO_DEVCO') == 'true') {
    //   //Auth::onceUsingId(1); // TEST BRIAN
    //   //Auth::onceUsingId(286); // TEST BRIAN
    //   Auth::onceUsingId(env('USER_ID_IMPERSONATION'));

    //   // this is normally setup upon login
    //   $current_user = Auth::user();
    //   if (null === $current_user->socket_id) {
    //     // create a socket id and store in user table
    //     $token                   = str_random(10);
    //     $current_user->socket_id = $token;
    //     $current_user->save();
    //   }
    // $this->middleware(function ($request, $next) {
    //     $current_user = Auth::user();
		  //   $auditor_access = Auth::user()->auditor_access();
		  //   view::share('current_user');
		  //   view::share('auditor_access');

		  //   return $next($request);
    // });

    // view()->composer('*', function ($view) {
    //     $view->with('current_user', auth()->user());
    //     $view->with('auditor_access', auth()->user()->auditor_access());
    // });

  }

  public function login()
  {
    return "This feature has been replaced with a DevCo login. Please visit Devco Online to login.";
  }

  public function index(Request $request)
  {
    if (!Auth::check()) {
      return redirect()->to('login');
    }
    if ($request->query('tab') >= 1) {
      $tab       = "dash-subtab-" . intval($request->query('tab'));
      $showHowTo = 2;
    } else {
      // default tab to load
      $tab = "dash-subtab-1";
    }

    //// load the sitevisit tab instead
    $routed = \Route::getFacadeRoot()->current()->uri();
    if ("site_visit_manager" == $routed) {
      // Give instruction on steps to take for a approved POs.
      $loadDetailTab = 2;
    } else {
      $loadDetailTab = 1;
    }

    $current_user = Auth::user();

    $tab = "detail-tab-1";
    //$model_source = null;
    if (session()->has('notification_main_tab')) {
      $tab = session()->pull('notification_main_tab', $tab);
      //$model_source = session()->pull('notification_modal_source', null);
    }

    $stats_audits_total = CachedAudit::count(); // all?

    $stats_communication_total = CommunicationRecipient::where('user_id', $current_user->id)
      ->where('seen', 0)
      ->count();

    $stats_reports_total = Report::where('user_id', '=', Auth::user()->id)->count();
    //return \view('dashboard.index'); //, compact('user')
    return view('dashboard.index', compact('tab', 'loadDetailTab', 'stats_audits_total', 'stats_communication_total', 'stats_reports_total', 'current_user'));
  }

  public function adminTools()
  {

    if (Auth::user()->admin_access()) {
      $sumStatData = [];
      $stats       = [];
      return view('dashboard.admin', compact('stats', 'sumStatData'));
    } else {
      return 'Sorry you do not have access to this page.';
    }
  }

  public function oldAudits(Request $request, $page = 0)
  {

        // TEST EVENT
        // $testaudit = Audit::where('development_key','=', 247660)->where('monitoring_status_type_key', '=', 4)->orderBy('start_date','desc')->first();
        // Event::fire('audit.created', $testaudit);

        // $request will contain filters
        // $auditFilterMineOnly
        // $auditFilterMineOnly

        $filter    = $request->get('filter');
        $filter_id = $request->get('filterId');

        if (session()->has('audit-sort-by')) {
          $sort_by = session('audit-sort-by');

          if (session()->has('audit-sort-order') && session('audit-sort-order') != 'undefined') {
            $sort_order = session('audit-sort-order');
          } else {
            session(['audit-sort-order', 0]);
            $sort_order = 0;
          }
        } else {
          session(['audit-sort-by', 'audit-sort-project']);
          $sort_by = 'audit-sort-project';

          session(['audit-sort-order', 1]);
          $sort_order = 1;
        }

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

        if ($sort_order) {
          $sort_order_query = "asc";
        } else {
          $sort_order_query = "desc";
        }

        $audits = CachedAudit::with('auditors');

        if(session()->has('audit-my-audits') && session('audit-my-audits') == 1) {
            $auditFilterMineOnly = 1;
            $current_user_id = Auth::user()->id;
            $audits = $audits->where(function ($query) use ( $current_user_id ){
                            $query->where('lead','=',$current_user_id)
                                    ->orWhereHas('auditors', function( $query2 ) use ( $current_user_id ){
                                        $query2->where('user_id', '=', $current_user_id );
                                    });
                        });
        }else{
            $auditFilterMineOnly = 0;
        }

        if(session()->has('filter-search-project')){
            $auditFilterProjectId = session('filter-search-project');
            $audits = $audits->where(function ($query) use ( $auditFilterProjectId ){
                            $query->where('audit_id','like', '%'.$auditFilterProjectId.'%')
                                    ->orWhere('project_ref','like', '%'.$auditFilterProjectId.'%');
                        });
        }else{
            $auditFilterProjectId = '';
        }

        if(session()->has('filter-search-pm')){
            $auditFilterProjectName = session('filter-search-pm');
            $audits = $audits->where(function ($query) use ( $auditFilterProjectName ){
                            $query->where('title','like', '%'.$auditFilterProjectName.'%')
                                    ->orWhere('pm','like', '%'.$auditFilterProjectName.'%');
                        });
        }else{
            $auditFilterProjectName = 0;
        }

        if(session()->has('file-audit-status-h') && session('file-audit-status-h') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('files');
                            });
        }

        if(session()->has('file-audit-status-r') && session('file-audit-status-r') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('files', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                });
                            });
        }

        if(session()->has('file-audit-status-ar') && session('file-audit-status-ar') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('files', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                    $query->where('pm_submitted_resolution', '<', 'auditor_approved_resolution' );
                                });
                            });
        }

        if(session()->has('file-audit-status-c') && session('file-audit-status-c') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('files', function( $query ) {
                                    $query->whereHas('followups', function( $query ) {
                                        $query->whereDate('date_due','<=', \Carbon\Carbon::today()->addHours(24))->whereDate('date_due','>=',\Carbon\Carbon::today());
                                    });
                                });
                            });
        }

        if(session()->has('file-audit-status-nf') && session('file-audit-status-nf') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereDoesntHave('files');
                            });
        }


        if(session()->has('nlt-audit-status-h') && session('nlt-audit-status-h') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('nlts');
                            });
        }

        if(session()->has('nlt-audit-status-r') && session('nlt-audit-status-r') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('nlts', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                });
                            });
        }

        if(session()->has('nlt-audit-status-ar') && session('nlt-audit-status-ar') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('nlts', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                    $query->where('pm_submitted_resolution', '<', 'auditor_approved_resolution' );
                                });
                            });
        }

        if(session()->has('nlt-audit-status-c') && session('nlt-audit-status-c') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('nlts', function( $query ) {
                                    $query->whereHas('followups', function( $query ) {
                                        $query->whereDate('date_due','<=', \Carbon\Carbon::today()->addHours(24))->whereDate('date_due','>=',\Carbon\Carbon::today());
                                    });
                                });
                            });
        }

        if(session()->has('nlt-audit-status-nf') && session('nlt-audit-status-nf') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereDoesntHave('nlts');
                            });
        }

        if(session()->has('lt-audit-status-h') && session('lt-audit-status-h') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('lts');
                            });
        }

        if(session()->has('lt-audit-status-r') && session('lt-audit-status-r') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('lts', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                });
                            });
        }

        if(session()->has('lt-audit-status-ar') && session('lt-audit-status-ar') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('lts', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                    $query->where('pm_submitted_resolution', '<', 'auditor_approved_resolution' );
                                });
                            });
        }

        if(session()->has('lt-audit-status-c') && session('lt-audit-status-c') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('lts', function( $query ) {
                                    $query->whereHas('followups', function( $query ) {
                                        $query->whereDate('date_due','<=', \Carbon\Carbon::today()->addHours(24))->whereDate('date_due','>=',\Carbon\Carbon::today());
                                    });
                                });
                            });
        }

        if(session()->has('lt-audit-status-nf') && session('lt-audit-status-nf') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereDoesntHave('lts');
                            });
        }

        if(session()->has('filter-search-address') && session('filter-search-address') != ''){
            $auditFilterAddress = session('filter-search-address');
            $audits = $audits->where(function ($query) use ( $auditFilterAddress ){
                            $query->where('address','like', '%'.$auditFilterAddress.'%')
                                    ->orWhere('city','like', '%'.$auditFilterAddress.'%')
                                    ->orWhere('state','like', '%'.$auditFilterAddress.'%')
                                    ->orWhere('zip','like', '%'.$auditFilterAddress.'%');
                        });
        }else{
            $auditFilterAddress = '';
        }

        if(session()->has('total_inspection_amount') && session('total_inspection_amount') > 0){

            $total_inspection_amount = session('total_inspection_amount');

            if(session('total_inspection_filter') != 1){

                $auditFilterInspection = "MORE THAN ".$total_inspection_amount." INSPECTABLE ITEMS";
                $audits = $audits->where('inspectable_items', '>=', $total_inspection_amount);
            }else{

                $auditFilterInspection = "LESS THAN ".$total_inspection_amount." INSPECTABLE ITEMS";
                $audits = $audits->where('inspectable_items', '<=', $total_inspection_amount);
            }
        }else{
            session(['total_inspection_amount' => 0]);
            session(['total_inspection_filter' => 0]);
            $auditFilterInspection = "";
        }

        if(session()->has('compliance-status-all') && session('compliance-status-all') != 0){
            $auditFilterComplianceRR = 0;
            $auditFilterComplianceNC = 0;
            $auditFilterComplianceC = 0;
        }else{
            $auditFilterComplianceRR = session('compliance-status-rr');
            $auditFilterComplianceNC = session('compliance-status-nc');
            $auditFilterComplianceC = session('compliance-status-c');

            $audits = $audits->where(function ($query) use ( $auditFilterComplianceRR, $auditFilterComplianceNC, $auditFilterComplianceC ){
                            if(session()->has('compliance-status-rr') && session('compliance-status-rr') != 0){

                                $query->OrWhere('audit_compliance_status_text', '=', 'UNITS REQUIRE REVIEW');
                            }
                            if(session()->has('compliance-status-nc') && session('compliance-status-nc') != 0){

                                $query->OrWhere('audit_compliance_status_text', '=', 'AUDIT NOT COMPLIANT');
                            }
                            if(session()->has('compliance-status-c') && session('compliance-status-c') != 0){

                                $query->OrWhere('audit_compliance_status_text', '=', 'AUDIT COMPLIANT');
                            }

                        });
        }

        if(session('schedule_assignment_unassigned') == 1){
            $audits = $audits->whereHas('inspection_items', function( $query ) {
                            $query->whereNull('auditor_id');
                        });
        }

        if(session('schedule_assignment_not_enough') == 1){
            $audits = $audits->whereDate('estimated_time_needed', '>', 0);
        }

        if(session('schedule_assignment_too_many') == 1){
            $audits = $audits->whereDate('estimated_time_needed', '=', 0)->orWhereNull('estimated_time_needed');
        }

        // load to list steps filtering and check for session variables
        $steps = GuideStep::where('guide_step_type_id', '=', 1)->orderBy('order', 'asc')->get();

        foreach ($steps as $step) {
          // for each step, check for filter in session variable
          if (session()->has($step->session_name) && session($step->session_name) == 1) {
            $audits = $audits->orWhere('step_id', '=', $step->id);
          }
        }
        $audits = $audits->orderBy($sort_by_field, $sort_order_query)->get();

        $data = [];

        $audits_to_remove = []; // ids of audits to remove after filtering by auditor
        $auditors_array   = [];
        $auditor_ids      = []; // to prevent duplicates when building the auditors list

        foreach ($audits as $audit) {
            // list all auditors based on previous filters
            // if($audit->update_cached_audit()){
            //     //refreshed - update values.
            //     $audit->refresh();
            // }
            if($audit->auditors && count($audit->auditors)){

                $keep_audit_based_on_auditor_filter = 0;
                foreach($audit->auditors as $auditor){
                    if(!in_array($auditor->user_id, $auditor_ids)){
                        $auditor_ids[] = $auditor->user_id;
                        $auditors_array[] = [ "user_id" => $auditor->user_id, "name" => strtoupper($auditor->user->full_name())];
                    }

                    if(session()->has('assignment-auditor') && is_array(session('assignment-auditor'))){
                        // there is a filter to select audits with specific auditors
                        if(in_array($auditor->user_id, session('assignment-auditor'))){
                            // the auditor is in this audit, we keep it
                            $keep_audit_based_on_auditor_filter = 1;
                        }
                    }else{
                        // no auditor filters, we keep all audits
                        $keep_audit_based_on_auditor_filter = 1;
                    }

                }

                if(!$keep_audit_based_on_auditor_filter){
                    $audits_to_remove[] = $audit->id;
                }
            }
        }

        $filtered_audits = $audits->reject(function ($value, $key) use ($audits_to_remove) {
          return in_array($value->id, $audits_to_remove);
        });

        $audits = $filtered_audits->all();

        foreach ($audits as $audit) {

          if ('critical' == $audit['status'] && Auth::user()->auditor_access()) {
            $notcritical = 'critical';
          } else {
            $notcritical = 'notcritical';
          }

          if (session('audit-hidenoncritical') == 1 && 'critical' != $audit['status']) {
            $display = 'none';
          } else {
            $display = 'table-row';
          }

          if ($audit->lead_json) {
            $lead_name     = $audit->lead_json->name;
            $lead_color    = $audit->lead_json->color;
            $lead_initials = $audit->lead_json->initials;
          } else {
            $lead_name     = '';
            $lead_color    = '';
            $lead_initials = '';
          }

          $pm = strtoupper($audit['pm']);
          if ($audit['inspection_schedule_date']) {
            $inspectionScheduleDate     = \Carbon\Carbon::createFromFormat('Y-m-d', $audit['inspection_schedule_date'])->format('m/d');
            $inspectionScheduleDateYear = \Carbon\Carbon::createFromFormat('Y-m-d', $audit['inspection_schedule_date'])->format('Y');
          } else {
            $inspectionScheduleDate     = null;
            $inspectionScheduleDateYear = null;
          }

          if ($audit['followup_date']) {
            $followupDate     = \Carbon\Carbon::createFromFormat('Y-m-d', $audit['followup_date'])->format('m/d');
            $followupDateYear = \Carbon\Carbon::createFromFormat('Y-m-d', $audit['followup_date'])->format('Y');
          } else {
            $followupDate     = null;
            $followupDateYear = null;
          }

          if (!Auth::user()->auditor_access()) {
            // PM - blank out some values
            $audit['inspection_status']       = '';
            $inspectionScheduleIcon           = '';
            $tooltipInspectionSchedule        = '';
            $tooltipInspectableItems          = '';
            $tooltipInspectionStatus          = '';
            $audit['inspectable_items']       = '';
            $audit['total_items']             = '';
            $audit['audit_compliance_icon']   = '';
            $audit['audit_compliance_status'] = '';
            $tooltipComplianceStatus          = '';

            $audit['auditor_status_icon'] = '';
            $audit['auditor_status']      = '';
            $auditorTooltip               = '';
            $audit['history_status_icon'] = '';
            $audit['history_status']      = '';
            $historyStatusText            = '';
            $audit['step_status_icon']    = '';
            $audit['step_status']         = '';
            $stepStatusText               = '';
          } elseif (Auth::user()->auditor_access()) {

            // to change existing records (tooltip wording)
            if($audit['inspection_schedule_text'] == 'CLICK TO SCHEDULE AUDIT') {
                $audit['inspection_schedule_text'] = 'SCHEDULED AUDITS/TOTAL AUDITS';
            }

            $inpectionScheduleIcon     = 'a-calendar-7';
            $inspectionScheduleIcon    = ''; // @todo need to put in icon here
            $tooltipInspectionSchedule = 'title:' . $audit['inspection_schedule_text'];
            $tooltipInspectableItems   = 'title:' . $audit['inspectable_items'] . ' INSPECTABLE ITEMS;';
            $tooltipInspectionStatus   = 'title:' . $audit['inspection_status_text'];
            $tooltipComplianceStatus   = 'title:' . $audit['audit_compliance_status_text'];
            $auditorTooltip            = 'title:' . $audit['auditor_status_text'] . ';';
            $historyStatusText         = 'title:' . $audit['history_status_text'] . ';';
            $stepStatusText            = 'title:' . $audit['step_status_text'] . ';';
          }

          $data[] = [
            'id'                            => $audit->id,
            'auditId'                       => $audit->audit_id,
            'title'                         => $audit->title,
            'notcritical'                   => $notcritical,
            'display'                       => $display,
            'tooltipLead'                   => 'pos:top-left;title:' . $lead_name . ';',
            'userBadgeColor'                => 'user-badge-' . $lead_color,
            'initials'                      => $lead_initials,
            'total_buildings'               => $audit['total_buildings'],
            'projectId'                     => $audit['project_id'],
            'projectKey'                    => $audit['project_key'],
            'projectRef'                    => $audit['project_ref'],
            'pm'                            => $pm,
            'address'                       => $audit['address'],
            'address2'                      => $audit['city'] . ', ' . $audit['state'] . ' ' . $audit['zip'],
            'inspectionStatus'              => $audit['inspection_status'],
            'tooltipInspectionStatus'       => $tooltipInspectionStatus,
            'tooltipInspectionSchedule'     => $tooltipInspectionSchedule,
            'tooltipInspectionScheduleIcon' => $inspectionScheduleIcon,
            'inspectionScheduleDate'        => $inspectionScheduleDate,
            'inspectionScheduleDateYear'    => $inspectionScheduleDateYear,
            'tooltipInspectableItems'       => $tooltipInspectableItems,
            'inspectableItems'              => $audit['inspectable_items'],
            'totalItems'                    => $audit['total_items'],
            'complianceIconClass'           => $audit['audit_compliance_icon'],
            'complianceStatusClass'         => $audit['audit_compliance_status'],
            'tooltipComplianceStatus'       => $tooltipComplianceStatus,

            'followupStatusClass'           => $audit['followup_status'],
            'tooltipFollowupStatus'         => 'title:' . $audit['followup_status_text'],
            'followupDate'                  => $followupDate,
            'followupDateYear'              => $followupDateYear,
            'fileAuditStatusClass'          => $audit['file_audit_status'],
            'tooltipFileAuditStatus'        => 'title:' . $audit['file_audit_status_text'],
            'fileAuditIconClass'            => $audit['file_audit_icon'],
            'nltAuditStatusClass'           => $audit['nlt_audit_status'],
            'tooltipNltAuditStatus'         => 'title:' . $audit['nlt_audit_status_text'],
            'nltAuditIconClass'             => $audit['nlt_audit_icon'],
            'ltAuditStatusClass'            => $audit['lt_audit_status'],
            'tooltipLtAuditStatus'          => 'title:' . $audit['lt_audit_status_text'],
            'ltAuditIconClass'              => $audit['lt_audit_icon'],

            'auditorStatusIconClass'        => $audit['auditor_status_icon'],
            'auditorStatusClass'            => $audit['auditor_status'],
            'tooltipAuditorStatus'          => $auditorTooltip,
            'messageStatusIconClass'        => $audit['message_status_icon'],
            'messageStatusClass'            => $audit['message_status'],
            'tooltipMessageStatus'          => 'title:' . $audit['message_status_text'] . ';',
            'documentStatusIconClass'       => $audit['document_status_icon'],
            'documentStatusClass'           => $audit['document_status'],
            'tooltipDocumentStatus'         => 'title:' . $audit['document_status_text'] . ';',
            'historyStatusIconClass'        => $audit['history_status_icon'],
            'historyStatusClass'            => $audit['history_status'],
            'tooltipHistoryStatus'          => $historyStatusText,
            'stepStatusIconClass'           => $audit['step_status_icon'],
            'stepStatusClass'               => $audit['step_status'],
            'tooltipStepStatus'             => $stepStatusText,
            'auditor_access'                => Auth::user()->auditor_access(),
          ];
        }

        $auditor_names = [];
        foreach ($auditors_array as $key => $row) {
          $auditor_names[$key] = $row['name'];
        }
        array_multisort($auditor_names, SORT_ASC, $auditors_array);

        if ($page > 0) {
          return response()->json($data);
        } else {
          return view('dashboard.audits', compact('data', 'filter', 'auditFilterMineOnly', 'auditFilterProjectId', 'auditFilterProjectName', 'auditFilterAddress', 'auditFilterComplianceALL', 'auditFilterComplianceRR', 'auditFilterComplianceNC', 'auditFilterComplianceC', 'auditFilterInspection', 'auditors_array', 'audits', 'sort_by', 'sort_order', 'steps'));
        }
  }
  public function audits(Request $request, $page = 0)
  {
  	ini_set('max_execution_time', 1800); //3 minutes

        // TEST EVENT
        // $testaudit = Audit::where('development_key','=', 247660)->where('monitoring_status_type_key', '=', 4)->orderBy('start_date','desc')->first();
        // Event::fire('audit.created', $testaudit);

        // $request will contain filters
        // $auditFilterMineOnly
        // $auditFilterMineOnly
  	// return $request->all();
        $filter    = $request->get('filter');
        $filter_id = $request->get('filterId');

        if (session()->has('audit-sort-by')) {
          $sort_by = session('audit-sort-by');

          if (session()->has('audit-sort-order') && session('audit-sort-order') != 'undefined') {
            $sort_order = session('audit-sort-order');
          } else {
            session(['audit-sort-order', 0]);
            $sort_order = 0;
          }
        } else {
          session(['audit-sort-by', 'audit-sort-project']);
          $sort_by = 'audit-sort-project';

          session(['audit-sort-order', 1]);
          $sort_order = 1;
        }

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

        if ($sort_order) {
          $sort_order_query = "asc";
        } else {
          $sort_order_query = "desc";
        }

        $audits = CachedAudit::with('auditors');

        if(!session()->has('first_load')){
            // my default we load only their audits.
            session(['first_load'=>1]);
            session(['audit-my-audits' => 1]);
        }
        if(session()->has('audit-my-audits') && session('audit-my-audits') == 1) {
            $auditFilterMineOnly = 1;
            $current_user_id = Auth::user()->id;
            $audits = $audits->where(function ($query) use ( $current_user_id ){
                            $query->where('lead','=',$current_user_id)
                                    ->orWhereHas('auditors', function( $query2 ) use ( $current_user_id ){
                                        $query2->where('user_id', '=', $current_user_id );
                                    });
                        });
        }else{
            $auditFilterMineOnly = 0;
        }

        if(session()->has('filter-search-project')){
            $auditFilterProjectId = session('filter-search-project');
            $audits = $audits->where(function ($query) use ( $auditFilterProjectId ){
                            $query->where('audit_id','like', '%'.$auditFilterProjectId.'%')
                                    ->orWhere('project_ref','like', '%'.$auditFilterProjectId.'%');
                        });
        }else{
            $auditFilterProjectId = '';
        }

        if(session()->has('filter-search-pm')){
            $auditFilterProjectName = session('filter-search-pm');
            $audits = $audits->where(function ($query) use ( $auditFilterProjectName ){
                            $query->where('title','like', '%'.$auditFilterProjectName.'%')
                                    ->orWhere('pm','like', '%'.$auditFilterProjectName.'%');
                        });
        }else{
            $auditFilterProjectName = 0;
        }

        if(session()->has('file-audit-status-h') && session('file-audit-status-h') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('files');
                            });
        }

        if(session()->has('file-audit-status-r') && session('file-audit-status-r') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('files', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                });
                            });
        }

        if(session()->has('file-audit-status-ar') && session('file-audit-status-ar') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('files', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                    $query->where('pm_submitted_resolution', '<', 'auditor_approved_resolution' );
                                });
                            });
        }

        if(session()->has('file-audit-status-c') && session('file-audit-status-c') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('files', function( $query ) {
                                    $query->whereHas('followups', function( $query ) {
                                        $query->whereDate('date_due','<=', \Carbon\Carbon::today()->addHours(24))->whereDate('date_due','>=',\Carbon\Carbon::today());
                                    });
                                });
                            });
        }

        if(session()->has('file-audit-status-nf') && session('file-audit-status-nf') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereDoesntHave('files');
                            });
        }


        if(session()->has('nlt-audit-status-h') && session('nlt-audit-status-h') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('nlts');
                            });
        }

        if(session()->has('nlt-audit-status-r') && session('nlt-audit-status-r') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('nlts', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                });
                            });
        }

        if(session()->has('nlt-audit-status-ar') && session('nlt-audit-status-ar') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('nlts', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                    $query->where('pm_submitted_resolution', '<', 'auditor_approved_resolution' );
                                });
                            });
        }

        if(session()->has('nlt-audit-status-c') && session('nlt-audit-status-c') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('nlts', function( $query ) {
                                    $query->whereHas('followups', function( $query ) {
                                        $query->whereDate('date_due','<=', \Carbon\Carbon::today()->addHours(24))->whereDate('date_due','>=',\Carbon\Carbon::today());
                                    });
                                });
                            });
        }

        if(session()->has('nlt-audit-status-nf') && session('nlt-audit-status-nf') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereDoesntHave('nlts');
                            });
        }

        if(session()->has('lt-audit-status-h') && session('lt-audit-status-h') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('lts');
                            });
        }

        if(session()->has('lt-audit-status-r') && session('lt-audit-status-r') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('lts', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                });
                            });
        }

        if(session()->has('lt-audit-status-ar') && session('lt-audit-status-ar') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('lts', function( $query ) {
                                    $query->where('auditor_approved_resolution', '>=', 0 );
                                    $query->where('pm_submitted_resolution', '<', 'auditor_approved_resolution' );
                                });
                            });
        }

        if(session()->has('lt-audit-status-c') && session('lt-audit-status-c') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereHas('lts', function( $query ) {
                                    $query->whereHas('followups', function( $query ) {
                                        $query->whereDate('date_due','<=', \Carbon\Carbon::today()->addHours(24))->whereDate('date_due','>=',\Carbon\Carbon::today());
                                    });
                                });
                            });
        }

        if(session()->has('lt-audit-status-nf') && session('lt-audit-status-nf') == 1){
            $audits = $audits->whereHas('audit', function( $query ) {
                                $query->whereDoesntHave('lts');
                            });
        }

        if(session()->has('filter-search-address') && session('filter-search-address') != ''){
            $auditFilterAddress = session('filter-search-address');
            $audits = $audits->where(function ($query) use ( $auditFilterAddress ){
                            $query->where('address','like', '%'.$auditFilterAddress.'%')
                                    ->orWhere('city','like', '%'.$auditFilterAddress.'%')
                                    ->orWhere('state','like', '%'.$auditFilterAddress.'%')
                                    ->orWhere('zip','like', '%'.$auditFilterAddress.'%');
                        });
        }else{
            $auditFilterAddress = '';
        }

        if(session()->has('total_inspection_amount') && session('total_inspection_amount') > 0){

            $total_inspection_amount = session('total_inspection_amount');

            if(session('total_inspection_filter') != 1){

                $auditFilterInspection = "MORE THAN ".$total_inspection_amount." INSPECTABLE ITEMS";
                $audits = $audits->where('inspectable_items', '>=', $total_inspection_amount);
            }else{

                $auditFilterInspection = "LESS THAN ".$total_inspection_amount." INSPECTABLE ITEMS";
                $audits = $audits->where('inspectable_items', '<=', $total_inspection_amount);
            }
        }else{
            session(['total_inspection_amount' => 0]);
            session(['total_inspection_filter' => 0]);
            $auditFilterInspection = "";
        }
                // return session()->all();


        if(session()->has('compliance-status-all') && session('compliance-status-all') != 0){
            $auditFilterComplianceRR = 0;
            $auditFilterComplianceNC = 0;
            $auditFilterComplianceC = 0;
        }else{
            $auditFilterComplianceRR = session('compliance-status-rr');
            $auditFilterComplianceNC = session('compliance-status-nc');
            $auditFilterComplianceC = session('compliance-status-c');

            $audits = $audits->where(function ($query) use ( $auditFilterComplianceRR, $auditFilterComplianceNC, $auditFilterComplianceC ){
                            if(session()->has('compliance-status-rr') && session('compliance-status-rr') != 0){

                                $query->OrWhere('audit_compliance_status_text', '=', 'UNITS REQUIRE REVIEW');
                            }
                            if(session()->has('compliance-status-nc') && session('compliance-status-nc') != 0){

                                $query->OrWhere('audit_compliance_status_text', '=', 'AUDIT NOT COMPLIANT');
                            }
                            if(session()->has('compliance-status-c') && session('compliance-status-c') != 0){

                                $query->OrWhere('audit_compliance_status_text', '=', 'AUDIT COMPLIANT');
                            }

                        });
        }

        if(session('schedule_assignment_unassigned') == 1){
            $audits = $audits->whereHas('inspection_items', function( $query ) {
                            $query->whereNull('auditor_id');
                        });
        }

        if(session('schedule_assignment_not_enough') == 1){
            $audits = $audits->whereDate('estimated_time_needed', '>', 0);
        }

        if(session('schedule_assignment_too_many') == 1){
            $audits = $audits->whereDate('estimated_time_needed', '=', 0)->orWhereNull('estimated_time_needed');
        }
        if(session()->has('assignment-auditor') && is_array(session('assignment-auditor'))){
            //$audits = $audits->whereIn('lead', session('assignment-auditor'));
            $audits = $audits->where(function ($query) {
                            $query->whereIn('lead',session('assignment-auditor'))
                                    ->orWhereHas('auditors', function( $query2 ){
                                        $query2->whereIn('user_id', session('assignment-auditor') );
                                    });
                                });
        }
                // return $audits->whereNotNull('car_status')->get();


        // load to list steps filtering and check for session variables
        $steps = GuideStep::where('guide_step_type_id', '=', 1)->orderBy('order', 'asc')->get();
        $multi = 0;
        foreach ($steps as $step) {
          // for each step, check for filter in session variable
          if (session()->has($step->session_name) && session($step->session_name) == 1) {
          	if($multi == 0) {
          		$audits = $audits->where('step_id', '=', $step->id);
          		$multi = 1;
          	} else {
          		$audits = $audits->orWhere('step_id', '=', $step->id);
          	}
          }
        }

        $report_config = config('allita.reports');
        $multi = 0;
        foreach ($report_config['car_status'] as $key => $value) {
        	if (session()->has($key) && session($key) == 1) {
        		if(session('car-report-selection-all') != 1) {
        			if($multi == 0) {
	          		//logic to check car report
	          		$multi = 1;
	          	} else {
	          		//logic to check car report
	          	}
        		}
          }
        }
        // $audits = $audits->get();

        $audits = $audits->with('audit.findings', 'audit.unique_unit_inspections')->orderBy($sort_by_field, $sort_order_query)->get()
            ->map(function ($audit) {
                if($audit->inspection_schedule_text == 'CLICK TO SCHEDULE AUDIT'){
                    $audit->inspection_schedule_text = 'SCHEDULED AUDITS/TOTAL AUDITS';
                }

                return $audit;
            });
	        if(session()->has('total_building_inspection_amount') && session('total_building_inspection_amount') > 0){

	            $total_building_inspection_amount = session('total_building_inspection_amount');

	            if(session('total_building_inspection_filter') != 1){

	                $auditBuildingFilterInspection = "MORE THAN OR EQUAL TO ".$total_building_inspection_amount." INSPECTABLE BUILDINGS";
	                $audits = $audits->where('total_buildings', '>=', $total_building_inspection_amount);
	            }else{

	                $auditBuildingFilterInspection = "LESS THAN OR EQUAL TO ".$total_building_inspection_amount." INSPECTABLE BUILDINGS";
	                $audits = $audits->where('total_buildings', '<=', $total_building_inspection_amount);
	            }
	        }else{
	            session(['total_building_inspection_amount' => 0]);
	            session(['total_building_inspection_filter' => 0]);
	            $auditBuildingFilterInspection = "";
	        }
        // $audits = $audits->take(5);
        $data = [];

        $audits_to_remove = []; // ids of audits to remove after filtering by auditor
        $auditors_array   = [];
        $auditor_ids      = []; // to prevent duplicates when building the auditors list


        $auditors = User::join('users_roles','user_id','id')->select('users.id','users.name')->where('users.active',1)->where('role_id','>',1)->where('role_id','<',4)->orderBy('name')->get()->all();
            // $auditor_names = [];
            // foreach ($auditors_array as $auditor) {
            //   $auditor_names[$auditor->id] = $auditor->name;
            // }
            // array_multisort($auditor_names, SORT_ASC, $auditors_array);
        $current_user = Auth::user();
        $auditor_access = Auth::user()->auditor_access();
        if ($page > 0) {
          return response()->json($data);
        } else {
          return view('dashboard.audits', compact('data', 'filter', 'auditFilterMineOnly', 'auditFilterProjectId', 'auditFilterProjectName', 'auditFilterAddress', 'auditFilterComplianceALL', 'auditFilterComplianceRR', 'auditFilterComplianceNC', 'auditFilterComplianceC', 'auditFilterInspection', 'auditBuildingFilterInspection', 'auditors', 'audits', 'sort_by', 'sort_order', 'steps', 'current_user', 'auditor_access', 'report_config'));
        }
  }

  public function reports(Request $request)
  {
    $reports = null;
    //return \view('dashboard.index'); //, compact('user')
    return view('dashboard.reports', compact('reports'));
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

    $projects = CachedAudit::select('address', 'city', 'zip', 'project_id', 'title', 'state', 'audit_id', 'pm', 'project_ref', 'project_key')->where(function ($q) use ($request) {
      //$request = Request::input();
      $q->where('project_ref', 'LIKE', '%' . $request->search . '%')
        ->orWhere('project_key', 'like', '%' . $request->search . '%')
        ->orWhere('audit_id', 'like', '%' . $request->search . '%')
        ->orWhere('address', 'like', '%' . $request->search . '%')
        ->orWhere('title', 'like', '%' . $request->search . '%')
        ->orWhere('pm', 'like', '%' . $request->search . '%')
        ->orWhere('zip', 'like', '%' . $request->search . '%');
    })->take(20)->get()->all();
    //Project Id Audit id Main address Property Manager Name Project Name
    $i       = 0;
    $results = [];
    foreach ($projects as $data) {
      //$audits[$i]->created_at_formatted = date('n/j/y \a\t g:h a', strtotime($data->created_at));
      $results[] = [
        $data->address,
        $data->city,
        $data->state,
        $data->zip,
        $data->title,
        $data->audit_id,
        $data->pm,
        $data->project_ref,
        $data->project_key,
      ];
    }

    // // search for primary address (project), project #, audit#
    // $results[] = [
    //                 '123 Street Name',
    //                 'City Name',
    //                 'OH',
    //                 '123456',
    //                 '654322',
    //                 'Bob Manager',
    //                 'Project Name'
    //             ];

    // $results[] = [
    //                 '456 Street Name',
    //                 'City 2 Name',
    //                 'OH',
    //                 '789',
    //                 '987',
    //                 'John Manager',
    //                 'Project Name 2'
    //             ];

    // $results[] = [
    //                 '456 Street Name',
    //                 'City 2 Name',
    //                 'OH',
    //                 '789',
    //                 '987',
    //                 'John Manager',
    //                 'Project Name 2'
    //             ];

    // $results[] = [
    //                 '456 Street Name',
    //                 'City 2 Name',
    //                 'OH',
    //                 '789',
    //                 '987',
    //                 'John Manager',
    //                 'Project Name 2'
    //             ];

    $results = json_encode($results);
    return $results;
  }
}
