<?php

namespace App\Http\Controllers;
use DB;
use DateTime;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
//use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Console\Scheduling\Schedule;
use Log;
use Event;
use App\Models\Audit;
use App\Jobs\CreateTestAuditJob;





class SyncController extends Controller
{
    //
    public function associate($model,$lookUpModel,$associations){
        foreach($associations as $associate){
            $updates = $model::select($associate['look_up_reference'])
                        ->whereNull($associate['null_field'])
                        ->where($associate['look_up_reference'],$associate['condition_operator'],$associate['condition'])
                        ->groupBy($associate['look_up_reference'])
                        //->toSQL();
                        ->get()->all();
            //dd($updates);
            foreach ($updates as $update) {
                //lookup model
                //dd($update,$update->{$associate['look_up_reference']});
                $key = $lookUpModel::select($associate['look_up_foreign_key'])
                ->where($associate['lookup_field'],$update->{$associate['look_up_reference']})
                ->first();
                if(!is_null($key)){
                    $model::whereNull($associate['null_field'])
                        ->where(
                                $associate['look_up_reference'],
                                $update->{$associate['look_up_reference']}
                                )
                        ->update([
                                  $associate['null_field'] => $key->{$associate['look_up_foreign_key']}
                                                                    ]);
                } else {
                    //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->{$associate['look_up_reference']}.' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the model.');
                    echo date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->{$associate['look_up_reference']}.' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the model.<hr />';

                }

            }
        }
    }

    public function sync(Request $request) {


        //Audit::where('audit_id',$request->get('development_key'))->update(['audit_status_id'=>4]);
        //TEST EVENT
        $testaudit = Audit::where('development_key','=', $request->get('development_key'))->where('monitoring_status_type_key', '=', 4)->orderBy('start_date','desc')->first();
        CreateTestAuditJob::dispatch($testaudit)->onQueue('cache');
        
    }

    public function brianTest(Request $request){
        $test = \App\Models\Project::where('project_id',$request->get('project_id'));

        dd('Project Model','projectProgramCounts:<br />',$test);
    }
}
