<?php

namespace App\Http\Controllers\Helper;

//
use DB;
use Validator;
use Carbon\Carbon;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\SyncOrganization;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

//
class DatafixController extends Controller
{

	public function __construct()
	{
		$this->middleware('allita.developer');
		$this->allitapc();
	}

	public function test()
	{
		$lastModifiedDate = SyncOrganization::select(DB::raw("CONCAT(last_edited) as 'last_edited_convert'"), 'last_edited', 'id')->orderBy('last_edited', 'desc')->first();
		// if the value is null set a default start date to start the sync.
		if (is_null($lastModifiedDate)) {
			$modified = '10/1/1900';
		} else {
			// format date stored to the format we are looking for...
			// we resync the last second of the data to be sure we get any records that happened to be recorded at the same second.
			$currentModifiedDateTimeStamp = strtotime($lastModifiedDate->last_edited_convert);
			settype($currentModifiedDateTimeStamp, 'float');
			$currentModifiedDateTimeStamp = $currentModifiedDateTimeStamp - .001;
			$modified = date('m/d/Y G:i:s.u', $currentModifiedDateTimeStamp);
			//dd($lastModifiedDate, $modified);
		}
		return 12;
		// $apiConnect = new DevcoService();
		if (!is_null($apiConnect)) {
			$syncData = $apiConnect->listOrganizations(1, $modified, 1, 'admin@allita.org', 'System Sync Job', 1, 'Server');
			$syncData = json_decode($syncData, true);
			$syncPage = 1;
			//dd($syncData);
			//dd($lastModifiedDate->last_edited_convert,$currentModifiedDateTimeStamp,$modified,$syncData);
			if ($syncData['meta']['totalPageCount'] > 0) {
				do {
					if ($syncPage > 1) {
						//Get Next Page
						$syncData = $apiConnect->listOrganizations($syncPage, $modified, 1, 'admin@allita.org', 'System Sync Job', 1, 'Server');
						$syncData = json_decode($syncData, true);
						//dd('Page Count is Higher',$syncData);
					}
					foreach ($syncData['data'] as $i => $v) {
						// check if record exists
						$updateRecord = SyncOrganization::select('id', 'allita_id', 'last_edited', 'updated_at')->where('organization_key', $v['attributes']['organizationKey'])->first();
						// convert booleans
						settype($v['attributes']['isActive'], 'boolean');
						//dd($updateRecord,$updateRecord->updated_at);
						if (isset($updateRecord->id)) {
							// record exists - get matching table record

							/// NEW CODE TO UPDATE ALLITA TABLE PART 1
							$allitaTableRecord = Organization::find($updateRecord->allita_id);
							/// END NEW CODE PART 1

							// convert dates to seconds and miliseconds to see if the current record is newer.
							$devcoDate = new DateTime($v['attributes']['lastEdited']);
							$allitaDate = new DateTime($updateRecord->last_edited);

							$allitaFloat = "." . $allitaDate->format('u');
							$devcoFloat = "." . $devcoDate->format('u');
							settype($allitaFloat, 'float');
							settype($devcoFloat, 'float');
							$devcoDateEval = strtotime($devcoDate->format('Y-m-d G:i:s')) + $devcoFloat;
							$allitaDateEval = strtotime($allitaDate->format('Y-m-d G:i:s')) + $allitaFloat;

							//dd($allitaTableRecord,$devcoDateEval,$allitaDateEval,$allitaTableRecord->last_edited, $updateRecord->updated_at);

							if ($devcoDateEval > $allitaDateEval) {
								if (!is_null($allitaTableRecord) && $allitaTableRecord->last_edited <= $updateRecord->updated_at) {
									// record is newer than the one currently on file in the allita db.
									// update the sync table first
									SyncOrganization::where('id', $updateRecord['id'])
										->update([
											'default_address_key' => $v['attributes']['defaultAddressKey'],
											'is_active' => $v['attributes']['isActive'],
											'default_phone_number_key' => $v['attributes']['defaultPhoneNumberKey'],
											'default_fax_number_key' => $v['attributes']['defaultFaxNumberKey'],
											'default_contact_person_key' => $v['attributes']['defaultContactPersonKey'],
											'parent_organization_key' => $v['attributes']['parentOrganizationKey'],
											'organization_name' => $v['attributes']['organizationName'],
											'fed_id_number' => $v['attributes']['fedIDNumber'],

											'organization_key' => $v['attributes']['organizationKey'],
											'last_edited' => $v['attributes']['lastEdited'],
										]);
									$UpdateAllitaValues = SyncOrganization::find($updateRecord['id']);
									// update the allita db - we use the updated at of the sync table as the last edited value for the actual Allita Table.
									$allitaTableRecord->update([
										'default_address_key' => $v['attributes']['defaultAddressKey'],
										'default_address_id' => null,
										'is_active' => $v['attributes']['isActive'],
										'default_phone_number_key' => $v['attributes']['defaultPhoneNumberKey'],
										'default_phone_number_id' => null,
										'default_fax_number_key' => $v['attributes']['defaultFaxNumberKey'],
										'default_fax_number_key' => null,
										'default_contact_person_key' => $v['attributes']['defaultContactPersonKey'],
										'default_contact_person_id' => null,
										'parent_organization_key' => $v['attributes']['parentOrganizationKey'],
										'parent_organization_id' => null,
										'organization_name' => $v['attributes']['organizationName'],
										'fed_id_number' => $v['attributes']['fedIDNumber'],

										'organization_key' => $v['attributes']['organizationKey'],
										'last_edited' => $UpdateAllitaValues->updated_at,
									]);
									//dd('inside.');
								} elseif (is_null($allitaTableRecord)) {
									// the allita table record doesn't exist
									// create the allita table record and then update the record
									// we create it first so we can ensure the correct updated at
									// date ends up in the allita table record
									// (if we create the sync record first the updated at date would become out of sync with the allita table.)

									$allitaTableRecord = Organization::create([
										'default_address_key' => $v['attributes']['defaultAddressKey'],
										'is_active' => $v['attributes']['isActive'],
										'default_phone_number_key' => $v['attributes']['defaultPhoneNumberKey'],
										'default_fax_number_key' => $v['attributes']['defaultFaxNumberKey'],
										'default_contact_person_key' => $v['attributes']['defaultContactPersonKey'],
										'parent_organization_key' => $v['attributes']['parentOrganizationKey'],
										'organization_name' => $v['attributes']['organizationName'],
										'fed_id_number' => $v['attributes']['fedIDNumber'],
										'organization_key' => $v['attributes']['organizationKey'],
									]);
									// Create the sync table entry with the allita id
									$syncTableRecord = SyncOrganization::create([
										'default_address_key' => $v['attributes']['defaultAddressKey'],
										'is_active' => $v['attributes']['isActive'],
										'default_phone_number_key' => $v['attributes']['defaultPhoneNumberKey'],
										'default_fax_number_key' => $v['attributes']['defaultFaxNumberKey'],
										'default_contact_person_key' => $v['attributes']['defaultContactPersonKey'],
										'parent_organization_key' => $v['attributes']['parentOrganizationKey'],
										'organization_name' => $v['attributes']['organizationName'],
										'fed_id_number' => $v['attributes']['fedIDNumber'],
										'organization_key' => $v['attributes']['organizationKey'],
										'last_edited' => $v['attributes']['lastEdited'],
										'allita_id' => $allitaTableRecord->id,
									]);
									// Update the Allita Table Record with the Sync Table's updated at date
									$allitaTableRecord->update(['last_edited' => $syncTableRecord->updated_at]);
								}
							}
						} else {
							// Create the Allita Entry First
							// We do this so the updated_at value of the Sync Table does not become newer
							// when we add in the allita_id
							$allitaTableRecord = Organization::create([
								'default_address_key' => $v['attributes']['defaultAddressKey'],
								'is_active' => $v['attributes']['isActive'],
								'default_phone_number_key' => $v['attributes']['defaultPhoneNumberKey'],
								'default_fax_number_key' => $v['attributes']['defaultFaxNumberKey'],
								'default_contact_person_key' => $v['attributes']['defaultContactPersonKey'],
								'parent_organization_key' => $v['attributes']['parentOrganizationKey'],
								'organization_name' => $v['attributes']['organizationName'],
								'fed_id_number' => $v['attributes']['fedIDNumber'],
								'organization_key' => $v['attributes']['organizationKey'],
							]);
							// Create the sync table entry with the allita id
							$syncTableRecord = SyncOrganization::create([
								'default_address_key' => $v['attributes']['defaultAddressKey'],
								'is_active' => $v['attributes']['isActive'],
								'default_phone_number_key' => $v['attributes']['defaultPhoneNumberKey'],
								'default_fax_number_key' => $v['attributes']['defaultFaxNumberKey'],
								'default_contact_person_key' => $v['attributes']['defaultContactPersonKey'],
								'organization_name' => $v['attributes']['organizationName'],
								'fed_id_number' => $v['attributes']['fedIDNumber'],
								'organization_key' => $v['attributes']['organizationKey'],
								'parent_organization_key' => $v['attributes']['parentOrganizationKey'],
								'last_edited' => $v['attributes']['lastEdited'],
								'allita_id' => $allitaTableRecord->id,
							]);
							// Update the Allita Table Record with the Sync Table's updated at date
							$allitaTableRecord->update(['last_edited' => $syncTableRecord->updated_at]);
						}
					}
					$syncPage++;
				} while ($syncPage <= $syncData['meta']['totalPageCount']);
			}
		}
	}

	public function changeSyncTableLastEditedDateBackDecade($table)
	{
		$error = 0;
		if (Schema::hasTable($table)) {
			$now = Carbon::now();
			$decage_ago = $now->subYears(10);
			return view('helpers.table-latest-date', compact('table', 'decage_ago', 'error'));
		}
		$error = 1;
		return view('helpers.table-latest-date', compact('table', 'error'));
	}

	public function changeSyncTableLastEditedDateBackDecadeSave(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'table' => 'required',
		]);
		if (Schema::hasTable($request->table)) {
			ini_set('max_execution_time', 300);
			$now = Carbon::now();
			$decage_ago = $now->subYears(10); //->format('Y-m-d H:i:s.u');
			$records = DB::table($request->table)->get();
			DB::table($request->table)->where('id', '>', 0)->update([
				'last_edited' => $decage_ago,
			]);
			return 1;
		} else {
			$validator->getMessageBag()->add('table', 'Given table does not exist in Database');
			return response()->json(['errors' => $validator->errors()->all()]);
		}

		return $this->extraCheckErrors($validator);
	}

	public function extraCheckErrors($validator)
	{
		$validator->getMessageBag()->add('error', 'Something went wrong. Check your code!!');
		return response()->json(['errors' => $validator->errors()->all()]);
	}
};
