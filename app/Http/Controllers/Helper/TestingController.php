<?php

namespace App\Http\Controllers\Helper;

//
use DB;
use Log;
use App\Models\Unit;
use App\Models\User;
use App\Models\People;
use GuzzleHttp\Client;
use App\Models\Finding;
use GuzzleHttp\Promise;
use App\Models\Document;
use App\Models\SyncPeople;
use App\Models\DocumentAudit;
use App\Models\ProjectContactRole;
use App\Http\Controllers\Controller;

//
class TestingController extends Controller
{

	public function __construct()
	{
		$this->allitapc();
	}

	public function getTestAll()
	{
		// echo 12;
		$total = Document::count();
		$chunk = 20;
		$progressBar = '';
		$progress = $total / $chunk;
		$documents = Document::with('communication_details')->where('id', 15978)->get();

		$communication_documents = Document::with('communication_details')->chunk($chunk, function ($documents) use ($progressBar, $progress) {
			// $progressBar->advance($progress);
			foreach ($documents as $key => $doc) {
				$all_findings = [];
				$all_units = [];
				$all_buildings = [];
				$all_sites = [];
				$exist = false;
				if (!is_null($doc->finding_ids)) {
					$exist = true;
					//dd($findingsToInsert, $request->comment, $request->categories, $request->buValue, $request->audit_id);
					$findingIds = json_decode($doc->finding_ids, true);
					if (!is_array($findingIds)) {
						dd($doc);
					}
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

					if (!empty($findingIds)) {
						$all_findings = array_merge($all_findings, $findingIds);
						// $document->finding_ids = json_encode($findingIds, true);
					}
					if (!empty($siteIds)) {
						$all_sites = array_merge($all_sites, $siteIds);
						// $document->site_ids = json_encode($siteIds, true);
					}
					if (!empty($buildingIds)) {
						$all_buildings = array_merge($all_buildings, $buildingIds);
						// $document->building_ids = json_encode($buildingIds, true);
					}
					if (!empty($unitIds)) {
						$all_units = array_merge($all_units, $unitIds);
						// $document->unit_ids = json_encode($unitIds, true);
					}
				}
				if (count($doc->communication_details) > 0) {
					$all_findings = !is_null($doc->finding_ids) ? json_decode($doc->finding_ids) : [];
					$all_units = !is_null($doc->unit_ids) ? json_decode($doc->unit_ids) : [];
					$all_buildings = !is_null($doc->building_ids) ? json_decode($doc->building_ids) : [];
					$all_sites = !is_null($doc->site_ids) ? json_decode($doc->site_ids) : [];

					foreach ($doc->communication_details as $cd) {
						if (!is_null($cd->finding_ids)) {
							$exist = true;

							$findingIds = json_decode($cd->finding_ids);
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
							if (!empty($findingIds)) {
								$all_findings = array_merge($all_findings, $findingIds);
								// $document->finding_ids = json_encode($findingIds, true);
							}
							if (!empty($siteIds)) {
								$all_sites = array_merge($all_sites, $siteIds);
								// $document->site_ids = json_encode($siteIds, true);
							}
							if (!empty($buildingIds)) {
								$all_buildings = array_merge($all_buildings, $buildingIds);
								// $document->building_ids = json_encode($buildingIds, true);
							}
							if (!empty($unitIds)) {
								$all_units = array_merge($all_units, $unitIds);
								// $document->unit_ids = json_encode($unitIds, true);
							}

							$all_sites = array_map('strval', $all_sites);
							$all_buildings = array_map('strval', $all_buildings);
							$all_units = array_map('strval', $all_units);
						}
					}
				}
				if ($exist) {
					$all_sites = array_map('strval', $all_sites);
					$all_buildings = array_map('strval', $all_buildings);
					$all_units = array_map('strval', $all_units);

					if (!empty($all_findings)) {
						$doc->finding_ids = json_encode(array_unique($all_findings), true);
					}
					if (!empty($all_sites)) {
						$doc->site_ids = json_encode(array_unique($all_sites), true);
					}
					if (!empty($all_buildings)) {
						$doc->building_ids = json_encode(array_unique($all_buildings), true);
					}
					if (!empty($all_units)) {
						$doc->unit_ids = json_encode(array_unique($all_units), true);
					}
					$doc->save();
				}
			}
		});

		return 122;

		foreach ($documents as $key => $doc) {
			$all_findings = [];
			$all_units = [];
			$all_buildings = [];
			$all_sites = [];
			$exist = false;
			if (!is_null($doc->finding_ids)) {
				$exist = true;
				//dd($findingsToInsert, $request->comment, $request->categories, $request->buValue, $request->audit_id);
				$findingIds = json_decode($doc->finding_ids);
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

				if (!empty($findingIds)) {
					$all_findings = array_merge($all_findings, $findingIds);
					// $document->finding_ids = json_encode($findingIds, true);
				}
				if (!empty($siteIds)) {
					$all_sites = array_merge($all_sites, $siteIds);
					// $document->site_ids = json_encode($siteIds, true);
				}
				if (!empty($buildingIds)) {
					$all_buildings = array_merge($all_buildings, $buildingIds);
					// $document->building_ids = json_encode($buildingIds, true);
				}
				if (!empty($unitIds)) {
					$all_units = array_merge($all_units, $unitIds);
					// $document->unit_ids = json_encode($unitIds, true);
				}
			}
			if (count($doc->communication_details) > 0) {
				foreach ($doc->communication_details as $cd) {
					if (!is_null($cd->finding_ids)) {
						$exist = true;

						$findingIds = json_decode($cd->finding_ids);
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
						if (!empty($findingIds)) {
							$all_findings = array_merge($all_findings, $findingIds);
							// $document->finding_ids = json_encode($findingIds, true);
						}
						if (!empty($siteIds)) {
							$all_sites = array_merge($all_sites, $siteIds);
							// $document->site_ids = json_encode($siteIds, true);
						}
						if (!empty($buildingIds)) {
							$all_buildings = array_merge($all_buildings, $buildingIds);
							// $document->building_ids = json_encode($buildingIds, true);
						}
						if (!empty($unitIds)) {
							$all_units = array_merge($all_units, $unitIds);
							// $document->unit_ids = json_encode($unitIds, true);
						}

						$all_sites = array_map('strval', $all_sites);
						$all_buildings = array_map('strval', $all_buildings);
						$all_units = array_map('strval', $all_units);
					}
				}

				if ($exist) {
					if (!empty($all_findings)) {
						$doc->finding_ids = json_encode(array_unique($all_findings), true);
					}
					if (!empty($all_sites)) {
						$doc->site_ids = json_encode(array_unique($all_sites), true);
					}
					if (!empty($all_buildings)) {
						$doc->building_ids = json_encode(array_unique($all_buildings), true);
					}
					if (!empty($all_units)) {
						$doc->unit_ids = json_encode(array_unique($all_units), true);
					}
					$doc->save();
				}
			}
		}
		return 'done';

		$communication_documents = Document::with('communication_details')
			->chunk($chunk, function ($cds) use ($progressBar, $progress) {
				$progressBar->advance($progress);
				foreach ($cds as $key => $cd) {
					$check_audit = DocumentAudit::where('audit_id', $cd->communication->audit_id)->where('document_id', $cd->document_id)->first();
					if (!$check_audit) {
						$doc_audit = new DocumentAudit;
						$doc_audit->audit_id = $cd->communication->audit_id;
						$doc_audit->document_id = $cd->document_id;
						$doc_audit->save();
					}
				}
			});

		return 'Already completed';

		DB::beginTransaction();
		$repeat_contact_role = DB::select(DB::raw("SELECT project_contact_role_key,project_id,person_key, COUNT(person_key) FROM project_contact_roles GROUP BY person_key, project_contact_role_key, project_id HAVING COUNT(person_key) > 1"));

		$repeat_people = DB::select(DB::raw("SELECT person_key,first_name,last_name, COUNT(person_key) FROM people GROUP BY person_key, first_name, last_name HAVING COUNT(person_key) > 1"));
		// For each of these duplicate results
		// 	Check if there are multiple sync records
		// 		For these records check if the allita_Id is same or even exists.
		// 			If allita_id is not same - NOT SURE WHAT TO DO
		// 			If allita_id exists only for one record, keep that and delete others
		// 				Make sure people record exists with this allita_id, else create one
		// 				Delete all other people records.... What about User? or any other associated person_key or person_id records?
		// 			If allita_id doesn't exist for any record, keep the latest one
		// 				Create person record and update that as allita_id on sync_people
		// 				Delete all other people records.... What about User? or any other associated person_key or person_id records?
		// 	Find which ones to delete
		//
		//
		// List of tables with person_id
		// 	audits
		// 	organizations - default_contact_person_key, default_contact_person_id
		// 	project_contact_roles - person_id, person_key
		// 	users - - person_id, person_key
		foreach ($repeat_people as $key => $people) {
			Log::info('running for: ' . $people->person_key);
			//get all records from sync_person for this key
			$sync_person = SyncPeople::wherePersonKey($people->person_key)->get();
			//get the records which has allita_id
			$sync_person_with_allita = SyncPeople::wherePersonKey($people->person_key)->whereNotNull('allita_id')->get();
			if (count($sync_person_with_allita) == 0) {
				Log::info('This record has no allita id: ' . $sync_person_with_allita->first()->id);
			} elseif (count($sync_person_with_allita) > 1) {
				Log::info('Multiple records: ' . $sync_person_with_allita->first()->id);
			} elseif (count($sync_person_with_allita) == 1) {
				$correct_sync_person = $sync_person_with_allita->first();
				Log::info('Correcting record for people_id/allita_id: ' . $correct_sync_person->allita_id);
				$delete_duplicate_sync_records = SyncPeople::wherePersonKey($people->person_key)->where('id', '<>', $correct_sync_person->id)->whereNull('allita_id')->delete();
				$delete_duplicate_person_records = People::wherePersonKey($people->person_key)->where('id', '<>', $correct_sync_person->allita_id)->delete();
				$project_contact_roles = ProjectContactRole::wherePersonKey($people->person_key)->get();
				foreach ($project_contact_roles as $key => $project_contact_role) {
					$project_contact_role->person_id = $correct_sync_person->allita_id;
					$project_contact_role->save();
				}
				$users = User::wherePersonKey($people->person_key)->get();
				foreach ($users as $key => $user) {
					$user->person_id = $correct_sync_person->allita_id;
					$user->save();
				}
				// return $person = People::wherePersonKey($people->person_key)->get()->count();
			}
			//get allita record for this sync_person records
		}
		//Remove duplicates from projectcontactrole
		$repeat_contact_role = DB::select(DB::raw("SELECT project_contact_role_key,project_id,person_key, COUNT(person_key) FROM project_contact_roles GROUP BY person_key, project_contact_role_key, project_id HAVING COUNT(person_key) > 1"));
		foreach ($repeat_contact_role as $key => $cr) {
			$check_multiple = ProjectContactRole::where('project_contact_role_key', $cr->project_contact_role_key)
				->where('project_id', $cr->project_id)
				->where('person_key', $cr->person_key)
				->get();
			// return count($check_multiple);
			if (count($check_multiple) > 1) {
				foreach ($check_multiple as $key => $value) {
					if ($key > 0) {
						$value->delete();
					}
				}
			}
		}

		DB::commit();

		// DB::rollback();
		Log::info('DONE');
		return 'done';
		//DB::commit();

		$person = People::wherePersonKey($people->person_key)->get()->count();

		$people = People::with('user', 'phone', 'fax', 'email')->get();
		$urls = [
			'http://homestead2.test',
			'http://homestead2.test/logout',
		];
		$post_urls = [
			'http://localhost/add-announcement',
		];
		$client = new Client(['timeout' => 300]);
		$request_promises = [];
		foreach ($urls as $key => $url) {
			$request_promises[$key] = $client->getAsync($url);
		}
		// foreach ($post_urls as $key => $url) {
		//   $request_promises[$key + 100] = $client->postAsync($url);
		// }
		$one = microtime(true);
		$results = Promise\settle($request_promises)->wait();
		$two = microtime(true);
		$count = 0;
		foreach ($results as $key => $result) {
			if (array_key_exists('value', $result)) {
				$response = $result['value']->getReasonPhrase();
			} else {
				$response = 'ERROR';
			}
			if ($key > 99) {
				echo 'POST -- ' . $post_urls[$count] . ' -- RESPONSE: ' . $response;
				$count++;
			} else {
				echo '<a href="' . $urls[$key] . '">' . $urls[$key] . ' -- RESPONSE: ' . $response;
			}
			echo '<br>';
		}
		return ' <br> -- done in ' . ' -- Time: ' . ($two - $one);

		/*Teacher tests*/
	}
}
