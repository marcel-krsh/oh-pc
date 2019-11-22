<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        
        DB::table('users')->insert([
            'name' => 'Brian Greenwood',
            'email' => 'brian@greenwood360.com',
            'password' => bcrypt('M0therBoard4247'),
            'entity_type'=>'hfa',
            'badge_color' => 'blue',
            'entity_id' => '1',
            'active' => '1',
            'verified' => '1',
        ]);

        // Users Seed
        // DO NOT ADD USERS TO THE TOP - THE Entities use the cardinality of the users entered. Add to the bottom.
        DB::table('users')->insert([
            'name' => 'Holly Swisher',
            'email' => 'hswisher@ohiohome.org',
            'password' => bcrypt('Allita12'),
            'badge_color' => 'pink',
            'entity_id' => '1',
            'entity_type'=>'hfa',
            'active' => '1',
            'verified' => '1',

        ]);

        
    }
}
