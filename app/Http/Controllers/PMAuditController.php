<?php

namespace App\Http\Controllers;

use Auth;
use View;
use Carbon;
use Session;
use App\Models\Unit;
use App\Models\User;
use App\Models\Audit;
use App\Models\Group;
use App\Models\Finding;
use App\Models\Program;
use App\Models\Project;
use App\Models\Building;
use App\Models\CrrReport;
use App\Models\UserEmail;
use App\Models\CachedAudit;
use App\Models\UnitProgram;
use Illuminate\Support\Arr;
use App\Models\AuditAuditor;
use App\Models\Availability;
use App\Models\ReportAccess;
use App\Models\ScheduleTime;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\UserAddresses;
use App\Models\CachedBuilding;
use App\Models\UnitInspection;
use App\Models\CrrApprovalType;
use App\Models\UserPhoneNumber;
use App\Models\UserOrganization;
use App\Models\AmenityInspection;
use App\Http\Controllers\Controller;

class PMAuditController extends Controller
{
	private $htc_group_id;
	
	
	public function __construct()
	{
		$this->allitapc();
		$this->htc_group_id = 7;
		View::share('htc_group_id', $this->htc_group_id);
		ini_set('memory_limit', '8G');
	}

	public function getPMProject($id = null, $audit_id = 0)
	{
		// PROJECT MANAGER SPECIFIC
		$project = Project::where('project_key', '=', $id)->first();
		if ($project) {
			$projectId = $project->id;

			// the project tab has a audit selection to display previous audit's stats, compliance info and assignments.

			$projectTabs = collect([
				['title' => 'Audits', 'icon' => 'a-mobile-home', 'status' => '', 'badge' => '', 'action' => 'pm-project.details-with-audit'],
				// ['title' => 'Communications', 'icon' => 'a-envelope-incoming', 'status' => '', 'badge' => '', 'action' => 'project.audit-communications'],
				['title' => 'Documents', 'icon' => 'a-file-clock', 'status' => '', 'badge' => '', 'action' => 'pm-project.documents'],
				// ['title' => 'Notes', 'icon' => 'a-file-text', 'status' => '', 'badge' => '', 'action' => 'project.notes'],
				// ['title' => 'Comments', 'icon' => 'a-comment-text', 'status' => '', 'badge' => '', 'action' => 'project.comments'],
				// ['title' => 'Photos', 'icon' => 'a-picture', 'status' => '', 'badge' => '', 'action' => 'project.photos'],
				// ['title' => 'Findings', 'icon' => 'a-mobile-info', 'status' => '', 'badge' => '', 'action' => 'project.findings'],
				// ['title' => 'Follow-ups', 'icon' => 'a-bell-ring', 'status' => '', 'badge' => '', 'action' => 'project.followups'],
				// ['title' => 'Findings', 'icon' => 'a-mobile-info', 'status' => '', 'badge' => '', 'action' => 'project.stream'],
				// ['title' => 'Reports', 'icon' => 'a-file-chart-3', 'status' => '', 'badge' => '', 'action' => 'project.reports'],
				//['title' => 'Contacts', 'icon' => 'a-person-notebook icon', 'status' => '', 'badge' => '', 'action' => 'project.contacts'],
			]);
			$tab = 'project-detail-tab-1';

			return view('projects.pm-project', compact('tab', 'projectTabs', 'projectId', 'audit_id', 'project'));
		} else {
			$error = "I was not able to find the project from the link you clicked. Please let the Allita Support Team know what link you clicked on to arrive at this error.";
			abort(403, $error);
		}
	}

	public function getPMProjectDetails($id = null, $audit_id = 0)
	{
		$selected_audit = null;
		// the project tab has a audit selection to display previous audit's stats, compliance info and assignments.
		try {
			$project = Project::where('id', '=', $id)->first();
			//return Session::get('project.'.$id.'.selectedaudit');
			// if($audit_id) {
			//   $selected_audit = CachedAudit::where('audit_id', '=', $audit_id)->first();
			// } else {
			//  $selected_audit = $project->selected_audit();
			// }
			$selected_audit = $project->selected_audit($audit_id, 0);
			//dd($id, $project, $selected_audit);
			// get that audit's stats and contact info from the project_details table
			$details = $project->details();
			// get the list of all audits for this project
			$audits = $project->audits;
			//dd($selected_audit->checkStatus('schedules'));
			// get auditors from user roles
			// $auditors = User::whereHas('roles', function ($query) {
			// 	$query->where('role_id', '=', 2);
			// 	$query->orWhere('role_id', '=', 3);
			// })
			// 	->where('active', '=', 1)
			// 	->orderBy('name', 'asc')
			// 	->get();
			$auditors = [];
			return view('projects.partials.pm-details', compact('details', 'audits', 'project', 'selected_audit', 'auditors'));
		} catch (\Exception $e) {
			app('sentry')->captureException($e);
			if (!$selected_audit) {
				$error = 'Audit not found';
				$message = 'Looks like you are trying to access an audit that is not available.';
				$code = 404;
				return view('errors.message', compact('error', 'message', 'code'));
			}
		}
	}

	public function getPMProjectTitle($id = null)
	{
		//PROJECT MANAGER SPECIFIC
		$project_number = Project::where('project_key', '=', $id)->first()->project_number;

		$audit = CachedAudit::where('project_key', '=', $id)->orderBy('id', 'desc')->first();

		// TBD add step to title
		$step = $audit->step_status_text; //  :: CREATED DYNAMICALLY FROM CONTROLLER
		$step_icon = $audit->step_status_icon;

		return '<i class="a-mobile-repeat"></i><i class="' . $step_icon . '"></i> <span class="list-tab-text"> PROJECT ' . $project_number . '</span>';
	}

	public function getPMProjectDetailsAjax(Request $request)
	{
		$id = $request->id;
		$audit_id = $request->audit_id;
		$cached_audit = CachedAudit::whereAuditId($audit_id)->first();
		$project = Project::with('contactRoles.person.user')->find($id);
		$project_default_user = $project->contactRoles->where('project_role_key', 21)->first();
		$details = $project->details($audit_id);
		$details_new = $details->replicate();
		$pm = $project->pm();
		//Check if the project has default
		$default_user = ReportAccess::with('user')->where('project_id', $id)->where('default', 1)->first();
		if ($default_user) {
			//&& $default_user->user->name != $details_new->manager_poc
			$details_new->manager_poc = $default_user->user->name;
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person && $project_default_user->person->user) {
			$details_new->manager_poc = $project_default_user->person->user->name;
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person) {
			$details_new->manager_poc = $project_default_user->person->first_name . ' ' . $project_default_user->person->last_name;
			$details_new->save();
		}

		$default_address = UserAddresses::with('user', 'address')->where('project_id', $id)->where('default', 1)->first();
		if ($default_address) {
			// && $default_address->address->line_1 != $details_new->manager_address
			$details_new->manager_address = $default_address->address->line_1;
			$details_new->manager_address2 = $default_address->address->line_2;
			$details_new->manager_city = $default_address->address->city;
			$details_new->manager_state = $default_address->address->state;
			$details_new->manager_zip = $default_address->address->zip;
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person && $project_default_user->person->user && !is_null($project_default_user->person->user->organization_id) && $project_default_user->person->user->organization_details) {
			$default_address = $project_default_user->person->user->organization_details;
			$details_new->manager_address = $default_address->address->line_1;
			$details_new->manager_address2 = $default_address->address->line_2;
			$details_new->manager_city = $default_address->address->city;
			$details_new->manager_state = $default_address->address->state;
			$details_new->manager_zip = $default_address->address->zip;
			$details_new->save();
		}
		// Cached audit pm update
		// if ($cached_audit) {
		// 	$cached_audit->pm = $details_new->manager_poc;
		// 	$cached_audit->address = $details_new->manager_address;
		// 	$cached_audit->state = $details_new->manager_state;
		// 	$cached_audit->zip = $details_new->manager_zip;
		// 	$cached_audit->city = $details_new->manager_city;
		// 	$cached_audit->save();
		// }

		$default_org = UserOrganization::with('user', 'organization')->where('project_id', $id)->where('default', 1)->first();
		if ($default_org) {
			// && $default_org->organization->organization_name != $details_new->manager_name
			$details_new->manager_name = $default_org->organization->organization_name;
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person && $project_default_user->person->user && !is_null($project_default_user->person->user->organization_id) && $project_default_user->person->user->organization_details) {
			$details_new->manager_name = $project_default_user->person->user->organization_details->organization_name;
			$details_new->save();
		}
		$default_phone = UserPhoneNumber::with('user', 'phone')->where('project_id', $id)->where('default', 1)->first();
		if ($default_phone) {
			// && $default_org->organization->organization_name != $details_new->manager_name
			$details_new->manager_phone = $default_phone->phone_number_formatted();
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person->user && !is_null($project_default_user->person->user->organization_id) && $project_default_user->person->user->organization_details) {
			$details_new->manager_phone = $project_default_user->person->user->organization_details->phone_number_formatted();
			$details_new->save();
		}
		$default_email = UserEmail::with('user', 'email_address')->where('project_id', $id)->where('default', 1)->first();
		if ($default_email) {
			// && $default_org->organization->organization_name != $details_new->manager_name
			$details_new->manager_email = $default_email->email_address->email_address;
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person && $project_default_user->person->email) {
			$details_new->manager_email = $project_default_user->person->email->email_address;
			$details_new->save();
		}

		// OWNER INFO
		$project_default_user = $project->contactRoles->where('project_role_key', 20)->first();
		$details = $project->details($audit_id);
		$details_new = $details->replicate();
		$pm = $project->owner();
		//Check if the project has default
		$default_user = ReportAccess::with('user')->where('project_id', $id)->where('owner_default', 1)->first();
		if ($default_user) {
			//&& $default_user->user->name != $details_new->manager_poc
			$details_new->owner_poc = $default_user->user->name;
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person->user) {
			$details_new->owner_poc = $project_default_user->person->user->name;
			$details_new->save();
		}
		$default_address = UserAddresses::with('user', 'address')->where('project_id', $id)->where('owner_default', 1)->first();
		if ($default_address) {
			// && $default_address->address->line_1 != $details_new->manager_address
			$details_new->owner_address = $default_address->address->line_1;
			$details_new->owner_address2 = $default_address->address->line_2;
			$details_new->owner_city = $default_address->address->city;
			$details_new->owner_state = $default_address->address->state;
			$details_new->owner_zip = $default_address->address->zip;
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person->user && !is_null($project_default_user->person->user->organization_id) && $project_default_user->person->user->organization_details) {
			$default_address = $project_default_user->person->user->organization_details;
			$details_new->owner_address = $default_address->address->line_1;
			$details_new->owner_address2 = $default_address->address->line_2;
			$details_new->owner_city = $default_address->address->city;
			$details_new->owner_state = $default_address->address->state;
			$details_new->owner_zip = $default_address->address->zip;
			$details_new->save();
		}
		$default_org = UserOrganization::with('user', 'organization')->where('project_id', $id)->where('owner_default', 1)->first();
		if ($default_org) {
			// && $default_org->organization->organization_name != $details_new->manager_name
			$details_new->owner_name = $default_org->organization->organization_name;
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person->user && !is_null($project_default_user->person->user->organization_id) && $project_default_user->person->user->organization_details) {
			$details_new->owner_name = $project_default_user->person->user->organization_details->organization_name;
			$details_new->save();
		}
		$default_phone = UserPhoneNumber::with('user', 'phone')->where('project_id', $id)->where('owner_default', 1)->first();
		if ($default_phone) {
			// && $default_org->organization->organization_name != $details_new->manager_name
			$details_new->owner_phone = $default_phone->phone_number_formatted();
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person->user && !is_null($project_default_user->person->user->organization_id) && $project_default_user->person->user->organization_details) {
			$details_new->owner_phone = $project_default_user->person->user->organization_details->phone_number_formatted();
			$details_new->save();
		}
		$default_email = UserEmail::with('user', 'email_address')->where('project_id', $id)->where('owner_default', 1)->first();
		if ($default_email) {
			// && $default_org->organization->organization_name != $details_new->manager_name
			$details_new->owner_email = $default_email->email_address->email_address;
			$details_new->save();
		} elseif ($project_default_user && $project_default_user->person && $project_default_user->person->email) {
			$details_new->owner_email = $project_default_user->person->email->email_address;
			$details_new->save();
		}

		$details = $details_new;

		// $details_new-> =

		//         'manager_name' => $this->pm()['organization'],
		//         'manager_poc' => $this->pm()['name'],
		//         'manager_phone' => $this->pm()['phone'],
		//         'manager_fax' => $this->pm()['fax'],
		//         'manager_email' => $this->pm()['email'],
		//         'manager_address' => $this->pm()['line_1'],
		//         'manager_address2' => $this->pm()['line_2'],
		//         'manager_city' => $this->pm()['city'],
		//         'manager_state' => $this->pm()['state'],
		//         'manager_zip' => $this->pm()['zip']
		// $details_new = ProjectDetail::where('project_id', '=', $id)
		//              ->where('audit_id', '=', $audit_id)
		//              ->orderBy('id', 'desc')
		//              ->first();
		$returnHTML = view('projects.partials.details-project-details')->with('details', $details)->render();

		return response()->json(['success' => true, 'html' => $returnHTML]);
	}

	public function getPMProjectDetailsInfo($id, $type, $audit, $return_raw = 0)
	{
		// types: compliance, assignment, findings, followups, reports, documents, comments, photos
		// project: project_id?
		$project = Project::where('id', '=', $id)->first();
		$audit = CachedAudit::with('auditors', 'audit', 'lead_auditor')->where('audit_id', $audit)->first();
		//dd($project->selected_audit());
		$current_user = Auth::user();
		$manager_access = $current_user->manager_access();

		switch ($type) {
			case 'compliance':
				// get the compliance summary for this audit
				//
				$audit = $audit->audit;
				$selection_summary = json_decode($audit->selection_summary, 1);
				//dd($selection_summary['programs']);

				/*
				SUMMARY STATS:
				Requirement (without overlap)
				- required units (this is given by the selection process) $program['totals_before_optimization']
				- selected (this is counted in the db)
				- needed (this is calculated)
				- to be inspected (this is counted in the db)

				To meet compliance (optimized and overlap) & without duplicates (group by unit)
				- sample size (this is given by the selection process) $program['totals_after_optimization']
				- completed (this is counted)
				- remaining inspection (this is calculated)

				FOR EACH PROGRAM: (with unit duplicates)
				- required units (this is given by the selection process)
				- selected (this is counted in the db)
				- needed (this is calculated)
				- to be inspected (this is counted in the db with grouped by unit)
				 */

				$data = [
					'project' => [
						'id' => $project->id,
					],
				];

				/*
				the output of the compliance process should produce the "required units" count. Then the selected should be the same unless they changed some units. That would increase the value of needed units.

				Inspected units are counted when the inspection is completed for that unit.
				To be inspected units is the balance.

				A unit is complete once all of its amenities have been marked complete - it has a completed date on it

				 */

				$stats = $audit->stats_compliance();
				//dd($stats);

				$summary_required = 0;
				$summary_selected = 0;
				$summary_needed = 0;
				$summary_inspected = 0;
				$summary_to_be_inspected = 0;
				$summary_optimized_remaining_inspections = 0;
				$summary_optimized_sample_size = 0;
				$summary_optimized_completed_inspections = 0;

				$summary_required_file = 0;
				$summary_selected_file = 0;
				$summary_needed_file = 0;
				$summary_inspected_file = 0;
				$summary_to_be_inspected_file = 0;
				$summary_optimized_remaining_inspections_file = 0;
				$summary_optimized_sample_size_file = 0;
				$summary_optimized_completed_inspections_file = 0;

				$summary_optimized_unit_ids = [];
				$summary_unit_ids = [];
				$all_program_keys = [];

				// create stats for each group
				// we may have multiple buildings for a group (group 1 or HTC group 7...)
				if (null !== $selection_summary) {
					foreach ($selection_summary['programs'] as $program) {
						// count selected units using the list of program ids
						$program_keys = explode(',', $program['program_keys']);
						$all_program_keys = array_merge($all_program_keys, $program_keys);

						// are we working with a building?
						if (array_key_exists('building_key', $program)) {
							if ('' != $program['building_key']) {
								$selected_units_site = UnitInspection::where('group_id', '=', $program['group'])
									->where('building_key', '=', $program['building_key'])
									->where('audit_id', '=', $audit->id)
									->where('is_site_visit', '=', 1)
									->count();

								$selected_units_file = UnitInspection::where('group_id', '=', $program['group'])
									->where('building_key', '=', $program['building_key'])
									->where('audit_id', '=', $audit->id)
									->where('is_file_audit', '=', 1)
									->count();

								$building = Building::where('building_key', '=', $program['building_key'])->first();
								if ($building) {
									$building_name = $building->building_name;
								} else {
									$building_name = '';
								}

								$inspected_units_site = UnitInspection::where('audit_id', '=', $audit->id)
									->where('group_id', '=', $program['group'])
									->where('building_key', '=', $program['building_key'])
									->where('is_site_visit', '=', 1)
									->where('complete', '!=', null)
									->get()
									->count();

								$inspected_units_file = UnitInspection::where('audit_id', '=', $audit->id)
									->where('group_id', '=', $program['group'])
									->where('building_key', '=', $program['building_key'])
									->where('is_file_audit', '=', 1)
									->where('complete', '!=', null)
									->get()
									->count();
							} else {
								$selected_units_site = UnitInspection::where('group_id', '=', $program['group'])->where('audit_id', '=', $audit->id)->where('is_site_visit', '=', 1)->count();
								$selected_units_file = UnitInspection::where('group_id', '=', $program['group'])->where('audit_id', '=', $audit->id)->where('is_file_audit', '=', 1)->count();

								$building_name = '';

								$inspected_units_site = UnitInspection::where('audit_id', '=', $audit->id)
									->where('group_id', '=', $program['group'])
									->where('is_site_visit', '=', 1)
									->where('complete', '!=', null)
									->get()
									->count();

								$inspected_units_file = UnitInspection::where('audit_id', '=', $audit->id)
									->where('group_id', '=', $program['group'])
									->where('is_file_audit', '=', 1)
									->where('complete', '!=', null)
									->get()
									->count();
							}
						} else {
							$selected_units_site = UnitInspection::where('group_id', '=', $program['group'])->where('audit_id', '=', $audit->id)->where('is_site_visit', '=', 1)->count();
							$selected_units_file = UnitInspection::where('group_id', '=', $program['group'])->where('audit_id', '=', $audit->id)->where('is_file_audit', '=', 1)->count();

							$building_name = '';

							$inspected_units_site = UnitInspection::where('audit_id', '=', $audit->id)
								->where('group_id', '=', $program['group'])
								->where('is_site_visit', '=', 1)
								->where('complete', '!=', null)
								->get()
								->count();

							$inspected_units_file = UnitInspection::where('audit_id', '=', $audit->id)
								->where('group_id', '=', $program['group'])
								->where('is_file_audit', '=', 1)
								->where('complete', '!=', null)
								->get()
								->count();
						}

						$needed_units_site = max($program['required_units'] - $selected_units_site, 0);
						$needed_units_file = max($program['required_units_file'] - $selected_units_file, 0);

						$unit_keys = $program['units_before_optimization'];

						$summary_unit_ids = array_merge($summary_unit_ids, $program['units_before_optimization']);
						$summary_optimized_unit_ids = array_merge($summary_optimized_unit_ids, $program['units_after_optimization']);

						$to_be_inspected_units_site = $selected_units_site - $inspected_units_site;
						$to_be_inspected_units_file = $selected_units_file - $inspected_units_file;

						$summary_required = $summary_required + $program['required_units'];
						$summary_required_file = $summary_required_file + $program['required_units_file'];

						$data['programs'][] = [
							'id' => $program['group'],
							'name' => $program['name'],
							'pool' => $program['pool'],
							'building_key' => $program['building_key'],
							'building_name' => $building_name,
							'comments' => $program['comments'],
							'user_limiter' => $program['use_limiter'],
							// 'totals_after_optimization' => $program['totals_after_optimization_not_merged'],
							// 'units_before_optimization' => $program['units_before_optimization'],
							// 'totals_before_optimization' => $program['totals_before_optimization'],
							'required_units' => $program['required_units'],
							'selected_units' => $selected_units_site,
							'needed_units' => $needed_units_site,
							'inspected_units' => $inspected_units_site,
							'to_be_inspected_units' => $to_be_inspected_units_site,

							'required_units_file' => $program['required_units_file'],
							'selected_units_file' => $selected_units_file,
							'needed_units_file' => $needed_units_file,
							'inspected_units_file' => $inspected_units_file,
							'to_be_inspected_units_file' => $to_be_inspected_units_file,
						];

						// if($program['group'] == 3){
						//     dd($data['programs']);
						// }
					}
				}

				$summary_optimized_unit_ids = array_unique($summary_optimized_unit_ids);

				$all_program_keys = array_unique($all_program_keys);

				$summary_inspected = UnitInspection::whereIn('unit_key', $summary_optimized_unit_ids)
					->where('audit_id', '=', $audit->id)
					->where('is_site_visit', '=', 1)
					->where('complete', '!=', null)
					->count();

				$summary_inspected_file = UnitInspection::whereIn('unit_key', $summary_optimized_unit_ids)
					->where('audit_id', '=', $audit->id)
					->where('is_file_audit', '=', 1)
					->where('complete', '!=', null)
					->count();

				// $summary_required = UnitInspection::whereIn('unit_key', $summary_unit_ids)
				//                 ->where('audit_id', '=', $audit->id)
				//                 ->where('is_site_visit', '=', 1)
				//                 ->count();

				// $summary_required_file = UnitInspection::whereIn('unit_key', $summary_unit_ids)
				//             ->where('audit_id', '=', $audit->id)
				//             ->where('is_file_audit', '=', 1)
				//             ->count();

				$summary_optimized_inspected = UnitInspection::whereIn('unit_key', $summary_optimized_unit_ids)
					->where('audit_id', '=', $audit->id)
					->where('is_site_visit', '=', 1)
					->where('complete', '!=', null)
					->select('unit_id')->groupBy('unit_id')->get()
					->count();

				$summary_optimized_inspected_file = UnitInspection::whereIn('unit_key', $summary_unit_ids)
					->where('audit_id', '=', $audit->id)
					->where('is_file_audit', '=', 1)
					->where('complete', '!=', null)
					->select('unit_id')->groupBy('unit_id')->get()
					->count();

				$summary_optimized_required = UnitInspection::whereIn('unit_key', $summary_optimized_unit_ids)
					->where('audit_id', '=', $audit->id)
					->where('is_site_visit', '=', 1)
					->select('unit_id')->groupBy('unit_id')->get()
					->count();

				$summary_optimized_required_file = UnitInspection::whereIn('unit_key', $summary_unit_ids)
					->where('audit_id', '=', $audit->id)
					->where('is_file_audit', '=', 1)
					->select('unit_id')->groupBy('unit_id')->get()
					->count();

				//$summary_optimized_required_file = $summary_required_file;

				$summary_selected = UnitInspection::whereIn('program_key', $all_program_keys)->where('audit_id', '=', $audit->id)->where('is_site_visit', '=', 1)->select('unit_id')->count();
				$summary_selected_file = UnitInspection::whereIn('program_key', $all_program_keys)->where('audit_id', '=', $audit->id)->where('is_file_audit', '=', 1)->count();

				$summary_needed = max($summary_required - $summary_selected, 0);
				$summary_needed_file = max($summary_required_file - $summary_selected_file, 0);

				$summary_to_be_inspected = $summary_selected - $summary_inspected;
				$summary_to_be_inspected_file = $summary_selected_file - $summary_inspected_file;

				$summary_optimized_sample_size = $summary_optimized_required;
				$summary_optimized_completed_inspections = $summary_optimized_inspected;
				$summary_optimized_remaining_inspections = $summary_optimized_sample_size - $summary_optimized_completed_inspections;

				$summary_optimized_sample_size_file = $summary_optimized_required_file;
				$summary_optimized_completed_inspections_file = $summary_inspected_file;
				$summary_optimized_remaining_inspections_file = $summary_optimized_sample_size_file - $summary_optimized_completed_inspections_file;

				$data['summary'] = [
					'required_units' => $summary_required,
					'selected_units' => $summary_selected,
					'needed_units' => $summary_needed,
					'inspected_units' => $summary_inspected,
					'to_be_inspected_units' => $summary_to_be_inspected,
					'optimized_sample_size' => $summary_optimized_sample_size,
					'optimized_completed_inspections' => $summary_optimized_completed_inspections,
					'optimized_remaining_inspections' => $summary_optimized_remaining_inspections,
					'required_units_file' => $summary_required_file,
					'selected_units_file' => $summary_selected_file,
					'needed_units_file' => $summary_needed_file,
					'inspected_units_file' => $summary_inspected_file,
					'to_be_inspected_units_file' => $summary_to_be_inspected_file,
					'optimized_sample_size_file' => $summary_optimized_required_file,
					'optimized_completed_inspections_file' => $summary_optimized_completed_inspections_file,
					'optimized_remaining_inspections_file' => $summary_optimized_remaining_inspections_file,

				];

				$data['auditID'] = $audit->id;

				if ($return_raw) {
					return $data;
				}

				break;
			case 'assignment':
				// check if the lead is listed as an auditor and add it if needed
				$auditors = $audit->auditors;
				$is_lead_an_auditor = 0;
				$auditors_key = []; // used to store in which order the auditors will be displayed
				if ($audit->lead_auditor) {
					$auditors_key[] = $audit->lead_auditor->id;
				}

				foreach ($auditors as $auditor) {
					if ($audit->lead_auditor) {
						if ($audit->lead_auditor->id == $auditor->user_id) {
							$is_lead_an_auditor = 1;
						} else {
							$auditors_key[] = $auditor->user_id;
						}
					} else {
						$auditors_key[] = $auditor->user_id;
					}
				}

				if (0 == $is_lead_an_auditor && $audit->lead_auditor) {
					// add to audit_auditors
					$new_auditor = new AuditAuditor([
						'user_id' => $audit->lead_auditor->id,
						'user_key' => $audit->lead_auditor->devco_key,
						'monitoring_key' => $audit->audit_key,
						'audit_id' => $audit->audit_id,
					]);
					$new_auditor->save();
				}

				$chart_data = $audit->estimated_chart_data();

				//foreach auditor and for each day, fetch calendar combining availability and schedules
				$daily_schedules = [];
				foreach ($audit->days as $day) {
					$date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $day->date);
					foreach ($auditors_key as $auditor_id) {
						$daily_schedules[$day->id][] = $this->getAuditorDailyCalendar($date, $day->id, $audit->audit_id, $auditor_id);
					}
				}

				// list all the audits that have any of the auditors assigned
				// foreach day
				// $potential_conflict_audits_ids = array();
				// foreach($audit->days as $day){
				//     $potential_conflict_audits = ScheduleTime::select('audit_id')->whereIn('auditor_id', $auditors_key)->where('day_id','=',$day->id)->groupBy('audit_id')->pluck('audit_id')->toArray();

				//     $potential_conflict_audits_ids = array_unique(array_merge($potential_conflict_audits_ids,$potential_conflict_audits), SORT_REGULAR);
				// }

				// for each audit, and using the auditors key as the order, check if auditor is scheduled, not scheduled or not at all involved with the audit
				// also get the audits information for display (date, project name, etc)
				// $potential_conflict_audits = CachedAudit::whereIn('audit_id', $potential_conflict_audits_ids)->orderBy('project_ref','asc')->get();

				// $daily_schedules = array();
				// foreach($audit->days as $day){
				//     // set current audit $audit
				//     foreach($auditors_key as $auditor_id){
				//         // auditors are in the audit for sure
				//         // check if they are scheduled on not
				//         if(ScheduleTime::where('audit_id','=',$audit->audit_id)->where('auditor_id','=',$auditor_id)->where('day_id','=',$day->id)->count()){
				//             $daily_schedules[$day->id][$audit->audit_id]['auditors'][$auditor_id] = 'scheduled'; // scheduled
				//         }else{
				//             $daily_schedules[$day->id][$audit->audit_id]['auditors'][$auditor_id] = 'notscheduled'; // not scheduled
				//         }
				//         $daily_schedules[$day->id][$audit->audit_id]['audit'] = $audit;
				//     }

				//     // set all other audits
				//     foreach($potential_conflict_audits as $potential_conflict_audit){
				//         if($potential_conflict_audit->audit_id != $audit->audit_id){
				//             foreach($auditors_key as $auditor_id){
				//                 // is auditor in the audit?
				//                 if(AuditAuditor::where('audit_id','=',$potential_conflict_audit->audit_id)->where('user_id','=',$auditor_id)->count()){
				//                     if(ScheduleTime::where('audit_id','=',$potential_conflict_audit->audit_id)->where('auditor_id','=',$auditor_id)->where('day_id','=',$day->id)->count()){
				//                         $daily_schedules[$day->id][$potential_conflict_audit->audit_id]['auditors'][$auditor_id] = 'scheduled'; // scheduled
				//                     }else{
				//                         $daily_schedules[$day->id][$potential_conflict_audit->audit_id]['auditors'][$auditor_id] = 'notscheduled'; // not scheduled
				//                     }
				//                 }else{
				//                     $daily_schedules[$day->id][$potential_conflict_audit->audit_id]['auditors'][$auditor_id] = 'notinaudit';
				//                 }
				//                 $daily_schedules[$day->id][$potential_conflict_audit->audit_id]['audit'] = $potential_conflict_audit;
				//             }
				//         }
				//     }
				// }

				$data = collect([
					'project' => [
						'id' => $project->id,
						'ref' => $project->project_number,
						'audit_id' => $audit->audit_id,
					],
					'summary' => [
						'required_unit_selected' => 0,
						'inspectable_areas_assignment_needed' => 0,
						'required_units_selection' => 0,
						'file_audits_needed' => 0,
						'physical_audits_needed' => 0,
						'schedule_conflicts' => 0,
						'estimated' => $audit->estimated_hours() . ':' . $audit->estimated_minutes(),
						'estimated_hours' => $audit->estimated_hours(),
						'estimated_minutes' => $audit->estimated_minutes(),
						'needed' => $audit->hours_still_needed(),
					],
					'audits' => [
						[
							'id' => '19200114',
							'ref' => '111111',
							'date' => '12/22/2018',
							'name' => 'The Garden Oaks',
							'street' => '123466 Silvegwood Street',
							'city' => 'Columbus',
							'state' => 'OH',
							'zip' => '43219',
							'lead' => 2, // user_id
							'schedules' => [
								['icon' => 'a-circle', 'status' => '', 'is_lead' => 1, 'tooltip' => ''],
								['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' => ''],
								['icon' => 'a-circle', 'status' => '', 'is_lead' => 0, 'tooltip' => ''],
								['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' => ''],
							],
						],
					],
				]);

				return view('projects.partials.details-assignment', compact('data', 'project', 'chart_data', 'auditors_key', 'daily_schedules', 'audit', 'current_user', 'manager_access'));

				break;
			case 'findings':
				break;
			case 'followups':
				break;
			case 'reports':
				break;
			case 'documents':
				break;
			case 'comments':
				break;
			case 'photos':
				break;
			case 'selections':
				$details = $project->details();
				// return_raw: site, building, unit
				if ($return_raw) {
					$dpView = 1;
					$findings = $audit->audit->findings->where('cancelled_at', NULL);
					$print = null;
					$report = $audit;
					$detailsPage = 1;
					switch ($return_raw) {
						case 'site':
							$inspections = $audit->audit->project_amenity_inspections()->paginate(12);
							return view('crr_parts.pm_crr_inspections_site', compact('inspections', 'dpView', 'findings', 'print', 'report', 'detailsPage'));
							break;
						case 'building':
							$allBuildingInspections = $audit->audit->building_inspections;

							$selected_audit = $audit;
							if (session()->has('type_id') && session()->has('is_uncorrected')) {
								$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
								$result = array_intersect($bulidingUnresolved, $type_id);
								$inspections = $audit->audit->building_inspections()->whereIn('building_id', $result)->paginate(12);
							} else if (session()->has('is_uncorrected')) {
								$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
								$inspections = $audit->audit->building_inspections()->whereIn('building_id', $bulidingUnresolved)->paginate(12);
							} else if (session()->has('type_id')) {
								$inspections = $audit->audit->building_inspections()->whereIn('building_id', session()->has('type_id'))->paginate(12);
							} else {
								$inspections = $audit->audit->building_inspections()->paginate(12);
							}

							// $selected_audit = $audit;
							// // if(session()->has('type_id') && session()->has('is_uncorrected')){
							// // 	$inspections = $audit->audit->building_inspections()->whereIn('building_id',$type_id)->paginate(10);
							// // }else
							// if(session()->has('type_id')){
							// 	$inspections = $audit->audit->building_inspections()->whereIn('building_id',$type_id)->paginate(12);
							// }
							// // else if(!session()->has('type_id') && session()->has('is_uncorrected')){
							// // 	$inspections = $audit->audit->building_inspections()->paginate(12);
							// // }
							// else{
							// 	$inspections = $audit->audit->building_inspections()->paginate(12);
							// }

							return view('crr_parts.pm_crr_inspections_building', compact('inspections', 'allBuildingInspections', 'dpView', 'findings', 'print', 'report', 'selected_audit', 'detailsPage'));
							break;
						case 'unit':
							$allUnitInspections = $audit->audit->unit_inspections;
							if (session()->has('type_id') && session()->has('is_uncorrected')) {
								$allBuildingInspections = $audit->audit->building_inspections;
								$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
								$result = array_intersect($bulidingUnresolved, $type_id);
								$unitUnresolvedId = $audit->audit->unitUnResolved($allUnitInspections, $findings);
								$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->whereIn('building_id', $result)->whereIn('unit_id', $unitUnresolvedId)->with('documents')->paginate(12);
							} else if (session()->has('is_uncorrected')) {
								$allUnitInspections1 = $audit->audit->unit_inspections()->groupBy('unit_id')->get();
								// echo count($allUnitInspections);exit;
								$unitUnresolvedId = $audit->audit->unitUnResolved($allUnitInspections1, $findings);

								$inspections = $audit->audit->unit_inspections()->whereIn('unit_id', $unitUnresolvedId)->with('documents')->paginate(12);
							} else if (session()->has('type_id')) {
								$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->whereIn('building_id', session()->get('type_id'))->with('documents')->paginate(12);
							} else {
								$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->with('documents')->paginate(12);
							}

							// if(session()->has('type_id')){
							// 	$inspections = $audit->audit->unit_inspections()->whereIn('building_id',session()->get('type_id'))->groupBy('unit_id')->with('documents')->paginate(12);
							// }else{
							// 	$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->with('documents')->paginate(12);
							// }
							return view('crr_parts.pm_crr_inspections_unit', compact('inspections', 'allUnitInspections', 'dpView', 'print', 'report', 'findings', 'detailsPage', 'audit'));
							break;
						default:
					}
				}

				Session::forget('type_id');
				Session::forget('name');
				Session::forget('is_uncorrected');

				return view('projects.partials.pm-details-selections', compact('audit', 'details'));
				break;
			default:
		}
		// return $data['programs'];
		return view('projects.partials.details-' . $type, compact('data', 'project'));
	}

	public function getPMAuditorDailyCalendar($date, $day_id, $audit_id, $auditor_id)
	{
		$events_array = [];
		$availabilities = Availability::where('user_id', '=', $auditor_id)
			->where('date', '=', $date->format('Y-m-d'))
			->orderBy('start_slot', 'asc')
			->get();

		$a_array = $availabilities->toArray();

		// $day = ScheduleDay::where('id','=',$day_id)->first();

		$schedules = ScheduleTime::where('auditor_id', '=', $auditor_id)
			->whereHas('day', function ($q) use ($date) {
				$q->whereDate('date', '=', $date);
			})
			->with('audit.project')
			->orderBy('start_slot', 'asc')
			->get();

		$s_array = $schedules->toArray();

		//dd($a_array, $s_array);

		// we check chronologically by slot of 15 min
		// detect if slot is at the beginning of a scheduled time, add that schedule to the event array and set slot to the end of the scheduled event. Also if there are variables for avail start and span (see below) then add the availability before and reset those variables.
		// detect if slot is inside an availability, save start in a variable and span in another
		// detect if no avail or no schedule, if there are variables for avail start and span, add avail, reset them. slot++.

		$slot = 1;
		$check_avail_start = null;
		$check_avail_span = null;

		// get user default address and compare with project's address for estimated driving times
		$auditor = User::where('id', '=', $auditor_id)->first();
		$default_address = $auditor->default_address();
		$distanceAndTime = $auditor->distanceAndTime($audit_id);
		if ($distanceAndTime) {
			// round up to the next 15 minute slot
			$minutes = intval($distanceAndTime[2] / 60, 10);
			$travel_time = ($minutes - ($minutes % 15) + 15) / 15; // time in 15 min slots
		} else {
			$travel_time = null;
		}
		while ($slot <= 60) {
			$skip = 0;

			// Is slot the start of an event
			// if there is check_avail_start and check_avail_span, add avail and reset them.
			// add travel event
			// add event
			// reset slot to the end of the event
			foreach ($s_array as $s) {
				if ($audit_id == $s['audit_id']) {
					$thisauditclass = 'thisaudit';
				} else {
					$thisauditclass = '';
				}

				if ($s['start_slot'] - $s['travel_span'] == $slot) {
					// save any previous availability
					if (null != $check_avail_start && null != $check_avail_span) {
						$hours = sprintf('%02d', floor(($check_avail_start - 1) * 15 / 60) + 6);
						$minutes = sprintf('%02d', ($check_avail_start - 1) * 15 % 60);
						$start_time = formatTime($hours . ':' . $minutes . ':00', 'H:i:s');
						$hours = sprintf('%02d', floor(($check_avail_start + $check_avail_span - 1) * 15 / 60) + 6);
						$minutes = sprintf('%02d', ($check_avail_start + $check_avail_span - 1) * 15 % 60);
						$end_time = formatTime($hours . ':' . $minutes . ':00', 'H:i:s');

						$events_array[] = [
							'id' => uniqid(),
							'auditor_id' => $auditor_id,
							'audit_id' => $audit_id,
							'status' => '',
							'travel_time' => $travel_time,
							'start_time' => strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $start_time)->format('h:i A')),
							'end_time' => strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $end_time)->format('h:i A')),
							'start' => $check_avail_start,
							'span' => $check_avail_span,
							'travel_span' => null,
							'icon' => 'a-circle-plus',
							'class' => 'available no-border-top no-border-bottom',
							'modal_type' => 'addschedule',
							'tooltip' => 'AVAILABLE TIME ' . strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $start_time)->format('h:i A')) . ' ' . strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $end_time)->format('h:i A')),
						];

						$check_avail_start = null;
						$check_avail_span = null;
					}

					// save travel
					if ($s['travel_span'] > 0) {
						$hours = sprintf('%02d', floor(($s['start_slot'] - $s['travel_span'] - 1) * 15 / 60) + 6);
						$minutes = sprintf('%02d', ($s['start_slot'] - $s['travel_span'] - 1) * 15 % 60);
						$start_time = formatTime($hours . ':' . $minutes . ':00', 'H:i:s');
						$hours = sprintf('%02d', floor(($s['start_slot'] - 1) * 15 / 60) + 6);
						$minutes = sprintf('%02d', ($s['start_slot'] - 1) * 15 % 60);
						$end_time = formatTime($hours . ':' . $minutes . ':00', 'H:i:s');

						$events_array[] = [
							'id' => uniqid(),
							'auditor_id' => $auditor_id,
							'audit_id' => $audit_id,
							'status' => '',
							'travel_time' => '',
							'start_time' => strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $start_time)->format('h:i A')),
							'end_time' => strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $end_time)->format('h:i A')),
							'start' => $s['start_slot'] - $s['travel_span'],
							'span' => $s['travel_span'],
							'travel_span' => null,
							'icon' => '',
							'class' => 'travel ' . $thisauditclass,
							'modal_type' => '',
							'tooltip' => 'TRAVEL TIME ' . strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $start_time)->format('h:i A')) . ' ' . strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $end_time)->format('h:i A')),
						];
						$travelclass = ' no-border-top';
					} else {
						$travelclass = '';
					}

					// save schedule
					$events_array[] = [
						'id' => $s['id'],
						'auditor_id' => $auditor_id,
						'audit_id' => $audit_id,
						'status' => '',
						'travel_time' => '',
						'start_time' => strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $s['start_time'])->format('h:i A')),
						'end_time' => strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $s['end_time'])->format('h:i A')),
						'start' => $s['start_slot'],
						'span' => $s['span'],
						'travel_span' => null,
						'icon' => 'a-mobile-checked',
						'class' => 'schedule ' . $thisauditclass . $travelclass,
						'modal_type' => 'removeschedule',
						'tooltip' => 'SCHEDULED TIME ' . strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $s['start_time'])->format('h:i A')) . ' ' . strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $s['end_time'])->format('h:i A')) . ' (PROJECT NUMBER: ' . $s['audit']['project']['project_number'] . ')',
					];

					// reset slot to the just after the scheduled time
					$slot = $s['start_slot'] + $s['span'];
					$skip = 1;
				}
			}

			// Is slot within an availability
			// if there is already check_avail_start, only update check_avail_span, otherwise save both. slot++
			if (!$skip) {
				foreach ($a_array as $a) {
					if ($slot >= $a['start_slot'] && $slot < $a['start_slot'] + $a['span']) {
						if (null != $check_avail_start && null != $check_avail_span) {
							$check_avail_span++;
						} else {
							$check_avail_start = $slot;
							$check_avail_span = 1;
						}
						$slot++;
						$skip = 1;
					}
				}
			}

			// Is slot in nothing
			// Are there check_avail_start and check_avail_span? If so add avail to events and reset the variables. slot++
			if (!$skip) {
				if (null != $check_avail_start && null != $check_avail_span) {
					$hours = sprintf('%02d', floor(($check_avail_start - 1) * 15 / 60) + 6);
					$minutes = sprintf('%02d', ($check_avail_start - 1) * 15 % 60);
					$start_time = formatTime($hours . ':' . $minutes . ':00', 'H:i:s');
					$hours = sprintf('%02d', floor(($check_avail_start + $check_avail_span - 1) * 15 / 60) + 6);
					$minutes = sprintf('%02d', ($check_avail_start + $check_avail_span - 1) * 15 % 60);
					$end_time = formatTime($hours . ':' . $minutes . ':00', 'H:i:s');

					$events_array[] = [
						'id' => uniqid(),
						'auditor_id' => $auditor_id,
						'audit_id' => $audit_id,
						'status' => '',
						'travel_time' => $travel_time,
						'start_time' => strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $start_time)->format('h:i A')),
						'end_time' => strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $end_time)->format('h:i A')),
						'start' => $check_avail_start,
						'span' => $check_avail_span,
						'travel_span' => null,
						'icon' => 'a-circle-plus',
						'class' => 'available no-border-top no-border-bottom',
						'modal_type' => 'addschedule',
						'tooltip' => 'AVAILABLE TIME ' . strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $start_time)->format('h:i A')) . ' ' . strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $end_time)->format('h:i A')),
					];

					$check_avail_start = null;
					$check_avail_span = null;
					$slot++;
				} else {
					$slot++;
				}
			}
		}

		$header[] = $date->copy()->format('m/d');

		if (count($events_array)) {
			// figure out the before and after areas on the schedule
			$start_slot = 60;
			$end_slot = 1;
			foreach ($events_array as $e) {
				if ($e['start'] <= $start_slot) {
					$start_slot = $e['start'];
				}

				if ($e['start'] + $e['span'] >= $end_slot) {
					$end_slot = $e['start'] + $e['span'];
				}
			}

			$before_time_start = 1;
			$before_time_span = $start_slot - 1;
			$after_time_start = $end_slot;
			$after_time_span = 61 - $end_slot;
			$no_availability = 0;
		} else {
			$events_array = [];
			$before_time_start = 1;
			$before_time_span = 0;
			$after_time_start = 60;
			$after_time_span = 1;
			$no_availability = 1;
		}

		$days = [
			'date' => $date->copy()->format('m/d'),
			'date_formatted' => $date->copy()->format('F j, Y'),
			'date_formatted_name' => strtolower($date->copy()->englishDayOfWeek),
			'no_availability' => $no_availability,
			'before_time_start' => $before_time_start,
			'before_time_span' => $before_time_span,
			'after_time_start' => $after_time_start,
			'after_time_span' => $after_time_span,
			'events' => $events_array,
		];

		$calendar = [
			'header' => $header,
			'content' => $days,
		];

		return $calendar;
	}

	public function getPMProjectDetailsAssignmentSchedule($project, $dateid)
	{
		$data = collect([
			'project' => [
				'id' => 1,
			],
			'summary' => [
				'required_unit_selected' => 0,
				'inspectable_areas_assignment_needed' => 12,
				'required_units_selection' => 13,
				'file_audits_needed' => 14,
				'physical_audits_needed' => 15,
				'schedule_conflicts' => 16,
				'estimated' => '107:00',
				'estimated_minutes' => '',
				'needed' => '27:00',
			],
			'auditors' => [
				[
					'id' => 1,
					'name' => 'Brian Greenwood',
					'initials' => 'BG',
					'color' => 'pink',
				],
				[
					'id' => 2,
					'name' => 'Brianna Bluewood',
					'initials' => 'BB',
					'color' => 'blue',
				],
				[
					'id' => 3,
					'name' => 'John Smith',
					'initials' => 'JS',
					'color' => 'black',
				],
				[
					'id' => 4,
					'name' => 'Sarah Connor',
					'initials' => 'SC',
					'color' => 'red',
				],
			],
			'days' => [
				[
					'id' => 6,
					'date' => '12/22/2018',
					'status' => 'action-required',
					'icon' => 'a-avatar-fail',
				],
				[
					'id' => 7,
					'date' => '12/23/2018',
					'status' => 'ok-actionable',
					'icon' => 'a-avatar-approve',
				],
			],
			'projects' => [
				[
					'id' => '19200114',
					'date' => '12/22/2018',
					'name' => 'The Garden Oaks',
					'street' => '123466 Silvegwood Street',
					'city' => 'Columbus',
					'state' => 'OH',
					'zip' => '43219',
					'lead' => 2, // user_id
					'schedules' => [
						['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
						['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
						['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
						['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
					],
				],
				[
					'id' => '19200115',
					'date' => '12/22/2018',
					'name' => 'The Garden Oaks 2',
					'street' => '123466 Silvegwood Street',
					'city' => 'Columbus',
					'state' => 'OH',
					'zip' => '43219',
					'lead' => 1, // user_id
					'schedules' => [
						['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
						['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
						['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
						['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
					],
				],
				[
					'id' => '19200116',
					'date' => '12/22/2018',
					'name' => 'The Garden Oaks 3',
					'street' => '123466 Silvegwood Street',
					'city' => 'Columbus',
					'state' => 'OH',
					'zip' => '43219',
					'lead' => 2, // user_id
					'schedules' => [
						['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
						['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
						['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
						['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' => 'APPROVE SCHEDULE CONFLICT'],
					],
				],
			],
		]);

		return view('projects.partials.details-assignment-schedule', compact('data'));
	}

	public function getPMProjectStream($project = null)
	{
		if (Auth::user()->auditor_access()) {
			$project = Project::where('id', '=', $project)->first();

			if ($project->currentAudit()) {
				$auditid = $project->currentAudit()->audit_id;
			} else {
				return 'Sorry, there is no audit associated with this project.';
			}

			$findings = Finding::where('project_id', $project)
				->whereNull('cancelled_at')
				->orderBy('updated_at', 'desc')
				->get();

			$buildingid = '';
			$unitid = '';
			$amenityid = '';
			$type = 'all';
		} else {
			return 'Sorry, you do not have permission to access this page.';
		}

		return view('projects.partials.stream', compact('type', 'findings', 'auditid', 'buildingid', 'unitid', 'amenityid'));
	}

	public function getPMProjectReports(Request $request, $project_id = null)
	{
		$id = $project_id;
		$project = Project::find($project_id);
		$prefix = 'project' . $project->id;
		$messages = [];
		// Perform Actions First.
		if (!is_null($request->get('due'))) {
			$data = [];
			$data['due'] = $request->get('due');
			$data['id'] = $request->get('report_id');
			$messages[] = $this->dueDate($data);
		}

		if (!is_null($request->get('action'))) {
			$data = [];
			$data['action'] = intval($request->get('action'));
			$report = CrrReport::find($request->get('id'));
			$rc = new ReportsController($request);
			$messages[] = $rc->reportAction($report, $data);
		}

		// Set default filters for first view of page:
		if (session($prefix . 'crr_first_load') !== 1) {
			//session([$prefix.'crr_report_status_id' => 1]);
			// set some default parameters
			if (Auth::user()->can('access_manager')) {
				//session([$prefix.'crr_report_status_id' => 2]);
				// pending manager review
			} elseif (Auth::user()->can('access_auditor')) {
				//session([$prefix.'crr_report_lead_id' => Auth::user()->id]);
				// show this auditors reports
			} elseif (Auth::user()->can('access_pm')) {
				//session([$prefix.'crr_report_status_id' => 6]);
				// @todo
			}
			session([$prefix . 'crr_first_load' => 1]);
			// makes sure if they override or clear the default filter it doesn't get overrideen.
		}

		// Search Number
		$sessionCrrpSearchName = $prefix . 'crrp_search';
		if ($request->get('search')) {
			session([$sessionCrrpSearchName => $request->get('search')]);
		} elseif (is_null(session('crrp_search'))) {
			session([$sessionCrrpSearchName => 'all']);
		}
		if (session($sessionCrrpSearchName) !== 'all') {
			$searchEval = '=';
			$searchVal = intval(session($sessionCrrpSearchName));
		} else {
			session([$sessionCrrpSearchName => 'all']);
			$searchEval = '>';
			$searchVal = '0';
		}

		// Report Type
		$sessionCrrReportType = $prefix . 'crr_report_type';
		if ($request->get('crr_report_type')) {
			session([$sessionCrrReportType => $request->get('crr_report_type')]);
		} elseif (is_null(session($sessionCrrReportType))) {
			session([$sessionCrrReportType => 'all']);
		}
		if (session($sessionCrrReportType) !== 'all') {
			$typeEval = '=';
			$typeVal = intval(session($sessionCrrReportType));
		} else {
			session([$sessionCrrReportType => 'all']);
			$typeEval = '>';
			$typeVal = '0';
		}

		// Report Status
		if ($request->get('crr_report_status_id')) {
			session([$prefix . 'crr_report_status_id' => $request->get('crr_report_status_id')]);
		} elseif (is_null(session($prefix . 'crr_report_status_id'))) {
			session([$prefix . 'crr_report_status_id' => 'all']);
		}
		if (Auth::user()->can('access_auditor')) {
			if (session($prefix . 'crr_report_status_id') !== 'all') {
				$approvalTypeEval = '=';
				$approvalTypeVal = intval(session($prefix . 'crr_report_status_id'));
			} else {
				session([$prefix . 'crr_report_status_id' => 'all']);
				$approvalTypeEval = '>';
				$approvalTypeVal = 0;
			}
		} else {
			if (session($prefix . 'crr_report_status_id') !== 'all') {
				if (intval(session($prefix . 'crr_report_status_id')) < 6) {
					//user is trying to get a status they cannot access
					session([$prefix . 'crr_report_status_id' => 6]); // default them to the sent
				}
				$approvalTypeEval = '=';
				$approvalTypeVal = intval(session($prefix . 'crr_report_status_id'));
			} else {
				session([$prefix . 'crr_report_status_id' => 'all']);
				$approvalTypeEval = '>';
				$approvalTypeVal = 5;
			}
		}

		// Lead Selection
		if ($request->get('crr_report_lead_id')) {
			session([$prefix . 'crr_report_lead_id' => $request->get('crr_report_lead_id')]);
		} elseif (is_null(session($prefix . 'crr_report_lead_id'))) {
			session([$prefix . 'crr_report_lead_id' => 'all']);
		}
		if (session($prefix . 'crr_report_lead_id') !== 'all') {
			$leadEval = '=';
			$leadVal = intval(session($prefix . 'crr_report_lead_id'));
		} else {
			session([$prefix . 'crr_report_lead_id' => 'all']);
			$leadEval = '>';
			$leadVal = 0;
		}

		// Check For Newer Than Selection
		if ($request->get('newer_than')) {
			// this is only used for checking for updated records
			$newerThan = $request->get('newer_than');
		} else {
			$newerThan = '1900-01-01 00:00:01';
		}

		if (Auth::user()->can('access_auditor')) {
			if (!is_null($project_id)) {
				$auditLeads = Audit::where('project_id', $project_id)->with('lead')->with('project')->whereNotNull('lead_user_id')->groupBy('lead_user_id')->get();
				$auditProjects = CrrReport::where('project_id', $project_id)->select('project_id')->with('project')->groupBy('project_id')->get();
			} else {
				$auditLeads = Audit::select('*')->with('lead')->with('project')->whereNotNull('lead_user_id')->groupBy('lead_user_id')->get();
				$auditProjects = CrrReport::select('project_id')->with('project')->groupBy('project_id')->get();
			}

			//$auditProjects   = CrrReport::select('*')->with('project')->groupBy('project_id')->get();
			$crr_types_array = CrrReport::select('id', 'template_name')->groupBy('template_name')->whereNotNull('template')->get()->all();
			$hfa_users_array = [];
			$projects_array = [];
		} else {
			$auditLeads = []; //Audit::select('*')->with('lead')->with('project')->whereNotNull('lead_user_id')->groupBy('lead_user_id')->get();
			$auditProjects = CrrReport::select('project_id')->when(Auth::user()->cannot('access_auditor'), function ($query) {
				$userProjects = \App\Models\ProjectContactRole::select('project_id')->where('person_id', Auth::user()->person_id)->get()->toArray();

				return $query->whereIn('project_id', $userProjects);
			})->with('project')->groupBy('project_id')->get();
			$crr_types_array = CrrReport::select('id', 'template_name', 'crr_approval_type_id')->where('crr_approval_type_id', '>', 5)->groupBy('template_name')->whereNotNull('template')->get()->all();
			$hfa_users_array = [];
			$projects_array = [];
		}
		foreach ($auditLeads as $hfa) {
			if ($hfa->lead_user_id && $hfa->lead) {
				//check this relationship, we are checking lead_user_id but fetching lead based on devco_key - Div 20191205
				$hfa_users_array[] = $hfa->lead;
			}
		}
		foreach ($auditProjects as $hfa) {
			if ($hfa->project) {
				$projects_array[] = $hfa->project;
			}
		}
		$hfa_users_array = array_values(Arr::sort($hfa_users_array, function ($value) {
			return $value['name'];
		}));
		$projects_array = array_values(Arr::sort($projects_array, function ($value) {
			return $value['project_name'];
		}));
		if (Auth::user()->can('access_auditor')) {
			$crrApprovalTypes = CrrApprovalType::orderBy('order')->get();
		} else {
			$crrApprovalTypes = CrrApprovalType::where('id', '>', 5)->orderBy('order')->get();
		}
		if (null !== $request->get('order_by')) {
			switch ($request->get('order_by')) {
				case 'id':
					if (null !== session($prefix . 'report_order_by') && session($prefix . 'report_order_by') == 'id') {
						if (session($prefix . 'report_asc_desc') == 'asc') {
							session([$prefix . 'report_asc_desc' => 'desc']);
						} else {
							session([$prefix . 'report_asc_desc' => 'asc']);
						}
					} else {
						session([$prefix . 'report_asc_desc' => 'asc']);
					}
					session([$prefix . 'report_order_by' => 'id']);
					break;

				case 'project_id':
					if (null !== session($prefix . 'report_order_by') && session($prefix . 'report_order_by') == 'project_id') {
						if (session($prefix . 'report_asc_desc') == 'asc') {
							session([$prefix . 'report_asc_desc' => 'desc']);
						} else {
							session([$prefix . 'report_asc_desc' => 'asc']);
						}
					} else {
						session([$prefix . 'report_asc_desc' => 'asc']);
					}
					session([$prefix . 'report_order_by' => 'project_id']);
					break;

				case 'audit_id':
					if (null !== session($prefix . 'report_order_by') && session($prefix . 'report_order_by') == 'audit_id') {
						if (session($prefix . 'report_asc_desc') == 'asc') {
							session([$prefix . 'report_asc_desc' => 'desc']);
						} else {
							session([$prefix . 'report_asc_desc' => 'asc']);
						}
					} else {
						session([$prefix . 'report_asc_desc' => 'asc']);
					}
					session([$prefix . 'report_order_by' => 'audit_id']);
					break;

				case 'lead_id':
					if (null !== session($prefix . 'report_order_by') && session($prefix . 'report_order_by') == 'lead_id') {
						if (session($prefix . 'report_asc_desc') == 'asc') {
							session([$prefix . 'report_asc_desc' => 'desc']);
						} else {
							session([$prefix . 'report_asc_desc' => 'asc']);
						}
					} else {
						session([$prefix . 'report_asc_desc' => 'asc']);
					}
					session([$prefix . 'report_order_by' => 'lead_id']);
					break;
				case 'from_template_id':
					if (null !== session($prefix . 'report_order_by') && session($prefix . 'report_order_by') == 'from_template_id') {
						if (session($prefix . 'report_asc_desc') == 'asc') {
							session([$prefix . 'report_asc_desc' => 'desc']);
						} else {
							session([$prefix . 'report_asc_desc' => 'asc']);
						}
					} else {
						session([$prefix . 'report_asc_desc' => 'asc']);
					}
					session([$prefix . 'report_order_by' => 'from_template_id']);
					break;

				case 'crr_approval_type_id':
					if (null !== session($prefix . 'report_order_by') && session($prefix . 'report_order_by') == 'crr_approval_type_id') {
						if (session($prefix . 'report_asc_desc') == 'asc') {
							session([$prefix . 'report_asc_desc' => 'desc']);
						} else {
							session([$prefix . 'report_asc_desc' => 'asc']);
						}
					} else {
						session([$prefix . 'report_asc_desc' => 'asc']);
					}
					session([$prefix . 'report_order_by' => 'crr_approval_type_id']);
					break;

				case 'created_at':
					if (null !== session($prefix . 'report_order_by') && session($prefix . 'report_order_by') == 'created_at') {
						if (session($prefix . 'report_asc_desc') == 'asc') {
							session([$prefix . 'report_asc_desc' => 'desc']);
						} else {
							session([$prefix . 'report_asc_desc' => 'asc']);
						}
					} else {
						session([$prefix . 'report_asc_desc' => 'asc']);
					}
					session([$prefix . 'report_order_by' => 'created_at']);
					break;

				case 'response_due_date':
					if (null !== session($prefix . 'report_order_by') && session($prefix . 'report_order_by') == 'response_due_date') {
						if (session($prefix . 'report_asc_desc') == 'asc') {
							session([$prefix . 'report_asc_desc' => 'desc']);
						} else {
							session([$prefix . 'report_asc_desc' => 'asc']);
						}
					} else {
						session([$prefix . 'report_asc_desc' => 'asc']);
					}
					session([$prefix . 'report_order_by' => 'response_due_date']);
					break;

				default:
					session([$prefix . 'report_asc_desc' => 'desc']);
					session([$prefix . 'report_order_by' => 'updated_at']);
					break;
			}
		} else {
			session([$prefix . 'report_asc_desc' => 'desc']);
			session([$prefix . 'report_order_by' => 'updated_at']);
		}

		$reports = CrrReport::select('id', 'audit_id', 'project_id', 'lead_id', 'manager_id', 'response_due_date', 'version', 'crr_approval_type_id', 'created_at', 'updated_at', 'default', 'template', 'from_template_id', 'last_updated_by', 'created_by', 'report_history', 'signed_by', 'signed_by_id', 'signed_version', 'date_signed', 'requires_approval', 'viewed_by_property_date', 'all_ehs_resolved_date', 'all_findings_resolved_date', 'all_findings_resolved_date', 'date_ehs_resolutions_due', 'date_all_resolutions_due')
			->where('crr_approval_type_id', $approvalTypeEval, $approvalTypeVal)
			->whereNull('template')
			->where('project_id', '=', $id)
			->where('lead_id', $leadEval, $leadVal)
			->where('updated_at', '>', $newerThan)
			->where('from_template_id', $typeEval, $typeVal)
			->where('id', $searchEval, $searchVal)
			->with('lead')
			->with('project')
			->with('crr_approval_type')
			->with('cached_audit')
		// ->when(Auth::user()->cannot('access_auditor'), function ($query) {
		//         $userProjects = \App\Models\ProjectContactRole::select('project_id')->where('person_id',Auth::user()->person_id)->get()->toArray();
		//         return $query->whereIn('project_id', $userProjects);
		// })
			->orderBy(session($prefix . 'report_order_by'), session($prefix . 'report_asc_desc'))
			->paginate(100);

		if (count($reports)) {
			$newest = $reports->sortByDesc('updated_at');
			$newest = date('Y-m-d G:i:s', strtotime($newest[0]->updated_at));
		} else {
			$newest = null;
		}

		// return view('projects.partials.reports',compact('id','reports', 'project', 'hfa_users_array', 'crrApprovalTypes', 'crr_types_array', 'messages', 'newest','prefix','sessionCrrReportType'));

		if ($request->get('check')) {
			if (count($reports)) {
				return json_encode($reports);
			} else {
				return 1;
			}
		} elseif ($request->get('rows_only')) {
			return view('projects.partials.reports-row', compact('reports', 'prefix'));
		} else {
			return view('projects.partials.reports', compact('id', 'reports', 'project', 'hfa_users_array', 'crrApprovalTypes', 'crr_types_array', 'messages', 'newest', 'prefix', 'sessionCrrReportType'));
		}
	}

	public function modalPMProjectProgramSummaryFilterProgram($project_id, $program_id, Request $request, $audit)
	{
		/**
		 * Make chart data refelct the filter along with filter
		 * Include selected numbers for each group below chart
		 * Show the group to which the unit belongs to
		 *         this is tricky part, need to crate a function that automatically reads the program_settings and populates program_groups table
		 * Include Substitute for : Program (Program group)
		 * Show selection of HTC group -- need help on this.
		 *
		 * SWAP MODAL
		 *     Make this as 4 sections
		 *         Chart
		 *         Groups audit info (Is this programs?)
		 *         Units info
		 */
		$programs = $request->get('programs');
		$audit = CachedAudit::where('audit_id', $audit)->first();
		if (is_array($programs) && count($programs) > 0) {
			$filters = collect([
				'programs' => $programs,
			]);
		} else {
			$filters = null;
		}
		$project = Project::where('id', '=', $project_id)->first();
		$audit = $audit->audit;
		$selection_summary = json_decode($audit->selection_summary, 1);
		// get units filterd in programs
		if (empty($programs)) {
			$unitprograms = UnitProgram::where('audit_id', '=', $audit->id)
				->with('unit', 'program.relatedGroups', 'unit.building', 'unit.building.address', 'unitInspected')

				->join('units', 'units.id', 'unit_programs.unit_id')
				->join('buildings', 'buildings.id', 'units.building_id')
				->orderBy('buildings.building_name', 'asc')
				->orderBy('units.unit_name', 'asc')
				->get();
		} else {
			$unitprograms = UnitProgram::where('audit_id', '=', $audit->id)
				->whereIn('program_key', $programs)
				->with('unit', 'program.relatedGroups', 'unit.building', 'unit.building.address', 'unitInspected')

				->join('units', 'units.id', 'unit_programs.unit_id')
				->join('buildings', 'buildings.id', 'units.building_id')
				->orderBy('buildings.building_name', 'asc')
				->orderBy('units.unit_name', 'asc')
				->get();
		}
		$all_unitprograms = UnitProgram::where('audit_id', '=', $audit->id)
			->with('unit', 'program.relatedGroups', 'unit.building', 'unit.building.address', 'unitInspected')

			->join('units', 'units.id', 'unit_programs.unit_id')
			->join('buildings', 'buildings.id', 'units.building_id')
			->orderBy('buildings.building_name', 'asc')
			->orderBy('units.unit_name', 'asc')
			->get();
		$actual_programs = $all_unitprograms->pluck('program')->unique()->toArray();
		$unitprograms = $unitprograms->groupBy('unit_id');
		foreach ($actual_programs as $key => $actual_program) {
			$group_names = array_column($actual_program['related_groups'], 'group_name');
			$group_ids = array_column($actual_program['related_groups'], 'id');
			if (!empty($group_names)) {
				$actual_programs[$key]['group_names'] = implode(', ', $group_names);
				$actual_programs[$key]['group_ids'] = $group_ids;
			} else {
				$actual_programs[$key]['group_names'] = ' - ';
				$actual_programs[$key]['group_ids'] = [];
			}
		}

		return view('dashboard.partials.project-summary-unit', compact('unitprograms', 'actual_programs', 'audit'));
	}

	private function pmProjectSummaryComposite($project_id, $audit_id = 0)
	{
		$project = Project::where('id', '=', $project_id)->first();
		if ($audit_id) {
			$audit = $project->selected_audit($audit_id)->audit;
		} else {
			$audit = $project->selected_audit()->audit;
		}
		$selection_summary = json_decode($audit->selection_summary, 1);
		session(['audit-' . $audit->id . '-selection_summary' => $selection_summary]);
		$programs = [];
		$program_keys_list = '';
		if (null !== $selection_summary) {
			foreach ($selection_summary['programs'] as $p) {
				if ($p['pool'] > 0) {
					$programs[] = [
						'id' => $p['group'],
						'name' => $p['name'],
					];
					if ('' != $program_keys_list) {
						$program_keys_list = $program_keys_list . ',';
					}
					$program_keys_list = $program_keys_list . $p['program_keys'];
				}
			}
		}
		// get all the programs
		$data = [
			'project' => [
				'id' => $project->id,
			],
		];
		$stats = $audit->stats_compliance();
		$summary_required = 0;
		$summary_selected = 0;
		$summary_needed = 0;
		$summary_inspected = 0;
		$summary_to_be_inspected = 0;
		$summary_optimized_remaining_inspections = 0;
		$summary_optimized_sample_size = 0;
		$summary_optimized_completed_inspections = 0;
		$summary_required_file = 0;
		$summary_selected_file = 0;
		$summary_needed_file = 0;
		$summary_inspected_file = 0;
		$summary_to_be_inspected_file = 0;
		$summary_optimized_remaining_inspections_file = 0;
		$summary_optimized_sample_size_file = 0;
		$summary_optimized_completed_inspections_file = 0;
		// create stats for each group
		// build the dataset for the chart
		$datasets = [];
		$all_program_keys = [];
		if (null !== $selection_summary) {
			foreach ($selection_summary['programs'] as $program) {
				//this is actually groups not programs!
				// count selected units using the list of program ids
				$program_keys = explode(',', $program['program_keys']);
				$all_program_keys[] = $program_keys;
				$selected_units_site = UnitInspection::whereIn('program_key', $program_keys)->where('audit_id', '=', $audit->id)->where('group_id', '=', $program['group'])->where('is_site_visit', '=', 1)->select('unit_id')->groupBy('unit_id')->get()->count();
				$selected_units_file = UnitInspection::whereIn('program_key', $program_keys)->where('audit_id', '=', $audit->id)->where('group_id', '=', $program['group'])->where('is_file_audit', '=', 1)->select('unit_id')->groupBy('unit_id')->get()->count();
				$needed_units_site = $program['totals_after_optimization'] - $selected_units_site;
				$needed_units_file = $program['totals_after_optimization'] - $selected_units_file;
				$unit_keys = $program['units_after_optimization'];
				$inspected_units_site = UnitInspection::whereIn('unit_key', $unit_keys)
					->where('audit_id', '=', $audit->id)
					->where('group_id', '=', $program['group'])
				// ->whereHas('amenity_inspections', function($query) {
				//     $query->where('completed_date_time', '!=', null);
				// })
					->where('is_site_visit', '=', 1)
					->where('complete', '!=', null)
					->count();
				$inspected_units_file = UnitInspection::whereIn('unit_key', $unit_keys)
					->where('audit_id', '=', $audit->id)
					->where('group_id', '=', $program['group'])
					->where('is_file_audit', '=', 1)
					->where('complete', '!=', null)
					->count();
				$to_be_inspected_units_site = $program['totals_after_optimization'] - $inspected_units_site;
				$to_be_inspected_units_file = $program['totals_after_optimization'] - $inspected_units_file;
				// $data['programs'][] = [
				//     'id' => $program['group'],
				//     'name' => $program['name'],
				//     'pool' => $program['pool'],
				//     'comments' => $program['comments'],
				//     'user_limiter' => $program['use_limiter'],
				//     'totals_after_optimization' => $program['totals_after_optimization'],
				//     'units_before_optimization' => $program['units_before_optimization'],
				//     'totals_before_optimization' => $program['totals_before_optimization'],
				//     'required_units' => $program['totals_after_optimization'],
				//     'selected_units' => $selected_units_site,
				//     'needed_units' => $needed_units_site,
				//     'inspected_units' => $inspected_units_site,
				//     'to_be_inspected_units' => $to_be_inspected_units_site,
				//     'required_units_file' => $program['totals_after_optimization'],
				//     'selected_units_file' => $selected_units_file,
				//     'needed_units_file' => $needed_units_file,
				//     'inspected_units_file' => $inspected_units_file,
				//     'to_be_inspected_units_file' => $to_be_inspected_units_file,
				// ];
				//chartjs data
				$datasets[] = [
					'program_name' => $program['name'],
					'required' => $program['totals_after_optimization'],
					'selected' => $selected_units_site + $selected_units_file,
					'needed' => $needed_units_site + $needed_units_file,
				];
				// $summary_required = $summary_required + $program['totals_before_optimization'];
				// $summary_selected = $summary_selected + $selected_units_site;
				// $summary_needed = $summary_needed + $needed_units_site;
				// $summary_inspected = $summary_inspected + $inspected_units_site;
				// $summary_to_be_inspected = $summary_to_be_inspected + $to_be_inspected_units_site;
				// $summary_optimized_sample_size = $summary_optimized_sample_size + $program['totals_after_optimization'];
				// $summary_optimized_completed_inspections = $summary_optimized_completed_inspections + $inspected_units_site;
				// $summary_optimized_remaining_inspections = $summary_optimized_sample_size - $summary_optimized_completed_inspections;
				// $summary_required_file = $summary_required_file + $program['totals_before_optimization'];
				// $summary_selected_file = $summary_selected_file + $selected_units_file;
				// $summary_needed_file = $summary_needed_file + $needed_units_file;
				// $summary_inspected_file = $summary_inspected_file + $inspected_units_file;
				// $summary_to_be_inspected_file = $summary_to_be_inspected_file + $to_be_inspected_units_file;
				// $summary_optimized_sample_size_file = $summary_optimized_sample_size_file + $program['totals_after_optimization'];
				// $summary_optimized_completed_inspections_file = $summary_optimized_completed_inspections_file + $inspected_units_file;
				// $summary_optimized_remaining_inspections_file = $summary_optimized_sample_size_file - $summary_optimized_completed_inspections_file;
			}
		}
		/*
		$data['summary'] = [
		'required_units' => $summary_required,
		'selected_units' => $summary_selected,
		'needed_units' => $summary_needed,
		'inspected_units' => $summary_inspected,
		'to_be_inspected_units' => $summary_to_be_inspected,
		'optimized_sample_size' => $summary_optimized_sample_size,
		'optimized_completed_inspections' => $summary_optimized_completed_inspections,
		'optimized_remaining_inspections' => $summary_optimized_remaining_inspections,
		'required_units_file' => $summary_required_file,
		'selected_units_file' => $summary_selected_file,
		'needed_units_file' => $summary_needed_file,
		'inspected_units_file' => $summary_inspected_file,
		'to_be_inspected_units_file' => $summary_to_be_inspected_file,
		'optimized_sample_size_file' => $summary_optimized_sample_size_file,
		'optimized_completed_inspections_file' => $summary_optimized_completed_inspections_file,
		'optimized_remaining_inspections_file' => $summary_optimized_remaining_inspections_file,
		];
		 */

		$data = $this->getProjectDetailsInfo($project_id, 'compliance', $audit->id, 1);

		$send_project_details = [
			'audit' => $audit,
			'data' => $data,
			'datasets' => $datasets,
			'project' => $project,
			'programs' => $programs,
			'all_program_keys' => $all_program_keys,
		];

		return $send_project_details;
	}

	public function modalPMProjectProgramSummary($project_id, $program_id = 0, $audit_id = 0)
	{
		if (0 == $program_id) {
			// if program_id == 0 we display all the programs (Here these are actually gorups not programs!)
			// units are automatically selected using the selection process
			// then randomize all units before displaying them on the modal
			// then user can adjust selection for that program

			// get all the units in the selected audit

			$get_project_details = $this->projectSummaryComposite($project_id, $audit_id);
			collect($get_project_details['all_program_keys'])->flatten()->unique();
			$audit = $get_project_details['audit'];
			$data = $get_project_details['data'];
			$datasets = $get_project_details['datasets'];
			$project = $get_project_details['project'];
			$programs = $get_project_details['programs'];
			//@divyam We need to make it so the swap unit modal shows all units regardless of the unit status type and if there are no programs on it but the program exists on the project, show it as a substitution option. -NOT IMPLEMENTED YET 09/23/2019
			// $unit_ids = UnitGroup::where('audit_id', $audit->id)->get()->pluck('unit_id');
			$unitprograms = UnitProgram::where('audit_id', '=', $audit->id)
				->join('units', 'units.id', 'unit_programs.unit_id')
				->join('buildings', 'buildings.id', 'units.building_id')
			//->where('unit_id', 151063)
				->with('unit', 'program.relatedGroups', 'unit.building', 'unit.building.address', 'unitInspected', 'project_program')
				->orderBy('buildings.building_name', 'asc')
				->orderBy('units.unit_name', 'asc')
				->get();
			$actual_programs = $unitprograms->pluck('program')->unique()->toArray();
			$unitprograms = $unitprograms->groupBy('unit_id');
			foreach ($actual_programs as $key => $actual_program) {
				$group_names = array_column($actual_program['related_groups'], 'group_name');
				$group_ids = array_column($actual_program['related_groups'], 'id');
				if (!empty($group_names)) {
					$actual_programs[$key]['group_names'] = implode(', ', $group_names);
					$actual_programs[$key]['group_ids'] = $group_ids;
				} else {
					$actual_programs[$key]['group_names'] = ' - ';
					$actual_programs[$key]['group_ids'] = [];
				}
			}
			// return $unitprograms;
			return view('modals.project-summary-composite', compact('data', 'project', 'audit', 'programs', 'unitprograms', 'datasets', 'actual_programs'));
		} else {
			//dd($selection_summary['programs'][$program_id-1]);
			//
			$project = Project::where('id', '=', $project_id)->first();
			$audit = $project->selected_audit()->audit;
			$selection_summary = json_decode($audit->selection_summary, 1);
			session(['audit-' . $audit->id . '-selection_summary' => $selection_summary]);
			$programs = [];
			$program_keys_list = '';
			foreach ($selection_summary['programs'] as $p) {
				if ($p['pool'] > 0) {
					$programs[] = [
						'id' => $p['group'],
						'name' => $p['name'],
					];
					if ('' != $program_keys_list) {
						$program_keys_list = $program_keys_list . ',';
					}
					$program_keys_list = $program_keys_list . $p['program_keys'];
				}
			}

			$program = $selection_summary['programs'][$program_id - 1];

			// count selected units using the list of program ids
			$program_keys = explode(',', $program['program_keys']);
			$selected_units_site = UnitInspection::whereIn('program_key', $program_keys)->where('audit_id', '=', $audit->id)->where('group_id', '=', $program['group'])->where('is_site_visit', '=', 1)->select('unit_id')->groupBy('unit_id')->get()->count();
			$selected_units_file = UnitInspection::whereIn('program_key', $program_keys)->where('audit_id', '=', $audit->id)->where('group_id', '=', $program['group'])->where('is_file_audit', '=', 1)->select('unit_id')->groupBy('unit_id')->get()->count();

			$needed_units_site = $program['totals_after_optimization'] - $selected_units_site;
			$needed_units_file = $program['totals_after_optimization'] - $selected_units_file;

			$unit_keys = $program['units_after_optimization'];
			$inspected_units_site = UnitInspection::whereIn('unit_key', $unit_keys)
				->where('audit_id', '=', $audit->id)
				->where('group_id', '=', $program['group'])
			// ->whereHas('amenity_inspections', function($query) {
			//     $query->where('completed_date_time', '!=', null);
			// })
				->where('is_site_visit', '=', 1)
				->where('complete', '!=', null)
				->count();

			$inspected_units_file = UnitInspection::whereIn('unit_key', $unit_keys)
				->where('audit_id', '=', $audit->id)
				->where('group_id', '=', $program['group'])
				->where('is_file_audit', '=', 1)
				->where('complete', '!=', null)
				->count();

			$to_be_inspected_units_site = $program['totals_after_optimization'] - $inspected_units_site;
			$to_be_inspected_units_file = $program['totals_after_optimization'] - $inspected_units_file;

			$stats = [
				'id' => $program['group'],
				'name' => $program['name'],
				'pool' => $program['pool'],
				'totals_after_optimization' => $program['totals_after_optimization'],
				'units_before_optimization' => $program['units_before_optimization'],
				'totals_before_optimization' => $program['totals_before_optimization'],
				'required_units' => $program['totals_after_optimization'],
				'selected_units' => $selected_units_site,
				'needed_units' => $needed_units_site,
				'inspected_units' => $inspected_units_site,
				'to_be_inspected_units' => $to_be_inspected_units_site,
				'required_units_file' => $program['totals_after_optimization'],
				'selected_units_file' => $selected_units_file,
				'needed_units_file' => $needed_units_file,
				'inspected_units_file' => $inspected_units_file,
				'to_be_inspected_units_file' => $to_be_inspected_units_file,
			];

			//$units = $project->units;

			// only select programs that we cover in the groups
			$program_home_ids = explode(',', SystemSetting::get('program_home'));
			$program_medicaid_ids = explode(',', SystemSetting::get('program_medicaid'));
			$program_811_ids = explode(',', SystemSetting::get('program_811'));
			$program_bundle_ids = explode(',', SystemSetting::get('program_bundle'));
			$program_ohtf_ids = explode(',', SystemSetting::get('program_ohtf'));
			$program_nhtf_ids = explode(',', SystemSetting::get('program_nhtf'));
			$program_htc_ids = explode(',', SystemSetting::get('program_htc'));

			// TBD something is missing here. We selected all the programs for that units, ignoring the SystemSettings???

			$unitprograms = UnitProgram::where('audit_id', '=', $audit->id)->with('unit', 'program', 'unit.building.address')->orderBy('unit_id', 'asc')->get();

			$data = collect([
				'project' => [
					'id' => $project->id,
					'name' => $project->project_name,
					'selected_program' => $program_id,
				],
				'programs' => [
					['id' => 1, 'name' => 'Program Name 1'],
					['id' => 2, 'name' => 'Program Name 2'],
					['id' => 3, 'name' => 'Program Name 3'],
					['id' => 4, 'name' => 'Program Name 4'],
				],
				'units' => [
					[
						'id' => 1,
						'status' => 'not-inspectable',
						'address' => '123457 Silvegwood Street',
						'address2' => '#102',
						'move_in_date' => '1/29/2018',
						'programs' => [
							['id' => 1, 'name' => 'Program name 1', 'physical_audit_checked' => 'true', 'file_audit_checked' => 'false', 'selected' => '', 'status' => 'not-inspectable'],
							['id' => 2, 'name' => 'Program name 2', 'physical_audit_checked' => 'false', 'file_audit_checked' => 'true', 'selected' => '', 'status' => 'not-inspectable'],
						],
					],
					[
						'id' => 2,
						'status' => 'inspectable',
						'address' => '123457 Silvegwood Street',
						'address2' => '#102',
						'move_in_date' => '1/29/2018',
						'programs' => [
							['id' => 1, 'name' => 'Program name 1', 'physical_audit_checked' => '', 'file_audit_checked' => '', 'selected' => '', 'status' => 'inspectable'],
							['id' => 2, 'name' => 'Program name 2', 'physical_audit_checked' => '', 'file_audit_checked' => '', 'selected' => '', 'status' => 'not-inspectable'],
						],
					],
					[
						'id' => 2,
						'status' => 'inspectable',
						'address' => '123457 Silvegwood Street',
						'address2' => '#102',
						'move_in_date' => '1/29/2018',
						'programs' => [
							['id' => 1, 'name' => 'Program name 1', 'physical_audit_checked' => '', 'file_audit_checked' => '', 'selected' => '', 'status' => 'not-inspectable'],
							['id' => 2, 'name' => 'Program name 2', 'physical_audit_checked' => '', 'file_audit_checked' => '', 'selected' => '', 'status' => 'inspectable'],
						],
					],
					[
						'id' => 2,
						'status' => 'inspectable',
						'address' => '123457 Silvegwood Street',
						'address2' => '#102',
						'move_in_date' => '1/29/2018',
						'programs' => [
							['id' => 1, 'name' => 'Program name 1', 'physical_audit_checked' => 'true', 'file_audit_checked' => 'false', 'selected' => '', 'status' => 'inspectable'],
							['id' => 2, 'name' => 'Program name 2', 'physical_audit_checked' => 'false', 'file_audit_checked' => 'true', 'selected' => '', 'status' => 'inspectable'],
						],
					],
					[
						'id' => 2,
						'status' => 'inspectable',
						'address' => '123457 Silvegwood Street',
						'address2' => '#102',
						'move_in_date' => '1/29/2018',
						'programs' => [
							['id' => 1, 'name' => 'Program name 1', 'physical_audit_checked' => 'true', 'file_audit_checked' => 'false', 'selected' => '', 'status' => 'inspectable'],
							['id' => 2, 'name' => 'Program name 2', 'physical_audit_checked' => 'false', 'file_audit_checked' => 'true', 'selected' => '', 'status' => 'inspectable'],
						],
					],
					[
						'id' => 2,
						'status' => 'inspectable',
						'address' => '123457 Silvegwood Street',
						'address2' => '#102',
						'move_in_date' => '1/29/2018',
						'programs' => [
							['id' => 1, 'name' => 'Program name 1', 'physical_audit_checked' => 'true', 'file_audit_checked' => 'false', 'selected' => '', 'status' => 'inspectable'],
							['id' => 2, 'name' => 'Program name 2', 'physical_audit_checked' => 'false', 'file_audit_checked' => 'true', 'selected' => '', 'status' => 'not-inspectable'],
						],
					],
				],
			]);

			return view('modals.project-summary', compact('data', 'project', 'stats', 'programs', 'unitprograms'));
		}
	}

	public function getPMAssignmentAuditorCalendar($id, $auditorid, $currentdate, $beforeafter)
	{
		// from the current date and beforeafter, calculate new target date
		$created = Carbon\Carbon::createFromFormat('Ymd', $currentdate);
		if ('before' == $beforeafter) {
			$newdate = $created->subDays(9);

			$newdate_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->subDays(18)->format('F d, Y');
			$newdate_ref_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->subDays(18)->format('Ymd');
			$newdate_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('F d, Y');
			$newdate_ref_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('Ymd');

			$newdateref = $newdate->format('Ymd');
			$newdateformatted = $newdate->format('F d, Y');

			$header_dates = [];
			$header_dates[] = $newdate->subDays(4)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
		} else {
			$newdate = $created->addDays(9);

			$newdate_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('F d, Y');
			$newdate_ref_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('Ymd');
			$newdate_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->addDays(18)->format('F d, Y');
			$newdate_ref_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->addDays(18)->format('Ymd');

			$newdateref = $newdate->format('Ymd');
			$newdateformatted = $newdate->format('F d, Y');

			$header_dates = [];
			$header_dates[] = $newdate->subDays(4)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
			$header_dates[] = $newdate->addDays(1)->format('m/d');
		}
		// dd($header_dates);
		// dd($currentdate." - ".$created." - ".$newdate." - ".$newdateformatted." - ".$newdateref);
		$data = collect([
			'project' => [
				'id' => $id,
				'name' => 'Project Name',
			],
			'summary' => [
				'id' => $auditorid,
				'name' => 'Jane Doe',
				'initials' => 'JD',
				'color' => 'blue',
				'date' => $newdateformatted,
				'ref' => $newdateref,
			],
			'calendar' => [
				'header' => $header_dates,
				'content' => [
					[
						'id' => 111,
						'date' => '12/18',
						'no_availability' => 0,
						'start_time' => '08:00 AM',
						'end_time' => '05:30 PM',
						'before_time_start' => '1',
						'before_time_span' => '8',
						'after_time_start' => '46',
						'after_time_span' => '15',
						'events' => [
							[
								'id' => 112,
								'status' => 'action-required',
								'start' => '9',
								'span' => '24',
								'icon' => 'a-mobile-not',
								'lead' => 2,
								'class' => '',
								'modal_type' => '',
							],
							[
								'id' => 113,
								'status' => 'breaktime',
								'start' => '33',
								'span' => '2',
								'icon' => '',
								'lead' => 1,
								'class' => '',
								'modal_type' => '',
							],
							[
								'id' => 114,
								'status' => '',
								'start' => '35',
								'span' => '11',
								'icon' => 'a-mobile-checked',
								'lead' => 1,
								'class' => 'no-border-bottom',
								'modal_type' => '',
							],
						],
					],
					[
						'id' => 112,
						'date' => '12/19',
						'no_availability' => 0,
						'start_time' => '08:00 AM',
						'end_time' => '05:30 PM',
						'before_time_start' => '1',
						'before_time_span' => '8',
						'after_time_start' => '46',
						'after_time_span' => '15',
						'events' => [
							[
								'id' => 112,
								'status' => '',
								'start' => '9',
								'span' => '12',
								'icon' => 'a-mobile-not',
								'lead' => 2,
								'class' => '',
								'modal_type' => '',
							],
							[
								'id' => 113,
								'status' => 'breaktime',
								'start' => '21',
								'span' => '1',
								'icon' => '',
								'lead' => 1,
								'class' => '',
								'modal_type' => '',
							],
							[
								'id' => 114,
								'status' => '',
								'start' => '22',
								'span' => '24',
								'icon' => 'a-circle-plus',
								'lead' => 1,
								'class' => 'available no-border-top no-border-bottom',
								'modal_type' => 'choose-filing',
							],
						],
					],
					[
						'id' => 113,
						'date' => '12/20',
						'no_availability' => 0,
						'start_time' => '08:00 AM',
						'end_time' => '05:30 PM',
						'before_time_start' => '1',
						'before_time_span' => '8',
						'after_time_start' => '46',
						'after_time_span' => '15',
						'events' => [
							[
								'id' => 112,
								'status' => 'action-required',
								'start' => '9',
								'span' => '12',
								'icon' => 'a-mobile-not',
								'lead' => 1,
								'class' => '',
								'modal_type' => '',
							],
							[
								'id' => 113,
								'status' => 'breaktime',
								'start' => '21',
								'span' => '4',
								'icon' => '',
								'lead' => 1,
								'class' => '',
								'modal_type' => '',
							],
							[
								'id' => 114,
								'status' => '',
								'start' => '25',
								'span' => '21',
								'icon' => 'a-circle-plus',
								'lead' => 1,
								'class' => 'available no-border-top no-border-bottom',
								'modal_type' => 'choose-filing',
							],
						],
					],
					[
						'id' => 115,
						'date' => '12/21',
						'no_availability' => 0,
						'start_time' => '08:00 AM',
						'end_time' => '05:30 PM',
						'before_time_start' => '1',
						'before_time_span' => '8',
						'after_time_start' => '46',
						'after_time_span' => '15',
						'events' => [
							[
								'id' => 112,
								'status' => '',
								'start' => '9',
								'span' => '16',
								'icon' => 'a-circle-plus',
								'lead' => 1,
								'class' => 'available no-border-top',
								'modal_type' => 'choose-filing',
							],
							[
								'id' => 113,
								'status' => '',
								'start' => '30',
								'span' => '16',
								'icon' => 'a-circle-plus',
								'lead' => 1,
								'class' => 'available no-border-bottom',
								'modal_type' => 'choose-filing',
							],
						],
					],
					[
						'id' => 116,
						'date' => '12/22',
						'no_availability' => 0,
						'start_time' => '08:00 AM',
						'end_time' => '05:30 PM',
						'before_time_start' => '1',
						'before_time_span' => '8',
						'after_time_start' => '46',
						'after_time_span' => '15',
						'events' => [
							[
								'id' => 112,
								'status' => 'in-progress',
								'start' => '9',
								'span' => '16',
								'icon' => 'a-mobile-checked',
								'lead' => 1,
								'class' => '',
								'modal_type' => 'change-date',
							],
							[
								'id' => 113,
								'status' => 'breaktime',
								'start' => '25',
								'span' => '1',
								'icon' => '',
								'lead' => 1,
								'class' => '',
								'modal_type' => '',
							],
							[
								'id' => 113,
								'status' => '',
								'start' => '26',
								'span' => '12',
								'icon' => 'a-folder',
								'lead' => 2,
								'class' => '',
								'modal_type' => '',
							],
							[
								'id' => 113,
								'status' => '',
								'start' => '38',
								'span' => '8',
								'icon' => 'a-folder',
								'lead' => 1,
								'class' => 'no-border-bottom',
								'modal_type' => '',
							],
						],
					],
					[
						'id' => 114,
						'date' => '12/23',
						'no_availability' => 1,
					],
					[
						'id' => 114,
						'date' => '12/24',
						'no_availability' => 1,
					],
					[
						'id' => 114,
						'date' => '12/25',
						'no_availability' => 1,
					],
					[
						'id' => 114,
						'date' => '12/26',
						'no_availability' => 1,
					],
				],
				'footer' => [
					'previous' => $newdate_previous,
					'ref-previous' => $newdate_ref_previous,
					'today' => $newdateformatted,
					'next' => $newdate_next,
					'ref-next' => $newdate_ref_next,
				],
			],
		]);

		return view('projects.partials.details-assignment-auditor-calendar', compact('data'));
	}

	public function pm_reload_auditors($audit_id, $unit_id, $building_id)
	{
		if (null != $unit_id && null != $building_id && 'null' != $unit_id && 'null' != $building_id) {
			$unit_auditor_ids = AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit_id)->whereNotNull('auditor_id')->whereNotNull('unit_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();

			$building_auditor_ids = [];
			$units = Unit::where('building_id', '=', $building_id)->get();
			foreach ($units as $unit) {
				$building_auditor_ids = array_merge($building_auditor_ids, \App\Models\AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit->id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
			}
		} else {
			if (0 == $building_id && 0 == $unit_id) {
				$unit_auditor_ids = [];
				$building_auditor_ids = [];
			} else {
				$unit_auditor_ids = [];

				$building_auditor_ids = [];
				$units = Unit::where('building_id', '=', $building_id)->get();
				foreach ($units as $unit) {
					$unit_auditor_ids = array_merge($unit_auditor_ids, AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit_id)->whereNotNull('auditor_id')->whereNotNull('unit_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());

					$building_auditor_ids = array_merge($building_auditor_ids, \App\Models\AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit->id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
				}
				$building_auditor_ids = array_merge($building_auditor_ids, AmenityInspection::where('audit_id', '=', $audit_id)->where('building_id', '=', $building_id)->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
			}
		}

		$unit_auditors = User::whereIn('id', $unit_auditor_ids)->get();
		foreach ($unit_auditors as $unit_auditor) {
			$unit_auditor->full_name = $unit_auditor->full_name();
			$unit_auditor->initials = $unit_auditor->initials();
		}
		$building_auditors = User::whereIn('id', $building_auditor_ids)->get();
		foreach ($building_auditors as $building_auditor) {
			$building_auditor->full_name = $building_auditor->full_name();
			$building_auditor->initials = $building_auditor->initials();
		}

		return ['unit_auditors' => $unit_auditors, 'building_auditors' => $building_auditors];
	}

	public function pmAjaxAuditRequiredUnits(Request $request)
	{
		// return $request->building_key;
		$audit_id = $request->post('id');
		$name = $request->post('name');
		$req_type = $request->post('req_type');
		if ('required_units' == $req_type) {
			$requiredTypeDisplay = 'physical inspection';
		}
		if ('required_units_file' == $req_type) {
			$requiredTypeDisplay = 'file inspection';
		}
		$value = $request->post('req_val');
		$audit_details = Audit::where('id', '=', $audit_id)->first();
		$selection_summary = json_decode($audit_details->selection_summary, 1);
		if (null !== $selection_summary) {
			foreach ($selection_summary['programs'] as $key => $program) {
				$selection_summary['programs'][$key]['name'];
				//need to check if building exists
				if (null != $request->building_key && '' != $request->building_key) {
					$has_building = 1;
				} else {
					$has_building = 0;
				}
				if ($selection_summary['programs'][$key]['name'] == $name && !$has_building) {
					@$selection_summary['programs'][$key]['comments'][] = date('m/d/Y h:i:s a', time()) . ': ' . Auth::user()->name . ' ( user id: ' . Auth::user()->id . ' ) changed the ' . $requiredTypeDisplay . ' required amount from ' . $selection_summary['programs'][$key][$req_type] . ' to ' . $value;
					@$selection_summary['programs'][$key][$req_type] = $value;
				} elseif ($selection_summary['programs'][$key]['name'] == $name && $has_building && $selection_summary['programs'][$key]['building_key'] == $request->building_key) {
					@$selection_summary['programs'][$key]['comments'][] = date('m/d/Y h:i:s a', time()) . ': ' . Auth::user()->name . ' ( user id: ' . Auth::user()->id . ' ) changed the ' . $requiredTypeDisplay . ' required amount from ' . $selection_summary['programs'][$key][$req_type] . ' to ' . $value;
					@$selection_summary['programs'][$key][$req_type] = $value;
				}
			}
			$audit_details->selection_summary = json_encode($selection_summary);
			if ($audit_details->save()) {
				return 'true';
			} else {
				return 'false';
			}
			// dd($selection_summary['programs']);
		}
	}

	public function pmHouseholdInfo($unit)
	{
		$unit = Unit::where('id', $unit)->with('household')->first();

		return view('modals.audit_details.householdInfo', compact('unit'));
	}

	public function pmCachedAuditCheck(Request $request)
	{
		if ($request->audits) {
			$audits = json_decode($request->audits);
			//dd($audits);
			foreach ($audits as $audit) {
				//dd($audit[0],$audit[1]);
				$auditCheck = CachedAudit::select('audit_id')->where('audit_id', $audit[0])->where('updated_at', '>', date('Y-m-d H:i:s', strtotime($audit[1])))->pluck('audit_id');
				if (count($auditCheck)) {
					return json_encode($auditCheck);
				}
			}
			return 0;
		}
	}

	public function pmSingleCachedAudit($audit_id)
	{
		$audit = CachedAudit::where('audit_id', intval($audit_id))->first();
		$auditor_access = Auth::user()->auditor_access();
		$audits = $audit->toArray();
		if (null !== $audit) {
			return view('dashboard.partials.pm_audit_row', compact('audit', 'auditor_access', 'audits'));
		} else {
			return 0;
		}
	}

	public function pmBuildingsFromAudit($audit, Request $request)
	{
		$target = $request->get('target');
		$context = $request->get('context');

		// check if user can see that audit TBD
		//

		// start by checking each cached_building and make sure there is a clear link to amenity_inspection records if this is a building-level amenity
		// return CachedBuilding::first();
		$buildings = CachedBuilding::where('audit_id', '=', $audit)->orderBy('amenity_id', 'desc')->orderBy('building_name', 'asc')->get();
		//dd($buildings);

		//// Optimized code...
		if (count($buildings)) {
			// $duplicates = []; // to store amenity_inspection_ids for each amenity_id to see when we have duplicates
			// $previous_name = []; // used in case we have building-level amenity duplicates

			// foreach ($buildings as $building) {
			// 	if (null === $building->building_id && null === $building->amenity_inspection_id) {
			// 		// this is a building-level amenity without a clear link to the amenity inspection
			// 		// we need to add amenity_inspection_id

			// 		// first there may already be an amenity_inspection with cachedbuilding_id
			// 		$amenity_inspection = AmenityInspection::where('audit_id', '=', $audit)
			// 			->where('amenity_id', '=', $building->amenity_id)
			// 			->whereNull('building_id')
			// 			->where('cachedbuilding_id', '=', $building->id)
			// 			->first();
			// 		if ($amenity_inspection) {
			// 			$building->amenity_inspection_id = $amenity_inspection->id;
			// 			$building->save();

			// 			// $amenity_inspection->cachedbuilding_id = $building->id;
			// 			// $amenity_inspection->save();
			// 		} else {
			// 			$amenity_inspection = AmenityInspection::where('audit_id', '=', $audit)
			// 				->where('amenity_id', '=', $building->amenity_id)
			// 				->whereNull('building_id')
			// 				->whereNull('cachedbuilding_id')
			// 				->first();
			// 			if ($amenity_inspection) {
			// 				$building->amenity_inspection_id = $amenity_inspection->id;
			// 				$building->save();

			// 				$amenity_inspection->cachedbuilding_id = $building->building->id;
			// 				//$amenity_inspection->cachedbuilding_id = $building->id;
			// 				$amenity_inspection->save();
			// 			}
			// 		}
			// 		//dd($building, $amenity_inspection);
			// 	} elseif (null === $building->building_id && null !== $building->amenity_inspection_id && $building->amenity() === null) {
			// 		// we had a case where a amenity_inspection_id was referring to a record on the wrong audit
			// 		$building->amenity_inspection_id = null;
			// 		$building->save();
			// 	}
			// 	if ($building && isset($updateRow)) {
			// 		$building->recount_findings();
			// 	}
			// 	if (null === $building->building_id && $building->amenity_id && null === $building->amenity_inspection_id) {
			// 		// this is an amenity with no link to the amenity inspection -> there might be issues in case of duplicates.

			// 		$amenity_id = $building->amenity_id;
			// 		$audit_id = $building->audit_id;

			// 		// we look to see if amenityinspection has a record for this amenity
			// 		if (!array_key_exists($amenity_id, $duplicates)) {
			// 			$duplicates[$amenity_id] = [];
			// 		}

			// 		$cached_building = CachedBuilding::where('audit_id', '=', $audit_id)
			// 			->where('amenity_id', '=', $amenity_id)
			// 			->whereNotIn('amenity_inspection_id', $duplicates[$amenity_id])
			// 			->first();

			// 		if ($cached_building) {
			// 			$duplicates[$amenity_id][] = $cached_building->amenity_inspection_id;
			// 			$building->amenity_inspection_id = $cached_building->amenity_inspection_id;
			// 			$building->save();
			// 		}
			// 	}
			// 	if (null === $building->building_id && $building->building) {
			// 		// naming duplicates should only apply to amenities
			// 		if (!array_key_exists($building->building->building_name, $previous_name)) {
			// 			$previous_name[$building->building->building_name]['counter'] = 1; // counter
			// 			$first_encounter = $building->building;
			// 		} else {
			// 			if (1 == $previous_name[$building->building->building_name]['counter']) {
			// 				// this is our second encounter, change the first one since we now know there are more
			// 				$first_encounter->building_name = $first_encounter->building_name . ' 1';
			// 			}

			// 			$previous_name[$building->building->building_name]['counter'] = $previous_name[$building->building->building_name]['counter'] + 1;
			// 			$building->building->building_name = $building->building->building_name . ' ' . $previous_name[$building->building->building_name]['counter'];
			// 		}
			// 	}
			// }
		}

		$amenities_query = AmenityInspection::where('audit_id', $audit)->with('amenity', 'user', 'building.units');
		$amenities = $amenities_query->get();

		return view('dashboard.partials.pm_audit_buildings', compact('audit', 'target', 'buildings', 'context', 'amenities'));
	}

	public function getPMBuildingDetailsInfo(Request $request, $id, $type, $audit)
	{
		$type_id = $request->post('type_id');
		$name = $request->post('name');
		$is_uncorrected = $request->post('is_uncorrected');

		if ($type == 'all') {
			Session::forget('type_id');
			Session::forget('name');
			Session::forget('is_uncorrected');
			return 1;
		}

		if (!empty($type_id)) {
			Session::put('type_id', $type_id);
			Session::put('name', $name);
		} else {
			Session::forget('type_id');
			Session::forget('name');
		}
		if ($is_uncorrected == 'true') {
			Session::put('is_uncorrected', $is_uncorrected);
		} else {
			Session::forget('is_uncorrected');
		}

		// dd($request->all());

		// types:building, unit
		// project: project_id?
		// type_id: building or unit id
		$project = Project::where('id', '=', $id)->first();
		$audit = CachedAudit::with('auditors', 'audit', 'lead_auditor')->where('audit_id', $audit)->first();
		$current_user = Auth::user();
		$manager_access = $current_user->manager_access();
		$details = $project->details();

		$dpView = 1;
		$findings = $audit->audit->findings->where('cancelled_at', NULL);
		$print = null;
		$report = $audit;
		$detailsPage = 1;
		switch ($type) {
			case 'building':
				$allBuildingInspections = $audit->audit->building_inspections;

				$selected_audit = $audit;
				if (session()->has('type_id') && session()->has('is_uncorrected')) {
					$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
					$result = array_intersect($bulidingUnresolved, $type_id);
					// print_r($type_id);
					// print_r($bulidingUnresolved);
					// print_r($result);
					$inspections = $audit->audit->building_inspections()->whereIn('building_id', $result)->paginate(12);
				} else if (session()->has('is_uncorrected')) {
					$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
					// print_r($bulidingUnresolved);

					$inspections = $audit->audit->building_inspections()->whereIn('building_id', $bulidingUnresolved)->paginate(12);
				}
				// if(session()->has('type_id') && session()->has('is_uncorrected')){
				// 	$inspections = $audit->audit->building_inspections()->whereIn('building_id',$type_id)->paginate(10);
				// }else
				else if (session()->has('type_id')) {
					$inspections = $audit->audit->building_inspections()->whereIn('building_id', $type_id)->paginate(12);
				}
				// else if(!session()->has('type_id') && session()->has('is_uncorrected')){
				// 	$inspections = $audit->audit->building_inspections()->paginate(12);
				// }
				else {
					$inspections = $audit->audit->building_inspections()->paginate(12);
				}

				return view('crr_parts.pm_crr_inspections_building', compact('inspections', 'allBuildingInspections', 'dpView', 'findings', 'print', 'report', 'selected_audit', 'detailsPage'));
				break;
			case 'unit':
				$allUnitInspections = $audit->audit->unit_inspections;
				if (session()->has('type_id') && session()->has('is_uncorrected')) {
					$allBuildingInspections = $audit->audit->building_inspections;
					$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
					$result = array_intersect($bulidingUnresolved, $type_id);
					$unitUnresolvedId = $audit->audit->unitUnResolved($allUnitInspections, $findings);
					// print_r($type_id);
					// print_r($bulidingUnresolved);
					// print_r($result);
					$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->whereIn('building_id', $result)->whereIn('unit_id', $unitUnresolvedId)->with('documents')->paginate(12);
				} else if (session()->has('is_uncorrected')) {
					$allUnitInspections1 = $audit->audit->unit_inspections()->groupBy('unit_id')->get();
					// echo count($allUnitInspections);exit;
					$unitUnresolvedId = $audit->audit->unitUnResolved($allUnitInspections1, $findings);

					$inspections = $audit->audit->unit_inspections()->whereIn('unit_id', $unitUnresolvedId)->with('documents')->paginate(12);
				} else if (session()->has('type_id')) {
					$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->whereIn('building_id', $type_id)->with('documents')->paginate(12);
				} else {
					$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->with('documents')->paginate(12);
				}

				return view('crr_parts.pm_crr_inspections_unit', compact('inspections', 'allUnitInspections', 'dpView', 'print', 'report', 'findings', 'detailsPage', 'audit'));
				break;
			default:
		}
	}
}
