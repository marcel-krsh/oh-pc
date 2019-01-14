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
use Auth;
use Session;
use App\LogConverter;
use Carbon;

class AuditController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
            Auth::onceUsingId(286); // TEST BRIAN
        }
    }

    public function buildingsFromAudit($audit, Request $request)
    {
        $target = $request->get('target');
        $context = $request->get('context');

        // check if user can see that audit
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


        return view('dashboard.partials.audit_building_details', compact('audit', 'target', 'building', 'details', 'targetaudit', 'context'));
    }

    public function inspectionFromBuilding($audit_id, $building_id, Request $request)
    {
        $target = $request->get('target');
        $rowid = $request->get('rowid');
        $context = $request->get('context');
        $inspection = "test";
        
        $data['detail'] = CachedInspection::first();

        $data['menu'] = $data['detail']->menu_json;

        // $data['amenities'] = CachedAmenity::where('audit_id', '=', $audit_id)->where('building_id', '=', $building_id)->get();
        // count amenities & count ordering_amenities
        $ordered_amenities_count = OrderingAmenity::where('audit_id', '=', $audit_id)->where('user_id', '=', Auth::user()->id);
        if ($building_id) {
            $ordered_amenities_count = $ordered_amenities_count->where('building_id', '=', $building_id);
        }
            $ordered_amenities_count = $ordered_amenities_count->count();

        $amenities_count = CachedAmenity::where('audit_id', '=', $audit_id);
        if ($building_id) {
            $amenities_count = $amenities_count->where('building_id', '=', $building_id);
        }
            $amenities_count = $amenities_count->count();

        if ($amenities_count != $ordered_amenities_count && $amenities_count != 0) {
            // this shouldn't happen
            // reset ordering
            $amenities_to_reset = CachedAmenity::where('audit_id', '=', $audit_id);
            if ($building_id) {
                $amenities_to_reset = $amenities_to_reset->where('building_id', '=', $building_id);
            }
                $amenities_to_reset = $amenities_to_reset->delete();
        }

        if ($ordered_amenities_count == 0 && $amenities_count != 0) {
            // if ordering_amenities is empty, create a default entry for the ordering
            $amenities = CachedAmenity::where('audit_id', '=', $audit_id);
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

        switch ($type) {
            case 'compliance':
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
                        'schedule_conflicts' => 16
                    ],
                    "programs" => [
                        ['id' => 1, 'name' => 'Program Name A'],
                        ['id' => 2, 'name' => 'Program Name B'],
                        ['id' => 3, 'name' => 'Program Name C'],
                        ['id' => 4, 'name' => 'Program Name D'],
                        ['id' => 5, 'name' => 'Program Name E'],
                        ['id' => 6, 'name' => 'Program Name F']
                    ]
                ]);
                break;
            case 'assignment':
                $data = collect([
                    "project" => [
                        'id' => 1,
                        'audit_id' => 123
                    ],
                    "summary" => [
                        'required_unit_selected' => 0,
                        'inspectable_areas_assignment_needed' => 12,
                        'required_units_selection' => 13,
                        'file_audits_needed' => 14,
                        'physical_audits_needed' => 15,
                        'schedule_conflicts' => 16,
                        'estimated' => '107:00',
                        'estimated_hours' => '107',
                        'estimated_minutes' => '00',
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

        return view('projects.partials.details-'.$type, compact('data'));
    }

    public function saveEstimatedHours(Request $request, $id){
        // audit id
        $forminputs = $request->get('inputs');
        parse_str($forminputs, $forminputs);

        $hours = (int) $forminputs['estimated_hours'];
        $minutes = (int) $forminputs['estimated_minutes'];

        $audit = Audit::where('id','=',$id)->where('lead_user_id','=',Auth::user()->id)->first();

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
            return ['status'=>0, 'message'=>'Sorry, this audit reference cannot be found.'];
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

    public function addAssignmentAuditor($id, $orderby = null)
    {

        $data = collect([
            "project" => [
                "id" => $id,
                "name" => "Project Name"
            ],
            "summary" => [
                'date' => 'DECEMBER 22, 2018',
                'estimated' => '107:00',
                'needed' => '27:00'
            ],
            "auditors" => [
                [
                    "id" => 1,
                    "name" => "Jane Doe",
                    "status" => "ok-actionable",
                    "icon" => "a-circle-checked",
                    "icon_tooltip" => "CLICK TO REMOVE AUDITOR",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-home-marker",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 2,
                    "name" => "Jane Doe 2",
                    "status" => "",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "CLICK TO ADD AUDITOR",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-home-marker",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 3,
                    "name" => "Jane Doe 3",
                    "status" => "action-required",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "THIS AUDITOR WILL REQUIRE CONFLICT APPROVAL BY LEAD",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-marker-basic",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 4,
                    "name" => "Jane Doe 4",
                    "status" => "action-required",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "THIS AUDITOR WILL REQUIRE CONFLICT APPROVAL BY LEAD",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-marker-basic",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 5,
                    "name" => "Jane Doe 5",
                    "status" => "action-required",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "THIS AUDITOR WILL REQUIRE CONFLICT APPROVAL BY LEAD",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-marker-basic",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 6,
                    "name" => "Jane Doe 6",
                    "status" => "action-required",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "THIS AUDITOR WILL REQUIRE CONFLICT APPROVAL BY LEAD",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-marker-basic",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 7,
                    "name" => "Jane Doe 7",
                    "status" => "action-required",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "THIS AUDITOR WILL REQUIRE CONFLICT APPROVAL BY LEAD",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-marker-basic",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ]
            ]
        ]);
        return view('modals.project-assignment-add-auditor', compact('data'));
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
