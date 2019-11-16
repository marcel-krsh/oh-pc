<?php

namespace App\Http\Controllers;

use App\Models\AmenityInspection;
use App\Models\Audit;
use App\Models\AuditAuditor;
use App\Models\BuildingInspection;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComplianceGenerator extends Controller
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function details(Audit $audit)
    {
        foreach ($audit->project->amenities as $pa) {
            AmenityInspection::insert([
                'audit_id'=>$audit->id,
                'monitoring_key'=>$audit->monitoring_key,
                'project_id'=>$audit->project_id,
                'development_key'=>$audit->development_key,
                'amenity_id'=>$pa->amenity_id,
                'amenity_key'=>$pa->amenity_key,

           ]);
        }
        foreach ($audit->project->buildings as $b) {
            foreach ($b->amenities as $ba) {
                AmenityInspection::insert([
                    'audit_id'=>$audit->id,
                    'monitoring_key'=>$audit->monitoring_key,
                    'building_key'=>$b->building_key,
                    'building_id'=>$b->id,
                    'amenity_id'=>$ba->amenity_id,
                    'amenity_key'=>$ba->amenity_key,
                    'project_id'=>$audit->project_id,
                    'development_key'=>$audit->development_key,

               ]);
            }
        }
        foreach ($audit->unique_unit_inspections as $u) {
            foreach ($u->amenities as $ua) {
                AmenityInspection::insert([
                    'audit_id'=>$audit->id,
                    'monitoring_key'=>$audit->monitoring_key,
                    'unit_key'=>$ua->unit_key,
                    'unit_id'=>$ua->unit_id,
                    'amenity_id'=>$ua->amenity_id,
                    'amenity_key'=>$ua->amenity_key,
                    'building_key'=>$b->building_key,
                    'building_id'=>$b->id,
                    'project_id'=>$audit->project_id,
                    'development_key'=>$audit->development_key,

               ]);
            }
        }
        echo $audit->total_items();
    }

    public function createCaches(CachedAudit $cached_audit)
    {
        $cached_audit->update(['status'=>'sup yall '.time()]);
    }
}
