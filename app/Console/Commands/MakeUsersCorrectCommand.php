<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use DB;

/**
 * MakeUsersCorrect Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class MakeUsersCorrectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make_users_correct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user info to what is in DEVCO.';

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
    public function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function handle()
    {
        
        
        if($this->confirm('Would you like to reset all DEVCO based users to their current email and name in DEVCO?'.PHP_EOL.'Enter "no" to set enter a specific user_id.')){
            $users = User::whereNotNull('devco_key')->get()->all();
            $i = 0;
            $this->line(PHP_EOL.'We will set each login email to be their email specified in DEVCO - ie "brian@allita360.com".'.PHP_EOL.'(NOTE: we remove spaces and () characters from last names)');
            if($this->confirm('Would you like to reset all DEVCO based users to a new random password that they need to reset?.')){
                $passwordReset = 1;
            } else {
                $passwordReset = 0;
            }

            $processBar = $this->output->createProgressBar(count($users));
            foreach ($users as $user) {
                $i++;
                if($passwordReset){
                    // New random passord
                    $password = bcrypt($this->generateRandomString());
                } else {
                    // keep existing password
                    $password = $user->password;
                }
                $userNewEmail = $user->person->email;
                //$this->line($i.' User: '.$user->email.' new login email address: '.$userNewEmail.PHP_EOL);
                if ($userNewEmail !== 0) {
                    User::where('id', $user->id)->update(['email'=> $userNewEmail, 'password'=> $password,'name'=>$user->person->first_name.' '.$user->person->last_name]);
                }
                $processBar->advance();
            }
            $this->line('All users now reset to their DEVCO name and emails.".');
        } else {
            $user_id = $this->ask('What user_id would you like to reset?'.PHP_EOL.'(DO NOT ENTER THEIR DEVCO USER KEY - ENTER THEIR ALLITA USER ID)');
            $users = User::whereNotNull('devco_key')->where('id',intval($user_id))->get()->all();
            $i = 0;
            if(count($users)){
                if($this->confirm('Update '.$users[0]->person->first_name.' '.$users[0]->person->last_name)){
                    if($this->confirm('Would you like to reset all DEVCO based users to a new random password that they need to reset?.')){
                        $passwordReset = 1;
                    } else {
                        $passwordReset = 0;
                    }

                    $processBar = $this->output->createProgressBar(count($users));
                    foreach ($users as $user) {

                        $this->line(PHP_EOL.'We will set '.$user->person->first_name.' '.$user->person->last_name.' login email to be their email specified in DEVCO.)');
                        $i++;
                        if($passwordReset){
                            // New random passord
                            $password = bcrypt($this->generateRandomString());
                        } else {
                            // keep existing password
                            $password = $user->password;
                        }
                        
                        //$this->line($i.' User: '.$user->email.' new login email address: '.$userNewEmail.PHP_EOL);
                        
                            User::where('id', $user->id)->update(['email'=> $user->person->email, 'password'=> $password,'name'=>$user->person->first_name.' '.$user->person->last_name]);
                        
                        $processBar->advance();
                    }
                    $this->line('Updated their Allita info to match their DEVCO name and emails.".');
                } else {
                    $this->line('Cancelled Update');
                }
            }else{
                $this->line('Either the user_id '.$user_id.' is not a DEVCO user, or they do not exist in the users table.');
            }
        }
        
    }
}
