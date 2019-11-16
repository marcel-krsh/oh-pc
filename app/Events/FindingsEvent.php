<?php

namespace App\Events;

use App\Jobs\ComplianceSelectionJob;
use App\Models\Audit;
use App\Models\BuildingInspection;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
use App\Models\Finding;
use App\Models\FindingType;
use App\Models\Organization;
use App\Models\Program;
use App\Models\Project;
use App\Models\ProjectContactRole;
use App\Models\SystemSetting;
use App\Models\Unit;
use App\Models\UnitInspection;
use App\Models\UnitProgram;
use App\Models\User;
use App\Services\DevcoService;
use Auth;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Log;

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
            $type = FindingType::where('id', '=', $finding->finding_type_id)->first()->type;

            if ($finding->building_id) {
                $building = CachedBuilding::where('building_id', '=', $finding->building_id)->where('audit_id', '=', $finding->audit_id)->first();
                $finding_total = $building->finding_total;

                if ($type == 'file') {
                    $finding_file_total = $building->finding_file_total;
                    $building->finding_file_total = $finding_file_total + 1;
                    $building->finding_total = $finding_total + 1;
                    $building->save();
                } elseif ($type == 'nlt') {
                    $finding_nlt_total = $building->finding_nlt_total;
                    $building->finding_nlt_total = $finding_nlt_total + 1;
                    $building->finding_total = $finding_total + 1;
                    $building->save();
                } elseif ($type == 'lt') {
                    $finding_lt_total = $building->finding_lt_total;
                    $building->finding_lt_total = $finding_lt_total + 1;
                    $building->finding_total = $finding_total + 1;
                    $building->save();
                }
            }
            if ($finding->unit_id) {
                $unit = CachedUnit::where('unit_id', '=', $finding->unit_id)->where('audit_id', '=', $finding->audit_id)->first();
                $finding_total = $unit->finding_total;

                if ($type == 'file') {
                    $finding_file_total = $unit->finding_file_total;
                    $unit->finding_file_total = $finding_file_total + 1;
                    $unit->finding_total = $finding_total + 1;
                    $unit->save();
                } elseif ($type == 'nlt') {
                    $finding_nlt_total = $unit->finding_nlt_total;
                    $unit->finding_nlt_total = $finding_nlt_total + 1;
                    $unit->finding_total = $finding_total + 1;
                    $unit->save();
                } elseif ($type == 'lt') {
                    $finding_lt_total = $unit->finding_lt_total;
                    $unit->finding_lt_total = $finding_lt_total + 1;
                    $unit->finding_total = $finding_total + 1;
                    $unit->save();
                }

                // also save totals at the building level
                $building = CachedBuilding::where('building_id', '=', $unit->building_id)->where('audit_id', '=', $finding->audit_id)->first();
                $finding_total = $building->finding_total;

                if ($type == 'file') {
                    $finding_file_total = $building->finding_file_total;
                    $building->finding_file_total = $finding_file_total + 1;
                    $building->finding_total = $finding_total + 1;
                    $building->save();
                } elseif ($type == 'nlt') {
                    $finding_nlt_total = $building->finding_nlt_total;
                    $building->finding_nlt_total = $finding_nlt_total + 1;
                    $building->finding_total = $finding_total + 1;
                    $building->save();
                } elseif ($type == 'lt') {
                    $finding_lt_total = $building->finding_lt_total;
                    $building->finding_lt_total = $finding_lt_total + 1;
                    $building->finding_total = $finding_total + 1;
                    $building->save();
                }
            }
        }
    }
}
