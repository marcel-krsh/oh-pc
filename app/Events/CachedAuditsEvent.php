<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\AuditAuditor;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Audit;
use App\Models\Unit;
use App\Models\Project;
use App\Models\UnitInspection;
use App\Models\Organization;
use App\Models\BuildingInspection;
use App\Models\ProjectContactRole;
use App\Models\Program;
use App\Services\DevcoService;
use App\Models\UnitProgram;
use App\Models\AmenityInspection;
use App\Models\CachedUnit;
use Auth;



class CachedAuditsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
           // Auth::onceUsingId(1); // TEST BRIAN
            Auth::onceUsingId(286); // TEST
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    
    public function cachedAuditCreated(CachedAudit $cached_audit)
    {
        
        $jsonRun = 0;
        // get buildings from cached_audit
        $buildings = BuildingInspection::where('audit_id', '=', $cached_audit->audit_id)->with('building','building.address')->get();
        //dd($buildings);

        // get the auditors' list from audit_auditors table
        // [{"id": "1", "name": "Brian Greenwood", "color": "green", "status": "alert", "initials": "BG"}, {"id": "2", "name": "Brian Greenwood 2", "color": "blue", "status": "", "initials": "BF"}]
        //
        // also save the lead auditor in the table
        // $audit = Audit::where('id', '=', $cached_audit->audit_id)->first();
        // if ($audit->user_id) {
        //     $lead_key = $audit->user_key;
        //     $lead_id = $audit->lead_user_id;
        // } else {
        //     $lead_key = null;
        //     $lead_id = null;
        // }

        $auditors = AuditAuditor::where('audit_id', '=', $cached_audit->audit_id)->with('user')->get();
        $auditors_array = [];
        // if ($auditors) {
        //     foreach ($auditors as $auditor) {
        //         if ($auditor->user) {
        //             $lead = $auditor->user;
        //             $words = explode(" ", $lead->name);
        //             $initials = "";
        //             foreach ($words as $w) {
        //                 $initials .= $w[0];
        //             }
        //             $initials = substr($initials, 0, 2); // keep the first two letters only

        //             $auditors_array[] = [
        //                 'id' => $auditor->user->id,
        //                 'name' => $auditor->user->name,
        //                 'color' => $auditor->user->color,
        //                 'status' => '',
        //                 'initials' => $initials
        //             ];
        //         }
        //     }
        // }

        // create cached buildings related to this audit
        // 
        CachedBuilding::where('audit_id',$cached_audit->audit_id)->delete();
        foreach ($buildings as $building) {
            //dd($building->building);
            $count_units = UnitInspection::where('building_key', '=', $building->building_key)->count();
            $finding_total = $building->nlt_count + $building->lt_count + $building->file_count;
            $building_amenities = AmenityInspection::where('building_id',$building->building_id)->with('amenity')->get();
            //dd($building_amenities,$building);
            //build amenity json:
            //[{"id": "295", "qty": "2", "type": "Elevator", "status": "pending"},]
            //
            $baJson = '';
            $baJson = '[';
            forEach($building_amenities as $ba){

                if($ba->amenity->inspectable == 1){
                    if($jsonRun == 1){
                        $baJson .= ' , ';
                        //insert comma between groups
                    }
                    $jsonRun = 1;
                    $baJson .= '{"id": "'.$ba->amenity_id.'", "qty": "0", "type": "'.addslashes($ba->amenity->amenity_description).'","status":"","common_area":"'.$ba->common_area.'","project":"'.$ba->project.'","building_system":"'.$ba->building_system.'","building_exterior":"'.$ba->building_exterior.'","unit":"'.$ba->unit.'","file":"'.$ba->file.'"}';
                } else {
                    //dd($ba,$ba->amenity->inspectable);
                }
            }
            $baJson .= ']';
            $jsonRun = 0;
            
            
            $cached_building = new CachedBuilding([
                'building_name' => $building->building_name,
                'building_id' => $building->building_id,
                'building_key' => $building->building_key,
                'audit_id' => $cached_audit->audit_id,
                'audit_key' => $cached_audit->audit_key,
                'project_id' => $building->project_id,
                'project_key' => $building->project_key,
                'lead_id' => $building->project_id,
                'lead_key' => $building->project_key,
                'status' => '',
                'type' => 'unit',
                'type_total' => $count_units,
                'type_text' => 'UNIT',
                'type_text_plural' => 'UNITS',
                'finding_total' => $finding_total,
                'finding_file_status' => '',
                'finding_nlt_status' => '',
                'finding_lt_status' => '',
                'finding_file_total' => $building->file_count,
                'finding_file_completed' => 0,
                'finding_nlt_total' => $building->nlt_count,
                'finding_nlt_completed' => 0,
                'finding_lt_total' => $building->lt_count,
                'finding_lt_completed' => 0,
                'address' => $building->building->address->line_1,
                'city' => $building->building->address->city,
                'state' => $building->building->address->state,
                'zip' => $building->building->address->zip,
                'auditors_json' => json_encode($auditors_array),
                'amenities_json' => $baJson
            ]);
            $cached_building->save();

        }

            // create project level amenities
            $project_amenities = AmenityInspection::where('project_id',$cached_audit->project_id)->with('amenity')->get();

            //build amenity json:
            //[{"id": "295", "qty": "2", "type": "Elevator", "status": "pending"},]
            
            forEach($project_amenities as $ba){

                if($ba->amenity->inspectable){
                    $baJson = '[{"id": "'.$ba->amenity_id.'", "qty": "0", "type": "'.addslashes($ba->amenity->amenity_description).'","status":"","common_area":"'.$ba->common_area.'","project":"'.$ba->project.'","building_system":"'.$ba->building_system.'","building_exterior":"'.$ba->building_exterior.'","unit":"'.$ba->unit.'","file":"'.$ba->file.'"}]';
                
            
            
                    $cached_building = new CachedBuilding([
                        'building_name' => $ba->amenity->amenity_description,
                        'building_id' => null,
                        'building_key' => null,
                        'audit_id' => $cached_audit->audit_id,
                        'audit_key' => $cached_audit->audit_key,
                        'project_id' => $cached_audit->project_id,
                        'project_key' => $building->project_key,
                        'lead_id' => $cached_audit->lead_id,
                        'lead_key' => $cached_audit->lead_key,
                        'status' => '',
                        'type' => $ba->amenity->icon,
                        'type_total' => null,
                        'type_text' => null,
                        'type_text_plural' => null,
                        'finding_total' => $finding_total,
                        'finding_file_status' => '',
                        'finding_nlt_status' => '',
                        'finding_lt_status' => '',
                        'finding_file_total' => 0,
                        'finding_file_completed' => 0,
                        'finding_nlt_total' => 0,
                        'finding_nlt_completed' => 0,
                        'finding_lt_total' => 0,
                        'finding_lt_completed' => 0,
                        'address' => $cached_audit->address,
                        'city' => $cached_audit->city,
                        'state' => $cached_audit->state,
                        'zip' => $cached_audit->zip,
                        'auditors_json' => json_encode($auditors_array),
                        'amenities_json' => $baJson,
                        'amenity_id' =>$ba->amenity_id
                    ]);
                    $cached_building->save();
                }
            }

        

        // create cached units
        // $units = UnitInspection::select('unit_id', 'project_id',  'project_key', 'building_id', 'building_key')->where('audit_key', '=', $cached_audit->audit_key)->with('unit','unit.building.address')->groupBy('unit_id')->get();
        
        $units = UnitInspection::select('unit_id', 'project_id',  'project_key', 'building_id', 'building_key')->where('audit_key', '=', '5974')->get()->groupBy('unit_id');

        CachedUnit::where('audit_id',$cached_audit->audit_id)->delete();
        
        foreach ($units as $unit) {
            // get the unit type (bedroom type)
            //
            //
            $unit_amenities = AmenityInspection::where('unit_id',$unit->unit_id)->with('amenity')->get();
            //dd($unit_amenities);
            $uaCount = 0;
            //Unit amenity json:
            //[{"id": "295", "qty": "2", "type": "Elevator", "status": "pending"},]
            $uaJson = '[';
            forEach($unit_amenities as $ua){
                if($ua->amenity->inspectable){
                    $uaCount++;
                    if($jsonRun == 1){
                        $uaJson .= ' , ';
                        //insert comma between groups
                    }
                    $jsonRun = 1;

                    $uaJson .= '{"id": "'.$ua->amenity_id.'", "qty": "0", "type": "'.addslashes($ua->amenity->amenity_description).'","status":"","common_area":"'.$ua->common_area.'","project":"'.$ua->project.'","building_system":"'.$ua->building_system.'","building_exterior":"'.$ua->building_exterior.'","unit":"'.$ua->unit.'","file":"'.$ua->file.'"}';
                    
                }
            }
            $uaJson .= ']';
            $jsonRun = 0;
            

            $cached_unit = new CachedUnit([
                'audit_id' => $cached_audit->audit_id,
                'audit_key' => $cached_audit->audit_key,
                'project_id' => $unit->project_id,
                'project_key' => $unit->project_key,
                'amenity_id' => null,
                'building_id' => $unit->building_id,
                'building_key' => $unit->building_key,
                'status' => null,
                'type' => 'amenity',
                'type_total' => $uaCount,
                'type_text' => 'AMENITY',
                'type_text_plural' => 'AMENITIES',
                'program_total' => null,
                'finding_total' => 0,
                'finding_file_status' => '',
                'finding_nlt_status' => '',
                'finding_lt_status' => '',
                'finding_sd_status' => '',
                'finding_file_total' => '0',
                'finding_nlt_total' => '0',
                'finding_lt_total' => '0',
                'finding_sd_total' => '0',
                'finding_file_completed' => '0',
                'finding_nlt_completed' => '0',
                'finding_lt_completed' => '0',
                'finding_sd_completed' => '0',
              //  'followup_date' => '',
                'address' => $unit->unit->building->address->line_1,
                'city' => $unit->unit->building->address->city,
                'state' => $unit->unit->building->address->state,
                'zip' => $unit->unit->building->address->zip,
                'auditors_json' => null,
                'amenities_json' => $uaJson,
                'unit_id'=>$unit->unit->id,
                'unit_key'=>$unit->unit->unit_key,
                'unit_name'=>$unit->unit->unit_name
            ]);
            $cached_unit->save();
        }
    }
}
