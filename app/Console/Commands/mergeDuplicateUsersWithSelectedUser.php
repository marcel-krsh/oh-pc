<?php

namespace App\Console\Commands;

use App\Models\People;
use App\Models\Communication;
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
			$persons = People::with('user')->where('first_name', $first_name)->where('last_name', $last_name)->get();
			if ($persons->count() == 0) {
				$this->line('No users found.');
			} elseif ($persons->count() > 10) {
				$this->line('Too many users found, please contact admin.');
			} else {
				$this->line('COUNT' . '. -- USER ID  -- FIRST NAME -- LAST NAME -- DEVCO KEY -- EMAIL');
				$user_exists = 0;
				foreach ($persons as $key => $person) {
					if ($person->user) {
						$user_exists = 1;
						$this->line($key + 1 . '.     -- ' . $person->user->id . ' -- ' . $person->first_name . ' -- ' . $person->last_name . ' -- ' . $person->user->devco_key . ' -- ' . $person->user->email);
					} else {
						$this->line($key + 1 . '.     -- ' . 'NA' . ' -- ' . $person->first_name . ' -- ' . $person->last_name . ' -- ' . 'NA' . ' -- ' . 'NA');
					}
				}
				if ($user_exists == 0) {
					$this->line('No associate user exists for the selected people.');
				}
				$user_id = $this->ask('Chooose user_id whose information is valid. Other users info will be moved to selected user_id');
				$main_user = $persons->where('user.id', $user_id)->first();
				if ($main_user) {
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
							$user->delete();
						}
						$op->delete();
					}
					$this->line('completed');
				} else {
					$this->line('Selected user_id does not belong to selected users list');
				}
			}
		} elseif ($selection == 2) {
		} elseif ($selection == 3) {
		} else {
			$this->line('Selected option is not valid, try again by choosing the provided options. ');
		}
	}
}
