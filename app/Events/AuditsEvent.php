<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Log;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Audit;
use App\Models\Unit;
use App\Models\Project;
use App\Models\UnitInspection;
use App\Models\Organization;
use App\Models\BuildingInspection;
use App\Models\ProjectContactRole;
use App\Models\CachedAudit;
use App\Models\Program;
use App\Services\DevcoService;
use App\Models\UnitProgram;
use Illuminate\Support\Facades\Redis;
use Auth;

use App\Jobs\ComplianceSelectionJob;

class AuditsEvent 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */


    public $user;

    public function __construct()
    {
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    

    public function auditCreated(Audit $audit)
    {
        // check the monitoring_status_type_key for 4,5 or 6
        // that means we have to create CachedAudit row if doesn't exist (shouldn't, since it is a creation)
        if ($audit) {
            if (in_array($audit->monitoring_status_type_key, [4,5,6])) {
                if (!CachedAudit::where('audit_id', '=', $audit->id)->count()) {
                    //if($this->fetchAuditUnits($audit)){             // first get units
                    if (1) {
                        // run the selection process 10 times and keep the best one
                        $best_run = null;
                        $best_total = null;
                        $overlap = null;
                        $project = null;
                        $organization_id = null;

                        for ($i=0; $i<3; $i++) {
                            $summary = $this->selectionProcess($audit);
                            if (count($summary[0]['grouped']) < $best_total || $best_run == null) {
                                $best_run = $summary[0];
                                $overlap = $summary[1];
                                $project = $summary[2];
                                $organization_id = $summary[3];
                                $best_total = count($summary[0]['grouped']);
                            }
                        }

                        // save all units selected in selection table
                        if ($best_run) {
                            $group_id = 1;

                            foreach ($best_run['programs'] as $program) {
                                $unit_keys = $program['units_after_optimization'];

                                $units = Unit::whereIn('unit_key', $unit_keys)->get();

                                foreach ($units as $unit) {
                                    if (in_array($unit->unit_key, $overlap)) {
                                        $has_overlap = 1;
                                    } else {
                                        $has_overlap = 0;
                                    }

                                    $program_keys = explode(',', $program['program_keys']);

                                    foreach ($unit->programs as $unit_program) {
                                        if (in_array($unit_program->program_key, $program_keys)) {
                                            $u = new UnitInspection([
                                                'group' => $program['name'],
                                                'group_id' => $group_id,
                                                'unit_id' => $unit->id,
                                                'unit_key' => $unit->unit_key,
                                                'building_id' => $unit->building_id,
                                                'building_key' => $unit->building_key,
                                                'audit_id' => $audit->id,
                                                'audit_key' => $audit->monitoring_key,
                                                'project_id' => $project->id,
                                                'project_key' => $project->project_key,
                                                'program_key' => $unit_program->program_key,
                                                'pm_organization_id' => $organization_id,
                                                'has_overlap' => $has_overlap
                                            ]);
                                            $u->save();
                                        }
                                    }
                                }
                                $group_id = $group_id + 1;
                            }
                        }
                        
                        $this->createNewCachedAudit($audit, $best_run);    // finally create the audit
                    }
                }
            }
        }
    }

    public function auditUpdated(Audit $audit)
    {
        if ($audit) {
            //dd($audit);
            if (( ( in_array($audit->monitoring_status_type_key, [4,5,6]) && $audit->compliance_run == null) || $audit->rerun_compliance == 1) && $audit->findings->count() < 1) {
                
                // fire event
                $check = \DB::table('jobs')->where('payload','LIKE',"%s:2:\"id\";i:{$audit->id}%")->where('queue','compliance')->count();

                if($check<1){
                    ComplianceSelectionJob::dispatch($audit)->onQueue('compliance');
                }
                       
            }
        }
         
    }

    
}
