<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Project;
use App\Models\People;
use App\Models\Address;
use App\Models\CachedAudit;
use App\Models\Audit;
use DB;
use Faker\Factory as Faker;
use Event;

/**
 * MakeTestFriendly Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class MakeSuperTestFriendlyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make_super_test_friendly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change all usernames, names, property names to be something we can display in public.';

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
        
       
        $people = People::where('id','<>','7859')->get()->all();
       
        $email = '@allita.org';
        $password = 'password1234';

        $this->line('We are changing the names of all the people first..');
        $processBar = $this->output->createProgressBar(count($people));
        foreach ($people as $person) {
            $faker = Faker::create();
            $person->first_name = $faker->firstName;
            $person->last_name = $faker->lastName;
            $person->save();
            $processBar->advance();
        }
        unset($people);
        $properties = Project::get()->all();
        $this->line(PHP_EOL.'We are changing the names of all the properties next..');
        $processBar = $this->output->createProgressBar(count($properties));
        foreach ($properties as $property) {
            $faker = Faker::create();
            $property->project_name = $faker->company.' '.$faker->companySuffix;
            $property->save();
            $processBar->advance();
        }
        unset($addresses);
        $addresses = Address::get()->all();
        $this->line(PHP_EOL.'We are changing the addresses of all the properties next..');
        $processBar = $this->output->createProgressBar(count($addresses));
        foreach ($addresses as $property) {
            $faker = Faker::create();
            $property->line_1 = $faker->streetAddress;
            $property->city = $faker->city;
            $property->zip = $faker->postcode;
            $property->latitude = $faker->latitude;
            $property->longitude = $faker->longitude;
            $property->save();
            $processBar->advance();
        }
        $addresses = Address::get()->all();
        
        unset($addresses);

        
        $users = User::get()->all();
        if($this->confirm('Would you like to set all emails to @allita.org with a password of "password1234" ?'.PHP_EOL.'Enter "no" to set a custom email and password.')){
            $i = 0;
            $this->line(PHP_EOL.'We will set each login email to be first initial + last name + plus their user_id number @allita.org - ie "bgreenwood1234@allita.org".'.PHP_EOL.'(NOTE: we remove spaces and () characters from last names)');
            $processBar = $this->output->createProgressBar(count($users));
            foreach ($users as $user) {
                $i++;
               
                $userNewEmail = substr($user->person->first_name, 0, 1).str_replace(' ','', str_replace('(','',str_replace(')','',$user->person->last_name))).$user->id."@allita.org";
                //$this->line($i.' User: '.$user->email.' new login email address: '.$userNewEmail.PHP_EOL);
                if ($userNewEmail !== 0) {
                    User::where('id', $user->id)->update(['email'=> $userNewEmail, 'password'=> bcrypt('password1234'),'name'=>$user->person->first_name.' '.$user->person->last_name]);
                }
                $processBar->advance();
            }
            $this->line('All users now have password "password1234".');
        } else {
            $email = $this->ask('What email domain would you like to use?'.PHP_EOL.'(include the @ symbol - for example to use gmail.com enter "@gmail.com"');
            $password = $this->ask('What password would you like each account to have?');
            $i = 0;
             $this->line(PHP_EOL.'We will set each login email to be first initial + last name + plus their user_id number '.$email.' - ie "bgreenwood1234'.$email.'".'.PHP_EOL.'(NOTE: we remove spaces and () characters from last names)');
            $processBar = $this->output->createProgressBar(count($users));
            foreach ($users as $user) {
                $i++;
                
               $userNewEmail = substr($user->person->first_name, 0, 1).str_replace(' ','', str_replace('(','',str_replace(')','',$user->person->last_name))).$user->id.$email;
                //$this->line($i.' User: '.$user->email.' new login email address: '.$userNewEmail.PHP_EOL);
                if ($userNewEmail !== 0) {
                    User::where('id', $user->id)->update(['email'=> $userNewEmail, 'password'=> bcrypt($password),'name'=>$user->person->first_name.' '.$user->person->last_name]);
                }
                $processBar->advance();
            }
            $this->line('All users now have password "'.$password.'".');
        }
        unset($users);
        $cachedAudits = CachedAudit::get()->all();
        $this->line(PHP_EOL.'We updating the caches..');
        $processBar = $this->output->createProgressBar(count($cachedAudits));
        forEach($cachedAudits as $ca){
            $this->info('Working on audit '.$ca->audit_id);

            $audit = Audit::where('id','=',$ca->audit_id)->first();
            if($audit){
                Event::fire('audit.cache', $audit);
            }else{
            }
        }
    }
}
