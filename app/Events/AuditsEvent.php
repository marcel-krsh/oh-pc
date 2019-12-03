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

use App\Jobs\ComplianceSelectionJobJune19Optimized;

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
        if ($audit) {
            //dd($audit);
            Log::info('Audit Log Triggered');
            if (( ( in_array($audit->monitoring_status_type_key, [4,5,6]) && $audit->compliance_run == null) || $audit->rerun_compliance == 1) && $audit->findings->count() < 1) {

                // fire event
                $check = \DB::table('jobs')->where('payload','LIKE',"%".$audit->id."%")->where('queue','compliance')->count();

                if($check<1){
                    ComplianceSelectionJobJune19Optimized::dispatch($audit)->onQueue('compliance');
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
                $check = \DB::table('jobs')->where('payload','LIKE',"%".$audit->id."%")->where('queue','compliance')->count();

                if($check<1){
                    ComplianceSelectionJobJune19Optimized::dispatch($audit)->onQueue('compliance');
                }

            }
        }

    }


}
