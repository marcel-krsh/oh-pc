<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CachedAudit;
use \DB;

class PCStatsController extends Controller
{
    //
    public function showStats(Request $request){
    	$thirtyDaysAgo = date('Y-m-d 23:59:59',str_to_time('31 days ago'));
    	$sixtyDaysAgo = date('Y-m-d 23:59:59',str_to_time('61 days agao'));
    	$ninetyDaysAgo = date('Y-m-d 23:59:59',str_to_time('91 days agao'));
    	dd($thirtyDaysAgo,$sixtyDaysAgo,$ninetyDaysAgo);
    	$cachedAudits30Days = CachedAudit::where('inspection_schedule_date','>','$thirtyDaysAgo')->where('inspection_schedule_date','>','$thirtyDaysAgo')->get();
    	return view('layouts.stats.audit_stats', compact('cachedAudits30Days'));
    }

    public function showStatsRawData(Request $request){
    	
    	$cachedAudits = CachedAudit::get();
    	$totalEstimatedTime = CachedAudit::sum(DB::raw("TIME_TO_SEC(estimated_time)"));
    	$totalEstimatedTimeNeeded = CachedAudit::sum(DB::raw("TIME_TO_SEC(estimated_time_needed)"));

    	return view('layouts.stats.audit_raw_data', compact('cachedAudits','totalEstimatedTime','totalEstimatedTimeNeeded'));
    }
}
