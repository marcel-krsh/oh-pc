<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use DB;

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
    protected $description = 'Change all usernames to be @allita.org addresses with a password of SurfBoard0914.';

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
        $i = 0;
        foreach ($users as $user) {
            $i++;
            $userNewEmailEnd = strrpos($user->email, '@');
            $userNewEmail = substr($user->email, 0, $userNewEmailEnd).$i."@allita.org";
            $this->line($i.' User: '.$user->email.' new login email address: '.$userNewEmail.PHP_EOL);
            if ($userNewEmail !== 0) {
                User::where('id', $user->id)->update(['email'=> $userNewEmail, 'password'=> bcrypt('password1234')]);
            }
        }
        $this->line('All users now have password "password1234".');
    }
}
