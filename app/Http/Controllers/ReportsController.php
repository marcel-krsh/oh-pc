<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Project;
use App\Models\Report;
use App\Models\CrrReport;
use App\Models\CrrApprovalType;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Events\Dispatchable;

class ReportsController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    public function reportHistory(CrrReport $report,$data){
                    $reportHistory = $report->report_history;
                    //$reportHistory[] = $data;
                    if(!is_null($reportHistory)){
                        // put newest entry at the beginning - solves issue of same moment entries going out of order on sorts.
                        //dd($data);
                        array_unshift($reportHistory, $data);
                        //dd($reportHistory, $report->report_hitory);
                    }else{
                        // this is the first piece of history - proud moment :)
                        $reportHistory[] = $data;
                    }
                    $report->report_history = $reportHistory;
                    $report->last_updated_by = Auth::user()->id;
                    $report->save();
    }


    public function dueDate($data){
        // check permissions
        if(Auth::user()->can('access_auditor')){
            //2019-03-20 14:41:55
            $dateY = substr($data['due'], 0,4);
            $dateM = substr($data['due'], 4,2);
            $dateD = substr($data['due'], 6,2);
            // Get the Report
            $report = CrrReport::find(intval($data['id']));
            // Record old Values that will be updated for history
            if(!is_null($report)){
               
                if($report->response_due_date){
                    $oldDate = date('M d, Y', strtotime($report->response_due_date));   
                }else{
                    $oldDate = 'Not Set'; 
                }

                $updated = $report->update(['response_due_date' =>$dateY.'-'.$dateM.'-'.$dateD.' 18:00:00']);
                if($updated){
                    // Record Historical Record.
                    $history = ['date'=>date('m-d-Y g:i a'),'user_id'=>Auth::user()->id,'user_name'=>Auth::user()->full_name(),'note'=>'Updated due date from '.$oldDate.' to '.date('M d, Y',strtotime($report->response_due_date))];
                    $this->reportHistory($report,$history);
                    return 'Due Date Updated for Report '.intval($data['id']).' to '.$dateM.'/'.$dateD.'/'.$dateY;

                } else {
                    // Record Historical Record.
                    $history = ['date'=>date('m/d/Y g:i a'),'user_id'=>Auth::user()->id,'user_name'=>Auth::user()->full_name(),'note'=>'Attempted update to due date failed - value submitted: '.$data['due']];
                    $this->reportHistory($report,$history);
                    return 'I was not able to update Report #'.intval($data['id']).' due date to '.$dateY.'-'.$dateM.'-'.$dateD.' 18:00:00';
                }
            }else{
                return 'I was not able to find a report matching this id:'.intval($data['id']);
            }
        } else {
            return 'Sorry, you do not have permission to do this action.';
        }
    }

    public function reportAction(CrrReport $report, $data){
        $note = 'Attempted an Action, but no action was taken.';
        if(Auth::user()->can('access_auditor')){
            switch ($data['action']) {
                case 1:
                    # DRAFT
                    //send manager notification
                    $note = "Changed report status from ".$report->status_name()." to Draft.";
                    $report->update(['crr_approval_type_id'=>1]);
                    break;
                case 2:
                    # Pending Manager Review...
                    $note = "Changed report status from ".$report->status_name()." to Pending Manger Review.";

                    $report->update(['crr_approval_type_id'=>2]);
                    break;
                case 3:
                    # Declined By Manager...
                    if(Auth::user()->can('access_manager')){
                        $note = "Changed report status from ".$report->status_name()." to Declined by Manager.";
                        $report->update(['crr_approval_type_id'=>3]);
                    } else {
                        $note = "Attempted change to Declined by Manger can only be done by a manager or higher.";
                    }
                    break;
                case 4:
                    # Approved with Changes...
                    if(Auth::user()->can('access_manager')){
                        $note = "Changed report status from ".$report->status_name()." to Approved with Changes.";
                        $report->update(['crr_approval_type_id'=>4]);
                    } else {
                        $note = "Attempted change to Approved with Changes can only be done by a manager or higher.";
                    }
                    break;
                case 5:
                    # Approved...
                    if(Auth::user()->can('access_manager')){
                        $note = "Changed report status from ".$report->status_name()." to Approved.";
                        $report->update(['crr_approval_type_id'=>5]);
                    } else {
                        $note = "Attempted change to Approved can only be done by a manager or higher.";
                    }
                    break;
                case 6:
                    # Sent...
                    if($report->project->pm() && strlen($report->project->pm()['email'])>3){
                        // send notification that report is ready to be viewed.
                        $note = "Changed report status from ".$report->status_name()." to Sent and sent notification to ".$report->project->pm()['email'].".";
                        $report->update(['crr_approval_type_id'=>6]);
                    } else {
                        $note = "Unable to send report. There is no default email for a property manager on this project. Status will remain:".$report->status_name().".";
                    }
                    
                    break;
                case 7:
                    # Viewed by PM...
                    if(!Auth::user()->isOhfa()){
                        $note = "Changed report status from ".$report->status_name()." to Viewed by PM.";
                        $report->update(['crr_approval_type_id'=>7]);
                    } else {
                        $note = "Viewed by OHFA staff.";
                    }
                    break;
                case 8:
                    # code...
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        $history = ['date'=>date('m-d-Y g:i a'),'user_id'=>Auth::user()->id,'user_name'=>Auth::user()->full_name(),'note'=>$note];
        $this->reportHistory($report,$history);
        return $note;

    }

    public function reports(Request $request, $project=null)
    {

        $messages = array(); //this is to send messages back to the view confirming actions or errors.
        // set values - ensure this single request works for both dashboard and project details
        if(!is_null($project)){
            // this sets values for if it is a project details view
            $project = Project::find($project);
            session(['crr_report_project_id'=>$project->id]);
        }
        // Perform Actions First.
        if(!is_null($request->get('due'))){
            $data= array();
            $data['due'] = $request->get('due');
            $data['id'] = $request->get('report_id');
            //dd($data);
            $messages[] = $this->dueDate($data);
            //dd($messages);
        }

        if(!is_null($request->get('action'))){
            $data= array();
            $data['action'] = intval($request->get('action'));
            
            //dd($data);
            $report = CrrReport::find($request->get('id'));
            $messages[] = $this->reportAction($report,$data);
            //dd($messages);
        } 


        // Search
        if($request->get('search')){
            session(['crr_search'=>$request->get('search')]);
        }
        if(session('crr_search') !== '%%clear-search%%'){
            $approvalTypeEval = 'LIKE';
            $approvalTypeVal = "%".session('crr_search')."%";
        }else{
            $request->session()->forget('crr_search');
            $approvalTypeEval = 'LIKE';
            $approvalTypeVal = "%%";
        }

        // Report Status
        if($request->get('crr_report_status_id')){
            session(['crr_report_status_id'=>$request->get('crr_report_status_id')]);
        } elseIf(is_null(session('crr_report_status_id'))){
            session(['crr_report_status_id'=>'all']);
        }
        if( session('crr_report_status_id') !== 'all'){
            $approvalTypeEval = '=';
            $approvalTypeVal = intval(session('crr_report_status_id'));
        }else{
            session(['crr_report_status_id'=>'all']);
            $approvalTypeEval = '>';
            $approvalTypeVal = 0;
        }

        // Project Selection
        if($request->get('crr_report_project_id')){
                session(['crr_report_project_id'=>$request->get('crr_report_project_id')]);
        }elseIf(is_null(session('crr_report_project_id'))){
            session(['crr_report_project_id'=>'all']);
        }
        if( session('crr_report_project_id') !== 'all'){
            $projectEval = '=';
            $projectVal = intval(session('crr_report_project_id'));
        } else {
            session(['crr_report_project_id'=>'all']);
            $projectEval = '>';
            $projectVal = 0;
        }

        // Lead Selection
        if($request->get('crr_report_lead_id')){
                session(['crr_report_lead_id'=>$request->get('crr_report_lead_id')]);
        }elseIf(is_null(session('crr_report_lead_id'))){
            session(['crr_report_lead_id'=>'all']);
        }
        if(session('crr_report_lead_id') !== 'all'){
            $leadEval = '=';
            $leadVal = intval(session('crr_report_lead_id'));
        } else {
            session(['crr_report_lead_id'=>'all']);
            $leadEval = '>';
            $leadVal = 0;
        }

        // Check For Newer Than Selection
        if($request->get('newer_than')){
                // this is only used for checking for updated records
                $newerThan = $request->get('newer_than');
        }else{
            $newerThan = '1900-01-01 00:00:01';
        }
        

        if(!$request->get('check')){
            // if this is just a check - we do not need this information.
            $auditLeads = Audit::select('*')->with('lead')->with('project')->whereNotNull('lead_user_id')->groupBy('lead_user_id')->get();
            $auditProjects = CrrReport::select('*')->with('project')->groupBy('project_id')->get();
            $hfa_users_array = array();
            $projects_array = array();
            foreach ($auditLeads as $hfa) {
                if($hfa->lead_user_id){
                    $hfa_users_array[] = $hfa->lead;
                }
                
            }
            foreach ($auditProjects as $hfa) {
                
                if($hfa->project){
                    $projects_array[] = $hfa->project;
                }
            }
            $hfa_users_array = array_values(Arr::sort($hfa_users_array, function ($value){
                return $value['name'];
            }));
            $projects_array = array_values(Arr::sort($projects_array, function ($value){
                return $value['project_name'];
            }));
            $crrApprovalTypes = CrrApprovalType::orderBy('order')->get();
        }
        //dd($hfa_users_array);
        $reports = CrrReport::where('crr_approval_type_id',$approvalTypeEval,$approvalTypeVal)
                            ->whereNull('template')
                            ->where('project_id',$projectEval,$projectVal)
                            ->where('lead_id',$leadEval,$leadVal)
                            ->where('updated_at','>', $newerThan)
                            ->paginate(150);

        if(count($reports)){
            $newest = $reports->sortByDesc('updated_at');
            $newest = date('Y-m-d G:i:s',strtotime($newest[0]->updated_at));
        } else {
            $newest = null;
        }
            //dd($reports,$approvalTypeVal,$projectVal,$leadVal);
            //return \view('dashboard.index'); //, compact('user')
        if($request->get('check')){
            if(count($reports)){
                return json_encode($reports);
            } else {
                return 1;
            }
        }else if($request->get('rows_only')){
            return view('dashboard.partials.reports-row', compact('reports'));
        }else{
            return view('dashboard.reports', compact('reports','project','hfa_users_array','crrApprovalTypes','projects_array','messages','newest'));
        }
    }

    public function newReportForm(){
        
            // list out templates
            $audits = Audit::where('compliance_run',1)->orderBy('updated_at','desc')->get();
            $templates = CrrReport::where('template',1)->where('active_template',1)->get();

            return view('modals.new-report', compact('templates','audits'));
       
    }
    public function freeTextPlaceHolders(Audit $audit, $string, CrrReport $report){
        //replace string value with current audit values.
        $string = str_replace("||PROJECT NAME||", $audit->project->project_name, $string);
        $string = str_replace("||AUDIT ID||", $audit->id, $string);
        $string = str_replace("||PROJECT NUMBER||", $audit->project->project_name, $string);
        if($audit->start_date){
            $string = str_replace("||REVIEW DATE||", date('m/d/Y',strtotime($audit->start_date, $string)));
        } else {
            $string = str_replace("||REVIEW DATE||", 'START DATE NOT SET', $string);
        }
        $string = str_replace("||TODAY||", date('M d, Y',time()), $string);
            //return ['organization_id'=> $owner_organization_id,'organization'=> $owner_organization, 'name'=>$owner_name, 'email'=>$owner_email, 'phone'=>$owner_phone, 'fax'=>$owner_fax, 'address'=>$owner_address, 'line_1'=>$owner_line_1, 'line_2'=>$owner_line_2, 'city'=>$owner_city, 'state'=>$owner_state, 'zip'=>$owner_zip ];
        $string = str_replace("||OWNER ORGANIZATION NAME||",$audit->project->owner()['organization'], $string);
        $string = str_replace("||OWNER NAME||",$audit->project->owner()['name'], $string);
        $string = str_replace("||OWNER ADDRESS||",$audit->project->owner()['address'], $string);
        $string = str_replace("||OWNER ADDRESS LINE 1||",$audit->project->owner()['line_1'], $string);
        $string = str_replace("||OWNER ADDRESS LINE 2||",$audit->project->owner()['address'], $string);
        $string = str_replace("||OWNER ADDRESS CITY||",$audit->project->owner()['address'], $string);
        $string = str_replace("||OWNER ADDRESS STATE||",$audit->project->owner()['address'], $string);
        $string = str_replace("||OWNER ADDRESS ZIP||",$audit->project->owner()['address'], $string);
        $string = str_replace("||REPORT RESPONSE DUE||",$report->response_due_date, $string);
        if($audit->lead_user_id){
            $string = str_replace("||LEAD NAME||",$audit->lead->full_name(), $string);
        } else {
            $string = str_replace("||LEAD NAME||",'LEAD NOT SET', $string);
        }

        if($report->manager_id){
            $string = str_replace("||MANAGER NAME||",$report->manager->full_name(), $string);
        } else {
            $string = str_replace("||MANAGER NAME||",'REPORT NOT APPROVED', $string);
        }

        // PROGRAMS LIST
        if(strpos($string, '||PROGRAMS||')){
            $programs = $audit->project->programs();
            $programNames = '';
            $totalPrograms = count($programs);
            $programCount = 1;
            forEach($programs as $program){
                $programNames .= $program->program_name;
                if($programCount = count($totalPrograms)-1){
                    $programNames .= " and ";
                }elseif($totalPrograms > 1){
                    $programNames .= ", ";
                }
            }
            if($totalPrograms > 1){
                $programNames .= " programs";
            } else {
                $programNames .= " program";
            }
            $string = str_replace("||PROGRAMS||",$programs, $string);
                
            
        }




    }
    public function setCrrData(CrrReport $template, $reportId = null, $audit = null){
        //get the report parts
        $audit = Audit::find($audit);
        $report = CrrReport::find($reportId);
        if(!is_null($audit)){
            //dd('CREATING REPORT',$audit,$report);
            
            if(is_null($report)){
                //no report yet - let's make one real quick :)
                $report = new CrrReport;
                $report->audit_id = $audit->id;
                $report->lead_id = $audit->lead_user_id;
                $report->project_id = $audit->project_id;
                $report->version = 1;
                $report->crr_approval_type_id = 8; //draft
                $report->from_template_id = $template->id;
                $report->last_updated_by = Auth::user()->id;
                $report->created_by = Auth::user()->id;
                $report->save();
                // record creation history:
                $history = ['date'=>date('m-d-Y g:i a'),'user_id'=>Auth::user()->id,'user_name'=>Auth::user()->full_name(),'note'=>'Created report using template '.$template->template_name.'.'];
                    $this->reportHistory($report,$history);


            } else {
                return 1;
            }
            
            return 1;
        }else{
            return 'Please Select an Audit.';
        }
    }

    public function createNewReport(Request $request){
            //dd($request->input('template_id'), $request->input('audit_id'));
            $template = CrrReport::find($request->input('template_id'));
            if(!is_null($template)){
                if($template->id == 3 ||$template->id == 5 || $template->id == 6  || $template->id == 8 ){
                    // these are document style reports
                    $message = $this->setCrrData($template,null,$request->input('audit_id'));
                } else {
                    // these are data export style reports
                    $message = 'Data options would be here';
                }

                return $message;
            } else {
                return 'Please Select a Report Type';
            }
       
    }

    

   

   

    
    
    

}
