<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
use App\Models\CachedAudit;
use App\Models\Program;
use App\Services\DevcoService;
use App\Models\UnitProgram;
use App\Models\AmenityInspection;
use Auth;

class ComplianceGenerator extends Controller
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function details(Audit $audit){
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
        	foreach($b->amenities as $ba){
	           AmenityInspection::insert([
	                'audit_id'=>$audit->id,
                	'monitoring_key'=>$audit->monitoring_key,
	                'building_key'=>$b->building_key,
	                'building_id'=>$b->building_id,
	                'amenity_id'=>$ba->amenity_id,
	                'amenity_key'=>$ba->amenity_key,

	           ]);
	   		}
        }
    	echo $audit->total_items();
    }
}
