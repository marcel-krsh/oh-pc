<?php

namespace App\Http\Controllers;

use Auth;
use File;
use View;
use Storage;
use Carbon\Carbon;
use App\Models\Unit;
use App\Models\User;
use App\Models\Audit;
use App\Models\Photo;
use App\Models\People;
use App\Models\Amenity;
use App\Models\Finding;
use App\Models\Project;
use App\Models\Building;
use App\Models\Document;
use App\Models\CachedAudit;
use App\Models\FindingType;
use Illuminate\Http\Request;
use App\Models\DocumentAudit;
use App\Models\SystemSetting;
use App\Models\DocumentCategory;
use App\Models\CommunicationDraft;
use App\Models\LocalDocumentCategory;
use App\Http\Controllers\Traits\DocumentTrait;

class DocumentController extends Controller
{
	use DocumentTrait;

	public function __construct(Request $request)
	{
		$this->allitapc();
	}

	/**
	 * Show the documents' list for a specific parcel.
	 *
	 * @param  int  $parcel_id
	 * @return Response
	 */

	public function compare($value1, $value2)
	{
		if ($value1 != $value2) {
			return false;
		} else {
			return true;
		}
	}

	public function getProjectDocuments(Project $project, Request $request)
	{
		$audit_id = $request->audit_id;
		return view('projects.partials.documents', compact('project', 'audit_id'));
	}

	public function getProjectUploadFindingsList(Project $project, Request $request)
	{
		$showLinks = 1;
		//SHOW HIDE RESOLVED / UNRESOLVED
		if ($request->has('document-upload-unresolved')) {
			//dd($request->get('document-upload-unresolved'));
			if ($request->get('document-upload-unresolved') == 'on') {
				$unresolved = 1;
				session(['document-upload-documents-unresolved' => 1]);
			} else if ($request->get('document-upload-unresolved') == 'off') {
				$unresolved = 2;
				session(['document-upload-documents-unresolved' => 2]);
			} else if ($request->get('document-upload-unresolved') == 'both') {
				$unresolved = 3; // both
				session(['document-upload-documents-unresolved' => 3]);
			} else if ($request->get('document-upload-unresolved') == 'reset') {
				$unresolved = 3; // both
				$request->session()->forget('document-upload-documents-unresolved');
			}
		} else if (session('document-upload-documents-unresolved') > 0) {
			$unresolved = session('document-upload-documents-unresolved');
		} else {
			$unresolved = 3;
		}

		if ($request->has('document-upload-building-unit') && $request->get('document-upload-building-unit') != "") {
			//dd($request->get('document-upload-unresolved'));
			if (substr($request->get('document-upload-building-unit'), 0, 9) == "building-") {
				$isBuilding = 1;
				$buSearchValue = intval(str_replace('building-', '', $request->get('document-upload-building-unit')));
				session(['document-upload-findings-filter-is-building' => $isBuilding]);
				session(['document-upload-findings-filter-search-value' => $buSearchValue]);
			} else if (substr($request->get('document-upload-building-unit'), 0, 5) == "unit-") {
				$isBuilding = 2;
				$buSearchValue = intval(str_replace('unit-', '', $request->get('document-upload-building-unit')));
				session(['document-upload-findings-filter-is-building' => $isBuilding]);
				session(['document-upload-findings-filter-search-value' => $buSearchValue]);
			} else if ($request->get('document-upload-building-unit') == 'reset') {
				$isBuilding = NULL;
				$buSearchValue = NULL;
				$request->session()->forget('document-upload-findings-filter-is-building');
				$request->session()->forget('document-upload-findings-filter-search-value');
			}
		} else if (session('document-upload-findings-filter-search-value') > 0) {
			$isBuilding = session('document-upload-findings-filter-is-building');
			$buSearchValue = session('document-upload-findings-filter-search-value');
		} else {
			$isBuilding = NULL;
			$buSearchValue = NULL;
		}

		if ($request->has('document-upload-audit')) {
			//dd($request->get('document-upload-unresolved'));
			if ($request->get('document-upload-audit') == "reset") {
				$audit_id = NULL;
				$request->session()->forget('document-upload-findings-filter-audit');
			} else {
				$audit_id = $request->get('document-upload-audit');
				session(['document-upload-findings-filter-audit' => $audit_id]);
			}
		} else if (session('document-upload-findings-filter-audit') > 0) {
			$audit_id = session('document-upload-findings-filter-audit');
		} else {
			$audit_id = NULL;
		}

		//Check the Step Update of the audit
		//'66','Pending Property Resolution'
		//'67','Archive audit'
		//'68','Audit Score'
		if ($this->auditor_access) {
			$considered_audits = CachedAudit::where('project_id', $project->id)->pluck('audit_id');
		} else {
			$considered_audits = CachedAudit::where('project_id', $project->id)->whereIn('step_id', [66, 67, 68])->pluck('audit_id');
		}

		$useOrWhere = 'where';
		$allFindings = Finding::whereNull('cancelled_at')->where('project_id', $project->id)->whereIn('audit_id', $considered_audits);
		if ($isBuilding != NULL) {
			if ($isBuilding == 1) {
				$allFindings = $allFindings->where('building_id', $buSearchValue);
			} else {
				$allFindings = $allFindings->where('unit_id', $buSearchValue);
			}
			$useOrWhere = 'orWhere';
		}
		if ($unresolved == 1) {
			// filter to show unresolved
			$allFindings = $allFindings->where('auditor_approved_resolution', '<>', 1);
			$useOrWhere = 'orWhere';
		}
		if ($unresolved == 2) {
			// filter to show unresolved
			$allFindings = $allFindings->where('auditor_approved_resolution', 1);
			$useOrWhere = 'orWhere';
		}

		if ($audit_id) {
			// filter to show unresolved
			$allFindings = $allFindings->where('audit_id', $audit_id);
			$useOrWhere = 'orWhere';
		}

		$allFindings = $allFindings->paginate(100);

		//dd($allFindings);

		return view('projects.partials.findings-list-for-uploader', compact('project', 'audit_id', 'unresolved', 'allFindings', 'showLinks', 'isBuilding', 'buSearchValue'));
	}

	public function projectLocalDocumentUpload(Project $project, $audit_id = null, Request $request)
	{
		$showLinks = 0;
		$resolved = 1;
		$unresolved = 1;
		$searchTerm = NULL;
		$audits = collect([]);
		$findings = collect([]);
		$document_categories = DocumentCategory::select('parent.document_category_name as parent_category_name', 'document_categories.*')->join('document_categories as parent', 'document_categories.parent_id', '=', 'parent.id')
			->where('document_categories.parent_id', '<>', 0)
			->where('document_categories.active', 1)
			->orderBy('parent.document_category_name')
			->orderBy('document_categories.document_category_name')
			->get();

		//dd($document_categories);

		//$allUnits = $project->units()->orderBy('unit_name')->get();
		$allBuildings = $project->buildings()->with('units')->orderBy('building_name')->get();
		//dd($allUnits);
		// return $allFindings = $project->load('findings');
		$allFindings = $project->findings()->count();
		$loadFindingsSeperately = 0;
		if ($allFindings > 100) {
			$allFindings = collect([]); //$project->findings()->paginate(50);
			$loadFindingsSeperately = 1;
		} else {
			$allFindings = $project->findings()->get();
		}
		$allUnits = [];
		$allAudits = $project->audits;

		return view('projects.partials.document-uploader', compact('project', 'audit_id', 'searchTerm', 'audits', 'findings', 'resolved', 'unresolved', 'document_categories', 'allAudits', 'allFindings', 'allUnits', 'allBuildings', 'loadFindingsSeperately', 'showLinks'));
	}

	public function projectPMLocalDocumentUpload(Project $project, $audit_id = null, Request $request)
	{
		$showLinks = 0;
		$resolved = 1;
		$unresolved = 1;
		$searchTerm = NULL;
		$audits = collect([]);
		$findings = collect([]);
		$document_categories = DocumentCategory::select('parent.document_category_name as parent_category_name', 'document_categories.*')->join('document_categories as parent', 'document_categories.parent_id', '=', 'parent.id')
			->where('document_categories.parent_id', '<>', 0)
			->where('document_categories.active', 1)
			->orderBy('parent.document_category_name')
			->orderBy('document_categories.document_category_name')
			->get();

		//dd($document_categories);

		//$allUnits = $project->units()->orderBy('unit_name')->get();
		$allBuildings = $project->buildings()->with('units')->orderBy('building_name')->get();
		//dd($allUnits);
		$allFindings = $project->findings()->count();
		$loadFindingsSeperately = 0;
		if ($allFindings > 100) {
			$allFindings = collect([]); //$project->findings()->paginate(50);
			$loadFindingsSeperately = 1;
		} else {
			$allFindings = $project->findings()->get();
		}
		$allUnits = [];
		$allAudits = $project->audits;

		return view('projects.partials.pm-document-uploader', compact('project', 'audit_id', 'searchTerm', 'audits', 'findings', 'resolved', 'unresolved', 'document_categories', 'allAudits', 'allFindings', 'allUnits', 'allBuildings', 'loadFindingsSeperately', 'showLinks'));
	}

	public function getProjectLocalDocuments(Project $project, $audit_id = null, Request $request)
	{
		// ini_set('max_execution_time', 300);
		//check if filters exist
		// return $request->all();

		/// SEARCH TERM
		if ($request->has('local-search')) {
			if ($request->get('local-search') == "") {
				// reset the search
				$searchTerm = NULL;
				session(['local-documents-search-term' => NULL]);
			} else {
				$searchTerm = $request->get('local-search');
				session(['local-documents-search-term' => $request->get('local-search')]);
			}
		} else if (session()->has('local-documents-search-term') && session('local-documents-search-term') != NULL) {
			$searchTerm = session('local-documents-search-term');
		} else {
			$searchTerm = NULL;
		}

		//SHOW HIDE RESOLVED / UNRESOLVED
		if ($request->has('local-unresolved')) {
			//dd($request->get('local-unresolved'));
			if ($request->get('local-unresolved') == 'on') {
				$unresolved = 1;
				session(['local-documents-unresolved' => 1]);
			} else {
				$unresolved = 0;
				session(['local-documents-unresolved' => 0]);
			}
		} else if (session()->has('local-documents-unresolved') && session('local-documents-unresolved') == 0) {
			$unresolved = session('local-documents-unresolved');
		} else {
			$unresolved = 1;
		}

		if ($request->has('local-resolved')) {
			if ($request->get('local-resolved') == 'on') {
				$resolved = 1;
				session(['local-documents-resolved' => 1]);
			} else {
				$resolved = 0;
				session(['local-documents-resolved' => 0]);
			}
		} else if (session()->has('local-documents-resolved') && session('local-documents-resolved') == 0) {
			$resolved = session('local-documents-resolved');
		} else {
			$resolved = 1;
		}

		//SHOW HIDE REVIEWED / UNREVIEWED
		if ($request->has('local-unreviewed')) {
			//dd($request->get('local-unreviewed'));
			if ($request->get('local-unreviewed') == 'on') {
				$unreviewed = 1;
				session(['local-documents-unreviewed' => 1]);
			} else {
				$unreviewed = 0;
				session(['local-documents-unreviewed' => 0]);
			}
		} else if (session()->has('local-documents-unreviewed') && session('local-documents-unreviewed') == 0) {
			$unreviewed = session('local-documents-unreviewed');
		} else {
			$unreviewed = 1;
		}

		if ($request->has('local-reviewed')) {
			if ($request->get('local-reviewed') == 'on') {
				$reviewed = 1;
				session(['local-documents-reviewed' => 1]);
			} else {
				$reviewed = 0;
				session(['local-documents-reviewed' => 0]);
			}
		} else if (session()->has('local-documents-reviewed') && session('local-documents-reviewed') == 0) {
			$reviewed = session('local-documents-reviewed');
		} else {
			$reviewed = 1;
		}

		$documents_all = [];
		///

		$findings = collect([]);
		$all_finding_ids = [];
		$allUnits = $project->units()->orderBy('unit_name')->get();

		$documents_query = Document::where('project_id', $project->id)->with('assigned_categories.parent', 'communications.communication', 'audits', 'audit', 'user.person', 'buildings', 'units', 'findings.finding_type', 'findings.amenity')->orderBy('created_at', 'DESC');
		// return $documents_query->get();
		if ($searchTerm != NULL) {
			// apply search term to documents
			// The Query Searches:
			// 	Document Categories - OK
			// 	Finding Types - OK
			// 	Building Names - OK
			// 	Unit Names - OK
			// 	Amenity Names - amenity_description
			// 	First and Last Names of Uploaders - OK
			// 	Audit Numbers - OK
			// 	Finding Number - OK
			// 	Document Comments - OK
			$searchCategoryIds = DocumentCategory::where('document_category_name', 'like', '%' . $searchTerm . '%')->pluck('id')->toArray();
			$searchFindingTypeIds = FindingType::where('name', 'like', '%' . $searchTerm . '%')->pluck('id')->toArray();
			$searchFindingId = Finding::where('id', intval($searchTerm))->where('project_id', $project->id)->pluck('id')->toArray();
			$searchAuditId = Audit::where('id', intval($searchTerm))->pluck('id')->toArray();
			$searchBuildingId = Building::where('building_name', 'like', '%' . $searchTerm . '%')->pluck('id')->toArray();
			$searchUnitId = Unit::where('unit_name', 'like', '%' . $searchTerm . '%')->pluck('id')->toArray();
			$searchAmenitiesIds = Amenity::where('amenity_description', 'like', '%' . $searchTerm . '%')->pluck('id')->toArray();
			//dd($searchAuditId,$searchFindingId,$searchFindingTypeIds,$searchCategoryIds );
			$searchDocumentCommentId = Document::where('project_id', $project->id)->where('comment', 'like', '%' . $searchTerm . '%')->pluck('id')->toArray();

			$documents_query = $documents_query->get();
			$uploader_ids = $documents_query->pluck('user.person.id');
			// return $searchTerm;
			$searchUserId = People::whereIn('id', $uploader_ids)->with('user')->where(function ($query) use ($searchTerm) {
				$query->where('first_name', 'like', '%' . $searchTerm . '%');
				$query->orWhere('last_name', 'like', '%' . $searchTerm . '%');
			})->get()->pluck('user.id')->toArray();

			$documents_query = $documents_query->map(function ($doc) use ($searchCategoryIds, $searchFindingTypeIds, $searchFindingId, $searchAuditId, $searchUserId, $searchTerm, $searchDocumentCommentId, $searchBuildingId, $searchUnitId, $searchAmenitiesIds) {
				//document categories
				foreach ($doc->assigned_categories as $key => $cat) {
					if (in_array($cat->id, $searchCategoryIds) || in_array($cat->parent->id, $searchCategoryIds)) {
						return $doc;
					}
				}
				//Finding number or finding types or amenities
				foreach ($doc->findings as $key => $finding) {
					if (in_array($finding->id, $searchFindingId) || in_array($finding->finding_type->id, $searchFindingTypeIds) || in_array($finding->amenity->id, $searchAmenitiesIds)) {
						return $doc;
					}
				}
				// Buildings
				foreach ($doc->buildings as $key => $building) {
					if (in_array($building->id, $searchBuildingId)) {
						return $doc;
					}
				}
				//Unit
				foreach ($doc->units as $key => $unit) {
					if (in_array($unit->id, $searchUnitId)) {
						return $doc;
					}
				}
				//First and Last Names of Uploaders
				if (!empty($searchUserId) && in_array($doc->user_id, $searchUserId)) {
					return $doc;
				}
				//Audit Numbers
				foreach ($doc->audits as $key => $audit) {
					if (in_array($audit->id, $searchAuditId)) {
						return $doc;
					}
				}
				//Document comment
				if (in_array($doc->id, $searchDocumentCommentId)) {
					return $doc;
				}
			});

			$documents_ids = $documents_query->filter()->pluck('id');
			$documents_query = Document::whereIn('id', $documents_ids)->where('project_id', $project->id)->with('assigned_categories.parent', 'communications.communication', 'audits', 'audit', 'user', 'buildings', 'units', 'findings')->orderBy('created_at', 'DESC');
		}

		// return $documents_query->count();
		if ($unreviewed == 0) {
			// filter to show unreviewed
			$documents_query = $documents_query->where(function ($query) {
				$query->where('notapproved', 1);
				$query->orWhere('approved', 1);
			});
		}
		if ($reviewed == 0) {
			// filter to show reviewed
			$documents_query = $documents_query->whereNull('notapproved')->whereNull('approved');
		}

		if ($unresolved == 0) {
			// filter to show unresolved
			$documents_query = $documents_query->get();
			$documents_query = $documents_query->map(function ($doc) {
				$total = count($doc->findings);
				$criteria = $doc->findings->where('auditor_approved_resolution', 1);
				$meets_criteria_count = count($criteria);
				if ($total == $meets_criteria_count) {
					return $doc;
				}
			});
			$documents_ids = $documents_query->filter()->pluck('id');
			$documents_query = Document::whereIn('id', $documents_ids)->where('project_id', $project->id)->with('assigned_categories.parent', 'communications.communication', 'audits', 'audit', 'user', 'buildings', 'units', 'findings')->orderBy('created_at', 'DESC');
		}

		if ($resolved == 0) {
			$documents_query = $documents_query->get();
			// filter to show resolved
			// $documents_query = $documents_query->whereHas('findings', function ($query) use ($request) {
			// 	$query->where('auditor_approved_resolution', '<>', 1);
			// 	// $query->orWhereNull('auditor_approved_resolution');
			// });
			$documents_query = $documents_query->map(function ($doc) use ($unresolved) {
				$total = count($doc->findings);
				$criteria = $doc->findings->where('auditor_approved_resolution', '<>', 1);
				$meets_criteria_count = count($criteria);
				if ($total == $meets_criteria_count) {
					return $doc;
				}
				// foreach ($doc->findings as $key => $finding) {
				// 	if ($finding->auditor_approved_resolution != 1 || is_null($finding->auditor_approved_resolution)) {
				// 		if ($unresolved == 0) {
				// 			if ($finding->auditor_approved_resolution == 1) {
				// 				return $doc;
				// 			}
				// 		} else {
				// 			return $doc;
				// 		}
				// 	}
				// }
			});
			$documents_ids = $documents_query->filter()->pluck('id');
			$documents_query = Document::whereIn('id', $documents_ids)->where('project_id', $project->id)->with('assigned_categories.parent', 'communications.communication', 'audits', 'audit', 'user', 'buildings', 'units', 'findings')->orderBy('created_at', 'DESC');
		}
		// return $documents_query->pluck('id');

		$documents = $documents_query->paginate(20);
		$documents_all = $documents_query->get();
		$documents_count = $documents_query->count();
		// $new_audits = $documents_all->pluck('audits')->flatten()->unique('id');
		// $old_audits = $documents_all->pluck('audit')->flatten()->unique('id');
		// $audits = $new_audits->merge($old_audits)->filter()->unique('id'); //removes null records too
		$categories = $documents_all->pluck('assigned_categories')->flatten()->unique('id');
		// $findings = collect([]);
		// $all_finding_ids = [];
		// return $documents;

		return view('projects.partials.local-documents', compact('project', 'documents', 'audit_id', 'categories', 'searchTerm', 'findings', 'resolved', 'unresolved', 'reviewed', 'unreviewed'));
	}

	public function getPMProjectDocuments(Project $project, Request $request)
	{
		$audit_id = $request->audit_id;
		return view('projects.partials.pm-documents', compact('project', 'audit_id'));
	}

	public function getPMProjectLocalDocuments(Project $project, $audit_id = null, Request $request)
	{
		$current_user = $this->user;
		// ini_set('max_execution_time', 300);
		//check if filters exist
		// return $request->all();
		/// SEARCH TERM

		/// determine what findings/docs can be shown
		/// the issue here is if a document is attached to a finding but the report for that audit has not been
		/// released yet, we must not show those documents to the property mangement or owner until that audit's
		/// report has been released... so we set a system setting pm_can_see_findings_with_audit_step with the statuses allowed.

		$allowedSteps = SystemSetting::where('key', 'pm_can_see_findings_with_audit_step')->first();
		$allowedSteps = explode(',', $allowedSteps->value);

		$allowedDocumentsOnFindings = $project->audits()->whereIn('step_id', $pmCanViewFindingsStepIds)->pluck('audit_id')->toArray();

		dd($allowedAuditsForFindings, $allowedSteps, $project->audits);
		if ($request->has('local-search')) {
			if ($request->get('local-search') == "") {
				// reset the search
				$searchTerm = NULL;
				session(['local-documents-search-term' => NULL]);
			} else {
				$searchTerm = $request->get('local-search');
				session(['local-documents-search-term' => $request->get('local-search')]);
			}
		} else if (session('local-documents-search-term') != NULL) {
			$searchTerm = session('local-documents-search-term');
		} else {
			$searchTerm = NULL;
		}

		//SHOW HIDE RESOLVED / UNRESOLVED
		if ($request->has('local-unresolved')) {
			//dd($request->get('local-unresolved'));
			if ($request->get('local-unresolved') == 'on') {
				$unresolved = 1;
				session(['local-documents-unresolved' => 1]);
			} else {
				$unresolved = 0;
				session(['local-documents-unresolved' => 0]);
			}
		} else if (session('local-documents-unresolved') == 0) {
			$unresolved = session('local-documents-unresolved');
		} else {
			$unresolved = 1;
		}

		if ($request->has('local-resolved')) {
			if ($request->get('local-resolved') == 'on') {
				$resolved = 1;
				session(['local-documents-resolved' => 1]);
			} else {
				$resolved = 0;
				session(['local-documents-resolved' => 0]);
			}
		} else if (session('local-documents-resolved') == 0) {
			$resolved = session('local-documents-resolved');
		} else {
			$resolved = 1;
		}

		//SHOW HIDE REVIEWED / UNREVIEWED
		if ($request->has('local-unreviewed')) {
			//dd($request->get('local-unreviewed'));
			if ($request->get('local-unreviewed') == 'on') {
				$unreviewed = 1;
				session(['local-documents-unreviewed' => 1]);
			} else {
				$unreviewed = 0;
				session(['local-documents-unreviewed' => 0]);
			}
		} else if (session('local-documents-unreviewed') == 0) {
			$unreviewed = session('local-documents-unreviewed');
		} else {
			$unreviewed = 1;
		}

		if ($request->has('local-reviewed')) {
			if ($request->get('local-reviewed') == 'on') {
				$reviewed = 1;
				session(['local-documents-reviewed' => 1]);
			} else {
				$reviewed = 0;
				session(['local-documents-reviewed' => 0]);
			}
		} else if (session('local-documents-reviewed') == 0) {
			$reviewed = session('local-documents-reviewed');
		} else {
			$reviewed = 1;
		}

		$documents_all = [];
		///

		$findings = collect([]);
		$all_finding_ids = [];
		$allUnits = $project->units()->orderBy('unit_name')->get();

		// CLIENT REVERSED DECISION ---
		//Check if this PM is primary one
		// $project_report_access = ReportAccess::where('project_id', $project->id)->get();
		// $default_report_user = $project_report_access->where('default', 1)->first();
		$project = Project::with('contactRoles.person.user')->find($project->id); //DEVCO
		// $default_user = $project->contactRoles->where('project_role_key', 21)->first();
		// if ($default_report_user) {
		// 	$default_user_id = $default_report_user->user_id;
		// } elseif ($default_user && $default_user->person && $default_user->person->user) {
		// 	$default_user_id = $default_devco_user_id = $default_user->person->user->id;
		// }
		// if ($default_user_id == $current_user->id) {

		$documents_query = Document::where('project_id', $project->id)->with('assigned_categories.parent', 'finding', 'communications.communication', 'audits', 'audit', 'user')->orderBy('created_at', 'DESC');
		// } else {
		// 	$documents_query = Document::where('project_id', $project->id)->where('user_id', $current_user->id)->with('assigned_categories.parent', 'finding', 'communications.communication', 'audits', 'audit', 'user')->orderBy('created_at', 'DESC');
		// }

		$documents = $documents_query->paginate(20);
		$documents_all = $documents_query->get();
		$documents_count = $documents_query->count();
		$new_audits = $documents_all->pluck('audits')->flatten()->unique('id');
		$old_audits = $documents_all->pluck('audit')->flatten()->unique('id');
		$audits = $new_audits->merge($old_audits)->filter()->unique('id'); //removes null records too
		$categories = $documents_all->pluck('assigned_categories')->flatten()->unique('id');
		$findings = collect([]);
		$all_finding_ids = [];
		// return $documents;

		foreach ($documents as $key => $document) {
			$finding_ids = [];
			$doc_finding_ids = [];
			foreach ($document->communications as $key => $communication) {
				$finding_ids = $communication->communication ? $communication->communication->finding_ids : null;
				if (!is_null($finding_ids)) {
					$finding_ids = json_decode($finding_ids);
					$doc_finding_ids = array_merge($doc_finding_ids, $finding_ids);
					// $doc_findings = Finding::whereIn('id', $finding_ids)->get();
					// $doc_findings = Finding::whereIn('id', $finding_ids)->get();
					// $findings = $findings->merge($doc_findings);
				}
			}
			if (!empty($doc_finding_ids)) {
				$all_finding_ids = array_merge($all_finding_ids, $doc_finding_ids);
				$document->has_findings = 1;
				$document->finding_ids = $doc_finding_ids;
			} else {
				$document->has_findings = 0;
				$document->finding_ids = [];
			}
			// return $document;
		}

		$findings = Finding::with('audit_plain', 'building.address', 'unit.building.address', 'project.address', 'finding_type')->whereIn('id', $all_finding_ids)->get();
		$findings = $findings->unique('id');
		$findings_audits = $findings->pluck('audit_plain')->flatten()->unique('id');
		$audits = $audits->merge($findings_audits)->filter()->unique('id'); //removes null records too

		$filter = [];
		$filtered_documents = $documents;
		if ($request->filter_audit_id) {
			$filter['filter_audit_id'] = $request->filter_audit_id;
		}
		if ($request->filter_finding_id) {
			$filter['filter_finding_id'] = $request->filter_finding_id;
		}
		if ($request->filter_category_id) {
			$filter['filter_category_id'] = $request->filter_category_id;
		}

		// return $filter;
		// return $documents->first()->findings();
		$document_categories = DocumentCategory::with('parent')
			->where('parent_id', '<>', 0)
			->active()
			->orderBy('parent_id')
			->orderBy('document_category_name')
			->get();
		return view('projects.partials.pm-local-documents', compact('project', 'documents', 'document_categories', 'audit_id', 'audits', 'findings', 'categories', 'filter', 'documents_count', 'unreviewed', 'reviewed', 'resolved', 'unresolved', 'searchTerm'));
	}

	public function localUpload(Project $project, Request $request)
	{
		if (app('env') == 'local') {
			app('debugbar')->disable();
		}
		// return $request->all();
		if (!$request->has('categories') || is_null($request->categories)) {
			return 'You must select at least one category!';
		}
		if ($request->has('findings') && $request->findings != '') {
			/// we will ignore the selected audit and get it from the findings

			$findingIds = explode(',', $request->findings);
			//dd($findingsToInsert, $request->comment, $request->categories, $request->buValue, $request->audit_id);
			$findingDetails = Finding::whereIn('id', $findingIds)->get();
			// get unit ids from findings
			$unitIds = $findingDetails->pluck('unit_id')->unique()->filter()->toArray();
			// get the building ids of the units
			$unitBuildingIds = Unit::whereIn('id', $unitIds)->pluck('building_id')->unique()->filter();
			// get the building ids from the findings
			$findingBuildingIds = $findingDetails->pluck('building_id')->unique()->filter();
			// merge them togeter merging duplicates
			$buildingIds = $unitBuildingIds->merge($findingBuildingIds)->unique()->toArray();
			// get site ids from findings
			$siteIds = $findingDetails->where('site', 1)->pluck('amenity_id')->unique()->toArray();
			$unitIds = array_values($unitIds);

			$audit_ids = $findingDetails->pluck('audit_id')->unique()->toArray();
			$unitIds = array_map('strval', $unitIds);
			$findingIds = array_map('strval', $findingIds);
			$siteIds = array_map('strval', $siteIds);
			$buildingIds = array_map('strval', $buildingIds);
		}

		// return [$findingIds, $unitIds, $buildingIds, $siteIds];
		// dd(is_array($buildingIds));

		// return $request->findings;
		if ($request->hasFile('files')) {
			$data = [];
			$user = Auth::user();
			$files = $request->file('files');

			$audit_id = $request->audit_id;
			foreach ($files as $file) {
				// $file = $request->file('files')[0];
				$selected_audit = 'non-audit-files';
				$categories = DocumentCategory::with('parent')->find($request->categories);
				//document_category_name
				$parent_cat_folder = snake_case(strtolower($categories->parent->document_category_name));
				$cat_folder = snake_case(strtolower($categories->document_category_name));
				$folderpath = 'documents/project_' . $project->project_number . '/audit_' . $selected_audit . '/class_' . $parent_cat_folder . '/description_' . $cat_folder . '/';
				$characters = [' ', '´', '`', "'", '~', '"', '\'', '\\', '/'];
				$original_filename = str_replace($characters, '_', $file->getClientOriginalName());
				$file_extension = $file->getClientOriginalExtension();
				$filename = pathinfo($original_filename, PATHINFO_FILENAME);
				$document = new Document([
					'user_id' => $user->id,
					'project_id' => $project->id,
					'comment' => $request->comment,
				]);
				if ($request->has('findings') && $request->findings != '') {
					if (!empty($findingIds)) {
						$document->finding_ids = json_encode($findingIds);
					}
					if (!empty($siteIds)) {
						$document->site_ids = ($siteIds);
					}
					if (!empty($buildingIds)) {
						$document->building_ids = ($buildingIds);
					}
					if (!empty($unitIds)) {
						$document->unit_ids = ($unitIds);
					}
				}
				$document->save();
				// return $document;
				if (!is_null($audit_id) && $audit_id != 'reset') {
					$doc_audit = new DocumentAudit;
					$doc_audit->audit_id = $audit_id;
					$doc_audit->document_id = $document->id;
					$doc_audit->save();
				}
				foreach ($audit_ids as $key => $a_id) {
					if (!DocumentAudit::where('audit_id', $a_id)->where('document_id')->first()) {
						$doc_audit = new DocumentAudit;
						$doc_audit->audit_id = $a_id;
						$doc_audit->document_id = $document->id;
						$doc_audit->save();
					}
				}
				//Parent
				$document_categories = new LocalDocumentCategory;
				$document_categories->document_id = $document->id;
				$document_categories->document_category_id = $categories->parent->id;
				$document_categories->project_id = $project->id;
				$document_categories->save();
				//Category
				$document_categories = new LocalDocumentCategory;
				$document_categories->document_id = $document->id;
				$document_categories->document_category_id = $categories->id;
				$document_categories->project_id = $project->id;
				$document_categories->save();
				// Sanitize filename and append document id to make it unique
				$filename = snake_case(strtolower($filename)) . '_' . $document->id . '.' . $file_extension;
				$filepath = $folderpath . $filename;
				if ($request->has('ohfa_file')) {
					$document->update([
						'file_path' => $filepath,
						'filename' => $filename,
						'ohfa_file_path' => $filepath,
					]);
				} else {
					$document->update([
						'file_path' => $filepath,
						'filename' => $filename,
					]);
				}

				// store original file
				Storage::put($filepath, File::get($file));
				$data[] = $document->id;
				$data['document_ids'] = [$document->id];
			}
			return json_encode($data);
		} else {
			// shouldn't happen - UIKIT shouldn't send empty files
			// nothing to do here
		}
	}

	public function photoUpload(Project $project, Request $request)
	{
		if (app('env') == 'local') {
			app('debugbar')->disable();
		}
		if ($request->hasFile('files')) {
			$data = [];
			$user = Auth::user();
			$files = $request->file('files');

			foreach ($files as $file) {
				$selected_audit = $project->selected_audit();

				$folderpath = 'photos/project_' . $project->project_number . '/audit_' . $selected_audit->audit_id . '/';
				$characters = [' ', '´', '`', "'", '~', '"', '\'', '\\', '/'];
				$original_filename = str_replace($characters, '_', $file->getClientOriginalName());
				$file_extension = $file->getClientOriginalExtension();
				$filename = pathinfo($original_filename, PATHINFO_FILENAME);
				$photo = new Photo([
					'user_id' => $user->id,
					'project_id' => $project->id,
					'audit_id' => $selected_audit->id,
					'notes' => $request->comment,
					'finding_id' => $request->finding_id,
				]);
				$photo->save();

				// Sanitize filename and append document id to make it unique
				$filename = snake_case(strtolower($filename)) . '_' . $photo->id . '.' . $file_extension;
				$filepath = $folderpath . $filename;
				$photo->update([
					'file_path' => $filepath,
					'filename' => $filename,
				]);

				// store original file
				Storage::put($filepath, File::get($file));
				$data[] = [
					'id' => $photo->id,
					'filename' => $filename,
				];
			}
			return json_encode($data);
		} else {
			// shouldn't happen - UIKIT shouldn't send empty files
			// nothing to do here
		}
	}

	public function approveLocalDocument($project = null, Request $request)
	{
		if (!$request->get('id') && !$request->get('catid')) {
			return 'Something went wrong';
		}
		$catid = $request->get('catid');
		$document = Document::where('id', $request->get('id'))->first();
		// if already "notapproved", remove from notapproved
		if (!is_null($document->notapproved)) {
			$document->update([
				'notapproved' => null,
				'document_decliner_id' => Auth::user()->id,
			]);
		}
		// if not already approved, add to approved array
		if (is_null($document->approved)) {
			$document->update([
				'approved' => 1,
				'document_approver_id' => Auth::user()->id,
			]);
		}
		return 1;
	}

	public function notApproveLocalDocument($project = null, Request $request)
	{
		if (!$request->get('id') && !$request->get('catid')) {
			return 'Something went wrong';
		}
		$catid = $request->get('catid');
		$document = Document::where('id', $request->get('id'))->first();
		// if already approved, remove from approved array
		if (!is_null($document->approved)) {
			$document->update([
				'approved' => null,
				'document_approver_id' => Auth::user()->id,
			]);
		}
		// if not already notapproved  --confused yet? :), add to notapproved array
		if (is_null($document->notapproved)) {
			$document->update([
				'notapproved' => 1,
				'document_decliner_id' => Auth::user()->id,
			]);
		}
		return 1;
	}

	public function clearLocalReview($project = null, Request $request)
	{
		if (!$request->get('id') && !$request->get('catid')) {
			return 'Something went wrong';
		}
		$catid = $request->get('catid');
		$document = Document::where('id', $request->get('id'))->first();
		if (!is_null($document->approved)) {
			$document->update([
				'approved' => null,
				'document_approver_id' => Auth::user()->id,
			]);
		}
		if (!is_null($document->notapproved)) {
			$document->update([
				'notapproved' => null,
				'document_decliner_id' => Auth::user()->id,
			]);
		}
		return 1;
	}

	public function editLocalDocument($id, Request $request)
	{
		$document = Document::with('assigned_categories.parent')->find($id);
		$project = Project::find($document->project_id);
		$document_categories = DocumentCategory::where('parent_id', '<>', 0)->where('active', '1')->orderby('document_category_name', 'asc')->get();
		$categories_used = $document->assigned_categories->first();
		return view('modals.edit-document', compact('document', 'document_categories', 'categories_used', 'project'));
	}

	public function saveEditedLocalDocument($document_id, Request $request)
	{
		$document = Document::with('assigned_categories.parent')->find($document_id);
		$project = Project::where('id', '=', $document->project_id)->first();
		$forminputs = $request->get('inputs');
		parse_str($forminputs, $forminputs);
		if (isset($forminputs['comments'])) {
			$comments = $forminputs['comments'];
		} else {
			$comments = '';
		}
		$document->update([
			"comment" => $comments,
			'last_edited' => Carbon::now(),
		]);
		$user = Auth::user();
		$categories = $request->get('cats')[0];
		$categories = DocumentCategory::with('parent')->find($categories);
		if ($categories->id != $document->assigned_categories->first()->id) {
			$assigned_categories = LocalDocumentCategory::where('document_id', $document->id)->delete();
			$document_categories = new LocalDocumentCategory;
			$document_categories->document_id = $document->id;
			$document_categories->document_category_id = $categories->parent->id;
			$document_categories->project_id = $project->id;
			$document_categories->save();
			//Category
			$document_categories = new LocalDocumentCategory;
			$document_categories->document_id = $document->id;
			$document_categories->document_category_id = $categories->id;
			$document_categories->project_id = $project->id;
			$document_categories->save();
		}
		return 1;
	}

	public function deleteLocalDocument(Project $project, Request $request)
	{
		$document = Document::find($request->id);
		// remove files
		Storage::delete($document->file_path);
		// remove categoried of the document
		LocalDocumentCategory::where('document_id', $document->id)->delete();
		// remove database record
		$document->delete();
		return 1;
	}

	public function downloadLocalDocument(Document $document)
	{
		$filepath = $document->file_path;
		// Get the audit, projects, communication owner and communication receipients of this document
		$current_user = $this->user->load('roles', 'person.projects', 'report_access', 'communications_receipient', 'communications_owner');
		$user_details['project_ids'] = [];
		$user_details['communication_ids'] = [];
		if ($current_user->person && $current_user->person->projects) {
			$user_details['project_ids'] = $current_user->person->projects->pluck('id')->toArray();
		}
		if ($current_user->communications_receipient || $current_user->communications_owner) {
			if ($current_user->communications_receipient) {
				$user_details['communication_ids'] = array_merge($user_details['communication_ids'], $current_user->communications_receipient->pluck('id')->toArray());
			}
			if ($current_user->communications_owner) {
				$user_details['communication_ids'] = array_merge($user_details['communication_ids'], $current_user->communications_owner->pluck('id')->toArray());
			}
		}
		// Get the audit, project, communications of this user
		$current_document = $document->load('communications', 'audits.project', 'user', 'project', 'communication_details.owner', 'communication_details.recipients');
		$document_details['project_ids'] = [];
		$document_details['communication_ids'] = [];
		if ($current_document->audits) {
			$document_projects = $current_document->audits->pluck('project');
			if ($document_projects) {
				$document_details['project_ids'] = $document_projects->pluck('id')->toArray();
			}
		}
		if ($current_document->communication_details) {
			$document_details['communication_ids'] = $current_document->communication_details->pluck('id')->toArray();
		}
		// Check if there is any common item. If yes, allow download else don't
		$projects_match = array_intersect($document_details['project_ids'], $user_details['project_ids']);
		$communications_match = array_intersect($document_details['communication_ids'], $user_details['communication_ids']);

		if (empty($projects_match) && empty($communications_match)) {
			if (Auth::user()->cannot('access_auditor')) {
				exit('You have no access to the requested file!  ' . $document->id);
			}
		}

		if (Storage::exists($filepath)) {
			$file = Storage::get($filepath);
			ob_end_clean();
			return response()->download(storage_path('app/' . $filepath));

			// header('Content-Description: File Transfer');
			// header('Content-Type: application/octet-stream');
			// header('Content-Disposition: attachment; filename=' . $document->filename);
			// header('Content-Transfer-Encoding: binary');
			// header('Expires: 0');
			// header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			// header('Pragma: public');
			// header('Content-Length: ' . Storage::size($filepath));
			// return $file;
		} else {
			exit('Requested file does not exist on our server! ' . $filepath);
		}
	}

	public function getProjectDocuwareDocuments(Project $project, Request $request)
	{
		if (env('APP_DEBUG_NO_DEVCO')) {
			return "<div uk-grid><div class='uk-width-1-1'><hr><h2>SORRY!</h2><p>Docuware is only accessible on an approved server and by approved users. Please contact your admin for help.</p></div></div>";
		} else {
			$documents = $this->projectDocuwareDocumets($project);
			$document_categories = DocumentCategory::where('parent_id', '<>', 0)->orderBy('parent_id')->orderBy('document_category_name')->get()->all();

			return view('projects.partials.docuware-documents', compact('project', 'documents', 'document_categories'));
		}
	}

	public function showTabFromParcelId(Parcel $parcel, Request $request)
	{

		// get documents
		$documents = Document::where('parcel_id', $parcel->id)
			->orderBy('created_at', 'desc')
			->get();

		// get categories
		// Testing only - TBD: preselect using rules
		$document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();

		// build a list of all categories used for uploaded documents in this parcel
		$categories_used = [];
		// category keys for name reference ['id' => 'name']
		$document_categories_key = [];
		foreach ($document_categories as $document_category) {
			$document_categories_key[$document_category->id] = $document_category->document_category_name;
		}

		if (count($documents)) {
			// create an associative array to simplify category references for each document
			foreach ($documents as $document) {
				$categories = []; // store the new associative array cat id, cat name

				if ($document->categories) {
					$categories_decoded = json_decode($document->categories, true); // cats used by the doc

					$categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
				} else {
					$categories_decoded = [];
				}

				foreach ($document_categories as $document_category) {
					// sub key for each document's categories for quick reference
					if (in_array($document_category->id, $categories_decoded)) {
						$categories[$document_category->id] = $document_category->document_category_name;
					}
				}
				$document->categoriesarray = $categories;

				// get approved category id in an array
				if ($document->approved) {
					$document->approved_array = json_decode($document->approved, true);
				} else {
					$document->approved_array = [];
				}

				// get notapproved category id in an array
				if ($document->notapproved) {
					$document->notapproved_array = json_decode($document->notapproved, true);
				} else {
					$document->notapproved_array = [];
				}
			}
		} else {
			$documents = [];
		}

		// 1) Rules give us what documents are required
		// 2) Cost items with expense category id 7 with amount > 1000 requires category #8
		// 3) Combine (1) and (2), this is the categories_needed array of ids
		// 4) Check existing documents, get their ids
		// 5) Get the difference in arrays, this is the pending cats.

		// get required categories based on parcel rules $this->getDocumentCategory(parcelid)
		$categories_needed = $this->getDocumentCategory($parcel->id);

		// categories currently used (documents that are in the database now)
		$categories_used = array_unique($categories_used);

		// based on costs added by the LB user, we need to upload certain categories
		$cost_items_expense_cats = CostItem::where('parcel_id', '=', $parcel->id)
			->where('expense_category_id', '=', 7) // only admin > 1000 requires upload
			->where('amount', '>', 1000)
			->count();
		if ($cost_items_expense_cats) {
			$categories_needed[] = 8; //Reimbursement Support Document
		}

		// list all remaining categories in the pending row
		$pending_categories = array_diff($categories_needed, $categories_used);
		//       $pending_categories = $categories_used;

		if (count($pending_categories)) {
			$pending_categories_list = implode(",", $pending_categories);
		} else {
			$pending_categories_list = '';
		}

		return view('parcels.parcel_documents', compact('parcel', 'documents', 'document_categories', 'pending_categories', 'pending_categories_list', 'document_categories_key'));
	}

	public function editDocument(Document $document)
	{
		$document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();
		$categories_used = [];

		if ($document->categories) {
			$categories_used = json_decode($document->categories, true); // cats used by the doc
		}

		return view('modals.edit-document', compact('document', 'document_categories', 'categories_used'));
	}

	public function saveEditedDocument(Document $document, Request $request)
	{
		$parcel = Parcel::where('id', '=', $document->parcel_id)->first();

		$forminputs = $request->get('inputs');
		parse_str($forminputs, $forminputs);

		if (isset($forminputs['comments'])) {
			$comments = $forminputs['comments'];
		} else {
			$comments = '';
		}

		$user = Auth::user();

		$categories = $request->get('cats');

		if (is_array($categories)) {
			if (count($categories)) {
				$categories_json = json_encode($categories, true);

				if (in_array(47, $categories)) {
					$is_advance = 1;
				} else {
					$is_advance = 0;
				}
				if (in_array(9, $categories)) {
					$is_retainage = 1;
				} else {
					$is_retainage = 0;
				}

				// update document
				$document->update([
					"comment" => $comments,
					"categories" => $categories_json,
				]);

				if ($is_retainage) {
					// if only one retainage in database, then no need to display the modal with the select form
					if ($parcel->retainages) {
						if (count($parcel->retainages) == 1) {
							// assign to retainage
							$retainage = $parcel->retainages->first();
							$check = $retainage->documents()->where('document_id', $document->id)->first();
							if (count($check) < 1) {
								$retainage->documents()->attach($document->id);
							}
						} elseif (count($parcel->retainages) == 0) {
							$is_retainage = 0;
						}
					}
				}
				if ($is_advance) {
					// if only one advance in database, then no need to display the modal with the select form
					if ($parcel->costItemsWithAdvance) {
						if (count($parcel->costItemsWithAdvance) == 1) {
							// assign to cost item
							$advance = $parcel->costItemsWithAdvance->first();
							$advance->documents()->attach($document->id);
						} elseif (count($parcel->costItemsWithAdvance) == 0) {
							$is_advance = 0;
						}
					}
				}

				return 1;
			} else {
				// no category selected
				return 'You must select at least one category!';
			}
		} else {
			return 'You must select at least one category!';
		}

		perform_all_parcel_checks($parcel);
		guide_next_pending_step(2, $parcel->id);
	}

	/**
	 * Upload documents to parcel.
	 *
	 * @param  int  $parcel_id
	 * @return Response
	 */
	public function upload(Parcel $parcel, Request $request)
	{
		if (app('env') == 'local') {
			app('debugbar')->disable();
		}

		if ($request->hasFile('files')) {
			$files = $request->file('files');
			$file_count = count($files);
			$uploadcount = 0; // counter to keep track of uploaded files
			$document_ids = '';

			$categories = explode(",", $request->get('categories'));
			$categories_json = json_encode($categories, true);

			$user = Auth::user();

			if (in_array(47, $categories)) {
				$is_advance = 1;
			} else {
				$is_advance = 0;
			}
			if (in_array(9, $categories)) {
				$is_retainage = 1;
			} else {
				$is_retainage = 0;
			}

			foreach ($files as $file) {
				// Create filepath
				$folderpath = 'documents/entity_' . $parcel->entity_id . '/program_' . $parcel->program_id . '/parcel_' . $parcel->id . '/';

				// sanitize filename
				$characters = [' ', '´', '`', "'", '~', '"', '\'', '\\', '/'];
				$original_filename = str_replace($characters, '_', $file->getClientOriginalName());

				// Create a record in documents table
				$document = new Document([
					'user_id' => $user->id,
					'parcel_id' => $parcel->id,
					'categories' => $categories_json,
					'filename' => $original_filename,
				]);

				$document->save();

				// Save document ids in an array to return
				if ($document_ids != '') {
					$document_ids = $document_ids . ',' . $document->id;
				} else {
					$document_ids = $document->id;
				}

				// Sanitize filename and append document id to make it unique
				// documents/entity_0/program_0/parcel_0/0_filename.ext
				$filename = $document->id . '_' . $original_filename;
				$filepath = $folderpath . $filename;

				$document->update([
					'file_path' => $filepath,
				]);

				// store original file
				Storage::put($filepath, File::get($file));

				if ($is_retainage) {
					// if only one retainage in database, then no need to display the modal with the select form
					if ($parcel->retainages) {
						if (count($parcel->retainages) == 1) {
							// assign to retainage
							$retainage = $parcel->retainages->first();
							$retainage->documents()->attach($document->id);
						} elseif (count($parcel->retainages) == 0) {
							$is_retainage = 0;
						}
					}
				}
				if ($is_advance) {
					// if only one advance in database, then no need to display the modal with the select form
					if ($parcel->costItemsWithAdvance) {
						if (count($parcel->costItemsWithAdvance) == 1) {
							// assign to cost item
							$advance = $parcel->costItemsWithAdvance->first();
							$advance->documents()->attach($document->id);
						} elseif (count($parcel->costItemsWithAdvance) == 0) {
							$is_advance = 0;
						}
					}
				}

				$uploadcount++;
			}
			if ($is_retainage) {
				if ($parcel->retainages) {
					if (count($parcel->retainages) == 1) {
						$is_retainage = 0;
					}
				}
			}
			if ($is_advance) {
				if ($parcel->costItemsWithAdvance) {
					if (count($parcel->costItemsWithAdvance) == 1) {
						$is_advance = 0;
					}
				}
			}

			if ($uploadcount != $file_count) {
				// something went wrong
			}

			perform_all_parcel_checks($parcel);
			guide_next_pending_step(2, $parcel->id);

			$data = [];
			$data['document_ids'] = $document_ids;
			$data['is_retainage'] = $is_retainage;
			$data['is_advance'] = $is_advance;

			return json_encode($data);
		} else {
			// shouldn't happen - UIKIT shouldn't send empty files
			// nothing to do here
		}
	}

	/**
	 * Add comments to the documents uploaded.
	 *
	 * @param  int  $parcel_id
	 * @return Response
	 */
	public function uploadComment(Parcel $parcel, Request $request)
	{
		if (!$request->get('postvars')) {
			return 'Something went wrong...';
		}

		// get document ids
		$documentids = explode(",", $request->get('postvars'));

		// get comment
		$comment = $request->get('comment');

		if (is_array($documentids) && count($documentids)) {
			foreach ($documentids as $documentid) {
				$document = Document::find($documentid);
				$document->update([
					'comment' => $comment,
				]);
			}
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * Get document attributes from ids and return JSON.
	 *
	 * @param  int  $parcel_id
	 * @return Response
	 */
	public function documentInfo(Project $project, Request $request)
	{
		if (!$request->get('postvars')) {
			return 'Something went wrong';
		}
		// get document ids
		//$documentids = explode(",", $request->get('postvars'));
		$documentids = $request->get('postvars');
		$documents = Document::whereIn('id', $documentids)
			->with('assigned_categories.parent')
			->orderBy('created_at', 'desc')
			->get();
		$document_info_array = [];
		foreach ($documents as $document) {
			$document_info_array[$document->id]['filename'] = $document->filename;
			foreach ($document->assigned_categories as $category) {
				$document_info_array[$document->id]['categories']['category_name'] = $category->document_category_name;
				$document_info_array[$document->id]['categories']['category_parent_name'] = $category->parent->document_category_name;
			}
		}
		return $document_info_array;
	}

	public function localUploadDraft(Project $project, Request $request)
	{
		if (app('env') == 'local') {
			app('debugbar')->disable();
		}
		$communication_draft = CommunicationDraft::find($request->draft_id);
		if (!$communication_draft) {
			return 'No associated communication draft was found, try again by closing communication modal or contact admin.';
		}
		$document_draft_info = [];
		// return $request->all();
		if (!$request->has('categories') || is_null($request->categories)) {
			return 'You must select at least one category!';
		}
		if ($request->has('findings') && $request->findings != '') {
			/// we will ignore the selected audit and get it from the findings
			$findingIds = explode(',', $request->findings);
			//dd($findingsToInsert, $request->comment, $request->categories, $request->buValue, $request->audit_id);
			$findingDetails = Finding::whereIn('id', $findingIds)->get();
			// get unit ids from findings
			$unitIds = $findingDetails->pluck('unit_id')->unique()->filter()->toArray();
			// get the building ids of the units
			$unitBuildingIds = Unit::whereIn('id', $unitIds)->pluck('building_id')->unique()->filter();
			// get the building ids from the findings
			$findingBuildingIds = $findingDetails->pluck('building_id')->unique()->filter();
			// merge them togeter merging duplicates
			$buildingIds = $unitBuildingIds->merge($findingBuildingIds)->unique()->toArray();
			// get site ids from findings
			$siteIds = $findingDetails->where('site', 1)->pluck('amenity_id')->unique()->toArray();
			$unitIds = array_values($unitIds);
			// dd($findingIds, $unitIds, $buildingIds, $siteIds);
			$unitIds = array_map('strval', $unitIds);
			$siteIds = array_map('strval', $siteIds);
			$buildingIds = array_map('strval', $buildingIds);
		}
		// return $findingIds;
		$audit_id = $request->audit_id;
		if ($request->hasFile('files')) {
			$data = [];
			$user = Auth::user();
			$files = $request->file('files');
			foreach ($files as $file) {
				// $file = $request->file('files')[0];
				$selected_audit = 'non-audit-files';
				$categories = DocumentCategory::with('parent')->find($request->categories);
				//document_category_name
				$parent_cat_folder = snake_case(strtolower($categories->parent->document_category_name));
				$cat_folder = snake_case(strtolower($categories->document_category_name));
				$folderpath = 'documents/project_' . $project->project_number . '/audit_' . $selected_audit . '/class_' . $parent_cat_folder . '/description_' . $cat_folder . '/';
				$characters = [' ', '´', '`', "'", '~', '"', '\'', '\\', '/'];
				$original_filename = str_replace($characters, '_', $file->getClientOriginalName());
				$file_extension = $file->getClientOriginalExtension();
				$filename = pathinfo($original_filename, PATHINFO_FILENAME);
				// Log::info($filename);
				$document = new Document([
					'user_id' => $user->id,
					'project_id' => $project->id,
					'comment' => $request->comment,
				]);
				if ($request->has('findings') && $request->findings != '') {
					if (!empty($findingIds)) {
						$document->finding_ids = json_encode($findingIds, true);
					}
					if (!empty($siteIds)) {
						$document->site_ids = ($siteIds);
					}
					if (!empty($buildingIds)) {
						$document->building_ids = ($buildingIds);
					}
					if (!empty($unitIds)) {
						$document->unit_ids = ($unitIds);
					}
				}
				$document->save();
				if (!is_null($audit_id)) {
					$doc_audit = new DocumentAudit;
					$doc_audit->audit_id = $audit_id;
					$doc_audit->document_id = $document->id;
					$doc_audit->save();
				}
				//Parent
				$document_categories = new LocalDocumentCategory;
				$document_categories->document_id = $document->id;
				$document_categories->document_category_id = $categories->parent->id;
				$document_categories->project_id = $project->id;
				$document_categories->save();
				//Category
				$document_categories = new LocalDocumentCategory;
				$document_categories->document_id = $document->id;
				$document_categories->document_category_id = $categories->id;
				$document_categories->project_id = $project->id;
				$document_categories->save();
				// Sanitize filename and append document id to make it unique
				$filename = snake_case(strtolower($filename)) . '_' . $document->id . '.' . $file_extension;
				$filepath = $folderpath . $filename;
				if ($request->has('ohfa_file')) {
					$document->update([
						'file_path' => $filepath,
						'filename' => $filename,
						'ohfa_file_path' => $filepath,
					]);
				} else {
					$document->update([
						'file_path' => $filepath,
						'filename' => $filename,
					]);
				}

				// store original file
				Storage::put($filepath, File::get($file));
				$data[] = $document->id;
				$data['document_ids'][] = [$document->id];
			}
			if (!is_null($communication_draft->documents)) {
				$docs = json_decode($communication_draft->documents, true);
				$docs = array_merge([$data], $docs);
				$communication_draft->documents = json_encode($docs);
			} else {
				$communication_draft->documents = json_encode([$data]);
			}
			$communication_draft->save();
			return json_encode($data);
		} else {
			// shouldn't happen - UIKIT shouldn't send empty files
			// nothing to do here
		}
	}

	/**
	 * Delete document.
	 *
	 * @param  int  $parcel_id
	 * @return Response
	 */
	public function deleteDocument(Parcel $parcel, Request $request)
	{
		$document_id = $request->get('id');
		$document = Document::find($document_id);

		// remove files
		Storage::delete($document->file_path);

		// remove database record
		$document->delete();

		perform_all_parcel_checks($parcel);
		guide_next_pending_step(2, $parcel->id);

		return 1;
	}

	/**
	 * Download document.
	 *
	 * @param  int  $parcel_id
	 * @return Response
	 */
	public function downloadDocument(Parcel $parcel, Document $document)
	{
		$filepath = $document->file_path;

		if (Storage::exists($filepath)) {
			$file = Storage::get($filepath);

			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $document->filename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . Storage::size($filepath));

			return $file;
		} else {
			// Error
			exit('Requested file does not exist on our server! ' . $filepath);
		}
	}

	/**
	 * Approve document.
	 *
	 * @param  int  $parcel_id
	 * @return Response
	 */
	public function approveDocument(Parcel $parcel, Request $request)
	{
		if (!$request->get('id') && !$request->get('catid')) {
			return 'Something went wrong';
		}

		$catid = $request->get('catid');
		$document = Document::where('id', $request->get('id'))->first();

		// get current approval array (category ids that had their document approved)
		if ($document->approved) {
			$current_approval_array = json_decode($document->approved, true);
		} else {
			$current_approval_array = [];
		}

		// get current 'notapproval' array (category ids that had their document approved)
		if ($document->notapproved) {
			$current_notapproval_array = json_decode($document->notapproved, true);
		} else {
			$current_notapproval_array = [];
		}

		// if already "notapproved", remove from notapproved array
		if (in_array($catid, $current_notapproval_array)) {
			unset($current_notapproval_array[array_search($catid, $current_notapproval_array)]);
			$current_notapproval_array = array_values($current_notapproval_array);
			$notapproval = json_encode($current_notapproval_array);

			$document->update([
				'notapproved' => $notapproval,
			]);
			//TODO: Add event logger
		}

		// if not already approved, add to approved array
		if (!in_array($catid, $current_approval_array)) {
			$current_approval_array[] = $catid;
			$approval = json_encode($current_approval_array);

			$document->update([
				'approved' => $approval,
			]);
			//TODO: Add event logger
		}

		perform_all_parcel_checks($parcel);
		guide_next_pending_step(2, $parcel->id);

		return 1;
	}

	/**
	 * "Not Approve" document.
	 *
	 * @param  int  $parcel_id
	 * @return Response
	 */
	public function notApproveDocument(Parcel $parcel, Request $request)
	{
		if (!$request->get('id') && !$request->get('catid')) {
			return 'Something went wrong';
		}

		$catid = $request->get('catid');
		$document = Document::where('id', $request->get('id'))->first();

		if ($document->approved) {
			$current_approval_array = json_decode($document->approved, true);
		} else {
			$current_approval_array = [];
		}

		// get current 'notapproval' array (category ids that had their document approved)
		if ($document->notapproved) {
			$current_notapproval_array = json_decode($document->notapproved, true);
		} else {
			$current_notapproval_array = [];
		}

		// if already approved, remove from approved array
		if (in_array($catid, $current_approval_array)) {
			unset($current_approval_array[array_search($catid, $current_approval_array)]);
			$current_approval_array = array_values($current_approval_array);
			$approval = json_encode($current_approval_array);

			$document->update([
				'approved' => $approval,
			]);
			//TODO: Add event logger
		}

		// if not already notapproved  --confused yet? :), add to notapproved array
		if (!in_array($catid, $current_notapproval_array)) {
			$current_notapproval_array[] = $catid;
			$notapproval = json_encode($current_notapproval_array);

			$document->update([
				'notapproved' => $notapproval,
			]);
			//TODO: Add event logger
		}

		perform_all_parcel_checks($parcel);
		guide_next_pending_step(2, $parcel->id);

		return 1;
	}

	/**
	 * Retrieve document categories needed based on parcel rules.
	 *
	 * @param  int  $parcel_id
	 * @return Response
	 */
	public function getDocumentCategory($id)
	{
		$rid = Parcel::where('id', $id)->pluck('program_rules_id');
		$docRules = DocumentRule::where('program_rules_id', $rid)->pluck('id');
		$docCatIds = DocumentRuleEntry::whereIn('document_rule_id', $docRules)->pluck('document_category_id');
		if (is_array($docCatIds)) {
			return $docCatIds;
		} else {
			return [];
		}
	}

	public function retainageForm(Parcel $parcel, $documentids)
	{
		return view('modals.document-retainage-form', compact('parcel', 'documentids'));
	}

	public function retainageFormSave(Parcel $parcel, Request $request)
	{
		$retainage_ids = $request->get('retainages');
		$document_ids = $request->get('documentids');

		foreach ($retainage_ids as $retainage_id) {
			// check if retainage id belongs to parcel
			$retainage = Retainage::where('id', '=', $retainage_id)->where('parcel_id', '=', $parcel->id)->first();

			// attach document
			if (count($retainage)) {
				foreach ($document_ids as $document_id) {
					$document = Document::where('parcel_id', '=', $parcel->id)->where('id', '=', $document_id)->first();
					if (count($document)) {
						$retainage->documents()->attach($document->id);
					}
				}
			}
		}
		return 1;
	}

	public function advanceForm(Parcel $parcel, $documentids)
	{
		return view('modals.document-advance-form', compact('parcel', 'documentids'));
	}

	public function advanceFormSave(Parcel $parcel, Request $request)
	{
		$advance_ids = $request->get('advances');
		$document_ids = $request->get('documentids');

		foreach ($advance_ids as $advance_id) {
			// check if advance id belongs to parcel
			$advance = CostItem::where('id', '=', $advance_id)->where('parcel_id', '=', $parcel->id)->first();

			// attach document
			if (count($advance)) {
				foreach ($document_ids as $document_id) {
					$document = Document::where('parcel_id', '=', $parcel->id)->where('id', '=', $document_id)->first();
					if (count($document)) {
						$advance->documents()->attach($document->id);
					}
				}
			}
		}
		return 1;
	}
}
