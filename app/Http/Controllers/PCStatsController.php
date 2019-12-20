<?php

namespace App\Http\Controllers;

use App\Exports\AuditsExport;
use App\Models\CachedAudit;
use Excel;
use Illuminate\Http\Request;
use \DB;

class PCStatsController extends Controller
{
	//
	public function showStats(Request $request)
	{
		$thirtyDaysAgo = date('Y-m-d 23:59:59', str_to_time('31 days ago'));
		$sixtyDaysAgo = date('Y-m-d 23:59:59', str_to_time('61 days agao'));
		$ninetyDaysAgo = date('Y-m-d 23:59:59', str_to_time('91 days agao'));
		dd($thirtyDaysAgo, $sixtyDaysAgo, $ninetyDaysAgo);
		$cachedAudits30Days = CachedAudit::where('inspection_schedule_date', '>', '$thirtyDaysAgo')->where('inspection_schedule_date', '>', '$thirtyDaysAgo')->get();
		return view('layouts.stats.audit_stats', compact('cachedAudits30Days'));
	}

	public function showStatsRawData(Request $request)
	{
		$dashboard_controller = new DashboardController;
		$audit_ids = $dashboard_controller->audits($request, -100);
		$cachedAudits = CachedAudit::whereIn('id', $audit_ids)->get();
		$totalEstimatedTime = CachedAudit::sum(DB::raw("TIME_TO_SEC(estimated_time)"));
		$totalEstimatedTimeNeeded = CachedAudit::sum(DB::raw("TIME_TO_SEC(estimated_time_needed)"));
		return Excel::download(new AuditsExport($cachedAudits, $totalEstimatedTime, $totalEstimatedTimeNeeded), 'Audits_raw_data.xlsx');

		return view('layouts.stats.audit_raw_data', compact('cachedAudits', 'totalEstimatedTime', 'totalEstimatedTimeNeeded'));

		return Excel::download(new AuditsExport, 'Audits_raw_data.xlsx');

		return view('layouts.stats.audit_raw_data', compact('cachedAudits', 'totalEstimatedTime', 'totalEstimatedTimeNeeded'));
	}
}
