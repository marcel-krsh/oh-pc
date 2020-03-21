<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\CachedAudit;

class AdhocReportController extends Controller
{
    //
    public function __construct(){
        $this->allitapc();
    }

    public function contactsToProject(){
    	$cachedAudits = CachedAudit::select('project_id')->pluck('project_id');
    	//dd($cachedAudits);

    	$projects = Project::with('contactRoles')->with('contactRoles.person')->with('contactRoles.projectRole')->whereIn('id',$cachedAudits)->orderBy('project_number','ASC')->get();
    	return view('.adhoc_reports.contacts_to_project', compact('projects'));

    }
}
