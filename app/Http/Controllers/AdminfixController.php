<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CommunicationRecipient;
use App\Models\EmailAddress;
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
		foreach ($non_sync_records as $key => $record) {
			$record->email_address_key = NULL;
			$record->save();
		}
		DB::commit();

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
			return 'SUCCESS: All email_address_key ducplicates removed';
		}
	}
}
