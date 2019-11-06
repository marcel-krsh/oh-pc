<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Project;
use App\Models\People;
use App\Models\Address;
use App\Models\CachedAudit;
use App\Models\Organization;
use App\Models\Audit;
use DB;
use Faker\Factory as Faker;
use Event;

/**
 * MakeTestFriendly Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class MakeSuperTestFriendlyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make_super_test_friendly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change all usernames, names, property names to be something we can display in public.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function createNewCachedAudit($audit)
    {
        // create cached audit
        //
        
        $project_id = null;
        $project_ref = '';
        $project_name = null;
        $total_buildings = 0;
        $lead = null;
        $total_items = null;
        $lead_json = '{ "id": null, "name": "", "initials": "", "color": "", "status": "" }';
        //$this->processes++;

        // project address
        $address = '';
        $city = '';
        $state = '';
        $zip = '';

        $estimated_time = null;
        $estimated_time_needed = null;


        if ($audit->user_key) {
            $lead_user = User::where('devco_key', '=', $this->audit->user_key)->first();
            //$this->processes++;
        }else{
            $lead_user = null;
            //$this->processes++;
        }

        if ($lead_user) {
            $lead = $lead_user->id;
            // $words = explode(" ", $lead_user->name);
            // $initials = "";
            // foreach ($words as $w) {
            //     $initials .= $w[0];
            // }
            // $initials = substr($initials, 0, 2); // keep the first two letters only

            $data = [
                "id" => $lead_user->id,
                "name" => $lead_user->full_name(),
                "initials" => $lead_user->initials(),
                "color" => $lead_user->badge_color,
                "status" => ""
            ];
            $lead_json = json_encode($data);
        }

        if ($audit->project_id) {
            $project = $audit->project;
            if ($project) {
                $project_id = $project->id;
                $project_ref = $project->project_number;
                $project_name = $project->project_name;
                $total_buildings = $project->total_building_count;

                if ($project->address) {
                    $address = $project->address->line_1;
                    $city = $project->address->city;
                    $state = $project->address->state;
                    $zip = $project->address->zip;
                }
            }
        }
        //create inspection days if they are set in devco

        if(null !== $audit->start_date){
            $auditInspectionDate = date('Y-m-d H:i:s', strtotime($audit->start_date));
            //insert the date into the schedule
            $scheduleCheck = ScheduleDay::where('date', $auditInspectionDate)->where('audit_id',$audit->id)->count();
            if($scheduleCheck < 1){
                $schedule = new ScheduleDay;
                $schedule->audit_id = $audit->id;
                $schedule->date = $auditInspectionDate;
                $schedule->save();
            }
            $inspection_schedule_text = 'DATE SET FROM ORIGINATOR';
            $inspection_status_text = 'CLICK TO RESCHEDULE';
            $inspection_status = 'ok-actionable'; 
            $inspection_icon = 'a-mobile-clock'; 
        } else {
            $auditInspectionDate = null;
            $inspection_schedule_text = 'SCHEDULED AUDITS/TOTAL AUDITS';
            $inspection_status = 'action-needed'; 
            $inspection_icon = 'a-mobile-clock'; 

        }
        

        // inspection status and schedule date set to default when creating a new audit
         
        $inspection_schedule_date = $auditInspectionDate; // Y-m-d H:i:s
        

        // in project_roles
        // primary owner: project_role_key = 20, id = 98
        // primary manager: project_role_key = 21, id = 161
        $pm_name = '';
        $pm_contact = ProjectContactRole::where('project_key', '=', $audit->development_key)
                                ->where('project_role_key', '=', 21)
                                ->with('organization.address')
                                ->first();

        if ($pm_contact) {
            if ($pm_contact->organization) {
                $pm_name = $pm_contact->organization->organization_name;
            }
            if ($pm_name == '') {
                if ($pm_contact->person) {
                    $pm_name = $pm_contact->person->first_name." ".$pm_contact->person->last_name;
                }
            }
        }
        

        // inspection_schedule_json needs to be populated TBD

        // if no organization put contact name under the project name

        // build amenities array using amenity_inspections table

        // save summary
        $audit->selection_summary = json_encode($summary);
        //$this->audit->save();

        // create or update
        $cached_audit = CachedAudit::where('audit_id','=',$audit->id)->first();

        // total items is the total number of units added during the selection process
        

        if($cached_audit){
            // when updating a cachedaudit, run the status test
            $total_items = $audit->total_items(); 
            // $inspection_schedule_checks = $cached_audit->checkStatus('schedules');
            // $inspection_status_text = $inspection_schedule_checks['inspection_status_text']; 
            // $inspection_schedule_date = $inspection_schedule_checks['inspection_schedule_date'];
            // $inspection_schedule_text = $inspection_schedule_checks['inspection_schedule_text'];
            // $inspection_status = $inspection_schedule_checks['inspection_status']; 
            // $inspection_icon = $inspection_schedule_checks['inspection_icon'];
            
            $inspection_status_text = $cached_audit->inspection_status_text; 
            $inspection_schedule_date = $cached_audit->inspection_schedule_date;
            $inspection_schedule_text = $cached_audit->inspection_schedule_text;
            $inspection_status = $cached_audit->inspection_status; 
            $inspection_icon = $cached_audit->inspection_icon;

            //if($inspection_schedule_checks['status'] == 'critical'){
            //    $status = 'critical'; // TBD critical/other
            //}else{
                $status = ''; // TBD critical/other
            //}
            
            // current step
            $step = $cached_audit->current_step();
            if(!$step){
                $step_id = 1;
                $step_icon = 'a-home-question';
                $step_status_text = 'REVIEW INSPECTABLE AREAS';
            }else{
                $step_id = $step->id;
                $step_icon = $step->icon;
                $step_status_text = $step->step_help;
            }

            $cached_audit->update([
                'audit_id' => $audit->id,
                'audit_key' => $audit->monitoring_key,
                'project_id' => $project->id,
                'project_key' => $audit->development_key,
                'project_ref' => $project_ref,
                'status' => $status,
                'lead' => $lead,
                'lead_json' => $lead_json,
                'title' => $project_name,
                'pm' => $pm_name,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'total_buildings' => $total_buildings,
                'inspection_icon' => $inspection_icon,
                'inspection_status' => $inspection_status, 
                'inspection_status_text' => $inspection_status_text,
                'inspection_schedule_text' => $inspection_schedule_text,
                'inspection_schedule_date' => $inspection_schedule_date,
                'inspection_schedule_json' => null, // TBD
                'inspectable_items' => 0,
                'total_items' => $total_items,
                'audit_compliance_icon' => 'a-circle-checked',
                'audit_compliance_status' => 'ok-actionable',
                'audit_compliance_status_text' => 'AUDIT COMPLIANT',
                'followup_status' => '',
                'followup_status_text' => 'NO FOLLOWUPS',
                'file_audit_icon' => 'a-folder',
                'file_audit_status' => '',
                'file_audit_status_text' => 'CLICK TO ADD A FINDING',
                'nlt_audit_icon' => 'a-booboo',
                'nlt_audit_status' => '',
                'nlt_audit_status_text' => 'CLICK TO ADD A FINDING',
                'lt_audit_icon' => 'a-skull',
                'lt_audit_status' => '',
                'lt_audit_status_text' => 'CLICK TO ADD A FINDING',
                'smoke_audit_icon' => 'a-flames',
                'smoke_audit_status' => '',
                'smoke_audit_status_text' => 'CLICK TO ADD A FINDING',
                'auditor_status_icon' => 'a-avatar-fail',
                'auditor_status' => 'action-required',
                'auditor_status_text' => 'ASSIGN AUDITORS',
                'message_status_icon' => 'a-envelope-4',
                'message_status' => '',
                'message_status_text' => '',
                'document_status_icon' => 'a-files',
                'document_status' => '',
                'document_status_text' => 'DOCUMENT STATUS',
                'history_status_icon' => 'a-person-clock',
                'history_status' => '',
                'history_status_text' => 'NO/VIEW HISTORY',
                'step_id' => $step_id,
                'step_status_icon' => $step_icon,
                'step_status' => 'no-action',
                'step_status_text' => $step_status_text,
                'estimated_time' => $cached_audit->estimated_time,
                'estimated_time_needed' => $cached_audit->estimated_time_needed,
                //'amenities_json' => json_encode($amenities)
            ]);
        }else{
            

            $cached_audit = new CachedAudit([
                'audit_id' => $audit->id,
                'audit_key' => $audit->monitoring_key,
                'project_id' => $project_id,
                'project_key' => $audit->development_key,
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
                'inspection_icon' => $inspection_icon,
                'inspection_status' => $inspection_status, 
                'inspection_status_text' => $inspection_status_text,
                'inspection_schedule_text' => $inspection_schedule_text,
                'inspection_schedule_date' => $inspection_schedule_date,
                'inspection_schedule_json' => null, // TBD
                'inspectable_items' => $audit->amenity_inspections->count(),
                'total_items' => $audit->total_items(),
                'audit_compliance_icon' => 'a-circle-checked',
                'audit_compliance_status' => 'ok-actionable',
                'audit_compliance_status_text' => 'AUDIT COMPLIANT',
                'followup_status' => '',
                'followup_status_text' => 'NO FOLLOWUPS',
                'file_audit_icon' => 'a-folder',
                'file_audit_status' => '',
                'file_audit_status_text' => 'CLICK TO ADD A FINDING',
                'nlt_audit_icon' => 'a-booboo',
                'nlt_audit_status' => '',
                'nlt_audit_status_text' => 'CLICK TO ADD A FINDING',
                'lt_audit_icon' => 'a-skull',
                'lt_audit_status' => '',
                'lt_audit_status_text' => 'CLICK TO ADD A FINDING',
                'smoke_audit_icon' => 'a-flames',
                'smoke_audit_status' => '',
                'smoke_audit_status_text' => 'CLICK TO ADD A FINDING',
                'auditor_status_icon' => 'a-avatar-fail',
                'auditor_status' => 'action-required',
                'auditor_status_text' => 'ASSIGN AUDITORS',
                'message_status_icon' => 'a-envelope-4',
                'message_status' => '',
                'message_status_text' => '',
                'document_status_icon' => 'a-files',
                'document_status' => '',
                'document_status_text' => 'DOCUMENT STATUS',
                'history_status_icon' => 'a-person-clock',
                'history_status' => '',
                'history_status_text' => 'NO/VIEW HISTORY',
                'step_id' => 1,
                'step_status_icon' => 'a-home-question',
                'step_status' => 'no-action',
                'step_status_text' => 'REVIEW INSPECTABLE AREAS',
                'estimated_time' => $estimated_time,
                'estimated_time_needed' => $estimated_time_needed,
                //'amenities_json' => json_encode($amenities)
            ]);
            $cached_audit->save();
        }

        // $data = [
        //     'event' => 'NewMessage',
        //     'data' => [
        //         'stats_communication_total' => $stats_communication_total
        //     ]
        // ];

        // Redis::publish('communications', json_encode($data));
    }
    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        
       
        $people = People::where('id','<>','7859')->get()->all();
       
        $email = '@allita.org';
        $password = 'password1234';

        $this->line('We are changing the names of all the people first..');
        $processBar = $this->output->createProgressBar(count($people));
        foreach ($people as $person) {
            $faker = Faker::create();
            $person->first_name = $faker->firstName;
            $person->last_name = $faker->lastName;
            $person->save();
            $processBar->advance();
        }
        unset($people);
        $properties = Project::get()->all();
        $this->line(PHP_EOL.'We are changing the names of all the properties next..');
        $processBar = $this->output->createProgressBar(count($properties));
        foreach ($properties as $property) {
            $faker = Faker::create();
            $property->project_name = $faker->company.' '.$faker->companySuffix;
            $property->save();
            $processBar->advance();
        }
        unset($properties);
        $addresses = Address::get()->all();
        $this->line(PHP_EOL.'We are changing the addresses of all the properties next..');
        $processBar = $this->output->createProgressBar(count($addresses));
        foreach ($addresses as $property) {
            $faker = Faker::create();
            $property->line_1 = $faker->streetAddress;
            $property->city = $faker->city;
            $property->zip = $faker->postcode;
            $property->latitude = $faker->latitude;
            $property->longitude = $faker->longitude;
            $property->save();
            $processBar->advance();
        }
        
        
        unset($addresses);

        $organizations = Organization::get()->all();
        $this->line(PHP_EOL.'We are changing the organizations next..');
        $processBar = $this->output->createProgressBar(count($organizations));
        foreach ($organizations as $property) {
            $faker = Faker::create();
            $property->organization_name = $faker->company;
            $property->save();
            $processBar->advance();
        }
        
        unset($organizations);

        
        $users = User::get()->all();
        if($this->confirm('Would you like to set all emails to @allita.org with a password of "password1234" ?'.PHP_EOL.'Enter "no" to set a custom email and password.')){
            $i = 0;
            $this->line(PHP_EOL.'We will set each login email to be first initial + last name + plus their user_id number @allita.org - ie "bgreenwood1234@allita.org".'.PHP_EOL.'(NOTE: we remove spaces and () characters from last names)');
            $processBar = $this->output->createProgressBar(count($users));
            foreach ($users as $user) {
                $i++;
               
                $userNewEmail = substr($user->person->first_name, 0, 1).str_replace(' ','', str_replace('(','',str_replace(')','',$user->person->last_name))).$user->id."@allita.org";
                //$this->line($i.' User: '.$user->email.' new login email address: '.$userNewEmail.PHP_EOL);
                if ($userNewEmail !== 0) {
                    User::where('id', $user->id)->update(['email'=> $userNewEmail, 'password'=> bcrypt('password1234'),'name'=>$user->person->first_name.' '.$user->person->last_name]);
                }
                $org = Organization::find($user->organization_id);
                if($org){
                    $user->organization = $org->organization_name;
                }
                $processBar->advance();
            }
            $this->line('All users now have password "password1234".');
        } else {
            $email = $this->ask('What email domain would you like to use?'.PHP_EOL.'(include the @ symbol - for example to use gmail.com enter "@gmail.com"');
            $password = $this->ask('What password would you like each account to have?');
            $i = 0;
             $this->line(PHP_EOL.'We will set each login email to be first initial + last name + plus their user_id number '.$email.' - ie "bgreenwood1234'.$email.'".'.PHP_EOL.'(NOTE: we remove spaces and () characters from last names)');
            $processBar = $this->output->createProgressBar(count($users));
            foreach ($users as $user) {
                $i++;
                
               $userNewEmail = substr($user->person->first_name, 0, 1).str_replace(' ','', str_replace('(','',str_replace(')','',$user->person->last_name))).$user->id.$email;
                //$this->line($i.' User: '.$user->email.' new login email address: '.$userNewEmail.PHP_EOL);
                if ($userNewEmail !== 0) {
                    User::where('id', $user->id)->update(['email'=> $userNewEmail, 'password'=> bcrypt($password),'name'=>$user->person->first_name.' '.$user->person->last_name]);
                }
                $org = Organization::find($user->organization_id);
                if($org){
                    $user->organization = $org->organization_name;
                }
                $processBar->advance();
            }
            $this->line('All users now have password "'.$password.'".');
        }
        unset($users);
        $cachedAudits = CachedAudit::get()->all();
        $this->line(PHP_EOL.'We updating the caches..');
        $processBar = $this->output->createProgressBar(count($cachedAudits));
        forEach($cachedAudits as $ca){
            
            $audit = Audit::where('id','=',$ca->audit_id)->first();
            if($audit){
                $this->createNewCachedAudit($audit);
            }else{
            }
            $processBar->advance();
        }
    }
}
