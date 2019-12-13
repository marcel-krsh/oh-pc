<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CommunicationRecipient;
use App\Models\EmailAddress;
use App\Models\People;
use App\Models\SyncEmailAddress;
use App\Models\SyncPeople;
use Carbon\Carbon;
use DB;
use Event;

class AdminfixController extends Controller
{
	/**
	 * Created to fix the non-triggered notifications from 12-01-2019 to NOW. Recommended to run once
	 * @return [type] [description]
	 */
	public function communicationNotifications()
	{
		return 'Already ran';

		if (!session()->has('ran-notification')) {
			//get all the messages sent to receipients with communication and user, which are not seen
			$receipients = CommunicationRecipient::with('communication', 'user')->whereBetween('created_at', ['2019-12-01 00:00:00', Carbon::now()])->where('seen', '<>', 1)->get();
			foreach ($receipients as $key => $receipient) {
				//Check if the notification was already triggered for this message - HOW!!!
				Event::dispatch('communication.created', $receipient);
				// event(new CommunicationReceipientEvent($receipient->id));
			}
			session(['ran-notification' => 1]);
			return 'Processed, DONOT RUN AGAIN';
		} else {
			return 'Already ran';
		}
	}

/**
 * Duplicate email_address_key creating issues
 * null the email_address_key for any records that have a null last_edited.
 * 	If lasted_edited is not null and there are multiple instances of the email_address_key
 *  	delete the older records - while updating any foreign keys using those ids (you'll have to look through the models)
 *   	update the sync record's allita_id for sync_email_addresses with the correct email address id
 * @return text success or failure message
 */
	public function emailAddressDuplicateKey()
	{
		//null the email_address_key for any records that have a null last_edited.
		DB::beginTransaction();
		$non_sync_records = EmailAddress::whereNull('last_edited')->whereNotNull('email_address_key')->get();
		$email_keys = $non_sync_records->pluck('email_address_key');
		$allita_ids = $non_sync_records->pluck('id');
		//Sync email address records are correct
		foreach ($non_sync_records as $key => $record) {
			$sync_email_address = SyncEmailAddress::where('email_address_key', $record->email_address_key)->first();
			if (!$sync_email_address || $sync_email_address->allita_id != $record->id) {
				$record->email_address_key = NULL;
				$record->save();
			} elseif ($sync_email_address) {
				$record->last_edited = $sync_email_address->last_edited;
				$record->save();
			}
		}
		//
		//Need to fix 2 tables, people, sync_email_address - Check user_emails table too
		//	Get SynPeople default_email_address_key
		//		> Check if record exists in email_addresses
		//			->take this record and update it in SyncEmailAddress table (allita_id)
		$people = People::whereIn('default_email_address_key', $email_keys)->get();
		foreach ($people as $key => $person) {
			$sync_people = SyncPeople::where('allita_id', $person->id)->get();
			$correction = EmailAddress::where('email_address_key', $person->default_email_address_key)->get();
			$sync_email_address = SyncEmailAddress::where('email_address_key', $person->default_email_address_key)->first();
			if (!$sync_email_address) {
				$person->default_email_address_key = NULL;
				$person->save();
				foreach ($correction as $key => $corr) {
					$corr->email_address_key = NULL;
					$corr->save();
				}
			}
			if (count($correction) > 1) {
				return 'Still multiple records exist for this key  ' . $correction;
			} elseif ($sync_email_address) {
				//Correct symc email address for allita_id
				// if(!$correction->first()) {
				// 	return EmailAddress::find(5897);//183019
				// }
				if ($sync_email_address->allita_id != $correction->first()->id) {
					// return 'Wrong correction for ' . $sync_email_address->email_address_key;
					$sync_email_address->allita_id = $correction->first()->id;
					$sync_email_address->save();
				}
				//Check the person email key and correct for email_address_id
				if ($person->default_email_address_id != $correction->first()->id) {
					$person->default_email_address_id = $correction->first()->id;
					$person->save();
				}
			}
		}
		//Check if
		// $user_email_ids = UserEmail::whereIn('email_address_id', $allita_ids)->get()->pluck('email_address_id');
		// $email_addresses = EmailAddress::whereIn('id', $user_email_ids)->get();
		$duplicates = DB::table('email_addresses')
			->select('email_address_key')
		// ->where('group_id', 3)
			->groupBy('email_address_key')
			->havingRaw('COUNT(*) > 1')
			->pluck('email_address_key');
		$duplicates_count = EmailAddress::whereIn('email_address_key', $duplicates)->get();
		if ($duplicates_count->count() > 0) {
			return 'Looks like all the duplicates are not removed, check these email_address_keys- ' . $duplicates_count->pluck('email_address_key');
		} else {
			DB::commit();
			return 'SUCCESS: All email_address_key ducplicates removed';
		}
	}
}
