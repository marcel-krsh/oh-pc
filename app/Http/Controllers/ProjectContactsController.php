<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Project;
use App\Models\ReportAccess;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class ProjectContactsController extends Controller
{
  public function __construct(Request $request)
  {
    // $this->middleware('auth');
  }

  public function contacts($project)
  {
    $user_ids = $this->allUserIdsInProject($project);
    $project_user_ids = $this->projectUserIds($project);
    $report_user_ids = $this->allitaUserIds($project);
    $project  = Project::with('contactRoles.person')->find($project); //DEVCO
    // Check if they have Devco, else check allita -
    // Test with Charlene Wray

    // REmove this code later
    /*if(count($user_ids) == 0) {
    $cr         = $project->contactRoles;
    $person_ids = $cr->pluck('person_id');
    $project_id = $project->id;
    foreach ($person_ids as $key => $recipient) {
    $user = User::where('person_id', $recipient)->first();
    $check_user = ReportAccess::where('project_id', $project_id)->where('user_id', $user->id)->get();
    if (count($check_user) == 0) {
    $report_user             = new ReportAccess;
    $report_user->project_id = $project_id;
    $report_user->user_id    = $user->id;
    $report_user->save();
    }
    }
    }*/
    //end remove

   $users = User::whereIn('users.id', $user_ids)->
      join('people', 'users.person_id', '=', 'people.id')->
      leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')->
      leftJoin('roles', 'users_roles.role_id', '=', 'roles.id')->
      leftJoin('organizations', 'users.organization_id', 'organizations.id')->
      leftJoin('addresses', 'organizations.default_address_id', 'addresses.id')->
      leftJoin('phone_numbers', 'organizations.default_phone_number_id', 'phone_numbers.id')->
      select('users.*', 'line_1', 'line_2', 'city', 'state', 'zip', 'organization_name', 'role_id', 'role_name', 'area_code', 'phone_number', 'extension', 'last_name', 'first_name')->
      orderBy('last_name', 'asc')->
      paginate(25);
    return view('projects.partials.contacts', compact('users', 'user_role', 'project', 'project_user_ids', 'report_user_ids'));
  }

  protected function projectUserIds($project_id)
  {
  	$project = Project::with('contactRoles.person')->find($project_id); //DEVCO
    // Check if they have Devco, else check allita -
    // Test with Charlene Wray
    if ($project->contactRoles) {
      $project_person_ids = $project->contactRoles->pluck('person_id');
      $project_user_ids   = User::whereIn('person_id', $project_person_ids)->pluck('id')->toArray();
    } else {
      $project_user_ids = [];
    }
    return $project_user_ids;
  }

  protected function allitaUserIds($project_id)
  {
  	$report_user_ids = ReportAccess::where('project_id', $project_id)->get()->pluck('user_id')->toArray(); //Allita
  	return $report_user_ids;
  }

  protected function allUserIdsInProject($project_id)
  {
    $project_user_ids = $this->projectUserIds($project_id);
    $report_user_ids = $this->allitaUserIds($project_id);
    $user_ids        = array_merge($project_user_ids, $report_user_ids);
    return $user_ids;
  }

  public function addUserToProject($project_id)
  {
    if (Auth::user()->manager_access()) {
      $roles         = Role::where('id', '<', 2)->active()->orderBy('role_name', 'ASC')->get();
      $organizations = Organization::active()->orderBy('organization_name', 'ASC')->get();
      $states        = State::get();
      $user_ids      = $this->allUserIdsInProject($project_id);
      $recipients    = User::whereNotIn('users.id', $user_ids)
        ->join('people', 'people.id', 'users.person_id')
        ->leftJoin('organizations', 'organizations.id', 'users.organization_id')
        ->join('users_roles', 'users_roles.user_id', 'users.id')
        ->select('users.*', 'last_name', 'first_name', 'organization_name')
        ->where('active', 1)
        ->orderBy('organization_name', 'asc')
        ->orderBy('last_name', 'asc')
        ->get();
      return view('modals.add-user-to-project', compact('roles', 'organizations', 'states', 'recipients', 'project_id'));
    } else {
      $tuser = Auth::user();
      $lc    = new LogConverter('user', 'unauthorized createuser');
      $lc->setDesc($tuser->email . ' Attempted to create a new user ')->setFrom($tuser)->setTo($tuser)->save();
      return 'Sorry you do not have access to this page.';
    }
  }

  public function saveAddUserToProject($project_id, Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'recipients_array' => 'required',
    ], [
      'recipients_array.required' => 'Select atleast one user',
    ]);
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    }
    if ('' == $project_id || is_null($project_id)) {
      return response()->json(['errors' => ['Something went wrong, contact admin']]);
    }
    $recipients_array = $request->recipients_array;
    foreach ($recipients_array as $key => $recipient) {
      $check_user = ReportAccess::where('project_id', $project_id)->where('user_id', $recipient)->get();
      if (count($check_user) == 0) {
        $report_user             = new ReportAccess;
        $report_user->project_id = $project_id;
        $report_user->user_id    = $recipient;
        $report_user->save();
      }
    }
    return 1;
  }

  public function removeUserFromProject($project_id, $user_id)
  {
    $user_access = ReportAccess::where('project_id', $project_id)->where('user_id', $user_id)->first();
    if ($user_access) {
      $message = 'Are you sure you want to remove access';
      $status  = 1;
    } else {
      $message = 'Something went wrong, contact admin';
      $status  = 0;
    }
    return view('modals.remove-user-from-project', compact('project_id', 'user_id', 'message', 'status'));
  }

  public function deleteAddUserToProject($project_id, Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'project_id' => 'required',
      'user_id'    => 'required',
    ]);
    if ($validator->fails()) {
      return response()->json(['errors' => ['Something went wrong, contact admin']]);
    }
    $user_access = ReportAccess::where('project_id', $request->project_id)->where('user_id', $request->user_id)->delete();
    return 1;
  }
}
