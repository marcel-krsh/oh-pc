<?php

namespace App\Http\Controllers;

use Auth;
use View;
use Carbon;
use Session;
use App\Models\Job;
use App\Models\Unit;
use App\Models\User;
use App\Models\Audit;
use App\Models\Group;
use App\Models\Amenity;
use App\Models\Comment;
use App\Models\Finding;
use App\Models\Program;
use App\Models\Project;
use App\Models\Building;
use App\Models\CrrReport;
use App\Models\GuideStep;
use App\Models\UserEmail;
use App\Models\CachedUnit;
use App\Models\CachedAudit;
use App\Models\ScheduleDay;
use App\Models\UnitAmenity;
use App\Models\UnitProgram;
use Illuminate\Support\Arr;
use App\Models\AuditAuditor;
use App\Models\Availability;
use App\Models\OrderingUnit;
use App\Models\ProgramGroup;
use App\Models\ReportAccess;
use App\Models\ScheduleTime;
use Illuminate\Http\Request;
use App\Models\CachedComment;
use App\Models\GuideProgress;
use App\Models\SystemSetting;
use App\Models\UserAddresses;
use App\Models\CachedBuilding;
use App\Models\ProjectAmenity;
use App\Models\UnitInspection;
use App\Models\BuildingAmenity;
use App\Models\CrrApprovalType;
use App\Models\OrderingAmenity;
use App\Models\UserPhoneNumber;
use App\Models\CachedInspection;
use App\Models\OrderingBuilding;
use App\Models\UserOrganization;
use App\Models\AmenityInspection;
use App\Models\BuildingInspection;
use App\Http\Controllers\Controller;

class AuditController extends Controller
{
	private $htc_group_id;

	public function __construct()
	{
		
        $this->allitapc();

		$this->htc_group_id = 7;
		View::share('htc_group_id', $this->htc_group_id);
		// increase the memory for this controller
		ini_set('memory_limit', '8G'); // change as needed, as long as your system can support it

	}

	public function rerunCompliance(Audit $audit)
	{
		// if there are findings, we cannot rerun the compliance
		// dd($audit->findings->count(), count($audit->findings));
		if ($audit->findings->where('cancelled_at',NULL)->count() < 1) {
			$auditsAhead = Job::where('queue', 'compliance')->count();
			$audit->rerun_compliance = 1;
			$audit->save();

			// if($auditsAhead == 0){
			//     ComplianceSelectionJob::dispatch($audit)->onQueue('compliance');
			// }

			return 1;
			//return '<p>Your request to re-run the compliance selection has been added to the queue. There are currently '.$auditsAhead.' audit(s) ahead of your request.</p><p>It usually takes approximately 1-10 minutes per audit selection depending on the size of the project.<p>';
		} else {
			return 0;
			//return '<p>I am sorry, we cannot rerun your audit as it currently has findings against amenities on that project. You must finalize the current audit in order to refresh the program to unit association.</p>';
		}
	}

	public function runCompliance(Project $project)
	{
		// this either reruns compliance if there is an active audit or creates an audit and run compliance
		if ($project->currentAudit()) {
			$this->rerunCompliance($project->currentAudit());
		} else {
			// $new_audit = new Audit([
			//     'project_id' => $project->id,
			//     'development_key' => $project->project_key,

			// ]);
			// $new_audit->save();
		}
	}

	public function buildingsFromAudit($audit, Request $request)
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
			$duplicates = []; // to store amenity_inspection_ids for each amenity_id to see when we have duplicates
			$previous_name = []; // used in case we have building-level amenity duplicates

			foreach ($buildings as $building) {
				if (null === $building->building_id && null === $building->amenity_inspection_id) {
					// this is a building-level amenity without a clear link to the amenity inspection
					// we need to add amenity_inspection_id

					// first there may already be an amenity_inspection with cachedbuilding_id
					$amenity_inspection = AmenityInspection::where('audit_id', '=', $audit)
						->where('amenity_id', '=', $building->amenity_id)
						->whereNull('building_id')
						->where('cachedbuilding_id', '=', $building->id)
						->first();
					if ($amenity_inspection) {
						$building->amenity_inspection_id = $amenity_inspection->id;
						$building->save();

						// $amenity_inspection->cachedbuilding_id = $building->id;
						// $amenity_inspection->save();
					} else {
						$amenity_inspection = AmenityInspection::where('audit_id', '=', $audit)
							->where('amenity_id', '=', $building->amenity_id)
							->whereNull('building_id')
							->whereNull('cachedbuilding_id')
							->first();
						if ($amenity_inspection) {
							$building->amenity_inspection_id = $amenity_inspection->id;
							$building->save();

							$amenity_inspection->cachedbuilding_id = $building->building->id;
							//$amenity_inspection->cachedbuilding_id = $building->id;
							$amenity_inspection->save();
						}
					}
					//dd($building, $amenity_inspection);
				} elseif (null === $building->building_id && null !== $building->amenity_inspection_id && $building->amenity() === null) {
					// we had a case where a amenity_inspection_id was referring to a record on the wrong audit
					$building->amenity_inspection_id = null;
					$building->save();
				}
				if ($building && isset($updateRow)) {
					$building->recount_findings();
				}
				if (null === $building->building_id && $building->amenity_id && null === $building->amenity_inspection_id) {
					// this is an amenity with no link to the amenity inspection -> there might be issues in case of duplicates.

					$amenity_id = $building->amenity_id;
					$audit_id = $building->audit_id;

					// we look to see if amenityinspection has a record for this amenity
					if (!array_key_exists($amenity_id, $duplicates)) {
						$duplicates[$amenity_id] = [];
					}

					$cached_building = CachedBuilding::where('audit_id', '=', $audit_id)
						->where('amenity_id', '=', $amenity_id)
						->whereNotIn('amenity_inspection_id', $duplicates[$amenity_id])
						->first();

					if ($cached_building) {
						$duplicates[$amenity_id][] = $cached_building->amenity_inspection_id;
						$building->amenity_inspection_id = $cached_building->amenity_inspection_id;
						$building->save();
					}
				}
				if (null === $building->building_id && $building->building) {
					// naming duplicates should only apply to amenities
					if (!array_key_exists($building->building->building_name, $previous_name)) {
						$previous_name[$building->building->building_name]['counter'] = 1; // counter
						$first_encounter = $building->building;
					} else {
						if (1 == $previous_name[$building->building->building_name]['counter']) {
							// this is our second encounter, change the first one since we now know there are more
							$first_encounter->building_name = $first_encounter->building_name . ' 1';
						}

						$previous_name[$building->building->building_name]['counter'] = $previous_name[$building->building->building_name]['counter'] + 1;
						$building->building->building_name = $building->building->building_name . ' ' . $previous_name[$building->building->building_name]['counter'];
					}
				}
			}
		}

		// end optimized code
		// count buildings & count ordering_buildings

		// we need to do a few checks to fix incorrect data
		// 1) when amenity_inspection_id has duplicates, rebuild with correct references
		// 2) when amenity_inspection_id is null, make sure we get the next amenity_inspection reference and not a duplicate

		// are there duplicates in amenity_inspection_id?
		// $check_ordering_buildings = OrderingBuilding::whereNull('building_id')
		//                                 ->where('audit_id', '=', $audit)
		//                                 ->where('user_id', '=', Auth::user()->id)
		//                                 ->pluck('amenity_inspection_id')
		//                                 ->toArray();

		// if(count($check_ordering_buildings) !== count(array_unique($check_ordering_buildings))){
		//     // rebuild with correct references
		//     // each orderingbuilding should corresponds to a cachedbuilding that has a correct amenity_inspection_id
		//     $ordering_buildings = OrderingBuilding::whereNull('building_id')
		//                                 ->where('audit_id', '=', $audit)
		//                                 ->where('user_id', '=', Auth::user()->id)
		//                                 ->get();

		//     $already_in_there_array = array();
		//     foreach($ordering_buildings as $ordering_building){
		//         if( in_array($ordering_building->amenity_inspection_id, $already_in_there_array) ){

		//             // get a corresponding cachedbuilding record that has a different amenity_inspection_id (and not already in there)
		//             $checked_cached_building = CachedBuilding::where('audit_id', '=', $audit)->where('amenity_id', '=', $ordering_building->amenity_id)->whereNotIn('amenity_inspection_id',$already_in_there_array)->first();

		//             // update record
		//             if($checked_cached_building){
		//                 $ordering_building->amenity_inspection_id = $checked_cached_building->amenity_inspection_id;
		//                 $ordering_building->save();
		//             }

		//             $already_in_there_array[] = $ordering_building->amenity_inspection_id;
		//         }else{

		//             $already_in_there_array[] = $ordering_building->amenity_inspection_id;

		//         }
		//     }
		// }

		// if (CachedBuilding::where('audit_id', '=', $audit)->count() != OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->count() && CachedBuilding::where('audit_id', '=', $audit)->count() != 0) {
		//     // this case shouldn't happen
		//     // delete all ordered records
		//     // reorder them
		//     // OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->delete();
		// }

		// if (OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->count() == 0 && CachedBuilding::where('audit_id', '=', $audit)->count() != 0) {
		// if ordering_buildings is empty, create a default entry for the ordering
		// if there is a previous audit and that user has ordering records, use those as default

		// also make sure that we have the same buildings now

		// is there a previous record in the OrderingBuilding for the same project and user?
		// if yes, we get it and check if all the buildings match
		// if they do we copy the ordering
		// if they don't we apply the default ordering

		// if not we apply the default ordering

		// $default_ordering_needed = 0;

		// // get the project from audit_id
		// $project_id = Audit::where('id', '=', $audit)->first()->project_id;

		// // is there a previous record in the OrderingBuilding for the same project and user?
		// $previous_ordering_records_check = OrderingBuilding::where('project_id', '=', $project_id)->where('user_id', '=', Auth::user()->id)->where('audit_id', '!=', $audit)->orderBy('audit_id', 'desc')->first();

		// if ($previous_ordering_records_check) {
		//     $previous_ordering_records_audit_id = $previous_ordering_records_check->audit_id;

		//     // if yes, we get it and check if all the buildings match
		//     $previous_ordering_records = OrderingBuilding::where('project_id', '=', $project_id)->where('user_id', '=', Auth::user()->id)->where('audit_id', '=', $previous_ordering_records_audit_id)->orderBy('order', 'asc')->get();

		//     // compare count first
		//     if (count($previous_ordering_records) == CachedBuilding::where('audit_id', '=', $audit)->count()) {
		//         // check if buildings are the same
		//         foreach ($previous_ordering_records as $ordering_building) {
		//             if (!CachedBuilding::where('audit_id', '=', $audit)->where('building_id', '=', $ordering_building->building_id)->count()) {
		//                 // use the default ordering
		//                 $default_ordering_needed = 1;
		//                 break;
		//             }
		//         }

		//         // all good, let's copy the previous ordering
		//         if ($default_ordering_needed == 0) {
		//             foreach ($previous_ordering_records as $ordering_building) {
		//                 $ordering = new OrderingBuilding([
		//                     'user_id' => Auth::user()->id,
		//                     'audit_id' => $audit,
		//                     'project_id' => $ordering_building->project_id,
		//                     'building_id' => $ordering_building->building_id,
		//                     'amenity_id' => $ordering_building->amenity_id,
		//                     'amenity_inspection_id' => $ordering_building->amenity_inspection_id,
		//                     'order' => $ordering_building->order,
		//                 ]);
		//                 $ordering->save();
		//             }
		//         }
		//     } else {
		//         // use the default ordering
		//         $default_ordering_needed = 1;
		//     }

		// } else {
		//     // use the default ordering
		//     $default_ordering_needed = 1;
		// }

		// use the default ordering
		// if ($default_ordering_needed) {

		//     $buildings = CachedBuilding::where('audit_id', '=', $audit)->orderBy('id', 'desc')->get();

		//     $i = 1;

		//     foreach ($buildings as $building) {
		//         if ($building->building_id !== null) {
		//             $ordering = new OrderingBuilding([
		//                 'user_id' => Auth::user()->id,
		//                 'audit_id' => $audit,
		//                 'project_id' => $building->project_id,
		//                 'building_id' => $building->building_id,
		//                 'amenity_id' => 0,
		//                 'order' => $i,
		//             ]);
		//             $ordering->save();
		//             $i++;
		//         } else {
		//             // it is a building-level amenity
		//             $ordering = new OrderingBuilding([
		//                 'user_id' => Auth::user()->id,
		//                 'audit_id' => $audit,
		//                 'project_id' => $building->project_id,
		//                 'building_id' => null,
		//                 'amenity_id' => $building->amenity_id,
		//                 'amenity_inspection_id' => $building->amenity_inspection_id,
		//                 'order' => $i,
		//             ]);
		//             $ordering->save();
		//             $i++;
		//         }
		//     }
		// }

		//}

		// $buildings = OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->orderBy('order', 'asc')->get();

		// in the case of an amenity at the building level (like a parking lot), there won't be a clear link between the amenityinspection and the cachedbuilding

		//return $buildings;
		$amenities_query = AmenityInspection::where('audit_id', $audit)->with('amenity', 'user', 'building.units');
		$amenities = $amenities_query->get();
		// echo 'ets';
		// return 12;

		return view('dashboard.partials.audit_buildings', compact('audit', 'target', 'buildings', 'context', 'amenities'));
	}

	public function reorderBuildingsFromAudit($audit, Request $request)
	{
		$building = $request->get('building'); // this is building_id not cached_building_id
		$amenity = $request->get('amenity');
		$amenity_inspection = $request->get('amenity_inspection');
		$project = $request->get('project');
		$index = $request->get('index');

		if (null !== $building) {
			$current_ordering = OrderingBuilding::where('audit_id', '=', $audit)
				->where('user_id', '=', Auth::user()->id)
				->where(function ($query) use ($building) {
					$query->where('building_id', '!=', $building)
						->orwhereNull('building_id');
				})
				->orderBy('order', 'asc')
				->get()->toArray();

			$inserted = [[
				'user_id' => Auth::user()->id,
				'audit_id' => $audit,
				'project_id' => $project,
				'building_id' => $building,
				'amenity_id' => 0,
				'amenity_inspection_id' => 0,
				'order' => $index,
			]];
		} else {
			// select all building orders except for the one we want to reorder
			$current_ordering = OrderingBuilding::where('audit_id', '=', $audit)
				->where('user_id', '=', Auth::user()->id)
				->where(function ($query) use ($amenity_inspection) {
					$query->where('amenity_inspection_id', '!=', $amenity_inspection)
						->orwhereNull('amenity_inspection_id');
				})
				->orderBy('order', 'asc')
				->get()->toArray();

			$inserted = [[
				'user_id' => Auth::user()->id,
				'audit_id' => $audit,
				'project_id' => $project,
				'building_id' => null,
				'amenity_id' => $amenity,
				'amenity_inspection_id' => $amenity_inspection,
				'order' => $index,
			]];
		}

		// insert the building ordering in the array
		$reordered_array = $current_ordering;
		array_splice($reordered_array, $index, 0, $inserted);

		// delete previous ordering
		OrderingBuilding::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->delete();

		// clean-up the ordering and store
		foreach ($reordered_array as $key => $ordering) {
			$new_ordering = new OrderingBuilding([
				'user_id' => $ordering['user_id'],
				'audit_id' => $ordering['audit_id'],
				'project_id' => $ordering['project_id'],
				'building_id' => $ordering['building_id'],
				'amenity_id' => $ordering['amenity_id'],
				'amenity_inspection_id' => $ordering['amenity_inspection_id'],
				'order' => $key + 1,
			]);
			$new_ordering->save();
		}
	}

	public function reorderUnitsFromAudit($audit, $building, Request $request)
	{
		$unit = $request->get('unit');
		$index = $request->get('index');

		// select all building orders except for the one we want to reorder
		$current_ordering = OrderingUnit::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->where('building_id', '=', $building)->where('unit_id', '!=', $unit)->orderBy('order', 'asc')->get()->toArray();

		$inserted = [[
			'user_id' => Auth::user()->id,
			'audit_id' => $audit,
			'building_id' => $building,
			'unit_id' => $unit,
			'order' => $index,
		]];

		// insert the building ordering in the array
		$reordered_array = $current_ordering;
		array_splice($reordered_array, $index, 0, $inserted);

		// delete previous ordering
		OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->delete();

		// clean-up the ordering and store
		foreach ($reordered_array as $key => $ordering) {
			$new_ordering = new OrderingUnit([
				'user_id' => $ordering['user_id'],
				'audit_id' => $ordering['audit_id'],
				'building_id' => $ordering['building_id'],
				'unit_id' => $ordering['unit_id'],
				'order' => $key + 1,
			]);
			$new_ordering->save();
		}
	}

	public function getProjectContact(Project $project)
	{
		return view('modals.project-contact', compact('project'));
	}

	public function detailsFromBuilding($audit, $building, Request $request)
	{
		$target = $request->get('target');
		$targetaudit = $request->get('targetaudit');
		$context = $request->get('context');

		// count buildings & count ordering_buildings
		if (OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->count() == 0 && CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->count() != 0) {
			// if ordering_buildings is empty, create a default entry for the ordering
			$details = CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->orderBy('id', 'desc')->get();

			$i = 1;
			$new_ordering = [];

			foreach ($details as $detail) {
				// fix total findings if needed
				$detail->recount_findings();

				$ordering = new OrderingUnit([
					'user_id' => Auth::user()->id,
					'audit_id' => $audit,
					'building_id' => $detail->building_id,
					'unit_id' => $detail->id,
					'order' => $i,
				]);
				$ordering->save();
				$i++;
			}
		} elseif (OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->count() != CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->count()) {

			// there is a mismatch, go through each cachedunit and add the missing ones at the end of the ordering
			$details = CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->orderBy('id', 'desc')->get();

			// highest ordering
			$last_ordering = OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->orderBy('order', 'desc')->first()->order;
			$new_ordering = $last_ordering;

			foreach ($details as $detail) {
				// fix total findings if needed
				$detail->recount_findings();

				//dd(OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $detail->building_id)->where('user_id', '=', Auth::user()->id)->where('unit_id','=',$detail->id)->count());

				if (OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $detail->building_id)->where('user_id', '=', Auth::user()->id)->where('unit_id', '=', $detail->id)->count() == 0) {
					$new_ordering++;
					//dd($detail);
					$ordering = new OrderingUnit([
						'user_id' => Auth::user()->id,
						'audit_id' => $audit,
						'building_id' => $detail->building_id,
						'unit_id' => $detail->id,
						'order' => $new_ordering,
					]);
					$ordering->save();
				}
			}
		} elseif (CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->count() != OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->count() && CachedUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->count() != 0) {
			$details = null;
		}

		$details = OrderingUnit::where('audit_id', '=', $audit)->where('building_id', '=', $building)->where('user_id', '=', Auth::user()->id)->orderBy('order', 'asc')->with('unit')->get();
		foreach ($details as $detail) {
			// fix total findings if needed... the brutal way
			$detail->unit->recount_findings();
		}

		// swap needs project_id

		$project_id = CachedAudit::where('audit_id', '=', $audit)->first()->project_id;

		return view('dashboard.partials.audit_building_details', compact('audit', 'target', 'building', 'details', 'targetaudit', 'context', 'project_id'));
	}

	public function inspectionFromBuilding($audit_id, $building_id, Request $request)
	{
		//dd($audit_id, $building_id);
		$target = $request->get('target');
		$rowid = $request->get('rowid');
		$context = $request->get('context');
		$inspection = 'test';

		$audit = Audit::where('id', '=', $audit_id)->first();

		if (CachedInspection::first()) {
			$data['detail'] = CachedInspection::first();
			$data['menu'] = $data['detail']->menu_json;
		} else {
			$data['detail'] = null;
			$data['menu'] = null;
		}

		// forget cachedinspection, populate without
		// details: unit_id, building_id, project_id
		$data['detail'] = collect([
			'unit_id' => null,
			'building_id' => $building_id,
			'project_id' => $audit->project_id,
		]);

		// if unit or building is completed, the icon for the unit turn solid green
		// check circle by the name of amenity to mark amenity complete

		$data['menu'] = collect([
			['name' => 'SITE AUDIT', 'icon' => 'a-mobile-home', 'status' => '', 'style' => '', 'action' => 'site_audit', 'audit_id' => $audit->id, 'building_id' => $building_id, 'unit_id' => null],
			['name' => 'FILE AUDIT', 'icon' => 'a-folder', 'status' => '', 'style' => '', 'action' => 'file_audit', 'audit_id' => $audit->id, 'building_id' => $building_id, 'unit_id' => null],
			['name' => 'COMPLETE', 'icon' => 'a-circle-checked', 'status' => '', 'style' => 'margin-top:30px;', 'action' => 'complete', 'audit_id' => $audit->id, 'building_id' => $building_id, 'unit_id' => null],
			['name' => 'SUBMIT', 'icon' => 'a-avatar-star', 'status' => '', 'style' => 'margin-top:30px;', 'action' => 'submit', 'audit_id' => $audit->id, 'building_id' => $building_id, 'unit_id' => null],
		]);

		// $data['amenities'] = CachedAmenity::where('audit_id', '=', $audit_id)->where('building_id', '=', $building_id)->get();
		// count amenities & count ordering_amenities
		$ordered_amenities = OrderingAmenity::where('audit_id', '=', $audit_id)->whereNull('unit_id')->where('user_id', '=', Auth::user()->id)->whereHas('amenity_inspection');
		if ($building_id) {
			$ordered_amenities = $ordered_amenities->where('building_id', '=', $building_id);
		}
		//$ordered_amenities = $ordered_amenities->get();

		$ordered_amenities_count = $ordered_amenities->count();

		$amenities_count = AmenityInspection::where('audit_id', '=', $audit_id);
		if ($building_id) {
			$amenities_count = $amenities_count->where('building_id', '=', $building_id);
		}
		$amenities_count = $amenities_count->count();

		if ($amenities_count != $ordered_amenities_count && 0 != $amenities_count) {
			// this shouldn't happen
			// reset ordering
			$ordered_amenities = OrderingAmenity::where('audit_id', '=', $audit_id)->whereNull('unit_id')->where('user_id', '=', Auth::user()->id);
			if ($building_id) {
				$ordered_amenities = $ordered_amenities->where('building_id', '=', $building_id);
			}
			$ordered_amenities->delete();
		}

		if (0 == $ordered_amenities_count && 0 != $amenities_count) {
			// if ordering_amenities is empty, create a default entry for the ordering
			// $amenities = CachedAmenity::where('audit_id', '=', $audit_id);
			$amenities = AmenityInspection::where('audit_id', '=', $audit_id);
			if ($building_id) {
				$amenities = $amenities->where('building_id', '=', $building_id);
			}
			$amenities = $amenities->orderBy('id', 'desc')->get();

			$i = 1;
			$new_ordering = [];

			foreach ($amenities as $amenity) {
				$ordering = new OrderingAmenity([
					'user_id' => Auth::user()->id,
					'audit_id' => $audit_id,
					'building_id' => $building_id,
					'amenity_id' => $amenity->amenity_id,
					'amenity_inspection_id' => $amenity->id,
					'order' => $i,
				]);
				$ordering->save();
				$i++;
			}
		}

		$amenities = OrderingAmenity::where('audit_id', '=', $audit_id)->whereNull('unit_id')->where('user_id', '=', Auth::user()->id)->whereHas('amenity_inspection');
		if ($building_id) {
			$amenities = $amenities->where('building_id', '=', $building_id);
		}
		$amenities = $amenities->orderBy('order', 'asc')->with('amenity')->get(); //->pluck('amenity')->flatten()

		$data_amenities = [];

		// manage name duplicates, number them based on their id
		$amenity_names = [];
		foreach ($amenities as $amenity) {
			$amenity_names[$amenity->amenity->amenity_description][] = $amenity->amenity_inspection_id;
		}

		foreach ($amenities as $amenity) {
			if (null !== $amenity->amenity_inspection->auditor_id) {
				$auditor_initials = $amenity->amenity_inspection->user->initials();
				$auditor_name = $amenity->amenity_inspection->user->full_name();
				$auditor_id = $amenity->amenity_inspection->user->id;
				$auditor_color = $amenity->amenity_inspection->user->badge_color;
			} else {
				$auditor_initials = '<i class="a-avatar-plus_1"></i>';
				$auditor_name = 'CLICK TO ASSIGN TO AUDITOR';
				$auditor_color = '';
				$auditor_id = 0;
			}

			if (null == $amenity->amenity_inspection->completed_date_time) {
				$completed_icon = 'a-circle';
			} else {
				$completed_icon = 'a-circle-checked ok-actionable';
			}

			if (1 == $amenity->amenity->file) {
				$status = ' fileaudit';
			} else {
				$status = ' siteaudit';
			}

			// check for name duplicates and assign a #
			$key = array_search($amenity->amenity_inspection_id, $amenity_names[$amenity->amenity->amenity_description]);
			if ($key > 0) {
				$key = $key + 1;
				$name = $amenity->amenity->amenity_description . ' ' . $key;
			} else {
				$name = $amenity->amenity->amenity_description;
			}

			// check if this amenity has findings (to disable trash)
			if (Finding::where('amenity_id', '=', $amenity->amenity_inspection_id)->where('audit_id', '=', $audit_id)->where('cancelled_at',NULL)->count()) {
				$has_findings = 1;
			} else {
				$has_findings = 0;
			}

			$data_amenities[] = [
				'id' => $amenity->amenity_inspection_id,
				'audit_id' => $amenity->audit_id,
				'name' => $name,
				'status' => $status,
				'auditor_initials' => $auditor_initials,
				'auditor_id' => $auditor_id,
				'auditor_name' => $auditor_name,
				'auditor_color' => $auditor_color,
				'finding_nlt_status' => '',
				'finding_lt_status' => '',
				'finding_sd_status' => '',
				'finding_photo_status' => '',
				'finding_comment_status' => '',
				'finding_copy_status' => '',
				'finding_trash_status' => '',
				'building_id' => $amenity->building_id,
				'unit_id' => 0,
				'completed_icon' => $completed_icon,
				'has_findings' => $has_findings,
			];
		}

		$data['amenities'] = $data_amenities;

		$data['comments'] = CachedComment::where('parent_id', '=', null)->with('replies')->get();

		//dd("new approach");

		return response()->json($data);
		//return view('dashboard.partials.audit_building_inspection', compact('audit_id', 'target', 'detail_id', 'building_id', 'detail', 'inspection', 'areas', 'rowid'));
	}

	public function inspectionFromBuildingDetail($audit_id, $building_id, $detail_id, Request $request)
	{
		$target = $request->get('target');
		$rowid = $request->get('rowid');
		$context = $request->get('context');
		$inspection = 'test';

		//dd($audit_id, $building_id, $detail_id);
		/*
		"6410"
		"16725"
		"1005405"
		 */

		// Fetch inspection data from different models:
		// $details (cached_audit_inspections)
		// $areas (cached_audit_inspection_areas)
		// $comments (cached_audit_inspection_comments)
		//
		// inspectionDetails(1005325,23060,6659,0,6659,1,'audits');

		// $detail_id is the cachedunit id
		$cached_unit = CachedUnit::where('id', '=', $detail_id)->first();
		$unit = $cached_unit->unit;

		$audit = Audit::where('id', '=', $audit_id)->first();

		$data['detail'] = collect([
			'unit_id' => $unit->id,
			'building_id' => $building_id,
			'project_id' => $audit->project_id,
		]);

		$data['menu'] = collect([
			['name' => 'SITE AUDIT', 'icon' => 'a-mobile-home', 'status' => 'active', 'style' => '', 'action' => 'site_audit', 'audit_id' => $audit->id, 'building_id' => $building_id, 'unit_id' => $unit->id],
			['name' => 'FILE AUDIT', 'icon' => 'a-folder', 'status' => '', 'style' => '', 'action' => 'file_audit', 'audit_id' => $audit->id, 'building_id' => $building_id, 'unit_id' => $unit->id],
			['name' => 'COMPLETE', 'icon' => 'a-circle-checked', 'status' => '', 'style' => 'margin-top:30px;', 'action' => 'complete', 'audit_id' => $audit->id, 'building_id' => $building_id, 'unit_id' => $unit->id],
			['name' => 'SUBMIT', 'icon' => 'a-avatar-star', 'status' => '', 'style' => 'margin-top:30px;', 'action' => 'submit', 'audit_id' => $audit->id, 'building_id' => $building_id, 'unit_id' => $unit->id],
		]);

		$ordered_amenities = OrderingAmenity::where('audit_id', '=', $audit_id)->where('user_id', '=', Auth::user()->id)->whereHas('amenity_inspection');
		if ($detail_id) {
			$ordered_amenities = $ordered_amenities->where('unit_id', '=', $unit->id);
		}

		$ordered_amenities_count = $ordered_amenities->count();

		$amenities_count = AmenityInspection::where('audit_id', '=', $audit_id);
		if ($detail_id) {
			$amenities_count = $amenities_count->where('unit_id', '=', $unit->id);
		}
		$amenities_count = $amenities_count->count();

		if ($amenities_count != $ordered_amenities_count && 0 != $amenities_count) {
			// this shouldn't happen
			// reset ordering
			$ordered_amenities = OrderingAmenity::where('audit_id', '=', $audit_id)->where('user_id', '=', Auth::user()->id);
			if ($detail_id) {
				$ordered_amenities = $ordered_amenities->where('unit_id', '=', $unit->id);
			}
			$ordered_amenities->delete();
		}

		if (0 == $ordered_amenities_count && 0 != $amenities_count) {
			// if ordering_amenities is empty, create a default entry for the ordering
			// $amenities = CachedAmenity::where('audit_id', '=', $audit_id);
			$amenities = AmenityInspection::where('audit_id', '=', $audit_id);
			if ($detail_id) {
				$amenities = $amenities->where('unit_id', '=', $unit->id);
			}
			$amenities = $amenities->orderBy('id', 'desc')->get();

			$i = 1;
			$new_ordering = [];

			foreach ($amenities as $amenity) {
				$ordering = new OrderingAmenity([
					'user_id' => Auth::user()->id,
					'audit_id' => $audit_id,
					'building_id' => $building_id,
					'unit_id' => $unit->id,
					'amenity_id' => $amenity->amenity_id,
					'amenity_inspection_id' => $amenity->id,
					'order' => $i,
				]);
				$ordering->save();
				$i++;
			}
		}

		$amenities = OrderingAmenity::where('audit_id', '=', $audit_id)->where('user_id', '=', Auth::user()->id)->whereHas('amenity_inspection');
		if ($detail_id) {
			$amenities = $amenities->where('unit_id', '=', $unit->id);
		}
		$amenities = $amenities->orderBy('order', 'asc')->with('amenity')->get(); //->pluck('amenity')->flatten()

		$data_amenities = [];

		// manage name duplicates, number them based on their id
		$amenity_names = [];
		foreach ($amenities as $amenity) {
			$amenity_names[$amenity->amenity->amenity_description][] = $amenity->amenity_inspection_id;
		}

		foreach ($amenities as $amenity) {
			//if(!$amenity->amenity_inspection){dd($amenity->id);} // 6093
			if (null !== $amenity->amenity_inspection->auditor_id) {
				$auditor_initials = $amenity->amenity_inspection->user->initials();
				$auditor_name = $amenity->amenity_inspection->user->full_name();
				$auditor_id = $amenity->amenity_inspection->user->id;
				$auditor_color = $amenity->amenity_inspection->user->badge_color;
			} else {
				$auditor_initials = '<i class="a-avatar-plus_1"></i>';
				$auditor_name = 'CLICK TO ASSIGN TO AUDITOR';
				$auditor_color = '';
				$auditor_id = 0;
			}

			if (null == $amenity->amenity_inspection->completed_date_time) {
				$completed_icon = 'a-circle';
			} else {
				$completed_icon = 'a-circle-checked ok-actionable';
			}

			if (1 == $amenity->amenity->file) {
				$status = ' fileaudit';
			} else {
				$status = ' siteaudit';
			}

			// check for name duplicates and assign a #
			$key = array_search($amenity->amenity_inspection_id, $amenity_names[$amenity->amenity->amenity_description]);
			if ($key > 0) {
				$key = $key + 1;
				$name = $amenity->amenity->amenity_description . ' ' . $key;
			} else {
				$name = $amenity->amenity->amenity_description;
			}

			// check if this amenity has findings (to disable trash)
			if (Finding::where('amenity_id', '=', $amenity->amenity_inspection_id)->where('audit_id', '=', $audit_id)->where('cancelled_at',NULL)->count()) {
				$has_findings = 1;
			} else {
				$has_findings = 0;
			}

			$data_amenities[] = [
				'id' => $amenity->amenity_inspection_id,
				'audit_id' => $amenity->audit_id,
				'name' => $name,
				'status' => $status,
				'auditor_id' => $auditor_id,
				'auditor_initials' => $auditor_initials,
				'auditor_name' => $auditor_name,
				'auditor_color' => $auditor_color,
				'finding_nlt_status' => '',
				'finding_lt_status' => '',
				'finding_sd_status' => '',
				'finding_photo_status' => '',
				'finding_comment_status' => '',
				'finding_copy_status' => '',
				'finding_trash_status' => '',
				'finding_file_status' => '',
				'building_id' => $amenity->building_id,
				'unit_id' => $amenity->unit_id,
				'completed_icon' => $completed_icon,
				'has_findings' => $has_findings,
			];
		}

		$data['amenities'] = $data_amenities;

		$data['comments'] = CachedComment::where('parent_id', '=', null)->with('replies')->get();

		return response()->json($data);
		//return view('dashboard.partials.audit_building_inspection', compact('audit_id', 'target', 'detail_id', 'building_id', 'detail', 'inspection', 'areas', 'rowid'));
	}

	public function deleteAmenity($amenity_id, $audit_id, $building_id, $unit_id, $element = '')
	{
		// deleteAmenity('building-audits-r-1', 6816, 0, 0, 13726, 0, 1);
		return view('modals.amenity-delete', compact('amenity_id', 'audit_id', 'building_id', 'unit_id', 'element'));
	}

	public function deleteFindingAmenity($amenity_id, $audit_id, $building_id, $unit_id, $element = '')
	{
		// deleteAmenity('building-audits-r-1', 6816, 0, 0, 13726, 0, 1);
		return view('modals.findings-amenity-delete', compact('amenity_id', 'audit_id', 'building_id', 'unit_id', 'element'));
	}

	public function saveDeleteAmenity(Request $request)
	{
		$comment = ($request->get('comment') !== null) ? $request->get('comment') : '';
		$amenity_id = $request->get('amenity_id');
		$audit_id = $request->get('audit_id');
		$building_id = $request->get('building_id');
		$unit_id = $request->get('unit_id');
		$element = $request->get('element');

		//dd($comment, $amenity_id, $audit_id, $building_id, $unit_id);
		/*
		"blah"
		"6230"
		"6410"
		"16725"
		"0"
		 */

		$project_id = Audit::where('id', '=', $audit_id)->first()->project_id;

		// check if the amenity has findings
		if (Finding::where('amenity_id', '=', $amenity_id)->where('audit_id', '=', $audit_id)->count()) {
			return 0;
		} else {
			if ('null' != $unit_id && null !== $unit_id && 0 != $unit_id) {
				// dd("unit", $comment, $amenity_id, $audit_id, $building_id, $unit_id);

				$amenity_inspection = AmenityInspection::where('id', '=', $amenity_id)->first();
				$ordering_amenities = OrderingAmenity::where('audit_id', '=', $audit_id)->where('amenity_inspection_id', '=', $amenity_id)->where('unit_id', '=', $unit_id)->first();
				if (null !== $amenity_inspection) {
					$unit_amenity = UnitAmenity::where('unit_id', '=', $unit_id)->where('amenity_id', '=', $amenity_inspection->amenity_id)->first();
					$amenity_inspection->delete();
				} else {
					$unit_amenity = null;
					// $unit_amenity->delete();
				}

				if (null !== $ordering_amenities) {
					$ordering_amenities->delete();
				}

				$new_comment = new Comment([
					'user_id' => Auth::user()->id,
					'project_id' => $project_id,
					'audit_id' => $audit_id,
					'amenity_id' => $amenity_id,
					'unit_id' => $unit_id,
					'building_id' => $building_id,
					'comment' => $comment,
					'recorded_date' => date('Y-m-d H:i:s', time()),
				]);
				$new_comment->save();

				// reload auditor names at the unit and building row levels
				$reload_auditors = $this->reload_auditors($audit_id, $unit_id, null);
				$unit_auditors = $reload_auditors['unit_auditors'];
				$building_auditors = $reload_auditors['building_auditors'];

				$data['element'] = $element;
				$data['auditor'] = ['unit_auditors' => $unit_auditors, 'building_auditors' => $building_auditors, 'unit_id' => $unit_id, 'building_id' => $building_id, 'audit_id' => $audit_id];

				$unit = CachedUnit::where('unit_id', '=', $unit_id)->first();
				if ($unit && $unit->amenity_totals() != $unit->type_total) {
					$unit->type_total = $unit->amenity_totals();
					$unit->save();
				}
				$data['amenity_count'] = $unit->amenity_totals();
				$data['amenity_count_id'] = $audit_id . $unit->building_id . $unit_id;

				return $data;
			} elseif ('null' != $building_id && null !== $building_id && 0 != $building_id) {
				// dd("building", $comment, $amenity_id, $audit_id, $building_id, $unit_id);
				$amenity_inspection = AmenityInspection::where('id', '=', $amenity_id)->first();
				$ordering_amenities = OrderingAmenity::where('audit_id', '=', $audit_id)->where('amenity_inspection_id', '=', $amenity_id)->whereNull('unit_id')->where('building_id', '=', $building_id)->first();
				$building_amenity = BuildingAmenity::where('building_id', '=', $building_id)->where('amenity_id', '=', $amenity_inspection->amenity_id)->first();

				$amenity_inspection->delete();
				if (null !== $ordering_amenities) {
					$ordering_amenities->delete();
				}
				if (null !== $building_amenity) {
					$building_amenity->delete();
				}
				// $ordering_amenities->delete();
				// $building_amenity->delete();

				$new_comment = new Comment([
					'user_id' => Auth::user()->id,
					'project_id' => $project_id,
					'audit_id' => $audit_id,
					'amenity_id' => $amenity_id,
					'unit_id' => $unit_id,
					'building_id' => $building_id,
					'comment' => $comment,
					'recorded_date' => date('Y-m-d H:i:s', time()),
				]);
				$new_comment->save();

				// reload auditor names at the unit and building row levels
				$reload_auditors = $this->reload_auditors($audit_id, $unit_id, $building_id);
				$unit_auditors = $reload_auditors['unit_auditors'];
				$building_auditors = $reload_auditors['building_auditors'];

				$data['element'] = $element;
				$data['auditor'] = ['unit_auditors' => $unit_auditors, 'building_auditors' => $building_auditors, 'unit_id' => $unit_id, 'building_id' => $building_id, 'audit_id' => $audit_id];
				$data['amenity_count'] = '';
				$data['amenity_count_id'] = '';

				return $data;
			} else {
				// TBD - we don't have a button yet for this
				// project_amenities
				// ordering_amenities
				// amenity_inspection
				$amenity_inspection = AmenityInspection::where('id', '=', $amenity_id)->first();
				$ordering_buildings = OrderingBuilding::where('audit_id', '=', $audit_id)->where('amenity_inspection_id', '=', $amenity_id)->first();
				$cached_building = CachedBuilding::where('audit_id', '=', $audit_id)->where('amenity_inspection_id', '=', $amenity_id)->first();
				if ($amenity_inspection) {
					$project_amenity = ProjectAmenity::where('project_id', '=', $project_id)->where('amenity_id', '=', $amenity_inspection->amenity_id)->first();
					$amenity_inspection->delete();
					$project_amenity->delete();
				}
				if (null !== $ordering_buildings) {
					$ordering_buildings->delete();
				}
				if (null !== $cached_building) {
					$cached_building->delete();
				}
				// $ordering_buildings->delete();
				// $cached_building->delete();

				$new_comment = new Comment([
					'user_id' => Auth::user()->id,
					'project_id' => $project_id,
					'audit_id' => $audit_id,
					'amenity_id' => $amenity_id,
					'unit_id' => null,
					'building_id' => null,
					'comment' => $comment,
					'recorded_date' => date('Y-m-d H:i:s', time()),
				]);
				$new_comment->save();

				$data['element'] = $element;

				return $data;
			}
		}

		return 0;
	}

	public function propertyMarkComplete($amenity_id, $audit_id, $building_id, $unit_id, $toplevel, $building_option = 0)
	{
		return view('modals.property-amenities-complete', compact('amenity_id', 'audit_id', 'building_id', 'unit_id', 'toplevel', 'building_option'));
	}

	public function markCompleted(Request $request, $amenity_id, $audit_id, $building_id, $unit_id, $toplevel, $building_option = 0)
	{
		// This is the helper method to store a session for hiding confirmation in findings modals
		modal_confirm($request);
		if (0 == $amenity_id) {
			if ('null' != $unit_id && 0 != $unit_id) {
				// the complete button was clicked at the unit level
				$amenity_inspections = AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit_id)->get();
			} else {
				if (1 == $toplevel) {
					$amenity_inspections = AmenityInspection::where('audit_id', '=', $audit_id)->whereNull('unit_id')->whereNull('building_id')->get();
				} else {
					// the complete button was clicked at the building level
					if (2 == $building_option) {
						$units = UnitInspection::where('audit_id', $audit_id)->where('building_id', '=', $building_id)
							->pluck('unit_id');
						$amenity_inspections = AmenityInspection::where('audit_id', '=', $audit_id)->whereIn('unit_id', $units)->orWhere('building_id', '=', $building_id)->get();
					} else {
						$amenity_inspections = AmenityInspection::where('audit_id', '=', $audit_id)->where('building_id', '=', $building_id)->whereNull('unit_id')->get();
					}
				}
			}
			foreach ($amenity_inspections as $amenity_inspection) {
				// if an amenity has already been completed, do not update the date
				if (null === $amenity_inspection->completed_date_time) {
					$amenity_inspection->completed_date_time = date('Y-m-d H:i:s', time());
					$amenity_inspection->save();
				}
			}
			// Already completed amenites in buliding and unit level are not marked complete, below code make it complete
			if (2 == $building_option) {
				$buildings = BuildingInspection::where('audit_id', $audit_id)->where('building_id', $building_id)->get();
				foreach ($buildings as $key => $building) {
					$building->complete = 1;
					$building->save();
				}
			} elseif (1 == $building_option) {
				// Check if building has units
				$units = UnitInspection::where('audit_id', $audit_id)->where('building_id', '=', $building_id)
					->count();
				if (0 == $units) {
					$buildings = BuildingInspection::where('audit_id', $audit_id)->where('building_id', $building_id)->get();
					foreach ($buildings as $key => $building) {
						$building->complete = 1;
						$building->save();
					}
				}
			}
			//$this->markCompleted($amenity_inspections);
			//dd($amenity_id, $audit_id, $building_id, $unit_id, $amenity_inspections);
			return ['status' => 'complete'];
		} else {
			if ('null' != $unit_id && 0 != $unit_id) {
				$amenity_inspection = AmenityInspection::where('audit_id', '=', $audit_id)->where('id', '=', $amenity_id)->where('unit_id', '=', $unit_id)->first();
			} else {
				if (1 == $toplevel) {
					// clicked at the building level, but this is an amenity
					$amenity_inspection = AmenityInspection::where('audit_id', '=', $audit_id)->where('id', '=', $amenity_id)->whereNull('unit_id')->first();
				} else {
					$amenity_inspection = AmenityInspection::where('audit_id', '=', $audit_id)->where('id', '=', $amenity_id)->where('building_id', '=', $building_id)->whereNull('unit_id')->first();
				}
			}

			if (null !== $amenity_inspection->completed_date_time) {
				// it was already completed, we remove completion
				$amenity_inspection->completed_date_time = null;
				$amenity_inspection->save();

				return ['status' => 'not completed'];
			} else {
				$amenity_inspection->completed_date_time = date('Y-m-d H:i:s', time());
				$amenity_inspection->save();

				return ['status' => 'complete'];
			}
		}

		return 0;
	}

	public function assignAuditorToAmenity($amenity_id, $audit_id, $building_id, $unit_id, $element, $in_model = null)
	{
		// check if we are mass assigning
		if (0 == $amenity_id) {
			if (0 != $unit_id) {
				$amenity = 0;
				$name = 'Unit ' . CachedUnit::where('unit_id', '=', $unit_id)->first()->unit_name;
			} elseif (0 != $building_id) {
				$amenity = 0;
				$name = 'Building ' . CachedBuilding::where('building_id', '=', $building_id)->first()->building_name;
			} else {
				$amenity = 0;
				$audit = CachedAudit::where('audit_id', $audit_id)->with('inspection_items')->first();
				$name = 'Site ' . $audit->project->address->basic_address() . ' (swap)';
			}
		} else {
			if ('null' != $unit_id && 0 != $unit_id) {
				// $amenity = AmenityInspection::where('amenity_id', '=', $amenity_id)
				//     ->where('audit_id', '=', $audit_id)
				//     ->where('unit_id', '=', $unit_id)
				//     ->first();
				$amenity = AmenityInspection::where('id', '=', $amenity_id)
					->where('audit_id', '=', $audit_id)
					->where('unit_id', '=', $unit_id)
					->first();
				$name = 'Unit ' . Unit::where('id', '=', $unit_id)->first()->unit_name;
			} elseif (null === $building_id || 0 == $building_id) {
				// $amenity = AmenityInspection::where('amenity_id', '=', $amenity_id)
				//     ->where('audit_id', '=', $audit_id)
				//     ->whereNull('building_id')
				//     ->whereNull('unit_id')
				// //     ->first();
				// dd($amenity_id, $audit_id);
				$amenity = AmenityInspection::where('id', '=', $amenity_id)
					->where('audit_id', '=', $audit_id)
					->whereNull('building_id')
					->whereNull('unit_id')
					->first();
				$name = 'Building ' . CachedBuilding::where('id', '=', $amenity->cachedbuilding_id)->first()->building_name;
			} else {
				// $amenity = AmenityInspection::where('amenity_id', '=', $amenity_id)
				//     ->where('audit_id', '=', $audit_id)
				//     ->where('building_id', '=', $building_id)
				//     ->whereNull('unit_id')
				//     ->first();
				$amenity = AmenityInspection::where('id', '=', $amenity_id)
					->where('audit_id', '=', $audit_id)
					->where('building_id', '=', $building_id)
					->whereNull('unit_id')
					->first();
				$name = 'Building ' . CachedBuilding::where('building_id', '=', $building_id)->first()->building_name;
			}
		}

		$cached_audit = $auditors = CachedAudit::with('auditors.user')->where('audit_id', '=', $audit_id)->first();
		$lead_auditor_id = Audit::find($audit_id)->lead_user_id;
		$auditors = $cached_audit->auditors;
		if (count($auditors)) {
			$audit_user_ids = $auditors->pluck('user_id');
			array_merge($audit_user_ids->toArray(), [$lead_auditor_id]);
		} else {
			$audit_user_ids = [$lead_auditor_id];
		}
		$audit_users = User::whereIn('id', $audit_user_ids)->get();
		$current_auditor = null;

		return view('modals.auditor-amenity-assignment', compact('auditors', 'amenity', 'name', 'amenity_id', 'audit_id', 'building_id', 'unit_id', 'element', 'current_auditor', 'in_model', 'audit_users'));
	}

	public function swapAuditorToAmenity($amenity_id, $audit_id, $building_id, $unit_id, $auditor_id, $element, $in_model = null)
	{
		//$in_model = null; // we do not use this feature here.
		//dd($amenity_id, $audit_id, $building_id, $unit_id, $auditor_id, $element);
		if (0 != $amenity_id) {
			// $amenity = AmenityInspection::where('amenity_id', '=', $amenity_id)
			//     ->where('audit_id', '=', $audit_id)
			//     ->whereNull('building_id')
			//     ->whereNull('unit_id')
			//     ->first();
			$amenity = AmenityInspection::where('id', $amenity_id)
				->where('audit_id', '=', $audit_id)
				->whereNull('building_id')
				->whereNull('unit_id')
				->first();
			$name = 'Building ' . CachedBuilding::where('id', '=', $amenity->cachedbuilding_id)->first()->building_name . ' (swap)';
		} elseif (0 != $unit_id) {
			$amenity = 0;
			$name = 'Unit ' . CachedUnit::where('unit_id', '=', $unit_id)->first()->unit_name . ' (swap)';
		} elseif (0 != $building_id) {
			$amenity = 0;
			$name = 'Building ' . CachedBuilding::where('building_id', '=', $building_id)->first()->building_name . ' (swap)';
		} else {
			$amenity = 0;
			$audit = CachedAudit::where('audit_id', $audit_id)->with('inspection_items')->first();
			$name = 'Site ' . $audit->project->address->basic_address() . ' (swap)';
		}

		$current_auditor = User::where('id', '=', $auditor_id)->first();

		$cached_audit = $auditors = CachedAudit::with('auditors.user')->where('audit_id', '=', $audit_id)->first();
		$lead_auditor_id = Audit::find($audit_id)->lead_user_id;
		$auditors = $cached_audit->auditors;
		if (count($auditors)) {
			$audit_user_ids = $auditors->pluck('user_id');
			array_merge($audit_user_ids->toArray(), [$lead_auditor_id]);
		} else {
			$audit_user_ids = [$lead_auditor_id];
		}
		$audit_users = User::whereIn('id', $audit_user_ids)->get();

		return view('modals.auditor-amenity-assignment', compact('auditors', 'current_auditor', 'amenity', 'name', 'amenity_id', 'audit_id', 'building_id', 'unit_id', 'element', 'auditor_id', 'in_model', 'audit_users'));
	}

	public function saveSwapAuditorToAmenity(Request $request, $amenity_id, $audit_id, $building_id, $unit_id, $auditor_id)
	{
		$new_auditor_id = $request->get('new_auditor_id');

		if (0 == $amenity_id && 0 != $unit_id) {
			// make sure this id is already in the auditor's list for this audit
			if (AuditAuditor::where('audit_id', '=', $audit_id)->where('user_id', '=', $new_auditor_id)->first()) {
				$building = CachedBuilding::where('building_id', '=', $building_id)->first();
				$unit = CachedUnit::where('unit_id', '=', $unit_id)->first();
				$cached_unit_id = $unit->unit_id;

				$amenities = AmenityInspection::where('audit_id', '=', $audit_id)->where('auditor_id', '=', $auditor_id)->where('unit_id', '=', $unit->unit_id)->update([
					'auditor_id' => $new_auditor_id,
				]);

				$user = User::where('id', '=', $new_auditor_id)->first();

				$unit_auditor_ids = AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit_id)->whereNotNull('auditor_id')->whereNotNull('unit_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();

				$building_auditor_ids = [];
				$units = Unit::where('building_id', '=', $building_id)->get();
				foreach ($units as $unit) {
					$building_auditor_ids = array_merge($building_auditor_ids, \App\Models\AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit->id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
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

				$initials = $user->initials();
				$color = 'auditor-badge-' . $user->badge_color;

				return ['initials' => $initials, 'color' => $color, 'name' => $user->full_name(), 'unit_auditors' => $unit_auditors, 'building_auditors' => $building_auditors, 'unit_id' => $cached_unit_id, 'building_id' => $building->building_id];
			}
		} elseif (0 == $amenity_id && 0 != $building_id) {

			// make sure this id is already in the auditor's list for this audit
			if (AuditAuditor::where('audit_id', '=', $audit_id)->where('user_id', '=', $new_auditor_id)->first()) {
				$building = CachedBuilding::where('building_id', '=', $building_id)->first();

				$amenities = AmenityInspection::where('audit_id', '=', $audit_id)->where('auditor_id', '=', $auditor_id)->where('building_id', '=', $building->building_id)->update([
					'auditor_id' => $new_auditor_id,
				]);

				// add to units
				$unit_auditor_ids = [];
				foreach ($building->building->units as $unit) {
					$amenities_unit = AmenityInspection::where('audit_id', '=', $audit_id)->where('auditor_id', '=', $auditor_id)->where('unit_id', '=', $unit->id)->update([
						'auditor_id' => $new_auditor_id,
					]);

					$unit_auditor_ids = array_merge($unit_auditor_ids, AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit->id)->whereNotNull('auditor_id')->whereNotNull('unit_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
				}

				$user = User::where('id', '=', $new_auditor_id)->first();

				$building_auditor_ids = [];
				$units = Unit::where('building_id', '=', $building_id)->get();
				foreach ($units as $unit) {
					$building_auditor_ids = array_merge($building_auditor_ids, \App\Models\AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit->id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
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

				$initials = $user->initials();
				$color = 'auditor-badge-' . $user->badge_color;

				return ['initials' => $initials, 'color' => $color, 'name' => $user->full_name(), 'unit_auditors' => $unit_auditors, 'building_auditors' => $building_auditors, 'unit_id' => 0, 'building_id' => $building->building_id];
			}
		} elseif (0 != $amenity_id && 0 == $building_id) {
			if (AuditAuditor::where('audit_id', '=', $audit_id)->where('user_id', '=', $new_auditor_id)->first()) {
				$amenity = AmenityInspection::where('id', '=', $amenity_id)
					->where('audit_id', '=', $audit_id)
					->whereNull('building_id')
					->whereNull('unit_id')
					->first();

				$building = CachedBuilding::where('id', '=', $amenity->cachedbuilding_id)->first();

				$amenities = AmenityInspection::where('audit_id', '=', $audit_id)->where('auditor_id', '=', $auditor_id)->where('building_id', '=', $building->building_id)->update([
					'auditor_id' => $new_auditor_id,
				]);

				$unit_auditor_ids = [];
				$building_auditor_ids = [];
				$user = User::where('id', '=', $new_auditor_id)->first();

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

				$initials = $user->initials();
				$color = 'auditor-badge-' . $user->badge_color;

				return ['initials' => $initials, 'color' => $color, 'name' => $user->full_name(), 'unit_auditors' => $unit_auditors, 'building_auditors' => $building_auditors, 'unit_id' => 0, 'building_id' => $building->building_id];
			}
		} elseif (0 == $amenity_id && 0 == $building_id && 0 == $unit_id) {
			if (AuditAuditor::where('audit_id', '=', $audit_id)->where('user_id', '=', $new_auditor_id)->first()) {
				$amenities = AmenityInspection::where('audit_id', '=', $audit_id)->where('auditor_id', '=', $auditor_id)->whereNull('building_id')->whereNull('unit_id')->update([
					'auditor_id' => $new_auditor_id,
				]);
				$user = User::where('id', '=', $new_auditor_id)->first();
				$initials = $user->initials();
				$color = 'auditor-badge-' . $user->badge_color;

				return ['initials' => $initials, 'color' => $color, 'name' => $user->full_name(), 'unit_auditors' => [], 'building_auditors' => [], 'unit_id' => 0, 'building_id' => 0];
			}
		}

		return 0;
	}

	public function saveAssignAuditorToAmenity(Request $request, $amenity_id, $audit_id, $building_id, $unit_id)
	{
		//dd($amenity_id, $audit_id, $building_id, $unit_id);
		// "395" "6659" "23058" "208307"
		//return $request->all();

		// is it mass assignment
		$auditor_id = $request->get('auditor_id');
		$lead_auditor_selected = Audit::find($audit_id);
		if ($lead_auditor_selected->lead_user_id == $auditor_id) {
			$lead_auditor_selected = true;
		} else {
			$lead_auditor_selected = false;
		}
		if (0 == $amenity_id && 0 != $unit_id) {
			$auditor_id = $request->get('auditor_id');

			if ($auditor_id) {
				// make sure this id is already in the auditor's list for this audit
				if (AuditAuditor::where('audit_id', '=', $audit_id)->where('user_id', '=', $auditor_id)->first() || $lead_auditor_selected) {
					$unit = CachedUnit::where('unit_id', '=', $unit_id)->first();

					$amenities = AmenityInspection::where('audit_id', '=', $audit_id)->whereNotNull('unit_id')->where('unit_id', '=', $unit->unit_id)->update([
						'auditor_id' => $auditor_id,
					]);

					$user = User::where('id', '=', $auditor_id)->first();

					$initials = $user->initials();
					$color = 'auditor-badge-' . $user->badge_color;

					return ['initials' => $initials, 'color' => $color, 'id' => $user->id, 'name' => $user->full_name()];
				}
			}
		} elseif (0 == $amenity_id && 0 != $building_id) {
			$auditor_id = $request->get('auditor_id');

			if ($auditor_id) {
				// make sure this id is already in the auditor's list for this audit
				if (AuditAuditor::where('audit_id', '=', $audit_id)->where('user_id', '=', $auditor_id)->first() || $lead_auditor_selected) {
					$building = CachedBuilding::where('building_id', '=', $building_id)->first();

					$amenities = AmenityInspection::where('audit_id', '=', $audit_id)->where('building_id', '=', $building->building_id)->update([
						'auditor_id' => $auditor_id,
					]);

					// add to units
					foreach ($building->building->units as $unit) {
						$amenities_unit = AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit->id)->update([
							'auditor_id' => $auditor_id,
						]);
					}

					$unit_auditor_ids = [];
					$building_auditor_ids = [];
					$units = Unit::where('building_id', '=', $building_id)->get();
					foreach ($units as $unit) {
						$unit_auditor_ids = array_merge($unit_auditor_ids, AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit->id)->whereNotNull('auditor_id')->whereNotNull('unit_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());

						$building_auditor_ids = array_merge($building_auditor_ids, \App\Models\AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit->id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
					}
					$building_auditor_ids = array_merge($building_auditor_ids, AmenityInspection::where('audit_id', '=', $audit_id)->where('building_id', '=', $building_id)->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());

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

					$user = User::where('id', '=', $auditor_id)->first();

					$initials = $user->initials();
					$color = 'auditor-badge-' . $user->badge_color;

					return ['initials' => $initials, 'color' => $color, 'id' => $user->id, 'name' => $user->full_name(), 'unit_auditors' => $unit_auditors, 'building_auditors' => $building_auditors, 'unit_id' => 0, 'building_id' => $building->building_id];
				}
			}
		} elseif (0 == $amenity_id && 0 == $building_id && 0 == $unit_id) {
			$auditor_id = $request->get('auditor_id');

			if ($auditor_id) {
				// make sure this id is already in the auditor's list for this audit
				if (AuditAuditor::where('audit_id', '=', $audit_id)->where('user_id', '=', $auditor_id)->first() || $lead_auditor_selected) {
					// $amenities = AmenityInspection::where('audit_id', '=', $audit_id)->whereNull('unit_id')->whereNull('building_id')->get();
					// $building = CachedBuilding::where('building_id', '=', $building_id)->first();
					$amenities = AmenityInspection::where('audit_id', '=', $audit_id)->whereNull('unit_id')->whereNull('building_id')->update([
						'auditor_id' => $auditor_id,
					]);

					$user = User::where('id', '=', $auditor_id)->first();

					$initials = $user->initials();
					$color = 'auditor-badge-' . $user->badge_color;

					return ['initials' => $initials, 'color' => $color, 'id' => $user->id, 'name' => $user->full_name(), 'unit_id' => 0, 'building_id' => 0];
				}
			}
		} else {
			$auditor_id = $request->get('auditor_id');

			if ($auditor_id) {
				//dd(AuditAuditor::where('audit_id','=',$audit_id)->where('user_id','=',$auditor_id)->first(), $audit_id, $auditor_id);
				// return AuditAuditor::where('audit_id', '=', $audit_id)->where('user_id', '=', $auditor_id)->first();
				// make sure this id is already in the auditor's list for this audit
				if (AuditAuditor::where('audit_id', '=', $audit_id)->where('user_id', '=', $auditor_id)->first() || $lead_auditor_selected) {
					// if $building_id = 0 we are working with an amenity at the building level like parking lot
					if (0 == $building_id && 0 == $unit_id) {
						//$amenity = AmenityInspection::where('audit_id', '=', $audit_id)->where('amenity_id', '=', $amenity_id)->whereNull('building_id')->first(); // TBD this may not work. might be a flaw in the selection...
						$amenity = AmenityInspection::where('audit_id', '=', $audit_id)->where('id', '=', $amenity_id)->whereNull('building_id')->first();
						$building = CachedBuilding::where('id', '=', $amenity->cachedbuilding_id)->first();
					} else {
						$building = CachedBuilding::where('building_id', '=', $building_id)->first();
					}

					//dd($building_id, $amenity_id, $unit_id, $building);

					if ('null' != $unit_id && 0 != $unit_id) {
						// $amenity = AmenityInspection::where('audit_id', '=', $audit_id)->where('amenity_id', '=', $amenity_id)->where('unit_id', '=', $unit_id)->first();
						$amenity = AmenityInspection::where('audit_id', '=', $audit_id)->where('id', '=', $amenity_id)->where('unit_id', '=', $unit_id)->first();

						$unit = CachedUnit::where('unit_id', '=', $unit_id)->first();
						$cached_unit_id = $unit_id;
						// reset unit auditors list
						//$unit_auditors = OrderingUnit::where('audit_id', '=', $audit_id)->where('user_id', '=', Auth::user()->id)->where('unit_id', '=', $unit->id)->first()->auditors();

						// reset building auditors list
						// $building = CachedBuilding::where('building_id','=',$building_id)->first();
						// $building_auditors = OrderingBuilding::where('audit_id', '=', $audit_id)->where('user_id', '=', Auth::user()->id)->where('building_id', '=', $building->id)->first();
						//->auditors();
					} else {
						if (0 == $building_id && 0 == $unit_id) {
							// $amenity = AmenityInspection::where('audit_id', '=', $audit_id)->where('amenity_id', '=', $amenity_id)->whereNull('building_id')->whereNull('unit_id')->first();
							$amenity = AmenityInspection::where('audit_id', '=', $audit_id)->where('id', '=', $amenity_id)->whereNull('building_id')->whereNull('unit_id')->first();
							$cached_unit_id = 0;
						} else {
							// $amenity = AmenityInspection::where('audit_id', '=', $audit_id)->where('amenity_id', '=', $amenity_id)->where('building_id', '=', $building_id)->whereNull('unit_id')->first();
							$amenity = AmenityInspection::where('audit_id', '=', $audit_id)->where('id', '=', $amenity_id)->where('building_id', '=', $building_id)->whereNull('unit_id')->first();
							$cached_unit_id = 0;
						}
					}
					$amenity->auditor_id = $auditor_id;
					$amenity->save();

					if ('null' != $unit_id && 0 != $unit_id) {
						$unit_auditor_ids = AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit_id)->whereNotNull('auditor_id')->whereNotNull('unit_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();

						$building_auditor_ids = [];
						$units = Unit::where('building_id', '=', $building_id)->get();
						foreach ($units as $unit) {
							$building_auditor_ids = array_merge($building_auditor_ids, \App\Models\AmenityInspection::where('audit_id', '=', $audit_id)->where('unit_id', '=', $unit->id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
						}
						// $building_auditor_ids = AmenityInspection::where('audit_id', '=', $audit_id)->where('building_id','=',$building_id)->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();
					} else {
						if (0 == $building_id && 0 == $unit_id) {
							$unit_auditor_ids = [];
							$building_auditor_ids = [];
						} else {
							$unit_auditor_ids = [];
							// reset building auditors list

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

					$user = User::where('id', '=', $auditor_id)->first();
					$initials = $amenity->user->initials();
					$color = 'auditor-badge-' . $amenity->user->badge_color;

					return ['initials' => $initials, 'color' => $color, 'id' => $user->id, 'name' => $user->full_name(), 'unit_auditors' => $unit_auditors, 'building_auditors' => $building_auditors, 'unit_id' => $cached_unit_id, 'building_id' => $building_id];
				}
			}
		}

		return 0;
	}

	public function getProject($id = null, $audit_id = 0)
	{
		$project = Project::where('project_key', '=', $id)->first();
		if ($project) {
			$projectId = $project->id;

			// the project tab has a audit selection to display previous audit's stats, compliance info and assignments.

			$projectTabs = collect([
				['title' => 'Audits', 'icon' => 'a-mobile-home', 'status' => '', 'badge' => '', 'action' => 'project.details-with-audit'],
				['title' => 'Communications', 'icon' => 'a-envelope-incoming', 'status' => '', 'badge' => '', 'action' => 'project.audit-communications'],
				['title' => 'Documents', 'icon' => 'a-file-clock', 'status' => '', 'badge' => '', 'action' => 'project.documents'],
				['title' => 'Notes', 'icon' => 'a-file-text', 'status' => '', 'badge' => '', 'action' => 'project.notes'],
				// ['title' => 'Comments', 'icon' => 'a-comment-text', 'status' => '', 'badge' => '', 'action' => 'project.comments'],
				// ['title' => 'Photos', 'icon' => 'a-picture', 'status' => '', 'badge' => '', 'action' => 'project.photos'],
				// ['title' => 'Findings', 'icon' => 'a-mobile-info', 'status' => '', 'badge' => '', 'action' => 'project.findings'],
				// ['title' => 'Follow-ups', 'icon' => 'a-bell-ring', 'status' => '', 'badge' => '', 'action' => 'project.followups'],
				['title' => 'Findings', 'icon' => 'a-mobile-info', 'status' => '', 'badge' => '', 'action' => 'project.stream'],
				['title' => 'Reports', 'icon' => 'a-file-chart-3', 'status' => '', 'badge' => '', 'action' => 'project.reports'],
				['title' => 'Contacts', 'icon' => 'a-person-notebook icon', 'status' => '', 'badge' => '', 'action' => 'project.contacts'],
			]);
			$tab = 'project-detail-tab-1';

			return view('projects.project', compact('tab', 'projectTabs', 'projectId', 'audit_id', 'project'));
		} else {
			$error = "I was not able to find the project from the link you clicked. Please let the Allita Support Team know what link you clicked on to arrive at this error.";
			abort(403, $error);
		}
	}

	public function getProjectTitle($id = null)
	{
		$project_number = Project::where('project_key', '=', $id)->first()->project_number;

		$audit = CachedAudit::where('project_key', '=', $id)->orderBy('id', 'desc')->first();

		// TBD add step to title
		$step = $audit->step_status_text; //  :: CREATED DYNAMICALLY FROM CONTROLLER
		$step_icon = $audit->step_status_icon;

		return '<i class="a-mobile-repeat"></i><i class="' . $step_icon . '"></i> <span class="list-tab-text"> PROJECT ' . $project_number . '</span>';
	}

	public function getProjectDetails($id = null, $audit_id = 0)
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
			$auditors = User::whereHas('roles', function ($query) {
				$query->where('role_id', '=', 2);
				$query->orWhere('role_id', '=', 3);
			})
				->where('active', '=', 1)
				->orderBy('name', 'asc')
				->get();
			return view('projects.partials.details', compact('details', 'audits', 'project', 'selected_audit', 'auditors'));
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

	public function getProjectDetailsAjax(Request $request)
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
  
	public function getBuildingDetailsInfo(Request $request, $id, $type, $audit)
	{
		$type_id = $request->post('type_id');
		$name = $request->post('name');
		$is_uncorrected = $request->post('is_uncorrected');
		
		if($type == 'all'){
			Session::forget('type_id');
			Session::forget('name');
			Session::forget('is_uncorrected');
			return 1;
		}

	 	if (!empty($type_id)){
	 		Session::put('type_id', $type_id);
	 		Session::put('name', $name);
		}else{
			Session::forget('type_id');
			Session::forget('name');
		}
		if ($is_uncorrected == 'true') {
			Session::put('is_uncorrected', $is_uncorrected);
		}else{
			Session::forget('is_uncorrected');
		}

		
		// types:building, unit
		// project: project_id?
		// type_id: building or unit id
		$project = Project::where('id', '=', $id)->first();
		$audit = CachedAudit::with('auditors', 'audit', 'lead_auditor')->where('audit_id', $audit)->first();
		$current_user = Auth::user();
		$manager_access = $current_user->manager_access();
		$details = $project->details();

		$dpView = 1;
		$findings = $audit->audit->findings->where('cancelled_at',NULL);
		$print = null;
		$report = $audit;
		$detailsPage = 1;
		switch ($type) {
			
			case 'building':

				$allBuildingInspections = $audit->audit->building_inspections;
				
				$selected_audit = $audit;
				if(session()->has('type_id') && session()->has('is_uncorrected')){
					$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
					$result = array_intersect($bulidingUnresolved, $type_id);
				
					$inspections = $audit->audit->building_inspections()->whereIn('building_id',$result)->paginate(12);
				}
				else if(session()->has('is_uncorrected')){
					$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
				
					$inspections = $audit->audit->building_inspections()->whereIn('building_id',$bulidingUnresolved)->paginate(12);
				}
				else if(session()->has('type_id')){
					$inspections = $audit->audit->building_inspections()->whereIn('building_id',$type_id)->paginate(12);
				}
				else{
					$inspections = $audit->audit->building_inspections()->paginate(12);
				}
				
				
				return view('crr_parts.crr_inspections_building', compact('inspections','allBuildingInspections','dpView','findings','print','report','selected_audit','detailsPage'));
				break;
			case 'unit':
				$allUnitInspections = $audit->audit->unit_inspections;
				if(session()->has('type_id') && session()->has('is_uncorrected')){
					$allBuildingInspections = $audit->audit->building_inspections;
					$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
					$result = array_intersect($bulidingUnresolved, $type_id);
					$unitUnresolvedId = $audit->audit->unitUnResolved($allUnitInspections, $findings);
					
					$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->whereIn('building_id',$result)->whereIn('unit_id',$unitUnresolvedId)->with('documents')->paginate(12);
				}
				else if(session()->has('is_uncorrected')){
					$allUnitInspections1 = $audit->audit->unit_inspections()->groupBy('unit_id')->get();
					$unitUnresolvedId = $audit->audit->unitUnResolved($allUnitInspections1, $findings);
					
					// $inspections = $audit->audit->unit_inspections()->whereIn('unit_id',$unitUnresolvedId)->paginate(12);
					$inspections = $audit->audit->unit_inspections()->whereIn('unit_id',$unitUnresolvedId)->with('documents')->paginate(12);
				}
				else if(session()->has('type_id')){
					$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->with('documents')->whereIn('building_id',$type_id)->paginate(12);
					// $inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->whereIn('building_id',$type_id)->paginate(12);
				}else{
					$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->with('documents')->paginate(12);
					// $inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->paginate(12);
				}
				
				return view('crr_parts.crr_inspections_unit', compact('inspections','allUnitInspections','dpView','print','report','findings','detailsPage','audit'));
				break;
			default:
		}
	}

	public function getProjectDetailsInfo($id, $type, $audit, $return_raw = 0)
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
					$findings = $audit->audit->findings->where('cancelled_at',NULL);
					$print = null;
					$report = $audit;
					$detailsPage = 1;
					switch ($return_raw) {
						case 'site':
							$inspections = $audit->audit->project_amenity_inspections()->paginate(5);
							return view('crr_parts.crr_inspections_site', compact('inspections','dpView','findings','print','report','detailsPage'));
							break;
						case 'building':
							$allBuildingInspections = $audit->audit->building_inspections;
							$selected_audit = $audit;
							if(session()->has('type_id') && session()->has('is_uncorrected')){
								$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
								$result = array_intersect($bulidingUnresolved, $type_id);
							
								$inspections = $audit->audit->building_inspections()->whereIn('building_id',$result)->paginate(12);
							}
							else if(session()->has('is_uncorrected')){
								$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
							
								$inspections = $audit->audit->building_inspections()->whereIn('building_id',$bulidingUnresolved)->paginate(12);
							}
							else if(session()->has('type_id')){
								$inspections = $audit->audit->building_inspections()->whereIn('building_id',session()->has('type_id'))->paginate(12);
							}
							else{
								$inspections = $audit->audit->building_inspections()->paginate(12);
							}
							
							// if(session()->has('type_id') && session()->has('is_uncorrected')){
							// 	$inspections = $audit->audit->building_inspections()->whereIn('building_id',$type_id)->paginate(10);
							// }else 
							// if(session()->has('type_id')){
							// 	$inspections = $audit->audit->building_inspections()->whereIn('building_id',$type_id)->paginate(12);
							// }
							// else if(!session()->has('type_id') && session()->has('is_uncorrected')){
							// 	$inspections = $audit->audit->building_inspections()->paginate(12);
							// }
							// else{
							// 	$inspections = $audit->audit->building_inspections()->paginate(12);
							// }

							// $inspections = $audit->audit->building_inspections()->paginate(10);
							
							return view('crr_parts.crr_inspections_building', compact('inspections','allBuildingInspections','dpView','findings','print','report','selected_audit','detailsPage'));
							break;
						case 'unit':
						$allUnitInspections = $audit->audit->unit_inspections;
							if(session()->has('type_id') && session()->has('is_uncorrected')){
								$allBuildingInspections = $audit->audit->building_inspections;
								$bulidingUnresolved = $audit->audit->buildingUnResolved($allBuildingInspections, $findings);
								$result = array_intersect($bulidingUnresolved, $type_id);
								$unitUnresolvedId = $audit->audit->unitUnResolved($allUnitInspections, $findings);
								
								$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->whereIn('building_id',$result)->whereIn('unit_id',$unitUnresolvedId)->with('documents')->paginate(12);
							}
							else if(session()->has('is_uncorrected')){
								$allUnitInspections1 = $audit->audit->unit_inspections()->groupBy('unit_id')->get();
								$unitUnresolvedId = $audit->audit->unitUnResolved($allUnitInspections1, $findings);
								
								// $inspections = $audit->audit->unit_inspections()->whereIn('unit_id',$unitUnresolvedId)->paginate(12);
								$inspections = $audit->audit->unit_inspections()->whereIn('unit_id',$unitUnresolvedId)->with('documents')->paginate(12);
							}
							else if(session()->has('type_id')){
								$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->with('documents')->whereIn('building_id',$type_id)->paginate(12);
								// $inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->whereIn('building_id',session()->has('type_id'))->paginate(12);
							}else{
								$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->with('documents')->paginate(12);
								// $inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->paginate(12);
							}
							
							// if(session()->has('type_id')){
							// 	$inspections = $audit->audit->unit_inspections()->whereIn('building_id',session()->get('type_id'))->groupBy('unit_id')->paginate(12);
							// 	// $inspections = $audit->audit->unit_inspections()->whereIn('building_id',session()->get('type_id'))->groupBy('unit_id')->with('documents')->paginate(12);
							// }else{
							// 	$inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->paginate(12);
							// 	// $inspections = $audit->audit->unit_inspections()->groupBy('unit_id')->with('documents')->paginate(12);
							// }
							return view('crr_parts.crr_inspections_unit', compact('inspections','allUnitInspections','dpView','print','report','findings','detailsPage','audit'));
							break;
						default:
					}
				}
				
				Session::forget('name');
				Session::forget('type_id');
				Session::forget('is_uncorrected');

				return view('projects.partials.details-selections', compact('audit', 'details'));
				break;
			default:
		}
		// return $data['programs'];
		return view('projects.partials.details-' . $type, compact('data', 'project'));
	}

	public function getAuditorDailyCalendar($date, $day_id, $audit_id, $auditor_id)
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

	public function deleteSchedule(Request $request, $event_id)
	{
		// TBD check users
		$current_user = Auth::user();

		$event = ScheduleTime::where('id', '=', $event_id)->first();

		// user needs to be the lead
		// TBD add manager/roles
		if ($event) {
			if ($event->cached_audit->lead == $current_user->id || $current_user->manager_access()) {
				$event->delete();

				return 1;
			} else {
				return 'Only the lead or a manager can delete schedules.';
			}
		} else {
			return 'Event not found.';
		}
	}

	public function scheduleAuditor(Request $request, $audit_id, $day_id, $auditor_id)
	{
		// TBD user check

		$start = $request->get('start');
		$duration = $request->get('duration');
		$travel = $request->get('travel');

		$hours = sprintf('%02d', floor(($start - 1) * 15 / 60) + 6);
		$minutes = sprintf('%02d', ($start - 1) * 15 % 60);
		$start_time = formatTime($hours . ':' . $minutes . ':00', 'H:i:s');

		$hours = sprintf('%02d', floor(($start + $duration - 1) * 15 / 60) + 6);
		$minutes = sprintf('%02d', ($start + $duration - 1) * 15 % 60);
		$end_time = formatTime($hours . ':' . $minutes . ':00', 'H:i:s');

		$new_schedule = new ScheduleTime([
			'audit_id' => $audit_id,
			'day_id' => $day_id,
			'auditor_id' => $auditor_id,
			'start_time' => $start_time,
			'end_time' => $end_time,
			'start_slot' => $start,
			'span' => $duration,
			'travel_span' => $travel,
		]);
		$new_schedule->save();

		return 1;
	}

	public function addADay(Request $request, $id)
	{
		$audit = Audit::where('id', '=', $id)->first();

		if (Auth::user()->id == $audit->lead_user_id || Auth::user()->manager_access()) {
			$date = formatDate($request->get('date'), 'Y-m-d H:i:s', 'F d, Y');
			$check = ScheduleDay::where('audit_id', $id)->where('date', $date)->count();
			if ($check < 1) {
				// Day has not been entered yet :)
				$day = new ScheduleDay([
					'audit_id' => $id,
					'date' => $date,
				]);
				$day->save();

				// see if day is earlier than previous days
				$check = ScheduleDay::where('audit_id', $id)->where('date', '<', $date)->count();
				if (!$check) {
					// this is the new starting date - set it to be so on the audit and cached audit table
					$audit->completed_date = $date;
					$audit->save();
					$audit->cached_audit->inspection_schedule_date = $date;
					$audit->cached_audit->save();
				}

				return 1;
			} else {
				return 'This day was already scheduled!';
			}
		} else {
			return 'Sorry, only the lead or a manager can schedule days for an audit.';
		}
	}

	public function deleteDay(Request $request, $id, $day_id)
	{
		// TBD only authorized users can add days (lead/managers)

		// 1. delete schedules
		// 2. delete day
		// 3. update estimated needed time and checks by rebuilding CachedAudit
		$audit = Audit::where('id', '=', $id)->first();
		if (Auth::user()->id == $audit->lead_user_id || Auth::user()->manager_access()) {
			$schedules = ScheduleTime::where('day_id', '=', $day_id)->where('audit_id', '=', $id)->delete();
			$day = ScheduleDay::where('id', '=', $day_id)->where('audit_id', '=', $id)->delete();

			// Event::fire('audit.cache', $audit->audit);

			$check = ScheduleDay::where('audit_id', $id)->orderBy('date', 'asc')->first();
			if ($check) {
				// this is the new starting date - set it to be so on the audit and cached audit table
				$audit->completed_date = $check->date;
				$audit->save();
				$audit->cached_audit->inspection_schedule_date = $check->date;
				$audit->cached_audit->save();
			}

			$output = ['data' => 1];

			return $output;
		} else {
			return 'Sorry, only the lead or a manager can remove days from an audit.';
		}
	}

	public function saveEstimatedHours(Request $request, $id)
	{
		// audit id
		$forminputs = $request->get('inputs');
		parse_str($forminputs, $forminputs);

		$hours = (int) $forminputs['estimated_hours'];
		$minutes = (int) $forminputs['estimated_minutes'];

		$audit = CachedAudit::where('audit_id', '=', $id)->with('audit');
		if (!Auth::user()->can('access_manager')) {
			$audit = $audit->where('lead', '=', Auth::user()->id);
		}
		$audit = $audit->first();

		$new_estimate = $hours . ':' . $minutes . ':00';
		if (null !== $audit && null !== $hours && null !== $minutes) {
			if (Auth::user()->id == $audit->audit->lead_user_id || Auth::user()->manager_access()) {
				if ($audit) {
					$audit->estimated_time = $new_estimate;
					$audit->audit->estimated_time = $new_estimate;
					$audit->save();
					$audit->audit->save();

					$needed = $audit->hours_still_needed();

					return ['status' => 1, 'hours' => $hours . ':' . $minutes, 'needed' => $needed];
				} else {
					return ['status' => 0, 'message' => 'Sorry, this audit reference cannot be found or no lead has been set yet.'];
				}
			} else {
				return 'Sorry, only the lead or a manager can input estimated hours for an audit.';
			}
		} else {
			return 'Sorry, there is a problem with the data that was submitted. Please let the support team know to check the AC:2646 Save Estimated Hours feature and which audit (' . $id . ') you are working on.';
		}
	}

	public function getProjectDetailsAssignmentSchedule($project, $dateid)
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

	// public function getProjectCommunications ( $project = null, $page=0 ) {

	//     $data = [];
	//     return view('projects.partials.communications', compact('data'));
	// }

	// public function getProjectNotes($project_id = null)
	// {
	//     $project = Project::where('id','=',$project_id)->first();
	//     dd($project);

	//     return view('projects.partials.notes', compact($project));
	// }

	// public function getProjectComments ( $project = null ) {
	//     return view('projects.partials.comments');
	// }

	// public function getProjectPhotos ( $project = null ) {
	//     return view('projects.partials.photos');
	// }

	// public function getProjectFindings ( $project = null ) {
	//     return view('projects.partials.findings');
	// }

	// public function getProjectFollowups ( $project = null ) {
	//     return view('projects.partials.followups');
	// }

	public function getProjectStream($project = null)
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

	public function getProjectReports(Request $request, $project_id = null)
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

	public function modalProjectProgramSummaryFilterProgram($project_id, $program_id, Request $request, $audit)
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

	private function projectSummaryComposite($project_id, $audit_id = 0)
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

	public function modalProjectProgramSummary($project_id, $program_id = 0, $audit_id = 0)
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

	public function addAssignmentAuditor($audit_id, $day_id, $auditorid = null)
	{
		$audit = CachedAudit::where('audit_id', '=', $audit_id)->first();

		// make sure the logged in user is a manager or the lead on the audit TBD
		$current_user = Auth::user();
		// is user manager? TBD
		// if($audit->lead != $current_user->id){
		//     dd("You must be the lead.");
		// }

		$day = ScheduleDay::where('id', '=', $day_id)->where('audit_id', '=', $audit_id)->first();

		$auditor = User::where('id', '=', $auditorid)->where('active', '=', 1)->first();
		// dd($audit_id, $audit, $day, $auditorid, $auditor);

		// get auditors from user roles
		$auditors = User::whereHas('roles', function ($query) {
			$query->where('role_id', '=', 2);
			$query->orWhere('role_id', '=', 3);
		})
			->where('active', '=', 1)
			->orderBy('name', 'asc')
			->get();

		return view('modals.project-assignment-add-auditor', compact('day', 'auditor', 'audit', 'auditors'));
	}

	public function addAuditorToAudit(Request $request, $userid, $auditid)
	{
		// TBD user should be a manager or a lead or an auditor?

		// dd($userid, $auditid, $request->get('dayid'));
		// 6301 6410 4
		$day = ScheduleDay::where('audit_id', '=', $auditid)->where('id', '=', $request->get('dayid'))->first();

		$audit = Audit::where('id', '=', $auditid)->first();

		$user = User::where('id', '=', $userid)->first();

		if ($day && $audit && $user && count(AuditAuditor::where('audit_id', '=', $auditid)->where('user_id', '=', $userid)->get()) == 0) {
			$new_auditor = new AuditAuditor([
				'audit_id' => $auditid,
				'monitoring_key' => $audit->monitoring_key,
				'user_id' => $userid,
				'user_key' => $user->devco_key,
			]);
			$new_auditor->save();

			return 1;
		}

		return 0;
	}

	public function removeAuditorFromAudit(Request $request, $userid, $auditid)
	{
		$audit = Audit::where('id', '=', $auditid)->first();

		$user = User::where('id', '=', $userid)->first();

		if ($audit && $user) {
			AuditAuditor::where('user_id', '=', $user->id)->where('audit_id', '=', $auditid)->first()->delete();

			// remove their assignements
			AmenityInspection::where('auditor_id', $user->id)->where('audit_id', '=', $auditid)->update(['auditor_id' => null]);

			return 1;
		}

		return 0;
	}

	public function addAssignmentAuditorStats($id, $auditorid)
	{
		// id is project id

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
				'date' => 'DECEMBER 22, 2018',
				'ref' => '20181222',
				'date-previous' => 'DECEMBER 13, 2018',
				'ref-previous' => '20181213',
				'date-next' => 'DECEMBER 31, 2018',
				'ref-next' => '20181231',
				'preferred_longest_drive' => '02:30',
				'preferred_lunch' => '00:30',
				'total_estimated_commitment' => '07:40',
			],
			'itinerary-start' => [
				'id' => 1,
				'icon' => 'a-home-marker',
				'type' => 'start',
				'status' => '',
				'name' => 'Default address',
				'address' => 'address here',
				'unit' => 'unit 3',
				'city' => 'city',
				'state' => 'OH',
				'zip' => '12345',
				'average' => '00:00',
				'end' => '08:30 AM',
				'lead' => 1, // user id
				'order' => 1,
				'itinerary' => [],
			],
			'itinerary-end' => [
				'id' => 9,
				'icon' => 'a-home-marker',
				'type' => 'end',
				'status' => '',
				'name' => 'The Ending Address',
				'address' => 'address here',
				'unit' => 'unit 3',
				'city' => 'city',
				'state' => 'OH',
				'zip' => '12345',
				'average' => '01:00',
				'end' => '4:10 PM',
				'lead' => 1,
				'order' => 5,
				'itinerary' => [],
			],
			'itinerary' => [
				[
					'id' => 2,
					'icon' => 'a-marker-basic',
					'type' => 'site',
					'status' => 'in-progress',
					'name' => 'The Garden Oaks',
					'average' => '00:00',
					'end' => '08:30 AM',
					'lead' => 1,
					'order' => 2,
					'itinerary' => [
						[
							'id' => 3,
							'icon' => 'a-mobile-home',
							'type' => 'site',
							'status' => 'in-progress',
							'name' => 'The Garden Oaks',
							'average' => '02:00',
							'end' => '11:30 AM',
							'lead' => 1,
							'order' => 1,
						],
						[
							'id' => 4,
							'icon' => 'a-suitcase-2',
							'type' => 'break',
							'status' => '',
							'name' => 'LUNCH',
							'average' => '00:30',
							'end' => '12:00 AM',
							'lead' => 1,
							'order' => 2,
						],
					],
				],
				[
					'id' => 5,
					'icon' => 'a-marker-basic',
					'type' => 'site',
					'status' => '',
					'name' => 'The Other Place',
					'average' => '00:15',
					'end' => '12:15 PM',
					'lead' => 1,
					'order' => 3,
					'itinerary' => [
						[
							'id' => 6,
							'icon' => 'a-folder',
							'type' => 'file',
							'status' => 'in-progress',
							'name' => 'The Other Place',
							'average' => '01:40',
							'end' => '1:55 PM',
							'lead' => 1,
							'order' => 1,
						],
					],
				],
				[
					'id' => 7,
					'icon' => 'a-marker-basic',
					'type' => 'site',
					'status' => '',
					'name' => 'The Womping Willow',
					'average' => '00:15',
					'end' => '2:10 PM',
					'lead' => 2,
					'order' => 4,
					'itinerary' => [
						[
							'id' => 8,
							'icon' => 'a-folder',
							'type' => 'file',
							'status' => 'in-progress',
							'name' => 'The Womping Willow',
							'average' => '01:00',
							'end' => '3:10 PM',
							'lead' => 2,
							'order' => 1,
						],
					],
				],
			],
			'calendar' => [
				'header' => ['12/18', '12/19', '12/20', '12/21', '12/22', '12/23', '12/24', '12/25', '12/26'],
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
					'previous' => 'DECEMBER 13, 2018',
					'ref-previous' => '20181213',
					'today' => 'DECEMBER 22, 2018',
					'next' => 'DECEMBER 31, 2018',
					'ref-next' => '20181231',
				],
			],
			'calendar-previous' => [
				'header' => ['12/09', '12/10', '12/11', '12/12', '12/13', '12/14', '12/15', '12/16', '12/17'],
				'content' => [
					[
						'id' => 111,
						'date' => '12/09',
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
						'date' => '12/10',
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
						'date' => '12/11',
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
						'date' => '12/12',
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
						'date' => '12/13',
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
						'id' => 116,
						'date' => '12/14',
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
						'id' => 116,
						'date' => '12/15',
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
						'id' => 116,
						'date' => '12/16',
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
						'id' => 116,
						'date' => '12/17',
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
				],
				'footer' => [
					'previous' => 'DECEMBER 04, 2018',
					'ref-previous' => '20181204',
					'today' => 'DECEMBER 13, 2018',
					'next' => 'DECEMBER 22, 2018',
					'ref-next' => '20181222',
				],
			],
			'calendar-next' => [
				'header' => ['12/27', '12/28', '12/29', '12/30', '12/31', '01/01', '01/02', '01/03', '01/04'],
				'content' => [
					[
						'id' => 111,
						'date' => '12/09',
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
						'date' => '12/10',
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
						'date' => '12/11',
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
						'date' => '12/12',
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
						'date' => '12/13',
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
						'id' => 116,
						'date' => '12/14',
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
						'id' => 116,
						'date' => '12/15/18',
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
						'id' => 116,
						'date' => '12/16',
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
						'id' => 116,
						'date' => '12/17',
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
				],
				'footer' => [
					'previous' => 'DECEMBER 22, 2018',
					'ref-previous' => '20181222',
					'today' => 'DECEMBER 31, 2018',
					'next' => 'JANUARY 09, 2019',
					'ref-next' => '20190109',
				],
			],
		]);

		return view('projects.partials.details-assignment-auditor-stat', compact('data'));
	}

	public function getAssignmentAuditorCalendar($id, $auditorid, $currentdate, $beforeafter)
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

	public function addAmenity($type, $id, $finding_modal, Request $request)
	{
		switch ($type) {
			case 'project':
				$project_id = $id;
				$building_id = null;
				$unit_id = null;

				$audit = CachedAudit::where('audit_id', '=', $project_id)->first();
				$auditors_collection = $audit->auditors;
				$amenities_collection = Amenity::where('inspectable', '=', 1)
					->where('project', '=', 1)
					->orderBy('amenity_description', 'asc')->get();

				break;
			case 'building':
				$building_id = $id;
				$unit_id = null;
				$audit_id = $request->has('audit') ? $request->audit : 0;

				// get project_id from db
				if ($audit_id) {
					$building = CachedBuilding::where('building_id', '=', $building_id)->where('audit_id', $audit_id)->first();
				} else {
					$building = CachedBuilding::where('building_id', '=', $building_id)->first();
				}
				if ($building) {
					$project_id = $building->project_id;
				} else {
					$project_id = null;
				}

				$audit = CachedAudit::where('audit_id', '=', $building->audit_id)->first();
				$auditors_collection = $audit->auditors;
				$amenities_collection = Amenity::where('inspectable', '=', 1)
					->where(function ($query) {
						$query->where('building_exterior', '=', 1)
							->orWhere('building_system', '=', 1)
							->orWhere('common_area', '=', 1);
					})
					->orderBy('amenity_description', 'asc')->get();

				break;
			case 'unit':
				$unit_id = $id;
				$audit_id = $request->has('audit') ? $request->audit : 0;
				// get building_id and project_id from db
				if ($audit_id) {
					$unit = CachedUnit::where('unit_id', '=', $unit_id)->where('audit_id', $audit_id)->first();
				} else {
					$unit = CachedUnit::where('unit_id', '=', $unit_id)->first();
				}
				if ($unit) {
					$project_id = $unit->project_id;
					$building_id = $unit->building_id;
				} else {
					$project_id = null;
					$building_id = null;
				}

				$audit = CachedAudit::where('audit_id', '=', $unit->audit_id)->first();
				$auditors_collection = $audit->auditors;
				$amenities_collection = Amenity::where('unit', '=', 1)->orWhere('file', '=', 1)->where('inspectable', '=', 1)->orderBy('amenity_description', 'asc')->get();

				break;
			default:
				// something is wrong, there should be at least either a unit_id or a building_id or a project_id
				dd('Error 3788 - cannot add amenity');
		}

		$data = collect([
			'project_id' => $project_id,
			'building_id' => $building_id,
			'unit_id' => $unit_id,
			'audit_id' => $audit->audit_id,
		]);

		// get auditors for that audit
		//[['Brian', '1'], ['Bob', '2'], ['Bill', '3']]
		$auditors = '[';
		foreach ($auditors_collection as $auditor) {
			$auditors = $auditors . '["' . $auditor->user->full_name() . '","' . $auditor->user->id . '"],';
		}
		$auditors = $auditors . ']';

		$amenities = '[';
		foreach ($amenities_collection as $amenity) {
			$amenities = $amenities . '["' . $amenity->amenity_description . '","' . $amenity->id . '"],';
		}
		$amenities = $amenities . ']';
		if ($finding_modal) {
			return view('modals.findings-amenity-add', compact('data', 'auditors', 'amenities'));
		}

		return view('modals.amenity-add', compact('data', 'auditors', 'amenities'));
	}

	public function saveAmenity(Request $request)
	{
		$project_id = $request->get('project_id');
		$building_id = $request->get('building_id');
		$unit_id = $request->get('unit_id');
		$audit_id = $request->get('audit_id');
		$amenity_id = $request->get('amenity_id');
		$toplevel = $request->get('toplevel');
		modal_confirm($request);
		// $request->session()->forget('hide_confirm_modal');
		// Session::save();
		//dd(session()->all());
		$new_amenities = $request->get('new_amenities');

		//dd($project_id, $building_id, $unit_id, $amenity_id, $audit_id, $toplevel);

		// get current audit id using project_id
		// only one audit can be active at one time
		$audit = CachedAudit::where('audit_id', '=', $audit_id)->orderBy('id', 'desc')->first();

		if (!$audit) {
			dd('There is an error - cannot find that audit - 3854');
		}

		$user = Auth::user();

		if (null !== $new_amenities) {
			foreach ($new_amenities as $new_amenity) {
				if ($new_amenity['auditor_id']) {
					$auditor = AuditAuditor::where('user_id', '=', $new_amenity['auditor_id'])->where('audit_id', '=', $audit->audit_id)->with('user')->first();
					if (!$auditor) {
						dd("There is an error - this auditor doesn't seem to be assigned to this audit.");
					}

					$auditor_color = $auditor->user->badge_color;
					$auditor_initials = $auditor->user->initials();
					$auditor_name = $auditor->user->full_name();
					$auditorid = $auditor->user_id;
				} else {
					$auditor_color = '';
					$auditor_initials = '';
					$auditor_name = '';
					$auditorid = null;
				}

				// get amenity type
				$amenity_type = Amenity::where('id', '=', $new_amenity['amenity_id'])->first();

				// project level amenities are handled through OrderingBuilding and CachedBuilding
				if ($project_id && '' == $unit_id && '' == $building_id) {
					$name = $amenity_type->amenity_description;

					// create ProjectAmenity
					// create CachedBuilding
					// create AmenityInspection
					// create OrderingBuilding
					// load buildings

					$project_amenity = new ProjectAmenity([
						'project_key' => $audit->project_key,
						'project_id' => $audit->project_id,
						'amenity_type_key' => $amenity_type->amenity_type_key,
						'amenity_id' => $amenity_type->id,
						'comment' => 'manually added by ' . Auth::user()->id,
					]);
					$project_amenity->save();

					$cached_building = new CachedBuilding([
						'building_name' => $name,
						'building_id' => null,
						'building_key' => null,
						'audit_id' => $audit->audit_id,
						'audit_key' => $audit->audit_key,
						'project_id' => $audit->project_id,
						'project_key' => $audit->project_key,
						'lead_id' => $audit->lead_id,
						'lead_key' => $audit->lead_key,
						'status' => '',
						'type' => $amenity_type->icon,
						'type_total' => null,
						'type_text' => null,
						'type_text_plural' => null,
						'finding_total' => 0,
						'finding_file_status' => '',
						'finding_nlt_status' => '',
						'finding_lt_status' => '',
						'finding_file_total' => 0,
						'finding_file_completed' => 0,
						'finding_nlt_total' => 0,
						'finding_nlt_completed' => 0,
						'finding_lt_total' => 0,
						'finding_lt_completed' => 0,
						'address' => $audit->address,
						'city' => $audit->city,
						'state' => $audit->state,
						'zip' => $audit->zip,
						'amenity_id' => $amenity_type->id,
					]);
					$cached_building->save();

					$amenity = new AmenityInspection([
						'audit_id' => $audit->audit_id,
						'project_id' => $audit->project_id,
						'amenity_id' => $amenity_type->id,
						'auditor_id' => $auditorid,
						'cachedbuilding_id' => $cached_building->id,
					]);
					$amenity->save();

					$cached_building->amenity_inspection_id = $amenity->id;
					$cached_building->save();

					// latest ordering
					$latest_ordering = OrderingBuilding::where('user_id', '=', Auth::user()->id)
						->where('audit_id', '=', $audit->audit_id)
						->orderBy('order', 'desc')
						->first();

					if (is_object($latest_ordering)) {
						$latest_ordering = $latest_ordering->order;
					} else {
						$latest_ordering = 0;
					}
					// save the ordering
					$ordering = new OrderingBuilding([
						'user_id' => Auth::user()->id,
						'audit_id' => $audit->audit_id,
						'building_id' => null,
						'project_id' => $audit->project_id,
						'amenity_id' => $amenity_type->id,
						'amenity_inspection_id' => $amenity->id,
						'order' => $latest_ordering + 1,
					]);
					$ordering->save();

					$buildings = OrderingBuilding::where('audit_id', '=', $audit->audit_id)->where('user_id', '=', Auth::user()->id)->orderBy('order', 'asc')->with('building')->get();

					$data = $buildings;
				} else {
					$name = $amenity_type->amenity_description;
					// save new amenity
					if ($unit_id) {
						$unitamenity = new UnitAmenity([
							'unit_id' => $unit_id,
							'amenity_id' => $amenity_type->id,
							'comment' => 'manually added by ' . Auth::user()->id,
						]);
						$unitamenity->save();

						$amenity = new AmenityInspection([
							'audit_id' => $audit_id,
							'unit_id' => $unit_id,
							'amenity_id' => $amenity_type->id,
							'auditor_id' => $auditorid,
						]);
						$amenity->save();
						// dd($amenity,$audit_id,$unit_id,$amenity_type->id,$auditorid);

						// latest ordering
						$latest_ordering = OrderingAmenity::where('user_id', '=', Auth::user()->id)
							->where('audit_id', '=', $audit->audit_id)
							->where('unit_id', '=', $unit_id)
							->orderBy('order', 'desc')
							->first();
						if (is_object($latest_ordering)) {
							$latest_ordering = $latest_ordering->order;
						} else {
							$latest_ordering = 0;
						}
						// save the ordering
						$ordering = new OrderingAmenity([
							'user_id' => Auth::user()->id,
							'audit_id' => $audit->audit_id,
							'unit_id' => $unit_id,
							'amenity_id' => $amenity_type->id,
							'amenity_inspection_id' => $amenity->id,
							'order' => $latest_ordering + 1,
						]);
						$ordering->save();
					} elseif ($building_id) {
						$buildingamenity = new BuildingAmenity([
							'building_id' => $building_id,
							'amenity_id' => $amenity_type->id,
							'comment' => 'manually added by ' . Auth::user()->id,
						]);
						$buildingamenity->save();

						$amenity = new AmenityInspection([
							'audit_id' => $audit->audit_id,
							'building_id' => $building_id,
							'amenity_id' => $amenity_type->id,
							'auditor_id' => $auditorid,
						]);
						$amenity->save();

						// latest ordering
						$latest_ordering = OrderingAmenity::where('user_id', '=', Auth::user()->id)
							->where('audit_id', '=', $audit->audit_id)
							->where('building_id', '=', $building_id)
							->orderBy('order', 'desc')
							->first();
						if (is_object($latest_ordering)) {
							$latest_ordering = $latest_ordering->order;
						} else {
							$latest_ordering = 0;
						}
						// save the ordering
						$ordering = new OrderingAmenity([
							'user_id' => Auth::user()->id,
							'audit_id' => $audit->audit_id,
							'building_id' => $building_id,
							'amenity_id' => $amenity_type->id,
							'amenity_inspection_id' => $amenity->id,
							'order' => $latest_ordering + 1,
						]);
						$ordering->save();
					}

					$amenities = OrderingAmenity::where('audit_id', '=', $audit->audit_id)->where('user_id', '=', Auth::user()->id);
					if ($unit_id) {
						$amenities = $amenities->where('unit_id', '=', $unit_id);
					} elseif ($building_id) {
						$amenities = $amenities->where('building_id', '=', $building_id);
						$amenities = $amenities->whereNull('unit_id');
					}
					$amenities = $amenities->orderBy('order', 'asc')->with('amenity')->get(); //->pluck('amenity')->flatten()

					$data_amenities = [];

					// manage name duplicates, number them based on their id
					$amenity_names = [];
					foreach ($amenities as $amenity) {
						$amenity_names[$amenity->amenity->amenity_description][] = $amenity->amenity_inspection_id;
					}

					foreach ($amenities as $amenity) {
						if ($amenity->amenity_inspection) {
							if (null !== $amenity->amenity_inspection->auditor_id) {
								$auditor_initials = $amenity->amenity_inspection->user->initials();
								$auditor_name = $amenity->amenity_inspection->user->full_name();
								$auditor_id = $amenity->amenity_inspection->user->id;
								$auditor_color = $amenity->amenity_inspection->user->badge_color;
							} else {
								$auditor_initials = '<i class="a-avatar-plus_1"></i>';
								$auditor_name = 'CLICK TO ASSIGN TO AUDITOR';
								$auditor_color = '';
								$auditor_id = 0;
							}

							if (null == $amenity->amenity_inspection->completed_date_time) {
								$completed_icon = 'a-circle';
							} else {
								$completed_icon = 'a-circle-checked ok-actionable';
							}
						} else {
							$auditor_initials = '<i class="a-avatar-plus_1"></i>';
							$auditor_name = 'CLICK TO ASSIGN TO AUDITOR';
							$auditor_color = '';
							$auditor_id = 0;
							$completed_icon = 'a-circle-checked ok-actionable';
						}

						if (1 == $amenity->amenity->file) {
							$status = ' fileaudit';
						} else {
							$status = ' siteaudit';
						}

						// check for name duplicates and assign a #
						$key = array_search($amenity->amenity_inspection_id, $amenity_names[$amenity->amenity->amenity_description]);
						if ($key > 0) {
							$key = $key + 1;
							$name = $amenity->amenity->amenity_description . ' ' . $key;
						} else {
							$name = $amenity->amenity->amenity_description;
						}

						if (Finding::where('amenity_id', '=', $amenity->amenity_inspection_id)->where('audit_id', '=', $audit->audit_id)->where('cancelled_at',NULL)->count()) {
							$has_findings = 1;
						} else {
							$has_findings = 0;
						}

						$data_amenities[] = [
							'id' => $amenity->amenity_inspection_id,
							'audit_id' => $amenity->audit_id,
							'name' => $name,
							'status' => $status,
							'auditor_id' => $auditor_id,
							'auditor_initials' => $auditor_initials,
							'auditor_name' => $auditor_name,
							'auditor_color' => $auditor_color,
							'finding_nlt_status' => '',
							'finding_lt_status' => '',
							'finding_sd_status' => '',
							'finding_photo_status' => '',
							'finding_comment_status' => '',
							'finding_copy_status' => '',
							'finding_trash_status' => '',
							'building_id' => $building_id,
							'unit_id' => $amenity->unit_id,
							'completed_icon' => $completed_icon,
							'has_findings' => $has_findings,
						];
					}

					$data['amenities'] = $data_amenities;

					// TBD update amenity totals?

					// reload auditor names at the unit and building row levels
					$reload_auditors = $this->reload_auditors($audit->audit_id, $unit_id, $building_id);
					$unit_auditors = $reload_auditors['unit_auditors'];
					$building_auditors = $reload_auditors['building_auditors'];

					$data['auditor'] = ['unit_auditors' => $unit_auditors, 'building_auditors' => $building_auditors, 'unit_id' => $unit_id, 'building_id' => $building_id];
				} // end if not project
			} // end foreach amenity
		} elseif (0 != $amenity_id) {
			// we are copying the amenity
			//
			//dd($amenity_id);//13299

			$auditor_color = '';
			$auditor_initials = '';
			$auditor_name = '';
			$auditorid = null;

			$amenity_to_copy = AmenityInspection::where('id', '=', $amenity_id)->first();

			if (!$amenity_to_copy) {
				dd("This amenity couldn't be found.");
			}

			// get amenity type
			$amenity_type = Amenity::where('id', '=', $amenity_to_copy->amenity_id)->first();

			$name = $amenity_type->amenity_description;

			// save new amenity
			if ($unit_id) {
				$unitamenity = new UnitAmenity([
					'unit_id' => $unit_id,
					'amenity_id' => $amenity_type->id,
					'comment' => 'manually added by ' . Auth::user()->id,
				]);
				$unitamenity->save();

				$amenity = new AmenityInspection([
					'audit_id' => $audit->audit_id,
					'unit_id' => $unit_id,
					'amenity_id' => $amenity_type->id,
					'auditor_id' => $auditorid,
				]);
				$amenity->save();

				// latest ordering
				$latest_ordering = OrderingAmenity::where('user_id', '=', Auth::user()->id)
					->where('audit_id', '=', $audit->audit_id)
					->where('unit_id', '=', $unit_id)
					->orderBy('order', 'desc')
					->first();
				if (is_object($latest_ordering)) {
					$latest_ordering = $latest_ordering->order;
				} else {
					$latest_ordering = 0;
				}

				// save the ordering
				$ordering = new OrderingAmenity([
					'user_id' => Auth::user()->id,
					'audit_id' => $audit->audit_id,
					'unit_id' => $unit_id,
					'amenity_id' => $amenity_type->id,
					'amenity_inspection_id' => $amenity->id,
					'order' => $latest_ordering + 1,
				]);
				$ordering->save();
			} elseif ($building_id) {
				$buildingamenity = new BuildingAmenity([
					'building_id' => $building_id,
					'amenity_id' => $amenity_type->id,
					'comment' => 'manually added by ' . Auth::user()->id,
				]);
				$buildingamenity->save();

				$amenity = new AmenityInspection([
					'audit_id' => $audit->audit_id,
					'building_id' => $building_id,
					'amenity_id' => $amenity_type->id,
					'auditor_id' => $auditorid,
				]);
				$amenity->save();

				// latest ordering
				$latest_ordering = OrderingAmenity::where('user_id', '=', Auth::user()->id)
					->where('audit_id', '=', $audit->audit_id)
					->where('building_id', '=', $building_id)
					->orderBy('order', 'desc')
					->first();
				if ($latest_ordering) {
					$latest_ordering = $latest_ordering->order;
				} else {
					$latest_ordering = 0;
				}

				// if(is_object($latest_ordering)){
				//     $latest_ordering = $latest_ordering->order;
				// } else {
				//     $latest_ordering = 0;
				// }
				// save the ordering
				$ordering = new OrderingAmenity([
					'user_id' => Auth::user()->id,
					'audit_id' => $audit->audit_id,
					'building_id' => $building_id,
					'amenity_id' => $amenity_type->id,
					'amenity_inspection_id' => $amenity->id,
					'order' => $latest_ordering + 1,
				]);
				$ordering->save();
			} else {
				// adding amenity to project

				$project_amenity = new ProjectAmenity([
					'project_key' => $audit->project_key,
					'project_id' => $audit->project_id,
					'amenity_type_key' => $amenity_type->amenity_type_key,
					'amenity_id' => $amenity_type->id,
					'comment' => 'manually added by ' . Auth::user()->id,
				]);
				$project_amenity->save();

				$cached_building = new CachedBuilding([
					'building_name' => $name,
					'building_id' => null,
					'building_key' => null,
					'audit_id' => $audit->audit_id,
					'audit_key' => $audit->audit_key,
					'project_id' => $audit->project_id,
					'project_key' => $audit->project_key,
					'lead_id' => $audit->lead_id,
					'lead_key' => $audit->lead_key,
					'status' => '',
					'type' => $amenity_type->icon,
					'type_total' => null,
					'type_text' => null,
					'type_text_plural' => null,
					'finding_total' => 0,
					'finding_file_status' => '',
					'finding_nlt_status' => '',
					'finding_lt_status' => '',
					'finding_file_total' => 0,
					'finding_file_completed' => 0,
					'finding_nlt_total' => 0,
					'finding_nlt_completed' => 0,
					'finding_lt_total' => 0,
					'finding_lt_completed' => 0,
					'address' => $audit->address,
					'city' => $audit->city,
					'state' => $audit->state,
					'zip' => $audit->zip,
					'amenity_id' => $amenity_type->id,
				]);
				$cached_building->save();

				$amenity = new AmenityInspection([
					'audit_id' => $audit->audit_id,
					'project_id' => $audit->project_id,
					'amenity_id' => $amenity_type->id,
					'auditor_id' => $auditorid,
					'cachedbuilding_id' => $cached_building->id,
				]);
				$amenity->save();

				$cached_building->amenity_inspection_id = $amenity->id;
				$cached_building->save();

				// latest ordering
				$latest_ordering = OrderingBuilding::where('user_id', '=', Auth::user()->id)
					->where('audit_id', '=', $audit->audit_id)
					->orderBy('order', 'desc')
					->first();
				if ($latest_ordering) {
					$latest_ordering = $latest_ordering->order;
				} else {
					$latest_ordering = 0;
				}
				// save the ordering
				$ordering = new OrderingBuilding([
					'user_id' => Auth::user()->id,
					'audit_id' => $audit->audit_id,
					'building_id' => null,
					'project_id' => $audit->project_id,
					'amenity_id' => $amenity_type->id,
					'amenity_inspection_id' => $amenity->id,
					'order' => $latest_ordering + 1,
				]);
				$ordering->save();
			}

			$amenities = OrderingAmenity::where('audit_id', '=', $audit->audit_id)->where('user_id', '=', Auth::user()->id);
			if ($unit_id) {
				$amenities = $amenities->where('unit_id', '=', $unit_id);
			} elseif ($building_id) {
				$amenities = $amenities->where('building_id', '=', $building_id);
				$amenities = $amenities->whereNull('unit_id');
			}
			$amenities = $amenities->orderBy('order', 'asc')->with('amenity')->get(); //->pluck('amenity')->flatten()

			$data_amenities = [];

			// manage name duplicates, number them based on their id
			$amenity_names = [];
			foreach ($amenities as $amenity) {
				$amenity_names[$amenity->amenity->amenity_description][] = $amenity->amenity_inspection_id;
			}

			foreach ($amenities as $amenity) {
				if ($amenity->amenity_inspection && null !== $amenity->amenity_inspection->auditor_id) {
					$auditor_initials = $amenity->amenity_inspection->user->initials();
					$auditor_name = $amenity->amenity_inspection->user->full_name();
					$auditor_id = $amenity->amenity_inspection->user->id;
					$auditor_color = $amenity->amenity_inspection->user->badge_color;
				} else {
					$auditor_initials = '<i class="a-avatar-plus_1"></i>';
					$auditor_name = 'CLICK TO ASSIGN TO AUDITOR';
					$auditor_color = '';
					$auditor_id = 0;
				}

				if ($amenity->amenity_inspection && null == $amenity->amenity_inspection->completed_date_time) {
					$completed_icon = 'a-circle';
				} else {
					$completed_icon = 'a-circle-checked ok-actionable';
				}

				if (1 == $amenity->amenity->file) {
					$status = ' fileaudit';
				} else {
					$status = ' siteaudit';
				}

				// check for name duplicates and assign a #
				$key = array_search($amenity->amenity_inspection_id, $amenity_names[$amenity->amenity->amenity_description]);
				if ($key > 0) {
					$key = $key + 1;
					$name = $amenity->amenity->amenity_description . ' ' . $key;
				} else {
					$name = $amenity->amenity->amenity_description;
				}

				if (Finding::where('amenity_id', '=', $amenity->amenity_inspection_id)->where('audit_id', '=', $audit->audit_id)->where('cancelled_at',NULL)->count()) {
					$has_findings = 1;
				} else {
					$has_findings = 0;
				}

				$data_amenities[] = [
					'id' => $amenity->amenity_inspection_id,
					'audit_id' => $amenity->audit_id,
					'name' => $name,
					'status' => $status,
					'auditor_id' => $auditor_id,
					'auditor_initials' => $auditor_initials,
					'auditor_name' => $auditor_name,
					'auditor_color' => $auditor_color,
					'finding_nlt_status' => '',
					'finding_lt_status' => '',
					'finding_sd_status' => '',
					'finding_photo_status' => '',
					'finding_comment_status' => '',
					'finding_copy_status' => '',
					'finding_trash_status' => '',
					'building_id' => $building_id,
					'unit_id' => $amenity->unit_id,
					'completed_icon' => $completed_icon,
					'has_findings' => $has_findings,
				];
			}

			$data['amenities'] = $data_amenities;

			// TBD update amenity totals?

			// reload auditor names at the unit and building row levels
			$reload_auditors = $this->reload_auditors($audit->audit_id, $unit_id, $building_id);
			$unit_auditors = $reload_auditors['unit_auditors'];
			$building_auditors = $reload_auditors['building_auditors'];

			$data['auditor'] = ['unit_auditors' => $unit_auditors, 'building_auditors' => $building_auditors, 'unit_id' => $unit_id, 'building_id' => $building_id];
		}
		$data['hide_confirm_modal_js'] = session()->has('hide_confirm_modal');

		return $data;
	}

	public function reload_auditors($audit_id, $unit_id, $building_id)
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

	public function reorderAmenitiesFromAudit($audit, Request $request)
	{
		$building_id = $request->get('building_id');
		$unit_id = $request->get('unit_id');
		$amenity_inspection_id = $request->get('amenity_id'); // this is the amenity_inspection_id
		$index = $request->get('index');

		//dd($building_id." ".$unit_id." ".$amenity_id." ".$index);

		// select all amenity orders except for the one we want to reorder
		$current_ordering = OrderingAmenity::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id);
		$current_ordering = $current_ordering->where('amenity_inspection_id', '!=', $amenity_inspection_id);
		if ($unit_id) {
			$current_ordering = $current_ordering->where('unit_id', '=', $unit_id);
		}
		if ($building_id) {
			$current_ordering = $current_ordering->where('building_id', '=', $building_id);
		}
		$current_ordering = $current_ordering->orderBy('order', 'asc')->get()->toArray();

		$pre_reordering = OrderingAmenity::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id)->where('amenity_inspection_id', '=', $amenity_inspection_id);
		if ($unit_id) {
			$pre_reordering = $pre_reordering->where('unit_id', '=', $unit_id);
		}
		if ($building_id) {
			$pre_reordering = $pre_reordering->where('building_id', '=', $building_id);
		}
		$pre_reordering = $pre_reordering->orderBy('order', 'asc')->first();

		$inserted = [[
			'user_id' => Auth::user()->id,
			'audit_id' => $audit,
			'building_id' => $building_id,
			'unit_id' => $unit_id,
			'amenity_id' => $pre_reordering->amenity_id,
			'amenity_inspection_id' => $pre_reordering->amenity_inspection_id,
			'order' => $index,
		]];

		// insert the building ordering in the array
		$reordered_array = $current_ordering;
		array_splice($reordered_array, $index, 0, $inserted);

		// delete previous ordering
		$previous_ordering = OrderingAmenity::where('audit_id', '=', $audit)->where('user_id', '=', Auth::user()->id);
		if ($unit_id) {
			$previous_ordering = $previous_ordering->where('unit_id', '=', $unit_id);
		}
		if ($building_id) {
			$previous_ordering = $previous_ordering->where('building_id', '=', $building_id);
		}
		$previous_ordering->delete();

		// clean-up the ordering and store
		foreach ($reordered_array as $key => $ordering) {
			$new_ordering = new OrderingAmenity([
				'user_id' => $ordering['user_id'],
				'audit_id' => $ordering['audit_id'],
				'building_id' => $ordering['building_id'],
				'unit_id' => $ordering['unit_id'],
				'amenity_id' => $ordering['amenity_id'],
				'amenity_inspection_id' => $ordering['amenity_inspection_id'],
				'order' => $key + 1,
			]);
			$new_ordering->save();
		}
	}

	public function updateStep($id, Request $request)
	{
		$audit = CachedAudit::where('audit_id', '=', $id)->with('audit')->first();
		$steps = GuideStep::where('guide_step_type_id', '=', 1)->orderBy('order', 'asc');
		if (count($audit->audit->findings->where('cancelled_at',NULL)) || count($audit->audit->reports)) {
			$steps = $steps->where('id', '>', 59);
		} elseif (!count($audit->audit->reports) && count($audit->audit->findings->where('cancelled_at',NULL))) {
			$steps = $steps->where('id', '<', 61)->where('id', '>', 59);
		} elseif (!count($audit->audit->reports) && !count($audit->audit->findings->where('cancelled_at',NULL))) {
			$steps = $steps->where('id', '<', 61);
		}
		$steps = $steps->get();
		$detailsPage = null;
		if (1 == $request->details_refresh) {
			$detailsPage = 1;
		}

		return view('modals.audit-update-step', compact('steps', 'audit', 'detailsPage'));
	}

	public function saveStep(Request $request, $id)
	{
		$message = 1;
		$audit = CachedAudit::where('id', '=', $id)->with('audit')->first();
		$step_id = intval($request->get('step'));
		if ((count($audit->audit->findings->where('cancelled_at',NULL)) || count($audit->audit->reports)) && $step_id < 60) {
			$step_id = 60;
			//if there are findings or a report- the step must be defaulted to inprogress - it cannot be lower.
			$message = 'There is either a report, or findings on this audit. The lowest step possible to set this audit to is In Progress.';
		}
		$step = GuideStep::where('id', '=', $step_id)->first();

		// check if user has the right to save step using roles TBD
		if (Auth::user()->id == $audit->lead || Auth::user()->manager_access()) {
			// Logic to prevent bad selections:

			// add new guide_progress entry
			$progress = new GuideProgress([
				'user_id' => Auth::user()->id,
				'audit_id' => $audit->id,
				'project_id' => $audit->project_id,
				'guide_step_id' => $step_id,
				'type_id' => 1,
			]);
			$progress->save();

			// update CachedAudit table with new step info
			$audit->update([
				'step_id' => $step->id,
				'step_status_icon' => $step->icon,
				'step_status_text' => $step->step_help,
			]);

			return $message;
		} else {
			return 'Sorry, you do not have the correct permissions to update step progress.';
		}
	}

	public function saveProgramUnitInspection($project_id, Request $request)
	{
		//return $inputs = $request->all();
		//Unit_id, program_id, group_ids, type
		//need to insert data in unitinspections
		//get the count
		//load chart and below

		$unit_id = $request->get('unit_id');
		$program_key = $request->get('program_key');
		$group_ids = $request->get('group_ids');
		$type = $request->get('type');
		$audit_id = $request->get('audit_id');
		$project = Project::where('id', '=', $project_id)->first();
		if (!is_null($audit_id)) {
			$audit = $project->selected_audit($audit_id)->audit;
		} else {
			$audit = $project->selected_audit()->audit;
		}

		//dd($unit_id, $program_key, $group_ids, $type, $project->id, $audit->id);

		$unit = Unit::with('building')->find($unit_id);

		$building_key = $unit->building_key;

		//Consider one program any type, no nulls, maybe nulls for groups
		// This is for existing programs!

		//groups, other or HTC, how to deal?
		//If file
		//  check if exists, if yes remove
		//If site
		//  Check if exists, if yes remove
		//if Both
		//  check if both file and site extsts
		//    if yes remove both
		//  check if only one of these exists
		//    insert that doesn't exist
		//  If none exists
		//    insert both
		if (!is_null($program_key)) {
			$program = Program::where('program_key', $program_key)->first();
			$unitprograms = UnitProgram::where('audit_id', $audit->id)
				->where('program_key', $program->program_key)
				->where('unit_id', $unit->id)
				->where('project_id', $project->id)
				->get()
				->count();

			$new_program = false;
			if (0 == $unitprograms) {
				/// this means we are adding this as a substitute
				$add_unit_program = new UnitProgram;
				//unit_key
				//unit_id
				//program_id
				//program_key
				//audit_id
				//monitoring_key - from audit
				//project_id
				//development_key -- from audit
				$add_unit_program->unit_key = $unit->unit_key;
				$add_unit_program->unit_id = $unit->id;
				$add_unit_program->program_id = $program->id;
				$add_unit_program->program_key = $program->program_key;
				$add_unit_program->project_id = $project->id;
				$add_unit_program->audit_id = $audit->id;
				$add_unit_program->monitoring_key = $audit->monitoring_key;
				$add_unit_program->development_key = $audit->development_key;
				$add_unit_program->is_substitute = 1;
				$add_unit_program->save();
				$new_program = true;
			}

			$unitprogram = UnitProgram::where('audit_id', $audit->id)
				->where('program_key', $program->program_key)
				->where('unit_id', $unit->id)
				->where('project_id', $project->id)
				->first();
			if ('file' == $type) {
				$this->inspectionsUpdate($unit, $program, $audit, $group_ids, $project, 'is_file_audit', $new_program);
			} elseif ('physical' == $type) {
				$this->inspectionsUpdate($unit, $program, $audit, $group_ids, $project, 'is_site_visit', $new_program);
			} else {
				$check_if_file_exists = UnitInspection::where('unit_id', $unit->id)->where('program_key', $program->program_key)->where('audit_id', $audit->id)->whereIn('group_id', $group_ids)->where('is_file_audit', 1)->get()->count();
				$check_if_site_exists = UnitInspection::where('unit_id', $unit->id)->where('program_key', $program->program_key)->where('audit_id', $audit->id)->whereIn('group_id', $group_ids)->where('is_site_visit', 1)->get()->count();
				if (($check_if_file_exists > 0 && $check_if_site_exists > 0) || (0 == $check_if_file_exists && 0 == $check_if_site_exists)) {
					$this->inspectionsUpdate($unit, $program, $audit, $group_ids, $project, 'is_file_audit', $new_program);
					$this->inspectionsUpdate($unit, $program, $audit, $group_ids, $project, 'is_site_visit', $new_program);
				} elseif ($check_if_file_exists > 0 && 0 == $check_if_site_exists) {
					$this->inspectionsUpdate($unit, $program, $audit, $group_ids, $project, 'is_site_visit', $new_program);
				} elseif (0 == $check_if_file_exists && $check_if_site_exists > 0) {
					$this->inspectionsUpdate($unit, $program, $audit, $group_ids, $project, 'is_file_audit', $new_program);
				}
			}
		} else {
			// no program specified, we are selecting all programs for that unit
			$unitprograms = UnitProgram::where('audit_id', $audit->id)
				->where('unit_id', $unit->id)
				->where('project_id', $project->id)
				->get();
			//dd($unitprograms);

			foreach ($unitprograms as $unitprogram) {
				$program = $unitprogram->program;
				$group_ids = ProgramGroup::where('program_id', '=', $program->id)->get()->pluck('group_id')->toArray();
				$new_program = 0;

				$check_if_file_exists = UnitInspection::where('unit_id', $unit->id)->where('program_key', $program->program_key)->where('audit_id', $audit->id)->whereIn('group_id', $group_ids)->where('is_file_audit', 1)->get()->count();
				$check_if_site_exists = UnitInspection::where('unit_id', $unit->id)->where('program_key', $program->program_key)->where('audit_id', $audit->id)->whereIn('group_id', $group_ids)->where('is_site_visit', 1)->get()->count();

				//dd($program, $group_ids, $check_if_file_exists, $check_if_site_exists);

				if (($check_if_file_exists > 0 && $check_if_site_exists > 0) || (0 == $check_if_file_exists && 0 == $check_if_site_exists)) {
					$this->inspectionsUpdate($unit, $program, $audit, $group_ids, $project, 'is_file_audit', $new_program, 1);
					$this->inspectionsUpdate($unit, $program, $audit, $group_ids, $project, 'is_site_visit', $new_program, 1);
				} elseif ($check_if_file_exists > 0 && 0 == $check_if_site_exists) {
					$this->inspectionsUpdate($unit, $program, $audit, $group_ids, $project, 'is_site_visit', $new_program, 1);
				} elseif (0 == $check_if_file_exists && $check_if_site_exists > 0) {
					$this->inspectionsUpdate($unit, $program, $audit, $group_ids, $project, 'is_file_audit', $new_program, 1);
				}
			}
		}

		//Substitute programs

		$get_project_details = $this->projectSummaryComposite($project_id);
		$data = $get_project_details['data'];
		//dd($unitprogram->project_program);
		if ($unitprogram->project_program && 2 == $unitprogram->project_program->multiple_building_election_key) {
			//dd($building_key,$unit->building_key);

			$program = collect($data['programs'])->where('building_key', $building_key)->first();
			if (!$program && $group_ids) {
				$program = collect($data['programs'])->where('id', $group_ids[0])->first();
			}
		} else {
			$programGroup = $program->groups();
			$programGroup = $programGroup[0];
			if (array_key_exists('programs', $data)) {
				$program = collect($data['programs'])->where('id', $programGroup);
			} else {
				$program = [];
			}
			if (count($program) > 1) {
				$program = collect($data['programs'])->where('building_key', $building_key)->first();
			} elseif (count($program) > 0) {
				$program = $program->first();
			} else {
				$program = null;
			}
			//dd($program, $unitprogram->project_program->multiple_building_election_key);
		}

		return view('dashboard.partials.project-summary-left-row', compact('data', 'project', 'audit', 'program'));
	}

	private function inspectionsUpdate($unit, $program, $audit, $group_ids, $project, $type, $new_program = false, $force_select = false)
	{
		$check_if_record_exists = UnitInspection::where('unit_id', $unit->id)
			->where('program_key', $program->program_key)
			->where('audit_id', $audit->id)
			->whereIn('group_id', $group_ids)
			->where($type, 1)
			->get();

		if ($check_if_record_exists->count() > 0 && !$force_select) {
			// force_select prevents removal
			foreach ($check_if_record_exists as $key => $exists) {
				if ('is_file_audit' == $type) {
					$exists->is_file_audit = 0;
					$exists->save();
				} else {
					$exists->is_site_visit = 0;
					$exists->save();
				}

				// update cached_building, cached_units
				$exists->swap_remove($audit);
			}
		} else {
			$unitprograms = UnitProgram::where('audit_id', '=', $audit->id)
				->where('program_key', $program->program_key)
				->with('unit', 'program.relatedGroups', 'unit.building.address', 'unitInspected')
				->orderBy('unit_id', 'asc')
				->get();

			if ($unitprograms->count() == 0) {
				$group_ids = array_diff($group_ids, [$this->htc_group_id]);
			}

			foreach ($group_ids as $key => $group_id) {
				//HTC
				//  New
				//
				//  Old
				//Other
				//  New
				//
				//  Old
				//
				if ($new_program && $group_id == $this->htc_group_id) {
				} else {
					$group = Group::find($group_id);
					$insert_new = new UnitInspection;
					$insert_new->program_id = $program->id;
					$insert_new->audit_id = $audit->id;
					$insert_new->audit_id = $audit->id;
					$insert_new->group = $group->group_name;
					$insert_new->group_id = $group_id;
					$insert_new->unit_id = $unit->id;
					$insert_new->unit_key = $unit->unit_key;
					$insert_new->unit_name = $unit->unit_name;
					$insert_new->building_key = $unit->building_key;
					$insert_new->building_id = $unit->building->id;
					$insert_new->audit_key = $audit->monitoring_key;
					$insert_new->project_id = $project->id;
					$insert_new->project_key = $project->project_key;
					$insert_new->program_key = $program->program_key;
					$insert_new->has_overlap = 0;
					if ('is_file_audit' == $type) {
						$insert_new->is_file_audit = 1;
						$insert_new->is_site_visit = 0;
					} else {
						$insert_new->is_site_visit = 1;
						$insert_new->is_file_audit = 0;
					}
					$insert_new->save();

					// update cached_building, cached_units
					$insert_new->swap_add($audit); // only adds once
				}
			}
		}

		return true;
	}

	public function ajaxAuditRequiredUnits(Request $request)
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

	public function householdInfo($unit)
	{
		$unit = Unit::where('id', $unit)->with('household')->first();

		return view('modals.audit_details.householdInfo', compact('unit'));
	}

	public function cachedAuditCheck(Request $request)
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

	public function singleCachedAudit($audit_id)
	{
		$audit = CachedAudit::where('audit_id', intval($audit_id))->first();
		$auditor_access = Auth::user()->auditor_access();
		$audits = $audit->toArray();
		if (null !== $audit) {
			return view('dashboard.partials.audit_row', compact('audit', 'auditor_access', 'audits'));
		} else {
			return 0;
		}
	}

	public function swapAuditorToAudit($cahced_audit_id, Request $request)
	{
		// return $request->all();
		$cached_audit = CachedAudit::with('audit.reports')->find($cahced_audit_id);
		if ($cached_audit && $cached_audit->audit) {
			//update cached audit
			$user = User::find($request->selected_auditor);
			$lead_array['id'] = $user->id;
			$lead_array['name'] = $user->full_name();
			$lead_array['color'] = $user->badge_color;
			$lead_array['status'] = "";
			$lead_array['initials'] = $user->initials();

			$cached_audit->lead = $user->id;
			$cached_audit->lead_json = json_encode($lead_array);
			$cached_audit->save();

			//update Audit
			$audit = $cached_audit->audit;
			$audit->lead_user_id = $user->id;
			$audit->user_key = $user->devco_key;
			$audit->save();
			if ($audit) {
				foreach ($audit->reports as $key => $report) {
					$report->lead_id = $user->id;
					$report->save();
				}
			}
			return 1;
		} else {
			return 'Could not find either the Cached audit or Audit, please contact admin';
		}
	}
}
