<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Organization;
use App\Models\Project;
use App\Models\ReportAccess;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use App\Models\UserAddresses;
use App\Models\UserOrganization;
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
    $user_ids         = $this->allUserIdsInProject($project);
    $project_user_ids = $this->projectUserIds($project);
    $report_user_ids  = $this->allitaUserIds($project);
    $default_user_id  = ReportAccess::where('project_id', $project)->where('default', 1)->first();
    $project          = Project::with('contactRoles.person.user')->find($project); //DEVCO
    // return $project->details();
    if ($default_user_id) {
      $default_user_id = $default_user_id->user_id;
    } else {
      $default_user = $project->contactRoles->where('project_role_key', 21)->first();
      if ($default_user) {
        $default_user_id = $default_user->person->user->id;
      } else {
        $default_user_id == 0;
      }
    }

    // replace joins with relationship
    $users        = User::whereIn('id', $user_ids)->with('role', 'person', 'organization_details', 'user_addresses.address', 'user_organizations.organization', 'report_access')->orderBy('name')->get(); //->paginate(25);
    $default_org  = $users->pluck('user_organizations')->filter()->flatten()->where('default', 1)->count();
    $default_addr = $users->pluck('user_addresses')->filter()->flatten()->where('default', 1)->count();
    return view('projects.partials.contacts', compact('users', 'user_role', 'project', 'project_user_ids', 'report_user_ids', 'default_user_id', 'default_org', 'default_addr'));

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
    $report_user_ids = ReportAccess::where('project_id', $project_id)->where('devco', '!=', 1)->get()->pluck('user_id')->toArray(); //Allita
    return $report_user_ids;
  }

  protected function allUserIdsInProject($project_id)
  {
    $project_user_ids = $this->projectUserIds($project_id);
    $report_user_ids  = $this->allitaUserIds($project_id);
    $user_ids         = array_merge($project_user_ids, $report_user_ids);
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

  public function addOrganizationToUser($user_id, $project_id)
  {
    $user       = User::with('role', 'person', 'organization_details', 'addresses', 'user_organizations.organization')->find($user_id);
    $allita_org = [];
    $devco_orgs = [];
    if ($user->user_organizations) {
      $allita_org = $user->user_organizations->pluck('organization_id')->toArray();
    }
    if ($user->organization_details) {
      $devco_orgs = [$user->organization_id];
    }
    $existing_orgs = array_merge($allita_org, $devco_orgs);
    $organizations = Organization::whereNotIn('id', $existing_orgs)->active()->orderBy('organization_name', 'ASC')->pluck('organization_name', 'id');
    return view('modals.user-project-organization', compact('organizations', 'user', 'project_id'));
  }

  public function saveOrganizationToUser($user_id, Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'organization_id' => 'required',
    ], [
      'organization_id.required' => 'Organization field is required',
    ]);
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    }
    $uo                  = new UserOrganization;
    $uo->organization_id = $request->organization_id;
    $uo->user_id         = $user_id;
    $uo->project_id      = $request->project_id;
    $uo->save();
    return 1;
  }

  public function editOrganizationOfUser($org_id, $project_id)
  {
    $uo = UserOrganization::with('organization', 'user')->find($org_id);
    return view('modals.edit-organization-of-user', compact('uo', 'project_id'));
  }

  public function saveOrganizationOfUser($org_id, Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'organization_id'   => 'required',
      'organization_name' => 'required',
    ], [
      'organization_id.required' => 'Something went wrong, please contact admin',
    ]);
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    }
    $org                    = Organization::find($request->organization_id);
    $org->organization_name = $request->organization_name;
    $org->save();
    return 1;
  }

  public function removeOrganizationOfUser($org_id, Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'organization_id' => 'required',
    ], [
      'organization_id.required' => 'Something went wrong, please contact admin',
    ]);
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    }
    $org = UserOrganization::find($request->organization_id);
    $org->delete();
    return 1;
  }

  public function defaultOrganizationOfUserForProject(Request $request)
  {
    //return $request->all();
    $validator = \Validator::make($request->all(), [
      'project_id'      => 'required',
      'organization_id' => 'required',
      'user_id'         => 'required',
    ]);
    if ($validator->fails()) {
      return 'Something went wrong, please contact admin';
    }
    $selected_org = $request->organization_id;
    // Check if it is devco user and exists in project orgs
    if ($request->devco_org) {
      $devco_organization = UserOrganization::where('project_id', $request->project_id)
        ->where('organization_id', $request->organization_id)
        ->where('user_id', $request->user_id)
        ->where('devco', 1)
        ->first();
      if ($devco_organization) {
        $selected_org = $devco_organization->id;
      } else {
        $uo                  = new UserOrganization;
        $uo->organization_id = $request->organization_id;
        $uo->user_id         = $request->user_id;
        $uo->project_id      = $request->project_id;
        $uo->devco           = $request->devco_org;
        $uo->save();
        $selected_org = $uo->id;
      }
    }
    $orgs = UserOrganization::where('project_id', $request->project_id)->get();
    foreach ($orgs as $key => $org) {
      if ($org->id == $selected_org) {
        $org->default = 1;
      } else {
        $org->default = 0;
      }
      $org->save();
    }
    return 1;
  }

  public function editNameOfUser($user_id, $project_id)
  {
    $user = User::find($user_id);
    return view('modals.edit-name-of-user', compact('user', 'project_id'));
  }

  public function saveNameOfUser($user_id, Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'user_id'   => 'required',
      'user_name' => 'required',
    ], [
      'user_id.required' => 'Something went wrong, please contact admin',
    ]);
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    }
    $user       = User::find($request->user_id);
    $user->name = $request->user_name;
    $user->save();
    return 1;
  }

  public function defaultUserForProject(Request $request)
  {
    //return $request->all();
    $validator = \Validator::make($request->all(), [
      'project_id' => 'required',
      'user_id'    => 'required',
    ], [
      'user_id.required'    => 'Something went wrong, please contact admin',
      'project_id.required' => 'Something went wrong, please contact admin',
    ]);
    if ($validator->fails()) {
      return 'Something went wrong, please contact admin';
      // return response()->json(['errors' => $validator->errors()->all()]);
    }
    $selected_user = $request->user_id;
    // Check if it is devco org and exists in ReportAccess
    if ($request->devco) {
      $devco_user = ReportAccess::where('project_id', $request->project_id)
        ->where('user_id', $request->user_id)
        ->where('devco', 1)
        ->first();
      if ($devco_user) {
        $selected_user = $devco_user->user_id;
      } else {
        $ra             = new ReportAccess;
        $ra->user_id    = $request->user_id;
        $ra->project_id = $request->project_id;
        $ra->devco      = $request->devco;
        $ra->save();
        $selected_user = $ra->user_id;
      }
    }
    $defaults = ReportAccess::where('project_id', $request->project_id)->get();
    foreach ($defaults as $key => $default) {
      if ($default->user_id == $selected_user) {
        $default->default = 1;
      } else {
        $default->default = 0;
      }
      $default->save();
    }
    return 1;
  }

  public function addAddressToUser($user_id, $project_id)
  {
    $user   = User::with('role', 'person', 'organization_details', 'addresses', 'user_organizations.organization')->find($user_id);
    $states = State::get();
    return view('modals.user-project-address', compact('user', 'project_id', 'states'));
  }

  public function saveAddressToUser($user_id, Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'user_id'        => 'required',
      'address_line_1' => 'required',
      'city'           => 'required',
      'state_id'       => 'required',
      'zip'            => 'required',
    ], [
      'user_id.required'  => 'Something went wrong, please contact admin',
      'state_id.required' => 'State field is required',
    ]);
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    }
    $address           = new Address;
    $address->line_1   = $request->address_line_1;
    $address->line_2   = $request->address_line_2;
    $address->city     = $request->city;
    $address->state_id = $request->state_id;
    $state             = State::find($request->state_id);
    $address->state    = $state->state_acronym;
    $address->zip      = $request->zip;
    $address->zip_4    = $request->zip_4;
    $address->save();
    $ua             = new UserAddresses;
    $ua->user_id    = $request->user_id;
    $ua->project_id = $request->project_id;
    $ua->address_id = $address->id;
    $ua->save();
    return 1;
  }

  public function defaultAddressOfUserForProject(Request $request)
  {
    //return $request->all();
    $validator = \Validator::make($request->all(), [
      'project_id' => 'required',
      'address_id' => 'required',
      'user_id'    => 'required',
    ]);
    if ($validator->fails()) {
      return 'Something went wrong, please contact admin';
    }
    $selected = $request->address_id;
    // Check if it is devco user and exists in project orgs
    if ($request->devco) {
      $devco_address = UserAddresses::where('project_id', $request->project_id)
        ->where('address_id', $request->address_id)
        ->where('user_id', $request->user_id)
        ->where('devco', 1)
        ->first();
      if ($devco_address) {
        $selected = $devco_address->id;
      } else {
        $uo             = new UserAddresses;
        $uo->address_id = $request->address_id;
        $uo->user_id    = $request->user_id;
        $uo->project_id = $request->project_id;
        $uo->devco      = $request->devco;
        $uo->save();
        $selected = $uo->id;
      }
    }
    $defaults = UserAddresses::where('project_id', $request->project_id)->get();
    foreach ($defaults as $key => $default) {
      if ($default->id == $selected) {
        $default->default = 1;
      } else {
        $default->default = 0;
      }
      $default->save();
    }
    return 1;
  }

  public function editAddressOfUser($address_id, $project_id)
  {
    $ua     = UserAddresses::with('address', 'user')->find($address_id);
    $states = State::get();
    return view('modals.edit-address-of-user', compact('ua', 'project_id', 'states'));
  }

  public function saveAddressOfUser($address_id, Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'address_id'     => 'required',
      'address_line_1' => 'required',
      'city'           => 'required',
      'state_id'       => 'required',
      'zip'            => 'required',
      'project_id'     => 'required',
    ], [
      'address_id.required' => 'Something went wrong, please contact admin',
      'project_id.required' => 'Something went wrong, please contact admin',
    ]);
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    }
    $ua                = UserAddresses::find($request->address_id);
    $address           = Address::find($ua->address_id);
    $address->line_1   = $request->address_line_1;
    $address->line_2   = $request->address_line_2;
    $address->city     = $request->city;
    $address->state_id = $request->state_id;
    $state             = State::find($request->state_id);
    $address->state    = $state->state_acronym;
    $address->zip      = $request->zip;
    $address->zip_4    = $request->zip_4;
    $address->save();
    return 1;
  }

  public function removeAddressOfUser($address_id, Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'address_id' => 'required',
    ], [
      'address_id.required' => 'Something went wrong, please contact admin',
    ]);
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    }
    $org = UserAddresses::find($request->address_id);
    $org->delete();
    return 1;
  }
}
