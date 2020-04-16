<?php

namespace App\Http\Controllers;

use Excel;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\CachedAudit;
use App\Exports\ContactsToProjectExcel;

class AdhocReportController extends Controller
{
	//
	public function __construct()
	{
		$this->allitapc();
	}

	public function contactsToProject()
	{
		$cachedAudits = CachedAudit::select('project_id')->pluck('project_id');
		//dd($cachedAudits);

		$projects = Project::with('contactRoles')->with('contactRoles.person')->with('contactRoles.projectRole')->whereIn('id', $cachedAudits)->orderBy('project_number', 'ASC')->get();
		return view('.adhoc_reports.contacts_to_project', compact('projects'));

	}

	public function contactsToProjectExport()
	{
		ini_set('max_execution_time', 600); //3 minutes

		$cachedAudits = CachedAudit::select('project_id')->pluck('project_id');
		//dd($cachedAudits);

		$projects = Project::with('contactRoles')->with('contactRoles.person')->with('contactRoles.projectRole')->whereIn('id', $cachedAudits)->orderBy('project_number', 'ASC')->get();
		$time = Carbon::now()->format('m_d_Y_h_m_A'); //Carbon::createFromFormat('m-d-Y H:m:s', Carbon::now());
		$file_name = 'PROJECT_CONTACTS_DATA_' . $time . '.xls'; //BG_AUDIT_DATA_12_20_2019_9_36_AM.xls
		// return view('layouts.stats.project_contacts', compact('cachedAudits', 'projects'));

		return Excel::download(new ContactsToProjectExcel($cachedAudits, $projects), $file_name);
	}
}
