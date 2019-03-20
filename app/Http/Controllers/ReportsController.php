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

class ReportsController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    public function reportHistory(CrrReport $report,$data){
                    $reportHistory = $report->report_history;
                    $reportHistory[] = $data;
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
                $oldDate = $report->response_due_date;

                $updated = $report->update(['response_due_date' =>$dateY.'-'.$dateM.'-'.$dateD.' 18:00:00']);
                if($updated){
                    // Record Historical Record.
                    $history = ['date'=>date('Y-m-d g:i:a'),'user_id'=>Auth::user()->id,'user_name'=>Auth::user()->full_name(),'note'=>'Updated Due Date FROM '.$oldDate.' to '.$dateY.'-'.$dateM.'-'.$dateD.' 18:00:00 '];
                    $this->reportHistory($report,$history);
                    return 'Due Date Updated for Report '.intval($data['id']).' to '.$dateM.'/'.$dateD.'/'.$dateY;

                } else {
                    // Record Historical Record.
                    $history = ['date'=>date('Y-m-d g:i:a'),'user_id'=>Auth::user()->id,'user_name'=>Auth::user()->full_name(),'note'=>'Attempted update to due date failed - value submitted: '.$data['due']];
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
        //dd($hfa_users_array);
        $reports = CrrReport::where('crr_approval_type_id',$approvalTypeEval,$approvalTypeVal)
                            ->whereNull('template')
                            ->where('project_id',$projectEval,$projectVal)
                            ->where('lead_id',$leadEval,$leadVal) 
                            ->paginate(150);
        $crrApprovalTypes = CrrApprovalType::orderBy('order')->get();
        //dd($reports,$approvalTypeVal,$projectVal,$leadVal);
        //return \view('dashboard.index'); //, compact('user')
        return view('dashboard.reports', compact('reports','project','hfa_users_array','crrApprovalTypes','projects_array','messages'));
    }

    

   

   

    
    
    

}
