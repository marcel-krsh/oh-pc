<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Organization;
use App\Models\ProjectContactRole;
use App\Models\CachedAudit;
use Illuminate\Support\Facades\Redis;
use Auth;

class AuditsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(env('APP_DEBUG_NO_DEVCO') == 'true'){
           // Auth::onceUsingId(1); // TEST BRIAN
           Auth::onceUsingId(286); // TEST 
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public function auditCreated(Audit $audit)
    {

        // check the monitoring_status_type_key for 4,5 or 6
        // that means we have to create CachedAudit row if doesn't exist (shouldn't, since it is a creation)
        if($audit){
            if(in_array($audit->monitoring_status_type_key, [4,5,6])){
                if(!CachedAudit::where('audit_id', '=', $audit->id)->count()){
                    $this->createNewCachedAudit($audit);
                }
            }
        }
        

    }

    public function auditUpdated(Audit $audit)
    {
        // check the monitoring_status_type_key for 4,5 or 6
        // check if audit already exists in cachedaudits if not create it
        // createNewCachedAudit($audit);

    }

    public function adjustedLimit($n)
    {
        // based on $n units, return the corresponding adjusted sample size
        switch(true){
            case ($n >= 1 && $n <=4): return $n; break;
            case ($n == 5 || $n == 6): return 5; break;
            case ($n == 7): return 6; break;
            case ($n == 8 || $n == 9): return 7; break;
            case ($n == 10 || $n == 11): return 8; break;
            case ($n == 12 || $n == 13): return 9; break;
            case ($n >= 14 && $n <= 16): return 10; break;
            case ($n >= 17 && $n <= 18): return 11; break;
            case ($n >= 19 && $n <= 21): return 12; break;
            case ($n >= 22 && $n <= 25): return 13; break;
            case ($n >= 26 && $n <= 29): return 14; break;
            case ($n >= 30 && $n <= 34): return 15; break;
            case ($n >= 35 && $n <= 40): return 16; break;
            case ($n >= 41 && $n <= 47): return 17; break;
            case ($n >= 48 && $n <= 56): return 18; break;
            case ($n >= 57 && $n <= 67): return 19; break;
            case ($n >= 68 && $n <= 81): return 20; break;
            case ($n >= 82 && $n <= 101): return 21; break;
            case ($n >= 102 && $n <= 130): return 22; break;
            case ($n >= 131 && $n <= 175): return 23; break;
            case ($n >= 176 && $n <= 257): return 24; break;
            case ($n >= 258 && $n <= 449): return 25; break;
            case ($n >= 450 && $n <= 1461): return 26; break;
            case ($n >= 1462): return 27; break;
            default:
                return 0;
        }
    }

    public function selectionProcess(Audit $audit)
    {
        // is the project processing all the buildings together? or do we have a combination of grouped buildings and single buildings?
        if($audit->development_key){
            $project = Project::where('project_key', '=', $audit->development_key)->with('programs')->first();
        }else{
            return "Error, this audit isn't associated with a project somehow...";
        }

        if(!$project->programs){
            return "Error, this project doesn't have a program.";
        }

        $total_buildings = $project->total_building_count;
        $total_units = $project->total_unit_count;

        $pm_contact = ProjectContactRole::where('project_key', '=', $audit->development_key)
                                ->where('project_role_key', '=', 21)
                                ->with('organization')
                                ->first();

        $organization_id = null;                        
        if($pm_contact){
            if($pm_contact->organization){
                $organization_id = $pm_contact->organization->id;
            }
        }

        // for each program (funding), select the corresponding units
        // if($project->programs){
        //     foreach($project->programs as $program_pivot){

        //     }
        // }
        
        // save all buildings in building_inspection table
        $buildings = $project->buildings;

        if($buildings){
            foreach($buildings as $building){
                $b = new BuildingInspection([
                    'building_id' => $building->id,
                    'building_key' => $building->building_key,
                    'audit_id' => $audit->id,
                    'audit_key' => $audit->monitoring_key,
                    'project_id' => $project->id,
                    'project_key' => $project->project_key,
                    'pm_organization_id' => $organization_id,
                    'auditors' => null,
                    'nlt_count' => 0,
                    'lt_count' => 0,
                    'followup_count' => 0,
                    'complete' => 0,
                    'submitted_date_time' => null
                ]);
                $b->save();
            }
        }
        

        $programs = ['FAF', 'NSP', 'TCE' ,'RTCAP', '811', 'Medicaid', 'HOME', 'OHTF', 'NHTF', 'HTC'];
        $selection = [];
        $summary = [];

        // vacant units that are rent ready: 100% selection
        $units = []; // tbd
        $selection[] = [
            "program_name" => "Vacant rent ready",
            "units" => $units,
            "totals" => count($units)
        ];

        // 1 - FAF || NSP || TCE || RTCAP || 811 units
        // [30001, 80015, 80016, 45, 30024, 30025, 30023, 49, 222, 30032, 30033, 30034, 30043, 30058]
        // total for all those programs combined
        $current_program_ids = [30001, 80015, 80016, 45, 30024, 30025, 30023, 49, 222, 30032, 30033, 30034, 30043, 30058];
        $units = []; // tbd select units for the programs above

        $total = count($units);
        // HTC programs: [30001, 30005, 30004, 30030, 30031, 600009, 600010, 30036, 30049, 30048, 66, 67, 68, 36, 30043, 30059, 30055]
        $htc_program_ids = [30001, 30005, 30004, 30030, 30031, 600009, 600010, 30036, 30049, 30048, 66, 67, 68, 36, 30043, 30059, 30055];
        foreach($units as $unit){
            if(in_array($unit->program->program_key, $htc_program_ids)){
                $units_with_htc[] = $unit;
            }else{
                $units_without_htc[] = $unit;
            }
        }

        $units[] = $this->randomSelection($units_without_htc, 20);

        // check in project_program->first_year_award_claimed date for the 15 year test
        
        $first_year = null;
        foreach($project->programs as $program){
            if(in_array($program->program_key, $current_program_ids)){
                if($first_year == null || $first_year < $program->first_year_award_claimed){
                    $first_year = $program->first_year_award_claimed;
                }
            }
        }
        if(idate("Y")-15 > $first_year && $first_year != null){
            $first_fifteen_years = 0;
        }else{
            $first_fifteen_years = 1;
        }
        
        if($first_fifteen_years){
            // check project type
            if($project->isLeasePurchase()){
                $units[] = $this->randomSelection($units_with_htc, 20);
            }else{
                $is_multi_building_project = 0;
                // for each of the current programs+project, check if multiple_building_election_key is 2 for multi building project 
                foreach($project->programs as $program){
                    if(in_array($program->program_key, $current_program_ids){
                        if($program->multiple_building_election_key == 2){
                            $is_multi_building_project = 1;
                        }
                    }
                }

                if($is_multi_building_project){
                    $units[] = $this->randomSelection($units_with_htc, 20);
                }else{
                    // group units by building, then proceed with the random selection
                    
                }
            }
        }else{
            $units[] = $this->randomSelection($units_with_htc, 20);
        }

        $selection[] = [
            "program_name" => "FAF NSP TCE RTCAP 811",
            "units" => $units,
            "adjusted_units" => $this->adjustedLimit(count($units)),
            "totals" => count($units),
            "adjusted_totals" => $this->adjustedLimit(count($units))
        ];

        // 2 - 811 units
        // 100% selection
        // for units with 811 funding
        $units = []; // tbd
        $selection[] = [
            "program_name" => "811",
            "units" => $units,
            "totals" => count($units)
        ];

        // 3 - Medicaid units
        // 100% selection
        $units = []; // tbd
        $selection[] = [
            "program_name" => "Medicaid",
            "units" => $units,
            "totals" => count($units)
        ];

        // 4 - HOME
        $units = []; // tbd
        $selection[] = [
            "program_name" => "HOME",
            "units" => $units,
            "totals" => count($units)
        ];
        
        // 5 - OHTF
        $units = []; // tbd
        $selection[] = [
            "program_name" => "OHTF",
            "units" => $units,
            "totals" => count($units)
        ];

        // 6 - NHTF
        $units = []; // tbd
        $selection[] = [
            "program_name" => "NHTF",
            "units" => $units,
            "totals" => count($units)
        ];

        // 7 - HTC
        // get totals of all units HTC and select all units without NHTF. OHTF and HOME
        // check in project_program->first_year_award_claimed date for the 15 year test
        // after 15 years: 20% of total
        $units = []; // tbd
        $selection[] = [
            "program_name" => "HTC",
            "units" => $units,
            "totals" => count($units)
        ];

        // save all units selected in selection table
        if($units_selected){
            foreach($units_selected as $unit_selected){
                $u = new UnitInspection([
                    'unit_id' => $unit_selected->id,
                    'unit_key' => $unit_selected->building_key,
                    'audit_id' => $audit->id,
                    'audit_key' => $audit->monitoring_key,
                    'project_id' => $project->id,
                    'project_key' => $project->project_key,
                    'program_id' => $unit_selected->program_id,
                    'program_key' => $unit_selected->program_key,
                    'pm_organization_id' => $organization_id,
                    'auditors' => null,
                    'nlt_count' => 0,
                    'lt_count' => 0,
                    'followup_count' => 0,
                    'swap_reason' => null,
                    'complete' => 0,
                    'submitted_date_time' => null
                ]);
                $u->save();
            }
        }


        $data = [
            'units' => [],
            'programs' => [
                [
                    'id' => '',
                    'program_name' => '',
                    
                ]
            ]
        ];
        return $data;
    }

    public function createNewCachedAudit(Audit $audit)
    {
        // collect programs
        // 
        

        // for each program, proceed with unit selection
        // 
        

        // create cached audit
        // 
        
        $project_id = null;
        $project_ref = '';
        $project_name = null;
        $total_buildings = 0;
        $lead = null;
        $lead_json = '{ "id": null, "name": "", "initials": "", "color": "", "status": "" }';

        // project address
        $address = ''; 
        $city = ''; 
        $state = ''; 
        $zip = ''; 


        if($audit->user_key){
            $lead_user = User::where('devco_key', '=', $audit->user_key)->first();
            if($lead_user){
                $lead = $lead_user->id;
                $words = explode(" ", $lead_user->name);
                $initials = "";
                foreach ($words as $w) {
                   $initials .= $w[0];
                }
                $initials = substr($initials, 0, 2); // keep the first two letters only

                $data = [
                    "id" => $lead_user->id, 
                    "name" => $lead_user->name, 
                    "initials" => $initials, 
                    "color" => $lead_user->badge_color, 
                    "status" => ""
                ];
                $lead_json = json_encode($data);
            }
        }

        if($audit->development_key){
            $project = Project::where('project_key', '=', $audit->development_key)->with('address')->first();
            if($project){
                $project_id = $project->id;
                $project_ref = $project->project_number;
                $project_name = $project->project_name;
                $total_buildings = $project->total_building_count;

                if($project->address){
                    $address = $project->address->line1; 
                    $city = $project->address->city; 
                    $state = $project->address->state; 
                    $zip = $project->address->zip;
                }
            }
        }

        // in project_roles
        // primary owner: project_role_key = 20, id = 98
        // primary manager: project_role_key = 21, id = 161
        $pm_name = '';
        $pm_contact = ProjectContactRole::where('project_key', '=', $audit->development_key)
                                ->where('project_role_key', '=', 21)
                                ->with('organization.address')
                                ->first();

        if($pm_contact){
            if($pm_contact->organization){
                $pm_name = $pm_contact->organization->organization_name;
            }
        }

        // total items? from amenity inspection table
        $total_items = 0; // TBD


       

        

        $cached_audit = new CachedAudit([
                'audit_id' => $audit->id,
                'project_id' => $project_id,
                'project_ref' => $project_ref, 
                'status' => '',
                'lead' => $lead, 
                'lead_json' => $lead_json,
                'title' => $project_name,
                'pm' => $pm_name,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'total_buildings' => $total_buildings,
                'inspection_icon' => 'a-mobile-repeat',
                'inspection_status' => 'action-needed', // no scheduled date in array yet, that's why
                'inspection_status_text' => 'Audit needs scheduling',
                'inspection_schedule_date' => '', // combine and use one date for both fields
                'inspection_schedule_text' => 'Click to schedule audit',
                'inspectable_items' => 0,
                'total_items' => $total_items,
                'audit_compliance_icon' => 'a-circle-checked',
                'audit_compliance_status' => 'ok-actionable', 
                'audit_compliance_status_text' => 'Audit Compliant',
                'followup_status' => 'ok-actionable',
                'followup_status_text' => 'No followups',
                'followup_date' => '2018-12-10', // combine and use one date for both fields
                'file_audit_icon' => 'a-folder',
                'file_audit_status' => 'ok-actionable',
                'file_audit_status_text' => '',
                'nlt_audit_icon' => 'a-booboo',
                'nlt_audit_status' => 'action-required',
                'nlt_audit_status_text' => '',
                'lt_audit_icon' => 'a-skull',
                'lt_audit_status' => 'in-progress',
                'lt_audit_status_text' => '',
                'smoke_audit_icon' => 'a-flames',
                'smoke_audit_status' => 'action-needed',
                'smoke_audit_status_text' => '',
                'auditor_status_icon' => 'a-avatar',
                'auditor_status' => 'action-required',
                'auditor_status_text' => 'Auditors / schedule conflicts / unasigned items',
                'message_status_icon' => 'a-envelope-4',
                'message_status' => '',
                'message_status_text' => '',
                'document_status_icon' => 'a-files',
                'document_status' => '',
                'document_status_text' => 'Document status',
                'history_status_icon' => 'a-person-clock',
                'history_status' => '',
                'history_status_text' => 'NO/VIEW HISTORY',
                'step_status_icon' => 'a-calendar-7',
                'step_status' => 'no-action',
                'step_status_text' => ''
            ]);
            $cached_audit->save();

        dd($audit);

        // $data = [
        //     'event' => 'NewMessage',
        //     'data' => [
        //         'stats_communication_total' => $stats_communication_total
        //     ]
        // ];

        // Redis::publish('communications', json_encode($data)); 
    }

}
