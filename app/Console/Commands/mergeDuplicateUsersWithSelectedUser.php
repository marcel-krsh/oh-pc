<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\People;
use App\Models\Communication;
use App\Models\HistoricEmail;
use Illuminate\Console\Command;
use App\Models\ProjectContactRole;
use App\Models\CommunicationRecipient;

class mergeDuplicateUsersWithSelectedUser extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'merge_duplicate_users';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Search the user by
    	* first_name and last_name
    	* User ID, fetch data by first_name and last_name
    	* Find by 2 user ids and merge by slected one
    	If there are multiple users with same name, show them. Merge others with selected user. ';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$selection = $this->ask('Select one of the following options to pick and merge the users.' . PHP_EOL .
			'1. Enter first_name and last_name of the user' . PHP_EOL .
			'2. Enter user ID and search based on first name and last of this user' . PHP_EOL .
			'3. Enter multiple user ids and merge these with selected one user');
		if ($selection == 1) {
			$first_name = $this->ask('Enter first name');
			$last_name = $this->ask('Enter last name');
			$persons = People::with('user')->where('first_name', 'like', '%' . $first_name . '%')->where('last_name', 'like', '%' . $last_name . '%')->get();
			$this->fetchPersons($persons);
		} elseif ($selection == 2) {
			// $this->line('Work in progress...');
			$user_id = $this->ask('Enter user_id');
			$user = User::with('person')->find($user_id);
			if ($user) {
				if ($user->person) {
					$persons = People::with('user')->where('first_name', 'like', '%' . $user->person->first_name . '%')->where('last_name', 'like', '%' . $user->person->last_name . '%')->get();
					$this->fetchPersons($persons);
				} else {
					$this->line('No user found with the provided user_id.');
				}
			} else {
				$this->line('No user found with the provided ID');
			}
		} elseif ($selection == 3) {
			$cs_user_ids = $this->ask('Enter multiple user_id\'s using comma seperated');
			$user_ids = explode(',', $cs_user_ids);
			$users = User::with('person')->whereIn('id', $user_ids)->get();
			if (count($users)) {
				$persons = $users->pluck('person');
				$this->fetchPersons($persons);
			} else {
				$this->line('No user found with the provided ID');
			}
			// $this->line('Work in progress...');
		} else {
			$this->line('Selected option is not valid, try again by choosing the provided options. ');
		}
	}

	protected function fetchPersons($persons)
	{
		if ($persons->count() == 0) {
			$this->line('No users found.');
		} elseif ($persons->count() > 10) {
			$this->line('Too many users found, please contact admin.');
		} else {
			$this->line('COUNT' . '.| USER ID | FIRST NAME | LAST NAME | DEVCO KEY |      EMAIL     ');
			$user_exists = 0;
			foreach ($persons as $key => $person) {
				if ($person->user) {
					$user_exists = 1;
					$this->line($key + 1 . '.     | ' . $person->user->id . ' | ' . $person->first_name . ' | ' . $person->last_name . ' | ' . $person->user->devco_key . ' | ' . $person->user->email);
				} else {
					$this->line($key + 1 . '.     | ' . 'NA' . ' | ' . $person->first_name . ' | ' . $person->last_name . ' | ' . 'NA' . ' | ' . 'NA');
				}
			}
			if ($user_exists == 0) {
				$this->line('No associate user exists for the selected people.');
			}
			$user_id = $this->ask('Chooose user_id whose information is valid. Other users info will be moved to selected user_id');
			$main_user = $persons->where('user.id', $user_id)->first();
			if ($main_user) {
				$this->removePeopleDuplicates($main_user, $persons);
				$this->removeUserDuplicates($main_user);
				$this->line('Removed the duplicate users.');
			} else {
				$this->line('Selected user_id does not belong to selected users list');
			}
		}
		return 1;
	}

	protected function removePeopleDuplicates($main_user, $persons)
	{
		//consider the following tables
		//	People
		//	Project Contact
		//	Communications
		$other_persons = $persons->where('user.id', '<>', $main_user->user->id);
		$project_contacts = ProjectContactRole::whereIn('person_id', $other_persons->pluck('id'))->get();
		foreach ($project_contacts as $key => $pc) {
			$pc->person_id = $main_user->id;
			$pc->save();
		}
		$communications = Communication::whereIn('owner_id', $other_persons->pluck('user.id'))->get();
		foreach ($communications as $key => $pc) {
			$pc->owner_id = $main_user->user->id;
			$pc->save();
		}
		$communication_rcs = CommunicationRecipient::whereIn('user_id', $other_persons->pluck('user.id'))->get();
		foreach ($communication_rcs as $key => $pc) {
			$pc->user_id = $main_user->user->id;
			$pc->save();
		}
		foreach ($other_persons as $key => $op) {
			$user = $op->user;
			if ($user) {
				$historic_emails = HistoricEmail::where('user_id', $user->id)->get();
				foreach ($historic_emails as $key => $email) {
					$email->user_id = $main_user->user->id;
					$email->save();
				}
				$user->delete();
			}
			$op->delete();
		}

		return 1;
	}

	protected function removeUserDuplicates($main_user)
	{
		//Check if user exists with this name without person record
		$check_user = User::where('name', 'like', '%' . $main_user->first_name . ' ' . $main_user->last_name . '%')->get();
		if (count($check_user) > 10) {
			$this->line('Too many users found, please contact admin.');
		} elseif (count($check_user) > 1) {
			$this->line('Looks like there are users with no person associated with them.');
			$this->line('COUNT' . '.| USER ID |     NAME    | DEVCO KEY |      EMAIL     ');
			$user_exists = 0;
			foreach ($check_user as $key => $user) {
				$user_exists = 1;
				$this->line($key + 1 . '.     | ' . $user->id . ' | ' . $user->name . ' | ' . $user->devco_key . ' | ' . $user->email);
			}
			$user_id = $this->ask('Chooose user_id whose information is valid. Other users info will be moved to selected user_id');
			$main_user = $check_user->where('id', $user_id)->first();
			if (!$main_user) {
				$this->line('The chosen user_id does not exist');
			} else {
				$other_users = $check_user->where('id', '<>', $user_id);
				$communications = Communication::whereIn('owner_id', $other_users->pluck('id'))->get();
				foreach ($communications as $key => $pc) {
					$pc->owner_id = $main_user->id;
					$pc->save();
				}
				$communication_rcs = CommunicationRecipient::whereIn('user_id', $other_users->pluck('id'))->get();
				foreach ($communication_rcs as $key => $pc) {
					$pc->user_id = $main_user->id;
					$pc->save();
				}
				foreach ($other_users as $key => $user) {
					$historic_emails = HistoricEmail::where('user_id', $user->id)->get();
					foreach ($historic_emails as $key => $email) {
						$email->user_id = $main_user->id;
						$email->save();
					}
					$user->delete();
				}
			}
		}
		return 1;
	}
}
