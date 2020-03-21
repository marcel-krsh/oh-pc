<?php

namespace App\Http\Controllers;

use \Auth;
use \View;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public $user;
	public $pm_access;
	public $auditor_access;
	public $manager_access;
	public $admin_access;
	public $root_access;

	public function allitaPc()
	{
		//dd('hello world');
		$this->middleware('allita.auth');
		$this->middleware(function ($request, $next) {
			$this->user = Auth::user()->load('roles');
			//dd('My life as a user',$this->user);
			if ($this->user != null) {
				$this->auditor_access = $this->access($this->user, 'auditor');
				// $this->auditor_access = $this->user->auditor_access();
				View::share('auditor_access', $this->auditor_access);

				$this->pm_access = $this->access($this->user, 'pm');
				// $this->pm_access = $this->user->pm_access();
				View::share('pm_access', $this->pm_access);

				$this->manager_access = $this->access($this->user, 'manager');
				// $this->manager_access = $this->user->manager_access();
				View::share('manager_access', $this->manager_access);

				$this->admin_access = $this->access($this->user, 'admin');
				// $this->admin_access = $this->user->admin_access();
				View::share('admin_access', $this->admin_access);

				$this->root_access = $this->access($this->user, 'root');
				// $this->root_access = $this->user->root_access();
				View::share('root_access', $this->root_access);

				if (!$this->auditor_access) {
					// the following helpers provide the step_ids for
					// audits that determine property managers ability
					// to see file inspections, site inspections, both and
					// findings for an audit - this is a system setting on
					// the database. View helpers.php to get more details
					// Setting them here reduces the number of database queries.
					// The View::share() passes these values to the views for use
					// again to avoid extra calls to the database
					$this->pmFileInspectionsOnlyStepIds = pmCanViewFileInspectionIds();
					View::share('pmFileInspectionsOnlyStepIds', $this->pmFileInspectionsOnlyStepIds);
					$this->pmSiteInspectionsOnlyStepIds = pmCanViewSiteInspectionIds();
					View::share('pmSiteInspectionsOnlyStepIds', $this->pmSiteInspectionsOnlyStepIds);
					$this->pmBothInspectionsOnlyStepIds = pmCanViewBothInspectionIds();
					View::share('pmBothInspectionsOnlyStepIds', $this->pmBothInspectionsOnlyStepIds);
					$this->pmCanViewAuditStepIds = pmCanViewAuditIds();
					View::share('pmCanViewAuditStepIds', $this->pmCanViewAuditStepIds);
					$this->pmCanViewFindingsStepIds = pmCanViewFindingsIds();
					View::share('pmCanViewFindingsStepIds', $this->pmCanViewFindingsStepIds);
					View::share('current_user', $this->user);
				}
			}
			return $next($request);
		});
	}

	public function access($user, $which_role = null)
	{
		$role_id = 0;
		if ($which_role == 'pm') {
			$role_id = 1;
		}
		if ($which_role == 'auditor') {
			$role_id = 2;
		}
		if ($which_role == 'manager') {
			$role_id = 3;
		}
		if ($which_role == 'admin') {
			$role_id = 4;
		}
		if ($which_role == 'root') {
			$role_id = 5;
		}
		foreach ($user->roles as $role) {
			if ($role->role_id >= $role_id) {
				return true;
			}
		}
		return false;
	}
}
