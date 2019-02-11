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
use App\Models\Finding;
use App\Models\Project;
use App\Models\UnitInspection;
use App\Models\Organization;
use App\Models\BuildingInspection;
use App\Models\ProjectContactRole;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
use App\Models\Program;
use App\Services\DevcoService;
use App\Models\FindingType;
use App\Models\UnitProgram;
use Illuminate\Support\Facades\Redis;
use Auth;

use App\Jobs\ComplianceSelectionJob;

class FindingsEvent 
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
    

    public function findingCreated(Finding $finding)
    {
        if ($finding) {
            //dd($audit);
            // update findings totals in cached building, unit, audit as needed
            
            // type of finding
            $type = FindingType::where('id','=',$finding->finding_type_id)->first()->type;

            if($finding->building_id){
                $building = CachedBuilding::where('building_id','=',$finding->building_id)->where('audit_id','=',$finding->audit_id)->first();

            }
            if($finding->unit_id){
                $unit = CachedUnit::where('unit_id','=',$finding->unit_id)->where('audit_id','=',$finding->audit_id)->first();
            }
        }
        
    }

    
}
