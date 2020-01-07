<?php

namespace App\Http\Controllers;

use Auth;
// use App\LogConverter;
use Session;
use App\Models\Role;
use App\Models\User;
use App\Models\County;
use App\Models\Amenity;
use App\Models\Program;
use App\Models\UserRole;
use App\Models\AmenityHud;
use App\Models\Boilerplate;
use App\Models\FindingType;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\HudFindingType;
//use Illuminate\Foundation\Auth\User;
use App\Models\DefaultFollowup;
use App\Models\DocumentCategory;
use App\Models\HudInspectableArea;
use App\Models\FindingTypeBoilerplate;
use App\Http\Controllers\FormsController as Form;

class AdminToolController extends Controller
{
	public function __construct(Request $request)
	{
		// $this->middleware('auth');
		//Auth::onceUsingId(2);
		//
		if (env('APP_DEBUG_NO_DEVCO') == 'true') {
			//Auth::onceUsingId(1); // TEST BRIAN
			//Auth::onceUsingId(286); // TEST BRIAN
			Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
		}
	}

	/**
	 * Document Category Create.
	 *
	 * @param \App\Http\Controllers\FormsController $form
	 * @param null                                  $id
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function documentCategoryCreate(Form $form, $id = null)
	{
		$documentCategory = DocumentCategory::where('id', $id)->first();
		if (!$id) {
			$formRows['tag'] = $form->formBuilder('/admin/document_category/store', 'post', 'application/x-www-form-urlencoded', 'Create New Document Category', 'plus-circle');
			$formRows['rows']['ele1'] = $form->text(['Document Category Name', 'document_category_name', '', 'Enter document category name', 'required']);
			$formRows['rows']['ele2'] = $form->submit(['Create Document Category']);

			return view('formtemplate', ['formRows' => $formRows]);
		} else {
			$formRows['tag'] = $form->formBuilder('/admin/document_category/store/' . $documentCategory->id, 'post', 'application/x-www-form-urlencoded', 'Edit Document Category', 'plus-circle');
			$formRows['rows']['ele1'] = $form->text(['Document Category Name', 'document_category_name', $documentCategory->document_category_name, '', 'required']);
			$formRows['rows']['ele2'] = $form->submit(['Update Document Category']);

			return view('formtemplate', ['formRows' => $formRows]);
		}
	}

	/**
	 * County Create.
	 *
	 * @param \App\Http\Controllers\FormsController $form
	 * @param null                                  $id
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
	 */
	public function countyCreate(Form $form, $id = null)
	{
		$county = County::where('id', $id)->first();

		if (!$id) {
			return '<h2>No county was provided? Weird!</h2><p>Try closing and refreshing to come back and try again.</p>';
		} else {
			$formRows['tag'] = $form->formBuilder('/admin/county/store/' . $county->id, 'post', 'application/x-www-form-urlencoded', 'Edit County', 'edit');
			$formRows['rows']['ele1'] = $form->text(['County Name', 'county_name', $county->county_name, '', 'required']);
			$formRows['rows']['ele2'] = $form->text(['Auditor Site', 'auditor_site', $county->auditor_site, '', 'required']);
			$formRows['rows']['ele3'] = $form->submit(['Update County Information']);

			return view('pages.formtemplate', ['formRows' => $formRows]);
		}
	}

	/**
	 * Amenity Update.
	 *
	 * @param  $id
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
	 */
	public function amenityCreate($id = null)
	{
		$amenity = Amenity::where('id', '=', $id)->first();
		$huds = HudInspectableArea::orderBy('name')->get()->all();

		if (!$amenity) {
			$amenity = null;

			return view('modals.amenity-admin-edit', compact('amenity', 'huds'));
		} else {
			return view('modals.amenity-admin-edit', compact('amenity', 'huds'));
		}
	}

	/**
	 * Finding Type Create.
	 *
	 * @param null  $id
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
	 */
	public function findingtypeCreate($id = null)
	{
		$finding_type = FindingType::where('id', $id)->first();

		if (!$finding_type) {
			$finding_type = null;
			$boilerplates = Boilerplate::where('global', '=', 1)->orderBy('name', 'asc')->get();

			// people who can be assigned to the follow ups
			// the audit lead, or the PM, or whoever is creating the finding (hardcoded)

			$document_categories = DocumentCategory::where('active', '=', 1)->get();
			$huds = HudInspectableArea::orderBy('name', 'asc')->get();

			return view('modals.finding-type-create', compact('finding_type', 'boilerplates', 'document_categories', 'huds'));
		} else {
			$boilerplates = Boilerplate::where('global', '=', 1)->orderBy('name', 'asc')->get();
			$document_categories = DocumentCategory::where('active', '=', 1)->get();
			$huds = HudInspectableArea::orderBy('name', 'asc')->get();

			return view('modals.finding-type-create', compact('finding_type', 'boilerplates', 'document_categories', 'huds'));
		}
	}

	/**
	 * Boilerplate Create.
	 *
	 * @param \App\Http\Controllers\FormsController $form
	 * @param null                                  $id
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
	 */
	public function boilerplateCreate(Form $form, $id = null)
	{
		$boilerplate = Boilerplate::where('id', $id)->first();

		if (!$id) {
			$formRows['tag'] = $form->formBuilder('/admin/boilerplate/store', 'post', 'application/x-www-form-urlencoded', 'Create New Boilerplate', 'plus-circle');
			$formRows['rows']['ele1'] = $form->text(['Title', 'name', '', 'Enter boilerplate title', 'required']);
			$formRows['rows']['ele2'] = $form->textArea(['Boilerplate', 'boilerplate', '', '', '']);
			$formRows['rows']['ele3'] = $form->checkbox(['Global', 'global', '', '', 'true', 'required']);
			$formRows['rows']['ele4'] = $form->submit(['Create Boilerplate']);

			return view('formtemplate', ['formRows' => $formRows]);
		} else {
			$formRows['tag'] = $form->formBuilder('/admin/boilerplate/store/' . $boilerplate->id, 'post', 'application/x-www-form-urlencoded', 'Edit Boilerplate', 'edit');
			$formRows['rows']['ele1'] = $form->text(['Title', 'name', $boilerplate->name, 'Enter boilerplate title', 'required']);
			$formRows['rows']['ele2'] = $form->textArea(['Boilerplate', 'boilerplate', $boilerplate->boilerplate, '', 'required']);
			$formRows['rows']['ele3'] = $form->checkbox(['Global', 'global', $boilerplate->global, '', 'true', 'required']);
			$formRows['rows']['ele4'] = $form->submit(['Update Boilerplate Information']);

			return view('formtemplate', ['formRows' => $formRows]);
		}
	}

	/**
	 * Hud Area Create.
	 *
	 * @param \App\Http\Controllers\FormsController $form
	 * @param null                                  $id
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
	 */
	public function hudAreaCreate($id = null)
	{
		$hud = HudInspectableArea::where('id', $id)->first();

		if (!$hud) {
			$hud = null;
			$amenities = Amenity::orderBy('amenity_description', 'asc')->get();
			$findingTypes = FindingType::orderBy('name', 'asc')->get();

			return view('modals.hud-area-create', compact('hud', 'amenities', 'findingTypes'));
		} else {
			$amenities = Amenity::orderBy('amenity_description', 'asc')->get();
			$findingTypes = FindingType::orderBy('name', 'asc')->get();

			return view('modals.hud-area-create', compact('hud', 'amenities', 'findingTypes'));
		}
	}

	// display tabs

	public function searchOrganizations(Request $request)
	{
		if ($request->has('organizations-search')) {
			Session::put('organizations-search', $request->get('organizations-search'));
		} else {
			Session::forget('organizations-search');
		}

		return 1;
	}

	/**
	 * Organizations Index.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function organizationIndex()
	{
		if (Session::has('organizations-search') && Session::get('organizations-search') != '') {
			$search = Session::get('organizations-search');
			$organizations = Organization::with(['address', 'person'])
				->where(function ($query) use ($search) {
					$query->where('organization_name', 'LIKE', '%' . $search . '%');
				})
				->orderBy('organization_name', 'asc')
				->paginate(40);
		} else {
			$organizations = Organization::with(['address', 'person'])->orderBy('organization_name', 'asc')->paginate(40);
		}

		return view('admin_tabs.organizations', compact('organizations'));
	}

	public function searchUsers(Request $request)
	{
		if ($request->has('users-search')) {
			Session::put('users-search', $request->get('users-search'));
		} else {
			Session::forget('users-search');
		}

		return 1;
	}

	/**
	 * Users Index.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function usersIndex()
	{
		if (Session::has('users-search') && Session::get('users-search') != '') {
			$search = Session::get('users-search');
			$users = User::with('person.projects', 'report_access.project')->
				join('people', 'users.person_id', '=', 'people.id')->
				leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')->
				leftJoin('roles', 'users_roles.role_id', '=', 'roles.id')->
				leftJoin('organizations', 'users.organization_id', 'organizations.id')->
				leftJoin('phone_numbers', 'organizations.default_phone_number_id', 'phone_numbers.id')->
				leftJoin('addresses', 'organizations.default_address_id', 'addresses.id')->
				select('users.*', 'line_1', 'line_2', 'city', 'state', 'zip', 'organization_name', 'role_id', 'role_name', 'area_code', 'phone_number', 'extension', 'last_name', 'first_name')->
				where('first_name', 'LIKE', '%' . $search . '%')->
				orWhere('last_name', 'LIKE', '%' . $search . '%')->
				orWhere('organization_name', 'LIKE', '%' . $search . '%')->
				orWhere('role_name', 'LIKE', '%' . $search . '%')->
				orderBy('last_name', 'asc')->
				paginate(25);
		} else {
			$users = User::with('person.projects', 'report_access.project')->
				join('people', 'users.person_id', '=', 'people.id')->
				leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')->
				leftJoin('roles', 'users_roles.role_id', '=', 'roles.id')->
				leftJoin('organizations', 'users.organization_id', 'organizations.id')->
				leftJoin('addresses', 'organizations.default_address_id', 'addresses.id')->
				leftJoin('phone_numbers', 'organizations.default_phone_number_id', 'phone_numbers.id')->
				select('users.*', 'line_1', 'line_2', 'city', 'state', 'zip', 'organization_name', 'role_id', 'role_name', 'area_code', 'phone_number', 'extension', 'last_name', 'first_name')->
				orderBy('last_name', 'asc')->
				paginate(25);
		}
		//dd($users);
		if (Auth::user()->roles) {
			$user_role = Auth::user()->roles->first()->role_id;
		} else {
			$user_role = 0;
		}

		return view('admin_tabs.users', compact('users', 'user_role'));
	}

	/**
	 * Amenities Index.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function amenityIndex()
	{
		$amenities = Amenity::orderBy('amenity_description', 'asc')->get();

		return view('admin_tabs.amenities', compact('amenities'));
	}

	/**
	 * HUD INSPECTABLE AREA Index.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function hudIndex()
	{
		$huds = HudInspectableArea::orderBy('name', 'asc')->get();

		return view('admin_tabs.huds', compact('huds'));
	}

	public function searchFindingTypes(Request $request)
	{
		if ($request->has('findingtypes-search')) {
			Session::put('findingtypes-search', $request->get('findingtypes-search'));
		} else {
			Session::forget('findingtypes-search');
		}

		return 1;
	}

	/**
	 * Finding Type Index.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function findingtypeIndex()
	{
		if (Session::has('findingtypes-search') && Session::get('findingtypes-search') != '') {
			$search = Session::get('findingtypes-search');
			$findingtypes = FindingType::where(function ($query) use ($search) {
				$query->where('name', 'LIKE', '%' . $search . '%');
			})
				->orderBy('name', 'asc')
				->paginate(25);
		} else {
			$findingtypes = FindingType::orderBy('name', 'asc')->paginate(25);
		}

		return view('admin_tabs.findingtypes', compact('findingtypes'));
	}

	/**
	 * defaultfollowup Index.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function defaultfollowupIndex()
	{
		$followups = DefaultFollowup::orderBy('description', 'asc')->get();

		return view('admin_tabs.followups', compact('followups'));
	}

	/**
	 * defaultfollowup Index.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function boilerplateIndex()
	{
		$boilerplates = Boilerplate::with('user')->orderBy('name', 'asc')->get();

		return view('admin_tabs.boilerplates', compact('boilerplates'));
	}

	/**
	 * Program Index.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function programIndex()
	{
		$programs = Program::with(['entity', 'county', 'programRule'])->orderBy('program_name', 'asc')->get();

		return view('admin_tabs.program', compact('programs'));
	}

	/**
	 * Document Index.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function documentIndex()
	{
		$documents = DocumentCategory::orderBy('document_category_name', 'asc')->get()->all();

		return view('admin_tabs.document_categories', compact('documents'));
	}

	/**
	 * County Index.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function countyIndex()
	{
		$counties = County::orderBy('county_name', 'asc')->get();

		return view('admin_tabs.counties', compact('counties'));
	}

	//store form data.

	/**
	 * Program Store.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param null                     $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function programStore(Request $request, $id = null)
	{
		$entityIds = Entity::pluck('id')->toArray();
		$countyIds = County::pluck('id')->toArray();
		$ruleIds = ProgramRule::pluck('id')->toArray();
		$this->validate($request, [
			'program_name' => 'string|required',
			'entity_id' => 'in:' . implode(',', $entityIds),
			'county_id' => 'in:' . implode(',', $countyIds),
			'rule_id' => 'in:' . implode(',', $ruleIds),
		]);
		if (!$id) {
			$p = Program::create([
				'owner_type' => 'entity',
				'owner_id' => Request::get('entity_id'),
				'program_name' => Request::get('program_name'),
				'entity_id' => Request::get('entity_id'),
				'active' => 1,
				'default_program_rules_id' => Request::get('rule_id'),
				'county_id' => Request::get('county_id'),
			]);
			// $lc = new LogConverter('program', 'create');
			// $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user()->email . ' Created program ' . $p->program_name)->save();

			return response('We created a program together, and now all it needs is an account! <hr /> <a onclick="dynamicModalLoad(\'admin/account/create\')" class="uk-button uk-width-2-5@m uk-float-right">CREATE NEW ACCOUNT</a>');
		} else {
			$pold = Program::find($id)->toArray();
			Program::where('id', $id)->update([
				'owner_type' => 'entity',
				'owner_id' => Request::get('entity_id'),
				'program_name' => Request::get('program_name'),
				'entity_id' => Request::get('entity_id'),
				'active' => 1,
				'default_program_rules_id' => Request::get('rule_id'),
				'county_id' => Request::get('county_id'),
			]);
			$p = Program::find($id);
			$pnew = $p->toArray();
			// $lc   = new LogConverter('program', 'update');
			// $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user()->email . ' Updated program ' . $p->program_name);
			// $lc->smartAddHistory($pold, $pnew);
			// $lc->save();

			return response('I updated ' . $p->program_name . ' for you. We are task masters!  <script>$(\'#programs-tab\').trigger(\'click\');</script>');
		}
	}

	/**
	 * Document Category Store.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param null                     $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function documentCategoryStore(Request $request, $id = null)
	{
		$this->validate($request, [
			'document_category_name' => 'string|required',
		]);
		if (!$id) {
			$d = DocumentCategory::create([
				'document_category_name' => Request::get('document_category_name'),
			]);
			// $lc = new LogConverter('documentcategory', 'create');
			// $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Created Document Category ' . $d->document_category_name)->save();
			return response('I created the document category. I stored it. I love it.');
		} else {
			$dold = DocumentCategory::find($id)->toArray();
			DocumentCategory::where('id', $id)->update([
				'document_category_name' => Request::get('document_category_name'),
			]);
			$d = DocumentCategory::find($id);
			$dnew = $d->toArray();
			// $lc = new LogConverter('documentcategory', 'update');
			// $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Updated Document Category ' . $d->document_category_name);
			// $lc->smartAddHistory($dold, $dnew);
			// $lc->save();
			return response('I updated your document category. That was fun! What else do you have for me?');
		}
	}

	/**
	 * Boilerplate Store.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param null                     $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function boilerplateStore(Request $request, $id = null)
	{
		$this->validate($request, [
			'name' => 'string|required',
		]);
		$inputs = $request->all();
		if (!$id) {
			$d = Boilerplate::create([
				'name' => $request->get('name'),
				'boilerplate' => $request->get('boilerplate'),
				'global' => (array_key_exists('global', $inputs)) ? 1 : 0,
				'creator_id' => Auth::user()->id,
			]);
			// $lc = new LogConverter('documentcategory', 'create');
			// $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Created Document Category ' . $d->document_category_name)->save();
			return response('<h2>Success</h2><p>I created the boilerplate.</p>');
		} else {
			$dold = Boilerplate::find($id)->toArray();
			Boilerplate::where('id', $id)->update([
				'name' => $request->get('name'),
				'boilerplate' => $request->get('boilerplate'),
				'global' => (array_key_exists('global', $inputs)) ? 1 : 0,
			]);
			$d = Boilerplate::find($id);
			$dnew = $d->toArray();
			// $lc = new LogConverter('documentcategory', 'update');
			// $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Updated Document Category ' . $d->document_category_name);
			// $lc->smartAddHistory($dold, $dnew);
			// $lc->save();
			return response('I updated your boilerplate. That was fun! What else do you have for me?');
		}
	}

	/**
	 * HUD Area Store.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param null                     $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function hudAreaStore(Request $request, $id = null)
	{
		$inputs = $request->get('inputs');
		$amenities = json_decode($request->get('amenities'), true);
		$findingTypes = json_decode($request->get('findingTypes'), true);
		//dd($findingTypes,$amenities);
		$amenities = $amenities['items'];
		$findingTypes = $findingTypes['items'];
		$site = (array_key_exists('site', $inputs)) ? 1 : 0;
		$buildingExterior = (array_key_exists('building_exterior', $inputs)) ? 1 : 0;
		$buildingSystem = (array_key_exists('building_system', $inputs)) ? 1 : 0;
		$commonArea = (array_key_exists('common_area', $inputs)) ? 1 : 0;
		$file = (array_key_exists('file', $inputs)) ? 1 : 0;
		$unit = (array_key_exists('unit', $inputs)) ? 1 : 0;
		//dd($inputs);

		if (!$id) {
			$hud = HudInspectableArea::create([
				'name' => $inputs['name'],
				'site' => $site,
				'building_system' => $buildingSystem,
				'building_exterior' => $buildingExterior,
				'common_area' => $commonArea,
				'unit' => $unit,
				'file' => $file,

			]);

			// add amenities
			if (count($amenities)) {
				//add in the update
				foreach ($amenities as $amenity) {
					AmenityHud::create([
						'hud_inspectable_area_id' => $hud->id,
						'amenity_id' => $amenity['id'],
					]);
				}
			}

			// add finding types
			if (count($findingTypes)) {
				foreach ($findingTypes as $findingType) {
					HudFindingType::create([
						'hud_inspectable_area_id' => $hud->id,
						'finding_type_id' => $findingType['id'],
					]);
				}
			}

			// $lc = new LogConverter('documentcategory', 'create');
			// $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Created Document Category ' . $d->document_category_name)->save();
			return response('<h2>Success!</h2><p>I created the HUD area.</p>');
		} else {
			$hud = HudInspectableArea::where('id', '=', $id)->first();

			if ($hud) {
				$hud->update([
					'name' => $inputs['name'],
					'site' => $site,
					'building_system' => $buildingSystem,
					'building_exterior' => $buildingExterior,
					'common_area' => $commonArea,
					'unit' => $unit,
					'file' => $file,
				]);
				$hud->touch(); // ensure timestamps are updated

				// remove amenities
				AmenityHud::where('hud_inspectable_area_id', '=', $hud->id)->delete();

				// add amenities
				if (count($amenities)) {
					foreach ($amenities as $amenity) {
						AmenityHud::create([
							'hud_inspectable_area_id' => $hud->id,
							'amenity_id' => $amenity['id'],
						]);
					}
				}

				// remove finding types
				HudFindingType::where('hud_inspectable_area_id', '=', $hud->id)->delete();

				// add finding types
				if (count($findingTypes)) {
					foreach ($findingTypes as $findingType) {
						HudFindingType::create([
							'hud_inspectable_area_id' => $hud->id,
							'finding_type_id' => $findingType['id'],
						]);
					}
				}

				return response('<h2>Success!</h2><p>I updated your HUD area.</p>');
			} else {
				return response('<h2>Problem...</h2><p>I cannot find that record.</p>');
			}

			// $lc = new LogConverter('documentcategory', 'update');
			// $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Updated Document Category ' . $d->document_category_name);
			// $lc->smartAddHistory($dold, $dnew);
			// $lc->save();
		}
	}

	/**
	 * Amenity Store.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param null                     $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function amenityStore(Request $request, $id = null)
	{
		$inputs = $request->get('inputs');

		$project = (array_key_exists('project', $inputs)) ? 1 : 0;
		$buildingExterior = (array_key_exists('building_exterior', $inputs)) ? 1 : 0;
		$buildingSystem = (array_key_exists('building_system', $inputs)) ? 1 : 0;
		$commonArea = (array_key_exists('common_area', $inputs)) ? 1 : 0;
		$file = (array_key_exists('file', $inputs)) ? 1 : 0;
		$unit = (array_key_exists('unit', $inputs)) ? 1 : 0;
		$unitDefault = (array_key_exists('unit_default', $inputs)) ? 1 : 0;
		$buildingDefault = (array_key_exists('building_default', $inputs)) ? 1 : 0;
		$projectDefault = (array_key_exists('project_default', $inputs)) ? 1 : 0;
		$inspectable = (array_key_exists('inspectable', $inputs)) ? 1 : 0;
		$huds = json_decode($request->get('huds'), true);
		$huds = $huds['items'];

		if (!$id) {
			$amenity = Amenity::create([
				'amenity_description' => $inputs['amenity_description'],
				'project' => $project,
				'building_exterior' => $buildingExterior,
				'building_system' => $buildingSystem,
				'common_area' => $commonArea,
				'file' => $file,
				'unit' => $unit,
				'unit_default' => $unitDefault,
				'building_default' => $buildingDefault,
				'project_default' => $projectDefault,
				'inspectable' => $inspectable,
				'policy' => $inputs['policy'],
				'time_to_complete' => $inputs['time'],
				'icon' => $inputs['icon'],
			]);

			// add huds
			if (count($huds)) {
				foreach ($huds as $hud) {
					AmenityHud::create([
						'amenity_id' => $amenity->id,
						'hud_inspectable_area_id' => $hud['id'],
					]);
				}
			}

			return response('<h2>Success!</h2><p>I created the amenity.</p>');
		} else {
			$amenity = Amenity::where('id', '=', $id)->first();

			if ($amenity) {
				$amenity->update([
					'amenity_description' => $inputs['amenity_description'],
					'project' => $project,
					'building_exterior' => $buildingExterior,
					'building_system' => $buildingSystem,
					'common_area' => $commonArea,
					'file' => $file,
					'unit' => $unit,
					'unit_default' => $unitDefault,
					'building_default' => $buildingDefault,
					'project_default' => $projectDefault,
					'inspectable' => $inspectable,
					'policy' => $inputs['policy'],
					'time_to_complete' => $inputs['time'],
					'icon' => $inputs['icon'],
				]);
				// remove huds
				AmenityHud::where('amenity_id', '=', $amenity->id)->delete();

				// add huds
				if (count($huds)) {
					foreach ($huds as $hud) {
						//dd($hud,$amenity->id);
						AmenityHud::create([
							'amenity_id' => $amenity->id,
							'hud_inspectable_area_id' => $hud['id'],
						]);
					}
				}

				return response('I updated the amenity. That was fun! What else do you have for me?');
			} else {
				return response('I cannot find that record.');
			}
		}

		// $lc = new LogConverter('documentcategory', 'update');
		// $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Updated Document Category ' . $d->document_category_name);
		// $lc->smartAddHistory($dold, $dnew);
		// $lc->save();
	}

	/**
	 * Finding Type Store.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param null                     $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function findingtypeStore(Request $request, $id = null)
	{
		$inputs = $request->get('inputs');
		$boilerplates = json_decode($request->get('boilerplates'), true);
		$boilerplates = $boilerplates['items'];
		$huds = json_decode($request->get('huds'), true);
		$huds = $huds['items'];
		$followups = json_decode($request->get('followups'), true);
		$followups = $followups['items'];

		if (!$id) {
			$f = FindingType::create([
				'name' => $inputs['name'],
				'nominal_item_weight' => $inputs['nominal_item_weight'],
				'criticality' => $inputs['criticality'],
				'one' => (array_key_exists('one', $inputs)) ? 1 : 0,
				'two' => (array_key_exists('two', $inputs)) ? 1 : 0,
				'three' => (array_key_exists('three', $inputs)) ? 1 : 0,
				'one_description' => $inputs['one_description'],
				'two_description' => $inputs['two_description'],
				'three_description' => $inputs['three_description'],
				'type' => $inputs['type'],
				'building_exterior' => (array_key_exists('building_exterior', $inputs)) ? 1 : 0,
				'building_system' => (array_key_exists('building_system', $inputs)) ? 1 : 0,
				'site' => (array_key_exists('site', $inputs)) ? 1 : 0,
				'common_area' => (array_key_exists('common_area', $inputs)) ? 1 : 0,
				'unit' => (array_key_exists('unit', $inputs)) ? 1 : 0,
				'file' => (array_key_exists('file', $inputs)) ? 1 : 0,
			]);

			// add boilerplates
			if (count($boilerplates)) {
				foreach ($boilerplates as $boilerplate) {
					FindingTypeBoilerplate::create([
						'finding_type_id' => $f->id,
						'boilerplate_id' => $boilerplate['id'],
					]);
				}
			}

			// add huds
			if (count($huds)) {
				foreach ($huds as $hud) {
					HudFindingType::create([
						'finding_type_id' => $f->id,
						'hud_inspectable_area_id' => $hud['id'],
					]);
				}
			}

			// add followups
			if (count($followups)) {
				foreach ($followups as $followup) {
					DefaultFollowup::create([
						'finding_type_id' => $f->id,
						'description' => $followup['description'],
						'quantity' => $followup['number'],
						'duration' => $followup['duration'],
						'assignment' => $followup['assignment'],
						'reply' => $followup['reply'],
						'photo' => $followup['photo'],
						'doc' => $followup['doc'],
						'doc_categories' => json_encode($followup['cats']),
					]);
				}
			}

			// $lc = new LogConverter('documentcategory', 'create');
			// $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Created Document Category ' . $d->document_category_name)->save();
			return response('I created the finding type. I stored it. I love it.');
		} else {
			$finding_type = FindingType::where('id', '=', $id)->first();

			if ($finding_type) {
				$finding_type->update([
					'name' => $inputs['name'],
					'nominal_item_weight' => $inputs['nominal_item_weight'],
					'criticality' => $inputs['criticality'],
					'one' => (array_key_exists('one', $inputs)) ? 1 : 0,
					'two' => (array_key_exists('two', $inputs)) ? 1 : 0,
					'three' => (array_key_exists('three', $inputs)) ? 1 : 0,
					'one_description' => $inputs['one_description'],
					'two_description' => $inputs['two_description'],
					'three_description' => $inputs['three_description'],
					'type' => $inputs['type'],
					'building_exterior' => (array_key_exists('building_exterior', $inputs)) ? 1 : 0,
					'building_system' => (array_key_exists('building_system', $inputs)) ? 1 : 0,
					'site' => (array_key_exists('site', $inputs)) ? 1 : 0,
					'common_area' => (array_key_exists('common_area', $inputs)) ? 1 : 0,
					'unit' => (array_key_exists('unit', $inputs)) ? 1 : 0,
					'file' => (array_key_exists('file', $inputs)) ? 1 : 0,
				]);

				// remove boilerplates
				// remove followups

				FindingTypeBoilerplate::where('finding_type_id', '=', $finding_type->id)->delete();
				HudFindingType::where('finding_type_id', '=', $finding_type->id)->delete();
				DefaultFollowup::where('finding_type_id', '=', $finding_type->id)->delete();

				// add boilerplates
				if (count($boilerplates)) {
					foreach ($boilerplates as $boilerplate) {
						FindingTypeBoilerplate::create([
							'finding_type_id' => $finding_type->id,
							'boilerplate_id' => $boilerplate['id'],
						]);
					}
				}

				// add huds
				if (count($huds)) {
					foreach ($huds as $hud) {
						HudFindingType::create([
							'finding_type_id' => $finding_type->id,
							'hud_inspectable_area_id' => $hud['id'],
						]);
					}
				}

				// add followups
				if (count($followups)) {
					foreach ($followups as $followup) {
						DefaultFollowup::create([
							'finding_type_id' => $finding_type->id,
							'description' => $followup['description'],
							'quantity' => $followup['number'],
							'duration' => $followup['duration'],
							'assignment' => $followup['assignment'],
							'reply' => $followup['reply'],
							'photo' => $followup['photo'],
							'doc' => $followup['doc'],
							'doc_categories' => json_encode($followup['cats']),
						]);
					}
				}

				return response('<h2>Success!</h2><p>I updated the finding type.</p>');
			} else {
				return response('<h2>Problem...</h2><p>I am sorry, but I cannot find that record.</p>');
			}
		}
	}

	public function userManageRoles(User $id)
	{
		//$user = User::where('id','=',$id)->first();
		$user = $id;
		//dd($user);

		$current_user = Auth::user();
		// current user's highest roles
		$current_user_highest_role = UserRole::where('user_id', '=', $current_user->id)->orderBy('role_id', 'desc')->first();

		if ($current_user_highest_role) {
			if ($user->id !== $current_user->id) {
				$roles = Role::where('id', '<', $current_user_highest_role->role_id)->get();
			} else {
				$maxRoleForSelf = $current_user_highest_role->role_id + 1;
				$roles = Role::where('id', '<', $maxRoleForSelf)->get();
			}
		} else {
			$roles = null;
		}

		return view('modals.user-roles-edit', compact('user', 'roles'));
	}

	public function userSaveRoles(Request $request, $id)
	{
		$user = User::where('id', '=', $id)->first();

		$inputs = $request->input('inputs');
		parse_str($inputs, $inputs);

		// delete all roles for this user first
		UserRole::where('user_id', '=', $user->id)->delete();

		if ($inputs['roles'] > 0) {
			$new_role = new UserRole([
				'user_id' => $user->id,
				'role_id' => $inputs['roles'],
			]);
			$new_role->save();
		}

		return 1;
	}

	/*
	 * Expense Category Store
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param null                     $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	// public function expenseCategoryStore(Request $request, $id = null)
	// {
	//     $this->validate($request, [
	//         'expense_category_name'=>'string|required'
	//     ]);
	//     if (!$id) {
	//         $e = ExpenseCategory::create([
	//             'expense_category_name' => Request::get('expense_category_name')
	//         ]);
	//         $lc = new LogConverter('expensecategory', 'create');
	//         $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' Created expense category ' . $e->expense_category_name)->save();
	//         return response('I did it. I made it exactly as you asked. What should we do next?');
	//     } else {
	//         $eold = ExpenseCategory::find($id)->toArray();
	//         ExpenseCategory::where('id', $id)->update([
	//             'expense_category_name' => Request::get('expense_category_name')
	//         ]);
	//         $e = ExpenseCategory::find($id);
	//         $enew = $e->toArray();
	//         $lc = new LogConverter('expensecategory', 'update');
	//         $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' Updated expense category ' . $e->expense_category_name);
	//         $lc->smartAddHistory($eold, $enew);
	//         $lc->save();
	//         return response('Consider that expense category updated. What is next?');
	//     }
	// }

	/*
	 * Vendor Store
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param null                     $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	// public function vendorStore(Request $request, $id = null)
	// {
	//     $this->validate($request, [
	//         'vendor_name'=>'string|required',
	//         'vendor_email'=>'email',
	//         'vendor_phone'=>'string',
	//         'vendor_mobile'=>'string',
	//         'vendor_fax'=>'string',
	//         'vendor_street'=>'string',
	//         'vendor_street2'=>'string',
	//         'vendor_city'=>'string',
	//         'vendor_zip'=>'string',
	//         'vendor_duns'=>'string',
	//         'vendor_notes'=>'string'
	//     ]);
	//     if (!$id) {
	//         $v = Vendor::create([
	//             'vendor_name'=> Request::get('vendor_name'),
	//             'vendor_email'=>Request::get('vendor_email'),
	//             'vendor_phone'=>Request::get('vendor_phone'),
	//             'vendor_mobile_phone'=> Request::get('vendor_mobile'),
	//             'vendor_fax'=>Request::get('vendor_fax'),
	//             'vendor_street_address'=>Request::get('vendor_street'),
	//             'vendor_street_address2'=>Request::get('vendor_street2'),
	//             'vendor_city'=>Request::get('vendor_city'),
	//             'vendor_state_id'=>Request::get('state_id'),
	//             'vendor_zip'=>Request::get('vendor_zip'),
	//             'vendor_duns'=>Request::get('vendor_duns'),
	//             'vendor_notes'=>Request::get('vendor_notes')
	//         ]);
	//         $lc = new LogConverter('vendor', 'create');
	//         $lc->setFrom(Auth::user())->setTo($v)->setDesc(Auth::user()->email . ' Created Vendor' . $v->vendor_name)->save();

	//         return response('I created your vendor. Let us put them to work now!');
	//     } else {
	//         $vold = Vendor::find($id)->toArray();
	//         Vendor::where('id', $id)->update([
	//             'vendor_name'=> Request::get('vendor_name'),
	//             'vendor_email'=>Request::get('vendor_email'),
	//             'vendor_phone'=>Request::get('vendor_phone'),
	//             'vendor_mobile_phone'=> Request::get('vendor_mobile'),
	//             'vendor_fax'=>Request::get('vendor_fax'),
	//             'vendor_street_address'=>Request::get('vendor_street'),
	//             'vendor_street_address2'=>Request::get('vendor_street2'),
	//             'vendor_city'=>Request::get('vendor_city'),
	//             'vendor_state_id'=>Request::get('state_id'),
	//             'vendor_zip'=>Request::get('vendor_zip'),
	//             'vendor_duns'=>Request::get('vendor_duns'),
	//             'vendor_notes'=>Request::get('vendor_notes')
	//         ]);
	//         $v = Vendor::find($id);
	//         $vnew = $v->toArray();
	//         $lc = new LogConverter('vendor', 'update');
	//         $lc->setFrom(Auth::user())->setTo($v)->setDesc(Auth::user() . ' Updated vendor ' . $v->vendor_name);
	//         $lc->smartAddHistory($vold, $vnew);
	//         $lc->save();
	//         return response('I successfully updated your vendor. Let us do a little dance.');
	//     }
	// }

	/*
	 * Target Area Store
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param null                     $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	// public function targetAreaStore(Request $request, $id = null)
	// {
	//     $countyIds = County::pluck('id')->toArray();
	//     $this->validate($request, [
	//         'target_area_name'=>'string|required',
	//         'county'=>'in:'.implode(',', $countyIds),
	//     ]);
	//     if (!$id) {
	//         $t = TargetArea::create([
	//             'county_id'=>Request::get('county'),
	//             'target_area_name'=>Request::get('target_area_name')
	//         ]);
	//         $lc = new LogConverter('target', 'create');
	//         $lc->setFrom(Auth::user())->setTo($t)->setDesc(Auth::user()->email . ' Created Target' . $t->target_area_name)->save();
	//         return response('I\'ve added '.Request::get('target_area_name').' to the list of available target areas.');
	//     } else {
	//         $told = TargetArea::find($id)->toArray();
	//         TargetArea::where('id', $id)->update([
	//             'county_id'=>Request::get('county'),
	//             'target_area_name'=>Request::get('target_area_name')
	//         ]);
	//         $t = TargetArea::find($id);
	//         $tnew = $t->toArray();
	//         $lc = new LogConverter('target', 'update');
	//         $lc->setFrom(Auth::user())->setTo($t)->setDesc(Auth::user() . ' Updated target area ' . $t->target_area_name);
	//         $lc->smartAddHistory($told, $tnew);
	//         $lc->save();
	//         return response('I updated the target area successfully. You\'re welcome ;) <script>$(\'#target-areas-tab\').trigger(\'click\');</script>');
	//     }
	// }

	/*
	 * County Store
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param null                     $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	// public function countyStore(Request $request, $id = null)
	// {
	//     if (!$id) {
	//         return response('Oops - no county was selected? That\'s weird.');
	//     } else {
	//         $told = County::find($id)->toArray();
	//         DB::table('counties')->where('id', $id)->update([
	//             'county_name'=>Request::get('county_name'),
	//             'auditor_site'=>Request::get('auditor_site')
	//         ]);
	//         $t = TargetArea::find($id);
	//         $tnew = $t->toArray();
	//         $lc = new LogConverter('county', 'update');
	//         $lc->setFrom(Auth::user())->setTo($t)->setDesc(Auth::user() . ' Updated county information for ' . $t->county_name);
	//         $lc->smartAddHistory($told, $tnew);
	//         $lc->save();
	//         return response('I updated the county area successfully. You\'re welcome ;) <script>$(\'#counties-tab\').trigger(\'click\');</script>');
	//     }
	// }

	/*
	 * Deactivate Tools
	 *
	 * @param $type
	 * @param $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	// public function deactivateTools($type, $id)
	// {
	//     switch ($type) {
	//         case "entity":
	//             $e = Entity::find($id);
	//              Entity::where('id', $id)->update(['active'=>0]);
	//             $lc = new LogConverter('entity', 'deactivate');
	//             $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' deactivated entity ' . $e->entity_name.' as well as its programs, accounts, and users.')->save();
	//             Program::where('owner_id', $id)->update(['active'=>0]);
	//             Account::where('entity_id', $id)->update(['active'=>0]);
	//             User::where('entity_id', $id)->update(['active'=>0]);
	//             return response($e->entity_name.' has been deactivated. The users belonging to this entity can no longer access the site. I have also deactivated their users, programs, and accounts. Their programs are no longer available for landbank users to register as members through the registration screen. It will continue to show up in reports if it has parcels assigned to it.');
	//         break;
	//         case "program":
	//             $p = Program::find($id);
	//             Program::where('id', $id)->update(['active'=>0]);
	//             Account::where('owner_id', $id)->update(['active'=>0]);
	//             $lc = new LogConverter('program', 'deactivate');
	//             $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user()->email . ' deactivated program ' . $p->program_name)->save();
	//             return response('I have deactivated the program and its associated account. Users can no longer see the account in the registration list. If it has parcels or transactions associated with it, it will still appear in reports.');
	//         break;
	//         case "rule":
	//             $pr = ProgramRule::find($id);
	//             $p = Program::where('default_program_rules_id', $id)->where('active', 1)->count();
	//             if ($p > 0) {
	//                 if ($p > 1) {
	//                     $plural = "s";
	//                 } else {
	//                     $plural = "";
	//                 }
	//                 return "I'm sorry, I cannot deactivate $pr->rules_name because it is still being used by $p program$plural. Please reassign those program$plural to use a differnt rule before deactivating.";
	//             } else {
	//                 ProgramRule::where('id', $id)->update(['active'=>0]);
	//                 $lc = new LogConverter('programrule', 'deactivate');
	//                 $lc->setFrom(Auth::user())->setTo($pr)->setDesc(Auth::user()->email . ' deactivated program rule')->save();
	//                 return response($pr->rules_name.' has been deactivated and will not be used going forward. Parcels that used this rule previously will still show this rule unless they are reassigned to another. Inactive programs are allowed to have this rule associated with them because they cannot be used.');
	//             }
	//             break;
	//         case "account":
	//             $a = Account::find($id);
	//             $p = Program::find($a->owner_id);
	//             Account::where('id', $id)->update(['active'=>0]);
	//             $lc = new LogConverter('account', 'deactivate');
	//             $lc->setFrom(Auth::user())->setTo($a)->setDesc(Auth::user()->email . ' deactivated account ' . $a->account_name)->save();
	//             return response($a->account_name.' has been deactivated and cannot accept any new Transactions. However, if it has transactions, it will still show up in accounting until all its transactions have been moved to a different account. Please be sure to create a new account for '.$p->program_name.', otherwise it will not be able to accept reimbursements.');
	//         break;
	//         case "vendor":
	//             Vendor::where('id', $id)->update(['active'=>0]);
	//             $v = Vendor::find($id);
	//             $lc = new LogConverter('vendor', 'deactivate');
	//             $lc->setFrom(Auth::user())->setTo($v)->setDesc(Auth::user()->email . ' deactivated vendor ' . $v->vendor_name)->save();
	//             return response($v->vendor_name.' is no longer available for new expense reimbursements.');
	//         break;
	//         case "target":
	//             TargetArea::where('id', $id)->update(['active'=>0]);
	//             //TODO: add event logging
	//             $ta = TargetArea::find($id);
	//             return response('The '.$ta->target_area_name.' target area is no longer available for new parcels. Old parcels assigned to it will still show it; thus it will still show up in the parcels list until all parcels assigned to it have been reassigned.');
	//         break;
	//         case "document":
	//             $dc = DocumentCategory::find($id);
	//             DocumentCategory::where('id', $id)->update(['active'=>0]);
	//             $lc = new LogConverter('documentcategory', 'deactivate');
	//             $lc->setFrom(Auth::user())->setTo($dc)->setDesc(Auth::user()->email . ' deactivated decument category ' . $dc->document_category_name)->save();
	//             return response('I deactivated '.$dc->document_category_name.' and it will no longer be available. Please note that old documents uploaded under it will still show on their respective tabs.');
	//         break;
	//         case "expense":
	//             $ec = ExpenseCategory::find($id);
	//             ExpenseCategory::where('id', $id)->update(['active'=>0]);
	//             $lc = new LogConverter('expensecategory', 'deactivate');
	//             $lc->setFrom(Auth::user())->setTo($ec)->setDesc(Auth::user()->email . ' deactivated expense category ' . $ec->expense_category_name)->save();
	//             return response('I deactivated the expense category. Note that it will still show on parcels that used it. Account totals will also show it parenthetically if they had any expenses within it.');
	//         break;
	//     }
	// }

	/*
	 * Activate Tools
	 *
	 * @param $type
	 * @param $id
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	// public function activateTools($type, $id)
	// {
	//     switch ($type) {
	//         case "entity":
	//             $e = Entity::find($id);
	//             Entity::where('id', $id)->update(['active'=>1]);
	//             $lc = new LogConverter('entity', 'activate');
	//             $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' activated entity ' . $e->entity_name)->save();
	//             return response('I activated '.$e->entity_name.' for you. You may want to check and make sure it has an active program, account, and user.');
	//         break;
	//         case "program":
	//             Program::where('id', $id)->update(['active'=>1]);
	//             $p = Program::find($id);
	//             $lc = new LogConverter('program', 'activate');
	//             $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user()->email . ' activated program ' . $p->program_name)->save();
	//             return response('I activated '.$p->program_name.' for you. What\'s next?');
	//         break;
	//         case "rule":
	//             ProgramRule::where('id', $id)->update(['active'=>1]);
	//             $pr = ProgramRule::find($id);
	//             $lc = new LogConverter('programrule', 'activate');
	//             $lc->setFrom(Auth::user())->setTo($pr)->setDesc(Auth::user()->email . ' activated program rule')->save();
	//             return response('Rule is activated');
	//         break;
	//         case "account":
	//             Account::where('id', $id)->update(['active'=>1]);
	//             $a = Account::find($id);
	//             $lc = new LogConverter('account', 'activate');
	//             $lc->setFrom(Auth::user())->setTo($a)->setDesc(Auth::user()->email . ' activated account ' . $a->account_name)->save();
	//             return response('Account is activated');
	//         break;
	//         case "vendor":
	//             Vendor::where('id', $id)->update(['active'=>1]);
	//             $v = Vendor::find($id);
	//             $lc = new LogConverter('vendor', 'activate');
	//             $lc->setFrom(Auth::user())->setTo($v)->setDesc(Auth::user()->email . ' activated vendor ' . $v->vendor_name)->save();
	//             return response('Vendor is activated');
	//         break;
	//         case "target":
	//             TargetArea::where('id', $id)->update(['active'=>1]);
	//             //TODO: Add event logging
	//             return response('Target area is activated');
	//         break;
	//         case "document":
	//             DocumentCategory::where('id', $id)->update(['active'=>1]);
	//             $dc = DocumentCategory::find($id);
	//             $lc = new LogConverter('documentcategory', 'activate');
	//             $lc->setFrom(Auth::user())->setTo($dc)->setDesc(Auth::user()->email . 'activated document category ' . $dc->document_category_name)->save();
	//             return response('Document category is activated');
	//         break;
	//         case "expense":
	//             ExpenseCategory::where('id', $id)->update(['active'=>1]);
	//             $ec = ExpenseCategory::find($id);
	//             $lc = new LogConverter('expensecategory', 'activate');
	//             $lc->setFrom(Auth::user())->setTo($ec)->setDesc(Auth::user()->email . ' activated expense category ' . $ec->expense_category_name)->save();
	//             return response('Expense category is activated');
	//         break;
	//     }
	// }
}
