<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserRole;
use DB;

/**
 * MakeUsersCorrect Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class MakeUsersHaveRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make_have_roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user role to be property manager.';

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
        
        $duplicates = array();

        if($this->confirm('Would you like to update all users without a role to be property managers?'.PHP_EOL.'Enter "no" to set enter a specific user_id.')){
            $users = User::whereNotNull('devco_key')->get()->all();
            $i = 0;
            

            $processBar = $this->output->createProgressBar(count($users));
            $updated = 0;
            foreach ($users as $user) {
                $i++;
                
                $check = UserRole::where('user_id',$user->id)->count();
                
                // check if the email is a duplicate in the system:
                if($check < 1){

                    $userRole = new UserRole;
                    $userRole->role_id = 1;
                    $userRole->user_id = $user->id;
                    $userRole->save();
                    $updated++;

                }
                
                $processBar->advance();
                
            }
            $this->line('Updated '.$updated.' users to be property managers');
            

        } else {
            $user_id = $this->ask('What user_id would you like to reset?'.PHP_EOL.'(DO NOT ENTER THEIR DEVCO USER KEY - ENTER THEIR ALLITA USER ID)');
            $users = User::where('id',intval($user_id))->get()->all();
            $i = 0;
            if(count($users)){
                if($this->confirm('Update '.$users[0]->person->first_name.' '.$users[0]->person->last_name)){
                    

                    $processBar = $this->output->createProgressBar(count($users));
                    foreach ($users as $user) {

                        $i++;
                
                    $check = UserRole::where('user_id',$user->id)->count();
                    
                    // check if the email is a duplicate in the system:
                    if($check < 1){

                        $userRole = new UserRole;
                        $userRole->role_id = 1;
                        $userRole->user_id = $user->id;
                        $userRole->save();
                        $updated++;

                    }
                    
                    $processBar->advance();

                    }
                    $this->line('Updated the user to be a property manager');
                } else {
                    $this->line('Cancelled Update.');
                }
            }else{
                $this->line('Either the user_id '.$user_id.' is not a DEVCO user, or they do not exist in the users table.');
            }
        }
        
    }
}
