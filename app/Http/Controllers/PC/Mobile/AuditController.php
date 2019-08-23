<?php

namespace App\Http\Controllers\PC\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\AmenityInspection;
use App\Models\Audit;
use App\Models\AuditAuditor;
use App\Models\Building;
use App\Models\BuildingAmenity;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedComment;
use App\Models\CachedInspection;
use App\Models\CachedUnit;
use App\Models\Comment;
use App\Models\Finding;
use App\Models\Job;
use App\Models\Project;
use App\Models\ProjectAmenity;
use App\Models\SystemSetting;
use App\Models\Unit;
use App\Models\UnitAmenity;
use App\Models\UnitInspection;
use App\Models\ProjectDetail;
use App\Models\UnitProgram;
use App\Models\User;
use DB;
use Auth;
use Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Session;
use App\Models\Group;
use App\Models\Program;
use App\Models\ProgramGroup;
use View;
use App\Models\BuildingInspection;


class AuditController extends Controller
{
	private $htc_group_id;

    public function __construct()
    {
        // $this->middleware('auth');
        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
            Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
            //Auth::onceUsingId(286); // TEST BRIAN
            // 6281 holly
            // 6346 Robin (Abigail)
        }
      $this->htc_group_id = 7;
      View::share ('htc_group_id', $this->htc_group_id );
    }

    public function index(Request $request){
        $user = Auth::user();
        $cachedAudits = CachedAudit::with('audit.auditors')->where('auditors.user_id',$user->id)->orWhere('lead',$user->id)->where('step_id','>',59)->where('step_id','<',67);
        return view('pc.mobile.audits',compact('user','cachedAudits'));
    }
    

}
