<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * MakeTestFriendly Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class MakeTestFriendlyCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'make_test_friendly';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Change all usernames to be an address specified by the user.';

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
	 */
	public function handle()
	{
		$users = User::get()->all();
		$email = '@allita.org';
		$password = 'password1234';
		if ($this->confirm('Would you like to set all emails to @allita.org with a password of "password1234" ?' . PHP_EOL . 'Enter "no" to set a custom email and password.')) {
			$i = 0;
			$this->line(PHP_EOL . 'We will set each login email to be first initial + last name + plus their user_id number @allita.org - ie "bgreenwood1234@allita.org".' . PHP_EOL . '(NOTE: we remove spaces and () characters from last names)');
			$processBar = $this->output->createProgressBar(count($users));
			foreach ($users as $user) {
				$i++;

				if ($user->person) {
					$userNewEmail = substr($user->person->first_name, 0, 1) . str_replace(' ', '', str_replace('(', '', str_replace(')', '', $user->person->last_name))) . $user->id . "@allita.org";
					if ($userNewEmail !== 0) {
						User::where('id', $user->id)->update(['email' => $userNewEmail, 'password' => bcrypt('password1234'), 'name' => $user->person->first_name . ' ' . $user->person->last_name]);
					}
					//$this->line($i.' User: '.$user->email.' new login email address: '.$userNewEmail.PHP_EOL);
				} else {
					$userNewEmail = str_replace(' ', '', str_replace('(', '', str_replace(')', '', $user->name))) . $user->id . "@allita.org";
					if ($userNewEmail !== 0) {
						User::where('id', $user->id)->update(['email' => $userNewEmail, 'password' => bcrypt('password1234'), 'name' => $user->name]);
					}
				}
				if ($userNewEmail !== 0) {
					User::where('id', $user->id)->update(['email' => $userNewEmail, 'password' => bcrypt('password1234'), 'name' => $user->person->first_name . ' ' . $user->person->last_name]);
				}
				$processBar->advance();
			}
			$this->line('All users now have password "password1234".');
		} else {
			$email = $this->ask('What email domain would you like to use?' . PHP_EOL . '(include the @ symbol - for example to use gmail.com enter "@gmail.com"');
			$password = $this->ask('What password would you like each account to have?');
			$i = 0;
			$this->line(PHP_EOL . 'We will set each login email to be first initial + last name + plus their user_id number ' . $email . ' - ie "bgreenwood1234' . $email . '".' . PHP_EOL . '(NOTE: we remove spaces and () characters from last names)');
			$processBar = $this->output->createProgressBar(count($users));
			foreach ($users as $user) {
				$i++;

				if ($user->person) {
					$userNewEmail = substr($user->person->first_name, 0, 1) . str_replace(' ', '', str_replace('(', '', str_replace(')', '', $user->person->last_name))) . $user->id . $email;
					//$this->line($i.' User: '.$user->email.' new login email address: '.$userNewEmail.PHP_EOL);
					if ($userNewEmail !== 0) {
						User::where('id', $user->id)->update(['email' => $userNewEmail, 'password' => bcrypt('password1234'), 'name' => $user->person->first_name . ' ' . $user->person->last_name]);
					}
				} else {
					$userNewEmail = str_replace(' ', '', str_replace('(', '', str_replace(')', '', $user->name))) . $user->id . $email;
					if ($userNewEmail !== 0) {
						User::where('id', $user->id)->update(['email' => $userNewEmail, 'password' => bcrypt('password1234'), 'name' => $user->name]);
					}
				}

				$processBar->advance();
			}
			$this->line('All users now have password "' . $password . '".');
		}
	}
}
