<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixUsernames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:usernames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //
        $this->line('Fixing User Names'.PHP_EOL);
        $users = User::get();
        foreach($users as $user){
            if($user->person){
                //dd($user->full_name());
                $user->update(['name'=>$user->full_name()]);
                $this->line('Updated '.$user->full_name().PHP_EOL);
            }

        }
    }
}
