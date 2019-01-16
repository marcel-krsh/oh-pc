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
use Auth;

class ComplianceGenerator extends Controller
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function details(Audit $audit){
    	echo $audit->total_items();
    }
}
