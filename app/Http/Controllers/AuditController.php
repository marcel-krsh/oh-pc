<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Audit;
use App\Models\Project;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
use App\Models\OrderingBuilding;
use App\Models\OrderingUnit;
use App\Models\OrderingAmenity;
use App\Models\CachedInspection;
use App\Models\CachedAmenity;
use App\Models\CachedComment;
use App\Models\ProjectDetail;
use App\Models\UnitProgram;
use App\Models\GuideStep;
use App\Models\GuideProgress;
use App\Models\ScheduleDay;
use App\Models\ScheduleTime;
use App\Models\AuditAuditor;
use App\Models\Availability;
use App\Models\AmenityInspection;
use App\Models\UnitInspection;
use Auth;
use Session;
use App\LogConverter;
use Carbon;
use Event;

class AuditController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
            Auth::onceUsingId(286); // TEST BRIAN
        }
    }

    public function rerunCompliance(Audit $audit){
        if($audit->findings->count() < 1){
            $audit->rerun_compliance = 1;
            $audit->save();
            return 'No findings! Good to go!';
        }
        
    }
    public function buildingsFromAudit($audit, Request $request)
    {
        $target = $request->get('target');
        $context = $request->get('context');

        // check if user can see that audit TBD
        //

        // count buildings & count ordering_buildings
  
        if (CachedBuilding::where('audit_id', '=', $audit)->count() != OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->count() && CachedBuilding::where('audit_id', '=', $audit)->count() != 0) {
            // this case shouldn't happen
            // delete all ordered records
            // reorder them
            OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->delete();
        }

        if (OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->count() == 0 && CachedBuilding::where('audit_id', '=', $audit)->count() != 0) {
            // if ordering_buildings is empty, create a default entry for the ordering
            $buildings = CachedBuilding::where('audit_id', '=', $audit)->orderBy('id', 'desc')->get();
            
            $i = 1;
            $new_ordering = [];

            foreach ($buildings as $building) {
                $ordering = new OrderingBuilding([
                    'user_id' => Auth::user()->id,
                    'audit_id' => $audit,
                    'building_id' => $building->id,
                    'order' => $i
                ]);
                $ordering->save();
                $i++;
            }
        }
        
        $buildings = OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->orderBy('order', 'asc')->with('building')->get();

        return view('dashboard.partials.audit_buildings', compact('audit', 'target', 'buildings', 'context'));
    }

    public function reorderBuildingsFromAudit($audit, Request $request)
    {
        $building = $request->get('building');
        $index = $request->get('index');

        // select all building orders except for the one we want to reorder
        $current_ordering = OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->where('building_id', '!=', $building)->orderBy('order', 'asc')->get()->toArray();

        $inserted = [ [
                    'user_id' => Auth::user()->id,
                    'audit_id' => $audit,
                    'building_id' => $building,
                    'order' => $index
               ]];

        // insert the building ordering in the array
        $reordered_array = $current_ordering;
        array_splice($reordered_array, $index, 0, $inserted);

        // delete previous ordering
        OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->delete();

        // clean-up the ordering and store
        foreach ($reordered_array as $key => $ordering) {
            $new_ordering = new OrderingBuilding([
                'user_id' => $ordering['user_id'],
                'audit_id' => $ordering['audit_id'],
                'building_id' => $ordering['building_id'],
                'order' => $key+1
            ]);
            $new_ordering->save();
        }
    }

    public function reorderUnitsFromAudit($audit, $building, Request $request)
    {

        $unit = $request->get('unit');
        $index = $request->get('index');

        // select all building orders except for the one we want to reorder
        $current_ordering = OrderingUnit::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->where('building_id', '=', $building)->where('unit_id', '!=', $unit)->orderBy('order', 'asc')->get()->toArray();

        $inserted = [ [
                    'user_id' => Auth::user()->id,
                    'audit_id' => $audit,
                    'building_id' => $building,
                    'unit_id' => $unit,
                    'order' => $index
               ]];

        // insert the building ordering in the array
        $reordered_array = $current_ordering;
        array_splice($reordered_array, $index, 0, $inserted);

        // delete previous ordering
        OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->delete();

        // clean-up the ordering and store
        foreach ($reordered_array as $key => $ordering) {
            $new_ordering = new OrderingUnit([
                'user_id' => $ordering['user_id'],
                'audit_id' => $ordering['audit_id'],
                'building_id' => $ordering['building_id'],
                'unit_id' => $ordering['unit_id'],
                'order' => $key+1
            ]);
            $new_ordering->save();
        }
    }

    public function getProjectContact(Project $project) {
        
        return view('modals.project-contact', compact('project'));
    }

    public function detailsFromBuilding($audit, $building, Request $request)
    {
        //dd(OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->count(), CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->count(), CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->count());
        $target = $request->get('target');
        $targetaudit = $request->get('targetaudit');
        $context = $request->get('context');

        // check if user can see that audit
        //

        // count buildings & count ordering_buildings
        if (OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->count() == 0 && CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->count() != 0) {
            // if ordering_buildings is empty, create a default entry for the ordering
            $details = CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->orderBy('id', 'desc')->get();
            
            $i = 1;
            $new_ordering = [];

            foreach ($details as $detail) {
                $ordering = new OrderingUnit([
                    'user_id' => Auth::user()->id,
                    'audit_id' => $audit,
                    'building_id' => $detail->building_id,
                    'unit_id' => $detail->id,
                    'order' => $i
                ]);
                $ordering->save();
                $i++;
            }
        } elseif (CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->count() != OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->count() && CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->count() != 0) {
            $details = null; 
        }


        $details = OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->orderBy('order', 'asc')->with('unit')->get();

foreach($details as $detail){
    dd($detail);
}
        return view('dashboard.partials.audit_building_details', compact('audit', 'target', 'building', 'details', 'targetaudit', 'context'));
    }

    public function inspectionFromBuilding($audit_id, $building_id, Request $request)
    {
        $target = $request->get('target');
        $rowid = $request->get('rowid');
        $context = $request->get('context');
        $inspection = "test";

        $audit = Audit::where('id','=',$audit_id)->first();
        
        // we may not user CachedInspection...
        if(CachedInspection::first()){
            $data['detail'] = CachedInspection::first();
            $data['menu'] = $data['detail']->menu_json;
        }else{
            $data['detail'] = null;
            $data['menu'] = null;
        }

        // forget cachedinspection, populate without
        // details: unit_id, building_id, project_id
        $data['detail'] = collect([
            'unit_id' => null,
            'building_id' => $building_id,
            'project_id' => $audit->project_id
        ]);

        $data['menu'] = collect([
            ['name' => 'SITE AUDIT', 'icon' => 'a-mobile-home', 'status' => 'critical active', 'style' => '', 'action' => 'site_audit'],
            ['name' => 'FILE AUDIT', 'icon' => 'a-folder', 'status' => 'action-required', 'style' => '', 'action' => 'file_audit'],
            ['name' => 'SUBMIT', 'icon' => 'a-avatar-star', 'status' => 'in-progress', 'style' => 'margin-top:30px;', 'action' => 'submit']
        ]);

        // $data['amenities'] = CachedAmenity::where('audit_id', '=', $audit_id)->where('building_id', '=', $building_id)->get();
        // count amenities & count ordering_amenities
        $ordered_amenities_count = OrderingAmenity::where('audit_id', '=', $audit_id)->where('user_id', '=', Auth::user()->id);
        if ($building_id) {
            $ordered_amenities_count = $ordered_amenities_count->where('building_id', '=', $building_id);
        }
            $ordered_amenities_count = $ordered_amenities_count->count();

        // $amenities_count = CachedAmenity::where('audit_id', '=', $audit_id);
        $amenities_count = AmenityInspection::where('audit_id', '=', $audit_id);
        if ($building_id) {
            $amenities_count = $amenities_count->where('building_id', '=', $building_id);
        }
            $amenities_count = $amenities_count->count();

        if ($amenities_count != $ordered_amenities_count && $amenities_count != 0) {
            // this shouldn't happen
            // reset ordering
            // $amenities_to_reset = CachedAmenity::where('audit_id', '=', $audit_id);
            $amenities_to_reset = AmenityInspection::where('audit_id', '=', $audit_id);
            if ($building_id) {
                $amenities_to_reset = $amenities_to_reset->where('building_id', '=', $building_id);
            }
                $amenities_to_reset = $amenities_to_reset->delete();
        }

        if ($ordered_amenities_count == 0 && $amenities_count != 0) {
            // if ordering_amenities is empty, create a default entry for the ordering
            // $amenities = CachedAmenity::where('audit_id', '=', $audit_id);
            $amenities = AmenityInspection::where('audit_id', '=', $audit_id);
            if ($building_id) {
                $amenities = $amenities->where('building_id', '=', $building_id);
            }
                $amenities = $amenities->orderBy('id', 'desc')->get();
            
            $i = 1;
            $new_ordering = [];

            foreach ($amenities as $amenity) {
                $ordering = new OrderingAmenity([
                    'user_id' => Auth::user()->id,
                    'audit_id' => $audit_id,
                    'building_id' => $building_id,
                    'amenity_id' => $amenity->id,
                    'order' => $i
                ]);
                $ordering->save();
                $i++;
            }
        }

        $amenities = OrderingAmenity::where('audit_id', '=', $audit_id)->where('user_id', '=', Auth::user()->id);
        if ($building_id) {
            $amenities = $amenities->where('building_id', '=', $building_id);
        }
                $amenities = $amenities->orderBy('order', 'asc')->with('amenity')->get()->pluck('amenity')->flatten();

        $data['amenities'] = $amenities;

        $data['comments'] = CachedComment::where('parent_id', '=', null)->with('replies')->get();

        //dd("new approach");

        return response()->json($data);
        //return view('dashboard.partials.audit_building_inspection', compact('audit_id', 'target', 'detail_id', 'building_id', 'detail', 'inspection', 'areas', 'rowid'));
    }

    public function inspectionFromBuildingDetail($audit_id, $building_id, $detail_id, Request $request)
    {
        $target = $request->get('target');
        $rowid = $request->get('rowid');
        $context = $request->get('context');
        $inspection = "test";

        // Fetch inspection data from different models:
        // $details (cached_audit_inspections)
        // $areas (cached_audit_inspection_areas)
        // $comments (cached_audit_inspection_comments)
        
        //$data['detail'] = CachedInspection::where('audit_id', '=', $audit_id)->where('building_id', '=', $building_id)->get();
        $data['detail'] = CachedInspection::first();

        $data['menu'] = $data['detail']->menu_json;

        $data['amenities'] = CachedAmenity::where('audit_id', '=', $audit_id)->where('building_id', '=', $building_id)->get();
        // $data['amenities'] = CachedAmenity::get()->toArray();

        $data['comments'] = CachedComment::where('parent_id', '=', null)->with('replies')->get();

        return response()->json($data);
        //return view('dashboard.partials.audit_building_inspection', compact('audit_id', 'target', 'detail_id', 'building_id', 'detail', 'inspection', 'areas', 'rowid'));
    }

    public function getProject($id = null)
    {
        $project = Project::where('project_key','=',$id)->first();
        $projectId = $project->id;
        
        // the project tab has a audit selection to display previous audit's stats, compliance info and assignments.
        
        $projectTabs = collect([
                ['title' => 'Details', 'icon' => 'a-clipboard', 'status' => '', 'badge' => '', 'action' => 'project.details'],
                ['title' => 'Communications', 'icon' => 'a-envelope-incoming', 'status' => '', 'badge' => '', 'action' => 'project.communications'],
                ['title' => 'Documents', 'icon' => 'a-file-clock', 'status' => '', 'badge' => '', 'action' => 'project.documents'],
                ['title' => 'Notes', 'icon' => 'a-file-text', 'status' => '', 'badge' => '', 'action' => 'project.notes'],
                // ['title' => 'Comments', 'icon' => 'a-comment-text', 'status' => '', 'badge' => '', 'action' => 'project.comments'],
                // ['title' => 'Photos', 'icon' => 'a-picture', 'status' => '', 'badge' => '', 'action' => 'project.photos'],
                // ['title' => 'Findings', 'icon' => 'a-mobile-info', 'status' => '', 'badge' => '', 'action' => 'project.findings'],
                // ['title' => 'Follow-ups', 'icon' => 'a-bell-ring', 'status' => '', 'badge' => '', 'action' => 'project.followups'],
                ['title' => 'Audit Stream', 'icon' => 'a-mobile-info', 'status' => '', 'badge' => '', 'action' => 'project.stream'],
                ['title' => 'Reports', 'icon' => 'a-file-chart-3', 'status' => '', 'badge' => '', 'action' => 'project.reports'],
            ]);
        $tab = 'project-detail-tab-1';

        return view('projects.project', compact('tab', 'projectTabs', 'projectId'));
    }

    public function getProjectTitle($id = null)
    {

        $project_number = Project::where('project_key','=',$id)->first()->project_number;

        $audit = CachedAudit::where('project_key', '=', $id)->orderBy('id', 'desc')->first();

        // TBD add step to title
        $step = $audit->step_status_text; //  :: CREATED DYNAMICALLY FROM CONTROLLER
        $step_icon = $audit->step_status_icon;

        return '<i class="a-mobile-repeat"></i><i class="'.$step_icon.'"></i> <span class="list-tab-text"> PROJECT '. $project_number." ".$step.'</span>';
    }

    public function getProjectDetails($id = null)
    {
        // the project tab has a audit selection to display previous audit's stats, compliance info and assignments.
          
        $project = Project::where('id','=',$id)->first();

        // which audit is selected (latest if no selection)
        $selected_audit = $project->selected_audit();

        // get that audit's stats and contact info from the project_details table
        $details = $project->details();

        // get the list of all audits for this project
        $audits = $project->audits;
        //dd($selected_audit->checkStatus('schedules'));
       
        return view('projects.partials.details', compact('details', 'audits', 'project', 'selected_audit'));
    }

    public function getProjectDetailsInfo($id, $type)
    {
        // types: compliance, assignment, findings, followups, reports, documents, comments, photos
        // project: project_id?

        $project = Project::where('id','=',$id)->first();
        //dd($project->selected_audit());

        switch ($type) {
            case 'compliance':
                // get the compliance summary for this audit
                // 
                $audit = $project->selected_audit()->audit; 
                $selection_summary = json_decode($audit->selection_summary, 1); //dd($selection_summary);

                $data = [
                    "project" => [
                        'id' => $project->id
                    ]
                ];

                /*
                    the output of the compliance process should produce the "required units" count. Then the selected should be the same unless they changed some units. That would increase the value of needed units.

                    Inspected units are counted when the inspection is completed for that unit. 
                    To be inspected units is the balance.

                    A unit is complete once all of its amenities have been marked complete - it has a completed date on it

                 */

                $summary_required = 0;
                $summary_selected = 0;
                $summary_needed = 0;
                $summary_inspected = 0;
                $summary_to_be_inspected = 0;
                $summary_optimized_remaining_inspections = 0;
                $summary_optimized_sample_size = 0;
                $summary_optimized_completed_inspections = 0;

                // create stats for each program
                foreach($selection_summary['programs'] as $program){

                    // count selected units using the list of program ids
                    $program_keys = explode(',', $program['program_keys']); 
                    $selected_units = UnitInspection::whereIn('program_key', $program_keys)->where('group_id', '=', $program['group'])->count();

                    if($program['group'] == 7){
                        //dd( UnitInspection::whereIn('program_key', $program_keys)->where('group_id', '=', $program['group'])->get());
                    }

                    $needed_units = $program['totals_after_optimization'] - $selected_units;

                    $unit_keys = $program['units_after_optimization']; 
                    $inspected_units = UnitInspection::whereIn('unit_key', $unit_keys)
                                ->where('group_id', '=', $program['group'])
                                // ->whereHas('amenity_inspections', function($query) {
                                //     $query->where('completed_date_time', '!=', null);
                                // })
                                ->where('complete', '!=', null)
                                ->count();

                    $to_be_inspected_units = $program['totals_after_optimization'] - $inspected_units;


                    $data['programs'][] = [
                        'id' => $program['group'],
                        'name' => $program['name'],
                        'pool' => $program['pool'],
                        'comments' => $program['comments'],
                        'user_limiter' => $program['use_limiter'],
                        'totals_after_optimization' => $program['totals_after_optimization'],
                        'units_before_optimization' => $program['units_before_optimization'],
                        'totals_before_optimization' => $program['totals_before_optimization'],
                        'required_units' => $program['totals_after_optimization'],
                        'selected_units' => $selected_units,
                        'needed_units' => $needed_units,
                        'inspected_units' => $inspected_units,
                        'to_be_inspected_units' => $to_be_inspected_units
                    ];

                    $summary_required = $summary_required + $program['totals_before_optimization'];
                    $summary_selected = $summary_selected + $selected_units;
                    $summary_needed = $summary_needed + $needed_units;
                    $summary_inspected = $summary_inspected + $inspected_units;
                    $summary_to_be_inspected = $summary_to_be_inspected + $to_be_inspected_units;

                    $summary_optimized_sample_size = $summary_optimized_sample_size + $program['totals_after_optimization'];
                    $summary_optimized_completed_inspections = $summary_optimized_completed_inspections + $inspected_units;
                    $summary_optimized_remaining_inspections = $summary_optimized_sample_size - $summary_optimized_completed_inspections = $summary_optimized_completed_inspections + $inspected_units;
                }

                $data['summary'] = [
                        'required_unit_selected' => 0,
                        'inspectable_areas_assignment_needed' => 12,
                        'required_units_selection' => 13,
                        'file_audits_needed' => 14,
                        'physical_audits_needed' => 15,
                        'schedule_conflicts' => 16,
                        'required_units' => $summary_required,
                        'selected_units' => $summary_selected,
                        'needed_units' => $summary_needed,
                        'inspected_units' => $summary_inspected,
                        'to_be_inspected_units' => $summary_to_be_inspected,
                        'optimized_sample_size' => $summary_optimized_sample_size,
                        'optimized_completed_inspections' => $summary_optimized_completed_inspections,
                        'optimized_remaining_inspections' => $summary_optimized_remaining_inspections
                ];

                // 
                
                break;
            case 'assignment':

                // check if the lead is listed as an auditor and add it if needed
                $auditors = $project->selected_audit()->auditors;
                $is_lead_an_auditor = 0;
                $auditors_key = array(); // used to store in which order the auditors will be displayed
                if($project->selected_audit()->lead_auditor){
                    $auditors_key[] = $project->selected_audit()->lead_auditor->id;
                }

                foreach($auditors as $auditor){
                    if($project->selected_audit()->lead_auditor){
                        if($project->selected_audit()->lead_auditor->id == $auditor->user_id){
                            $is_lead_an_auditor = 1;
                        }else{
                            $auditors_key[] = $auditor->user_id;
                        }
                    }else{
                        $auditors_key[] = $auditor->user_id;
                    }
                }
                if($is_lead_an_auditor == 0 && $project->selected_audit()->lead_auditor){
                    // add to audit_auditors
                    $new_auditor = new AuditAuditor([
                        'user_id' => $project->selected_audit()->lead_auditor->id,
                        'user_key' => $project->selected_audit()->lead_auditor->devco_key,
                        'monitoring_key' => $project->selected_audit()->audit_key,
                        'audit_id' => $project->selected_audit()->audit_id
                    ]);
                    $new_auditor->save();
                }

                $chart_data = $project->selected_audit()->estimated_chart_data(); 


                //foreach auditor and for each day, fetch calendar combining availability and schedules
                $daily_schedules = array();
                foreach($project->selected_audit()->days as $day){
                    $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $day->date);
                    foreach($auditors_key as $auditor_id){
                        $daily_schedules[$day->id][] = $this->getAuditorDailyCalendar($date, $day->id, $project->selected_audit()->audit_id, $auditor_id);
                    }
                }

                // list all the audits that have any of the auditors assigned
                // foreach day
                // $potential_conflict_audits_ids = array();
                // foreach($project->selected_audit()->days as $day){
                //     $potential_conflict_audits = ScheduleTime::select('audit_id')->whereIn('auditor_id', $auditors_key)->where('day_id','=',$day->id)->groupBy('audit_id')->pluck('audit_id')->toArray();

                //     $potential_conflict_audits_ids = array_unique(array_merge($potential_conflict_audits_ids,$potential_conflict_audits), SORT_REGULAR);
                // }              

                // for each audit, and using the auditors key as the order, check if auditor is scheduled, not scheduled or not at all involved with the audit
                // also get the audits information for display (date, project name, etc)
                // $potential_conflict_audits = CachedAudit::whereIn('audit_id', $potential_conflict_audits_ids)->orderBy('project_ref','asc')->get();
                
                // $daily_schedules = array();
                // foreach($project->selected_audit()->days as $day){
                //     // set current audit $project->selected_audit()
                //     foreach($auditors_key as $auditor_id){
                //         // auditors are in the audit for sure
                //         // check if they are scheduled on not
                //         if(ScheduleTime::where('audit_id','=',$project->selected_audit()->audit_id)->where('auditor_id','=',$auditor_id)->where('day_id','=',$day->id)->count()){
                //             $daily_schedules[$day->id][$project->selected_audit()->audit_id]['auditors'][$auditor_id] = 'scheduled'; // scheduled
                //         }else{
                //             $daily_schedules[$day->id][$project->selected_audit()->audit_id]['auditors'][$auditor_id] = 'notscheduled'; // not scheduled
                //         }
                //         $daily_schedules[$day->id][$project->selected_audit()->audit_id]['audit'] = $project->selected_audit();
                //     }

                //     // set all other audits
                //     foreach($potential_conflict_audits as $potential_conflict_audit){
                //         if($potential_conflict_audit->audit_id != $project->selected_audit()->audit_id){
                //             foreach($auditors_key as $auditor_id){
                //                 // is auditor in the audit?
                //                 if(AuditAuditor::where('audit_id','=',$potential_conflict_audit->audit_id)->where('user_id','=',$auditor_id)->count()){
                //                     if(ScheduleTime::where('audit_id','=',$potential_conflict_audit->audit_id)->where('auditor_id','=',$auditor_id)->where('day_id','=',$day->id)->count()){
                //                         $daily_schedules[$day->id][$potential_conflict_audit->audit_id]['auditors'][$auditor_id] = 'scheduled'; // scheduled
                //                     }else{
                //                         $daily_schedules[$day->id][$potential_conflict_audit->audit_id]['auditors'][$auditor_id] = 'notscheduled'; // not scheduled
                //                     }
                //                 }else{
                //                     $daily_schedules[$day->id][$potential_conflict_audit->audit_id]['auditors'][$auditor_id] = 'notinaudit';
                //                 }
                //                 $daily_schedules[$day->id][$potential_conflict_audit->audit_id]['audit'] = $potential_conflict_audit;
                //             }
                //         }
                //     }
                // }

                $data = collect([
                    "project" => [
                        'id' => $project->id,
                        'ref' => $project->project_number,
                        'audit_id' => $project->selected_audit()->audit_id
                    ],
                    "summary" => [
                        'required_unit_selected' => 0,
                        'inspectable_areas_assignment_needed' => 0,
                        'required_units_selection' => 0,
                        'file_audits_needed' => 0,
                        'physical_audits_needed' => 0,
                        'schedule_conflicts' => 0,
                        'estimated' => $project->selected_audit()->estimated_hours().':'.$project->selected_audit()->estimated_minutes(),
                        'estimated_hours' => $project->selected_audit()->estimated_hours(),
                        'estimated_minutes' => $project->selected_audit()->estimated_minutes(),
                        'needed' => $project->selected_audit()->hours_still_needed()
                    ],
                    'audits' => [
                        [
                            'id' => '19200114',
                            'ref' => '111111',
                            'date' => '12/22/2018',
                            'name' => 'The Garden Oaks',
                            'street' => '123466 Silvegwood Street',
                            'city' => 'Columbus',
                            'state' => 'OH',
                            'zip' => '43219',
                            'lead' => 2, // user_id
                            'schedules' => [
                                ['icon' => 'a-circle', 'status' => '', 'is_lead' => 1, 'tooltip' =>''],
                                ['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' =>''],
                                ['icon' => 'a-circle', 'status' => '', 'is_lead' => 0, 'tooltip' =>''],
                                ['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' =>'']
                            ]
                        ]
                    ]
                ]);

                return view('projects.partials.details-assignment', compact('data', 'project', 'chart_data', 'auditors_key', 'daily_schedules'));

                break;
            case 'findings':
                break;
            case 'followups':
                break;
            case 'reports':
                break;
            case 'documents':
                break;
            case 'comments':
                break;
            case 'photos':
                break;
            default:
        }

        return view('projects.partials.details-'.$type, compact('data', 'project'));
    }

    public function getAuditorDailyCalendar($date, $day_id, $audit_id, $auditor_id) {

        $events_array = array();
        $availabilities = Availability::where('user_id', '=', $auditor_id)
                            ->where('date', '=', $date->format('Y-m-d'))
                            ->orderBy('start_slot', 'asc')
                            ->get();

        $a_array = $availabilities->toArray();

        // $day = ScheduleDay::where('id','=',$day_id)->first();

        $schedules = ScheduleTime::where('auditor_id', '=', $auditor_id)
                            ->where('day_id', '=', $day_id)
                            ->orderBy('start_slot', 'asc')
                            ->get();

        $s_array = $schedules->toArray();

       //dd($a_array, $s_array);

        // we check chronologically by slot of 15 min
        // detect if slot is at the beginning of a scheduled time, add that schedule to the event array and set slot to the end of the scheduled event. Also if there are variables for avail start and span (see below) then add the availability before and reset those variables.
        // detect if slot is inside an availability, save start in a variable and span in another
        // detect if no avail or no schedule, if there are variables for avail start and span, add avail, reset them. slot++.
        
        $slot = 1;
        $check_avail_start = null;
        $check_avail_span = null;

        // get user default address and compare with project's address for estimated driving times
        $auditor = User::where('id','=',$auditor_id)->first();
        $default_address = $auditor->default_address();
        $distanceAndTime = $auditor->distanceAndTime($audit_id);
        if($distanceAndTime){
            // round up to the next 15 minute slot
            $minutes = intval($distanceAndTime[2] / 60, 10);
            $travel_time = ($minutes - ($minutes % 15) + 15) / 15; // time in 15 min slots
        }else{
            $travel_time = null;
        }

        while($slot <= 60){
            $skip = 0;

            // Is slot the start of an event
            // if there is check_avail_start and check_avail_span, add avail and reset them.
            // add travel event
            // add event
            // reset slot to the end of the event
            foreach($s_array as $s){
                if($audit_id == $s['audit_id']){
                    $thisauditclass="thisaudit";
                }else{
                    $thisauditclass="";
                }

                if($slot == $s['start_slot'] - $s['travel_span'] ){
                    // save any previous availability
                    if($check_avail_start != null && $check_avail_span != null){

                        $hours = sprintf("%02d",  floor(($check_avail_start - 1) * 15 / 60) + 6);
                        $minutes = sprintf("%02d", ($check_avail_start - 1) * 15 % 60);
                        $start_time = formatTime($hours.':'.$minutes.':00', 'H:i:s');
                        $hours = sprintf("%02d",  floor(($check_avail_start + $check_avail_span - 1) * 15 / 60) + 6);
                        $minutes = sprintf("%02d", ($check_avail_start + $check_avail_span - 1) * 15 % 60);
                        $end_time = formatTime($hours.':'.$minutes.':00', 'H:i:s');
                        
                        $events_array[] = [
                            "id" => uniqid(),
                            "auditor_id" => $auditor_id,
                            "audit_id" => $audit_id,
                            "status" => "",
                            "travel_time" => $travel_time,
                            "start_time" => strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$start_time)->format('h:i A')),
                            "end_time" => strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$end_time)->format('h:i A')),
                            "start" => $check_avail_start,
                            "span" =>  $check_avail_span,
                            "travel_span" =>  null,
                            "icon" => "a-circle-plus",
                            "class" => "available no-border-top no-border-bottom",
                            "modal_type" => "addschedule",
                            "tooltip" => "AVAILABLE TIME ".strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$start_time)->format('h:i A'))." ".strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$end_time)->format('h:i A'))
                        ];

                        $check_avail_start = null;
                        $check_avail_span = null;
                    }

                    // save travel
                    if($s['travel_span'] > 0){

                        $hours = sprintf("%02d",  floor(($s['start_slot'] - $s['travel_span'] - 1) * 15 / 60) + 6);
                        $minutes = sprintf("%02d", ($s['start_slot'] - $s['travel_span'] - 1) * 15 % 60);
                        $start_time = formatTime($hours.':'.$minutes.':00', 'H:i:s');
                        $hours = sprintf("%02d",  floor(($s['start_slot'] - 1) * 15 / 60) + 6);
                        $minutes = sprintf("%02d", ($s['start_slot'] - 1) * 15 % 60);
                        $end_time = formatTime($hours.':'.$minutes.':00', 'H:i:s');

                        $events_array[] = [
                            "id" => uniqid(),
                            "auditor_id" => $auditor_id,
                            "audit_id" => $audit_id,
                            "status" => "",
                            "travel_time" => "",
                            "start_time" => strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$start_time)->format('h:i A')),
                            "end_time" => strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$end_time)->format('h:i A')),
                            "start" => $s['start_slot'] - $s['travel_span'],
                            "span" =>  $s['travel_span'],
                            "travel_span" =>  null,
                            "icon" => "",
                            "class" => "travel ".$thisauditclass,
                            "modal_type" => "",
                            "tooltip" => "TRAVEL TIME ".strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$start_time)->format('h:i A'))." ".strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$end_time)->format('h:i A'))
                        ];
                        $travelclass = " no-border-top";
                    }else{
                        $travelclass = "";
                    }

                    // save schedule
                    $events_array[] = [
                        "id" => $s['id'],
                        "auditor_id" => $auditor_id,
                        "audit_id" => $audit_id,
                        "status" => "",
                        "travel_time" => "",
                        "start_time" => strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$s['start_time'])->format('h:i A')),
                        "end_time" => strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$s['end_time'])->format('h:i A')),
                        "start" => $s['start_slot'],
                        "span" =>  $s['span'],
                        "travel_span" =>  null,
                        "icon" => "a-mobile-checked",
                        "class" => "schedule ".$thisauditclass.$travelclass,
                        "modal_type" => "removeschedule",
                        "tooltip" => "SCHEDULED TIME ".strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$s['start_time'])->format('h:i A'))." ".strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$s['end_time'])->format('h:i A'))
                    ];

                    // reset slot to the just after the scheduled time
                    $slot = $s['start_slot'] + $s['span'];
                    $skip = 1;
                }
            }

            // Is slot within an availability
            // if there is already check_avail_start, only update check_avail_span, otherwise save both. slot++
            if(!$skip){
                foreach($a_array as $a){
                    if($slot >= $a['start_slot'] && $slot < $a['start_slot'] + $a['span']){
                        if($check_avail_start != null && $check_avail_span != null){
                            $check_avail_span++;
                        }else{
                            $check_avail_start = $slot;
                            $check_avail_span = 1;
                        }
                        $slot++;
                        $skip = 1;
                    }
                }
            }

            // Is slot in nothing
            // Are there check_avail_start and check_avail_span? If so add avail to events and reset the variables. slot++
            if(!$skip){
                if($check_avail_start != null && $check_avail_span != null){
                    $hours = sprintf("%02d",  floor(($check_avail_start - 1) * 15 / 60) + 6);
                    $minutes = sprintf("%02d", ($check_avail_start - 1) * 15 % 60);
                    $start_time = formatTime($hours.':'.$minutes.':00', 'H:i:s');
                    $hours = sprintf("%02d",  floor(($check_avail_start + $check_avail_span - 1) * 15 / 60) + 6);
                    $minutes = sprintf("%02d", ($check_avail_start + $check_avail_span - 1) * 15 % 60);
                    $end_time = formatTime($hours.':'.$minutes.':00', 'H:i:s');

                    $events_array[] = [
                        "id" => uniqid(),
                        "auditor_id" => $auditor_id,
                        "audit_id" => $audit_id,
                        "status" => "",
                        "travel_time" => $travel_time,
                        "start_time" => strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$start_time)->format('h:i A')),
                        "end_time" => strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$end_time)->format('h:i A')),
                        "start" => $check_avail_start,
                        "span" =>  $check_avail_span,
                        "travel_span" =>  null,
                        "icon" => "a-circle-plus",
                        "class" => "available no-border-top no-border-bottom",
                        "modal_type" => "addschedule",
                        "tooltip" => "AVAILABLE TIME ".strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$start_time)->format('h:i A'))." ".strtoupper(Carbon\Carbon::createFromFormat('H:i:s',$end_time)->format('h:i A'))
                    ];

                    $check_avail_start = null;
                    $check_avail_span = null;
                    $slot++;
                }else{
                    $slot++;
                }
            }
        }

        $header[] = $date->copy()->format('m/d');

        if(count($events_array)){
            
            // figure out the before and after areas on the schedule
            $start_slot = 60;
            $end_slot = 1;
            foreach($events_array as $e){
                if($e['start'] <= $start_slot) $start_slot = $e['start'];
                if($e['start'] + $e['span'] >= $end_slot) $end_slot = $e['start'] + $e['span'];
            }

            $before_time_start = 1;
            $before_time_span = $start_slot - 1;
            $after_time_start = $end_slot;
            $after_time_span = 61-$end_slot;
            $no_availability = 0;
        }else{
            $events_array = [];
            $before_time_start = 1;
            $before_time_span = 0;
            $after_time_start = 60;
            $after_time_span = 1;
            $no_availability = 1;
        }

        $days = [ 
            "date" => $date->copy()->format('m/d'), 
            "date_formatted" => $date->copy()->format('F j, Y'), 
            "date_formatted_name" => strtolower($date->copy()->englishDayOfWeek), 
            "no_availability" => $no_availability,
            "before_time_start" => $before_time_start,
            "before_time_span" => $before_time_span,
            "after_time_start" => $after_time_start,
            "after_time_span" => $after_time_span,
            "events" => $events_array
        ];


        $calendar = [
            "header" => $header,
            "content" => $days
        ];

         return $calendar;
    }

    public function deleteSchedule(Request $request, $event_id){
        // TBD check users
        $current_user = Auth::user();

        $event = ScheduleTime::where('id','=',$event_id)->first();

        // user needs to be the lead
        // TBD add manager/roles
        if($event && $event->cached_audit->lead == $current_user->id){
            $event->delete();
            return 1;
        }

        return 0;
    }

    public function scheduleAuditor(Request $request, $audit_id, $day_id, $auditor_id){
        // TBD user check
        
        $start = $request->get('start');
        $duration = $request->get('duration');
        $travel = $request->get('travel');

        $hours = sprintf("%02d",  floor(($start - 1) * 15 / 60) + 6);
        $minutes = sprintf("%02d", ($start - 1) * 15 % 60);
        $start_time = formatTime($hours.':'.$minutes.':00', 'H:i:s');

        $hours = sprintf("%02d",  floor(($start + $duration - 1) * 15 / 60) + 6);
        $minutes = sprintf("%02d", ($start + $duration - 1) * 15 % 60);
        $end_time = formatTime($hours.':'.$minutes.':00', 'H:i:s');

        $new_schedule = new ScheduleTime([
            'audit_id' => $audit_id,
            'day_id' => $day_id,
            'auditor_id' => $auditor_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'start_slot' => $start,
            'span' => $duration,
            'travel_span' => $travel
        ]);
        $new_schedule->save();

        return 1;
    }

    public function addADay(Request $request, $id){
        // TBD only authorized users can add days (lead/managers)
        
        $audit = Audit::where('id','=',$id)->first();
        $date = formatDate($request->get('date'), "Y-m-d H:i:s", "F d, Y");

        $day = new ScheduleDay([
            'audit_id' => $id,
            'date' => $date
        ]);
        $day->save();

        return 1;
    }

    public function deleteDay(Request $request, $id, $day_id){
        // TBD only authorized users can add days (lead/managers)
         
        // 1. delete schedules
        // 2. delete day
        // 3. update estimated needed time and checks by rebuilding CachedAudit 

        $audit = Audit::where('id','=',$id)->first();
        $schedules = ScheduleTime::where('day_id','=',$day_id)->where('audit_id','=',$id)->delete();
        $day = ScheduleDay::where('id','=',$day_id)->where('audit_id','=',$id)->delete();
 
        // Event::fire('audit.cache', $audit->audit);

        $output = ['data' => 1];
        return $output;
    }

    public function saveEstimatedHours(Request $request, $id){ 
        // audit id
        $forminputs = $request->get('inputs');
        parse_str($forminputs, $forminputs);

        $hours = (int) $forminputs['estimated_hours'];
        $minutes = (int) $forminputs['estimated_minutes'];

        $audit = CachedAudit::where('audit_id','=',$id)->where('lead','=',Auth::user()->id)->first();

        $new_estimate = $hours.":".$minutes.":00";

        if($audit){
            $audit->update([
                'estimated_time' => $new_estimate
            ]);

            // get new needed time
            $audit->fresh();

            $needed = $audit->hours_still_needed();

            return ['status'=>1, 'hours'=> $hours.":".$minutes, 'needed'=>$needed];
        }else{
            return ['status'=>0, 'message'=>'Sorry, this audit reference cannot be found or no lead has been set yet.'];
        }

        
    }

    public function getProjectDetailsAssignmentSchedule($project, $dateid)
    {

        $data = collect([
            "project" => [
                'id' => 1
            ],
            "summary" => [
                'required_unit_selected' => 0,
                'inspectable_areas_assignment_needed' => 12,
                'required_units_selection' => 13,
                'file_audits_needed' => 14,
                'physical_audits_needed' => 15,
                'schedule_conflicts' => 16,
                'estimated' => '107:00',
                'estimated_minutes' => '',
                'needed' => '27:00',
            ],
            'auditors' => [
                [
                    'id' => 1,
                    'name' => 'Brian Greenwood',
                    'initials' => 'BG',
                    'color' => 'pink'
                ],
                [
                    'id' => 2,
                    'name' => 'Brianna Bluewood',
                    'initials' => 'BB',
                    'color' => 'blue'
                ],
                [
                    'id' => 3,
                    'name' => 'John Smith',
                    'initials' => 'JS',
                    'color' => 'black'
                ],
                [
                    'id' => 4,
                    'name' => 'Sarah Connor',
                    'initials' => 'SC',
                    'color' => 'red'
                ]
            ],
            "days" => [
                [
                    'id' => 6,
                    'date' => '12/22/2018',
                    'status' => 'action-required',
                    'icon' => 'a-avatar-fail'
                ],
                [
                    'id' => 7,
                    'date' => '12/23/2018',
                    'status' => 'ok-actionable',
                    'icon' => 'a-avatar-approve'
                ]
            ],
            'projects' => [
                [
                    'id' => '19200114',
                    'date' => '12/22/2018',
                    'name' => 'The Garden Oaks',
                    'street' => '123466 Silvegwood Street',
                    'city' => 'Columbus',
                    'state' => 'OH',
                    'zip' => '43219',
                    'lead' => 2, // user_id
                    'schedules' => [
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT']
                    ]
                ],
                [
                    'id' => '19200115',
                    'date' => '12/22/2018',
                    'name' => 'The Garden Oaks 2',
                    'street' => '123466 Silvegwood Street',
                    'city' => 'Columbus',
                    'state' => 'OH',
                    'zip' => '43219',
                    'lead' => 1, // user_id
                    'schedules' => [
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT']
                    ]
                ],
                [
                    'id' => '19200116',
                    'date' => '12/22/2018',
                    'name' => 'The Garden Oaks 3',
                    'street' => '123466 Silvegwood Street',
                    'city' => 'Columbus',
                    'state' => 'OH',
                    'zip' => '43219',
                    'lead' => 2, // user_id
                    'schedules' => [
                        ['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' =>'APPROVE SCHEDULE CONFLICT']
                    ]
                ]
            ]
        ]);
        return view('projects.partials.details-assignment-schedule', compact('data'));
    }

    // public function getProjectCommunications ( $project = null, $page=0 ) {

    //     $data = [];
    //     return view('projects.partials.communications', compact('data'));
    // }

    public function getProjectDocuments($project = null)
    {
        if (!is_null($project)) {
            $documents = \App\Models\Document::where('project_id', $project->id);
            return view('projects.partials.documents', compact($project));
        } else {
            return '<h2 class="uk-text-align-center uk-heading">Sorry.</h2><p align="center">No documents were found attached to this project.<hr> Approximately '.date('mMi').' documents have been found in docuware<br /> and we are assigning them all to their projects. <br /><br />Thanks for your patience!</p>';
        }
    }

    public function getProjectNotes($project = null)
    {
        return view('projects.partials.notes');
    }

    // public function getProjectComments ( $project = null ) {
    //     return view('projects.partials.comments');
    // }

    // public function getProjectPhotos ( $project = null ) {
    //     return view('projects.partials.photos');
    // }

    // public function getProjectFindings ( $project = null ) {
    //     return view('projects.partials.findings');
    // }

    // public function getProjectFollowups ( $project = null ) {
    //     return view('projects.partials.followups');
    // }

    public function getProjectStream($project = null)
    {
        return view('projects.partials.stream');
    }

    public function getProjectReports($project = null)
    {
        return view('projects.partials.reports');
    }

    public function modalProjectProgramSummaryFilterProgram($project_id, $program_id, Request $request)
    {
        $programs = $request->get('programs');

        if (is_array($programs) && count($programs)>0) {
            $filters = collect([
                'programs' => $programs
            ]);
        } else {
            $filters = null;
        }

        // query here

        $data = collect([
                'project' => [
                    "id" => 1,
                    "name" => "Project Name",
                    'selected_program' => $program_id
                ],
                'programs' => [
                    ["id" => 1, "name" => "Program Name 1"],
                    ["id" => 2, "name" => "Program Name 2"],
                    ["id" => 3, "name" => "Program Name 3"],
                    ["id" => 4, "name" => "Program Name 4"]
                ],
                'units' => [
                    [
                        "id" => 1,
                        "status" => "not-inspectable",
                        "address" => "123457 Silvegwood Street",
                        "address2" => "#102",
                        "move_in_date" => "1/29/2018",
                        "programs" => [
                            ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "inspectable" ],
                            ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "not-inspectable" ]
                        ]
                    ],
                    [
                        "id" => 2,
                        "status" => "inspectable",
                        "address" => "123457 Silvegwood Street",
                        "address2" => "#102",
                        "move_in_date" => "1/29/2018",
                        "programs" => [
                            ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "not-inspectable" ],
                            ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "inspectable" ]
                        ]
                    ]
                ]
            ]);

        return view('dashboard.partials.project-summary-unit', compact('data'));
    }
    public function modalProjectProgramSummary($project_id, $program_id)
    {
        // units are automatically selected using the selection process
        // then randomize all units before displaying them on the modal
        // then user can adjust selection for that program

        $data = collect([
            'project' => [
                "id" => 1,
                "name" => "Project Name",
                'selected_program' => $program_id
            ],
            'programs' => [
                ["id" => 1, "name" => "Program Name 1"],
                ["id" => 2, "name" => "Program Name 2"],
                ["id" => 3, "name" => "Program Name 3"],
                ["id" => 4, "name" => "Program Name 4"]
            ],
            'units' => [
                [
                    "id" => 1,
                    "status" => "not-inspectable",
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "not-inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "not-inspectable" ]
                    ]
                ],
                [
                    "id" => 2,
                    "status" => "inspectable",
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "", "file_audit_checked" => "", "selected" => "", "status" => "inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "", "file_audit_checked" => "", "selected" => "", "status" => "not-inspectable" ]
                    ]
                ],
                [
                    "id" => 2,
                    "status" => "inspectable",
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "", "file_audit_checked" => "", "selected" => "", "status" => "not-inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "", "file_audit_checked" => "", "selected" => "", "status" => "inspectable" ]
                    ]
                ],
                [
                    "id" => 2,
                    "status" => "inspectable",
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "inspectable" ]
                    ]
                ],
                [
                    "id" => 2,
                    "status" => "inspectable",
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "inspectable" ]
                    ]
                ],
                [
                    "id" => 2,
                    "status" => "inspectable",
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "not-inspectable" ]
                    ]
                ]
            ]
        ]);
        
        return view('modals.project-summary', compact('data'));
    }

    public function addAssignmentAuditor($audit_id, $day_id, $auditorid=null)
    {
        $audit = CachedAudit::where('audit_id','=',$audit_id)->first();

        // make sure the logged in user is a manager or the lead on the audit TBD
        $current_user = Auth::user();
        // is user manager? TBD
        // if($audit->lead != $current_user->id){
        //     dd("You must be the lead.");
        // }

        $day = ScheduleDay::where('id','=',$day_id)->where('audit_id','=',$audit_id)->first();

        $auditor = User::where('id','=',$auditorid)->first();
        // dd($audit_id, $audit, $day, $auditorid, $auditor);
        
        // get auditors from user roles
        $auditors = User::whereHas('roles', function($query){
            $query->where('role_id', '=', 2);
        })->get();

        return view('modals.project-assignment-add-auditor', compact('day', 'auditor', 'audit', 'auditors'));
    }

    public function addAuditorToAudit(Request $request, $userid, $auditid)
    {
        // TBD user should be a manager or a lead or an auditor?

        // dd($userid, $auditid, $request->get('dayid'));
        // 6301 6410 4
        $day = ScheduleDay::where('audit_id','=',$auditid)->where('id','=',$request->get('dayid'))->first();
        
        $audit = Audit::where('id','=',$auditid)->first();

        $user = User::where('id','=',$userid)->first();

        if($day && $audit && $user && count(AuditAuditor::where('audit_id','=',$auditid)->where('user_id','=',$userid)->get()) == 0){
            $new_auditor = new AuditAuditor([
                'audit_id' => $auditid,
                'monitoring_key' => $audit->monitoring_key,
                'user_id' => $userid,
                'user_key' => $user->devco_key
            ]);
            $new_auditor->save();
            return 1;
        }

        return 0;
    }

    public function removeAuditorFromAudit(Request $request, $userid, $auditid)
    {

        $audit = Audit::where('id','=',$auditid)->first();

        $user = User::where('id','=',$userid)->first();

        if( $audit && $user){
            AuditAuditor::where('user_id','=',$user->id)->where('audit_id','=',$auditid)->first()->delete();
            return 1;
        }

        return 0;
    }

    public function addAssignmentAuditorStats($id, $auditorid)
    {
        // id is project id
        
        $data = collect([
            "project" => [
                "id" => $id,
                "name" => "Project Name"
            ],
            "summary" => [
                "id" => $auditorid,
                "name" => "Jane Doe",
                'initials' => 'JD',
                'color' => 'blue',
                'date' => 'DECEMBER 22, 2018',
                'ref' => '20181222',
                'date-previous' => 'DECEMBER 13, 2018',
                'ref-previous' => '20181213',
                'date-next' => 'DECEMBER 31, 2018',
                'ref-next' => '20181231',
                'preferred_longest_drive' => '02:30',
                'preferred_lunch' => '00:30',
                'total_estimated_commitment' => "07:40"
            ],
            "itinerary-start" => [
                "id" => 1,
                "icon" => "a-home-marker",
                "type" => "start",
                "status" => "",
                "name" => "Default address",
                "address" => "address here",
                "unit" => "unit 3",
                "city" => "city",
                "state" => "OH",
                "zip" => "12345",
                "average" => "00:00",
                "end" => "08:30 AM",
                "lead" => 1, // user id
                "order" => 1,
                "itinerary" => []
            ],
            "itinerary-end" => [
                "id" => 9,
                "icon" => "a-home-marker",
                "type" => "end",
                "status" => "",
                "name" => "The Ending Address",
                "address" => "address here",
                "unit" => "unit 3",
                "city" => "city",
                "state" => "OH",
                "zip" => "12345",
                "average" => "01:00",
                "end" => "4:10 PM",
                "lead" => 1,
                "order" => 5,
                "itinerary" => []
            ],
            "itinerary" => [
                [
                    "id" => 2,
                    "icon" => "a-marker-basic",
                    "type" => "site",
                    "status" => "in-progress",
                    "name" => "The Garden Oaks",
                    "average" => "00:00",
                    "end" => "08:30 AM",
                    "lead" => 1,
                    "order" => 2,
                    "itinerary" => [
                        [
                            "id" => 3,
                            "icon" => "a-mobile-home",
                            "type" => "site",
                            "status" => "in-progress",
                            "name" => "The Garden Oaks",
                            "average" => "02:00",
                            "end" => "11:30 AM",
                            "lead" => 1,
                            "order" => 1,
                        ],
                        [
                            "id" => 4,
                            "icon" => "a-suitcase-2",
                            "type" => "break",
                            "status" => "",
                            "name" => "LUNCH",
                            "average" => "00:30",
                            "end" => "12:00 AM",
                            "lead" => 1,
                            "order" => 2,
                        ]
                    ]
                ],
                [
                    "id" => 5,
                    "icon" => "a-marker-basic",
                    "type" => "site",
                    "status" => "",
                    "name" => "The Other Place",
                    "average" => "00:15",
                    "end" => "12:15 PM",
                    "lead" => 1,
                    "order" => 3,
                    "itinerary" => [
                        [
                            "id" => 6,
                            "icon" => "a-folder",
                            "type" => "file",
                            "status" => "in-progress",
                            "name" => "The Other Place",
                            "average" => "01:40",
                            "end" => "1:55 PM",
                            "lead" => 1,
                            "order" => 1,
                        ]
                    ]
                ],
                [
                    "id" => 7,
                    "icon" => "a-marker-basic",
                    "type" => "site",
                    "status" => "",
                    "name" => "The Womping Willow",
                    "average" => "00:15",
                    "end" => "2:10 PM",
                    "lead" => 2,
                    "order" => 4,
                    "itinerary" => [
                        [
                            "id" => 8,
                            "icon" => "a-folder",
                            "type" => "file",
                            "status" => "in-progress",
                            "name" => "The Womping Willow",
                            "average" => "01:00",
                            "end" => "3:10 PM",
                            "lead" => 2,
                            "order" => 1,
                        ]
                    ]
                ]
            ],
            "calendar" => [
                "header" => ["12/18", "12/19", "12/20", "12/21", "12/22", "12/23", "12/24", "12/25", "12/26"],
                "content" => [
                    [
                        "id" => 111,
                        "date" => "12/18",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "24",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "33",
                                "span" =>  "2",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "35",
                                "span" =>  "11",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 112,
                        "date" => "12/19",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "22",
                                "span" =>  "24",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 113,
                        "date" => "12/20",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/21",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/22",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 114,
                        "date" => "12/23",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/24",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/25",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/26",
                        "no_availability" => 1
                    ]
                ],
                "footer" => [
                    "previous" => "DECEMBER 13, 2018",
                    'ref-previous' => '20181213',
                    "today" => "DECEMBER 22, 2018",
                    "next" => "DECEMBER 31, 2018",
                    'ref-next' => '20181231'
                ]
            ],
            "calendar-previous" => [
                "header" => ["12/09", "12/10", "12/11", "12/12", "12/13", "12/14", "12/15", "12/16", "12/17"],
                "content" => [
                    [
                        "id" => 111,
                        "date" => "12/09",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "24",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "33",
                                "span" =>  "2",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "35",
                                "span" =>  "11",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 112,
                        "date" => "12/10",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "22",
                                "span" =>  "24",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 113,
                        "date" => "12/11",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/12",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/13",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/14",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/15",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/16",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/17",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ]
                ],
                "footer" => [
                    "previous" => "DECEMBER 04, 2018",
                    'ref-previous' => '20181204',
                    "today" => "DECEMBER 13, 2018",
                    "next" => "DECEMBER 22, 2018",
                    'ref-next' => '20181222'
                ]
            ],
            "calendar-next" => [
                "header" => ["12/27", "12/28", "12/29", "12/30", "12/31", "01/01", "01/02", "01/03", "01/04"],
                "content" => [
                    [
                        "id" => 111,
                        "date" => "12/09",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "24",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "33",
                                "span" =>  "2",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "35",
                                "span" =>  "11",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 112,
                        "date" => "12/10",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "22",
                                "span" =>  "24",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 113,
                        "date" => "12/11",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/12",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/13",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/14",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/15/18",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/16",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/17",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ]
                ],
                "footer" => [
                    "previous" => "DECEMBER 22, 2018",
                    'ref-previous' => '20181222',
                    "today" => "DECEMBER 31, 2018",
                    "next" => "JANUARY 09, 2019",
                    'ref-next' => '20190109',
                ]
            ]
        ]);

        return view('projects.partials.details-assignment-auditor-stat', compact('data'));
    }

    public function getAssignmentAuditorCalendar($id, $auditorid, $currentdate, $beforeafter)
    {
        // from the current date and beforeafter, calculate new target date
        $created = Carbon\Carbon::createFromFormat('Ymd', $currentdate);
        if ($beforeafter == "before") {
            $newdate = $created->subDays(9);

            $newdate_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->subDays(18)->format('F d, Y');
            $newdate_ref_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->subDays(18)->format('Ymd');
            $newdate_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('F d, Y');
            $newdate_ref_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('Ymd');

            $newdateref = $newdate->format('Ymd');
            $newdateformatted = $newdate->format('F d, Y');

            $header_dates = [];
            $header_dates[] = $newdate->subDays(4)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
        } else {
            $newdate = $created->addDays(9);

            $newdate_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('F d, Y');
            $newdate_ref_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('Ymd');
            $newdate_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->addDays(18)->format('F d, Y');
            $newdate_ref_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->addDays(18)->format('Ymd');

            $newdateref = $newdate->format('Ymd');
            $newdateformatted = $newdate->format('F d, Y');

            $header_dates = [];
            $header_dates[] = $newdate->subDays(4)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
        }
        // dd($header_dates);
        // dd($currentdate." - ".$created." - ".$newdate." - ".$newdateformatted." - ".$newdateref);
        $data = collect([
            "project" => [
                "id" => $id,
                "name" => "Project Name"
            ],
            "summary" => [
                "id" => $auditorid,
                "name" => "Jane Doe",
                'initials' => 'JD',
                'color' => 'blue',
                'date' => $newdateformatted,
                'ref' => $newdateref
            ],
            "calendar" => [
                "header" => $header_dates,
                "content" => [
                    [
                        "id" => 111,
                        "date" => "12/18",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "24",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "33",
                                "span" =>  "2",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "35",
                                "span" =>  "11",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 112,
                        "date" => "12/19",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "22",
                                "span" =>  "24",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 113,
                        "date" => "12/20",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/21",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/22",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 114,
                        "date" => "12/23",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/24",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/25",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/26",
                        "no_availability" => 1
                    ]
                ],
                "footer" => [
                    "previous" => $newdate_previous,
                    'ref-previous' => $newdate_ref_previous,
                    "today" => $newdateformatted,
                    "next" => $newdate_next,
                    'ref-next' => $newdate_ref_next
                ]
            ]
        ]);

        return view('projects.partials.details-assignment-auditor-calendar', compact('data'));
    }

    public function addAmenity($type, $id)
    {

        switch ($type) {
            case 'project':
                $project_id = $id;
                $building_id = null;
                $unit_id = null;

                break;
            case 'building':
                $building_id = $id;
                $unit_id = null;

                // get project_id from db
                $building = CachedBuilding::where('id', '=', $building_id)->first();
                if ($building) {
                    $project_id = $building->project_id;
                } else {
                    $project_id = null;
                }

                break;
            case 'unit':
                $unit_id = $id;

                // get building_id and project_id from db
                $unit = CachedUnit::where('id', '=', $unit_id)->first();
                if ($unit) {
                    $project_id = $unit->project_id;
                    $building_id = $unit->building_id;
                } else {
                    $project_id = null;
                    $building_id = null;
                }
                
                break;
            default:
               // something is wrong, there should be at least either a unit_id or a building_id or a project_id
                dd("Error 2464 - cannot add amenity");
        }

        $data = collect([
            "project_id" => $project_id,
            "building_id" => $building_id,
            "unit_id" => $unit_id
        ]);

        $auditors = collect([
            ['id' => 1, 'name' => "auditor name 1"],
            ['id' => 2, 'name' => "auditor name 2"],
            ['id' => 3, 'name' => "auditor name 3"],
            ['id' => 4, 'name' => "auditor name 4"]
        ]);

        return view('modals.amenity-add', compact('data', 'auditors'));
    }

    public function saveAmenity(Request $request)
    {
        // TBD
        //
        //
        //
        $project_id = $request->get('project_id');
        $building_id =  $request->get('building_id');
        $unit_id =  $request->get('unit_id');

        $new_amenities = $request->get('new_amenities');

        // get current audit id using project_id
        // only one audit can be active at one time
        $audit = CachedAudit::where("project_id", "=", $project_id)->orderBy('id', 'desc')->first();

        if (!$audit) {
            dd("There is an error - cannot find that audit - 2541");
        }

        $user = Auth::user();

        if (count($new_amenities)) {
            foreach ($new_amenities as $new_amenity) {
                // TBD
                // Get auditor's name, color and initials
                // 1) check if auditor_id is a valid auditor on that audit
                // 2) get the information
                
                $auditor = Auditor::where("id", "=", $auditor_id)->where("audit_id", "=", $audit->id)->with('user')->first();
                if (!$auditor) {
                    dd("There is an error - this auditor doesn't seem to be assigned to this audit.");
                }

                //tmp
                $auditor_color = 'green';
                $auditor_initials = "BG";
                $auditor_name = "Brian Greenwood";
                
                // get amenity type
                $amenity_type = AmenityType::where("id", "=", $new_amenity['amenity_id'])->first();

                // check name and add numeric counter at the end if duplicate
                $existing_amenities = CachedAmenity::where('project_id', '=', $project_id);
                if ($building_id) {
                    $existing_amenities = $existing_amenities->where('building_id', '=', $building_id);
                }
                if ($unit_id) {
                    $existing_amenities = $existing_amenities->where('unit_id', '=', $unit_id);
                }
                    //$existing_amenities = $existing_amenities->whereRaw('LOWER(name) like ?', [strtolower($amenity->name).'%']);
                    $existing_amenities = $existing_amenities->where('amenity_type_id', '=', $amenity_type->id);
                    $existing_amenities = $existing_amenities->get();

                if ($existing_amenities) {
                    if (count($existing_amenities) == 1) {
                        // only one record that could be the same
                        if (strlen(rtrim($existing_amenities[0]->name)) == strlen(rtrim($name))) {
                            // definitely replace the name
                            $name = $name." #2";
                        }
                    } else {
                        // we have more than one, but we need to make sure they are actually duplicates
                        $found_one = 0;
                        $new_index = 0;
                        $name = rtrim($name);
                        foreach ($existing_amenities as $existing_amenity) {
                            if (strlen(rtrim($existing_amenities[0]->name)) == strlen($name)) {
                                $new_index = 2;
                            } else {
                                // there is a second part to the string ( #000), make sure it has the right format and get the highest digit
                                $name_end = substr(rtrim($existing_amenity->name), strpos(rtrim($existing_amenity->name), $name." #") + strlen($name." #"));

                                if (substr(rtrim($existing_amenity->name), 0, strlen($name." #")) === $name." #" && ctype_digit($name_end)) {
                                    // the string starts with the exact name and there is a digit after space #
                                    if (int($name_end) > $new_index) {
                                        $new_index = int($name_end);
                                    }
                                }
                            }
                        }

                        if ($new_index > 0) {
                            $name = $name." #".$new_index;
                        }
                    }
                } else {
                    // when no existing amenities, nothing to rename
                }

                // save new amenity
                $amenity = new CachedAmenity([
                            'audit_id' => $audit->id,
                            'project_id' => $project_id,
                            'building_id' => $building_id,
                            'unit_id' => $unit_id,
                            'amenity_type_id' => $amenity_type->id,
                            'name' => $name,
                            'finding_nlt_status' => 'action-needed',
                            'finding_lt_status' => 'action-required',
                            'finding_sd_status' => 'no-action',
                            'finding_copy_status' => 'no-action',
                            'auditor_id' => $auditor_id,
                            'auditor_name' => $auditor_name,
                            'auditor_initials' => $auditor_initials,
                            'auditor_color' => $auditor_color
                        ]);
                $amenity->save();
            }
        }
        
        // reload amenities (!! filter, not all of them, ok for now as we need to test)
        $data = CachedAmenity::where('audit_id', '=', $audit->id)->where('building_id', '=', $building_id);
        if ($unit_id) {
            $data = $data->where('unit_id', '=', $unit_id);
        }
        $data = $data->get();
            
        return $data;
    }

    public function reorderAmenitiesFromAudit($audit, Request $request)
    {

        $building_id = $request->get('building_id');
        $unit_id = $request->get('unit_id');
        $amenity_id = $request->get('amenity_id');
        $index = $request->get('index');

        //dd($building_id." ".$unit_id." ".$amenity_id." ".$index);

        // select all amenity orders except for the one we want to reorder
        $current_ordering = OrderingAmenity::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id);
            $current_ordering = $current_ordering->where('amenity_id', '!=', $amenity_id);
        if ($unit_id) {
            $current_ordering = $current_ordering->where('unit_id', '=', $unit_id);
        }
        if ($building_id) {
            $current_ordering = $current_ordering->where('building_id', '=', $building_id);
        }
            $current_ordering = $current_ordering->orderBy('order', 'asc')->get()->toArray();

        $inserted = [ [
                    'user_id' => Auth::user()->id,
                    'audit_id' => $audit,
                    'building_id' => $building_id,
                    'unit_id' => $unit_id,
                    'amenity_id' => $amenity_id,
                    'order' => $index
               ]];

        // insert the building ordering in the array
        $reordered_array = $current_ordering;
        array_splice($reordered_array, $index, 0, $inserted);

        // delete previous ordering
        $previous_ordering = OrderingAmenity::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id);
        if ($unit_id) {
            $previous_ordering = $previous_ordering->where('unit_id', '=', $unit_id);
        }
        if ($building_id) {
            $previous_ordering = $previous_ordering->where('building_id', '=', $building_id);
        }
            $previous_ordering->delete();

        // clean-up the ordering and store
        foreach ($reordered_array as $key => $ordering) {
            $new_ordering = new OrderingAmenity([
                'user_id' => $ordering['user_id'],
                'audit_id' => $ordering['audit_id'],
                'building_id' => $ordering['building_id'],
                'unit_id' => $ordering['unit_id'],
                'amenity_id' => $ordering['amenity_id'],
                'order' => $key+1
            ]);
            $new_ordering->save();
        }
    }

    public function updateStep($id){
        // can this user have the right to change step?? TBD
        // 
        $audit = CachedAudit::where('audit_id','=',$id)->first();
        $steps = GuideStep::where('guide_step_type_id','=',1)->orderBy('order','asc')->get();

        return view('modals.audit-update-step', compact('steps', 'audit'));
    }

    public function saveStep(Request $request, $id){
        $step_id = $request->get('step');
        $step = GuideStep::where('id','=',$step_id)->first();
        $audit = CachedAudit::where('id','=',$id)->first();

        // check if user has the right to save step using roles TBD
        
        // add new guide_progress entry
        $progress = new GuideProgress([
            'user_id' => Auth::user()->id,
            'audit_id' => $audit->id,
            'project_id' => $audit->project_id,
            'guide_step_id' => $step_id,
            'type_id' => 1
        ]);
        $progress->save();

        // update CachedAudit table with new step info
        $audit->update([
            'step_id' => $step->id,
            'step_status_icon' => $step->icon,
            'step_status_text' => $step->step_help,
        ]);

        return 1;
    }

}
