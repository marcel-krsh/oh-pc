<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Audit;
use App\Models\Project;
use App\Models\ProjectContactRole;
use App\Models\CachedAudit;
use Event;

class CreateCachedAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:cache {id=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CachedAudit based on audit_id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Working on audit '.$this->argument('id'));

        $audit = Audit::where('id','=',$this->argument('id'))->first();
        if($audit){
            $this->line('Found the audit...');

            Event::listen('audit.cache', $audit);

        }else{
            $this->error('Looks like there is no audit with that id.');
        }
    }

    // public function createCachedAudit(Audit $audit){

    //     // defaults
    //     $project_id = null;
    //     $project_ref = '';
    //     $project_name = null;
    //     $total_buildings = 0;
    //     $lead = null;
    //     $lead_json = '{ "id": null, "name": "", "initials": "", "color": "", "status": "" }';
    //     $address = '';
    //     $city = '';
    //     $state = '';
    //     $zip = '';
    //     $estimated_time = '';
    //     $estimated_time_needed = '';

    //     if ($audit->user_key) {
    //         $lead_user = User::where('devco_key', '=', $audit->user_key)->first();
    //         if ($lead_user) {
    //             $lead = $lead_user->id;
    //             $words = explode(" ", $lead_user->name);
    //             $initials = "";
    //             foreach ($words as $w) {
    //                 $initials .= $w[0];
    //             }
    //             $initials = substr($initials, 0, 2); // keep the first two letters only

    //             $data = [
    //                 "id" => $lead_user->id,
    //                 "name" => $lead_user->name,
    //                 "initials" => $initials,
    //                 "color" => $lead_user->badge_color,
    //                 "status" => ""
    //             ];
    //             $lead_json = json_encode($data);
    //         }
    //     }

    //     if ($audit->development_key) {
    //         $project = Project::where('project_key', '=', $audit->development_key)->with('address')->first();
    //         if ($project) {
    //             $project_id = $project->id;
    //             $project_ref = $project->project_key;
    //             $project_name = $project->project_name;
    //             $total_buildings = $project->total_building_count;

    //             if ($project->address) {
    //                 $address = $project->address->line_1;
    //                 $city = $project->address->city;
    //                 $state = $project->address->state;
    //                 $zip = $project->address->zip;
    //             }
    //         }
    //     }

    //     // in project_roles
    //     // primary owner: project_role_key = 20, id = 98
    //     // primary manager: project_role_key = 21, id = 161
    //     $pm_name = '';
    //     $pm_contact = ProjectContactRole::where('project_key', '=', $audit->development_key)
    //                             ->where('project_role_key', '=', 21)
    //                             ->with('organization.address')
    //                             ->first();

    //     if ($pm_contact) {
    //         if ($pm_contact->organization) {
    //             $pm_name = $pm_contact->organization->organization_name;
    //         }
    //     }
    //     if ($pm_name == '') {
    //         if ($pm_contact->person) {
    //             $pm_name = $pm_contact->person->first_name." ".$pm_contact->person->last_name;
    //         }
    //     }

    //     // if no organization put contact name under the project name

    //     // build amenities array using amenity_inspections table

    //     // set status
    //     $status = 'tbd'; // critical, ok-actionable, etc


    //     $cached_audit = new CachedAudit([
    //             'audit_id' => $audit->id,
    //             'audit_key' => $audit->monitoring_key,
    //             'project_id' => $project->id,
    //             'project_key' => $audit->development_key,
    //             'project_ref' => $project->project_name,
    //             'status' => $status,
    //             'lead' => $lead,
    //             'lead_json' => $lead_json,
    //             'title' => $project_name,
    //             'pm' => $pm_name,
    //             'address' => $address,
    //             'city' => $city,
    //             'state' => $state,
    //             'zip' => $zip,
    //             'total_buildings' => $total_buildings,
    //             'inspection_icon' => 'a-mobile-repeat',
    //             'inspection_status' => 'action-needed', // no scheduled date in array yet, that's why
    //             'inspection_status_text' => 'AUDIT NEEDS SCHEDULING',
    //             'inspection_schedule_text' => 'CLICK TO SCHEDULE AUDIT',
    //             'inspectable_items' => 0,
    //             'total_items' => $total_items,
    //             'audit_compliance_icon' => 'a-circle-checked',
    //             'audit_compliance_status' => 'ok-actionable',
    //             'audit_compliance_status_text' => 'AUDIT COMPLIANT',
    //             'followup_status' => '',
    //             'followup_status_text' => 'NO FOLLOWUPS',
    //             'file_audit_icon' => 'a-folder',
    //             'file_audit_status' => '',
    //             'file_audit_status_text' => 'CLICK TO ADD A FINDING',
    //             'nlt_audit_icon' => 'a-booboo',
    //             'nlt_audit_status' => '',
    //             'nlt_audit_status_text' => 'CLICK TO ADD A FINDING',
    //             'lt_audit_icon' => 'a-skull',
    //             'lt_audit_status' => '',
    //             'lt_audit_status_text' => 'CLICK TO ADD A FINDING',
    //             'smoke_audit_icon' => 'a-flames',
    //             'smoke_audit_status' => '',
    //             'smoke_audit_status_text' => 'CLICK TO ADD A FINDING',
    //             'auditor_status_icon' => 'a-avatar-fail',
    //             'auditor_status' => 'action-required',
    //             'auditor_status_text' => 'ASSIGN AUDITORS',
    //             'message_status_icon' => 'a-envelope-4',
    //             'message_status' => '',
    //             'message_status_text' => '',
    //             'document_status_icon' => 'a-files',
    //             'document_status' => '',
    //             'document_status_text' => 'DOCUMENT STATUS',
    //             'history_status_icon' => 'a-person-clock',
    //             'history_status' => '',
    //             'history_status_text' => 'NO/VIEW HISTORY',
    //             'step_status_icon' => 'a-home-question',
    //             'step_status' => 'no-action',
    //             'step_status_text' => 'REVIEW AND ASSIGN INSPECTABLE AREAS',
    //             'estimated_time' => '',
    //             'estimated_time_needed' => '',
    //             //'amenities_json' => json_encode($amenities)
    //         ]);
    //     $cached_audit->save();

    //     return true;
    // }
}
