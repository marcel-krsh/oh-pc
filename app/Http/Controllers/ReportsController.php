<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\People;
use App\Models\CachedAudit;
use App\Models\CrrApprovalType;
use App\Models\CrrPart;
use App\Models\CrrPartOrder;
use App\Models\CrrReport;
use App\Models\CrrSection;
use App\Models\CrrSectionOrder;
use App\Models\Project;
use App\Models\ProjectProgram;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ReportsController extends Controller
{
  public function __construct(Request $request)
  {
    // $this->middleware('auth');
  }

  public function reportHistory(CrrReport $report, $data)
  {
    $reportHistory = $report->report_history;
    //$reportHistory[] = $data;
    if (!is_null($reportHistory)) {
      // put newest entry at the beginning - solves issue of same moment entries going out of order on sorts.
      //dd($data);
      array_unshift($reportHistory, $data);
      //dd($reportHistory, $report->report_hitory);
    } else {
      // this is the first piece of history - proud moment :)
      $reportHistory[] = $data;
    }
    $report->report_history  = $reportHistory;
    $report->last_updated_by = Auth::user()->id;
    $report->save();
  }

  public function dueDate($data)
  {
    // check permissions
    if (Auth::user()->can('access_auditor')) {
      //2019-03-20 14:41:55
      $dateY = substr($data['due'], 0, 4);
      $dateM = substr($data['due'], 4, 2);
      $dateD = substr($data['due'], 6, 2);
      // Get the Report
      $report = CrrReport::find(intval($data['id']));
      // Record old Values that will be updated for history
      if (!is_null($report)) {
        if ($report->response_due_date) {
          $oldDate = date('M d, Y', strtotime($report->response_due_date));
        } else {
          $oldDate = 'Not Set';
        }

        $updated = $report->update(['response_due_date' => $dateY . '-' . $dateM . '-' . $dateD . ' 18:00:00']);
        if ($updated) {
          // Record Historical Record.
          $history = ['date' => date('m-d-Y g:i a'), 'user_id' => Auth::user()->id, 'user_name' => Auth::user()->full_name(), 'note' => 'Updated due date from ' . $oldDate . ' to ' . date('M d, Y', strtotime($report->response_due_date))];
          $this->reportHistory($report, $history);
          return 'Due Date Updated for Report ' . intval($data['id']) . ' to ' . $dateM . '/' . $dateD . '/' . $dateY;
        } else {
          // Record Historical Record.
          $history = ['date' => date('m/d/Y g:i a'), 'user_id' => Auth::user()->id, 'user_name' => Auth::user()->full_name(), 'note' => 'Attempted update to due date failed - value submitted: ' . $data['due']];
          $this->reportHistory($report, $history);
          return 'I was not able to update Report #' . intval($data['id']) . ' due date to ' . $dateY . '-' . $dateM . '-' . $dateD . ' 18:00:00';
        }
      } else {
        return 'I was not able to find a report matching this id:' . intval($data['id']);
      }
    } else {
      return 'Sorry, you do not have permission to do this action.';
    }
  }

  public function reportAction(CrrReport $report, $data)
  {
    $note = 'Attempted an Action, but no action was taken.';
    if (Auth::user()->can('access_auditor')) {
      switch ($data['action']) {
        case 1:
          # DRAFT
          //send manager notification
          $note = "Changed report status from " . $report->status_name() . " to Draft.";
          if (!is_null($report->manager_id)) {
            $report->update(['crr_approval_type_id' => 1, 'manager_id' => null]);
            $note = ' Removed prior manager approval, and refreshed report to reflect the change.';
            $this->generateReport($report, 0, 1);
          } else {
            $report->update(['crr_approval_type_id' => 1, 'manager_id' => null]);
          }
          break;
        case 2:
          # Pending Manager Review...
          $note = "Changed report status from " . $report->status_name() . " to Pending Manger Review.";
          if (!is_null($report->manager_id)) {
            $report->update(['crr_approval_type_id' => 2, 'manager_id' => null]);
            $note .= ' Removed prior manager approval, and refreshed report to reflect the change.';
            $this->generateReport($report, 0, 1);
          } else {
            $report->update(['crr_approval_type_id' => 2, 'manager_id' => null]);
          }
          break;
        case 3:
          # Declined By Manager...
          if (Auth::user()->can('access_manager')) {
            $note = "Changed report status from " . $report->status_name() . " to Declined by Manager.";
            if (!is_null($report->manager_id)) {
              $report->update(['crr_approval_type_id' => 3, 'manager_id' => null]);
              $note .= ' Removed prior manager approval, and refreshed report to reflect the change.';
              $this->generateReport($report, 0, 1);
            } else {
              $report->update(['crr_approval_type_id' => 3, 'manager_id' => null]);
            }
          } else {
            $note = "Attempted change to Declined by Manger can only be done by a manager or higher.";
          }
          break;
        case 4:
          # Approved with Changes...
          if (Auth::user()->can('access_manager')) {
            $note = "Changed report status from " . $report->status_name() . " to Approved with Changes.";
            if (is_null($report->manager_id) || Auth::user()->id != $report->manger_id) {
              $note .= ' Updated manager approval, and refreshed report to reflect the change.';
            }
            $report->update(['crr_approval_type_id' => 4, 'manager_id' => Auth::user()->id]);
            $this->generateReport($report, 0, 1);
          } else {
            $note = "Attempted change to Approved with Changes can only be done by a manager or higher.";
          }
          break;
        case 5:
          # Approved...
          if (Auth::user()->can('access_manager')) {
            $note = "Changed report status from " . $report->status_name() . " to Approved.";
            if (is_null($report->manager_id) || Auth::user()->id != $report->manger_id) {
              $note .= ' Updated prior manager approval, and refreshed report to reflect the change.';
              $this->generateReport($report, 0, 1);
            }
            $report->update(['crr_approval_type_id' => 5, 'manager_id' => Auth::user()->id]);
            $this->generateReport($report, 0, 1);
          } else {
            $note = "Attempted change to Approved can only be done by a manager or higher.";
          }
          break;
        case 6:
          # Sent...
          if ($report->project->pm() && strlen($report->project->pm()['email']) > 3) {
            // send notification that report is ready to be viewed.
            $note = "Changed report status from " . $report->status_name() . " to Sent and sent notification to " . $report->project->pm()['email'] . ".";
            $report->update(['crr_approval_type_id' => 6]);
          } else {
            $note = "Unable to send report. There is no default email for a property manager on this project. Status will remain:" . $report->status_name() . ".";
          }

          break;
        case 7:
          # Viewed by PM...
          if (!Auth::user()->isOhfa()) {
            $note = "Changed report status from " . $report->status_name() . " to Viewed by PM.";
            $report->update(['crr_approval_type_id' => 7]);
          } else {
            $note = "Viewed by OHFA staff.";
          }
          break;
        case 8:
          # code...
          break;
        case 9:
          # All items resolved ...
          if (Auth::user()->can('access_manager')) {
            $note = Auth::user()->name ." updated the status to " . $report->status_name();
            if (!is_null($report->manager_id)) {
              $report->update(['crr_approval_type_id' => 9, 'manager_id' => null]);
              $note .= 'Removed prior status, and refreshed report to reflect the change.';
              $this->generateReport($report, 0, 1);
            } else {
              $report->update(['crr_approval_type_id' => 9, 'manager_id' => null]);
            }
          } else {
            $note = "Attempted change to All Items Resolved but something went wrong.";
          }
          break;

        default:
          # code...
          break;
      }
    }

    $history = ['date' => date('m-d-Y g:i a'), 'user_id' => Auth::user()->id, 'user_name' => Auth::user()->full_name(), 'note' => $note];
    $this->reportHistory($report, $history);
    return $note;
  }

  public function reports(Request $request, $project = null)
  {
    $messages = []; //this is to send messages back to the view confirming actions or errors.
    // set values - ensure this single request works for both dashboard and project details
    if (!is_null($project)) {
      // this sets values for if it is a project details view
      $project = Project::find($project);
      session(['crr_report_project_id' => $project->id]);
    }

    // Perform Actions First.
    if (!is_null($request->get('due'))) {
      $data        = [];
      $data['due'] = $request->get('due');
      $data['id']  = $request->get('report_id');
      //dd($data);
      $messages[] = $this->dueDate($data);
      //dd($messages);
    }

    if (!is_null($request->get('action'))) {
      $data           = [];
      $data['action'] = intval($request->get('action'));

      //dd($data);
      $report     = CrrReport::find($request->get('id'));
      $messages[] = $this->reportAction($report, $data);
      //dd($messages);
    }

    // Set default filters for first view of page:
    if (session('crr_first_load') !== 1) {
        session(['crr_report_status_id' => 1]);
      // set some default parameters
      if (Auth::user()->can('access_manager')) {
        session(['crr_report_status_id' => 2]);
        // pending manager review
      } elseif (Auth::user()->can('access_auditor')) {
        session(['crr_report_lead_id' => Auth::user()->id]);
        // show this auditors reports
      } elseif (Auth::user()->can('access_pm')) {
        session(['crr_report_status_id' => 6]);
        // @todo
      }
      session(['crr_first_load' => 1]);
      // makes sure if they override or clear the default filter it doesn't get overrideen.
    }
    // Search Number
    if ($request->get('search')) {
      session(['crr_search' => $request->get('search')]);
    } elseif (is_null(session('crr_search'))) {
      session(['crr_search' => 'all']);
    }
    if (session('crr_search') !== 'all') {
      $searchEval = '=';
      $searchVal  = intval(session('crr_search'));
    } else {
      session(['crr_search' => 'all']);
      $searchEval = '>';
      $searchVal  = '0';
    }

    // Report Type
    if ($request->get('crr_report_type')) {
      session(['crr_report_type' => $request->get('crr_report_type')]);
    } elseif (is_null(session('crr_report_type'))) {
      session(['crr_report_type' => 'all']);
    }
    if (session('crr_report_type') !== 'all') {
      $typeEval = '=';
      $typeVal  = intval(session('crr_report_type'));
    } else {
      session(['crr_report_type' => 'all']);
      $typeEval = '>';
      $typeVal  = '0';
    }

    // Report Status
    if ($request->get('crr_report_status_id')) {
      session(['crr_report_status_id' => $request->get('crr_report_status_id')]);
    } elseif (is_null(session('crr_report_status_id'))) {
      session(['crr_report_status_id' => 'all']);
    }
    if(Auth::user()->can('access_auditor')){
      if (session('crr_report_status_id') !== 'all') {
        $approvalTypeEval = '=';
        $approvalTypeVal  = intval(session('crr_report_status_id'));
      } else {
        session(['crr_report_status_id' => 'all']);
        $approvalTypeEval = '>';
        $approvalTypeVal  = 0;
      }
    }else{
      if (session('crr_report_status_id') !== 'all') {
        if(intval(session('crr_report_status_id'))<6){
          //user is trying to get a status they cannot access
          session(['crr_report_status_id' => 6]); // default them to the sent
        }
        $approvalTypeEval = '=';
        $approvalTypeVal  = intval(session('crr_report_status_id'));
      } else {
        session(['crr_report_status_id' => 'all']);
        $approvalTypeEval = '>';
        $approvalTypeVal  = 5;
      }
    }

    // Project Selection
    if ($request->get('crr_report_project_id')) {
      session(['crr_report_project_id' => $request->get('crr_report_project_id')]);
    } elseif (is_null(session('crr_report_project_id'))) {
      session(['crr_report_project_id' => 'all']);
    }
    if (session('crr_report_project_id') !== 'all') {
      $projectEval = '=';
      $projectVal  = intval(session('crr_report_project_id'));
    } else {
      session(['crr_report_project_id' => 'all']);
      $projectEval = '>';
      $projectVal  = 0;
    }

    // Lead Selection
    if ($request->get('crr_report_lead_id')) {
      session(['crr_report_lead_id' => $request->get('crr_report_lead_id')]);
    } elseif (is_null(session('crr_report_lead_id'))) {
      session(['crr_report_lead_id' => 'all']);
    }
    if (session('crr_report_lead_id') !== 'all') {
      $leadEval = '=';
      $leadVal  = intval(session('crr_report_lead_id'));
    } else {
      session(['crr_report_lead_id' => 'all']);
      $leadEval = '>';
      $leadVal  = 0;
    }

    // Check For Newer Than Selection
    if ($request->get('newer_than')) {
      // this is only used for checking for updated records
      $newerThan = $request->get('newer_than');
    } else {
      $newerThan = '1900-01-01 00:00:01';
    }

    if (!$request->get('check')) {
      // if this is just a check - we do not need this information.
      if (Auth::user()->can('access_auditor')) {
        $auditLeads      = Audit::select('*')->with('lead')->with('project')->whereNotNull('lead_user_id')->groupBy('lead_user_id')->get();
        $auditProjects   = CrrReport::select('*')->with('project')->groupBy('project_id')->get();
        $crr_types_array = CrrReport::select('id', 'template_name')->groupBy('template_name')->whereNotNull('template')->get()->all();
        $hfa_users_array = [];
        $projects_array  = [];
      } else {
        $auditLeads      = []; //Audit::select('*')->with('lead')->with('project')->whereNotNull('lead_user_id')->groupBy('lead_user_id')->get();
        $auditProjects   = CrrReport::select('*')->when(Auth::user()->cannot('access_auditor'), function ($query) {
              $userProjects = \App\Models\ProjectContactRole::select('project_id')->where('person_id',Auth::user()->person_id)->get()->toArray();

              //dd(Auth::user()->person_id,$userProjects);
              return $query->whereIn('project_id', $userProjects);
      })->with('project')->groupBy('project_id')->get();
        $crr_types_array = CrrReport::select('id', 'template_name', 'crr_approval_type_id')->where('crr_approval_type_id','>', 5)->groupBy('template_name')->whereNotNull('template')->get()->all();
        $hfa_users_array = [];
        $projects_array  = [];

      }
      foreach ($auditLeads as $hfa) {
        if ($hfa->lead_user_id) {
          $hfa_users_array[] = $hfa->lead;
        }
      }
      foreach ($auditProjects as $hfa) {
        if ($hfa->project) {
          $projects_array[] = $hfa->project;
        }
      }
      $hfa_users_array = array_values(Arr::sort($hfa_users_array, function ($value) {
        return $value['name'];
      }));
      $projects_array = array_values(Arr::sort($projects_array, function ($value) {
        return $value['project_name'];
      }));
      if(Auth::user()->can('access_auditor')){
        $crrApprovalTypes = CrrApprovalType::orderBy('order')->get();
      } else {
        $crrApprovalTypes = CrrApprovalType::where('id','>',5)->orderBy('order')->get();
      }
    }
    //dd($hfa_users_array);
    //dd($searchVal,$searchEval,session('crr_search'),intval($request->get('search')));
    $reports = CrrReport::where('crr_approval_type_id', $approvalTypeEval, $approvalTypeVal)
      ->whereNull('template')
      ->where('project_id', $projectEval, $projectVal)
      ->where('lead_id', $leadEval, $leadVal)
      ->where('updated_at', '>', $newerThan)
      ->where('from_template_id', $typeEval, $typeVal)
      ->where('id', $searchEval, $searchVal)
      ->when(Auth::user()->cannot('access_auditor'), function ($query) {
              $userProjects = \App\Models\ProjectContactRole::select('project_id')->where('person_id',Auth::user()->person_id)->get()->toArray();

              //dd(Auth::user()->person_id,$userProjects);
              return $query->whereIn('project_id', $userProjects);
      })
      ->orderBy('updated_at', 'desc')
      ->paginate(10);

    if (count($reports)) {
      $newest = $reports->sortByDesc('updated_at');
      $newest = date('Y-m-d G:i:s', strtotime($newest[0]->updated_at));
    } else {
      $newest = null;
    }
    //dd($reports,$approvalTypeVal,$projectVal,$leadVal);
    //return \view('dashboard.index'); //, compact('user')

    if ($request->get('check')) {
      if (count($reports)) {
        return json_encode($reports);
      } else {
        return 1;
      }
    } else if ($request->get('rows_only')) {
      return view('dashboard.partials.reports-row', compact('reports'));
    } else {
      return view('dashboard.reports', compact('reports', 'project', 'hfa_users_array', 'crrApprovalTypes', 'projects_array', 'crr_types_array', 'messages', 'newest'));
    }
  }

  public function newReportForm()
  {
   if(Auth::user()->can('access_auditor')){
    // list out templates
    $audits    = CachedAudit::where('step_id','>', 59)->where('step_id','<',67)->with('project')->with('audit.reports')
    //->orderBy('projects.project_name', 'asc')
    ->get();
    $templates = CrrReport::where('template', 1)->where('active_template', 1)->get();

    return view('modals.new-report', compact('templates', 'audits'));
    }else{
      return 'Sorry, you are not allowed to access this page.';
    }
  }

  public function freeTextPlaceHolders(Audit $audit, $string, CrrReport $report)
  {
    //replace string value with current audit values.
     $string = str_replace("||REPORT ID||", $report->id, $string);
      $string = str_replace("||VERSION||", ($report->version + 1), $string);
    $string = str_replace("||PROJECT NAME||", $audit->project->project_name, $string);
    $string = str_replace("||AUDIT ID||", $audit->id, $string);
    $string = str_replace("||PROJECT NUMBER||", $audit->project->project_number, $string);
    if ($audit->start_date) {
      $string = str_replace("||REVIEW DATE||", date('m/d/Y', strtotime($audit->start_date)), $string);
    } else {
      $string = str_replace("||REVIEW DATE||", 'START DATE NOT SET', $string);
    }
    if ($report->response_due_date) {
      $string = str_replace("||RESPONSE DUE||", date('m/d/Y', strtotime($report->response_due_date)), $string);
    } else {
      $string = str_replace("||RESPONSE DUE||", '<span style="color:red;" class="attention">DATE NOT SET</span>', $string);
    }
    $string = str_replace("||TODAY||", date('M d, Y', time()), $string);
    //return ['organization_id'=> $owner_organization_id,'organization'=> $owner_organization, 'name'=>$owner_name, 'email'=>$owner_email, 'phone'=>$owner_phone, 'fax'=>$owner_fax, 'address'=>$owner_address, 'line_1'=>$owner_line_1, 'line_2'=>$owner_line_2, 'city'=>$owner_city, 'state'=>$owner_state, 'zip'=>$owner_zip ];
    $string = str_replace("||OWNER ORGANIZATION NAME||", $audit->project->owner()['organization'], $string);
    if ($audit->project->owner()['name']) {
      $string = str_replace("||OWNER NAME||", $audit->project->owner()['name'], $string);
    } else {
      $string = str_replace("||OWNER NAME||", 'Sir or Madam', $string);
    }
    if (strpos($string, "||OWNER FORMATTED ADDRESS||")) {
      $address = '';
      if ($audit->project->owner()['line_1']) {
        $address = $audit->project->owner()['line_1'];
        if ($audit->project->owner()['line_2']) {
          $address .= '<br /> ' . $audit->project->owner()['line_2'].'<br />';
        } else {
          $address .= '<br />';
        }
      } else if ($audit->project->owner()['line_2']) {
        $address = $audit->project->owner()['line_2'] . '<br />';
      }
      if ($audit->project->owner()['city']) {
        $address .= $audit->project->owner()['city'];
        if ($audit->project->owner()['state']) {
          $address .= ', ' . $audit->project->owner()['state'];
        }
      }
      if ($audit->project->owner()['zip']) {
        $address .= ' '.$audit->project->owner()['zip'];
      }
      $address .= '<br />';
      $string = str_replace("||OWNER FORMATTED ADDRESS||", $address, $string);
    }
    $string = str_replace("||OWNER ADDRESS||", $audit->project->owner()['address'], $string);
    $string = str_replace("||OWNER ADDRESS||", $audit->project->owner()['address'], $string);
    $string = str_replace("||OWNER ADDRESS LINE 1||", $audit->project->owner()['line_1'], $string);
    $string = str_replace("||OWNER ADDRESS LINE 2||", $audit->project->owner()['line_2'], $string);
    $string = str_replace("||OWNER ADDRESS CITY||", $audit->project->owner()['city'], $string);
    $string = str_replace("||OWNER ADDRESS STATE||", $audit->project->owner()['state'], $string);
    $string = str_replace("||OWNER ADDRESS ZIP||", $audit->project->owner()['zip'], $string);
    if ($report->response_due_date) {
      $string = str_replace("||REPORT RESPONSE DUE||", $report->response_due_date, $string);
    } else {
      $string = str_replace("||REPORT RESPONSE DUE||", '<span style="color:red;" class="attention">NO DUE DATE SET</span>', $string);
    }
    if ($audit->lead_user_id) {
      $string = str_replace("||LEAD NAME||", $audit->lead->full_name(), $string);
    } else {
      $string = str_replace("||LEAD NAME||", 'LEAD NOT SET', $string);
    }

    if ($report->manager_id) {
      $string = str_replace("||MANAGER NAME||", $report->manager->full_name(), $string);
    } else {
      $string = str_replace("||MANAGER NAME||", '<span style="color:red;" class="attention">REPORT NOT APPROVED</span>', $string);
    }

    // PROGRAMS LIST
    if (strpos($string, '||PROGRAMS||')) {
      $programs = $audit->project->programs;
      //dd($programs);
      $programNames = '';
      //dd($programs);
      $totalPrograms = count($programs);
      $programCount  = 1;
      foreach ($programs as $program) {
        //dd($program->program->program_name);
        $programNames .= $program->program->program_name;

        if ((count($programs) - 1) == $programCount) {
          $programNames .= " and ";
        } elseif ($totalPrograms > 1 && $programCount !== $totalPrograms) {
          $programNames .= ", ";
        }
        $programCount++;
      }
      if ($totalPrograms > 1) {
        $programNames .= " programs";
      } else {
        $programNames .= " program";
      }
      if (count($programs)) {
        $string = str_replace("||PROGRAMS||", $programNames, $string);
      } else {
        $programsStatus    = ProjectProgram::where('project_id', $report->project_id)->get();
        $programStatusText = "<ul>";
        foreach ($programsStatus as $ps) {
          //dd($ps->program,$ps->status,$ps);
          $programStatusText .= '<li>' . $ps->program->program_name . ' ( id: ' . $ps->program->id . ' key: ' . $ps->program->program_key . ' ) Award Number: ' . $ps->award_number . ' status: ' . $ps->status->status_name . ' ( id: ' . $ps->status->id . ' key: ' . $ps->status->project_program_status_type_key . ' )</li>';
        }
        $programStatusText .= "</ul>";
        $string = str_replace("||PROGRAMS||", '<span style="color:red;" class="attention">FAILED TO FIND PROGRAMS</span><br /><p>PROGRAMS ON PROJECT<br />' . $programStatusText . '</p>', $string);
      }
    }

    return $string;
  }

  public function getComments(CrrReport $report, $part)
  {
    $comments = null;

    return view('crr_parts.crr_comments', compact('report', 'part', 'comments'));
  }

  public function setCrrData(CrrReport $template, $reportId = null, $audit = null){
    //get the report parts
    $audit  = Audit::find($audit);
    $report = CrrReport::find($reportId);
    if (!is_null($audit)) {
      //dd('CREATING REPORT',$audit,$report);

      if (is_null($report)) {
        //no report yet - let's make one real quick :)
        $report                       = new CrrReport;
        $report->audit_id             = $audit->id;
        $report->lead_id              = $audit->lead_user_id;
        $report->project_id           = $audit->project_id;
        $report->version              = 1;
        $report->crr_approval_type_id = 1; //draft
        $report->from_template_id     = $template->id;
        $report->last_updated_by      = Auth::user()->id;
        $report->created_by           = Auth::user()->id;
        $report->requires_approval = $template->requires_approval;
        $report->save();
        // record creation history:
        $history = ['date' => date('m-d-Y g:i a'), 'user_id' => Auth::user()->id, 'user_name' => Auth::user()->full_name(), 'note' => 'Created report using template ' . $template->template_name . '.'];
        $this->reportHistory($report, $history);
        // create report's copies of the tempate sections and parts
        foreach ($template->sections as $section) {
          $newSection                = new CrrSection;
          $newSection->crr_report_id = $report->id;
          $newSection->audit_id      = $report->audit_id;
          $newSection->title         = $section->title;
          $newSection->description   = $section->description;
          $newSection->save();
          //create the order record
          $newSectionOrder                 = new CrrSectionOrder;
          $newSectionOrder->crr_report_id  = $report->id;
          $newSectionOrder->audit_id       = $report->audit_id;
          $newSectionOrder->crr_section_id = $newSection->id;
          $newSectionOrder->order          = $section->order;
          $newSectionOrder->save();

          //create copies of this sections parts
          foreach ($section->parts as $part) {
            $newPart                   = new CrrPart;
            $newPart->crr_report_id    = $report->id;
            $newPart->audit_id         = $report->audit_id;
            $newPart->title            = $part->title;
            $newPart->data             = $part->data;
            $newPart->crr_section_id   = $newSection->id;
            $newPart->crr_part_type_id = $part->crr_part_type_id;
            $newPart->description      = $part->description;
            $newPart->save();
            //create the order record
            $newPartOrder                 = new CrrPartOrder;
            $newPartOrder->crr_report_id  = $report->id;
            $newPartOrder->audit_id       = $report->audit_id;
            $newPartOrder->crr_section_id = $newSection->id;
            $newPartOrder->crr_part_id    = $newPart->id;
            $newPartOrder->order          = $part->order;
            $newPartOrder->save();
          }
        }
      } else {
        return 1;
      }

      return 1;
    } else {
      return 'Please Select an Audit.';
    }
  }

  public function resetToTemplate(CrrReport $report)
  {
    if ($report && Auth::user()->can('access_auditor') && is_null($report->template)) {
      $template = CrrReport::find($report->from_template_id);
      $history  = ['date' => date('m-d-Y g:i a'), 'user_id' => Auth::user()->id, 'user_name' => Auth::user()->full_name(), 'note' => 'Resetting report content back to template ' . $template->template_name . '. Previouse versions are still able to be viewed.'];
      $this->reportHistory($report, $history);
      //remove the current template info.
      CrrSection::where('crr_report_id', $report->id)->delete();
      CrrSectionOrder::where('crr_report_id', $report->id)->delete();
      CrrPart::where('crr_report_id', $report->id)->delete();
      CrrPartOrder::where('crr_report_id', $report->id)->delete();
      // create report's copies of the tempate sections and parts
      foreach ($template->sections as $section) {
        $newSection                = new CrrSection;
        $newSection->crr_report_id = $report->id;
        $newSection->audit_id      = $report->audit_id;
        $newSection->title         = $section->title;
        $newSection->description   = $section->description;
        $newSection->save();
        //create the order record
        $newSectionOrder                 = new CrrSectionOrder;
        $newSectionOrder->crr_report_id  = $report->id;
        $newSectionOrder->audit_id       = $report->audit_id;
        $newSectionOrder->crr_section_id = $newSection->id;
        $newSectionOrder->order          = $section->order;
        $newSectionOrder->save();

        //create copies of this sections parts
        foreach ($section->parts as $part) {
          $newPart                   = new CrrPart;
          $newPart->crr_report_id    = $report->id;
          $newPart->audit_id         = $report->audit_id;
          $newPart->title            = $part->title;
          $newPart->data             = $part->data;
          $newPart->crr_section_id   = $newSection->id;
          $newPart->crr_part_type_id = $part->crr_part_type_id;
          $newPart->description      = $part->description;
          $newPart->save();
          //create the order record
          $newPartOrder                 = new CrrPartOrder;
          $newPartOrder->crr_report_id  = $report->id;
          $newPartOrder->audit_id       = $report->audit_id;
          $newPartOrder->crr_section_id = $newSection->id;
          $newPartOrder->crr_part_id    = $newPart->id;
          $newPartOrder->order          = $part->order;
          $newPartOrder->save();
        }
      }
      $this->generateReport($report, 1, 0);
    } else {
      return "Cannot find a matching report, or you do not have sufficient priveledges.";
    }
  }

  public function createNewReport(Request $request)
  {
    //dd($request->input('template_id'), $request->input('audit_id'));
    $template = CrrReport::find($request->input('template_id'));
    if (!is_null($template)) {
      if (1 == $template->id || 2 == $template->id) {
        // these are document style reports
        $message = $this->setCrrData($template, null, $request->input('audit_id'));
      } else {
        // these are data export style reports
        $message = 'Data options would be here';
      }

      return $message;
    } else {
      return 'Please Select a Report Type';
    }
  }

  public function getReport(CrrReport $report, Request $request)
  {
    if ($report) {
    $current_user    = Auth::user();
      // check if logged in user has access to this report if they are not an auditor:
      $loadReport = 0;
      if(Auth::user()->cannot('access_auditor')){
         $userProjects = \App\Models\ProjectContactRole::where('project_id',$report->project_id)->where('person_id',Auth::user()->person_id)->count();
         if($userProjects && $report->crr_approval_type_id > 5){
          $loadReport = 1;
         } else {
          //dd($userProjects, $report->project_id);
         }

      } else {
        $loadReport = 1;
      }
     // $cr = CrrReport::with('audit', 'project.contactRoles','sections.parts.crr_part_type')->find($report->id);
      if($loadReport){
      	$users = $report->signators(); //change this to actual audit users (auditors, owner, pm, and all managers)
        if (is_null($report->crr_data)) {
          //return 'This report has no data.';
          $this->generateReport($report, 1);
          return '<meta http-equiv="refresh" content="0;url=/report/'.$report->id.'" />';
        } else {
          $data    = json_decode($report->crr_data);
          $version = $report->version;
          if ($request->get('version')) {
            $version = intval($request->get('version'));
          }
          $versionText = 'version-' . $version;
          $data        = $data[$version - 1]->$versionText;
          $print       = $request->get('print');

          $history = ['date' => date('m/d/Y g:i a'), 'user_id' => Auth::user()->id, 'user_name' => Auth::user()->full_name(), 'note' => 'Opened and viewed report'];
          if(Auth::user()->cannot('access_auditor')){
            //user is a PM
            $report->update(['crr_approval_type_id'=>7]);
          }
          $this->reportHistory($report, $history);
          $x = json_decode($report->crr_data);
          //return dd(collect($x)[48]);

          if ($request->get('print') != 1) {
            return view('crr.crr', compact('report', 'data', 'version', 'print', 'users', 'current_user'));
          } else {
            return view('crr.crr_print', compact('report', 'data', 'version', 'print', 'users', 'current_user'));
          }
        }
      } else {
        if($report->crr_approval_type_id < 6){
          return '<script>alert("Sorry! This report has not been released for review.");</script>';

        }else{
        return '<script>alert("Sorry! It does not appear you have access to this report. Please notify your partner to add you to the project\'s contacts.");</script>';
        }
      }
    } else {
      return 'I was not able to load the requested report because it does not exist... please notify support with the report number you are trying to open.';
    }
  }

  /// the following methods should be moved to a trait and then accessed via job

  public function generateReport(CrrReport $report, $goToView = 1, $noStatusChange = 0)
  {
    if ($report) {
      $data = [];
      if (!is_null($report->crr_data)) {
        // get current version and add 1 to it
        $version = $report->version + 1;
        $data    = collect(json_decode($report->crr_data))->toArray();
      } else {
        $version = 1;
      }
      $index        = $version - 1; // set the object index
      $sections     = $report->sections;
      $sectionOrder = 1;
      foreach ($sections as $section) {
        // process each section
        $data[$index]['version-' . $version]['section-' . $sectionOrder] = ['crr_section_id' => $section->id];
        $partOrder                                                       = 1;
        foreach ($section->parts as $part) {
          //dd($part);
          //make magic happen.
          $method = $part->crr_part_type->method_name;
          if($method == 'propertyInspections')
           $partValue = $this->$method($report, $part);
         else
          $partValue = $this->$method($report, $part);
          $data[$index]['version-' . $version]['section-' . $sectionOrder]['parts']['part-' . $partOrder] = [$partValue];
          $partOrder++;
        }
        $sectionOrder++;
      }

      $approvalId = 1;
      if ($noStatusChange) {
        // we will not modify the status if this flag is set.
        $approvalId = $report->crr_approval_type_id;
      }

      $report->update(['crr_data' => json_encode($data), 'version' => $version, 'crr_approval_type_id' => $approvalId, 'signature' => null]);
      $history = ['date' => date('m/d/Y g:i a'), 'user_id' => Auth::user()->id, 'user_name' => Auth::user()->full_name(), 'note' => 'Generated version '.$version.' of the report'];
        $this->reportHistory($report, $history);
      //dd($data);
      //if ($goToView) {
        //dd($goToView);

        return redirect('/report/' . $report->id);
      //}
    } else {
      return 'I was unable to find the requested report to process. Did it get deleted?';
    }
    return 'Nothing happened.';
  }

  //// CRR PARTS - MAKE THESE TRAITS?

  public function crrLetter(CrrReport $report, CrrPart $part)
  {
    // calculate data for the letter.
    $response            = [];
    $response['content'] = $this->freeTextPlaceHolders($report->audit, $part->crr_part_type->content, $report);
    $response['blade']   = $part->crr_part_type->blade;
    $response['data']    = $part->data;
    $response['name']    = $part->crr_part_type->name;
    $response['part_id'] = $part->id;
    return $response;
  }

  public function projectHeader(CrrReport $report, CrrPart $part)
  {
    // calculate data for the header.
    //dd($part);
    $response            = [];
    $response['content'] = $this->freeTextPlaceHolders($report->audit, $part->crr_part_type->content, $report);
    $response['blade']   = $part->crr_part_type->blade;
    $response['data']    = $part->data;
    $response['name']    = $part->crr_part_type->name;
    $response['part_id'] = $part->id;
    return $response;
  }

  public function propertyFindings(CrrReport $report, CrrPart $part)
  {
    // calculate data for the header.
    //dd($part);
    //get findings
    $originalData = json_decode($part->data);
    $data[]       = $originalData[0];
    $data[]       = $report->audit->reportableFindings;
    //dd($data);
    $response            = [];
    $response['content'] = '';
    $response['blade']   = $part->crr_part_type->blade;
    $response['data']    = json_encode($data);
    $response['name']    = $part->crr_part_type->name;
    $response['part_id'] = $part->id;
    //$test=json_decode($response['data']);
    //dd($response,$test[1]);
    return $response;
  }

  public function propertyInspections(CrrReport $report, CrrPart $part)
  {
    // calculate data for the header.
    //dd($part);
    //get findings
    $originalData = json_decode($part->data);
    $data[]       = $originalData[0];
    $data[]       = $report->audit->unit_inspections;
    $data[]       = $report->audit->project_amenity_inspections;
    $data[]       = $report->audit->building_inspections;
    $data[]       = $report->audit->project->auditSpecificProjectDetails($report->audit_id);
    $data[]       = $report->audit->reportableFindings; // need this to do counts :/
    //dd($data);
    $response            = [];
    $response['content'] = '';
    $response['blade']   = $part->crr_part_type->blade;
    $response['data']    = json_encode($data);
    $response['name']    = $part->crr_part_type->name;
    $response['part_id'] = $part->id;
    //$test=json_decode($response['data']);
    //dd($response,$test[1]);
    return $response;
  }

  public function freeText(CrrReport $report, CrrPart $part)
  {
    // calculate data for the header.
    //dd($part);
    $response            = [];
    $response['content'] = $this->freeTextPlaceHolders($report->audit, $part->crr_part_type->content, $report);
    $response['blade']   = $part->crr_part_type->blade;
    $response['data']    = $part->data;
    $response['name']    = $part->crr_part_type->name;
    $response['part_id'] = $part->id;
    return $response;
  }

  public function signDigitally(CrrReport $report, CrrPart $part)
  {
    // calculate data for the header.
    //dd($part);
    $response            = [];
    $response['content'] = $this->freeTextPlaceHolders($report->audit, $part->crr_part_type->content, $report);
    $response['blade']   = $part->crr_part_type->blade;
    $response['data']    = $part->data;
    $response['name']    = $part->crr_part_type->name;
    $response['part_id'] = $part->id;
    return $response;
  }

  public function signViaClick(CrrReport $report, CrrPart $part)
  {
    // calculate data for the header.
    //dd($part);
    $response            = [];
    $response['content'] = $this->freeTextPlaceHolders($report->audit, $part->crr_part_type->content, $report);
    $response['blade']   = $part->crr_part_type->blade;
    $response['data']    = $part->data;
    $response['name']    = $part->crr_part_type->name;
    $response['part_id'] = $part->id;
    return $response;
  }

  public function ehsHeader(CrrReport $report, CrrPart $part)
  {
    // calculate data for the header.
    //dd($part);
    $response            = [];
    $response['content'] = $this->freeTextPlaceHolders($report->audit, $part->crr_part_type->content, $report);
    $response['blade']   = $part->crr_part_type->blade;
    $response['data']    = $part->data;
    $response['name']    = $part->crr_part_type->name;
    $response['part_id'] = $part->id;
    return $response;
  }

  public function ltFindings(CrrReport $report, CrrPart $part)
  {
    // calculate data for the header.
    //dd($part);
    $originalData = json_decode($part->data);
    $data[]       = $originalData[0];
    $data[]       = $report->audit->reportableLtFindings;

    //dd($data);

    $response            = [];
    $response['content'] = '';
    $response['blade']   = $part->crr_part_type->blade;
    $response['data']    = json_encode($data);
    $response['name']    = $part->crr_part_type->name;
    $response['part_id'] = $part->id;
    return $response;
  }

  public function postDigitalSignature(CrrReport $report, Request $request)
  {
  	//validations -  Done on frontend side
  	$validator = \Validator::make($request->all(), [
      'signature' => 'required',
      'other_name'   => 'required_if:signing_user,other'
    ]);
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    }
  	//Data insert in crr reports
  	if($report) {
  		$user = Auth::user();

  		$report->last_updated_by = $user->id;
  		$report->signature = $request->signature;
        $report->signed_version = $report->version;
        $report->date_signed = date('Y-m-d H:i:s',time());
        $report->response_due_date = date('Y-m-d H:i:s', time()+86400);
        $report->crr_approval_type_id = 7;
  		if($request->signing_user == 'other') {
  			$report->signed_by = $request->other_name;
  			$report->signed_by_id = null;
            $name = $request->other_name;
  		} else {
            $signing_user = People::where('id',$request->signing_user)->first();
            //dd($signing_user);
  			$report->signed_by_id = $request->signing_user;
  			$report->signed_by = $signing_user->first_name.' '.$signing_user->last_name;
            $name = $signing_user->first_name.' '.$signing_user->last_name;
  		}
  		$report->save();
  		$history = ['date' => date('m-d-Y g:i a'), 'user_id' => $user->id, 'user_name' => $user->full_name(), 'note' => $name.' signed the report and their response is due '.date('m-d-Y g:i a', strtotime($report->response_due_date)).'.'];
  		 $this->reportHistory($report, $history);
  		 return 1;
  	} else{
  		$validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Technical Team');
    	return response()->json(['errors' => $validator->errors()->all()]);
  	}
  	//last_updated_by
  	//report_history
  	//signature
  	//signed_by
  	//signed_by_id
  	//How should this be shown? to user if already signed!
  }


}
