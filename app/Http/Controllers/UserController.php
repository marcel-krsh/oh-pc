<?php

namespace App\Http\Controllers;

use Auth;
use View;
use Carbon;
use Session;
use App\Models\User;
use App\Models\Address;
use App\Models\Availability;
use Illuminate\Http\Request;
use App\Events\AuditorAddressEvent;
use App\Http\Controllers\Controller;
use App\Models\UserNotificationPreferences;

class UserController extends Controller
{
	public function __construct()
	{
		// $this->middleware('auth');
		if (env('APP_DEBUG_NO_DEVCO') == 'true') {
			//Auth::onceUsingId(286); // TEST BRIAN
			//Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
		}
		$this->middleware(function ($request, $next) {
			$this->user = Auth::user();
			$this->auditor_access = $this->user->auditor_access();
			View::share('auditor_access', $this->auditor_access);
			View::share('current_user', $this->user);
			return $next($request);
		});
	}

	public function saveAuditorAddress(Request $request, $user)
	{
		if (Auth::user()->can('access_manager')) {
			$user = $user;
			$userObj = User::find($user);
		} else {
			$user = Auth::user()->id;
			$userObj = Auth::user();
		}
		$forminputs = $request->get('inputs');
		parse_str($forminputs, $forminputs);

		$address = new Address([
			'line_1' => $forminputs['address1'],
			'line_2' => $forminputs['address2'],
			'city' => $forminputs['city'],
			'state' => $forminputs['state'],
			'zip' => $forminputs['zip'],
			'user_id' => $user,
		]);
		$address->save();

		$formatted_address = $forminputs['address1'];
		if ($forminputs['address2']) {$formatted_address = $formatted_address . ", " . $forminputs['address2'];}
		if ($forminputs['city']) {$formatted_address = $formatted_address . ", " . $forminputs['city'];}
		if ($forminputs['state']) {$formatted_address = $formatted_address . ", " . $forminputs['state'];}
		if ($forminputs['zip']) {$formatted_address = $formatted_address . " " . $forminputs['zip'];}

		broadcast(new AuditorAddressEvent($userObj, $address->id, $formatted_address));

		return 1;
	}

	public function deleteAvailability(Request $request, $userid, $id)
	{
		$user = Auth::user();

		// check if the availability is owned by the user
		$availability = Availability::where('id', '=', $id)->first();

		if ($availability) {
			if ($availability->user_id == $user->id) {
				$availability->delete();
				return 1;
			}
		}
		return 0;
	}

	public function saveAuditorAvailability(Request $request, $user)
	{
		$forminputs = $request->get('inputs');
		parse_str($forminputs, $forminputs);

		$current_user = Auth::user();
		$daterange = $forminputs['daterange'];
		$starttime = $forminputs['starttime'];
		$endtime = $forminputs['endtime'];
		$availability = $forminputs['availability']; //available or notavailable
		$monday = (array_key_exists('monday', $forminputs) && "on" == $forminputs['monday']) ? 1 : 0;
		$tuesday = (array_key_exists('tuesday', $forminputs) && "on" == $forminputs['tuesday']) ? 1 : 0;
		$wednesday = (array_key_exists('wednesday', $forminputs) && "on" == $forminputs['wednesday']) ? 1 : 0;
		$thursday = (array_key_exists('thursday', $forminputs) && "on" == $forminputs['thursday']) ? 1 : 0;
		$friday = (array_key_exists('friday', $forminputs) && "on" == $forminputs['friday']) ? 1 : 0;
		$saturday = (array_key_exists('saturday', $forminputs) && "on" == $forminputs['saturday']) ? 1 : 0;
		$sunday = (array_key_exists('sunday', $forminputs) && "on" == $forminputs['sunday']) ? 1 : 0;

		$date_array = explode(" to ", $daterange);
		if (count($date_array) != 2) {
			// make it work for a single day
			$date_array[0] = $daterange;
			$date_array[1] = $date_array[0];
		}
		if (empty($date_array)) {
			return "Date range provided is not in correct format. Please let the Allita Support Team know date range you have selected";
		}
		$startdate = Carbon\Carbon::createFromFormat('F j, Y', $date_array[0]);
		$enddate = Carbon\Carbon::createFromFormat('F j, Y', $date_array[1]);

		$days = [];

		// go through each day of the week
		if ($monday) {
			$tmp_start = Carbon\Carbon::createFromFormat('F j, Y', $date_array[0]);
			if ($tmp_start->isMonday()) {
				$days[] = $tmp_start->format('Y-m-d');
			}
			for ($date = $tmp_start->next(Carbon\Carbon::MONDAY); $date->lte($enddate); $date->addWeek()) {
				$days[] = $date->format('Y-m-d');
			}
		}
		if ($tuesday) {
			$tmp_start = Carbon\Carbon::createFromFormat('F j, Y', $date_array[0]);
			if ($tmp_start->isTuesday()) {
				$days[] = $tmp_start->format('Y-m-d');
			}
			for ($date = $tmp_start->next(Carbon\Carbon::TUESDAY); $date->lte($enddate); $date->addWeek()) {
				$days[] = $date->format('Y-m-d');
			}
		}
		if ($wednesday) {
			$tmp_start = Carbon\Carbon::createFromFormat('F j, Y', $date_array[0]);
			if ($tmp_start->isWednesday()) {
				$days[] = $tmp_start->format('Y-m-d');
			}
			for ($date = $tmp_start->next(Carbon\Carbon::WEDNESDAY); $date->lte($enddate); $date->addWeek()) {
				$days[] = $date->format('Y-m-d');
			}
		}
		if ($thursday) {
			$tmp_start = Carbon\Carbon::createFromFormat('F j, Y', $date_array[0]);
			if ($tmp_start->isThursday()) {
				$days[] = $tmp_start->format('Y-m-d');
			}
			for ($date = $tmp_start->next(Carbon\Carbon::THURSDAY); $date->lte($enddate); $date->addWeek()) {
				$days[] = $date->format('Y-m-d');
			}
		}
		if ($friday) {
			$tmp_start = Carbon\Carbon::createFromFormat('F j, Y', $date_array[0]);
			if ($tmp_start->isFriday()) {
				$days[] = $tmp_start->format('Y-m-d');
			}
			for ($date = $tmp_start->next(Carbon\Carbon::FRIDAY); $date->lte($enddate); $date->addWeek()) {
				$days[] = $date->format('Y-m-d');
			}
		}
		if ($saturday) {
			$tmp_start = Carbon\Carbon::createFromFormat('F j, Y', $date_array[0]);
			if ($tmp_start->isSaturday()) {
				$days[] = $tmp_start->format('Y-m-d');
			}
			for ($date = $tmp_start->next(Carbon\Carbon::SATURDAY); $date->lte($enddate); $date->addWeek()) {
				$days[] = $date->format('Y-m-d');
			}
		}
		if ($sunday) {
			$tmp_start = Carbon\Carbon::createFromFormat('F j, Y', $date_array[0]);
			if ($tmp_start->isSunday()) {
				$days[] = $tmp_start->format('Y-m-d');
			}
			for ($date = $tmp_start->next(Carbon\Carbon::SUNDAY); $date->lte($enddate); $date->addWeek()) {
				$days[] = $date->format('Y-m-d');
			}
		}

		usort($days, "strcmp");

		if (Carbon\Carbon::createFromFormat('H:i:s', $starttime)->gt(Carbon\Carbon::createFromFormat('H:i:s', $endtime))) {
			return "The end time must be later than the start time.";
		}

		// convert time in slot position and span
		// slot 1 is 6am, then one slot every 15 min
		$hour_1 = Carbon\Carbon::createFromFormat('H:i:s', $starttime)->format('H');
		$min_1 = Carbon\Carbon::createFromFormat('H:i:s', $starttime)->format('i');
		$hour_2 = Carbon\Carbon::createFromFormat('H:i:s', $endtime)->format('H');
		$min_2 = Carbon\Carbon::createFromFormat('H:i:s', $endtime)->format('i');

		$slot_start = ($hour_1 - 6) * 4 + 1 + $min_1 / 15;
		$slot_end = ($hour_2 - 6) * 4 + 1 + $min_2 / 15;
		$slot_span = $slot_end - $slot_start;

		// for each day
		// look through database
		// if there is a record in the day, compare times

		if ("available" == $availability) {
			foreach ($days as $day) {
				$avail_records = Availability::where('user_id', '=', $current_user->id)->where('date', '=', $day)->get();

				$records_to_delete = []; // stores the records to delete
				$create_new_record = 1;

				if (count($avail_records)) {
					foreach ($avail_records as $avail_record) {
						// if no overlap, ignore and prepare to insert unless another match is found on the next record

						// if there is an overlap, combine and override the new availability with the new values, prepare to delete the overlaping record

						// in all cases, we will insert one or more records at the end unless the overlap is full.

						if (($slot_start < $avail_record->start_slot && $slot_start + $slot_span < $avail_record->start_slot) || $slot_start > $avail_record->start_slot + $avail_record->span) {
							// no overlap
						} else {
							if ($slot_start >= $avail_record->start_slot && $slot_start + $slot_span <= $avail_record->start_slot + $avail_record->span) {
								// full overlap, nothing to do
								//return "nothing to do";
								$create_new_record = 0;
							} else {
								// combine and update record
								if ($slot_start < $avail_record->start_slot) {
									// return "overlap and starting before";
									$updated_slot_start = $slot_start;
									$updated_start_time = $starttime;

									if ($slot_start + $slot_span > $avail_record->start_slot + $avail_record->span) {
										$updated_span = $slot_span;
										$updated_end_time = $endtime;
									} else {

										$tmp_hour_1 = Carbon\Carbon::createFromFormat('H:i:s', $starttime)->format('H');
										$tmp_min_1 = Carbon\Carbon::createFromFormat('H:i:s', $starttime)->format('i');
										$tmp_hour_2 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->end_time)->format('H');
										$tmp_min_2 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->end_time)->format('i');

										$tmp_slot_start = ($tmp_hour_1 - 6) * 4 + 1 + $tmp_min_1 / 15;
										$tmp_slot_end = ($tmp_hour_2 - 6) * 4 + 1 + $tmp_min_2 / 15;

										$updated_span = $tmp_slot_end - $tmp_slot_start;
										$updated_end_time = $avail_record->end_time;
									}

									// save current record for deletion
									$records_to_delete[] = $avail_record;

									// reset the new availability values with the new ones
									$slot_start = $updated_slot_start;
									$starttime = $updated_start_time;
									$slot_span = $updated_span;
									$endtime = $updated_end_time;
								} else {
									// return "overlap and starting after";
									$updated_slot_start = $avail_record->start_slot;
									$updated_start_time = $avail_record->start_time;

									$tmp_hour_1 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->start_time)->format('H');
									$tmp_min_1 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->start_time)->format('i');
									$tmp_hour_2 = Carbon\Carbon::createFromFormat('H:i:s', $endtime)->format('H');
									$tmp_min_2 = Carbon\Carbon::createFromFormat('H:i:s', $endtime)->format('i');

									$tmp_slot_start = ($tmp_hour_1 - 6) * 4 + 1 + $tmp_min_1 / 15;
									$tmp_slot_end = ($tmp_hour_2 - 6) * 4 + 1 + $tmp_min_2 / 15;

									$updated_span = $tmp_slot_end - $tmp_slot_start;
									$updated_end_time = $endtime;

									// save current record for deletion
									$records_to_delete[] = $avail_record;

									// reset the new availability values with the new ones
									$slot_start = $updated_slot_start;
									$starttime = $updated_start_time;
									$slot_span = $updated_span;
									$endtime = $updated_end_time;
								}
							}
						}
					}
				}

				if ($create_new_record) {
					foreach ($records_to_delete as $record_to_delete) {
						$record_to_delete->delete();
					}
					$new_avail = new Availability([
						'user_id' => $current_user->id,
						'date' => $day,
						'start_time' => $starttime,
						'end_time' => $endtime,
						'start_slot' => $slot_start,
						'span' => $slot_span,
					]);
					$new_avail->save();
				}
			}
		} else {
			foreach ($days as $day) {
				$avail_records = Availability::where('user_id', '=', $current_user->id)->where('date', '=', $day)->get();

				if (count($avail_records)) {
					foreach ($avail_records as $avail_record) {
						// if times overlap, remove and update existing record
						if (($slot_start < $avail_record->start_slot &&
							$slot_start + $slot_span < $avail_record->start_slot) ||
							$slot_start > $avail_record->start_slot + $avail_record->span
						) {
							// no overlap, nothing to do
							//return "no overlap";
						} else {
							if ($slot_start <= $avail_record->start_slot && $slot_start + $slot_span >= $avail_record->start_slot + $avail_record->span) {
								//return "full overlap, deleto";
								// full overlap, remove record
								$avail_record->delete();
							} else {
								// remove and update record
								if ($slot_start <= $avail_record->start_slot) {
									// return "overlap and starting before";
									// update record, keep only whatever is after the end of the span

									$updated_slot_start = $slot_end;
									$updated_start_time = $endtime;

									$tmp_hour_1 = Carbon\Carbon::createFromFormat('H:i:s', $endtime)->format('H');
									$tmp_min_1 = Carbon\Carbon::createFromFormat('H:i:s', $endtime)->format('i');
									$tmp_hour_2 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->end_time)->format('H');
									$tmp_min_2 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->end_time)->format('i');

									$tmp_slot_start = ($tmp_hour_1 - 6) * 4 + 1 + $tmp_min_1 / 15;
									$tmp_slot_end = ($tmp_hour_2 - 6) * 4 + 1 + $tmp_min_2 / 15;

									$updated_span = $tmp_slot_end - $tmp_slot_start;
									$updated_end_time = $avail_record->end_time;

									// update record
									$avail_record->update([
										"start_time" => $updated_start_time,
										"end_time" => $updated_end_time,
										"start_slot" => $updated_slot_start,
										"span" => $updated_span,
									]);

									// return "overlap: ".$updated_slot_start." ".$updated_start_time." ".$updated_span." ".$updated_end_time;
								} elseif ($slot_start > $avail_record->start_slot && $slot_end >= $avail_record->start_slot + $avail_record->span) {
									// overlap, removing the end, keeping record up to starttime
									$updated_slot_start = $avail_record->start_slot;
									$updated_start_time = $avail_record->start_time;
									$tmp_hour_1 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->start_time)->format('H');
									$tmp_min_1 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->start_time)->format('i');
									$tmp_hour_2 = Carbon\Carbon::createFromFormat('H:i:s', $starttime)->format('H');
									$tmp_min_2 = Carbon\Carbon::createFromFormat('H:i:s', $starttime)->format('i');

									$tmp_slot_start = ($tmp_hour_1 - 6) * 4 + 1 + $tmp_min_1 / 15;
									$tmp_slot_end = ($tmp_hour_2 - 6) * 4 + 1 + $tmp_min_2 / 15;

									$updated_span = $tmp_slot_end - $tmp_slot_start;
									$updated_end_time = $starttime;

									// update record
									$avail_record->update([
										"start_time" => $updated_start_time,
										"end_time" => $updated_end_time,
										"start_slot" => $updated_slot_start,
										"span" => $updated_span,
									]);

									// return "overlap: ".$updated_slot_start." ".$updated_start_time." ".$updated_span." ".$updated_end_time;
								} else {
									// return "bloabla";
									// splitting into two records:
									// the one before starttime and the one after starttime+span

									// return "overlap and starting after";
									$updated_slot_start = $avail_record->start_slot;
									$updated_start_time = $avail_record->start_time;

									$tmp_hour_1 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->start_time)->format('H');
									$tmp_min_1 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->start_time)->format('i');
									$tmp_hour_2 = Carbon\Carbon::createFromFormat('H:i:s', $starttime)->format('H');
									$tmp_min_2 = Carbon\Carbon::createFromFormat('H:i:s', $starttime)->format('i');

									$tmp_slot_start = ($tmp_hour_1 - 6) * 4 + 1 + $tmp_min_1 / 15;
									$tmp_slot_end = ($tmp_hour_2 - 6) * 4 + 1 + $tmp_min_2 / 15;

									$updated_span = $tmp_slot_end - $tmp_slot_start;
									$updated_end_time = $starttime;

									$updated_slot_start2 = $slot_start + $slot_span + 1;
									$updated_start_time2 = $endtime;

									$tmp_hour_1 = Carbon\Carbon::createFromFormat('H:i:s', $endtime)->format('H');
									$tmp_min_1 = Carbon\Carbon::createFromFormat('H:i:s', $endtime)->format('i');
									$tmp_hour_2 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->end_time)->format('H');
									$tmp_min_2 = Carbon\Carbon::createFromFormat('H:i:s', $avail_record->end_time)->format('i');

									$tmp_slot_start = ($tmp_hour_1 - 6) * 4 + 1 + $tmp_min_1 / 15;
									$tmp_slot_end = ($tmp_hour_2 - 6) * 4 + 1 + $tmp_min_2 / 15;

									$updated_span2 = $tmp_slot_end - $tmp_slot_start;
									$updated_end_time2 = $avail_record->end_time;

									//return "overlap: ".$updated_slot_start2." ".$updated_start_time2." ".$updated_span2." ".$updated_end_time2;

									// delete current record, add split records
									$avail_record->delete();

									$new_avail = new Availability([
										'user_id' => $current_user->id,
										'date' => $day,
										'start_time' => $updated_start_time,
										'end_time' => $updated_end_time,
										'start_slot' => $updated_slot_start,
										'span' => $updated_span,
									]);
									$new_avail->save();

									$new_avail = new Availability([
										'user_id' => $current_user->id,
										'date' => $day,
										'start_time' => $updated_start_time2,
										'end_time' => $updated_end_time2,
										'start_slot' => $updated_slot_start2,
										'span' => $updated_span2,
									]);
									$new_avail->save();
								}

								// return "overlap: ".$updated_slot_start." ".$updated_start_time." ".$updated_span." ".$updated_end_time;
							}
						}
					}
				}
			}
		}

		return 1;
	}

	public function deleteAuditorAddress(Request $request, $address_id)
	{
		// check if current user can delete this record and if the record exists.

		$address = Address::where('id', '=', $address_id)->first();

		if ($address && Auth::user()->id == $address->user_id) {
			$address->delete();
			return 1;
		} else {
			return 0;
		}
	}

	public function preferences($id)
	{
		if (Auth::user()->id != $id && !Auth::user()->can('access_manager')) {
			$output['message'] = 'You can only edit and view your own preferences.';
			return $output;
		}
		$user = User::with('person')->find($id);
		$phone_number = '';
		// if ($user->person) {
		// 	if ($user->person->phone) {
		// 		if ($user->person->phone->number()) {
		// 			$phone_number = $user->person->phone->number();
		// 		}
		// 	}
		// }

		//dd($user->organization_details->address->line_1);

		$org_name = $user->organization;
		$org_address1 = '';
		$org_address2 = '';
		$org_city = '';
		$org_state = '';
		$org_zip = '';
		if ($user->organization_details) {
			if ($user->organization_details->address) {
				$org_id = $user->organization_details->address->id;
				$org_address1 = $user->organization_details->address->line_1;
				$org_address2 = $user->organization_details->address->line_2;
				$org_city = $user->organization_details->address->city;
				$org_state = $user->organization_details->address->state;
				$org_zip = $user->organization_details->address->zip;
			}
		}

		$addresses = [];
		foreach ($user->auditor_addresses as $address) {
			$formatted_address = $address->line_1;
			if ($address->line_2) {
				$formatted_address = $formatted_address . ", " . $address->line_2;
			}
			if ($address->city) {
				$formatted_address = $formatted_address . ", " . $address->city;
			}
			if ($address->state) {
				$formatted_address = $formatted_address . ", " . $address->state;
			}
			if ($address->zip) {
				$formatted_address = $formatted_address . " " . $address->zip;
			}

			$addresses[] = [
				'address_id' => $address->id,
				'address' => $formatted_address,
			];
		}

		// build calendar
		if (Session::has('availability.currentdate') && Session::get('availability.currentdate') != '') {
			$d = Session::get('availability.currentdate');
		} else {
			$d = Carbon\Carbon::now()->startOfWeek();
			Session::put('availability.currentdate', $d);
		}

		$calendar = $this->getCalendar($d); //dd($calendar);
		$unp = UserNotificationPreferences::where('user_id', $user->id)->first();

		if ($user->person && $user->person->allita_phone) {
			$phone_number = $user->person->allita_phone->number;
		}

		$data = collect([
			"summary" => [
				"id" => $id,
				"name" => $user->name,
				'initials' => $user->initials(),
				'active' => $user->active,
				'email' => $user->email,
				'color' => $user->badge_color,
				'phone' => $phone_number,
				'organization' => [
					"id" => isset($org_id) ? $org_id : null,
					"name" => $org_name,
					"address1" => $org_address1,
					"address2" => $org_address2,
					"city" => $org_city,
					"state" => $org_state,
					"zip" => $org_zip,
				],
				'availability_max_hours' => $user->availability_max_hours,
				'availability_lunch' => $user->availability_lunch,
				'availability_max_driving' => $user->availability_max_driving,
				'addresses' => $addresses,
				'date' => $d->copy()->subDays(0)->format('F j, Y'),
				'ref' => $d->copy()->subDays(0)->format('Ymd'),
				'date-previous' => $d->copy()->subDays(7)->format('F j, Y'),
				'ref-previous' => $d->copy()->subDays(7)->format('Ymd'),
				'date-next' => strtoupper($d->copy()->addDays(7)->format('F j, Y')),
				'ref-next' => $d->copy()->addDays(7)->format('Ymd'),
			],
			"calendar" => $calendar['now'],
			"calendar-previous" => $calendar['previous'],
			"calendar-next" => $calendar['next'],
			"notification_preference" => [],
		]);

		return view('modals.user-preferences', compact('data', 'user', 'unp'));
	}

	public function preferencesView($id)
	{
		$user = User::find($id);
		$phone_number = '';
		if ($user->person) {
			if ($user->person->phone) {
				if ($user->person->phone->number()) {
					$phone_number = $user->person->phone->number();
				}
			}
		}
		$org_name = $user->organization;
		$org_address1 = '';
		$org_address2 = '';
		$org_city = '';
		$org_state = '';
		$org_zip = '';
		if ($user->organization_details) {
			if ($user->organization_details->address) {
				$org_id = $user->organization_details->address->id;
				$org_address1 = $user->organization_details->address->line_1;
				$org_address2 = $user->organization_details->address->line_2;
				$org_city = $user->organization_details->address->city;
				$org_state = $user->organization_details->address->state;
				$org_zip = $user->organization_details->address->zip;
			}
		}

		$addresses = [];
		foreach ($user->auditor_addresses as $address) {
			$formatted_address = $address->line_1;
			if ($address->line_2) {
				$formatted_address = $formatted_address . ", " . $address->line_2;
			}
			if ($address->city) {
				$formatted_address = $formatted_address . ", " . $address->city;
			}
			if ($address->state) {
				$formatted_address = $formatted_address . ", " . $address->state;
			}
			if ($address->zip) {
				$formatted_address = $formatted_address . " " . $address->zip;
			}

			$addresses[] = [
				'address_id' => $address->id,
				'address' => $formatted_address,
			];
		}

		// build calendar
		if (Session::has('availability.currentdate') && Session::get('availability.currentdate') != '') {
			$d = Session::get('availability.currentdate');
		} else {
			$d = Carbon\Carbon::now()->startOfWeek();
			Session::put('availability.currentdate', $d);
		}

		$calendar = $this->getCalendar($d); //dd($calendar);
		$unp = UserNotificationPreferences::where('user_id', $user->id)->first();

		$data = collect([
			"summary" => [
				"id" => $id,
				"name" => $user->name,
				'initials' => $user->initials(),
				'active' => $user->active,
				'email' => $user->email,
				'phone' => $user->initials(),
				'color' => $user->badge_color,
				'phone' => $phone_number,
				'organization' => [
					"id" => isset($org_id) ? $org_id : null,
					"name" => $org_name,
					"address1" => $org_address1,
					"address2" => $org_address2,
					"city" => $org_city,
					"state" => $org_state,
					"zip" => $org_zip,
				],
				'availability_max_hours' => $user->availability_max_hours,
				'availability_lunch' => $user->availability_lunch,
				'availability_max_driving' => $user->availability_max_driving,
				'addresses' => $addresses,
				'date' => $d->copy()->subDays(0)->format('F j, Y'),
				'ref' => $d->copy()->subDays(0)->format('Ymd'),
				'date-previous' => $d->copy()->subDays(7)->format('F j, Y'),
				'ref-previous' => $d->copy()->subDays(7)->format('Ymd'),
				'date-next' => strtoupper($d->copy()->addDays(7)->format('F j, Y')),
				'ref-next' => $d->copy()->addDays(7)->format('Ymd'),
			],
			"calendar" => $calendar['now'],
			"calendar-previous" => $calendar['previous'],
			"calendar-next" => $calendar['next'],
			"notification_preference" => [],
		]);

		return view('modals.user-preferences-view', compact('data', 'user', 'unp'));
	}

	public function setDefaultAddress(Request $request, $auditor_id, $address_id)
	{
		// TBD user check
		if (Auth::user()->id == $auditor_id) {
			$current_user = User::where('id', '=', $auditor_id)->first();
			$current_user->update([
				'default_address_id' => $address_id,
			]);
			return 1;
		}

		return 0;
	}

	public function getCalendar($d)
	{

		// create the content for the selected week and one week before/after
		$tmp_day = $d->copy()->subDays(7);
		$days = [];
		$events = [];

		$first_day = $tmp_day->copy()->format('Y-m-d');
		$last_day = $tmp_day->copy()->addDays(20)->format('Y-m-d');

		$availabilities = Availability::where('user_id', '=', Auth::user()->id)
			->whereBetween('date', [$first_day, $last_day])
			->orderBy('date', 'asc')
			->get();

		foreach ($availabilities as $a) {
			$events[$a->date][] = [
				"id" => $a->id,
				"status" => "",
				"start_time" => strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $a->start_time)->format('h:i A')),
				"end_time" => strtoupper(Carbon\Carbon::createFromFormat('H:i:s', $a->end_time)->format('h:i A')),
				"start" => $a->start_slot,
				"span" => $a->span,
				"icon" => "a-circle-minus",
				"lead" => 0,
				"class" => "available no-border-top no-border-bottom",
				"modal_type" => "",
			];
		}

		for ($i = 0; $i < 21; $i++) {
			$header[] = $tmp_day->copy()->addDays($i)->format('m/d');

			if (array_key_exists($tmp_day->copy()->addDays($i)->format('Y-m-d'), $events)) {
				$events_array = $events[$tmp_day->copy()->addDays($i)->format('Y-m-d')];

				// figure out the before and after areas on the schedule
				$start_slot = 1;
				$end_slot = 60;
				foreach ($events_array as $e) {
					if ($e['start'] > $start_slot) {
						$start_slot = $e['start'];
					}

					if ($e['start'] + $e['span'] < $end_slot) {
						$end_slot = $e['start'] + $e['span'];
					}
				}

				$before_time_start = 1;
				$before_time_span = $start_slot - 1;
				$after_time_start = $end_slot;
				$after_time_span = 61 - $end_slot;
			} else {
				$events_array = [];
				$before_time_start = 1;
				$before_time_span = 0;
				$after_time_start = 60;
				$after_time_span = 1;
			}

			$index_date = $tmp_day->copy()->addDays($i)->format('Y-m-d');
			$days[$index_date] = [
				"date" => $tmp_day->copy()->addDays($i)->format('m/d'),
				"date_formatted" => $tmp_day->copy()->addDays($i)->format('F j, Y'),
				"date_formatted_name" => strtolower($tmp_day->copy()->addDays($i)->englishDayOfWeek),
				"no_availability" => 0,
				"before_time_start" => $before_time_start,
				"before_time_span" => $before_time_span,
				"after_time_start" => $after_time_start,
				"after_time_span" => $after_time_span,
				"events" => $events_array,
			];
		}

		$calendar['now'] = [
			"header" => array_slice($header, 7, 7),
			"content" => array_slice($days, 7, 7),
			"footer" => [
				"previous" => strtoupper($d->copy()->subDays(7)->format('F j, Y')),
				'ref-previous' => $d->copy()->subDays(7)->format('Ymd'),
				"today" => strtoupper($d->copy()->subDays(0)->format('F j, Y')),
				"next" => strtoupper($d->copy()->addDays(7)->format('F j, Y')),
				'ref-next' => $d->copy()->addDays(7)->format('Ymd'),
			],
		];
		$calendar['previous'] = [
			"header" => array_slice($header, 0, 7),
			"content" => array_slice($days, 0, 7),
			"footer" => [
				"previous" => strtoupper($d->copy()->subDays(14)->format('F j, Y')),
				'ref-previous' => $d->copy()->subDays(14)->format('Ymd'),
				"today" => strtoupper($d->copy()->subDays(7)->format('F j, Y')),
				"next" => strtoupper($d->copy()->addDays(0)->format('F j, Y')),
				'ref-next' => $d->copy()->addDays(0)->format('Ymd'),
			],
		];
		$calendar['next'] = [
			"header" => array_slice($header, 14, 7),
			"content" => array_slice($days, 14, 7),
			"footer" => [
				"previous" => strtoupper($d->copy()->subDays(0)->format('F j, Y')),
				'ref-previous' => $d->copy()->subDays(0)->format('Ymd'),
				"today" => strtoupper($d->copy()->addDays(7)->format('F j, Y')),
				"next" => strtoupper($d->copy()->addDays(14)->format('F j, Y')),
				'ref-next' => $d->copy()->addDays(14)->format('Ymd'),
			],
		];

		return $calendar;
	}

	public function getAvailabilityCalendar($id, $currentdate = null, $beforeafter = null)
	{
		if (Auth::user()->id != $id) {
			$output['message'] = 'You can only edit your own preferences.';
			return $output;
		}

		if (null === $currentdate) {
			if (Session::has('availability.currentdate') && Session::get('availability.currentdate') != '') {
				$d = Session::get('availability.currentdate');
			} else {
				$d = Carbon\Carbon::now()->startOfWeek();
				session(['availability.currentdate' => $d]);
				//$calendar_current_date = Session::get('availability.currentdate');
			}
		} else {
			$d = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->startOfWeek();
			session(['availability.currentdate' => $d]);
		}

		if ("before" == $beforeafter) {
			$newdate = $d->subDays(7);
			session(['availability.currentdate' => $d->copy()->addDays(7)]);
		} elseif ("after" == $beforeafter) {
			$newdate = $d->addDays(7);
			session(['availability.currentdate' => $d->copy()->subDays(7)]);
		} else {
			$newdate = $d;
		}

		$calendar = $this->getCalendar($newdate);

		$data = collect([
			"summary" => [
				"id" => $id,
				"name" => "",
				'initials' => '',
				'color' => '',
				'date' => $d->copy()->subDays(0)->format('F j, Y'),
				'ref' => $d->copy()->subDays(0)->format('Ymd'),
				'date-previous' => $d->copy()->subDays(7)->format('F j, Y'),
				'ref-previous' => $d->copy()->subDays(7)->format('Ymd'),
				'date-next' => strtoupper($d->copy()->addDays(7)->format('F j, Y')),
				'ref-next' => $d->copy()->addDays(7)->format('Ymd'),
			],
			"calendar" => $calendar['now'],
			"calendar-previous" => $calendar['previous'],
			"calendar-next" => $calendar['next'],
		]);

		return view('auditors.partials.auditor-calendar', compact('data', 'beforeafter'));
	}
}
